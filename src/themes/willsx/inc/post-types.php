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
		'name'               => _x('Partners', 'post type general name', 'willsx'),
		'singular_name'      => _x('Partner', 'post type singular name', 'willsx'),
		'menu_name'          => _x('Partners', 'admin menu', 'willsx'),
		'add_new'            => _x('Add New', 'partner', 'willsx'),
		'add_new_item'       => __('Add New Partner', 'willsx'),
		'edit_item'          => __('Edit Partner', 'willsx'),
		'new_item'           => __('New Partner', 'willsx'),
		'view_item'          => __('View Partner', 'willsx'),
		'search_items'       => __('Search Partners', 'willsx'),
		'not_found'          => __('No partners found', 'willsx'),
		'not_found_in_trash' => __('No partners found in Trash', 'willsx'),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array('slug' => 'partner'),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-groups',
		'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
	);

	register_post_type('partner', $args);
}
add_action('init', 'willsx_register_post_types');

/**
 * Add meta boxes for partner details
 */
function willsx_add_partner_meta_boxes() {
	add_meta_box(
		'partner_branding_details',
		__('Partner Branding', 'willsx'),
		'willsx_partner_branding_meta_box',
		'partner',
		'normal',
		'high'
	);

	add_meta_box(
		'partner_details',
		__('Partner Details', 'willsx'),
		'willsx_partner_details_meta_box',
		'partner',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'willsx_add_partner_meta_boxes');

/**
 * Partner branding meta box callback
 */
function willsx_partner_branding_meta_box($post) {
	wp_nonce_field('willsx_partner_branding_meta_box', 'willsx_partner_branding_meta_box_nonce');

	$logo_id = get_post_meta($post->ID, '_partner_logo_id', true);
	$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
	$primary_color = get_post_meta($post->ID, '_partner_primary_color', true);
	$secondary_color = get_post_meta($post->ID, '_partner_secondary_color', true);
	$accent_color = get_post_meta($post->ID, '_partner_accent_color', true);
	$font_family = get_post_meta($post->ID, '_partner_font_family', true);
	?>
	<div class="partner-branding-fields">
		<p class="logo-upload">
			<label for="partner_logo"><?php _e('Partner Logo', 'willsx'); ?></label><br>
			<input type="hidden" id="partner_logo_id" name="partner_logo_id" value="<?php echo esc_attr($logo_id); ?>">
			<img id="partner_logo_preview" src="<?php echo esc_url($logo_url); ?>" style="max-width: 200px; <?php echo $logo_url ? '' : 'display: none;'; ?>">
			<button type="button" class="button" id="upload_logo_button"><?php _e('Upload Logo', 'willsx'); ?></button>
			<button type="button" class="button" id="remove_logo_button" <?php echo $logo_url ? '' : 'style="display: none;"'; ?>><?php _e('Remove Logo', 'willsx'); ?></button>
			<p class="description"><?php _e('Upload a high-resolution logo (recommended size: 400x100px, transparent PNG)', 'willsx'); ?></p>
		</p>

		<p>
			<label for="partner_primary_color"><?php _e('Primary Brand Color', 'willsx'); ?></label><br>
			<input type="color" id="partner_primary_color" name="partner_primary_color" value="<?php echo esc_attr($primary_color); ?>">
			<span class="description"><?php _e('Main brand color used for headers and primary buttons', 'willsx'); ?></span>
		</p>

		<p>
			<label for="partner_secondary_color"><?php _e('Secondary Brand Color', 'willsx'); ?></label><br>
			<input type="color" id="partner_secondary_color" name="partner_secondary_color" value="<?php echo esc_attr($secondary_color); ?>">
			<span class="description"><?php _e('Used for accents and secondary elements', 'willsx'); ?></span>
		</p>

		<p>
			<label for="partner_accent_color"><?php _e('Accent Color', 'willsx'); ?></label><br>
			<input type="color" id="partner_accent_color" name="partner_accent_color" value="<?php echo esc_attr($accent_color); ?>">
			<span class="description"><?php _e('Used for highlights and call-to-action elements', 'willsx'); ?></span>
		</p>

		<p>
			<label for="partner_font_family"><?php _e('Brand Font Family', 'willsx'); ?></label><br>
			<select id="partner_font_family" name="partner_font_family">
				<option value=""><?php _e('Select a font family', 'willsx'); ?></option>
				<option value="Arial" <?php selected($font_family, 'Arial'); ?>>Arial</option>
				<option value="Helvetica" <?php selected($font_family, 'Helvetica'); ?>>Helvetica</option>
				<option value="Open Sans" <?php selected($font_family, 'Open Sans'); ?>>Open Sans</option>
				<option value="Roboto" <?php selected($font_family, 'Roboto'); ?>>Roboto</option>
				<option value="custom" <?php selected($font_family, 'custom'); ?>>Custom (specify in CSS)</option>
			</select>
			<span class="description"><?php _e('Select the primary font family for partner branding', 'willsx'); ?></span>
		</p>
	</div>

	<script>
	jQuery(document).ready(function($) {
		var mediaUploader;

		$('#upload_logo_button').click(function(e) {
			e.preventDefault();

			if (mediaUploader) {
				mediaUploader.open();
				return;
			}

			mediaUploader = wp.media({
				title: '<?php _e('Choose Partner Logo', 'willsx'); ?>',
				button: {
					text: '<?php _e('Use this logo', 'willsx'); ?>'
				},
				multiple: false
			});

			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#partner_logo_id').val(attachment.id);
				$('#partner_logo_preview').attr('src', attachment.url).show();
				$('#remove_logo_button').show();
			});

			mediaUploader.open();
		});

		$('#remove_logo_button').click(function(e) {
			e.preventDefault();
			$('#partner_logo_id').val('');
			$('#partner_logo_preview').attr('src', '').hide();
			$(this).hide();
		});
	});
	</script>
	<?php
}

/**
 * Partner details meta box callback
 */
function willsx_partner_details_meta_box($post) {
	wp_nonce_field('willsx_partner_details_meta_box', 'willsx_partner_details_meta_box_nonce');

	$partner_id = get_post_meta($post->ID, '_partner_id', true);
	$partner_url = get_post_meta($post->ID, '_partner_url', true);
	$api_key = get_post_meta($post->ID, '_partner_api_key', true);
	$active = get_post_meta($post->ID, '_partner_active', true);
	$contact_name = get_post_meta($post->ID, '_partner_contact_name', true);
	$contact_email = get_post_meta($post->ID, '_partner_contact_email', true);
	$contact_phone = get_post_meta($post->ID, '_partner_contact_phone', true);
	?>
	<div class="partner-details-fields">
		<p>
			<label for="partner_id"><?php _e('Partner ID', 'willsx'); ?></label><br>
			<input type="text" id="partner_id" name="partner_id" value="<?php echo esc_attr($partner_id); ?>" required>
			<span class="description"><?php _e('Unique identifier for the partner', 'willsx'); ?></span>
		</p>

		<p>
			<label for="partner_url"><?php _e('Partner Website URL', 'willsx'); ?></label><br>
			<input type="url" id="partner_url" name="partner_url" value="<?php echo esc_url($partner_url); ?>" required>
		</p>

		<p>
			<label for="partner_api_key"><?php _e('API Key', 'willsx'); ?></label><br>
			<input type="text" id="partner_api_key" name="partner_api_key" value="<?php echo esc_attr($api_key); ?>" readonly>
			<button type="button" class="button" id="generate_api_key"><?php _e('Generate New Key', 'willsx'); ?></button>
		</p>

		<p>
			<label for="partner_active">
				<input type="checkbox" id="partner_active" name="partner_active" value="1" <?php checked($active, '1'); ?>>
				<?php _e('Partner Active', 'willsx'); ?>
			</label>
		</p>

		<h4><?php _e('Contact Information', 'willsx'); ?></h4>
		
		<p>
			<label for="partner_contact_name"><?php _e('Contact Name', 'willsx'); ?></label><br>
			<input type="text" id="partner_contact_name" name="partner_contact_name" value="<?php echo esc_attr($contact_name); ?>">
		</p>

		<p>
			<label for="partner_contact_email"><?php _e('Contact Email', 'willsx'); ?></label><br>
			<input type="email" id="partner_contact_email" name="partner_contact_email" value="<?php echo esc_attr($contact_email); ?>">
		</p>

		<p>
			<label for="partner_contact_phone"><?php _e('Contact Phone', 'willsx'); ?></label><br>
			<input type="tel" id="partner_contact_phone" name="partner_contact_phone" value="<?php echo esc_attr($contact_phone); ?>">
		</p>
	</div>

	<script>
	jQuery(document).ready(function($) {
		$('#generate_api_key').click(function() {
			var apiKey = 'wsx_' + Math.random().toString(36).substr(2, 9);
			$('#partner_api_key').val(apiKey);
		});
	});
	</script>
	<?php
}

/**
 * Save partner meta box data
 */
function willsx_save_partner_meta_box_data($post_id) {
	// Verify nonces
	if (!isset($_POST['willsx_partner_branding_meta_box_nonce']) || 
		!wp_verify_nonce($_POST['willsx_partner_branding_meta_box_nonce'], 'willsx_partner_branding_meta_box') ||
		!isset($_POST['willsx_partner_details_meta_box_nonce']) || 
		!wp_verify_nonce($_POST['willsx_partner_details_meta_box_nonce'], 'willsx_partner_details_meta_box')) {
		return;
	}

	// Check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Check permissions
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	// Save branding fields
	if (isset($_POST['partner_logo_id'])) {
		update_post_meta($post_id, '_partner_logo_id', sanitize_text_field($_POST['partner_logo_id']));
	}
	if (isset($_POST['partner_primary_color'])) {
		update_post_meta($post_id, '_partner_primary_color', sanitize_hex_color($_POST['partner_primary_color']));
	}
	if (isset($_POST['partner_secondary_color'])) {
		update_post_meta($post_id, '_partner_secondary_color', sanitize_hex_color($_POST['partner_secondary_color']));
	}
	if (isset($_POST['partner_accent_color'])) {
		update_post_meta($post_id, '_partner_accent_color', sanitize_hex_color($_POST['partner_accent_color']));
	}
	if (isset($_POST['partner_font_family'])) {
		update_post_meta($post_id, '_partner_font_family', sanitize_text_field($_POST['partner_font_family']));
	}

	// Save details fields
	if (isset($_POST['partner_id'])) {
		update_post_meta($post_id, '_partner_id', sanitize_text_field($_POST['partner_id']));
	}
	if (isset($_POST['partner_url'])) {
		update_post_meta($post_id, '_partner_url', esc_url_raw($_POST['partner_url']));
	}
	if (isset($_POST['partner_api_key'])) {
		update_post_meta($post_id, '_partner_api_key', sanitize_text_field($_POST['partner_api_key']));
	}
	update_post_meta($post_id, '_partner_active', isset($_POST['partner_active']) ? '1' : '0');
	
	if (isset($_POST['partner_contact_name'])) {
		update_post_meta($post_id, '_partner_contact_name', sanitize_text_field($_POST['partner_contact_name']));
	}
	if (isset($_POST['partner_contact_email'])) {
		update_post_meta($post_id, '_partner_contact_email', sanitize_email($_POST['partner_contact_email']));
	}
	if (isset($_POST['partner_contact_phone'])) {
		update_post_meta($post_id, '_partner_contact_phone', sanitize_text_field($_POST['partner_contact_phone']));
	}
}
add_action('save_post_partner', 'willsx_save_partner_meta_box_data');

/**
 * Add custom columns to partner list
 */
function willsx_partner_columns($columns) {
	$new_columns = array();
	foreach ($columns as $key => $value) {
		if ($key === 'title') {
			$new_columns[$key] = $value;
			$new_columns['partner_logo'] = __('Logo', 'willsx');
			$new_columns['partner_id'] = __('Partner ID', 'willsx');
			$new_columns['partner_active'] = __('Active', 'willsx');
		} else {
			$new_columns[$key] = $value;
		}
	}
	return $new_columns;
}
add_filter('manage_partner_posts_columns', 'willsx_partner_columns');

/**
 * Display custom column content
 */
function willsx_partner_column_content($column, $post_id) {
	switch ($column) {
		case 'partner_logo':
			$logo_id = get_post_meta($post_id, '_partner_logo_id', true);
			if ($logo_id) {
				echo wp_get_attachment_image($logo_id, array(50, 50));
			} else {
				echo '—';
			}
			break;
		case 'partner_id':
			echo esc_html(get_post_meta($post_id, '_partner_id', true));
			break;
		case 'partner_active':
			$active = get_post_meta($post_id, '_partner_active', true);
			echo $active ? '<span class="dashicons dashicons-yes" style="color: green;"></span>' : '<span class="dashicons dashicons-no" style="color: red;"></span>';
			break;
	}
}
add_action('manage_partner_posts_custom_column', 'willsx_partner_column_content', 10, 2);

/**
 * Make custom columns sortable
 */
function willsx_partner_sortable_columns($columns) {
	$columns['partner_id'] = 'partner_id';
	$columns['partner_active'] = 'partner_active';
	return $columns;
}
add_filter('manage_edit-partner_sortable_columns', 'willsx_partner_sortable_columns'); 