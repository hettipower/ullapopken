<?php
    $getShopCookie = theme_get_cookie('shop');

    if( $getShopCookie == 'women' ) {
        $homePageID = get_field( 'women_home', 'option' );
    } else if( $getShopCookie == 'men' ) {
        $homePageID = get_field( 'men_home', 'option' );
    } else {
        $homePageID = get_field( 'women_home', 'option' );
    }
?>

<section class="sectionWrap" id="sliderWrap">
	<?php 
		$top_banner = get_field( 'top_banner' , $homePageID );
		if ( $top_banner ) { 
	?>
		<img src="<?php echo $top_banner['url']; ?>" alt="<?php echo $top_banner['alt']; ?>" />
	<?php } ?>
	<div class="container">
		<div class="contentWrap">
			<h1><?php the_field( 'top_banner_title' , $homePageID ); ?></h1>
			<p><?php the_field( 'top_banner_content' , $homePageID ); ?></p>
			<?php 
				$top_banner_link = get_field( 'top_banner_link' , $homePageID );
				if ( $top_banner_link ) { 
			?>
				<a href="<?php echo $top_banner_link['url']; ?>" target="<?php echo $top_banner_link['target']; ?>" class="btn black-btn"><?php echo $top_banner_link['title']; ?></a>
			<?php } ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="productCatWrap">
	<div class="container">
		<h2>Shop the Collection</h2>
		<div class="productCategories">
			<?php 
				if ( have_rows( 'product_categories' , $homePageID ) ) : while ( have_rows( 'product_categories' , $homePageID ) ) : the_row(); 
					$category_term = get_sub_field( 'category' );
					$banner = get_sub_field( 'banner' );
			?>
				<div class="productCat">
					<a href="<?php echo get_term_link( $category_term ); ?>"></a>
					<?php if ( $banner ) { ?>
						<img src="<?php echo $banner['url']; ?>" alt="<?php echo $banner['alt']; ?>" />
					<?php } ?>
					<?php if ( $category_term ): ?>
						<h3><?php echo $category_term->name; ?></h3>
					<?php endif; ?>
				</div>
			<?php endwhile; endif; ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="currentTrendingWrap">
	<div class="container">
		<h2>Current topics and trends</h2>
		<div class="trendingWrap">
			<?php 
				if ( have_rows( 'current_topics' , $homePageID ) ) : while ( have_rows( 'current_topics' , $homePageID ) ) : the_row(); 
					$image = get_sub_field( 'image' );
					$link = get_sub_field( 'link' );
			?>
				<div class="trending" style="background-color: <?php the_sub_field( 'color' ); ?>">
					<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"></a>
					<?php if ( $image  ) { ?>
						<img src="<?php echo $image ['url']; ?>" alt="<?php echo $image ['alt']; ?>" />
					<?php } ?>
					<div class="content"><?php the_sub_field( 'content' ); ?></div>
				</div>
			<?php endwhile; endif; ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="activeWareWrap">
	<div class="container">
		<h2>Activewear</h2>
		<div class="activeWareSlider">
			<?php 
				if ( have_rows( 'active_ware' , $homePageID ) ) : while ( have_rows( 'active_ware' , $homePageID ) ) : the_row(); 
					$image = get_sub_field( 'image' );
					$term = get_sub_field( 'term' );
			?>
				<div class="activeWare">
					<div class="imgWrap">
						<?php if ( $image  ) { ?>
							<a href="<?php echo get_term_link( $term ); ?>">
								<img src="<?php echo $image ['url']; ?>" alt="<?php echo $image ['alt']; ?>" />
							</a>
							<a class="btn" href="<?php echo get_term_link( $term ); ?>">Shop now</a>
						<?php } ?>
					</div>
					<div class="content">
						<a href="<?php echo get_term_link( $term ); ?>"></a>
						<h3><?php echo $term->name; ?></h3>
						<p><?php the_sub_field( 'sub_title' ); ?></p>
					</div>
				</div>
			<?php endwhile; endif; ?>
		</div>
	</div>
</section>

<section class="sectionWrap" id="bannerWrap">
	<div class="container">
		<?php 
			$banner = get_field( 'banner' , $homePageID );
			if ( $banner ) { 
		?>
			<img src="<?php echo $banner['url']; ?>" alt="<?php echo $banner['alt']; ?>" />
		<?php } ?>
		<?php 
			$banner_link = get_field( 'banner_link' , $homePageID );
			if ( $banner_link ) { 
		?>
			<a href="<?php echo $banner_link['url']; ?>" target="<?php echo $banner_link['target']; ?>"></a>
		<?php } ?>
	</div>
</section>