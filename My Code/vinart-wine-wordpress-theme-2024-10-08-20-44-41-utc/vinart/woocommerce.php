<?php
/**
 * WooCommerce
 * Description: Page template for WooCommerce
 *
 * @package WordPress
 * @subpackage vinart
 */
get_header(); ?>
<div class="content-container">
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
		<div class="woocommerce-wrapper">
			<?php woocommerce_content(); ?>
		</div>
		<?php vinart_page_sidebar(); ?>
	</div><!-- /.page-content -->

	

</section>
</div>

<?php get_footer(); ?>