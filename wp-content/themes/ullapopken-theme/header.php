<?php
	$getShopCookie = theme_get_cookie('shop');
	if( !isset($getShopCookie) ) {
		theme_set_cookie( 'shop' , 'women' );
	} else {
		if( is_shop() ) {
			$women_category = get_field('women_category' , 'option');
			$men_category = get_field('men_category' , 'option');

			if( $getShopCookie == 'women' ) {
				wp_redirect(get_term_link($women_category));
			} else if( $getShopCookie == 'men' ) {
				wp_redirect(get_term_link($men_category));
			}
		}
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
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
	<?php wp_head();?>
</head>
<body <?php body_class(); ?>>

<div id="page">
<?php 
	$detect = new Mobile_Detect;
	
	if( $detect->isMobile() || $detect->isTablet() ) {
		get_template_part( 'template-part/header/mobile' );
	} else {
		get_template_part( 'template-part/header/desktop' );
	}
?>