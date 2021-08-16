<?php
defined( 'ABSPATH' ) || exit;

/**
 *  CSS Code Require
 * .product-grid .product { opacity: 1 !important;}
 */

add_action( 'wp_footer', 'pwf_atelier_theme_js_code', 500 );

if ( ! function_exists( 'pwf_atelier_theme_js_code' ) ) {
	function pwf_atelier_theme_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				"use strict";
				$( document.body ).on( 'pwf_filter_js_ajax_done', function() {
					$('.product').each(function(i) {
						$(this).delay(i*200).animate({
							'opacity' : 1
						}, 800, 'easeOutExpo', function() {
							$(this).addClass('item-animated');
						});
					});
				});
			})(jQuery);
		</script>
		<?php
	}
}
