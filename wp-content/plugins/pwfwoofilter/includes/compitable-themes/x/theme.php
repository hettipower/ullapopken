<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_x_theme_js_code', 500 );

if ( ! function_exists( 'pwf_x_theme_js_code' ) ) {

	function pwf_x_theme_js_code() {
		?>
	<script type="text/javascript">
	(function( $ ) {
		"use strict";
		$( document.body ).on( "pwf_filter_js_ajax_done", function() {
			let t = $("body"),
				e = $(".x-cart-notification");
			e.length > 0 && ( $(".add_to_cart_button.product_type_simple").on("click", function() {
				e.addClass("bring-forward appear loading")
			}), t.on("added_to_cart", function() {
				setTimeout(function() {
					e.removeClass("loading").addClass("added"), setTimeout(function() {
						e.removeClass("appear"), setTimeout(function() {
							e.removeClass("added bring-forward")
						}, 650)
					}, 500)
				}, 650)
			}));
		});
	})(jQuery);
	</script>
		<?php
	}
}
