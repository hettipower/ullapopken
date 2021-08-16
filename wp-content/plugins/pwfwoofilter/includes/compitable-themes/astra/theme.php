<?php
defined( 'ABSPATH' ) || exit;

/**
 * @since 1.1.2, 1.1.3
 */
add_action( 'pwf_before_doing_ajax', 'pwf_astra_theme_before_doing_ajax', 10 );

if ( ! function_exists( 'pwf_astra_theme_before_doing_ajax' ) ) {
	function pwf_astra_theme_before_doing_ajax() {
		if ( class_exists( 'Astra_Woocommerce' ) && class_exists( 'ASTRA_Ext_WooCommerce_Markup' ) ) {
			$astra_woocommerce_instance   = Astra_Woocommerce::get_instance();
			$astra_ext_woocommerce_markup = ASTRA_Ext_WooCommerce_Markup::get_instance();
			add_action( 'pwf_before_shop_loop', array( $astra_woocommerce_instance, 'woocommerce_init' ), 10 );
			add_action( 'pwf_before_shop_loop', array( $astra_woocommerce_instance, 'shop_customization' ), 20 );
			add_action( 'pwf_before_shop_loop', array( $astra_woocommerce_instance, 'shop_page_styles' ), 20 );
			add_action( 'pwf_before_shop_loop', array( $astra_ext_woocommerce_markup, 'common_actions' ), 999 );
		} elseif ( class_exists( 'Astra_Woocommerce' ) ) {
			$astra_woocommerce_instance = Astra_Woocommerce::get_instance();
			add_action( 'pwf_before_shop_loop', array( $astra_woocommerce_instance, 'woocommerce_init' ), 10 );
			add_action( 'pwf_before_shop_loop', array( $astra_woocommerce_instance, 'shop_customization' ), 20 );
		}
	}
}
