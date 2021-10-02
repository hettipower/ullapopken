<?php 
/* Template Name: Store Finder */
get_header();
?>

<?php 
    if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        $storesData = stores_data();
?>

<section class="sectionWrap" id="storeFinderWrap">
</section>

<?php endwhile; endif; ?>

<?php 
	get_footer();