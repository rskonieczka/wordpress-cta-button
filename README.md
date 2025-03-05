# [WH] CTA Button - WordPress Plugin

A feature-rich WordPress plugin that displays a sticky Call-to-Action (CTA) button on all devices, allowing visitors to instantly connect or take action. The button can be fully customized in terms of position, display times, colors, and target actions.

## Features

### Comprehensive Display Settings
- **Device Selection**: Display the button on mobile devices, desktops, or both
- **Position Control**: Place the button in any of the 8 positions (top, middle or bottom, combined with left, center or right)
- **Scheduling Options**: Configure the button to display on specific days and during specific hours
- **Visibility Rules**: Show or hide the button based on various conditions

### Flexible Action Configuration
- **Multiple Link Types**:
  - Phone calls (`tel:` links)
  - Email links (`mailto:` links)
  - SMS messages (`sms:` links)
  - Regular URLs (`http/https` links)
- **Target Control**: Open links in the same tab or a new tab

### Extensive Customization
- **Material Icons**: Choose from a variety of Material Design icons for the button
- **Custom Styling**:
  - Button background color
  - Icon color
  - Text color
  - Font weight
  - Custom CSS classes and IDs
- **Animation Effects**: Configurable pulsing animation with adjustable timing

### Advanced Features
- **Debug Mode**: Enable console logging for troubleshooting
- **REST API Integration**: Settings are exposed via a REST API endpoint for advanced integrations
- **Cache Prevention**: Implements measures to avoid caching issues
- **High Performance**: Optimized JavaScript for minimal impact on page load speed
- **Mobile Optimization**: Responsive design ensures proper display on all device sizes
- **Time-Based Visibility**: Automatically shows/hides the button based on configured time ranges

## Installation

1. Upload the plugin files to the `/wp-content/plugins/sticky-phone-button` directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure the plugin settings via the '[WH] CTA Button' option in the WordPress Settings menu

## Configuration

### Basic Setup
1. Navigate to Settings → [WH] CTA Button in your WordPress admin panel
2. Choose the devices you want to display the button on (mobile, desktop, or both)
3. Select the link type (phone, email, URL, SMS)
4. Enter the link value (phone number, email address, URL, etc.)
5. Choose the button position on the screen
6. Configure the CTA text (if needed)
7. Select days and hours for button display
8. Customize colors and icon

### Advanced Options
- Add custom CSS classes or IDs for additional styling
- Enable debug mode for troubleshooting
- Adjust blink animation timing
- Configure font weight and text appearance

## Testing

The plugin includes several debug parameters that can be added to any page URL to test functionality:

- `?debug=1`: Forces the button to show regardless of device settings
- `?alwaysShowCTA=1` or `?forceCTA=1`: Always displays the button ignoring time-based rules
- `?forceInit=1`: Forces button initialization

## Technical Details

### REST API
The plugin exposes settings via a REST API endpoint:
```
/wp-json/sticky-phone-button/v1/settings
```

### Ajax Endpoint
For backward compatibility, an Ajax endpoint is also available:
```
/wp-admin/admin-ajax.php?action=sticky_phone_button_get_settings
```

### Custom HTML Structure
The button is rendered with the following HTML structure:
```html
<div id="sticky-cta-container" class="sticky-phone-button sticky-position-class custom-class" style="custom-styles">
    <a href="link-target" target="_blank" class="sticky-phone-button-link">
        <span class="material-symbols-outlined">icon_name</span>
        <span class="sticky-phone-button-text">CTA Text</span>
    </a>
</div>
```

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Android Chrome)

## Version History

### 1.3 (Current)
- Added Material Icons integration
- Improved caching prevention
- Enhanced debug mode functionality
- Added REST API endpoint

### Previous Versions
- Initial release with core functionality
- Added scheduling options
- Implemented customization features

## Credits

Developed by [Wirtualny Handlowiec](http://wirtualnyhandlowiec.pl/)

## License

This WordPress plugin is licensed under the GPL v2 or later.

---

For support or feature requests, please contact the developer through the website: [http://wirtualnyhandlowiec.pl/](http://wirtualnyhandlowiec.pl/)
