<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_mediacenter_theme_add_js_code', 1000 );
if ( ! function_exists( 'pwf_mediacenter_theme_add_js_code' ) ) {

	function pwf_mediacenter_theme_add_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				"use strict";
				$( document.body ).on( "pwf_filter_js_ajax_done", function() {
					let currentTallest  = 0;
					let currentRowStart = 0;
					let rowDivs         = new Array();

					columnConform();

					function setConformingHeight(el, newHeight) {
						el.data("originalHeight", (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight")));
						el.height(newHeight);
					}

					function getOriginalHeight(el) {
						return (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight"));
					}

					function columnConform() {

						// find the tallest DIV in the row, and set the heights of all of the DIVs to match it.
						$( '.products > .product' ).each(function() {

							// "caching"
							var $el = $(this);

							if( $el.is( ':visible' ) ) {

								var topPosition = $el.position().top;

								if (currentRowStart != topPosition) {

									// we just came to a new row.  Set all the heights on the completed row
									for ( var currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
										setConformingHeight(rowDivs[currentDiv], currentTallest);
									}

									// set the variables for the new row
									rowDivs.length = 0; // empty the array
									currentRowStart = topPosition;
									currentTallest = getOriginalHeight($el);
									rowDivs.push($el);

								} else {
									rowDivs.push($el);
									currentTallest = (currentTallest < getOriginalHeight($el)) ? (getOriginalHeight($el)) : (currentTallest);

								}

								for ( var currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
									setConformingHeight(rowDivs[currentDiv], currentTallest);
								}
							}

						});
					}
				});
			})(jQuery);
		</script>
		<?php
	}
}
