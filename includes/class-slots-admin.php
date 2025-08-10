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
        
        add_settings_section(
            'slots_general',
            __('General Settings', 'slots'),
            array($this, 'settings_section_callback'),
            'slots-settings'
        );
        
        add_settings_field(
            'slots_enable_feature',
            __('Enable Feature', 'slots'),
            array($this, 'checkbox_field_callback'),
            'slots-settings',
            'slots_general',
            array('label_for' => 'slots_enable_feature')
        );
    }
    
    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __('Configure your slots plugin settings below.', 'slots') . '</p>';
    }
    
    /**
     * Checkbox field callback
     */
    public function checkbox_field_callback($args) {
        $options = get_option('slots_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : 0;
        
        echo '<input type="checkbox" id="' . esc_attr($args['label_for']) . '" name="slots_settings[' . esc_attr($args['label_for']) . ']" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="' . esc_attr($args['label_for']) . '">' . __('Enable this feature', 'slots') . '</label>';
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
}
