<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_flatsome_theme_add_js_code', 10000 );

if ( ! function_exists( 'pwf_flatsome_theme_add_js_code' ) ) {
	function pwf_flatsome_theme_add_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				"use strict";
				$( document.body ).on( "pwf_filter_js_ajax_done", function( ) {

					$('.pwf-new-product-added').find(".quick-view").each((function(t, e) {
						jQuery(e).hasClass("quick-view-added") || ( jQuery(e).on("click", (function(t) {
							if ("" != jQuery(this).attr("data-prod")) {
								jQuery(this).parent().parent().addClass("processing");
								var e = {
									action: "flatsome_quickview",
									product: jQuery(this).attr("data-prod")
								};
								jQuery.post(flatsomeVars.ajaxurl, e, (function(t) {
									jQuery(".processing").removeClass("processing"), jQuery.magnificPopup.open({
										removalDelay: 300,
										autoFocusLast: !1,
										closeMarkup: flatsomeVars.lightbox.close_markup,
										closeBtnInside: flatsomeVars.lightbox.close_btn_inside,
										items: {
											src: '<div class="product-lightbox lightbox-content">' + t + "</div>",
											type: "inline"
										}
									});
									var e = jQuery(".product-gallery-slider img", t).length > 1;
									setTimeout((function() {
										jQuery(".product-lightbox").imagesLoaded((function() {
											jQuery(".product-lightbox .slider").flickity({
												cellAlign: "left",
												wrapAround: !0,
												autoPlay: !1,
												prevNextButtons: !0,
												adaptiveHeight: !0,
												imagesLoaded: !0,
												dragThreshold: 15,
												pageDots: e,
												rightToLeft: flatsomeVars.rtl
											})
										}))
									}), 300);
									var i = jQuery(".product-lightbox form.variations_form");
									jQuery(".product-lightbox form").hasClass("variations_form") && i.wc_variation_form();
									var r = jQuery(".product-lightbox .product-gallery-slider"),
										o = jQuery(".product-lightbox .product-gallery-slider .slide.first img"),
										a = jQuery(".product-lightbox .product-gallery-slider .slide.first a"),
										s = o.attr("data-src") ? o.attr("data-src") : o.attr("src"),
										n = function() {
											r.data("flickity") && r.flickity("select", 0)
										},
										c = function() {
											r.data("flickity") && r.imagesLoaded((function() {
												r.flickity("resize")
											}))
										};
									i.on("show_variation", (function(t, e) {
										e.image.src ? (o.attr("src", e.image.src).attr("srcset", ""), a.attr("href", e.image_link), n(), c()) : e.image_src && (o.attr("src", e.image_src).attr("srcset", ""), a.attr("href", e.image_link), n(), c())
									})), i.on("hide_variation", (function(t, e) {
										o.attr("src", s).attr("srcset", ""), c()
									})), i.on("click", ".reset_variations", (function() {
										o.attr("src", s).attr("srcset", ""), n(), c()
									})), jQuery(".product-lightbox .quantity").addQty()
								})), t.preventDefault()
							}
						})), jQuery(e).addClass("quick-view-added"));
					}));

					$('.pwf-new-product-added').find('.wishlist-button').each(function (index, element) {
						'use strict'

						jQuery(element).on('click', function (e) {
							// Browse wishlist
							if (jQuery(this).parent().find('.yith-wcwl-wishlistexistsbrowse, .yith-wcwl-wishlistaddedbrowse').length) {
							var link = jQuery(this).parent().find('.yith-wcwl-wishlistexistsbrowse a, .yith-wcwl-wishlistaddedbrowse a').attr('href')
							window.location.href = link
							return
							}
							jQuery(this).addClass('loading')
							// Delete or add item (only one of both is present).
							jQuery(this).parent().find('.delete_item').click()
							jQuery(this).parent().find('.add_to_wishlist').click()

							e.preventDefault()
						});
					});
				});
			})(jQuery);
		</script>
		<?php
	}
}
