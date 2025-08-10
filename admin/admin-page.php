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
.slots-admin-dashboard {
    margin-top: 20px;
}

.slots-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.slots-stat-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.slots-stat-card h3 {
    margin: 0 0 10px 0;
    color: #23282d;
    font-size: 14px;
    font-weight: 600;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #0073aa;
}

.slots-quick-actions {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.action-buttons {
    margin-top: 15px;
}

.action-buttons .button {
    margin-right: 10px;
    margin-bottom: 10px;
}

.slots-recent-activity {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f1;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-content {
    flex: 1;
}

.activity-meta {
    color: #666;
    font-size: 12px;
}
</style>
