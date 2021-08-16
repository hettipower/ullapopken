<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_thefox_theme_customize_before_loop', 10, 1 );

if ( ! function_exists( 'pwf_thefox_theme_customize_before_loop' ) ) {

	function pwf_thefox_theme_customize_before_loop( $filter_id ) {
		add_filter( 'woocommerce_post_class', 'pwf_thefox_theme_add_css_for_num_of_columns', 10, 1 );
	}
}

add_filter( 'pwf_wc_setup_loop_args', 'pwf_thefox_theme_add_change_column_numbers', 10, 2 );

if ( ! function_exists( 'pwf_thefox_theme_add_change_column_numbers' ) ) {
	function pwf_thefox_theme_add_change_column_numbers( $args, $filter_id ) {
		global $rd_data;
		$cols = '';

		if ( 4 == $rd_data['rd_shop_columns'] ) {
			$cols = 4;
		} elseif ( 2 == $rd_data['rd_shop_columns'] ) {
			$cols = 2;
		} else {
			$cols = 3;
		}

		if ( ! empty( $cols ) ) {
			$args['columns'] = $cols;
		}

		return $args;
	}
}
if ( ! function_exists( 'pwf_thefox_theme_add_css_for_num_of_columns' ) ) {
	function pwf_thefox_theme_add_css_for_num_of_columns( $classes ) {
		global $rd_data;
		$col_class = '';

		if ( 4 == $rd_data['rd_shop_columns'] ) {
			$col_class = 'shop_four_col';
		} elseif ( 2 == $rd_data['rd_shop_columns'] ) {
			$col_class = 'shop_two_col';
		} else {
			$col_class = 'shop_three_col';
		}

		if ( ! empty( $col_class ) ) {
			array_push( $classes, $col_class );
		}

		return $classes;
	}
}
