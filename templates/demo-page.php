<?php
/**
 * Demo Page Template for Slots Plugin
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Slots Plugin Demo', 'slots'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="slots-demo-page">
        <header class="slots-demo-header">
            <div class="slots-container">
                <h1><?php _e('🎰 Slots Plugin Demo', 'slots'); ?></h1>
                <p><?php _e('This page demonstrates the various features of the Slots plugin', 'slots'); ?></p>
                
                <!-- Theme Switcher -->
                <div class="slots-theme-switcher">
                    <h3><?php _e('Theme Switcher', 'slots'); ?></h3>
                    <p><?php _e('Try different themes to see how they affect the appearance:', 'slots'); ?></p>
                    
                    <?php
                    $themes = new Slots_Themes();
                    $available_themes = $themes->get_themes();
                    $current_theme = $themes->get_current_theme();
                    ?>
                    
                    <div class="theme-options">
                        <?php foreach ($available_themes as $theme_key => $theme): ?>
                            <label class="theme-option">
                                <input type="radio" name="demo_theme" value="<?php echo esc_attr($theme_key); ?>" 
                                       <?php checked($current_theme, $theme_key); ?>>
                                <span class="theme-name"><?php echo esc_html($theme['name']); ?></span>
                                <span class="theme-description"><?php echo esc_html($theme['description']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <p class="theme-note">
                        <em><?php _e('Note: To permanently change the theme, go to Slots → Settings → Styling Options', 'slots'); ?></em>
                    </p>
                </div>
            </div>
        </header>

        <main class="slots-demo-main">
            <div class="slots-container">
                
                <!-- Grid Display Section -->
                <section class="slots-demo-section">
                    <h2><?php _e('Grid Display', 'slots'); ?></h2>
                    <p><?php _e('Display slots in a responsive grid layout with filtering and sorting options.', 'slots'); ?></p>
                    
                    <?php echo do_shortcode('[slots_grid limit="6" sort="recent" show_filters="true" show_pagination="true"]'); ?>
                </section>

                <!-- Individual Slot Section -->
                <section class="slots-demo-section">
                    <h2><?php _e('Individual Slot Display', 'slots'); ?></h2>
                    <p><?php _e('Display detailed information about a specific slot game.', 'slots'); ?></p>
                    
                    <?php 
                    // Get the first available slot to display
                    $slots = get_posts(array(
                        'post_type' => 'slot',
                        'post_status' => 'publish',
                        'posts_per_page' => 1
                    ));
                    
                    if (!empty($slots)) {
                        echo do_shortcode('[slot_detail id="' . $slots[0]->ID . '" show_rating="true" show_description="true"]');
                    } else {
                        echo '<p>' . __('No slots available to display. Please add some slot games first.', 'slots') . '</p>';
                    }
                    ?>
                </section>

                <!-- Shortcode Examples Section -->
                <section class="slots-demo-section">
                    <h2><?php _e('Shortcode Examples', 'slots'); ?></h2>
                    <p><?php _e('Here are examples of how to use the available shortcodes:', 'slots'); ?></p>
                    
                    <div class="slots-code-examples">
                        <div class="code-example">
                            <h4><?php _e('Basic Grid', 'slots'); ?></h4>
                            <code>[slots_grid]</code>
                            <p><?php _e('Display slots in default grid layout', 'slots'); ?></p>
                        </div>
                        
                        <div class="code-example">
                            <h4><?php _e('Filtered Grid', 'slots'); ?></h4>
                            <code>[slots_grid limit="6" sort="random" show_filters="true"]</code>
                            <p><?php _e('Display 6 randomly sorted slots with filter controls', 'slots'); ?></p>
                        </div>
                        
                        <div class="code-example">
                            <h4><?php _e('Small Grid', 'slots'); ?></h4>
                            <code>[slots_grid limit="3" sort="recent" show_filters="true"]</code>
                            <p><?php _e('Display 3 most recent slots with filter controls', 'slots'); ?></p>
                        </div>
                        
                        <div class="code-example">
                            <h4><?php _e('Single Slot', 'slots'); ?></h4>
                            <code>[slots_grid limit="1" sort="random" show_filters="false"]</code>
                            <p><?php _e('Display 1 random slot without filter controls', 'slots'); ?></p>
                        </div>
                        
                        <div class="code-example">
                            <h4><?php _e('Custom Styling', 'slots'); ?></h4>
                            <code>[slots_grid class="my-custom-slots" show_filters="false"]</code>
                            <p><?php _e('Apply custom CSS class and hide filters', 'slots'); ?></p>
                        </div>
                    </div>
                </section>

                <!-- Features Section -->
                <section class="slots-demo-section">
                    <h2><?php _e('Plugin Features', 'slots'); ?></h2>
                    
                    <div class="slots-features">
                        <div class="feature-item">
                            <h3>🎨 <?php _e('Customizable Styling', 'slots'); ?></h3>
                            <p><?php _e('Change colors, fonts, and border radius through the admin panel', 'slots'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h3>📱 <?php _e('Responsive Design', 'slots'); ?></h3>
                            <p><?php _e('Automatically adapts to different screen sizes and devices', 'slots'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h3>🔍 <?php _e('Advanced Filtering', 'slots'); ?></h3>
                            <p><?php _e('Filter by provider, category, rating, and more', 'slots'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h3>⚡ <?php _e('AJAX Loading', 'slots'); ?></h3>
                            <p><?php _e('Load more slots without page refresh', 'slots'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h3>🎯 <?php _e('Multiple Display Options', 'slots'); ?></h3>
                            <p><?php _e('Grid view, individual slot display, and customizable layouts', 'slots'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h3>🛠️ <?php _e('Developer Friendly', 'slots'); ?></h3>
                            <p><?php _e('Extensible architecture with hooks and filters', 'slots'); ?></p>
                        </div>
                    </div>
                </section>

            </div>
        </main>

        <footer class="slots-demo-footer">
            <div class="slots-container">
                <p><?php _e('Slots Plugin Demo - Built with WordPress and Tailwind-inspired CSS', 'slots'); ?></p>
            </div>
        </footer>
    </div>

    <?php wp_footer(); ?>
    
    <script>
    jQuery(document).ready(function($) {
        // Theme switcher functionality
        $('input[name="demo_theme"]').on('change', function() {
            var selectedTheme = $(this).val();
            var container = $('.slots-demo-page');
            
            // Remove all theme classes
            container.removeClass('slots-theme-dark slots-theme-light slots-theme-minimal slots-theme-rounded slots-theme-colorful');
            
            // Add selected theme class
            if (selectedTheme !== 'default') {
                container.addClass('slots-theme-' + selectedTheme);
            }
            
            // Show feedback
            $('<div class="theme-feedback">Theme changed to: ' + $(this).siblings('.theme-name').text() + '</div>')
                .appendTo('.slots-theme-switcher')
                .fadeOut(3000, function() { $(this).remove(); });
        });
    });
    </script>
</body>
</html>
