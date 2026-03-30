/**
 * MCP Maintenance Scheduler
 *
 * Responsibilities:
 *  - Health check  : every 5 minutes  — GET  /health
 *  - Security audit: every 7 days     — POST /run-audit
 *  - Dependency update: every 30 days — POST /update-repo
 *
 * All schedule state is persisted in scheduler-state.json so intervals
 * survive restarts without triggering unnecessary work.
 */

const http = require('http');
const fs   = require('fs');
const path = require('path');

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------
const MCP_HOST    = (process.env.MCP_HOST || 'http://localhost:4000');
const MCP_API_KEY = process.env.MCP_API_KEY || '';
const STATE_FILE  = path.join(__dirname, 'scheduler-state.json');
const LOG_FILE    = path.join(__dirname, 'scheduler.log');

const INTERVALS = {
    healthCheck:   5 * 60 * 1000,         //  5 minutes
    audit:         7 * 24 * 60 * 60 * 1000, //  7 days
    update:       30 * 24 * 60 * 60 * 1000  // 30 days
};

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
        return { lastAudit: 0, lastUpdate: 0 };
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

const get  = url => request('GET',  url);
const post = url => request('POST', url);

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

async function runAudit() {
    log('[audit] Starting security audit via /run-audit …');
    try {
        const { status, body } = await post(`${MCP_HOST}/run-audit`);
        if (status === 200) {
            log(`[audit] Completed — ${body.message}`);
        } else {
            log(`[audit] FAILED — HTTP ${status}: ${body.message || JSON.stringify(body)}`);
        }
    } catch (err) {
        log(`[audit] ERROR — ${err.message}`);
    }
}

async function updateRepo() {
    log('[update] Checking for repository updates via /update-repo …');
    try {
        const { status, body } = await post(`${MCP_HOST}/update-repo`);
        if (status === 200) {
            log(`[update] Completed — ${body.message}`);
        } else {
            log(`[update] FAILED — HTTP ${status}: ${body.message || JSON.stringify(body)}`);
        }
    } catch (err) {
        log(`[update] ERROR — ${err.message}`);
    }
}

// ---------------------------------------------------------------------------
// Scheduler loop
// ---------------------------------------------------------------------------
async function tick() {
    const state = loadState();
    const now   = Date.now();

    // Always run health check
    await checkHealth();

    // Weekly audit
    if (now - state.lastAudit >= INTERVALS.audit) {
        await runAudit();
        state.lastAudit = now;
        saveState(state);
    }

    // Monthly update
    if (now - state.lastUpdate >= INTERVALS.update) {
        await updateRepo();
        state.lastUpdate = now;
        saveState(state);
    }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
log('MCP Maintenance Scheduler starting …');
log(`  Health check  : every ${INTERVALS.healthCheck / 60000} minutes`);
log(`  Security audit: every ${INTERVALS.audit / 86400000} days`);
log(`  Repo update   : every ${INTERVALS.update / 86400000} days (monthly)`);

// Run immediately on startup, then on each health-check interval
tick();
setInterval(tick, INTERVALS.healthCheck);
