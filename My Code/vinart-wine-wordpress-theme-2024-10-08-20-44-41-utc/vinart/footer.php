<?php
/**
 * Footer
 *
 * @package WordPress
 * @subpackage vinart
 */
?>

<?php
    /**
     * Functions hooked in to vinart_after_content action
     *
     * @hooked vinart_end_content - 10
     */
    do_action( 'vinart_after_content' );


    /**
     * Functions hooked into vinart_footer action
     *
     * @hooked vinart_footer_wrapper_open            - 5
     * @hooked vinart_footer_widgets                 - 10
     * @hooked vinart_footer_newsletter              - 20
     * @hooked vinart_footer_credit                  - 30
     * @hooked vinart_footer_wrapper_close           - 60
     */
    do_action( 'vinart_footer' );
    
    wp_footer(); ?>
    </body>
</html>