<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage vinart
 */

    use VinartTheme\Classes\Vinart_Helper;

        $archive_post_featured_image   = Vinart_Helper::get_option( 'archive_post_featured_image', 'yes' );
        $archive_post_meta             = Vinart_Helper::get_option( 'archive_post_meta', 'yes' );
        $archive_post_meta_date        = Vinart_Helper::get_option( 'archive_post_meta_date', 'yes' );
        $archive_post_meta_category    = Vinart_Helper::get_option( 'archive_post_meta_category', 'yes' );
        $archive_post_content          = Vinart_Helper::get_option( 'archive_post_content', 'full_content' );
        $archive_post_excerpt_count    = Vinart_Helper::get_option( 'archive_post_excerpt_count', '12' );
        $archive_read_more_button      = Vinart_Helper::get_option( 'archive_read_more_button', 'no' );
        $archive_read_more_button_text = Vinart_Helper::get_option( 'archive_read_more_button_text', 'Read more' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	

		<?php if ( '' !== get_the_post_thumbnail() && $archive_post_featured_image == 'yes' ) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
            </a>
        </div><!-- .post-thumbnail -->
        <?php endif; ?>

        <div class="post-content-wrapper">
        <div class="entry-header">
            <?php
            if ( 'post' === get_post_type() && $archive_post_meta == 'yes' && $archive_post_meta_category == 'yes' ) {
                vinart_entry_header();
            };
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            ?>
        </div><!-- .entry-header -->

        <?php if ( $archive_post_content  == 'full_content' ) : ?>
            <div class="entry-content">
                <?php
                /* translators: %s: Name of current post */
                the_content( sprintf(
                    esc_html__( 'Continue reading %s', 'vinart' ),
                    the_title( '<span class="screen-reader-text">', '</span>', false )
                ) );
                ?>
            </div><!-- .entry-content -->
        <?php elseif ( has_excerpt() && $archive_post_content  == 'excerpt' ): ?>
            <div class="entry-summary">
                <?php echo wp_trim_words(get_the_excerpt(), $archive_post_excerpt_count['number']); ?>
            </div><!-- .entry-summary -->
        <?php endif; ?>
        
            <div class="entry-meta">
                <?php if ( 'post' === get_post_type() && $archive_post_meta == 'yes' && $archive_post_meta_date == 'yes' ) {
                        echo vinart_time_link();
                }; ?>
                <?php if ($archive_read_more_button == 'yes') : ?>
                    <a class="link-with-arrow" href="<?php echo get_permalink(); ?>"><?php echo esc_html($archive_read_more_button_text); ?>
                        <?php echo vinart_get_icons('arrow'); ?>
                    </a>
                <?php endif; ?>
            </div><!-- .entry-meta -->

        </div>

</article><!-- article -->
