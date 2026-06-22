# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Project Is

Linavelt is a Laravel 12 + Livewire platform for building module-driven web experiences, paired with a Node.js MCP server that automates repository maintenance and security operations. It ships three runtimes from one repo: a web app, a desktop app (Electron), and the MCP server.

## Commands

### Laravel app

```bash
composer install                  # requires Flux Pro credentials (see Private Packages below)
php artisan key:generate
php artisan migrate
php artisan serve                 # dev server on :8000
./vendor/bin/pint                 # code style (PSR-12); run before committing
php artisan test                  # full test suite
./vendor/bin/phpunit --filter TestName   # single test
```

### Frontend

```bash
npm install
npm run dev          # Vite dev server
npm run build        # production build (required by CI)
```

### Quality gate (runs all local checks)

```bash
npm run check:preflight
```

### MCP server

```bash
cd mcp-server && npm install
sh ./start-daemon.sh              # starts on port 4000; requires MCP_API_KEY env var
sh ./stop-daemon.sh
curl http://127.0.0.1:4000/health
npm run automation:readiness      # pre-check before enabling scheduled automation
npm run automation:report         # observability summary of past automation runs
```

### Containers (local pod)

```bash
APP_KEY="$(openssl rand -base64 32 | sed 's#^#base64:#')" MCP_API_KEY="dev-local-key" sh ./containers/quadlet/up.sh
```

Starts the full pod: Laravel app (:8000), MCP server (:4000), MariaDB (:3306).

### Security

```bash
npm run security:audit            # npm audit for root + mcp-server
```

## Architecture

### Three runtimes, one repo

| Runtime | Entry point | Port |
|---|---|---|
| Laravel web app | `php artisan serve` / `public/index.php` | 8000 |
| MCP server | `mcp-server/server.js` | 4000 |
| Electron desktop | `electron/main.cjs` | — |

The Electron app is a thin shell that loads the Laravel app in a webview — no separate frontend build.

### Laravel request path

`routes/web.php` + `routes/auth.php` → controller or Livewire component → Blade view using Flux UI primitives → Tailwind CSS v4 (compiled by Vite at build time).

Fortify handles all auth including 2FA. Settings pages use Livewire Volt inline components (`resources/views/livewire/settings/`).

### Module generation flow

1. User completes builder onboarding (`BuilderOnboardingController` → `builder/onboarding` route), selecting stack preferences stored as JSON in `users.onboarding_preferences`.
2. `GenerateProjectModules` job fires asynchronously, reads `config/linavelt.php`'s `module_templates` map, and writes scaffolding stubs to `storage/app/projects/{user_id}/` via Laravel's Storage facade.
3. Job tracks status in three `users` columns: `module_generation_status`, `module_generation_started_at`, `module_generation_completed_at`.
4. Admin can retry failed jobs via `POST /admin/users/{user}/module-generation/retry`.

### MCP server internals

`server.js` — Express 5 HTTP server with `X-Api-Key` auth middleware (timing-safe compare). Protected endpoints shell out via `execFile`.

`scheduler.js` — cron-like loop driven by `automation-policy.json`. Forks `security-automation.js` which runs composer/npm audits, checks GitHub Dependabot alerts, and creates weekly update PRs.

`automation-observability.js` — reads past run logs from `security-reports/` and emits a summary.

### Container topology

```
podman pod: linavelt
  ├── linavelt-app   (Containerfile at repo root)   :8000
  ├── linavelt-mcp   (mcp-server/Containerfile)     :4000
  └── linavelt-db    (mariadb:10.11)                :3306
```

Local dev: `podman-compose.yml`. Local quadlet: `containers/quadlet/`. Remote always-on (GHCR pull): `containers/remote-quadlet/`.

## CI Pipeline

All 7 checks are required on PRs to `main`:

| Check | Workflow | What it does |
|---|---|---|
| `ci` | `tests.yml` | PHPUnit suite (SQLite in-memory) |
| `quality` | `lint.yml` | Pint + `composer validate` |
| `web-release-readiness` | `release-readiness.yml` | Vite production build |
| `electron-release-readiness` | `release-readiness.yml` | Electron smoke check |
| `Analyze (javascript)` | `codeql-analysis.yml` | CodeQL default scan |
| `Analyze (security)` | `security-scan.yml` | CodeQL security-category scan |
| `CodeQL` | GHAS gate | Summary gate (depends on both scans) |

Two separate CodeQL workflows are intentional: branch protection requires both a `default` and a `security` SARIF category upload.

On merge to `main`: `publish-containers.yml` pushes `linavelt-app` and `linavelt-mcp` images to GHCR.

Weekly (Monday 03:00 UTC): `php-security-update.yml` runs `composer audit`, updates deps, and opens a PR if vulnerabilities are found.

## Private Packages

`livewire/flux-pro` is fetched from `composer.fluxui.dev`:

```bash
composer config http-basic.composer.fluxui.dev <email> <license-key>
```

For CI, store the JSON form as `COMPOSER_AUTH` in the GitHub **Testing** environment and **Dependabot** secrets:

```json
{"http-basic":{"composer.fluxui.dev":{"username":"<email>","password":"<license-key>"}}}
```

## Key Environment Variables

| Variable | Purpose |
|---|---|
| `MCP_API_KEY` | Required to enable all protected MCP endpoints |
| `MCP_PORT` | MCP server port (default 4000) |
| `COMPOSER_AUTH` | CI/Dependabot auth for `livewire/flux-pro` |
| `APP_KEY` | Laravel application key |
| `AUTOMATION_POLICY_PROFILE` | Selects automation behavior profile in `automation-policy.json` |

## Coding Conventions

- PHP: PSR-12 enforced by Pint. Run `vendor/bin/pint` before committing.
- JS: ESM modules; no TypeScript.
- Blade: Flux components preferred over raw HTML; Livewire Volt for reactive components.
- Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) — no running DB needed.
- No comments unless the WHY is non-obvious.
