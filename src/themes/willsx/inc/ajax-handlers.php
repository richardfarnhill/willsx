<?php
/**
 * AJAX Handler Functions
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save partner branding settings
 */
function willsx_save_partner_branding() {
    // Check nonce
    if (!check_ajax_referer('willsx_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Check user capabilities
    if (!current_user_can('partner')) {
        wp_send_json_error('Insufficient permissions');
    }

    $partner_id = willsx_get_current_partner_id();
    if (!$partner_id) {
        wp_send_json_error('Partner not found');
    }

    // Handle logo upload
    if (!empty($_FILES['partner_logo'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $logo_id = media_handle_upload('partner_logo', 0);
        if (!is_wp_error($logo_id)) {
            update_post_meta($partner_id, '_partner_logo_id', $logo_id);
        }
    }

    // Update colors
    if (isset($_POST['primary_color'])) {
        update_post_meta($partner_id, '_partner_primary_color', sanitize_hex_color($_POST['primary_color']));
    }
    if (isset($_POST['secondary_color'])) {
        update_post_meta($partner_id, '_partner_secondary_color', sanitize_hex_color($_POST['secondary_color']));
    }
    if (isset($_POST['accent_color'])) {
        update_post_meta($partner_id, '_partner_accent_color', sanitize_hex_color($_POST['accent_color']));
    }

    // Update font family
    if (isset($_POST['font_family'])) {
        update_post_meta($partner_id, '_partner_font_family', sanitize_text_field($_POST['font_family']));
    }

    wp_send_json_success('Branding settings saved successfully');
}
add_action('wp_ajax_willsx_save_partner_branding', 'willsx_save_partner_branding');

/**
 * Save partner settings
 */
function willsx_save_partner_settings() {
    // Check nonce
    if (!check_ajax_referer('willsx_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Check user capabilities
    if (!current_user_can('partner')) {
        wp_send_json_error('Insufficient permissions');
    }

    $partner_id = willsx_get_current_partner_id();
    if (!$partner_id) {
        wp_send_json_error('Partner not found');
    }

    // Update contact information
    if (isset($_POST['contact_name'])) {
        update_post_meta($partner_id, '_partner_contact_name', sanitize_text_field($_POST['contact_name']));
    }
    if (isset($_POST['contact_email'])) {
        update_post_meta($partner_id, '_partner_contact_email', sanitize_email($_POST['contact_email']));
    }
    if (isset($_POST['contact_phone'])) {
        update_post_meta($partner_id, '_partner_contact_phone', sanitize_text_field($_POST['contact_phone']));
    }

    // Update notification preferences
    $notifications = array(
        'notify_new_client' => isset($_POST['notify_new_client']),
        'notify_will_complete' => isset($_POST['notify_will_complete']),
        'notify_analytics' => isset($_POST['notify_analytics'])
    );
    update_post_meta($partner_id, '_partner_notifications', $notifications);

    // Update webhook URL
    if (isset($_POST['webhook_url'])) {
        update_post_meta($partner_id, '_partner_webhook_url', esc_url_raw($_POST['webhook_url']));
    }

    wp_send_json_success('Settings saved successfully');
}
add_action('wp_ajax_willsx_save_partner_settings', 'willsx_save_partner_settings');

/**
 * Generate new API key for partner
 */
function willsx_generate_api_key() {
    // Check nonce
    if (!check_ajax_referer('willsx_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Check user capabilities
    if (!current_user_can('partner')) {
        wp_send_json_error('Insufficient permissions');
    }

    $partner_id = willsx_get_current_partner_id();
    if (!$partner_id) {
        wp_send_json_error('Partner not found');
    }

    // Generate new API key
    $api_key = wp_generate_password(32, false);
    update_post_meta($partner_id, '_partner_api_key', $api_key);

    wp_send_json_success(array('api_key' => $api_key));
}
add_action('wp_ajax_willsx_generate_api_key', 'willsx_generate_api_key');

/**
 * Get partner analytics data
 */
function willsx_get_partner_analytics() {
    // Check nonce
    if (!check_ajax_referer('willsx_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Check user capabilities
    if (!current_user_can('partner')) {
        wp_send_json_error('Insufficient permissions');
    }

    $partner_id = willsx_get_current_partner_id();
    if (!$partner_id) {
        wp_send_json_error('Partner not found');
    }

    // Get date range
    $days = isset($_GET['days']) ? intval($_GET['days']) : 30;
    $end_date = current_time('Y-m-d');
    $start_date = date('Y-m-d', strtotime("-{$days} days"));

    // Get analytics data
    $analytics = array(
        'client_growth' => willsx_get_client_growth($partner_id, $start_date, $end_date),
        'revenue' => willsx_get_revenue_data($partner_id, $start_date, $end_date),
        'completion_rate' => willsx_get_completion_rate($partner_id),
        'traffic_sources' => willsx_get_traffic_sources($partner_id, $start_date, $end_date)
    );

    wp_send_json_success($analytics);
}
add_action('wp_ajax_willsx_get_partner_analytics', 'willsx_get_partner_analytics');

/**
 * Get client growth data
 */
function willsx_get_client_growth($partner_id, $start_date, $end_date) {
    global $wpdb;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(post_date) as date, COUNT(*) as count
        FROM {$wpdb->posts}
        WHERE post_type = 'will'
        AND post_status IN ('publish', 'draft')
        AND post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )
        AND post_date BETWEEN %s AND %s
        GROUP BY DATE(post_date)
        ORDER BY date ASC",
        $partner_id,
        $start_date,
        $end_date
    ));

    $data = array();
    foreach ($results as $row) {
        $data[] = array(
            'date' => $row->date,
            'count' => intval($row->count)
        );
    }

    return $data;
}

/**
 * Get revenue data
 */
function willsx_get_revenue_data($partner_id, $start_date, $end_date) {
    global $wpdb;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(post_date) as date, SUM(meta_value) as revenue
        FROM {$wpdb->posts} p
        JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = 'will'
        AND p.post_status = 'publish'
        AND pm.meta_key = '_will_price'
        AND p.post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )
        AND p.post_date BETWEEN %s AND %s
        GROUP BY DATE(post_date)
        ORDER BY date ASC",
        $partner_id,
        $start_date,
        $end_date
    ));

    $data = array();
    foreach ($results as $row) {
        $data[] = array(
            'date' => $row->date,
            'revenue' => floatval($row->revenue)
        );
    }

    return $data;
}

/**
 * Get completion rate data
 */
function willsx_get_completion_rate($partner_id) {
    global $wpdb;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT post_status, COUNT(*) as count
        FROM {$wpdb->posts}
        WHERE post_type = 'will'
        AND post_status IN ('publish', 'draft', 'auto-draft')
        AND post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )
        GROUP BY post_status",
        $partner_id
    ));

    $data = array(
        'completed' => 0,
        'in_progress' => 0,
        'not_started' => 0
    );

    foreach ($results as $row) {
        switch ($row->post_status) {
            case 'publish':
                $data['completed'] = intval($row->count);
                break;
            case 'draft':
                $data['in_progress'] = intval($row->count);
                break;
            case 'auto-draft':
                $data['not_started'] = intval($row->count);
                break;
        }
    }

    return $data;
}

/**
 * Get traffic sources data
 */
function willsx_get_traffic_sources($partner_id, $start_date, $end_date) {
    // This would typically integrate with Google Analytics or similar
    // For now, return sample data
    return array(
        'direct' => 40,
        'organic' => 30,
        'referral' => 20,
        'social' => 10
    );
}

/**
 * Get partner clients
 */
function willsx_get_partner_clients() {
    // Check nonce
    if (!check_ajax_referer('willsx_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Check user capabilities
    if (!current_user_can('partner')) {
        wp_send_json_error('Insufficient permissions');
    }

    $partner_id = willsx_get_current_partner_id();
    if (!$partner_id) {
        wp_send_json_error('Partner not found');
    }

    // Get search parameters
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 10;

    // Get clients
    $args = array(
        'post_type' => 'will',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'meta_query' => array(
            array(
                'key' => '_partner_id',
                'value' => $partner_id
            )
        )
    );

    if ($search) {
        $args['s'] = $search;
    }

    if ($status) {
        $args['post_status'] = $status;
    }

    $query = new WP_Query($args);
    $clients = array();

    foreach ($query->posts as $post) {
        $client = array(
            'id' => $post->ID,
            'name' => get_post_meta($post->ID, '_client_name', true),
            'email' => get_post_meta($post->ID, '_client_email', true),
            'status' => $post->post_status,
            'last_updated' => $post->post_modified
        );
        $clients[] = $client;
    }

    $response = array(
        'clients' => $clients,
        'total' => $query->found_posts,
        'pages' => ceil($query->found_posts / $per_page)
    );

    wp_send_json_success($response);
}
add_action('wp_ajax_willsx_get_partner_clients', 'willsx_get_partner_clients');

/**
 * Send client reminder
 */
function willsx_send_client_reminder() {
    // Check nonce
    if (!check_ajax_referer('willsx_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }

    // Check user capabilities
    if (!current_user_can('partner')) {
        wp_send_json_error('Insufficient permissions');
    }

    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
    if (!$client_id) {
        wp_send_json_error('Client ID not provided');
    }

    $client_email = get_post_meta($client_id, '_client_email', true);
    if (!$client_email) {
        wp_send_json_error('Client email not found');
    }

    // Send reminder email
    $subject = 'Complete Your Will - Reminder';
    $message = 'This is a friendly reminder to complete your will. Click here to continue: ' . get_permalink($client_id);
    $headers = array('Content-Type: text/html; charset=UTF-8');

    if (wp_mail($client_email, $subject, $message, $headers)) {
        wp_send_json_success('Reminder sent successfully');
    } else {
        wp_send_json_error('Failed to send reminder');
    }
}
add_action('wp_ajax_willsx_send_client_reminder', 'willsx_send_client_reminder'); 