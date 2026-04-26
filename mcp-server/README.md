# MCP Server

The MCP (Model Context Protocol) server is designed to keep the repository project files updated and continuously audit and resolve issues, including application and security concerns.

## Features
- Automatically updates the repository files.
- Runs security audits and resolves vulnerabilities.
- Provides a health check endpoint to monitor the server status.
- Runs daily security scans (npm/composer + GitHub alerts) and writes reports.
- Creates one weekly combined security commit and PR titled `Weekly Security Update` after checks pass.

## Endpoints

### 1. Update Repository
**POST** `/update-repo`
- Pulls the latest changes from the repository.

### 2. Run Audit
**POST** `/run-audit`
- Runs `npm audit fix` and `composer update` to resolve vulnerabilities.

### 3. Health Check
**GET** `/health`
- Returns the status of the MCP server.

## Setup

1. Install dependencies:
   ```bash
   npm install
   ```

2. Verify full-automation prerequisites:
   ```bash
   npm run automation:readiness
   ```

3. Start full automation stack in background (server + daily scheduler):
   ```bash
   npm run automation:start
   ```

4. Check full automation status:
   ```bash
   npm run automation:status
   ```

5. Stop full automation stack:
   ```bash
   npm run automation:stop
   ```

6. Access server health at `http://127.0.0.1:4000/health`.

### Individual Process Controls

- Server only:
  - Start once (foreground): `npm run start:once`
  - Start daemon: `npm run start:daemon`
  - Status: `npm run status`
  - Stop: `npm run stop`
- Scheduler only:
  - Start daemon: `npm run scheduler:start`
  - Status: `npm run scheduler:status`
  - Stop: `npm run scheduler:stop`

## Security Automation (Daily + Weekly)

The scheduler runs `security-automation.js` daily. That script:

- Performs daily security auditing of:
  - `npm audit` in repository root
  - `npm audit` in `mcp-server/`
  - `composer audit`
  - Open Dependabot alerts in the linked GitHub repo
  - Open code scanning alerts in the linked GitHub repo
- Writes daily reports to `mcp-server/security-reports/YYYY-MM-DD.json`
- Once per week, creates a single combined update branch and one commit titled `Weekly Security Update`
- Pushes exactly one weekly update branch and opens/updates a PR with the same title
- Waits for PR checks to pass via GitHub CLI
- Requires a clean git working tree for weekly commit/push safety
- Uses scheduler run-locking to prevent overlapping daily automation runs

### Policy Profiles and Runbook

Security automation behavior is profile-driven using `mcp-server/automation-policy.json`.
Set the active profile with:

```bash
AUTOMATION_POLICY_PROFILE=default node security-automation.js
```

Available profiles currently include `default`, `ci`, `maintenance-light`, and `production-strict`.

For full operations guidance, recovery procedures, and validation checklists, see:

- `docs/SECURITY_AUTOMATION_RUNBOOK.md`

### Required Environment

- `MCP_API_KEY`: API key for protected MCP endpoints
- `GITHUB_REPOSITORY`: GitHub repository in `owner/repo` format (optional if `gh` is already linked)
- GitHub CLI authentication (`gh auth login`) with permissions for:
  - Pull requests
  - Actions/checks read
  - Dependabot alerts read
  - Code scanning alerts read

### Manual Run

You can run the same process on demand:

```bash
npm run security:automation
```

## Example Usage

- To update the repository:
  ```bash
  curl -X POST http://127.0.0.1:4000/update-repo
  ```

- To run a security audit:
  ```bash
  curl -X POST http://127.0.0.1:4000/run-audit
  ```

- To check server health:
  ```bash
  curl http://127.0.0.1:4000/health
  ```
