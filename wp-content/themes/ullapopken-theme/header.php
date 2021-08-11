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

	<?php wp_head();?>
</head>
<body <?php body_class(); ?>>

	<header>
		<!-- Static navbar -->
		<nav class="navbar navbar-expand-md mb-4">
	    	<a class="navbar-brand" href="<?php echo home_url(); ?>">
				<?php
					$site_logo = get_field( 'site_logo', 'option' ); 
				  	if( $site_logo ): 
				?>
	          		<img src="<?php echo $site_logo['url']; ?>" alt="<?php echo $site_logo['alt']; ?>" />
	          	<?php endif; ?>
          	</a>
	      	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
	        	<span class="navbar-toggler-icon"></span>
	      	</button>
	      	<div class="collapse navbar-collapse" id="navbarCollapse">
	      		<?php
	            	$defaults = array(
		                'menu'            => 'Main Menu',
		                'container'       => false,
		                'menu_class'      => 'menu',
		                'echo'            => true,
		                'fallback_cb'     => 'wp_page_menu',
		                'items_wrap'      => '<ul id="%1$s" class="%2$s navbar-nav mr-auto">%3$s</ul>',
		                'depth'           => 0
	              	);
	              	wp_nav_menu( $defaults );
	            ?>
	      	</div>
	    </nav>
	</header>