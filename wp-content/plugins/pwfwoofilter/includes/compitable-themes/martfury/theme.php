<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_result_count', 'pwf_martfury_theme_customize_result_count', 10, 3 );

if ( ! function_exists( 'pwf_martfury_theme_customize_result_count' ) ) {

	function pwf_martfury_theme_customize_result_count( $output, $filter_id, $args ) {
		$output = '<div class="products-found"><strong>' . $args['total'] . '</strong>' . esc_html__( 'Products found', 'martfury' ) . '</div>';

		return $output;
	}
}

