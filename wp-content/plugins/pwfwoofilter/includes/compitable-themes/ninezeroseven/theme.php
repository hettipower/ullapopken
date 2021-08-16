<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_ninezeroseven_theme_customize_before_loop' );

if ( ! function_exists( 'pwf_ninezeroseven_theme_customize_before_loop' ) ) {
	function pwf_ninezeroseven_theme_customize_before_loop() {
		remove_action( 'woocommerce_after_shop_loop', 'wbc_shop_pagination', 10 );
	}
}

add_filter( 'pwf_html_pagination', 'pwf_ninezeroseven_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_ninezeroseven_theme_customize_pagination' ) ) {
	function pwf_ninezeroseven_theme_customize_pagination( $output, $filter_id, $args ) {
		$shop_links = paginate_links(
			array(
				'base'     => $args['base'],
				'format'   => '',
				'add_args' => false,
				'current'  => max( 1, $args['current'] ),
				'total'    => $args['total'],
				'type'     => 'array',
				'end_size' => 3,
				'mid_size' => 3,
			)
		);

		if ( is_array( $shop_links ) ) {
			$output  = '<div class="text-right">';
			$output .= '<ul class="wbc-pagination">';

			foreach ( $shop_links as $link ) {
				$output .= '<li>' . $link . '</li>';
			}

			$output .= '</ul></div>';
		}

		return $output;
	}
}
