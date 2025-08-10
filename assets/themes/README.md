# Slots Plugin Themes

This directory contains theme files for the Slots plugin. Each theme provides different visual styles and can be selected through the WordPress admin panel.

## Available Themes

### 1. Default Theme
- **File**: None (built into main CSS)
- **Description**: Clean, modern design with balanced shadows and borders
- **Features**: Standard styling with moderate shadows and border radius

### 2. Dark Theme
- **File**: `dark.css`
- **Description**: Dark theme with high contrast and modern aesthetics
- **Features**: 
  - Dark backgrounds (#1e293b, #334155, #475569)
  - Light text (#f1f5f9, #cbd5e1)
  - Enhanced shadows for depth
  - Blue accent borders (#60a5fa)

### 3. Light Theme
- **File**: `light.css`
- **Description**: Bright, clean theme with enhanced shadows
- **Features**:
  - White/light backgrounds
  - Dark text (#1e293b, #64748b)
  - Enhanced shadows for depth
  - Blue accent borders (#3b82f6)

### 4. Minimal Theme
- **File**: `minimal.css`
- **Description**: Flat design with no shadows or rounded corners
- **Features**:
  - No shadows
  - No border radius
  - Clean, flat appearance
  - Simplified borders and colors

### 5. Rounded Theme
- **File**: `rounded.css`
- **Description**: Soft, friendly design with enhanced border radius
- **Features**:
  - Large border radius (0.75rem - 2.5rem)
  - Enhanced shadows
  - Smooth transitions
  - Warm, friendly appearance

### 6. Colorful Theme
- **File**: `colorful.css`
- **Description**: Vibrant theme with playful colors and animations
- **Features**:
  - Blue-tinted backgrounds (#f0f9ff, #e0f2fe)
  - Bright text colors (#0c4a6e, #0369a1)
  - Colorful borders (#38bdf8, #7dd3fc, #f59e0b)
  - Enhanced animations with custom easing

## How to Use

### Admin Panel
1. Go to **Slots → Settings → Styling Options**
2. Select your preferred theme from the "Theme" dropdown
3. Save changes

### Frontend
Themes are automatically applied to:
- `[slots_grid]` shortcode
- `[slot_detail]` shortcode
- Demo page

### Demo Page
Visit the demo page to see all themes in action and switch between them in real-time.

## Creating Custom Themes

To create a custom theme:

1. Create a new CSS file in this directory (e.g., `my-theme.css`)
2. Define your theme class (e.g., `.slots-theme-my-theme`)
3. Add your theme to the `Slots_Themes` class in `includes/class-slots-themes.php`
4. Define CSS custom properties for:
   - Backgrounds (`--slots-bg-*`)
   - Text colors (`--slots-text-*`)
   - Borders (`--slots-border-*`)
   - Shadows (`--slots-shadow-*`)
   - Border radius (`--slots-radius-*`)
   - Transitions (`--slots-transition-*`)

### Example Custom Theme
```css
.slots-theme-my-theme {
    --slots-bg-primary: #your-color;
    --slots-bg-secondary: #your-color;
    --slots-text-primary: #your-color;
    --slots-border-primary: #your-color;
    --slots-shadow-sm: your-shadow;
    --slots-radius-sm: your-radius;
}
```

## CSS Variables Reference

The plugin uses these CSS custom properties that you can override:

### Backgrounds
- `--slots-bg-primary`: Main background color
- `--slots-bg-secondary`: Secondary background color
- `--slots-bg-card`: Card background color
- `--slots-bg-overlay`: Overlay background color

### Text Colors
- `--slots-text-primary`: Primary text color
- `--slots-text-secondary`: Secondary text color
- `--slots-text-muted`: Muted text color
- `--slots-text-inverse`: Inverse text color

### Borders
- `--slots-border-primary`: Primary border color
- `--slots-border-secondary`: Secondary border color
- `--slots-border-accent`: Accent border color

### Shadows
- `--slots-shadow-sm`: Small shadow
- `--slots-shadow-md`: Medium shadow
- `--slots-shadow-lg`: Large shadow
- `--slots-shadow-xl`: Extra large shadow

### Border Radius
- `--slots-radius-sm`: Small border radius
- `--slots-radius-md`: Medium border radius
- `--slots-radius-lg`: Large border radius
- `--slots-radius-xl`: Extra large border radius

### Transitions
- `--slots-transition`: Default transition
- `--slots-transition-fast`: Fast transition
- `--slots-transition-slow`: Slow transition

## Notes

- Themes are loaded dynamically based on admin selection
- The default theme has no additional CSS file
- All themes inherit from the base CSS variables
- Themes can be switched in real-time on the demo page
- Custom themes are automatically included in the admin dropdown
