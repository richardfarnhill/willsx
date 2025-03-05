<?php
/**
 * Template Name: Homepage
 *
 * @package WillsX
 */

get_header();

// Get partner data if this is a partner page
$partner_id = willsx_get_current_partner_id();
$partner = $partner_id ? get_post($partner_id) : null;
?>

<main id="primary" class="site-main">
    <section class="hero-section">
        <div class="hero-container">
            <?php if ($partner) : ?>
                <div class="partner-hero">
                    <h1 class="hero-title">
                        <?php
                        printf(
                            /* translators: %s: partner name */
                            esc_html__('Welcome to %s\'s Will Writing Service', 'willsx'),
                            esc_html($partner->post_title)
                        );
                        ?>
                    </h1>
                    <div class="hero-description">
                        <?php echo wp_kses_post($partner->post_content); ?>
                    </div>
                </div>
            <?php else : ?>
                <h1 class="hero-title"><?php esc_html_e('Expert Will Writing Made Simple', 'willsx'); ?></h1>
                <div class="hero-description">
                    <p><?php esc_html_e('Create your legally valid will online with professional guidance and support.', 'willsx'); ?></p>
                </div>
            <?php endif; ?>

            <div class="hero-cta">
                <a href="<?php echo esc_url(home_url('/start-your-will')); ?>" class="button button-primary">
                    <?php esc_html_e('Start Your Will', 'willsx'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/learn-more')); ?>" class="button button-secondary">
                    <?php esc_html_e('Learn More', 'willsx'); ?>
                </a>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="section-container">
            <h2 class="section-title"><?php esc_html_e('Why Choose WillsX', 'willsx'); ?></h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="dashicons dashicons-shield"></span>
                    </div>
                    <h3><?php esc_html_e('Legal Expertise', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Created and reviewed by experienced legal professionals.', 'willsx'); ?></p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="dashicons dashicons-clock"></span>
                    </div>
                    <h3><?php esc_html_e('Quick & Easy', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Complete your will in as little as 15 minutes with our guided process.', 'willsx'); ?></p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="dashicons dashicons-lock"></span>
                    </div>
                    <h3><?php esc_html_e('Secure & Confidential', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Your information is protected with bank-level security.', 'willsx'); ?></p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="dashicons dashicons-update"></span>
                    </div>
                    <h3><?php esc_html_e('Free Updates', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Update your will anytime as your circumstances change.', 'willsx'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="process-section">
        <div class="section-container">
            <h2 class="section-title"><?php esc_html_e('How It Works', 'willsx'); ?></h2>
            
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3><?php esc_html_e('Answer Questions', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Complete our simple questionnaire about your wishes and circumstances.', 'willsx'); ?></p>
                </div>

                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3><?php esc_html_e('Review & Customize', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Review your will and make any necessary adjustments with expert guidance.', 'willsx'); ?></p>
                </div>

                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3><?php esc_html_e('Download & Sign', 'willsx'); ?></h3>
                    <p><?php esc_html_e('Download your will, sign it with witnesses, and it becomes legally valid.', 'willsx'); ?></p>
                </div>
            </div>

            <div class="process-cta">
                <a href="<?php echo esc_url(home_url('/start-your-will')); ?>" class="button button-primary">
                    <?php esc_html_e('Create Your Will Now', 'willsx'); ?>
                </a>
            </div>
        </div>
    </section>

    <?php if (!$partner) : ?>
    <section class="partners-section">
        <div class="section-container">
            <h2 class="section-title"><?php esc_html_e('Our Partners', 'willsx'); ?></h2>
            
            <div class="partners-grid">
                <?php
                $partners = get_posts(array(
                    'post_type' => 'partner',
                    'posts_per_page' => 6,
                    'meta_key' => '_partner_active',
                    'meta_value' => '1'
                ));

                foreach ($partners as $partner) {
                    $logo_id = get_post_meta($partner->ID, '_partner_logo_id', true);
                    if ($logo_id) {
                        echo '<div class="partner-logo-item">';
                        echo wp_get_attachment_image($logo_id, 'medium');
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <div class="partners-cta">
                <a href="<?php echo esc_url(home_url('/our-partners')); ?>" class="button button-secondary">
                    <?php esc_html_e('View All Partners', 'willsx'); ?>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="testimonials-section">
        <div class="section-container">
            <h2 class="section-title"><?php esc_html_e('What Our Clients Say', 'willsx'); ?></h2>
            
            <div class="testimonials-slider">
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p><?php esc_html_e('The process was straightforward and the guidance provided was excellent. I feel confident that my will reflects my wishes exactly.', 'willsx'); ?></p>
                    </div>
                    <div class="testimonial-author">
                        <p class="author-name">Sarah Thompson</p>
                        <p class="author-location">London, UK</p>
                    </div>
                </div>

                <div class="testimonial">
                    <div class="testimonial-content">
                        <p><?php esc_html_e('As someone who kept putting off making a will, WillsX made it incredibly easy. The customer support was fantastic throughout.', 'willsx'); ?></p>
                    </div>
                    <div class="testimonial-author">
                        <p class="author-name">James Wilson</p>
                        <p class="author-location">Manchester, UK</p>
                    </div>
                </div>

                <div class="testimonial">
                    <div class="testimonial-content">
                        <p><?php esc_html_e('I appreciated being able to take my time and make updates as needed. The platform is user-friendly and the process was clear.', 'willsx'); ?></p>
                    </div>
                    <div class="testimonial-author">
                        <p class="author-name">Emma Roberts</p>
                        <p class="author-location">Birmingham, UK</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="section-container">
            <h2 class="section-title"><?php esc_html_e('Ready to Secure Your Family\'s Future?', 'willsx'); ?></h2>
            <p class="section-description">
                <?php esc_html_e('Create your legally valid will today with our expert guidance and support.', 'willsx'); ?>
            </p>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/start-your-will')); ?>" class="button button-primary">
                    <?php esc_html_e('Start Your Will', 'willsx'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="button button-secondary">
                    <?php esc_html_e('Contact Us', 'willsx'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer(); 