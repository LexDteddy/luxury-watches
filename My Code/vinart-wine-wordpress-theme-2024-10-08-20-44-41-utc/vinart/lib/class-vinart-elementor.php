<?php
/**
 * Elementor Compatibility File.
 *
 * @package Vinart
 */

// If plugin - 'Elementor' not exist then return.
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use ElementorPro\Modules\ThemeBuilder\Module;
use Elementor\Plugin;
use VinartTheme\Classes\Vinart_Helper;
use VinartTheme\Classes\Vinart_Page_Header_Typography;

/**
 * Vinart Elementor Compatibility
 */
if ( ! class_exists( 'Vinart_Elementor' ) ) :

	/**
	 * Vinart Elementor Compatibility
	 *
	 * @since 1.0.0
	 */
	class Vinart_Elementor {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'elementor_default_setting' ), 20 );
			add_action( 'elementor/preview/init', array( $this, 'elementor_default_setting' ) );
				
			add_action( 'elementor/init', array( $this, 'sync_theme_colors_with_elementor_kit' ) );
			add_action( 'csf_vinart_options_saved', array( $this, 'sync_theme_colors_with_elementor_kit' ) );
		
			
			add_filter( 'elementor/document/save/data', function( $data, $document ) {
				$theme_colors = Vinart_Helper::get_global_colors();
				$vinart_options = get_option('vinart_options', []);
				$needs_update = false; // Flag to track changes
			
				// Synchronize Colors
				if (isset($data['settings'], $data['settings']['custom_colors'])) {
					foreach ($theme_colors as $key => $theme_color) {
						foreach ($data['settings']['custom_colors'] as $elementor_color) {
							if ($elementor_color['_id'] === $key && $elementor_color['color'] !== $theme_color['value']) {
								$vinart_options[$key] = $elementor_color['color'];
								$needs_update = true;
								break;
							}
						}
					}
				}
			
				// Synchronize Container Width
				if (isset($data['settings'], $data['settings']['container_width'])) {
					$elementor_container_width = $data['settings']['container_width']['size'] . $data['settings']['container_width']['unit'];
					if ($vinart_options['container_width']['number'] . $vinart_options['container_width']['unit'] !== $elementor_container_width) {
						$vinart_options['container_width']['number'] = $data['settings']['container_width']['size'];
						$vinart_options['container_width']['unit'] = $data['settings']['container_width']['unit'];
						$needs_update = true;
					}
				}
			
				// Update the theme options if changes were made
				if ($needs_update) {
					update_option('vinart_options', $vinart_options);
				}
			
				return $data;
			}, 10, 2 );
		
				
			//Elementor Pro
			if ( ! class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
				return;
			}
			// Add locations.
			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			// Override theme templates.
			add_action( 'vinart_header', array( $this, 'do_header' ), 0 );
			add_action( 'vinart_footer', array( $this, 'do_footer' ), 0 );
		
		}

		public function sync_theme_colors_with_elementor_kit() {

			$updates_made = false; // Flag to track if any updates are made

			if (! did_action( 'elementor/init' )) {
				return;
			}

			$vinart_options = get_option('vinart_options', []);
			$theme_colors = Vinart_Helper::get_global_colors(); // Assuming this returns an array of your colors
			$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
			$kit_settings = $kit->get_settings();

			$theme_container_width = $vinart_options['container_width']['number'] . $vinart_options['container_width']['unit'];
		
			if (!$kit) {
				return;
			}
				
			// Update theme colors
			$custom_colors = $kit->get_settings('custom_colors') ?: [];
			foreach ($theme_colors as $key => $color) {
				$color_exists = false;
		
				foreach ($custom_colors as $index => &$custom_color) {
					if ($custom_color['_id'] === $key) {
						$color_exists = true;
						
						if ($custom_color['color'] !== $color['value']) {
							$custom_color['color'] = $color['value'];
							$updates_made = true;
						}
						break;
					}
				}
		
				if (!$color_exists) {
					$custom_colors[] = [
						'_id' => $key,
						'title' => $color['title'],
						'color' => $color['value']
					];
					$updates_made = true;
				}
			}

			if (!empty($kit_settings['container_width']) && $kit_settings['container_width']['size'] . $kit_settings['container_width']['unit'] !== $theme_container_width) {
				$kit_settings['container_width']['size'] = intval($vinart_options['container_width']['number']);
				$kit_settings['container_width']['unit'] = $vinart_options['container_width']['unit'];
					
				$updates_made = true;
			}
		
			if ($updates_made) {
				Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option('custom_colors', $custom_colors);
				Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option('container_width', $kit_settings['container_width']);
			}
				
			// Save the kit if there were any updates
			if ($updates_made) {
				$kit->save([]);
			}
		}


		public function sync_theme_options_and_colors_with_elementor_kit() {
			$vinart_options = get_option('vinart_options', []);
			$theme_colors = Vinart_Helper::get_global_colors(); // Assuming this returns an array of your colors
			$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();

			if (!$kit) {
				return;
			}
		
			$updates_made = false; // Flag to track if any updates are made
		
			// Update theme colors
			$custom_colors = $kit->get_settings('custom_colors') ?: [];
			foreach ($theme_colors as $key => $color) {
				$color_exists = false;
		
				foreach ($custom_colors as $index => &$custom_color) {
					if ($custom_color['_id'] === $key) {
						$color_exists = true;
						
						if ($custom_color['color'] !== $color['value']) {
							$custom_color['color'] = $color['value'];
							$updates_made = true;
						}
						break;
					}
				}
		
				if (!$color_exists) {
					$custom_colors[] = [
						'_id' => $key,
						'title' => $color['title'],
						'color' => $color['value']
					];
					$updates_made = true;
				}
			}
		
			if ($updates_made) {
				Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option('custom_colors', $custom_colors);
			}
		
			// Update typography settings
			$typography_elements = ['body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
		
			foreach ($typography_elements as $element) {
				// Check if the typography setting exists in the theme options
				if (isset($vinart_options[$element . '_typography'])) {
					foreach ($vinart_options[$element . '_typography'] as $prop => $value) {
						$elementor_prop = $element . '_typography_' . str_replace('-', '_', $prop);
		
						// Special handling for font-family, text-transform, font-weight
						if (in_array($prop, ['font-family', 'text-transform', 'font-weight'])) {
							// Update the individual typography setting in the Elementor kit
							Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option($elementor_prop, $value);
							$updates_made = true;
						} elseif (in_array($prop, ['font-size', 'line-height', 'letter-spacing'])) {
							// Special handling for size properties with units
							$unit_key = $prop . '-unit';
							$elementor_prop_value = [
								'unit' => $vinart_options[$element . '_typography'][$unit_key] ?? 'px',
								'size' => $value,
								'sizes' => []
							];
		
							// Update the individual typography setting in the Elementor kit
							Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option($elementor_prop, $elementor_prop_value);
							$updates_made = true;
						} elseif ($prop === 'color') {
							// Handle the color property
							$color_prop = $element . '_color'; // Construct the color property name for Elementor
							Elementor\Plugin::instance()->kits_manager->update_kit_settings_based_on_option($color_prop, $value);
							$updates_made = true;
						}
						
					}
				}
			}
		
			// Save the kit if there were any updates
			if ($updates_made) {
				$kit->save([]);
			}
		}

		/**
		 * Elementor set content width
		 *
		 * @return void
		 * @since  1.0.2
		 */
		public function vinart_elementor_set_content_width() {
			return $GLOBALS['content_width'] = 1500;
		}
		


		/**
		 * Elementor Content layout set as Full width template (only the first time the page is created)
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function elementor_default_setting() {
			// Don't modify post meta settings if we are not on Elementor's edit page.
			if ( ! $this->is_elementor_editor() ) {
				return;
			}
		
			global $post;
			if ( !isset( $post ) ) {
				return;
			}
		
			$id = $post->ID;
		
			// Only run this check and update on singular admin or frontend edit pages
			if ( is_admin() || is_singular() ) {
				// Check if the custom meta field is set
				$template_set = get_post_meta( $id, '_elementor_template_set', true );
		
				if ( empty( $template_set ) ) {
					// Set the page template to Elementor Fullwidth
					update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
		
					// Update the custom meta field to indicate the template has been set
					update_post_meta( $id, '_elementor_template_set', 'yes' );
				}
			}
		}


		/**
		 * Check is elementor activated.
		 *
		 * @param int $id Post/Page Id.
		 * @return boolean
		 */
		public function is_elementor_activated( $id ) {

			$document = Plugin::$instance->documents->get( $id );
			if ( $document ) {
				return $document->is_built_with_elementor();
			} else {
				return false;
			}

		}

		/**
		 * Check if Elementor Editor is open.
		 *
		 * @since  1.2.7
		 *
		 * @return boolean True IF Elementor Editor is loaded, False If Elementor Editor is not loaded.
		 */
		private function is_elementor_editor() {
			if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}

			return false;
		}

		/**
		 * Register Locations
		 *
		 * @since 1.2.7
		 * @param object $manager Location manager.
		 * @return void
		 */
		public function register_locations( $manager ) {
			$manager->register_all_core_location();
		}

		/**
		 * Header Support
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function do_header() {
			$did_location = Module::instance()->get_locations_manager()->do_location( 'header' );
			if ( $did_location ) {
				remove_all_actions( 'vinart_header' );
				//subheader
				remove_action( 'vinart_subheader', 'vinart_page_header', 0 );
			}
		}

		/**
		 * Footer Support
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function do_footer() {
			$did_location = Module::instance()->get_locations_manager()->do_location( 'footer' );
			if ( $did_location ) {
				remove_all_actions( 'vinart_footer' );
			}
		}
		
	}

endif;

/**
 * Kicking this off by calling 'get_instance()' method
 */
Vinart_Elementor::get_instance();
