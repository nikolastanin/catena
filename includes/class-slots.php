<?php
/**
 * Main Slots Plugin Class
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots {
    
    /**
     * Plugin version
     *
     * @var string
     */
    public $version = SLOTS_PLUGIN_VERSION;
    
    /**
     * Plugin instance
     *
     * @var Slots
     */
    private static $instance = null;
    
    /**
     * Admin instance
     *
     * @var Slots_Admin
     */
    private $admin = null;
    
    /**
     * Public instance
     *
     * @var Slots_Public
     */
    private $public = null;
    
    /**
     * Post types instance
     *
     * @var Slots_Post_Types
     */
    private $post_types = null;
    
    /**
     * REST API instance
     *
     * @var Slots_REST_API
     */
    private $rest_api = null;
    
    /**
     * Get plugin instance
     *
     * @return Slots
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_components();
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('slots', false, dirname(SLOTS_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Initialize plugin components
     */
    private function init_components() {
        // Include required files
        require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-admin.php';
        require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-public.php';
        require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-post-types.php';
        require_once SLOTS_PLUGIN_DIR . 'includes/class-slots-rest-api.php';
        
        // Initialize admin
        if (is_admin()) {
            $this->admin = new Slots_Admin();
        }
        
        // Initialize public
        $this->public = new Slots_Public();
        
        // Initialize post types
        $this->post_types = new Slots_Post_Types();
        
        // Initialize REST API
        $this->rest_api = new Slots_REST_API();
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'slots-frontend',
            SLOTS_PLUGIN_URL . 'assets/dist/css/slots-frontend.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            'slots-frontend',
            SLOTS_PLUGIN_URL . 'assets/dist/js/slots-frontend.js',
            array('jquery'),
            $this->version,
            true
        );

        // todo:this is for debug, real setup with vite for production, but didnt have time to get it working
        wp_enqueue_script(
            'tailwindcss',
             'https://cdn.tailwindcss.com',
            [],
            $this->version,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('slots-frontend', 'slots_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('slots_nonce')
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style(
            'slots-admin',
            SLOTS_PLUGIN_URL . 'assets/dist/css/slots-admin.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            'slots-admin',
            SLOTS_PLUGIN_URL . 'assets/dist/js/slots-admin.js',
            array('jquery'),
            $this->version,
            true
        );
        
        // Localize admin script
        wp_localize_script('slots-admin', 'slots_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('slots_admin_nonce')
        ));
    }
}
