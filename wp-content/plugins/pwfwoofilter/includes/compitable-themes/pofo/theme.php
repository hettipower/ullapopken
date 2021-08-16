<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_pofo_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_pofo_theme_customize_pagination' ) ) {
	function pwf_pofo_theme_customize_pagination( $output, $filter_id, $args ) {
		$shop_links = paginate_links(
			array(
				'base'      => $args['base'],
				'format'    => '',
				'add_args'  => false,
				'current'   => max( 1, $args['current'] ),
				'total'     => $args['total'],
				'prev_text' => '<i class="fas fa-long-arrow-alt-left margin-5px-right"></i> <span class="xs-display-none border-none">' . esc_html__( 'Prev', 'pofo' ) . '</span>',
				'next_text' => '<span class="xs-display-none border-none">' . esc_html__( 'Next', 'pofo' ) . '</span> <i class="fas fa-long-arrow-alt-right margin-5px-left"></i>',
				'type'      => 'plain',
				'end_size'  => 2,
				'mid_size'  => 2,
			)
		);

		$output = '<div class=" text-center clear-both float-left width-100"><div class="woocommerce-pagination text-small text-uppercase text-extra-dark-gray pagination">' . $shop_links . '</div></div>';

		return $output;
	}
}
