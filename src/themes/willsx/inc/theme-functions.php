<?php
/**
 * WillsX Theme Functions
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom theme-specific classes to body
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function willsx_custom_body_classes($classes) {
    // Add a class for singular pages
    if (is_singular()) {
        $classes[] = 'singular';
    }

    // Add a class for the color scheme
    $classes[] = 'color-scheme-default';

    // Add a class if co-branding is active
    if (willsx_get_cobranding()) {
        $classes[] = 'has-cobranding';
        $classes[] = 'cobranding-' . sanitize_title(willsx_get_cobranding()['partner']);
    }

    // Add a class for the current page template
    if (is_page_template()) {
        $template = get_page_template_slug();
        $template = str_replace('.php', '', $template);
        $template = str_replace('template-', '', $template);
        $classes[] = 'template-' . sanitize_title($template);
    }

    return $classes;
}
add_filter('body_class', 'willsx_custom_body_classes');

/**
 * Add custom classes to posts
 *
 * @param array $classes Existing post classes.
 * @return array Modified post classes.
 */
function willsx_post_classes($classes) {
    // Add a class for posts with thumbnails
    if (has_post_thumbnail()) {
        $classes[] = 'has-thumbnail';
    }

    return $classes;
}
add_filter('post_class', 'willsx_post_classes');

/**
 * Geolocation detection for displaying region-specific warnings
 * (England and Wales vs. other jurisdictions)
 *
 * @return string|bool The detected region or false if unavailable
 */
function willsx_detect_region() {
    // This will be implemented with proper geolocation service
    // For now, this is a placeholder
    return 'england'; // Default for testing
}

/**
 * Check if user needs legal jurisdiction warning
 *
 * @return bool Whether to show jurisdiction warning
 */
function willsx_show_jurisdiction_warning() {
    $region = willsx_detect_region();
    
    // Show warning for non-England/Wales visitors
    return ($region && !in_array($region, ['england', 'wales']));
}

/**
 * Add custom image sizes
 */
function willsx_add_image_sizes() {
    // Featured post size
    add_image_size('willsx-featured', 1200, 630, true);
    
    // Partner logo size
    add_image_size('willsx-partner-logo', 300, 150, false);
    
    // Team member photo size
    add_image_size('willsx-team', 400, 400, true);
}
add_action('after_setup_theme', 'willsx_add_image_sizes');

/**
 * Dark mode toggle functionality
 */
function willsx_dark_mode_script() {
    ?>
    <script>
        // Check for saved theme preference or prefer-color-scheme
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        const storedTheme = localStorage.getItem('willsxTheme');
        
        // If the user has explicitly chosen light or dark mode
        if (storedTheme) {
            document.documentElement.setAttribute('data-theme', storedTheme);
        } 
        // If they haven't been explicit and prefer dark mode
        else if (prefersDarkScheme.matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        
        // Function to toggle between light and dark mode
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            let targetTheme = 'light';
            
            if (currentTheme === 'light') {
                targetTheme = 'dark';
            }
            
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('willsxTheme', targetTheme);
        }
        
        // Make function available globally
        window.willsxToggleTheme = toggleTheme;
    </script>
    <?php
}
add_action('wp_head', 'willsx_dark_mode_script');

/**
 * Auto-linking for blog posts
 * 
 * @param string $content Post content
 * @return string Modified content with auto-links
 */
function willsx_auto_internal_linking($content) {
    // Only process content for single posts
    if (!is_singular('post')) {
        return $content;
    }
    
    // This function will scan content and add internal links
    // This is a placeholder - will implement full functionality later
    
    return $content;
}
add_filter('the_content', 'willsx_auto_internal_linking');

/**
 * Add custom rewrite rules for partner pages
 */
function willsx_add_rewrite_rules() {
    // Add rewrite rule for partner landing pages
    add_rewrite_rule('^partner/([^/]+)/?$', 'index.php?pagename=partner-landing&partner=$matches[1]', 'top');
}
add_action('init', 'willsx_add_rewrite_rules');

/**
 * Add custom query vars
 *
 * @param array $vars Existing query vars.
 * @return array Modified query vars.
 */
function willsx_add_query_vars($vars) {
    $vars[] = 'partner';
    $vars[] = 'referral';
    return $vars;
}
add_filter('query_vars', 'willsx_add_query_vars');

/**
 * Flush rewrite rules on theme activation
 */
function willsx_rewrite_flush() {
    willsx_add_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'willsx_rewrite_flush'); 