<?php /* Template Name: Home */ ?>
<?php get_header(); ?>

<section class="sectionWrap" id="sliderWrap">
	<?php 
		$top_banner = get_field( 'top_banner' );
		if ( $top_banner ) { 
	?>
		<img src="<?php echo $top_banner['url']; ?>" alt="<?php echo $top_banner['alt']; ?>" />
	<?php } ?>
	<div class="container">
		<div class="contentWrap">
			<h1><?php the_field( 'top_banner_title' ); ?></h1>
			<p><?php the_field( 'top_banner_content' ); ?></p>
			<?php 
				$top_banner_link = get_field( 'top_banner_link' );
				if ( $top_banner_link ) { 
			?>
				<a href="<?php echo $top_banner_link['url']; ?>" target="<?php echo $top_banner_link['target']; ?>" class="btn black-btn"><?php echo $top_banner_link['title']; ?></a>
			<?php } ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="bannerWrap">
	<div class="container">
		<?php 
			$banner = get_field( 'banner' );
			if ( $banner ) { 
		?>
			<img src="<?php echo $banner['url']; ?>" alt="<?php echo $banner['alt']; ?>" />
		<?php } ?>
		<?php 
			$banner_link = get_field( 'banner_link' );
			if ( $banner_link ) { 
		?>
			<a href="<?php echo $banner_link['url']; ?>" target="<?php echo $banner_link['target']; ?>"></a>
		<?php } ?>
	</div>
</section>

<section class="sectionWrap" id="trendsWrap">
	<div class="container">
		<h2><?php the_field( 'trends_title' ); ?></h2>
		<div class="trends">
			<?php 
				$i=1;
				if ( have_rows( 'trends' ) ) : while ( have_rows( 'trends' ) ) : the_row(); 
			?>
				<div class="trend trend-<?php echo $i; ?>">
					<?php 
						$link = get_sub_field( 'link' );
						$image = get_sub_field( 'image' );
						if ( $image ) { 
					?>
						<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
					<?php } ?>
					<div class="titleWrap" style="background-color: <?php the_sub_field( 'color' ); ?>">
						<h5><?php echo $link['title']; ?></h5>
						<p><?php the_sub_field( 'title' ); ?></p>
					</div>
					<?php if ( $link ) { ?>
						<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"></a>
					<?php } ?>
				</div>	
			<?php $i++; endwhile; endif; ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="featuredBannerWrap">
	<div class="container">
		<div class="innerContent">
			<?php 
				$featured_banner_link = get_field( 'featured_banner_link' );
				$featured_banner = get_field( 'featured_banner' );
				if ( $featured_banner ) { 
			?>
				<img src="<?php echo $featured_banner['url']; ?>" alt="<?php echo $featured_banner['alt']; ?>" />
			<?php } ?>
			<div class="titleWrap">
				<h5><?php echo $featured_banner_link['title']; ?></h5>
				<p><?php the_field( 'featured_banner_content' ); ?></p>
			</div>
			<?php if ( $featured_banner_link ) { ?>
				<a href="<?php echo $featured_banner_link['url']; ?>" target="<?php echo $featured_banner_link['target']; ?>"></a>
			<?php } ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="subscribeWrap">
	<div class="container">
		<div class="innerWrap">
			<?php the_field( 'subscribe_text' ); ?>
			<?php 
				$subscribe_link = get_field( 'subscribe_link' );
				if ( $subscribe_link ) { 
			?>
				<a class="btn black-btn" href="<?php echo $subscribe_link['url']; ?>" target="<?php echo $subscribe_link['target']; ?>"><?php echo $subscribe_link['title']; ?></a>
			<?php } ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="aboutContWrap">
	<div class="container">
		<h2><?php the_field( 'about_title' ); ?></h2>
		<div class="content"><?php the_field( 'about_content' ); ?></div>
	</div>
</section>

<?php get_footer();