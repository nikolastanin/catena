<?php
/**
 * Template Helper Functions for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register a custom template for slot detail shortcode
 *
 * @param string $key Template key (e.g., 'my-custom-template')
 * @param array $template Template configuration array
 * @return bool True on success, false on failure
 */
function slots_register_template($key, $template) {
    if (empty($key) || !is_array($template)) {
        return false;
    }
    
    // Validate required fields
    if (!isset($template['name']) || !isset($template['file'])) {
        return false;
    }
    
    // Add to custom templates filter
    add_filter('slots_custom_templates', function($templates) use ($key, $template) {
        $templates[$key] = $template;
        return $templates;
    });
    
    return true;
}

/**
 * Get available template options for shortcode
 *
 * @return array Array of template options
 */
function slots_get_template_options() {
    $template_manager = new Slots_Template_Manager();
    return $template_manager->get_template_options();
}

/**
 * Check if a template exists
 *
 * @param string $template_key Template key to check
 * @return bool True if template exists, false otherwise
 */
function slots_template_exists($template_key) {
    $template_manager = new Slots_Template_Manager();
    return $template_manager->template_exists($template_key);
}

/**
 * Get template information
 *
 * @param string $template_key Template key
 * @return array|false Template information or false if not found
 */
function slots_get_template($template_key) {
    $template_manager = new Slots_Template_Manager();
    return $template_manager->get_template($template_key);
}

/**
 * Get template file path
 *
 * @param string $template_key Template key
 * @return string|false Template file path or false if not found
 */
function slots_get_template_path($template_key) {
    $template_manager = new Slots_Template_Manager();
    return $template_manager->get_template_path($template_key);
}

/**
 * Load a template manually
 *
 * @param string $template_key Template key
 * @param array $slot_data Slot data array
 * @param array $atts Shortcode attributes
 */
function slots_load_template($template_key, $slot_data, $atts = array()) {
    $template_manager = new Slots_Template_Manager();
    $template_manager->load_template($template_key, $slot_data, $atts);
}

/**
 * Example usage function - shows how to register a custom template
 */
function slots_example_custom_template() {
    // Example of registering a custom template
    slots_register_template('my-custom-layout', array(
        'name' => __('My Custom Layout', 'slots'),
        'file' => 'my-custom-slot-template.php',
        'description' => __('A custom layout for my theme', 'slots')
    ));
}

/**
 * Get template usage examples
 *
 * @return array Array of usage examples
 */
function slots_get_template_usage_examples() {
    return array(
        'default' => '[slot_detail id="123"]',
        'minimal' => '[slot_detail id="123" template="minimal"]',
        'compact' => '[slot_detail id="123" template="compact"]',
        'featured' => '[slot_detail id="123" template="featured"]',
        'custom' => '[slot_detail id="123" template="my-custom-layout"]',
        'with_attributes' => '[slot_detail id="123" template="minimal" show_rating="false" show_provider="true"]'
    );
}

/**
 * Display template usage examples
 */
function slots_display_template_examples() {
    $examples = slots_get_template_usage_examples();
    
    echo '<div class="slots-template-examples">';
    echo '<h3>' . __('Template Usage Examples', 'slots') . '</h3>';
    echo '<ul>';
    
    foreach ($examples as $template => $example) {
        echo '<li><strong>' . esc_html($template) . ':</strong> <code>' . esc_html($example) . '</code></li>';
    }
    
    echo '</ul>';
    echo '</div>';
}
