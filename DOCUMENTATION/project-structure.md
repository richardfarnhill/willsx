# WillsX Project Structure

## Overview

The new WillsX architecture uses a headless approach with two main components:

1. **WordPress Backend** - For content management, partner admin, and API endpoints
2. **Next.js Frontend** - For user-facing interfaces and complex form handling

## Directory Structure

```
WillsX/
├── wordpress/                    # WordPress installation
│   ├── wp-content/
│   │   ├── plugins/
│   │   │   └── willsx-api/       # Custom API plugin
│   │   └── themes/
│   │       └── willsx-admin/     # Admin theme (minimal)
│   └── ...
│
├── frontend/                     # Next.js application
│   ├── src/
│   │   ├── components/           # React components
│   │   ├── pages/                # Page components
│   │   ├── styles/               # CSS/SCSS files
│   │   ├── lib/                  # Utility functions
│   │   ├── hooks/                # Custom React hooks
│   │   ├── api/                  # API integration layer
│   │   └── contexts/             # React contexts
│   └── ...
│
└── docs/                         # Project documentation
    ├── technical/                # Developer documentation
    └── stakeholder/              # Non-technical documentation
```

## Key Components

### WordPress Backend

1. **willsx-api Plugin**:
   - REST API endpoints for all data
   - Partner management system
   - Authentication handling
   - External integrations (HubSpot, ClickUp)

2. **willsx-admin Theme**:
   - Streamlined admin interface
   - Partner management dashboard
   - Content management

### Next.js Frontend

1. **Core Pages**:
   - Dynamic landing pages for partners
   - Will instruction process
   - Booking system
   - User dashboard

2. **Component Library**:
   - UI components matching design system
   - Form components with validation
   - Navigation elements
   - Partner branding components

3. **API Layer**:
   - WordPress API connection
   - Authentication handling
   - Form state management
   - Error handling
