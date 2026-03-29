const express = require('express');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const app = express();
const PORT = 4000; // Change port to 4000 for testing
const LOG_FILE = path.join(__dirname, 'server.log');

// Middleware to parse JSON requests
app.use(express.json());

// API key authentication for administrative endpoints.
// Set MCP_API_KEY in the environment before starting the server.
// Example: MCP_API_KEY=$(node -e "console.log(require('crypto').randomBytes(32).toString('hex'))") node server.js
const API_KEY = process.env.MCP_API_KEY;

function requireApiKey(req, res, next) {
    if (!API_KEY) {
        logMessage('WARNING: MCP_API_KEY is not set. Administrative endpoints are disabled.');
        return res.status(503).json({ message: 'Server not configured: MCP_API_KEY environment variable is required.' });
    }
    const provided = req.headers['x-api-key'] || '';
    // Hash both values before comparing to normalise length (timingSafeEqual requires equal-length buffers).
    const providedHash = crypto.createHash('sha256').update(provided).digest();
    const expectedHash = crypto.createHash('sha256').update(API_KEY).digest();
    if (!crypto.timingSafeEqual(providedHash, expectedHash)) {
        logMessage(`Unauthorized request to ${req.path} from ${req.ip}`);
        return res.status(401).json({ message: 'Unauthorized: valid X-Api-Key header required.' });
    }
    next();
}

// Utility function to log messages
function logMessage(message) {
    const timestamp = new Date().toISOString();
    const logEntry = `[${timestamp}] ${message}\n`;
    fs.appendFileSync(LOG_FILE, logEntry);
    console.log(message);
}

// Function to execute a command and log output
function executeCommand(command, description) {
    return new Promise((resolve, reject) => {
        exec(command, { cwd: process.cwd() }, (error, stdout, stderr) => {
            if (error) {
                logMessage(`${description} failed: ${error.message}`);
                reject(error);
            } else {
                logMessage(`${description} succeeded: ${stdout}`);
                resolve(stdout);
            }
        });
    });
}

// Endpoint to trigger repository update (requires API key)
app.post('/update-repo', requireApiKey, async (req, res) => {
    try {
        const output = await executeCommand('git pull origin main', 'Repository update');
        res.json({ message: 'Repository updated successfully', output });
    } catch (error) {
        res.status(500).json({ message: 'Failed to update repository', error: error.message });
    }
});

// Endpoint to run security audits (requires API key)
app.post('/run-audit', requireApiKey, async (req, res) => {
    try {
        const output = await executeCommand('npm audit fix && composer update', 'Security audit');
        res.json({ message: 'Audit completed successfully', output });
    } catch (error) {
        res.status(500).json({ message: 'Failed to run audit', error: error.message });
    }
});

// Endpoint to check application health
app.get('/health', (req, res) => {
    res.json({ status: 'MCP server is running', timestamp: new Date() });
});

// Function to run periodic tasks
async function runPeriodicTasks() {
    try {
        logMessage('Starting periodic repository update...');
        await executeCommand('git pull origin main', 'Periodic repository update');

        logMessage('Starting periodic security audit...');
        await executeCommand('npm audit fix && composer update', 'Periodic security audit');

        logMessage('Periodic tasks completed successfully.');
    } catch (error) {
        logMessage(`Periodic tasks encountered an error: ${error.message}`);
    }
}

// Schedule periodic tasks every hour
setInterval(runPeriodicTasks, 60 * 60 * 1000);

// Start the server
app.listen(PORT, '0.0.0.0', () => {
    logMessage(`MCP server is attempting to bind to http://0.0.0.0:${PORT}`);
});
