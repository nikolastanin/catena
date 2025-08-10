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
        //add_filter('the_content', array($this, 'filter_slot_content'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue Tailwind CSS from CDN
        wp_enqueue_style(
            'tailwindcss',
            'https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css',
            array(),
            '3.4.0'
        );
        
        // Enqueue main CSS
        wp_enqueue_style(
            'slots-public',
            SLOTS_PLUGIN_URL . 'assets/css/slots-public.css',
            array('tailwindcss'),
            SLOTS_PLUGIN_VERSION
        );
        
        // Enqueue theme CSS if selected
        $themes = new Slots_Themes();
        $current_theme = $themes->get_current_theme();
        $themes->enqueue_theme($current_theme);
        
        // Add Tailwind CSS fallback and debugging
        add_action('wp_head', array($this, 'add_tailwind_fallback'), 20);
        
        // Enqueue jQuery (if not already loaded)
        wp_enqueue_script('jquery');
        
        // Enqueue custom JavaScript
        wp_enqueue_script(
            'slots-public',
            SLOTS_PLUGIN_URL . 'assets/js/slots-public.js',
            array('jquery'),
            SLOTS_PLUGIN_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('slots-public', 'slots_ajax', array(
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
     * Filter slot post content to append slot_detail shortcode if not present
     * 
     * @param string $content The post content
     * @return string Modified content
     */
    public function filter_slot_content($content) {
        // Prevent infinite loop by checking if we're already processing
        static $processing = false;
        if ($processing) {
            return $content;
        }
        
        // Only apply to slot post types
        if (get_post_type() !== 'slot') {
            return $content;
        }
        
        // Only apply on single slot pages
        if (!is_singular('slot')) {
            return $content;
        }
        
        // Check if slot_detail already exists in content using regex
        if (preg_match('/slot_detail/', $content)) {
            // slot_detail already exists, return content as is
            return $content;
        }
        
        // Set processing flag to prevent recursion
        $processing = true;
        
        // Append slot_detail shortcode to the top of existing content
        $slot_detail = do_shortcode('[slot_detail]');
        $result = $slot_detail . "\n\n" . $content;
        
        // Reset processing flag
        $processing = false;
        
        return $result;
    }
    
    /**
     * Add Tailwind CSS fallback and debugging
     */
    public function add_tailwind_fallback() {
        echo '<script>
        // Check if Tailwind CSS is loaded
        document.addEventListener("DOMContentLoaded", function() {
            // Test if Tailwind classes are working
            var testElement = document.createElement("div");
            testElement.className = "bg-red-500 text-white p-4 rounded";
            testElement.style.position = "fixed";
            testElement.style.top = "10px";
            testElement.style.right = "10px";
            testElement.style.zIndex = "9999";
            testElement.innerHTML = "Tailwind Test";
            document.body.appendChild(testElement);
            
            // Remove after 3 seconds
            setTimeout(function() {
                if (testElement.parentNode) {
                    testElement.parentNode.removeChild(testElement);
                }
            }, 3000);
        });
        </script>';
        
        // Also add some basic Tailwind-like styles as fallback
        echo '<style id="slots-tailwind-fallback">
        .bg-white { background-color: #ffffff !important; }
        .border { border-width: 1px !important; }
        .border-gray-200 { border-color: #e5e7eb !important; }
        .rounded-xl { border-radius: 0.75rem !important; }
        .p-6 { padding: 1.5rem !important; }
        .my-5 { margin-top: 1.25rem !important; margin-bottom: 1.25rem !important; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }
        .font-sans { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif !important; }
        .flex { display: flex !important; }
        .items-center { align-items: center !important; }
        .mb-5 { margin-bottom: 1.25rem !important; }
        .gap-4 { gap: 1rem !important; }
        .md\\:gap-6 { gap: 1.5rem !important; }
        .flex-shrink-0 { flex-shrink: 0 !important; }
        .w-20 { width: 5rem !important; }
        .h-20 { height: 5rem !important; }
        .md\\:w-24 { width: 6rem !important; }
        .md\\:h-24 { height: 6rem !important; }
        .object-cover { object-fit: cover !important; }
        .rounded-lg { border-radius: 0.5rem !important; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; }
        .flex-1 { flex: 1 1 0% !important; }
        .text-xl { font-size: 1.25rem !important; line-height: 1.75rem !important; }
        .md\\:text-2xl { font-size: 1.5rem !important; line-height: 2rem !important; }
        .font-semibold { font-weight: 600 !important; }
        .text-gray-900 { color: #111827 !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .leading-tight { line-height: 1.25 !important; }
        .text-gray-500 { color: #6b7280 !important; }
        .text-sm { font-size: 0.875rem !important; line-height: 1.25rem !important; }
        .bg-gray-100 { background-color: #f3f4f6 !important; }
        .px-2 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
        .py-1 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
        .rounded-md { border-radius: 0.375rem !important; }
        .text-xs { font-size: 0.75rem !important; line-height: 1rem !important; }
        .font-medium { font-weight: 500 !important; }
        .flex-col { flex-direction: column !important; }
        .md\\:flex-row { flex-direction: row !important; }
        .mb-6 { margin-bottom: 1.5rem !important; }
        .bg-gray-50 { background-color: #f9fafb !important; }
        .text-center { text-align: center !important; }
        .flex-1 { flex: 1 1 0% !important; }
        .uppercase { text-transform: uppercase !important; }
        .font-medium { font-weight: 500 !important; }
        .tracking-wide { letter-spacing: 0.05em !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .text-lg { font-size: 1.125rem !important; line-height: 1.75rem !important; }
        .font-bold { font-weight: 700 !important; }
        .inline-block { display: inline-block !important; }
        .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)) !important; }
        .from-blue-500 { --tw-gradient-from: #3b82f6 !important; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(59, 130, 246, 0)) !important; }
        .to-purple-600 { --tw-gradient-to: #9333ea !important; }
        .text-white { color: #ffffff !important; }
        .px-8 { padding-left: 2rem !important; padding-right: 2rem !important; }
        .py-3\\.5 { padding-top: 0.875rem !important; padding-bottom: 0.875rem !important; }
        .font-semibold { font-weight: 600 !important; }
        .text-base { font-size: 1rem !important; line-height: 1.5rem !important; }
        .transition-all { transition-property: all !important; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important; transition-duration: 150ms !important; }
        .duration-300 { transition-duration: 300ms !important; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }
        .hover\\:shadow-xl:hover { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; }
        .hover\\:-translate-y-0\\.5:hover { transform: translateY(-0.125rem) !important; }
        .active\\:translate-y-0:active { transform: translateY(0) !important; }
        .min-w-\\[140px\\] { min-width: 140px !important; }
        .no-underline { text-decoration: none !important; }
        .hover\\:text-white:hover { color: #ffffff !important; }
        </style>';
    }
    
    /**
     * Generate single slot card for AJAX requests
     */
    private function generate_single_slot_card_for_ajax($slot, $atts) {
        // Use the new render_slot_card method from Slots_Admin
        return Slots_Admin::render_slot_card($slot);
    }
}
