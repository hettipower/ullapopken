<?php 
/* Template Name: With Banner */
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<section class="sectionWrap" id="pageBannerWrap">
    <style>
        .page-template-page-with-banner #pageBannerWrap::after {
            background-color: <?php the_field( 'content_bg' ); ?>
        }
    </style>
    <div class="container">
        <div class="banner">
            <?php if ( get_field( 'banner' ) ) { ?>
                <img src="<?php the_field( 'banner' ); ?>" />
            <?php } ?>
        </div>
        <div class="bannerContent">
            <div class="bannerContInner" style="background-color: <?php the_field( 'content_bg' ); ?>"><?php the_field( 'banner_content' ); ?></div>
        </div>
    </div>
</section>

<section class="sectionWrap" id="defaultWrap">
	<div class="container">
        <?php the_content(); ?>
	</div>
</section>

<?php endwhile; endif; ?>

<?php 
	get_footer();