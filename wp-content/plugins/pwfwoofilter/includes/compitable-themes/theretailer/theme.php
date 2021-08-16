<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_theretailer_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_theretailer_theme_customize_pagination' ) ) {

	function pwf_theretailer_theme_customize_pagination( $html, $filter_id, $args ) {

		$output = paginate_links(
			array( // WPCS: XSS ok.
				'base'      => $args['base'],
				'format'    => '',
				'add_args'  => false,
				'current'   => max( 1, $args['current'] ),
				'total'     => $args['total'],
				'prev_text' => '',
				'next_text' => '',
				'type'      => 'plain',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		);

		$output = '<nav class="woocommerce-pagination">' . $output . '</nav>';

		return $output;
	}
}

add_action( 'wp_footer', 'pwf_theretailer_theme_js_code', 500 );

if ( ! function_exists( 'pwf_theretailer_theme_js_code' ) ) {

	function pwf_theretailer_theme_js_code() {
		?>
	<script type="text/javascript">
		(function( $ ) {
			"use strict";
			$(document).on( 'mouseenter', '.product_item', function() {
				$(this).find('.product_button').fadeIn(100, function() {});
			});
			$(document).on( 'mouseleave', '.product_item', function() {
				$(this).find('.product_button').fadeOut(100, function() {});
			});
		})(jQuery);
	</script>
		<?php
	}
}
