<?php
/**
 * Slots REST API Class
 *
 * @package Slots
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Slots_REST_API {

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
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register REST routes
     */
    public function register_routes() {
        register_rest_route('slots/v1', '/slots', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_slots'),
                'permission_callback' => array($this, 'get_slots_permissions_check'),
                'args' => array(
                    'slot_id' => array(
                        'default' => '',
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                    'provider' => array(
                        'default' => '',
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                    'min_rating' => array(
                        'default' => 0,
                        'sanitize_callback' => function($param) {
                            return floatval($param);
                        },
                        'validate_callback' => function($param) {
                            return is_numeric($param) && $param >= 0 && $param <= 5;
                        }
                    ),
                    'max_rating' => array(
                        'default' => 5,
                        'sanitize_callback' => function($param) {
                            return floatval($param);
                        },
                        'validate_callback' => function($param) {
                            return is_numeric($param) && $param >= 0 && $param <= 5;
                        }
                    ),
                    'orderby' => array(
                        'default' => 'date',
                        'sanitize_callback' => 'sanitize_text_field',
                        'enum' => array('date', 'title', 'rating', 'provider', 'rtp')
                    ),
                    'order' => array(
                        'default' => 'DESC',
                        'sanitize_callback' => 'sanitize_text_field',
                        'enum' => array('ASC', 'DESC')
                    )
                )
            )
        ));

        register_rest_route('slots/v1', '/slots/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_slot'),
                'permission_callback' => array($this, 'get_slot_permissions_check'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        ));
    }

    /**
     * Check permissions for getting slots list
     */
    public function get_slots_permissions_check() {
        return true; // Public endpoint
    }

    /**
     * Check permissions for getting single slot
     */
    public function get_slot_permissions_check() {
        return true; // Public endpoint
    }

    /**
     * Get all slots
     */
    public function get_slots($request) {
        $slot_id = $request->get_param('slot_id');
        $provider = $request->get_param('provider');
        $min_rating = $request->get_param('min_rating');
        $max_rating = $request->get_param('max_rating');
        $orderby = $request->get_param('orderby');
        $order = $request->get_param('order');

        // Build query args
        $args = array(
            'post_type' => 'slot',
            'post_status' => 'publish',
            'posts_per_page' => -1, // Return all slots
            'orderby' => $orderby,
            'order' => $order
        );

        // Add meta query for filtering
        $meta_query = array();

        if (!empty($slot_id)) {
            $meta_query[] = array(
                'key' => '_slots_slot_id',
                'value' => $slot_id,
                'compare' => '='
            );
        }

        if (!empty($provider)) {
            $meta_query[] = array(
                'key' => '_slots_provider_name',
                'value' => $provider,
                'compare' => 'LIKE'
            );
        }

        if ($min_rating > 0 || $max_rating < 5) {
            $meta_query[] = array(
                'key' => '_slots_star_rating',
                'value' => array($min_rating, $max_rating),
                'type' => 'DECIMAL',
                'compare' => 'BETWEEN'
            );
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        // Handle custom ordering
        if ($orderby === 'rating') {
            $args['meta_key'] = '_slots_star_rating';
            $args['orderby'] = 'meta_value_num';
        } elseif ($orderby === 'provider') {
            $args['meta_key'] = '_slots_provider_name';
            $args['orderby'] = 'meta_value';
        } elseif ($orderby === 'rtp') {
            $args['meta_key'] = '_slots_rtp';
            $args['orderby'] = 'meta_value_num';
        }

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return new WP_REST_Response(array(
                'success' => true,
                'data' => array(),
                'total' => 0
            ), 200);
        }

        $slots = array();

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            $slots[] = $this->format_slot_data($post_id);
        }

        wp_reset_postdata();

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $slots,
            'total' => $query->found_posts
        ), 200);
    }

    /**
     * Get single slot
     */
    public function get_slot($request) {
        $slot_id = $request->get_param('id');

        $post = get_post($slot_id);

        if (!$post || $post->post_type !== 'slot') {
            return new WP_Error(
                'slot_not_found',
                __('Slot not found', 'slots'),
                array('status' => 404)
            );
        }

        $slot_data = $this->format_slot_data($slot_id);

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $slot_data
        ), 200);
    }

    /**
     * Format slot data for API response
     */
    private function format_slot_data($post_id) {
        $post = get_post($post_id);

        // Get featured image
        $featured_image = null;
        if (has_post_thumbnail($post_id)) {
            $image_id = get_post_thumbnail_id($post_id);
            $image_src = wp_get_attachment_image_src($image_id, 'full');
            $featured_image = array(
                'id' => $image_id,
                'url' => $image_src[0],
                'width' => $image_src[1],
                'height' => $image_src[2],
                'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true)
            );
        }

        // Get custom fields
        $slot_id = get_post_meta($post_id, '_slots_slot_id', true);
        $star_rating = get_post_meta($post_id, '_slots_star_rating', true);
        $provider_name = get_post_meta($post_id, '_slots_provider_name', true);
        $rtp = get_post_meta($post_id, '_slots_rtp', true);
        $min_wager = get_post_meta($post_id, '_slots_min_wager', true);
        $max_wager = get_post_meta($post_id, '_slots_max_wager', true);

        return array(
            'id' => $post_id,
            'slot_id' => $slot_id ?: null,
            'title' => get_the_title($post_id),
            'description' => get_the_excerpt($post_id),
            'content' => get_the_content(null, false, $post_id),
            'slug' => $post->post_name,
            'date' => get_the_date('c', $post_id),
            'modified' => get_the_modified_date('c', $post_id),
            'status' => $post->post_status,
            'featured_image' => $featured_image,
            'meta' => array(
                'star_rating' => $star_rating ? floatval($star_rating) : null,
                'provider_name' => $provider_name ?: null,
                'rtp' => $rtp ? floatval($rtp) : null,
                'min_wager' => $min_wager ? floatval($min_wager) : null,
                'max_wager' => $max_wager ? floatval($max_wager) : null
            ),
            'links' => array(
                'self' => rest_url("slots/v1/slots/{$post_id}"),
                'collection' => rest_url('slots/v1/slots')
            )
        );
    }
}
