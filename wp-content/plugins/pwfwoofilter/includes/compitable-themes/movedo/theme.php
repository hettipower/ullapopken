<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_movedo_theme_js_code', 500 );

if ( ! function_exists( 'pwf_movedo_theme_js_code' ) ) {

	function pwf_movedo_theme_js_code() {
		?>
<script type="text/javascript">
	(function( $ ) {
		"use strict";
		$( document.body ).on( 'pwf_filter_js_ajax_done', function() {
			let pwfFilterSetting  = pwffilterVariables.filter_setting;
			let productsContainer = pwfFilterSetting.products_container_selector;
			$(productsContainer).isotope('destroy').removeAttr('style');
			setTimeout(function(){
				GRVE.wooProductsLoop.init();
			},100);
		});
	})(jQuery);
</script>
		<?php
	}
}
