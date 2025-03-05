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
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widgets-grid">
                    <div class="footer-widget">
                        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                            <?php dynamic_sidebar( 'footer-1' ); ?>
                        <?php else : ?>
                            <h3 class="widget-title"><?php esc_html_e( 'About Us', 'willsx' ); ?></h3>
                            <p><?php esc_html_e( 'WillsX provides expert guidance on wills, trusts, and estate planning to help secure your family\'s future.', 'willsx' ); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="footer-widget">
                        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                            <?php dynamic_sidebar( 'footer-2' ); ?>
                        <?php else : ?>
                            <h3 class="widget-title"><?php esc_html_e( 'Quick Links', 'willsx' ); ?></h3>
                            <ul>
                                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'willsx' ); ?></a></li>
                                <li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( 'About', 'willsx' ); ?></a></li>
                                <li><a href="<?php echo esc_url( home_url( '/services' ) ); ?>"><?php esc_html_e( 'Services', 'willsx' ); ?></a></li>
                                <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'willsx' ); ?></a></li>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div class="footer-widget">
                        <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                            <?php dynamic_sidebar( 'footer-3' ); ?>
                        <?php else : ?>
                            <h3 class="widget-title"><?php esc_html_e( 'Contact Us', 'willsx' ); ?></h3>
                            <p><?php esc_html_e( 'Email: info@willsx.com', 'willsx' ); ?></p>
                            <p><?php esc_html_e( 'Phone: +44 123 456 7890', 'willsx' ); ?></p>
                            <p><?php esc_html_e( 'Address: 123 Estate Street, London, UK', 'willsx' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-grid">
                    <div class="site-info">
                        <p>
                            <?php
                            /* translators: %s: Current year. */
                            printf( esc_html__( '© %s WillsX. All rights reserved.', 'willsx' ), date_i18n( 'Y' ) );
                            ?>
                        </p>
                    </div><!-- .site-info -->
                    
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'menu-2',
                                'menu_id'        => 'footer-menu',
                                'depth'          => 1,
                                'fallback_cb'    => false,
                            )
                        );
                        ?>
                    </nav>
                </div>
            </div>
        </div><!-- .container -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php get_template_part( 'template-parts/components/cookie-notice' ); ?>

<?php wp_footer(); ?>

</body>
</html> 