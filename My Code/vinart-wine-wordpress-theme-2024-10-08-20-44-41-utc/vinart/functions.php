<?php
/**
 * Theme Functions
 *
 * @author Gogoneata Cristian <cristian.gogoneata@gmail.com>
 * @package WordPress
 * @subpackage vinart
 */

$theme = wp_get_theme();
if ( is_child_theme() ) {
    $theme = wp_get_theme( $theme->get( 'Template' ) );
}
$theme_version = $theme->get( 'Version' );

define("vinart_THEMEVERSION", $theme_version);

// Load the main classes
require get_template_directory() . '/lib/class-helper.php';
require get_template_directory() . '/lib/class-vinart.php';
require get_template_directory() . '/lib/vinart-functions.php';
require get_template_directory() . '/lib/vinart-template-functions.php';

if ( vinart_is_okt_toolkit_activated() ) {
    require get_template_directory() . '/lib/class-dynamic-css.php';

    /**
     * Elementor compatibility
     */
    if ( vinart_is_elementor_activated() ) {
        require_once get_template_directory() . '/lib/elementor/el-custom-header-typography.php';
        require get_template_directory() . '/lib/class-vinart-elementor.php';
    }
}

if ( is_admin() ) {
    // Load plugins
    require get_template_directory() . '/lib/register-tgm-plugins.php';

    // Include the importer
    require get_template_directory() . '/admin/importer/init.php';
    require get_template_directory() . '/lib/register-demo-import.php';
}

/**
 * Load woocommerce functions
 */
if ( vinart_is_wc_activated() ) {
    require get_template_directory() . '/lib/woocommerce/class-vinart-woocommerce.php';
    require get_template_directory() . '/lib/woocommerce/vinart-woocommerce-template-hooks.php';
    require get_template_directory() . '/lib/woocommerce/vinart-woocommerce-template-functions.php';
}

/**
 * Maximum allowed width of content within the theme.
 */
if (!isset($content_width)) {
    $content_width = 1500;
}