<?php
/**
 * Search Form Template
 *
 * @package WordPress
 * @subpackage vinart
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="<?php echo esc_attr( uniqid( 'search-form-' ) ); ?>">
        <span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'label', 'vinart' ); ?></span>
    </label>
    <input type="search" id="<?php echo esc_attr( uniqid( 'search-form-' ) ); ?>" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'vinart' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    <button type="submit" class="search-submit"><?php echo esc_html_x( 'Search', 'submit button', 'vinart' ); ?></button>
</form>


