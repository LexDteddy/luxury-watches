<?php
use VinartTheme\Classes\Vinart_Helper;
class Vinart_Dynamic_Css {

	/**
	 * Register actions.
	 */
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 100 );
	}

	/**
	 * Load frontend style.
	 */
	public function enqueue() {

		$is_for_gutenberg = (current_action() === 'enqueue_block_editor_assets');

		$style = '';
		$style .= self::get_root_css();
		$style = self::minify_css( $style );

		wp_add_inline_style( $is_for_gutenberg ? 'vinart-editor-style' : 'vinart-style', $style );
	
	}

	/**
	 * Basic CSS minification.
	 *
	 * @param string $css
	 *
	 * @return string
	 */
	public static function minify_css($css) {
		// Remove CSS comments
		$css = preg_replace('!/\*.*?\*/!s', '', $css);
	
		// Remove space around selectors and braces
		$css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
	
		// Remove space after commas
		$css = preg_replace('/\s+,/', ',', $css);
	
		// Replace `0px`, `0em`, etc. with `0`
		$css = preg_replace('/(:|\s)0(px|em|%|in|cm|mm|pc|pt|ex)/', '${1}0', $css);
	
		// Shorten color codes from #aabbcc to #abc where possible
		$css = preg_replace('/#([a-fA-F0-9])\\1([a-fA-F0-9])\\2([a-fA-F0-9])\\3/', '#$1$2$3', $css);
	
		// Remove semicolons before closing braces
		$css = preg_replace('/;}/', '}', $css);
	
		// Minify as much as possible
		$css = trim(str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css));
	
		return $css;
	}
	

	/**
	 * Get root style (css variables)
	 *
	 * @return string
	 */
	public static function get_root_css() {
		
		$css = ':root{';

		$css .= self::get_css_vars();

		$css .= '}';

		$css .= apply_filters( 'vinart_after_css_root', $css );
		
		return $css;
	}

	public static function generateBorderStyle($borderArray, $borderRadiusArray, $prefix) {
		$borderStyle = '';
	
		foreach ($borderArray as $key => $value) {
			if ($key === 'style') {
				$borderStyle .= $prefix . 'border-style: ' . $value . ';';
			} else {
				$borderStyle .= $prefix . 'border-' . $key . '-width: ' . $value . 'px;';
			}
		}
	
		if ($borderRadiusArray) {
			// Concatenate the number and unit for border-radius
			$borderRadiusValue = $borderRadiusArray['number'] . $borderRadiusArray['unit'];
			$borderStyle .= $prefix . 'border-radius: ' . $borderRadiusValue . ';';
		}
	
		return $borderStyle;
	}
	
	
	public static function generatePaddingStyle($paddingArray, $prefix) {
		$paddingStyle = '';
	
		foreach ($paddingArray as $key => $value) {
			if ($key !== 'unit') {
				$paddingStyle .= $prefix . 'padding-' . $key . ': ' . $value . $paddingArray['unit'] . ';';
			}
		}
	
		return $paddingStyle;
	}
	
	public static function generateColorStyle($colorArray, $prefix) {
		$colorStyle = '';
	
		foreach ($colorArray as $key => $value) {
			$colorStyle .= $prefix . $key . ': ' . $value . ';';
		}
	
		return $colorStyle;
	}

	// Process typography styles and include units with specific naming
	public static function process_typography_styles($typography, $prefix, $media_query = '') {
		$inline_css = '';
		$exclude_keys = ['type', 'unit']; // Keys to exclude
	
		foreach ($typography as $prop => $value) {
			if (!in_array($prop, $exclude_keys) && strpos($prop, '-unit') === false && $value !== '') {
				$unit_key = $prop . '-unit';
				$unit = isset($typography[$unit_key]) ? $typography[$unit_key] : '';
	
				$css_var_name = "--vinart-{$prefix}-typography-" . str_replace('_', '-', $prop);
				$inline_css .= "{$css_var_name}: {$value}{$unit}; ";
			}
		}
	
		if (!empty($media_query)) {
			$inline_css = "@media {$media_query} { {$inline_css} }";
		}
	
		return rtrim($inline_css); // Remove trailing space
	}
	

	// Process padding styles and include units with specific naming
	public static function process_combined_padding_styles($padding_values, $prefix, $media_query = '') {
		$css_output = '';
	
		if (isset($padding_values['top'], $padding_values['right'], $padding_values['bottom'], $padding_values['left'], $padding_values['unit'])) {
			$unit = $padding_values['unit'];
			$padding_style = "{$padding_values['top']}{$unit} {$padding_values['right']}{$unit} {$padding_values['bottom']}{$unit} {$padding_values['left']}{$unit}";
			$css_variable = "--vinart-{$prefix}-padding: {$padding_style};";
	
			if (!empty($media_query)) {
				$css_output = "@media {$media_query} { {$css_variable} }";
			} else {
				$css_output = $css_variable;
			}
		}
	
		return $css_output;
	}
	

	public static function process_border_styles($border_values, $prefix, $unit = 'px') {
		$inline_css = '';
	
		if (isset($border_values['top'], $border_values['right'], $border_values['bottom'], $border_values['left'])) {
			$border_widths = "{$border_values['top']}{$unit} {$border_values['right']}{$unit} {$border_values['bottom']}{$unit} {$border_values['left']}{$unit}";
			$inline_css .= "--vinart-{$prefix}-border: {$border_widths};";
		}
	
		if (isset($border_values['style'])) {
			$border_style = $border_values['style'];
			$inline_css .= "--vinart-{$prefix}-border-style: {$border_style};";
		}
	
		return $inline_css;
	}
	

	public static function process_number_el_with_media_query($element, $prefix, $media_query = '') {
		$inline_css = '';
	
		if (is_array($element) && isset($element['number'])) {
			$value = $element['number'] . (isset($element['unit']) ? $element['unit'] : '');
			$css_var_name = "--vinart-{$prefix}";
	
			if (!empty($media_query)) {
				$inline_css = "@media {$media_query} { {$css_var_name}: {$value}; }";
			} else {
				$inline_css = "{$css_var_name}: {$value};";
			}
		}
	
		return $inline_css;
	}
	
	

	/**
	 * Returns CSS vars style.
	 *
	 * @return string
	 */
	public static function get_css_vars() {

		$inline_css = [];

		//General
		//Layout
		$container_width = Vinart_Helper::get_option('container_width');

		//Header -> Styling
		//Menu Items
		$menu_item_color = Vinart_Helper::get_option('menu_item_color');
		$menu_item_hover_color = Vinart_Helper::get_option('menu_item_hover_color');
		$menu_typography = Vinart_Helper::get_option('menu_typography');

		//Submenu
		$submenu_bg = Vinart_Helper::get_option('submenu_bg');
		$submenu_item_color = Vinart_Helper::get_option('submenu_item_color');
		$submenu_item_hover_color = Vinart_Helper::get_option('submenu_item_hover_color');
		$submenu_typography = Vinart_Helper::get_option('submenu_typography');

		//Header -> Mobile
		//Mobile Menu
		$mobile_bg = Vinart_Helper::get_option('mobile_bg');

		//Page -> Page Header
		//Page Header Styling
		$page_header_min_height = Vinart_Helper::get_option('page_header_min_height');
		$page_header_padding = Vinart_Helper::get_option('page_header_padding');
		$page_header_bg = Vinart_Helper::get_option('page_header_bg');

		//Page -> Sidebars
		//General
		$sidebar_width = Vinart_Helper::get_option('sidebar_width');

		//Typography
		$body_typography = Vinart_Helper::get_option('body_typography');
		$body_typography_tablet = Vinart_Helper::get_option('body_typography_tablet');
		$body_typography_mobile = Vinart_Helper::get_option('body_typography_mobile');

		$h1_typography = Vinart_Helper::get_option('h1_typography');
		$h1_typography_tablet = Vinart_Helper::get_option('h1_typography_tablet');
		$h1_typography_mobile = Vinart_Helper::get_option('h1_typography_mobile');

		$h2_typography = Vinart_Helper::get_option('h2_typography');
		$h2_typography_tablet = Vinart_Helper::get_option('h2_typography_tablet');
		$h2_typography_mobile = Vinart_Helper::get_option('h2_typography_mobile');

		$h3_typography = Vinart_Helper::get_option('h3_typography');
		$h3_typography_tablet = Vinart_Helper::get_option('h3_typography_tablet');
		$h3_typography_mobile = Vinart_Helper::get_option('h3_typography_mobile');

		$h4_typography = Vinart_Helper::get_option('h4_typography');
		$h4_typography_tablet = Vinart_Helper::get_option('h4_typography_tablet');
		$h4_typography_mobile = Vinart_Helper::get_option('h4_typography_mobile');

		$h5_typography = Vinart_Helper::get_option('h5_typography');
		$h5_typography_tablet = Vinart_Helper::get_option('h5_typography_tablet');
		$h5_typography_mobile = Vinart_Helper::get_option('h5_typography_mobile');

		$h6_typography = Vinart_Helper::get_option('h6_typography');
		$h6_typography_tablet = Vinart_Helper::get_option('h6_typography_tablet');
		$h6_typography_mobile = Vinart_Helper::get_option('h6_typography_mobile');

		// Paragraph spacing
		$paragraph_spacing = Vinart_Helper::get_option('paragraph_spacing');
		$paragraph_spacing_tablet = Vinart_Helper::get_option('paragraph_spacing_tablet');
		$paragraph_spacing_mobile = Vinart_Helper::get_option('paragraph_spacing_mobile');
		
		//Buttons
		$buttons_text_color = Vinart_Helper::get_option('buttons_text_color');
		$buttons_background_color = Vinart_Helper::get_option('buttons_background_color');
		$buttons_border = Vinart_Helper::get_option('buttons_border');
		$buttons_border_color = Vinart_Helper::get_option('buttons_border_color');
		$buttons_border_radius = Vinart_Helper::get_option('buttons_border_radius');
		
		$buttons_typography = Vinart_Helper::get_option('buttons_typography');
		$buttons_typography_tablet = Vinart_Helper::get_option('buttons_typography_tablet');
		$buttons_typography_mobile = Vinart_Helper::get_option('buttons_typography_mobile');

		$buttons_spacing = Vinart_Helper::get_option('buttons_spacing');
		$buttons_spacing_tablet = Vinart_Helper::get_option('buttons_spacing_tablet');
		$buttons_spacing_mobile = Vinart_Helper::get_option('buttons_spacing_mobile');


		// ------------ Refactored ----------- //
	
		
		// Container width
		if (is_array($container_width) && isset($container_width['number'])) {
			$inline_css[] = '--vinart-container-width: ' . $container_width['number'] . $container_width['unit'];
		}

		// Header Styling
		if ($menu_item_color) {
			$inline_css[] = "--vinart-menu-item-color: {$menu_item_color}";
		}
		if ($menu_item_hover_color) {
			$inline_css[] = "--vinart-menu-item-hover-color: {$menu_item_hover_color}";
		}

		// Menu Typography
		$menu_typography_css = self::process_typography_styles($menu_typography, 'menu');
		$inline_css[] = $menu_typography_css;

		// Submenu Styling
		if ($submenu_bg) {
			$inline_css[] = "--vinart-submenu-bg: {$submenu_bg}";
		}
		if ($submenu_item_color) {
			$inline_css[] = "--vinart-submenu-item-color: {$submenu_item_color}";
		}
		if ($submenu_item_hover_color) {
			$inline_css[] = "--vinart-submenu-item-hover-color: {$submenu_item_hover_color}";
		}

		if ($mobile_bg) {
			$inline_css[] = "--vinart-mobile-bg: {$mobile_bg}";
		}

		// SubMenu Typography
		$submenu_typography_css = self::process_typography_styles($submenu_typography, 'submenu');
		$inline_css[] = $submenu_typography_css;


		// Page header height
		if (is_array($page_header_min_height) && isset($page_header_min_height['height'])) {
			$inline_css[] = '--vinart-page-header-min-height: ' . $page_header_min_height['height'] . $page_header_min_height['unit'];
		}

		//Page header padding
		$page_header_padding_css = self::process_combined_padding_styles($page_header_padding, 'page-header');
		$inline_css[] = $page_header_padding_css;

		//Page header background
		if ($page_header_bg) {
			$inline_css[] = "--vinart-page-header-bg-color: {$page_header_bg}";
		}

		//Sidebar width
		if (is_array($sidebar_width) && $sidebar_width['width']) {
			$inline_css[] = '--vinart-sidebar-width: ' . $sidebar_width['width'] . '%' ;
		}

		//Typography
		$body_typography_css = self::process_typography_styles($body_typography, 'body');
		$inline_css[] = $body_typography_css;
		$body_typography_tablet_css = self::process_typography_styles($body_typography_tablet, 'body','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $body_typography_tablet_css;
		$body_typography_mobile_css = self::process_typography_styles($body_typography_mobile, 'body','(max-width: 767px)');
		$inline_css[] = $body_typography_mobile_css;

		$h1_typography_css = self::process_typography_styles($h1_typography, 'h1');
		$inline_css[] = $h1_typography_css;
		$h1_typography_tablet_css = self::process_typography_styles($h1_typography_tablet, 'h1','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $h1_typography_tablet_css;
		$h1_typography_mobile_css = self::process_typography_styles($h1_typography_mobile, 'h1','(max-width: 767px)');
		$inline_css[] = $h1_typography_mobile_css;

		$h2_typography_css = self::process_typography_styles($h2_typography, 'h2');
		$inline_css[] = $h2_typography_css;
		$h2_typography_tablet_css = self::process_typography_styles($h2_typography_tablet, 'h2','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $h2_typography_tablet_css;
		$h2_typography_mobile_css = self::process_typography_styles($h2_typography_mobile, 'h2','(max-width: 767px)');
		$inline_css[] = $h2_typography_mobile_css;

		$h3_typography_css = self::process_typography_styles($h3_typography, 'h3');
		$inline_css[] = $h3_typography_css;
		$h3_typography_tablet_css = self::process_typography_styles($h3_typography_tablet, 'h3','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $h3_typography_tablet_css;
		$h3_typography_mobile_css = self::process_typography_styles($h3_typography_mobile, 'h3','(max-width: 767px)');
		$inline_css[] = $h3_typography_mobile_css;

		$h4_typography_css = self::process_typography_styles($h4_typography, 'h4');
		$inline_css[] = $h4_typography_css;
		$h4_typography_tablet_css = self::process_typography_styles($h4_typography_tablet, 'h4','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $h4_typography_tablet_css;
		$h4_typography_mobile_css = self::process_typography_styles($h4_typography_mobile, 'h4','(max-width: 767px)');
		$inline_css[] = $h4_typography_mobile_css;

		$h5_typography_css = self::process_typography_styles($h5_typography, 'h5');
		$inline_css[] = $h5_typography_css;
		$h5_typography_tablet_css = self::process_typography_styles($h5_typography_tablet, 'h5','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $h5_typography_tablet_css;
		$h5_typography_mobile_css = self::process_typography_styles($h5_typography_mobile, 'h5','(max-width: 767px)');
		$inline_css[] = $h5_typography_mobile_css;

		$h6_typography_css = self::process_typography_styles($h6_typography, 'h6');
		$inline_css[] = $h6_typography_css;
		$h6_typography_tablet_css = self::process_typography_styles($h6_typography_tablet, 'h6','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $h6_typography_tablet_css;
		$h6_typography_mobile_css = self::process_typography_styles($h6_typography_mobile, 'h6','(max-width: 767px)');
		$inline_css[] = $h6_typography_mobile_css;

		// Paragraph spacing
		$paragraph_spacing_css = self::process_number_el_with_media_query($paragraph_spacing, 'paragraph-spacing');
		$inline_css[] = $paragraph_spacing_css;

		$paragraph_spacing_tablet_css = self::process_number_el_with_media_query($paragraph_spacing_tablet, 'paragraph-spacing', '(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $paragraph_spacing_tablet_css;

		$paragraph_spacing_mobile_css = self::process_number_el_with_media_query($paragraph_spacing_mobile, 'paragraph-spacing', '(max-width: 767px)');
		$inline_css[] = $paragraph_spacing_mobile_css;

		
		//Buttons
		//Color
		if (is_array($buttons_text_color) && isset($buttons_text_color['color'])) {
			$inline_css[] = "--vinart-buttons-color: {$buttons_text_color['color']}";
		}
		if (is_array($buttons_text_color) && isset($buttons_text_color['hover'])) {
			$inline_css[] = "--vinart-buttons-color-hover: {$buttons_text_color['hover']}";
		}

		//Background Color
		if (is_array($buttons_background_color) && isset($buttons_background_color['color'])) {
			$inline_css[] = "--vinart-buttons-bg-color: {$buttons_background_color['color']}";
		}
		if (is_array($buttons_background_color) && isset($buttons_background_color['hover'])) {
			$inline_css[] = "--vinart-buttons-bg-color-hover: {$buttons_background_color['hover']}";
		}

		//Border
		$buttons_border_css = self::process_border_styles($buttons_border, 'buttons', 'px');
		$inline_css[] = $buttons_border_css;

		//Border Color
		if (is_array($buttons_border_color) && isset($buttons_border_color['color'])) {
			$inline_css[] = "--vinart-buttons-border-color: {$buttons_border_color['color']}";
		}
		if (is_array($buttons_border_color) && isset($buttons_border_color['hover'])) {
			$inline_css[] = "--vinart-buttons-border-color-hover: {$buttons_border_color['hover']}";
		}

		//Border radius
		if (is_array($buttons_border_radius) && isset($buttons_border_radius['number'])) {
			$inline_css[] = '--vinart-buttons-border-radius: ' . $buttons_border_radius['number'] . $buttons_border_radius['unit'];
		}

		//Typography
		$buttons_typography_css = self::process_typography_styles($buttons_typography, 'buttons');
		$inline_css[] = $buttons_typography_css;
		$buttons_typography_tablet_css = self::process_typography_styles($buttons_typography_tablet, 'buttons','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $buttons_typography_tablet_css;
		$buttons_typography_mobile_css = self::process_typography_styles($buttons_typography_mobile, 'buttons','(max-width: 767px)');
		$inline_css[] = $buttons_typography_mobile_css;

		$buttons_spacing_css = self::process_combined_padding_styles($buttons_spacing, 'buttons');
		$inline_css[] = $buttons_spacing_css;
		$buttons_spacing_tablet_css = self::process_combined_padding_styles($buttons_spacing_tablet, 'buttons','(min-width: 768px) and (max-width: 1023px)');
		$inline_css[] = $buttons_spacing_tablet_css;
		$buttons_spacing_mobile_css = self::process_combined_padding_styles($buttons_spacing_mobile, 'buttons','(max-width: 767px)');
		$inline_css[] = $buttons_spacing_mobile_css;
	
		// Colors
		$colors = Vinart_Helper::get_global_colors();
	
		foreach ($colors as $key => $color) {
			$inline_css[] = '--' . $color['slug'] . ':' . $color['value'];
		}
	
		if (did_action('elementor/loaded')) {
			foreach ($colors as $key => $color) {
				$inline_css[] = '--e-global-color-' . $key . ':' . $color['value'];
			}
		}
	
		$output = esc_attr(implode('; ', $inline_css) . ';');
		
		error_log($output);
	
		return $output;
	}
	


}
return new Vinart_Dynamic_Css();
