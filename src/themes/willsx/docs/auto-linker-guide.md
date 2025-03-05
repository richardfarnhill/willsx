# WillsX Auto-Linker Guide

## Overview
The Auto-Linker system automatically creates links for legal terms and phrases in your content. This guide explains how to configure and use the auto-linking feature.

## Features
- Automatic keyword linking
- Custom link destinations
- Link tracking
- Exclusion rules
- Link limits per page

## Configuration
### Accessing Settings
1. Go to WillsX Dashboard
2. Click "Auto Linker Settings"
3. Configure your preferences

### General Settings
- Maximum links per page
- Link opening behavior (new tab/same window)
- Link styling options
- Tracking options

### Keywords
#### Adding Keywords
1. Click "Add New Keyword"
2. Enter the keyword or phrase
3. Set the destination URL
4. Configure additional options:
   - Case sensitivity
   - Exact match only
   - Priority level

#### Managing Keywords
- Edit existing keywords
- Delete keywords
- Import/export keyword lists
- Bulk actions

### Exclusion Rules
#### Content Exclusions
- Headings
- Tables
- Code blocks
- Custom HTML elements

#### Page Exclusions
- Specific pages
- Post types
- Categories
- Custom post types

## Usage
### Automatic Linking
The system automatically processes content and adds links based on your settings.

### Manual Override
Use the `[noautolink]` shortcode to prevent auto-linking in specific sections:
```php
[noautolink]
Content that should not be auto-linked
[/noautolink]
```

### Link Attributes
Default link attributes:
```html
<a href="URL" class="willsx-autolink" data-keyword="KEYWORD">KEYWORD</a>
```

## Analytics
### Link Performance
- Click tracking
- Popular keywords
- Conversion rates
- User engagement

### Reports
- Daily/weekly/monthly statistics
- Export options
- Custom date ranges
- Detailed analytics

## Best Practices
1. Start with important keywords
2. Avoid overlapping terms
3. Use exact matches for legal terms
4. Monitor link density
5. Regular review and updates

## Support
For auto-linker support, contact support@willsx.com 