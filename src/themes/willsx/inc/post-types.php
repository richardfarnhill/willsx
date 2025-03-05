<?php
/**
 * WillsX Custom Post Types
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types
 */
function willsx_register_post_types() {
    // Partner post type
    $labels = array(
        'name'                  => _x('Partners', 'Post type general name', 'willsx'),
        'singular_name'         => _x('Partner', 'Post type singular name', 'willsx'),
        'menu_name'             => _x('Partners', 'Admin Menu text', 'willsx'),
        'name_admin_bar'        => _x('Partner', 'Add New on Toolbar', 'willsx'),
        'add_new'               => __('Add New', 'willsx'),
        'add_new_item'          => __('Add New Partner', 'willsx'),
        'new_item'              => __('New Partner', 'willsx'),
        'edit_item'             => __('Edit Partner', 'willsx'),
        'view_item'             => __('View Partner', 'willsx'),
        'all_items'             => __('All Partners', 'willsx'),
        'search_items'          => __('Search Partners', 'willsx'),
        'parent_item_colon'     => __('Parent Partners:', 'willsx'),
        'not_found'             => __('No partners found.', 'willsx'),
        'not_found_in_trash'    => __('No partners found in Trash.', 'willsx'),
        'featured_image'        => _x('Partner Logo', 'Overrides the "Featured Image" phrase', 'willsx'),
        'set_featured_image'    => _x('Set partner logo', 'Overrides the "Set featured image" phrase', 'willsx'),
        'remove_featured_image' => _x('Remove partner logo', 'Overrides the "Remove featured image" phrase', 'willsx'),
        'use_featured_image'    => _x('Use as partner logo', 'Overrides the "Use as featured image" phrase', 'willsx'),
        'archives'              => _x('Partner archives', 'The post type archive label used in nav menus', 'willsx'),
        'insert_into_item'      => _x('Insert into partner', 'Overrides the "Insert into post" phrase', 'willsx'),
        'uploaded_to_this_item' => _x('Uploaded to this partner', 'Overrides the "Uploaded to this post" phrase', 'willsx'),
        'filter_items_list'     => _x('Filter partners list', 'Screen reader text for the filter links', 'willsx'),
        'items_list_navigation' => _x('Partners list navigation', 'Screen reader text for the pagination', 'willsx'),
        'items_list'            => _x('Partners list', 'Screen reader text for the items list', 'willsx'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'partner'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    );

    register_post_type('partner', $args);
}
add_action('init', 'willsx_register_post_types');

/**
 * Add custom meta boxes for partners
 */
function willsx_add_partner_meta_boxes() {
    add_meta_box(
        'willsx_partner_details',
        __('Partner Details', 'willsx'),
        'willsx_partner_details_callback',
        'partner',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'willsx_add_partner_meta_boxes');

/**
 * Render partner details meta box
 */
function willsx_partner_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('willsx_save_partner_details', 'willsx_partner_details_nonce');
    
    // Get current values
    $contact_name = get_post_meta($post->ID, 'partner_contact_name', true);
    $contact_email = get_post_meta($post->ID, 'partner_contact_email', true);
    $contact_phone = get_post_meta($post->ID, 'partner_contact_phone', true);
    $website = get_post_meta($post->ID, 'partner_website', true);
    $commission_rate = get_post_meta($post->ID, 'partner_commission_rate', true);
    $primary_color = get_post_meta($post->ID, 'partner_primary_color', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="partner_contact_name"><?php _e('Contact Name', 'willsx'); ?></label>
            </th>
            <td>
                <input type="text" id="partner_contact_name" name="partner_contact_name" value="<?php echo esc_attr($contact_name); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="partner_contact_email"><?php _e('Contact Email', 'willsx'); ?></label>
            </th>
            <td>
                <input type="email" id="partner_contact_email" name="partner_contact_email" value="<?php echo esc_attr($contact_email); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="partner_contact_phone"><?php _e('Contact Phone', 'willsx'); ?></label>
            </th>
            <td>
                <input type="text" id="partner_contact_phone" name="partner_contact_phone" value="<?php echo esc_attr($contact_phone); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="partner_website"><?php _e('Website', 'willsx'); ?></label>
            </th>
            <td>
                <input type="url" id="partner_website" name="partner_website" value="<?php echo esc_attr($website); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="partner_commission_rate"><?php _e('Commission Rate (%)', 'willsx'); ?></label>
            </th>
            <td>
                <input type="number" id="partner_commission_rate" name="partner_commission_rate" value="<?php echo esc_attr($commission_rate); ?>" class="small-text" min="0" max="100" step="0.5">
                <p class="description"><?php _e('Commission percentage for referrals from this partner.', 'willsx'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="partner_primary_color"><?php _e('Primary Brand Color', 'willsx'); ?></label>
            </th>
            <td>
                <input type="color" id="partner_primary_color" name="partner_primary_color" value="<?php echo esc_attr($primary_color ? $primary_color : '#0073aa'); ?>">
                <p class="description"><?php _e('Primary color used for partner branding on landing pages.', 'willsx'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save partner meta box data
 */
function willsx_save_partner_details($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['willsx_partner_details_nonce'])) {
        return;
    }
    
    // Verify the nonce
    if (!wp_verify_nonce($_POST['willsx_partner_details_nonce'], 'willsx_save_partner_details')) {
        return;
    }
    
    // If this is an autosave, we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save the data
    if (isset($_POST['partner_contact_name'])) {
        update_post_meta($post_id, 'partner_contact_name', sanitize_text_field($_POST['partner_contact_name']));
    }
    
    if (isset($_POST['partner_contact_email'])) {
        update_post_meta($post_id, 'partner_contact_email', sanitize_email($_POST['partner_contact_email']));
    }
    
    if (isset($_POST['partner_contact_phone'])) {
        update_post_meta($post_id, 'partner_contact_phone', sanitize_text_field($_POST['partner_contact_phone']));
    }
    
    if (isset($_POST['partner_website'])) {
        update_post_meta($post_id, 'partner_website', esc_url_raw($_POST['partner_website']));
    }
    
    if (isset($_POST['partner_commission_rate'])) {
        update_post_meta($post_id, 'partner_commission_rate', floatval($_POST['partner_commission_rate']));
    }
    
    if (isset($_POST['partner_primary_color'])) {
        update_post_meta($post_id, 'partner_primary_color', sanitize_hex_color($_POST['partner_primary_color']));
    }
}
add_action('save_post_partner', 'willsx_save_partner_details'); 