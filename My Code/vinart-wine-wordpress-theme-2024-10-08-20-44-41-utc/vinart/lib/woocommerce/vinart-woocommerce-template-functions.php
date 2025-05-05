<?php
use VinartTheme\Classes\Vinart_Helper;

//Get the store theme options
$activate_product_image_sizes = Vinart_Helper::get_option( 'activate_product_image_sizes', 'enabled' );
$store_catalog_mode           = Vinart_Helper::get_option( 'store_catalog_mode', 'disabled' );
$product_loop_columns         = Vinart_Helper::get_option( 'product_loop_columns', '3' );
$product_loop_per_page        = Vinart_Helper::get_option( 'product_loop_per_page', '9' );

$store_sale_flash             = Vinart_Helper::get_option( 'store_sale_flash', 'enabled' );
$store_products_price         = Vinart_Helper::get_option( 'store_products_price', 'enabled' );
$store_add_to_cart            = Vinart_Helper::get_option( 'store_add_to_cart', 'enabled' );

$product_pdf_factsheet        = Vinart_Helper::get_option( 'product_pdf_factsheet', 'enabled' );
$product_sale_flash           = Vinart_Helper::get_option( 'product_sale_flash', 'enabled' );
$product_products_price       = Vinart_Helper::get_option( 'product_products_price', 'enabled' );
$product_products_excerpt     = Vinart_Helper::get_option( 'product_products_excerpt', 'enabled' );
$product_products_meta        = Vinart_Helper::get_option( 'product_products_meta', 'enabled' );
$product_add_to_cart          = Vinart_Helper::get_option( 'product_add_to_cart', 'enabled' );
$product_related_products     = Vinart_Helper::get_option( 'product_related_products', 'enabled' );
$product_description_tab      = Vinart_Helper::get_option( 'product_description_tab', 'enabled' );

$product_crosssells_products  = Vinart_Helper::get_option( 'product_crosssells_products', 'enabled' );

//Get the Shop filter options
$store_filter = Vinart_Helper::get_option( 'store_filter', 'disabled' );


/**
 * Enable/Disable the products filter
 */
if ( $store_filter == 'enabled' ) {
    require get_template_directory() . '/lib/woocommerce/class-vinart-shop-filters.php';
}


/**
 * Set default columns number
 */
if ($product_loop_columns) {
    add_filter('loop_shop_columns', function($cols) use ($product_loop_columns) {
        return (int)$product_loop_columns;
    });
}

/**
 * Set default products per page
 */
if ($product_loop_per_page) {
    add_filter('loop_shop_per_page', function($cols) use ($product_loop_per_page) {
        return (int)$product_loop_per_page['number'];
    },20);
}

/**
 * Hide shop page title
 */
add_filter('woocommerce_show_page_title', 'vinart_remove_shop_title' );
function vinart_remove_shop_title() {
    return false;
}

/**
 * Wrap the products loop and add a spinner
 */
add_action( 'woocommerce_before_shop_loop', 'vinart_products_loop_open', 40);
function vinart_products_loop_open() {
    echo '<div class="products-wrapper">';
    echo '<div class="spinner"></div>';
}
add_action( 'woocommerce_after_shop_loop', 'vinart_products_loop_close', 5);
function vinart_products_loop_close() {
    echo '</div>';
}

/*
 * Add custom pagination
 */
function vinart_wc_pagination() {
    vinart_pagination();       
}
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
add_action( 'woocommerce_after_shop_loop', 'vinart_wc_pagination', 10);


if ( ! function_exists( 'vinart_open_product_category_wrap' ) ) {
    /**
     * Product category wrap - open
     */
    function vinart_open_product_category_wrap() {
        echo '<div class="product-category-wrap">';
    }
}

if ( ! function_exists( 'vinart_close_product_category_wrap' ) ) {
    /**
     * Product category wrap - close
     */
    function vinart_close_product_category_wrap() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vinart_add_category_description' ) ) {
    /**
     * Add category description
     */
    function vinart_add_category_description ($category) {
        $cat_id      =    $category->term_id;
        $prod_term   =    get_term($cat_id,'product_cat');
        $description =    $prod_term->description;

        if ( $description) {
            echo '<div class="term-description">' . wc_format_content($description) . '</div>';
        }
    }
}


if ( ! function_exists( 'vinart_open_product_image_wrap' ) ) {
    /**
     * Product image wrap - open
     */
    function vinart_open_product_image_wrap() {
        echo '<div class="product-image-wrap">';
    }
}

if ( ! function_exists( 'vinart_close_product_image_wrap' ) ) {
    /**
     * Product image wrap - close
     */
    function vinart_close_product_image_wrap() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vinart_open_product_meta_wrap' ) ) {
    /**
     * Product meta wrap - open
     */
    function vinart_open_product_meta_wrap() {
        echo '<div class="product-meta-wrap">';
    }
}

if ( ! function_exists( 'vinart_close_product_meta_wrap' ) ) {
    /**
     * Product meta wrap - close
     */
    function vinart_close_product_meta_wrap() {
        echo '</div>';
    }
}


if ( ! function_exists( 'vinart_add_permalink_to_title' ) ) {
    /**
     * Add product permalink to product title
     */
    function vinart_add_permalink_to_title() {
        echo '<h2 class="woocommerce-loop-product__title"><a href="'.get_the_permalink().'">' . get_the_title() . '</a></h2>';
    }
}

if ( ! function_exists( 'vinart_category_on_title' ) ) {
    /**
     * Add product category on top of the product title
     */
    function vinart_category_on_title() {
        global $product;
        echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="product_posted_in">', '</span>' );
    }
}

if ( ! function_exists( 'vinart_open_product_gallery_div' ) ) {
    /**
     * Product images and summary div - open
     */
    function vinart_open_product_gallery_div() {
        echo '<div class="product-gallery-wrap">';
    }
}

if ( ! function_exists( 'vinart_close_product_gallery_div' ) ) {
    /**
     * Product images and summary div - close
     */
    function vinart_close_product_gallery_div() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vinart_open_images_summary_div' ) ) {
    /**
     * Product images and summary div - open
     */
    function vinart_open_images_summary_div() {
        echo '<div class="product-images-summary-wrap">';
    }
}

if ( ! function_exists( 'vinart_close_images_summary_div' ) ) {
    /**
     * Product images and summary div - close
     */
    function vinart_close_images_summary_div() {
        echo '</div>';
    }
}


if ( ! function_exists( 'vinart_product_description' ) ) {
    /**
     * Add product description before upsells
     */
    function vinart_product_description() {
        echo '<div class="product-description-wrapper">';
        wc_get_template( 'single-product/tabs/description.php' );
        echo '</div>';
    }
}

if ( ! function_exists( 'vinart_rename_additional_information_tab' ) ) {
    /**
     * Add product description before upsells
     */
    function vinart_rename_additional_information_tab( $tabs ) {
        $tabs['additional_information']['title']    = esc_html__( 'Product Data', 'vinart' );
        return $tabs;
    }
}

if ( ! function_exists( 'vinart_remove_description_tab' ) ) {
    /**
     * Remove Description tab 
     */
    function vinart_remove_description_tab( $tabs ) {
        unset( $tabs['description'] ); 
        return $tabs;
    }
}


if ( ! function_exists( 'vinart_wrap_qty_add_to_cart_open' ) ) {
    /**
     * Product QTY and add to cart div - open
     */
    function vinart_wrap_qty_add_to_cart_open() {
        echo '<div class="wrap-qty-add-to-cart">';
    }
}

if ( ! function_exists( 'vinart_wrap_qty_add_to_cart_close' ) ) {
    /**
     * Product QTY and add to cart div - open
     */
    function vinart_wrap_qty_add_to_cart_close() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vinart_open_my_account_wrapper' ) ) {
    /**
     * Wrap my account page - open div
     */
    function vinart_open_my_account_wrapper() {
        echo '<div class="woocommerce-MyAccount-content-wrapper">';
    }
}

if ( ! function_exists( 'vinart_close_my_account_wrapper' ) ) {
    /**
     * Wrap my account page - close div
     */
    function vinart_close_my_account_wrapper() {
        echo '</div>';
    }
}

if ( ! function_exists( 'vinart_wrap_checkout_order_review_open' ) ) {
    /**
     * Wrap my account page - open div
     */
    function vinart_wrap_checkout_order_review_open() {
        echo '<div class="woocommerce-order-details-wrapper">';
    }
}

if ( ! function_exists( 'vinart_wrap_checkout_order_review_close' ) ) {
    /**
     * Wrap my account page - close div
     */
    function vinart_wrap_checkout_order_review_close() {
        echo '</div>';
    }
}

/**
 * Hide category product count in product archives
 */
add_filter( 'woocommerce_subcategory_count_html', '__return_false' );


/* Store theme options */


/* Shop page */

/**
 * Enable/Disable Sale Flash
 */
if ( $store_sale_flash == 'disabled' ) {
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
}

/**
 * Enable/Disable Products price
 */
if ( $store_products_price == 'disabled' ) {
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
}

/**
 * Enable/Disable Add to cart
 */
if ( $store_add_to_cart == 'disabled' ) {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart',10);
}

/**
 * Options for product page
 */

/**
 * Enable/Disable Product PDF factsheet
 */
if ( $product_pdf_factsheet == 'disabled' ) {
    remove_action( 'woocommerce_single_product_summary', 'vinart_product_factsheet_link', 75);
}

/*Sale flash*/
if ( $product_sale_flash == 'disabled' ) {
    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
}

/*Price*/
if ( $product_products_price == 'disabled' ) {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);
}

/*Product summary*/
if ( $product_products_excerpt == 'disabled' ) {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
} 

/*Add to cart*/
if ( $product_add_to_cart == 'disabled' ) {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
}

/*Meta*/
if ( $product_products_meta == 'disabled' ) {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 80 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 80 );
}

/**
 * Enable/Disable Related products
 */
if ( $product_related_products == 'disabled' ) {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
}

/**
 * Enable/Disable Up Sells products
 */
//if ( $product_upsells_products == 'disabled' ) {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
//}


/**
 * Enable/Disable product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'vinart_remove_product_tabs', 98 );
function vinart_remove_product_tabs( $tabs ) {

    $product_reviews_tab          = Vinart_Helper::get_option( 'product_reviews_tab', 'enabled' );
    $product_attributes_tab       = Vinart_Helper::get_option( 'product_attributes_tab', 'enabled' );
    $product_awards               = Vinart_Helper::get_option( 'product_awards', 'enabled' );

    if ( $product_reviews_tab == 'disabled' ) {
        unset( $tabs['reviews'] );
    }
    if ( $product_attributes_tab == 'disabled' ) {
        unset( $tabs['additional_information'] );
    }
    if ( $product_awards == 'disabled' ) {
        unset( $tabs['awards'] );
    }

    return $tabs;
}

/**
 * Enable/Disable produc main description
 */
if ( $product_description_tab == 'disabled' ) {
    remove_action( 'woocommerce_after_single_product_summary', 'vinart_product_description', 12 );
}

/**
 * Enable/Disable Cross Sells products
 */
if ( $product_crosssells_products == 'disabled' ) {
    remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
}

/**
 * Catalog mode functions (must be always the last function)
 */

if ( $store_catalog_mode == 'disabled' ) {
    // Remove add to cart button from the product loop
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart',12);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

    // Remove add to cart button from the product details page
    remove_action( 'woocommerce_before_add_to_cart_form', 'woocommerce_template_single_product_add_to_cart', 10, 2);
    
    add_filter( 'woocommerce_add_to_cart_validation', '__return_false', 10, 2 );

    // check for clear-cart get param to clear the cart
    add_action( 'init', 'vinart_wc_clear_cart_url' );
    function vinart_wc_clear_cart_url() {    
        if ( isset( $_GET['clear-cart'] ) ) {
            WC()->cart->empty_cart();
        } 
    }

    add_action( 'wp', 'vinart_check_pages_redirect');
    function vinart_check_pages_redirect() {
        $cart     = is_page( wc_get_page_id( 'cart' ) );
        $checkout = is_page( wc_get_page_id( 'checkout' ) );

        if ( $cart || $checkout ) {
            wp_redirect( esc_url( home_url( '/' ) ) );
            exit;
        }
    }
    
}