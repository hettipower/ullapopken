<?php
defined( 'ABSPATH' ) || exit;

// URL  https://themeforest.net/item/consulting-business-finance-wordpress-theme/14740561

add_filter( 'pwf_html_pagination', 'pwf_consulting_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_consulting_theme_customize_pagination' ) ) {

	function pwf_consulting_theme_customize_pagination( $output, $filter_id, $args ) {

		if ( $args['total'] > 1 ) {
			$output = paginate_links(
				apply_filters(
					'woocommerce_pagination_args',
					array( // WPCS: XSS ok.
						'base'      => $args['base'],
						'format'    => '',
						'add_args'  => false,
						'current'   => max( 1, $args['current'] ),
						'total'     => $args['total'],
						'prev_text' => '<i class="fa fa-chevron-left"></i>',
						'next_text' => '<i class="fa fa-chevron-right"></i>',
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3,
					)
				)
			);
		} else {
			$output = '<ul class="page-numbers"></ul>';
		}

		return $output;
	}
}

add_filter( 'pwf_html_result_count', 'pwf_consulting_theme_result_count', 10, 3 );

if ( ! function_exists( 'pwf_consulting_theme_result_count' ) ) {

	function pwf_consulting_theme_result_count( $output, $filter_id, $args ) {
		$paged    = $args['current'];
		$per_page = $args['per_page'];
		$total    = $args['total'];
		$first    = ( $per_page * $paged ) - $per_page + 1;
		$last     = min( $total, $per_page * $paged );

		if ( 1 === $total ) {
			$output = esc_html( 'Showing the single result', 'consulting' );
		} elseif ( $total <= $per_page || -1 === $per_page ) {
			$output = sprintf( wp_kses( __( 'Showing all <strong>%d</strong> results', 'consulting' ), array( 'strong' => array() ) ), $total );
		} else {
			$output = sprintf( wp_kses( _x( 'Showing <strong>%1$d&ndash;%2$d</strong> of <strong>%3$d</strong> results', '%1$d = first, %2$d = last, %3$d = total', 'consulting' ), array( 'strong' => array() ) ), $first, $last, $total );
		}

		return '<p class="woocommerce-result-count">' . $output . '</p>';
	}
}
