<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_stockie_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_stockie_theme_customize_pagination' ) ) {
	function pwf_stockie_theme_customize_pagination( $output, $filter_id, $args ) {
		$output = paginate_links(
			array(
				'base'      => $args['base'],
				'format'    => '',
				'add_args'  => false,
				'current'   => max( 1, $args['current'] ),
				'total'     => $args['total'],
				'prev_text' => '<span class="btn btn-link"><i class="ion-left ion ion-ios-arrow-back"></i> ' . esc_html__( 'Prev', 'stockie' ) . '</span>',
				'next_text' => '<span class="btn btn-link">' . esc_html__( 'Next', 'stockie' ) . ' <i class="ion-right ion ion-ios-arrow-forward"></i></span>',
				'type'      => 'list',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		);

		$output = '<nav class="pagination text-left">' . $output . '</nav>';

		return $output;
	}
}

add_filter( 'pwf_woo_filter_product_loop_template', 'pwf_stockie_theme_customize_product_template', 10, 2 );

if ( ! function_exists( 'pwf_stockie_theme_customize_product_template' ) ) {
	function pwf_stockie_theme_customize_product_template( $template, $filter_id ) {
		$grid_type = StockieSettings::get( 'woocommerce_grid_type', 'global' );
		if ( null === $grid_type ) {
			$grid_type = 'type_1';
		}

		switch ($grid_type) {
			case 'type_1':
				$template = array(
					'grid',
					'product',
				);
				break;
			case 'type_2':
				$template = array(
					'grid',
					'product',
				);
				break;
			case 'type_3':
				$template = array(
					'grid',
					'product',
				);
				break;
			case 'type_4':
				$template = array(
					'content',
					'product-type-4',
				);
				break;
			default:
				$template = array(
					'grid',
					'product',
				);
				break;
		}

		return $template;
	}
}

add_action( 'wp_footer', 'pwf_stockie_theme_js_code', 500 );

if ( ! function_exists( 'pwf_stockie_theme_js_code' ) ) {

	function pwf_stockie_theme_js_code() {
		?>
	<script type="text/javascript">
		(function( $ ) {
			"use strict";
			// file woocommerce-hack.js
			$( document.body ).on( 'pwf_filter_js_ajax_done', function() {

				let pwfFilterSetting  = pwffilterVariables.filter_setting;
				let productsContainer = pwfFilterSetting.products_container_selector;
				let sliders           = $( productsContainer ).find('.slider');

				$(sliders).each(function(){

					if($(this).find('img').length > 1) {
						var slider = $(this);

						if ( slider.parents('.shop-product-type_4').length == 1 && !slider.parents('.product-hover-2').length) {

							slider.owlCarousel({
								items: 1,
								slideBy: 1,
								nav: false,
								dots: true,
								loop: true,
								autoHeight: true,
								autoplay: false,
								autoplayTimeout: 5000,
								autoplayHoverPause: true,
								autoplaySpeed: 1000,
								mouseDrag: false,
								navClass:   ['owl-prev btn-round', 'owl-next btn-round'],
								navText: [ '<i class="ion ion-ios-arrow-back"></i>', '<i class="ion ion-ios-arrow-forward"></i>' ],
							});

						}

						if ( !slider.parents('.product-hover-2').length) {
							slider.owlCarousel({
								items: 1,
								slideBy: 1,
								nav: false,
								dots: true,
								loop: true,
								autoHeight: true,
								autoplay: false,
								autoplayTimeout: 5000,
								autoplayHoverPause: true,
								autoplaySpeed: 1000,
								navClass:   ['owl-prev btn-round', 'owl-next btn-round'],
								navText: [ '<i class="ion ion-ios-arrow-back"></i>', '<i class="ion ion-ios-arrow-forward"></i>' ],
							});
						}
					}
				});

				if ( $(productsContainer).find('.product-hover-2').length ) {
					let img = $(productsContainer).find('img');
					img.each(function(){
						if ( $(this).parents('a').length ) {
							$(this).parents('a').addClass('remove_underline');
						}
					});

					$(sliders).each(function(){
						if ( $(this).find('img').length > 1 ) {
							$(this).addClass('slider-images');
						}
					});
				}

				$( productsContainer ).find('.slider').addClass('visible');
			});
		})(jQuery);
	</script>
		<?php
	}
}
