<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_upstore_theme_customize_before_loop' );

if ( ! function_exists( 'pwf_upstore_theme_customize_before_loop' ) ) {
	function pwf_upstore_theme_customize_before_loop() {
		if ( function_exists( 'upstore_remove_hooks_from_shop_loop' ) ) {
			upstore_remove_hooks_from_shop_loop();
			$show_grid_desc = upstore_get_theme_options( 'ts_prod_cat_grid_desc' );
			if ( ! $show_grid_desc ) {
				remove_action( 'woocommerce_after_shop_loop_item', 'upstore_template_loop_short_description', 60 );
			}
		}
	}
}
