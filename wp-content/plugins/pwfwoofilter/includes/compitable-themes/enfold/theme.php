<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_enfold_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_enfold_theme_customize_pagination' ) ) {

	function pwf_enfold_theme_customize_pagination( $output, $filter_id, $args ) {
		$page_links = paginate_links(
			array(
				'base'      => $args['base'],
				'format'    => '',
				'add_args'  => false,
				'current'   => max( 1, $args['current'] ),
				'total'     => $args['total'],
				'prev_next' => false,
				'prev_text' => false,
				'type'      => 'plain',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		);

		$num_txt = '<span class="pagination-meta">' . sprintf( __( "Page %d of %d", 'avia_framework' ), $args['current'], $args['total'] ) . '</span>';
		$output  = '<nav class="pagination">' . $num_txt . $page_links . '</nav>';

		return $output;
	}
}

add_action( 'wp_footer', 'pwf_enfold_theme_js_code', 500 );

if ( ! function_exists( 'pwf_enfold_theme_js_code' ) ) {

	function pwf_enfold_theme_js_code() {
		?>
		<style>
			#top .pwf-checkbox-label input[type="checkbox"] {
				display: none;	
			}
			.responsive #top {
				overflow-x: visible !important;
			}
		</style>
		<?php
	}
}
