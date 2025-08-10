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
    
    <div class="slots-settings-info">
        <h2><?php _e('Plugin Information', 'slots'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Version', 'slots'); ?></th>
                <td><?php echo esc_html(SLOTS_PLUGIN_VERSION); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Plugin Directory', 'slots'); ?></th>
                <td><code><?php echo esc_html(SLOTS_PLUGIN_DIR); ?></code></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Plugin URL', 'slots'); ?></th>
                <td><code><?php echo esc_html(SLOTS_PLUGIN_URL); ?></code></td>
            </tr>
        </table>
    </div>
    
    <div class="slots-settings-help">
        <h2><?php _e('Help & Documentation', 'slots'); ?></h2>
        <div class="help-content">
            <h3><?php _e('Shortcode Usage', 'slots'); ?></h3>
            <p><?php _e('Use the following shortcodes to display slots on any page or post:', 'slots'); ?></p>
            
            <h4><?php _e('Grid Display', 'slots'); ?></h4>
            <code>[slots_grid limit="12" sort="recent" show_filters="true" show_pagination="true"]</code>
            
            <h4><?php _e('Individual Slot', 'slots'); ?></h4>
            <code>[slot_detail id="123" template="editor" show_rating="true" show_description="true"]</code>
            <p class="description"><?php _e('Available templates: default, minimal, compact, featured, editor', 'slots'); ?></p>
            
            <h3><?php _e('Available Parameters', 'slots'); ?></h3>
            <ul>
                <li><strong>limit</strong> - Maximum number of slots to display (1, 3, 6, 9, or 12)</li>
                <li><strong>sort</strong> - Sorting (recent, random)</li>
                <li><strong>show_filters</strong> - Show/hide filter controls</li>
                <li><strong>show_pagination</strong> - Show/hide pagination</li>
            </ul>
            
            <h3><?php _e('Custom Fields', 'slots'); ?></h3>
            <p><?php _e('The plugin adds custom fields to slot posts for:', 'slots'); ?></p>
            <ul>
                <li><strong>Slot ID</strong> - Unique identifier for the slot game</li>
                <strong>Star Rating</strong> - 1-5 star rating system</li>
                <li><strong>Provider Name</strong> - Game provider/developer</li>
                <li><strong>RTP</strong> - Return to Player percentage</li>
                <li><strong>Wager Limits</strong> - Minimum and maximum bet amounts</li>
            </ul>
        </div>
    </div>
</div>

<style>
.slots-settings-info,
.slots-settings-help {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-top: 30px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.help-content h3 {
    margin-top: 20px;
    margin-bottom: 10px;
    color: #23282d;
}

.help-content h3:first-child {
    margin-top: 0;
}

.help-content h4 {
    margin-top: 15px;
    margin-bottom: 8px;
    color: #23282d;
}

.help-content code {
    background: #f1f1f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

.help-content ul {
    margin-left: 20px;
}

.help-content li {
    margin-bottom: 5px;
}

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
