<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WillsX
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Prints HTML with meta information for the current post-date/time.
 */
if (!function_exists('willsx_posted_on')) {
    function willsx_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x('Posted on %s', 'post date', 'willsx'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Prints HTML with meta information for the current author.
 */
if (!function_exists('willsx_posted_by')) {
    function willsx_posted_by() {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x('by %s', 'post author', 'willsx'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
if (!function_exists('willsx_entry_footer')) {
    function willsx_entry_footer() {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'willsx'));
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'willsx') . '</span>', $categories_list);
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html__(', ', 'willsx'));
            if ($tags_list) {
                /* translators: 1: list of tags. */
                printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'willsx') . '</span>', $tags_list);
            }
        }

        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'willsx'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit <span class="screen-reader-text">%s</span>', 'willsx'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
}

/**
 * Display SVG icons
 *
 * @param string $icon Icon name.
 * @param array  $args Optional arguments.
 */
function willsx_icon($icon, $args = []) {
    // SVG icon handling code will be implemented here
    // For now, this is a placeholder
    echo '<span class="willsx-icon willsx-icon-' . esc_attr($icon) . '"></span>';
}

/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function willsx_post_thumbnail() {
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail(); ?>
        </div><!-- .post-thumbnail -->

    <?php else : ?>

        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail(
                'post-thumbnail',
                array(
                    'alt' => the_title_attribute(
                        array(
                            'echo' => false,
                        )
                    ),
                )
            );
            ?>
        </a>

        <?php
    endif; // End is_singular().
}

/**
 * Displays the breadcrumbs
 */
function willsx_breadcrumbs() {
    // Breadcrumbs implementation will be added here
    // This is a placeholder for now
    echo '<div class="breadcrumbs">Breadcrumbs placeholder</div>';
}

/**
 * Get co-branding information if available
 */
function willsx_get_cobranding() {
    // This function will get co-branding information based on URL parameters or session
    // This is a placeholder for now
    return false;
}

/**
 * Display a CTA button
 * 
 * @param string $text Button text
 * @param string $url  Button URL
 * @param string $type Button type (primary, secondary, etc.)
 */
function willsx_cta_button($text, $url, $type = 'primary') {
    echo '<a href="' . esc_url($url) . '" class="btn btn-' . esc_attr($type) . '">' . esc_html($text) . '</a>';
}

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif; 