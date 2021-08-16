<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_brooklyn_theme_css_code', 500 );

if ( ! function_exists( 'pwf_brooklyn_theme_css_code' ) ) {
	function pwf_brooklyn_theme_css_code() {
		?>
		<style>
			.pwf-radiolist-label input[type="radio"], 
			.pwf-checkbox-label input[type="checkbox"] {
				display: none !important;
			}
		</style>
		<?php
	}
}
