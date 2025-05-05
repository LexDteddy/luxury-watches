<?php
namespace VinartTheme\Classes;
defined( 'ABSPATH' ) || exit;

/**
 * Initial Helper functions for this theme.
 */
class Vinart_Helper {
    /**
     * Get Theme Options
     *
     * @param $option Required Option id
     * @param $default Optional set default value
     *
     * @return mixed
     */
    public static function get_option( $option, $default = null ) {
        $options = get_option( 'vinart_options' );
        return ( isset( $options[$option] ) ) ? $options[$option] : $default;
    }

    /**
     * Get a metaboxes
     *
     * @param $prefix_key Required Meta unique slug
     * @param $meta_key Required Meta slug
     * @param $default Optional Set default value
     * @param $id Optional Set post id
     *
     * @return mixed
     */
    public static function get_meta( $prefix_key, $meta_key, $default = null, $id = '' ) {
        if ( ! $id ) {
            $id = self::get_post_id();
        }

        $meta_boxes = get_post_meta( $id, $prefix_key, true );
        return ( isset( $meta_boxes[$meta_key] ) ) ? $meta_boxes[$meta_key] : $default;
    }

    /**
     * Check Theme Default Header
     */
    public static function check_default_header() {
        $default_header = self::get_option( 'default_header', 'enabled' );

        if ( is_page() || ( is_single() && 'post' === get_post_type() ) || is_home() ) {
            $page_default_header = self::get_meta( 'vinart_global_meta', 'page_default_header', 'default');

            if ( 'default' !== $page_default_header ) {
                $default_header = $page_default_header;
            }
        } 

        return $default_header;
    }

    /**
     * Check Default Footer
     *
     * @return void
     */
    public static function check_default_footer() {
        $default_footer = self::get_option( 'default_footer', 'enabled' );

        if ( is_page() || ( is_single() && 'post' === get_post_type() ) || is_home() ) {
            $page_default_footer = self::get_meta( 'vinart_global_meta', 'page_default_footer', 'default' );

            if ( 'default' !== $page_default_footer ) {
                $default_footer = $page_default_footer;
            }
        }

        return $default_footer;
    }

    /**
     * Get theme Global Color
     *
     * @return array
     */
    public static function get_global_colors() {
        // Define the default color values
        $default_colors = [
            'primary_color' => '#D68F5E',
            'secondary_color' => '#212121',
            'accent_color' => '#999999',
            'accent_color_1' => '#373737',
            'accent_color_2' => '#493F38',
            'body_background_color' => '#ffffff',
            'header_background_color' => '#ffffff',
            'footer_background_color' => '#2c2c2c',
            'modules_background_color' => '#F8F5F2',
        ];
    
        // Retrieve the current color settings
        $primary_color = self::get_option( 'primary_color', $default_colors['primary_color'] );
        $secondary_color = self::get_option( 'secondary_color', $default_colors['secondary_color'] );
        $accent_color = self::get_option( 'accent_color', $default_colors['accent_color'] );
        $accent_color_1 = self::get_option( 'accent_color_1', $default_colors['accent_color_1'] );
        $accent_color_2 = self::get_option( 'accent_color_2', $default_colors['accent_color_2'] );
        $body_background_color = self::get_option( 'body_background_color', $default_colors['body_background_color'] );
        $header_background_color = self::get_option( 'header_background_color', $default_colors['header_background_color'] );
        $footer_background_color = self::get_option( 'footer_background_color', $default_colors['footer_background_color'] );
        $modules_background_color = self::get_option( 'modules_background_color', $default_colors['modules_background_color'] );
    
        // Construct the colors array including both current and default values
        $colors = [
            'primary_color' => [
                'slug'  => 'vinart-primary-color',
                'title' => esc_html__( 'Vinart Primary Color', 'vinart' ),
                'value' => $primary_color,
                'default' => $default_colors['primary_color'],
            ],
            'secondary_color' => [
                'slug'  => 'vinart-secondary-color',
                'title' => esc_html__( 'Vinart Secondary Color', 'vinart' ),
                'value' => $secondary_color,
                'default' => $default_colors['secondary_color'],
            ],
            'accent_color' => [
                'slug'  => 'vinart-accent-color',
                'title' => esc_html__( 'Vinart Accent Color', 'vinart' ),
                'value' => $accent_color,
                'default' => $default_colors['accent_color'],
            ],
            'accent_color_1' => [
                'slug'  => 'vinart-accent-color-1',
                'title' => esc_html__( 'Vinart Accent Color #1', 'vinart' ),
                'value' => $accent_color_1,
                'default' => $default_colors['accent_color_1'],
            ],
            'accent_color_2' => [
                'slug'  => 'vinart-accent-color-2',
                'title' => esc_html__( 'Vinart Accent Color #2', 'vinart' ),
                'value' => $accent_color_2,
                'default' => $default_colors['accent_color_2'],
            ],
            'body_background_color' => [
                'slug'  => 'vinart-body-background-color',
                'title' => esc_html__( 'Vinart Body Background Color', 'vinart' ),
                'value' => $body_background_color,
                'default' => $default_colors['body_background_color'],
            ],
            'header_background_color' => [
                'slug'  => 'vinart-header-background-color',
                'title' => esc_html__( 'Vinart Header Background Color', 'vinart' ),
                'value' => $header_background_color,
                'default' => $default_colors['header_background_color'],
            ],
            'footer_background_color' => [
                'slug'  => 'vinart-footer-background-color',
                'title' => esc_html__( 'Vinart Footer Background Color', 'vinart' ),
                'value' => $footer_background_color,
                'default' => $default_colors['footer_background_color'],
            ],
            'modules_background_color' => [
                'slug'  => 'vinart-modules-background-color',
                'title' => esc_html__( 'Vinart Modules Background Color', 'vinart' ),
                'value' => $modules_background_color,
                'default' => $default_colors['modules_background_color'],
            ],
        ];
    
        return $colors;
    }
    

    /**
     * Get Elementor content for display
     *
     * @param int $content_id
     */
    public static function get_elementor_content( $content_id ) {
        $content = '';
        if ( \class_exists( '\Elementor\Plugin' ) ) {
            $elementor_instance = \Elementor\Plugin::instance();
            $content            = $elementor_instance->frontend->get_builder_content_for_display( $content_id );
        }
        return $content;
    }

    /**
	 * Get post ID.
	 *
	 * @param  string $post_id_override Get override post ID.
	 * @return number                   Post ID.
	 */
	public static function get_post_id() {
        global $post, $wp_query;
    
        $post_id = 0;
    
        if (is_home()) {
            $post_id = get_option('page_for_posts');
        } elseif (is_archive()) {
            
            if (function_exists('is_shop') && is_shop()) {
                // WooCommerce shop page ID
                $post_id = wc_get_page_id('shop');
            } elseif (function_exists('is_product_category') && is_product_category()) {
                // WooCommerce product category archive
                $post_id = $wp_query->get_queried_object_id();
            } elseif (function_exists('is_product_tag') && is_product_tag()) {
                // WooCommerce product tag archive
                $post_id = $wp_query->get_queried_object_id();
            } else {
                // Other archive pages
                $post_id = $wp_query->get_queried_object_id();
            }
        } elseif (isset($post->ID) && !is_search() && !is_category()) {
            $post_id = $post->ID;
        }
    
        return $post_id;
    }
    


}
