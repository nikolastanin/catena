<?php
/**
 * Custom Post Types and Taxonomies for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Post_Types {
    
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
        add_action('init', array($this, 'register_post_types'));
    }
    
    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Register Slot post type
        register_post_type('slot', array(
            'labels' => array(
                'name' => __('Slots', 'slots'),
                'singular_name' => __('Slot', 'slots'),
                'menu_name' => __('Slots', 'slots'),
                'add_new' => __('Add New Slot', 'slots'),
                'add_new_item' => __('Add New Slot', 'slots'),
                'edit_item' => __('Edit Slot', 'slots'),
                'new_item' => __('New Slot', 'slots'),
                'view_item' => __('View Slot', 'slots'),
                'search_items' => __('Search Slots', 'slots'),
                'not_found' => __('No slots found', 'slots'),
                'not_found_in_trash' => __('No slots found in trash', 'slots'),
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'slot'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-games',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest' => true,
        ));
    }
    

    

}
