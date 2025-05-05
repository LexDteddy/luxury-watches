<?php
//Load the TGM Class
require_once get_template_directory() . '/lib/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'vinart_register_required_plugins' );
function vinart_register_required_plugins() {
    $plugins = array(
        array(
            'name'               => 'OKThemes Toolkit', // The plugin name
            'slug'               => 'okthemes-toolkit', // The plugin slug (typically the folder name)
            'source'             => get_template_directory() . '/plugins/okthemes-toolkit.zip', // The plugin source
            'required'           => true, // If false, the plugin is only 'recommended' instead of required
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'       => '', // If set, overrides default API URL and points to an external URL
            'version'            => '1.3',
        ),     
        array(
            'name'      => 'Elementor Website Builder',
            'slug'      => 'elementor',
            'required'  => true,
        ),
        array(
            'name'      => 'One Click Demo Import',
            'slug'      => 'one-click-demo-import',
            'required'  => false,
        ),
        array(
            'name'      => 'WooCommerce',
            'slug'      => 'woocommerce',
            'required'  => false,
        ),
        array(
            'name'     => 'Contact Form 7',
            'slug'     => 'contact-form-7',
            'required' => false,
        ),
        array(
            'name'     => 'MC4WP: Mailchimp for WordPress',
            'slug'     => 'mailchimp-for-wp',
            'required' => false,
        ),
    );

    $config = array(
        'id'           => 'vinart',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
    );
    tgmpa( $plugins, $config );
}
?>