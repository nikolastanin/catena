# Slot Editor Template

The Slot Editor Template allows you to customize the HTML markup for individual slot pages through the WordPress admin interface.

## Overview

When you fill out the "Custom Slot Markup" field in the admin settings (Slots > Settings > Slot Editor Markup), all `[slot_detail]` shortcodes automatically use this custom template. If the field is left empty, the default template is used.

## Usage

### Basic Shortcode
```php
[slot_detail id="123" show_rating="true" show_description="true"]
```

**Note**: No template parameter is needed - the template is automatically selected based on your admin settings.

### Available Attributes
- `id` - Slot ID (required)
- `show_rating` - Show/hide star rating (true/false)
- `show_description` - Show/hide description (true/false)
- `show_provider` - Show/hide provider name (true/false)
- `show_rtp` - Show/hide RTP information (true/false)
- `show_wager` - Show/hide wager range (true/false)
- `class` - Additional CSS classes

## Setting Up Custom Markup

1. Go to **Slots > Settings** in your WordPress admin
2. Navigate to the **Slot Editor Markup** section
3. Fill out the "Custom Slot Markup" field with your HTML
4. Use the available variables (see below) to insert dynamic content
5. Save your changes

## Available Variables

Use these variables in your custom markup to insert dynamic content:

- `{{slot_image}}` - Slot thumbnail image URL
- `{{slot_title}}` - Slot title (text only)
- `{{slot_provider}}` - Provider name
- `{{slot_rtp}}` - RTP percentage
- `{{slot_wager_min}}` - Minimum wager amount
- `{{slot_wager_max}}` - Maximum wager amount
- `{{slot_rating}}` - Star rating (HTML)
- `{{slot_rating_value}}` - Rating value (number only)
- `{{slot_description}}` - Slot description
- `{{slot_features}}` - Slot features list
- `{{slot_bonus_features}}` - Bonus features list
- `{{slot_play_button}}` - Play button HTML
- `{{slot_demo_button}}` - Demo button HTML
- `{{slot_review_link}}` - Review link HTML

## Example Custom Markup

```html
<div class="custom-slot-layout">
    <div class="slot-header">
        <img src="{{slot_image}}" alt="{{slot_title}}" class="slot-thumbnail">
        <h1 class="slot-title">{{slot_title}}</h1>
        <div class="slot-provider">by {{slot_provider}}</div>
    </div>
    
    <div class="slot-stats">
        <div class="stat-item">
            <span class="label">RTP:</span>
            <span class="value">{{slot_rtp}}</span>
        </div>
        <div class="stat-item">
            <span class="label">Wager:</span>
            <span class="value">{{slot_wager_min}} - {{slot_wager_max}}</span>
        </div>
        <div class="stat-item">
            <span class="label">Rating:</span>
            <span class="value">{{slot_rating}}</span>
        </div>
    </div>
    
    <div class="slot-description">
        {{slot_description}}
    </div>
    
    <div class="slot-actions">
        {{slot_play_button}}
        {{slot_demo_button}}
    </div>
</div>
```

## Template Selection Logic

The system automatically selects the appropriate template:

- **If Custom Slot Markup is filled**: Uses the editor template with your custom HTML
- **If Custom Slot Markup is empty**: Uses the default template

This means you don't need to specify a template in your shortcodes - the system handles it automatically based on your admin configuration.

## Best Practices

1. **Test your markup**: Always test your custom markup to ensure it displays correctly
2. **Use CSS classes**: Add custom CSS classes to style your layout
3. **Keep it simple**: Start with a basic layout and add complexity gradually
4. **Mobile responsive**: Ensure your custom markup works on mobile devices
5. **Performance**: Avoid complex HTML structures that might impact page load times

## Troubleshooting

### Template Not Working
- Ensure the Custom Slot Markup field is filled out in admin settings
- Check that your HTML is valid
- Verify that the slot ID exists

### Variables Not Replacing
- Make sure variable names are exactly as shown (case-sensitive)
- Check that the slot has the required data
- Verify the shortcode is properly formatted

### Styling Issues
- Add custom CSS to your theme
- Use the `class` attribute in your shortcode for additional styling
- Check for CSS conflicts with your theme

## Advanced Usage

### Conditional Display
You can use PHP-like logic in your markup:

```html
<div class="slot-info">
    {{slot_title}}
    {{slot_provider}}
    
    <!-- Only show rating if it exists -->
    <div class="rating-section" style="display: {{slot_rating_value > 0 ? 'block' : 'none'}}">
        {{slot_rating}}
    </div>
</div>
```

### Custom CSS Integration
Add custom CSS to your theme's stylesheet:

```css
.custom-slot-layout {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
}

.slot-header {
    text-align: center;
    margin-bottom: 20px;
}

.slot-stats {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
}

.slot-actions {
    text-align: center;
    margin-top: 20px;
}
```

## Support

For additional help with the Slot Editor Template:

1. Check the main plugin documentation
2. Review the template system README
3. Test with different slot data
4. Use browser developer tools to debug HTML/CSS issues

Remember: The editor template respects all standard shortcode attributes, so you can still control which elements are displayed using the `show_*` parameters.
