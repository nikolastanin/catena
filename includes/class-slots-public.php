<?php
/**
 * Public functionality for Slots Plugin
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
        add_action('wp_ajax_slots_action', array($this, 'ajax_handler'));
        add_action('wp_ajax_nopriv_slots_action', array($this, 'ajax_handler'));
        add_shortcode('slots', array($this, 'shortcode_callback'));
    }
    
    /**
     * AJAX handler
     */
    public function ajax_handler() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'slots_nonce')) {
            wp_die('Security check failed');
        }
        
        $action = sanitize_text_field($_POST['action_type']);
        
        switch ($action) {
            case 'get_slots':
                $this->get_slots();
                break;
            default:
                wp_send_json_error('Invalid action');
        }
    }
    
    /**
     * Get available slots
     */
    private function get_slots() {
        // Example data - replace with your actual logic
        $slots = array(
            array('id' => 1, 'time' => '09:00', 'available' => true),
            array('id' => 2, 'time' => '10:00', 'available' => true),
            array('id' => 3, 'time' => '11:00', 'available' => false),
            array('id' => 4, 'time' => '12:00', 'available' => true),
        );
        
        wp_send_json_success($slots);
    }
    

    
    /**
     * Shortcode callback
     */
    public function shortcode_callback($atts) {
        $atts = shortcode_atts(array(
            'type' => 'list',
            'limit' => 10
        ), $atts);
        
        ob_start();
        include SLOTS_PLUGIN_DIR . 'public/shortcode-template.php';
        return ob_get_clean();
    }
    
    
    /**
     * Get available slots for display
     */
    public function get_available_slots() {
        // This would typically query your database
        // For now, returning example data
        return array(
            array('id' => 1, 'time' => '09:00'),
            array('id' => 2, 'time' => '10:00'),
            array('id' => 3, 'time' => '11:00'),
            array('id' => 4, 'time' => '12:00'),
        );
    }
}
