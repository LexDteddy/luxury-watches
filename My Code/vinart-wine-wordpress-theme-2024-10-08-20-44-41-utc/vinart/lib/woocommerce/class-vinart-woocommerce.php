<?php
/**
 * vinart WooCommerce Class
 *
 * @package  vinart
 * @since    2.0.0
 */
namespace VinartTheme\Classes;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'vinart_WooCommerce' ) ) :

	/**
	 * The vinart WooCommerce Integration class
	 */
	class vinart_WooCommerce {

		/**
		 * Setup class.
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_filter( 'body_class', array( $this, 'woocommerce_body_class' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'woocommerce_scripts' ),20 );
		
			add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
            add_filter( 'woocommerce_upsell_display_args', array( $this, 'upsell_products_args' ) );
            add_filter( 'woocommerce_cross_sells_total', array( $this, 'cross_sell_products_total' ) );
            add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sell_products_columns' ) );
			add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'thumbnail_columns' ) );
			add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'change_breadcrumb_delimiter' ) );

			add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'menu_cart_fragments' ] );
			/*
			* Prevent the wc wizard from loading
			*/
			add_filter( 'woocommerce_enable_setup_wizard', '__return_false');
			add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true');
		}

		/**
		 * Sets up theme defaults and registers support for various WooCommerce features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * @since 2.4.0
		 * @return void
		 */
		public function setup() {
			add_theme_support(
				'woocommerce', apply_filters(
					'vinart_woocommerce_args', array(
						'single_image_width'    => 9999,
						'thumbnail_image_width' => 9999,
						'product_grid'          => array(
							'default_columns' => 3,
							'default_rows'    => 3,
							'min_columns'     => 1,
							'max_columns'     => 6,
							'min_rows'        => 1,
						),
					)
				)
			);

			//add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}


		/**
		 * Add WooCommerce specific classes to the body tag
		 */
		public function woocommerce_body_class( $classes ) {
			$classes[] = 'woocommerce-active';

			// Specific check for WooCommerce shop page
            if (is_shop()) {
                $shop_page_id = wc_get_page_id('shop'); // Get the shop page ID
                if (has_post_thumbnail($shop_page_id)) { // Check if the shop page has a featured image
                    $classes[] = 'gg-page-has-header-image';
                }
            }

			//WC
            if ( is_shop() && isset( $_GET['shop_style'] ) ) {
				$shop_style = $_GET['shop_style'];
				$classes[] = 'gg-shop-'.$shop_style;
			}			 

			// Remove `no-wc-breadcrumb` body class.
			$key = array_search( 'no-wc-breadcrumb', $classes );

			if ( false !== $key ) {
				unset( $classes[ $key ] );
			}

			return $classes;
		}

		/**
		 * WooCommerce specific scripts & stylesheets
		 */
		public function woocommerce_scripts() {
			global $vinart_version;
			wp_enqueue_style( 'vinart-fontawesome', get_template_directory_uri() . '/assets/fontawesome/css/fontawesome.min.css', '', $vinart_version );
            wp_enqueue_style( 'vinart-fontawesome-regular', get_template_directory_uri() . '/assets/fontawesome/css/solid.min.css', '', $vinart_version );
    		wp_enqueue_style( 'vinart-woocommerce-style', get_template_directory_uri() . '/assets/css/woocommerce.css', array(), $vinart_version );
		}


		/**
		 * Related Products Args
		 */
		public function related_products_args( $args ) {
			$args = apply_filters(
				'vinart_related_products_args', array(
					'posts_per_page' => 3,
					'columns'        => 3,
				)
			);

			return $args;
		}

        /**
         * Upsell Products Args
         */
        public function upsell_products_args( $args ) {
            $args = apply_filters(
                'vinart_upsell_products_args', array(
                    'posts_per_page' => 3,
                    'columns'        => 3,
                )
            );

            return $args;
        }

        /**
         * Cross Sell Products Columns
         */
        public function cross_sell_products_columns( $columns ) {
            return 1;
        }

        /**
         * Cross Sell Products Total
         */
        public function cross_sell_products_total( $limit ) {
            return 1;
        }

		/**
		 * Product gallery thumbnail columns
		 */
		public function thumbnail_columns() {
			$columns = 4;

			if ( ! is_active_sidebar( 'sidebar-1' ) ) {
				$columns = 5;
			}

			return intval( apply_filters( 'vinart_product_thumbnail_columns', $columns ) );
		}

		/**
		 * Query WooCommerce Extension Activation.
		 */
		public function is_woocommerce_extension_activated( $extension = 'WC_Bookings' ) {
			return class_exists( $extension ) ? true : false;
		}

		/**
		 * Remove the breadcrumb delimiter
		 */
		public function change_breadcrumb_delimiter( $defaults ) {
			$defaults['delimiter']   = '<span class="breadcrumb-separator"> / </span>';
			$defaults['wrap_before'] = '<div class="vinart-breadcrumb"><div class="col-full"><nav class="woocommerce-breadcrumb">';
			$defaults['wrap_after']  = '</nav></div></div>';
			return $defaults;
		}

		public static function render_menu_cart_cart_count() {

			if ( null === WC()->cart ) {
				return;
			}

			if (WC()->cart->get_cart_contents_count() > 0) {
				echo '<span class="count">' . WC()->cart->get_cart_contents_count() . '</span>';
			} else {
				echo '<span class="count"></span>'; //we need this because we're only refreshing the span
			}

		}


		/**
		 * Render menu cart toggle button
		 */
		public static function render_menu_cart_toggle_button($customIcon) { ?>
			<div class="cart-drawer-wrapper">
				<a id="cart-drawer-trigger" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_attr_e('View your shopping cart', 'vinart'); ?>">
				<?php
					if (!empty($customIcon)) {
						echo wp_kses($customIcon, vinart_kses_allowed_html(array(), 'icon'));
					} else {
						echo vinart_get_icons('header-minicart');
					}

					self::render_menu_cart_cart_count();
				?>
				</a>
			</div>
		<?php
		}

		/**
		 * Render menu cart fragments
		 */
		public function menu_cart_fragments( $fragments, $customIcon = '' ) {
			$has_cart = is_a( WC()->cart, 'WC_Cart' );
	
			if ( ! $has_cart ) {
				return $fragments;
			}
	
			ob_start();
			
			self::render_menu_cart_cart_count();
	
			$menu_cart_toggle_button_html = ob_get_clean();
	
			if ( ! empty( $menu_cart_toggle_button_html ) ) {
				$fragments['body:not(.elementor-editor-active) #cart-drawer-trigger .count'] = $menu_cart_toggle_button_html;
			}
	
			return $fragments;
		}


		/**
		 * Render menu cart
		 */
		public static function render_menu_cart($customIcon = '') {

			self::render_menu_cart_toggle_button($customIcon);

			if ( null === WC()->cart ) {
				return;
			}
	
			if ( ! is_cart() && ! is_checkout() ) {
			?>

				<div id="cartDrawer">
					<div class="cart-drawer-container">
						<div class="cart-drawer-header">
							<h4><?php esc_html_e( 'Shopping cart', 'vinart' ); ?></h4>
							<a href="#" id="closeDrawerbtn" title="<?php esc_attr_e( 'Close', 'vinart' ); ?>">
								<?php echo vinart_get_icons('shopping-cart-close'); ?>
							</a>
						</div>
						<div class="cart-drawer-content">
							<?php
							if ( class_exists( 'WC_Widget_Cart' ) ) {
								the_widget(
									'WC_Widget_Cart',
									array(
										'title' => false,
									)
								);
							}
							?>
						</div>
					</div>
				</div>
				<div id="panelOverlay"></div>
				<?php
			}
		}
		
	}

endif;

return new vinart_WooCommerce();
