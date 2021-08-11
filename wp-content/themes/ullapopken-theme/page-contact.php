<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<section class="section_wrap" id="banner_wrap"><?php the_post_thumbnail(); ?></section>

<section class="section_wrap" id="contact_wrap">
	<div class="container d-flex flex-wrap">
		<div class="contact_form col-sm-12 col-md-6">
			<h3>Connect with Us</h3>
			<?php echo do_shortcode( '[contact-form-7 id="88" title="Contact Us"]' ); ?>
		</div>
		<div class="contact_details col-sm-12 col-md-6">
			<h3>Contact Details</h3>
			<ul>
				<?php if( get_field('business_address') ): ?>
					<li><i class="fa fa-map-marker" aria-hidden="true"></i> <?php the_field('business_address'); ?></li>
				<?php endif; ?>
				<?php if( get_field('name') ): ?>
					<li><i class="fa fa-user" aria-hidden="true"></i> <?php the_field('name'); ?></li>
				<?php endif; ?>
				<?php if( get_field('telephone') ): ?>
					<li><i class="fa fa-phone" aria-hidden="true"></i> <?php the_field('telephone'); ?></li>
				<?php endif; ?>
				<?php if( get_field('fax') ): ?>
					<li><i class="fa fa-fax" aria-hidden="true"></i> <?php the_field('fax'); ?></li>
				<?php endif; ?>
				<?php if( get_field('mobile') ): ?>
					<li><i class="fa fa-mobile" aria-hidden="true"></i> <?php the_field('mobile'); ?></li>
				<?php endif; ?>
				<?php if( get_field('email') ): ?>
					<li><i class="fa fa-envelope" aria-hidden="true"></i> <?php the_field('email'); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>	
	<div class="google_map">
		<?php
			$location = get_field('google_map');
			if( !empty($location) ):
		?>
			<div class="acf-map">
				<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php endwhile; endif; ?>

<?php get_footer();