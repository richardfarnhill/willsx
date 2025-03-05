<?php
/**
 * WillsX Theme Customizer
 *
 * @package WillsX
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function willsx_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'willsx_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'willsx_customize_partial_blogdescription',
			)
		);
	}

	// Add Theme Options Panel
	$wp_customize->add_panel(
		'willsx_theme_options',
		array(
			'title'       => esc_html__( 'Theme Options', 'willsx' ),
			'description' => esc_html__( 'Configure theme settings', 'willsx' ),
			'priority'    => 130,
		)
	);

	// Add Header Options Section
	$wp_customize->add_section(
		'willsx_header_options',
		array(
			'title'       => esc_html__( 'Header Options', 'willsx' ),
			'description' => esc_html__( 'Configure header settings', 'willsx' ),
			'panel'       => 'willsx_theme_options',
			'priority'    => 10,
		)
	);

	// Add Sticky Header Option
	$wp_customize->add_setting(
		'willsx_sticky_header',
		array(
			'default'           => true,
			'sanitize_callback' => 'willsx_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'willsx_sticky_header',
		array(
			'label'       => esc_html__( 'Enable Sticky Header', 'willsx' ),
			'description' => esc_html__( 'Keep the header visible when scrolling down', 'willsx' ),
			'section'     => 'willsx_header_options',
			'type'        => 'checkbox',
		)
	);

	// Add Footer Options Section
	$wp_customize->add_section(
		'willsx_footer_options',
		array(
			'title'       => esc_html__( 'Footer Options', 'willsx' ),
			'description' => esc_html__( 'Configure footer settings', 'willsx' ),
			'panel'       => 'willsx_theme_options',
			'priority'    => 20,
		)
	);

	// Add Footer Copyright Text
	$wp_customize->add_setting(
		'willsx_footer_copyright',
		array(
			'default'           => sprintf( esc_html__( '© %s WillsX. All rights reserved.', 'willsx' ), date_i18n( 'Y' ) ),
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'willsx_footer_copyright',
		array(
			'label'       => esc_html__( 'Copyright Text', 'willsx' ),
			'description' => esc_html__( 'Enter your copyright text. Use %s for the current year.', 'willsx' ),
			'section'     => 'willsx_footer_options',
			'type'        => 'textarea',
		)
	);

	// Add Cookie Notice Options Section
	$wp_customize->add_section(
		'willsx_cookie_options',
		array(
			'title'       => esc_html__( 'Cookie Notice Options', 'willsx' ),
			'description' => esc_html__( 'Configure cookie notice settings', 'willsx' ),
			'panel'       => 'willsx_theme_options',
			'priority'    => 30,
		)
	);

	// Add Cookie Notice Enable Option
	$wp_customize->add_setting(
		'willsx_cookie_notice_enable',
		array(
			'default'           => true,
			'sanitize_callback' => 'willsx_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'willsx_cookie_notice_enable',
		array(
			'label'       => esc_html__( 'Enable Cookie Notice', 'willsx' ),
			'description' => esc_html__( 'Show the cookie consent notice to visitors', 'willsx' ),
			'section'     => 'willsx_cookie_options',
			'type'        => 'checkbox',
		)
	);

	// Add Cookie Notice Text
	$wp_customize->add_setting(
		'willsx_cookie_notice_text',
		array(
			'default'           => esc_html__( 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.', 'willsx' ),
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'willsx_cookie_notice_text',
		array(
			'label'       => esc_html__( 'Cookie Notice Text', 'willsx' ),
			'description' => esc_html__( 'Text to display in the cookie notice', 'willsx' ),
			'section'     => 'willsx_cookie_options',
			'type'        => 'textarea',
		)
	);

	// Add Color Options Section
	$wp_customize->add_section(
		'willsx_color_options',
		array(
			'title'       => esc_html__( 'Color Options', 'willsx' ),
			'description' => esc_html__( 'Customize theme colors', 'willsx' ),
			'panel'       => 'willsx_theme_options',
			'priority'    => 40,
		)
	);

	// Add Primary Color Option
	$wp_customize->add_setting(
		'willsx_primary_color',
		array(
			'default'           => '#2563EB',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'willsx_primary_color',
			array(
				'label'       => esc_html__( 'Primary Color', 'willsx' ),
				'description' => esc_html__( 'Main theme color used for links and buttons', 'willsx' ),
				'section'     => 'willsx_color_options',
			)
		)
	);

	// Add Secondary Color Option
	$wp_customize->add_setting(
		'willsx_secondary_color',
		array(
			'default'           => '#059669',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'willsx_secondary_color',
			array(
				'label'       => esc_html__( 'Secondary Color', 'willsx' ),
				'description' => esc_html__( 'Secondary theme color used for accents', 'willsx' ),
				'section'     => 'willsx_color_options',
			)
		)
	);
}
add_action( 'customize_register', 'willsx_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function willsx_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function willsx_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function willsx_customize_preview_js() {
	wp_enqueue_script( 'willsx-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), WILLSX_VERSION, true );
}
add_action( 'customize_preview_init', 'willsx_customize_preview_js' );

/**
 * Sanitize checkbox values
 *
 * @param bool $checked Whether the checkbox is checked.
 * @return bool
 */
function willsx_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Generate CSS from customizer settings
 */
function willsx_customizer_css() {
	$primary_color   = get_theme_mod( 'willsx_primary_color', '#2563EB' );
	$secondary_color = get_theme_mod( 'willsx_secondary_color', '#059669' );

	$css = '
		:root {
			--color-primary: ' . esc_attr( $primary_color ) . ';
			--color-primary-dark: ' . esc_attr( willsx_adjust_brightness( $primary_color, -20 ) ) . ';
			--color-primary-light: ' . esc_attr( willsx_adjust_brightness( $primary_color, 20 ) ) . ';
			--color-secondary: ' . esc_attr( $secondary_color ) . ';
			--color-secondary-dark: ' . esc_attr( willsx_adjust_brightness( $secondary_color, -20 ) ) . ';
			--color-secondary-light: ' . esc_attr( willsx_adjust_brightness( $secondary_color, 20 ) ) . ';
		}
	';

	wp_add_inline_style( 'willsx-main', $css );
}
add_action( 'wp_enqueue_scripts', 'willsx_customizer_css' );

/**
 * Adjust brightness of a hex color
 *
 * @param string $hex Hex color code.
 * @param int    $steps Steps to adjust brightness (negative for darker, positive for lighter).
 * @return string Adjusted hex color
 */
function willsx_adjust_brightness( $hex, $steps ) {
	// Remove # if present
	$hex = ltrim( $hex, '#' );

	// Convert to RGB
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	// Adjust brightness
	$r = max( 0, min( 255, $r + $steps ) );
	$g = max( 0, min( 255, $g + $steps ) );
	$b = max( 0, min( 255, $b + $steps ) );

	// Convert back to hex
	return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
} 