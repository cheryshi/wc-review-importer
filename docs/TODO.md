# Development Milestones

This project is developed one milestone at a time. Do not start a later milestone until the current milestone has been reviewed and explicitly approved.

## Milestone Rules

- Read the required documentation before every milestone.
- Explain planned file changes before implementation.
- Explain architecture decisions before implementation.
- Keep each milestone in a focused commit.
- Do not modify unrelated files.
- Do not continue automatically to the next milestone.
- Update documentation when milestone scope or architecture changes.

## Milestone 0: Documentation-Driven Project Setup

Status: In progress

Goal: Establish repository guidance, project documentation, and AI engineering workflow.

Tasks:

- [x] Create `README.md`.
- [x] Create `docs/PRD.md`.
- [x] Create `docs/ARCHITECTURE.md`.
- [x] Create `docs/CODING_GUIDELINES.md`.
- [x] Create `docs/TODO.md`.
- [x] Create `docs/CHANGELOG.md`.
- [x] Create `.github/copilot-instructions.md`.
- [x] Create `docs/AI_PLAYBOOK.md`.
- [ ] Review all documentation for consistency.
- [ ] Approve transition to Milestone 1.

Acceptance criteria:

- Documentation defines roles, workflow, architecture, milestones, safety rules, and approval gates.
- AI contributors can understand how to work in the repository without additional context.
- No plugin functionality is introduced by this milestone.

## Milestone 1: Plugin Bootstrap

Status: Implemented, pending final approval

Goal: Create the minimum safe WordPress plugin bootstrap.

Tasks:

- [x] Create main plugin file with WordPress plugin headers.
- [x] Define plugin constants for version, paths, URLs, basename, text domain, options, and minimum requirements.
- [x] Add a `WCRI` autoloader mapped to `includes/`.
- [x] Add activation and deactivation hooks.
- [x] Add PHP, WordPress, and WooCommerce requirement checks.
- [x] Add conservative default option seeding.
- [x] Add central `WCRI\Plugin` composition root.
- [x] Load text domain on `init`.
- [ ] Run PHP syntax checks in an environment with PHP available.
- [ ] Validate activation on a WordPress/WooCommerce test site.

Acceptance criteria:

- Plugin activates only when requirements are met.
- Plugin bootstrap remains thin.
- `WCRI\Plugin` does not become a God Object.
- No admin, importer, CSV, AJAX, or logging features are implemented yet.

## Milestone 2: Core Architecture Skeleton

Status: Not started

Goal: Add class skeletons and service boundaries without business-heavy implementation.

Tasks:

- [ ] Create namespaces and directories for Admin, Ajax, CSV, Importer, Product, Review, Logger, Settings, Security, and Support.
- [ ] Add interfaces for replaceable strategies where needed.
- [ ] Add service registration points in `WCRI\Plugin`.
- [ ] Keep service construction explicit and easy to review.
- [ ] Add PHPDoc for all public methods.
- [ ] Verify bootstrap does not need unrelated changes.

Acceptance criteria:

- Responsibilities are clear before feature logic is added.
- Future services can be wired without rewriting the bootstrap.
- No CSV import behavior is implemented yet unless explicitly approved.

## Milestone 3: Settings Foundation

Status: Not started

Goal: Add settings storage and validation.

Tasks:

- [ ] Register plugin settings with WordPress Settings API.
- [ ] Add defaults for review status, verified owner, batch size, duplicate detection, logging, and execution limits.
- [ ] Sanitize all settings.
- [ ] Escape all settings output.
- [ ] Add settings repository methods for future services.

Acceptance criteria:

- Settings are safe, typed consistently, and reusable by importer services.
- No import UI or import execution is required in this milestone.

## Milestone 4: Admin Interface Shell

Status: Not started

Goal: Add the WooCommerce admin page without running imports.

Tasks:

- [ ] Add WooCommerce -> Import Reviews submenu.
- [ ] Render admin page structure.
- [ ] Add placeholders for upload, options, progress, statistics, and logs.
- [ ] Enqueue admin assets only on plugin pages.
- [ ] Add nonce fields and capability checks.

Acceptance criteria:

- Admin page loads safely.
- UI does not perform import work yet.
- Admin classes do not contain importer business logic.

## Milestone 5: CSV Template And Upload Validation

Status: Not started

Goal: Add safe CSV template download and upload validation.

Tasks:

- [ ] Add sample CSV template.
- [ ] Add template download action.
- [ ] Validate CSV extension and MIME type.
- [ ] Validate readability and required headers.
- [ ] Store uploads in a controlled plugin-owned location.
- [ ] Reject invalid uploads with clear errors.

Acceptance criteria:

- Uploads are validated before any import job exists.
- Unknown columns are tolerated.
- Missing `sku` header is rejected.

## Milestone 6: Streaming CSV Parser

Status: Not started

Goal: Parse CSV files safely without loading entire files into memory.

Tasks:

- [ ] Implement header normalization.
- [ ] Implement row streaming.
- [ ] Preserve row numbers.
- [ ] Support UTF-8, emoji, multiline reviews, and special characters.
- [ ] Return structured row objects or arrays.
- [ ] Convert malformed rows into row-level errors.

Acceptance criteria:

- Parser does not create reviews or match products.
- Parser can support large files.

## Milestone 7: Import Job State

Status: Not started

Goal: Persist import session state for batch processing.

Tasks:

- [ ] Create job entity or value object.
- [ ] Create job repository.
- [ ] Track status, file path, current row, counters, timestamps, and affected products.
- [ ] Support pending, running, paused, cancelled, completed, and failed states.
- [ ] Add stale job cleanup design.

Acceptance criteria:

- Progress can be resumed from saved state.
- Job state does not require custom database tables.

## Milestone 8: Product Matching

Status: Not started

Goal: Resolve imported rows to WooCommerce products.

Tasks:

- [ ] Define product matcher interface.
- [ ] Implement SKU matcher.
- [ ] Return structured match results.
- [ ] Log not-found and invalid product references as row-level outcomes.

Acceptance criteria:

- Product matching is replaceable.
- Future Product ID, GTIN, UPC, EAN, and ASIN matchers can be added without changing importer orchestration.

## Milestone 9: Review Validation And Creation

Status: Not started

Goal: Create WooCommerce-compatible reviews through WordPress APIs.

Tasks:

- [ ] Validate rating, email, name, content, and date.
- [ ] Apply settings defaults.
- [ ] Create comments through WordPress comment APIs.
- [ ] Store WooCommerce rating metadata.
- [ ] Store verified-owner metadata.
- [ ] Respect approval status.

Acceptance criteria:

- No review is created through direct SQL.
- One invalid row does not stop the import.

## Milestone 10: Duplicate Detection

Status: Not started

Goal: Skip duplicate reviews when enabled.

Tasks:

- [ ] Detect duplicates by product, email, and review content.
- [ ] Keep checks efficient for large imports.
- [ ] Log skipped duplicates.
- [ ] Allow duplicate detection to be disabled.

Acceptance criteria:

- Duplicate handling is optional and row-level.

## Milestone 11: Rating And Cache Updates

Status: Not started

Goal: Refresh WooCommerce rating data after review creation.

Tasks:

- [ ] Recalculate average rating.
- [ ] Recalculate review count.
- [ ] Recalculate rating histogram metadata where applicable.
- [ ] Clear WooCommerce caches and transients.
- [ ] Avoid excessive repeated recalculation.

Acceptance criteria:

- Frontend rating data updates correctly after import.

## Milestone 12: Logging System

Status: Not started

Goal: Record safe, useful, downloadable import logs.

Tasks:

- [ ] Implement info, warning, error, skipped, and summary log types.
- [ ] Store logs per import session.
- [ ] Include row number and product reference where useful.
- [ ] Sanitize stored log data.
- [ ] Escape displayed log data.
- [ ] Add protected log download.

Acceptance criteria:

- Logs help administrators diagnose imports without exposing unsafe raw input.

## Milestone 13: AJAX Batch Import

Status: Not started

Goal: Run imports through safe batch AJAX requests.

Tasks:

- [ ] Add start endpoint.
- [ ] Add process next batch endpoint.
- [ ] Add pause endpoint.
- [ ] Add resume endpoint.
- [ ] Add cancel endpoint.
- [ ] Return compact progress responses.
- [ ] Verify nonce and capability on every request.

Acceptance criteria:

- Imports do not run in one long request.
- Default batch size is 100 rows.

## Milestone 14: Admin Progress And Statistics

Status: Not started

Goal: Display real-time import progress and outcomes.

Tasks:

- [ ] Update progress percentage.
- [ ] Display processed, imported, skipped, warning, and error counts.
- [ ] Display recent logs.
- [ ] Handle pause, resume, cancel, completion, and AJAX failure states.

Acceptance criteria:

- Admin UI reflects job state accurately.

## Milestone 15: Testing And Quality Gates

Status: Not started

Goal: Verify correctness, security, and scale.

Tasks:

- [ ] Test activation and deactivation.
- [ ] Test valid small CSV import.
- [ ] Test invalid CSV input.
- [ ] Test missing SKU and SKU not found.
- [ ] Test invalid rating and invalid email.
- [ ] Test duplicate detection.
- [ ] Test UTF-8, emoji, multiline, and long reviews.
- [ ] Test interrupted import and resume.
- [ ] Test cancel and cleanup.
- [ ] Test large import target of 100,000 rows.
- [ ] Run PHP syntax checks.
- [ ] Run WordPress Coding Standards when tooling is available.

Acceptance criteria:

- No PHP warnings, notices, or deprecated messages.
- Security and performance reviews are complete.

## Milestone 16: Packaging And Release Readiness

Status: Not started

Goal: Prepare a production-ready plugin ZIP.

Tasks:

- [ ] Confirm installable ZIP structure.
- [ ] Exclude development-only files when needed.
- [ ] Confirm uninstall behavior is documented and safe.
- [ ] Update README and changelog.
- [ ] Perform final WooCommerce store test.

Acceptance criteria:

- Plugin can be installed from WordPress admin as a ZIP package.
- Documentation matches implemented behavior.
