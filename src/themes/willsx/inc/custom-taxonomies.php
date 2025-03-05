<?php
/**
 * WillsX Custom Taxonomies
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom taxonomies
 */
function willsx_register_taxonomies() {
    
    // Partner Type Taxonomy
    $partner_type_labels = array(
        'name'              => _x('Partner Types', 'taxonomy general name', 'willsx'),
        'singular_name'     => _x('Partner Type', 'taxonomy singular name', 'willsx'),
        'search_items'      => __('Search Partner Types', 'willsx'),
        'all_items'         => __('All Partner Types', 'willsx'),
        'parent_item'       => __('Parent Partner Type', 'willsx'),
        'parent_item_colon' => __('Parent Partner Type:', 'willsx'),
        'edit_item'         => __('Edit Partner Type', 'willsx'),
        'update_item'       => __('Update Partner Type', 'willsx'),
        'add_new_item'      => __('Add New Partner Type', 'willsx'),
        'new_item_name'     => __('New Partner Type Name', 'willsx'),
        'menu_name'         => __('Partner Types', 'willsx'),
    );

    $partner_type_args = array(
        'hierarchical'      => true,
        'labels'            => $partner_type_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'partner-type'),
        'show_in_rest'      => true,
    );

    register_taxonomy('partner_type', array('partner'), $partner_type_args);

    // Resource Type Taxonomy
    $resource_type_labels = array(
        'name'              => _x('Resource Types', 'taxonomy general name', 'willsx'),
        'singular_name'     => _x('Resource Type', 'taxonomy singular name', 'willsx'),
        'search_items'      => __('Search Resource Types', 'willsx'),
        'all_items'         => __('All Resource Types', 'willsx'),
        'parent_item'       => __('Parent Resource Type', 'willsx'),
        'parent_item_colon' => __('Parent Resource Type:', 'willsx'),
        'edit_item'         => __('Edit Resource Type', 'willsx'),
        'update_item'       => __('Update Resource Type', 'willsx'),
        'add_new_item'      => __('Add New Resource Type', 'willsx'),
        'new_item_name'     => __('New Resource Type Name', 'willsx'),
        'menu_name'         => __('Resource Types', 'willsx'),
    );

    $resource_type_args = array(
        'hierarchical'      => true,
        'labels'            => $resource_type_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'resource-type'),
        'show_in_rest'      => true,
    );

    register_taxonomy('resource_type', array('resource'), $resource_type_args);

    // Team Department Taxonomy
    $department_labels = array(
        'name'              => _x('Departments', 'taxonomy general name', 'willsx'),
        'singular_name'     => _x('Department', 'taxonomy singular name', 'willsx'),
        'search_items'      => __('Search Departments', 'willsx'),
        'all_items'         => __('All Departments', 'willsx'),
        'parent_item'       => __('Parent Department', 'willsx'),
        'parent_item_colon' => __('Parent Department:', 'willsx'),
        'edit_item'         => __('Edit Department', 'willsx'),
        'update_item'       => __('Update Department', 'willsx'),
        'add_new_item'      => __('Add New Department', 'willsx'),
        'new_item_name'     => __('New Department Name', 'willsx'),
        'menu_name'         => __('Departments', 'willsx'),
    );

    $department_args = array(
        'hierarchical'      => true,
        'labels'            => $department_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'department'),
        'show_in_rest'      => true,
    );

    register_taxonomy('department', array('team'), $department_args);

    // Blog Categories Taxonomy (enhanced with custom fields)
    $blog_category_labels = array(
        'name'              => _x('Blog Categories', 'taxonomy general name', 'willsx'),
        'singular_name'     => _x('Blog Category', 'taxonomy singular name', 'willsx'),
        'search_items'      => __('Search Blog Categories', 'willsx'),
        'all_items'         => __('All Blog Categories', 'willsx'),
        'parent_item'       => __('Parent Blog Category', 'willsx'),
        'parent_item_colon' => __('Parent Blog Category:', 'willsx'),
        'edit_item'         => __('Edit Blog Category', 'willsx'),
        'update_item'       => __('Update Blog Category', 'willsx'),
        'add_new_item'      => __('Add New Blog Category', 'willsx'),
        'new_item_name'     => __('New Blog Category Name', 'willsx'),
        'menu_name'         => __('Blog Categories', 'willsx'),
    );

    $blog_category_args = array(
        'hierarchical'      => true,
        'labels'            => $blog_category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'blog-category'),
        'show_in_rest'      => true,
    );

    register_taxonomy('blog_category', array('post'), $blog_category_args);

    // Will Types Taxonomy
    $will_type_labels = array(
        'name'              => _x('Will Types', 'taxonomy general name', 'willsx'),
        'singular_name'     => _x('Will Type', 'taxonomy singular name', 'willsx'),
        'search_items'      => __('Search Will Types', 'willsx'),
        'all_items'         => __('All Will Types', 'willsx'),
        'parent_item'       => __('Parent Will Type', 'willsx'),
        'parent_item_colon' => __('Parent Will Type:', 'willsx'),
        'edit_item'         => __('Edit Will Type', 'willsx'),
        'update_item'       => __('Update Will Type', 'willsx'),
        'add_new_item'      => __('Add New Will Type', 'willsx'),
        'new_item_name'     => __('New Will Type Name', 'willsx'),
        'menu_name'         => __('Will Types', 'willsx'),
    );

    $will_type_args = array(
        'hierarchical'      => true,
        'labels'            => $will_type_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'will-type'),
        'show_in_rest'      => true,
    );

    register_taxonomy('will_type', array('post', 'resource'), $will_type_args);
}
add_action('init', 'willsx_register_taxonomies');

/**
 * Add default terms to taxonomies
 */
function willsx_add_default_terms() {
    // Partner Types
    wp_insert_term('Insurance', 'partner_type');
    wp_insert_term('Financial Advisor', 'partner_type');
    wp_insert_term('Legal Firm', 'partner_type');
    wp_insert_term('Charity', 'partner_type');

    // Resource Types
    wp_insert_term('Whitepaper', 'resource_type');
    wp_insert_term('E-book', 'resource_type');
    wp_insert_term('Guide', 'resource_type');
    wp_insert_term('Checklist', 'resource_type');
    wp_insert_term('Calculator', 'resource_type');

    // Departments
    wp_insert_term('Legal Advisors', 'department');
    wp_insert_term('Management', 'department');
    wp_insert_term('Client Support', 'department');
    wp_insert_term('Marketing', 'department');

    // Will Types
    wp_insert_term('Standard Will', 'will_type');
    wp_insert_term('Mirror Will', 'will_type');
    wp_insert_term('Trust Will', 'will_type');
    wp_insert_term('LPA', 'will_type');
}
// Run once after theme activation
add_action('after_switch_theme', 'willsx_add_default_terms'); 