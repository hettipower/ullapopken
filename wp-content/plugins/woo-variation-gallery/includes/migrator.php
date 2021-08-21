<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	// Migration
	function wvg_add_migration( $tools = array() ) {
		
		$tools[ 'woo_variation_gallery_wc_avi_migrate' ] = array(
			'name'     => esc_html__( 'Migrate from "WooCommerce Additional Variation Images" plugin', 'woo-variation-gallery' ),
			'button'   => esc_html__( 'Start migration', 'woo-variation-gallery' ),
			'desc'     => esc_html__( 'This will migrate from "WooCommerce Additional Variation Images" to "Additional Variation Images Gallery for WooCommerce".', 'woo-variation-gallery' ),
			'callback' => 'wvg_wc_avi_migration_queue'
		);
		
		$tools[ 'woo_variation_gallery_woothumbs_migrate' ] = array(
			'name'     => esc_html__( 'Migrate from "WooThumbs for WooCommerce by Iconic" plugin', 'woo-variation-gallery' ),
			'button'   => esc_html__( 'Start migration', 'woo-variation-gallery' ),
			'desc'     => esc_html__( 'This will migrate from "WooThumbs for WooCommerce by Iconic" to "Additional Variation Images Gallery for WooCommerce".', 'woo-variation-gallery' ),
			'callback' => 'wvg_woothumbs_migration_queue'
		);
		
		$tools[ 'woo_variation_gallery_smart_variations_images_migrate' ] = array(
			'name'     => esc_html__( 'Migrate from "Smart Variations Images for WooCommerce" plugin', 'woo-variation-gallery' ),
			'button'   => esc_html__( 'Start migration', 'woo-variation-gallery' ),
			'desc'     => esc_html__( 'This will migrate from "Smart Variations Images for WooCommerce" to "Additional Variation Images Gallery for WooCommerce".', 'woo-variation-gallery' ),
			'callback' => 'wvg_smart_variations_images_migration_queue'
		);
		
		$tools[ 'woo_variation_gallery_avmi_migrate' ] = array(
			'name'     => esc_html__( 'Migrate from "Ajaxy Woocommerce Multiple Variation Image" plugin', 'woo-variation-gallery' ),
			'button'   => esc_html__( 'Start migration', 'woo-variation-gallery' ),
			'desc'     => esc_html__( 'This will migrate from "Ajaxy Woocommerce Multiple Variation Image" to "Additional Variation Images Gallery for WooCommerce".', 'woo-variation-gallery' ),
			'callback' => 'wvg_avmi_migration_queue'
		);
		
		return $tools;
	}
	
	function wvg_wc_avi_migration_queue() {
		Woo_Variation_Gallery_Migrate::queue_migration( 'woocommerce-additional-variation-images' );
		
		return esc_html__( 'Variation product migration has been scheduled to run in the background.', 'woo-variation-gallery' );
	}
	
	function wvg_woothumbs_migration_queue() {
		Woo_Variation_Gallery_Migrate::queue_migration( 'woothumbs' );
		
		return esc_html__( 'Variation product migration has been scheduled to run in the background.', 'woo-variation-gallery' );
	}
	
	function wvg_smart_variations_images_migration_queue() {
		Woo_Variation_Gallery_Migrate::queue_migration( 'smart-variations-images' );
		
		return esc_html__( 'Variation product migration has been scheduled to run in the background.', 'woo-variation-gallery' );
	}
	
	function wvg_avmi_migration_queue() {
		Woo_Variation_Gallery_Migrate::queue_migration( 'avmi' );
		
		return esc_html__( 'Variation product migration has been scheduled to run in the background.', 'woo-variation-gallery' );
	}
	
	add_filter( 'woocommerce_debug_tools', 'wvg_add_migration' );
	add_filter( 'woo_variation_gallery_migration_list', 'wvg_add_migration' );
	
	add_filter( 'woo_variation_gallery_migrate_images', function ( $images, $migrate_from, $product_id ) {
		
		if ( 'woocommerce-additional-variation-images' === $migrate_from ) {
			$wc_gallery_images = get_post_meta( $product_id, '_wc_additional_variation_images', true );
			$images            = array_values( array_filter( explode( ',', $wc_gallery_images ) ) );
		}
		
		if ( 'woothumbs' === $migrate_from ) {
			$wc_gallery_images = get_post_meta( $product_id, 'variation_image_gallery', true );
			$images            = array_values( array_filter( explode( ',', $wc_gallery_images ) ) );
		}
		
		if ( 'smart-variations-images' === $migrate_from ) {
			$wc_gallery_images = get_post_meta( $product_id, '_product_image_gallery', true );
			$images            = array_values( array_filter( explode( ',', $wc_gallery_images ) ) );
		}
		
		if ( 'avmi' === $migrate_from ) {
			$wc_gallery_images = get_post_meta( $product_id, 'avmi_image_id', true );
			$images            = array_values( array_filter( explode( ',', $wc_gallery_images ) ) );
		}
		
		return $images;
	}, 10, 3 );