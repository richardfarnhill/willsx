<?php
/**
 * WillsX Dark Mode
 *
 * Adds dark mode toggle functionality to the theme
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WillsX_Dark_Mode
 */
class WillsX_Dark_Mode {
    /**
     * Settings
     *
     * @var array
     */
    private $settings = array();

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize settings
        $this->settings = get_option('willsx_dark_mode_settings', array(
            'enabled' => true,
            'default_mode' => 'light',
            'toggle_position' => 'header',
            'auto_detect' => true,
            'custom_colors' => false,
            'dark_background' => '#121212',
            'dark_text' => '#ffffff',
            'dark_accent' => '#bb86fc',
        ));

        // Add actions and filters
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'add_inline_styles'));
        add_action('wp_footer', array($this, 'add_toggle_button'));
        
        // Add settings page
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue if dark mode is enabled
        if (!$this->settings['enabled']) {
            return;
        }

        wp_enqueue_script(
            'willsx-dark-mode',
            get_template_directory_uri() . '/assets/js/dark-mode.js',
            array('jquery'),
            WILLSX_VERSION,
            true
        );

        // Pass settings to script
        wp_localize_script('willsx-dark-mode', 'willsxDarkMode', array(
            'defaultMode' => $this->settings['default_mode'],
            'autoDetect' => $this->settings['auto_detect'],
        ));
    }

    /**
     * Add inline styles
     */
    public function add_inline_styles() {
        // Only add styles if dark mode is enabled
        if (!$this->settings['enabled']) {
            return;
        }

        // Base dark mode styles
        $css = '
            /* Dark Mode Toggle Button */
            .dark-mode-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: transparent;
                border: none;
                cursor: pointer;
                padding: 0;
                transition: background-color 0.3s;
            }
            
            .dark-mode-toggle:hover {
                background-color: rgba(0, 0, 0, 0.1);
            }
            
            .dark-mode-toggle svg {
                width: 24px;
                height: 24px;
                fill: currentColor;
            }
            
            /* Dark Mode Styles */
            [data-theme="dark"] {
                --background-color: ' . esc_attr($this->settings['dark_background']) . ';
                --text-color: ' . esc_attr($this->settings['dark_text']) . ';
                --accent-color: ' . esc_attr($this->settings['dark_accent']) . ';
                --border-color: rgba(255, 255, 255, 0.1);
                --card-background: rgba(255, 255, 255, 0.05);
                color-scheme: dark;
            }
            
            [data-theme="dark"] body {
                background-color: var(--background-color);
                color: var(--text-color);
            }
            
            [data-theme="dark"] a {
                color: var(--accent-color);
            }
            
            [data-theme="dark"] .site-header,
            [data-theme="dark"] .site-footer {
                background-color: rgba(0, 0, 0, 0.2);
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .entry-content {
                color: var(--text-color);
            }
            
            [data-theme="dark"] input[type="text"],
            [data-theme="dark"] input[type="email"],
            [data-theme="dark"] input[type="url"],
            [data-theme="dark"] input[type="password"],
            [data-theme="dark"] input[type="search"],
            [data-theme="dark"] input[type="number"],
            [data-theme="dark"] input[type="tel"],
            [data-theme="dark"] input[type="range"],
            [data-theme="dark"] input[type="date"],
            [data-theme="dark"] input[type="month"],
            [data-theme="dark"] input[type="week"],
            [data-theme="dark"] input[type="time"],
            [data-theme="dark"] input[type="datetime"],
            [data-theme="dark"] input[type="datetime-local"],
            [data-theme="dark"] input[type="color"],
            [data-theme="dark"] textarea,
            [data-theme="dark"] select {
                background-color: rgba(255, 255, 255, 0.05);
                border-color: var(--border-color);
                color: var(--text-color);
            }
            
            [data-theme="dark"] button,
            [data-theme="dark"] input[type="button"],
            [data-theme="dark"] input[type="reset"],
            [data-theme="dark"] input[type="submit"] {
                background-color: var(--accent-color);
                color: #000;
            }
            
            [data-theme="dark"] .wp-block-button__link {
                background-color: var(--accent-color);
                color: #000;
            }
            
            [data-theme="dark"] .wp-block-quote {
                border-color: var(--accent-color);
            }
            
            [data-theme="dark"] .wp-block-table table {
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .wp-block-table th,
            [data-theme="dark"] .wp-block-table td {
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .wp-block-separator {
                border-color: var(--border-color);
            }
            
            [data-theme="dark"] .wp-block-code {
                background-color: rgba(255, 255, 255, 0.05);
                border-color: var(--border-color);
                color: var(--text-color);
            }
        ';

        // Add custom colors if enabled
        if ($this->settings['custom_colors']) {
            $css .= '
                /* Custom Dark Mode Colors */
                [data-theme="dark"] {
                    --background-color: ' . esc_attr($this->settings['dark_background']) . ';
                    --text-color: ' . esc_attr($this->settings['dark_text']) . ';
                    --accent-color: ' . esc_attr($this->settings['dark_accent']) . ';
                }
            ';
        }

        echo '<style id="willsx-dark-mode-css">' . $css . '</style>';
    }

    /**
     * Add toggle button to the site
     */
    public function add_toggle_button() {
        // Only add toggle if dark mode is enabled
        if (!$this->settings['enabled']) {
            return;
        }

        // If toggle position is not footer and we're in the footer, return
        if ($this->settings['toggle_position'] !== 'footer' && current_filter() === 'wp_footer') {
            return;
        }

        // If toggle position is not header and we're in the header, return
        if ($this->settings['toggle_position'] !== 'header' && current_filter() === 'wp_head') {
            return;
        }

        ?>
        <button class="dark-mode-toggle" aria-label="<?php esc_attr_e('Toggle Dark Mode', 'willsx'); ?>">
            <span class="sun-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.03 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41.39.39 1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41.39.39 1.03.39 1.41 0l1.06-1.06z"/>
                </svg>
            </span>
            <span class="moon-icon" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/>
                </svg>
            </span>
        </button>
        <?php
    }

    /**
     * Add settings page
     */
    public function add_settings_page() {
        add_submenu_page(
            'willsx-dashboard',
            __('Dark Mode Settings', 'willsx'),
            __('Dark Mode', 'willsx'),
            'manage_options',
            'willsx-dark-mode',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('willsx_dark_mode', 'willsx_dark_mode_settings');

        add_settings_section(
            'willsx_dark_mode_general',
            __('General Settings', 'willsx'),
            array($this, 'render_general_section'),
            'willsx_dark_mode'
        );

        add_settings_field(
            'enabled',
            __('Enable Dark Mode', 'willsx'),
            array($this, 'render_enabled_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_general'
        );

        add_settings_field(
            'default_mode',
            __('Default Mode', 'willsx'),
            array($this, 'render_default_mode_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_general'
        );

        add_settings_field(
            'toggle_position',
            __('Toggle Position', 'willsx'),
            array($this, 'render_toggle_position_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_general'
        );

        add_settings_field(
            'auto_detect',
            __('Auto Detect', 'willsx'),
            array($this, 'render_auto_detect_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_general'
        );

        add_settings_section(
            'willsx_dark_mode_colors',
            __('Color Settings', 'willsx'),
            array($this, 'render_colors_section'),
            'willsx_dark_mode'
        );

        add_settings_field(
            'custom_colors',
            __('Use Custom Colors', 'willsx'),
            array($this, 'render_custom_colors_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_colors'
        );

        add_settings_field(
            'dark_background',
            __('Background Color', 'willsx'),
            array($this, 'render_dark_background_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_colors'
        );

        add_settings_field(
            'dark_text',
            __('Text Color', 'willsx'),
            array($this, 'render_dark_text_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_colors'
        );

        add_settings_field(
            'dark_accent',
            __('Accent Color', 'willsx'),
            array($this, 'render_dark_accent_field'),
            'willsx_dark_mode',
            'willsx_dark_mode_colors'
        );
    }

    /**
     * Render general section
     */
    public function render_general_section() {
        echo '<p>' . __('Configure how dark mode works on your site.', 'willsx') . '</p>';
    }

    /**
     * Render colors section
     */
    public function render_colors_section() {
        echo '<p>' . __('Customize the colors used in dark mode.', 'willsx') . '</p>';
    }

    /**
     * Render enabled field
     */
    public function render_enabled_field() {
        $value = isset($this->settings['enabled']) ? $this->settings['enabled'] : true;
        echo '<input type="checkbox" name="willsx_dark_mode_settings[enabled]" value="1" ' . checked($value, true, false) . ' />';
        echo '<p class="description">' . __('Enable dark mode functionality on your site.', 'willsx') . '</p>';
    }

    /**
     * Render default mode field
     */
    public function render_default_mode_field() {
        $value = isset($this->settings['default_mode']) ? $this->settings['default_mode'] : 'light';
        echo '<select name="willsx_dark_mode_settings[default_mode]">';
        echo '<option value="light" ' . selected($value, 'light', false) . '>' . __('Light', 'willsx') . '</option>';
        echo '<option value="dark" ' . selected($value, 'dark', false) . '>' . __('Dark', 'willsx') . '</option>';
        echo '</select>';
        echo '<p class="description">' . __('The default mode to use when a user first visits the site.', 'willsx') . '</p>';
    }

    /**
     * Render toggle position field
     */
    public function render_toggle_position_field() {
        $value = isset($this->settings['toggle_position']) ? $this->settings['toggle_position'] : 'header';
        echo '<select name="willsx_dark_mode_settings[toggle_position]">';
        echo '<option value="header" ' . selected($value, 'header', false) . '>' . __('Header', 'willsx') . '</option>';
        echo '<option value="footer" ' . selected($value, 'footer', false) . '>' . __('Footer', 'willsx') . '</option>';
        echo '</select>';
        echo '<p class="description">' . __('Where to display the dark mode toggle button.', 'willsx') . '</p>';
    }

    /**
     * Render auto detect field
     */
    public function render_auto_detect_field() {
        $value = isset($this->settings['auto_detect']) ? $this->settings['auto_detect'] : true;
        echo '<input type="checkbox" name="willsx_dark_mode_settings[auto_detect]" value="1" ' . checked($value, true, false) . ' />';
        echo '<p class="description">' . __('Automatically detect user\'s system preference for dark mode.', 'willsx') . '</p>';
    }

    /**
     * Render custom colors field
     */
    public function render_custom_colors_field() {
        $value = isset($this->settings['custom_colors']) ? $this->settings['custom_colors'] : false;
        echo '<input type="checkbox" name="willsx_dark_mode_settings[custom_colors]" value="1" ' . checked($value, true, false) . ' />';
        echo '<p class="description">' . __('Use custom colors for dark mode instead of the default ones.', 'willsx') . '</p>';
    }

    /**
     * Render dark background field
     */
    public function render_dark_background_field() {
        $value = isset($this->settings['dark_background']) ? $this->settings['dark_background'] : '#121212';
        echo '<input type="color" name="willsx_dark_mode_settings[dark_background]" value="' . esc_attr($value) . '" />';
        echo '<p class="description">' . __('Background color for dark mode.', 'willsx') . '</p>';
    }

    /**
     * Render dark text field
     */
    public function render_dark_text_field() {
        $value = isset($this->settings['dark_text']) ? $this->settings['dark_text'] : '#ffffff';
        echo '<input type="color" name="willsx_dark_mode_settings[dark_text]" value="' . esc_attr($value) . '" />';
        echo '<p class="description">' . __('Text color for dark mode.', 'willsx') . '</p>';
    }

    /**
     * Render dark accent field
     */
    public function render_dark_accent_field() {
        $value = isset($this->settings['dark_accent']) ? $this->settings['dark_accent'] : '#bb86fc';
        echo '<input type="color" name="willsx_dark_mode_settings[dark_accent]" value="' . esc_attr($value) . '" />';
        echo '<p class="description">' . __('Accent color for dark mode (links, buttons, etc).', 'willsx') . '</p>';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('willsx_dark_mode');
                do_settings_sections('willsx_dark_mode');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the dark mode
$willsx_dark_mode = new WillsX_Dark_Mode();

/**
 * Create the dark mode JavaScript file if it doesn't exist
 */
function willsx_create_dark_mode_js() {
    $js_file = get_template_directory() . '/assets/js/dark-mode.js';
    
    // Check if the directory exists, if not create it
    $js_dir = dirname($js_file);
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
    
    // Only create the file if it doesn't exist
    if (!file_exists($js_file)) {
        $js_content = <<<EOT
/**
 * WillsX Dark Mode
 */
(function($) {
    'use strict';
    
    // Variables
    var darkModeToggle = $('.dark-mode-toggle');
    var sunIcon = $('.sun-icon');
    var moonIcon = $('.moon-icon');
    var htmlElement = $('html');
    var storageKey = 'willsxDarkMode';
    var defaultMode = willsxDarkMode.defaultMode || 'light';
    var autoDetect = willsxDarkMode.autoDetect || false;
    
    // Functions
    function setDarkMode(isDark) {
        if (isDark) {
            htmlElement.attr('data-theme', 'dark');
            sunIcon.hide();
            moonIcon.show();
        } else {
            htmlElement.attr('data-theme', 'light');
            moonIcon.hide();
            sunIcon.show();
        }
        
        // Save preference to localStorage
        localStorage.setItem(storageKey, isDark ? 'dark' : 'light');
    }
    
    function toggleDarkMode() {
        var isDark = htmlElement.attr('data-theme') === 'dark';
        setDarkMode(!isDark);
    }
    
    function initDarkMode() {
        var savedMode = localStorage.getItem(storageKey);
        
        if (savedMode) {
            // Use saved preference
            setDarkMode(savedMode === 'dark');
        } else if (autoDetect) {
            // Check system preference
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            setDarkMode(prefersDark);
        } else {
            // Use default
            setDarkMode(defaultMode === 'dark');
        }
    }
    
    // Event listeners
    darkModeToggle.on('click', toggleDarkMode);
    
    // Listen for system preference changes
    if (autoDetect) {
        window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) {
            // Only apply if user hasn't manually set a preference
            if (!localStorage.getItem(storageKey)) {
                setDarkMode(e.matches);
            }
        });
    }
    
    // Initialize
    initDarkMode();
    
})(jQuery);
EOT;
        
        // Write the file
        file_put_contents($js_file, $js_content);
    }
}
add_action('after_switch_theme', 'willsx_create_dark_mode_js');
add_action('admin_init', 'willsx_create_dark_mode_js'); 