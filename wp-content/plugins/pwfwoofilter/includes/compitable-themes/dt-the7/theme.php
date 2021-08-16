<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_the7_theme_customize_before_shop_loop' );

if ( ! function_exists( 'pwf_the7_theme_customize_before_shop_loop' ) ) {

	function pwf_the7_theme_customize_before_shop_loop() {
		global $woocommerce_loop;

		$config    = presscore_config();
		$wc_layout = 'masonry';

		switch ( of_get_option( 'wc_view_mode' ) ) {
			case 'masonry_grid':
				if ( 'grid' === of_get_option( 'woocommerce_shop_template_isotope' ) ) {
					$wc_layout = 'grid';
				}
				break;
			case 'list':
				$wc_layout = 'list';
				break;
			case 'view_mode':
				$wc_layout = 'list';
				if ( 'masonry_grid' === the7_get_view_mode() ) {
					$wc_layout = ( 'grid' === of_get_option( 'woocommerce_shop_template_isotope' ) ? 'grid' : 'masonry' );
				}
				break;
		}

		$config->set( 'layout', $wc_layout );

		//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( in_array( $wc_layout, array( 'masonry', 'grid' ) ) ) {
			$description_style = of_get_option( 'woocommerce_display_product_info', 'wc_btn_on_hoover' );
		} else {
			$description_style = 'under_image';
		}
		$config->set( 'post.preview.description.style', $description_style );
		$config->set( 'justified_grid', false );
		$config->set( 'all_the_same_width', true );
		$config->set( 'image_layout', 'original' );
		$config->set( 'load_style', 'default' );
		$config->set( 'post.preview.load.effect', 'fade_in' );
		$config->set( 'post.preview.background.enabled', false );
		$config->set( 'post.preview.background.style', false );
		$config->set( 'post.preview.description.alignment', 'left' );
		$config->set( 'full_width', false );
		$config->set( 'woocommerce_shop_template_responsiveness', of_get_option( 'woocommerce_shop_template_responsiveness' ) );
		$config->set( 'woocommerce_show_masonry_desc', of_get_option( 'woocommerce_show_masonry_desc' ), false );
		$config->set( 'item_padding', (int) of_get_option( 'woocommerce_shop_template_gap', 20 ), 20 );

		if ( $woocommerce_loop && ! empty( $woocommerce_loop['columns'] ) ) {
			$config->set( 'template.columns.number', absint( $woocommerce_loop['columns'] ) );
		} else {
			$config->set( 'template.columns.number', of_get_option( 'woocommerce_shop_template_columns', 3 ), 3 );
		}

		$config->set( 'post.preview.width.min', (int) of_get_option( 'woocommerce_shop_template_column_min_width', 370 ), 370 );
		$config->set( 'show_titles', of_get_option( 'woocommerce_show_product_titles', true ), true );
		$config->set( 'product.preview.show_price', of_get_option( 'woocommerce_show_product_price', true ), true );
		$config->set( 'product.preview.show_rating', of_get_option( 'woocommerce_show_product_rating', true ), true );
		$config->set( 'product.preview.icons.show_cart', of_get_option( 'woocommerce_show_cart_icon', true ), true );
		$config->set( 'post.preview.load.effect', of_get_option( 'woocommerce_shop_template_loading_effect', 'fade_in' ), 'fade_in' );
		$config->set( 'product.preview.icons.show_cart', true );

		$icons_count = 0;
		if ( $config->get( 'show_details' ) ) {
			$icons_count++;
		}

		if ( $config->get( 'product.preview.icons.show_cart' ) ) {
			$icons_count++;
		}

		$show_product_content = $config->get( 'product.preview.show_price' ) || $config->get( 'product.preview.show_rating' ) || $config->get( 'show_titles' ) || $icons_count > 0;

		$config->set( 'post.preview.content.visible', $show_product_content );

		$products_config = $config->get();

		dt_woocommerce_set_product_cart_button_position();
		dt_woocommerce_product_info_controller();
	}
}

add_action( 'wp_footer', 'pwf_the7_theme_js_code', 500 );

if ( ! function_exists( 'pwf_the7_theme_js_code' ) ) {

	function pwf_the7_theme_js_code() {
		?>
	<script type="text/javascript">
	(function( $ ) {
		"use strict";
		$( document.body ).on( 'pwf_filter_js_ajax_done', function() {
			$(window).scroll();
		});
	})(jQuery);
	</script>
		<?php
	}
}
