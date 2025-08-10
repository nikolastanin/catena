# Slot Editor Template

The Slot Editor Template allows editors to customize the markup for individual slot pages through the WordPress admin settings. **All variables return raw values (not HTML-wrapped content), giving you complete control over the HTML structure and styling.**

## Features

- **Custom Markup**: Define your own HTML structure for slot detail pages
- **Variable Replacement**: Use placeholders that get replaced with actual slot data
- **Admin Interface**: Easy-to-use textarea in the Slots Settings page
- **Fallback Support**: Default markup provided if no custom markup is set

## Usage

### 1. Access Settings

Go to **Slots-Settings** in your WordPress admin menu.

### 2. Configure Markup

In the **Slot Editor Markup** section, enter your custom HTML markup using the available variables.

### 3. Use the Template

Use the shortcode with the `editor` template:

```php
[slot_detail template="editor" show_rating="true" show_description="true"]
```

## Available Variables

| Variable | Description | Example Output |
|----------|-------------|----------------|
| `{{slot_image}}` | Slot thumbnail image URL | `https://example.com/image.jpg` |
| `{{slot_title}}` | Slot title (text only) | `Slot Name` |
| `{{slot_provider}}` | Provider name (text only, if enabled) | `NetEnt` |
| `{{slot_rating}}` | Star rating value (if enabled) | `4.5/5` |
| `{{slot_rtp}}` | RTP percentage (if enabled) | `96.5%` |
| `{{slot_wager}}` | Wager range (if enabled) | `$0.10 - $100.00` |
| `{{slot_id}}` | Slot ID (text only) | `SLOT123` |
| `{{slot_description}}` | Description/excerpt (formatted text, if enabled) | `This is the slot description...` |
| `{{slot_permalink}}` | Slot permalink URL | `https://example.com/slot-name` |
| `{{star_rating}}` | Raw star rating HTML | `<div class="star-rating">★★★★☆</div>` |
| `{{rtp_value}}` | RTP value only | `96.5%` |
| `{{wager_range}}` | Wager range value only | `$0.10 - $100.00` |
| `{{provider_name}}` | Provider name only | `NetEnt` |
| `{{slot_id_value}}` | Slot ID value only | `SLOT123` |

## Example Markup

```html
<div class="custom-slot-layout">
    <header class="slot-header">
        {{slot_image}}
        <div class="slot-info">
            {{slot_title}}
            <div class="slot-meta">
                {{slot_provider}}
                {{slot_rating}}
            </div>
        </div>
    </header>
    
    <div class="slot-details">
        <div class="detail-row">
            <span class="label">RTP:</span>
            {{slot_rtp}}
        </div>
        <div class="detail-row">
            <span class="label">Bet Range:</span>
            {{slot_wager}}
        </div>
    </div>
    
    {{slot_description}}
    
    <div class="slot-actions">
        <a href="{{slot_permalink}}" class="play-button">Play Now</a>
    </div>
</div>
```

## Shortcode Attributes

The editor template respects all standard shortcode attributes:

- `show_rating` - Show/hide rating (default: true)
- `show_description` - Show/hide description (default: true)
- `show_provider` - Show/hide provider (default: true)
- `show_rtp` - Show/hide RTP (default: true)
- `show_wager` - Show/hide wager range (default: true)

## Notes

- Variables are only displayed if the corresponding data exists and the relevant shortcode attribute is enabled
- The template automatically handles missing data gracefully
- Custom CSS can be added in the Custom CSS section to style your custom markup
- Changes to the markup take effect immediately after saving

## Troubleshooting

**Template not working?**
- Ensure you're using `template="editor"` in your shortcode
- Check that the markup is saved in the admin settings
- Verify that the variables are spelled correctly (including double braces)

**Variables not showing?**
- Check if the slot has the required data
- Verify that the corresponding shortcode attributes are enabled
- Ensure the variable names match exactly (case-sensitive)

**Styling issues?**
- Add custom CSS in the Custom CSS section
- Use the browser inspector to debug layout issues
- Check that your markup follows proper HTML structure
