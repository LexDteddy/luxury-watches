<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to bootstrapwp_comment() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage vinart
 */

 use VinartTheme\Classes\Vinart_Helper;

// Return early no password has been entered for protected posts.
if (post_password_required())
  return;

//Default value

$vinart_post_comments = Vinart_Helper::get_option( 'post_comments','enabled' );
$vinart_page_comments = Vinart_Helper::get_option( 'page_comments','enabled' );


// If is page and general page comments are disabled return
if (is_page() && $vinart_page_comments == 'disabled')
  return;

// If is post and general page comments are disabled return
if (is_single() && !$vinart_post_comments == 'disabled')
  return;

?>
<div id="comments" class="<?php echo ( comments_open() || have_comments() ) ? 'comments-area' : 'comments-area comments-closed'; ?>">
    <?php if (have_comments()) : ?>

        <h2 class="comments-title">
          <?php
            printf( _nx( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'vinart' ),
              number_format_i18n( get_comments_number() ), get_the_title() );
          ?>
        </h2>

        <ul class="media-list">
            <?php wp_list_comments(array('callback' => 'vinart_comment')); ?>
        </ul><!-- /.commentlist -->

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-below" class="navigation" role="navigation">
                <div class="nav-previous">
                    <?php previous_comments_link( esc_html_e('&larr; Older Comments', 'vinart')); ?>
                </div>
                <div class="nav-next">
                    <?php next_comments_link(esc_html_e('Newer Comments &rarr;', 'vinart')); ?>
                </div>
            </nav>
        <?php endif; // check for comment navigation ?>

        <?php elseif (!comments_open() && '0' != get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
            <p class="nocomments"><?php esc_html_e('Comments are closed.', 'vinart'); ?></p>
    <?php endif; ?>

<?php comment_form(); ?>
</div><!-- #comments .comments-area -->