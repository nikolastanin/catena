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
            'dark' => array(
                'name' => __('Dark', 'slots'),
                'description' => __('Dark theme with high contrast and modern aesthetics', 'slots'),
                'file' => 'dark.css',
                'preview' => 'dark'
            ),
            'light' => array(
                'name' => __('Light', 'slots'),
                'description' => __('Bright, clean theme with enhanced shadows', 'slots'),
                'file' => 'light.css',
                'preview' => 'light'
            ),
            'minimal' => array(
                'name' => __('Minimal', 'slots'),
                'description' => __('Flat design with no shadows or rounded corners', 'slots'),
                'file' => 'minimal.css',
                'preview' => 'minimal'
            ),
            'rounded' => array(
                'name' => __('Rounded', 'slots'),
                'description' => __('Soft, friendly design with enhanced border radius', 'slots'),
                'file' => 'rounded.css',
                'preview' => 'rounded'
            ),
            'colorful' => array(
                'name' => __('Colorful', 'slots'),
                'description' => __('Vibrant theme with playful colors and animations', 'slots'),
                'file' => 'colorful.css',
                'preview' => 'colorful'
            ),
            'ai' => array(
                'name' => __('AI', 'slots'),
                'description' => __('Vibrant theme with playful colors and animations', 'slots'),
                'file' => 'ai_generated.css',
                'preview' => 'ai'
            )
        );
    }
    
    /**
     * Get all available themes
     */
    public function get_themes() {
        return $this->themes;
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
        return isset($settings['theme']) ? $settings['theme'] : 'default';
    }
    
    /**
     * Get theme CSS file path
     */
    public function get_theme_file($theme_key) {
        if ($theme_key === 'default' || empty($theme_key)) {
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
        
        return 'slots-theme-' . $theme_key;
    }
    
    /**
     * Enqueue theme CSS
     */
    public function enqueue_theme($theme_key) {
        if ($theme_key === 'default' || empty($theme_key)) {
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
}
