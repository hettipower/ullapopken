<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	function woo_variation_gallery_settings( $settings ) {
		$settings[] = include 'class-woo-variation-gallery-settings.php';
		
		return $settings;
	}
	
	function woo_variation_gallery_settings_menu() {
		
		$page_title = esc_html__( 'Additional Variation Images Gallery for WooCommerce Settings', 'woo-variation-gallery' );
		$menu_title = esc_html__( 'Gallery Settings', 'woo-variation-gallery' );
		
		$settings_link = esc_url( add_query_arg( array(
			                                         'page' => 'wc-settings',
			                                         'tab'  => 'woo-variation-gallery',
			                                         // 'section' => 'woo-variation-gallery'
		                                         ), admin_url( 'admin.php' ) ) );
		
		add_menu_page( $page_title, $menu_title, 'edit_theme_options', $settings_link, '', 'dashicons-images-alt2', 32 );
		
	}
	
	add_filter( 'woocommerce_get_settings_pages', 'woo_variation_gallery_settings', 11 );
	add_action( 'admin_menu', 'woo_variation_gallery_settings_menu' );
	