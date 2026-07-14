<?php
/**
 * Import logging contract.
 *
 * @package WCRI\Logger
 */

declare(strict_types=1);

namespace WCRI\Logger;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Defines import logging behavior.
 */
interface LoggerInterface
{
    /**
     * Records one import log entry.
     *
     * @param LogEntry $entry Log entry to record.
     * @return void
     */
    public function log(LogEntry $entry): void;
}
