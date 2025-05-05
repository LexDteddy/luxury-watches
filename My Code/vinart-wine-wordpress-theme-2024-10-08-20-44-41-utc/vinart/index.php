<?php
/**
 * Description: Default Index template to display loop of blog posts
 *
 * @package WordPress
 * @subpackage vinart
 */
use VinartTheme\Classes\Vinart_Helper;

get_header();

    $blog_archive_post_layout       = Vinart_Helper::get_option( 'blog_archive_post_layout', 'list' );
    $blog_archive_post_list_style   = Vinart_Helper::get_option( 'blog_archive_post_list_style', 'block' );
    $blog_archive_post_grid_columns = Vinart_Helper::get_option( 'blog_archive_post_grid_columns', '1' );

    if ( $blog_archive_post_layout == 'list' ) {
        $blog_archive_post_grid_columns = '1';
    }    
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

        <div class="gg_posts_grid">
            <?php if ( have_posts() ) : ?>
                
                <ul 
                class="el-grid" 
                data-layout-mode="<?php echo esc_attr($blog_archive_post_layout); ?>" 
                data-gap="gap" 
                data-columns="<?php echo esc_attr($blog_archive_post_grid_columns); ?>"
                <?php if ( $blog_archive_post_layout == 'list' ) : ?>
                data-list-style="<?php echo esc_attr($blog_archive_post_list_style); ?>"
                <?php endif; ?>
                >
                <?php while ( have_posts() ) : the_post(); ?>
                    <li><?php get_template_part( 'parts/post-formats/part' ); ?></li>
                <?php endwhile; ?>
                </ul>

                <?php 
                    if (function_exists("vinart_pagination")) {
                        vinart_pagination();
                    }
                ?>

            <?php else : ?>

                <?php get_template_part( 'parts/post-formats/part', 'none' ); ?>

            <?php endif; // end have_posts() check ?>
        </div><!--/ .gg_posts_grid-->

        <?php vinart_page_sidebar(); ?>

    </div>

</section>

<?php get_footer(); ?>