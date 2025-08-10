<?php
/**
 * Admin functionality for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('wp_insert_post', array($this, 'auto_generate_slot_id'), 10, 3);
        add_action('admin_init', array($this, 'maybe_assign_slot_ids_to_existing'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Slots-Settings', 'slots'),
            __('Slots-Settings', 'slots'),
            'manage_options',
            'slots-settings',
            array($this, 'settings_page'),
            'dashicons-admin-generic',
            31
        );
        

    }
    
    /**
     * Main admin page
     */
    public function admin_page() {
        include SLOTS_PLUGIN_DIR . 'admin/admin-page.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        include SLOTS_PLUGIN_DIR . 'admin/settings-page.php';
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting('slots_options', 'slots_settings');
        
        // General Settings Section
        add_settings_section(
            'slots_general',
            __('General Settings', 'slots'),
            array($this, 'settings_section_callback'),
            'slots-settings'
        );
        
        // Styling Settings Section
        add_settings_section(
            'slots_styling',
            __('Styling Options', 'slots'),
            array($this, 'styling_section_callback'),
            'slots-settings'
        );
        

        

        
        // Slot Editor Section
        add_settings_section(
            'slots_editor',
            __('Single Slot Page Markup', 'slots'),
            array($this, 'editor_section_callback'),
            'slots-settings'
        );
        
        // Slot Card Template Section
        add_settings_section(
            'slots_card_template',
            __('Slot Card Template', 'slots'),
            array($this, 'card_template_section_callback'),
            'slots-settings'
        );
        

        
        // General Settings Fields
        add_settings_field(
            'enable_data_sync',
            __('Enable Data Sync', 'slots'),
            array($this, 'checkbox_field_callback'),
            'slots-settings',
            'slots_general',
            array('label_for' => 'enable_data_sync')
        );
        
        add_settings_field(
            'api_url',
            __('API Route Example URL', 'slots'),
            array($this, 'text_field_callback'),
            'slots-settings',
            'slots_general',
            array('label_for' => 'api_url')
        );
        
        add_settings_field(
            'sync_data_button',
            __('Sync Data', 'slots'),
            array($this, 'button_field_callback'),
            'slots-settings',
            'slots_general',
            array('label_for' => 'sync_data_button')
        );
        
        // Styling Settings Fields
        add_settings_field(
            'primary_color',
            __('Primary Color', 'slots'),
            array($this, 'color_field_callback'),
            'slots-settings',
            'slots_styling',
            array('label_for' => 'primary_color')
        );
        
        add_settings_field(
            'secondary_color',
            __('Secondary Color', 'slots'),
            array($this, 'color_field_callback'),
            'slots-settings',
            'slots_styling',
            array('label_for' => 'secondary_color')
        );
        
        add_settings_field(
            'accent_color',
            __('Accent Color', 'slots'),
            array($this, 'color_field_callback'),
            'slots-settings',
            'slots_styling',
            array('label_for' => 'accent_color')
        );
        
        add_settings_field(
            'border_radius',
            __('Border Radius', 'slots'),
            array($this, 'number_field_callback'),
            'slots-settings',
            'slots_styling',
            array('label_for' => 'border_radius')
        );
        
        add_settings_field(
            'font_family',
            __('Font Family', 'slots'),
            array($this, 'select_field_callback'),
            'slots-settings',
            'slots_styling',
            array('label_for' => 'font_family')
        );
        
        add_settings_field(
            'custom_theme_css',
            __('Custom Theme CSS', 'slots'),
            array($this, 'custom_theme_css_field_callback'),
            'slots-settings',
            'slots_styling',
            array('label_for' => 'custom_theme_css')
        );
        

        
        // Slot Editor Markup Field
        add_settings_field(
            'slot_editor_markup',
            __('Single Slot Page Markup:', 'slots'),
            array($this, 'slot_editor_markup_field_callback'),
            'slots-settings',
            'slots_editor',
            array('label_for' => 'slot_editor_markup')
        );
        
        // Slot Card Template Field
        add_settings_field(
            'slot_card_template',
            __('Slot Card Template:', 'slots'),
            array($this, 'slot_card_template_field_callback'),
            'slots-settings',
            'slots_card_template',
            array('label_for' => 'slot_card_template')
        );
        

        

    }
    
    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __('Configure your slots plugin settings below.', 'slots') . '</p>';
    }
    
    /**
     * Styling section callback
     */
    public function styling_section_callback() {
        echo '<p>' . __('Customize the appearance of your slots display with colors, fonts, and custom CSS.', 'slots') . '</p>';
    }
    

    

    
    /**
     * Editor section callback
     */
    public function editor_section_callback() {
        echo '<p>' . __('Customize the markup for individual slot pages. When this field is filled out, all slot detail shortcodes will automatically use this custom template. If left empty, the default template will be used. Use the variables below to insert dynamic content.', 'slots') . '</p>';
        echo '<div class="slots-editor-help">';
        echo '<h4>' . __('Available Variables:', 'slots') . '</h4>';
        echo '<ul>';
                            echo '<li><code>{{slot_image}}</code> - Slot thumbnail image URL</li>';
                    echo '<li><code>{{slot_rating}}</code> - Star rating value (e.g., "4.5/5", if enabled)</li>';
                    echo '<li><code>{{slot_rtp}}</code> - RTP percentage (e.g., "96.5%", if enabled)</li>';
                    echo '<li><code>{{slot_wager}}</code> - Wager range (e.g., "$0.10 - $100", if enabled)</li>';
                    echo '<li><code>{{slot_id}}</code> - Slot ID (text only)</li>';
                    echo '<li><code>{{slot_description}}</code> - Description/excerpt (formatted text, if enabled)</li>';
                    echo '<li><code>{{slot_permalink}}</code> - Slot permalink URL</li>';
                    echo '<li><code>{{star_rating}}</code> - Raw star rating HTML</li>';
                    echo '<li><code>{{rtp_value}}</code> - RTP value only (e.g., "96.5%")</li>';
                    echo '<li><code>{{wager_range}}</code> - Wager range value only (e.g., "$0.10 - $100")</li>';
                    echo '<li><code>{{provider_name}}</code> - Provider name only (text only)</li>';
                    echo '<li><code>{{slot_id_value}}</code> - Slot ID value only (text only)</li>';
        echo '</ul>';
        echo '</div>';
    }
    
    /**
     * Card Template section callback
     */
    public function card_template_section_callback() {
        echo '<p>' . __('Customize the markup for slot cards in grid view. When this field is filled out, all slot cards will automatically use this custom template. If left empty, the default template will be used. Use the variables below to insert dynamic content.', 'slots') . '</p>';
        echo '<div class="slots-editor-help">';
        echo '<h4>' . __('Available Variables:', 'slots') . '</h4>';
        echo '<ul>';
        echo '<li><code>{{slot_image}}</code> - Slot thumbnail image URL</li>';
        echo '<li><code>{{slot_title}}</code> - Slot title (text only)</li>';
        echo '<li><code>{{slot_provider}}</code> - Provider name (text only, if enabled)</li>';
        echo '<li><code>{{slot_rating}}</code> - Star rating value (e.g., "4.5/5", if enabled)</li>';
        echo '<li><code>{{slot_rtp}}</code> - RTP percentage (e.g., "96.5%", if enabled)</li>';
        echo '<li><code>{{slot_wager}}</code> - Wager range (e.g., "$0.10 - $100", if enabled)</li>';
        echo '<li><code>{{slot_id}}</code> - Slot ID (text only)</li>';
        echo '<li><code>{{slot_excerpt}}</code> - Description/excerpt (formatted text, if enabled)</li>';
        echo '<li><code>{{slot_permalink}}</code> - Slot permalink URL</li>';
        echo '<li><code>{{star_rating}}</code> - Raw star rating HTML</li>';
        echo '<li><code>{{rtp_value}}</code> - RTP value only (e.g., "96.5%")</li>';
        echo '<li><code>{{wager_range}}</code> - Wager range value only (e.g., "$0.10 - $100")</li>';
        echo '<li><code>{{provider_name}}</code> - Provider name only (text only)</li>';
        echo '<li><code>{{slot_id_value}}</code> - Slot ID value only (text only)</li>';
        echo '</ul>';
        echo '</div>';
    }
    
    /**
     * Grid Editor section callback
     */

    
    /**
     * Checkbox field callback
     */
    public function checkbox_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : 0;
        
        $field_name = $args['label_for'];
        
        echo '<input type="checkbox" id="' . esc_attr($field_name) . '" name="slots_settings[' . esc_attr($field_name) . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        switch ($field_name) {
            case 'enable_data_sync':
                echo '<label for="' . esc_attr($field_name) . '">' . __('Enable data sync', 'slots') . '</label>';
                break;
                
            case 'show_filters_default':
                echo '<label for="' . esc_attr($field_name) . '">' . __('Show filter controls by default in grid view', 'slots') . '</label>';
                break;
                
            case 'show_pagination_default':
                echo '<label for="' . esc_attr($field_name) . '">' . __('Show pagination controls by default', 'slots') . '</label>';
                break;
                
            default:
                echo '<label for="' . esc_attr($field_name) . '">' . __('Enable this feature', 'slots') . '</label>';
                break;
        }
    }
    
    /**
     * Color field callback
     */
    public function color_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        
        $field_name = $args['label_for'];
        
        echo '<input type="color" id="' . esc_attr($field_name) . '" name="slots_settings[' . esc_attr($field_name) . ']" value="' . esc_attr($value) . '" />';
        
        switch ($field_name) {
            case 'primary_color':
                echo '<p class="description">' . __('Main color for buttons and highlights', 'slots') . '</p>';
                break;
                
            case 'secondary_color':
                echo '<p class="description">' . __('Secondary color for text and borders', 'slots') . '</p>';
                break;
                
            case 'accent_color':
                echo '<p class="description">' . __('Accent color for special elements', 'slots') . '</p>';
                break;
        }
    }
    
    /**
     * Number field callback
     */
    public function number_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        
        $field_name = $args['label_for'];
        
        switch ($field_name) {
            case 'border_radius':
                echo '<input type="number" id="' . esc_attr($field_name) . '" name="slots_settings[' . esc_attr($field_name) . ']" value="' . esc_attr($value) . '" min="0" max="20" step="1" /> px';
                break;
                

                
            default:
                echo '<input type="number" id="' . esc_attr($field_name) . '" name="slots_settings[' . esc_attr($field_name) . ']" value="' . esc_attr($value) . '" min="0" max="100" step="1" />';
                break;
        }
    }
    
    /**
     * Select field callback
     */
    public function select_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        
        $field_name = $args['label_for'];
        
        switch ($field_name) {
            case 'font_family':
                echo '<select id="' . esc_attr($field_name) . '" name="slots_settings[' . esc_attr($field_name) . ']">';
                echo '<option value="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif"' . selected($value, '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif', false) . '>' . __('System Default', 'slots') . '</option>';
                echo '<option value="\'Arial\', sans-serif"' . selected($value, "'Arial', sans-serif", false) . '>' . __('Arial', 'slots') . '</option>';
                echo '<option value="\'Georgia\', serif"' . selected($value, "'Georgia', serif", false) . '>' . __('Georgia', 'slots') . '</option>';
                echo '<option value="\'Courier New\', monospace"' . selected($value, "'Courier New', monospace", false) . '>' . __('Courier New', 'slots') . '</option>';
                echo '</select>';
                echo '<p class="description">' . __('Font family for slot cards and text', 'slots') . '</p>';
                break;
                
            case 'default_columns':
                echo '<select id="' . esc_attr($field_name) . '" name="slots_settings[' . esc_attr($field_name) . ']">';
                echo '<option value="3"' . selected($value, '3', false) . '>' . __('3 Columns', 'slots') . '</option>';
                echo '<option value="4"' . selected($value, '4', false) . '>' . __('4 Columns', 'slots') . '</option>';
                echo '<option value="5"' . selected($value, '5', false) . '>' . __('5 Columns', 'slots') . '</option>';
                echo '</select>';
                echo '<p class="description">' . __('Default number of columns in grid view', 'slots') . '</p>';
                break;
        }
    }
    
    /**
     * Text field callback
     */
    public function text_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="slots_settings[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        
        if ($args['label_for'] === 'api_url') {
            echo '<p class="description">' . __('Enter the API endpoint URL for data synchronization', 'slots') . '</p>';
        }
    }
    
    /**
     * Button field callback
     */
    public function button_field_callback($args) {
        if ($args['label_for'] === 'sync_data_button') {
            echo '<button type="button" id="' . esc_attr($args['label_for']) . '" class="button button-secondary">' . __('Sync Data', 'slots') . '</button>';
            echo '<p class="description">' . __('Click to manually synchronize data from the master site using WordPress queue or cron', 'slots') . '</p>';
        }
    }
    
    /**
     * Textarea field callback
     */
    public function textarea_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        
        echo '<textarea id="' . esc_attr($args['label_for']) . '" name="slots_settings[' . esc_attr($args['label_for']) . ']" rows="10" cols="50" class="large-text code">' . esc_textarea($value) . '</textarea>';
        
        if ($args['label_for'] === 'custom_css') {
            echo '<p class="description">' . __('Add custom CSS to override default styles. You can use CSS variables like:', 'slots') . '</p>';
            echo ' <code>{{primary_color}}</code>, <code>{{secondary_color}}</code>, <code>{{border_radius}}</code>';
        }
    }
    
    /**
     * Slot Editor Markup field callback
     */
    public function slot_editor_markup_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        $override_checked = isset($options['slot_editor_override']) ? $options['slot_editor_override'] : 0;
        
        // Default markup example
                            $default_markup = '<div class="slot-detail-container">
                <div class="slot-detail-header">
                    <div class="slot-detail-image">
                        <img src="{{slot_image}}" alt="{{slot_title}}" class="slot-detail-main-image">
                    </div>
                    <div class="slot-detail-info">
                        <h1 class="slot-detail-title">{{slot_title}}</h1>
                        <div class="slot-detail-provider">Provider: {{slot_provider}}</div>
                        <div class="slot-detail-rating">Rating: {{slot_rating}}</div>
                        <div class="slot-detail-rtp">RTP: {{slot_rtp}}</div>
                        <div class="slot-detail-wager">Wager Range: {{slot_wager}}</div>
                        <div class="slot-detail-id">Slot ID: {{slot_id}}</div>
                    </div>
                </div>
                <div class="slot-detail-description">{{slot_description}}</div>
                <div class="slot-detail-actions">
                    <a href="{{slot_permalink}}" class="slot-detail-button primary">Play Now</a>
                </div>
            </div>';
        
        if (empty($value)) {
            $value = $default_markup;
        }
        
        // Show override checkbox
        echo '<p><input type="checkbox" id="slot_editor_override" name="slots_settings[slot_editor_override]" value="1" ' . checked(1, $override_checked, false) . ' />';
        echo '<label for="slot_editor_override">' . __('Override default template with custom markup', 'slots') . '</label></p>';
        
        echo '<textarea id="' . esc_attr($args['label_for']) . '" name="slots_settings[' . esc_attr($args['label_for']) . ']" rows="15" cols="80" class="large-text code">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . __('Customize the HTML markup for individual slot pages. Use the variables listed above to insert dynamic content.', 'slots') . '</p>';
        echo '<p class="description"><strong>' . __('Note:', 'slots') . '</strong> ' . __('To use this custom template, both the override checkbox must be checked AND this field must contain markup. If unchecked or empty, the default template will be used.', 'slots') . '</p>';
    }
    
    /**
     * Slot Card Template field callback
     */
    public function slot_card_template_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        $override_checked = isset($options['slot_card_template_override']) ? $options['slot_card_template_override'] : 0;
        
        // Default slot card markup example
        $default_markup = '<div class="slot-card" data-slot-id="{{slot_id}}">
    <div class="slot-card-image-container">
        <img src="{{slot_image}}" alt="{{slot_title}}" class="slot-card-image" loading="lazy">
        {{star_rating}}
    </div>
    <div class="slot-card-content">
        <h3 class="slot-card-title">
            <a href="{{slot_permalink}}" title="{{slot_title}}">{{slot_title}}</a>
        </h3>
        {{slot_provider}}
        <div class="slot-card-meta">
            {{slot_rating}}
            {{slot_rtp}}
        </div>
        {{slot_wager}}
        {{slot_excerpt}}
        <div class="slot-card-actions">
            <a href="{{slot_permalink}}" class="slot-card-button">More Info</a>
        </div>
    </div>
</div>';
        
        if (empty($value)) {
            $value = $default_markup;
        }
        
        // Show override checkbox
        echo '<p><input type="checkbox" id="slot_card_template_override" name="slots_settings[slot_card_template_override]" value="1" ' . checked(1, $override_checked, false) . ' />';
        echo '<label for="slot_card_template_override">' . __('Override default template with custom markup', 'slots') . '</label></p>';
        
        echo '<textarea id="' . esc_attr($args['label_for']) . '" name="slots_settings[' . esc_attr($args['label_for']) . ']" rows="15" cols="80" class="large-text code">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . __('Customize the HTML markup for slot cards in grid view. Use the variables listed above to insert dynamic content.', 'slots') . '</p>';
        echo '<p class="description"><strong>' . __('Note:', 'slots') . '</strong> ' . __('To use this custom template, both the override checkbox must be checked AND this field must contain markup. If unchecked or empty, the default template will be used.', 'slots') . '</p>';
    }
    

    
    /**
     * Custom Theme CSS field callback
     */
    public function custom_theme_css_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        
        // Default custom theme CSS example
        $default_css = '/* Custom Theme CSS for Slots */
.slots-grid {
    /* Add your custom styles here */
}

.slot-card {
    /* Customize slot card appearance */
}

.slot-card:hover {
    /* Hover effects */
}

/* You can use CSS variables for dynamic values:
   --primary-color: {{primary_color}};
   --secondary-color: {{secondary_color}};
   --border-radius: {{border_radius}}px;
*/';
        
        if (empty($value)) {
            $value = $default_css;
        }
        
        echo '<textarea id="' . esc_attr($args['label_for']) . '" name="slots_settings[' . esc_attr($args['label_for']) . ']" rows="15" cols="80" class="large-text code">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . __('Add your custom CSS to create a unique theme for your slots display. This CSS will override the default styles.', 'slots') . '</p>';
        echo '<p class="description"><strong>' . __('Note:', 'slots') . '</strong> ' . __('You can use CSS variables like <code>{{primary_color}}</code>, <code>{{secondary_color}}</code>, and <code>{{border_radius}}</code> to reference your color settings.', 'slots') . '</p>';
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'slots_meta_box',
            __('Slot Details', 'slots'),
            array($this, 'meta_box_callback'),
            'slot',
            'normal',
            'high'
        );
    }
    
    /**
     * Meta box callback
     */
    public function meta_box_callback($post) {
        wp_nonce_field('slots_meta_box', 'slots_meta_box_nonce');
        
        // Get existing values
        $slot_id = get_post_meta($post->ID, '_slots_slot_id', true);
        $star_rating = get_post_meta($post->ID, '_slots_star_rating', true);
        $provider_name = get_post_meta($post->ID, '_slots_provider_name', true);
        $rtp = get_post_meta($post->ID, '_slots_rtp', true);
        $min_wager = get_post_meta($post->ID, '_slots_min_wager', true);
        $max_wager = get_post_meta($post->ID, '_slots_max_wager', true);
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label>' . __('Slot ID', 'slots') . '</label></th>';
        echo '<td><strong>' . esc_html($slot_id ?: __('Not assigned yet', 'slots')) . '</strong>';
        echo '<p class="description">' . __('Unique identifier for this slot (auto-generated, used for syncing between sites)', 'slots') . '</p></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th><label for="slots_star_rating">' . __('Star Rating', 'slots') . '</label></th>';
        echo '<td>';
        echo '<select id="slots_star_rating" name="slots_star_rating">';
        for ($i = 1; $i <= 5; $i += 0.5) {
            $selected = ($star_rating == $i) ? 'selected' : '';
            echo '<option value="' . $i . '" ' . $selected . '>' . $i . ' Stars</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __('Select the star rating for this slot (1 to 5 stars)', 'slots') . '</p>';
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th><label for="slots_provider_name">' . __('Provider Name', 'slots') . '</label></th>';
        echo '<td><input type="text" id="slots_provider_name" name="slots_provider_name" value="' . esc_attr($provider_name) . '" class="regular-text" /></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th><label for="slots_rtp">' . __('RTP (%)', 'slots') . '</label></th>';
        echo '<td><input type="number" id="slots_rtp" name="slots_rtp" value="' . esc_attr($rtp) . '" min="0" max="100" step="0.1" />%</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th><label for="slots_min_wager">' . __('Minimum Wager', 'slots') . '</label></th>';
        echo '<td><input type="number" id="slots_min_wager" name="slots_min_wager" value="' . esc_attr($min_wager) . '" min="0" step="0.01" /></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th><label for="slots_max_wager">' . __('Maximum Wager', 'slots') . '</label></th>';
        echo '<td><input type="number" id="slots_max_wager" name="slots_max_wager" value="' . esc_attr($max_wager) . '" min="0" step="0.01" /></td>';
        echo '</tr>';
        echo '</table>';
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['slots_meta_box_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['slots_meta_box_nonce'], 'slots_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
                if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save star rating
        if (isset($_POST['slots_star_rating'])) {
            $star_rating = floatval($_POST['slots_star_rating']);
            if ($star_rating >= 1 && $star_rating <= 5) {
                update_post_meta($post_id, '_slots_star_rating', $star_rating);
            }
        }
        
        // Save provider name
        if (isset($_POST['slots_provider_name'])) {
            update_post_meta($post_id, '_slots_provider_name', sanitize_text_field($_POST['slots_provider_name']));
        }
        
        // Save RTP
        if (isset($_POST['slots_rtp'])) {
            $rtp = floatval($_POST['slots_rtp']);
            if ($rtp >= 0 && $rtp <= 100) {
                update_post_meta($post_id, '_slots_rtp', $rtp);
            }
        }
        
        // Save minimum wager
        if (isset($_POST['slots_min_wager'])) {
            $min_wager = floatval($_POST['slots_min_wager']);
            if ($min_wager >= 0) {
                update_post_meta($post_id, '_slots_min_wager', $min_wager);
            }
        }
        
        // Save maximum wager
        if (isset($_POST['slots_max_wager'])) {
            $max_wager = floatval($_POST['slots_max_wager']);
            if ($max_wager >= 0) {
                update_post_meta($post_id, '_slots_max_wager', $max_wager);
            }
        }
    }
    
    /**
     * Auto-generate slot ID when a new slot is created
     */
    public function auto_generate_slot_id($post_id, $post, $update) {
        // Only process slot post type
        if ($post->post_type !== 'slot') {
            return;
        }
        
        // Only generate ID for new posts, not updates
        if ($update) {
            return;
        }
        
        // Check if slot_id already exists
        $existing_slot_id = get_post_meta($post_id, '_slots_slot_id', true);
        if (!empty($existing_slot_id)) {
            return;
        }
        
        // Generate unique slot ID
        $slot_id = $this->generate_unique_slot_id();
        
        // Save the generated slot ID
        update_post_meta($post_id, '_slots_slot_id', $slot_id);
    }
    
    /**
     * Generate a unique slot ID
     */
    private function generate_unique_slot_id() {
        $prefix = 'SLOT';
        $counter = 1;
        
        do {
            $slot_id = $prefix . str_pad($counter, 6, '0', STR_PAD_LEFT);
            $counter++;
            
            // Check if this ID already exists
            $existing_posts = get_posts(array(
                'post_type' => 'slot',
                'meta_key' => '_slots_slot_id',
                'meta_value' => $slot_id,
                'post_status' => 'any',
                'posts_per_page' => 1
            ));
            
        } while (!empty($existing_posts));
        
        return $slot_id;
    }
    
    /**
     * Assign slot IDs to existing slots that don't have one
     */
    public function maybe_assign_slot_ids_to_existing() {
        // Only run once per session
        if (get_transient('slots_ids_assigned')) {
            return;
        }
        
        // Get all slots without slot_id
        $slots_without_id = get_posts(array(
            'post_type' => 'slot',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_slots_slot_id',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        
        if (!empty($slots_without_id)) {
            foreach ($slots_without_id as $slot) {
                $slot_id = $this->generate_unique_slot_id();
                update_post_meta($slot->ID, '_slots_slot_id', $slot_id);
            }
        }
        
        // Set transient to prevent running again this session
        set_transient('slots_ids_assigned', true, HOUR_IN_SECONDS);
    }
    
    /**
     * Get slot meta data
     */
    public static function get_slot_meta($post_id, $key = '') {
        if (empty($key)) {
            return array(
                'slot_id' => get_post_meta($post_id, '_slots_slot_id', true),
                'star_rating' => get_post_meta($post_id, '_slots_star_rating', true),
                'provider_name' => get_post_meta($post_id, '_slots_provider_name', true),
                'rtp' => get_post_meta($post_id, '_slots_rtp', true),
                'min_wager' => get_post_meta($post_id, '_slots_min_wager', true),
                'max_wager' => get_post_meta($post_id, '_slots_max_wager', true)
            );
        }
        
        $meta_keys = array(
            'slot_id' => '_slots_slot_id',
            'star_rating' => '_slots_star_rating',
            'provider_name' => '_slots_provider_name',
            'rtp' => '_slots_rtp',
            'min_wager' => '_slots_min_wager',
            'max_wager' => '_slots_max_wager'
        );
        
        if (isset($meta_keys[$key])) {
            return get_post_meta($post_id, $meta_keys[$key], true);
        }
        
        return false;
    }
    
    /**
     * Process slot card template with variables
     */
    public static function process_slot_card_template($template, $slot_data) {
        if (empty($template)) {
            return false;
        }
        
        // Prepare variables for replacement
        $variables = array(
            '{{slot_image}}' => !empty($slot_data['thumbnail']) ? $slot_data['thumbnail'] : SLOTS_PLUGIN_URL . 'assets/images/default-slot.svg',
            '{{slot_title}}' => !empty($slot_data['title']) ? esc_html($slot_data['title']) : '',
            '{{slot_provider}}' => !empty($slot_data['provider_name']) ? '<div class="slot-card-provider">' . esc_html($slot_data['provider_name']) . '</div>' : '',
            '{{slot_rating}}' => !empty($slot_data['star_rating']) ? '<div class="slot-card-rating"><span class="rating-value">' . number_format($slot_data['star_rating'], 1) . '</span></div>' : '',
            '{{slot_rtp}}' => !empty($slot_data['rtp']) ? '<div class="slot-card-rtp">RTP: ' . number_format($slot_data['rtp'], 1) . '%</div>' : '',
            '{{slot_wager}}' => self::format_wager_display($slot_data),
            '{{slot_id}}' => !empty($slot_data['slot_id']) ? esc_attr($slot_data['slot_id']) : '',
            '{{slot_excerpt}}' => !empty($slot_data['excerpt']) ? '<div class="slot-card-excerpt">' . wp_trim_words($slot_data['excerpt'], 15, '...') . '</div>' : '',
            '{{slot_permalink}}' => !empty($slot_data['permalink']) ? esc_url($slot_data['permalink']) : '',
            '{{star_rating}}' => !empty($slot_data['star_rating']) ? self::generate_star_rating_html($slot_data['star_rating']) : '',
            '{{rtp_value}}' => !empty($slot_data['rtp']) ? number_format($slot_data['rtp'], 1) . '%' : 'N/A',
            '{{wager_range}}' => self::format_wager_display($slot_data, false),
            '{{provider_name}}' => !empty($slot_data['provider_name']) ? esc_html($slot_data['provider_name']) : '',
            '{{slot_id_value}}' => !empty($slot_data['slot_id']) ? esc_html($slot_data['slot_id']) : ''
        );
        
        // Replace variables in template
        $processed_template = str_replace(array_keys($variables), array_values($variables), $template);
        
        return $processed_template;
    }
    
    /**
     * Format wager display
     */
    private static function format_wager_display($slot_data, $with_wrapper = true) {
        $wager_display = '';
        
        if (!empty($slot_data['min_wager']) && !empty($slot_data['max_wager'])) {
            $wager_display = '$' . number_format($slot_data['min_wager'], 2) . ' - $' . number_format($slot_data['max_wager'], 2);
        } elseif (!empty($slot_data['min_wager'])) {
            $wager_display = '$' . number_format($slot_data['min_wager'], 2) . '+';
        } elseif (!empty($slot_data['max_wager'])) {
            $wager_display = 'Up to $' . number_format($slot_data['max_wager'], 2);
        }
        
        if (empty($wager_display)) {
            return '';
        }
        
        if ($with_wrapper) {
            return '<div class="slot-card-wager"><span class="wager-label">' . __('Wager:', 'slots') . '</span><span class="wager-value">' . esc_html($wager_display) . '</span></div>';
        }
        
        return $wager_display;
    }
    
    /**
     * Generate star rating HTML
     */
    private static function generate_star_rating_html($rating) {
        if (empty($rating)) {
            return '';
        }
        
        $full_stars = floor($rating);
        $half_star = ($rating - $full_stars) >= 0.5;
        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
        
        $html = '<div class="slot-card-rating-overlay">';
        
        // Full stars
        for ($i = 0; $i < $full_stars; $i++) {
            $html .= '<span class="star full">★</span>';
        }
        
        // Half star
        if ($half_star) {
            $html .= '<span class="star half">★</span>';
        }
        
        // Empty stars
        for ($i = 0; $i < $empty_stars; $i++) {
            $html .= '<span class="star empty">☆</span>';
        }
        
        $html .= '</div>';
        
                return $html;
    }
    
    /**
     * Render slot card using custom template or default
     */
    public static function render_slot_card($slot_data) {
        $settings = get_option('slots_settings', array());
        $custom_template = isset($settings['slot_card_template']) ? $settings['slot_card_template'] : '';
        $override_enabled = isset($settings['slot_card_template_override']) ? $settings['slot_card_template_override'] : 0;
        
        if ($override_enabled && !empty($custom_template)) {
            // Use custom template
            $processed_template = self::process_slot_card_template($custom_template, $slot_data);
            if ($processed_template) {
                return $processed_template;
            }
        }
        
        // Fallback to default template
        return self::render_default_slot_card($slot_data);
    }
    
    /**
     * Render default slot card (fallback)
     */
    private static function render_default_slot_card($slot_data) {
        // Default image if no thumbnail
        $default_image = SLOTS_PLUGIN_URL . 'assets/images/default-slot.svg';
        $slot_image = !empty($slot_data['thumbnail']) ? $slot_data['thumbnail'] : $default_image;
        
        // Format RTP
        $rtp_display = !empty($slot_data['rtp']) ? number_format($slot_data['rtp'], 1) . '%' : 'N/A';
        
        // Format wager range
        $wager_display = self::format_wager_display($slot_data, false);
        
        // Generate star rating
        $star_rating = self::generate_star_rating_html($slot_data['star_rating']);
        
        ob_start();
        ?>
        <div class="slot-card" data-slot-id="<?php echo esc_attr($slot_data['slot_id']); ?>">
            
            <!-- Slot Image -->
            <div class="slot-card-image-container">
                <img src="<?php echo esc_url($slot_image); ?>" 
                     alt="<?php echo esc_attr($slot_data['title']); ?>" 
                     class="slot-card-image"
                     loading="lazy">
                
                <?php if (!empty($slot_data['star_rating'])): ?>
                <div class="slot-card-rating-overlay">
                    <?php echo $star_rating; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Slot Content -->
            <div class="slot-card-content">
                
                <!-- Title -->
                <h3 class="slot-card-title">
                    <a href="<?php echo esc_url($slot_data['permalink']); ?>" title="<?php echo esc_attr($slot_data['title']); ?>">
                        <?php echo esc_html($slot_data['title']); ?>
                    </a>
                </h3>
                
                <!-- Provider -->
                <?php if (!empty($slot_data['provider_name'])): ?>
                <div class="slot-card-provider">
                    <?php echo esc_html($slot_data['provider_name']); ?>
                </div>
                <?php endif; ?>
                
                <!-- Rating and RTP -->
                <div class="slot-card-meta">
                    <?php if (!empty($slot_data['star_rating'])): ?>
                    <div class="slot-card-rating">
                        <?php echo $star_rating; ?>
                        <span class="rating-value"><?php echo number_format($slot_data['star_rating'], 1); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($slot_data['rtp'])): ?>
                    <div class="slot-card-rtp">
                        RTP: <?php echo $rtp_display; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Wager Range -->
                <?php if (!empty($wager_display)): ?>
                <div class="slot-card-wager">
                    <span class="wager-label"><?php _e('Wager:', 'slots'); ?></span>
                    <span class="wager-value"><?php echo esc_html($wager_display); ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Excerpt -->
                <?php if (!empty($slot_data['excerpt'])): ?>
                <div class="slot-card-excerpt">
                    <?php echo wp_trim_words($slot_data['excerpt'], 15, '...'); ?>
                </div>
                <?php endif; ?>
                
                <!-- Action Button -->
                <div class="slot-card-actions">
                    <a href="<?php echo esc_url($slot_data['permalink']); ?>" class="slot-card-button">
                        <?php _e('More Info', 'slots'); ?>
                    </a>
                </div>
                
            </div>
            
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Demo page
     */
    public function demo_page() {
        include SLOTS_PLUGIN_DIR . 'templates/demo-page.php';
    }
}
