<?php
/**
 * The Sidebars
 *
 * @package WordPress
 * @subpackage vinart
 */
?>

<?php 
use VinartTheme\Classes\Vinart_Helper;

$post_id = Vinart_Helper::get_post_id();
$sidebar = 'default-sidebar'; //Default value
//Get page options
$sidebar_select = Vinart_Helper::get_meta( 'vinart_global_meta', 'sidebar_select', 'default-sidebar',$post_id);

if ($sidebar_select) {
	$sidebar = $sidebar_select;
}
if (function_exists('dynamic_sidebar')) {
	
    if( vinart_is_wc_activated() && is_product() ) {
		
		$dynamic_sidebar = 'sidebar-product';

	} elseif( vinart_is_wc_activated() && is_woocommerce() ) {
		
		$dynamic_sidebar = 'sidebar-shop';

	} elseif( is_search() ) {
		
		$dynamic_sidebar = 'sidebar-search';			

	} elseif( is_single() || is_home() || is_category() || is_archive() ) {
		
		$dynamic_sidebar = 'sidebar-posts';

	} else { //else default sidebar
		
		$dynamic_sidebar = 'sidebar-page';

	}
}

//Conditional check for user and dynamic generated sidebars
if ( $sidebar && $sidebar != 'default-sidebar' ) {
	dynamic_sidebar($sidebar);
} elseif ( is_active_sidebar( $dynamic_sidebar ) ) {
    dynamic_sidebar( $dynamic_sidebar );
}