<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_stockholm_theme_customize_before_loop' );
if ( ! function_exists( 'pwf_stockholm_theme_customize_before_loop' ) ) {

	function pwf_stockholm_theme_customize_before_loop() {
		$product_list_type = stockholm_qode_options()->getOptionValue( 'woo_products_list_type' );
		if ( 'standard' === $product_list_type ) {
			do_action( 'stockholm_qode_action_shop_standard_initial_setup' );
		}

		if ( 'simple' === $product_list_type ) {
			do_action( 'stockholm_qode_action_shop_simple_initial_setup' );
		}
	}
}
