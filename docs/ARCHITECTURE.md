# Architecture

WooCommerce Review Importer is a modular WordPress plugin for importing WooCommerce product reviews from CSV files. The architecture separates plugin bootstrapping, admin presentation, upload handling, CSV parsing, import orchestration, product matching, review creation, rating updates, logging, settings, and security.

The importer must support large CSV files without loading the full file into memory and without relying on a single long-running HTTP request.

## Design Goals

- Keep importer logic independent from the admin UI.
- Use WordPress and WooCommerce APIs whenever possible.
- Support large CSV imports through AJAX batch processing.
- Keep classes small and focused on one responsibility.
- Use namespaces and dependency injection where practical.
- Make future import sources possible without rewriting review creation logic.
- Never modify WordPress core, WooCommerce core, themes, or database schema.
- Prefer explicit service boundaries over static utility-heavy code.

## 1. Plugin Bootstrap Architecture

The main plugin file is the entrypoint WordPress loads. It should remain thin and should not contain business logic.

Bootstrap responsibilities:

- Define WordPress plugin headers.
- Define plugin constants for version, paths, URLs, basename, text domain, and minimum runtime requirements.
- Load the plugin autoloader.
- Register activation and deactivation hooks.
- Check runtime requirements before initializing feature services.
- Instantiate the central `WCRI\Plugin` class.
- Start plugin service registration on the appropriate WordPress hook.

Bootstrap sequence:

```text
WordPress loads wc-review-importer.php
-> Plugin constants are defined
-> Autoloader is registered
-> Activation/deactivation hooks are registered
-> plugins_loaded runs
-> Requirements are checked
-> WCRI\Plugin is instantiated
-> WCRI\Plugin registers plugin services and hooks
```

Activation responsibilities:

- Verify PHP version requirement.
- Verify WordPress version requirement where possible.
- Set default plugin options.
- Prepare plugin-owned upload/log directories if needed.
- Avoid creating custom database tables.
- Avoid importing data or running long tasks during activation.

Deactivation responsibilities:

- Stop transient runtime state where appropriate.
- Leave settings, logs, uploaded import files, and imported reviews intact unless future uninstall settings explicitly say otherwise.
- Avoid destructive cleanup during ordinary deactivation.

Uninstall behavior should be conservative and explicit. A future uninstall routine may remove plugin-owned options, temporary files, and logs, but must never remove WooCommerce reviews unless a future feature provides a clear administrator-controlled deletion workflow.

## 2. Namespace Structure

Namespace root: `WCRI`.

Suggested layout:

```text
wc-review-importer.php
includes/
  Plugin.php                         WCRI\Plugin
  Admin/
    AdminMenu.php                    WCRI\Admin\AdminMenu
    ImportPage.php                   WCRI\Admin\ImportPage
    AssetManager.php                 WCRI\Admin\AssetManager
    TemplateController.php           WCRI\Admin\TemplateController
  Ajax/
    ImportAjaxController.php         WCRI\Ajax\ImportAjaxController
  CSV/
    CsvUploadHandler.php             WCRI\CSV\CsvUploadHandler
    CsvValidator.php                 WCRI\CSV\CsvValidator
    CsvParser.php                    WCRI\CSV\CsvParser
    CsvRow.php                       WCRI\CSV\CsvRow
    CsvHeader.php                    WCRI\CSV\CsvHeader
  Importer/
    ImportController.php             WCRI\Importer\ImportController
    ImportJob.php                    WCRI\Importer\ImportJob
    ImportJobRepository.php          WCRI\Importer\ImportJobRepository
    ImportResult.php                 WCRI\Importer\ImportResult
    BatchProcessor.php               WCRI\Importer\BatchProcessor
  Product/
    ProductMatcherInterface.php      WCRI\Product\ProductMatcherInterface
    SkuProductMatcher.php            WCRI\Product\SkuProductMatcher
    ProductMatchResult.php           WCRI\Product\ProductMatchResult
  Review/
    ReviewValidator.php              WCRI\Review\ReviewValidator
    ReviewCreator.php                WCRI\Review\ReviewCreator
    DuplicateDetector.php            WCRI\Review\DuplicateDetector
    RatingUpdater.php                WCRI\Review\RatingUpdater
  Logger/
    LoggerInterface.php              WCRI\Logger\LoggerInterface
    ImportLogger.php                 WCRI\Logger\ImportLogger
    LogEntry.php                     WCRI\Logger\LogEntry
    LogExporter.php                  WCRI\Logger\LogExporter
  Settings/
    SettingsRepository.php           WCRI\Settings\SettingsRepository
    SettingsPage.php                 WCRI\Settings\SettingsPage
  Security/
    CapabilityChecker.php            WCRI\Security\CapabilityChecker
    NonceVerifier.php                WCRI\Security\NonceVerifier
  Support/
    Requirements.php                 WCRI\Support\Requirements
    FileSystem.php                   WCRI\Support\FileSystem
    Sanitizer.php                    WCRI\Support\Sanitizer
    ResponseFactory.php              WCRI\Support\ResponseFactory
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

Implementation may adjust exact file names if a simpler structure becomes clearer, but namespaces should remain stable and responsibility-driven.

## 3. Class Responsibility Map

### Core

| Class | Responsibility |
| --- | --- |
| `WCRI\Plugin` | Composition root. Instantiates services and registers WordPress hooks. |
| `WCRI\Support\Requirements` | Checks PHP, WordPress, and WooCommerce compatibility. |
| `WCRI\Support\FileSystem` | Handles plugin-owned directories and file paths. |
| `WCRI\Support\Sanitizer` | Provides reusable sanitization helpers where WordPress functions need consistent wrapping. |
| `WCRI\Support\ResponseFactory` | Creates consistent AJAX response arrays. |

### Admin

| Class | Responsibility |
| --- | --- |
| `WCRI\Admin\AdminMenu` | Registers WooCommerce -> Import Reviews. |
| `WCRI\Admin\ImportPage` | Prepares data and renders the import admin screen. |
| `WCRI\Admin\AssetManager` | Enqueues admin CSS and JavaScript only on plugin screens. |
| `WCRI\Admin\TemplateController` | Handles CSV template download requests. |

### Import

| Class | Responsibility |
| --- | --- |
| `WCRI\Importer\ImportController` | Coordinates start, pause, resume, cancel, and completion behavior. |
| `WCRI\Importer\ImportJob` | Represents import state and counters. |
| `WCRI\Importer\ImportJobRepository` | Persists and retrieves import job state. |
| `WCRI\Importer\BatchProcessor` | Processes a limited number of rows in one request. |
| `WCRI\Importer\ImportResult` | Carries structured import outcomes. |

### CSV

| Class | Responsibility |
| --- | --- |
| `WCRI\CSV\CsvUploadHandler` | Receives, validates, and stores uploaded CSV files. |
| `WCRI\CSV\CsvValidator` | Validates MIME type, extension, readability, and required headers. |
| `WCRI\CSV\CsvParser` | Streams normalized rows from the CSV file. |
| `WCRI\CSV\CsvHeader` | Maps source headers to canonical fields. |
| `WCRI\CSV\CsvRow` | Represents one normalized CSV row plus row number and raw values. |

### Product and Review

| Class | Responsibility |
| --- | --- |
| `WCRI\Product\ProductMatcherInterface` | Contract for product lookup strategies. |
| `WCRI\Product\SkuProductMatcher` | Resolves products by SKU. |
| `WCRI\Product\ProductMatchResult` | Represents found, not found, invalid, and ambiguous product match outcomes. |
| `WCRI\Review\ReviewValidator` | Validates review row data before creation. |
| `WCRI\Review\ReviewCreator` | Creates reviews through WordPress comment APIs and WooCommerce metadata. |
| `WCRI\Review\DuplicateDetector` | Detects optional duplicates by product, email, and content. |
| `WCRI\Review\RatingUpdater` | Refreshes WooCommerce rating data and caches. |

### Logging, Settings, Security

| Class | Responsibility |
| --- | --- |
| `WCRI\Logger\LoggerInterface` | Contract for import logging. |
| `WCRI\Logger\ImportLogger` | Stores per-import log entries. |
| `WCRI\Logger\LogEntry` | Represents one sanitized log entry. |
| `WCRI\Logger\LogExporter` | Produces downloadable logs. |
| `WCRI\Settings\SettingsRepository` | Reads, writes, sanitizes, and returns defaults for plugin settings. |
| `WCRI\Settings\SettingsPage` | Registers and renders settings if separated from the import page. |
| `WCRI\Security\CapabilityChecker` | Centralizes admin capability checks. |
| `WCRI\Security\NonceVerifier` | Centralizes nonce verification. |

## 4. Admin Module Design

The admin module provides the user interface but does not perform import business logic directly.

Admin page responsibilities:

- Register a WooCommerce submenu: WooCommerce -> Import Reviews.
- Render upload controls for CSV files.
- Render a CSV template download action.
- Render import options such as review status, verified owner, batch size, duplicate detection, and logging.
- Render progress, statistics, and recent logs.
- Render pause, resume, and cancel controls.
- Include nonce fields and capability-aware actions.

Admin JavaScript responsibilities:

- Submit upload/start requests through AJAX.
- Repeatedly call the batch endpoint while an import is running.
- Update progress percentage, status text, statistics, and recent logs.
- Handle pause, resume, cancel, and AJAX failure states.
- Prevent duplicate start requests while a job is active.

Admin boundaries:

- Admin classes may format data for display.
- Admin classes may call controllers or services.
- Admin classes must not parse CSV rows.
- Admin classes must not create reviews directly.
- Admin classes must not recalculate ratings directly.

Security requirements for admin screens:

- Use `manage_woocommerce` as the primary capability.
- Verify nonces for all state-changing actions.
- Sanitize all incoming request values.
- Escape all output.
- Load assets only on plugin-owned admin pages.

## 5. Import Service Flow

The import service is the core orchestration layer. It coordinates parser, matcher, validator, duplicate detector, review creator, rating updater, logger, and job repository.

High-level service flow:

```text
ImportController starts job
-> CsvUploadHandler stores uploaded file
-> CsvValidator checks file and headers
-> ImportJobRepository creates job state
-> BatchProcessor receives job ID
-> CsvParser streams next rows
-> ProductMatcher resolves product
-> ReviewValidator validates row
-> DuplicateDetector checks duplicates when enabled
-> ReviewCreator creates review through WordPress APIs
-> RatingUpdater records product for refresh or refreshes at safe points
-> ImportLogger records outcome
-> ImportJobRepository persists progress
-> ImportResult is returned to AJAX controller
```

Batch processing rules:

- Process no more than the configured batch size per request.
- Default batch size is 100 rows.
- Persist progress after every batch.
- Continue after row-level failures.
- Mark the job completed only after all rows are read and final rating/cache updates are done.
- Mark the job failed only for unrecoverable infrastructure or state problems.

Review creation rules:

- Use WordPress comment APIs.
- Store WooCommerce rating metadata.
- Store verified-owner metadata according to settings.
- Respect configured approval status.
- Avoid direct SQL unless a WordPress or WooCommerce API cannot satisfy the requirement.

Rating update rules:

- Avoid recalculating the same product excessively in tight loops.
- Track affected product IDs during the import when useful.
- Recalculate ratings and clear caches at controlled points, especially at job completion or after a batch if needed for visibility.

## 6. CSV Parser Design

The CSV parser must be streaming and memory-safe.

CSV input requirements:

- UTF-8 encoded CSV.
- Header row must include `sku`.
- Supported canonical columns: `sku`, `name`, `email`, `rating`, `title`, `review`, `date`.
- Unknown columns are ignored.
- Optional columns may be missing where defaults exist.
- Multiline review content must be supported.

Parser responsibilities:

- Open the CSV file safely.
- Read the header row.
- Normalize header names.
- Map source columns to canonical fields.
- Iterate rows without loading the full file into memory.
- Preserve row numbers for logs.
- Return normalized row objects or arrays with consistent keys.
- Detect malformed rows and return row-level errors without stopping the whole import.

Parser boundaries:

- The parser does not match products.
- The parser does not create reviews.
- The parser does not decide duplicate status.
- The parser does not render admin output.

CSV validation concerns:

- File extension and MIME type.
- Readability.
- Required header presence.
- Empty file handling.
- Encoding problems where detectable.
- CSV injection protection for values that will be displayed or exported in logs.

## 7. AJAX Batch Import Flow

AJAX endpoints are the bridge between the admin UI and importer services.

Required endpoint groups:

- Start import.
- Process next batch.
- Pause import.
- Resume import.
- Cancel import.
- Download logs.
- Download CSV template.

Start flow:

```text
User selects CSV and options
-> Browser sends start_import AJAX request
-> Nonce and capability are verified
-> Upload and settings are sanitized
-> File is validated and stored
-> Import job is created with status pending
-> Response returns job ID and initial counters
```

Batch flow:

```text
Browser sends process_batch with job ID
-> Nonce and capability are verified
-> Job state is loaded
-> If paused/cancelled/completed, return current status
-> BatchProcessor processes up to batch size rows
-> Counters and logs are saved
-> Response returns progress and recent messages
-> Browser repeats until terminal status
```

Pause flow:

```text
User clicks Pause
-> Browser sends pause_import
-> Server verifies request
-> Job status becomes paused
-> Further batch requests do not process rows
```

Resume flow:

```text
User clicks Resume
-> Browser sends resume_import
-> Server verifies request
-> Job status becomes running
-> Browser resumes batch loop from saved position
```

Cancel flow:

```text
User clicks Cancel
-> Browser sends cancel_import
-> Server verifies request
-> Job status becomes cancelled
-> Safe cleanup may run
-> No further rows are processed
```

AJAX response shape should include:

- Job ID.
- Status.
- Total rows if known.
- Processed rows.
- Imported count.
- Skipped count.
- Warning count.
- Error count.
- Progress percentage.
- Recent log entries.
- Human-readable message.
- Estimated remaining time when enough data exists.

## 8. Logging System

Logging is per import session and must help administrators understand what happened without exposing unsafe raw input.

Log types:

- `info`: normal lifecycle messages.
- `warning`: recoverable data or validation concerns.
- `error`: failed row or infrastructure problem.
- `skipped`: intentionally skipped row, including duplicates.
- `summary`: final import summary.

Each log entry should include:

- Import job ID.
- Timestamp.
- Type.
- Message.
- Row number when applicable.
- Product reference such as SKU when applicable.
- Context array for structured details when useful.

Logging rules:

- Sanitize before storage where appropriate.
- Escape on output.
- Avoid storing secrets or unnecessary personal data.
- Keep log storage bounded for very large imports.
- Support downloadable logs for administrators.
- Keep log retrieval protected by capability and nonce checks.

Storage approach:

- Initial implementation may use plugin-owned files or WordPress options/transients if size remains controlled.
- For large imports, file-based logs in a protected plugin-owned upload directory are preferred over very large autoloaded options.
- The storage choice must not require custom database tables for v1.0.

## 9. Error Handling Strategy

The importer should distinguish row-level recoverable errors from job-level unrecoverable errors.

Row-level recoverable errors:

- SKU not found.
- Invalid rating.
- Invalid email.
- Empty review when review content is required.
- Duplicate review when duplicate detection is enabled.
- Malformed CSV row.
- Invalid date.

Row-level handling:

```text
Row problem occurs
-> Convert problem into structured ImportResult
-> Log warning, skipped, or error entry
-> Increment counters
-> Continue with next row
```

Job-level unrecoverable errors:

- Uploaded file is missing or unreadable.
- Import job state is missing or corrupted.
- Current user lacks permission.
- Nonce verification fails.
- Storage directory cannot be created or written.
- Parser cannot open the source file.
- Repeated infrastructure failure prevents safe continuation.

Job-level handling:

```text
Unrecoverable problem occurs
-> Log import-level error when possible
-> Mark job failed if safe
-> Return error response to admin UI
-> Stop processing further rows
```

Exception strategy:

- Catch exceptions at controller or batch boundaries.
- Convert expected validation failures into structured results, not uncaught exceptions.
- Do not expose stack traces or raw file paths to administrators.
- Preserve enough log context for debugging.

Security-related failures:

- Nonce failure returns an authorization error and does not modify state.
- Capability failure returns an authorization error and does not modify state.
- Invalid upload failure rejects the file before job creation.

## 10. Future Extension Points

The architecture must allow future features without rewriting the core CSV importer.

Product matching extensions:

- Product ID matcher.
- GTIN matcher.
- UPC matcher.
- EAN matcher.
- ASIN matcher.

Import source extensions:

- Amazon review import.
- AliExpress review import.
- Temu review import.
- REST API import.
- WP-CLI import.
- Scheduled import jobs.

Review data extensions:

- Review images.
- Pros and cons.
- Review replies.
- Review title storage if supported cleanly.
- Verified purchase source metadata.

Operational extensions:

- CSV export.
- More detailed reports.
- Background queue or Action Scheduler integration.
- Cron-based resume for interrupted imports.
- AI-generated review drafts.

Extension design rule:

Future import sources should transform their source data into normalized row objects compatible with the importer pipeline. They should reuse product matching, validation, duplicate detection, review creation, rating updates, and logging rather than duplicating those services.

## Security Model

Every admin and AJAX action must enforce:

- Current user capability checks.
- Nonce verification.
- Sanitization of input.
- Escaping of output.
- Upload validation.
- MIME validation.
- CSV injection protection when displaying or exporting CSV-derived values.

Suggested capability: `manage_woocommerce`, with fallback consideration for `manage_options` only if WooCommerce is unavailable during setup screens.

The plugin must not trust uploaded file names, CSV headers, CSV cell values, AJAX parameters, settings values, or log output.

## Performance Model

Large import support depends on these rules:

- Stream CSV rows instead of reading the full file into memory.
- Process a configurable batch size, default 100 rows per request.
- Persist job progress after each batch.
- Avoid expensive duplicate checks where possible.
- Avoid repeated full-product recalculation inside tight row loops when a deferred or per-product summary update is safer.
- Clear WooCommerce caches at controlled points.
- Keep AJAX responses compact.

The target is 100,000+ reviews without timeout or memory exhaustion.
