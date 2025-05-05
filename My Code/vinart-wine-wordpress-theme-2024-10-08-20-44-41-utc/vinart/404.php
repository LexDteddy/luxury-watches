<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage vinart
 */
get_header();
use VinartTheme\Classes\Vinart_Helper;

//Default value
$vinart_404_title = Vinart_Helper::get_option( 'error_title', esc_html__( '404 Error', 'vinart' ) );
$vinart_404_desc  = Vinart_Helper::get_option( 'error_desc', esc_html__( 'It seems we cannot find what you are looking for.', 'vinart' ) );
$vinart_404_button_text  = Vinart_Helper::get_option( 'error_button_text', esc_html__( 'Return To Home', 'vinart' ) );

?>
<section id="content" class="site-content">
    <div class="not_found_wrapper">
        <div class="not_found_box">
            <?php if ($vinart_404_title) : ?>
            <h1><?php echo esc_html($vinart_404_title); ?></h1>
            <?php endif; ?>

            <?php if ($vinart_404_desc) : ?> 
            <p class="info-404"><?php echo esc_html($vinart_404_desc); ?></p>
            <?php endif; ?>
            
            <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html($vinart_404_button_text); ?></a>
        </div>
    </div><!-- /.not_found_wrapper -->
</section>
    
<?php get_footer();