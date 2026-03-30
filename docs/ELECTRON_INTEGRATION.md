# Electron Integration

Linavelt ships an [Electron](https://www.electronjs.org/) wrapper that lets you run the application as a native desktop app on Linux, macOS, and Windows.

## How it works

| Component | File | Role |
|-----------|------|------|
| Main process | `electron/main.cjs` | Spawns `php artisan serve` on port **8500** and opens a `BrowserWindow` pointed at `http://127.0.0.1:8500`. |
| Preload script | `electron/preload.cjs` | Exposes a minimal, sandboxed API (`window.electron`) to renderer pages via `contextBridge`. |

Electron is a **dev dependency** only.  The standard `npm run build` (Vite) is unchanged and is still used by CI.

## Prerequisites

- Node.js 22+  
- PHP 8.4 with `php artisan serve` available in `$PATH`  
- A configured `.env` file (copy from `.env.example` and run `php artisan key:generate`)

## Running locally

```bash
# 1. Install dependencies
npm install

# 2. Start the desktop app (PHP server is launched automatically)
npm run electron:start
```

## Building a distributable package

```bash
# Build Vite assets then package with electron-builder
npm run electron:build
```

Packages are written to `release/`.  Supported targets:

| Platform | Default format |
|----------|---------------|
| Linux    | AppImage       |
| macOS    | DMG            |
| Windows  | NSIS installer |

To build for the current platform only without creating an installer:

```bash
npm run electron:pack
```

## Security notes

- `nodeIntegration` is **disabled** in every renderer (default safe).
- `contextIsolation` is **enabled** and `sandbox` is **enabled**.
- Only a tiny allowlist (`platform`, `version`) is exposed via `contextBridge`.
- External URLs (links outside `http://127.0.0.1:8500`) are opened in the system browser, not inside Electron.

## CI

The lint and tests workflows set `ELECTRON_SKIP_BINARY_DOWNLOAD=1` during `npm install` so the large Electron binary (~100 MB) is not downloaded in CI where it is not needed.
