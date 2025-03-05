<?php
/**
 * Partner Helper Functions
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get current partner ID
 *
 * @return int|false Partner ID or false if not found
 */
function willsx_get_current_partner_id() {
    $user_id = get_current_user_id();
    $args = array(
        'post_type' => 'partner',
        'meta_query' => array(
            array(
                'key' => '_partner_user_id',
                'value' => $user_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => 1
    );
    
    $partner_query = new WP_Query($args);
    if ($partner_query->have_posts()) {
        $partner_query->the_post();
        $partner_id = get_the_ID();
        wp_reset_postdata();
        return $partner_id;
    }
    
    return false;
}

/**
 * Check if user is a partner
 *
 * @param int|null $user_id Optional user ID. Defaults to current user.
 * @return bool Whether the user is a partner
 */
function willsx_is_partner($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return false;
    }

    $user = get_user_by('id', $user_id);
    return $user && in_array('partner', (array) $user->roles);
}

/**
 * Get partner branding
 *
 * @param int|null $partner_id Optional partner ID. Defaults to current partner.
 * @return array Partner branding settings
 */
function willsx_get_partner_branding($partner_id = null) {
    if (!$partner_id) {
        $partner_id = willsx_get_current_partner_id();
    }

    if (!$partner_id) {
        return array();
    }

    return array(
        'logo_id' => get_post_meta($partner_id, '_partner_logo_id', true),
        'primary_color' => get_post_meta($partner_id, '_partner_primary_color', true),
        'secondary_color' => get_post_meta($partner_id, '_partner_secondary_color', true),
        'accent_color' => get_post_meta($partner_id, '_partner_accent_color', true),
        'font_family' => get_post_meta($partner_id, '_partner_font_family', true)
    );
}

/**
 * Get partner settings
 *
 * @param int|null $partner_id Optional partner ID. Defaults to current partner.
 * @return array Partner settings
 */
function willsx_get_partner_settings($partner_id = null) {
    if (!$partner_id) {
        $partner_id = willsx_get_current_partner_id();
    }

    if (!$partner_id) {
        return array();
    }

    return array(
        'contact_name' => get_post_meta($partner_id, '_partner_contact_name', true),
        'contact_email' => get_post_meta($partner_id, '_partner_contact_email', true),
        'contact_phone' => get_post_meta($partner_id, '_partner_contact_phone', true),
        'notifications' => get_post_meta($partner_id, '_partner_notifications', true),
        'webhook_url' => get_post_meta($partner_id, '_partner_webhook_url', true),
        'api_key' => get_post_meta($partner_id, '_partner_api_key', true)
    );
}

/**
 * Get partner statistics
 *
 * @param int|null $partner_id Optional partner ID. Defaults to current partner.
 * @return array Partner statistics
 */
function willsx_get_partner_stats($partner_id = null) {
    if (!$partner_id) {
        $partner_id = willsx_get_current_partner_id();
    }

    if (!$partner_id) {
        return array();
    }

    global $wpdb;

    // Get total clients
    $total_clients = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'will'
        AND post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )",
        $partner_id
    ));

    // Get active wills
    $active_wills = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'will'
        AND post_status = 'publish'
        AND post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )",
        $partner_id
    ));

    // Get total revenue
    $total_revenue = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(meta_value)
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
        )",
        $partner_id
    ));

    // Get conversion rate
    $started_wills = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$wpdb->posts}
        WHERE post_type = 'will'
        AND post_status IN ('draft', 'auto-draft')
        AND post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )",
        $partner_id
    ));

    $conversion_rate = $started_wills > 0 ? ($active_wills / $started_wills) * 100 : 0;

    return array(
        'total_clients' => intval($total_clients),
        'active_wills' => intval($active_wills),
        'total_revenue' => floatval($total_revenue),
        'conversion_rate' => round($conversion_rate, 1)
    );
}

/**
 * Get partner recent activity
 *
 * @param int|null $partner_id Optional partner ID. Defaults to current partner.
 * @param int $limit Number of activities to return
 * @return array Recent activities
 */
function willsx_get_partner_activity($partner_id = null, $limit = 5) {
    if (!$partner_id) {
        $partner_id = willsx_get_current_partner_id();
    }

    if (!$partner_id) {
        return array();
    }

    global $wpdb;

    $activities = $wpdb->get_results($wpdb->prepare(
        "SELECT p.ID, p.post_title, p.post_status, p.post_modified,
            pm1.meta_value as client_name,
            pm2.meta_value as activity_type
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_client_name'
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_activity_type'
        WHERE p.post_type = 'will'
        AND p.post_author IN (
            SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = '_partner_id'
            AND meta_value = %d
        )
        ORDER BY p.post_modified DESC
        LIMIT %d",
        $partner_id,
        $limit
    ));

    $formatted_activities = array();
    foreach ($activities as $activity) {
        $formatted_activities[] = array(
            'id' => $activity->ID,
            'client_name' => $activity->client_name,
            'type' => $activity->activity_type ?: $activity->post_status,
            'timestamp' => strtotime($activity->post_modified)
        );
    }

    return $formatted_activities;
}

/**
 * Format activity for display
 *
 * @param array $activity Activity data
 * @return string Formatted activity message
 */
function willsx_format_activity($activity) {
    $client_name = $activity['client_name'];
    $type = $activity['type'];
    $time_ago = human_time_diff($activity['timestamp'], current_time('timestamp'));

    switch ($type) {
        case 'publish':
            return sprintf(
                /* translators: 1: client name, 2: time ago */
                __('Will completed by %1$s (%2$s ago)', 'willsx'),
                $client_name,
                $time_ago
            );
        case 'draft':
            return sprintf(
                /* translators: 1: client name, 2: time ago */
                __('Will started by %1$s (%2$s ago)', 'willsx'),
                $client_name,
                $time_ago
            );
        case 'updated':
            return sprintf(
                /* translators: 1: client name, 2: time ago */
                __('Will updated by %1$s (%2$s ago)', 'willsx'),
                $client_name,
                $time_ago
            );
        default:
            return sprintf(
                /* translators: 1: activity type, 2: client name, 3: time ago */
                __('%1$s by %2$s (%3$s ago)', 'willsx'),
                ucfirst($type),
                $client_name,
                $time_ago
            );
    }
}

/**
 * Get partner clients
 *
 * @param int|null $partner_id Optional partner ID. Defaults to current partner.
 * @param array $args Query arguments
 * @return array Clients data
 */
function willsx_get_partner_clients($partner_id = null, $args = array()) {
    if (!$partner_id) {
        $partner_id = willsx_get_current_partner_id();
    }

    if (!$partner_id) {
        return array();
    }

    $defaults = array(
        'search' => '',
        'status' => '',
        'page' => 1,
        'per_page' => 10
    );

    $args = wp_parse_args($args, $defaults);

    $query_args = array(
        'post_type' => 'will',
        'posts_per_page' => $args['per_page'],
        'paged' => $args['page'],
        'meta_query' => array(
            array(
                'key' => '_partner_id',
                'value' => $partner_id
            )
        )
    );

    if ($args['search']) {
        $query_args['s'] = $args['search'];
    }

    if ($args['status']) {
        $query_args['post_status'] = $args['status'];
    }

    $query = new WP_Query($query_args);
    $clients = array();

    foreach ($query->posts as $post) {
        $clients[] = array(
            'id' => $post->ID,
            'name' => get_post_meta($post->ID, '_client_name', true),
            'email' => get_post_meta($post->ID, '_client_email', true),
            'status' => $post->post_status,
            'last_updated' => $post->post_modified
        );
    }

    return array(
        'clients' => $clients,
        'total' => $query->found_posts,
        'pages' => ceil($query->found_posts / $args['per_page'])
    );
}

/**
 * Send notification to partner
 *
 * @param int $partner_id Partner ID
 * @param string $type Notification type
 * @param array $data Notification data
 * @return bool Whether the notification was sent
 */
function willsx_notify_partner($partner_id, $type, $data = array()) {
    $settings = willsx_get_partner_settings($partner_id);
    $notifications = $settings['notifications'] ?: array();

    // Check if notification type is enabled
    if (!isset($notifications[$type]) || !$notifications[$type]) {
        return false;
    }

    $contact_email = $settings['contact_email'];
    if (!$contact_email) {
        return false;
    }

    $subject = '';
    $message = '';

    switch ($type) {
        case 'notify_new_client':
            $subject = __('New Client Registration', 'willsx');
            $message = sprintf(
                /* translators: %s: client name */
                __('A new client "%s" has registered through your portal.', 'willsx'),
                $data['client_name']
            );
            break;

        case 'notify_will_complete':
            $subject = __('Will Completed', 'willsx');
            $message = sprintf(
                /* translators: %s: client name */
                __('Client "%s" has completed their will.', 'willsx'),
                $data['client_name']
            );
            break;

        case 'notify_analytics':
            $subject = __('Weekly Analytics Report', 'willsx');
            $message = willsx_generate_analytics_report($partner_id);
            break;
    }

    if (!$subject || !$message) {
        return false;
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send email notification
    $email_sent = wp_mail($contact_email, $subject, $message, $headers);

    // Send webhook notification if configured
    if ($settings['webhook_url']) {
        wp_remote_post($settings['webhook_url'], array(
            'body' => array(
                'type' => $type,
                'data' => $data
            )
        ));
    }

    return $email_sent;
}

/**
 * Generate analytics report
 *
 * @param int $partner_id Partner ID
 * @return string HTML report
 */
function willsx_generate_analytics_report($partner_id) {
    $stats = willsx_get_partner_stats($partner_id);
    $end_date = current_time('Y-m-d');
    $start_date = date('Y-m-d', strtotime('-7 days'));
    
    $growth = willsx_get_client_growth($partner_id, $start_date, $end_date);
    $revenue = willsx_get_revenue_data($partner_id, $start_date, $end_date);
    $completion = willsx_get_completion_rate($partner_id);

    ob_start();
    ?>
    <h2><?php esc_html_e('Weekly Analytics Report', 'willsx'); ?></h2>
    
    <h3><?php esc_html_e('Overview', 'willsx'); ?></h3>
    <ul>
        <li><?php printf(__('Total Clients: %d', 'willsx'), $stats['total_clients']); ?></li>
        <li><?php printf(__('Active Wills: %d', 'willsx'), $stats['active_wills']); ?></li>
        <li><?php printf(__('Total Revenue: £%.2f', 'willsx'), $stats['total_revenue']); ?></li>
        <li><?php printf(__('Conversion Rate: %.1f%%', 'willsx'), $stats['conversion_rate']); ?></li>
    </ul>

    <h3><?php esc_html_e('Will Completion Status', 'willsx'); ?></h3>
    <ul>
        <li><?php printf(__('Completed: %d', 'willsx'), $completion['completed']); ?></li>
        <li><?php printf(__('In Progress: %d', 'willsx'), $completion['in_progress']); ?></li>
        <li><?php printf(__('Not Started: %d', 'willsx'), $completion['not_started']); ?></li>
    </ul>

    <p><?php esc_html_e('View detailed analytics in your partner dashboard.', 'willsx'); ?></p>
    <?php
    return ob_get_clean();
}

/**
 * Get partner clients count
 */
function willsx_get_partner_client_count($partner_id) {
    $args = array(
        'meta_key' => '_partner_id',
        'meta_value' => $partner_id,
        'count_total' => true,
        'fields' => 'ID'
    );
    
    $users = new WP_User_Query($args);
    return $users->get_total();
}

/**
 * Get partner completed wills count
 */
function willsx_get_partner_completed_wills_count($partner_id) {
    $args = array(
        'post_type' => 'will',
        'meta_query' => array(
            array(
                'key' => '_partner_id',
                'value' => $partner_id
            ),
            array(
                'key' => '_will_status',
                'value' => 'completed'
            )
        ),
        'posts_per_page' => -1
    );
    
    $wills = new WP_Query($args);
    return $wills->found_posts;
}

/**
 * Get partner revenue
 */
function willsx_get_partner_revenue($partner_id) {
    $revenue = get_post_meta($partner_id, '_total_revenue', true);
    return number_format((float)$revenue, 2);
}

/**
 * Get partner clients
 */
function willsx_get_partner_clients($partner_id) {
    $args = array(
        'meta_key' => '_partner_id',
        'meta_value' => $partner_id
    );
    
    $users = new WP_User_Query($args);
    return $users->get_results();
}

/**
 * Get client will status
 */
function willsx_get_client_will_status($user_id) {
    $args = array(
        'post_type' => 'will',
        'author' => $user_id,
        'posts_per_page' => 1
    );
    
    $will = new WP_Query($args);
    if ($will->have_posts()) {
        $will->the_post();
        $status = get_post_meta(get_the_ID(), '_will_status', true);
        wp_reset_postdata();
        return ucfirst($status);
    }
    
    return 'No Will';
}

/**
 * Get revenue data for charts
 */
function willsx_get_revenue_data($partner_id) {
    $revenue_data = array();
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $revenue = get_post_meta($partner_id, "_revenue_$month", true);
        $revenue_data[] = $revenue ? floatval($revenue) : 0;
    }
    return $revenue_data;
}

/**
 * Get revenue labels for charts
 */
function willsx_get_revenue_labels($partner_id) {
    $labels = array();
    for ($i = 5; $i >= 0; $i--) {
        $labels[] = date('M Y', strtotime("-$i months"));
    }
    return $labels;
}

/**
 * Get clients data for charts
 */
function willsx_get_clients_data($partner_id) {
    $clients_data = array();
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $count = get_post_meta($partner_id, "_new_clients_$month", true);
        $clients_data[] = $count ? intval($count) : 0;
    }
    return $clients_data;
}

/**
 * Get clients labels for charts
 */
function willsx_get_clients_labels($partner_id) {
    $labels = array();
    for ($i = 5; $i >= 0; $i--) {
        $labels[] = date('M Y', strtotime("-$i months"));
    }
    return $labels;
}

/**
 * Handle partner branding form submission
 */
function willsx_handle_partner_branding_submission() {
    if (!isset($_POST['partner_branding_nonce']) || 
        !wp_verify_nonce($_POST['partner_branding_nonce'], 'update_partner_branding')) {
        return;
    }

    $partner_id = willsx_get_current_partner_id();
    if (!$partner_id) {
        return;
    }

    // Handle logo upload
    if (!empty($_FILES['partner_logo']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('partner_logo', 0);
        if (!is_wp_error($attachment_id)) {
            update_post_meta($partner_id, '_partner_logo', $attachment_id);
        }
    }

    // Update colors
    if (isset($_POST['primary_color'])) {
        update_post_meta($partner_id, '_primary_color', sanitize_hex_color($_POST['primary_color']));
    }
    if (isset($_POST['secondary_color'])) {
        update_post_meta($partner_id, '_secondary_color', sanitize_hex_color($_POST['secondary_color']));
    }
    if (isset($_POST['accent_color'])) {
        update_post_meta($partner_id, '_accent_color', sanitize_hex_color($_POST['accent_color']));
    }
}
add_action('admin_post_update_partner_branding', 'willsx_handle_partner_branding_submission'); 