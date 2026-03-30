'use strict';

const { contextBridge } = require('electron');

/**
 * Expose a minimal, safe API surface to renderer processes via contextBridge.
 * No Node.js internals are exposed to keep renderer context isolated.
 */
contextBridge.exposeInMainWorld('electron', {
    /** The host platform identifier, e.g. 'linux', 'darwin', 'win32'. */
    platform: process.platform,
    /** The running Electron version string. */
    version: process.versions.electron,
});
