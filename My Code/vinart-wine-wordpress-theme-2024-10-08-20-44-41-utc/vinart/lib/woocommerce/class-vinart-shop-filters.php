<?php
/**
 * vinart WooCommerce Shop filters Class
 *
 * @package  vinart
 * @since    1.0.0
 */

namespace VinartTheme\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class vinart_ShopFilter {

    /**
     * Setup class.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'filter_scripts' ));
        
        // Load the ajax filters
        add_action( 'wp_ajax_ajax_filter_products', array( $this, 'ajax_filter_products' ) );
        add_action( 'wp_ajax_nopriv_ajax_filter_products', array( $this, 'ajax_filter_products' ) );

        // Add the filters before the products
        add_action('woocommerce_before_shop_loop', array( $this, 'render_filters' ) );
    }

    /**
     * WooCommerce specific scripts & stylesheets
     */
    public function filter_scripts() {
        global $vinart_version;

        /* Filter */
        $current_category_slug = is_tax('product_cat') ? get_queried_object()->slug : '';
        $current_tag_slug = is_tax('product_tag') ? get_queried_object()->slug : '';

        wp_enqueue_script('vinart-ajax-filter', get_template_directory_uri() . '/lib/woocommerce/js/custom.js', array('jquery'), $vinart_version, true);

        wp_localize_script('vinart-ajax-filter', 'ajax_filter_params', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('ajax_filter_nonce'),
            'current_category' => $current_category_slug,
            'current_tag' => $current_tag_slug,
        ));
    }

    public function ajax_filter_products() {
        // Check for nonce and verify it
        if ( ! isset( $_POST['ajax_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ajax_nonce'] ) ), 'ajax_filter_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid nonce verification', 'vinart' ) ) );
            return;
        }
    
        $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : array();
        $selected_tags = isset($_POST['tags']) ? $_POST['tags'] : array();
        $selected_attributes = isset($_POST['attributes']) ? $_POST['attributes'] : array();
        $current_category_slug = isset($_POST['current_category']) ? $_POST['current_category'] : '';
        $current_tag_slug = isset($_POST['current_tag']) ? $_POST['current_tag'] : '';
    
        // Debug the incoming filter parameters
        //error_log('Selected Categories: ' . print_r($selected_categories, true));
        //error_log('Selected Tags: ' . print_r($selected_tags, true));
        //error_log('Selected Attributes: ' . print_r($selected_attributes, true));
    
        // Query products based on the current filters
        $loop = $this->get_filtered_products($selected_categories, $selected_tags, $selected_attributes, $current_category_slug, $current_tag_slug);
    
        // Debug the products returned by the query
        $product_ids = wp_list_pluck($loop->posts, 'ID');
        //error_log('Filtered Product IDs: ' . print_r($product_ids, true));
    
        $results_count = $loop->found_posts;
    
        // Render products
        ob_start();
        $this->render_product_loop($loop->posts);
        $products_html = ob_get_clean();
    
        // Generate filters HTML based on the queried products
        $filters_html = $this->generate_filters_html($product_ids, $selected_categories, $selected_tags, $selected_attributes, $current_category_slug, $current_tag_slug);
    
        wp_send_json_success(array(
            'products' => $products_html,
            'filters' => $filters_html,
            'results_count' => $results_count
        ));
    }
    

    /**
     * Query products based on the selected filters.
     *
     * @return WP_Query
     */
    protected function get_filtered_products($selected_categories, $selected_tags, $selected_attributes, $current_category_slug, $current_tag_slug) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(),
            'tax_query'      => $this->build_tax_query($selected_categories, $selected_tags, $selected_attributes, $current_category_slug, $current_tag_slug),
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        );
    
        return new \WP_Query($args);
    }
    

    /**
     * Render the product loop.
     *
     * @param array $products
     */
    protected function render_product_loop( $products ) {
        if ( ! empty( $products ) ) {
            wc_setup_loop();
            foreach ( $products as $product_id ) {
                $GLOBALS['post'] = get_post( $product_id );
                setup_postdata( $GLOBALS['post'] );
                wc_get_template_part( 'content', 'product' );
            }
            wp_reset_postdata();
            wc_reset_loop();
        } else {
            echo '<li>'._e( 'No products found.', 'woocommerce' ).'</li>';
        }
    }

    /**
     * Generate filters HTML based on the current products.
     */
    protected function generate_filters_html($product_ids, $selected_categories, $selected_tags, $selected_attributes, $current_category_slug, $current_tag_slug) {
        $filters_html = '';
    
        // Get the setting for displaying product count
        $product_count_enabled = Vinart_Helper::get_option('store_filter_product_count', 'enabled');
    
        // Categories Filter (only show on the shop page)
        if (empty($current_category_slug)) {
            $categories = $this->get_filtered_terms('product_cat', $product_ids, $selected_categories);
            if (!empty($categories)) {
                $filters_html .= '<div class="filter-section"><h6>Categories</h6><div class="small-spinner"></div>';
                foreach ($categories as $category) {
                    $checked = in_array($category->slug, $selected_categories) ? 'checked="checked"' : '';
                    $count_display = $product_count_enabled === 'enabled' ? ' (' . esc_html($category->count) . ')' : ''; // Only show count if enabled
                    $filters_html .= '<label><input type="checkbox" name="categories[]" value="' . esc_attr($category->slug) . '" class="filter" ' . $checked . '> ' . esc_html($category->name) . $count_display . '</label>';
                }
                $filters_html .= '</div>';
            }
        }
    
        // Tags Filter (only show on the shop page)
        if (empty($current_tag_slug)) {
            $tags = $this->get_filtered_terms('product_tag', $product_ids, $selected_tags);
            if (!empty($tags)) {
                $filters_html .= '<div class="filter-section"><h6>Tags</h6><div class="small-spinner"></div>';
                foreach ($tags as $tag) {
                    $checked = in_array($tag->slug, $selected_tags) ? 'checked="checked"' : '';
                    $count_display = $product_count_enabled === 'enabled' ? ' (' . esc_html($tag->count) . ')' : ''; // Only show count if enabled
                    $filters_html .= '<label><input type="checkbox" name="tags[]" value="' . esc_attr($tag->slug) . '" class="filter" ' . $checked . '> ' . esc_html($tag->name) . $count_display . '</label>';
                }
                $filters_html .= '</div>';
            }
        }
    
        // Attributes Filter
        $attributes = wc_get_attribute_taxonomies();
        foreach ($attributes as $attribute) {
            $taxonomy = 'pa_' . $attribute->attribute_name;
            $terms = $this->get_filtered_terms($taxonomy, $product_ids, $selected_attributes[$attribute->attribute_name] ?? []);
            if (!empty($terms)) {
                $filters_html .= '<div class="attribute-filter filter-section" data-attribute="' . esc_attr($attribute->attribute_name) . '">';
                $filters_html .= '<h6>' . esc_html($attribute->attribute_label) . '</h6><div class="small-spinner"></div>';
                foreach ($terms as $term) {
                    $checked = in_array($term->slug, $selected_attributes[$attribute->attribute_name] ?? []) ? 'checked="checked"' : '';
                    $count_display = $product_count_enabled === 'enabled' ? ' (' . esc_html($term->count) . ')' : ''; // Only show count if enabled
                    $filters_html .= '<label><input type="checkbox" class="filter" name="attributes[' . esc_attr($attribute->attribute_name) . '][]" value="' . esc_attr($term->slug) . '" ' . $checked . '> ' . esc_html($term->name) . $count_display . '</label>';
                }
                $filters_html .= '</div>';
            }
        }
    
        return $filters_html;
    }
    

    /**
     * Get terms filtered by the currently visible products.
     *
     * @param string $taxonomy
     * @param array $product_ids
     * @param array $selected_terms
     * @return array
     */
    protected function get_filtered_terms($taxonomy, $product_ids, $selected_terms, $selected_categories = [], $selected_tags = [], $selected_attributes = [], $current_category_slug = '', $current_tag_slug = '') {
        $args = array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,  // Hide empty terms
            'object_ids' => $product_ids,  // Only include terms related to visible products
            'include'    => $selected_terms,  // Ensure selected terms are included
        );
    
        // Build tax query using the same helper function
        $tax_query = $this->build_tax_query($selected_categories, $selected_tags, $selected_attributes, $current_category_slug, $current_tag_slug);
    
        // Fetch terms and count products as before
        $terms = get_terms($args);
    
        foreach ($terms as $key => $term) {
            $product_query = new \WP_Query(array(
                'post_type'      => 'product',
                'posts_per_page' => -1,  // Get all products
                'post_status'    => 'publish',
                'post__in'       => $product_ids,  // Only check the current visible products
                'tax_query'      => array_merge(array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => $term->slug,
                    ),
                ), $tax_query),  // Add the tax query
            ));
    
            $terms[$key]->count = $product_query->found_posts;
        }
    
        return $terms;
    } 
        
    protected function build_tax_query($selected_categories = [], $selected_tags = [], $selected_attributes = [], $current_category_slug = '', $current_tag_slug = '') {
        // Get WooCommerce visibility term IDs
        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
    
        // Initialize the tax query
        $tax_query = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
                'operator' => 'NOT IN',
            ),
        );
    
        // Exclude out-of-stock products if WooCommerce is set to hide them
        if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => $product_visibility_term_ids['outofstock'],
                'operator' => 'NOT IN',
            );
        }
    
        // Handle category filtering
        if (!empty($selected_categories)) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $selected_categories,
            );
        } else if ($current_category_slug) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $current_category_slug,
            );
        }
    
        // Handle tag filtering
        if (!empty($selected_tags)) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $selected_tags,
            );
        } else if ($current_tag_slug) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $current_tag_slug,
            );
        }
    
        // Handle attributes filtering
        if (!empty($selected_attributes)) {
            foreach ($selected_attributes as $attribute => $terms) {
                $tax_query[] = array(
                    'taxonomy' => 'pa_' . $attribute,
                    'field'    => 'slug',
                    'terms'    => $terms,
                );
            }
        }
    
        return $tax_query;
    }
    
    

    public function render_filters() {
        // Theme options
        $filter_label = Vinart_Helper::get_option('store_filter_label', 'Filter the wines');
        $clear_label = Vinart_Helper::get_option('store_filter_clear_label', 'Clear filters');
        $active_filters = Vinart_Helper::get_option('store_filter_active_filters', 'enabled');
        $product_count = Vinart_Helper::get_option('store_filter_product_count', 'enabled'); // Check product count option
    
        $queried_object = get_queried_object();
        $current_category_slug = '';
        $current_tag_slug = '';
    
        if ($queried_object && isset($queried_object->slug)) {
            if (is_product_category()) {
                $current_category_slug = $queried_object->slug;
            } elseif (is_product_tag()) {
                $current_tag_slug = $queried_object->slug;
            }
        }
    
        // Initial query to get all visible products
        $loop = $this->get_filtered_products([], [], [], $current_category_slug, $current_tag_slug);
        $product_ids = wp_list_pluck($loop->posts, 'ID');
    
        ?>
        <div class="filter-actions">
            <button id="toggle-filters" class="button link-style">
                <span class="icon">
                    <svg width="15" height="7" viewBox="0 0 15 7" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-filter">
                        <line x1="0" y1="1.5" x2="15" y2="1.5" stroke="currentcolor"></line>
                        <line x1="0" y1="5.5" x2="15" y2="5.5" stroke="currentcolor"></line>
                        <circle cx="10.5" cy="1.5" r="1.5" fill="currentcolor"></circle>
                        <circle cx="3.5" cy="5.5" r="1.5" fill="currentcolor"></circle>
                    </svg>
                </span>
                <span class="label"><?php echo esc_html($filter_label); ?></span>
            </button>
    
            <?php if ($active_filters == 'enabled') : ?>
            <div class="active-filters"></div>
            <?php endif; ?>
    
            <button id="reset-filters" class="button link-style"><?php echo esc_html($clear_label); ?></button>
    
        </div>  
        <div class="filters-container">
            <?php if (!$current_category_slug && $current_tag_slug) : // Show filtered categories when on a tag page ?>
            <div class="filter-section">
                <h6><?php _e( 'Categories', 'woocommerce' ); ?></h6>
                <div class="small-spinner"></div>
                <?php
                $categories = $this->get_filtered_terms('product_cat', $product_ids, []);
                foreach ($categories as $category) {
                    $count_display = $product_count === 'enabled' ? ' (' . esc_html($category->count) . ')' : ''; // Show count if enabled
                    echo '<label><input type="checkbox" class="filter" name="categories[]" value="' . esc_attr($category->slug) . '"> ' . esc_html($category->name) . $count_display . '</label>';
                }
                ?>
            </div>
            <?php elseif (!$current_category_slug) : // Show all categories if not on a category or tag page ?>
            <div class="filter-section">
                <h6><?php _e( 'Categories', 'woocommerce' ); ?></h6>
                <div class="small-spinner"></div>
                <?php
                $categories = $this->get_filtered_terms('product_cat', $product_ids, []);
                foreach ($categories as $category) {
                    $count_display = $product_count === 'enabled' ? ' (' . esc_html($category->count) . ')' : ''; // Show count if enabled
                    echo '<label><input type="checkbox" class="filter" name="categories[]" value="' . esc_attr($category->slug) . '"> ' . esc_html($category->name) . $count_display . '</label>';
                }
                ?>
            </div>
            <?php endif; ?>
    
            <?php if (!$current_tag_slug) : // Only show tags if not on a tag page ?>
            <div class="filter-section">
                <h6><?php _e( 'Tags', 'woocommerce' ); ?></h6>
                <div class="small-spinner"></div>
                <?php
                $tags = $this->get_filtered_terms('product_tag', $product_ids, []);
                foreach ($tags as $tag) {
                    $count_display = $product_count === 'enabled' ? ' (' . esc_html($tag->count) . ')' : ''; // Show count if enabled
                    echo '<label><input type="checkbox" class="filter" name="tags[]" value="' . esc_attr($tag->slug) . '"> ' . esc_html($tag->name) . $count_display . '</label>';
                }
                ?>
            </div>
            <?php endif; ?>
    
            <?php
            $attributes = wc_get_attribute_taxonomies();
            foreach ($attributes as $attribute) {
                $taxonomy = 'pa_' . $attribute->attribute_name;
                $terms = $this->get_filtered_terms($taxonomy, $product_ids, []);
                if ($terms) {
                    echo '<div class="filter-section attribute-filter" data-attribute="' . esc_attr($attribute->attribute_name) . '">';
                    echo '<h6>' . esc_html($attribute->attribute_label) . '</h6>';
                    foreach ($terms as $term) {
                        $count_display = $product_count === 'enabled' ? ' (' . esc_html($term->count) . ')' : ''; // Show count if enabled
                        echo '<label><input type="checkbox" class="filter" name="attributes[' . esc_attr($attribute->attribute_name) . '][]" value="' . esc_attr($term->slug) . '"> ' . esc_html($term->name) . $count_display . '</label>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>
    
        <?php
    }
    

}

new vinart_ShopFilter();
