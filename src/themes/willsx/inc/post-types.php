<?php
/**
 * Custom Post Types for WillsX
 *
 * @package WillsX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register custom post types
 */
function willsx_register_post_types() {
	// Register Partner post type
	$labels = array(
		'name'                  => _x( 'Partners', 'Post type general name', 'willsx' ),
		'singular_name'         => _x( 'Partner', 'Post type singular name', 'willsx' ),
		'menu_name'             => _x( 'Partners', 'Admin Menu text', 'willsx' ),
		'name_admin_bar'        => _x( 'Partner', 'Add New on Toolbar', 'willsx' ),
		'add_new'               => __( 'Add New', 'willsx' ),
		'add_new_item'          => __( 'Add New Partner', 'willsx' ),
		'new_item'              => __( 'New Partner', 'willsx' ),
		'edit_item'             => __( 'Edit Partner', 'willsx' ),
		'view_item'             => __( 'View Partner', 'willsx' ),
		'all_items'             => __( 'All Partners', 'willsx' ),
		'search_items'          => __( 'Search Partners', 'willsx' ),
		'parent_item_colon'     => __( 'Parent Partners:', 'willsx' ),
		'not_found'             => __( 'No partners found.', 'willsx' ),
		'not_found_in_trash'    => __( 'No partners found in Trash.', 'willsx' ),
		'featured_image'        => _x( 'Partner Logo', 'Overrides the "Featured Image" phrase', 'willsx' ),
		'set_featured_image'    => _x( 'Set partner logo', 'Overrides the "Set featured image" phrase', 'willsx' ),
		'remove_featured_image' => _x( 'Remove partner logo', 'Overrides the "Remove featured image" phrase', 'willsx' ),
		'use_featured_image'    => _x( 'Use as partner logo', 'Overrides the "Use as featured image" phrase', 'willsx' ),
		'archives'              => _x( 'Partner archives', 'The post type archive label used in nav menus', 'willsx' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'partner' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-groups',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'partner', $args );
}
add_action( 'init', 'willsx_register_post_types' );

/**
 * Add meta boxes for partner post type
 */
function willsx_add_partner_meta_boxes() {
	add_meta_box(
		'willsx_partner_details',
		__( 'Partner Details', 'willsx' ),
		'willsx_partner_details_callback',
		'partner',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'willsx_add_partner_meta_boxes' );

/**
 * Partner details meta box callback
 *
 * @param WP_Post $post Current post object.
 */
function willsx_partner_details_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'willsx_partner_details', 'willsx_partner_details_nonce' );

	// Get existing values
	$partner_id = get_post_meta( $post->ID, '_partner_id', true );
	$primary_color = get_post_meta( $post->ID, '_primary_color', true );
	$secondary_color = get_post_meta( $post->ID, '_secondary_color', true );
	$partner_url = get_post_meta( $post->ID, '_partner_url', true );
	$partner_api_key = get_post_meta( $post->ID, '_partner_api_key', true );
	$partner_active = get_post_meta( $post->ID, '_partner_active', true );

	// Output fields
	?>
	<p>
		<label for="partner_id"><?php esc_html_e( 'Partner ID (used in URL parameters)', 'willsx' ); ?></label><br>
		<input type="text" id="partner_id" name="partner_id" value="<?php echo esc_attr( $partner_id ); ?>" class="regular-text">
	</p>
	<p>
		<label for="primary_color"><?php esc_html_e( 'Primary Color', 'willsx' ); ?></label><br>
		<input type="color" id="primary_color" name="primary_color" value="<?php echo esc_attr( $primary_color ); ?>">
	</p>
	<p>
		<label for="secondary_color"><?php esc_html_e( 'Secondary Color', 'willsx' ); ?></label><br>
		<input type="color" id="secondary_color" name="secondary_color" value="<?php echo esc_attr( $secondary_color ); ?>">
	</p>
	<p>
		<label for="partner_url"><?php esc_html_e( 'Partner Website URL', 'willsx' ); ?></label><br>
		<input type="url" id="partner_url" name="partner_url" value="<?php echo esc_url( $partner_url ); ?>" class="regular-text">
	</p>
	<p>
		<label for="partner_api_key"><?php esc_html_e( 'Partner API Key (if applicable)', 'willsx' ); ?></label><br>
		<input type="text" id="partner_api_key" name="partner_api_key" value="<?php echo esc_attr( $partner_api_key ); ?>" class="regular-text">
	</p>
	<p>
		<label for="partner_active">
			<input type="checkbox" id="partner_active" name="partner_active" value="1" <?php checked( $partner_active, '1' ); ?>>
			<?php esc_html_e( 'Partner Active', 'willsx' ); ?>
		</label>
	</p>
	<?php
}

/**
 * Save partner meta box data
 *
 * @param int $post_id Post ID.
 */
function willsx_save_partner_meta_box_data( $post_id ) {
	// Check if nonce is set
	if ( ! isset( $_POST['willsx_partner_details_nonce'] ) ) {
		return;
	}

	// Verify nonce
	if ( ! wp_verify_nonce( $_POST['willsx_partner_details_nonce'], 'willsx_partner_details' ) ) {
		return;
	}

	// If this is an autosave, don't do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check user permissions
	if ( isset( $_POST['post_type'] ) && 'partner' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Save partner ID
	if ( isset( $_POST['partner_id'] ) ) {
		update_post_meta( $post_id, '_partner_id', sanitize_text_field( $_POST['partner_id'] ) );
	}

	// Save primary color
	if ( isset( $_POST['primary_color'] ) ) {
		update_post_meta( $post_id, '_primary_color', sanitize_hex_color( $_POST['primary_color'] ) );
	}

	// Save secondary color
	if ( isset( $_POST['secondary_color'] ) ) {
		update_post_meta( $post_id, '_secondary_color', sanitize_hex_color( $_POST['secondary_color'] ) );
	}

	// Save partner URL
	if ( isset( $_POST['partner_url'] ) ) {
		update_post_meta( $post_id, '_partner_url', esc_url_raw( $_POST['partner_url'] ) );
	}

	// Save partner API key
	if ( isset( $_POST['partner_api_key'] ) ) {
		update_post_meta( $post_id, '_partner_api_key', sanitize_text_field( $_POST['partner_api_key'] ) );
	}

	// Save partner active status
	$partner_active = isset( $_POST['partner_active'] ) ? '1' : '0';
	update_post_meta( $post_id, '_partner_active', $partner_active );
}
add_action( 'save_post', 'willsx_save_partner_meta_box_data' );

/**
 * Add partner columns to admin list
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function willsx_partner_columns( $columns ) {
	$new_columns = array();
	
	// Insert columns after title
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		
		if ( 'title' === $key ) {
			$new_columns['partner_id'] = __( 'Partner ID', 'willsx' );
			$new_columns['partner_active'] = __( 'Active', 'willsx' );
		}
	}
	
	return $new_columns;
}
add_filter( 'manage_partner_posts_columns', 'willsx_partner_columns' );

/**
 * Display partner column data
 *
 * @param string $column Column name.
 * @param int    $post_id Post ID.
 */
function willsx_partner_column_data( $column, $post_id ) {
	switch ( $column ) {
		case 'partner_id':
			echo esc_html( get_post_meta( $post_id, '_partner_id', true ) );
			break;
		case 'partner_active':
			$active = get_post_meta( $post_id, '_partner_active', true );
			echo $active ? '<span style="color:green;">✓</span>' : '<span style="color:red;">✗</span>';
			break;
	}
}
add_action( 'manage_partner_posts_custom_column', 'willsx_partner_column_data', 10, 2 ); 