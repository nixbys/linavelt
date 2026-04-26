/**
 * MCP Maintenance Scheduler
 *
 * Responsibilities:
 *  - Health check      : every 5 minutes  — GET /health
 *  - Security pipeline : every 24 hours   — local automation script
 *  - Weekly push/PR    : handled by security-automation.js once every 7 days
 *
 * Schedule state is persisted in scheduler-state.json so intervals survive
 * restarts without triggering unnecessary work.
 */

const http = require('http');
const fs   = require('fs');
const path = require('path');
const { execFile } = require('child_process');

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------
const MCP_HOST    = (process.env.MCP_HOST || 'http://localhost:4000');
const MCP_API_KEY = process.env.MCP_API_KEY || '';
const STATE_FILE  = path.join(__dirname, 'scheduler-state.json');
const LOG_FILE    = path.join(__dirname, 'scheduler.log');
const SECURITY_AUTOMATION_SCRIPT = path.join(__dirname, 'security-automation.js');

const INTERVALS = {
    healthCheck: 5 * 60 * 1000,         // 5 minutes
    security:   24 * 60 * 60 * 1000     // 24 hours
};

let securityRunInProgress = false;

// ---------------------------------------------------------------------------
// Utilities
// ---------------------------------------------------------------------------
function log(message) {
    const entry = `[${new Date().toISOString()}] ${message}\n`;
    fs.appendFileSync(LOG_FILE, entry);
    process.stdout.write(entry);
}

function loadState() {
    try {
        return JSON.parse(fs.readFileSync(STATE_FILE, 'utf8'));
    } catch {
        return { lastSecurityRun: 0 };
    }
}

function saveState(state) {
    fs.writeFileSync(STATE_FILE, JSON.stringify(state, null, 2));
}

// ---------------------------------------------------------------------------
// HTTP helpers
// ---------------------------------------------------------------------------
function request(method, urlStr) {
    return new Promise((resolve, reject) => {
        const url = new URL(urlStr);
        const options = {
            hostname: url.hostname,
            port:     url.port || 80,
            path:     url.pathname,
            method,
            headers:  {
                'Content-Type': 'application/json',
                'X-Api-Key':    MCP_API_KEY
            }
        };

        const req = http.request(options, res => {
            let body = '';
            res.on('data', chunk => { body += chunk; });
            res.on('end', () => {
                try { resolve({ status: res.statusCode, body: JSON.parse(body) }); }
                catch { resolve({ status: res.statusCode, body }); }
            });
        });

        req.on('error', reject);
        req.end();
    });
}

const get = url => request('GET', url);

// ---------------------------------------------------------------------------
// Endpoint tasks
// ---------------------------------------------------------------------------
async function checkHealth() {
    try {
        const { status, body } = await get(`${MCP_HOST}/health`);
        if (status === 200) {
            log(`[health] OK — ${body.status || JSON.stringify(body)}`);
        } else {
            log(`[health] WARNING — HTTP ${status}`);
        }
    } catch (err) {
        log(`[health] ERROR — ${err.message}`);
    }
}

function runSecurityAutomation() {
    return new Promise((resolve, reject) => {
        execFile('node', [SECURITY_AUTOMATION_SCRIPT], { cwd: __dirname, env: process.env }, (error, stdout, stderr) => {
            if (stdout) {
                log(`[security] ${stdout.trim()}`);
            }

            if (stderr) {
                log(`[security] stderr: ${stderr.trim()}`);
            }

            if (error) {
                reject(new Error(error.message));
                return;
            }

            resolve();
        });
    });
}

// ---------------------------------------------------------------------------
// Scheduler loop
// ---------------------------------------------------------------------------
async function tick() {
    const state = loadState();
    const now   = Date.now();

    // Always run health check
    await checkHealth();

    // Daily security automation (daily audit + weekly push handled internally)
    if (now - state.lastSecurityRun >= INTERVALS.security) {
        if (securityRunInProgress) {
            log('[security] Skip: previous security automation run is still in progress.');
            return;
        }

        securityRunInProgress = true;
        log('[security] Starting daily security automation run …');
        try {
            await runSecurityAutomation();
        } catch (err) {
            log(`[security] ERROR — ${err.message}`);
        } finally {
            securityRunInProgress = false;
        }

        state.lastSecurityRun = now;
        saveState(state);
    }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
log('MCP Maintenance Scheduler starting …');
log(`  Health check  : every ${INTERVALS.healthCheck / 60000} minutes`);
log(`  Security run  : every ${INTERVALS.security / 86400000} days`);

// Run immediately on startup, then on each health-check interval
tick();
setInterval(tick, INTERVALS.healthCheck);
