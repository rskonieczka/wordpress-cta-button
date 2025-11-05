# Wordpress CTA Button - by WirtualnyHandlowiec.pl

A feature-rich WordPress plugin that displays a sticky Call-to-Action (CTA) button on all devices, allowing visitors to instantly connect or take action. The button can be fully customized in terms of position, display times, colors, and target actions.

## Perfect for Your Business

This plugin is designed to boost conversions for both service-based businesses and e-commerce stores:

### Service Businesses
Make it easy for customers to reach you instantly:
- **Medical & Healthcare**: Schedule appointments, consultations, emergency services
- **Professional Services**: Book consultations (legal, financial, consulting)
- **Home Services**: Request quotes for plumbing, HVAC, cleaning, moving
- **Beauty & Wellness**: Book appointments for salons, spas, fitness centers
- **Local Services**: Connect with automotive, photography, pet care, event planning

### E-commerce Stores
Drive sales and customer engagement:
- **Product Support**: Quick access to customer service and live chat
- **Special Offers**: Promote discount codes, coupons, and flash sales
- **Cart Recovery**: Encourage customers to complete purchases with exclusive offers
- **Shipping Information**: Highlight free shipping thresholds and delivery times
- **Pre-sale Questions**: Enable instant communication before purchase decisions

### Key Benefits
- **Increase Conversions**: Make it effortless for visitors to take action
- **24/7 Availability**: Display your contact options even outside business hours
- **Smart Targeting**: Show relevant CTAs based on specific pages or products
- **Mobile-First**: Optimized for the growing mobile audience
- **Professional Appearance**: Customizable design that matches your brand

## Table of Contents

- [Perfect for Your Business](#perfect-for-your-business)
  - [Service Businesses](#service-businesses)
  - [E-commerce Stores](#e-commerce-stores)
  - [Key Benefits](#key-benefits)
- [Plugin Previews](#plugin-previews)
- [Features](#features)
  - [Comprehensive Display Settings](#comprehensive-display-settings)
  - [Flexible Action Configuration](#flexible-action-configuration)
  - [Extensive Customization](#extensive-customization)
  - [Advanced Features](#advanced-features)
- [Installation](#installation)
  - [Method 1: WordPress Dashboard](#method-1-wordpress-dashboard)
  - [Method 2: Manual Installation](#method-2-manual-installation)
  - [Requirements](#requirements)
- [Configuration](#configuration)
  - [Basic Setup](#basic-setup)
  - [Advanced Options](#advanced-options)
  - [URL Filtering Examples](#url-filtering-examples)
  - [Border & Shadow Examples](#border--shadow-examples)
  - [CTA Text Formatting Examples](#cta-text-formatting-examples)
- [Testing](#testing)
  - [Debug Examples](#debug-examples)
- [Technical Details](#technical-details)
  - [REST API](#rest-api)
  - [Ajax Endpoint](#ajax-endpoint)
  - [Custom HTML Structure](#custom-html-structure)
- [Browser Compatibility](#browser-compatibility)
- [Version History](#version-history)
- [License](#license)
  - [What GPL v2 means](#what-gpl-v2-means)
  - [Paid License with Invoice](#paid-license-with-invoice)
- [Credits](#credits)

## Plugin Previews

### Want to Schedule an Appointment?
![Button Style Example 1](podglad2.png)

### Have a Question? Call Us
![Button Style Example 2](podglad3.png)

### Download Coupon
![Button Style Example 3](podglad4.png)

### See How to Book NFZ Appointment
![Button Style Example 4](podglad5.png)

## Features

### Comprehensive Display Settings
- **Device Selection**: Display the button on mobile devices, desktops, or both
- **Position Control**: Place the button in any of the 8 positions (top, middle or bottom, combined with left, center or right)
- **Scheduling Options**: Configure the button to display on specific days and during specific hours
- **URL Filtering**: Advanced visibility control based on page URLs
  - **Show on URLs containing** (whitelist): Display button only on pages with specific URL fragments
  - **Hide on URLs containing** (blacklist): Hide button on pages with specific URL fragments
  - Exclude rules have priority over include rules
  - Debug mode support (ignores URL rules when `?debug=1`, `?forceCTA=1`, or `?show=1` is used)
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
- **CTA Text Formatting**:
  - Support for HTML tags in CTA text
  - Small text using `<small>` tag
  - Italic text using `<em>` or `<i>` tags
  - Combine multiple formatting options
- **Border & Shape Control**:
  - Border radius (from square to fully circular buttons)
  - Border width, style, and color
  - 9 different border styles (solid, dashed, dotted, double, groove, ridge, inset, outset, none)
- **Shadow Effects**:
  - Enable/disable shadow
  - Shadow position (offset X/Y)
  - Shadow blur and spread control
  - Shadow color with transparency support
  - Inset (inner) shadow option
- **Animation Effects**: Configurable pulsing animation with adjustable timing

### Advanced Features
- **Debug Mode**: Enable console logging for troubleshooting
- **REST API Integration**: Settings are exposed via a REST API endpoint for advanced integrations
- **Cache Prevention**: Implements measures to avoid caching issues
- **High Performance**: Optimized JavaScript for minimal impact on page load speed
- **Mobile Optimization**: Responsive design ensures proper display on all device sizes
- **Time-Based Visibility**: Automatically shows/hides the button based on configured time ranges

![Settings preview](view.png)

## Installation

### Method 1: WordPress Dashboard

1. Go to Plugins → Add New in your WordPress admin panel
2. Search for "Wordpress CTA Button"
3. Click "Install Now" and then activate the plugin

### Method 2: Manual Installation

1. Download the plugin from [GitHub repository](https://github.com/rskonieczka/wordpress-cta-button/archive/refs/heads/main.zip)
2. Upload the plugin files to the `/wp-content/plugins/sticky-phone-button` directory, or install the plugin through the WordPress plugins screen directly
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Configure the plugin settings via the 'Wordpress CTA Button' option in the WordPress Settings menu

### Requirements
- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern web browser

## Configuration

### Basic Setup
1. Navigate to Settings → Wordpress CTA Button in your WordPress admin panel
2. Choose the devices you want to display the button on (mobile, desktop, or both)
3. Select the link type (phone, email, URL, SMS)
4. Enter the link value (phone number, email address, URL, etc.)
5. Choose the button position on the screen
6. Configure the CTA text (if needed)
7. Select days and hours for button display
8. Customize colors and icon

### Advanced Options
- **URL Filtering**: Configure whitelist/blacklist rules for specific pages
- **Border Customization**: Set border radius, width, style, and color
- **Shadow Effects**: Control shadow position, blur, spread, color, and inset options
- **Custom CSS**: Add custom CSS classes or IDs for additional styling
- **Debug Mode**: Enable console logs and testing parameters
- **Animation**: Adjust blink animation timing
- **Typography**: Configure font weight and text appearance

### URL Filtering Examples
```
Show on URLs containing (whitelist):
product
/shop/
category

Hide on URLs containing (blacklist):
contact
/admin/
checkout
```

### Border & Shadow Examples
```
Border Radius:
50px - rounded corners (default)
0px - square corners
25px - slightly rounded
50% - fully circular

Shadow Effects:
0px 2px 10px rgba(0,0,0,0.3) - subtle drop shadow
2px 2px 5px rgba(0,0,0,0.5) - offset shadow
inset 0px 2px 4px rgba(0,0,0,0.2) - inner shadow
```

### CTA Text Formatting Examples
```
Basic text:
Call Now

Bold text:
<b>Call Now</b> or <strong>Call Now</strong>

Small text:
Call Now <small>Available 24/7</small>

Italic text:
<em>Quick</em> Response or <i>Quick</i> Response

Line breaks:
Call Now<br>24/7 Support

Combined formatting:
<b>Call Now</b> <small><em>Open Mon-Fri</em></small>

Multiple formatting options:
Contact Us <small>Get <em>instant</em> support</small>

Real-world examples:

Medical/Healthcare:
<b>Schedule Appointment</b><br><small>Same-day available</small>

Colorectal Surgery/Oncology:
<strong>Expert Consultation</strong><br><small>Specialized in <em>colorectal cancer</em></small>

Restaurant/Food:
Order Now <small>Delivery in <em>30 min</em></small>

E-commerce:
<strong>Shop Now</strong><br><small>Free shipping over $50</small>

Service Business:
<b>Get Quote</b> <small>Response in <i>2 hours</i></small>

Emergency Services:
<strong>CALL NOW</strong><br><small><em>24/7 Emergency Line</em></small>

Real Estate:
Schedule Viewing<br><small>Virtual tours <i>available</i></small>

Consulting:
<b>Book Consultation</b><br><small>First 30 min <em>free</em></small>

Salon/Beauty:
Book Now <small>Online booking <b>10% OFF</b></small>

Fitness/Gym:
Start Today<br><small><b>First week FREE</b></small>

Legal Services:
<strong>Free Consultation</strong><br><small>Call <em>anytime</em></small>

Hotel/Accommodation:
<b>Book Your Stay</b><br><small>Best price <em>guarantee</em></small>

Automotive/Mechanic:
<strong>Schedule Service</strong><br><small>Same-day repairs <i>available</i></small>

Education/Tutoring:
Start Learning<br><small><b>First class FREE</b></small>

Plumbing/HVAC:
<b>EMERGENCY SERVICE</b><br><small><em>Available 24/7</em></small>

Photography:
<b>Book Session</b><br><small>Wedding packages from <i>$999</i></small>

Pet Services:
Grooming Appointment<br><small>Walk-ins <b>welcome</b></small>

Insurance:
<strong>Get Quote</strong><br><small>Compare rates in <em>minutes</em></small>

Moving Services:
<b>Free Estimate</b><br><small>Local & <i>long-distance</i></small>

Web Design:
<strong>Start Your Project</strong><br><small>Free consultation & <em>quote</em></small>

Cleaning Services:
<b>Book Cleaning</b><br><small>First-time customers <b>20% OFF</b></small>

Taxi/Transportation:
<strong>Order Ride</strong><br><small>Pickup in <em>5-10 min</em></small>

Dental Practice:
Schedule Checkup<br><small><b>New patients</b> welcome</small>

Event Planning:
<b>Plan Your Event</b><br><small>Weddings, parties & <em>corporate</em></small>

Tech Support:
<strong>Get Help Now</strong><br><small>Remote support <i>available</i></small>
```

## Testing

The plugin includes several debug parameters that can be added to any page URL to test functionality:

- `?debug=1`: Forces the button to show regardless of device settings and URL filtering rules
- `?alwaysShowCTA=1` or `?forceCTA=1`: Always displays the button ignoring time-based rules and URL filters
- `?show=1`: Forces button display ignoring all visibility conditions
- `?forceInit=1`: Forces button initialization

### Debug Examples
```
https://yoursite.com/contact/?debug=1
https://yoursite.com/admin/?forceCTA=1
https://yoursite.com/checkout/?show=1
```

**Note**: Debug parameters override URL filtering rules, making testing easier during development.

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
</div>s
```

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Android Chrome)

## Version History

### 1.5 (Current)
- **Stable Release**: All features from version 1.4 tested and verified
- **Documentation Update**: Enhanced documentation with visual previews
- **Performance Optimization**: Improved CSS generation and caching
- **Code Quality**: Refined implementation of URL filtering, border controls, and shadow effects

### 1.4
- **URL Filtering System**: Advanced whitelist/blacklist functionality for URL-based visibility control
- **Border & Shape Control**: Complete border customization (radius, width, style, color) with 9 border styles
- **Shadow Effects**: Full box-shadow management (enable/disable, position, blur, spread, color, inset option)
- **Enhanced Styling**: Dynamic CSS generation for all new visual options
- **Debug Mode Improvements**: URL filtering respects debug parameters for testing
- **Backward Compatibility**: All existing settings preserved during updates

### 1.3
- Added Material Icons integration
- Improved caching prevention
- Enhanced debug mode functionality
- Added REST API endpoint

### Previous Versions
- Initial release with core functionality
- Added scheduling options
- Implemented customization features

## License

**Free to Use - GPL v2**

This plugin is released under the GPL v2 (GNU General Public License version 2) or later.

### What GPL v2 means:
- You are free to use, modify, and distribute the plugin
- If you distribute modified versions, you must keep them under GPL
- The source code must be made available
- No warranty is provided

This plugin is free to use for all purposes. However, if you need an invoice or official documentation for your commercial use, please purchase a license.

### Paid License with Invoice
- One-time payment for a single domain
- Includes official invoice for business documentation
- No recurring fees
- **Business Benefits:**
  - Tax-deductible expense for your company
  - Proper accounting documentation for audits
  - Proof of legal software acquisition
  - Compliance with business software regulations
  - Priority email support
- Purchase online: [Buy license with invoice](https://buy.stripe.com/6oEcP3fJ90lN6oo9AK)

## Credits

Developed by [Wirtualny Handlowiec](http://wirtualnyhandlowiec.pl/)

---

For support or feature requests, please contact the developer through the website: [http://wirtualnyhandlowiec.pl/](http://wirtualnyhandlowiec.pl/)
