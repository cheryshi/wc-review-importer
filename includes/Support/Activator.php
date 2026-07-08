<?php
/**
 * Plugin activation lifecycle handling.
 *
 * @package WCRI\Support
 */

declare(strict_types=1);

namespace WCRI\Support;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Handles activation and deactivation tasks.
 */
final class Activator
{
    /**
     * Runs plugin activation tasks.
     *
     * @return void
     */
    public static function activate(): void
    {
        $requirements = new Requirements();

        if (! $requirements->areMet()) {
            self::deactivatePlugin();

            wp_die(
                esc_html(implode(' ', $requirements->getErrors())),
                esc_html__('Plugin Activation Error', 'wc-review-importer'),
                array('back_link' => true)
            );
        }

        if (false === get_option(WCRI_OPTION_SETTINGS, false)) {
            add_option(WCRI_OPTION_SETTINGS, self::getDefaultSettings());
        }

        update_option(WCRI_OPTION_VERSION, WCRI_VERSION);
    }

    /**
     * Runs plugin deactivation tasks.
     *
     * @return void
     */
    public static function deactivate(): void
    {
        // Reserved for future non-destructive shutdown tasks.
    }

    /**
     * Deactivates the plugin when activation requirements fail.
     *
     * @return void
     */
    private static function deactivatePlugin(): void
    {
        if (! function_exists('deactivate_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        deactivate_plugins(WCRI_PLUGIN_BASENAME);
    }

    /**
     * Returns default plugin settings.
     *
     * @return array<string, mixed>
     */
    private static function getDefaultSettings(): array
    {
        return array(
            'default_review_status' => 'approved',
            'default_verified_owner' => false,
            'default_batch_size' => 100,
            'duplicate_detection' => true,
            'logging_enabled' => true,
            'maximum_execution_time' => 20,
        );
    }
}
