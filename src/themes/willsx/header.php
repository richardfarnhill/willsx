<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WillsX
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<?php
	// Get partner branding if this is a partner page
	$partner_id = willsx_get_current_partner_id();
	if ($partner_id) {
		$partner = get_post($partner_id);
		$logo_id = get_post_meta($partner_id, '_partner_logo_id', true);
		$primary_color = get_post_meta($partner_id, '_partner_primary_color', true);
		$secondary_color = get_post_meta($partner_id, '_partner_secondary_color', true);
		$accent_color = get_post_meta($partner_id, '_partner_accent_color', true);
		$font_family = get_post_meta($partner_id, '_partner_font_family', true);
		?>
		<style>
			:root {
				--partner-primary: <?php echo esc_attr($primary_color); ?>;
				--partner-secondary: <?php echo esc_attr($secondary_color); ?>;
				--partner-accent: <?php echo esc_attr($accent_color); ?>;
				--partner-font: <?php echo esc_attr($font_family); ?>;
			}
		</style>
		<?php
	}
	?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if (willsx_show_jurisdiction_warning()) : ?>
<div class="jurisdiction-warning">
    <div class="container">
        <p><?php esc_html_e('This website provides information about legal services in England and Wales only. The information provided may not be applicable to other jurisdictions.', 'willsx'); ?></p>
        <button class="close-warning"><?php esc_html_e('I understand', 'willsx'); ?></button>
    </div>
</div>
<?php endif; ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e('Skip to content', 'willsx'); ?>
    </a>

    <header id="masthead" class="site-header">
        <div class="header-container">
            <div class="site-branding">
                <?php if ($partner_id && $logo_id) : ?>
                    <div class="partner-logo">
                        <?php echo wp_get_attachment_image($logo_id, 'full'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="site-logo">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <nav id="site-navigation" class="main-navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="menu-toggle-icon"></span>
                    <span class="screen-reader-text"><?php esc_html_e('Menu', 'willsx'); ?></span>
                </button>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container_class' => 'primary-menu-container',
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>

            <div class="header-actions">
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(admin_url()); ?>" class="dashboard-link">
                        <span class="dashicons dashicons-dashboard"></span>
                        <?php esc_html_e('Dashboard', 'willsx'); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="login-link">
                        <span class="dashicons dashicons-admin-users"></span>
                        <?php esc_html_e('Login', 'willsx'); ?>
                    </a>
                <?php endif; ?>
                
                <button id="dark-mode-toggle" class="dark-mode-toggle" aria-label="<?php esc_attr_e('Toggle dark mode', 'willsx'); ?>">
                    <span class="dark-mode-icon sun"></span>
                    <span class="dark-mode-icon moon"></span>
                </button>
            </div>
        </div>

        <?php if ($partner_id) : ?>
            <div class="partner-banner">
                <div class="partner-banner-content">
                    <?php
                    printf(
                        /* translators: %s: partner name */
                        esc_html__('You are viewing %s\'s branded portal', 'willsx'),
                        esc_html($partner->post_title)
                    );
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <div id="content" class="site-content">
        <div class="container">
<?php
// Display breadcrumbs if not on homepage
if (!is_front_page()) {
    willsx_breadcrumbs();
}
?> 