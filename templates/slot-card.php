<?php
/**
 * Individual Slot Card Template
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get settings for customization
$settings = get_option('slots_settings', array());

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
?>

<div class="slot-card" data-slot-id="<?php echo esc_attr($slot['id']); ?>">
    
    <!-- Slot Image -->
    <div class="slot-card-image-container">
        <img src="<?php echo esc_url($slot_image); ?>" 
             alt="<?php echo esc_attr($slot['title']); ?>" 
             class="slot-card-image"
             loading="lazy">
        
        <?php if (!empty($slot['star_rating'])): ?>
        <div class="slot-card-rating-overlay">
            <?php echo $star_rating; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Slot Content -->
    <div class="slot-card-content">
        
        <!-- Title -->
        <h3 class="slot-card-title">
            <a href="<?php echo esc_url($slot['permalink']); ?>" title="<?php echo esc_attr($slot['title']); ?>">
                <?php echo esc_html($slot['title']); ?>
            </a>
        </h3>
        
        <!-- Provider -->
        <?php if (!empty($slot['provider_name'])): ?>
        <div class="slot-card-provider">
            <?php echo esc_html($slot['provider_name']); ?>
        </div>
        <?php endif; ?>
        
        <!-- Rating and RTP -->
        <div class="slot-card-meta">
            <?php if (!empty($slot['star_rating'])): ?>
            <div class="slot-card-rating">
                <?php echo $star_rating; ?>
                <span class="rating-value"><?php echo number_format($slot['star_rating'], 1); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($slot['rtp'])): ?>
            <div class="slot-card-rtp">
                RTP: <?php echo $rtp_display; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Wager Range -->
        <?php if (!empty($wager_display)): ?>
        <div class="slot-card-wager">
            <span class="wager-label"><?php _e('Wager:', 'slots'); ?></span>
            <span class="wager-value"><?php echo esc_html($wager_display); ?></span>
        </div>
        <?php endif; ?>
        
        <!-- Excerpt -->
        <?php if (!empty($slot['excerpt'])): ?>
        <div class="slot-card-excerpt">
            <?php echo wp_trim_words($slot['excerpt'], 15, '...'); ?>
        </div>
        <?php endif; ?>
        
        <!-- Action Button -->
        <div class="slot-card-actions">
            <a href="<?php echo esc_url($slot['permalink']); ?>" class="slot-card-button">
                <?php _e('More Info', 'slots'); ?>
            </a>
        </div>
        
    </div>
    
</div>
