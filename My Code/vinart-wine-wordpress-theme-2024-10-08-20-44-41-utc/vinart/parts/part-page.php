<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage vinart
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<?php if (!is_search()) : ?>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'vinart' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
	<?php else: ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	<?php endif; ?>
	
	<?php 
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	?>
	
</article><!-- #post -->