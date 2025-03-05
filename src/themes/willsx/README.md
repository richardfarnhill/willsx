# WillsX WordPress Theme

A modern WordPress theme for WillsX, a wills and estate planning service.

## Features

- **Modern Design**: Clean, professional design with a focus on readability and user experience.
- **Responsive Layout**: Fully responsive design that works on all devices.
- **Dark Mode Support**: Built-in dark mode toggle for user preference.
- **GDPR Compliant**: Includes a customizable cookie notice with consent management.
- **Co-branding Support**: Special header section for partner branding.
- **Customizer Options**: Extensive theme options in the WordPress Customizer.
- **Performance Optimized**: Lightweight and fast-loading.
- **Accessibility Ready**: Built with accessibility in mind.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern web browser

## Installation

1. Upload the `willsx` folder to the `/wp-content/themes/` directory
2. Activate the theme through the 'Themes' menu in WordPress
3. Configure theme settings in Appearance > Customize

## Theme Structure

```
willsx/
├── assets/
│   ├── css/
│   │   └── main.css
│   ├── js/
│   │   ├── main.js
│   │   └── customizer.js
│   └── images/
│       └── partners/
├── inc/
│   ├── customizer.php
│   ├── template-functions.php
│   └── template-tags.php
├── languages/
├── template-parts/
│   ├── components/
│   │   └── cookie-notice.php
│   ├── content.php
│   ├── content-none.php
│   ├── content-page.php
│   └── header/
│       └── cobranding.php
├── footer.php
├── functions.php
├── header.php
├── index.php
├── style.css
└── README.md
```

## Customization

### Theme Options

The theme includes several customization options available in the WordPress Customizer:

1. **Header Options**: Configure header behavior and appearance.
2. **Footer Options**: Customize footer content and layout.
3. **Cookie Notice Options**: Enable/disable and customize the cookie consent notice.
4. **Color Options**: Change the primary and secondary colors.

### Co-branding

The theme supports co-branding with partners. To enable co-branding:

1. Add a query parameter `?partner=partner_id` to the URL
2. The theme will display the partner's logo and name in the header

Available partner IDs:
- acme
- globex
- initech

### Cookie Notice

The theme includes a GDPR-compliant cookie notice that:

- Informs users about cookie usage
- Allows users to accept all cookies, only necessary cookies, or set preferences
- Remembers user preferences for future visits

## Development

### CSS Structure

The theme uses CSS variables for consistent styling. Main variables are defined in the `:root` selector in `assets/css/main.css`.

### JavaScript

The theme's JavaScript is organized into modules in `assets/js/main.js`. It includes:

- Mobile menu functionality
- Sticky header behavior
- Dark mode toggle
- Cookie notice handling
- Lazy loading for images
- Smooth scrolling

### Adding Custom Functionality

To add custom functionality:

1. Create a new function in `inc/template-functions.php`
2. Hook it into WordPress using the appropriate action or filter

## Credits

- Theme developed by WillsX Team
- Icons from [Feather Icons](https://feathericons.com/)
- Font: Inter from Google Fonts

## License

This theme is licensed under the GPL v2 or later.

## Support

For support, please contact the WillsX development team at support@willsx.com. 