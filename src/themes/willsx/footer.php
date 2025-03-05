<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WillsX
 */

?>
        </div><!-- .container -->
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="footer-container">
            <div class="footer-branding">
                <?php
                $partner_id = willsx_get_current_partner_id();
                if ($partner_id) {
                    $partner = get_post($partner_id);
                    $logo_id = get_post_meta($partner_id, '_partner_logo_id', true);
                    if ($logo_id) {
                        echo wp_get_attachment_image($logo_id, 'medium', false, array('class' => 'partner-footer-logo'));
                    }
                }
                
                if (has_custom_logo()) {
                    echo '<div class="footer-logo">';
                    the_custom_logo();
                    echo '</div>';
                }
                ?>
            </div>

            <div class="footer-widgets">
                <div class="footer-widget-area">
                    <h3 class="widget-title"><?php esc_html_e('Quick Links', 'willsx'); ?></h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-1',
                        'menu_class'     => 'footer-menu',
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>

                <div class="footer-widget-area">
                    <h3 class="widget-title"><?php esc_html_e('Services', 'willsx'); ?></h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-2',
                        'menu_class'     => 'footer-menu',
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>

                <div class="footer-widget-area">
                    <h3 class="widget-title"><?php esc_html_e('Support', 'willsx'); ?></h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-3',
                        'menu_class'     => 'footer-menu',
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>

                <div class="footer-widget-area contact-info">
                    <h3 class="widget-title"><?php esc_html_e('Contact Us', 'willsx'); ?></h3>
                    <?php
                    if ($partner_id) {
                        $contact_name = get_post_meta($partner_id, '_partner_contact_name', true);
                        $contact_email = get_post_meta($partner_id, '_partner_contact_email', true);
                        $contact_phone = get_post_meta($partner_id, '_partner_contact_phone', true);
                        
                        if ($contact_name || $contact_email || $contact_phone) {
                            echo '<div class="contact-details">';
                            if ($contact_name) {
                                printf('<p class="contact-name">%s</p>', esc_html($contact_name));
                            }
                            if ($contact_email) {
                                printf('<p class="contact-email"><a href="mailto:%1$s">%1$s</a></p>', esc_attr($contact_email));
                            }
                            if ($contact_phone) {
                                printf('<p class="contact-phone"><a href="tel:%1$s">%1$s</a></p>', esc_attr($contact_phone));
                            }
                            echo '</div>';
                        }
                    } else {
                        // Default WillsX contact information
                        ?>
                        <div class="contact-details">
                            <p class="contact-email">
                                <a href="mailto:contact@willsx.com">contact@willsx.com</a>
                            </p>
                            <p class="contact-phone">
                                <a href="tel:+441234567890">+44 (0) 123 456 7890</a>
                            </p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-info">
                    <div class="copyright">
                        <?php
                        printf(
                            /* translators: %1$s: current year, %2$s: site name */
                            esc_html__('© %1$s %2$s. All rights reserved.', 'willsx'),
                            date_i18n('Y'),
                            get_bloginfo('name')
                        );
                        ?>
                    </div>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-bottom',
                        'menu_class'     => 'footer-bottom-menu',
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>

                <?php if (!$partner_id) : ?>
                    <div class="partner-cta">
                        <p><?php esc_html_e('Are you a legal professional?', 'willsx'); ?></p>
                        <a href="<?php echo esc_url(home_url('/become-a-partner')); ?>" class="button partner-button">
                            <?php esc_html_e('Become a Partner', 'willsx'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php get_template_part( 'template-parts/components/cookie-notice' ); ?>

<?php wp_footer(); ?>

</body>
</html> 