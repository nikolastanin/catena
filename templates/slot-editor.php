<?php
/**
 * Slot Editor Template - Uses custom markup from admin settings
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
$show_description = filter_var($atts['show_description'], FILTER_VALIDATE_BOOLEAN);
$show_provider = filter_var($atts['show_provider'], FILTER_VALIDATE_BOOLEAN);
$show_rtp = filter_var($atts['show_rtp'], FILTER_VALIDATE_BOOLEAN);
$show_wager = filter_var($atts['show_wager'], FILTER_VALIDATE_BOOLEAN);

// Get custom markup from settings
$custom_markup = !empty($settings['slot_editor_markup']) ? $settings['slot_editor_markup'] : '';

// If no custom markup is set, use default fallback
if (empty($custom_markup)) {
    $custom_markup = '<div class="slot-detail-container">
        <div class="slot-detail-header">
            <div class="slot-detail-image">
                <img src="{{slot_image}}" alt="{{slot_title}}" class="slot-detail-main-image">
            </div>
                            <div class="slot-detail-info">
                    <h1 class="slot-detail-title">{{slot_title}}</h1>
                    <div class="slot-detail-provider">Provider: {{slot_provider}}</div>
                    <div class="slot-detail-rating">Rating: {{slot_rating}}</div>
                    <div class="slot-detail-rtp">RTP: {{slot_rtp}}</div>
                    <div class="slot-detail-wager">Wager Range: {{slot_wager}}</div>
                    <div class="slot-detail-id">Slot ID: {{slot_id}}</div>
                </div>
            </div>
            <div class="slot-detail-description">{{slot_description}}</div>
        <div class="slot-detail-actions">
            <a href="{{slot_permalink}}" class="slot-detail-button primary">Play Now</a>
            <a href="' . home_url('/slots/') . '" class="slot-detail-button secondary">Browse More Slots</a>
        </div>
    </div>';
}

// Parse custom markup and replace variables
$parsed_markup = parse_slot_markup($custom_markup, $slot, $atts);

// Output the parsed markup
echo $parsed_markup;

/**
 * Parse slot markup and replace variables with actual values
 */
function parse_slot_markup($markup, $slot_data, $atts) {
                    // Define available variables and their values
                $variables = array(
                    '{{slot_image}}' => esc_url($slot_data['thumbnail'] ?: SLOTS_PLUGIN_URL . 'assets/images/default-slot.png'),
                    '{{slot_title}}' => esc_html($slot_data['title']),
                    '{{slot_provider}}' => (!empty($slot_data['provider_name']) && filter_var($atts['show_provider'], FILTER_VALIDATE_BOOLEAN)) ? esc_html($slot_data['provider_name']) : '',
                    '{{slot_rating}}' => (!empty($slot_data['star_rating']) && filter_var($atts['show_rating'], FILTER_VALIDATE_BOOLEAN)) ? number_format($slot_data['star_rating'], 1) . '/5' : '',
                    '{{slot_rtp}}' => (!empty($slot_data['rtp']) && filter_var($atts['show_rtp'], FILTER_VALIDATE_BOOLEAN)) ? number_format($slot_data['rtp'], 1) . '%' : '',
                    '{{slot_wager}}' => get_wager_display($slot_data, $atts),
                    '{{slot_id}}' => (!empty($slot_data['slot_id'])) ? esc_html($slot_data['slot_id']) : '',
                    '{{slot_description}}' => get_description_display($slot_data, $atts),
                    '{{slot_permalink}}' => esc_url($slot_data['permalink']),
                    '{{slot_excerpt}}' => wpautop($slot_data['excerpt']),
                    '{{slot_content}}' => wpautop($slot_data['content']),
                    '{{slot_modified_date}}' => date_i18n(get_option('date_format'), $slot_data['modified']),
                    '{{star_rating}}' => Slots_Shortcodes::generate_star_rating($slot_data['star_rating']),
                    '{{rtp_value}}' => !empty($slot_data['rtp']) ? number_format($slot_data['rtp'], 1) . '%' : 'N/A',
                    '{{wager_range}}' => get_wager_display($slot_data, $atts),
                    '{{provider_name}}' => esc_html($slot_data['provider_name'] ?: ''),
                    '{{slot_id_value}}' => esc_html($slot_data['slot_id'] ?: '')
                );
    
    // Replace variables in markup
    $parsed = $markup;
    foreach ($variables as $variable => $value) {
        $parsed = str_replace($variable, $value, $parsed);
    }
    
    return $parsed;
}

/**
 * Get wager display based on slot data and attributes
 */
function get_wager_display($slot_data, $atts) {
    if (!filter_var($atts['show_wager'], FILTER_VALIDATE_BOOLEAN)) {
        return '';
    }
    
    $wager_display = '';
    if (!empty($slot_data['min_wager']) && !empty($slot_data['max_wager'])) {
        $wager_display = '$' . number_format($slot_data['min_wager'], 2) . ' - $' . number_format($slot_data['max_wager'], 2);
    } elseif (!empty($slot_data['min_wager'])) {
        $wager_display = '$' . number_format($slot_data['min_wager'], 2) . '+';
    } elseif (!empty($slot_data['max_wager'])) {
        $wager_display = 'Up to $' . number_format($slot_data['max_wager'], 2);
    }
    
    return $wager_display;
}

/**
 * Get description display based on slot data and attributes
 */
function get_description_display($slot_data, $atts) {
    if (!filter_var($atts['show_description'], FILTER_VALIDATE_BOOLEAN)) {
        return '';
    }
    
    if (!empty($slot_data['content'])) {
        return wpautop($slot_data['content']);
    } elseif (!empty($slot_data['excerpt'])) {
        return wpautop($slot_data['excerpt']);
    }
    
    return '';
}
?>
