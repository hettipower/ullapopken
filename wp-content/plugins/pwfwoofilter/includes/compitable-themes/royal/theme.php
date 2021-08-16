<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_royal_theme_customize_before_shop_loop' );

if ( ! function_exists( 'pwf_royal_theme_customize_before_shop_loop' ) ) {

	function pwf_royal_theme_customize_before_shop_loop() {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	}
}

add_action( 'wp_footer', 'pwf_royal_theme_js_code', 500 );

if ( ! function_exists( 'pwf_royal_theme_js_code' ) ) {

	function pwf_royal_theme_js_code() {
		?>
	<script type="text/javascript">
	(function( $ ) {
		"use strict";
		$( document.body ).on( 'pwf_filter_js_ajax_done', function() {
			let pwfFilterSetting  = pwffilterVariables.filter_setting;
			let productsContainer = pwfFilterSetting.products_container_selector;
			let yithWishButton    = $(productsContainer).find('.yith-wcwl-add-button.show');

			$(yithWishButton).each(function(){
				var wishListText = $(this).find('a').text();
				$(this).find('a').attr('data-hover',wishListText);
			});

			let sliders = $(productsContainer).find('.hover-effect-slider');

			if ( sliders.length ) {
				$(sliders).each(function() {
					var slider = $(this);
					var index = 0;
					var autoSlide;
					var imageLink = slider.find('.product-content-image');
					var imagesList = imageLink.data('images');
					imagesList = imagesList.split(",");
					var arrowsHTML = '<div class="sm-arrow arrow-left">left</div><div class="sm-arrow arrow-right">right</div>';
					var counterHTML = '<div class="slider-counter"><span class="current-index">1</span>/<span class="slides-count">' + imagesList.length + '</span></div>';

					if(imagesList.length > 1) {
						slider.prepend(arrowsHTML);
						//slider.prepend(counterHTML);

						// Previous image on click on left arrow
						slider.find('.arrow-left').click(function(event) {
							if(index > 0) {
								index--;
							} else {
								index = imagesList.length-1; // if the first item set it to last
							}
							imageLink.find('img').attr('src', imagesList[index]).attr('srcset', imagesList[index]); // change image src
							slider.find('.current-index').text(index + 1); // update slider counter
						});

						// Next image on click on left arrow
						slider.find('.arrow-right').click(function(event) {
							if(index < imagesList.length - 1) {
								index++;
							} else {
								index = 0; // if the last image set it to first
							}
							imageLink.find('img').attr('src', imagesList[index]).attr('srcset', imagesList[index]);// change image src
							slider.find('.current-index').text(index + 1);// update slider counter
						});


					}

				});
			}
		});
	})(jQuery);
	</script>
	<style>
.pwf-checkbox-label input[type=checkbox] { display: none !important; }
	</style>
		<?php
	}
}
