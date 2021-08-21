<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
?>

<script type="text/html" id="tmpl-woo-variation-gallery">
    <div data-thumb="{{data.gallery_thumbnail_src}}" class="woocommerce-product-gallery__image">
        <a href="{{data.full_src}}">
            <img width="{{data.src_w}}" height="{{data.src_h}}" src="{{data.src}}" class="{{data.css_class}}" alt="{{data.alt}}" title="{{data.title}}" data-caption="{{data.caption}}" data-src="{{data.full_src}}" data-large_image="{{data.full_src}}" data-large_image_width="{{data.full_src_w}}" data-large_image_height="{{data.full_src_h}}" srcset="{{data.srcset}}" sizes="{{data.sizes}}"/>
        </a>
    </div>
</script>