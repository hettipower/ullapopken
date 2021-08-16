<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_doing_ajax', 'pwf_ciyashop_theme_before_doing_ajax', 10 );
if ( ! function_exists( 'pwf_ciyashop_theme_before_doing_ajax' ) ) {
	function pwf_ciyashop_theme_before_doing_ajax() {
		add_action( 'pwf_before_shop_loop', 'ciyashop_before_page_wrapper_check', 10 );
		add_action( 'pwf_before_shop_loop', 'ciyashop_set_product_list_elements', 40 ); 
		add_action( 'pwf_before_shop_loop', 'ciyashop_shop_loop_item_hover_style_init', 50 );
		add_action( 'pwf_before_shop_loop', 'ciyashop_wc_set_add_to_cart_element', 10 );
		add_action( 'pwf_before_shop_loop', 'ciyashop_wc_wishlist', 10 );
	}
}

add_filter( 'wp_doing_ajax', 'pwf_ciyashop_theme_doing_ajax_filter', 10, 1 );
if ( ! function_exists( 'pwf_ciyashop_theme_doing_ajax_filter' ) ) {
	function pwf_ciyashop_theme_doing_ajax_filter( $is_ajax ) {
		if ( $is_ajax ) {
			$_REQUEST['context'] = 'frontend';
		}

		return $is_ajax;
	}
}

add_action( 'wp_footer', 'pwf_ciyashop_theme_js_code', 500 );

if ( ! function_exists( 'pwf_ciyashop_theme_js_code' ) ) {

	function pwf_ciyashop_theme_js_code() {
		?>
	<script type="text/javascript">
	(function( $ ) {
		"use strict";
		var calculate_margin_list = function ($el) {
			let heightHideInfo = $el.find('.ciyashop-product-description').outerHeight();
			$el.find('.content-hover-block').css({
				marginBottom: -heightHideInfo
			});
			$el.addClass('element-hovered');
		};	
		$( document.body ).on( "pwf_filter_js_ajax_done", function() {
			let pwfFilterSetting  = pwffilterVariables.filter_setting;
			let productsContainer = pwfFilterSetting.products_container_selector;
			let products          = $(productsContainer).find('li');
			let summry = $(productsContainer).find('.ciyashop-product-description');

			if ( summry.length ) {

				$(summry).each( function(){
					let $description_height = $(this).outerHeight();
					if($description_height > 90){
						let btnHTML = '<a href="javascript:void(0)" class="cs-more-btn"><span>load more</span></a>';
						jQuery(this).addClass('ciyashop-short-description');
						jQuery(this).append(btnHTML);
					}
				});

				if ( products.length ) {
					$(products).each(function(){
						calculate_margin_list($(this).find('.product-inner')); // set div margin
					});
				}
			}		
		});
	})(jQuery);
	</script>
		<?php
	}
}
