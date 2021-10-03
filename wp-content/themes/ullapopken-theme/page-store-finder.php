<?php 
/* Template Name: Store Finder */
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php get_template_part( 'template-part/page/top', 'banner' ); ?>

<?php the_content(); ?>

<?php endwhile; endif; ?>

<?php 
	get_footer();