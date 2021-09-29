<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<section class="section_wrap" id="defaultBannerWrap"><?php the_post_thumbnail(); ?></section>

<section class="section_wrap" id="defaultWrap">
	<div class="container">
		<?php the_content(); ?>
	</div>
</section>

<?php endwhile; endif; ?>

<?php get_footer(); ?>