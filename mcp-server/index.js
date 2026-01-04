const { McpServer } = require('./node_modules/@modelcontextprotocol/sdk/dist/cjs/server/mcp');
const { StdioServerTransport } = require('./node_modules/@modelcontextprotocol/sdk/dist/cjs/server/stdio');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');
const dotenv = require('dotenv');

dotenv.config();

const server = new McpServer({
  name: 'laravel-maintenance',
  version: '1.0.0',
});

server.registerTool('runGitHubChecks', {
  title: 'Run GitHub Workflow Checks',
  description: 'Executes GitHub workflow checks and resolves any issues.',
  inputSchema: {},
}, async () => {
  return new Promise((resolve, reject) => {
    exec('gh workflow run --repo <your-repo> --all', (error, stdout, stderr) => {
      if (error) {
        reject(`Error: ${stderr}`);
      } else {
        resolve({
          content: [{ type: 'text', text: stdout }],
        });
      }
    });
  });
});

server.registerTool('updateDependencies', {
  title: 'Update Dependencies',
  description: 'Updates project dependencies to the latest versions.',
  inputSchema: {},
}, async () => {
  return new Promise((resolve, reject) => {
    exec('npm update', { cwd: path.resolve(__dirname, '../') }, (error, stdout, stderr) => {
      if (error) {
        reject(`Error: ${stderr}`);
      } else {
        resolve({
          content: [{ type: 'text', text: stdout }],
        });
      }
    });
  });
});

server.registerTool('logChanges', {
  title: 'Log Changes',
  description: 'Logs changes made to the project.',
  inputSchema: {},
}, async () => {
  const logPath = path.resolve(__dirname, 'changes.log');
  const logMessage = `Changes logged at ${new Date().toISOString()}\n`;

  return new Promise((resolve, reject) => {
    fs.appendFile(logPath, logMessage, (error) => {
      if (error) {
        reject(`Error: ${error.message}`);
      } else {
        resolve({
          content: [{ type: 'text', text: 'Changes logged successfully.' }],
        });
      }
    });
  });
});

async function main() {
  const transport = new StdioServerTransport();
  await server.connect(transport);
  console.error('Laravel Maintenance MCP Server is running.');
}

main().catch((error) => {
  console.error('Fatal error in main():', error);
  process.exit(1);
});
