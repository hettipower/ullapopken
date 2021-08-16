<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_exponent_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_exponent_theme_customize_pagination' ) ) {

	function pwf_exponent_theme_customize_pagination( $output, $filter_id, $args ) {

		$full_width = be_themes_get_option( 'wc_archive_full_width' );
		$alignment  = be_themes_get_option( 'wc_loop_pagination_alignment' );
		$sidebar    = be_themes_get_option( 'wc_loop_sidebar' );
		$gutter_val = esc_attr( exponent_wc_get_gutter_value() );
		$style      = '';
		$pagination = '';

		if ( ! empty( $full_width ) && empty( $sidebar ) ) {
			$style = sprintf( ' style = "padding : 0 %spx"', $gutter_val );
		}

		$alignment_class = ! empty( $alignment ) ? 'exp-pagination-' . $alignment : '';

		if ( $args['total'] > 1 ) {
			$pagination = paginate_links(
				apply_filters(
					'woocommerce_pagination_args',
					array( // WPCS: XSS ok.
						'base'      => $args['base'],
						'format'    => '',
						'add_args'  => false,
						'current'   => max( 1, $args['current'] ),
						'total'     => $args['total'],
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3,
					)
				)
			);
		}

		$output = '<nav class="woocommerce-pagination exp-pagination' . $alignment_class . '"' . $style . '>' . $pagination . '</nav>';

		return $output;
	}
}

add_action( 'wp_footer', 'pwf_exponent_theme_add_js_code', 1000 );
if ( ! function_exists( 'pwf_exponent_theme_add_js_code' ) ) {

	function pwf_exponent_theme_add_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				// function from woocommerce.js
				"use strict";
				function quickView() {
					var quickViewEle = jQuery('.exp-quick-view');
					if( 0 < quickViewEle.length ) {
						quickViewEle.on( 'click', function( e ) {
							var curEle = jQuery( this ),
								productId = curEle.attr( 'data-product-id' );
							if( null != productId ) {
								curEle.addClass( 'exp-show-loader' );
								loadQuickView( productId, curEle );
							}
						} );
					}
				};
				function loadQuickView( productId, quickViewButton ) {
					if( null != exponentThemeConfig ) {
						jQuery.post( exponentThemeConfig.ajaxurl, 
							{ action : 'exponent_quickview', product : productId }, function(response){
								jQuery( document.body ).append( response ); // this line changes by plugin
								var quickViewWrap = jQuery( '.exp-wc-quickview-wrap' ),
									quickviewSlider,
									variationForms,
									productInfo;
								if( 0 < quickViewWrap.length ) {
									quickViewWrap.find( '.exp-wc-close-quickview, .exp-wc-quickview-overlay' ).on( 'click', function( e ) {
										quickViewWrap.removeClass( 'exp-wc-quickview-animate' );
										setTimeout( function() {
											quickViewWrap.remove();
										}, 500 );
									} );
									quickviewSlider = quickViewWrap.find( '.exp-quickview-slider' );  
									variationForms = quickViewWrap.find( '.variations_form' );  
									productInfo = quickViewWrap.find( '.exp-wc-product-info' );
									asyncloader.require( [ 'flickity', 'perfectScrollbar' ], function() {
										if( 0 < quickviewSlider.length ) {
											quickviewSlider.flickity({
												contain : true,
												lazyLoad : '1' == quickviewSlider.attr( 'data-lazy-load' ) ? true : false,
												pageDots : '1' == quickviewSlider.attr('data-dots') && 1 < quickviewSlider.find('.be-slide').length ? true : false,
												prevNextButtons : '1' == quickviewSlider.attr('data-arrows') && 1 < quickviewSlider.find('.be-slide').length ? true : false,
											});
										}
										if( 0 < productInfo.length ) {
											new PerfectScrollbar( productInfo[0] );
										}
										if( 0 < variationForms.length ) {
											variationForms.wc_variation_form();
											variationForms.on( 'found_variation', updateVariationImagesQuickView );
										}
										setTimeout( function() {
											quickViewButton.removeClass( 'exp-show-loader' );
											quickViewWrap.addClass( 'exp-wc-quickview-animate' );
										}, 50);
									});
								}
							});
					}
				};

				function updateVariationImagesQuickView(e, variation) {
					var variationForm = jQuery(this),
						quickView     = variationForm.closest( '.exp-wc-quickview' ),
						galleryHasImage,
						targetSlideImgSrc,
						moveToIndex,
						imagesToReplace,
						quickViewSlider = quickView.find('.exp-quickview-slider' );
					if( 0 < quickViewSlider.length && variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
						galleryHasImage = quickViewSlider.find( '.be-slide img[src = "' + variation.image.src + '"]'  ).length > 0;
						if( galleryHasImage ) {
							moveToIndex = quickViewSlider.find( '.be-slide img[src = "' + variation.image.src + '"]'  ).closest( '.be-slide' ).index();
							quickViewSlider.flickity( 'selectCell', moveToIndex );
						}else {
							targetSlideImgSrc = quickViewSlider.find( '.be-slide' ).eq(0).find( 'img' ).attr( 'src' ) || quickViewSlider.find( '.be-slide' ).eq(0).find( 'img' ).attr( 'data-src' );
							imagesToReplace = quickViewSlider.find( 'img[src = "' + targetSlideImgSrc + '"]' );
							imagesToReplace.each(function() {
								var curImage = jQuery(this);
								if( null != curImage.attr( 'src' ) ) {
									curImage.attr( 'src', variation.image.src );
								}else if( null != curImage.attr( 'data-src' ) ) {
									curImage.attr( 'data-src', variation.image.src );
								}
							});
							quickViewSlider.flickity( 'selectCell', 0 );
						}
					}
				};

				$( document.body ).on( "pwf_filter_js_ajax_done", function() {
					quickView();
					/*let currentTallest  = 0;
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
					}*/
				});
			})(jQuery);
		</script>
		<?php
	}
}
