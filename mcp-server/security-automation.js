'use strict';

const { execFile } = require('child_process');
const fs = require('fs');
const path = require('path');

const REPO_ROOT = path.resolve(__dirname, '..');
const MCP_DIR = __dirname;
const STATE_FILE = path.join(MCP_DIR, 'security-automation-state.json');
const REPORT_DIR = path.join(MCP_DIR, 'security-reports');
const LOG_FILE = path.join(MCP_DIR, 'security-automation.log');

const ONE_DAY_MS = 24 * 60 * 60 * 1000;
const ONE_WEEK_MS = 7 * ONE_DAY_MS;
const WEEKLY_BRANCH = 'automation/weekly-security-update';
const WEEKLY_COMMIT_TITLE = 'Weekly Security Update';

function log(message) {
    const line = `[${new Date().toISOString()}] ${message}`;
    fs.appendFileSync(LOG_FILE, `${line}\n`);
    console.log(line);
}

function loadState() {
    try {
        return JSON.parse(fs.readFileSync(STATE_FILE, 'utf8'));
    } catch {
        return { lastDailyAuditAt: 0, lastWeeklyPushAt: 0 };
    }
}

function saveState(state) {
    fs.writeFileSync(STATE_FILE, JSON.stringify(state, null, 2));
}

function isDue(lastRunAt, intervalMs) {
    return (Date.now() - lastRunAt) >= intervalMs;
}

function parseJsonOrNull(text) {
    if (!text || typeof text !== 'string') {
        return null;
    }

    try {
        return JSON.parse(text);
    } catch {
        return null;
    }
}

function runCommand(file, args, options = {}) {
    const cwd = options.cwd || REPO_ROOT;
    const allowFailure = Boolean(options.allowFailure);

    return new Promise((resolve, reject) => {
        execFile(file, args, {
            cwd,
            env: process.env,
            maxBuffer: 20 * 1024 * 1024,
        }, (error, stdout, stderr) => {
            if (error) {
                const result = {
                    code: typeof error.code === 'number' ? error.code : 1,
                    stdout: stdout || '',
                    stderr: stderr || error.message || '',
                };

                if (allowFailure) {
                    resolve(result);
                    return;
                }

                reject(new Error(`${file} ${args.join(' ')} failed: ${result.stderr.trim() || result.stdout.trim()}`));
                return;
            }

            resolve({ code: 0, stdout: stdout || '', stderr: stderr || '' });
        });
    });
}

async function hasCommand(name) {
    const result = await runCommand('which', [name], { allowFailure: true });
    return result.code === 0;
}

async function getRepositorySlug() {
    if (process.env.GITHUB_REPOSITORY) {
        return process.env.GITHUB_REPOSITORY;
    }

    const result = await runCommand('gh', ['repo', 'view', '--json', 'nameWithOwner', '--jq', '.nameWithOwner'], { allowFailure: true });
    if (result.code !== 0) {
        return '';
    }

    return result.stdout.trim();
}

function summarizeComposerAudit(parsed) {
    if (!parsed || typeof parsed !== 'object') {
        return { advisories: null, abandoned: null };
    }

    const advisories = parsed.advisories && typeof parsed.advisories === 'object'
        ? Object.values(parsed.advisories).reduce((acc, list) => acc + (Array.isArray(list) ? list.length : 0), 0)
        : null;

    const abandoned = parsed.abandoned && typeof parsed.abandoned === 'object'
        ? Object.keys(parsed.abandoned).length
        : null;

    return { advisories, abandoned };
}

function summarizeNpmAudit(parsed) {
    if (!parsed || typeof parsed !== 'object' || !parsed.metadata || !parsed.metadata.vulnerabilities) {
        return null;
    }

    return parsed.metadata.vulnerabilities;
}

async function runDailyAudit(state) {
    fs.mkdirSync(REPORT_DIR, { recursive: true });

    log('Running daily security scans for npm/composer/dependabot/code scanning alerts.');

    const npmRoot = await runCommand('npm', ['audit', '--json'], { cwd: REPO_ROOT, allowFailure: true });
    const npmMcp = await runCommand('npm', ['audit', '--json'], { cwd: MCP_DIR, allowFailure: true });
    const composer = await runCommand('composer', ['audit', '--format=json'], { cwd: REPO_ROOT, allowFailure: true });

    const repo = await getRepositorySlug();
    let dependabotAlerts = null;
    let codeScanningAlerts = null;

    if (repo && await hasCommand('gh')) {
        const dependabot = await runCommand('gh', ['api', `repos/${repo}/dependabot/alerts?state=open&per_page=100`], { allowFailure: true });
        const codeScanning = await runCommand('gh', ['api', `repos/${repo}/code-scanning/alerts?state=open&per_page=100`], { allowFailure: true });

        const parsedDependabot = parseJsonOrNull(dependabot.stdout);
        const parsedCodeScanning = parseJsonOrNull(codeScanning.stdout);

        dependabotAlerts = Array.isArray(parsedDependabot) ? parsedDependabot.length : null;
        codeScanningAlerts = Array.isArray(parsedCodeScanning) ? parsedCodeScanning.length : null;
    }

    const npmRootParsed = parseJsonOrNull(npmRoot.stdout);
    const npmMcpParsed = parseJsonOrNull(npmMcp.stdout);
    const composerParsed = parseJsonOrNull(composer.stdout);

    const report = {
        timestamp: new Date().toISOString(),
        repository: repo || null,
        audits: {
            npmRoot: {
                exitCode: npmRoot.code,
                summary: summarizeNpmAudit(npmRootParsed),
            },
            npmMcpServer: {
                exitCode: npmMcp.code,
                summary: summarizeNpmAudit(npmMcpParsed),
            },
            composer: {
                exitCode: composer.code,
                summary: summarizeComposerAudit(composerParsed),
            },
        },
        githubAlerts: {
            dependabotOpen: dependabotAlerts,
            codeScanningOpen: codeScanningAlerts,
        },
    };

    const reportName = `${report.timestamp.slice(0, 10)}.json`;
    fs.writeFileSync(path.join(REPORT_DIR, reportName), JSON.stringify(report, null, 2));

    log(`Daily scan report written to security-reports/${reportName}`);

    state.lastDailyAuditAt = Date.now();
    saveState(state);
}

async function stashIfDirty() {
    const status = await runCommand('git', ['status', '--porcelain'], { cwd: REPO_ROOT });
    if (!status.stdout.trim()) {
        return false;
    }

    log('Working tree has uncommitted changes — stashing before weekly automation.');
    await runCommand('git', ['stash', 'push', '--include-untracked', '--message', 'mcp-automation: pre-weekly stash'], { cwd: REPO_ROOT });
    return true;
}

async function popStash() {
    log('Restoring stashed changes.');
    const result = await runCommand('git', ['stash', 'pop'], { cwd: REPO_ROOT, allowFailure: true });
    if (result.code !== 0) {
        log(`WARNING: git stash pop failed — stash may need manual recovery. ${result.stderr.trim()}`);
    }
}

// ---------------------------------------------------------------------------
// Pre-push repo health checks
// ---------------------------------------------------------------------------

/**
 * Returns a list of open PR titles/numbers that are not the weekly automation
 * PR itself, plus any PRs whose head branches have conflicts with main.
 */
async function detectUnresolvedPrs(repositorySlug) {
    const result = await runCommand('gh', [
        'pr', 'list',
        '--repo', repositorySlug,
        '--base', 'main',
        '--state', 'open',
        '--json', 'number,title,headRefName,mergeable',
        '--limit', '100',
    ], { allowFailure: true });

    if (result.code !== 0) {
        log(`[pre-push] WARNING: could not list open PRs — ${result.stderr.trim()}`);
        return [];
    }

    const prs = parseJsonOrNull(result.stdout);
    if (!Array.isArray(prs)) {
        return [];
    }

    const issues = [];
    for (const pr of prs) {
        if (pr.headRefName === WEEKLY_BRANCH) {
            continue; // our own PR — expected
        }
        if (pr.mergeable === 'CONFLICTING') {
            issues.push(`PR #${pr.number} "${pr.title}" has merge conflicts with main`);
        } else {
            issues.push(`PR #${pr.number} "${pr.title}" is unresolved (branch: ${pr.headRefName})`);
        }
    }

    return issues;
}

/**
 * Returns remote branches that diverge from main by more than
 * STALE_BRANCH_DAYS_THRESHOLD days with no recent activity.
 */
const STALE_BRANCH_DAYS_THRESHOLD = 30;

async function detectStaleBranches() {
    await runCommand('git', ['fetch', '--prune', 'origin'], { cwd: REPO_ROOT, allowFailure: true });

    const result = await runCommand('git', [
        'for-each-ref',
        '--sort=committerdate',
        '--format=%(refname:short) %(committerdate:iso8601)',
        'refs/remotes/origin/',
    ], { cwd: REPO_ROOT, allowFailure: true });

    if (result.code !== 0 || !result.stdout.trim()) {
        return [];
    }

    const cutoff = Date.now() - STALE_BRANCH_DAYS_THRESHOLD * 24 * 60 * 60 * 1000;
    const stale = [];

    for (const line of result.stdout.split('\n')) {
        const match = line.trim().match(/^(\S+)\s+(\S+)/);
        if (!match) {
            continue;
        }

        const [, ref, dateStr] = match;
        // Skip HEAD pseudo-ref and main
        if (ref === 'origin/HEAD' || ref === 'origin/main') {
            continue;
        }

        const branchDate = new Date(dateStr).getTime();
        if (!Number.isNaN(branchDate) && branchDate < cutoff) {
            const agedays = Math.floor((Date.now() - branchDate) / (24 * 60 * 60 * 1000));
            stale.push(`${ref} (last commit ${agedays} days ago)`);
        }
    }

    return stale;
}

/**
 * Checks whether the weekly automation branch already exists on origin and
 * has any failing or pending GitHub Actions runs. Throws if a previous push
 * to the branch left failing checks that were never resolved, preventing a
 * silent force-push over broken state.
 */
async function assertWeeklyBranchNotFailing(repositorySlug) {
    const branchCheck = await runCommand('git', [
        'ls-remote', '--exit-code', 'origin', `refs/heads/${WEEKLY_BRANCH}`,
    ], { cwd: REPO_ROOT, allowFailure: true });

    if (branchCheck.code !== 0) {
        return; // branch does not exist remotely yet — nothing to check
    }

    const runs = await runCommand('gh', [
        'api',
        `repos/${repositorySlug}/actions/runs?branch=${encodeURIComponent(WEEKLY_BRANCH)}&per_page=5`,
        '--jq', '.workflow_runs[] | {status,conclusion,name}',
    ], { allowFailure: true });

    if (runs.code !== 0 || !runs.stdout.trim()) {
        return; // API unavailable or no runs — proceed
    }

    const failing = [];
    for (const line of runs.stdout.trim().split('\n')) {
        const obj = parseJsonOrNull(line);
        if (obj && obj.conclusion === 'failure') {
            failing.push(obj.name || 'unknown workflow');
        }
    }

    if (failing.length) {
        throw new Error(
            `Weekly branch "${WEEKLY_BRANCH}" has unresolved failing workflow run(s): ${failing.join(', ')}. ` +
            'Resolve or close those failures before the next automated push.'
        );
    }
}

/**
 * Polls until at least one check run appears on the PR or the timeout
 * (default 90 s) is reached. Prevents gh pr checks --watch from exiting
 * immediately when GitHub Actions has not yet queued checks after the push.
 */
async function waitForChecksToBeQueued(repositorySlug, prNumber, timeoutMs = 90_000) {
    const deadline = Date.now() + timeoutMs;
    log('[checks] Waiting for workflow checks to be queued …');

    while (Date.now() < deadline) {
        const result = await runCommand('gh', [
            'pr', 'checks', String(prNumber),
            '--repo', repositorySlug,
            '--json', 'name,state',
        ], { allowFailure: true });

        if (result.code === 0) {
            const checks = parseJsonOrNull(result.stdout);
            if (Array.isArray(checks) && checks.length > 0) {
                log(`[checks] ${checks.length} check(s) queued.`);
                return;
            }
        }

        await new Promise(resolve => setTimeout(resolve, 5_000));
    }

    log('[checks] WARNING: no checks were queued within the timeout window. Proceeding anyway.');
}

/**
 * After gh pr checks --watch exits, reads the final state of every check
 * on the PR and throws if any are not in a passing or skipped terminal state.
 */
async function assertAllChecksPassed(repositorySlug, prNumber) {
    const result = await runCommand('gh', [
        'pr', 'checks', String(prNumber),
        '--repo', repositorySlug,
        '--json', 'name,state,conclusion',
    ], { allowFailure: true });

    if (result.code !== 0) {
        log('[checks] WARNING: could not read final check states — assuming pass.');
        return;
    }

    const checks = parseJsonOrNull(result.stdout);
    if (!Array.isArray(checks) || checks.length === 0) {
        log('[checks] WARNING: no check data returned for final verification.');
        return;
    }

    const PASSING = new Set(['pass', 'success', 'skipped', 'neutral']);
    const failed = checks.filter(c => c.state && !PASSING.has(c.state.toLowerCase()));

    if (failed.length) {
        const details = failed.map(c => `${c.name} (${c.state})`).join(', ');
        throw new Error(`${failed.length} workflow check(s) did not pass after push: ${details}`);
    }

    log(`[checks] All ${checks.length} workflow check(s) passed.`);
}

/**
 * Runs all pre-push checks. Returns a summary object with findings.
 * Never throws — issues are warnings that are logged and included in the PR body,
 * but only a failing check for the automation branch itself will abort the push.
 */
async function runPrePushChecks(repositorySlug) {
    log('[pre-push] Running repository health checks before push …');

    const [unresolvedPrs, staleBranches] = await Promise.all([
        detectUnresolvedPrs(repositorySlug),
        detectStaleBranches(),
    ]);

    if (unresolvedPrs.length) {
        log(`[pre-push] ${unresolvedPrs.length} unresolved/conflicting PR(s) detected:`);
        for (const item of unresolvedPrs) {
            log(`  - ${item}`);
        }
    } else {
        log('[pre-push] No unresolved PRs detected.');
    }

    if (staleBranches.length) {
        log(`[pre-push] ${staleBranches.length} stale remote branch(es) detected (>${STALE_BRANCH_DAYS_THRESHOLD} days):`);
        for (const b of staleBranches) {
            log(`  - ${b}`);
        }
    } else {
        log('[pre-push] No stale remote branches detected.');
    }

    return { unresolvedPrs, staleBranches };
}

async function runWorkflowEquivalentChecks() {
    log('Running local workflow-equivalent checks before push.');

    await runCommand('composer', ['validate'], { cwd: REPO_ROOT });
    await runCommand(path.join(REPO_ROOT, 'vendor/bin/pint'), [], { cwd: REPO_ROOT });
    await runCommand('npm', ['run', 'build'], { cwd: REPO_ROOT });
    await runCommand(path.join(REPO_ROOT, 'vendor/bin/phpunit'), [], { cwd: REPO_ROOT });
}

async function upsertWeeklyPr(repositorySlug, prFindings) {
    const findingsSection = [];

    if (prFindings && prFindings.unresolvedPrs && prFindings.unresolvedPrs.length) {
        findingsSection.push('');
        findingsSection.push('### Unresolved PRs detected at push time');
        for (const item of prFindings.unresolvedPrs) {
            findingsSection.push(`- ${item}`);
        }
    }

    if (prFindings && prFindings.staleBranches && prFindings.staleBranches.length) {
        findingsSection.push('');
        findingsSection.push(`### Stale remote branches (>${STALE_BRANCH_DAYS_THRESHOLD} days inactive)`);
        for (const b of prFindings.staleBranches) {
            findingsSection.push(`- ${b}`);
        }
    }

    const body = [
        'Automated weekly security refresh generated by MCP scheduler.',
        '',
        '- Runs npm/composer security remediation',
        '- Applies dependency updates for detected vulnerability paths',
        '- Runs local workflow-equivalent checks before push',
        '- Performs pre-push repo health scan (unresolved PRs, dirty branches, stale branches)',
        ...findingsSection,
    ].join('\n');

    const prList = await runCommand('gh', [
        'pr', 'list',
        '--repo', repositorySlug,
        '--head', WEEKLY_BRANCH,
        '--base', 'main',
        '--state', 'open',
        '--json', 'number,title',
    ]);

    const parsed = parseJsonOrNull(prList.stdout);
    const existing = Array.isArray(parsed) && parsed.length > 0 ? parsed[0] : null;

    if (existing) {
        await runCommand('gh', ['pr', 'edit', String(existing.number), '--repo', repositorySlug, '--title', WEEKLY_COMMIT_TITLE, '--body', body]);
        return existing.number;
    }

    await runCommand('gh', [
        'pr', 'create',
        '--repo', repositorySlug,
        '--base', 'main',
        '--head', WEEKLY_BRANCH,
        '--title', WEEKLY_COMMIT_TITLE,
        '--body', body,
    ]);

    const created = await runCommand('gh', [
        'pr', 'list',
        '--repo', repositorySlug,
        '--head', WEEKLY_BRANCH,
        '--base', 'main',
        '--state', 'open',
        '--json', 'number',
        '--jq', '.[0].number',
    ]);

    const prNumber = Number(created.stdout.trim());
    if (!prNumber) {
        throw new Error('Failed to locate newly created weekly security PR.');
    }

    return prNumber;
}

async function runWeeklyUpdate(state) {
    log('Starting weekly security update pipeline.');

    if (!await hasCommand('gh')) {
        throw new Error('GitHub CLI (gh) is required for weekly PR automation.');
    }

    const repositorySlug = await getRepositorySlug();
    if (!repositorySlug) {
        throw new Error('Unable to determine linked GitHub repository. Set GITHUB_REPOSITORY or authenticate gh CLI.');
    }

    const stashed = await stashIfDirty();

    try {
        await runCommand('git', ['fetch', 'origin', 'main'], { cwd: REPO_ROOT });
        await runCommand('git', ['checkout', '-B', WEEKLY_BRANCH, 'origin/main'], { cwd: REPO_ROOT });

        log('Applying automated security fixes and dependency updates.');
        await runCommand('npm', ['audit', 'fix'], { cwd: REPO_ROOT, allowFailure: true });
        await runCommand('composer', ['update', '--with-all-dependencies', '--no-interaction'], { cwd: REPO_ROOT, allowFailure: true });
        await runCommand('npm', ['audit', 'fix'], { cwd: MCP_DIR, allowFailure: true });

        await runWorkflowEquivalentChecks();

        // Guard: abort if the weekly branch already has unresolved failing runs
        await assertWeeklyBranchNotFailing(repositorySlug);

        // Pre-push repo health scan (warnings only — logged into PR body)
        const prFindings = await runPrePushChecks(repositorySlug);

        await runCommand('git', ['add', '-A'], { cwd: REPO_ROOT });
        const staged = await runCommand('git', ['diff', '--cached', '--name-only'], { cwd: REPO_ROOT });

        if (!staged.stdout.trim()) {
            log('No weekly security changes detected after remediation.');
        } else {
            await runCommand('git', ['commit', '-m', WEEKLY_COMMIT_TITLE], { cwd: REPO_ROOT });
            await runCommand('git', ['push', '--set-upstream', 'origin', WEEKLY_BRANCH, '--force-with-lease'], { cwd: REPO_ROOT });

            const prNumber = await upsertWeeklyPr(repositorySlug, prFindings);

            // Wait for GitHub Actions to queue checks before watching
            await waitForChecksToBeQueued(repositorySlug, prNumber);

            // Watch all checks — gh pr checks --watch exits non-zero on any failure
            await runCommand('gh', ['pr', 'checks', String(prNumber), '--repo', repositorySlug, '--watch', '--interval', '20']);

            // Final explicit verification: every check must be in a passing state
            await assertAllChecksPassed(repositorySlug, prNumber);

            log(`Weekly security PR #${prNumber} — all workflow checks passed.`);
        }

        state.lastWeeklyPushAt = Date.now();
        saveState(state);
    } finally {
        await runCommand('git', ['checkout', 'main'], { cwd: REPO_ROOT, allowFailure: true });
        if (stashed) {
            await popStash();
        }
    }
}

async function main() {
    const state = loadState();

    if (isDue(state.lastDailyAuditAt, ONE_DAY_MS)) {
        await runDailyAudit(state);
    } else {
        log('Daily security scan is not due yet.');
    }

    if (isDue(state.lastWeeklyPushAt, ONE_WEEK_MS)) {
        await runWeeklyUpdate(state);
    } else {
        log('Weekly PR push is not due yet.');
    }
}

main().catch((error) => {
    log(`Automation failed: ${error.message}`);
    process.exit(1);
});
