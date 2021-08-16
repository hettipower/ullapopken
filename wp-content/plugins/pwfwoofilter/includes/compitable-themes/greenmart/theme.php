<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_greenmart_theme_add_js_code', 1000 );
if ( ! function_exists( 'pwf_greenmart_theme_add_js_code' ) ) {

	function pwf_greenmart_theme_add_js_code() {
		?>
		<script type="text/javascript">
		(function( $ ) {
			"use strict";
			$( document.body ).on( "pwf_filter_js_ajax_done", function() {
				layzyLoadImage();
			});
		})(jQuery);
		</script>
		<?php
	}
}
