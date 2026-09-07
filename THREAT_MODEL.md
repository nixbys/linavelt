# Threat Model

Linavelt ships three runtimes from one repository: the Laravel web app, an
Electron desktop shell that loads it in a webview, and a Node.js MCP server
that automates repository/dependency maintenance. This document states the
trust boundaries between them so contributors can reason about security
decisions without re-deriving them from the auth stack each time.

## Trust Boundary

The Laravel app is a standard multi-tenant web application: any visitor can
register, but page/project data is scoped per-user and admin functionality
is gated behind `is_admin`. The MCP server is a separate, more privileged
component — it is meant for the repository operator, not end users, and
should never be exposed to them. The threat model tries to prevent:

- Non-admin users reaching `/admin` or another user's projects/page designs.
- The MCP server's automation (which can open pull requests and write to the
  repository) being reachable or triggerable by an unauthenticated caller.
- SQL injection, mass assignment, or CSRF against the Laravel app's write
  paths.
- Session/credential leakage via debug output, logs, or committed secrets.

It does **not** try to prevent an authenticated `is_admin` user from doing
admin things, or a repository operator with shell/CI access from doing
operator things — those are the intended privilege levels, not attacks.

## Roles and Capabilities

| Capability | Guest | Authenticated user | `is_admin` |
|---|---|---|---|
| Register / login / 2FA | ✓ | ✓ | ✓ |
| View public blog (`/blog`) | ✓ | ✓ | ✓ |
| Dashboard, settings, extensions | ✗ | ✓ | ✓ |
| Create/edit/publish own projects & page designs | ✗ | ✓ (own only) | ✓ (own only) |
| View/edit another user's projects or page designs | ✗ | ✗ | ✗ (no admin override exists today) |
| `/admin` dashboard (module-generation summary, automation report) | ✗ | ✗ | ✓ |
| Retry another user's module generation | ✗ | ✗ | ✓ |
| MCP server automation (audits, PR creation) | ✗ | ✗ | operator-only, not tied to `is_admin` |

Two things worth calling out explicitly:

- **`is_admin` does not grant access to other users' content.** Ownership
  checks in `PageBuilderController`/`ProjectController`
  (`abort_unless($model->user_id === Auth::id(), 403)`) run regardless of
  `is_admin` — an admin can see aggregate stats about other users
  (`AdminController::dashboard`) but cannot open their projects or page
  designs through any route that exists today. Keep it that way unless a
  deliberate "admin can view any project" feature is added with its own
  authorization rule, rather than an admin accidentally satisfying an
  ownership check.
- **The MCP server is a separate trust domain from `is_admin`.** It is
  gated by its own `MCP_API_KEY` bearer secret (`mcp-server/server.js`), not
  by the Laravel app's session or `is_admin` flag. A Laravel admin account
  does not, by itself, grant access to MCP endpoints, and vice versa.

## Authentication

- **Sessions:** Laravel Fortify handles login, registration, password
  reset, and email verification. Passwords are hashed with bcrypt
  (`BCRYPT_ROUNDS=12`). Session state uses the `database` driver
  (`SESSION_DRIVER=database`) with a 120-minute lifetime by default.
- **2FA:** Fortify's TOTP two-factor flow is wired up
  (`TwoFactorAuthenticatable` on `User`, `settings/two-factor` route) but is
  opt-in per user, not enforced. There is no policy today that requires
  `is_admin` accounts to enable it — see Known Gaps.
- **Email verification:** required (`MustVerifyEmail`) before reaching the
  dashboard, builder, or project routes (`middleware(['auth', 'verified'])`
  throughout `routes/web.php`); the verification link route is
  `signed`-protected and rate-limited (`throttle:6,1`).
- **CSRF:** Laravel's default `VerifyCsrfToken` middleware covers all
  session-authenticated POST/PUT/DELETE routes, including the page-builder
  and project save/publish/destroy endpoints and Livewire's own request
  cycle. No route in `routes/web.php` opts out of CSRF protection.

## Data Ownership & Authorization

- `PageDesign` and `Project` rows carry `user_id`; every controller action
  that reads or mutates one re-checks `user_id === Auth::id()` before
  proceeding (`PageBuilderController::authorizeOwnership`,
  `ProjectController::gate`). There is no Eloquent global scope enforcing
  this — it is a per-method check, so a new controller method that queries
  `PageDesign::find()`/`Project::find()` directly (instead of going through
  the existing ownership helpers) would reintroduce an IDOR unless it also
  calls the gate. Route model binding alone (`{project}`, `{design}`) does
  **not** imply ownership — it only guarantees the row exists.
- `User::$fillable` includes `is_admin` and the `module_generation_*`
  columns alongside ordinary profile fields. Every current write path
  builds its update array from an explicit Livewire `validate()` call, not
  from raw request input, so this is not exploitable today — see
  `SECURITY.md` for the defense-in-depth recommendation if that ever
  changes.
- Builder/project content (`project_data`, `html`, `css` columns) is
  arbitrary content produced by GrapesJS on the client and stored verbatim;
  no route in this codebase currently renders another user's `html`/`css`
  back into a page (the `data()` JSON endpoints are ownership-gated, and no
  public preview/embed route exists yet). If a public "preview a published
  project" page is added later, its `html` must be served in a sandboxed
  context (e.g. a separate origin or a sandboxed `<iframe>`), since it is
  attacker-influenced markup, not trusted server-rendered output — treat
  that as a design requirement for the feature, not an afterthought.

## Automation Trust (MCP server)

- `mcp-server/security-automation.js` runs `composer audit`/`npm audit` on
  a schedule and, per `docs/operate/SECURITY_AUTOMATION_RUNBOOK.md`, can
  push a branch and open a pull request. Whatever GitHub token it holds has
  write access to this repository and must be scoped/rotated with the same
  care as any other repo-write credential — a compromised MCP server host
  is equivalent to a compromised CI runner, not a harmless read-only tool.
- The MCP server intentionally fails closed when `MCP_API_KEY` is unset
  (administrative endpoints return 503 rather than running unauthenticated
  — see `SECURITY.md`). Do not change that to a permissive default.
- `AdminController::latestAutomationReport()` only ever reads JSON the
  automation scripts themselves wrote under
  `mcp-server/security-reports/`; no user-facing code writes to that
  directory. If that ever changes (e.g. a future feature lets a user upload
  or influence a "report"), the admin dashboard would need to stop treating
  its contents as trusted.

## Known Gaps

These are open and acknowledged, not silently accepted:

1. **2FA is optional, including for `is_admin` accounts.** There is no
   enforcement requiring privileged accounts to enable Fortify's TOTP flow.
   Consider a policy/middleware check for `is_admin` users without
   `two_factor_secret` set.
2. **No admin content-moderation path.** Admins can see aggregate
   module-generation stats but have no route to view or take down another
   user's published project/page design content if it turns out to be
   abusive — this may be intentional today (small user base) but should be
   revisited before opening registration broadly.
3. **No rate limiting on registration/login beyond Fortify's defaults.**
   Fortify ships sane throttling out of the box, but this repo has not
   layered additional abuse controls (e.g. CAPTCHA, IP reputation) on top
   for a public-facing deployment.
4. **Inconsistent request size limits between the two builder save
   endpoints.** `ProjectController::save` caps `html`/`css` at
   `max:5000000`/`max:500000` bytes; `PageBuilderController::save` (the
   legacy page-design builder) validates `html`/`css` as `nullable, string`
   with no length limit at all. Both are behind `throttle:builder-save`
   (30/minute per user/IP), which bounds but does not eliminate storage
   growth from repeated large saves. Bring `PageBuilderController::save`'s
   validation in line with `ProjectController::save`'s limits.
