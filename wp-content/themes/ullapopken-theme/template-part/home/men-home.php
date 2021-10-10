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