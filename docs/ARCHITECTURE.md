# Architecture

WooCommerce Review Importer is a modular WordPress plugin for importing WooCommerce product reviews from CSV files. The repository is documentation-driven: architecture, milestones, and AI workflow must be reviewed before implementation changes.

The plugin architecture separates bootstrap, composition, admin presentation, upload handling, CSV parsing, import orchestration, product matching, review creation, rating updates, logging, settings, security, and support utilities.

## Architectural Principles

- Keep `wc-review-importer.php` as a thin bootstrap only.
- Keep `WCRI\Plugin` as a composition root, not a God Object.
- Keep importer logic independent from the admin UI.
- Use WordPress and WooCommerce APIs whenever possible.
- Use streaming and batch processing for large imports.
- Prefer small classes with one clear responsibility.
- Use explicit namespaces and dependency direction.
- Avoid custom database tables for v1.0.
- Avoid modifying WordPress core, WooCommerce core, themes, or `functions.php`.

## Directory Structure

Planned production structure:

```text
wc-review-importer/
  wc-review-importer.php
  README.md
  .github/
    copilot-instructions.md
  docs/
    AI_PLAYBOOK.md
    ARCHITECTURE.md
    CHANGELOG.md
    CODING_GUIDELINES.md
    PRD.md
    TODO.md
  includes/
    Plugin.php
    Admin/
      AdminMenu.php
      ImportPage.php
      AssetManager.php
      TemplateController.php
    Ajax/
      ImportAjaxController.php
    CSV/
      CsvUploadHandler.php
      CsvValidator.php
      CsvParser.php
      CsvHeader.php
      CsvRow.php
    Importer/
      ImportController.php
      ImportJob.php
      ImportJobRepository.php
      BatchProcessor.php
      ImportResult.php
    Product/
      ProductMatcherInterface.php
      SkuProductMatcher.php
      ProductMatchResult.php
    Review/
      ReviewValidator.php
      ReviewCreator.php
      DuplicateDetector.php
      RatingUpdater.php
    Logger/
      LoggerInterface.php
      ImportLogger.php
      LogEntry.php
      LogExporter.php
    Settings/
      SettingsRepository.php
      SettingsPage.php
    Security/
      CapabilityChecker.php
      NonceVerifier.php
    Support/
      Activator.php
      Requirements.php
      FileSystem.php
      Sanitizer.php
      ResponseFactory.php
  assets/
    css/admin.css
    js/admin-import.js
  templates/
    admin/import-page.php
  sample/
    reviews-template.csv
  languages/
  tests/
```

Directories should be added only when their milestone requires them. Do not create empty speculative folders unless the approved milestone calls for them.

## Namespace Design

Namespace root: `WCRI`.

Namespace map:

| Namespace | Directory | Purpose |
| --- | --- | --- |
| `WCRI` | `includes/` | Composition root and top-level plugin services. |
| `WCRI\Admin` | `includes/Admin/` | WordPress admin UI, menu, assets, and templates. |
| `WCRI\Ajax` | `includes/Ajax/` | AJAX request controllers and response coordination. |
| `WCRI\CSV` | `includes/CSV/` | Upload validation, header mapping, and streaming parsing. |
| `WCRI\Importer` | `includes/Importer/` | Import sessions, job state, batch processing, and orchestration. |
| `WCRI\Product` | `includes/Product/` | Product matching strategies. |
| `WCRI\Review` | `includes/Review/` | Review validation, creation, duplicate checks, and rating updates. |
| `WCRI\Logger` | `includes/Logger/` | Import logs and log export. |
| `WCRI\Settings` | `includes/Settings/` | Settings storage, defaults, sanitization, and settings UI. |
| `WCRI\Security` | `includes/Security/` | Capability checks and nonce verification. |
| `WCRI\Support` | `includes/Support/` | Requirements, activation, filesystem, sanitization, and response helpers. |

Autoloading maps `WCRI\Some\ClassName` to `includes/Some/ClassName.php`.

## Class Responsibilities

### Bootstrap And Core

| Class or File | Responsibility |
| --- | --- |
| `wc-review-importer.php` | WordPress plugin headers, constants, autoloader, lifecycle hooks, requirements gate, and handoff to `WCRI\Plugin`. |
| `WCRI\Plugin` | Composition root. Registers bootstrap-level hooks and wires approved services. |
| `WCRI\Support\Activator` | Activation and deactivation lifecycle tasks. |
| `WCRI\Support\Requirements` | PHP, WordPress, and WooCommerce compatibility checks. |
| `WCRI\Support\FileSystem` | Plugin-owned directories and safe file paths. |
| `WCRI\Support\Sanitizer` | Shared sanitization helpers. |
| `WCRI\Support\ResponseFactory` | Consistent AJAX response payloads. |

### Admin

| Class | Responsibility |
| --- | --- |
| `AdminMenu` | Register WooCommerce -> Import Reviews. |
| `ImportPage` | Prepare view data and render the admin import page. |
| `AssetManager` | Enqueue admin assets only on plugin screens. |
| `TemplateController` | Serve CSV template downloads with capability and nonce checks. |

### Import And CSV

| Class | Responsibility |
| --- | --- |
| `CsvUploadHandler` | Receive and store uploaded CSV files. |
| `CsvValidator` | Validate extension, MIME type, readability, encoding expectations, and headers. |
| `CsvHeader` | Normalize and map headers to canonical columns. |
| `CsvParser` | Stream normalized CSV rows. |
| `CsvRow` | Represent one row with row number, normalized values, and raw context. |
| `ImportController` | Coordinate import lifecycle actions. |
| `ImportJob` | Represent job state and counters. |
| `ImportJobRepository` | Persist and retrieve job state. |
| `BatchProcessor` | Process a bounded number of rows per request. |
| `ImportResult` | Represent structured import outcomes. |

### Product, Review, Logging, Settings, Security

| Class | Responsibility |
| --- | --- |
| `ProductMatcherInterface` | Define product lookup strategy contract. |
| `SkuProductMatcher` | Resolve WooCommerce products by SKU. |
| `ProductMatchResult` | Represent found, not found, invalid, and ambiguous matches. |
| `ReviewValidator` | Validate review data before creation. |
| `ReviewCreator` | Create WordPress comments and WooCommerce review metadata. |
| `DuplicateDetector` | Detect duplicates by product, email, and content when enabled. |
| `RatingUpdater` | Recalculate rating data and clear caches. |
| `LoggerInterface` | Define import logging contract. |
| `ImportLogger` | Store per-job log entries. |
| `LogEntry` | Represent one sanitized log entry. |
| `LogExporter` | Produce protected downloadable logs. |
| `SettingsRepository` | Store defaults, read options, and sanitize settings. |
| `SettingsPage` | Render settings if separate from import page. |
| `CapabilityChecker` | Centralize capability checks. |
| `NonceVerifier` | Centralize nonce verification. |

## Dependency Flow

Dependencies should flow from controllers toward focused services. Business services must not depend on admin UI classes.

```text
WordPress hooks
  -> WCRI\Plugin
    -> Admin controllers
      -> Ajax controllers
        -> ImportController
          -> ImportJobRepository
          -> CsvParser
          -> ProductMatcherInterface
          -> ReviewValidator
          -> DuplicateDetector
          -> ReviewCreator
          -> RatingUpdater
          -> LoggerInterface
```

Rules:

- Admin classes may call controllers but must not perform import work directly.
- AJAX controllers validate requests and delegate to import services.
- Import services coordinate domain services but do not render UI.
- CSV services parse data but do not create reviews.
- Review services create reviews but do not read uploaded files.
- Logging services record outcomes but do not decide import control flow.

## Plugin Bootstrap Flow

```text
WordPress loads wc-review-importer.php
-> Constants are defined
-> Autoloader is registered
-> Activation/deactivation hooks are registered
-> plugins_loaded runs
-> Requirements are checked
-> WCRI\Plugin is created
-> WCRI\Plugin registers service hooks
```

Bootstrap boundaries:

- No admin page rendering.
- No CSV parsing.
- No AJAX endpoint logic.
- No review creation.
- No settings UI.
- No logging implementation.

## Import Workflow

Target import workflow:

```text
Administrator uploads CSV
-> Upload is validated
-> Import job is created
-> Browser begins AJAX batch loop
-> BatchProcessor reads rows through CsvParser
-> Product matcher resolves product
-> Review validator validates row
-> Duplicate detector checks row when enabled
-> Review creator inserts review through WordPress APIs
-> Rating updater records or refreshes affected product rating data
-> Logger records row outcome
-> Job repository saves progress
-> Admin UI displays progress and summary
```

Import invariants:

- One bad row must not terminate the whole import.
- Product matching must be replaceable.
- Review creation must use WordPress comment APIs.
- Rating refresh must use WooCommerce-compatible APIs and cache handling.
- Import state must persist after every batch.

## AJAX Workflow

Required AJAX actions:

- Start import.
- Process next batch.
- Pause import.
- Resume import.
- Cancel import.
- Download logs.
- Download CSV template.

AJAX request flow:

```text
Browser request
-> Nonce verification
-> Capability check
-> Input sanitization
-> Controller loads job or upload context
-> Controller delegates to service
-> Service returns structured result
-> Controller returns compact JSON response
```

AJAX response fields:

- Job ID.
- Status.
- Total rows when known.
- Processed rows.
- Imported count.
- Skipped count.
- Warning count.
- Error count.
- Progress percentage.
- Recent log entries.
- Human-readable message.
- Estimated remaining time when enough data exists.

## CSV Parser Design

CSV requirements:

- UTF-8 encoded input.
- Required header: `sku`.
- Supported headers: `sku`, `name`, `email`, `rating`, `title`, `review`, `date`.
- Unknown headers are ignored.
- Multiline reviews are supported.
- Large files are streamed.

Parser boundaries:

- Does not match products.
- Does not create reviews.
- Does not check duplicates.
- Does not update ratings.
- Does not render admin output.

## Logging Architecture

Logs are scoped to an import job.

Log types:

- `info`
- `warning`
- `error`
- `skipped`
- `summary`

Log entry fields:

- Job ID.
- Timestamp.
- Type.
- Message.
- Row number when applicable.
- Product reference when applicable.
- Structured context when useful.

Logging rules:

- Sanitize data before storage where appropriate.
- Escape data on output.
- Avoid storing secrets or unnecessary personal data.
- Keep log storage bounded for large imports.
- Protect log display and download with capability and nonce checks.
- Prefer protected plugin-owned files for large logs over large autoloaded options.

## Error Handling Strategy

Row-level errors are recoverable and should be logged while the batch continues.

Examples:

- SKU not found.
- Invalid rating.
- Invalid email.
- Empty review when required.
- Duplicate review.
- Malformed CSV row.
- Invalid date.

Job-level errors are unrecoverable and may fail the import.

Examples:

- Missing or unreadable source file.
- Missing or corrupted job state.
- Permission failure.
- Nonce failure.
- Upload directory cannot be written.
- Parser cannot open the file.

Exception rules:

- Expected validation failures become structured results.
- Unexpected failures are caught at controller or batch boundaries.
- Stack traces and raw file paths are not exposed to administrators.
- Logs retain enough context for debugging.

## Security Architecture

Every admin and AJAX action must enforce:

- Capability checks.
- Nonce verification.
- Input sanitization.
- Output escaping.
- Upload validation.
- MIME validation.
- CSV injection protection for displayed or exported values.

Primary capability: `manage_woocommerce`.

The plugin must not trust request values, uploaded file names, CSV cell values, settings, log values, or external data.

## Performance Architecture

Large import support requires:

- Streaming CSV reads.
- Configurable batch processing.
- Default batch size of 100 rows.
- Progress persistence after each batch.
- Compact AJAX responses.
- Efficient duplicate detection.
- Controlled rating recalculation and cache invalidation.

Target: 100,000+ reviews without timeout or memory exhaustion.

## Future Extension Points

Product matching:

- Product ID.
- GTIN.
- UPC.
- EAN.
- ASIN.

Import sources:

- Amazon.
- AliExpress.
- Temu.
- REST API.
- WP-CLI.
- Cron or scheduled imports.

Review data:

- Review images.
- Pros and cons.
- Review replies.
- Review title support.
- Verified purchase source metadata.

Operations:

- CSV export.
- Import reports.
- Action Scheduler integration.
- AI-generated review drafts.

Extension rule: future sources should normalize their data into the same importer pipeline instead of duplicating review creation, product matching, logging, or rating update logic.
