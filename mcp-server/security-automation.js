'use strict';

const { execFile } = require('child_process');
const fs = require('fs');
const path = require('path');

const REPO_ROOT = path.resolve(__dirname, '..');
const MCP_DIR = __dirname;
const STATE_FILE = path.join(MCP_DIR, 'security-automation-state.json');
const REPORT_DIR = path.join(MCP_DIR, 'security-reports');
const LOG_FILE = path.join(MCP_DIR, 'security-automation.log');
const LEARNING_FILE = path.join(MCP_DIR, 'automation-learning.json');
const POLICY_FILE = path.join(MCP_DIR, 'automation-policy.json');

const ONE_DAY_MS = 24 * 60 * 60 * 1000;
const ONE_WEEK_MS = 7 * ONE_DAY_MS;
const WEEKLY_BRANCH = 'automation/weekly-security-update';
const WEEKLY_COMMIT_TITLE = 'Weekly Security Update';
const HEALTH_BRANCH_PREFIX = 'automation/repo-health-fix';

function log(message) {
    const line = `[${new Date().toISOString()}] ${message}`;
    fs.appendFileSync(LOG_FILE, `${line}\n`);
    console.log(line);
}

function loadState() {
    try {
        return JSON.parse(fs.readFileSync(STATE_FILE, 'utf8'));
    } catch {
        return { lastDailyAuditAt: 0, lastWeeklyPushAt: 0, lastRepoHealthFixAt: 0 };
    }
}

function saveState(state) {
    fs.writeFileSync(STATE_FILE, JSON.stringify(state, null, 2));
}

function defaultPolicy() {
    return {
        profile: 'default',
        dailyAudit: true,
        repoHealth: true,
        weeklyUpdate: true,
        learning: { enabled: true },
        steps: {
            'composer-validate': true,
            'style-fix': true,
            'npm-audit-root': true,
            'npm-audit-mcp': true,
            'composer-audit': true,
            'composer-update': true,
            build: true,
            tests: true,
        },
        maintenance: {
            cleanupMergedBranches: true,
            reportOpenPrFailures: true,
        },
    };
}

function loadPolicyFile() {
    try {
        return JSON.parse(fs.readFileSync(POLICY_FILE, 'utf8'));
    } catch {
        return { profiles: { default: defaultPolicy() } };
    }
}

function resolvePolicy() {
    const policyFile = loadPolicyFile();
    const profileName = process.env.AUTOMATION_POLICY_PROFILE || 'default';
    const defaults = defaultPolicy();
    const profile = policyFile && policyFile.profiles && policyFile.profiles[profileName]
        ? policyFile.profiles[profileName]
        : {};

    return {
        ...defaults,
        ...profile,
        profile: profileName,
        learning: {
            ...defaults.learning,
            ...((profile && profile.learning) || {}),
        },
        steps: {
            ...defaults.steps,
            ...((profile && profile.steps) || {}),
        },
        maintenance: {
            ...defaults.maintenance,
            ...((profile && profile.maintenance) || {}),
        },
    };
}

function isStepEnabled(policy, stepId) {
    return !(policy && policy.steps && policy.steps[stepId] === false);
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
    let checks = null;

    for (let attempt = 1; attempt <= 3; attempt += 1) {
        const result = await runCommand('gh', [
            'pr', 'checks', String(prNumber),
            '--repo', repositorySlug,
            '--json', 'name,state,conclusion',
        ], { allowFailure: true });

        const parsed = parseJsonOrNull(result.stdout);
        if (result.code === 0 && Array.isArray(parsed) && parsed.length) {
            checks = parsed;
            break;
        }

        if (attempt < 3) {
            await new Promise(resolve => setTimeout(resolve, 4_000));
        }
    }

    if (!checks) {
        // Fallback: fetch aggregated status checks from PR view.
        const fallback = await runCommand('gh', [
            'pr', 'view', String(prNumber),
            '--repo', repositorySlug,
            '--json', 'statusCheckRollup',
        ], { allowFailure: true });

        const parsed = parseJsonOrNull(fallback.stdout);
        const rollup = parsed && parsed.statusCheckRollup;
        if (Array.isArray(rollup) && rollup.length) {
            checks = rollup.map(item => ({
                name: item.name || item.context || 'unknown-check',
                state: item.conclusion || item.state || item.status || 'unknown',
                conclusion: item.conclusion || item.state || item.status || 'unknown',
            }));
        }
    }

    if (!Array.isArray(checks) || checks.length === 0) {
        throw new Error('[checks] Could not verify final check states from GitHub after retries/fallback.');
    }

    const PASSING = new Set(['pass', 'success', 'skipped', 'neutral']);
    const failed = checks.filter(c => c.state && !PASSING.has(c.state.toLowerCase()));

    if (failed.length) {
        const details = failed.map(c => `${c.name} (${c.state})`).join(', ');
        throw new Error(`${failed.length} workflow check(s) did not pass after push: ${details}`);
    }

    log(`[checks] All ${checks.length} workflow check(s) passed.`);
}

async function listPrFailedChecks(repositorySlug, prNumber) {
    const result = await runCommand('gh', [
        'pr', 'checks', String(prNumber),
        '--repo', repositorySlug,
        '--json', 'name,state,conclusion',
    ], { allowFailure: true });

    if (result.code !== 0) {
        return [];
    }

    const checks = parseJsonOrNull(result.stdout);
    if (!Array.isArray(checks)) {
        return [];
    }

    const FAILING = new Set(['failure', 'error', 'failed', 'cancelled', 'timed_out']);
    return checks.filter(check => {
        const state = String(check.state || '').toLowerCase();
        const conclusion = String(check.conclusion || '').toLowerCase();
        return FAILING.has(state) || FAILING.has(conclusion);
    });
}

async function getPrManagedCommentIds(repositorySlug, prNumber, marker) {
    const result = await runCommand('gh', [
        'pr', 'view', String(prNumber),
        '--repo', repositorySlug,
        '--json', 'comments',
    ], { allowFailure: true });

    if (result.code !== 0) {
        return [];
    }

    const parsed = parseJsonOrNull(result.stdout);
    const comments = parsed && Array.isArray(parsed.comments) ? parsed.comments : [];
    return comments
        .filter(comment => comment && typeof comment.body === 'string' && comment.body.includes(marker))
        .map(comment => comment.id)
        .filter(Boolean);
}

async function deleteIssueComment(repositorySlug, commentId) {
    await runCommand('gh', [
        'api', '--method', 'DELETE',
        `repos/${repositorySlug}/issues/comments/${commentId}`,
    ], { allowFailure: true });
}

async function upsertManagedPrComment(repositorySlug, prNumber, marker, body) {
    const existingIds = await getPrManagedCommentIds(repositorySlug, prNumber, marker);

    if (existingIds.length > 0) {
        await runCommand('gh', [
            'api', '--method', 'PATCH',
            `repos/${repositorySlug}/issues/comments/${existingIds[0]}`,
            '--field', `body=${body}`,
        ], { allowFailure: true });

        // Guardrail: remove accidental duplicates and keep only one managed comment.
        for (const duplicateId of existingIds.slice(1)) {
            await deleteIssueComment(repositorySlug, duplicateId);
        }

        return;
    }

    await runCommand('gh', [
        'pr', 'comment', String(prNumber),
        '--repo', repositorySlug,
        '--body', body,
    ], { allowFailure: true });
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

async function runWorkflowEquivalentChecks(policy) {
    log('Running local workflow-equivalent checks before push.');

    if (isStepEnabled(policy, 'composer-validate')) {
        await runCommand('composer', ['validate'], { cwd: REPO_ROOT });
    }

    if (isStepEnabled(policy, 'style-fix')) {
        await runCommand(path.join(REPO_ROOT, 'vendor/bin/pint'), [], { cwd: REPO_ROOT });
    }

    if (isStepEnabled(policy, 'build')) {
        await runCommand('npm', ['run', 'build'], { cwd: REPO_ROOT });
    }

    if (isStepEnabled(policy, 'tests')) {
        await runCommand(path.join(REPO_ROOT, 'vendor/bin/phpunit'), [], { cwd: REPO_ROOT });
    }
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

async function runWeeklyUpdate(state, policy) {
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
        if (isStepEnabled(policy, 'npm-audit-root')) {
            await runCommand('npm', ['audit', 'fix'], { cwd: REPO_ROOT, allowFailure: true });
        }
        if (isStepEnabled(policy, 'composer-update')) {
            await runCommand('composer', ['update', '--with-all-dependencies', '--no-interaction'], { cwd: REPO_ROOT, allowFailure: true });
        }
        if (isStepEnabled(policy, 'npm-audit-mcp')) {
            await runCommand('npm', ['audit', 'fix'], { cwd: MCP_DIR, allowFailure: true });
        }

        await runWorkflowEquivalentChecks(policy);

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

// ---------------------------------------------------------------------------
// Repo-wide health automation with self-learning
// ---------------------------------------------------------------------------

function loadLearning() {
    try {
        const parsed = JSON.parse(fs.readFileSync(LEARNING_FILE, 'utf8'));
        if (!parsed || typeof parsed !== 'object') {
            return { failures: {}, runs: 0, lastRunAt: null };
        }

        return {
            failures: parsed.failures && typeof parsed.failures === 'object' ? parsed.failures : {},
            runs: Number.isFinite(parsed.runs) ? parsed.runs : 0,
            lastRunAt: typeof parsed.lastRunAt === 'string' ? parsed.lastRunAt : null,
        };
    } catch {
        return { failures: {}, runs: 0, lastRunAt: null };
    }
}

function saveLearning(learning) {
    fs.writeFileSync(LEARNING_FILE, JSON.stringify(learning, null, 2));
}

function detectFailureCategories(output) {
    const text = String(output || '').toLowerCase();
    const categories = [];

    if (/pint|psr-12|style issues|code style/.test(text)) categories.push('style');
    if (/phpunit|assert|tests?:|failures!|exception/.test(text)) categories.push('tests');
    if (/npm audit|vulnerab|critical|high/.test(text)) categories.push('npm-audit');
    if (/composer audit|advisories|abandoned/.test(text)) categories.push('composer-audit');
    if (/npm run build|vite|rollup|build failed/.test(text)) categories.push('build');
    if (/composer validate|composer\.json|schema/.test(text)) categories.push('composer-validate');
    if (/permission denied|eacces|operation not permitted/.test(text)) categories.push('permissions');

    if (!categories.length) {
        categories.push('unknown');
    }

    return categories;
}

function learnFromStage(learning, stage, result) {
    const now = new Date().toISOString();
    const exitCode = result && Number.isFinite(result.code) ? result.code : 1;
    const output = `${result?.stdout || ''}\n${result?.stderr || ''}`;

    if (exitCode === 0) {
        return;
    }

    for (const category of detectFailureCategories(output)) {
        if (!learning.failures[category]) {
            learning.failures[category] = {
                count: 0,
                lastStage: stage,
                lastSeenAt: now,
            };
        }

        learning.failures[category].count += 1;
        learning.failures[category].lastStage = stage;
        learning.failures[category].lastSeenAt = now;
    }
}

function getPrioritizedHealthSteps(learning) {
    const frequency = learning.failures || {};
    const score = category => (frequency[category] && frequency[category].count) || 0;

    const steps = [
        { id: 'composer-validate', category: 'composer-validate' },
        { id: 'style-fix', category: 'style' },
        { id: 'npm-audit-root', category: 'npm-audit' },
        { id: 'npm-audit-mcp', category: 'npm-audit' },
        { id: 'composer-audit', category: 'composer-audit' },
        { id: 'composer-update', category: 'composer-audit' },
        { id: 'build', category: 'build' },
        { id: 'tests', category: 'tests' },
    ];

    // Learned behavior: run historically failing categories earlier.
    return steps.sort((a, b) => score(b.category) - score(a.category));
}

function learnFromFailedChecks(learning, failedChecks) {
    if (!Array.isArray(failedChecks) || !failedChecks.length) {
        return;
    }

    const now = new Date().toISOString();
    for (const check of failedChecks) {
        const name = String(check.name || 'unknown-workflow').toLowerCase();
        let category = 'workflow-unknown';

        if (name.includes('lint') || name.includes('pint')) category = 'style';
        else if (name.includes('test')) category = 'tests';
        else if (name.includes('codeql') || name.includes('ossar') || name.includes('security')) category = 'security-workflow';
        else if (name.includes('build') || name.includes('deploy')) category = 'build';

        if (!learning.failures[category]) {
            learning.failures[category] = { count: 0, lastStage: 'workflow-checks', lastSeenAt: now };
        }

        learning.failures[category].count += 1;
        learning.failures[category].lastStage = `workflow:${name}`;
        learning.failures[category].lastSeenAt = now;
    }
}

async function cleanupMergedBranches(repositorySlug) {
    const merged = await runCommand('gh', [
        'pr', 'list', '--repo', repositorySlug, '--state', 'merged', '--json', 'headRefName,number', '--limit', '100',
    ], { allowFailure: true });

    if (merged.code !== 0) {
        return [];
    }

    const prs = parseJsonOrNull(merged.stdout);
    if (!Array.isArray(prs) || !prs.length) {
        return [];
    }

    const removed = [];
    for (const pr of prs) {
        const branch = pr.headRefName;
        if (!branch || branch === 'main' || branch === WEEKLY_BRANCH || branch.startsWith(HEALTH_BRANCH_PREFIX)) {
            continue;
        }

        const exists = await runCommand('git', ['ls-remote', '--exit-code', '--heads', 'origin', branch], { cwd: REPO_ROOT, allowFailure: true });
        if (exists.code !== 0) {
            continue;
        }

        const del = await runCommand('gh', [
            'api', '--method', 'DELETE', `repos/${repositorySlug}/git/refs/heads/${encodeURIComponent(branch)}`,
        ], { allowFailure: true });

        if (del.code === 0) {
            removed.push(branch);
        }
    }

    return removed;
}

async function reportOpenPrFailures(repositorySlug) {
    const marker = '<!-- mcp-security-automation -->';
    const openPrs = await runCommand('gh', [
        'pr', 'list', '--repo', repositorySlug, '--state', 'open', '--json', 'number,title', '--limit', '50',
    ], { allowFailure: true });

    if (openPrs.code !== 0) {
        return 0;
    }

    const prs = parseJsonOrNull(openPrs.stdout);
    if (!Array.isArray(prs) || !prs.length) {
        return 0;
    }

    let count = 0;
    for (const pr of prs) {
        const failures = await listPrFailedChecks(repositorySlug, pr.number);
        if (!failures.length) {
            const managedCommentIds = await getPrManagedCommentIds(repositorySlug, pr.number, marker);
            for (const id of managedCommentIds) {
                await deleteIssueComment(repositorySlug, id);
            }
            continue;
        }

        const body = [
            marker,
            `Automated repo health check found ${failures.length} failing workflow check(s):`,
            ...failures.map(item => `- ${item.name}: ${item.state || item.conclusion}`),
        ].join('\n');

        await upsertManagedPrComment(repositorySlug, pr.number, marker, body);
        count += 1;
    }

    return count;
}

async function executeHealthStep(stepId) {
    switch (stepId) {
    case 'composer-validate':
        return runCommand('composer', ['validate'], { cwd: REPO_ROOT, allowFailure: true });
    case 'style-fix':
        return runCommand(path.join(REPO_ROOT, 'vendor/bin/pint'), [], { cwd: REPO_ROOT, allowFailure: true });
    case 'npm-audit-root':
        return runCommand('npm', ['audit', 'fix'], { cwd: REPO_ROOT, allowFailure: true });
    case 'npm-audit-mcp':
        return runCommand('npm', ['audit', 'fix'], { cwd: MCP_DIR, allowFailure: true });
    case 'composer-audit':
        return runCommand('composer', ['audit', '--format=json'], { cwd: REPO_ROOT, allowFailure: true });
    case 'composer-update':
        return runCommand('composer', ['update', '--with-all-dependencies', '--no-interaction'], { cwd: REPO_ROOT, allowFailure: true });
    case 'build':
        return runCommand('npm', ['run', 'build'], { cwd: REPO_ROOT, allowFailure: true });
    case 'tests':
        return runCommand(path.join(REPO_ROOT, 'vendor/bin/phpunit'), [], { cwd: REPO_ROOT, allowFailure: true });
    default:
        return { code: 0, stdout: '', stderr: '' };
    }
}

async function upsertHealthPr(repositorySlug, branchName, summary) {
    const title = `Automated Repo Health Update (${new Date().toISOString().slice(0, 10)})`;
    const body = [
        'Automated repo-wide health remediation generated by MCP security automation.',
        '',
        '### Scope',
        '- Security remediations (npm/composer)',
        '- Style/lint remediations (Pint)',
        '- Build and test verification',
        '- Self-learning prioritization based on historical failures',
        '',
        '### Run Summary',
        ...summary.map(line => `- ${line}`),
    ].join('\n');

    const prList = await runCommand('gh', [
        'pr', 'list',
        '--repo', repositorySlug,
        '--head', branchName,
        '--base', 'main',
        '--state', 'open',
        '--json', 'number',
    ], { allowFailure: true });

    const parsed = parseJsonOrNull(prList.stdout);
    const existing = Array.isArray(parsed) && parsed.length ? parsed[0] : null;

    if (existing) {
        await runCommand('gh', [
            'pr', 'edit', String(existing.number), '--repo', repositorySlug,
            '--title', title, '--body', body,
        ]);
        return existing.number;
    }

    await runCommand('gh', [
        'pr', 'create',
        '--repo', repositorySlug,
        '--base', 'main',
        '--head', branchName,
        '--title', title,
        '--body', body,
    ]);

    const created = await runCommand('gh', [
        'pr', 'list',
        '--repo', repositorySlug,
        '--head', branchName,
        '--base', 'main',
        '--state', 'open',
        '--json', 'number',
        '--jq', '.[0].number',
    ]);

    const prNumber = Number(created.stdout.trim());
    if (!prNumber) {
        throw new Error('Failed to locate newly created repo health PR.');
    }

    return prNumber;
}

async function runRepoHealthResolution(state, policy) {
    log('Starting repo health resolution pipeline.');

    if (!await hasCommand('gh')) {
        throw new Error('GitHub CLI (gh) is required for repo health automation.');
    }

    const repositorySlug = await getRepositorySlug();
    if (!repositorySlug) {
        throw new Error('Unable to determine repository slug for repo health automation.');
    }

    const learningEnabled = !(policy && policy.learning && policy.learning.enabled === false);
    const learning = learningEnabled ? loadLearning() : { failures: {}, runs: 0, lastRunAt: null };
    learning.runs += 1;
    learning.lastRunAt = new Date().toISOString();

    const branchName = `${HEALTH_BRANCH_PREFIX}-${new Date().toISOString().slice(0, 10)}`;
    const stashed = await stashIfDirty();

    let pushedPrNumber = null;

    try {
        await runCommand('git', ['fetch', 'origin', 'main'], { cwd: REPO_ROOT });
        await runCommand('git', ['checkout', '-B', branchName, 'origin/main'], { cwd: REPO_ROOT });

        const orderedSteps = getPrioritizedHealthSteps(learning).filter(step => isStepEnabled(policy, step.id));
        const summary = [];

        if (!orderedSteps.length) {
            log('[health] All repo-health steps are disabled by policy.');
        }

        for (const step of orderedSteps) {
            log(`[health] Running step: ${step.id}`);
            const result = await executeHealthStep(step.id);
            if (learningEnabled) {
                learnFromStage(learning, step.id, result);
            }

            if (result.code === 0) {
                summary.push(`${step.id}: pass`);
            } else {
                summary.push(`${step.id}: non-zero exit (${result.code})`);
            }
        }

        if (learningEnabled) {
            saveLearning(learning);
        }

        await runCommand('git', ['add', '-A'], { cwd: REPO_ROOT });
        const staged = await runCommand('git', ['diff', '--cached', '--name-only'], { cwd: REPO_ROOT });

        if (!staged.stdout.trim()) {
            log('[health] No repository-wide fixes were needed.');
            state.lastRepoHealthFixAt = Date.now();
            saveState(state);
            return;
        }

        await runCommand('git', ['commit', '-m', 'Automated repository health remediation'], { cwd: REPO_ROOT });
        await runCommand('git', ['push', '--set-upstream', 'origin', branchName, '--force-with-lease'], { cwd: REPO_ROOT });

        const prNumber = await upsertHealthPr(repositorySlug, branchName, summary);
        pushedPrNumber = prNumber;

        await waitForChecksToBeQueued(repositorySlug, prNumber);
        await runCommand('gh', ['pr', 'checks', String(prNumber), '--repo', repositorySlug, '--watch', '--interval', '20']);
        await assertAllChecksPassed(repositorySlug, prNumber);

        log(`[health] Repo health PR #${prNumber} — all workflow checks passed.`);

        state.lastRepoHealthFixAt = Date.now();
        saveState(state);
    } finally {
        await runCommand('git', ['checkout', 'main'], { cwd: REPO_ROOT, allowFailure: true });
        if (stashed) {
            await popStash();
        }
    }

    if (pushedPrNumber) {
        const failedChecks = await listPrFailedChecks(repositorySlug, pushedPrNumber);
        if (learningEnabled && failedChecks.length) {
            learnFromFailedChecks(learning, failedChecks);
            saveLearning(learning);
        }
    }

    if (!(policy && policy.maintenance && policy.maintenance.cleanupMergedBranches === false)) {
        const removedBranches = await cleanupMergedBranches(repositorySlug);
        if (removedBranches.length) {
            log(`[health] Removed ${removedBranches.length} merged branch(es).`);
        }
    }

    if (!(policy && policy.maintenance && policy.maintenance.reportOpenPrFailures === false)) {
        const reported = await reportOpenPrFailures(repositorySlug);
        if (reported) {
            log(`[health] Reported failing workflow checks on ${reported} open PR(s).`);
        }
    }
}

async function main() {
    const state = loadState();
    const policy = resolvePolicy();

    log(`Loaded automation policy profile: ${policy.profile}`);

    if (!policy.dailyAudit) {
        log('Daily security scan disabled by policy.');
    } else if (isDue(state.lastDailyAuditAt, ONE_DAY_MS)) {
        await runDailyAudit(state);
    } else {
        log('Daily security scan is not due yet.');
    }

    if (!policy.repoHealth) {
        log('Repo health resolution disabled by policy.');
    } else if (isDue(state.lastRepoHealthFixAt, ONE_DAY_MS)) {
        await runRepoHealthResolution(state, policy);
    } else {
        log('Repo health resolution is not due yet.');
    }

    if (!policy.weeklyUpdate) {
        log('Weekly PR push disabled by policy.');
    } else if (isDue(state.lastWeeklyPushAt, ONE_WEEK_MS)) {
        await runWeeklyUpdate(state, policy);
    } else {
        log('Weekly PR push is not due yet.');
    }
}

main().catch((error) => {
    log(`Automation failed: ${error.message}`);
    process.exit(1);
});
