# WillsX Deployment Guide

## Development/Staging Server Setup

### 1. Choose a Hosting Provider

Based on the implementation plan, select one of the recommended managed WordPress hosting providers:

- **WP Engine**: Offers dedicated staging environments and Git deployment
- **Kinsta**: Provides excellent performance and staging functionality
- **20i**: UK-based hosting with good WordPress support

For the development/staging environment, we recommend:

- **WP Engine** for its robust staging features and Git integration
- **Development Plan**: $30-50/month (sufficient for staging)

### 2. Initial Server Setup

1. Sign up for the selected hosting provider
2. Create a new WordPress installation (select "Staging" if available)
3. Set up SSL certificate (usually automatic with managed hosting)
4. Configure DNS settings in CloudFlare:
   - Create a subdomain (e.g., `staging.willsx.com`)
   - Point the subdomain to your hosting provider's nameservers
   - Enable Cloudflare proxy for additional security

### 3. WordPress Configuration

1. Install WordPress core (usually automatic with managed hosting)
2. Configure security settings:
   ```php
   // Add to wp-config.php
   define('WP_DEBUG', false);
   define('DISALLOW_FILE_EDIT', true);
   define('WP_AUTO_UPDATE_CORE', false);
   ```
3. Set custom database prefix (not default 'wp_')
4. Configure proper file permissions:
   - Directories: 755
   - Files: 644
5. Set SEO-friendly permalink structure
6. Disable user registration
7. Set timezone to UK time

### 4. Git Deployment Setup

#### Option A: Direct Git Deployment (WP Engine, Kinsta)

1. In your hosting provider dashboard, locate Git deployment settings
2. Add your GitHub repository as a deployment source
3. Configure the branch to deploy (typically `staging` or `development`)
4. Set up deployment hooks:
   ```
   https://yourhosting.com/deployment-hook-url
   ```
5. Add this webhook to your GitHub repository settings

#### Option B: GitHub Actions Deployment

1. Create `.github/workflows/staging-deploy.yml` in your repository:
   ```yaml
   name: Deploy to Staging

   on:
     push:
       branches: [ staging ]

   jobs:
     deploy:
       runs-on: ubuntu-latest
       steps:
         - uses: actions/checkout@v3
         - name: Deploy to Staging
           uses: SamKirkland/FTP-Deploy-Action@v4.3.4
           with:
             server: ${{ secrets.FTP_SERVER }}
             username: ${{ secrets.FTP_USERNAME }}
             password: ${{ secrets.FTP_PASSWORD }}
             server-dir: /public_html/
   ```
2. Add repository secrets in GitHub:
   - FTP_SERVER
   - FTP_USERNAME
   - FTP_PASSWORD

### 5. Database and Content Migration

1. For initial setup, use the hosting provider's tools to create a fresh WordPress installation
2. For subsequent updates:
   - Use a plugin like WP Migrate DB Pro to sync databases
   - Use Git for code deployment
   - Use a tool like WP-CLI for database operations

### 6. Post-Deployment Configuration

1. Install and configure essential plugins:
   - Wordfence (Security)
   - Rank Math or Yoast SEO (SEO)
   - WP Rocket (Performance)
   - Advanced Custom Fields Pro
   - Other plugins as needed
2. Set up development-specific configurations:
   - Add noindex meta tags to prevent search indexing
   - Set up password protection for the staging site
   - Configure staging-specific environment variables

### 7. Testing and QA

1. Create a testing checklist for the staging environment:
   - Cross-browser compatibility
   - Mobile responsiveness
   - Form submission testing
   - Integration functionality
   - Performance testing

### 8. Maintenance and Updates

1. Set up automated backups
2. Configure update schedules for WordPress core, themes, and plugins
3. Implement a process for pushing changes from staging to production

## Local Development Environment

For local development before pushing to staging:

1. Install Local by Flywheel or DevKinsta
2. Create a new local WordPress site
3. Clone your GitHub repository into the site's directory
4. Import a database snapshot from staging if needed
5. Configure local development settings

## Production Deployment

Once testing on the staging server is complete:

1. Create a deployment plan
2. Schedule maintenance window
3. Backup production environment
4. Deploy changes from staging to production
5. Verify functionality in production
6. Monitor for any issues

## Rollback Plan

In case of deployment issues:

1. Restore from backup
2. Revert Git commits if necessary
3. Document issues for future prevention 