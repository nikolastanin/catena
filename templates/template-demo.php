<?php
/**
 * Template Demo Page
 * 
 * This page demonstrates all available template variations for the slot detail shortcode
 * 
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get a sample slot for demonstration
$sample_slot = get_posts(array(
    'post_type' => 'slot',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'rand'
));

if (empty($sample_slot)) {
    echo '<div class="notice notice-warning"><p>' . __('No slots found. Please create some slots first.', 'slots') . '</p></div>';
    return;
}

$slot_id = $sample_slot[0]->ID;
?>

<div class="slots-template-demo">
    <h1><?php _e('Slot Detail Template Variations', 'slots'); ?></h1>
    <p><?php _e('This page demonstrates all available template variations for the slot detail shortcode.', 'slots'); ?></p>
    
    <div class="template-variations">
        
        <!-- Default Template -->
        <div class="template-section">
            <h2><?php _e('Default Template', 'slots'); ?></h2>
            <p><?php _e('Standard layout with full information display.', 'slots'); ?></p>
            <div class="template-preview">
                <?php echo do_shortcode('[slot_detail id="' . $slot_id . '" template="default"]'); ?>
            </div>
            <div class="template-code">
                <code>[slot_detail id="<?php echo $slot_id; ?>" template="default"]</code>
            </div>
        </div>
        
        <!-- Minimal Template -->
        <div class="template-section">
            <h2><?php _e('Minimal Template', 'slots'); ?></h2>
            <p><?php _e('Clean, minimal layout with essential information only.', 'slots'); ?></p>
            <div class="template-preview">
                <?php echo do_shortcode('[slot_detail id="' . $slot_id . '" template="minimal"]'); ?>
            </div>
            <div class="template-code">
                <code>[slot_detail id="<?php echo $slot_id; ?>" template="minimal"]</code>
            </div>
        </div>
        
        <!-- Compact Template -->
        <div class="template-section">
            <h2><?php _e('Compact Template', 'slots'); ?></h2>
            <p><?php _e('Space-efficient layout for sidebar or small areas.', 'slots'); ?></p>
            <div class="template-preview">
                <?php echo do_shortcode('[slot_detail id="' . $slot_id . '" template="compact"]'); ?>
            </div>
            <div class="template-code">
                <code>[slot_detail id="<?php echo $slot_id; ?>" template="compact"]</code>
            </div>
        </div>
        
        <!-- Featured Template -->
        <div class="template-section">
            <h2><?php _e('Featured Template', 'slots'); ?></h2>
            <p><?php _e('Highlighted layout with prominent call-to-action.', 'slots'); ?></p>
            <div class="template-preview">
                <?php echo do_shortcode('[slot_detail id="' . $slot_id . '" template="featured"]'); ?>
            </div>
            <div class="template-code">
                <code>[slot_detail id="<?php echo $slot_id; ?>" template="featured"]</code>
            </div>
        </div>
        
        <!-- Custom Attributes Examples -->
        <div class="template-section">
            <h2><?php _e('Custom Attributes Examples', 'slots'); ?></h2>
            <p><?php _e('Examples of using different attributes with templates.', 'slots'); ?></p>
            
            <div class="attribute-examples">
                <div class="example-item">
                    <h4><?php _e('Hide Rating and Description', 'slots'); ?></h4>
                    <div class="template-preview">
                        <?php echo do_shortcode('[slot_detail id="' . $slot_id . '" template="minimal" show_rating="false" show_description="false"]'); ?>
                    </div>
                    <div class="template-code">
                        <code>[slot_detail id="<?php echo $slot_id; ?>" template="minimal" show_rating="false" show_description="false"]</code>
                    </div>
                </div>
                
                <div class="example-item">
                    <h4><?php _e('Show Only Provider and RTP', 'slots'); ?></h4>
                    <div class="template-preview">
                        <?php echo do_shortcode('[slot_detail id="' . $slot_id . '" template="compact" show_rating="false" show_description="false" show_wager="false"]'); ?>
                    </div>
                    <div class="template-code">
                        <code>[slot_detail id="<?php echo $slot_id; ?>" template="compact" show_rating="false" show_description="false" show_wager="false"]</code>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Usage Instructions -->
        <div class="template-section">
            <h2><?php _e('How to Use', 'slots'); ?></h2>
            <div class="usage-instructions">
                <h3><?php _e('Basic Usage', 'slots'); ?></h3>
                <p><?php _e('Use the template parameter to specify which layout you want:', 'slots'); ?></p>
                <ul>
                    <li><code>[slot_detail id="123"]</code> - <?php _e('Uses default template', 'slots'); ?></li>
                    <li><code>[slot_detail id="123" template="minimal"]</code> - <?php _e('Uses minimal template', 'slots'); ?></li>
                    <li><code>[slot_detail id="123" template="compact"]</code> - <?php _e('Uses compact template', 'slots'); ?></li>
                    <li><code>[slot_detail id="123" template="featured"]</code> - <?php _e('Uses featured template', 'slots'); ?></li>
                </ul>
                
                <h3><?php _e('Available Attributes', 'slots'); ?></h3>
                <ul>
                    <li><code>template</code> - <?php _e('Template variation to use', 'slots'); ?></li>
                    <li><code>show_rating</code> - <?php _e('Show/hide star rating (true/false)', 'slots'); ?></li>
                    <li><code>show_description</code> - <?php _e('Show/hide description (true/false)', 'slots'); ?></li>
                    <li><code>show_provider</code> - <?php _e('Show/hide provider name (true/false)', 'slots'); ?></li>
                    <li><code>show_rtp</code> - <?php _e('Show/hide RTP information (true/false)', 'slots'); ?></li>
                    <li><code>show_wager</code> - <?php _e('Show/hide wager range (true/false)', 'slots'); ?></li>
                    <li><code>class</code> - <?php _e('Additional CSS classes', 'slots'); ?></li>
                </ul>
                
                <h3><?php _e('Creating Custom Templates', 'slots'); ?></h3>
                <p><?php _e('You can create custom templates by placing them in your theme directory under /slots/ or by using the slots_register_template() function.', 'slots'); ?></p>
            </div>
        </div>
        
    </div>
</div>

<style>
.slots-template-demo {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.slots-template-demo h1 {
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 10px;
    font-size: 2.5em;
    font-weight: 700;
}

.slots-template-demo > p {
    text-align: center;
    color: #6b7280;
    font-size: 1.1em;
    margin-bottom: 40px;
}

.template-section {
    margin-bottom: 40px;
    padding: 24px;
    border: 1px solid #e1e5e9;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.template-section:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.template-section h2 {
    color: #1a1a1a;
    border-bottom: 3px solid #3b82f6;
    padding-bottom: 12px;
    margin-bottom: 20px;
    font-size: 1.8em;
    font-weight: 600;
}

.template-section p {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 20px;
}

.template-preview {
    margin: 20px 0;
    padding: 20px;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    background: #f8fafc;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.template-code {
    background: #1f2937;
    color: #f9fafb;
    padding: 16px;
    border-radius: 8px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 14px;
    margin: 16px 0;
    overflow-x: auto;
    border: 1px solid #374151;
}

.template-code code {
    background: transparent;
    color: inherit;
    padding: 0;
    border-radius: 0;
    font-family: inherit;
}

.attribute-examples .example-item {
    margin: 20px 0;
    padding: 20px;
    border-left: 4px solid #3b82f6;
    background: #f8fafc;
    border-radius: 0 8px 8px 0;
}

.attribute-examples h4 {
    color: #1a1a1a;
    margin-bottom: 16px;
    font-size: 1.2em;
    font-weight: 600;
}

.usage-instructions {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e1e5e9;
}

.usage-instructions h3 {
    color: #1a1a1a;
    margin: 20px 0 12px 0;
    font-size: 1.3em;
    font-weight: 600;
}

.usage-instructions ul {
    margin: 12px 0 20px 20px;
    color: #4b5563;
    line-height: 1.6;
}

.usage-instructions li {
    margin-bottom: 8px;
}

.usage-instructions code {
    background: #e5e7eb;
    color: #1f2937;
    padding: 3px 6px;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
    border: 1px solid #d1d5db;
}

/* Template-specific section styling */
.template-section:nth-child(1) {
    border-color: #6366f1;
    border-left-width: 6px;
}

.template-section:nth-child(2) {
    border-color: #10b981;
    border-left-width: 6px;
}

.template-section:nth-child(3) {
    border-color: #f59e0b;
    border-left-width: 6px;
}

.template-section:nth-child(4) {
    border-color: #ef4444;
    border-left-width: 6px;
}

/* Responsive design */
@media (max-width: 768px) {
    .slots-template-demo {
        padding: 15px;
    }
    
    .template-section {
        padding: 20px;
    }
    
    .slots-template-demo h1 {
        font-size: 2em;
    }
    
    .template-section h2 {
        font-size: 1.5em;
    }
}
</style>
