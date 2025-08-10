# Slot Detail Template System

This document explains how to use the flexible template system for the `[slot_detail]` shortcode in the Slots plugin.

## Overview

The template system allows you to display slot information using different HTML layouts while keeping the same shortcode. You can choose from built-in templates or create your own custom templates.

## Built-in Templates

### 1. Default Template (`default`)
- **File**: `slot-detail.php`
- **Description**: Standard layout with full information display
- **Usage**: `[slot_detail id="123"]` or `[slot_detail id="123" template="default"]`

### 2. Minimal Template (`minimal`)
- **File**: `slot-detail-minimal.php`
- **Description**: Clean, minimal layout with essential information only
- **Usage**: `[slot_detail id="123" template="minimal"]`

### 3. Compact Template (`compact`)
- **File**: `slot-detail-compact.php`
- **Description**: Space-efficient layout for sidebar or small areas
- **Usage**: `[slot_detail id="123" template="compact"]`

### 4. Featured Template (`featured`)
- **File**: `slot-detail-featured.php`
- **Description**: Highlighted layout with prominent call-to-action
- **Usage**: `[slot_detail id="123" template="featured"]`

## Shortcode Attributes

All templates support these attributes:

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `id` | string | current post ID | Slot ID or post ID to display |
| `template` | string | `default` | Template variation to use |
| `show_rating` | boolean | `true` | Show/hide star rating |
| `show_description` | boolean | `true` | Show/hide description |
| `show_provider` | boolean | `true` | Show/hide provider name |
| `show_rtp` | boolean | `true` | Show/hide RTP information |
| `show_wager` | boolean | `true` | Show/hide wager range |
| `class` | string | `''` | Additional CSS classes |

## Usage Examples

### Basic Usage
```php
// Default template
[slot_detail id="123"]

// Minimal template
[slot_detail id="123" template="minimal"]

// Compact template
[slot_detail id="123" template="compact"]

// Featured template
[slot_detail id="123" template="featured"]
```

### With Custom Attributes
```php
// Hide rating and description
[slot_detail id="123" template="minimal" show_rating="false" show_description="false"]

// Show only provider and RTP
[slot_detail id="123" template="compact" show_rating="false" show_description="false" show_wager="false"]

// Add custom CSS class
[slot_detail id="123" template="featured" class="my-custom-slot"]
```

## Creating Custom Templates

### Method 1: Theme Directory
Place your custom template in your theme directory:

```
your-theme/
├── slots/
│   └── my-custom-template.php
```

### Method 2: Using PHP Functions
Register a custom template programmatically:

```php
// In your theme's functions.php or a custom plugin
add_action('init', 'register_my_custom_template');

function register_my_custom_template() {
    slots_register_template('my-custom', array(
        'name' => 'My Custom Layout',
        'file' => 'my-custom-template.php',
        'description' => 'A custom layout for my theme'
    ));
}
```

Then use it:
```php
[slot_detail id="123" template="my-custom"]
```

### Template File Structure
Your custom template should follow this structure:

```php
<?php
/**
 * My Custom Template
 *
 * @package Slots
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Variables available:
// $slot - Slot data array
// $atts - Shortcode attributes
// $settings - Plugin settings
?>

<div class="my-custom-slot-layout">
    <h2><?php echo esc_html($slot['title']); ?></h2>
    
    <?php if (!empty($slot['provider_name'])): ?>
    <p>Provider: <?php echo esc_html($slot['provider_name']); ?></p>
    <?php endif; ?>
    
    <a href="<?php echo esc_url($slot['permalink']); ?>" class="play-button">
        Play Now
    </a>
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
$template_info = slots_get_template('minimal');

// Get template file path
$template_path = slots_get_template_path('featured');

// Load template manually
slots_load_template('compact', $slot_data, $attributes);

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
- **Minimal**: `.slot-detail-minimal`
- **Compact**: `.slot-detail-compact`
- **Featured**: `.slot-detail-featured`

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
