<?php
/**
 * vinart WooCommerce hooks
 *
 * @package vinart
 */

/**
 * Product categories loop hooks
 */

// Wrap product category
add_action( 'woocommerce_before_subcategory', 'vinart_open_product_category_wrap', 5);
add_action( 'woocommerce_after_subcategory', 'vinart_close_product_category_wrap', 20);

add_action( 'woocommerce_after_subcategory', 'vinart_add_category_description', 15);

/**
 * Product loop hooks
 */

// Wrap product image
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

add_action( 'woocommerce_before_shop_loop_item_title', 'vinart_open_product_image_wrap', 5, 2);
add_action( 'woocommerce_before_shop_loop_item_title', 'vinart_close_product_image_wrap', 13, 2);

// Wrap product meta
add_action( 'woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_link_open',6,2);
add_action( 'woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_link_close',11,2);

add_action( 'woocommerce_before_shop_loop_item_title', 'vinart_open_product_meta_wrap', 15, 2);
add_action( 'woocommerce_after_shop_loop_item', 'vinart_close_product_meta_wrap', 11, 2);

// Add permalink to title
remove_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title', 10 );
add_action('woocommerce_shop_loop_item_title', 'vinart_add_permalink_to_title', 10 );


/**
 * Product single hooks
 */

//Wrap product images in a div
add_action( 'woocommerce_before_single_product_summary','vinart_open_product_gallery_div',6);
add_action( 'woocommerce_before_single_product_summary','vinart_close_product_gallery_div',21);

//Wrap product images and products summary in a div
add_action( 'woocommerce_before_single_product_summary','vinart_open_images_summary_div',5);
add_action( 'woocommerce_single_product_summary','vinart_close_images_summary_div',90);

// Move the tabs in product summary
remove_action( 'woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10);
add_action( 'woocommerce_single_product_summary','woocommerce_output_product_data_tabs',70);

//Move product meta under tabs (in product summary)
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta',40);
add_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta',80);
add_action( 'woocommerce_single_product_summary','woocommerce_template_single_sharing',80);

//Remove Description tab
add_filter( 'woocommerce_product_tabs', 'vinart_remove_description_tab', 98 );
//Move product description before upsells
add_action( 'woocommerce_after_single_product_summary', 'vinart_product_description', 12 );

//Remove description tab title
add_filter( 'woocommerce_product_description_heading', '__return_empty_string');
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_reviews_heading', '__return_empty_string' );

//Rename additional information tab
add_filter( 'woocommerce_product_tabs', 'vinart_rename_additional_information_tab', 98 );

// Move price under short descrition
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_price',10);
add_action( 'woocommerce_single_product_summary','woocommerce_template_single_price',25);

//Add product category on top of the product title
add_action( 'woocommerce_single_product_summary','vinart_category_on_title', 4 );

//Wrap QTY and Add to cart in a div
add_action( 'woocommerce_before_add_to_cart_button','vinart_wrap_qty_add_to_cart_open',5);
add_action( 'woocommerce_after_add_to_cart_button','vinart_wrap_qty_add_to_cart_close',5);


/**
 * My Account page hooks
 */
add_action( 'woocommerce_account_navigation', 'vinart_open_my_account_wrapper', 0 );
add_action( 'woocommerce_account_content', 'vinart_close_my_account_wrapper' );

/**
 * Checkout page hooks
 */
add_action( 'woocommerce_checkout_before_order_review_heading', 'vinart_wrap_checkout_order_review_open', 5 );
add_action( 'woocommerce_checkout_after_order_review', 'vinart_wrap_checkout_order_review_close', 5 );

/**
 * Remove product category description - its included in page_header function
 **/
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );