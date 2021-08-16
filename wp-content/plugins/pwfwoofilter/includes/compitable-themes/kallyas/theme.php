<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_kallyas_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_kallyas_theme_customize_pagination' ) ) {
	function pwf_kallyas_theme_customize_pagination( $html, $filter_id, $args ) {

		$args = array(
			'range'         => 3,
			'showitems'     => 7,
			'paged'         => $args['current'],
			'pages'         => $args['total'],
			'previous_text' => esc_html__( 'Newer posts', 'zn_framework' ),
			'older_text'    => esc_html__( 'Older posts', 'zn_framework' ),
			'list_class'    => 'kl-pagination',
		);

		$output = '<div class="pagination--light">';

		if ( (int) $args['pages'] > 1 ) {

			$output .= '<ul class="zn-paginationList ' . $args['list_class'] . '">';

			if ( false !== $args['previous_text'] ) {
				if ( 1 !== $args['paged'] && $args['paged'] > 1 ) {
					$prev_text = '<a href="/page/' . ( $args['paged'] - 1 ) . '/"><span class="zn_icon" data-zniconfam="glyphicons_halflingsregular" data-zn_icon="&#xe257;"></span></a>';
					$output   .= '<li class="pagination-item pagination-item-prev pagination-prev">' . $prev_text . '</li>';
				} else {
					$output .= '<li class="pagination-item pagination-item-prev pagination-prev"><span class="pagination-item-span pagination-item-span-prev">' . $args['previous_text'] . '</span></li>';
				}
			}

			for ( $i = 1; $i <= $args['pages']; $i++ ) {
				if ( ! ( $i >= $args['paged'] + $args['range'] + 1 || $i <= $args['paged'] - $args['range'] - 1 ) || $args['pages'] <= $args['showitems'] ) {
					if ( $args['paged'] === $i ) {
						$output .= '<li class="pagination-item pagination-item-active active"><span class="pagination-item-span pagination-item-active-span">' . $i . '</span></li>';
					} else {
						$output .= '<li class="pagination-item"><a class="pagination-item-link" href="/page/' . $i . '/"><span class="pagination-item-span">'.$i.'</span></a></li>';
					}
				}
			}

			if ( false !== $args['older_text'] ) {

				if ( $args['paged'] < $args['pages'] ) {
					$text    = '<a href="/page/' . ( $args['paged'] + 1 ) . '/"><span class="zn_icon" data-zniconfam="glyphicons_halflingsregular" data-zn_icon="&#xe258;"></span></a>';
					$output .= '<li class="pagination-item pagination-item-next pagination-next">' . $text . '</li>';
				}
				else {
					$output .= '<li class="pagination-item pagination-item-next pagination-next"><span class="pagination-item-span pagination-item-span-next">' . $args['older_text'] . '</span></li>';
				}
			}
			$output .= '</ul></div>';
		}

		return $output;
	}
}
