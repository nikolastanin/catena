<?php
/**
 * Admin page template for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="slots-admin-dashboard">
        <div class="slots-stats-grid">
            <div class="slots-stat-card">
                <h3><?php _e('Total Slots', 'slots'); ?></h3>
                <div class="stat-number">
                    <?php echo esc_html(wp_count_posts('slot')->publish); ?>
                </div>
            </div>
            

            

        </div>
        
        <div class="slots-quick-actions">
            <h2><?php _e('Quick Actions', 'slots'); ?></h2>
            <div class="action-buttons">
                <a href="<?php echo admin_url('post-new.php?post_type=slot'); ?>" class="button button-primary">
                    <?php _e('Add New Slot', 'slots'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=slot'); ?>" class="button">
                    <?php _e('Manage Slots', 'slots'); ?>
                </a>

                <a href="<?php echo admin_url('admin.php?page=slots-settings'); ?>" class="button">
                    <?php _e('Settings', 'slots'); ?>
                </a>
            </div>
        </div>
        
        <div class="slots-recent-activity">
            <h2><?php _e('Recent Activity', 'slots'); ?></h2>
            <div class="activity-list">
                <?php
                $recent_slots = get_posts(array(
                    'post_type' => 'slot',
                    'post_status' => 'publish',
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_slots) {
                    foreach ($recent_slots as $slot) {
                        ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <strong><?php echo esc_html($slot->post_title); ?></strong>
                            </div>
                            <div class="activity-meta">
                                <?php echo esc_html(human_time_diff(strtotime($slot->post_date), current_time('timestamp'))); ?> <?php _e('ago', 'slots'); ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>' . __('No slots found.', 'slots') . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>

</style>
