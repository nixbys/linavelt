<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->
- [x] Verify that the copilot-instructions.md file in the .github directory is created.

- [x] Clarify Project Requirements
	<!-- Laravel/Livewire/Flux application with Tailwind CSS v4, GrapeJS page builder, and MCP server integration. -->

- [x] Scaffold the Project
	<!-- Project is a Laravel starter kit with Livewire Flux, Tailwind v4, Vite, and an MCP server in mcp-server/. -->

- [x] Customize the Project
	<!-- Applied: workflow version bumps (#13/#14/#15), fixed broken codeql-analysis.yml (#12/#18/#19), MCP server security patches (#20). -->

- [x] Install Required Extensions
	<!-- No additional VS Code extensions needed beyond what is already configured. -->

- [x] Compile the Project
	<!-- Dependencies: `composer install` (with Flux credentials) + `npm install` + `npm run build`. MCP server: `cd mcp-server && npm install`. -->

- [x] Create and Run Task
	<!-- VS Code tasks configured in mcp-server/.vscode/tasks.json for MCP server start, scheduler, and maintenance. -->

- [x] Launch the Project
	<!-- Use `php artisan serve` for the Laravel app; `node mcp-server/server.js` for the MCP server (requires MCP_API_KEY env var). -->

- [x] Ensure Documentation is Complete
	<!-- README.md exists. SECURITY_FIXES.md and docs/GRAPEJS_INTEGRATION.md added by prior PRs. -->

## Project Summary

Laravel 11 application (nixbys/linavelt) with:
- **Frontend**: Tailwind CSS v4 (via `@tailwindcss/vite`), Livewire Flux/Flux-Pro, GrapeJS page builder
- **Backend**: PHP 8.4, Laravel 11, PHPUnit, Laravel Pint (PSR-12)
- **MCP Server**: Node.js server in `mcp-server/` with API-key authentication on protected endpoints
- **CI/CD**: GitHub Actions (lint, tests, CodeQL, OSSAR) using PHP 8.4 and latest action versions

## Development Notes

- Flux requires `FLUX_USERNAME` and `FLUX_LICENSE_KEY` secrets for `composer install`
- Run `composer config http-basic.composer.fluxui.dev <user> <key>` before `composer install`
- MCP server uses `MCP_API_KEY` environment variable for endpoint authentication
- All open PRs (#12–#20) have been consolidated into PR #21 on `copilot/resolve-open-issues`

- Work through each checklist item systematically.
- Keep communication concise and focused.
- Follow development best practices.
