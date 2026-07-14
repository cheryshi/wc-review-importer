# AI Playbook

This playbook defines how an AI engineer works in the WooCommerce Review Importer repository. It is mandatory project guidance, not optional advice.

## Core Rule

Work one approved milestone at a time. Do not continue automatically to the next milestone.

## 1. Requirement Analysis

Before any task:

1. Read the required documents listed in `.github/copilot-instructions.md`.
2. Identify the user request type: documentation, planning, implementation, review, or verification.
3. Identify the active milestone in `docs/TODO.md`.
4. Compare the request against `docs/PRD.md`.
5. State any mismatch or missing information before editing files.

Output should include:

- Scope summary.
- Files expected to change.
- Files that must not change.
- Approval needed, if any.

## 2. Architecture Analysis

Before implementation or structural documentation changes:

1. Read `docs/ARCHITECTURE.md`.
2. Locate the relevant namespace and service boundary.
3. Confirm dependency direction.
4. Confirm whether the bootstrap file must remain unchanged.
5. Confirm whether `WCRI\Plugin` remains a composition root.
6. Identify whether new interfaces are needed.

Architecture questions to ask:

- Does this belong in Admin, Ajax, CSV, Importer, Product, Review, Logger, Settings, Security, or Support?
- Does this class have one responsibility?
- Can this change be tested independently?
- Does this introduce coupling that future milestones will regret?

## 3. Milestone Planning

Before coding a milestone:

1. Restate the milestone goal.
2. List planned files.
3. Explain architecture decisions.
4. Identify security and performance concerns.
5. Identify verification steps.
6. Wait for approval when the user requested approval gates.

A milestone plan must be smaller than the milestone itself. Do not turn planning into implementation.

## 4. Implementation

During implementation:

- Change only files required by the approved milestone.
- Prefer existing architecture and naming.
- Use WordPress APIs and WooCommerce APIs.
- Avoid static classes unless lifecycle or WordPress integration justifies them.
- Avoid global variables.
- Avoid placeholder implementations.
- Avoid unrelated refactors.
- Keep public methods documented.
- Keep input sanitization and output escaping explicit.

For documentation-only tasks, do not modify PHP files.

## 5. Self Review

After changes:

1. Reread every changed file.
2. Check for scope creep.
3. Check namespacing and file paths.
4. Check public method PHPDoc.
5. Check WordPress compatibility.
6. Check whether documentation needs updating.
7. Confirm no forbidden actions occurred.

Self-review should happen before final response.

## 6. Security Review

For each implementation task, verify:

- Capability checks are present where needed.
- Nonces protect state-changing admin and AJAX actions.
- Inputs are sanitized.
- Outputs are escaped.
- Uploads are validated by extension, MIME type, and readability.
- CSV-derived values are protected against spreadsheet injection when exported or displayed.
- Error messages do not leak secrets or sensitive paths.
- No direct SQL is used unless documented and approved.
- No customer data, private store data, or credentials are committed.

Security issues block milestone completion.

## 7. Performance Review

For import-related work, verify:

- CSV files are streamed.
- Rows are processed in batches.
- Batch size defaults to 100 and is configurable.
- Job progress is persisted after each batch.
- AJAX responses are compact.
- Duplicate detection is not obviously quadratic for large imports.
- Rating recalculation is controlled.
- Large imports can target 100,000+ rows.

Performance issues must be resolved before proceeding to the next milestone.

## 8. Documentation Updates

Update documentation when:

- Milestone scope changes.
- Architecture changes.
- Workflow changes.
- Public behavior changes.
- Security requirements change.
- Performance expectations change.
- Acceptance criteria change.

Documentation updates should be committed separately from feature work unless the user explicitly asks for a combined milestone commit.

## 9. Git Workflow

Preferred flow:

1. Inspect current repository state.
2. Make a focused change.
3. Verify changed files.
4. Commit with a conventional-style message.
5. Report commit SHA and summary.
6. Stop for approval when the milestone is complete.

Commit message examples:

- `docs: establish AI engineering workflow`
- `feat: add plugin bootstrap`
- `fix: harden activation checks`
- `refactor: separate settings repository`
- `test: add CSV parser fixtures`

Never:

- Force-push without approval.
- Rewrite history without approval.
- Delete files without approval.
- Rename directories without approval.
- Commit unrelated changes.

## 10. Approval Workflow

Ask for approval before:

- Starting a new milestone.
- Deleting files.
- Renaming directories.
- Adding dependencies.
- Changing architecture materially.
- Modifying unrelated files.
- Continuing after a blocked or ambiguous request.

Stop after:

- Completing a milestone.
- Completing a documentation-only task that asks to wait.
- Finding a conflict between request and documentation.
- Finding a security blocker.

## 11. Review Mode

When asked to review:

1. Prioritize bugs, security issues, regressions, missing tests, and architecture violations.
2. Lead with findings.
3. Include file references where possible.
4. Keep summaries brief.
5. State clearly if no issues are found.
6. Mention residual risk and verification gaps.

Do not silently fix review findings unless the user asks for fixes.

## 12. Documentation-Only Mode

When the user says documentation only:

- Do not edit PHP files.
- Do not add plugin functionality.
- Do not create implementation classes.
- Do not change behavior.
- Update only requested documentation and guidance files.
- Commit documentation in one logical commit when requested.

## 13. Implementation Stop Checklist

Before final response, confirm:

- Required docs were followed.
- Scope was honored.
- No unrelated files changed.
- No forbidden action occurred.
- Security and performance implications were considered.
- Verification status is clear.
- Commit SHA is reported when a commit was created.
