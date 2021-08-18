<?php
	$getShopCookie = theme_get_cookie('shop');
	if( !isset($getShopCookie) ) {
		theme_set_cookie( 'shop' , 'women' );
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- Primary Meta Tags -->
	<title><?php wp_title('|',true,'right');?><?php bloginfo('name');?></title>
	<meta name="title" content="<?php bloginfo('name');?>">
	<meta name="description" content="<?php bloginfo('description');?>">

	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo home_url(); ?>">
	<meta property="og:title" content="<?php bloginfo('name');?>">
	<meta property="og:description" content="<?php bloginfo('description');?>">
	<meta property="og:image" content="<?php the_field('site_logo' , 'option'); ?>">

	<!-- Twitter -->
	<meta property="twitter:card" content="summary_large_image">
	<meta property="twitter:url" content="<?php echo home_url(); ?>">
	<meta property="twitter:title" content="<?php bloginfo('name');?>">
	<meta property="twitter:description" content="<?php bloginfo('description');?>">
	<meta property="twitter:image" content="<?php the_field('site_logo' , 'option'); ?>">


	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php if( get_field( 'favicon' , 'option' ) ): ?>
		<link rel="shortcut icon" href="<?php the_field( 'favicon' , 'option' ); ?>" />
	<?php endif; ?>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
	<?php wp_head();?>
</head>
<body <?php body_class(); ?>>

	<header>
		<nav class="navbar navbar-expand-md">
			<div class="topnavvbar">
				<div class="container d-flex justify-content-end">
					<div class="lang"></div>
					<div class="menuWrap">
						<?php
							$defaults = array(
								'menu'            => 'Top Menu',
								'container'       => false,
								'menu_class'      => 'menu',
								'echo'            => true,
								'fallback_cb'     => 'wp_page_menu',
								'items_wrap'      => '<ul id="%1$s" class="%2$s navbar-nav">%3$s</ul>',
								'depth'           => 0
							);
							wp_nav_menu( $defaults );
						?>
					</div>
				</div>
			</div>
			<div class="middleWrap">
				<div class="container d-flex justify-content-between">
					<div class="topCatWrap">
						<ul class="navbar-nav">
							<li class="nav-item">
								<a class="nav-link <?php echo ( $getShopCookie == 'women' || !$getShopCookie ) ? 'active' : '' ; ?>" href="<?php echo admin_url('admin-post.php?action=theme_set_shop_cookie_func&url='.home_url().'&shop=women'); ?>">Women</a>
							</li>
							<li class="nav-item">
								<a class="nav-link <?php echo ( $getShopCookie == 'men' ) ? 'active' : '' ; ?>" href="<?php echo admin_url('admin-post.php?action=theme_set_shop_cookie_func&url='.home_url().'&shop=men'); ?>">Men</a>
							</li>
						</ul>
					</div>
					<a class="navbar-brand" href="<?php echo home_url(); ?>">
						<?php
							$site_logo = get_field( 'site_logo', 'option' ); 
							if( $site_logo ): 
						?>
							<img src="<?php echo $site_logo['url']; ?>" alt="<?php echo $site_logo['alt']; ?>" />
						<?php endif; ?>
					</a>
					<ul class="secondMenuWrap navbar-nav">
						<?php 
							$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
							if (is_user_logged_in()) { 
						?>
							<li class="nav-item">
								<a class="nav-link" href="<?php echo get_permalink( $myaccount_page_id ); ?>"><i class="far fa-user"></i><span>My Account</span></a>
							</li>
						<?php } else{ ?>
							<li class="nav-item">
								<a class="nav-link" href="<?php echo get_permalink( $myaccount_page_id ); ?>"><i class="far fa-user"></i><span>Sign In</span></a>
							</li>
						<?php } ?>
						<li class="nav-item">
							<a class="nav-link" href="#"><i class="fas fa-clipboard-list"></i><span>Quick Order</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo home_url('wishlist'); ?>"><i class="far fa-heart"></i><span>Favorites</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo WC()->cart->get_cart_url(); ?>"><i class="fas fa-shopping-bag"></i><span>Bag</span></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="bottomWrap">
				<div class="container d-flex justify-content-between">
					<div class="mainMenuWrap">
						<?php get_template_part( 'template-part/menu/parent', 'menu-item' ); ?>
					</div>
					<div class="searchWrap">
						<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
							<div class="input-group">
								<input 
									type="search" 
									class="form-control"
									placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>"
									value="<?php echo get_search_query() ?>" name="s" 
								>
								<span class="input-group-text">
									<button type="submit"><i class="fas fa-search"></i></button>
								</span>
							</div>
							<input type="hidden" value="product" name="post_type" id="post_type" />
						</form>
					</div>
				</div>
			</div>
	    </nav>
		<div class="menuContWrap">
			<div class="container">
				<?php get_template_part( 'template-part/menu/submenus' ); ?>
			</div>
		</div>
		<div class="saleWrap"></div>
	</header>