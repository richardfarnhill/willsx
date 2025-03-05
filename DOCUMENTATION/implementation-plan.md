# WillsX WordPress Implementation Plan

## Project Overview
WillsX is a professional WordPress site for will and LPA services in England and Wales, designed to reach non-traditional customers. The site will feature co-branded partner landing pages, a modern UI inspired by Sxxx.com, and both online will instruction processes and guided video call services.

## 1. Technical Foundation

### Hosting & Environment
- **Hosting recommendation**: Managed WordPress hosting with a UK provider (options: WP Engine, Kinsta, 20i)
- **Server requirements**: PHP 8.1+, MySQL 8.0+, SSL certificate
- **Development environment**: Local staging using Local by Flywheel or DevKinsta
- **Version control**: GitHub with branch workflow as specified in project-rules.md
- **Deployment**: GitHub Actions for automated deployment from staging to production

### Domain & DNS
- Domain to be finalized 
- CloudFlare for DNS management and additional security layer
- Configure for optimal security and performance

### Backup Solution
- Primary backup via hosting provider
- Secondary external backup using UpdraftPlus Premium to cloud storage
- Daily automated backups with 30-day retention

## 2. WordPress Core Configuration

### Installation & Setup
- Clean WordPress installation
- Define security constants in wp-config.php
- Optimize database prefix (not using default 'wp_')
- Configure proper file permissions

### Core Settings
- SEO-friendly permalink structure
- User registration disabled
- Comment moderation enabled
- Timezone set to UK time
- Media settings optimized for performance

### User Roles & Permissions
- Custom user roles:
  - Partner (limited dashboard access)
  - Legal Advisor (appointment booking access)
  - Content Manager (blog management only)
  - Administrator (full access)

## 3. Custom Theme Development

### Design Foundation
- Create custom theme based on Sxxx.com visual style
- Implement using block-based theme architecture (Full Site Editing)
- Develop with mobile-first responsive approach
- Accessibility features including light/dark mode toggle

### Key Theme Components
- Custom header with dynamic navigation
- Hero sections with adaptable co-branding
- Service cards and CTAs matching Sxxx aesthetic
- Custom footer with dynamic widget areas
- Blog layout with featured posts section

### Frontend Technologies
- HTML5 semantic markup
- CSS with SCSS preprocessing
- Modern JavaScript (ES6+) with minimal dependencies
- SVG for icons and lightweight graphics
- Performance optimization for Core Web Vitals

## 4. Core Functionality

### Co-branded Partner System
- Custom post type for Partners with:
  - Logo upload
  - Color scheme settings
  - URL/company info storage
  - Referral fee configuration
- Dynamic landing page generator
- URL parameter tracking system
- Partner dashboard with conversion metrics

### Booking System
- Integration with Calendly or custom booking solution
- Round-robin advisor assignment
- Video integration with Google Meet/Teams
- Custom form fields for client data collection
- Initial health assessment routing
- Payment processing integration

### Online Will Instruction Process
- Multi-step form with progress saving
- Client authentication system
- Supabase integration for data storage
- Conditional logic based on marital status
- Referral system with discount codes

### Blog & Content Platform
- Enhanced WordPress editor with custom blocks
- Auto-linking system for internal content references
- Featured content management
- SEO optimization tools
- Social sharing integration

## 5. Plugin Selection & Configuration

### Essential Plugins
- **Security**: Wordfence Premium
- **SEO**: Rank Math Pro or Yoast SEO Premium
- **Performance**: WP Rocket
- **Forms**: Gravity Forms or Formidable Pro
- **Backup**: UpdraftPlus Premium

### Additional Plugins
- Advanced Custom Fields Pro (for partner fields and settings)
- WooCommerce (for payment processing)
- WPML or TranslatePress (multilingual support)
- Custom Analytics Dashboard
- Cookie Notice & Consent Solution

## 6. Integrations

### External Services
- **CRM**: HubSpot integration
- **Project Management**: ClickUp via API
- **Email Marketing**: ConvertKit or Beehiiv
- **Payment Processing**: Square and/or GoCardless
- **Video Conferencing**: Google Meet and/or Microsoft Teams

### Automation Workflows
- Make.com (formerly Integromat) workflows for:
  - Lead nurturing sequences
  - Partner notification system
  - Booking confirmations
  - Payment processing
  - Data synchronization between systems

## 7. Analytics & Tracking

### Implementation
- Google Tag Manager setup with consent mode
- Google Analytics 4 configuration 
- Conversion tracking for key actions
- Partner referral attribution
- Demographics tracking
- Goal setup for conversion funnels

### Partner Dashboards
- Custom analytics dashboard for partners
- Conversion metrics visualization
- Financial reporting for referral fees
- CSV/PDF export functionality
- Automated reporting schedule

## 8. GDPR & Compliance

### Data Protection
- Privacy policy generator and management
- Consent management system (Google Consent Mode v2)
- Data encryption for sensitive information
- Data retention policy implementation
- Subject access request handling

### Region Detection
- Geolocation service for region detection
- Legal disclaimer for non-England/Wales visitors
- Cookie consent banner with granular options
- Database encryption for personal data

## 9. Development Workflow

### Version Control
- GitHub repository setup with branch protection
- Feature branch workflow as specified in project-rules
- Semantic versioning for releases (v{MAJOR}.{MINOR}.{PATCH})
- Standardized commit messages

### Development Phases
1. **Foundation Phase**:
   - Core WordPress setup
   - Custom theme development
   - Basic plugin configuration
   - Development environment setup

2. **Functionality Phase**:
   - Partner system implementation
   - Booking system integration
   - Will instruction process (placeholder)
   - User roles and permissions

3. **Integration Phase**:
   - External service connections
   - Analytics and tracking setup
   - Automation workflow configuration
   - GDPR compliance implementation

4. **Testing & Optimization Phase**:
   - Performance testing and optimization
   - Cross-browser compatibility testing
   - Security scanning and hardening
   - User testing and feedback integration

5. **Launch Phase**:
   - Final QA and testing
   - Content population
   - SEO verification
   - Production deployment

### Timeline Estimation
- Foundation Phase: 2-3 weeks
- Functionality Phase: 3-4 weeks
- Integration Phase: 2-3 weeks
- Testing & Optimization Phase: 1-2 weeks
- Launch Phase: 1 week

Total project timeline: Approximately 9-13 weeks

## 10. Ongoing Maintenance & Updates

### Regular Maintenance
- WordPress core updates
- Plugin updates and compatibility testing
- Theme updates and improvements
- Security monitoring and patching
- Performance optimization

### Content Strategy Support
- Blog post optimization
- SEO monitoring and adjustment
- Analytics review and reporting
- A/B testing of key conversion paths

### Future Enhancements
- Full online will creation process
- Enhanced partner integrations
- Additional language support
- Advanced analytics dashboards
- Mobile app development consideration 