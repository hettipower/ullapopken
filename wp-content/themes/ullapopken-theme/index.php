<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<section class="section_wrap" id="banner_wrap"><?php the_post_thumbnail(); ?></section>

<section class="section_wrap" id="default_wrap">
	<div class="container">
		<h1 class="text-center"><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</div>
</section>

<?php endwhile; endif; ?>

<?php get_footer(); ?>