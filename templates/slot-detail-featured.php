<?php
/**
 * Slot Detail Featured Template
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
$show_provider = filter_var($atts['show_provider'], FILTER_VALIDATE_BOOLEAN);
$show_rtp = filter_var($atts['show_rtp'], FILTER_VALIDATE_BOOLEAN);
$show_wager = filter_var($atts['show_wager'], FILTER_VALIDATE_BOOLEAN);
?>

<div class="slot-detail-featured<?php echo !empty($atts['class']) ? ' ' . esc_attr($atts['class']) : ''; ?><?php echo $theme_class; ?>">
    
    <!-- Hero Section -->
    <div class="slot-featured-hero">
        <div class="slot-featured-image">
            <img src="<?php echo esc_url($slot_image); ?>" 
                 alt="<?php echo esc_attr($slot['title']); ?>" 
                 class="slot-featured-main-image">
            
            <?php if ($show_rating && !empty($slot['star_rating'])): ?>
            <div class="slot-featured-rating-badge">
                <div class="rating-stars"><?php echo $star_rating; ?></div>
                <span class="rating-value"><?php echo number_format($slot['star_rating'], 1); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="slot-featured-content">
            <h1 class="slot-featured-title"><?php echo esc_html($slot['title']); ?></h1>
            
            <?php if ($show_provider && !empty($slot['provider_name'])): ?>
            <div class="slot-featured-provider">
                <span class="provider-badge"><?php echo esc_html($slot['provider_name']); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="slot-featured-highlights">
                <?php if ($show_rtp && !empty($slot['rtp'])): ?>
                <div class="highlight-item rtp-highlight">
                    <span class="highlight-icon">📊</span>
                    <span class="highlight-label"><?php _e('RTP', 'slots'); ?></span>
                    <span class="highlight-value"><?php echo $rtp_display; ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($show_wager && !empty($wager_display)): ?>
                <div class="highlight-item wager-highlight">
                    <span class="highlight-icon">💰</span>
                    <span class="highlight-label"><?php _e('Bet Range', 'slots'); ?></span>
                    <span class="highlight-value"><?php echo esc_html($wager_display); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Featured Action Section -->
    <div class="slot-featured-actions">
        <div class="action-primary">
            <a href="<?php echo esc_url($slot['permalink']); ?>" class="slot-featured-button primary">
                <span class="button-icon">🎮</span>
                <?php _e('Play Now', 'slots'); ?>
            </a>
        </div>
        
        <div class="action-secondary">
            <a href="<?php echo esc_url(home_url('/slots/')); ?>" class="slot-featured-button secondary">
                <?php _e('Browse More Slots', 'slots'); ?>
            </a>
        </div>
    </div>
    
    <!-- Quick Info -->
    <div class="slot-featured-quick-info">
        <?php if (!empty($slot['slot_id'])): ?>
        <div class="quick-info-item">
            <span class="info-label"><?php _e('Slot ID:', 'slots'); ?></span>
            <span class="info-value"><?php echo esc_html($slot['slot_id']); ?></span>
        </div>
        <?php endif; ?>
        
        <div class="quick-info-item">
            <span class="info-label"><?php _e('Updated:', 'slots'); ?></span>
            <span class="info-value"><?php echo date_i18n(get_option('date_format'), $slot['modified']); ?></span>
        </div>
    </div>
    
</div>

<style>
.slot-detail-featured {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 32px;
    margin: 24px 0;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: white;
    position: relative;
    overflow: hidden;
}

.slot-detail-featured::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    z-index: 1;
}

.slot-featured-hero {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 32px;
    align-items: center;
    position: relative;
    z-index: 2;
    margin-bottom: 32px;
}

.slot-featured-image {
    position: relative;
    text-align: center;
}

.slot-featured-main-image {
    width: 280px;
    height: 280px;
    object-fit: cover;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    border: 4px solid rgba(255, 255, 255, 0.2);
}

.slot-featured-rating-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, 0.95);
    color: #1a1a1a;
    padding: 8px 12px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.rating-stars {
    color: #fbbf24;
    font-size: 14px;
}

.rating-value {
    font-size: 14px;
    font-weight: 700;
}

.slot-featured-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.slot-featured-title {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.slot-featured-provider {
    display: inline-block;
}

.provider-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.slot-featured-highlights {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.highlight-item {
    background: rgba(255, 255, 255, 0.15);
    padding: 16px;
    border-radius: 12px;
    text-align: center;
    min-width: 120px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.highlight-icon {
    display: block;
    font-size: 24px;
    margin-bottom: 8px;
}

.highlight-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    opacity: 0.8;
    margin-bottom: 4px;
}

.highlight-value {
    display: block;
    font-size: 18px;
    font-weight: 700;
}

.rtp-highlight {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
}

.wager-highlight {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.4);
}

.slot-featured-actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 24px;
    position: relative;
    z-index: 2;
}

.action-primary,
.action-secondary {
    flex: 1;
    min-width: 200px;
}

.slot-featured-button {
    display: inline-block;
    width: 100%;
    padding: 16px 24px;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    text-align: center;
    border: none;
    cursor: pointer;
}

.slot-featured-button.primary {
    background: #ffffff;
    color: #667eea;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.slot-featured-button.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3);
    color: #667eea;
    text-decoration: none;
}

.slot-featured-button.secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.slot-featured-button.secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.button-icon {
    margin-right: 8px;
    font-size: 18px;
}

.slot-featured-quick-info {
    display: flex;
    gap: 24px;
    justify-content: center;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}

.quick-info-item {
    background: rgba(255, 255, 255, 0.1);
    padding: 12px 20px;
    border-radius: 8px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
}

.info-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    opacity: 0.8;
    margin-bottom: 4px;
}

.info-value {
    display: block;
    font-size: 14px;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .slot-featured-hero {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 24px;
    }
    
    .slot-featured-main-image {
        width: 240px;
        height: 240px;
    }
    
    .slot-featured-title {
        font-size: 28px;
    }
}

@media (max-width: 768px) {
    .slot-detail-featured {
        padding: 24px;
    }
    
    .slot-featured-highlights {
        flex-direction: column;
        gap: 16px;
    }
    
    .highlight-item {
        min-width: auto;
    }
    
    .slot-featured-actions {
        flex-direction: column;
    }
    
    .action-primary,
    .action-secondary {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .slot-featured-main-image {
        width: 200px;
        height: 200px;
    }
    
    .slot-featured-title {
        font-size: 24px;
    }
    
    .slot-featured-quick-info {
        flex-direction: column;
        gap: 16px;
    }
}
</style>
