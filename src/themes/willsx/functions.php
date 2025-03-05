<?php
/**
 * WillsX functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WillsX
 */

if ( ! defined( 'WILLSX_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'WILLSX_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function willsx_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */
	load_theme_textdomain( 'willsx', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'willsx' ),
			'menu-2' => esc_html__( 'Footer', 'willsx' ),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'willsx_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add support for custom color scheme.
	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => esc_html__( 'Primary', 'willsx' ),
			'slug'  => 'primary',
			'color' => '#2563EB',
		),
		array(
			'name'  => esc_html__( 'Secondary', 'willsx' ),
			'slug'  => 'secondary',
			'color' => '#059669',
		),
		array(
			'name'  => esc_html__( 'Accent', 'willsx' ),
			'slug'  => 'accent',
			'color' => '#8B5CF6',
		),
		array(
			'name'  => esc_html__( 'Dark', 'willsx' ),
			'slug'  => 'dark',
			'color' => '#1F2937',
		),
		array(
			'name'  => esc_html__( 'Light', 'willsx' ),
			'slug'  => 'light',
			'color' => '#F9FAFB',
		),
	) );
}
add_action( 'after_setup_theme', 'willsx_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function willsx_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'willsx_content_width', 1200 );
}
add_action( 'after_setup_theme', 'willsx_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function willsx_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'willsx' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'willsx' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1', 'willsx' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Add widgets here to appear in the first footer column.', 'willsx' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 2', 'willsx' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here to appear in the second footer column.', 'willsx' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 3', 'willsx' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here to appear in the third footer column.', 'willsx' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'willsx_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function willsx_scripts() {
	// Enqueue Google Fonts
	wp_enqueue_style( 'willsx-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );

	// Main stylesheet
	wp_enqueue_style( 'willsx-style', get_stylesheet_uri(), array(), WILLSX_VERSION );
	wp_enqueue_style( 'willsx-main', get_template_directory_uri() . '/assets/css/main.css', array(), WILLSX_VERSION );

	// Theme JavaScript
	wp_enqueue_script( 'willsx-main', get_template_directory_uri() . '/assets/js/main.js', array(), WILLSX_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'willsx_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Co-branding functions
 */

/**
 * Get partner data for co-branding
 * 
 * @return array|null Partner data or null if no partner
 */
function willsx_get_partner_data() {
	// This would typically come from a database or API
	// For now, we'll check for a query parameter or cookie
	
	$partner_id = null;
	
	// Check for partner ID in query string
	if ( isset( $_GET['partner'] ) ) {
		$partner_id = sanitize_text_field( $_GET['partner'] );
		
		// Set a cookie to remember the partner
		if ( ! headers_sent() ) {
			setcookie( 'willsx_partner', $partner_id, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
		}
	} 
	// Check for partner ID in cookie
	elseif ( isset( $_COOKIE['willsx_partner'] ) ) {
		$partner_id = sanitize_text_field( $_COOKIE['willsx_partner'] );
	}
	
	// If we have a partner ID, get the partner data
	if ( $partner_id ) {
		// In a real implementation, this would query a database or API
		// For demonstration, we'll use a simple array of sample partners
		$partners = array(
			'acme' => array(
				'name' => 'ACME Financial Services',
				'logo' => get_template_directory_uri() . '/assets/images/partners/acme-logo.png',
			),
			'globex' => array(
				'name' => 'Globex Corporation',
				'logo' => get_template_directory_uri() . '/assets/images/partners/globex-logo.png',
			),
			'initech' => array(
				'name' => 'Initech',
				'logo' => get_template_directory_uri() . '/assets/images/partners/initech-logo.png',
			),
		);
		
		if ( isset( $partners[ $partner_id ] ) ) {
			return array_merge( array( 'id' => $partner_id ), $partners[ $partner_id ] );
		}
	}
	
	return null;
}

/**
 * Add partner data to the header
 * 
 * @param array $args Template arguments
 * @return array Modified template arguments
 */
function willsx_add_partner_data_to_header( $args ) {
	$partner_data = willsx_get_partner_data();
	
	if ( $partner_data ) {
		$args['partner_data'] = $partner_data;
	}
	
	return $args;
}
add_filter( 'willsx_header_args', 'willsx_add_partner_data_to_header' );

/**
 * Get header with partner data
 */
function willsx_get_header() {
	$args = array();
	$args = apply_filters( 'willsx_header_args', $args );
	
	get_header( null, $args );
} 