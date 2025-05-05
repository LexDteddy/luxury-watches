<?php
/* Check if a extension is activated */

// Verify if WooCommerce is active
if ( ! function_exists( 'vinart_is_okt_toolkit_activated' ) ) {
  function vinart_is_okt_toolkit_activated() {
      return class_exists('Okthemes_Toolkit');
  }
}

// Verify if WooCommerce is active
if ( ! function_exists( 'vinart_is_wc_activated' ) ) {
  function vinart_is_wc_activated() {
      return class_exists('woocommerce');
  }
}

// Verify if Elementor is active
if ( ! function_exists( 'vinart_is_elementor_activated' ) ) {
  function vinart_is_elementor_activated() {
    return class_exists( '\Elementor\Plugin' );
  }
}

// Verify if WPML is active
if ( ! function_exists( 'vinart_is_wpml_activated' ) ) {
    function vinart_is_wpml_activated() {
        return class_exists( 'SitePressLanguageSwitcher' );
    }
}

//Disable WPML styles
if ( vinart_is_wpml_activated() ) {  
    define('ICL_DONT_LOAD_NAVIGATION_CSS', true);
    define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
    define('ICL_DONT_LOAD_LANGUAGES_JS', true);
}

/* Modify the titles */
function remove_archive_title_prefix($title) {
  // Remove any existing prefix from the title
  $title_parts = explode(': ', $title);
  if (count($title_parts) > 1) {
      array_shift($title_parts); // Remove the prefix
      $title = join(': ', $title_parts);
  }

  return $title;
}
add_filter('get_the_archive_title', 'remove_archive_title_prefix');


function vinart_kses_allowed_html($tags, $context) {
  switch($context) {
    case 'icon': 
      $tags = array( 
        'i' => array('class' => array(),'aria-hidden' => array(), 'style' => array()),
        'svg'  => array(
          'role'        => true,
          'width'       => true,
          'height'      => true,
          'fill'        => true,
          'xmlns'       => true,
          'viewbox'     => true,
          'aria-hidden' => true,
        ),
        'path' => array(
          'd'              => true,
          'fill'           => true,
          'fill-rule'      => true,
          'stroke'         => true,
          'stroke-width'   => true,
          'stroke-linecap' => true,
        ),
        'g'    => array(
          'd'    => true,
          'fill' => true,
        ),
      );
      return $tags;
    case 'pagination': 
      $tags = array( 
        'a' => array('href' => array(),'class' => array()),
        'ul' => array('class' => array()),
        'li' => array('class' => array()),
        'span' => array('class' => array())
      );
    case 'logo': 
      $tags = array( 
        'a' => array('href' => array(),'class' => array(),'rel' => array()),
        'img' => array('class' => array(),'src' => array(),'alt' => array()),
        'p' => array('class' => array()),
      );
      return $tags;
    default: 
      return $tags;
  }
}

add_filter( 'wp_kses_allowed_html', 'vinart_kses_allowed_html', 10, 2);


function vinart_get_icons( $icon, $is_echo = false ) {
  $output = '';
    switch ( $icon ) { 
      case 'next-article-arrow':
        $output = '<svg version="1.1" width="54" height="6" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 54.7 6" style="enable-background:new 0 0 54.7 6;" xml:space="preserve">
     <polygon points="0,3.5 50.6,3.5 50.6,6 54.7,3 50.6,0 50.6,2.5 0,2.5 "/>
     </svg>
     ';
        break;
      
      case 'menu-toggle-plus':
        $output = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z" fill="currentColor"/></svg>';
        break;
        
      case 'menu-toggle-minus':
        $output = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 11h12v2H6z" fill="currentColor"/></svg>';
        break;
      
      case 'header-search':
        $output = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
        break;

      case 'header-my-account':
          $output = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
          break;
      
      case 'header-minicart':
        $output = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
        break;

      case 'mobile-menu-toggle':
          $output = '<svg class="svg-icon" width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.5 6H19.5V7.5H4.5V6ZM4.5 12H19.5V13.5H4.5V12ZM19.5 18H4.5V19.5H19.5V18Z" fill="currentColor"></path></svg>';
          break;
      case 'mobile-menu-close':
        $output = '<svg class="svg-icon" width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 10.9394L5.53033 4.46973L4.46967 5.53039L10.9393 12.0001L4.46967 18.4697L5.53033 19.5304L12 13.0607L18.4697 19.5304L19.5303 18.4697L13.0607 12.0001L19.5303 5.53039L18.4697 4.46973L12 10.9394Z" fill="currentColor"></path></svg>';
        break;

      case 'scroll-up':
        $output = '<svg class="icon icon-scrollup" id="icon-scrollup" viewBox="0 0 45 45" width="100%" height="100%"><g fill="none" fill-rule="evenodd"><path d="M22.966 14.75v18.242H22V14.86l-2.317 2.317-.683-.684 3-3v-.26h.261l.232-.233.045.045.045-.045.232.232h.151v.152l3.11 3.11-.683.683-2.427-2.427z" fill="#ffffff"></path></g></svg>';
        break;

      case 'arrow':
        $output = '<svg class="arrow-icon-arrow" width="28" height="17" viewBox="0 0 28 17" fill="none" preserveAspectRatio="xMinYMin meet" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path class="arrow-icon-stroke" d="M26 8.01221H1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path class="arrow-icon-stroke" d="M19.5 1.01221L26.5 8.01221L19.5 15.0122" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        break;
      
      case 'search':
        $output = '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="25px" height="25px"><path d="M 21 3 C 11.601563 3 4 10.601563 4 20 C 4 29.398438 11.601563 37 21 37 C 24.355469 37 27.460938 36.015625 30.09375 34.34375 L 42.375 46.625 L 46.625 42.375 L 34.5 30.28125 C 36.679688 27.421875 38 23.878906 38 20 C 38 10.601563 30.398438 3 21 3 Z M 21 7 C 28.199219 7 34 12.800781 34 20 C 34 27.199219 28.199219 33 21 33 C 13.800781 33 8 27.199219 8 20 C 8 12.800781 13.800781 7 21 7 Z"/></svg>';
        break;

      case 'shopping-cart-close':
        $output = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="12" height="12" viewBox="1.1 1.1 12 12" enable-background="new 1.1 1.1 12 12" xml:space="preserve"><path d="M8.3 7.1l4.6-4.6c0.3-0.3 0.3-0.8 0-1.2 -0.3-0.3-0.8-0.3-1.2 0L7.1 5.9 2.5 1.3c-0.3-0.3-0.8-0.3-1.2 0 -0.3 0.3-0.3 0.8 0 1.2L5.9 7.1l-4.6 4.6c-0.3 0.3-0.3 0.8 0 1.2s0.8 0.3 1.2 0L7.1 8.3l4.6 4.6c0.3 0.3 0.8 0.3 1.2 0 0.3-0.3 0.3-0.8 0-1.2L8.3 7.1z"></path></svg>';
        break;

      default:
        $output = '';
        break;

  }
  $output = apply_filters( 'vinart_svg_icon_element', $output, $icon );

  $classes = array(
    'vinart-icon',
    'icon-' . $icon,
  );

  $output = sprintf(
    '<span class="%1$s">%2$s</span>',
    implode( ' ', $classes ),
    $output
  );

  if ( ! $is_echo ) {
    return apply_filters( 'vinart_svg_icon', $output, $icon );
  }

  echo apply_filters( 'vinart_svg_icon', $output, $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}