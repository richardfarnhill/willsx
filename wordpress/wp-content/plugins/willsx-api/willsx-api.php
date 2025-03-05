<?php
/**
 * Plugin Name: WillsX API
 * Description: Custom API endpoints and functionality for WillsX headless implementation
 * Version: 1.0.0
 * Author: WillsX Team
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WILLSX_API_VERSION', '1.0.0');
define('WILLSX_API_PATH', plugin_dir_path(__FILE__));
define('WILLSX_API_URL', plugin_dir_url(__FILE__));

// Include core files
require_once WILLSX_API_PATH . 'includes/class-willsx-api.php';
require_once WILLSX_API_PATH . 'includes/class-willsx-partners.php';
require_once WILLSX_API_PATH . 'includes/class-willsx-auth.php';
require_once WILLSX_API_PATH . 'includes/class-willsx-will-process.php';
require_once WILLSX_API_PATH . 'includes/class-willsx-bookings.php';

// Initialize the plugin
function willsx_api_init() {
    $willsx_api = new WillsX_API();
    $willsx_api->init();
}
add_action('plugins_loaded', 'willsx_api_init');

// Activation hook
register_activation_hook(__FILE__, 'willsx_api_activate');
function willsx_api_activate() {
    // Create necessary database tables
    // Set up initial options
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'willsx_api_deactivate');
function willsx_api_deactivate() {
    // Clean up
    flush_rewrite_rules();
}
