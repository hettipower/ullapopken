<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'pwf_oceanwp_theme_shop_loop_column_number' );

if ( ! function_exists( 'pwf_oceanwp_theme_shop_loop_column_number' ) ) {
	function pwf_oceanwp_theme_shop_loop_column_number() {
		add_filter( 'pwf_wc_setup_loop_args', 'pwf_oceanwp_theme_change_column_number', 10, 2 );
	}
}

if ( ! function_exists( 'pwf_oceanwp_theme_change_column_number' ) ) {

	function pwf_oceanwp_theme_change_column_number( $args, $filter_id ) {
		$args['columns'] = 3;
		return $args;
	}
}

