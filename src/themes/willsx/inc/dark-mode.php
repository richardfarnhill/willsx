<?php
/**
 * Dark Mode Functionality for WillsX
 *
 * Adds dark mode toggle and functionality to the theme
 *
 * @package WillsX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class to handle dark mode functionality
 */
class WillsX_Dark_Mode {

	/**
	 * Whether dark mode is enabled in settings
	 *
	 * @var bool
	 */
	private $enabled = true;

	/**
	 * Default mode (light or dark)
	 *
	 * @var string
	 */
	private $default_mode = 'light';

	/**
	 * Whether to respect system preference
	 *
	 * @var bool
	 */
	private $respect_system_preference = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Get settings
		$this->enabled = get_option( 'willsx_dark_mode_enabled', true );
		$this->default_mode = get_option( 'willsx_dark_mode_default', 'light' );
		$this->respect_system_preference = get_option( 'willsx_dark_mode_respect_system', true );
		
		// Add hooks if enabled
		if ( $this->enabled ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'add_toggle_button' ) );
			add_action( 'wp_head', array( $this, 'add_initial_mode_script' ), 0 );
			add_filter( 'body_class', array( $this, 'add_body_class' ) );
		}
	}

	/**
	 * Enqueue dark mode scripts and styles
	 */
	public function enqueue_scripts() {
		// Enqueue dark mode styles
		wp_enqueue_style(
			'willsx-dark-mode',
			get_template_directory_uri() . '/assets/css/dark-mode.css',
			array( 'willsx-style' ),
			filemtime( get_template_directory() . '/assets/css/dark-mode.css' )
		);
		
		// Enqueue dark mode script
		wp_enqueue_script(
			'willsx-dark-mode',
			get_template_directory_uri() . '/assets/js/dark-mode.js',
			array( 'jquery' ),
			filemtime( get_template_directory() . '/assets/js/dark-mode.js' ),
			true
		);
		
		// Pass settings to script
		wp_localize_script(
			'willsx-dark-mode',
			'willsxDarkMode',
			array(
				'defaultMode' => $this->default_mode,
				'respectSystemPreference' => $this->respect_system_preference,
			)
		);
	}

	/**
	 * Add toggle button to footer
	 */
	public function add_toggle_button() {
		?>
		<button id="willsx-dark-mode-toggle" class="willsx-dark-mode-toggle" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'willsx' ); ?>">
			<span class="willsx-dark-mode-toggle-icon light">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="5"></circle>
					<line x1="12" y1="1" x2="12" y2="3"></line>
					<line x1="12" y1="21" x2="12" y2="23"></line>
					<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
					<line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
					<line x1="1" y1="12" x2="3" y2="12"></line>
					<line x1="21" y1="12" x2="23" y2="12"></line>
					<line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
					<line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
				</svg>
			</span>
			<span class="willsx-dark-mode-toggle-icon dark">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
				</svg>
			</span>
		</button>
		<?php
	}

	/**
	 * Add script to set initial mode before page load
	 * This prevents flash of wrong theme
	 */
	public function add_initial_mode_script() {
		?>
		<script>
		(function() {
			// Get saved mode from localStorage
			var savedMode = localStorage.getItem('willsxDarkMode');
			var defaultMode = '<?php echo esc_js( $this->default_mode ); ?>';
			var respectSystemPreference = <?php echo $this->respect_system_preference ? 'true' : 'false'; ?>;
			
			// Check if we should use system preference
			if (respectSystemPreference && !savedMode) {
				// Check system preference
				if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
					document.documentElement.classList.add('dark-mode');
				}
			} else if (savedMode === 'dark' || (savedMode === null && defaultMode === 'dark')) {
				// Use saved mode or default if none saved
				document.documentElement.classList.add('dark-mode');
			}
		})();
		</script>
		<?php
	}

	/**
	 * Add body class for dark mode
	 *
	 * @param array $classes Body classes.
	 * @return array Modified body classes.
	 */
	public function add_body_class( $classes ) {
		// We don't actually add the class here since we handle it via JS
		// But we add a class to indicate dark mode is enabled
		$classes[] = 'willsx-dark-mode-enabled';
		return $classes;
	}

	/**
	 * Update settings
	 *
	 * @param bool   $enabled Whether dark mode is enabled.
	 * @param string $default_mode Default mode (light or dark).
	 * @param bool   $respect_system_preference Whether to respect system preference.
	 * @return bool Success status.
	 */
	public function update_settings( $enabled, $default_mode, $respect_system_preference ) {
		$this->enabled = (bool) $enabled;
		$this->default_mode = in_array( $default_mode, array( 'light', 'dark' ), true ) ? $default_mode : 'light';
		$this->respect_system_preference = (bool) $respect_system_preference;
		
		update_option( 'willsx_dark_mode_enabled', $this->enabled );
		update_option( 'willsx_dark_mode_default', $this->default_mode );
		update_option( 'willsx_dark_mode_respect_system', $this->respect_system_preference );
		
		return true;
	}

	/**
	 * Get settings
	 *
	 * @return array Settings array.
	 */
	public function get_settings() {
		return array(
			'enabled' => $this->enabled,
			'default_mode' => $this->default_mode,
			'respect_system_preference' => $this->respect_system_preference,
		);
	}
}

/**
 * Initialize dark mode
 */
function willsx_init_dark_mode() {
	global $willsx_dark_mode;
	
	if ( ! isset( $willsx_dark_mode ) ) {
		$willsx_dark_mode = new WillsX_Dark_Mode();
	}
	
	return $willsx_dark_mode;
}
add_action( 'init', 'willsx_init_dark_mode' );

/**
 * Get dark mode instance
 *
 * @return WillsX_Dark_Mode Dark mode instance.
 */
function willsx_get_dark_mode() {
	global $willsx_dark_mode;
	
	if ( ! isset( $willsx_dark_mode ) ) {
		$willsx_dark_mode = willsx_init_dark_mode();
	}
	
	return $willsx_dark_mode;
}

/**
 * Update dark mode settings from admin
 *
 * @param bool   $enabled Whether dark mode is enabled.
 * @param string $default_mode Default mode (light or dark).
 * @param bool   $respect_system_preference Whether to respect system preference.
 * @return bool Success status.
 */
function willsx_update_dark_mode_settings( $enabled, $default_mode, $respect_system_preference ) {
	$dark_mode = willsx_get_dark_mode();
	return $dark_mode->update_settings( $enabled, $default_mode, $respect_system_preference );
}

/**
 * Create dark mode CSS file if it doesn't exist
 */
function willsx_create_dark_mode_css() {
	$css_file = get_template_directory() . '/assets/css/dark-mode.css';
	
	// Check if file exists
	if ( file_exists( $css_file ) ) {
		return;
	}
	
	// Create directory if it doesn't exist
	$css_dir = dirname( $css_file );
	if ( ! is_dir( $css_dir ) ) {
		wp_mkdir_p( $css_dir );
	}
	
	// Default dark mode CSS
	$css = <<<CSS
/**
 * Dark Mode Styles for WillsX
 */

:root {
	--dark-bg: #121212;
	--dark-bg-secondary: #1e1e1e;
	--dark-text: #e0e0e0;
	--dark-text-secondary: #aaaaaa;
	--dark-border: #333333;
	--dark-link: #90caf9;
	--dark-link-hover: #bbdefb;
	--dark-input-bg: #2c2c2c;
	--dark-input-border: #444444;
	--dark-button-bg: #2e7d32;
	--dark-button-text: #ffffff;
	--dark-button-hover: #388e3c;
}

/* Dark mode toggle button */
.willsx-dark-mode-toggle {
	position: fixed;
	bottom: 20px;
	right: 20px;
	width: 50px;
	height: 50px;
	border-radius: 50%;
	background-color: #333;
	color: #fff;
	border: none;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 999;
	box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
	transition: background-color 0.3s ease;
}

.willsx-dark-mode-toggle:hover {
	background-color: #444;
}

.willsx-dark-mode-toggle-icon {
	display: block;
}

.willsx-dark-mode-toggle-icon.dark {
	display: none;
}

.dark-mode .willsx-dark-mode-toggle-icon.light {
	display: none;
}

.dark-mode .willsx-dark-mode-toggle-icon.dark {
	display: block;
}

.dark-mode .willsx-dark-mode-toggle {
	background-color: #f0f0f0;
	color: #333;
}

.dark-mode .willsx-dark-mode-toggle:hover {
	background-color: #e0e0e0;
}

/* Dark mode styles */
.dark-mode {
	background-color: var(--dark-bg);
	color: var(--dark-text);
}

.dark-mode body {
	background-color: var(--dark-bg);
	color: var(--dark-text);
}

.dark-mode h1, .dark-mode h2, .dark-mode h3, 
.dark-mode h4, .dark-mode h5, .dark-mode h6 {
	color: var(--dark-text);
}

.dark-mode a {
	color: var(--dark-link);
}

.dark-mode a:hover, .dark-mode a:focus {
	color: var(--dark-link-hover);
}

.dark-mode input, .dark-mode textarea, .dark-mode select {
	background-color: var(--dark-input-bg);
	border-color: var(--dark-input-border);
	color: var(--dark-text);
}

.dark-mode button, .dark-mode .button, .dark-mode input[type="submit"] {
	background-color: var(--dark-button-bg);
	color: var(--dark-button-text);
}

.dark-mode button:hover, .dark-mode .button:hover, .dark-mode input[type="submit"]:hover {
	background-color: var(--dark-button-hover);
}

.dark-mode header, .dark-mode footer, .dark-mode .site-header, .dark-mode .site-footer {
	background-color: var(--dark-bg-secondary);
	border-color: var(--dark-border);
}

.dark-mode .site-title, .dark-mode .site-description {
	color: var(--dark-text);
}

.dark-mode .main-navigation a {
	color: var(--dark-text);
}

.dark-mode .main-navigation a:hover {
	color: var(--dark-link);
}

.dark-mode .entry-title a {
	color: var(--dark-text);
}

.dark-mode .entry-meta {
	color: var(--dark-text-secondary);
}

.dark-mode .widget {
	background-color: var(--dark-bg-secondary);
	border-color: var(--dark-border);
}

.dark-mode .widget-title {
	color: var(--dark-text);
	border-color: var(--dark-border);
}

.dark-mode .comment-body {
	background-color: var(--dark-bg-secondary);
	border-color: var(--dark-border);
}

.dark-mode .comment-metadata {
	color: var(--dark-text-secondary);
}

.dark-mode hr {
	background-color: var(--dark-border);
}

.dark-mode blockquote {
	border-color: var(--dark-border);
	background-color: var(--dark-bg-secondary);
}

.dark-mode table {
	border-color: var(--dark-border);
}

.dark-mode th, .dark-mode td {
	border-color: var(--dark-border);
}

.dark-mode pre, .dark-mode code {
	background-color: var(--dark-bg-secondary);
	color: var(--dark-text);
}

/* Partner branding adjustments for dark mode */
.dark-mode .partner-branding {
	background-color: var(--dark-bg-secondary);
}

/* Add more dark mode styles as needed */
CSS;
	
	// Write CSS to file
	file_put_contents( $css_file, $css );
}

/**
 * Create dark mode JS file if it doesn't exist
 */
function willsx_create_dark_mode_js() {
	$js_file = get_template_directory() . '/assets/js/dark-mode.js';
	
	// Check if file exists
	if ( file_exists( $js_file ) ) {
		return;
	}
	
	// Create directory if it doesn't exist
	$js_dir = dirname( $js_file );
	if ( ! is_dir( $js_dir ) ) {
		wp_mkdir_p( $js_dir );
	}
	
	// Default dark mode JS
	$js = <<<JS
/**
 * Dark Mode JavaScript for WillsX
 */
(function($) {
	'use strict';
	
	// Variables
	var darkModeToggle = $('#willsx-dark-mode-toggle');
	var htmlElement = $('html');
	var darkModeClass = 'dark-mode';
	var storageKey = 'willsxDarkMode';
	var defaultMode = willsxDarkMode.defaultMode || 'light';
	var respectSystemPreference = willsxDarkMode.respectSystemPreference || false;
	
	// Function to set dark mode
	function setDarkMode(isDark) {
		if (isDark) {
			htmlElement.addClass(darkModeClass);
			localStorage.setItem(storageKey, 'dark');
		} else {
			htmlElement.removeClass(darkModeClass);
			localStorage.setItem(storageKey, 'light');
		}
	}
	
	// Function to toggle dark mode
	function toggleDarkMode() {
		var isDarkMode = htmlElement.hasClass(darkModeClass);
		setDarkMode(!isDarkMode);
	}
	
	// Initialize dark mode
	function initDarkMode() {
		// Get saved mode from localStorage
		var savedMode = localStorage.getItem(storageKey);
		
		// Check if we should use system preference
		if (respectSystemPreference && !savedMode) {
			// Check system preference
			if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				setDarkMode(true);
			} else {
				setDarkMode(false);
			}
		} else if (savedMode === 'dark' || (savedMode === null && defaultMode === 'dark')) {
			// Use saved mode or default if none saved
			setDarkMode(true);
		} else {
			setDarkMode(false);
		}
		
		// Listen for system preference changes
		if (respectSystemPreference && window.matchMedia) {
			window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
				// Only change if user hasn't manually set a preference
				if (!localStorage.getItem(storageKey)) {
					setDarkMode(e.matches);
				}
			});
		}
	}
	
	// Set up event listeners
	function setupEventListeners() {
		darkModeToggle.on('click', function(e) {
			e.preventDefault();
			toggleDarkMode();
		});
	}
	
	// Initialize on document ready
	$(document).ready(function() {
		initDarkMode();
		setupEventListeners();
	});
	
})(jQuery);
JS;
	
	// Write JS to file
	file_put_contents( $js_file, $js );
}

// Create CSS and JS files on theme activation
add_action( 'after_switch_theme', 'willsx_create_dark_mode_css' );
add_action( 'after_switch_theme', 'willsx_create_dark_mode_js' );

// Also create them when this file is included (for development)
willsx_create_dark_mode_css();
willsx_create_dark_mode_js(); 