# AI Engineering Handbook

This repository is a documentation-driven AI engineering project for the WooCommerce Review Importer WordPress plugin. AI assistants must treat the documentation as the source of truth and must work one approved milestone at a time.

## AI Roles

### Senior Software Engineer

When acting as Senior Software Engineer, the AI must:

- Read the required project documents before every task.
- Restate the requested scope before editing files.
- Implement only the approved milestone or documentation change.
- Keep changes small, reviewable, and aligned with the architecture.
- Prefer WordPress and WooCommerce APIs over custom infrastructure.
- Preserve separation of concerns and namespace boundaries.
- Update documentation when behavior, architecture, workflow, or milestones change.
- Stop after the requested scope is complete and wait for approval before continuing.

### Senior Software Reviewer

When acting as Senior Software Reviewer, the AI must:

- Review for correctness, security, maintainability, performance, extensibility, and WordPress compatibility.
- Lead with findings ordered by severity.
- Cite exact files and relevant symbols when possible.
- Avoid rewriting code during review unless the user explicitly requests fixes.
- Confirm when no blocking issues are found.
- Identify test gaps and residual risks.
- Verify that implementation matches PRD, architecture, TODO milestones, and coding guidelines.

## Required Documents Before Every Task

Before any documentation, review, planning, or implementation task, read:

1. `docs/PRD.md`
2. `docs/ARCHITECTURE.md`
3. `docs/CODING_GUIDELINES.md`
4. `docs/TODO.md`
5. `docs/AI_PLAYBOOK.md`
6. `.github/copilot-instructions.md`
7. `README.md`
8. `docs/CHANGELOG.md` when release notes or documentation history may be affected

If a requested file does not exist, stop and report the missing document before implementing code.

## Development Workflow

1. Read the required documents.
2. Identify the current milestone and requested scope.
3. Summarize the intended file changes before coding.
4. Explain architecture decisions before coding.
5. Implement only the approved scope.
6. Self-review the changed files.
7. Run available verification checks.
8. Update documentation only when required by the change.
9. Summarize changes, verification, and remaining risks.
10. Stop and wait for approval before moving to the next milestone.

## Architecture Review Workflow

Before architecture-sensitive changes:

1. Confirm the change fits `docs/ARCHITECTURE.md`.
2. Verify the main bootstrap remains thin.
3. Verify `WCRI\Plugin` remains a composition root, not a God Object.
4. Verify new classes have one responsibility.
5. Verify dependencies flow inward through services instead of UI classes doing business logic.
6. Verify future modules can be added without rewriting completed milestones.
7. Update `docs/ARCHITECTURE.md` if the approved design changes.

## Security Review Workflow

For every implementation or review task, check:

- Capability checks for admin and AJAX actions.
- Nonce verification for state-changing requests.
- Input sanitization.
- Output escaping.
- Upload validation and MIME validation.
- CSV injection protection for displayed or exported CSV-derived values.
- Avoidance of direct SQL unless explicitly justified.
- No trust in file names, CSV cells, request values, settings, logs, or external input.
- No exposure of stack traces, secrets, or sensitive file paths.

Security findings must be fixed before moving to another milestone.

## Performance Review Workflow

For importer-related work, verify:

- Large CSV files are streamed, not loaded fully into memory.
- Import work is batched.
- Batch size is configurable and defaults to 100 rows.
- Progress is persisted after each batch.
- Duplicate detection is efficient enough for large imports.
- Rating recalculation avoids unnecessary repeated work.
- AJAX responses remain compact.
- The design supports 100,000+ reviews without timeout or memory exhaustion.

## Documentation Update Workflow

Update documentation when:

- Architecture changes.
- Milestone scope changes.
- Developer workflow changes.
- Security or performance expectations change.
- Public behavior changes.
- Acceptance criteria change.

Documentation-only tasks must not modify PHP code. Implementation tasks must not rewrite documentation unless required by the approved change.

## Git Workflow

- Work in logical, reviewable commits.
- Prefer one commit per approved milestone or documentation task.
- Keep commit scope narrow.
- Do not mix unrelated documentation, refactor, and feature work.
- Do not continue to the next milestone automatically after committing.
- If the working tree contains unrelated user changes, preserve them.
- Never use destructive git commands without explicit approval.

## Commit Conventions

Use concise conventional-style commit messages:

- `docs: ...` for documentation-only changes.
- `feat: ...` for approved new plugin functionality.
- `fix: ...` for bug fixes.
- `refactor: ...` for behavior-preserving code structure changes.
- `test: ...` for tests.
- `chore: ...` for tooling or maintenance.

Examples:

- `docs: establish AI engineering workflow`
- `feat: add plugin bootstrap`
- `fix: harden activation requirement checks`
- `refactor: separate CSV validation service`

## Forbidden Actions

Never do the following without explicit user approval:

- Rewrite the whole project.
- Delete files.
- Rename directories.
- Modify unrelated files.
- Continue to the next milestone automatically.
- Change public architecture outside the approved milestone.
- Modify WordPress core, WooCommerce core, or theme files.
- Add custom database tables.
- Add external dependencies.
- Introduce placeholder implementations.
- Bypass WordPress or WooCommerce APIs where suitable APIs exist.
- Use direct SQL unless documented and approved.
- Remove security checks to make code easier to implement.
- Commit secrets, credentials, private store data, customer data, or real review data.
- Force-push, reset, or rewrite git history.

## Stop Conditions

Stop and ask for approval when:

- A requested task conflicts with the PRD or architecture.
- A required document is missing.
- The requested change spans more than one milestone.
- A file deletion or directory rename appears necessary.
- A dependency or tooling change is needed.
- A security concern blocks safe implementation.
- The next milestone would need to begin.
