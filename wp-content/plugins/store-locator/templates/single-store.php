<?php get_header(); ?>

<?php 
    if ( have_posts() ) : while ( have_posts() ) : the_post(); 
    $address = get_post_meta( get_the_ID(), 'stlo_address', true );
    $latitude = get_post_meta( get_the_ID(), 'stlo_latitude', true );
    $longitude = get_post_meta( get_the_ID(), 'stlo_longitude', true );
    $telephone = get_post_meta( get_the_ID(), 'stlo_telephone', true );
    $website = get_post_meta( get_the_ID(), 'stlo_website', true );
    $email = get_post_meta( get_the_ID(), 'stlo_email', true );
    $description = get_post_meta( get_the_ID(), 'stlo_description', true );
    $galleryUrls = get_post_meta( get_the_ID(), 'stlo_gallery-urls', true );
    $image = get_post_meta( get_the_ID(), 'stlo_image', true );
    $city = get_post_meta( get_the_ID(), 'stlo_city', true );
?>

<section class="sectionWrap" id="storeContentWrap">
	<div class="container">
        <div class="back">
            <a href="<?php echo home_url('storefinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                </svg> Back to Map
            </a>
        </div>

        <div class="mapWrap">
            <span class="overlay"></span>
            <?php if ( $latitude && $longitude ): ?>
                <div class="store-locator-map" data-zoom="16" data-disableDefaultUI="true">
                    <div class="marker" data-lat="<?php echo esc_attr($latitude); ?>" data-lng="<?php echo esc_attr($longitude); ?>"></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="titleWrap">
            <div class="title">
                <h1><?php the_title(); ?></h1>
                <?php if ( $address ): ?>
                    <address><?php echo $address; ?></address>
                <?php endif; ?>
            </div>
            <div class="direction">
                <a href="#" class="direction">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.854 4.854a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 4H3.5A2.5 2.5 0 0 0 1 6.5v8a.5.5 0 0 0 1 0v-8A1.5 1.5 0 0 1 3.5 5h9.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4z"/>
                    </svg>
                    Directions
                </a>
            </div>
        </div>

        <div class="detailsWrap d-flex justify-content-between">

            <div class="details">
                <div class="galleryDetailsWrap">

                    <div class="gallery">
                        <?php 
                            $gallery_images = explode(",",$galleryUrls);
                            if ( $gallery_images ) :
                                $galleryCount = count($gallery_images);
                                $i=1;
                                foreach ( $gallery_images as $gallery_image ): 
                                    $bigImg = ($i == 1) ? 'bigImg' : '' ;
                        ?>
                            <?php if( $i <= 4 ): ?>
                                <a class="item <?php echo $bigImg; ?>" href="<?php echo $gallery_image; ?>" data-fancybox="gallery">
                                    <img src="<?php echo $gallery_image; ?>" alt="" />
                                    <?php if( $i == 4 && ($galleryCount - 4 > 0) ): ?>
                                        <span class="count">Show more(+<?php echo $galleryCount - 4 ; ?>)</span>
                                    <?php endif; ?>
                                </a>
                            <?php else: ?>
                                <a  href="<?php echo $gallery_image; ?>" data-fancybox="gallery"></a>
                            <?php endif; ?>
                        <?php $i++; endforeach; endif; ?>
                    </div>

                    <div class="location">

                        <div class="tele">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                            </svg>
                            <?php echo $telephone; ?>
                        </div>

                        <div class="email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                            </svg>
                            <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
                        </div>

                        <div class="website">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                                <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
                            </svg>
                            <a href="<?php echo $website; ?>" target="_blank" rel="noopener noreferrer"><?php echo $website; ?></a>
                        </div>

                    </div>

                </div>

                <div class="descriptionWrap">
                    <h4 class="title">
                        Description
                    </h4>
                    <?php echo $description; ?>
                </div>

            </div>

        </div>

        <div class="showLocations">
            <a href="<?php echo home_url('storefinder'); ?>">Show all locations</a>
        </div>

	</div>
</section>

<?php endwhile; endif; ?>

<script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            return false;
        }
    }
    
    function showPosition(position) {
        return position;
    }
    getLocation();
</script>

<?php 
get_footer();