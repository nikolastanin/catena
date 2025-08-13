<?php
/**
 * Enhanced Shortcodes for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Shortcodes {
    
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
        add_shortcode('slots_grid', array($this, 'slots_grid_shortcode'));
        add_shortcode('slot_detail', array($this, 'slot_detail_shortcode'));
    }
    
    /**
     * Slots Grid Shortcode
     * Usage: [slots_grid limit="12" sort="recent"]
     */
    public function slots_grid_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => defined('SLOTS_DEFAULT_LIMIT') ? SLOTS_DEFAULT_LIMIT : 12,
            'sort' => defined('SLOTS_DEFAULT_SORT') ? SLOTS_DEFAULT_SORT : 'recent', // recent, random
            'class' => '',
            'show_filters' => defined('SLOTS_ENABLE_GRID_FILTERS') && SLOTS_ENABLE_GRID_FILTERS ? 'true' : 'false',
            'show_pagination' => defined('SLOTS_ENABLE_PAGINATION') && SLOTS_ENABLE_PAGINATION ? 'true' : 'false',
            'template' => 'auto' // auto, default, editor
        ), $atts, 'slots_grid');
        
        // Get slots based on parameters
        $slots = $this->get_slots_for_grid($atts);
        
        // Determine which template to use
        $template_file = $this->get_grid_template_file($atts, $slots);
        
        // Start output buffering
        ob_start();
        
        // Include the appropriate template
        include $template_file;
        
        return ob_get_clean();
    }
    
    /**
     * Slot Detail Shortcode
     * Usage: [slot_detail id="123" show_rating="true" show_description="true"]
     */
    public function slot_detail_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'show_rating' => 'true',
            'show_description' => 'true',
            'show_provider' => 'true',
            'show_rtp' => 'true',
            'show_wager' => 'true',
            'class' => ''
        ), $atts, 'slot_detail');
        
        // If no ID provided, try to get current post ID
        if (empty($atts['id'])) {
            $atts['id'] = get_the_ID();
        }
        
        // Get slot data
        $slot = $this->get_slot_detail($atts['id']);
        
        if (!$slot) {
            return '<div class="slots-error">' . __('Slot not found.', 'slots') . '</div>';
        }
        
        // Determine which template to use based on Custom Slot Markup setting
        $settings = get_option('slots_settings', array());
        $custom_markup = !empty($settings['slot_editor_markup']) ? $settings['slot_editor_markup'] : '';
        $override_enabled = !empty($settings['slot_editor_override']) ? $settings['slot_editor_override'] : 0;
        
        // If override is enabled AND Custom Slot Markup is filled, use editor template, otherwise use default
        $template_key = ($override_enabled && !empty($custom_markup)) ? 'editor' : 'default';
        
        // Start output buffering
        ob_start();
        
        // Get template manager and load template
        $template_manager = new Slots_Template_Manager();
        $template_key = $template_manager->validate_template_key($template_key);
        $template_manager->load_template($template_key, $slot, $atts);
        
        return ob_get_clean();
    }
    
    /**
     * Get slots for grid display
     */
    private function get_slots_for_grid($atts) {
        // Check cache first
        $cache_key = 'slots_grid_' . $atts['sort'] . '_' . $atts['limit'];
        $cached_slots = Slots_Cache::get($cache_key);
        if (false !== $cached_slots) {
            return $cached_slots;
        }

        $args = array(
            'post_type' => 'slot',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['limit'])
        );
        
        // Set sorting - only random and recent
        switch ($atts['sort']) {
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
        // Cache the results
        Slots_Cache::set($cache_key, $slots);
        
        return $slots;
    }
    
    /**
     * Get slot detail by ID
     */
    private function get_slot_detail($slot_id) {
        if (empty($slot_id)) {
            return false;
        }
        
        // Check cache first
        $cache_key = 'slot_detail_' . $slot_id;
        $cached_slot = Slots_Cache::get($cache_key);
        
        if (false !== $cached_slot) {
            return $cached_slot;
        }
        
        // Try to get by slot ID first
        $posts = get_posts(array(
            'post_type' => 'slot',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_slots_slot_id',
                    'value' => sanitize_text_field($slot_id),
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (empty($posts)) {
            // Try to get by post ID
            $post = get_post(intval($slot_id));
            if (!$post || $post->post_type !== 'slot') {
                return false;
            }
        } else {
            $post = $posts[0];
        }
        
        $post_id = $post->ID;
        
        $slot_data = array(
            'id' => $post_id,
            'title' => get_the_title($post_id),
            'excerpt' => get_the_excerpt($post_id),
            'content' => get_the_content(null, false, $post_id),
            'permalink' => get_permalink($post_id),
            'thumbnail' => get_the_post_thumbnail_url($post_id, 'large'),
            'slot_id' => Slots_Admin::get_slot_meta($post_id, 'slot_id'),
            'star_rating' => Slots_Admin::get_slot_meta($post_id, 'star_rating'),
            'provider_name' => Slots_Admin::get_slot_meta($post_id, 'provider_name'),
            'rtp' => Slots_Admin::get_slot_meta($post_id, 'rtp'),
            'min_wager' => Slots_Admin::get_slot_meta($post_id, 'min_wager'),
            'max_wager' => Slots_Admin::get_slot_meta($post_id, 'max_wager'),
            'modified' => get_the_modified_date('U', $post_id)
        );
        
        // Cache the slot data
        Slots_Cache::set($cache_key, $slot_data);
        
        return $slot_data;
    }
    
    /**
     * Determine which grid template file to use
     */
    private function get_grid_template_file($atts, $slots) {
        // Always use the default grid template
        return SLOTS_PLUGIN_DIR . 'templates/slots-grid.php';
    }
    
    /**
     * Generate star rating HTML
     */
    public static function generate_star_rating($rating, $max_rating = 5) {
        $rating = floatval($rating);
        $max_rating = intval($max_rating);
        
        $html = '<div class="star-rating">';
        
        for ($i = 1; $i <= $max_rating; $i++) {
            if ($i <= $rating) {
                $html .= '<span class="star">★</span>';
            } elseif ($i - 0.5 <= $rating) {
                $html .= '<span class="star">☆</span>';
            } else {
                $html .= '<span class="star empty">☆</span>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
