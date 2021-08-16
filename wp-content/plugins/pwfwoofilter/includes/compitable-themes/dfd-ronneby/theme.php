<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_ronneby_theme_customize_before_shop_loop' );

if ( ! function_exists( 'pwf_ronneby_theme_customize_before_shop_loop' ) ) {

	function pwf_ronneby_theme_customize_before_shop_loop() {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 11 );
	}
}

add_filter( 'pwf_wc_setup_loop_args', 'pwf_ronneby_theme_add_change_column_numbers', 10, 2 );

if ( ! function_exists( 'pwf_ronneby_theme_add_change_column_numbers' ) ) {
	function pwf_ronneby_theme_add_change_column_numbers( $args, $filter_id ) {
		global $dfd_ronneby;

		if ( isset( $dfd_ronneby['woo_category_columns'] ) && ! empty( $dfd_ronneby['woo_category_columns'] ) ) {
			$args['columns'] = (int) $dfd_ronneby['woo_category_columns'];
		} else {
			$args['columns'] = 3;
		}

		return $args;
	}
}

add_filter( 'pwf_html_pagination', 'pwf_ronneby_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_ronneby_theme_customize_pagination' ) ) {

	function pwf_ronneby_theme_customize_pagination( $html, $filter_id, $args ) {

		global $dfd_pagination_style;

		if ( empty( $dfd_pagination_style ) || $dfd_pagination_style == '1' ) {
			$pagination_class = 'dfd-pagination-style-1';

			$prev_link = '<div class="prev-next-links">';
			if ( 1 !== $args['paged'] && $args['paged'] > 1 ) {
				$prev_link .= '<a href="/page/' . ( $args['paged'] - 1 ) . '/">' . esc_html__( 'Prev.', 'dfd' ) . '</a>';
			}
			if ( $args['paged'] < $args['pages'] ) {
				$prev_link .= '<a href="/page/' . ( $args['paged'] + 1 ) . '/">' . esc_html__( 'Next', 'dfd' ) . '</a>';
			}
			$prev_link .= '</div>';
		} else {
			$prev_link        = '<div class="prev-link"><a href="/page/' . ( $args['paged'] - 1 ) . '/">' . esc_html__( 'Prev.', 'dfd' ) . '</a></div>';
			$next_link        = '<div class="next-link"><a href="/page/' . ( $args['paged'] + 1 ) . '/">' . esc_html__( 'Next', 'dfd' ) . '</a></div>';
			$pagination_class = 'dfd-pagination-style-' . $dfd_pagination_style;
		}

		$paginate_links = paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array( // WPCS: XSS ok.
					'base'     => $args['base'],
					'format'   => '',
					'add_args' => false,
					'current'  => max( 1, $args['current'] ),
					'total'    => $args['total'],
					'type'     => 'list',
					'mid_size' => 5,
				)
			)
		);

		// Display the pagination if more than one page is found
		if ( $paginate_links ) {
			$output  = '<nav class="page-nav"><div class="pagination ' . esc_attr( $pagination_class ) . '">';
			$output .= $prev_link;
			$output .= $paginate_links;
			$output .= $next_link;
			$output .= '</div></nav>';

			$html = $output;
		}

		return $html;
	}
}

add_action( 'wp_footer', 'pwf_ronneby_theme_js_code', 500 );

if ( ! function_exists( 'pwf_ronneby_theme_js_code' ) ) {

	function pwf_ronneby_theme_js_code() {
		?>
	<script type="text/javascript">
	(function( $ ) {
		"use strict";
		$( document.body ).on( 'pwf_filter_js_ajax_done', function() {
			let pwfFilterSetting  = pwffilterVariables.filter_setting;
			let productsContainer = pwfFilterSetting.products_container_selector;
			let carousel = $( productsContainer ).find('.woo-entry-thumb-carousel');

			$(carousel).each( function() {
				if( ! $(this).hasClass('slick-initialized') ) {
					let speed = $(this).data('speed') ? $carousel.data('speed') : 800;
					$(this).slick({
						infinite: true,
						slidesToShow: 1,
						slidesToScroll: 1,
						arrows: false,
						dots: false,
						fade: true,
						autoplay: true,
						autoplaySpeed: speed,
						pauseOnHover: false
					});
				}
			});

			setTimeout(function(){ 
				$(productsContainer).find('.product').equalHeights();
			}, 100) ;
		});
	})(jQuery);
	</script>
		<?php
	}
}

