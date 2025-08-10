<?php
/**
 * Slot Detail Compact Template
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
$default_image = SLOTS_PLUGIN_URL . 'assets/images/default-slot.png';
$slot_image = !empty($slot['thumbnail']) ? $slot['thumbnail'] : $default_image;

// Format RTP
$rtp_display = !empty($slot['rtp']) ? number_format($slot['rtp'], 1) . '%' : 'N/A';

// Show/hide sections based on shortcode attributes
$show_rating = filter_var($atts['show_rating'], FILTER_VALIDATE_BOOLEAN);
$show_provider = filter_var($atts['show_provider'], FILTER_VALIDATE_BOOLEAN);
$show_rtp = filter_var($atts['show_rtp'], FILTER_VALIDATE_BOOLEAN);
?>

<div class="slot-detail-compact<?php echo !empty($atts['class']) ? ' ' . esc_attr($atts['class']) : ''; ?><?php echo $theme_class; ?>">
    
    <!-- Compact Layout -->
    <div class="slot-compact-content">
        <div class="slot-compact-image">
            <img src="<?php echo esc_url($slot_image); ?>" 
                 alt="<?php echo esc_attr($slot['title']); ?>" 
                 class="slot-compact-thumbnail">
        </div>
        
        <div class="slot-compact-details">
            <h4 class="slot-compact-title"><?php echo esc_html($slot['title']); ?></h4>
            
            <?php if ($show_provider && !empty($slot['provider_name'])): ?>
            <div class="slot-compact-provider">
                <small><?php echo esc_html($slot['provider_name']); ?></small>
            </div>
            <?php endif; ?>
            
            <?php if ($show_rating && !empty($slot['star_rating'])): ?>
            <div class="slot-compact-rating">
                <span class="rating-stars">★★★★★</span>
                <span class="rating-text"><?php echo number_format($slot['star_rating'], 1); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($show_rtp && !empty($slot['rtp'])): ?>
            <div class="slot-compact-rtp">
                <span class="rtp-text"><?php echo $rtp_display; ?> RTP</span>
            </div>
            <?php endif; ?>
            
            <a href="<?php echo esc_url($slot['permalink']); ?>" class="slot-compact-link">
                <?php _e('View Details', 'slots'); ?> →
            </a>
        </div>
    </div>
    
</div>

<style>
.slot-detail-compact {
    background: #ffffff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 16px;
    margin: 16px 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 400px;
}

.slot-compact-content {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.slot-compact-image {
    flex-shrink: 0;
}

.slot-compact-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.slot-compact-details {
    flex: 1;
    min-width: 0;
}

.slot-compact-title {
    margin: 0 0 6px 0;
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.slot-compact-provider {
    margin-bottom: 8px;
}

.slot-compact-provider small {
    background: #f3f4f6;
    color: #6b7280;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.slot-compact-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}

.rating-stars {
    color: #fbbf24;
    font-size: 12px;
    letter-spacing: -1px;
}

.rating-text {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
}

.slot-compact-rtp {
    margin-bottom: 12px;
}

.rtp-text {
    background: #ecfdf5;
    color: #059669;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.slot-compact-link {
    display: inline-block;
    color: #3b82f6;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.2s ease;
    border-bottom: 1px solid transparent;
}

.slot-compact-link:hover {
    color: #1d4ed8;
    border-bottom-color: #1d4ed8;
    text-decoration: none;
}

/* Hover Effects */
.slot-detail-compact:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Responsive Design */
@media (max-width: 480px) {
    .slot-compact-content {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .slot-compact-thumbnail {
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }
    
    .slot-compact-title {
        white-space: normal;
        line-height: 1.4;
    }
}
</style>
