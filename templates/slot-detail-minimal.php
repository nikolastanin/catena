<?php
/**
 * Slot Detail Minimal Template
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get settings for customization
$settings = get_option('slots_settings', array());

// Get current theme
$themes = new Slots_Themes();
$current_theme = $themes->get_current_theme();
$theme_class = $themes->get_theme_class($current_theme);
$theme_class = !empty($theme_class) ? ' ' . esc_attr($theme_class) : '';

// Default image if no thumbnail
$default_image = SLOTS_PLUGIN_URL . 'assets/images/default-slot.png';
$slot_image = !empty($slot['thumbnail']) ? $slot['thumbnail'] : $default_image;

// Format RTP
$rtp_display = !empty($slot['rtp']) ? number_format($slot['rtp'], 1) . '%' : 'N/A';

// Show/hide sections based on shortcode attributes
$show_rating = filter_var($atts['show_rating'], FILTER_VALIDATE_BOOLEAN);
$show_provider = filter_var($atts['show_provider'], FILTER_VALIDATE_BOOLEAN);
$show_rtp = filter_var($atts['show_rtp'], FILTER_VALIDATE_BOOLEAN);
$show_wager = filter_var($atts['show_wager'], FILTER_VALIDATE_BOOLEAN);
?>

<div class="bg-white border border-gray-200 rounded-xl p-6 my-5 shadow-lg font-sans<?php echo !empty($atts['class']) ? ' ' . esc_attr($atts['class']) : ''; ?><?php echo $theme_class; ?>">
    
    <!-- Header with Image and Basic Info -->
    <div class="flex items-center mb-5 gap-4 md:gap-6">
        <div class="flex-shrink-0">
            <img src="<?php echo esc_url($slot_image); ?>" 
                 alt="<?php echo esc_attr($slot['title']); ?>" 
                 class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg shadow-md">
        </div>
        
        <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-2 leading-tight"><?php echo esc_html($slot['title']); ?></h2>
            
            <?php if ($show_provider && !empty($slot['provider_name'])): ?>
            <div class="text-gray-500 text-sm">
                <small class="bg-gray-100 px-2 py-1 rounded-md text-xs font-medium"><?php echo esc_html($slot['provider_name']); ?></small>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="flex flex-col md:flex-row gap-4 md:gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
        <?php if ($show_rating && !empty($slot['star_rating'])): ?>
        <div class="text-center flex-1">
            <span class="block text-xs text-gray-500 uppercase font-medium tracking-wide mb-1"><?php _e('Rating', 'slots'); ?></span>
            <span class="block text-lg font-bold text-gray-900"><?php echo number_format($slot['star_rating'], 1); ?>/5</span>
        </div>
        <?php endif; ?>
        
        <?php if ($show_rtp && !empty($slot['rtp'])): ?>
        <div class="text-center flex-1">
            <span class="block text-xs text-gray-500 uppercase font-medium tracking-wide mb-1"><?php _e('RTP', 'slots'); ?></span>
            <span class="block text-lg font-bold text-gray-900"><?php echo $rtp_display; ?></span>
        </div>
        <?php endif; ?>
        
        <?php if ($show_wager && !empty($slot['min_wager']) && !empty($slot['max_wager'])): ?>
        <div class="text-center flex-1">
            <span class="block text-xs text-gray-500 uppercase font-medium tracking-wide mb-1"><?php _e('Wager Range', 'slots'); ?></span>
            <span class="block text-lg font-bold text-gray-900">$<?php echo number_format($slot['min_wager'], 2); ?> - $<?php echo number_format($slot['max_wager'], 2); ?></span>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Primary Action -->
    <div class="text-center">
        <a href="<?php echo esc_url($slot['permalink']); ?>" class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3.5 rounded-lg font-semibold text-base transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 min-w-[140px] no-underline hover:text-white">
            <?php _e('Play Now', 'slots'); ?>
        </a>
    </div>
    
</div>
