<?php
/**
 * Configuration file for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Cache Configuration
define('SLOTS_CACHE_ENABLED', false);
define('SLOTS_CACHE_EXPIRATION', 3600); // 1 hour in seconds

// Plugin Configuration
define('SLOTS_DEFAULT_LIMIT', 12);
define('SLOTS_DEFAULT_SORT', 'recent');

// Feature Flags
define('SLOTS_ENABLE_GRID_FILTERS', true);
define('SLOTS_ENABLE_PAGINATION', true);
