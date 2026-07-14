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

        $this->registerServices();
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

    /**
     * Registers approved plugin service groups.
     *
     * @return void
     */
    private function registerServices(): void
    {
        $this->registerAdminServices();
        $this->registerAjaxServices();
        $this->registerImportServices();
        $this->registerSupportServices();
    }

    /**
     * Registers admin services when their milestone is approved.
     *
     * @return void
     */
    private function registerAdminServices(): void
    {
    }

    /**
     * Registers AJAX services when their milestone is approved.
     *
     * @return void
     */
    private function registerAjaxServices(): void
    {
    }

    /**
     * Registers import services when their milestone is approved.
     *
     * @return void
     */
    private function registerImportServices(): void
    {
    }

    /**
     * Registers shared support services when their milestone is approved.
     *
     * @return void
     */
    private function registerSupportServices(): void
    {
    }
}
