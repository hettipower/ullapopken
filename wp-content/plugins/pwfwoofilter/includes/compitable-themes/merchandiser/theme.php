<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_merchandiser_theme_before_shop_loop' );

if ( ! function_exists( 'pwf_merchandiser_theme_before_shop_loop' ) ) {
	function pwf_merchandiser_theme_before_shop_loop() {
		global $custom_shop_product_details;
		global $custom_shop_second_image;
		global $custom_shop_stock_label;
		global $custom_catalog_mode;
		global $custom_shop_quick_view;

		$custom_shop_product_details = getbowtied_theme_option( 'shop_product_details', 1 );
		$custom_shop_second_image    = getbowtied_theme_option( 'shop_second_image', 1 );
		$custom_shop_stock_label     = getbowtied_theme_option( 'custom_out_of_stock_label', 'Out of stock' );
		$custom_catalog_mode         = getbowtied_theme_option( 'catalog_mode', 0 );
		$custom_shop_quick_view      = getbowtied_theme_option( 'shop_quick_view', 1 );
	}
}
