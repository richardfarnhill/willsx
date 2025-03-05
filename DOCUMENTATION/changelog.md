# WillsX Development Changelog

## v0.2.0 - Core Functionality Implementation - [2024-07-12]

### Added
- Created custom admin dashboard with theme management interface
- Implemented partner custom post type with detailed meta fields
- Added blog post auto-linker functionality with keyword management
- Implemented dark mode toggle with customizable settings
- Created necessary directory structure for theme assets

### Changed
- Enhanced partner system with custom post type integration
- Updated functions.php to include new core functionality
- Improved co-branding functionality with better partner data handling

### Fixed
- N/A

## v0.1.5 - Theme Function Fix 2 - [2024-07-11]

### Added
- N/A

### Changed
- Renamed duplicate `willsx_add_image_sizes` function to `willsx_add_theme_image_sizes` in theme-functions.php

### Fixed
- Resolved fatal error caused by duplicate function declaration for image sizes

## v0.1.4 - Automated Theme Sync - [2024-07-11]

### Added
- Added Git hooks for automatic theme syncing
- Created post-commit hook to sync theme files after commits
- Created pre-push hook to sync theme files before pushes
- Added hook installation script and batch file
- Added standalone theme sync script and batch file

### Changed
- Updated project rules to include information about Git hooks
- Added new user shortcut for installing Git hooks

### Fixed
- N/A

## v0.1.3 - Theme Function Fix - [2024-07-11]

### Added
- N/A

### Changed
- Renamed duplicate `willsx_body_classes` function to `willsx_custom_body_classes` in theme-functions.php

### Fixed
- Resolved fatal error caused by duplicate function declaration

## v0.1.2 - Project Rules Enhancement - [2024-07-11]

### Added
- Added theme sync command to project rules
- Created shortcut for syncing theme files to local WordPress installation
- Implemented post-push workflow to ensure local WordPress stays in sync with repository

### Changed
- Updated version control documentation with sync requirements

### Fixed
- N/A

## v0.1.1 - Deployment Documentation - [2024-07-10]

### Added
- Created comprehensive deployment guide for development/staging environments
- Added detailed instructions for Git-based deployment workflows
- Included local development environment setup guidelines

### Changed
- N/A

### Fixed
- N/A

## v0.1.0 - Project Initialization - [2024-07-10]

### Added
- Created initial project documentation 
- Set up project directory structure
- Added implementation plan with detailed roadmap
- Created changelog for tracking development progress

### Changed
- N/A (Initial setup)

### Fixed
- N/A (Initial setup)

## Development Roadmap

### Upcoming in v0.3.0
- Partner system implementation
- Booking system integration 
- Will instruction process placeholder
- Custom user roles and permissions

### Upcoming in v0.4.0
- External service integrations (HubSpot, ClickUp, etc.)
- Analytics and tracking configuration
- GDPR compliance implementation
- Automation workflow setup 