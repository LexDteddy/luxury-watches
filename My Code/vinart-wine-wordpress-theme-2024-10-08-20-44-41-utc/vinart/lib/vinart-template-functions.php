<?php
use VinartTheme\Classes\Vinart_Helper;
//Page layout
if (! function_exists('vinart_page_layout')) :
    function vinart_page_layout() {
        
        $post_id = Vinart_Helper::get_post_id();
        $page_layout = 'right-sidebar';

        //Get page layout from each individual page
        $page_layout_select = Vinart_Helper::get_meta( 'vinart_global_meta', 'page_layout_select', 'theme_default', $post_id);
        

        if ( is_home() ) {
            // This checks if the current page is the blog posts index, regardless of the show_on_front setting.
            if ( $page_layout_select == 'theme_default' ) {
                $page_layout = Vinart_Helper::get_option( 'blog_archive_sidebar', 'right-sidebar' );
            } else {
                $page_layout = Vinart_Helper::get_meta( 'vinart_global_meta', 'page_layout', 'right-sidebar', $post_id);
            }
        } elseif ( is_archive() || is_search() ){
            
            $page_layout = Vinart_Helper::get_option( 'blog_archive_sidebar', 'right-sidebar' );

            
            if (function_exists('is_shop') && is_shop() && $page_layout_select != 'theme_default' ) {
                $page_layout = Vinart_Helper::get_meta( 'vinart_global_meta', 'page_layout', 'right-sidebar', $post_id);
            }
        } else {
            
            if ( $page_layout_select == 'theme_default' ) {
                $page_layout = Vinart_Helper::get_option( 'page_global_sidebar', 'right-sidebar' );
            } else {
                $page_layout = Vinart_Helper::get_meta( 'vinart_global_meta', 'page_layout', 'right-sidebar', $post_id);
            }
        }      

        return $page_layout;

    }
endif;

//Page sidebar
if (! function_exists('vinart_page_sidebar')) :
function vinart_page_sidebar() {
    
    $page_layout = vinart_page_layout();
    if ($page_layout == 'no-sidebar') {
        return;
    }

  ?>
  <aside class="sidebar-nav">
      <?php get_sidebar(); ?>
  </aside>
  <!--/aside .sidebar-nav -->

<?php }
endif;

/**
 * Featured image 
 *
 */
if ( ! function_exists( 'vinart_page_header_featured_image' ) ) :

    function vinart_page_header_featured_image($post_id) {
        
        if ( has_post_thumbnail($post_id) && !is_singular('product') && !is_single() && !is_archive() && !is_post_type_archive() && !is_search() && !is_tax('product_cat') ) : ?>
            <div class="page-header-image">
            <?php echo get_the_post_thumbnail( $post_id, 'full' ); ?>
            </div>
        <?php endif;
    }
endif;

if ( ! function_exists( 'vinart_page_header_featured_image_overlay' ) ) :

    function vinart_page_header_featured_image_overlay($post_id) {
        
        if ( has_post_thumbnail($post_id) && (is_singular() || is_post_type_archive('product')) ) {
            echo 'style = "background-image: url('. get_the_post_thumbnail_url( $post_id, 'full' ) .');" ';
        }
    }
        
endif;

/**
 * Next post navigation with featured image
 *
 */
if ( ! function_exists( 'vinart_next_post_nav' ) ) :

    function vinart_next_post_nav( $button_name ) {

        if ( ! $button_name) {
            $button_name = esc_html_e('Next article', 'vinart');
        }

        if ( is_singular( 'post' ) ) :
            
            $next_post = get_previous_post();
        
            if ( is_a( $next_post , 'WP_Post' ) ) : ?>
            <div class="next-post-nav">
                <div class="next-article-title">
                    <span class="top-title"><?php echo esc_html($button_name); ?></span>
                    <span class="next-article"><?php echo get_the_title( $next_post->ID ); ?></span>
                </div>
                <div class="next-article-btns">
                    <a class="button outline primary" href="<?php echo get_permalink( $next_post->ID ); ?>">Read article</a>
                    <a class="button outline primary" href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>">View all</a>
                </div>
            </div>
            <?php endif;
        endif;
    }
endif;


/**
 * Display site header
 *
 */
add_action( 'vinart_header', 'vinart_site_header', 5 );
if ( ! function_exists( 'vinart_site_header' ) ) : 
    /**
     * The global header
     */
    function vinart_site_header() {

        //Hook the builder header
        do_action('okthemes_builder_header');

        if( 'enabled' === Vinart_Helper::check_default_header() ) :
            
        ?>
        <header id="header" class="site-header">
            <div class="header-wrapper">
                <?php
                    vinart_site_branding();
                    vinart_primary_navigation_regular();
                ?>
            </div>
        </header>
    <?php
        endif;
}
endif;

if ( ! function_exists( 'vinart_site_branding' ) ) {
    /**
     * Site branding wrapper and display
     *
     * @since  1.0.0
     * @return void
     */
    function vinart_site_branding() { ?>

        <div class="site-branding" id="main-logo">

            <?php
            $site_logo_type      = Vinart_Helper::get_option( 'site_logo_type', 'text' );
            $site_image_logo      = Vinart_Helper::get_option( 'site_image_logo', ['url' => get_template_directory_uri() . '/assets/images/logo.png'] );
            
            $sticky_header_logo_check      = Vinart_Helper::get_option( 'sticky_header_logo_check', 'no' );
            $sticky_site_image_logo      = Vinart_Helper::get_option( 'sticky_site_image_logo', ['url' => get_template_directory_uri() . '/assets/images/sticky-logo.png'] );
            
            //Normal logo
            if ( $site_logo_type == 'image' && $site_image_logo['url'] ) {
                $image = '<img src="'.esc_url( $site_image_logo['url'] ).'" alt="'.get_bloginfo('name', 'display').'">';
                $html = sprintf(
                    '<a href="%1$s" class="default-logo" rel="home">%2$s</a>',
                    esc_url( home_url( '/' ) ),
                    $image
                );
            } else {
                $html = '<div class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></div>';

                if ( '' !== get_bloginfo( 'description' ) ) {
                    $html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
                }
            }

            //Sticky logo
            if ( $sticky_header_logo_check == 'yes' &&  $sticky_site_image_logo['url'] ) {
                $sticky_image = '<img src="'.esc_url( $sticky_site_image_logo['url'] ).'" alt="'.get_bloginfo('name', 'display').'">';
                $html .= sprintf(
                    '<a href="%1$s" class="sticky-logo" rel="home">%2$s</a>',
                    esc_url( home_url( '/' ) ),
                    $sticky_image
                );
            }

            echo wp_kses($html,'logo'); // WPCS: XSS ok.
            ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'vinart_wpml_language_display' ) ) {
    /**
     * Display Primary Navigation
     *
     * @since  1.0.0
     * @return void
     */
    function vinart_wpml_language_display() {

        $wpml = defined('ICL_SITEPRESS_VERSION');

        if ( ! $wpml ) {
            return;
        }

        //get languages
        $languages = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0' );

        if(!empty($languages)){
            echo '<ul class="vinart-wpml-langs">';
            foreach( $languages as $l ) {
                if(!$l['active']){
                    echo '<li><a href="' . $l['url'] . '">' . $l['code'] . '</a></li>';
                }
            }
            echo '</ul>';
        }

    }
}

if ( ! function_exists( 'vinart_polylang_language_display' ) ) {
    /**
     * Display Primary Navigation
     *
     * @since  1.0.0
     * @return void
     */
    function vinart_polylang_language_display() {

        $polylang = defined('POLYLANG');

        if ( ! $polylang || ! function_exists( 'pll_the_languages' ) ) {
            return;
        }

        echo '<div class="multilanguage-switcher">';
        echo '<ul>';
        pll_the_languages( array('display_names_as' => 'slug' ));
        echo '</ul>';
        echo '</div>';

    }
}

if ( ! function_exists( 'vinart_primary_navigation_regular' ) ) {
    /**
     * Display Primary Navigation
     *
     * @since  1.0.0
     * @return void
     */
    function vinart_primary_navigation_regular() {
        $menu_open_label = Vinart_Helper::get_option( 'header_menu_open_text', esc_html__('Menu', 'vinart') );
        $menu_close_label = Vinart_Helper::get_option( 'header_menu_close_text', esc_html__('Close', 'vinart') );
        ?>

        <!-- primary-mobile-menu -->
        <div class="menu-button-container">
            <button id="primary-mobile-menu" class="button" aria-controls="primary-menu-list" aria-expanded="false">
                <span class="dropdown-icon open"><?php echo esc_html($menu_open_label); ?>
                    <?php echo vinart_get_icons('mobile-menu-toggle');?>
                </span>
                <span class="dropdown-icon close"><?php echo esc_html($menu_close_label); ?>
                <?php echo vinart_get_icons('mobile-menu-toggle-close');?>
                </span>
            </button>
        </div>

        <div class="main-navigation-wrapper" id="main-navbar">

            <?php
            if(has_nav_menu('main-menu')){
                wp_nav_menu(
                    array(
                        'theme_location'  => 'main-menu',
                        'container_class' => 'main-menu',
                        'menu_class'      => 'main-menu-regular',
                        'show_toggles'   => true,
                    )
                );
            }
            else{
                wp_nav_menu( [
                    'theme_location' => 'main-menu',
                    'container'      => 'div',
                    'menu_class'     => 'main-menu',
                    'walker'      => new Vinart_Page_Walker(),
                    'show_toggles'   => true,
                ] );
            }

            vinart_wpml_language_display();
            vinart_polylang_language_display();
            ?>

            <div class="main-header-extras">
            <?php
                /**
                 * Functions hooked in to vinart_header_extras action
                 *
                 * @hooked vinart_header_search - 10
                 * @hooked vinart_header_my_account - 20
                 * @hooked vinart_header_minicart_hook - 30
                 */
                do_action( 'vinart_header_extras' );
            ?>
            </div>

        </div><!-- .main-navigation-wrapper -->

        <?php
    }
}

function vinart_sub_menu_toggle($depth) {
    // Add toggle button.
    $output = '<button class="sub-menu-toggle depth-'.$depth.'" aria-expanded="false" onClick="VinartExpandSubMenu(this)">';
    $output .= '<span class="icon-plus">'.vinart_get_icons('menu-toggle-plus').'</span>';
    $output .= '<span class="icon-minus">'.vinart_get_icons('menu-toggle-minus').'</span>';
    $output .= '<span class="screen-reader-text">' . esc_html__( 'Open menu', 'vinart' ) . '</span>';
    $output .= '</button>';
    return $output;
}

function vinart_add_sub_menu_toggle( $output, $item, $depth, $args ) {
    if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
        // Add toggle button.
        $output .= vinart_sub_menu_toggle($depth);
    }
    return $output;
}
add_filter( 'walker_nav_menu_start_el', 'vinart_add_sub_menu_toggle', 10, 4 );

// Custom walker for wp_page_menu to include submenu toggle buttons and add main-menu-regular class to the ul element
class Vinart_Page_Walker extends Walker_Page {
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class='children sub-menu'>\n";
    }

    function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {
        if ( isset($args['show_toggles']) && $args['show_toggles'] ) {
            $output .= '<li class="page_item page-item-' . $page->ID;
            if (isset($args['pages_with_children'][$page->ID])) {
                $output .= ' page_item_has_children';
            }
            $output .= '">';

            $output .= '<a href="' . get_permalink($page->ID) . '">' . apply_filters('the_title', $page->post_title, $page->ID) . '</a>';

            if (isset($args['pages_with_children'][$page->ID])) {
                $output .= vinart_sub_menu_toggle($depth);
            }
        } else {
            parent::start_el($output, $page, $depth, $args, $current_page);
        }
    }
}

/**
 * Display page header
 *
 */
add_action( 'vinart_subheader', 'vinart_page_header', 0 );
if ( ! function_exists( 'vinart_page_header' ) ) :

	function vinart_page_header() {
		$post_id = Vinart_Helper::get_post_id();

        //Bail if on any of these pages
        if (is_front_page() || is_404() || is_singular('product')) {
            return; 
        }

		// Theme Options
		$page_header = Vinart_Helper::get_option( 'page_header', 'enabled' );
		$page_title  = Vinart_Helper::get_option( 'page_title', 'enabled' );

		// Single post options
		$single_post_meta          = Vinart_Helper::get_option( 'single_post_meta', 'yes' );
		$single_post_meta_category = Vinart_Helper::get_option( 'single_post_meta_category', 'yes' );
		$single_post_meta_date     = Vinart_Helper::get_option( 'single_post_meta_date', 'yes' );

		// Page Options (overwrite)
		$page_header      = Vinart_Helper::get_meta( 'vinart_global_meta', 'meta_page_header', 'enabled');
		$page_title       = Vinart_Helper::get_meta( 'vinart_global_meta', 'meta_page_title', 'enabled');
		$page_top_title   = Vinart_Helper::get_meta( 'vinart_global_meta', 'page_header_top_title', '');
		$page_description = Vinart_Helper::get_meta( 'vinart_global_meta', 'page_header_description', '');

		// WooCommerce pages handling
		if ( function_exists( 'is_product_category' ) && is_product_category() || function_exists( 'is_product_tag' ) && is_product_tag() ) {
			$page_description = wc_format_content( term_description() );
		}

		// Page header conditions
		if ($page_header == 'enabled') {
			?>
            <!-- Page meta -->
            <section id="subheader" class="site-subheader">
                <div class="page-meta" <?php echo vinart_page_header_featured_image_overlay($post_id); ?>>
                    <div class="page-meta-wrapper">
						<?php
						// Display for singular 'post'
						if (is_singular('post') && $single_post_meta === 'yes' && $single_post_meta_category === 'yes') {
							echo vinart_entry_header();
						}

						// Top title
						if ($page_top_title != '') {
							echo '<p class="page-header-toptitle">' . esc_html($page_top_title) . '</p>';
						}

						// Page title display logic
						if ($page_title == 'enabled') {
							vinart_display_page_title(); // This is a refactored function to handle titles
						}

						// Meta for posts
						if (is_singular('post') && $single_post_meta === 'yes' && $single_post_meta_date === 'yes') {
							echo '<div class="entry-meta">' . vinart_posted_on() . '</div>';
						}

						// Description
						if ($page_description != '') {
							echo '<div class="header-page-description">' . wp_kses($page_description, 'page_description') . '</div>';
						}
						?>
                    </div><!-- .page-meta-wrapper -->
                </div><!-- .page-meta -->
            </section>
            <!-- End Page meta -->
			<?php
		}
	}
endif;

function vinart_display_page_title() {
	if (is_archive()) {
		the_archive_title('<h1 class="entry-title">', '</h1>');
	} elseif (is_search()) {
		echo '<h1 class="page-title">' . sprintf(esc_html__('Results for "%s"', 'vinart'), '<span class="page-description search-term">' . esc_html(get_search_query()) . '</span>') . '</h1>';
	} elseif (is_home()) {
		echo '<h1 class="entry-title">' . get_the_title(get_option('page_for_posts', true)) . '</h1>';
	} elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() || function_exists( 'is_shop' ) && is_shop() ) {
		woocommerce_page_title('<h1 class="entry-title">', '</h1>');
	} else {
		the_title('<h1 class="entry-title">', '</h1>');
	}
}

/**
 * Display template for post footer information (in single.php).
 *
 */
if (!function_exists('vinart_posted_in')) :
    function vinart_posted_in() {

    // Translators: used between list items, there is a space after the comma.
    $tag_list = get_the_tag_list('<ul class="list-inline post-tags"><li>','</li><li>','</li></ul>');

    // Translators: 1 is the tags
    if ( $tag_list ) {
        $utility_text = esc_html__( '%1$s', 'vinart' );
    } 

    printf($tag_list);

}
endif;

if ( ! function_exists( 'vinart_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function vinart_posted_on() {
    global $post;
    $author_id = $post->post_author;
    $author = get_the_author_meta('display_name', $author_id);
    // Get the author name; wrap it in a link.
    $byline = sprintf(
        /* translators: %s: post author */
        esc_html__( 'by %s', 'vinart' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $author_id ) ) . '">' . $author . '</a></span>'
    );

    // Finally, let's write all of this to the page.
    echo '<span class="posted-on">' . vinart_time_link() . '</span><span class="byline"> ' . $byline . '</span>';
}
endif;


if ( ! function_exists( 'vinart_time_link' ) ) :
/**
 * Gets a nicely formatted string for the published date.
 */
function vinart_time_link() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
        get_the_date( DATE_W3C ),
        get_the_date(),
        get_the_modified_date( DATE_W3C ),
        get_the_modified_date()
    );

    // Wrap the time string in a link, and preface it with 'Posted on'.
    return sprintf(
        /* translators: %s: post date */
        '<span class="screen-reader-text">%s</span> %s',
        esc_html__( 'Posted on', 'vinart' ),
        '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );
}
endif;

if ( ! function_exists( 'vinart_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function vinart_entry_footer() {

    // Get Tags for posts.
    $tags_list = get_the_tag_list( '' );

    // We don't want to output .entry-footer if it will be empty, so make sure its not.
    if ( $tags_list || get_edit_post_link() ) {

        echo '<footer class="entry-footer">';

            if ( 'post' === get_post_type() ) {
                if ( $tags_list ) {
                    echo '<span class="cat-tags-links">';

                        if ( $tags_list ) {
                            echo '<span class="tags-links"><span class="screen-reader-text">' . esc_html__( 'Tags', 'vinart' ) . '</span>' . $tags_list . '</span>';
                        }

                    echo '</span>';
                }
            }

        echo '</footer> <!-- .entry-footer -->';
    }
}
endif;

if ( ! function_exists( 'vinart_entry_header' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function vinart_entry_header() {

    // Get Categories for posts.
    $categories_list = get_the_category_list();

    // We don't want to output .entry-footer if it will be empty, so make sure its not.
    if ( $categories_list || get_edit_post_link() ) {

        echo '<div class="entry-meta-header">';

            if ( 'post' === get_post_type() ) {
                if ( $categories_list ) {
                    echo '<span class="cat-tags-links">';

                        // Make sure there's more than one category before displaying.
                        if ( $categories_list ) {
                            echo '<span class="cat-links"><span class="screen-reader-text">' . esc_html__( 'Categories', 'vinart' ) . '</span>' . $categories_list . '</span>';
                        }

                    echo '</span>';
                }
            }

        echo '</div>';
    }
}
endif;


/**
 * Excerpt read more
 *
 */

function vinart_excerpt_more( $more ) {
    return '<p class="more-link-wrapper"><a class="btn btn-primary" href="'. get_permalink( get_the_ID() ) . '">' . esc_html__('Read More', 'vinart') . '</a></p>';
}
add_filter( 'excerpt_more', 'vinart_excerpt_more' );

/**
 * Customize Continue reading
 *
 */

add_filter( 'the_content_more_link', 'vinart_read_more_link' );
function vinart_read_more_link() {
    return '<p class="more-link-wrapper"><a class="btn btn-primary" href="' . get_permalink() . '">' . esc_html__('Read More', 'vinart') . '</a></p>';
}

/**
 * Display template for pagination.
 *
 */
if ( !function_exists('vinart_pagination') ) :
    function vinart_pagination($query=null) { 
               
        global $wp_query;
        $query = $query ? $query : $wp_query;
        $total = $query->max_num_pages;
        $big   = 999999999; // need an unlikely integer

        if( !$current_page = get_query_var('paged') )
            $current_page = 1;
        if( get_option('permalink_structure') ) {
            $format = 'page/%#%/';
        } else {
            $format = '&paged=%#%';
        }

        $paginate_links = paginate_links(array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => $format,
            'current'   => max( 1, get_query_var('paged') ),
            'total'     => $total,
            'mid_size'  => 3,
            'type'      => 'list',
            'prev_text' => '<span class="prev-arrow"></span>',
            'next_text' => '<span class="next-arrow"></span>',
        ) );

        // Display the pagination if more than one page is found
        if ( $paginate_links ) {
          echo '<nav class="pagination-wrapper">';
          echo wp_kses($paginate_links,'pagination');
          echo '</nav>';
        }

    }
endif;


/**
 * Display template for comments and pingbacks.
 *
 */
if (!function_exists('vinart_comment')) :
    function vinart_comment($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback' :
            case 'trackback' : ?>

                <li <?php comment_class('media, comment'); ?> id="comment-<?php comment_ID(); ?>">
                    <div class="comments-body">
                        <p>
                            <?php esc_html_e('Pingback:', 'vinart'); ?> <?php comment_author_link(); ?>
                        </p>
                    </div><!--/.media-body -->
                <?php
                break;
            default :
                // Proceed with normal comments.
                global $post; ?>

                <li <?php comment_class('media'); ?> id="comment-<?php comment_ID(); ?>">
                        
                        <div class="comments-body">
                            <div class="comments-body-header">
                                <a href="<?php echo esc_url($comment->comment_author_url); ?>" class="avatar-holder">
                                    <?php echo get_avatar($comment, 70); ?>
                                </a>
                                <div class="vcard">
                                    <h4 class="comment-author ">
                                        <?php
                                        printf('<cite class="fn">%1$s %2$s</cite>',
                                            get_comment_author_link(),
                                            // If current post author is also comment author, make it known visually.
                                            ($comment->user_id === $post->post_author) ? '<span class="label"> ' . esc_html__(
                                                'Post author',
                                                'vinart'
                                            ) . '</span> ' : ''); ?>
                                    </h4>
                                    <p class="meta">
                                        <?php printf('<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                                                esc_url(get_comment_link($comment->comment_ID)),
                                                get_comment_time('c'),
                                                sprintf(
                                                    esc_html__('%1$s at %2$s', 'vinart'),
                                                    get_comment_date(),
                                                    get_comment_time()
                                                )
                                            ); ?>
                                    </p>
                                </div>
                                <?php comment_reply_link( array_merge($args, array(
                                            'reply_text' => esc_html__('Reply', 'vinart'),
                                            'depth'      => $depth,
                                            'max_depth'  => $args['max_depth']
                                        )
                                    )); ?>
                            </div>
                            
                            <div class="comments-body-content">
                                <?php if ('0' == $comment->comment_approved) : ?>
                                    <p class="comment-awaiting-moderation"><?php esc_html_e(
                                        'Your comment is awaiting moderation.',
                                        'vinart'
                                    ); ?></p>
                                <?php endif; ?>

                                <?php comment_text(); ?>
                            </div>
                                                    
                        </div>
                        <!--/.comments-body -->
                <?php
                break;
        endswitch;
    }
endif;


/**
 * Header extra - Header search widget
 **/
add_action( 'vinart_header_extras', 'vinart_header_search', 10 );
if ( ! function_exists('vinart_header_search') ) { 
    function vinart_header_search() {
    $site_header_search = Vinart_Helper::get_option( 'site_header_search', 'disabled' );
    if ( $site_header_search == 'enabled' ) {
    ?>
        <div class="header-extra header-search-widget">
            <a class="top-header-search toggle-search-box" id="trigger-header-search" href="#" title="<?php esc_attr_e('Search products', 'vinart'); ?>"><?php echo vinart_get_icons('header-search');?></a>
            <?php get_template_part( 'parts/part','searchform-overlay' ); ?>
        </div>
        
    <?php

        

    }
    } 
}



if ( ! function_exists( 'vinart_site_preloader' ) ) {
    /**
     * Site preloader
     *
     * @since  1.0.0
     * @return void
     */
    function vinart_site_preloader() { ?>
        <div class="preloader"></div>
    <?php }
}

add_action( 'vinart_footer', 'vinart_site_footer', 5 );
if ( ! function_exists( 'vinart_site_footer' ) ) {
    /**
     * The global footer
     */
    function vinart_site_footer() {
        
        //Hook the builder footer
        do_action('okthemes_builder_footer'); 

        if( 'enabled' === Vinart_Helper::check_default_footer() ) :
        ?>
        <footer id="footer" class="site-footer">
            <?php
                vinart_footer_widgets();
                vinart_footer_newsletter();
                vinart_footer_credit();
            ?>
        </footer>
        
    <?php
        endif;
    }
}

/**
 * Footer credit 
 */
if (!function_exists('vinart_footer_credit')) :
    function vinart_footer_credit() {
        
        //Default value 
        $copyright      = Vinart_Helper::get_option( 'copyright', 'enabled' );
        $copyright_text = Vinart_Helper::get_option( 'copyright_text', 'Copyright © 2024. All rights reserved.' );
        ?>
        
        <div class="footer-credit-wrapper">
            <?php if( $copyright == 'enabled' ) : ?>
            <div class="footer-credit">
                <?php echo wp_kses_post( $copyright_text ); ?>
            </div><!-- /footer-credit -->
            <?php endif; ?>

            <?php vinart_footer_navigation(); ?>
        </div>

    <?php }
endif;


/**
 * Footer newsletter 
 */
if (!function_exists('vinart_footer_newsletter')) :
    function vinart_footer_newsletter() { ?>
        
        <?php if( function_exists('mc4wp_show_form') ) : ?>
        <div class="footer-newsletter-wrapper">
            <?php mc4wp_show_form(); ?>
        </div>
        <?php endif; ?>

    <?php }
endif;


/**
 * Footer widgets
 */
if (!function_exists('vinart_footer_widgets')) :
    function vinart_footer_widgets() {
        if ( is_active_sidebar('footer-widgets-area') ) : ?>
    
            <div class="footer-widgets">
                <?php get_sidebar("footer"); ?>
            </div>
        
        <?php endif;
    }
endif;

if ( ! function_exists( 'vinart_footer_navigation' ) ) {
    /**
     * Display footer Navigation
     *
     * @since  1.0.0
     * @return void
     */
    function vinart_footer_navigation() { ?>

        <div class="footer-navigation" id="footer-nav-content">
            <?php
            if ( has_nav_menu( 'footer-menu' ) ) {
                wp_nav_menu(
                    array(
                        'theme_location'  => 'footer-menu',
                        'container_class' => '',
                        'fallback_cb'     => false,
                        'depth'          => 1
                    )
                );
            }
            ?>
        </div><!-- .footer-navigation -->
        <?php
    }
}

/**
 * Scroll to top functionality
 */
$back_to_top = Vinart_Helper::get_option('back_to_top', 'enabled');

if ($back_to_top == 'enabled') {
    add_action('wp_footer', 'vinart_back_to_top', 25);
    add_action('wp_body_open', 'vinart_add_top_anchor', 10);
}

if (!function_exists('vinart_back_to_top')) {
    function vinart_back_to_top() {       
        ?>
        <a href="#vinart-top" class="scrollup">
            <?php echo vinart_get_icons('scroll-up'); ?>
        </a>
        <?php
    }
}

if (!function_exists('vinart_add_top_anchor')) {
    function vinart_add_top_anchor() {
        echo '<div id="vinart-top"></div>';
    }
}

/**
 * Smooth scrolling functionality
 */
$site_smooth_scroll = Vinart_Helper::get_option('site_smooth_scroll', 'disabled');

if ($site_smooth_scroll == 'enabled') {
    add_action('wp_body_open', 'vinart_open_site_wrapper');
    add_action('okthemes_builder_footer', 'vinart_close_site_wrapper', 26);
}

if (!function_exists('vinart_open_site_wrapper')) {
    function vinart_open_site_wrapper() {
        echo '<div class="site-wrapper inertia">';
    }
}

if (!function_exists('vinart_close_site_wrapper')) {
    function vinart_close_site_wrapper() {
        echo '</div>';
    }
}
