'use strict';

const { execFile } = require('child_process');
const path = require('path');

const REPO_ROOT = path.resolve(__dirname, '..');

function run(file, args, options = {}) {
    return new Promise((resolve) => {
        execFile(file, args, {
            cwd: options.cwd || REPO_ROOT,
            env: process.env,
            maxBuffer: 10 * 1024 * 1024,
        }, (error, stdout, stderr) => {
            resolve({
                ok: !error,
                code: error && typeof error.code === 'number' ? error.code : 0,
                stdout: (stdout || '').trim(),
                stderr: (stderr || '').trim(),
            });
        });
    });
}

async function hasCommand(name) {
    const result = await run('which', [name]);
    return result.ok;
}

function line(status, message) {
    const icon = status ? '[PASS]' : '[FAIL]';
    console.log(`${icon} ${message}`);
}

function warn(message) {
    console.log(`[WARN] ${message}`);
}

async function main() {
    let failed = false;

    const commands = ['git', 'node', 'npm', 'composer', 'gh'];
    for (const command of commands) {
        const present = await hasCommand(command);
        line(present, `${command} is available`);
        if (!present && command !== 'composer') {
            failed = true;
        }
    }

    const ghAuth = await run('gh', ['auth', 'status'], { cwd: REPO_ROOT });
    line(ghAuth.ok, 'gh authentication is configured');
    if (!ghAuth.ok) {
        failed = true;
    }

    const repoFromEnv = process.env.GITHUB_REPOSITORY || '';
    let repo = repoFromEnv;
    if (!repo) {
        const repoView = await run('gh', ['repo', 'view', '--json', 'nameWithOwner', '--jq', '.nameWithOwner'], { cwd: REPO_ROOT });
        if (repoView.ok && repoView.stdout) {
            repo = repoView.stdout;
        }
    }

    const repoValid = Boolean(repo && /^[a-zA-Z0-9][a-zA-Z0-9_.-]*\/[a-zA-Z0-9][a-zA-Z0-9_.-]*$/.test(repo));
    line(repoValid, 'linked GitHub repository is detected');
    if (!repoValid) {
        failed = true;
    }

    if (repoValid) {
        const actionsAccess = await run('gh', ['api', `repos/${repo}/actions/runs?per_page=1`], { cwd: REPO_ROOT });
        line(actionsAccess.ok, 'actions/checks API is accessible');
        if (!actionsAccess.ok) {
            warn('Actions API check failed; weekly PR check watching may fail.');
        }

        const dependabotAccess = await run('gh', ['api', `repos/${repo}/dependabot/alerts?state=open&per_page=1`], { cwd: REPO_ROOT });
        line(dependabotAccess.ok, 'dependabot alerts API is accessible');
        if (!dependabotAccess.ok) {
            warn('Dependabot alerts API unavailable; daily alert count may be null.');
        }

        const codeScanningAccess = await run('gh', ['api', `repos/${repo}/code-scanning/alerts?state=open&per_page=1`], { cwd: REPO_ROOT });
        line(codeScanningAccess.ok, 'code scanning alerts API is accessible');
        if (!codeScanningAccess.ok) {
            warn('Code scanning alerts API unavailable; daily alert count may be null.');
        }
    }

    const cleanTree = await run('git', ['status', '--porcelain'], { cwd: REPO_ROOT });
    const clean = cleanTree.ok && cleanTree.stdout.length === 0;
    if (clean) {
        line(true, 'working tree is clean');
    } else {
        warn('Working tree has uncommitted changes \u2014 weekly automation will auto-stash and restore them.');
    }

    const hasApiKey = Boolean(process.env.MCP_API_KEY);
    if (hasApiKey) {
        line(true, 'MCP_API_KEY is set');
    } else {
        warn('MCP_API_KEY is not set in this shell. Daemon scripts default to a local dev key.');
    }

    if (failed) {
        process.exit(1);
    }
}

main().catch((error) => {
    console.error(`[FAIL] readiness check crashed: ${error.message}`);
    process.exit(1);
});
