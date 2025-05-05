<?php
/**
 * vinart Class
 *
 * @since    2.0.0
 * @package  vinart
 */
use VinartTheme\Classes\Vinart_Helper;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'vinart' ) ) :

    /**
     * The main vinart class
     */
    class vinart {

        /**
         * Setup class.
         *
         * @since 1.0
         */
        public function __construct() {
            add_action( 'after_setup_theme', array( $this, 'setup' ) );
            add_action( 'widgets_init', array( $this, 'widgets_init' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );
            add_action( 'wp_enqueue_scripts', array( $this, 'child_scripts' ), 30 ); // After WooCommerce.
            add_action( 'enqueue_block_editor_assets', array( $this, 'editor_styles' ));
            add_filter( 'body_class', array( $this, 'body_classes' ) );
            add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );
            add_filter( 'navigation_markup_template', array( $this, 'navigation_markup_template' ) );
            add_action( 'wp_head', array( $this, 'vinart_pingback_header') );
        }

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         */
        public function setup() {
            /*
             * Load Localisation files.
             *
             * Note: the first-loaded translation file overrides any following ones if the same translation is present.
             */

            // Loads wp-content/themes/vinart/languages/it_IT.mo.
            load_theme_textdomain( 'vinart', get_template_directory() . '/languages' );

            /**
             * Add default posts and comments RSS feed links to head.
             */
            add_theme_support( 'automatic-feed-links' );

            /*
             * Enable support for Post Thumbnails on posts and pages.
             *
             * @link https://developer.wordpress.org/reference/functions/add_theme_support/#Post_Thumbnails
             */
            add_theme_support( 'post-thumbnails' );
            set_post_thumbnail_size( 9999, 9999 );

            /**
             * Enable support for site logo.
             */
            add_theme_support(
                'custom-logo', apply_filters(
                    'vinart_custom_logo_args', array(
                        'height'      => 82,
                        'width'       => 140,
                        'flex-height' => true,
                        'flex-width'  => true,
                    )
                )
            );

            /**
             * Register menu locations.
             */
            register_nav_menus(
                apply_filters(
                    'vinart_register_nav_menus', array(
                        'main-menu'   => esc_html__('Main Menu', 'vinart'),
                        'footer-menu' => esc_html__('Footer Menu', 'vinart'),
                    )
                )
            );

            /*
             * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
             * to output valid HTML5.
             */
            add_theme_support(
                'html5', apply_filters(
                    'vinart_html5_args', array(
                        'search-form',
                        'comment-form',
                        'comment-list',
                        'gallery',
                        'caption',
                        'widgets',
                        'script',
                        'style',
                    )
                )
            );

            /**
             * Setup the WordPress core custom background feature.
             */
            add_theme_support(
                'custom-background', apply_filters(
                    'vinart_custom_background_args', array(
                        'default-color' => apply_filters( 'vinart_default_background_color', 'ffffff' ),
                        'default-image' => '',
                    )
                )
            );

            /**
             * Setup the WordPress core custom header feature.
             */
            add_theme_support(
                'custom-header', apply_filters(
                    'vinart_custom_header_args', array(
                        'default-image' => '',
                        'header-text'   => false,
                        'width'         => 1950,
                        'height'        => 500,
                        'flex-width'    => true,
                        'flex-height'   => true,
                    )
                )
            );

            /**
             *  Add support for the Site Logo plugin and the site logo functionality in JetPack
             *  https://github.com/automattic/site-logo
             *  http://jetpack.me/
             */
            add_theme_support(
                'site-logo', apply_filters(
                    'vinart_site_logo_args', array(
                        'size' => 'full',
                    )
                )
            );

            /**
             * Declare support for title theme feature.
             */
            add_theme_support( 'title-tag' );

            /**
             * Declare support for selective refreshing of widgets.
             */
            add_theme_support( 'customize-selective-refresh-widgets' );

            // Add support for block styles.
		    add_theme_support( 'wp-block-styles' );
            //add_editor_style();

            // Editor color palette
            add_theme_support( 'editor-color-palette', $this->get_gutenberg_color_palette() );

            // Deleted transient & setting up onboaded flag true to skip steps.
            delete_transient( 'elementor_activation_redirect' );
            update_option( 'elementor_onboarded', true );

        }

        /**
         * Gutenberg Block Color Palettes.
         *
         * Get the color palette in Gutenberg from Customizer colors.
         */
        public function get_gutenberg_color_palette() {

            $colors = Vinart_Helper::get_global_colors();
            $gutenberg_color_palette = array();

            foreach ( $colors as $slug => $args ) {
                array_push(
                    $gutenberg_color_palette,
                    array(
                        'name'  => esc_html( $args['title'] ),
                        'slug'  => esc_html( $args['slug'] ),
                        'color' => esc_html( $args['value'] ),
                    )
                );
            }
            
            return $gutenberg_color_palette;
        }

 
        /**
         * Register widget area.
         *
         * @link https://codex.wordpress.org/Function_Reference/register_sidebar
         */
        public function widgets_init() {

            //Construct the default sidebars array
            $sidebars_array = array(
                esc_html__('Page Sidebar', 'vinart')          => 'sidebar-page',
                esc_html__('Posts Sidebar', 'vinart')         => 'sidebar-posts',
                esc_html__('Search Sidebar', 'vinart')        => 'sidebar-search',
                esc_html__('Shop Sidebar', 'vinart')          => 'sidebar-shop',
                esc_html__('Product Sidebar', 'vinart')       => 'sidebar-product',
                esc_html__('Footer Hero Area', 'vinart')      => 'footer-hero-area',
                esc_html__('Footer Widgets Area', 'vinart')   => 'footer-widgets-area'
            );
            
            foreach ($sidebars_array as $sidebar_name => $sidebar_id) {
                register_sidebar(
                    array(
                        'name'          => $sidebar_name,
                        'id'            => $sidebar_id,
                        'before_widget' => '<div id="%1$s" class="gg-widget %2$s">',
                        'after_widget'  => '</div>',
                        'before_title'  => '<h4 class="widget-title">',
                        'after_title'   => '</h4>'
                    )
                );
            }

            //Get the dynamic ones
            $dynamic_sidebars = Vinart_Helper::get_option( 'sidebar_options','' );
            
            if ($dynamic_sidebars) {
                foreach ($dynamic_sidebars as $sidebar) {
                    if (!empty($sidebar['sidebar_name'])) {
                        register_sidebar(
                            array(
                                'name'          => $sidebar['sidebar_name'],
                                'id'            => sanitize_title_with_dashes($sidebar['sidebar_name']),
                                'before_widget' => '<div id="%1$s" class="gg-widget %2$s">',
                                'after_widget'  => '</div>',
                                'before_title'  => '<h4 class="widget-title">',
                                'after_title'   => '</h4>'
                            )
                        );
                    }
                }
            }
                      
        }

        /**
         * Enqueue scripts and styles.
         *
         * @since  1.0.0
         */
        public function scripts() {

            /**
             * Styles
             */          
            if ( vinart_is_wpml_activated() ) {
                // get lang direction and enqueue rtl stylesheet if needed.
                if( ICL_LANGUAGE_CODE == 'he' ){
                    wp_enqueue_style('rtl', get_template_directory_uri().'/rtl.css');
                }
            }

            wp_enqueue_style( 'vinart-style', get_template_directory_uri() . '/style.css', '', vinart_THEMEVERSION );
            wp_enqueue_style( 'vinart-fonts', $this->enqueue_google_fonts_url(), array(), null );

            /**
             * Scripts
             */
            wp_enqueue_script( 'vinart-navigation', get_template_directory_uri() . '/assets/js/primary-navigation.js', array(), vinart_THEMEVERSION, true );

                $vinart_l10n = array(
                    'expand'   => esc_html__( 'Expand child menu', 'vinart' ),
                    'collapse' => esc_html__( 'Collapse child menu', 'vinart' ),
                );

                wp_localize_script( 'vinart-navigation', 'vinartScreenReaderText', $vinart_l10n );

            /*Plugins*/
            /* $site_smooth_scroll      = Vinart_Helper::get_option( 'site_smooth_scroll', 'enabled' );
            if ( $site_smooth_scroll == 'enabled' ) {
                wp_enqueue_script('vinart-gsap', get_template_directory_uri() . '/assets/js/gsap.min.js', array('jquery'), vinart_THEMEVERSION, true);
            } */
            
            
            wp_enqueue_script('vinart-gsap', get_template_directory_uri() . '/assets/js/gsap.min.js', array('jquery'), vinart_THEMEVERSION, true);
            wp_enqueue_script('vinart-custom', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), vinart_THEMEVERSION, true);
            

            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }

            /* Custom scripts from theme options Custom Scripts tab */
            wp_add_inline_script( 'vinart-custom', Vinart_Helper::get_option( 'custom_scripts','' ) );
            
        }

        /**
         * Enqueue child theme stylesheet.
         * A separate function is required as the child theme css needs to be enqueued _after_ the parent theme
         * primary css and the separate WooCommerce css.
         *
         * @since  1.5.3
         */
        public function child_scripts() {
            if ( is_child_theme() ) {
                $child_theme = wp_get_theme( get_stylesheet() );
                wp_enqueue_style( 'vinart-child-style', get_stylesheet_uri(), array(), $child_theme->get( 'Version' ) );
            }
        }

        public function editor_styles() {
            /**
             * Editor Styles
             */          
            wp_enqueue_style( 'vinart-editor-style', get_template_directory_uri() . '/editor-style.css', '', vinart_THEMEVERSION );
        }

        public function enqueue_google_fonts_url(){
            $fonts_url = '';
            $fonts     = array();

            if ( 'off' !== _x( 'on', 'Barlow font: on or off', 'vinart' ) ) {
                $fonts[] = 'Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
            }

            if ( $fonts ) {
                $fonts_url = add_query_arg( array(
                    'family' => implode( '&family=', $fonts ),
                    'display' => 'swap',
                ), 'https://fonts.googleapis.com/css2' );
            }
            return esc_url_raw( $fonts_url );
        }

        /**
         * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
         *
         * @param array $args Configuration arguments.
         * @return array
         */
        public function page_menu_args( $args ) {
            $args['show_home'] = true;
            return $args;
        }

        /**
         * Adds custom classes to the array of body classes.
         *
         * @param array $classes Classes for the body element.
         * @return array
         */
        public function body_classes( $classes ) {

            $default_header           = Vinart_Helper::get_option( 'default_header', 'enabled' );
            $sticky_header            = Vinart_Helper::get_option( 'sticky_header', 'disabled' );
            $sticky_header_logo_check = Vinart_Helper::get_option( 'sticky_header_logo_check', 'no' );
            $sticky_site_image_logo   = Vinart_Helper::get_option( 'sticky_site_image_logo' );

            if ($default_header != 'enabled') {
                $classes[] = 'header-is-custom-editor';
            } else {
                if ( $sticky_header == 'enabled' ) {
                    $classes[] = 'header-is-sticky';
                }
                if ( $sticky_header_logo_check == 'yes' && $sticky_site_image_logo['url'] != '' ) {
                    $classes[] = 'has-sticky-logo';
                }
            }


            /* Add page slug if it doesn't exist */
            if (is_single() || is_page() && !is_front_page()) {
                if (!in_array(basename(get_permalink()), $classes)) {
                    $classes[] = basename(get_permalink());
                }
            }

            //Page layout
            if (is_singular() && has_post_thumbnail()) {
                $classes[] = 'gg-page-has-header-image';
            }
            

            if (!is_multi_author()) {
                $classes[] = 'single-author';
            }

            //WPML
            if ( vinart_is_wpml_activated() ) {
                
                $classes[] = 'gg-theme-has-wpml';
                
                //WPML currency
                if ( class_exists('woocommerce_wpml') ) {
                    $classes[] = 'gg-theme-has-wpml-currency';
                }
            }

            // Add class if sidebar is used.
            if ( ! is_active_sidebar( 'sidebar-page' ) && ( is_page() || is_home() ) ) {
                $classes[] = 'no-active-sidebar-page';
            } 

            if ( ! is_active_sidebar( 'sidebar-posts' ) && ( is_single() || is_archive() ) ) {
                $classes[] = 'no-active-sidebar-post';
            }

            if ( ! is_active_sidebar( 'sidebar-search' ) && is_search() ) {
                $classes[] = 'no-active-sidebar-search';
            }

            //Sidebars
            $classes[] = vinart_page_layout();

            return $classes;
        }

        /**
         * Custom navigation markup template hooked into `navigation_markup_template` filter hook.
         */
        public function navigation_markup_template() {
            $template  = '<nav id="post-navigation" class="navigation %1$s" role="navigation" aria-label="' . esc_html__( 'Post Navigation', 'vinart' ) . '">';
            $template .= '<h2 class="screen-reader-text">%2$s</h2>';
            $template .= '<div class="nav-links">%3$s</div>';
            $template .= '</nav>';

            return apply_filters( 'vinart_navigation_markup_template', $template );
        }

        /**
         * Add a pingback url auto-discovery header for single posts, pages, or attachments.
         */
        function vinart_pingback_header() {
            if ( is_singular() && pings_open() ) {
                echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
            }
        }
        
    }
endif;

return new vinart();