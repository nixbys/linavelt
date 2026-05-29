# Roadmap Execution Plan

This plan turns the roadmap direction into executable, measurable workstreams.

## Workstream 1: Module Generation and Admin Controls

### Initiation Steps

1. Baseline current module template matrix in `config/linavelt.php`.
2. Add versioned module schema notes for generated outputs.
3. Add admin-facing controls for module generation status and retry actions.

### Execution Workflow

1. Define template schema contracts (input options, output files, validation rules).
2. Implement generation orchestration with idempotent writes and failure metadata.
3. Add admin controls and audit log events around generation actions.
4. Validate with feature tests for success, failure, and retry paths.

### Exit Criteria

1. New and existing module profiles are generated deterministically.
2. Admin controls support monitor, retry, and clear-failure actions.
3. Feature tests cover generation lifecycle edge cases.

## Workstream 2: Builder Integrations

### Initiation Steps

1. Establish integration boundary contract between builder UI and Laravel endpoints.
2. Define content payload schema and revision metadata.
3. Add a migration path for saving and restoring page revisions.

### Execution Workflow

1. Implement ingestion endpoints with strict validation.
2. Add persistence and revision history for authored content.
3. Expose publish/draft transitions and rollback operations.
4. Add integration tests for end-to-end authoring flow.

### Exit Criteria

1. Builder-authored content can be created, revised, published, and rolled back.
2. Payload validation and authorization guardrails are enforced.
3. Integration tests verify lifecycle transitions.

## Workstream 3: Automation Observability

### Initiation Steps

1. Standardize machine-readable report shape for automation outputs.
2. Add a consolidated observability command for latest automation state.

### Execution Workflow

1. Continue daily and weekly security automation output capture.
2. Emit dashboard-friendly summary from latest state + report artifacts.
3. Integrate summary command into operations runbook and quick checks.

### Exit Criteria

1. `npm run automation:report` returns a stable JSON summary.
2. Latest run status and latest report are visible without log scraping.
3. Runbook includes observability command usage.

## Workstream 4: Release Workflow Tightening

### Initiation Steps

1. Add release-readiness workflow for PR validation.
2. Validate web asset build and Electron release prerequisites in CI.

### Execution Workflow

1. Enforce web build readiness on PRs.
2. Run Electron packaging smoke validation in CI.
3. Track release-readiness failures as blocking quality signals.

### Exit Criteria

1. PRs run release-readiness workflow automatically.
2. Web build and Electron readiness checks pass before merge.
3. Failing readiness checks are visible and actionable.

## Execution Status (Initial)

- [x] Workstream 3 initiated with `automation:report` command.
- [x] Workstream 4 initiated with `release-readiness` workflow and Electron smoke checks.
- [ ] Workstream 1 implementation pending schema and admin-control milestones.
- [ ] Workstream 2 implementation pending contract and persistence milestones.

## Next Recommended Iteration

1. Implement module schema versioning + admin retry controls.
2. Add builder revision persistence and publish/rollback API flow.
3. Surface observability JSON in an admin dashboard widget.
