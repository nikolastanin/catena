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

<style>


.form-table th {
    width: 200px;
}

.slots-editor-help {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-top: 15px;
}

.slots-editor-help h4 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #23282d;
}

.slots-editor-help ul {
    margin: 0;
    padding-left: 20px;
}

.slots-editor-help li {
    margin-bottom: 5px;
}

.slots-editor-help code {
    background: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
    border: 1px solid #ddd;
}
</style>
