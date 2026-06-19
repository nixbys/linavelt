# Changelog

All notable changes to Linavelt are documented here.  
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- SDLC-aligned repository structure: `docs/plan/`, `docs/design/`, `docs/operate/`
- `scripts/build/`, `scripts/release/`, `scripts/ops/` subdirectories
- GitHub issue templates (bug report, feature request)
- Pull request template with SDLC checklist
- `CODEOWNERS` — CI/workflow files require `@nixbys` review
- `CHANGELOG.md` and `docs/design/ARCHITECTURE.md`
- Workflow concurrency groups to cancel stale runs on new pushes
- Composer dependency caching in CI (lint + tests workflows)
- `php-security-update` workflow for automated PHP security patch PRs
- `security-scan` workflow restoring the CodeQL `security` category required by GHAS branch protection
- MCP server containerised and running via Podman with health endpoint at `:4000/health`
- Builder revision persistence (WorkStream 2) and module retry controls (WorkStream 1)

### Changed
- All GitHub Actions upgraded to Node 24-based runners (`checkout@v5`, `setup-node@v5`)
- `mcp-server/` Express upgraded from 4.x → 5.x; `@modelcontextprotocol/sdk` updated to 1.29.x
- Tailwind CSS updated to 4.3.1; Vite updated to 7.x; Electron updated to 42.x
- `npm overrides` added for `lodash` and `esbuild` to pin safe transitive versions
- `copilot-instructions.md` rewritten to reflect current project state
- Dependabot configured to ignore `livewire/flux-pro` (private, no auth available to Dependabot)

### Removed
- Deprecated `ossar.yml` workflow (replaced by `security-scan.yml`)
- Unused MCP server files: `index.js`, `minimal-server.js`, `simple-http-server.js`, `test-import.js`
- Root-level clutter: `test-server.js`, `.README`, duplicate `mcp-server-config.json`
- `electron` devDependency incorrectly placed in `mcp-server/package.json`
- Dead commented-out `git-auto-commit-action` step from `lint.yml`
- 3727 tracked `mcp-server/node_modules` files removed from git history

### Security
- PHP dependencies updated via automated `composer update` to resolve Dependabot security alerts
- 0 npm vulnerabilities (root and mcp-server)
