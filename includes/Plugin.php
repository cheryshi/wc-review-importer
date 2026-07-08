<?php
/**
 * Main plugin composition root.
 *
 * @package WCRI
 */

declare(strict_types=1);

namespace WCRI;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Wires the plugin services into WordPress.
 */
final class Plugin
{
    /**
     * Initializes bootstrap-level plugin behavior.
     *
     * @return void
     */
    public function init(): void
    {
        add_action('init', array($this, 'loadTextDomain'));
    }

    /**
     * Loads plugin translations.
     *
     * @return void
     */
    public function loadTextDomain(): void
    {
        load_plugin_textdomain(
            WCRI_TEXT_DOMAIN,
            false,
            dirname(WCRI_PLUGIN_BASENAME) . '/languages'
        );
    }
}
