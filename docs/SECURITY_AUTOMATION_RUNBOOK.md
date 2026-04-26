# Security Automation Runbook

This runbook describes the full security and repository health automation flow in the MCP server.

## Location

- Script: mcp-server/security-automation.js
- State: mcp-server/security-automation-state.json
- Learning state: mcp-server/automation-learning.json
- Policy profiles: mcp-server/automation-policy.json
- Reports: mcp-server/security-reports/
- Log: mcp-server/security-automation.log

## Execution Phases

1. Daily audit phase
- Runs npm audit in repository root.
- Runs npm audit in mcp-server.
- Runs composer audit.
- Queries GitHub Dependabot and code scanning alerts (if gh is available).
- Writes a dated report JSON in security-reports.

2. Repo health phase (daily)
- Uses a policy-selected set of remediation and validation steps.
- Supports self-learning step prioritization from previous failures.
- Creates or updates a dated health branch and PR.
- Waits for workflow checks and verifies passing states.
- Optional maintenance actions:
  - Cleanup merged remote branches.
  - Upsert one managed failure comment per open PR.

3. Weekly update phase
- Creates or updates weekly security update branch and PR.
- Applies policy-enabled dependency updates.
- Runs policy-enabled workflow-equivalent checks.
- Waits for workflow checks and verifies passing states.

## Policy Profiles

Set profile with environment variable:

- AUTOMATION_POLICY_PROFILE=default
- AUTOMATION_POLICY_PROFILE=ci
- AUTOMATION_POLICY_PROFILE=maintenance-light
- AUTOMATION_POLICY_PROFILE=production-strict

Behavior is controlled in mcp-server/automation-policy.json:

- dailyAudit: enable/disable daily audit phase.
- repoHealth: enable/disable repo health phase.
- weeklyUpdate: enable/disable weekly update phase.
- learning.enabled: enable/disable self-learning persistence.
- steps: toggle individual remediation/check steps:
  - composer-validate
  - style-fix
  - npm-audit-root
  - npm-audit-mcp
  - composer-audit
  - composer-update
  - build
  - tests
- maintenance:
  - cleanupMergedBranches
  - reportOpenPrFailures

## Self-Learning Model

The automation records failures by category and stage in automation-learning.json.
On subsequent runs, categories with higher failure counts are prioritized earlier in the repo health step order.

Learning categories include style, tests, npm-audit, composer-audit, build, composer-validate, permissions, and workflow-derived categories.

## PR Comment Guardrails

Managed comments are identified with this marker:

<!-- mcp-security-automation -->

Guardrails:

- Failure reports are upserted, not appended repeatedly.
- Duplicate managed comments are deleted automatically.
- Managed comments are removed from PRs when failures clear.

## Failure Recovery

1. GitHub checks could not be verified
- Script retries check retrieval.
- Script falls back to statusCheckRollup when direct checks retrieval fails.
- If verification still fails, run exits non-zero.

2. Broken local dependencies
- Re-run:
  - npm install
  - cd mcp-server && npm install
  - composer install

3. Stash pop conflicts
- Script logs warning if stash restore fails.
- Resolve manually using git stash list and git stash show -p.

4. Branch push/check failures
- Inspect PR checks with gh pr checks <number> --repo <owner/repo>.
- Fix locally and re-run automation.

## Operational Commands

Run automation now:

node mcp-server/security-automation.js

Force all phases to run on next execution:

node -e "const fs=require('fs');fs.writeFileSync('mcp-server/security-automation-state.json', JSON.stringify({lastDailyAuditAt:0,lastWeeklyPushAt:0,lastRepoHealthFixAt:0},null,2));"

Use CI profile:

AUTOMATION_POLICY_PROFILE=ci node mcp-server/security-automation.js

Use production-strict profile:

AUTOMATION_POLICY_PROFILE=production-strict node mcp-server/security-automation.js

## Validation Checklist

- Script syntax passes: node --check mcp-server/security-automation.js
- Policy file is valid JSON.
- security-reports contains a fresh dated report.
- Repo health and weekly PR checks reach passing state.
- No duplicate managed failure comments remain on open PRs.
