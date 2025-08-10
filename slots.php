<?php
/**
 * Plugin Name: Slots
 * Plugin URI: https://example.com/slots
 * Description: A WordPress plugin for managing slots functionality
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: slots
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SLOTS_PLUGIN_VERSION', '1.0.0');
define('SLOTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLOTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SLOTS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include the main plugin class
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots.php';

// Initialize the plugin
function slots_init() {
    $plugin = Slots::get_instance();
    $plugin->init();
}
add_action('plugins_loaded', 'slots_init');

// Activation hook
register_activation_hook(__FILE__, 'slots_activate');
function slots_activate() {
    // Activation tasks
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'slots_deactivate');
function slots_deactivate() {
    // Cleanup tasks
    flush_rewrite_rules();
}
