<?php 
/* Template Name: Store Finder */
get_header();
?>

<?php 
    if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        $storesData = stores_data();
?>

<section class="sectionWrap" id="storeFinderWrap">
	<div class="container">
		
        <div class="maplistWrap">
            <div class="searchWrap">
                <form action="" method="post">
                    <div class="input-group">
                        <select id="stores" name="stores" placeholder="Search locations">
                            <?php
                                if( $storesData ){
                                    foreach( $storesData as $store ) {
                                        echo '<option value="'.$store['ID'].'">'.$store['title'].' - '.$store['address'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                        <span class="input-group-text">
                            <button type="submit" class="btn">Search</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="searchResultsWrap">

            </div>
        </div>
        <div class="mapWrapper">
            <?php get_template_part( 'template-part/store/map');?>
        </div>

	</div>
</section>

<?php endwhile; endif; ?>

<?php 
	get_footer();