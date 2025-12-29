const express = require('express');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');
const app = express();
const PORT = 3000;
const LOG_FILE = path.join(__dirname, 'server.log');

// Middleware to parse JSON requests
app.use(express.json());

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

// Endpoint to trigger repository update
app.post('/update-repo', async (req, res) => {
    try {
        const output = await executeCommand('git pull origin main', 'Repository update');
        res.json({ message: 'Repository updated successfully', output });
    } catch (error) {
        res.status(500).json({ message: 'Failed to update repository', error: error.message });
    }
});

// Endpoint to run security audits
app.post('/run-audit', async (req, res) => {
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
app.listen(PORT, () => {
    logMessage(`MCP server is running on http://localhost:${PORT}`);
});
