'use strict';

const fs = require('fs');
const path = require('path');

const MCP_DIR = __dirname;
const REPORT_DIR = path.join(MCP_DIR, 'security-reports');
const STATE_FILE = path.join(MCP_DIR, 'security-automation-state.json');
const SCHEDULER_STATE_FILE = path.join(MCP_DIR, 'scheduler-state.json');

function safeReadJson(filePath, fallback) {
    try {
        return JSON.parse(fs.readFileSync(filePath, 'utf8'));
    } catch {
        return fallback;
    }
}

function isoOrNull(value) {
    if (!value || typeof value !== 'number') {
        return null;
    }

    try {
        return new Date(value).toISOString();
    } catch {
        return null;
    }
}

function getLatestReportSummary() {
    if (!fs.existsSync(REPORT_DIR)) {
        return null;
    }

    const files = fs.readdirSync(REPORT_DIR)
        .filter((name) => name.endsWith('.json'))
        .sort();

    if (files.length === 0) {
        return null;
    }

    const latestFile = files[files.length - 1];
    const report = safeReadJson(path.join(REPORT_DIR, latestFile), {});

    const npmRoot = report.audits && report.audits.npmRoot ? report.audits.npmRoot.summary : null;
    const npmMcp = report.audits && report.audits.npmMcpServer ? report.audits.npmMcpServer.summary : null;
    const composer = report.audits && report.audits.composer ? report.audits.composer.summary : null;

    return {
        file: latestFile,
        timestamp: report.timestamp || null,
        dependabotOpen: report.githubAlerts ? report.githubAlerts.dependabotOpen : null,
        codeScanningOpen: report.githubAlerts ? report.githubAlerts.codeScanningOpen : null,
        npmRootVulnerabilities: npmRoot || null,
        npmMcpVulnerabilities: npmMcp || null,
        composerSummary: composer || null,
    };
}

function main() {
    const automationState = safeReadJson(STATE_FILE, {});
    const schedulerState = safeReadJson(SCHEDULER_STATE_FILE, {});
    const latestReport = getLatestReportSummary();

    const payload = {
        generatedAt: new Date().toISOString(),
        automationState: {
            lastDailyAuditAt: isoOrNull(automationState.lastDailyAuditAt),
            lastWeeklyPushAt: isoOrNull(automationState.lastWeeklyPushAt),
            lastRepoHealthFixAt: isoOrNull(automationState.lastRepoHealthFixAt),
        },
        schedulerState: {
            lastRunStartedAt: isoOrNull(schedulerState.lastRunStartedAt),
            lastRunFinishedAt: isoOrNull(schedulerState.lastRunFinishedAt),
            lastRunSucceeded: typeof schedulerState.lastRunSucceeded === 'boolean'
                ? schedulerState.lastRunSucceeded
                : null,
            lockActive: Boolean(schedulerState.lockActive),
        },
        latestReport,
    };

    process.stdout.write(`${JSON.stringify(payload, null, 2)}\n`);
}

main();
