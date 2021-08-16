<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_result_count', 'pwf_medizin_theme_customize_result_count', 10, 3 );

if ( ! function_exists( 'pwf_medizin_theme_customize_result_count' ) ) {
	function pwf_medizin_theme_customize_result_count( $output, $filter_id, $args ) {

		ob_start();
		echo '<p class="woocommerce-result-count">';
		printf( _n( 'We found %s product available for you', 'We found %s products available for you', $args['total'], 'medizin' ), '<span class="total">' . $args['total'] . '</span>' );
		echo '</p>';
		$output = ob_get_clean();
		return $output;
	}
}


add_action( 'wp_footer', 'pwf_medizin_theme_add_js_code', 10000 );

if ( ! function_exists( 'pwf_medizin_theme_add_js_code' ) ) {
	function pwf_medizin_theme_add_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				"use strict";
				$( document.body ).on( "pwf_filter_js_ajax_done", function() {
					let pwfFilterSetting    = pwffilterVariables.filter_setting;
					let productsContainer   = pwfFilterSetting.products_container_selector;

					$(productsContainer).isotope('destroy');
					$(productsContainer).prepend('<div class="grid-sizer"></div>');
					$(productsContainer).removeAttr('style');
					$(productsContainer).removeClass('loaded');

					let mainProducts = $(productsContainer).closest('.medizin-product');
					$(mainProducts).removeData('MedizinGridLayout');
					$(mainProducts).MedizinGridLayout();
				});
			})(jQuery);
		</script>
		<?php
	}
}
