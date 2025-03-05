<?php
/**
 * Partners API functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class WillsX_Partners {
    /**
     * API namespace
     */
    private $namespace = 'willsx/v1';

    /**
     * Initialize Partners API
     */
    public function init() {
        // Register custom post type
        add_action('init', array($this, 'register_post_type'));
        
        // Register API routes
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register partner post type
     */
    public function register_post_type() {
        $labels = array(
            'name'               => 'Partners',
            'singular_name'      => 'Partner',
            'menu_name'          => 'Partners',
            'add_new'            => 'Add New Partner',
            'add_new_item'       => 'Add New Partner',
            'edit_item'          => 'Edit Partner',
            'new_item'           => 'New Partner',
            'view_item'          => 'View Partner',
            'search_items'       => 'Search Partners',
            'not_found'          => 'No partners found',
            'not_found_in_trash' => 'No partners found in trash',
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'partner'),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-groups',
            'supports'            => array('title', 'thumbnail', 'custom-fields'),
            'show_in_rest'        => true,
        );
        
        register_post_type('willsx_partner', $args);
    }
    
    /**
     * Register partner API routes
     */
    public function register_routes() {
        // Get all partners
        register_rest_route($this->namespace, '/partners', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'get_partners'),
            'permission_callback' => array($this, 'get_partners_permissions_check'),
        ));
        
        // Get single partner
        register_rest_route($this->namespace, '/partners/(?P<id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'get_partner'),
            'permission_callback' => array($this, 'get_partner_permissions_check'),
            'args'                => array(
                'id' => array(
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ),
            ),
        ));
        
        // Create partner (protected)
        register_rest_route($this->namespace, '/partners', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'create_partner'),
            'permission_callback' => array($this, 'create_partner_permissions_check'),
        ));
        
        // Update partner (protected)
        register_rest_route($this->namespace, '/partners/(?P<id>\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array($this, 'update_partner'),
            'permission_callback' => array($this, 'update_partner_permissions_check'),
            'args'                => array(
                'id' => array(
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ),
            ),
        ));
    }
    
    /**
     * Get all partners
     */
    public function get_partners() {
        $args = array(
            'post_type'      => 'willsx_partner',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        
        $posts = get_posts($args);
        $partners = array();
        
        foreach ($posts as $post) {
            $partner = $this->prepare_partner_for_response($post);
            $partners[] = $partner;
        }
        
        return rest_ensure_response($partners);
    }
    
    /**
     * Get single partner by ID
     */
    public function get_partner($request) {
        $id = $request['id'];
        $post = get_post($id);
        
        if (!$post || $post->post_type !== 'willsx_partner') {
            return new WP_Error('not_found', 'Partner not found', array('status' => 404));
        }
        
        $partner = $this->prepare_partner_for_response($post);
        
        return rest_ensure_response($partner);
    }
    
    /**
     * Create new partner
     */
    public function create_partner($request) {
        $params = $request->get_params();
        
        // Validate required fields
        if (empty($params['name'])) {
            return new WP_Error('missing_name', 'Partner name is required', array('status' => 400));
        }
        
        // Create partner post
        $post_data = array(
            'post_title'   => sanitize_text_field($params['name']),
            'post_type'    => 'willsx_partner',
            'post_status'  => 'publish',
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Save partner metadata
        if (!empty($params['logo'])) {
            update_post_meta($post_id, 'partner_logo', sanitize_text_field($params['logo']));
        }
        
        if (!empty($params['colors'])) {
            update_post_meta($post_id, 'partner_colors', $params['colors']);
        }
        
        if (!empty($params['contact'])) {
            update_post_meta($post_id, 'partner_contact', $params['contact']);
        }
        
        if (!empty($params['referral'])) {
            update_post_meta($post_id, 'partner_referral', $params['referral']);
        }
        
        // Return the new partner
        $post = get_post($post_id);
        $partner = $this->prepare_partner_for_response($post);
        
        return rest_ensure_response($partner);
    }
    
    /**
     * Update existing partner
     */
    public function update_partner($request) {
        $id = $request['id'];
        $post = get_post($id);
        $params = $request->get_params();
        
        if (!$post || $post->post_type !== 'willsx_partner') {
            return new WP_Error('not_found', 'Partner not found', array('status' => 404));
        }
        
        // Update partner post
        if (!empty($params['name'])) {
            $post_data = array(
                'ID'         => $id,
                'post_title' => sanitize_text_field($params['name']),
            );
            wp_update_post($post_data);
        }
        
        // Update partner metadata
        if (!empty($params['logo'])) {
            update_post_meta($id, 'partner_logo', sanitize_text_field($params['logo']));
        }
        
        if (!empty($params['colors'])) {
            update_post_meta($id, 'partner_colors', $params['colors']);
        }
        
        if (!empty($params['contact'])) {
            update_post_meta($id, 'partner_contact', $params['contact']);
        }
        
        if (!empty($params['referral'])) {
            update_post_meta($id, 'partner_referral', $params['referral']);
        }
        
        // Return the updated partner
        $post = get_post($id);
        $partner = $this->prepare_partner_for_response($post);
        
        return rest_ensure_response($partner);
    }
    
    /**
     * Prepare partner data for API response
     */
    private function prepare_partner_for_response($post) {
        $partner = array(
            'id'       => $post->ID,
            'name'     => $post->post_title,
            'slug'     => $post->post_name,
            'logo'     => get_post_meta($post->ID, 'partner_logo', true),
            'colors'   => get_post_meta($post->ID, 'partner_colors', true),
            'contact'  => get_post_meta($post->ID, 'partner_contact', true),
            'referral' => get_post_meta($post->ID, 'partner_referral', true),
            'created'  => $post->post_date,
            'modified' => $post->post_modified,
        );
        
        return $partner;
    }
    
    /**
     * Check permissions for getting partners list
     */
    public function get_partners_permissions_check() {
        return true; // Public endpoint
    }
    
    /**
     * Check permissions for getting single partner
     */
    public function get_partner_permissions_check() {
        return true; // Public endpoint
    }
    
    /**
     * Check permissions for creating a partner
     */
    public function create_partner_permissions_check() {
        return current_user_can('edit_posts');
    }
    
    /**
     * Check permissions for updating a partner
     */
    public function update_partner_permissions_check() {
        return current_user_can('edit_posts');
    }
}
