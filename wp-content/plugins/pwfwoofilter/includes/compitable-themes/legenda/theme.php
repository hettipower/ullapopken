<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_legenda_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_legenda_theme_customize_pagination' ) ) {
	function pwf_legenda_theme_customize_pagination( $output, $filter_id, $args ) {
		$output = paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array( // WPCS: XSS ok.
					'base'     => $args['base'],
					'format'   => '',
					'add_args' => false,
					'current'  => max( 1, $args['current'] ),
					'total'    => $args['total'],
					'type'     => 'list',
					'end_size' => 3,
					'mid_size' => 3,
				)
			)
		);

		$output = '<nav class="woocommerce-pagination">' . $output . '</nav>';

		return $output;
	}
}
