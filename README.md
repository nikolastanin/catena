# 🎰 Slots Plugin for WordPress

A comprehensive slots management plugin for WordPress with Tailwind-inspired CSS, advanced filtering, and customizable styling.

## ✨ Features

- **Custom Post Type**: Dedicated 'slot' post type for managing slot games
- **Advanced Shortcodes**: `[slots_grid]` and `[slot_detail]` for flexible display
- **Responsive Design**: Mobile-first, responsive grid layouts
- **Tailwind-Inspired CSS**: Modern utility-first CSS framework
- **Customizable Styling**: Admin panel for colors, fonts, and border radius
- **Advanced Filtering**: Filter by provider, category, rating, and more
- **AJAX Loading**: Load more slots without page refresh
- **SEO Friendly**: Proper meta fields and structured data
- **Developer Friendly**: Extensible architecture with hooks and filters

## 🚀 Installation

1. Upload the `slots` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Slots-Settings' in the admin menu to configure styling options
4. Use the shortcodes on any page or post to display slots

## 📖 Usage

### Basic Grid Display

```php
[slots_grid]
```

### Advanced Grid with Filters

```php
[slots_grid limit="12" sort="recent" show_filters="true"]
```

### Individual Slot Display

```php
[slot_detail id="123" show_rating="true" show_description="true"]
```

### Shortcode Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | number | 12 | Maximum number of slots to display (1, 3, 6, 9, or 12) |
| `sort` | string | recent | Sorting method (recent, random) |
| `show_filters` | boolean | true | Show/hide filter controls |
| `show_pagination` | boolean | true | Show/hide pagination controls |

## 🎨 Customization

### Admin Settings

The plugin includes a comprehensive admin panel for customization:

- **Primary Color**: Main color for buttons and highlights
- **Secondary Color**: Secondary color for text and borders
- **Accent Color**: Accent color for special elements
- **Border Radius**: Border radius for cards and buttons
- **Font Family**: Font family for slot cards and text
- **Grid Columns**: Default number of columns in grid view
- **Slots Per Page**: Default number of slots to display
- **Custom CSS**: Add custom CSS with variable support

### CSS Variables

The plugin provides CSS custom properties for easy theming:

```css
:root {
    --slots-primary: #3b82f6;
    --slots-secondary: #64748b;
    --slots-accent: #f59e0b;
    --slots-radius-md: 8px;
    --slots-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
```

### Custom CSS with Variables

You can use CSS variables in your custom CSS:

```css
.my-custom-slots .slot-card {
    background: {{primary_color}};
    border-radius: {{border_radius}};
    font-family: {{font_family}};
}
```

## 🏗️ Architecture

### File Structure

```
slots/
├── admin/
│   ├── admin-page.php
│   └── settings-page.php
├── assets/
│   ├── css/
│   │   ├── slots-public.css
│   │   └── slots-admin.css
│   ├── js/
│   │   ├── slots-public.js
│   │   └── slots-admin.js
│   └── images/
├── includes/
│   ├── class-slots.php
│   ├── class-slots-admin.php
│   ├── class-slots-public.php
│   ├── class-slots-post-types.php
│   └── class-slots-shortcodes.php
├── templates/
│   ├── slots-grid.php
│   ├── slot-card.php
│   ├── slot-detail.php
│   └── demo-page.php
├── slots.php
└── README.md
```

### Classes

- **`Slots`**: Main plugin class
- **`Slots_Admin`**: Admin functionality and settings
- **`Slots_Public`**: Public-facing functionality and assets
- **`Slots_Post_Types`**: Custom post type registration
- **`Slots_Shortcodes`**: Shortcode handling and templates

## 🔧 Development

### Hooks and Filters

The plugin provides several hooks for customization:

```php
// Modify slot data before display
add_filter('slots_slot_data', function($slot_data, $post_id) {
    // Customize slot data
    return $slot_data;
}, 10, 2);

// Modify grid query arguments
add_filter('slots_grid_query_args', function($args, $atts) {
    // Customize query arguments
    return $args;
}, 10, 2);
```

### Adding Custom Fields

To add custom fields to slots:

```php
add_action('add_meta_boxes', function() {
    add_meta_box(
        'custom_slot_field',
        'Custom Field',
        'custom_field_callback',
        'slot',
        'normal',
        'high'
    );
});
```

## 📱 Responsive Design

The plugin is built with mobile-first responsive design:

- **Mobile**: Single column layout with optimized touch targets
- **Tablet**: 2-3 column grid layout
- **Desktop**: 3-5 column grid layout with hover effects

## 🎯 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 🤝 Support

For support and feature requests, please create an issue in the plugin repository.

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Custom post type for slots
- Grid and detail shortcodes
- Admin settings panel
- Responsive design
- Tailwind-inspired CSS
- AJAX loading
- Advanced filtering

## 📚 Examples

### Basic Implementation

1. **Add a slot game**:
   - Go to Posts → Add New
   - Select "Slot" post type
   - Fill in slot details (title, description, featured image)
   - Add custom fields (provider, rating, RTP, wager limits)
   - Publish

2. **Display on a page**:
   - Create a new page
   - Add shortcode: `[slots_grid limit="6" sort="rating"]`
   - Publish and view

3. **Customize styling**:
   - Go to Slots-Settings in admin
   - Adjust colors, fonts, and border radius
   - Add custom CSS if needed
   - Save changes

### Advanced Usage

```php
// Display slots from specific provider with custom styling
[slots_grid 
    limit="8" 
    sort="rtp" 
    provider="Microgaming" 
    rating="4.5" 
    class="premium-slots"
    show_filters="true"
]

// Display individual slot with specific options
[slot_detail 
    id="456" 
    show_rating="true" 
    show_description="true" 
    show_provider="true"
    class="featured-slot"
]
```

## 🎨 Theme Integration

The plugin is designed to work with any WordPress theme. It includes:

- **CSS Reset**: Minimal CSS reset for consistent styling
- **Theme Compatibility**: Works with default WordPress themes
- **Custom Classes**: Easy to override with theme CSS
- **Responsive Breakpoints**: Standard breakpoints for theme integration

## 🚀 Performance

- **Lazy Loading**: Images load as needed
- **AJAX Pagination**: Load more content without page refresh
- **Optimized Queries**: Efficient database queries
- **Minified Assets**: Compressed CSS and JavaScript
- **Caching Ready**: Compatible with caching plugins

---

**Built with ❤️ for the WordPress community**
