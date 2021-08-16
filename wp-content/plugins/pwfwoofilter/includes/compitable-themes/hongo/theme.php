<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pwf_hongo_theme_customize_before_shop_loop' ) ) {

	add_action( 'pwf_before_shop_loop', 'pwf_hongo_theme_customize_before_shop_loop', 10, 1 );

	function pwf_hongo_theme_customize_before_shop_loop( $filter_id ) {
		$meta            = get_post_meta( absint( $filter_id ), '_pwf_woo_post_filter', true );
		$settings        = $meta['setting'];
		$pagination_type = $settings['pagination_type'];

		$product_archive_list_style = hongo_get_product_archive_list_style();

		if ( ! empty( $product_archive_list_style ) ) {

			switch ( $product_archive_list_style ) {

				case 'shop-minimalist':
				case 'shop-classic':
				case 'shop-clean':
				case 'shop-flat':
				case 'shop-list':
				case 'shop-masonry':
				case 'shop-metro':
				case 'shop-modern':
				case 'shop-standard':
				case 'shop-simple':
					require get_parent_theme_file_path( "/lib/woocommerce/archive-page-functions/{$product_archive_list_style}-functions.php" );
					break;
				case 'shop-default':
				default:
					require get_parent_theme_file_path( '/lib/woocommerce/archive-page-functions/shop-default-functions.php' );
					break;
			}
		}

		do_action( 'hongo_before_shop_loop', $product_archive_list_style );

		/**
		 * Hook: hongo_before_shop_loop_***
		 *
		 * @hooked hongo_before_shop_loop_***_callback - 10
		 */
		do_action( 'hongo_before_shop_loop_' . $product_archive_list_style, $product_archive_list_style );

		/**
		 * Hook: hongo_before_shop_loop_style_after
		 */
		do_action( 'hongo_before_shop_loop_style_after', $product_archive_list_style );

		if ( 'shop-list' !== $product_archive_list_style && 'numbers' === $pagination_type ) {
			echo '<li class="grid-sizer"></li>';
		} elseif ( 'shop-list' !== $product_archive_list_style && 'numbers' !== $pagination_type ) {
			if ( isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) && ! empty( $_POST['attributes'] ) ) {
				if ( ! isset( $_POST['attributes']['page'] ) ) {
					echo '<li class="grid-sizer"></li>';
				}
			}
		}
	}
}

add_filter( 'pwf_html_result_count', 'pwf_hongo_theme_result_count', 10, 3 );

if ( ! function_exists( 'pwf_hongo_theme_result_count' ) ) {

	function pwf_hongo_theme_result_count( $output, $filter_id, $args ) {
		$paged    = $args['current'];
		$per_page = $args['per_page'];
		$total    = $args['total'];

		if ( 1 === $total ) {
			$output = esc_html( 'Showing the single result', 'hongo' );
		} elseif ( $total <= $per_page || -1 === $per_page ) {
			/* translators: %d: total results */
			$output = sprintf( _n( 'Showing all %d result', 'Showing all %d results', $total, 'hongo' ), $total );
		} else {
			$first = ( $per_page * $paged ) - $per_page + 1;
			$last  = min( $total, $per_page * $paged );
			/* translators: 1: first result 2: last result 3: total results */
			$output = sprintf( _nx( 'Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'hongo' ), $first, $last, $total );
		}

		$output = '<div class="woocommerce-result-count">' . $output . '</div>';

		return $output;
	}
}

add_action( 'wp_footer', 'pwf_hongo_theme_js_code', 500 );

if ( ! function_exists( 'pwf_hongo_theme_js_code' ) ) {

	function pwf_hongo_theme_js_code() {
		?>
<script type="text/javascript">
	(function( $ ) {
		"use strict";
		$( document.body ).on( 'pwf_filter_js_ajax_done', function( event, data ) {
			let pwfFilterSetting  = pwffilterVariables.filter_setting;
			let productsContainer = pwfFilterSetting.products_container_selector;

			if ( $(productsContainer).hasClass('hongo-shop-common-isotope') ) {
				if ( data.queryArgs.hasOwnProperty('attributes') && ! data.queryArgs.attributes.hasOwnProperty('page') ) {
					if ( $(productsContainer).data( 'isotope' ) ) {
						$(productsContainer).isotope( 'destroy' );
					}
					let transitionTime = 0;
					if ( $( '.hongo-column-switch' ).length > 0 ) { // Column switch is found
						transitionTime = '0.4s';
					}
					$(productsContainer).imagesLoaded(function () {
						$(productsContainer).isotope({
							layoutMode: 'masonry',
							itemSelector: '.product',
							percentPosition: true,
							transitionDuration: transitionTime,
							stagger: 0,
							masonry: {
								columnWidth: '.grid-sizer',
							},
						});
					});

				} else {
					$(productsContainer).imagesLoaded(function () {
						let $items = $($(productsContainer).find('.pwf-new-product-added'));
						$(productsContainer).isotope( 'appended', $items );
					});
				}

				let sliders = $('.pwf-new-product-added').find('.hongo-loop-product-slider');
				if ( $(sliders).length ) {
					$(sliders).each(function ( index, element ) {
						let $this = $( this );
						let enableNavigation = $this.attr( 'data-attr' );
						let navigationOption = false;
						$this.addClass( 'loop-slider-'+ index );
						if( enableNavigation == 1 ){
							navigationOption = {
								nextEl: '.loop-slider-'+index+' .swiper-button-next',
								prevEl: '.loop-slider-'+index+' .swiper-button-prev',
							};
						}
						let swiperProductLoops = new Swiper( '.loop-slider-'+ index , {
							navigation: navigationOption,
							on: {
								resize: function () {
									this.update();
								}
							}
						});
					});
				}

			}
		});
	})(jQuery);
</script>
		<?php
	}
}
