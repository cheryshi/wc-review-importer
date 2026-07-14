# WooCommerce Review Importer

WooCommerce Review Importer is a documentation-driven WordPress plugin project for importing WooCommerce product reviews from CSV files.

The repository is intentionally guided by product, architecture, milestone, and AI engineering documents. Contributors must follow the documented workflow before implementing plugin functionality.

## Current Project State

- Product requirements are documented.
- Architecture and namespace boundaries are documented.
- AI engineering workflow is documented.
- Milestone 1 bootstrap has been implemented and reviewed.
- Plugin import features are not complete yet.

## Documentation Index

Read these documents before working in the repository:

- `docs/PRD.md` - product requirements and acceptance criteria.
- `docs/ARCHITECTURE.md` - directory structure, namespaces, class responsibilities, and workflows.
- `docs/CODING_GUIDELINES.md` - naming, coding, security, and formatting expectations.
- `docs/TODO.md` - milestone plan and approval gates.
- `docs/AI_PLAYBOOK.md` - AI engineering process for this repository.
- `.github/copilot-instructions.md` - AI engineering handbook and repository rules.
- `docs/CHANGELOG.md` - notable documentation and implementation changes.

## Engineering Workflow

1. Read the required documentation.
2. Identify the active milestone.
3. Explain planned file changes before editing.
4. Explain architecture decisions before editing.
5. Implement or document only the approved scope.
6. Self-review changes.
7. Run available verification checks.
8. Update documentation if the approved change affects workflow, architecture, or public behavior.
9. Commit one logical change.
10. Stop and wait for approval before the next milestone.

## AI Contributor Rules

AI contributors must act as either:

- Senior Software Engineer: plans and implements approved scope.
- Senior Software Reviewer: reviews for correctness, security, performance, maintainability, and architecture fit.

AI contributors must not:

- Rewrite the whole project.
- Delete files without approval.
- Rename directories without approval.
- Modify unrelated files.
- Continue to the next milestone automatically.
- Add plugin functionality during documentation-only tasks.
- Bypass WordPress or WooCommerce APIs where suitable APIs exist.

## Planned Features

The finished plugin is intended to support:

- CSV review import.
- AJAX batch processing.
- Duplicate detection.
- WooCommerce rating updates.
- Verified owner metadata.
- Import progress and statistics.
- Import logs.
- Large imports of 100,000+ reviews.

These features must be implemented through the milestone plan in `docs/TODO.md`.

## Target Environment

- WordPress 6.8+
- WooCommerce 10+
- PHP 8.1+
- MySQL 8+

## Development Safety

This plugin must not:

- Modify WordPress core.
- Modify WooCommerce core.
- Modify theme files.
- Require editing `functions.php`.
- Create custom database tables for v1.0.
- Import all rows in one request.

## Status

This project is not production-ready yet. Continue only through approved milestones.
