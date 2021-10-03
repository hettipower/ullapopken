<?php 
/* Template Name: With Banner */
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php get_template_part( 'template-part/page/top', 'banner' ); ?>

<section class="sectionWrap" id="defaultWrap">
	<div class="container">
        <?php the_content(); ?>
	</div>
</section>

<?php endwhile; endif; ?>

<?php 
	get_footer();