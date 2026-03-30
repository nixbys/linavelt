'use strict';

const { McpServer } = require('./node_modules/@modelcontextprotocol/sdk/dist/cjs/server/mcp');
const { StdioServerTransport } = require('./node_modules/@modelcontextprotocol/sdk/dist/cjs/server/stdio');
const { execFile } = require('child_process');
const fs = require('fs');
const path = require('path');
const dotenv = require('dotenv');

dotenv.config();

const REPO = process.env.GITHUB_REPOSITORY || '';

// ---------------------------------------------------------------------------
// MCP Server instance
// ---------------------------------------------------------------------------
const server = new McpServer({
    name: 'laravel-maintenance',
    version: '1.0.0',
});

// ---------------------------------------------------------------------------
// Tool: Run GitHub Workflow Checks
// ---------------------------------------------------------------------------
server.registerTool('runGitHubChecks', {
    title: 'Run GitHub Workflow Checks',
    description: 'Triggers all GitHub Actions workflow runs for the configured repository.',
    inputSchema: {},
}, async () => {
    if (!REPO) {
        return {
            content: [{
                type: 'text',
                text: 'GITHUB_REPOSITORY environment variable is not set. Cannot run workflow checks.',
            }],
        };
    }

    return new Promise((resolve, reject) => {
        execFile('gh', ['workflow', 'run', '--repo', REPO, '--all'], (error, stdout) => {
            if (error) {
                reject(new Error(error.message));
            } else {
                resolve({ content: [{ type: 'text', text: stdout }] });
            }
        });
    });
});

// ---------------------------------------------------------------------------
// Tool: Update Dependencies
// ---------------------------------------------------------------------------
server.registerTool('updateDependencies', {
    title: 'Update Dependencies',
    description: 'Updates project npm dependencies to the latest compatible versions.',
    inputSchema: {},
}, async () => {
    return new Promise((resolve, reject) => {
        execFile('npm', ['update'], { cwd: path.resolve(__dirname, '../') }, (error, stdout) => {
            if (error) {
                reject(new Error(error.message));
            } else {
                resolve({ content: [{ type: 'text', text: stdout }] });
            }
        });
    });
});

// ---------------------------------------------------------------------------
// Tool: Log Changes
// ---------------------------------------------------------------------------
server.registerTool('logChanges', {
    title: 'Log Changes',
    description: 'Appends a timestamped entry to the changes log.',
    inputSchema: {},
}, async () => {
    const logPath = path.resolve(__dirname, 'changes.log');
    const entry = `Changes logged at ${new Date().toISOString()}\n`;

    return new Promise((resolve, reject) => {
        fs.appendFile(logPath, entry, (error) => {
            if (error) {
                reject(new Error(error.message));
            } else {
                resolve({ content: [{ type: 'text', text: 'Changes logged successfully.' }] });
            }
        });
    });
});

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
async function main() {
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error('Laravel Maintenance MCP Server is running.');
}

main().catch((error) => {
    console.error('Fatal error in main():', error);
    process.exit(1);
});
