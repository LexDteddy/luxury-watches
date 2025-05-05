<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage vinart
 */
?>

<?php
    //Default value
    $blog_read_more    = false;
    $blog_post_content = 'show_content';
add_action('acf/init', function() {
	$blog_read_more    = get_field('gg_blog_show_read_more','option', false);
    $blog_post_content = get_field('gg_blog_post_content','option', 'show_content'); 
});
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	

		<?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
            </a>
        </div><!-- .post-thumbnail -->
        <?php endif; ?>

        <div class="post-content-wrapper">

        <header class="entry-header">
            <?php
            if ( 'post' === get_post_type() ) {
                echo '<div class="entry-meta">'.vinart_time_link().'</div><!-- .entry-meta -->';
            };
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            ?>
        </header><!-- .entry-header -->

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div><!-- .entry-summary -->

        <?php if ( $blog_read_more ) : ?>
            <div class="entry-read-more">
                <a class="btn btn-primary" href="<?php echo get_permalink(); ?>"><?php esc_html_e('Read more', 'vinart'); ?></a>
            </div>
        <?php endif; ?>

        </div>
</article><!-- article -->
