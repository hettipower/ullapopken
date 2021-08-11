<?php get_header(); ?>
<section class="section_wrap" id="page_not_found_wrap">
	<div class="container">
		<?php if( get_field('site_logo' , 'option') ): ?>
      		<img src="<?php the_field('site_logo' , 'option'); ?>" alt="<?php bloginfo('name');?>" />
      	<?php endif; ?>
  		<h1>404</h1>
  		<h3>Page Not Found<br/>Sorry, but the page you requested cannot be found.</h3>
	</div>
</section>
<?php get_footer(); ?>