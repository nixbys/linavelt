import fs from 'node:fs';
import path from 'node:path';

const root = process.cwd();

function fail(message) {
  console.error(`[FAIL] ${message}`);
  process.exit(1);
}

function pass(message) {
  console.log(`[PASS] ${message}`);
}

const packagePath = path.join(root, 'package.json');
if (!fs.existsSync(packagePath)) {
  fail('package.json is missing');
}

const pkg = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
const requiredScripts = ['electron:start', 'electron:pack', 'electron:build'];
for (const script of requiredScripts) {
  if (!pkg.scripts || !pkg.scripts[script]) {
    fail(`Missing npm script: ${script}`);
  }
}
pass('Electron npm scripts are defined');

const mainEntry = path.join(root, 'electron', 'main.cjs');
const preloadEntry = path.join(root, 'electron', 'preload.cjs');
if (!fs.existsSync(mainEntry)) {
  fail('electron/main.cjs is missing');
}
if (!fs.existsSync(preloadEntry)) {
  fail('electron/preload.cjs is missing');
}
pass('Electron entry files are present');

const buildConfig = pkg.build || {};
const linux = buildConfig.linux || {};
const mac = buildConfig.mac || {};
const win = buildConfig.win || {};
if (!linux.target || !mac.target || !win.target) {
  fail('electron-builder targets for linux/mac/win must be configured');
}
pass('electron-builder cross-platform targets are configured');

console.log('[PASS] Electron release smoke checks complete');
