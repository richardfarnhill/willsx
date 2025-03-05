<?php
/**
 * Partner Role and Capabilities
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register partner role
 */
function willsx_register_partner_role() {
    add_role(
        'partner',
        __('Partner', 'willsx'),
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => true,
            'manage_wills' => true,
            'view_partner_dashboard' => true,
        )
    );
}
add_action('init', 'willsx_register_partner_role');

/**
 * Add partner capabilities
 */
function willsx_add_partner_caps() {
    $partner = get_role('partner');
    
    if ($partner) {
        $partner->add_cap('manage_wills');
        $partner->add_cap('view_partner_dashboard');
    }
}
add_action('admin_init', 'willsx_add_partner_caps');

/**
 * Check if user has partner capabilities
 */
function willsx_user_has_partner_cap($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $user = get_userdata($user_id);
    return $user && $user->has_cap('view_partner_dashboard');
}

/**
 * Restrict partner dashboard access
 */
function willsx_restrict_partner_dashboard() {
    if (is_page_template('template-partner-dashboard.php') && !willsx_user_has_partner_cap()) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'willsx_restrict_partner_dashboard'); 