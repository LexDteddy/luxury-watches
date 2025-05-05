<?php
/**
 * Default Page Header
 *
 * @package WordPress
 * @subpackage vinart
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php
    /**
     * Functions hooked into vinart_preloader action
     *
     * @hooked vinart_preloader                   - 10
     */
    do_action( 'vinart_preloader' );

    /**
     * Functions hooked into vinart_header action
     *
     * @hooked vinart_header_wrapper_open              - 5
     * @hooked vinart_primary_navigation               - 10
     * @hooked vinart_site_branding                    - 20
     * @hooked vinart_main_navigation                  - 30
     * @hooked vinart_header_wrapper_close             - 60
     */
    do_action( 'vinart_header' );

    /**
     * Functions hooked into vinart_header action
     *
     * @hooked vinart_header_wrapper_open              - 5
     */
    do_action( 'vinart_dynamic_header' );

    /**
     * Functions hooked in to vinart_before_content action
     *
     * @hooked vinart_begin_content - 10
     */
    do_action( 'vinart_before_content' );
?>