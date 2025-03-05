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
    
    // Partner Post Type
    $partner_labels = array(
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
        'filter_items_list'     => _x('Filter partners list', 'Screen reader text for the filter links heading on the post type listing screen', 'willsx'),
        'items_list_navigation' => _x('Partners list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'willsx'),
        'items_list'            => _x('Partners list', 'Screen reader text for the items list heading on the post type listing screen', 'willsx'),
    );

    $partner_args = array(
        'labels'             => $partner_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'partner'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('partner', $partner_args);

    // Team Member Post Type
    $team_labels = array(
        'name'                  => _x('Team', 'Post type general name', 'willsx'),
        'singular_name'         => _x('Team Member', 'Post type singular name', 'willsx'),
        'menu_name'             => _x('Team', 'Admin Menu text', 'willsx'),
        'name_admin_bar'        => _x('Team Member', 'Add New on Toolbar', 'willsx'),
        'add_new'               => __('Add New', 'willsx'),
        'add_new_item'          => __('Add New Team Member', 'willsx'),
        'new_item'              => __('New Team Member', 'willsx'),
        'edit_item'             => __('Edit Team Member', 'willsx'),
        'view_item'             => __('View Team Member', 'willsx'),
        'all_items'             => __('All Team Members', 'willsx'),
        'search_items'          => __('Search Team Members', 'willsx'),
        'parent_item_colon'     => __('Parent Team Members:', 'willsx'),
        'not_found'             => __('No team members found.', 'willsx'),
        'not_found_in_trash'    => __('No team members found in Trash.', 'willsx'),
        'featured_image'        => _x('Member Photo', 'Overrides the "Featured Image" phrase', 'willsx'),
        'set_featured_image'    => _x('Set member photo', 'Overrides the "Set featured image" phrase', 'willsx'),
        'remove_featured_image' => _x('Remove member photo', 'Overrides the "Remove featured image" phrase', 'willsx'),
        'use_featured_image'    => _x('Use as member photo', 'Overrides the "Use as featured image" phrase', 'willsx'),
    );

    $team_args = array(
        'labels'             => $team_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'team'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-businessman',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('team', $team_args);

    // Testimonial Post Type
    $testimonial_labels = array(
        'name'                  => _x('Testimonials', 'Post type general name', 'willsx'),
        'singular_name'         => _x('Testimonial', 'Post type singular name', 'willsx'),
        'menu_name'             => _x('Testimonials', 'Admin Menu text', 'willsx'),
        'name_admin_bar'        => _x('Testimonial', 'Add New on Toolbar', 'willsx'),
        'add_new'               => __('Add New', 'willsx'),
        'add_new_item'          => __('Add New Testimonial', 'willsx'),
        'new_item'              => __('New Testimonial', 'willsx'),
        'edit_item'             => __('Edit Testimonial', 'willsx'),
        'view_item'             => __('View Testimonial', 'willsx'),
        'all_items'             => __('All Testimonials', 'willsx'),
        'search_items'          => __('Search Testimonials', 'willsx'),
        'parent_item_colon'     => __('Parent Testimonials:', 'willsx'),
        'not_found'             => __('No testimonials found.', 'willsx'),
        'not_found_in_trash'    => __('No testimonials found in Trash.', 'willsx'),
    );

    $testimonial_args = array(
        'labels'             => $testimonial_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'testimonial'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 22,
        'menu_icon'          => 'dashicons-format-quote',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('testimonial', $testimonial_args);

    // Resource Post Type (for whitepapers, ebooks, etc.)
    $resource_labels = array(
        'name'                  => _x('Resources', 'Post type general name', 'willsx'),
        'singular_name'         => _x('Resource', 'Post type singular name', 'willsx'),
        'menu_name'             => _x('Resources', 'Admin Menu text', 'willsx'),
        'name_admin_bar'        => _x('Resource', 'Add New on Toolbar', 'willsx'),
        'add_new'               => __('Add New', 'willsx'),
        'add_new_item'          => __('Add New Resource', 'willsx'),
        'new_item'              => __('New Resource', 'willsx'),
        'edit_item'             => __('Edit Resource', 'willsx'),
        'view_item'             => __('View Resource', 'willsx'),
        'all_items'             => __('All Resources', 'willsx'),
        'search_items'          => __('Search Resources', 'willsx'),
        'parent_item_colon'     => __('Parent Resources:', 'willsx'),
        'not_found'             => __('No resources found.', 'willsx'),
        'not_found_in_trash'    => __('No resources found in Trash.', 'willsx'),
    );

    $resource_args = array(
        'labels'             => $resource_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'resource'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 23,
        'menu_icon'          => 'dashicons-media-document',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('resource', $resource_args);
}
add_action('init', 'willsx_register_post_types'); 