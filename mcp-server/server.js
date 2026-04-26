'use strict';

const express = require('express');
const { execFile } = require('child_process');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------
const parsedPort = parseInt(process.env.MCP_PORT, 10);
const PORT = (parsedPort >= 1 && parsedPort <= 65535) ? parsedPort : 4000;
const HOST = process.env.MCP_HOST_BIND || '127.0.0.1';
const LOG_FILE = path.join(__dirname, 'server.log');
const API_KEY = process.env.MCP_API_KEY;
const PERIODIC_INTERVAL_MS = 60 * 60 * 1000; // 1 hour
const ENABLE_PERIODIC_MAINTENANCE = process.env.MCP_ENABLE_PERIODIC_MAINTENANCE === 'true';

// ---------------------------------------------------------------------------
// Application setup
// ---------------------------------------------------------------------------
const app = express();
app.use(express.json());

// ---------------------------------------------------------------------------
// Logging
// ---------------------------------------------------------------------------
function logMessage(message) {
    const entry = `[${new Date().toISOString()}] ${message}\n`;
    fs.appendFileSync(LOG_FILE, entry);
    console.log(message);
}

// ---------------------------------------------------------------------------
// Authentication
// ---------------------------------------------------------------------------
// Set MCP_API_KEY in the environment before starting the server.
// Example: MCP_API_KEY=$(node -e "console.log(require('crypto').randomBytes(32).toString('hex'))") node server.js
function requireApiKey(req, res, next) {
    if (!API_KEY) {
        logMessage('WARNING: MCP_API_KEY is not set. Administrative endpoints are disabled.');
        return res.status(503).json({ message: 'Server not configured: MCP_API_KEY environment variable is required.' });
    }

    const provided = req.headers['x-api-key'] || '';
    const providedBuf = Buffer.from(provided);
    const expectedBuf = Buffer.from(API_KEY);
    // Reject when lengths differ to avoid timingSafeEqual throwing; then compare bytes directly.
    const isValid = providedBuf.length === expectedBuf.length &&
        crypto.timingSafeEqual(providedBuf, expectedBuf);

    if (!isValid) {
        logMessage(`Unauthorized request to ${req.path} from ${req.ip}`);
        return res.status(401).json({ message: 'Unauthorized: valid X-Api-Key header required.' });
    }

    next();
}

// ---------------------------------------------------------------------------
// Command execution (no shell — prevents injection)
// ---------------------------------------------------------------------------
function executeCommand(file, args, description) {
    return new Promise((resolve, reject) => {
        execFile(file, args, { cwd: process.cwd() }, (error, stdout, stderr) => {
            if (error) {
                const detail = stderr ? `${error.message}\n${stderr.trim()}` : error.message;
                logMessage(`${description} failed: ${detail}`);
                reject(error);
            } else {
                if (stderr) logMessage(`${description} stderr: ${stderr.trim()}`);
                logMessage(`${description} succeeded: ${stdout.trim()}`);
                resolve(stdout);
            }
        });
    });
}

// ---------------------------------------------------------------------------
// Routes
// ---------------------------------------------------------------------------
/** Trigger a git pull from the main branch (requires API key). */
app.post('/update-repo', requireApiKey, async (req, res) => {
    try {
        const output = await executeCommand('git', ['pull', 'origin', 'main'], 'Repository update');
        res.json({ message: 'Repository updated successfully', output });
    } catch (error) {
        res.status(500).json({ message: 'Failed to update repository', error: error.message });
    }
});

/** Run npm and Composer security audits (requires API key). */
app.post('/run-audit', requireApiKey, async (req, res) => {
    try {
        const npmOutput = await executeCommand('npm', ['audit', 'fix'], 'npm security audit');
        const composerOutput = await executeCommand('composer', ['update'], 'Composer update');
        res.json({
            message: 'Audit completed successfully',
            output: { npm: npmOutput.trim(), composer: composerOutput.trim() },
        });
    } catch (error) {
        res.status(500).json({ message: 'Failed to run audit', error: error.message });
    }
});

/** Health check endpoint (public). */
app.get('/health', (_req, res) => {
    res.json({ status: 'MCP server is running', timestamp: new Date().toISOString() });
});

// ---------------------------------------------------------------------------
// Periodic maintenance tasks
// ---------------------------------------------------------------------------
async function runPeriodicTasks() {
    try {
        await executeCommand('git', ['pull', 'origin', 'main'], 'Periodic repository update');
        await executeCommand('npm', ['audit', 'fix'], 'Periodic npm audit');
        await executeCommand('composer', ['update'], 'Periodic composer update');
        logMessage('Periodic tasks completed successfully.');
    } catch (error) {
        logMessage(`Periodic tasks encountered an error: ${error.message}`);
    }
}

if (ENABLE_PERIODIC_MAINTENANCE) {
    setInterval(runPeriodicTasks, PERIODIC_INTERVAL_MS);
    logMessage('Legacy periodic maintenance is enabled (hourly).');
} else {
    logMessage('Legacy periodic maintenance is disabled; use scheduler.js for daily/weekly security automation.');
}

// ---------------------------------------------------------------------------
// Start
// ---------------------------------------------------------------------------
app.listen(PORT, HOST, () => {
    logMessage(`MCP server is running on http://${HOST}:${PORT}`);
});
