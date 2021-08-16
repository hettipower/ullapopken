<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_ark_theme_css_code', 500 );

if ( ! function_exists( 'pwf_ark_theme_css_code' ) ) {

	function pwf_ark_theme_css_code() {
		?>
	<style>
		.widget-body .pwf-colorlist-item {
			height: 45px;
		}
	</style>
		<?php
	}
}
