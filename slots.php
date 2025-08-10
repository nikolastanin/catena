<?php
/**
 * Plugin Name: Slots
 * Plugin URI: https://example.com/slots
 * Description: A comprehensive slots management plugin for WordPress
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: slots
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SLOTS_PLUGIN_FILE', __FILE__);
define('SLOTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLOTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SLOTS_PLUGIN_VERSION', '1.0.0');
define('SLOTS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots.php';
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-admin.php';
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-public.php';
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-post-types.php';
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-shortcodes.php';
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-themes.php';
require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-template-manager.php';

// Initialize the plugin
function slots_init() {
    // Initialize main plugin class
    $slots = new Slots();
    
    // Initialize shortcodes
    new Slots_Shortcodes();
}
add_action('plugins_loaded', 'slots_init');

// Activation hook
register_activation_hook(__FILE__, 'slots_activate');
function slots_activate() {
    // Create custom post type
    $post_types = new Slots_Post_Types();
    $post_types->create_slot_post_type();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'slots_deactivate');
function slots_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
