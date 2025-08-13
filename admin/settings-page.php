<?php
/**
 * Settings page template for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = get_option('slots_settings', array());
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('slots_options');
        do_settings_sections('slots-settings');
        ?>
        
        <?php submit_button(); ?>
    </form>
    

</div>
