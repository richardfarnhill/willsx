<?php
/**
 * WillsX Admin Dashboard
 *
 * Adds a custom admin dashboard for the WillsX theme
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register admin menu
 */
function willsx_admin_menu() {
    // Add main menu
    add_menu_page(
        __('WillsX Dashboard', 'willsx'),
        __('WillsX', 'willsx'),
        'manage_options',
        'willsx-dashboard',
        'willsx_dashboard_page',
        'dashicons-admin-customizer',
        30
    );

    // Add submenus
    add_submenu_page(
        'willsx-dashboard',
        __('Dashboard', 'willsx'),
        __('Dashboard', 'willsx'),
        'manage_options',
        'willsx-dashboard',
        'willsx_dashboard_page'
    );

    add_submenu_page(
        'willsx-dashboard',
        __('Partners', 'willsx'),
        __('Partners', 'willsx'),
        'manage_options',
        'edit.php?post_type=partner'
    );

    add_submenu_page(
        'willsx-dashboard',
        __('Auto Linker', 'willsx'),
        __('Auto Linker', 'willsx'),
        'manage_options',
        'willsx-autolinker',
        'willsx_autolinker_page'
    );

    add_submenu_page(
        'willsx-dashboard',
        __('Dark Mode', 'willsx'),
        __('Dark Mode', 'willsx'),
        'manage_options',
        'willsx-dark-mode',
        'willsx_dark_mode_page'
    );

    add_submenu_page(
        'willsx-dashboard',
        __('Theme Settings', 'willsx'),
        __('Theme Settings', 'willsx'),
        'manage_options',
        'willsx-settings',
        'willsx_settings_page'
    );

    // Add admin styles
    add_action('admin_enqueue_scripts', 'willsx_admin_styles');
}
add_action('admin_menu', 'willsx_admin_menu');

/**
 * Render dashboard page
 */
function willsx_dashboard_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get stats
    $partner_count = count(willsx_get_all_partners());
    $post_count = wp_count_posts('post')->publish;
    $page_count = wp_count_posts('page')->publish;

    ?>
    <div class="wrap willsx-admin-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="welcome-panel">
            <div class="welcome-panel-content">
                <h2><?php _e('Welcome to WillsX!', 'willsx'); ?></h2>
                <p class="about-description"><?php _e('Thank you for using the WillsX theme. Here you can manage all theme-specific settings.', 'willsx'); ?></p>
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column">
                        <h3><?php _e('Quick Links', 'willsx'); ?></h3>
                        <ul>
                            <li><a href="<?php echo admin_url('edit.php?post_type=partner'); ?>" class="button button-primary"><?php _e('Manage Partners', 'willsx'); ?></a></li>
                            <li><a href="<?php echo admin_url('admin.php?page=willsx-autolinker'); ?>" class="button"><?php _e('Auto Linker Settings', 'willsx'); ?></a></li>
                            <li><a href="<?php echo admin_url('admin.php?page=willsx-dark-mode'); ?>" class="button"><?php _e('Dark Mode Settings', 'willsx'); ?></a></li>
                            <li><a href="<?php echo admin_url('admin.php?page=willsx-settings'); ?>" class="button"><?php _e('Theme Settings', 'willsx'); ?></a></li>
                        </ul>
                    </div>
                    <div class="welcome-panel-column">
                        <h3><?php _e('Quick Stats', 'willsx'); ?></h3>
                        <ul>
                            <li><?php printf(_n('%s Partner', '%s Partners', $partner_count, 'willsx'), number_format_i18n($partner_count)); ?></li>
                            <li><?php printf(_n('%s Post', '%s Posts', $post_count, 'willsx'), number_format_i18n($post_count)); ?></li>
                            <li><?php printf(_n('%s Page', '%s Pages', $page_count, 'willsx'), number_format_i18n($page_count)); ?></li>
                        </ul>
                    </div>
                    <div class="welcome-panel-column welcome-panel-last">
                        <h3><?php _e('Documentation', 'willsx'); ?></h3>
                        <ul>
                            <li><a href="#" target="_blank"><?php _e('Theme Documentation', 'willsx'); ?></a></li>
                            <li><a href="#" target="_blank"><?php _e('Partner System Guide', 'willsx'); ?></a></li>
                            <li><a href="#" target="_blank"><?php _e('Auto Linker Guide', 'willsx'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render auto linker page
 */
function willsx_autolinker_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get auto linker instance
    $auto_linker = willsx_get_auto_linker();

    // Process form submission
    if (isset($_POST['willsx_save_autolinker']) && check_admin_referer('willsx_save_autolinker')) {
        // Save settings
        $enabled = isset($_POST['autolinker_enabled']) ? true : false;
        $max_links = isset($_POST['max_links']) ? absint($_POST['max_links']) : 5;
        $auto_linker->update_settings($enabled, $max_links);

        // Save keywords
        $keywords = array();
        if (isset($_POST['keywords']) && isset($_POST['urls'])) {
            $posted_keywords = array_map('sanitize_text_field', $_POST['keywords']);
            $posted_urls = array_map('esc_url_raw', $_POST['urls']);
            $keywords = array_combine($posted_keywords, $posted_urls);
        }
        $auto_linker->save_keywords($keywords);

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Auto linker settings saved successfully.', 'willsx') . '</p></div>';
    }

    // Get current settings
    $settings = $auto_linker->get_settings();
    $keywords = $auto_linker->get_keywords();

    ?>
    <div class="wrap willsx-admin-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post">
            <?php wp_nonce_field('willsx_save_autolinker'); ?>

            <h2><?php _e('Auto Linker Settings', 'willsx'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Enable Auto Linker', 'willsx'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="autolinker_enabled" value="1" <?php checked($settings['enabled']); ?>>
                            <?php _e('Automatically add links to keywords in post content', 'willsx'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Maximum Links', 'willsx'); ?></th>
                    <td>
                        <input type="number" name="max_links" value="<?php echo esc_attr($settings['max_links']); ?>" min="1" max="20" step="1">
                        <p class="description"><?php _e('Maximum number of auto-generated links per post', 'willsx'); ?></p>
                    </td>
                </tr>
            </table>

            <h2><?php _e('Keywords', 'willsx'); ?></h2>
            <table class="wp-list-table widefat fixed" id="willsx-keywords-table">
                <thead>
                    <tr>
                        <th><?php _e('Keyword', 'willsx'); ?></th>
                        <th><?php _e('URL', 'willsx'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($keywords)) : ?>
                        <?php foreach ($keywords as $keyword => $url) : ?>
                            <tr>
                                <td>
                                    <input type="text" name="keywords[]" value="<?php echo esc_attr($keyword); ?>" class="regular-text">
                                </td>
                                <td>
                                    <input type="url" name="urls[]" value="<?php echo esc_url($url); ?>" class="regular-text">
                                </td>
                                <td>
                                    <button type="button" class="button remove-row"><?php _e('Remove', 'willsx'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="empty-row screen-reader-text">
                        <td>
                            <input type="text" name="keywords[]" class="regular-text">
                        </td>
                        <td>
                            <input type="url" name="urls[]" class="regular-text">
                        </td>
                        <td>
                            <button type="button" class="button remove-row"><?php _e('Remove', 'willsx'); ?></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>
                <button type="button" class="button" id="add-keyword"><?php _e('Add Keyword', 'willsx'); ?></button>
            </p>

            <?php submit_button(__('Save Settings', 'willsx'), 'primary', 'willsx_save_autolinker'); ?>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Add row
        $('#add-keyword').on('click', function() {
            var row = $('.empty-row.screen-reader-text').clone(true);
            row.removeClass('empty-row screen-reader-text');
            row.insertBefore('#willsx-keywords-table tbody>tr:last');
        });

        // Remove row
        $('.remove-row').on('click', function() {
            $(this).closest('tr').remove();
        });
    });
    </script>
    <?php
}

/**
 * Render dark mode page
 */
function willsx_dark_mode_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get dark mode instance
    $dark_mode = willsx_get_dark_mode();

    // Process form submission
    if (isset($_POST['willsx_save_dark_mode']) && check_admin_referer('willsx_save_dark_mode')) {
        $enabled = isset($_POST['dark_mode_enabled']) ? true : false;
        $default_mode = isset($_POST['default_mode']) ? sanitize_text_field($_POST['default_mode']) : 'light';
        $respect_system = isset($_POST['respect_system']) ? true : false;

        $dark_mode->update_settings($enabled, $default_mode, $respect_system);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Dark mode settings saved successfully.', 'willsx') . '</p></div>';
    }

    // Get current settings
    $settings = $dark_mode->get_settings();

    ?>
    <div class="wrap willsx-admin-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post">
            <?php wp_nonce_field('willsx_save_dark_mode'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Enable Dark Mode', 'willsx'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="dark_mode_enabled" value="1" <?php checked($settings['enabled']); ?>>
                            <?php _e('Allow users to switch between light and dark mode', 'willsx'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Default Mode', 'willsx'); ?></th>
                    <td>
                        <select name="default_mode">
                            <option value="light" <?php selected($settings['default_mode'], 'light'); ?>><?php _e('Light', 'willsx'); ?></option>
                            <option value="dark" <?php selected($settings['default_mode'], 'dark'); ?>><?php _e('Dark', 'willsx'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('System Preference', 'willsx'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="respect_system" value="1" <?php checked($settings['respect_system_preference']); ?>>
                            <?php _e('Respect system dark mode preference', 'willsx'); ?>
                        </label>
                        <p class="description"><?php _e('If enabled, the theme will use the system dark mode preference by default', 'willsx'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Save Settings', 'willsx'), 'primary', 'willsx_save_dark_mode'); ?>
        </form>
    </div>
    <?php
}

/**
 * Render settings page
 */
function willsx_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Process form submission
    if (isset($_POST['willsx_save_settings']) && check_admin_referer('willsx_save_settings')) {
        $settings = array(
            'dark_mode_enabled' => isset($_POST['dark_mode_enabled']) ? 1 : 0,
            'auto_linker_enabled' => isset($_POST['auto_linker_enabled']) ? 1 : 0,
            'partner_system_enabled' => isset($_POST['partner_system_enabled']) ? 1 : 0,
        );
        
        update_option('willsx_theme_settings', $settings);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully.', 'willsx') . '</p></div>';
    }

    // Get current settings
    $settings = get_option('willsx_theme_settings', array(
        'dark_mode_enabled' => 1,
        'auto_linker_enabled' => 1,
        'partner_system_enabled' => 1,
    ));
    
    ?>
    <div class="wrap willsx-admin-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post">
            <?php wp_nonce_field('willsx_save_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Dark Mode', 'willsx'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="dark_mode_enabled" value="1" <?php checked($settings['dark_mode_enabled'], 1); ?>>
                            <?php _e('Enable Dark Mode', 'willsx'); ?>
                        </label>
                        <p class="description"><?php _e('Allow users to switch between light and dark mode.', 'willsx'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Auto Linker', 'willsx'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="auto_linker_enabled" value="1" <?php checked($settings['auto_linker_enabled'], 1); ?>>
                            <?php _e('Enable Auto Linker', 'willsx'); ?>
                        </label>
                        <p class="description"><?php _e('Automatically add links to specified keywords in blog posts.', 'willsx'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Partner System', 'willsx'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="partner_system_enabled" value="1" <?php checked($settings['partner_system_enabled'], 1); ?>>
                            <?php _e('Enable Partner System', 'willsx'); ?>
                        </label>
                        <p class="description"><?php _e('Enable the partner co-branding system.', 'willsx'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Save Settings', 'willsx'), 'primary', 'willsx_save_settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Get all partners
 * 
 * @return array Array of partner posts
 */
function willsx_get_all_partners() {
    $partners = get_posts(array(
        'post_type' => 'partner',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));
    
    return $partners;
}

/**
 * Add admin styles
 */
function willsx_admin_styles() {
    // Only load on WillsX admin pages
    $screen = get_current_screen();
    if (strpos($screen->id, 'willsx') === false && $screen->id !== 'partner') {
        return;
    }
    
    // Create admin CSS file if it doesn't exist
    willsx_create_admin_css();
    
    // Enqueue admin styles
    wp_enqueue_style('willsx-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), WILLSX_VERSION);
    
    // Enqueue media scripts for image upload
    wp_enqueue_media();
}

/**
 * Create admin CSS file if it doesn't exist
 */
function willsx_create_admin_css() {
    $css_file = get_template_directory() . '/assets/css/admin.css';
    
    // Check if the directory exists, if not create it
    $css_dir = dirname($css_file);
    if (!file_exists($css_dir)) {
        wp_mkdir_p($css_dir);
    }
    
    // Only create the file if it doesn't exist
    if (!file_exists($css_file)) {
        $css_content = <<<EOT
/**
 * WillsX Admin Styles
 */
.willsx-admin-wrap {
    max-width: 1200px;
}

.willsx-admin-wrap .nav-tab-wrapper {
    margin-bottom: 20px;
}

.willsx-admin-wrap .tab-content {
    display: none;
}

.willsx-admin-wrap .tab-content:first-of-type {
    display: block;
}

.willsx-admin-wrap .form-table th {
    width: 200px;
}

.willsx-admin-wrap .welcome-panel {
    padding-bottom: 20px;
}

.willsx-admin-wrap .welcome-panel-column {
    width: 32%;
    min-width: 200px;
}

.willsx-admin-wrap .welcome-panel-column ul {
    margin-top: 10px;
}

.willsx-admin-wrap .welcome-panel-column li {
    margin-bottom: 10px;
}

.willsx-admin-wrap .welcome-panel-column .button {
    margin-right: 5px;
}

.willsx-admin-wrap .color-preview {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    vertical-align: middle;
    margin-right: 5px;
    border: 1px solid #ddd;
}

/* Auto Linker styles */
#willsx-keywords-table {
    margin-top: 10px;
}

#willsx-keywords-table td {
    vertical-align: middle;
    padding: 10px;
}

#willsx-keywords-table .remove-row {
    color: #a00;
}

#willsx-keywords-table .remove-row:hover {
    color: #dc3232;
}

/* Dark Mode preview */
.dark-mode-preview {
    background: #121212;
    color: #e0e0e0;
    padding: 20px;
    border-radius: 4px;
    margin-top: 10px;
}

.dark-mode-preview h3 {
    color: #fff;
    margin-top: 0;
}
EOT;
        
        // Write the file
        file_put_contents($css_file, $css_content);
    }
}

// Initialize admin dashboard
add_action('admin_init', function() {
    // Create necessary directories
    $dirs = array(
        get_template_directory() . '/assets',
        get_template_directory() . '/assets/css',
        get_template_directory() . '/assets/js',
        get_template_directory() . '/assets/images',
        get_template_directory() . '/assets/images/partners',
    );
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
}); 