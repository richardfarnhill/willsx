# WillsX Partner System Guide

## Overview
The WillsX Partner System allows law firms and estate planners to offer will writing services through their own branded portal. This guide explains how to set up and manage partner accounts.

## Partner Features
- Custom branded portal
- Client management
- Revenue tracking
- Analytics dashboard
- API integration

## Setting Up a Partner
1. Go to WillsX Dashboard > Manage Partners
2. Click "Add New Partner"
3. Fill in partner details:
   - Company Name
   - Contact Information
   - API Credentials
4. Upload partner logo
5. Set brand colors
6. Configure notification preferences

## Partner Dashboard
### Overview
The partner dashboard provides:
- Quick statistics
- Client list
- Revenue reports
- Activity log

### Branding Options
- Logo upload
- Primary color
- Secondary color
- Accent color
- Font selection

### Client Management
- View all clients
- Track will progress
- Send notifications
- Download reports

### Analytics
- Revenue tracking
- Conversion rates
- Client growth
- Will completion rates

## API Integration
### Authentication
```php
$api_key = get_post_meta($partner_id, '_partner_api_key', true);
$api_secret = get_post_meta($partner_id, '_partner_api_secret', true);
```

### Endpoints
- `/api/v1/clients` - Client management
- `/api/v1/wills` - Will management
- `/api/v1/analytics` - Analytics data
- `/api/v1/notifications` - Notification system

## Webhooks
Partners can receive real-time updates through webhooks:
- Will completion
- New client registration
- Payment processing
- Document updates

## Support
For partner support, contact partners@willsx.com 