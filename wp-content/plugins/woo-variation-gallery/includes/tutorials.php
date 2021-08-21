<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
?>

<h2>
	<?php esc_html_e( 'How to tutorials', 'woo-variation-gallery' ); ?>
</h2>

<div id="gwp-variation-gallery-tutorials-wrapper">

    <ul>
        <li>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_gallery()->images_uri( 'tutorial-1.png' ) ?>"></div>
            <div class="tutorial-description-wrapper">
                <h3>Adjust Variation Gallery Width For Desktop, Tablet, Mobile</h3>
                <div class="tutorial-contents">
                    To fit your variation image gallery throughout viewing devices, It offers width control for Desktop, Tablet, and Mobile.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/gallery-tutorial-tab-live-demo-01" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/gallery-tutorial-tab-doc-ajdust-width" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
        </li>
        
        <li>
            <div class="tutorial-description-wrapper">
                <h3>Multiple Image Per Product Variation</h3>
                <div class="tutorial-contents">
                    With default WooCommerce, you can insert only a single image additionally. WooCommerce Additional Variation Images Gallery plugin brings an option to insert unlimited images for each WooCommerce product variation.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/gallery-tutorial-tab-live-demo-01" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/tutorial-tab-upload-image-gallery" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_gallery()->images_uri( 'tutorial-2.png' ) ?>"></div>
        </li>

        <li>
            <div class="tutorial-image-wrapper">
				<?php if ( ! woo_variation_gallery()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-variation-gallery' ) ?></span></div>
				<?php endif; ?>
                <img alt="" src="<?php echo woo_variation_gallery()->images_uri( 'tutorial-3.png' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Display YouTube, Vimeo, Hosted Video Per Product Variation</h3>
                <div class="tutorial-contents">
                    Besides adding extra images per WooCommerce product variation for product image gallery, with this plugin, you can insert unlimited YouTube, Vimeo and Self Hosted videos with ease.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/gallery-tutorial-tab-live-demo-01" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/upload-video-based-videmo-youtube-selfhosted" target="_blank" class="button button-docs">Documentation</a>
					<?php if ( ! woo_variation_gallery()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_gallery()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
					<?php endif; ?>
                </div>
            </div>

        </li>
    </ul>

</div>
