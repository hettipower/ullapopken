<?php /* Template Name: Woocommerce Templates */ ?>
<?php 
	if( is_checkout() ) {
		get_header('checkout');
	} else {
		get_header();
	}
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<section class="sectionWrap" id="wooDefaultWrap">
	<div class="container">
		<?php the_content(); ?>
	</div>
</section>

<?php endwhile; endif; ?>

<?php 
	if( is_checkout() ) {
		get_footer('checkout');
	} else {
		get_footer();
	}