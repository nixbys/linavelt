# Security Policy

Linavelt is a Laravel + Livewire application (web, Electron desktop, and a
companion Node.js MCP automation server) for building and managing
module-driven sites. It stores real user accounts, page/project content, and
automation credentials — treat any non-local deployment as a production
system.

## Supported Versions

Security fixes are handled on `main`; there are no maintained release
branches yet.

## Deployment Guidance

### Application

- Never deploy with `APP_DEBUG=true`. The default `.env.example` ships it
  `true` for local development only — Laravel's debug page renders stack
  traces, environment variables, and query bindings to any visitor who hits
  an unhandled exception. Set `APP_DEBUG=false` and `LOG_LEVEL=warning` (or
  stricter) for anything beyond localhost.
- Set `SESSION_SECURE_COOKIES=true` and `SESSION_ENCRYPT=true` once served
  over HTTPS (the `.env.example` comments call this out; it is not the
  default).
- Use a real queue/cache/session backend (Redis or the database driver
  already configured) rather than `sync`/`array` in production — module
  generation (`App\Jobs\GenerateProjectModules`) runs on the queue, and
  session/cache correctness matters for auth.
- `BCRYPT_ROUNDS` is 12 by default; do not lower it.
- Serve behind HTTPS end-to-end. Fortify's 2FA (`laravel/fortify`) is
  available per-user but not mandatory — consider requiring it for accounts
  with `is_admin = true`.

### Admin access

- `is_admin` on the `users` table gates `/admin` (`Gate::authorize('admin')`
  in `AdminController`, checked in every admin route). Grant it sparingly;
  there is no separate "moderator" tier today.
- `app/Models/User.php` lists `is_admin` in `$fillable`. No current code
  path mass-assigns unfiltered request input into `User` (registration and
  profile updates both build their `$validated` array from an explicit
  Livewire Volt validation rule set that does not include `is_admin`), but
  this is a defense-in-depth gap: any future `User::create($request->all())`
  or similar would let a caller self-promote to admin. Prefer
  `$request->validate()`-derived arrays (as the existing code already does)
  over raw request arrays when creating or updating `User` rows, and treat
  adding `is_admin` to any user-facing form as a change that needs review.

### MCP server (`mcp-server/`)

- `MCP_API_KEY` gates the server's administrative endpoints
  (`mcp-server/server.js`); with it unset, those endpoints are disabled
  (fail closed) rather than left open — do not "fix" a missing key by
  weakening that check. Generate it with
  `node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"`
  and protect it the same way as `APP_KEY` and `COMPOSER_AUTH`.
- The MCP server's security automation (`security-automation.js`) runs
  `composer audit` / `npm audit`, writes reports under
  `mcp-server/security-reports/`, and — per
  `docs/operate/SECURITY_AUTOMATION_RUNBOOK.md` — can open pull requests
  against this repo. Any token it uses for that (`GH_TOKEN`/`GITHUB_TOKEN`)
  is as sensitive as a maintainer's own write access and must not be logged
  or exposed via the server's HTTP API.
- `AdminController::latestAutomationReport()` reads whatever the newest file
  under `mcp-server/security-reports/*.json` contains and renders it in the
  `/admin` dashboard. That directory should only ever be written by the
  automation scripts, not by user-facing code — treat it as trusted input
  only because nothing untrusted can currently write to it.
- Keep port `4000` (MCP server) off any public interface; it is designed to
  be reached by the Laravel app / operators on the same host or private
  network, not by end users.

### Containers

- The container images (`Containerfile`, `mcp-server/Containerfile`) already
  run as a non-root user with a read-only root filesystem and
  `no-new-privileges` in `podman-compose.yml` — see
  `docs/operate/container-security.md`. Keep that hardening when changing
  either Containerfile.
- `.env`, OAuth/app keys, and `auth.json` (written transiently by
  `publish-containers.yml` to install the private `livewire/flux-pro`
  package) are deliberately excluded from build context/images — do not
  reintroduce a `COPY .env` or bake `auth.json` into a shipped layer.

## Protect These Secrets

- `.env` (in particular `APP_KEY`, `DB_*`, `MCP_API_KEY`, `MAIL_*`
  credentials, and any `COMPOSER_AUTH` value used locally for Flux Pro).
- The `COMPOSER_AUTH` GitHub Actions/Environment secret (Flux Pro license —
  billed per-seat; a leak lets someone else consume your license or resolve
  your private packages).
- `database/database.sqlite` (or the MariaDB/PostgreSQL credentials in
  production) — contains password hashes, 2FA secrets/recovery codes, and
  all stored project/page-builder content.
- `storage/` (logs, cached views, session files when file-based) and
  `mcp-server/security-reports/` (dependency-audit output; low sensitivity
  but not meant for public exposure).
- Any GitHub token available to `mcp-server/security-automation.js` (repo
  write access) and to `php-security-update.yml` (`contents: write`,
  `pull-requests: write`).

## Publishing A Fork

Before pushing a public fork or mirror, run:

```bash
git status --short
git check-ignore -v .env database/database.sqlite storage/logs auth.json
git grep -n -I -E "(sk-[A-Za-z0-9_-]{20,}|AIza[0-9A-Za-z_-]{20,}|Bearer [A-Za-z0-9._~+/-]{20,})" -- . ':!package-lock.json' ':!composer.lock'
```

Only `.env.example`, docs, source, tests, and static/build assets should be
committed. Never commit a live `.env`, `database.sqlite`, session/cache
files, `auth.json`, API keys, or password hashes.

## Reporting

Please report vulnerabilities privately via GitHub security advisories on
this repository, or by opening a minimal issue that does not disclose
exploit details. Do not open a public issue with a working proof-of-concept
against a real deployment.
