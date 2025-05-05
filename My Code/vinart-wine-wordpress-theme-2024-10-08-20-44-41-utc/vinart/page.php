<?php
/**
 * Default Page
 * Description: Page template with a content container and right sidebar.
 *
 * @package WordPress
 * @subpackage vinart
 */
get_header(); ?>

<section id="content" class="site-content">

    <?php
    /**
     * Functions hooked into vinart_subheader action
     *
     * @hooked vinart_page_header                      - 0
     */
    do_action( 'vinart_subheader' );
    ?>

    <div class="page-content">
        <?php
        while ( have_posts() ) : the_post();
            get_template_part( 'parts/part', 'page' );
        endwhile;
        ?>

        <?php vinart_page_sidebar(); ?>
    </div><!-- end page container -->
    
</section>

<?php get_footer(); ?>