<?php
/**
 * Slot Detail Template
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
$theme_class = !empty($theme_class) ? ' ' . $theme_class : '';

// Default image if no thumbnail
$default_image = SLOTS_PLUGIN_URL . 'assets/images/default-slot.avif';
$slot_image = !empty($slot['thumbnail']) ? $slot['thumbnail'] : $default_image;

// Format RTP
$rtp_display = !empty($slot['rtp']) ? number_format($slot['rtp'], 1) . '%' : 'N/A';

// Format wager range
$wager_display = '';
if (!empty($slot['min_wager']) && !empty($slot['max_wager'])) {
    $wager_display = '$' . number_format($slot['min_wager'], 2) . ' - $' . number_format($slot['max_wager'], 2);
} elseif (!empty($slot['min_wager'])) {
    $wager_display = '$' . number_format($slot['min_wager'], 2) . '+';
} elseif (!empty($slot['max_wager'])) {
    $wager_display = 'Up to $' . number_format($slot['max_wager'], 2);
}

// Generate star rating
$star_rating = Slots_Shortcodes::generate_star_rating($slot['star_rating']);

// Show/hide sections based on shortcode attributes
$show_rating = filter_var($atts['show_rating'], FILTER_VALIDATE_BOOLEAN);
$show_description = filter_var($atts['show_description'], FILTER_VALIDATE_BOOLEAN);
$show_provider = filter_var($atts['show_provider'], FILTER_VALIDATE_BOOLEAN);
$show_rtp = filter_var($atts['show_rtp'], FILTER_VALIDATE_BOOLEAN);
$show_wager = filter_var($atts['show_wager'], FILTER_VALIDATE_BOOLEAN);
?>

<div class="slot-detail-container<?php echo !empty($atts['class']) ? ' ' . esc_attr($atts['class']) : ''; ?><?php echo $theme_class; ?>">
    
    <!-- Header Section -->
    <div class="slot-detail-header">
        <div class="slot-detail-image">
            <img src="<?php echo esc_url($slot_image); ?>" 
                 alt="<?php echo esc_attr($slot['title']); ?>" 
                 class="slot-detail-main-image">
        </div>
        
        <div class="slot-detail-info">
            <h1 class="slot-detail-title"><?php echo esc_html($slot['title']); ?></h1>
            
            <?php if ($show_provider && !empty($slot['provider_name'])): ?>
            <div class="slot-detail-provider">
                <span class="provider-label"><?php _e('Provider:', 'slots'); ?></span>
                <span class="provider-name"><?php echo esc_html($slot['provider_name']); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($show_rating && !empty($slot['star_rating'])): ?>
            <div class="slot-detail-rating">
                <span class="rating-label"><?php _e('Rating:', 'slots'); ?></span>
                <div class="rating-display">
                    <?php echo $star_rating; ?>
                    <span class="rating-value"><?php echo number_format($slot['star_rating'], 1); ?>/5</span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($show_rtp && !empty($slot['rtp'])): ?>
            <div class="slot-detail-rtp">
                <span class="rtp-label"><?php _e('RTP:', 'slots'); ?></span>
                <span class="rtp-value"><?php echo $rtp_display; ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($show_wager && !empty($wager_display)): ?>
            <div class="slot-detail-wager">
                <span class="wager-label"><?php _e('Wager Range:', 'slots'); ?></span>
                <span class="wager-value"><?php echo esc_html($wager_display); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($slot['slot_id'])): ?>
            <div class="slot-detail-id">
                <span class="id-label"><?php _e('Slot ID:', 'slots'); ?></span>
                <span class="id-value"><?php echo esc_html($slot['slot_id']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Content Section -->
    <?php if ($show_description && !empty($slot['content'])): ?>
    <div class="slot-detail-content">
        <h2><?php _e('Description', 'slots'); ?></h2>
        <div class="slot-detail-description">
            <?php echo wpautop($slot['content']); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Excerpt Section -->
    <?php if ($show_description && !empty($slot['excerpt']) && empty($slot['content'])): ?>
    <div class="slot-detail-content">
        <h2><?php _e('Overview', 'slots'); ?></h2>
        <div class="slot-detail-description">
            <?php echo wpautop($slot['excerpt']); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="slot-detail-actions">
        <a href="<?php echo esc_url($slot['permalink']); ?>" class="slot-detail-button primary">
            <?php _e('Play Now', 'slots'); ?>
        </a>
        
        <a href="<?php echo esc_url(home_url('/slots/')); ?>" class="slot-detail-button secondary">
            <?php _e('Browse More Slots', 'slots'); ?>
        </a>
    </div>
    
    <!-- Additional Info -->
    <div class="slot-detail-meta">
        <div class="meta-item">
            <span class="meta-label"><?php _e('Last Updated:', 'slots'); ?></span>
            <span class="meta-value"><?php echo date_i18n(get_option('date_format'), $slot['modified']); ?></span>
        </div>
    </div>
    
</div>
