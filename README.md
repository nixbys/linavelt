# Linavelt

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![Livewire](https://img.shields.io/badge/Livewire-Volt%20%2B%20Flux-0F172A)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Node](https://img.shields.io/badge/Node-%3E%3D22-5FA04E?logo=nodedotjs&logoColor=white)](https://nodejs.org)
[![MCP](https://img.shields.io/badge/MCP-Server-111827)](https://modelcontextprotocol.io)

Linavelt is a Laravel + Livewire platform for building and managing modern, module-driven web experiences, paired with an MCP server that automates repository maintenance and security operations.

## Why This Project Exists

Linavelt is designed to bring three concerns into one practical system:

- Product velocity: ship pages, modules, and admin workflows quickly.
- Operational reliability: continuously monitor and maintain repo and dependency health.
- Secure automation: run controlled daily and weekly security routines with review-friendly outputs.

## End Goals

- Deliver a scalable application foundation for content, admin tooling, and onboarding flows.
- Provide a first-class DX using Laravel, Livewire Volt/Flux, Tailwind v4, and Vite.
- Keep security hygiene automated through MCP-backed daily audits and weekly update workflows.
- Support both web and desktop packaging paths (Electron) from one codebase.

## Core Capabilities

- Laravel 12 backend with Fortify authentication and two-factor support.
- Livewire + Volt + Flux UI architecture for rich, reactive interfaces.
- Tailwind CSS v4 pipeline with Vite for fast frontend iteration.
- Blog/admin and builder onboarding routes for product workflows.
- MCP server endpoints for repo updates, audits, and health checks.
- Scheduler-based security automation with report generation and weekly PR strategy.

## System Architecture

```mermaid
flowchart LR
    A[Users] --> B[Laravel App]
    B --> C[Livewire Volt and Flux UI]
    B --> D[Database]
    E[MCP Server] --> F[Repo Maintenance]
    E --> G[Security Automation]
    G --> H[Daily Reports]
    G --> I[Weekly Security PR]
```

## Tech Stack

- Backend: Laravel 12, PHP 8.2+, Fortify, PHPUnit
- Frontend: Livewire, Volt, Flux/Flux Pro, Tailwind CSS v4, Vite
- Automation: Node.js 22+, Express, Model Context Protocol SDK
- Packaging: Electron + electron-builder
- Quality and CI: Laravel Pint, PHPUnit, GitHub Actions

## Quick Start

### 1. Install application dependencies

```bash
composer config http-basic.composer.fluxui.dev <flux-username> <flux-license-key>
composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 3. Run the Laravel app

```bash
php artisan serve
```

### 4. Run frontend tooling

```bash
npm run dev
```

## MCP Server

The MCP server lives in mcp-server/ and is responsible for repository and security automation tasks.

### Start

```bash
cd mcp-server
npm install
sh ./start-daemon.sh
```

### Health Check

```bash
curl http://127.0.0.1:4000/health
```

### Stop

```bash
cd mcp-server
sh ./stop-daemon.sh
```

### Key Environment Variables

- MCP_API_KEY: required for protected MCP endpoints.
- GITHUB_REPOSITORY: optional owner/repo hint for automation.
- AUTOMATION_POLICY_PROFILE: selects automation behavior profile.

## Security Automation

The scheduler and automation scripts in mcp-server/ provide:

- Daily dependency and platform security checks.
- Daily report output in mcp-server/security-reports/.
- Weekly consolidated update branch and pull request flow.

Run readiness checks before enabling full automation:

```bash
cd mcp-server
npm run automation:readiness
npm run automation:start
```

## Testing and Quality

```bash
npm run check:preflight
```

Direct commands:

```bash
php artisan test
./vendor/bin/pint
npm run build
npm run security:audit
```

Preflight runs the full local quality gate in one command and reports warnings for environment-only blockers (for example, missing PHP/composer or missing GitHub CLI authentication).

## Key Project Paths

- app/: Laravel application code (controllers, jobs, Livewire, providers)
- resources/: frontend assets and Blade views
- routes/: web/auth/console routes
- tests/: feature and unit tests
- mcp-server/: automation and MCP server runtime
- docs/: operational and integration documentation

## Documentation

- docs/SECURITY_AUTOMATION_RUNBOOK.md
- docs/container-security.md
- docs/ELECTRON_INTEGRATION.md
- mcp-server/README.md

## Roadmap Direction

- Expand module generation workflows and admin controls.
- Deepen builder integrations for page and content authoring.
- Increase automation observability (alerts, dashboards, and run intelligence).
- Tighten release workflows for web and Electron distribution.

## Contributing

Contributions are welcome through focused pull requests with clear test coverage and changelog-ready summaries.

## License

This project is licensed under the MIT License.
