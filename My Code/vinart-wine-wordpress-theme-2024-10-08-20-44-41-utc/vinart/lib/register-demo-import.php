<?php
require get_template_directory() . '/admin/importer/cs_ocdi_import.php';

function vinart_import_files() {
    return array(
        array(
            'import_file_name'             => 'Vinart Demo Import',
            'categories'                   => array( 'Category 1'),
            'local_import_file'            => trailingslashit( get_template_directory() ) . 'admin/importer/demo-files/demo-content.xml',
            'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'admin/importer/demo-files/widgets.json',
            'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'admin/importer/demo-files/customizer.dat',
            'local_import_cs' => array(
                array(
                    'file_path'     => trailingslashit( get_template_directory() ) . 'admin/importer/demo-files/theme-options.json',
                    'option_name' => 'vinart_options',
                ),
            )
        )
    );
}
add_filter( 'pt-ocdi/import_files', 'vinart_import_files' );

function vinart_before_content_import() {
    update_option( 'elementor_disable_color_schemes', 'yes' );
    update_option( 'elementor_disable_typography_schemes', 'yes' );
    update_option( 'elementor_global_image_lightbox', 'no' );
    update_option( 'elementor_load_fa4_shim', 'yes' );
    update_option( 'elementor_unfiltered_files_upload', true );

    if (vinart_is_wc_activated()) {
        // Check if a WooCommerce shop page exists
        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id > 0) {
            // Delete the existing shop page or set it to draft
            wp_delete_post($shop_page_id, true);
        }
    }

}
add_action( 'ocdi/before_content_import', 'vinart_before_content_import' );

function get_custom_post_id_by_title($post_title, $post_type) {
    global $wpdb;
    
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s AND post_status = 'publish'",
        $post_title,
        $post_type
    ));
    
    return $post_id;
}

/**
* Retrieve a page given its slug.
*
* @global wpdb $wpdb WordPress database abstraction object.
*
* @param string       $page_slug  Page slug
* @param string       $output     Optional. Output type. OBJECT, ARRAY_N, or ARRAY_A.
*                                 Default OBJECT.
* @param string|array $post_type  Optional. Post type or array of post types. Default 'page'.
* @return WP_Post|null WP_Post on success or null on failure
*/
function get_page_by_slug( $page_slug, $output = OBJECT, $post_type = 'page' ) {
	global $wpdb;

	if ( is_array( $post_type ) ) {
		$post_type = esc_sql( $post_type );
		$post_type_in_string = "'" . implode( "','", $post_type ) . "'";
		$sql = $wpdb->prepare( "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_name = %s
			AND post_type IN ($post_type_in_string)
		", $page_slug );
	} else {
		$sql = $wpdb->prepare( "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_name = %s
			AND post_type = %s
		", $page_slug, $post_type );
	}

	$page = $wpdb->get_var( $sql );

	if ( $page )
		return get_post( $page, $output );

	return null;
}

function vinart_after_import_setup() {   

    // Assign menus to their locations.
    $main_menu      = get_term_by('name', 'Main Menu', 'nav_menu');
    $secondary_menu = get_term_by('name', 'Footer Menu', 'nav_menu');

    set_theme_mod( 'nav_menu_locations', array(
            'main-menu'   => $main_menu->term_id,
            'footer-menu' => $secondary_menu->term_id,
        )
    );

    // Assign front page and posts page (blog page).
    // Get the front page.
    $front_page = get_posts(
        [
        'post_type'              => 'page',
        'title'                  => 'Homepage',
        'post_status'            => 'all',
        'numberposts'            => 1,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        ]
    );
    
    if ( ! empty( $front_page ) ) {
        update_option( 'page_on_front', $front_page[0]->ID );
    }
    
    // Get the blog page.
    $blog_page = get_posts(
        [
        'post_type'              => 'page',
        'title'                  => 'News',
        'post_status'            => 'all',
        'numberposts'            => 1,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        ]
    );
    
    if ( ! empty( $blog_page ) ) {
        update_option( 'page_for_posts', $blog_page[0]->ID );
    }
    
    if ( ! empty( $blog_page ) || ! empty( $front_page ) ) {
        update_option( 'show_on_front', 'page' );
    }

    /* Set the Mailchimp form */
    update_option( 'mc4wp_lite_form', array() );
    update_option( 'mc4wp_default_form_id', get_custom_post_id_by_title('Footer form', 'mc4wp-form'));

    // Set the WooCommerce pages
    if (vinart_is_wc_activated()) {
        // Find the imported shop page by slug
        $shop_page = get_page_by_slug('shop-wines');
        if ($shop_page) {
            // Set the imported page as the default WooCommerce shop page
            update_option('woocommerce_shop_page_id', $shop_page->ID);
        } else {
            error_log('The page with slug "shop-wines" was not found.');
        }

    }
    
    // Update permalinks to use the "Post name" structure
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    flush_rewrite_rules(); // Ensure the new permalink structure is applied    

}
add_action( 'pt-ocdi/after_import', 'vinart_after_import_setup' );


//Remove branding
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

//Disable generation of smaller images
add_filter( 'pt-ocdi/regenerate_thumbnails_in_content_import', '__return_false' );

function ocdi_change_time_of_single_ajax_call() {
    return 10;
}
add_action( 'pt-ocdi/time_for_one_ajax_call', 'ocdi_change_time_of_single_ajax_call' );
