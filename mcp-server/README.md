# MCP Server

The MCP (Model Context Protocol) server is designed to keep the repository project files updated and continuously audit and resolve issues, including application and security concerns.

## Features
- Automatically updates the repository files.
- Runs security audits and resolves vulnerabilities.
- Provides a health check endpoint to monitor the server status.

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

2. Start the server:
   ```bash
   npm start
   ```

3. Access the server at `http://localhost:3000`.

## Example Usage

- To update the repository:
  ```bash
  curl -X POST http://localhost:3000/update-repo
  ```

- To run a security audit:
  ```bash
  curl -X POST http://localhost:3000/run-audit
  ```

- To check server health:
  ```bash
  curl http://localhost:3000/health
  ```
