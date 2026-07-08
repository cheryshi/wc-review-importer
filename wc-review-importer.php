<?php
/**
 * Plugin Name: WooCommerce Review Importer
 * Plugin URI: https://github.com/cheryshi/wc-review-importer
 * Description: Import WooCommerce product reviews from CSV files using a modular batch importer.
 * Version: 1.0.0
 * Author: Product Owner
 * Text Domain: wc-review-importer
 * Domain Path: /languages
 * Requires at least: 6.8
 * Requires PHP: 8.1
 * WC requires at least: 10.0
 *
 * @package WCRI
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('WCRI_VERSION', '1.0.0');
define('WCRI_MINIMUM_PHP_VERSION', '8.1');
define('WCRI_MINIMUM_WP_VERSION', '6.8');
define('WCRI_MINIMUM_WC_VERSION', '10.0');
define('WCRI_PLUGIN_FILE', __FILE__);
define('WCRI_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WCRI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCRI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCRI_TEXT_DOMAIN', 'wc-review-importer');
define('WCRI_OPTION_SETTINGS', 'wcri_settings');
define('WCRI_OPTION_VERSION', 'wcri_version');

/**
 * Registers the plugin class autoloader.
 *
 * @return void
 */
function wcri_register_autoloader(): void
{
    spl_autoload_register(
        static function (string $class_name): void {
            $prefix = 'WCRI\\';

            if (! str_starts_with($class_name, $prefix)) {
                return;
            }

            $relative_class = substr($class_name, strlen($prefix));
            $relative_path  = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class);
            $file_path      = WCRI_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . $relative_path . '.php';

            if (! is_readable($file_path)) {
                return;
            }

            require_once $file_path;
        }
    );
}

wcri_register_autoloader();

register_activation_hook(
    __FILE__,
    static function (): void {
        WCRI\Support\Activator::activate();
    }
);

register_deactivation_hook(
    __FILE__,
    static function (): void {
        WCRI\Support\Activator::deactivate();
    }
);

add_action(
    'plugins_loaded',
    static function (): void {
        $requirements = new WCRI\Support\Requirements();

        if (! $requirements->areMet()) {
            add_action(
                'admin_notices',
                static function () use ($requirements): void {
                    foreach ($requirements->getErrors() as $message) {
                        printf(
                            '<div class="notice notice-error"><p>%s</p></div>',
                            esc_html($message)
                        );
                    }
                }
            );

            return;
        }

        $plugin = new WCRI\Plugin();
        $plugin->init();
    }
);
