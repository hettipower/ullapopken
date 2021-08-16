<?php
defined( 'ABSPATH' ) || exit;

add_action( 'pwf_before_shop_loop', 'pwf_pinkmart_theme_pwf_before_shop_loop' );
add_action( 'wp_footer', 'pwf_pinkmart_theme_css_code', 500 );
add_action( 'wp_footer', 'pwf_pinkmart_theme_add_js_code', 1000 );

if ( ! function_exists( 'pwf_pinkmart_theme_pwf_before_shop_loop' ) ) {

	function pwf_pinkmart_theme_pwf_before_shop_loop() {
		global $yith_woocompare;
		$_REQUEST['context'] = 'frontend';
		$yith_woocompare     = new YITH_Woocompare();
	}
}

if ( ! function_exists( 'pwf_pinkmart_theme_css_code' ) ) {
	function pwf_pinkmart_theme_css_code() {
		?>
		<style>
			.woocommerce .shop-filter .special-filter.pwf-sort {
				opacity: 1;
				visibility: visible;
				height: 60px;
				max-height: 1000px;
				position: relative;
				width: fit-content;
				width: -moz-max-content;
				-webkit-transition: opacity .3s ease .15s;
				-moz-transition: opacity .3s ease .15s;
				-o-transition: opacity .3s ease .15s;
				transition: opacity .3s ease .15s;
			}
			.product_per_page_filter .pwf-num {
				font-family: Poppins,sans-serif;
				font-weight: 500;
				line-height: 15px;
				color: #a9a9a9;
				padding: 0 5px;
				cursor: pointer;
				font-size: 14px;
				max-height: 150px;
				-webkit-transition: color .3s ease;
				-moz-transition: color .3s ease;
				-ms-transition: color .3s ease;
				-o-transition: color .3s ease;
				transition: color .3s ease;
			}
			.product_per_page_filter .pwf-num.selected, .product_per_page_filter .pwf-num:hover {
				color: #000;
			}
		</style>
		<?php
	}
}

if ( ! function_exists( 'pwf_pinkmart_theme_add_js_code' ) ) {

	function pwf_pinkmart_theme_add_js_code() {
		?>
		<script type="text/javascript">
		(function( $ ) {
			let windowWidth      = $(window).width();
			var pwfisTouchDevice = (Modernizr.touchevents) ? true : false;
			var pwfisMobile      = (pwfisTouchDevice && windowWidth <= 767) ? true : false;
			var pwfisTablet      = (pwfisTouchDevice && windowWidth >= 768 && windowWidth <= 1140) ? true : false;

			var pinkMart = {
				showAnimation: function ($container, $carousel) {
					var self = this,
						counter = 0,
						$carouselItem,
						$duplicateSlider;

					var showAnimationBase = function (that) {
						$(that).each(function () {
							var $delay;
							setTimeout(function () {
								if ($container.hasClass('fadeinfrombottom') || $container.hasClass('fadeinfromtop') || $container.hasClass('fadeinfromright') || $container.hasClass('fadeinfromleft')) {
									$container.find('.productwrap , .swiper-slide,.product_category_container').addClass('isEaseInAnimated');
									$delay = 0.3 * counter;
									$(that).css({ 'transition-delay': $delay + 's' });

								} else if ($container.hasClass('zoomin')) {
									$container.find('.productwrap , .swiper-slide,.product_category_container').addClass('isZoomInAnimated');
									$(that).addClass('isanimated');
									$delay = 0.2 * counter;
									$(that).css({ 'transition-delay': $delay + 's' });
								} else {
									$container.find('.productwrap , .swiper-slide,.product_category_container').addClass('fadein');
									$(that).addClass('isanimated');
									$delay = 0.2 * counter;
									$(that).css({ 'transition-delay': $delay + 's' });
								}
								counter = counter + 1;
							}, 50);
							counter = 0;
						});
					}
					//Set animation for duplicate slides seperatly to do not consider in animation delays
					var setAnimationBaseForDuplicatSlides = function ($that) {
						$that.css({ 'transition-delay': '0s' });
						$that.addClass('isanimated');

						if ($container.hasClass('fadeinfrombottom') || $container.hasClass('fadeinfromtop') || $container.hasClass('fadeinfromright') || $container.hasClass('fadeinfromleft')) {
							$container.find('.productwrap , .swiper-slide,.product_category_container').addClass('isEaseInAnimated'); // Find .productWrap For Shop widge , Use Swiper-slide for image carousel
						} else if ($container.hasClass('zoomin')) {
							$container.find('.productwrap , .swiper-slide,.product_category_container').addClass('isZoomInAnimated');
						} else {
							$container.find('.productwrap , .swiper-slide,.product_category_container').addClass('fadein');
						}
					}

					if ((pwfisMobile || pwfisTablet) && $container.hasClass('no-responsive-animation')) {
						return true;
					}
					if (!$container.hasClass('main-shop-loop')) {
						$container = $container.closest('.woocommerce.wc-shortcode');
					}
					if (!$container.hasClass('default')) { // default = No animation for Product Grid
						$container.find('.productwrap:not(.isanimated),.product_category_container:not(.isanimated)').waypoint({
							handler: function () {
								var $item = $(this.element);
								showAnimationBase($item);
								this.destroy();
							},
							offset: '95%'
						});
					}
				},
				buttonsappearunderHover: function () {
					if ($('div.products.buttonsappearunder').parents('.woocommerce.carousel.wc-shortcode, .woocommerce .related.grid').length) {
						var $heightOfCarousel = $('div.products.buttonsappearunder').parents('.woocommerce.carousel.wc-shortcode ').height();
						var $heightOfGrid = $('div.products.buttonsappearunder').parents('.woocommerce .related.grid ').height();
						$('div.products.buttonsappearunder').parents('.woocommerce.carousel.wc-shortcode').css('height', $heightOfCarousel);
						$('div.products.buttonsappearunder').css('height', $heightOfCarousel);
						$('div.products.buttonsappearunder').parents('.woocommerce .related.grid').css('height', $heightOfGrid + 20 + 'px');
					}
				},
				products_infoOnclick: function ($infinite_scroll = false) {
					var self = this;
					if ($('div.infoonclick').length <= 0)
						return;

					if (!$infinite_scroll) {
						var $container = $('div.infoonclick div');
					} else {
						var $container = $infinite_scroll;
					}
					// off the previous click events
					$container.find('span.show-hover').off('click');
					$container.find('a.product_variation_item').off('click');

					$container.find('span.show-hover').on('click', function () {
						var $product_Id = $(this).parents('div.product:not(.parent_div_product)').attr('data-productid'); //data product ID
						$product_Id = $('div.product:not(.parent_div_product)[data-productid =' + $product_Id + ']');
						$(this).parents('div.products').find($product_Id).find('span.show-hover').toggleClass('show').closest('div.product').toggleClass('show-hover-content');
					});
					$container.find('a.product_variation_item').on('click', function () {
						$container.find('.selectlabel,.colorlabel').removeClass('selected');
						$(this).closest('.selectlabel,.colorlabel').addClass('selected');
					});
				},
				woocommerce_variation_item_select: function () {
					var self = $(this);
					var $currency_symbol = $('.woocommerce-Price-currencySymbol:first').text();
					var simpleAddToCart = function ($productItem) {
						var $cartButton = $productItem.find('div.addtocartbutton .addcartbutton,.product-buttons .product-button a,.mobileAddToCart');
						if (!$productItem.find('.simpleAddToCart').length) {
							$cartButton.after('<a href="" rel="nofollow" data-quantity="1" class="add_to_cart_button product_type_simple simpleAddToCart" style="display:none"><span class="icon icon-cart"></span><span class="txt" data-hover="'+kite_theme_vars.add_to_cart+'">'+kite_theme_vars.add_to_cart+'</span></a>');
							$('.simpleAddToCart').on('click', function () {
								$(this).closest('div.product:not(.parent_div_product)').addClass('cartButtonClicked');
							});
						}
					};
					$('.product_variation_item.info').on('click', function () {
						var $this = $(this),
							$origImage = $(this).closest('div.product:not(.parent_div_product) ').find('div.imageswrap img'),
							$hoverImage = $(this).closest('div.product:not(.parent_div_product) ').find('div.hover-image');
						simpleAddToCart($this.closest('div.product:not(.parent_div_product)'));

						$this.closest('div.product:not(.parent_div_product)').find('.add_to_cart_btn_wrap').addClass('is-loading');

						var $salePrice = $this.attr('data-sale-price');
						var $regPrice = $this.attr('data-regular-price');
						var $data_image = $this.attr('data-image');
						var $data_srcset = $this.attr('data-srcset');

						$('<img/>').attr('src', $data_image).on('load', function () {
							$(this).remove(); // prevent memory leaks as @benweet suggested
							$hoverImage.css('background', 'url(' + $data_image + ')');
							$origImage.attr('src', $data_image);
							$origImage.attr('srcset', $data_srcset);
							$this.closest('div.product').find('.add_to_cart_btn_wrap').removeClass('is-loading');
						});
						if ($this.closest('div.product').find('.simpleprice').length == 0) {
							$this.closest('div.product').find('.price').after('<span class="price simpleprice" style="display:none;"></span>');
						}
						if ($regPrice != '' && $salePrice != '') {
							$this.closest('div.product:not(.parent_div_product) ').find('.simpleprice').html('<span class="woocs_price_code"><del><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + $currency_symbol + '</span>' + $regPrice + '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + $currency_symbol + '</span>' + $salePrice + '</span></ins></span>');
						} else if ($regPrice != '') {
							$this.closest('div.product:not(.parent_div_product) ').find('.simpleprice').html('<span class="woocs_price_code"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + $currency_symbol + '</span>' + $regPrice + '</span></span>');
						} else if ($salePrice != '') {
							$this.closest('div.product:not(.parent_div_product) ').find('.simpleprice').html('<span class="woocs_price_code"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + $currency_symbol + '</span>' + $salePrice + '</span></span>');
						}
						$this.closest('div.product:not(.parent_div_product)').find('.price:not(.simpleprice)').attr('style', 'display: none !important');
						$this.closest('div.product:not(.parent_div_product)').find('.simpleprice').removeAttr('style');
						$this.closest('div.product:not(.parent_div_product)').find('.simpleAddToCart').removeAttr('style');
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.addcartbutton:not(.simpleAddToCart), .product-button > a:not(.simpleAddToCart)').css('display', 'none');

						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').removeData('product_id');
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').removeData('product_sku');
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').data('product_id', $(this).data('product_id'));
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').attr('data-product_id', $(this).data('product_id'));
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').data('product_sku', $(this).data('product_sku'));
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').attr('data-product_sku', $(this).data('product_sku'));
						$this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.simpleAddToCart, .product-button > a.simpleAddToCart').attr('href', $(this).data('cart-url'));
						if ($this.closest('div.product:not(.parent_div_product)').find('div.addtocartbutton a.addcartbutton, .variations').hasClass('ajax_enabled')) {
							$this.closest('div.product:not(.parent_div_product)').find('.simpleAddToCart').addClass('ajax_add_to_cart');
						}
					});
					$('.variable_item,.variations .nice-select li').on('click', function () {
						$('form.variations_form').addClass('variation_clicked');

					});
				},
				kite_scrollbar: function (element) {
					var $element = $(element);

					if (!$element)
						return;

					$element.each(function () {
						var $this = $(this),
							$scrollContainer = $this.find('.swiper-container');

						if ($scrollContainer.length > 0) {
							if ($scrollContainer[0].swiper != undefined) {
								return;
							}
						}

						$this.wrapInner('<div class="swiper-container sw-scrollbar"><div class="swiper-wrapper"><div class="swiper-slide"></div></div><div class="swiper-scrollbar"></div></div>');
						$scrollContainer = $this.find('.swiper-container');

						var swiper = new Swiper($scrollContainer, {
							scrollbar: {
								el: '.swiper-scrollbar',
								draggable: true,
							},
							direction: 'vertical',
							slidesPerView: 'auto',
							mousewheelControl: true,
							freeMode: true,
							touchReleaseOnEdges: true,
							mousewheelReleaseOnEdges: true,
							mousewheelSensitivity: .6,
						});
					})
				},
				compare: function () {
					var self   = this;
					self.$window = $(window);

					var $compare_modal = self.$document.find('#kt-modal'),
						$compare_content = $compare_modal.find('#modal-content'),
						$compare_wrapper = $compare_modal.find('.modal-content-wrapper');

					$('.compare').on('click', function () {
						$compare_modal.addClass('compare-modal open').removeClass('closed'); // content is ready, so show it
					});
					$(document).on('click', '.comparewrapper a.compareLink, .kt-compare .hd-btn-link', function (ev) {
						ev.preventDefault();
						$compare_modal.addClass('compare-modal open').removeClass('closed'); // content is ready, so show it
						var table_url = this.href;

						if (typeof table_url == 'undefined')
							return;
						$('body').trigger('yith_woocompare_open_popup', { response: table_url, button: $(this) });
					});

					// Close quickview by click outside of content
					$compare_modal.on('click', function (e) {
						if (!$compare_modal.hasClass('compare-modal'))
							return;

						if (!$compare_wrapper.is(e.target) && $compare_wrapper.has(e.target).length === 0) {
							closeCompareModal();
						}
					});

					self.$document.on('click', '#kt-modal.compare-modal #modal-close', function (e) {
						e.preventDefault();
						closeCompareModal();
					});

					var closeCompareModal = function () {
						$compare_modal.removeClass('shown loading open').addClass('closed');
						setTimeout(function () {
							self.$body.removeClass('modal-open');
							$compare_modal.removeClass('compare-modal');
						}, 300)

						setTimeout(function () {
							$compare_content.html('');
						}, 800);

						var widget_list = $('.yith-woocompare-widget div.products-list'),
							data = {
								action: yith_woocompare.actionview,
								context: 'frontend'
							};

						if (typeof $.fn.block != 'undefined') {
							widget_list.block({ message: null, overlayCSS: { background: '#fff url(' + yith_woocompare.loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });
						}

						$.ajax({
							type: 'post',
							url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionview),
							data: data,
							success: function (response) {
								// add the product in the widget
								if (typeof $.fn.block != 'undefined') {
									widget_list.unblock().html(response);
								}
								widget_list.html(response);
							}
						});
					}

					// open popup & Run yith_woocompare_open_popup handler 
					self.$body.off('yith_woocompare_open_popup');
					self.$body.on('yith_woocompare_open_popup', function (e, data) {
						var url = data.response;

						self.$body.addClass('modal-open'); // Disable scrollbar

						$.post({
							url: url + ' .compare-list',
							cache: false,
							headers: { 'cache-control': 'no-cache' },
							success: function (response) {
								$compare_content.html(response);
								$compare_modal.addClass('shown');
								pinkMart.kite_scrollbar('table.compare-list tr.description td p:first-child')
							}
						});
					});

					self.$document.find('kt-modal.compare-modal tr.remove a').off('click');

					// remove from table
					self.$document.on('click', '#kt-modal.compare-modal tr.remove a', function (e) {
						e.preventDefault();

						$(this).addClass('norotate');

						var button = $(this),
							data = {
								action: yith_woocompare.actionremove,
								id: button.data('product_id'),
								context: 'frontend'
							},
							product_cell = $('td.product_' + data.id + ', th.product_' + data.id);

						// add ajax loader
						if (typeof $.fn.block != 'undefined') {
							button.block({
								message: null,
								overlayCSS: {
									background: '#fff url(' + yith_woocompare.loader + ') no-repeat center',
									backgroundSize: '16px 16px',
									opacity: 0.6
								}
							});
						}

						$.ajax({
							type: 'post',
							url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionremove),
							data: data,
							dataType: 'html',
							success: function (response) {
								// in compare table
								var table = $(response).filter('table.compare-list');
								$('body  table.compare-list').replaceWith(table);
								$('.compare[data-product_id="' + button.data('product_id') + '"]', window.parent.document).removeClass('added').html(yith_woocompare.button_text);
								// removed trigger
								self.$window.trigger('yith_woocompare_product_removed');
							}
						});
					});
				},
				product_quickview: function () {
					var self = this;
					self.$document = $(document);
					self.$body = $('body');

					var $quickview_modal = self.$document.find('#kt-modal'),
						$quickview_wrapper = $quickview_modal.find('.modal-content-wrapper'),
						$quickview_content = $quickview_modal.find('#modal-content'),
						$quickview_next = $quickview_modal.find('a[rel="next"]'),
						$quickview_prev = $quickview_modal.find('a[rel="prev"]'),
						$items = $('div.products div.product');

					if ($quickview_modal.length <= 0 || ! $('.quick-view-button').length )
						return;

					$('.quick-view-button').on('click', function (e) {
						e.preventDefault();

						var $this = $(this),
							$product_id = $this.data('product_id');

						$this.closest('div.product:not(.parent_div_product)').addClass('qv-active');

						//put a delay to load images after css transitions
						setTimeout(function () {

							// next and Prev Buttons - in Quick view 
							var $next_item = $items.filter('.qv-active').next('div.product:not(.parent_div_product)'),
								$prev_item = $items.filter('.qv-active').prev('div.product:not(.parent_div_product)');

							if ($next_item.length <= 0) {
								$next_item = $items.eq(0);
							}

							if ($prev_item.length <= 0) {
								$prev_item = $items.eq($items.length - 1);
							}

							if ($this.closest('.products').find('div.product').length <= 1) {
								$quickview_modal.addClass('hidden-nav');
							}
							else {
								if (self.windowWidth > 767) {
									$quickview_next.find('img').remove();
									$quickview_prev.find('img').remove();

									var $next_img = $next_item.find('img').eq(0).clone(),
										$prev_img = $prev_item.find('img').eq(0).clone();
									$next_img.insertAfter($quickview_next.find('span'));
									$prev_img.insertAfter($quickview_prev.find('span'));
								}
							}

						}, 400);

						self.$body.addClass('modal-open'); // disable scrollbar
						$quickview_modal.addClass('quickview-modal');

						if (!$quickview_modal.removeClass('closed').hasClass('open')) {
							$quickview_modal.removeClass('loading').addClass('open');
						}

						var ajaxurl,
							data = {
								product_id: $product_id
							};
						// Use new WooCommerce endpoint URL if available
						if (typeof wc_add_to_cart_params !== 'undefined') {
							ajaxurl = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'load_quick_view'); // WooCommerce Ajax endpoint URL (available since 2.4)
						} else {
							ajaxurl = kite_theme_vars.ajax_url;
							data['action'] = 'load_quick_view';
						}

						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: data,
							dataType: 'html',
							cache: false,
							headers: { 'cache-control': 'no-cache' },
							success: function (data) {
								$quickview_content.html(data);
								$quickview_modal.addClass('shown'); // content is ready, so show it
								setTimeout(function () {
									self.product_thumbnails($quickview_content, true); // enable gallery slider of product
								}, 200);
								self.initSelectElements();
								self.countdown();
								self.kt_product_progressbar();
								self.product_variation();
								self.woocommerce_variation_attributes(); // enable variation attributes
								self.woocommerce_variation_attributes_selection();
								self.woocommerce_variation_attributes_trigger(); // update image attributes
								self.woocommerce_variation_item_select(); // update image attributes
								self.reInitVariation($quickview_content); // Variation Form
								self.addToCart(); // add to cart - open side bar add to cart
							}
						});
						});


						// Close quickview by click outside of content
						$quickview_modal.on('click', function (e) {
						if (!$quickview_wrapper.is(e.target) && $quickview_wrapper.has(e.target).length === 0 && !$quickview_next.is(e.target) && $quickview_next.has(e.target).length === 0 && !$quickview_prev.is(e.target) && $quickview_prev.has(e.target).length === 0) {
							self.close_quick_view();
						}
						});

						// Close quickview by click close button
						self.$document.on('click', '#kt-modal.quickview-modal #modal-close', function (e) {
						e.preventDefault();
						self.close_quick_view();
						});

						// Close box with esc key
						self.$document.keyup(function (e) {
						if (e.keyCode === 27)
							self.close_quick_view();
						});

						$quickview_next.on('click', function (e) {
						e.preventDefault();
						var $next_item = $items.filter('.qv-active').next('div.product:not(.parent_div_product)');
						if ($next_item.length <= 0) {
							$next_item = $items.eq(0);
						}

						$quickview_modal.removeClass('shown');
						$items.filter('.qv-active').removeClass('qv-active');
						$next_item.find('a.quick-view-button').trigger('click');
						});

						$quickview_prev.on('click', function (e) {
						e.preventDefault();
						var $prev_item = $items.filter('.qv-active').prev('div.product:not(.parent_div_product)');
						if ($prev_item.length <= 0) {
							$prev_item = $items.eq($items.length - 1);
						}

						$quickview_modal.removeClass('shown');
						$items.filter('.qv-active').removeClass('qv-active')
						$prev_item.find('a.quick-view-button').trigger('click');
						});
				},
				close_quick_view: function () {
					var self = this;

					var $quickview_modal = self.$document.find('#kt-modal.quickview-modal'),
						$quickview_content = $quickview_modal.find('#modal-content');

					$quickview_modal.removeClass('shown loading open').addClass('closed');
					$('div.product.qv-active:not(.parent_div_product)').removeClass('qv-active');

					setTimeout(function () {
						self.$body.removeClass('modal-open');
						$quickview_modal.removeClass('quickview-modal');
					}, 300)

					setTimeout(function () {
						$quickview_content.html('');
					}, 800);
				},
				mobile_hover_state: function () {
					if ( $(window).width() >= 768 )
						return;

					if ( $('body').hasClass('responsive-hover-state-off') ) {
						$('div.products.infoonclick .product, div.products.infoonhover .product').on( 'click', function(e){
							e.preventDefault();
							var url = $(this).find( 'a > h3, a > h2').parent('a').attr('href');
							if ( url != '' ) {
								window.location.href = url;
							}
						});
						return;
					}

					if (navigator.platform.match(/(iPhone|iPod|iPad)/i))
						var $eventListener = 'click mouseover';
					else
						var $eventListener = 'click';

					$('.woocommerce div.products div.product * ').on($eventListener, function (event) {
						var $self = $(this);
						if (!$self.parents('div.product:not(.parent_div_product)').hasClass('hover-state')) {
							event.preventDefault();
							$('.woocommerce div.products div.product').each(function (index, el) {
								if ($(this).hasClass('hover-state'))
									$(this).removeClass('hover-state');
							});
							$self.parents('div.product:not(.parent_div_product)').addClass('hover-state');
						}
					});

					$(document).on('click touchstart', function (event) {
						if ($(event.target).closest('.hover-state').length == 0) {
							$('.hover-state').removeClass('hover-state');
						}
					});

					// fix 2column show bug in you may also like carousel
					if ($('.related.carousel div.products').hasClass('column_res'))
						$('.related.carousel div.products').removeClass('column_res');
				},
				lazyLoadOnLoad: function (that) {
					var $lazyLoadCntainers = $(that).find('.lazy-load-on-load');
					if ($lazyLoadCntainers.length > 0) {
						$lazyLoadCntainers.each(function () {
							var $this = $(this);

							if (!$this.hasClass('lazy-loaded') && !$this.hasClass('is-loading')) {
								$this.addClass('is-loading');
								var $img, src;

								if ($this.hasClass('bg-lazy-load')) {
									src = $this.data('src');

								}
								else {
									$img = $this.find('img');
									src = $img.data('src');
								}

								var img = new Image();

								img.onload = function () {
									if ($this.hasClass('bg-lazy-load'))
										$this.css('background-image', 'url(' + src + ')').removeAttr('data-src');
									else
										$img.attr('src', src).removeAttr('data-src');

									setTimeout(function () {
										$this.addClass('lazy-loaded');
									}, 100);
								}

								img.src = src;
							}
						})
					}
				},
				lazyLoadOnHover: function ( $scope = '') {
					var self = this;

					if (pwfisTouchDevice) 
						return;
					if ( $scope == '' ) {
						var $items = $('.lazy-load-hover-container');
					} else {
						var $items = $scope.find('.lazy-load-hover-container');
					}

					$items.on('mouseenter', function () {
						var $this = $(this).find('.lazy-load-hover');
						if ($this.length > 0) {
							if (!$this.hasClass('lazy-loaded') && !$this.hasClass('is-loading')) {
								$this.addClass('is-loading');
								$this.closest('.lazy-load-hover-container').addClass('is-loading');

								var $img, src;

								if ($this.hasClass('bg-lazy-load')) {
									src = $this.data('src');
								}
								else {
									$img = $this.find('img');
									src = $img.data('src');
								}

								var img = new Image();

								img.onload = function () {
									if ($this.hasClass('bg-lazy-load'))
										$this.css('background', 'url(' + src + ')').removeAttr('data-src', '');
									else
										$img.attr('src', src).removeAttr('data-src', '');

									$this.closest('.lazy-load-hover-container').removeClass('is-loading');

									setTimeout(function () {
										$this.addClass('lazy-loaded');
									}, 100);
								}

								img.src = src;
							}
						}
					})
				},
				runIsotopeInProducts: function ($container) {
					$container.css('width', '');
					let layout = 'fitRows';
					if ($container.is('.main-shop-loop')) {
						layout = $container.data('layoutmode');
					} else {
						layout = $container.parent().data('layoutmode');
					}
					if (layout != 'fitRows') {
						layout = 'masonry';
					}
					if ($container.parents('.woocommerce.wc-shortcode.list').length == 0) {
						$container.isotope({
							itemSelector: '.product',
							layoutMode: layout,
						}, pinkMart.showAnimation($container));
					} else {
						pinkMart.showAnimation($container);
					}
				},
			};

			$(document).find('.special-filter.sort').removeClass('sort').addClass('pwf-sort');
			$(document).on('click', '.special-filter.pwf-sort li a', function( event ) {
				event.preventDefault();
				let link  = $(this).attr('href');
					let orderBy = link.substring(link.indexOf("=") + 1);;
					$( document.body ).trigger('pwfTriggerSorting', [{orderby:orderBy}]);
					let text = orderBy.charAt(0).toUpperCase() + orderBy.slice(1);
					$(this).closest('.widget_product_sorting').find('.current').text( text );
			});

			$(document).find('.product_per_page_filter .num').removeClass('num').addClass('pwf-num');

			$(document).on('click', '.product_per_page_filter .pwf-num', function( event ) {
				if ( $(this).hasClass('selected') ) {
					return;
				}
				let perPage = $(this).data('num');
				$(this).closest('.product_per_page_filter').find('selected').removeClass('selected');
				$(this).addClass('selected');
				$( document.body ).trigger('pwfTriggerPostPerPage', perPage );
			});

			jQuery(document).on( "pwf_filter_js_ajax_done", function() {
				let pwfFilterSetting  = pwffilterVariables.filter_setting;
				let productsContainer = pwfFilterSetting.products_container_selector;

				pinkMart.lazyLoadOnLoad($(productsContainer).find('.pwf-new-product-added'));
				pinkMart.lazyLoadOnHover();
				$(productsContainer).isotope( 'destroy' );
				pinkMart.runIsotopeInProducts($(productsContainer));
				pinkMart.buttonsappearunderHover();
				pinkMart.products_infoOnclick();
				pinkMart.woocommerce_variation_item_select();
				pinkMart.product_quickview();
				pinkMart.compare();
				//pinkMart.mobile_hover_state();
				// self.woocommerce_buttons_on_hover_cart_click();
			});
		})(jQuery);
		</script>
		<?php
	}
}
