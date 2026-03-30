'use strict';

const { app, BrowserWindow, shell } = require('electron');
const { spawn } = require('child_process');
const path = require('path');
const http = require('http');

const LARAVEL_PORT = 8500;
const LARAVEL_URL = `http://127.0.0.1:${LARAVEL_PORT}`;
const APP_ROOT = path.join(__dirname, '..');

let mainWindow = null;
let phpServer = null;

/**
 * Poll the Laravel dev server until it responds or retries are exhausted.
 *
 * @param {string} url
 * @param {number} retries
 * @param {number} delay  milliseconds between attempts
 * @returns {Promise<void>}
 */
function waitForServer(url, retries = 30, delay = 500) {
    return new Promise((resolve, reject) => {
        const attempt = () => {
            http.get(url, () => {
                resolve();
            }).on('error', () => {
                if (retries-- > 0) {
                    setTimeout(attempt, delay);
                } else {
                    reject(new Error('Laravel server did not start in time'));
                }
            });
        };
        attempt();
    });
}

/** Spawn `php artisan serve` as a child process. */
function startLaravelServer() {
    phpServer = spawn(
        'php',
        ['artisan', 'serve', `--port=${LARAVEL_PORT}`, '--no-interaction'],
        { cwd: APP_ROOT, stdio: 'pipe' }
    );

    phpServer.stdout.on('data', (data) => {
        process.stdout.write(`[PHP] ${data}`);
    });

    phpServer.stderr.on('data', (data) => {
        process.stderr.write(`[PHP] ${data}`);
    });

    phpServer.on('close', (code) => {
        console.log(`[PHP] server exited with code ${code}`);
    });
}

/** Create and configure the main BrowserWindow. */
function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        title: 'Linavelt',
        webPreferences: {
            preload: path.join(__dirname, 'preload.cjs'),
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true,
        },
    });

    mainWindow.loadURL(LARAVEL_URL);

    mainWindow.on('closed', () => {
        mainWindow = null;
    });

    // Open external links in the system browser instead of a new Electron window.
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url.startsWith(LARAVEL_URL)) {
            return { action: 'allow' };
        }
        shell.openExternal(url);
        return { action: 'deny' };
    });
}

app.whenReady().then(async () => {
    startLaravelServer();

    try {
        await waitForServer(LARAVEL_URL);
    } catch (err) {
        console.error(err.message);
        app.quit();
        return;
    }

    createWindow();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('before-quit', () => {
    if (phpServer) {
        phpServer.kill();
    }
});
