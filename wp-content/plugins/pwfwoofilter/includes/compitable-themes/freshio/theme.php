<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_freshio_theme_add_js_code', 10000 );

if ( ! function_exists( 'pwf_freshio_theme_add_js_code' ) ) {
	function pwf_freshio_theme_add_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				"use strict";
				$( document.body ).on( "pwf_filter_js_ajax_done", function() {
					if ( pwfIsResponsiveView ) {
						$('.freshio-canvas-filter').find('.filter-close').trigger('click');
					}
				});
			})(jQuery);
		</script>
		<?php
	}
}
