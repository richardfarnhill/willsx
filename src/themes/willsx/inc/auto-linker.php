<?php
/**
 * Auto-Linker Functionality for WillsX
 *
 * Automatically links keywords in post content to relevant internal pages
 *
 * @package WillsX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class to handle automatic keyword linking in post content
 */
class WillsX_Auto_Linker {

	/**
	 * Keywords to URLs mapping
	 *
	 * @var array
	 */
	private $keywords = array();

	/**
	 * Maximum number of links per post
	 *
	 * @var int
	 */
	private $max_links = 5;

	/**
	 * Whether auto-linking is enabled
	 *
	 * @var bool
	 */
	private $enabled = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Get settings
		$this->enabled = get_option( 'willsx_autolinker_enabled', true );
		$this->max_links = get_option( 'willsx_autolinker_max_links', 5 );
		
		// Load keywords
		$this->load_keywords();
		
		// Add content filter if enabled
		if ( $this->enabled ) {
			add_filter( 'the_content', array( $this, 'process_content' ) );
		}
	}

	/**
	 * Load keywords from database
	 */
	private function load_keywords() {
		$saved_keywords = get_option( 'willsx_autolinker_keywords', array() );
		
		if ( ! empty( $saved_keywords ) && is_array( $saved_keywords ) ) {
			$this->keywords = $saved_keywords;
		} else {
			// Default keywords if none are set
			$this->keywords = array(
				'will' => home_url( '/services/wills/' ),
				'estate planning' => home_url( '/services/estate-planning/' ),
				'power of attorney' => home_url( '/services/power-of-attorney/' ),
				'probate' => home_url( '/services/probate/' ),
				'inheritance tax' => home_url( '/services/inheritance-tax/' ),
			);
			
			// Save defaults
			update_option( 'willsx_autolinker_keywords', $this->keywords );
		}
	}

	/**
	 * Process post content and add links
	 *
	 * @param string $content Post content.
	 * @return string Modified content with links.
	 */
	public function process_content( $content ) {
		// Don't process if no keywords or in admin or feed
		if ( empty( $this->keywords ) || is_admin() || is_feed() ) {
			return $content;
		}
		
		// Don't process if not a single post or page
		if ( ! is_singular( array( 'post', 'page' ) ) ) {
			return $content;
		}
		
		// Sort keywords by length (longest first) to avoid partial matches
		$keywords = $this->keywords;
		uksort( $keywords, function( $a, $b ) {
			return strlen( $b ) - strlen( $a );
		} );
		
		$link_count = 0;
		
		// Process each keyword
		foreach ( $keywords as $keyword => $url ) {
			// Skip if max links reached
			if ( $link_count >= $this->max_links ) {
				break;
			}
			
			// Skip empty keywords or URLs
			if ( empty( $keyword ) || empty( $url ) ) {
				continue;
			}
			
			// Prepare regex pattern - match keyword not already in a link
			$pattern = '/(?<!<a[^>]*>)(?<!\w)(' . preg_quote( $keyword, '/' ) . ')(?!\w)(?![^<]*<\/a>)/i';
			
			// Check if keyword exists in content
			if ( preg_match( $pattern, $content ) ) {
				// Replace only first occurrence
				$replacement = '<a href="' . esc_url( $url ) . '" class="willsx-autolink">${1}</a>';
				$content = preg_replace( $pattern, $replacement, $content, 1 );
				$link_count++;
			}
		}
		
		return $content;
	}

	/**
	 * Save keywords to database
	 *
	 * @param array $keywords Keywords array.
	 * @return bool Success status.
	 */
	public function save_keywords( $keywords ) {
		if ( ! is_array( $keywords ) ) {
			return false;
		}
		
		// Sanitize keywords and URLs
		$sanitized = array();
		foreach ( $keywords as $keyword => $url ) {
			if ( ! empty( $keyword ) && ! empty( $url ) ) {
				$sanitized[ sanitize_text_field( $keyword ) ] = esc_url_raw( $url );
			}
		}
		
		$this->keywords = $sanitized;
		return update_option( 'willsx_autolinker_keywords', $sanitized );
	}

	/**
	 * Update settings
	 *
	 * @param bool $enabled Whether auto-linking is enabled.
	 * @param int  $max_links Maximum number of links per post.
	 * @return bool Success status.
	 */
	public function update_settings( $enabled, $max_links ) {
		$this->enabled = (bool) $enabled;
		$this->max_links = absint( $max_links );
		
		update_option( 'willsx_autolinker_enabled', $this->enabled );
		update_option( 'willsx_autolinker_max_links', $this->max_links );
		
		// Update filter if enabled status changed
		if ( $this->enabled ) {
			if ( ! has_filter( 'the_content', array( $this, 'process_content' ) ) ) {
				add_filter( 'the_content', array( $this, 'process_content' ) );
			}
		} else {
			remove_filter( 'the_content', array( $this, 'process_content' ) );
		}
		
		return true;
	}

	/**
	 * Get current keywords
	 *
	 * @return array Keywords array.
	 */
	public function get_keywords() {
		return $this->keywords;
	}

	/**
	 * Get settings
	 *
	 * @return array Settings array.
	 */
	public function get_settings() {
		return array(
			'enabled' => $this->enabled,
			'max_links' => $this->max_links,
		);
	}
}

/**
 * Initialize auto-linker
 */
function willsx_init_auto_linker() {
	global $willsx_auto_linker;
	
	if ( ! isset( $willsx_auto_linker ) ) {
		$willsx_auto_linker = new WillsX_Auto_Linker();
	}
	
	return $willsx_auto_linker;
}
add_action( 'init', 'willsx_init_auto_linker' );

/**
 * Get auto-linker instance
 *
 * @return WillsX_Auto_Linker Auto-linker instance.
 */
function willsx_get_auto_linker() {
	global $willsx_auto_linker;
	
	if ( ! isset( $willsx_auto_linker ) ) {
		$willsx_auto_linker = willsx_init_auto_linker();
	}
	
	return $willsx_auto_linker;
}

/**
 * Save auto-linker keywords from admin
 *
 * @param array $keywords Keywords array.
 * @return bool Success status.
 */
function willsx_save_auto_linker_keywords( $keywords ) {
	$auto_linker = willsx_get_auto_linker();
	return $auto_linker->save_keywords( $keywords );
}

/**
 * Update auto-linker settings from admin
 *
 * @param bool $enabled Whether auto-linking is enabled.
 * @param int  $max_links Maximum number of links per post.
 * @return bool Success status.
 */
function willsx_update_auto_linker_settings( $enabled, $max_links ) {
	$auto_linker = willsx_get_auto_linker();
	return $auto_linker->update_settings( $enabled, $max_links );
}

/**
 * Add auto-linker CSS
 */
function willsx_auto_linker_styles() {
	// Only add if auto-linker is enabled
	$auto_linker = willsx_get_auto_linker();
	$settings = $auto_linker->get_settings();
	
	if ( ! $settings['enabled'] ) {
		return;
	}
	
	// Add inline styles for auto-linked keywords
	$css = "
	.willsx-autolink {
		border-bottom: 1px dotted;
		text-decoration: none;
	}
	.willsx-autolink:hover {
		border-bottom: 1px solid;
	}";
	
	wp_add_inline_style( 'willsx-style', $css );
}
add_action( 'wp_enqueue_scripts', 'willsx_auto_linker_styles', 20 ); 