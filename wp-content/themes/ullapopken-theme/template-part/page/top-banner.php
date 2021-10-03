<section class="sectionWrap <?php the_field( 'style' ); ?>" id="pageBannerWrap">
    <style>
        #pageBannerWrap::after {
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