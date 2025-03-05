# WillsX Dark Mode Guide

## Overview
WillsX includes a built-in dark mode feature that allows users to switch between light and dark color schemes. This guide explains how to configure and customize the dark mode functionality.

## Features
- Automatic system preference detection
- Manual toggle switch
- Customizable color palette
- Persistent user preferences
- Smooth transitions

## Configuration
### Theme Settings
1. Navigate to WillsX Dashboard
2. Select "Dark Mode Settings"
3. Configure the following options:
   - Enable/disable dark mode
   - Default mode (light/dark/system)
   - Transition duration
   - Custom colors

### Color Customization
#### Default Dark Mode Colors
```css
:root[data-theme="dark"] {
  --background: #121212;
  --text: #ffffff;
  --primary: #bb86fc;
  --secondary: #03dac6;
  --accent: #cf6679;
  --surface: #1e1e1e;
}
```

#### Customizing Colors
1. Go to Dark Mode Settings
2. Click "Customize Colors"
3. Adjust the following:
   - Background color
   - Text color
   - Primary color
   - Secondary color
   - Accent color
   - Surface colors

## Implementation
### Toggle Switch
Add the dark mode toggle to any template:
```php
<?php willsx_dark_mode_toggle(); ?>
```

### JavaScript API
```javascript
// Check current mode
const isDarkMode = WillsX.isDarkMode();

// Toggle dark mode
WillsX.toggleDarkMode();

// Set specific mode
WillsX.setDarkMode(true); // Enable dark mode
WillsX.setDarkMode(false); // Disable dark mode
```

### CSS Classes
- `.dark-mode`: Applied to body when dark mode is active
- `.light-mode`: Applied to body when light mode is active
- `.dark-mode-transition`: Applied during mode transitions

## Best Practices
1. Test all components in both modes
2. Use CSS custom properties for colors
3. Provide sufficient contrast ratios
4. Consider image alternatives for dark mode
5. Maintain consistent branding

## Troubleshooting
### Common Issues
1. Flickering during page load
   - Solution: Add `data-theme` attribute server-side

2. Inconsistent colors
   - Solution: Check CSS variable usage

3. Toggle not working
   - Solution: Verify JavaScript initialization

### Debug Mode
Enable debug mode to log dark mode events:
```javascript
WillsX.setDebug(true);
```

## Support
For dark mode support, contact support@willsx.com 