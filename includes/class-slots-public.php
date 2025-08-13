<?php
/**
 * Public-facing functionality for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Public {
    
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'add_custom_styles'));
        add_action('wp_ajax_load_more_slots', array($this, 'load_more_slots'));
        add_action('wp_ajax_nopriv_load_more_slots', array($this, 'load_more_slots'));
        add_action('wp_ajax_load_slots_grid', array($this, 'load_slots_grid'));
        add_action('wp_ajax_nopriv_load_slots_grid', array($this, 'load_slots_grid'));
        
        // Filter content for slot posts to show slot_detail shortcode when no content
        add_filter('the_content', array($this, 'filter_slot_content'));
    }

    /**
     * Summary of filter_slot_contents
     * stupid way to render slot cpt content but it works...
     * @param mixed $content
     */
    public function filter_slot_content($content) {
        static $is_processing = false;
        
        // Prevent infinite loop
        if ($is_processing) {
            return $content;
        }
        
        if (is_singular('slot')) {
            if (str_contains($content, '[slot_detail]')) {
                // Set flag to prevent recursion
                $is_processing = true;
                return $content;
            } else {
                // Set flag to prevent recursion
                $is_processing = true;
                return '[slot_detail]' . $content;
            }
        }
        
        return $content;
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        
        // Enqueue main CSS (built by Vite)
        wp_enqueue_style(
            'slots-frontend',
            SLOTS_PLUGIN_URL . 'assets/dist/css/slots-frontend.css',
            array(),
            SLOTS_PLUGIN_VERSION
        );
        
        // Enqueue theme CSS if selected
        $themes = new Slots_Themes();
        $current_theme = $themes->get_current_theme();
        $themes->enqueue_theme($current_theme);
        

        
        // Enqueue jQuery (if not already loaded)
        wp_enqueue_script('jquery');
        
        // Enqueue custom JavaScript (built by Vite)
        wp_enqueue_script(
            'slots-frontend',
            SLOTS_PLUGIN_URL . 'assets/dist/js/slots-frontend.js',
            array('jquery'),
            SLOTS_PLUGIN_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('slots-frontend', 'slots_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('slots_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'slots'),
                'error' => __('Error loading slots. Please try again.', 'slots'),
                'no_more' => __('No more slots to load.', 'slots')
            )
        ));
    }
    
    /**
     * Add custom styles from settings
     */
    public function add_custom_styles() {
        $settings = get_option('slots_settings', array());
        
        // Always add dynamic CSS variables
        echo '<style id="slots-dynamic-styles">' . "\n";
        echo $this->generate_dynamic_css($settings);
        echo '</style>' . "\n";
        
        // Add custom CSS if provided
        if (!empty($settings['custom_css'])) {
            echo '<style id="slots-custom-styles">' . "\n";
            echo $this->process_custom_css($settings['custom_css']);
            echo '</style>' . "\n";
        }
    }
    
    /**
     * Process custom CSS with variable replacement
     */
    private function process_custom_css($css) {
        $settings = get_option('slots_settings', array());
        
        // Replace CSS variables with actual values
        $replacements = array(
            '{{primary_color}}' => !empty($settings['primary_color']) ? $settings['primary_color'] : '#3b82f6',
            '{{secondary_color}}' => !empty($settings['secondary_color']) ? $settings['secondary_color'] : '#64748b',
            '{{accent_color}}' => !empty($settings['accent_color']) ? $settings['accent_color'] : '#f59e0b',
            '{{text_color}}' => !empty($settings['text_color']) ? $settings['text_color'] : '#1e293b',
            '{{bg_color}}' => !empty($settings['bg_color']) ? $settings['bg_color'] : '#ffffff',
            '{{border_radius}}' => !empty($settings['border_radius']) ? $settings['border_radius'] . 'px' : '8px',
            '{{font_family}}' => !empty($settings['font_family']) ? $settings['font_family'] : '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
        );
        
        return str_replace(array_keys($replacements), array_values($replacements), $css);
    }
    
    /**
     * Generate dynamic CSS based on settings
     */
    private function generate_dynamic_css($settings) {
        $css = ":root {\n";
        
        // Colors
        $css .= "    --slots-primary: " . (!empty($settings['primary_color']) ? $settings['primary_color'] : '#3b82f6') . ";\n";
        $css .= "    --slots-primary-hover: " . (!empty($settings['primary_color']) ? $this->adjust_brightness($settings['primary_color'], -20) : '#2563eb') . ";\n";
        $css .= "    --slots-secondary: " . (!empty($settings['secondary_color']) ? $settings['secondary_color'] : '#64748b') . ";\n";
        $css .= "    --slots-accent: " . (!empty($settings['accent_color']) ? $settings['accent_color'] : '#f59e0b') . ";\n";
        
        // Border radius
        $css .= "    --slots-radius-md: " . (!empty($settings['border_radius']) ? $settings['border_radius'] . 'px' : '8px') . ";\n";
        $css .= "    --slots-radius-lg: " . (!empty($settings['border_radius']) ? ($settings['border_radius'] + 4) . 'px' : '12px') . ";\n";
        
        // Font family
        $css .= "    --slots-font-family: " . (!empty($settings['font_family']) ? $settings['font_family'] : '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif') . ";\n";
        
        $css .= "}\n";
        
        // Apply font family to slots container
        if (!empty($settings['font_family'])) {
            $css .= ".slots-container { font-family: " . $settings['font_family'] . "; }\n";
        }
        
        // Apply border radius to cards and buttons
        if (!empty($settings['border_radius'])) {
            $radius = $settings['border_radius'] . 'px';
            $css .= ".slot-card, .slot-detail-button, .slots-filter select { border-radius: " . $radius . "; }\n";
        }
        
        return $css;
    }
    
    /**
     * Adjust color brightness
     */
    private function adjust_brightness($hex, $steps) {
        $hex = str_replace('#', '', $hex);
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * AJAX handler for load more slots
     */
    public function load_more_slots() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'slots_nonce')) {
            wp_send_json_error('Security check failed');
        }
        
        $page = intval($_POST['page']);
        $limit = intval($_POST['limit']);
        $sort = sanitize_text_field($_POST['sort']);
        
        // Get slots based on parameters
        $args = array(
            'post_type' => 'slot',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'paged' => $page
        );
        
        // Set sorting - only random and recent
        switch ($sort) {
            case 'random':
                $args['orderby'] = 'rand';
                break;
            case 'recent':
            default:
                $args['orderby'] = 'modified';
                $args['order'] = 'DESC';
                break;
        }
        
        $query = new WP_Query($args);
        $slots = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                $slots[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'excerpt' => get_the_excerpt(),
                    'content' => get_the_content(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url($post_id, 'medium'),
                    'thumbnail_large' => get_the_post_thumbnail_url($post_id, 'large'),
                    'slot_id' => Slots_Admin::get_slot_meta($post_id, 'slot_id'),
                    'star_rating' => Slots_Admin::get_slot_meta($post_id, 'star_rating'),
                    'provider_name' => Slots_Admin::get_slot_meta($post_id, 'provider_name'),
                    'rtp' => Slots_Admin::get_slot_meta($post_id, 'rtp'),
                    'min_wager' => Slots_Admin::get_slot_meta($post_id, 'min_wager'),
                    'max_wager' => Slots_Admin::get_slot_meta($post_id, 'max_wager'),
                    'modified' => get_the_modified_date('U')
                );
            }
            wp_reset_postdata();
        }
        
        if (empty($slots)) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        ob_start();
        foreach ($slots as $slot) {
            echo Slots_Admin::render_slot_card($slot);
        }
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => count($slots) >= $limit
        ));
    }
    
    /**
     * Load slots for grid editor (AJAX)
     */
    public function load_slots_grid() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'slots_nonce')) {
            wp_send_json_error('Security check failed');
        }
        
        $page = intval($_POST['page']);
        $limit = intval($_POST['limit']);
        $sort = sanitize_text_field($_POST['sort']);
        
        // Get slots based on parameters
        $args = array(
            'post_type' => 'slot',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'paged' => $page
        );
        
        // Set sorting - only random and recent
        switch ($sort) {
            case 'random':
                $args['orderby'] = 'rand';
                break;
            case 'recent':
            default:
                $args['orderby'] = 'modified';
                $args['order'] = 'DESC';
                break;
        }
        
        $query = new WP_Query($args);
        $slots = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                $slots[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'excerpt' => get_the_excerpt(),
                    'content' => get_the_content(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url($post_id, 'medium'),
                    'thumbnail_large' => get_the_post_thumbnail_url($post_id, 'large'),
                    'slot_id' => Slots_Admin::get_slot_meta($post_id, 'slot_id'),
                    'star_rating' => Slots_Admin::get_slot_meta($post_id, 'star_rating'),
                    'provider_name' => Slots_Admin::get_slot_meta($post_id, 'provider_name'),
                    'rtp' => Slots_Admin::get_slot_meta($post_id, 'rtp'),
                    'min_wager' => Slots_Admin::get_slot_meta($post_id, 'min_wager'),
                    'max_wager' => Slots_Admin::get_slot_meta($post_id, 'max_wager'),
                    'modified' => get_the_modified_date('U')
                );
            }
            wp_reset_postdata();
        }
        
        if (empty($slots)) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        // Generate HTML using the grid editor template functions
        $html = '';
        foreach ($slots as $slot) {
            $html .= generate_single_slot_card_for_ajax($slot, array(
                'limit' => $limit,
                'sort' => $sort,
                'show_filters' => 'true',
                'show_pagination' => 'true'
            ));
        }
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => count($slots) >= $limit
        ));
    }
    
    /**
     * Get slots for display
     */
    public function get_slots($args = array()) {
        $defaults = array(
            'post_type' => 'slot',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'orderby' => 'modified',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        $query = new WP_Query($args);
        
        return $query;
    }
    
    /**
     * Get slot by ID
     */
    public function get_slot($slot_id) {
        if (empty($slot_id)) {
            return false;
        }
        
        $post = get_post($slot_id);
        
        if (!$post || $post->post_type !== 'slot') {
            return false;
        }
        
        return array(
            'id' => $post->ID,
            'title' => get_the_title($post->ID),
            'excerpt' => get_the_excerpt($post->ID),
            'content' => get_the_content(null, false, $post->ID),
            'permalink' => get_permalink($post->ID),
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
            'slot_id' => Slots_Admin::get_slot_meta($post->ID, 'slot_id'),
            'star_rating' => Slots_Admin::get_slot_meta($post->ID, 'star_rating'),
            'provider_name' => Slots_Admin::get_slot_meta($post->ID, 'provider_name'),
            'rtp' => Slots_Admin::get_slot_meta($post->ID, 'rtp'),
            'min_wager' => Slots_Admin::get_slot_meta($post->ID, 'min_wager'),
            'max_wager' => Slots_Admin::get_slot_meta($post->ID, 'max_wager'),
            'modified' => get_the_modified_date('U', $post->ID)
        );
    }
    

    

    
    /**
     * Generate single slot card for AJAX requests
     */
    private function generate_single_slot_card_for_ajax($slot, $atts) {
        // Use the new render_slot_card method from Slots_Admin
        return Slots_Admin::render_slot_card($slot);
    }
}
