# Slots Plugin - Grid Editor System

A WordPress plugin that provides a flexible and customizable slot grid editor with custom markup support, dynamic loading, and seamless theme integration.

## Features

### 🎯 Core Functionality
- **Custom Post Type**: `slot` post type with comprehensive meta fields
- **Flexible Grid System**: Multiple grid templates with customizable layouts
- **Custom Markup Support**: Define your own HTML structure in admin settings
- **Dynamic Loading**: AJAX-powered pagination and filtering without page reloads
- **Responsive Design**: Automatically adapts to different screen sizes
- **Theme Integration**: Seamlessly works with any WordPress theme

### 🔧 Grid Templates
- **Default Grid** (`slots-grid.php`): Standard grid layout with filters and pagination

- **Auto-detection**: Intelligently chooses template based on settings or shortcode attributes

### 📱 Shortcode System
- `[slots_grid]`: Main grid shortcode with extensive customization options
- `[slot_detail]`: Individual slot detail display
- Template selection via `template` attribute
- Configurable limits, sorting, and display options

## Installation

1. Upload the `slots` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings in 'Slots' → 'Settings'

## Configuration

### Admin Settings
Navigate to **Slots → Settings** to configure:

- **Grid Editor Markup**: Custom HTML template for slot cards
- **Default Display Options**: Default limits, sorting, and pagination settings
- **Custom Fields**: Configure which slot meta fields to display

### Grid Editor Markup
The grid editor allows you to define custom HTML structure for slot cards. Use these placeholders:

```html
<div class="custom-slot-card">
    <h3>{slot_title}</h3>
    <img src="{slot_image}" alt="{slot_title}">
    <div class="slot-meta">
        <span class="provider">{provider_name}</span>
        <span class="rating">{star_rating}</span>
        <span class="rtp">RTP: {rtp}%</span>
    </div>
    <a href="{slot_permalink}" class="play-button">Play Now</a>
</div>
```

**Available Placeholders:**
- `{slot_title}` - Slot title
- `{slot_image}` - Slot thumbnail image
- `{slot_permalink}` - Slot detail page URL
- `{provider_name}` - Game provider name
- `{star_rating}` - Star rating (1-5)
- `{rtp}` - Return to Player percentage
- `{min_wager}` - Minimum bet amount
- `{max_wager}` - Maximum bet amount
- `{slot_excerpt}` - Slot description excerpt

## Usage

### Basic Grid Display
```php
// Default grid with 12 slots
echo do_shortcode('[slots_grid]');

// Custom limit and sorting
echo do_shortcode('[slots_grid limit="6" sort="random"]');

// Force specific template
echo do_shortcode('[slots_grid template="editor" limit="9"]');
```

### Shortcode Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | integer | 12 | Number of slots to display |
| `sort` | string | recent | Sorting method (recent, random) |
| `template` | string | auto | Template to use (default, editor, auto) |
| `show_filters` | boolean | true | Show filter controls |
| `show_pagination` | boolean | true | Show pagination controls |

### PHP Integration
```php
// Get slots data
$slots = get_posts(array(
    'post_type' => 'slot',
    'posts_per_page' => 6,
    'post_status' => 'publish'
));

// Include grid template

```

## File Structure

```
slots/
├── slots.php                          # Main plugin file
├── README.md                          # This documentation
├── includes/
│   ├── class-slots-admin.php         # Admin functionality
│   ├── class-slots-public.php        # Public-facing features
│   ├── class-slots-shortcodes.php    # Shortcode handlers
│   └── class-slots-template-manager.php # Template management
├── templates/
│   ├── slots-grid.php                # Default grid template

│   └── slot-card.php                 # Individual slot card
├── assets/
│   ├── css/
│   │   └── slots-public.css         # Public styles
│   └── js/
│       └── slots-public.js          # Public JavaScript
└── languages/                        # Translation files
```

## Customization

### Adding Custom Fields
Extend the slot meta fields by adding to the `slot` post type:

```php
// Add custom meta field
add_post_meta_box('custom_field', 'Custom Field', 'slot');

// Display in template
$custom_value = get_post_meta($slot_id, 'custom_field', true);
```

### Custom CSS
Override default styles by adding CSS to your theme:

```css
/* Custom slot card styling */
.slot-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* Custom grid layout */
.slots-grid {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}
```

### JavaScript Extensions
Extend the grid functionality with custom JavaScript:

```javascript
// Custom slot card click handler
jQuery(document).on('click', '.slot-card', function() {
    var slotId = jQuery(this).data('slot-id');
    // Custom functionality
});

// Extend sorting options
jQuery(document).on('change', '.slots-sort-select', function() {
    var sort = jQuery(this).val();
    // Custom sorting logic
});
```

## AJAX Endpoints

### Load Slots Grid
**Action**: `load_slots_grid`
**Parameters**:
- `page`: Page number (integer)
- `limit`: Slots per page (integer)
- `sort`: Sorting method (string)
- `nonce`: Security nonce (string)

**Response**:
```json
{
    "success": true,
    "data": {
        "html": "Generated HTML content",
        "has_more": true
    }
}
```

## Hooks and Filters

### Actions
- `slots_before_grid_display` - Before grid rendering
- `slots_after_grid_display` - After grid rendering
- `slots_before_slot_card` - Before individual slot card
- `slots_after_slot_card` - After individual slot card

### Filters
- `slots_grid_query_args` - Modify grid query arguments
- `slots_card_data` - Modify slot card data before rendering
- `slots_grid_template_file` - Override template file selection

## Troubleshooting

### Common Issues

**Grid not displaying slots:**
- Check if slots exist in the `slot` post type
- Verify shortcode syntax
- Check browser console for JavaScript errors

**Custom markup not working:**
- Ensure markup is saved in admin settings
- Check placeholder syntax
- Verify template is set to "editor"

**AJAX loading not working:**
- Check nonce verification
- Verify AJAX action is registered
- Check browser network tab for errors

### Debug Mode
Enable debug mode in WordPress to see detailed error messages:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Performance Tips

1. **Use appropriate limits**: Don't load too many slots at once
2. **Enable caching**: Use WordPress caching plugins
3. **Optimize images**: Compress slot thumbnails
4. **Lazy loading**: Images are automatically lazy-loaded
5. **Minimize AJAX calls**: Use reasonable pagination limits

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Internet Explorer 11+ (with polyfills)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support and feature requests, please create an issue in the repository or contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: December 2024  
**WordPress Version**: 5.0+  
**PHP Version**: 7.4+
