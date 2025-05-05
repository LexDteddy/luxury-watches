<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage vinart
 */

    use VinartTheme\Classes\Vinart_Helper;

    $single_post_featured_image         = Vinart_Helper::get_option( 'single_post_featured_image', 'no' );
    $single_post_meta                   = Vinart_Helper::get_option( 'single_post_meta', 'yes' );
    $single_post_meta_tags              = Vinart_Helper::get_option( 'single_post_meta_tags', 'yes' );
    $single_post_navigation             = Vinart_Helper::get_option( 'single_post_navigation', 'yes' );
    $single_post_navigation_next_button = Vinart_Helper::get_option( 'single_post_navigation_next_button', 'Next article' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	

		<?php if ( '' !== get_the_post_thumbnail() && $single_post_featured_image == 'yes' ) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
            </a>
        </div><!-- .post-thumbnail -->
        <?php endif; ?>

        <div class="post-content-wrapper">
            <div class="entry-content">
                <?php
                /* translators: %s: Name of current post */
                the_content( sprintf(
                    esc_html__( 'Continue reading %s', 'vinart' ),
                    the_title( '<span class="screen-reader-text">', '</span>', false )
                ) );

                wp_link_pages( array(
                    'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'vinart' ) . '</span>',
                    'after'       => '</div>',
                    'link_before' => '<span>',
                    'link_after'  => '</span>',
                    'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'vinart' ) . ' </span>%',
                    'separator'   => '<span class="screen-reader-text">, </span>',
                ) );
            ?>
            </div><!-- .entry-content -->
        </div>

		<?php 
        if ( $single_post_meta == 'yes' && $single_post_meta_tags == 'yes'  ) {
            vinart_entry_footer();
        }
        ?>

        <?php if ( $single_post_navigation == 'yes') : ?>

        <?php vinart_next_post_nav($single_post_navigation_next_button); ?>

        <?php do_action( 'vinart_social_share' ); ?>

        <?php endif; ?>

        <?php comments_template( '', true ); ?>

</article><!-- article -->
