<!-- Workspace-specific instructions for GitHub Copilot -->

## Project Summary

Linavelt (nixbys/linavelt) is a Laravel 12 + Livewire platform for building module-driven web experiences, paired with a Node.js MCP server that automates repository maintenance and security operations.

## Tech Stack

- **Backend**: PHP 8.4, Laravel 12, Fortify (2FA), PHPUnit, Laravel Pint (PSR-12)
- **Frontend**: Livewire, Volt, Flux/Flux-Pro (private), Tailwind CSS v4, Vite 7
- **MCP Server**: Node.js 22+, Express 5, `@modelcontextprotocol/sdk`
- **Packaging**: Electron + electron-builder (desktop), Podman (container)
- **CI/CD**: GitHub Actions — lint, tests, CodeQL (2 scans), release-readiness, publish-containers, php-security-update

## Repository Layout (SDLC-aligned)

```
app/               Laravel application (controllers, jobs, Livewire, models, providers)
resources/views/   Blade views (components/, livewire/, layout/, common/, partials/)
routes/            web.php, auth.php, console.php
tests/             Feature/ and Unit/ PHPUnit suites
mcp-server/        MCP HTTP server, scheduler, security automation, shell daemon scripts
electron/          Electron main + preload for desktop packaging
containers/        Podman quadlet units (local + remote deployment)
scripts/build/     preflight-check.sh — local quality gate
scripts/release/   electron-release-smoke.mjs — Electron release validation
scripts/ops/       extract-runner.sh — self-hosted Actions runner setup
docs/plan/         ROADMAP_EXECUTION_PLAN.md
docs/design/       ELECTRON_INTEGRATION.md, ARCHITECTURE.md
docs/operate/      SECURITY_AUTOMATION_RUNBOOK.md, container-security.md
.github/workflows/ CI/CD pipeline (7 required checks + publish + php-security-update)
```

## Development Setup

```bash
# PHP (requires Flux Pro credentials)
composer config http-basic.composer.fluxui.dev <email> <license-key>
composer install

# Node
npm install
npm run build        # Vite production build
npm run dev          # Vite dev server

# MCP server
cd mcp-server && npm install
sh ./start-daemon.sh  # starts on port 4000; requires MCP_API_KEY
```

## Key Environment Variables

| Variable | Where | Purpose |
|---|---|---|
| `MCP_API_KEY` | local / container | Authenticates protected MCP endpoints |
| `MCP_PORT` | local / container | MCP server port (default 4000) |
| `COMPOSER_AUTH` | GitHub Testing env + Dependabot secrets | Installs livewire/flux-pro |
| `APP_KEY` | .env | Laravel application key |

## CI Checks (all required on PRs to main)

- `ci` — PHPUnit test suite
- `quality` — Pint code style + composer validate
- `web-release-readiness` — Vite build
- `electron-release-readiness` — Electron smoke check
- `Analyze (javascript)` — CodeQL default scan
- `Analyze (security)` — CodeQL security-category scan
- `CodeQL` — GHAS summary gate

## Private Package Auth

`livewire/flux-pro` is fetched from `composer.fluxui.dev` using HTTP basic auth:

```json
{"http-basic":{"composer.fluxui.dev":{"username":"<email>","password":"<license-key>"}}}
```

Store this as `COMPOSER_AUTH` in the GitHub Testing environment AND Dependabot secrets.

## Coding Guidelines

- PHP: PSR-12 enforced by Pint; run `vendor/bin/pint` before committing
- JS: ESM modules; no TypeScript
- Blade: Flux components preferred over raw HTML; Livewire Volt for reactive components
- No comments unless the WHY is non-obvious
- All PRs must pass all 7 CI checks before merge
