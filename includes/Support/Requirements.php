<?php
/**
 * Runtime requirements checks.
 *
 * @package WCRI\Support
 */

declare(strict_types=1);

namespace WCRI\Support;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Checks whether the current environment can run the plugin.
 */
final class Requirements
{
    /**
     * Requirement error messages.
     *
     * @var string[]
     */
    private array $errors = array();

    /**
     * Determines whether all plugin requirements are met.
     *
     * @return bool
     */
    public function areMet(): bool
    {
        $this->errors = array();

        $this->checkPhpVersion();
        $this->checkWordPressVersion();
        $this->checkWooCommerce();

        return empty($this->errors);
    }

    /**
     * Returns requirement error messages.
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Checks the current PHP version.
     *
     * @return void
     */
    private function checkPhpVersion(): void
    {
        if (version_compare(PHP_VERSION, WCRI_MINIMUM_PHP_VERSION, '>=')) {
            return;
        }

        $this->errors[] = sprintf(
            /* translators: 1: required PHP version, 2: current PHP version. */
            __('WooCommerce Review Importer requires PHP %1$s or higher. Current version: %2$s.', 'wc-review-importer'),
            WCRI_MINIMUM_PHP_VERSION,
            PHP_VERSION
        );
    }

    /**
     * Checks the current WordPress version.
     *
     * @return void
     */
    private function checkWordPressVersion(): void
    {
        global $wp_version;

        if (isset($wp_version) && version_compare((string) $wp_version, WCRI_MINIMUM_WP_VERSION, '>=')) {
            return;
        }

        $this->errors[] = sprintf(
            /* translators: 1: required WordPress version. */
            __('WooCommerce Review Importer requires WordPress %1$s or higher.', 'wc-review-importer'),
            WCRI_MINIMUM_WP_VERSION
        );
    }

    /**
     * Checks whether WooCommerce is active and compatible.
     *
     * @return void
     */
    private function checkWooCommerce(): void
    {
        if (! class_exists('WooCommerce')) {
            $this->errors[] = __('WooCommerce Review Importer requires WooCommerce to be installed and active.', 'wc-review-importer');
            return;
        }

        if (! defined('WC_VERSION')) {
            return;
        }

        if (version_compare((string) WC_VERSION, WCRI_MINIMUM_WC_VERSION, '>=')) {
            return;
        }

        $this->errors[] = sprintf(
            /* translators: 1: required WooCommerce version, 2: current WooCommerce version. */
            __('WooCommerce Review Importer requires WooCommerce %1$s or higher. Current version: %2$s.', 'wc-review-importer'),
            WCRI_MINIMUM_WC_VERSION,
            (string) WC_VERSION
        );
    }
}
