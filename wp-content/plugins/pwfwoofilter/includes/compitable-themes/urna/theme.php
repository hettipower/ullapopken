<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_urna_theme_add_js_code', 1000 );

if ( ! function_exists( 'pwf_urna_theme_add_js_code' ) ) {
	function pwf_urna_theme_add_js_code() {
		?>
		<script type="text/javascript">
		(function( $ ) {
			"use strict";
			$( document.body ).on( "pwf_filter_js_ajax_done", function() {
				new layzyLoadImage();
			});
		})(jQuery);
		</script>
		<?php
	}
}
