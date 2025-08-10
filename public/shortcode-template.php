<?php
/**
 * Shortcode template for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$slots = $this->get_available_slots();
?>

<div class="slots-container" data-type="<?php echo esc_attr($atts['type']); ?>">
    <h3 class="slots-title"><?php _e('Available Slots', 'slots'); ?></h3>
    
    <?php if (empty($slots)): ?>
        <p class="slots-no-slots"><?php _e('No slots available at the moment.', 'slots'); ?></p>
    <?php else: ?>
        <div class="slots-list">
            <?php foreach ($slots as $slot): ?>
                <div class="slot-item" data-slot-id="<?php echo esc_attr($slot['id']); ?>">
                    <div class="slot-time">
                        <span class="time-label"><?php _e('Time:', 'slots'); ?></span>
                        <span class="time-value"><?php echo esc_html($slot['time']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="slots-pagination">
            <button class="load-more-slots" data-page="1">
                <?php _e('Load More Slots', 'slots'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>



<div id="slots-notification" class="slots-notification" style="display: none;"></div>
