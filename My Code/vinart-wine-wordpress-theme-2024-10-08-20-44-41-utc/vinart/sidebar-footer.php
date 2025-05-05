<?php
/**
 * The dynamically generated footer sidebar
 */
?>

<?php if (is_active_sidebar('footer-hero-area')) : ?>
<div class="footer-hero-area">
	<?php dynamic_sidebar('footer-hero-area'); ?>
</div>
<?php endif;?>

<?php if (is_active_sidebar('footer-widgets-area')) : ?>
<div class="footer-widgets-area">
	<?php dynamic_sidebar('footer-widgets-area'); ?>
</div>
<?php endif;?>
