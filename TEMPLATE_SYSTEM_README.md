# Slot Detail Template System

This document explains how to use the flexible template system for the `[slot_detail]` shortcode in the Slots plugin.

## Overview

The template system automatically selects the appropriate template based on your admin settings. When the "Custom Slot Markup" field is filled out in the admin settings, all shortcodes automatically use the custom editor template. If left empty, the default template is used.

## Available Templates

### 1. Default Template (`default`)
- **File**: `slot-detail.php`
- **Description**: Standard layout with full information display
- **Usage**: Automatically used when Custom Slot Markup is empty

### 2. Editor Template (`editor`)
- **File**: `slot-editor.php`
- **Description**: Customizable layout using admin-defined markup
- **Usage**: Automatically used when Custom Slot Markup is filled out

## Shortcode Attributes

The `[slot_detail]` shortcode supports these attributes:

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `id` | string | current post ID | Slot ID or post ID to display |
| `show_rating` | boolean | `true` | Show/hide star rating |
| `show_description` | boolean | `true` | Show/hide description |
| `show_provider` | boolean | `true` | Show/hide provider name |
| `show_rtp` | boolean | `true` | Show/hide RTP information |
| `show_wager` | boolean | `true` | Show/hide wager range |
| `class` | string | `''` | Additional CSS classes |

## Usage Examples

### Basic Usage
```php
// Uses default template if Custom Slot Markup is empty
// Uses editor template if Custom Slot Markup is filled
[slot_detail id="123"]
```

### With Custom Attributes
```php
// Hide rating and description
[slot_detail id="123" show_rating="false" show_description="false"]

// Show only provider and RTP
[slot_detail id="123" show_rating="false" show_description="false" show_wager="false"]

// Add custom CSS class
[slot_detail id="123" class="my-custom-slot"]
```

## Custom Slot Markup

To use the editor template, fill out the "Custom Slot Markup" field in the admin settings (Slots-Settings > Slot Editor Markup). This field accepts HTML with special variables that get replaced with actual slot data.

### Available Variables

- `{{slot_image}}` - Slot thumbnail image URL
- `{{slot_title}}` - Slot title (text only)
- `{{slot_provider}}` - Provider name (text only, if enabled)
- `{{slot_rating}}` - Star rating value (e.g., "4.5/5", if enabled)
- `{{slot_rtp}}` - RTP percentage (e.g., "96.5%", if enabled)
- `{{slot_wager}}` - Wager range (e.g., "$0.10 - $100", if enabled)
- `{{slot_id}}` - Slot ID (text only)
- `{{slot_description}}` - Description/excerpt (formatted text, if enabled)
- `{{slot_permalink}}` - Slot permalink URL
- `{{star_rating}}` - Raw star rating HTML
- `{{rtp_value}}` - RTP value only (e.g., "96.5%")
- `{{wager_range}}` - Wager range value only (e.g., "$0.10 - $100")
- `{{provider_name}}` - Provider name only (text only)
- `{{slot_id_value}}` - Slot ID value only (text only)

### Example Custom Markup

```html
<div class="my-custom-slot-layout">
    <div class="slot-header">
        <img src="{{slot_image}}" alt="{{slot_title}}" class="slot-image">
        <h1>{{slot_title}}</h1>
    </div>
    
    <div class="slot-info">
        <p>Provider: {{slot_provider}}</p>
        <p>Rating: {{slot_rating}}</p>
        <p>RTP: {{slot_rtp}}</p>
    </div>
    
    <div class="slot-description">
        {{slot_description}}
    </div>
    
    <a href="{{slot_permalink}}" class="play-button">Play Now</a>
</div>
```

## Template Hierarchy

The system looks for templates in this order:

1. **Plugin templates directory**: `wp-content/plugins/slots/templates/`
2. **Parent theme directory**: `wp-content/themes/your-theme/slots/`
3. **Child theme directory**: `wp-content/themes/your-child-theme/slots/`

## Helper Functions

The plugin provides several helper functions:

```php
// Check if template exists
if (slots_template_exists('my-template')) {
    // Template is available
}

// Get template information
$template_info = slots_get_template('default');

// Get template file path
$template_path = slots_get_template_path('editor');

// Load template manually
slots_load_template('default', $slot_data, $attributes);

// Get all available templates
$templates = slots_get_template_options();
```

## Demo Page

To see all templates in action, you can include the demo template:

```php
include SLOTS_PLUGIN_DIR . 'templates/template-demo.php';
```

This will display all template variations with usage examples.

## Styling

Each template has its own CSS classes for easy styling:

- **Default**: `.slot-detail-container`
- **Editor**: `.slot-detail-container` (uses custom markup classes)

## Extending the System

### Adding New Template Types
To add new template types beyond slot detail, extend the `Slots_Template_Manager` class:

```php
class My_Custom_Template_Manager extends Slots_Template_Manager {
    protected function register_default_templates() {
        $this->available_templates = array(
            'my-template' => array(
                'name' => 'My Template',
                'file' => 'my-template.php',
                'description' => 'My custom template'
            )
        );
    }
}
```

### Custom Template Filters
Use WordPress filters to modify templates:

```php
// Modify template data
add_filter('slots_custom_templates', 'modify_slots_templates');

function modify_slots_templates($templates) {
    // Modify or add templates
    $templates['my-template']['description'] = 'Modified description';
    return $templates;
}
```

## Troubleshooting

### Template Not Found
- Check if the template file exists in the correct directory
- Verify the template key is registered correctly
- Check file permissions

### Template Not Loading
- Ensure the template file has proper PHP syntax
- Check for PHP errors in debug.log
- Verify the template file path is correct

### Styling Issues
- Check if CSS classes are properly applied
- Verify theme CSS isn't conflicting
- Use browser developer tools to inspect elements

## Best Practices

1. **Always escape output** using `esc_html()`, `esc_url()`, etc.
2. **Check for data existence** before displaying
3. **Use translation functions** (`__()`, `_e()`) for text
4. **Follow WordPress coding standards**
5. **Test templates** with different data scenarios
6. **Document custom templates** with clear descriptions

## Support

For issues or questions about the template system:
- Check the plugin documentation
- Review the demo templates for examples
- Use the helper functions for common operations
- Test with different shortcode attributes
