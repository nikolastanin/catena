<?php
/**
 * Theme Management for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Themes {
    
    /**
     * Available themes
     */
    private $themes = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_themes();
    }
    
    /**
     * Initialize available themes
     */
    private function init_themes() {
        $this->themes = array(
            'default' => array(
                'name' => __('Default', 'slots'),
                'description' => __('Clean, modern design with balanced shadows and borders', 'slots'),
                'file' => '',
                'preview' => 'default'
            ),
            'custom' => array(
                'name' => __('Custom Theme', 'slots'),
                'description' => __('Your own custom CSS theme defined in the settings', 'slots'),
                'file' => 'custom_theme.css',
                'preview' => 'custom'
            )
        );
    }
    
    /**
     * Get theme by key
     */
    public function get_theme($key) {
        return isset($this->themes[$key]) ? $this->themes[$key] : false;
    }
    
    /**
     * Get current theme from settings
     */
    public function get_current_theme() {
        $settings = get_option('slots_settings', array());
        return isset($settings['custom_theme_css']) && !empty($settings['custom_theme_css']) ? 'custom' : 'default';
    }
    
    /**
     * Get theme CSS file path
     */
    public function get_theme_file($theme_key) {
        if ($theme_key === 'default' || empty($theme_key)) {
            return false;
        }
        
        // Custom theme doesn't use a file, it uses inline CSS
        if ($theme_key === 'custom') {
            return false;
        }
        
        $theme = $this->get_theme($theme_key);
        if (!$theme || empty($theme['file'])) {
            return false;
        }
        
        $file_path = SLOTS_PLUGIN_DIR . 'assets/themes/' . $theme['file'];
        return file_exists($file_path) ? $file_path : false;
    }
    
    /**
     * Get theme CSS content
     */
    public function get_theme_css($theme_key) {
        if ($theme_key === 'custom') {
            $settings = get_option('slots_settings', array());
            return isset($settings['custom_theme_css']) ? $settings['custom_theme_css'] : '';
        }
        
        $file_path = $this->get_theme_file($theme_key);
        if (!$file_path) {
            return '';
        }
        
        return file_get_contents($file_path);
    }
    
    /**
     * Get theme class name
     */
    public function get_theme_class($theme_key) {
        if ($theme_key === 'default' || empty($theme_key)) {
            return '';
        }
        
        // For custom theme, we don't need a specific class since CSS is inline
        if ($theme_key === 'custom') {
            return 'slots-theme-custom';
        }
        
        return 'slots-theme-' . $theme_key;
    }
    
    /**
     * Enqueue theme CSS
     */
    public function enqueue_theme($theme_key) {
        if ($theme_key === 'default' || empty($theme_key)) {
            return;
        }
        
        // Handle custom theme CSS
        if ($theme_key === 'custom') {
            $this->enqueue_custom_theme();
            return;
        }
        
        $theme = $this->get_theme($theme_key);
        if (!$theme || empty($theme['file'])) {
            return;
        }
        
        wp_enqueue_style(
            'slots-theme-' . $theme_key,
            SLOTS_PLUGIN_URL . 'assets/themes/' . $theme['file'],
            array('slots-public'),
            SLOTS_PLUGIN_VERSION
        );
    }
    
    /**
     * Enqueue custom theme CSS
     */
    private function enqueue_custom_theme() {
        $settings = get_option('slots_settings', array());
        $custom_css = isset($settings['custom_theme_css']) ? $settings['custom_theme_css'] : '';
        
        if (empty($custom_css)) {
            return;
        }
        
        // Process CSS variables
        $custom_css = $this->process_css_variables($custom_css);
        
        // Add inline CSS
        wp_add_inline_style('slots-public', $custom_css);
    }
    
    /**
     * Process CSS variables in custom theme CSS
     */
    private function process_css_variables($css) {
        $settings = get_option('slots_settings', array());
        
        // Replace CSS variables with actual values
        $replacements = array(
            '{{primary_color}}' => isset($settings['primary_color']) ? $settings['primary_color'] : '#0073aa',
            '{{secondary_color}}' => isset($settings['secondary_color']) ? $settings['secondary_color'] : '#666666',
            '{{accent_color}}' => isset($settings['accent_color']) ? $settings['accent_color'] : '#ff6b6b',
            '{{border_radius}}' => isset($settings['border_radius']) ? $settings['border_radius'] : '8',
        );
        
        foreach ($replacements as $variable => $value) {
            $css = str_replace($variable, $value, $css);
        }
        
        return $css;
    }
}
