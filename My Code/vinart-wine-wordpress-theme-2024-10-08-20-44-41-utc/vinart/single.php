<?php
/**
 * Default Post Template
 *
 * @package WordPress
 * @subpackage vinart
 */
get_header();
?>


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
            while (have_posts()) : the_post();
                get_template_part( 'parts/post-formats/part', 'single' );
            endwhile; // end of the loop.
                        
            vinart_page_sidebar();
            ?>
            
        </div><!-- end page container -->
        
    </section>
<?php get_footer(); ?>