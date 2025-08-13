<?php
/**
 * Slots Grid Template
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get settings for customization
$settings = get_option('slots_settings', array());
$custom_class = !empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '';
$show_filters = filter_var($atts['show_filters'], FILTER_VALIDATE_BOOLEAN);
$show_pagination = filter_var($atts['show_pagination'], FILTER_VALIDATE_BOOLEAN);

// Get current theme
$themes = new Slots_Themes();
$current_theme = $themes->get_current_theme();
$theme_class = $themes->get_theme_class($current_theme);
$theme_class = !empty($theme_class) ? ' ' . $theme_class : '';
?>

<div class="slots-container<?php echo $custom_class . $theme_class; ?>" data-limit="<?php echo esc_attr($atts['limit']); ?>" data-sort="<?php echo esc_attr($atts['sort']); ?>">
    
    <?php if ($show_filters && defined('SLOTS_ENABLE_GRID_FILTERS') && SLOTS_ENABLE_GRID_FILTERS): ?>
    <div class="slots-controls">
        <div class="slots-filter">
            <label for="slots-sort"><?php _e('Sort by:', 'slots'); ?></label>
            <select id="slots-sort" class="slots-sort-select">
                <option value="recent" <?php selected($atts['sort'], 'recent'); ?>><?php _e('Most Recent', 'slots'); ?></option>
                <option value="random" <?php selected($atts['sort'], 'random'); ?>><?php _e('Random', 'slots'); ?></option>
            </select>
        </div>
        
        <div class="slots-filter">
            <label for="slots-limit"><?php _e('Show:', 'slots'); ?></label>
            <select id="slots-limit" class="slots-limit-select">
                <option value="1" <?php selected($atts['limit'], 1); ?>>1 <?php _e('Slot', 'slots'); ?></option>
                <option value="3" <?php selected($atts['limit'], 3); ?>>3 <?php _e('Slots', 'slots'); ?></option>
                <option value="6" <?php selected($atts['limit'], 6); ?>>6 <?php _e('Slots', 'slots'); ?></option>
                <option value="9" <?php selected($atts['limit'], 9); ?>>9 <?php _e('Slots', 'slots'); ?></option>
                <option value="12" <?php selected($atts['limit'], 12); ?>>12 <?php _e('Slots', 'slots'); ?></option>
            </select>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (empty($slots)): ?>
        <div class="slots-empty">
            <div class="slots-empty-icon">🎰</div>
            <h3><?php _e('No slots found', 'slots'); ?></h3>
            <p><?php _e('Try adjusting your filters or check back later for new slots.', 'slots'); ?></p>
        </div>
    <?php else: ?>
        <div class="slots-grid" id="slots-grid" data-nonce="<?php echo wp_create_nonce('slots_nonce'); ?>">
            <?php foreach ($slots as $slot): ?>
                <?php echo Slots_Admin::render_slot_card($slot); ?>
            <?php endforeach; ?>
        </div>
        
        <?php if ($show_pagination && defined('SLOTS_ENABLE_PAGINATION') && SLOTS_ENABLE_PAGINATION && count($slots) >= intval($atts['limit'])): ?>
        <div class="slots-pagination">
            <button class="load-more-slots" data-page="1" data-limit="<?php echo esc_attr($atts['limit']); ?>" data-sort="<?php echo esc_attr($atts['sort']); ?>">
                <?php _e('Load More Slots', 'slots'); ?>
            </button>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
