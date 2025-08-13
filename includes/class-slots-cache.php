<?php
/**
 * Cache Helper Class for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_Cache {
    
    /**
     * Cache group name
     *
     * @var string
     */
    const CACHE_GROUP = 'slots_cache';
    
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
        // Clear cache on post creation/update/deletion
        add_action('save_post', array($this, 'clear_cache_on_post_change'), 10, 3);
        add_action('deleted_post', array($this, 'clear_cache_on_post_change'), 10, 1);
        add_action('wp_trash_post', array($this, 'clear_cache_on_post_change'), 10, 1);
        
        // Clear cache on post meta changes
        add_action('updated_post_meta', array($this, 'clear_cache_on_meta_change'), 10, 4);
        add_action('added_post_meta', array($this, 'clear_cache_on_meta_change'), 10, 4);
        add_action('deleted_post_meta', array($this, 'clear_cache_on_meta_change'), 10, 4);
    }
    
    /**
     * Get cached data
     *
     * @param string $key Cache key
     * @param mixed $default Default value if cache miss
     * @return mixed Cached data or default value
     */
    public static function get($key, $default = false) {
        // Check if caching is enabled
        if (!self::is_enabled()) {
            return $default;
        }
        
        $cache_key = self::get_cache_key($key);
        $cached_data = wp_cache_get($cache_key, self::CACHE_GROUP);
        
        if (false === $cached_data) {
            return $default;
        }
        
        return $cached_data;
    }
    
    /**
     * Set cached data
     *
     * @param string $key Cache key
     * @param mixed $data Data to cache
     * @param int $expiration Expiration time in seconds (optional)
     * @return bool True on success, false on failure
     */
    public static function set($key, $data, $expiration = null) {
        // Check if caching is enabled
        if (!self::is_enabled()) {
            return false;
        }
        
        if (null === $expiration) {
            $expiration = self::get_expiration_time();
        }
        
        $cache_key = self::get_cache_key($key);
        return wp_cache_set($cache_key, $data, self::CACHE_GROUP, $expiration);
    }
    
    /**
     * Delete cached data
     *
     * @param string $key Cache key
     * @return bool True on success, false on failure
     */
    public static function delete($key) {
        $cache_key = self::get_cache_key($key);
        return wp_cache_delete($cache_key, self::CACHE_GROUP);
    }
    
    /**
     * Clear all slots cache
     *
     * @return bool True on success, false on failure
     */
    public static function clear_all_cache() {
        return wp_cache_flush_group(self::CACHE_GROUP);
    }
    
    /**
     * Get cache key with prefix
     *
     * @param string $key Base cache key
     * @return string Full cache key
     */
    private static function get_cache_key($key) {
        return 'slots_' . $key;
    }
    
    /**
     * Check if caching is enabled
     *
     * @return bool True if caching is enabled
     */
    public static function is_enabled() {
        return defined('SLOTS_CACHE_ENABLED') && SLOTS_CACHE_ENABLED;
    }
    
    /**
     * Get cache expiration time
     *
     * @return int Expiration time in seconds
     */
    public static function get_expiration_time() {
        return defined('SLOTS_CACHE_EXPIRATION') ? SLOTS_CACHE_EXPIRATION : 3600;
    }
    
    /**
     * Clear cache when post changes
     *
     * @param int $post_id Post ID
     * @param WP_Post $post Post object
     * @param bool $update Whether this is an update
     */
    public function clear_cache_on_post_change($post_id, $post = null, $update = null) {
        // Only process slot post type
        if (!$post || 'slot' !== $post->post_type) {
            return;
        }
        
        // Clear all cache when any slot changes
        self::clear_all_cache();
    }
    
    /**
     * Clear cache when post meta changes
     *
     * @param int $meta_id Meta ID
     * @param int $post_id Post ID
     * @param string $meta_key Meta key
     * @param mixed $meta_value Meta value
     */
    public function clear_cache_on_meta_change($meta_id, $post_id, $meta_key, $meta_value) {
        // Get post type
        $post_type = get_post_type($post_id);
        
        // Only process slot post type
        if ('slot' !== $post_type) {
            return;
        }
        
        // Clear all cache when slot meta changes
        self::clear_all_cache();
    }
}
