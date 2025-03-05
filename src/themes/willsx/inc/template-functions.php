<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package WillsX
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function willsx_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Add a class if a partner is present
	if ( willsx_get_partner_data() ) {
		$classes[] = 'has-partner';
	}

	return $classes;
}
add_filter( 'body_class', 'willsx_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function willsx_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'willsx_pingback_header' );

/**
 * Change the excerpt length
 */
function willsx_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'willsx_excerpt_length' );

/**
 * Change the excerpt more string
 */
function willsx_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'willsx_excerpt_more' );

/**
 * Add custom image sizes
 */
function willsx_add_image_sizes() {
	add_image_size( 'willsx-featured', 1200, 600, true );
	add_image_size( 'willsx-card', 600, 400, true );
}
add_action( 'after_setup_theme', 'willsx_add_image_sizes' );

/**
 * Add schema markup to the body
 */
function willsx_add_schema_markup() {
	// Check if we're in the header
	if ( ! did_action( 'wp_body_open' ) ) {
		return;
	}

	// Get the schema type
	$schema = 'WebPage';

	if ( is_home() || is_archive() || is_attachment() || is_tax() || is_single() ) {
		$schema = 'Blog';
	} elseif ( is_author() ) {
		$schema = 'ProfilePage';
	} elseif ( is_search() ) {
		$schema = 'SearchResultsPage';
	}

	// Apply filters for custom schema
	$schema = apply_filters( 'willsx_schema_type', $schema );

	// Output the schema
	echo 'itemscope itemtype="https://schema.org/' . esc_attr( $schema ) . '"';
}
add_action( 'wp_body_open', 'willsx_add_schema_markup' );

/**
 * Add preconnect for Google Fonts.
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function willsx_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'willsx-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'willsx_resource_hints', 10, 2 );

/**
 * Modify the "read more" link text
 */
function willsx_modify_read_more_link() {
	return '<a class="read-more" href="' . get_permalink() . '">' . __( 'Read More', 'willsx' ) . '</a>';
}
add_filter( 'the_content_more_link', 'willsx_modify_read_more_link' );

/**
 * Add a wrapper around the site content
 */
function willsx_wrap_site_content() {
	echo '<div class="site-content-inner">';
}
add_action( 'willsx_before_main_content', 'willsx_wrap_site_content' );

/**
 * Close the wrapper around the site content
 */
function willsx_close_site_content() {
	echo '</div><!-- .site-content-inner -->';
}
add_action( 'willsx_after_main_content', 'willsx_close_site_content' ); 