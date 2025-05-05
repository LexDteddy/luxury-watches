<?php
/**
 * vinart WooCommerce functions.
 *
 * @package vinart
 */

/**
 * Checks if the current page is a product archive
 *
 * @return boolean
 */
function vinart_is_product_archive() {
	if ( vinart_is_wc_activated() ) {
		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
