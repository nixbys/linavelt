# System Architecture

## Overview

Linavelt is a multi-runtime application that ships three runtimes from one repository:

| Runtime | Technology | Entry point |
|---|---|---|
| Web app | Laravel 12 + PHP 8.4 | `php artisan serve` / Podman |
| MCP server | Node.js 22 + Express 5 | `mcp-server/server.js` |
| Desktop app | Electron 42 | `electron/main.cjs` |

---

## Component Map

```
┌─────────────────────────────────────────────────────┐
│                    Browser / Electron                │
└───────────────────────┬─────────────────────────────┘
                        │ HTTP
┌───────────────────────▼─────────────────────────────┐
│               Laravel 12 Web App (:8000)             │
│  ┌──────────────┐  ┌────────────┐  ┌─────────────┐  │
│  │ Fortify Auth │  │  Livewire  │  │  Volt Pages │  │
│  └──────────────┘  └────────────┘  └─────────────┘  │
│  ┌──────────────────────────────────────────────┐    │
│  │         Flux / Flux-Pro UI Components        │    │
│  └──────────────────────────────────────────────┘    │
│  ┌──────────────┐  ┌────────────────────────────┐    │
│  │  Controllers │  │  Jobs (GenerateModules)    │    │
│  └──────────────┘  └────────────────────────────┘    │
└───────────────────────┬─────────────────────────────┘
                        │ PDO
        ┌───────────────▼──────────────┐
        │       MariaDB 10.11 (:3306)  │
        └──────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│            MCP Server — Node.js (:4000)              │
│  ┌──────────────────────────────────────────────┐    │
│  │  Express HTTP + API-key auth middleware      │    │
│  └──────────────────────────────────────────────┘    │
│  ┌────────────────┐  ┌────────────────────────┐      │
│  │   scheduler.js │  │ security-automation.js │      │
│  └────────────────┘  └────────────────────────┘      │
│  ┌──────────────────────────────────────────────┐    │
│  │  automation-policy.json / learning.json      │    │
│  └──────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

---

## Request Flow

### Web requests
1. Nginx / `php artisan serve` receives HTTP request
2. Laravel router (`routes/web.php`, `routes/auth.php`) dispatches to controller or Livewire component
3. Livewire components render Blade views using Flux UI primitives
4. Responses are rendered with Tailwind CSS v4 (compiled by Vite at build time)

### MCP automation
1. `scheduler.js` runs on a cron-like interval (configurable via `automation-policy.json`)
2. Scheduler forks `security-automation.js` which runs npm/composer audits, checks GitHub alerts, and creates weekly update PRs
3. All results are persisted to `security-reports/` and summarised by `automation-observability.js`

### Electron desktop
1. `electron/main.cjs` boots a `BrowserWindow` pointing at the Laravel dev server or a bundled build
2. `electron/preload.cjs` exposes a minimal IPC bridge
3. `electron-builder` packages the app for Linux (AppImage), macOS (dmg), Windows (nsis)

---

## Container Topology

```
podman pod: linavelt
  ├── linavelt-app   (Containerfile at repo root)   :8000
  ├── linavelt-mcp   (mcp-server/Containerfile)     :4000
  └── linavelt-db    (mariadb:10.11)                :3306
```

Local development: `podman-compose.yml` (root)  
Remote always-on: `containers/remote-quadlet/` systemd units pulling from GHCR  
Local quadlet: `containers/quadlet/` — one-command bootstrap via `up.sh`

---

## CI/CD Pipeline

```
Pull Request to main
  ├── ci                      PHPUnit test suite
  ├── quality                 Pint + composer validate
  ├── web-release-readiness   Vite build
  ├── electron-release-readiness  Electron smoke check
  ├── Analyze (javascript)    CodeQL default scan
  ├── Analyze (security)      CodeQL security-category scan
  └── CodeQL                  GHAS summary gate (all required)

Merge to main
  └── publish-containers      Builds + pushes linavelt-app and linavelt-mcp to GHCR

Weekly (Monday 03:00 UTC)
  └── php-security-update     composer audit → update → PR (requires COMPOSER_AUTH secret)
```

---

## Key Design Decisions

**Why two CodeQL scans?**  
Branch protection requires both a `default` and a `security` category SARIF upload. `codeql-analysis.yml` provides the default; `security-scan.yml` provides the `security` category (previously provided by the now-deleted `ossar.yml`).

**Why MCP server alongside Laravel?**  
The MCP server handles async, long-running repo maintenance tasks (audit runs, PR creation, GitHub API calls) that would block or time out in PHP. It runs as an independent process and is consumed by Claude Code / Copilot via the MCP protocol.

**Why Electron from the same repo?**  
The desktop app is a thin shell that loads the Laravel app in a webview. Keeping it in the same repo avoids a separate release cycle and allows the `electron-release-readiness` CI check to validate packaging on every PR.

**Why Podman over Docker?**  
Rootless container support and systemd/quadlet integration for always-on remote deployment without a container daemon running as root.
