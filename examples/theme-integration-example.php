<?php
/**
 * Theme Integration Example
 * 
 * This file shows how to integrate custom templates with the Slots plugin
 * from your theme's functions.php file.
 * 
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom slot templates for your theme
 */
function my_theme_register_slot_templates() {
    // Only register if the slots plugin is active
    if (!function_exists('slots_register_template')) {
        return;
    }
    
    // Register a custom minimal template
    slots_register_template('my-theme-minimal', array(
        'name' => __('My Theme Minimal', 'slots'),
        'file' => 'my-theme-minimal.php',
        'description' => __('A custom minimal layout designed for my theme', 'slots')
    ));
    
    // Register a custom sidebar template
    slots_register_template('my-theme-sidebar', array(
        'name' => __('My Theme Sidebar', 'slots'),
        'file' => 'my-theme-sidebar.php',
        'description' => __('Optimized for sidebar display in my theme', 'slots')
    ));
    
    // Register a custom hero template
    slots_register_template('my-theme-hero', array(
        'name' => __('My Theme Hero', 'slots'),
        'file' => 'my-theme-hero.php',
        'description' => __('Full-width hero layout for featured slots', 'slots')
    ));
}
add_action('init', 'my_theme_register_slot_templates');

/**
 * Example: Create a custom template file
 * 
 * Place this file in your theme directory: your-theme/slots/my-theme-minimal.php
 */
function my_theme_create_template_example() {
    $template_content = '<?php
/**
 * My Theme Minimal Template
 *
 * @package Slots
 */

// Prevent direct access
if (!defined(\'ABSPATH\')) {
    exit;
}

// Variables available:
// $slot - Slot data array
// $atts - Shortcode attributes
// $settings - Plugin settings
?>

<div class="my-theme-slot-minimal">
    <div class="slot-header">
        <div class="slot-image">
            <img src="<?php echo esc_url($slot[\'thumbnail\'] ?: SLOTS_PLUGIN_URL . \'assets/images/default-slot.png\'); ?>" 
                 alt="<?php echo esc_attr($slot[\'title\']); ?>" 
                 class="slot-thumbnail">
        </div>
        
        <div class="slot-info">
            <h3 class="slot-title"><?php echo esc_html($slot[\'title\']); ?></h3>
            
            <?php if (!empty($slot[\'provider_name\'])): ?>
            <div class="slot-provider">
                <small><?php echo esc_html($slot[\'provider_name\']); ?></small>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="slot-stats">
        <?php if (!empty($slot[\'star_rating\'])): ?>
        <div class="stat-item">
            <span class="stat-label">Rating</span>
            <span class="stat-value"><?php echo number_format($slot[\'star_rating\'], 1); ?>/5</span>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($slot[\'rtp\'])): ?>
        <div class="stat-item">
            <span class="stat-label">RTP</span>
            <span class="stat-value"><?php echo number_format($slot[\'rtp\'], 1); ?>%</span>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="slot-action">
        <a href="<?php echo esc_url($slot[\'permalink\']); ?>" class="play-button">
            Play Now
        </a>
    </div>
</div>';

    // This is just an example - in practice, you would create the actual file
    // in your theme directory
    return $template_content;
}

/**
 * Example: Add custom CSS for your templates
 */
function my_theme_slot_template_styles() {
    if (!function_exists('slots_template_exists')) {
        return;
    }
    
    // Only add CSS if we\'re using our custom templates
    if (slots_template_exists('my-theme-minimal') || 
        slots_template_exists('my-theme-sidebar') || 
        slots_template_exists('my-theme-hero')) {
        
        echo '<style>
        .my-theme-slot-minimal {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .my-theme-slot-minimal .slot-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .my-theme-slot-minimal .slot-image {
            margin-right: 15px;
        }
        
        .my-theme-slot-minimal .slot-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .my-theme-slot-minimal .slot-title {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .my-theme-slot-minimal .slot-provider {
            color: #666;
        }
        
        .my-theme-slot-minimal .slot-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .my-theme-slot-minimal .stat-item {
            text-align: center;
        }
        
        .my-theme-slot-minimal .stat-label {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .my-theme-slot-minimal .stat-value {
            display: block;
            font-weight: bold;
            color: #333;
        }
        
        .my-theme-slot-minimal .play-button {
            display: inline-block;
            background: #0073aa;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }
        
        .my-theme-slot-minimal .play-button:hover {
            background: #005a87;
            color: white;
        }
        </style>';
    }
}
add_action('wp_head', 'my_theme_slot_template_styles');

/**
 * Example: Add custom shortcode attributes
 */
function my_theme_slot_shortcode_attributes($atts, $content, $tag) {
    // Add custom attributes for your templates
    if ($tag === 'slot_detail') {
        $atts['my_theme_style'] = isset($atts['my_theme_style']) ? $atts['my_theme_style'] : 'default';
        $atts['show_theme_branding'] = isset($atts['show_theme_branding']) ? $atts['show_theme_branding'] : 'true';
    }
    
    return $atts;
}
add_filter('shortcode_atts_slot_detail', 'my_theme_slot_shortcode_attributes', 10, 3);

/**
 * Example: Usage in your theme
 * 
 * You can now use these custom templates in your theme:
 * 
 * [slot_detail id="123" template="my-theme-minimal"]
 * [slot_detail id="123" template="my-theme-sidebar"]
 * [slot_detail id="123" template="my-theme-hero"]
 * 
 * Or in PHP:
 * 
 * echo do_shortcode('[slot_detail id="123" template="my-theme-minimal"]');
 */
