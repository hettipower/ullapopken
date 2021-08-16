<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_konte_theme_add_js_code', 1000 );
if ( ! function_exists( 'pwf_konte_theme_add_js_code' ) ) {
	function pwf_konte_theme_add_js_code() {
		?>
		<script type="text/javascript">
		(function( $ ) {
			"use strict";
			$( document.body ).on( "pwf_filter_js_ajax_done", function() {
				let pwfFilterSetting  = pwffilterVariables.filter_setting;
				let productsContainer = pwfFilterSetting.products_container_selector;
				let products          = $(productsContainer).children();
				$( document.body ).trigger( 'konte_products_loaded', [$(products), true] );
			});
		})(jQuery);
		</script>
		<?php
	}
}
