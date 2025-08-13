<?php
/**
 * Template Manager for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Template_Manager {
    
    /**
     * Available templates for slot detail
     */
    private $available_templates = array();
    
    /**
     * Default template
     */
    private $default_template = 'default';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->register_default_templates();
        add_action('init', array($this, 'register_custom_templates'));
    }
    
    /**
     * Register default templates
     */
    private function register_default_templates() {
        $this->available_templates = array(
            'default' => array(
                'name' => __('Default', 'slots'),
                'file' => 'slot-detail.php',
                'description' => __('Standard slot detail layout with full information', 'slots')
            ),
            'editor' => array(
                'name' => __('Custom Editor', 'slots'),
                'file' => 'slot-editor.php',
                'description' => __('Customizable layout using admin-defined markup', 'slots')
            )
        );
    }
    
    /**
     * Register custom templates from theme or other sources
     */
    public function register_custom_templates() {
        // Allow themes to register custom templates
        $custom_templates = apply_filters('slots_custom_templates', array());
        
        if (!empty($custom_templates) && is_array($custom_templates)) {
            foreach ($custom_templates as $key => $template) {
                if (isset($template['name']) && isset($template['file'])) {
                    $this->available_templates[$key] = $template;
                }
            }
        }
    }
    

    
    /**
     * Get template by key
     */
    public function get_template($template_key) {
        if (isset($this->available_templates[$template_key])) {
            return $this->available_templates[$template_key];
        }
        
        // Fallback to default
        return $this->available_templates[$this->default_template];
    }
    
    /**
     * Check if template exists
     */
    public function template_exists($template_key) {
        return isset($this->available_templates[$template_key]);
    }
    
    /**
     * Get template file path
     */
    public function get_template_path($template_key) {
        $template = $this->get_template($template_key);
        
        if (!$template) {
            return false;
        }
        
        // Check if template exists in plugin templates directory
        $plugin_template_path = SLOTS_PLUGIN_DIR . 'templates/' . $template['file'];
        
        if (file_exists($plugin_template_path)) {
            return $plugin_template_path;
        }
        
        // Check if template exists in theme directory
        $theme_template_path = get_template_directory() . '/slots/' . $template['file'];
        
        if (file_exists($theme_template_path)) {
            return $theme_template_path;
        }
        
        // Check if template exists in child theme directory
        if (is_child_theme()) {
            $child_theme_template_path = get_stylesheet_directory() . '/slots/' . $template['file'];
            
            if (file_exists($child_theme_template_path)) {
                return $child_theme_template_path;
            }
        }
        
        return false;
    }
    
    /**
     * Load template
     */
    public function load_template($template_key, $slot_data, $atts) {
        $template_path = $this->get_template_path($template_key);
        
        if (!$template_path) {
            // Fallback to default template
            $template_path = $this->get_template_path($this->default_template);
        }
        
        if ($template_path && file_exists($template_path)) {
            // Make variables available to template
            $slot = $slot_data;
            $settings = get_option('slots_settings', array());
            
            // Include the template
            include $template_path;
        } else {
            // Fallback error message
            echo '<div class="slots-error">' . __('Template not found.', 'slots') . '</div>';
        }
    }
    

    
    /**
     * Validate template key
     */
    public function validate_template_key($template_key) {
        if (empty($template_key)) {
            return $this->default_template;
        }
        
        if ($this->template_exists($template_key)) {
            return $template_key;
        }
        
        return $this->default_template;
    }
}
