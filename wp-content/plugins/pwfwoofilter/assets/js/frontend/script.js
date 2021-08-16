var pwfIsResponsiveView = false;
(function( $ ) {
	"use strict";
	/**
	 * Start define a Global Variables
	 */
	var pwfActiveFilterItems   = {}; // activeFilters
	var pwfFilterAttributes    = {}; // orderby, page number, per_page
	var pwfCurrentUrlQuery     = ''; // hash
	var pwfCurrentPageURL      = ''; // Remove last slach;
	var pwCurrentfFilterItems  = {};
	var pwfTranslatedText      = '';
	var pwfCurrencySymbol      = '';
	var pwfCurrencyPosition    = 'left';

	var pwfOldActiveFilterItems         = false;
	var dateFormatUsingToSend           = 'YYYY-MM-DD';
	var dateFormatDisplayedInInputField = 'MMM DD,YYYY';
	var pwfResetButtonClicked           = false;
	var pwfIsShortcodeWoo               = false;
	var pwfIsURLHasSlash                = true;
	var pwfGetProductsOnly              = false; // if get page only we don't need filter HTML again
	var pwfPaginationType               = 'numbers';

	/**
	 * this varibales used inside pwfWooFilter.init()
	 */
	var pwfFilterData;
	var pwfFilterSetting;
	var pwfFilterDone;
	var pwfFilterID;

	/**
	 * End of Global Variables
	 */

	var pwfWooFilter = {
		init: function() {
			if ( typeof pwffilterVariables !== 'undefined' ) {
				pwfFilterData       = pwfFilterJSItems;
				pwfFilterSetting    = pwffilterVariables.filter_setting;
				pwfFilterDone       = pwffilterVariables.filter_done;
				pwfFilterID         = pwffilterVariables.filter_id;
				pwfTranslatedText   = pwf_woocommerce_filter.translated_text;
				pwfCurrencySymbol   = pwf_woocommerce_filter.currency_symbol;
				pwfCurrencyPosition = pwf_woocommerce_filter.currency_pos;

				if ( pwfFilterSetting.hasOwnProperty('pagination_type') && '' !== pwfFilterSetting.pagination_type ) {
					pwfPaginationType = pwfFilterSetting.pagination_type;
				}
			} else {
				return false; //there is no filter post defined for this page
			}

			$('.pwf-note-list').addClass('empty-active-items');

			pwfWooFilter.setCurrentFilterItems(pwfFilterData);
			if ( $.isEmptyObject( pwCurrentfFilterItems ) ) {
				return false; // There is no filter items
			}

			$( document.body ).trigger( 'pwf_filter_js_init_start' );

			if ( pwfMobileView.isMobileView() ) {
				if( ! $('.pwf-woo-filter').hasClass('pwf-hidden') ) {
					$('.pwf-woo-filter').addClass('pwf-hidden');
				}
				pwfMobileView.displayFilterAsSidebarSlide();
				$('.pwf-woo-filter').removeClass('pwf-hidden');
			}

			// do this code to make it fast
			pwfFilterEvent.targetDoMoreButton();

			pwfWooFilter.setURLhasSlash();
			pwfWooFilter.isShortcodeWoo();
			pwfWooFilter.setCSSClassForActiveFilterItems();
			pwfWooFilter.setCurrentPageURL();
			/**
			 * check if this filter excute in wordpress main Database query
			 */
			if ( 'yes' === pwfFilterDone ) {
				/**
				 * check if there are filter items excute in main Database query
				 */
				if ( pwffilterVariables.hasOwnProperty('selected_items') && ! $.isEmptyObject( pwffilterVariables.selected_items ) ) {
					pwfWooFilter.updateFilterActiveItemsVariableInJS();
					pwfWooFilter.UpdateChangeQueryString();
					pwfWooFilter.setOldActiveFilterItems('true');
				}
			} else {
				pwfWooFilter.checkIfPageURLHasActiveFilterItems();
			}
			
			pwfFilterEvent.initEvent();
			pwfFilterEvent.dateField();
			pwfFilterEvent.wooCatalogSorting();
			pwfFilterEvent.wooCatalogTriggerSorting();
			pwfFilterEvent.noUiSlider();
			pwfPagination.init();
			pwfFilterEvent.setApplyResetButtonStatus();
			pwfFilterEvent.disableApplyButton();
			

			if ( pwfIsResponsiveView ) {
				pwfMobileView.doChanges();
				pwfMobileView.setPlaceForActiveFilterItems();
			}

			$( document.body ).trigger( 'pwf_filter_js_init_end' );
		},
		/**
		 * check if is woocommerce come from woo shortcode
		 */
		isShortcodeWoo: function() {
			let isWooShortCode = false;

			if ( 'yes' === pwfFilterDone ) {
				isWooShortCode = false;
			} else if ( pwfFilterSetting.hasOwnProperty('is_shortcode') && 'on' === pwfFilterSetting.is_shortcode ) {
				isWooShortCode = true;
		    } else {
				let paginationSelector = pwfFilterSetting.pagination_selector;
				if ( $(paginationSelector).find('a').length ) {
					let link = $(paginationSelector).find('a').first().attr('href');
					if ( undefined !== link ) {
						let pageNum    = link.match(/\?product-page=\d+/);
						if ( null !== pageNum ) {
							isWooShortCode = true;
						}
					}
				}

				let queryString = window.location.search;
				if ( '' !== queryString ) {
					let urlParams  = new URLSearchParams(queryString);
					if ( urlParams.has('product-page') ) {
						isWooShortCode = true;
					}
				}
			}

			pwfIsShortcodeWoo = isWooShortCode;
		},
		/**
		 * Change the position for active filter items
		 */
		setCSSClassForActiveFilterItems: function() {
			let activeFiltersSelector = pwfFilterSetting.active_filters_selector;
			if ( '' !== activeFiltersSelector && $(activeFiltersSelector).length > 0 ) {
				let filterNote      = $('.pwf-woo-filter-notes');
				$('.pwf-woo-filter-notes').remove();
				$(activeFiltersSelector).each( function( index, current ) {
					if ( index === 0 ) {
						$(current).append(filterNote);
						return;
					}
				});
			}
		},
		setCurrentFilterItems: function( filterItemsData ) {
			/**
			 * assign varible to hold all filter items
			 * without columns, button
			 */
			for ( let key in filterItemsData ) {
				let filter = filterItemsData[key];
				if ( 'column' === filter['item_type'] ) {
					pwfWooFilter.setCurrentFilterItems( filterItemsData[key]['children'] );
				} else if ( 'button' !== filter['item_type'] ) {
					pwCurrentfFilterItems[key] = filter;
				}
			}
		},
		/**
		 * Check if Website end with slash or no
		 * Some site doesn't end with slash example .website/shop/ be .website.com/shop
		 */
		setURLhasSlash: function() {
			if ( typeof pwfSetURLHasSlash !== 'undefined' ) {
				// Check if user set pwfIsURLHasSlash inside theme
				pwfIsURLHasSlash = pwfSetURLHasSlash;
			} else {
				let pathName      = window.location.pathname;
				let countPathName = pathName.length;
				if ( countPathName <= 1 ) {
					let paginationSelector = pwfFilterSetting.pagination_selector;
					if ( $( paginationSelector ).find('a').length ) {
						let link = $(paginationSelector).find('a').first().attr('href');
						if ( undefined !== link ) {
							if ( ! link.endsWith('/') ) {
								pwfIsURLHasSlash = false;
							}
						}
					}
				} else {
					// check end of pathname
					if ( ! pathName.endsWith('/') ) {
						pwfIsURLHasSlash = false;
					}
				}
			}
		},
		setCurrentPageURL: function() {
			let regForPage = ( false === pwfIsURLHasSlash ) ? new RegExp("page/\\d+$") : new RegExp("page/\\d+/$");
			let regForNum  = new RegExp("\\d+");
			let pathName   = window.location.pathname;
			let PageURL    = '';
			let pageNum    = '';
			let urlHasPage = pathName.match(regForPage); // check url has /page/num/

			if ( null !== urlHasPage ) {
				/**
				 * Let this info
				 * inside this code you can keep /page/num/ if orgianl in the url 
				 * if there are active filter remove /page/num/ else this is orginal part from url and keetp it
				 */
				PageURL         = pathName.split(regForPage)[0]; // remove /page/num/
				pageNum = parseInt( urlHasPage[0].match(regForNum)[0] );
				if ( pageNum ) {
					pwfFilterAttributes['page'] = pageNum;
				}			
			} else {
				PageURL = pathName;
			}

			pwfCurrentPageURL = window.location.protocol + "//" + window.location.host + PageURL;
		},
		updateFilterActiveItemsVariableInJS: function() {
			let allFilters        = [];
			let selectedItems     =  pwffilterVariables.selected_items;
			let currentHtmlFilter = $('.filter-id-'+ pwfFilterID );
			for ( let key in selectedItems ) {
				let intValues = selectedItems[key];
				let values    = [];
				let labels    = [];
				let slugs     = [];
				intValues.forEach( function( value ) {
					values.push( value.toString() );
				});
				let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( key );
				
				if ( 'priceslider' === filterItem['item_type'] ) {
					labels.push( $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('.pwf-field-item-title').find('.text-title').text() );
				} else if ( 'rangeslider' === filterItem['item_type'] ) {
					labels.push( filterItem['title'] );
				} else if ( 'checkboxlist' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						let item = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('[value="' + value + '"]');
						slugs.push( $(item).attr('data-slug') );
						labels.push( $(item).closest('.pwf-checkbox-label').find('.pwf-title-container').first().find('.text-title').text() );
					});
				} else if ( 'radiolist' === filterItem['item_type'] ) {
					let item = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('[value="' + values[0] + '"]').closest('.pwf-item-label');
					labels = [ $(item).find('.pwf-title-container').find('.text-title').text() ];
					slugs  = [ $(item).find('.pwf-input-container').find('input').attr('data-slug') ];
				} else if ( 'dropdownlist' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						let item  = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('select').find('option[value="'+ value + '"]');
						labels.push( $(item).attr('data-title') );
						slugs.push( $(item).attr('data-slug') );
					});
				} else if ( 'boxlist' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						let item  = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('[data-item-value="' + value + '"]');
						labels.push( $(item).find('.text-title').text() );
						slugs.push( $(item).attr('data-slug') );
					});
				} else if ( 'colorlist' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						let item = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('[data-item-value="' + value + '"]');
						labels.push( $(item).attr('data-item-title') );
						slugs.push( $(item).attr('data-slug') );
					});
				} else if ( 'textlist' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						let item  = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('[data-item-value="' + value + '"]');
						labels.push( $(item).find('.text-title').first().text() );
						slugs.push( $(item).attr('data-slug') );
					});
				} else if ( 'date' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						labels.push( value );
						slugs.push( value );
					});
				} else if ( 'search' === filterItem['item_type'] ) {
					labels = values;
					slugs  = values;
				} else if ( 'rating' === filterItem['item_type'] ) {
					values.forEach( function( value ) {
						let item = $(currentHtmlFilter).find('[data-item-key="' + key + '"]').find('[data-item-value="' + value + '"]');
						labels.push( $(item).attr('data-item-value') );
						slugs.push( $(item).attr('data-slug') );
					});
				}
				let term = {
					'item':   filterItem,
					'values': values,
					'labels': labels,
					'slugs':  slugs,
				};
				allFilters.push( term );
			}
			if ( ! $.isEmptyObject( allFilters ) ) {
				pwfWooFilter.doingActiveFilterItemsForPageHasQueryString( allFilters );	
				pwfFilterActions.updateNotices();
			}
		},
		checkIfPageURLHasActiveFilterItems: function() {
			let activeFilterItems = pwfWooFilter.getPageURLQueryStrign();
			let pageNumber        = '';
			if ( pwfFilterAttributes.hasOwnProperty('page') ) {
				pageNumber = pwfFilterAttributes['page'];
			}
			if ( false !== activeFilterItems ) {
				pwfWooFilter.doingActiveFilterItemsForPageHasQueryString( activeFilterItems );
			}

			/**
			 * if page has hash ?=product-page=/d/
			 * You don't need to get products because woocommerce shortcode doing it
			 */
			if ( false !== activeFilterItems || pwfFilterAttributes.hasOwnProperty('orderby') ) {
				if ( false !== activeFilterItems ) {
					pwfWooFilter.setOldActiveFilterItems('true');
				}
				// used to hook if page exists in URL for first time and keep page 2 in page url
				// ex http://woodemo.wordpress.test/shop/page/2/?product-category=clothing
				/*if ( pwfFilterAttributes.hasOwnProperty('page') && 1 < pwfFilterAttributes['page'] ) {
					pwfFilterAttributes['page'] = pageNum;
				}*/
				// code for shortcode
				if ( '' !== pageNumber ) {
					pwfFilterAttributes['page'] = pageNumber;
				}
				pwfAjaxQuery.getProducts('getproducts');
			}
		},
		getPageURLQueryStrign: function() {
			/**
			 * allFilters
			 * hold all active filters contain objects
			 * filterType, currentFilter, values, labels
			 */
			let allFilters = [];
			let queryString = window.location.search;

			if ( '' === queryString ) {
				return false;
			}

			let urlParams  = new URLSearchParams(queryString);
			let currentHtmlFilter = $('.filter-id-'+ pwfFilterID );
			for ( let key in pwCurrentfFilterItems ) {
				let filter       = pwCurrentfFilterItems[key];
				let labels       = [];
				let slugs        = [];
				let values       = [];

				if ( urlParams.has( filter['url_key'] ) || 'priceslider' === filter['item_type'] || 'date' === filter['item_type'] || 'rangeslider' === filter['item_type']) {
					if ( 'priceslider' === filter['item_type'] ) {
						if ( 'two' === filter['price_url_format'] ) {						
							if ( urlParams.has( filter['url_key_min_price'] ) && urlParams.has( filter['url_key_max_price'] ) ) {
								let currentMin = parseInt(urlParams.get( filter['url_key_min_price'] ), 10);
								let currentMax = parseInt(urlParams.get( filter['url_key_max_price'] ), 10);
								
								if ( NaN !== currentMin && NaN !== currentMax ) {
									slugs = values = [ currentMin, currentMax ];
									labels.push( $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('.pwf-field-item-title').find('.text-title').text() );
								}
								// if there are values or no delete url_key
								urlParams.delete( filter['url_key_min_price'] );
								urlParams.delete( filter['url_key_max_price'] );
							}
						} else {
							if ( urlParams.has( filter['url_key'] ) ) {
								let slugsData = urlParams.getAll( filter['url_key'] ).toString().split('-');
								let currentMin = parseInt(slugsData[0], 10);
								let currentMax = parseInt(slugsData[1], 10);
								if ( NaN  !== currentMin && NaN !== currentMax ) {
									slugs = values = [ currentMin, currentMax ];
									labels.push( $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('.pwf-field-item-title').find('.text-title').text() );
								}
								urlParams.delete( filter['url_key'] );
							}
						}
					} else if ( 'rangeslider' === filter['item_type'] ) {
						if ( 'two' === filter['range_slider_url_format'] ) {						
							if ( urlParams.has( filter['url_key_range_slider_min'] ) && urlParams.has( filter['url_key_range_slider_max'] ) ) {
								let currentMin = parseInt(urlParams.get( filter['url_key_range_slider_min'] ), 10);
								let currentMax = parseInt(urlParams.get( filter['url_key_range_slider_max'] ), 10);
								
								if ( NaN  !== currentMin && NaN !== currentMax ) {
									slugs = values = [ currentMin, currentMax ];
									labels.push( $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('.pwf-field-item-title').find('.text-title').text() );
								}
								// if there are values or no delete url_key
								urlParams.delete( filter['url_key_range_slider_min'] );
								urlParams.delete( filter['url_key_range_slider_max'] );
							}
						} else {
							if ( urlParams.has( filter['url_key'] ) ) {
								let slugsData = urlParams.getAll( filter['url_key'] ).toString().split('-');
								let currentMin = parseInt(slugsData[0], 10);
								let currentMax = parseInt(slugsData[1], 10);
								if ( NaN  !== currentMin && NaN !== currentMax ) {
									slugs = values = [ currentMin, currentMax ];
									labels.push( $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('.pwf-field-item-title').find('.text-title').text() );
								}
								urlParams.delete( filter['url_key'] );
							}
						}
					} else if ( 'checkboxlist' === filter['item_type'] && '' !== urlParams.get( filter['url_key'] ) ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value ) {
							slugs.push( value );
						});
						slugs.forEach( function( slug ) {
							let item = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('input[data-slug="' + slug + '"]');
							labels.push( $(item).closest('.pwf-checkbox-label').find('.pwf-title-container').first().find('.text-title').text() );
							values.push( $(item).val() );
						});
					} else if ( 'radiolist' === filter['item_type'] && '' !== urlParams.get( filter['url_key'] ) ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value, index ) {
							if ( 0 === index ) {
								slugs.push( value );
							}
						});
						slugs.forEach( function( slug ) {
							let item = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('input[data-slug="' + slug + '"]');
							labels.push( $(item).closest('.pwf-item-label').find('.pwf-title-container').find('.text-title').text() );
							values.push( $(item).val() );						
						});					
					} else if ( 'dropdownlist' === filter['item_type'] && '' !== urlParams.get('url_key') ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value, index ) {
							if ( 0 === index ) {
								slugs.push( value );
							}
						});
						slugs.forEach( function( slug ) {
							let item  = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('select').find('option[data-slug="' + slug + '"]');
							labels.push( $(item).attr('data-title') );
							values.push($(item).val());						
						});
					} else if ( 'boxlist' === filter['item_type'] && '' !== urlParams.get( filter['url_key'] ) ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value ) {
							slugs.push( value );
						});

						slugs.forEach( function( slug ) {
							let item = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('[data-slug="' + slug + '"]');
							labels.push( $(item).find('.text-title').text() );
							values.push( $(item).attr('data-item-value') );
						});
					} else if ( 'colorlist' === filter['item_type'] && '' !== urlParams.get( filter['url_key'] ) ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value ) {
							slugs.push( value );
						});
						slugs.forEach( function( slug ) {
							let item = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('[data-slug="' + slug + '"]');
							labels.push( $(item).attr('data-item-title') );
							values.push( $(item).attr('data-item-value') );						
						});
					} else if ( 'textlist' === filter['item_type'] && '' !== urlParams.get('url_key') ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value ) {
							slugs.push( value );
						});

						slugs.forEach( function( slug ) {
							let item = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('[data-slug="' + slug + '"]');
							labels.push( $(item).find('.text-title').first().text() );
							values.push( $(item).attr('data-item-value') );						
						});
					} else if ( 'date' === filter['item_type'] ) {
						if ( urlParams.has( filter['url_key_date_before'] ) && urlParams.has( filter['url_key_date_after'] ) ) {
							let dateBefore = urlParams.get( filter['url_key_date_before'] );
							let dateAfter  = urlParams.get( filter['url_key_date_after'] );
							if ( '' !== dateBefore && '' !== dateAfter ) {
								let slugs = [ dateAfter, dateBefore ];
								slugs.forEach( function( slug ) {
									labels.push(slug);
									values.push(slug);	
								});
							}
							urlParams.delete( filter['url_key_date_before'] );
        					urlParams.delete( filter['url_key_date_after'] );
						}
					} else if ( 'search' === filter['item_type'] ) {
						let searchText = urlParams.get( filter['url_key'] );
						if ( '' !== searchText ) {
							slugs = labels = values = [ searchText ];
						}
					} else if ( 'rating' === filter['item_type'] ) {
						let slugsData = urlParams.getAll( filter['url_key'] ).toString().split(',');
						slugsData.forEach( function( value ) {
							slugs.push( value );
						});
						slugs.forEach( function( slug ) {
							let item = $(currentHtmlFilter).find('[data-item-key="' + filter['url_key'] + '"]').find('[data-slug="' + slug + '"]');
							labels.push( $(item).attr('data-item-value') );
							values.push( $(item).attr('data-item-value') );					
						});
					}

					if ( 'priceslider' !== filter['item_type'] && 'date' !== filter['item_type'] && 'rangeslider' !== filter['item_type']) {
						urlParams.delete( filter['url_key'] );
					}

					if ( values.length > 0 && labels.length > 0 ) {
						let term = {
							'item':   filter,
							'values': values,
							'labels': labels,
							'slugs':  slugs,
						};
						allFilters.push( term );
					}
				}
			}

			if ( urlParams.has('orderby') && '' !== urlParams.get('orderby') ) {
				pwfFilterAttributes['orderby'] = urlParams.get('orderby');
			}

			if ( urlParams.has('product-page') && parseInt( urlParams.get('product-page') ) > 1 ) {
				pwfFilterAttributes['page'] = parseInt( urlParams.get('product-page') );
				urlParams.delete('product-page');
			}
			
			if ( allFilters.length > 0  ) {
				return allFilters;
			} else {
				return false;
			}
		},
		doingActiveFilterItemsForPageHasQueryString: function( filterItems ) {
			if ( false === filterItems ) {
				return;
			}
			filterItems.forEach( function( filter, index ) {
				let currentFilter = filter.item;
				let values        = filter.values;
				let labels        = filter.labels;
				let slugs         = filter.slugs;
	
				if ( 'priceslider' === currentFilter['item_type'] || 'rangeslider' === currentFilter['item_type'] ) {
					pwfFilterActions.processingFilterItem( currentFilter, values, labels, slugs );
				} else if ( 'date' === currentFilter['item_type'] ) {
					labels[0] = moment( labels[0], dateFormatUsingToSend ).format(dateFormatDisplayedInInputField);
					labels[1] = moment( labels[1], dateFormatUsingToSend ).format(dateFormatDisplayedInInputField);
					labels    = labels[0] + ' / ' + labels[1];
					pwfFilterActions.processingFilterItem( currentFilter, values, labels, slugs );
				} else {
					values.forEach( function( value, indexed ) {
						if ( '' !== labels[indexed] && undefined !== labels[indexed] ) {
							pwfFilterActions.processingFilterItem( currentFilter, value, labels[indexed], slugs[indexed] );
						}
					});
				}
			});
		},
		UpdateChangeQueryString: function() {
			// if disable ajax paination the ',' display as '%2C'
			let queryString = window.location.search;
			if ( queryString.includes('%2C') ) {
				let newQuery = queryString.split('%2C').join(',');
				let newurl   =  pwfCurrentPageURL + newQuery;
				window.history.replaceState( { path:newurl }, '', newurl );
			}
		},
		setOldActiveFilterItems: function( str ) {
			// This used when click reset button
			pwfOldActiveFilterItems = ('true' === str ) ? true : false;
		},
		isFilterStartAuto: function() {
			let filtering_starts = pwfFilterSetting.filtering_starts;
			if ( pwfIsResponsiveView ) {
				filtering_starts = pwfFilterSetting.responsive_filtering_starts;
			}

			return ( 'auto' === filtering_starts );
		},
		getCurrencyTemplate: function() {
			let template = '<span class="pwf-currency-symbol">' + pwfCurrencySymbol + '</span>';
			return template;
		}
	};

	var pwfCustomization = {
		isDefine: function() {
			if ( typeof pwfWooFilterCustomization !== 'undefined') {
				if ( pwfWooFilterCustomization.hasOwnProperty('filterID') ) {
					if ( Array.isArray( pwfWooFilterCustomization.filterID ) ) {
						return pwfWooFilterCustomization.filterID.includes( pwfFilterID );
					} else if ( pwfFilterID === pwfWooFilterCustomization.filterID ) {
						return true;
					} else {
						return false;
					}
				}
				return true;
			}
			return false;
		},
		getPageLoader: function() {
			let htmlLoader = '';
			if ( '' !== pwf_woocommerce_filter.customize.pageLoader ) {
				htmlLoader = HtmlEntities.decode( pwf_woocommerce_filter.customize.pageLoader );
			}
			return htmlLoader;
		},
		getButtonLoader: function() {
			let htmlLoader = '';
			if ( '' !== pwf_woocommerce_filter.customize.buttonLoader ) {
				htmlLoader = HtmlEntities.decode( pwf_woocommerce_filter.customize.buttonLoader );
			}
			return htmlLoader;
		},
		getInfiniteLoader: function() {
			let htmlLoader = '';
			if ( '' !== pwf_woocommerce_filter.customize.infiniteLoader ) {
				htmlLoader = HtmlEntities.decode( pwf_woocommerce_filter.customize.infiniteLoader );
			}
			return htmlLoader;
		},
		getInfiniteDistance: function() {
			let distance = 0;
			if ( pwfCustomization.isDefine() && pwfWooFilterCustomization.hasOwnProperty('infiniteDisatance') && '' !== pwfWooFilterCustomization.infiniteDisatance ) {
				distance = pwfWooFilterCustomization.infiniteDisatance;
			}			
			return distance;
		},
		enablePaginationHash: function() {
			// this option avialable only when pagination type load more or infinite scroll
			let enable = true;
			if ( 'numbers' !== pwfPagination.getType() ) {
				if ( pwfCustomization.isDefine() && pwfWooFilterCustomization.hasOwnProperty('enablePaginationHash') && true === pwfWooFilterCustomization.enablePaginationHash ) {
					enable = true;
				} else {
					enable = false;
				}
			}
			return enable;
		},
		responsivePaginationType: function() {
			let paginationType = '';
			if ( pwfCustomization.isDefine() && pwfWooFilterCustomization.hasOwnProperty('responsivePagination') && '' !== pwfWooFilterCustomization.responsivePagination ) {
				let responsive = pwfWooFilterCustomization.responsivePagination;
				if ( responsive.hasOwnProperty('type') && '' !== responsive.type && responsive.hasOwnProperty('maxScreenWidth') && '' !== responsive.maxScreenWidth ) {
					if ( window.matchMedia( '(max-width: '+ parseInt( responsive.maxScreenWidth ) +'px)' ).matches ) {
						paginationType = responsive.type;
					}
				}				
			}
			return paginationType;
		},
		filterButtonSpeed: function() {
			let speed = 400;
			if ( pwfCustomization.isDefine() && pwfWooFilterCustomization.hasOwnProperty('filterButtonSpeed') && '' !== pwfWooFilterCustomization.filterButtonSpeed ) {
				let checkSpeed = parseInt( pwfWooFilterCustomization.filterButtonSpeed );
				if ( checkSpeed !== NaN  ) {
					speed = checkSpeed;
				}
			}
			return speed;
		},
	};
	
	var pwfPagination = {
		getType: function() {
			if ( '' !== pwfCustomization.responsivePaginationType() ) {
				pwfPaginationType = pwfCustomization.responsivePaginationType();
			}
			return pwfPaginationType; // can be numbers, infinite, load more button
		},
		init: function() {
			let usecomponents      = pwfFilterSetting.usecomponents;
			let paginationSelector = pwfFilterSetting.pagination_selector;

			if ( usecomponents.includes('pagination') && '' !== paginationSelector && $(paginationSelector).length && 'on' === pwfFilterSetting.pagination_ajax ) {
				if ( 'numbers' === pwfPagination.getType() ) {
					pwfPagination.addAjaxToPagination();
				} else {
					let button = pwfPagination.getHTMLLoadMoreButton();
	
					$( paginationSelector ).empty().append( button );

					pwfPagination.addEventToLoadMoreButton();

					if ( 'infinite_scroll' === pwfPagination.getType() ) {
						pwfPagination.addEventScrollInfinite();
					}
				}
			}
		},
		getHTMLLoadMoreButton: function() {
			let css      = 'pwf-load-more-button';
			let disabled = '';
			let nextPage = pwfPagination.getNextPage();

			if ( '' === nextPage ) {
				css     += ' pwf-disabled-btn';
				disabled = ' disabled="disabled"';
			}

			let paginationType = ( 'infinite_scroll' === pwfPagination.getType() ) ? 'infinite-scroll' : 'load-more';

			let html = '<div class="pwf-wrap-load-more pwf-pagination-type-' + paginationType + '">';
			html    += ( 'infinite_scroll' === pwfPagination.getType() ) ? pwfPagination.infiniteLoader() : '';
			html    += '<button id="pwf-load-more-button-' + pwfFilterID + '" class="' + css + '"' + disabled + ' data-next-page-num="' + nextPage + '">';
			html    += '<span class="button-text">' + pwfTranslatedText.load_more + '</span>';
			html    += pwfPagination.buttonLoader();
			html    += '</button>';
			html    +='</div>';

			return html;
		},
		getNextPage: function() {
			let currentPage = 1;
			let nextPage    = '';
			if ( pwfFilterAttributes.hasOwnProperty('page') && 1 < pwfFilterAttributes.page ) {
				currentPage = pwfFilterAttributes['page'];
				nextPage    = currentPage + 1;
			} else {
				let paginationSelector = pwfFilterSetting.pagination_selector;
				let pages = $(paginationSelector).find('a');
				$(pages).each( function() {
					if ( pwfIsShortcodeWoo ) {
						let link       = $(this).attr('href');
						let pageNum    = link.match(/\?product-page=\d+/);
						if ( null !== pageNum ) {
							pageNum = parseInt( pageNum[0].match( new RegExp("\\d+") )[0] );
							if ( pageNum > currentPage ) {
								nextPage = currentPage + 1 ;
								return false;
							}
						}
					} else {
						let link       = $(this).attr('href').split('?')[0];
						let pageNum    = link.match(/\/\d+\/$/);
						if ( false === pwfIsURLHasSlash ) {
							pageNum    = link.match(/\d+$/);
						}
						if ( null !== pageNum ) {
							pageNum = pageNum[0];
							pageNum = parseInt( pageNum.match(/(\d+)/)[0] );
							if ( pageNum > currentPage ) {
								nextPage = currentPage + 1 ;
								return false;
							}
						}
					}
				});
			}
			
			return nextPage;
		},
		buttonLoader: function() {
			let loaderCustomize = pwfCustomization.getButtonLoader();
			let loader          = ( '' !== loaderCustomize ) ? loaderCustomize : '<span class="pwf-loader"></span>';
			loader              = '<span class="pwf-button-loader">' + loader + '</span>';
			return loader;
		},
		infiniteLoader: function() {
			let loader          = '<span class="pwf-infinite-loader"><span class="pwf-bounce pwf-b1"></span><span class="pwf-bounce pwf-b2"></span><span class="pwf-bounce pwf-b3"></span></span>';
			let loaderCustomize = pwfCustomization.getInfiniteLoader();
			if ( '' !== loaderCustomize ) {
				loader = loaderCustomize;
			}
			loader = '<div class="pwf-button-loader">' + loader + '</div>';
			return loader;
		},
		addAjaxToPagination: function() {
			let paginationSelector = pwfFilterSetting.pagination_selector;
			// ajax pagination
			$('body').on('click', paginationSelector + ' a', function( event ) {
				event.preventDefault();

				if ( pwfIsShortcodeWoo ) {
					let link       = $(this).attr('href');
					let pageNum    = link.match(/\?product-page=\d+/);
					if ( null !== pageNum ) {
						pageNum = parseInt( pageNum[0].match( new RegExp("\\d+") )[0] );
						pwfGetProductsOnly          = true;
						pwfFilterAttributes['page'] = pageNum;
						pwfAjaxQuery.getProducts('getproducts');
					}
				} else {
					let link       = $(this).attr('href').split('?')[0];
					let pageNum    = link.match(/\/\d+\/$/);
					if ( false === pwfIsURLHasSlash ) {
						pageNum    = link.match(/\d+$/);
					}
					if ( null !== pageNum ) {
						pageNum = pageNum[0];
						pageNum = parseInt( pageNum.match(/(\d+)/)[0] );
						pwfGetProductsOnly          = true;
						pwfFilterAttributes['page'] = pageNum;
						pwfAjaxQuery.getProducts('getproducts');
					}
				}
			});
		},
		addEventToLoadMoreButton: function() {
			$('body').on('click', '.pwf-load-more-button', function( event ) {
				event.preventDefault();
				if ( ! $(this).hasClass('pwf-products-loading') ) {
					let nextPage = parseInt( $(this).attr('data-next-page-num') );
					if ( nextPage > 1 ) {
						pwfGetProductsOnly          = true;
						pwfFilterAttributes['page'] = nextPage;
						pwfAjaxQuery.getProducts('getproducts', 'getpagenumber' );
					}
				}
			});
		},
		addEventScrollInfinite: function() {
			$(window).on('scroll', function() {
				let element  = $('.pwf-load-more-button');
				let distance = pwfCustomization.getInfiniteDistance();
				if ( pwfMobileView.isOnViewport( element, distance ) ) {
					$(element).trigger('click');
				}
			});
		},
		disableLoadMoreButton: function() {
			$('.pwf-load-more-button').attr('data-next-page-num', '' );
			$('.pwf-load-more-button').addClass('pwf-disabled-btn').attr("disabled", true);
			$('.pwf-load-more-button').closest('.pwf-wrap-load-more').addClass('pwf-no-products');
		},
		enableLoadMoreButton: function( nextPage = '' ) {
			if ( '' !== nextPage ) {
				$('.pwf-load-more-button').attr('data-next-page-num', parseInt( nextPage ) );
				$('.pwf-load-more-button').removeClass('pwf-disabled-btn').prop( 'disabled', false );
				$('.pwf-load-more-button').closest('.pwf-wrap-load-more').removeClass('pwf-no-products');
			}
		},
		addLoadingToLoadMoreButton: function() {
			$('.pwf-load-more-button').addClass('pwf-products-loading');
		},
		removeLoadingToLoadMoreButton: function() {
			$('.pwf-load-more-button').removeClass('pwf-products-loading');
		},
	};

	var pwfMobileView = {
		isMobileView: function() {
			let responsive = pwfFilterSetting.responsive;
			if ( 'on' !== responsive ) {
				return false;
			}
			let responsiveWidth = parseInt(pwfFilterSetting.responsive_width);
			if ( '' === responsiveWidth ) {
				responsiveWidth = 768;
			}
			if ( window.matchMedia( '(max-width: '+ responsiveWidth +'px)' ).matches ) {
				pwfIsResponsiveView = true;
				return true;
			} else {
				pwfIsResponsiveView = false;
				return false;
			}
		},
		onResize: function() {
			// check mobile view
		},
		displayFilterAsSidebarSlide: function() {
			let css              = '';
			let orderByForm      = false;
			let orderByHtmlForm  = ''
			let orderbySelector  = pwfFilterSetting.sorting_selector;
			let AppendStickTo = 'body';

			pwfMobileView.filterDisplayAsButton(); // iF filter display as button

			if ( '' !== pwfFilterSetting.responsive_append_sticky && 'body' !== pwfFilterSetting.responsive_append_sticky ) {
				css          += ' pwf-sticky-inside-div';
				AppendStickTo = pwfFilterSetting.responsive_append_sticky;
			}
			
			if ( '' !== orderbySelector && $(orderbySelector).length ) {
				orderByForm     = true;
				let orderValue  = $( orderbySelector ).find('select').find('option:selected').text();
				orderByHtmlForm = '<span class="pwf-sticky-button pwf-form-sort"><span class="pwf-sorting"><span class="pwf-sorting-text">' + orderValue + '</span></span></span>';
			} else {
				css += ' pwf-sticky-has-filter-button-only';
			}

			let stickyFilter = '<div class="pwf-sticky-filter'+ css +'"><div class="pwf-sticky-filter-inner">';
			stickyFilter    += '<span class="pwf-sticky-button"><button id="pwf-sticky-filter-button" class="pwf-sticky-filter-button">';
			stickyFilter    += '<span class="pwf-button-text">'+ pwfTranslatedText.filter +'</span>';
			stickyFilter    += '<span class="pwf-sticky-filter-count"></span></button></span>';
			stickyFilter    += ( orderByForm ) ? orderByHtmlForm : '';
			stickyFilter += '</div></div>';

			$(AppendStickTo).append(stickyFilter);
			if ( orderByForm ) {
				let orderbyHTML = $( orderbySelector ).clone();
				$( orderbyHTML ).appendTo('.pwf-form-sort');
			}

			let canvasStart = '<div class="js-offcanvas pwf-off-canvas" id="pwf-off-canvas" role="complementary"><div class="pwf-canvas-inner-wrap">';
			let canvasInner = '<div class="pwf-off-canvas-content">';
			
			let canvasHeader = '<div class="pwf-off-canvas-header"><div class="pwf-canvas-wrap-title">';
			canvasHeader    += '<span class="header-text">'+ pwfTranslatedText.filter +'</span>';
			canvasHeader    += '<span class="pwf-canvas-close-btn"><span class="pwf-canvas-icon"></span></span>';
			canvasHeader    += '</div></div>';
			
			let canvasFooter = '<div class="pwf-canvas-footer"><div class="pwf-footer-inner">';
			canvasFooter    += '<span class="pwf-field-item-button"><button class="pwf-item pwf-reset-button">';
			canvasFooter    += '<span class="pwf-button-text">'+ pwfTranslatedText.reset +'</span>';
			canvasFooter    += '</button></span>';
			canvasFooter    += '<span class="pwf-field-item-button"><button class="pwf-item pwf-filter-button">';
			canvasFooter    += '<span class="pwf-button-text">'+ pwfTranslatedText.apply +'</span>';
			canvasFooter    += '</button></span>';
			canvasFooter    += '</div></div>';
			
			let canvasInnerEnd = '</div>';
			let canvasEnd      = '</div></div>';
			
			let canvasSidebar = canvasStart +  canvasHeader + canvasInner + canvasInnerEnd + canvasFooter + canvasEnd;

			$('body').append(canvasSidebar);
			let filterHTMLContainer = 'filter-id-' + pwfFilterID; // add filter to canvas
			$( '#' + filterHTMLContainer ).appendTo( '.pwf-off-canvas-content' );
			
			let modifiers = 'left,overlay';
			if ( $('body').hasClass('rtl') ) {
				modifiers = 'right,overlay';
			}
			$('#pwf-off-canvas').offcanvas({
				modifiers: modifiers,
				triggerButton: '.pwf-sticky-filter-button',
				closeButtonClass: 'pwf-canvas-close-btn',
			});
			$(document).trigger("enhance");

			if ( ! $('.pwf-sticky-filter').hasClass('pwf-sticky-show') ) {
				$('.pwf-sticky-filter').addClass("pwf-sticky-show");
			}
		},
		onScroll: function( AppendStickTo ) {
			if ( 'body' === AppendStickTo ) {
				var lastScrollTop = 0;
				var delta = 5;
				$(window).scroll(function() {
					var nowScrollTop = $(this).scrollTop();				
					if( Math.abs( lastScrollTop - nowScrollTop ) >= delta ){
						if ( nowScrollTop > lastScrollTop ) {
							// ACTION ON SCROLLING DOWN
							if ( ! $('.pwf-sticky-filter').hasClass('pwf-sticky-show') ) {
								$('.pwf-sticky-filter').addClass("pwf-sticky-show");
							}
						} else {
							// ACTION ON SCROLLING UP 
							if ( $('.pwf-sticky-filter').hasClass('pwf-sticky-show') ) {
								$('.pwf-sticky-filter').removeClass("pwf-sticky-show");
							}
						}
					lastScrollTop = nowScrollTop;
					}
					
				});
			} else {
				// append to container
				$(window).scroll(function() {
					if ( pwfMobileView.isOnViewport(AppendStickTo) ) {
						$('.pwf-sticky-filter').addClass("pwf-container-in-viewport");
					} else {
						$('.pwf-sticky-filter').removeClass("pwf-container-in-viewport");
					}
				});
			}
		},
		isOnViewport: function ( element, distance = 0 ) {
			var win = $(window);
				
			var viewport = {
				top : win.scrollTop(),
				left : win.scrollLeft()
			};
			viewport.right = viewport.left + win.width();
			viewport.bottom = viewport.top + win.height();
		
			var bounds    = $(element).offset();
			bounds.right  = bounds.left + $(element).outerWidth();
			bounds.bottom = bounds.top + $(element).outerHeight();
			bounds.top    = bounds.top - distance;
		
			return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
		},
		isTopElementIntoView: function( element ) {
			if ( ! $(element).length ) {
				return false;
			}

			let win = $(window);
				
			let viewport = {
				top : win.scrollTop(),
				left : win.scrollLeft()
			};
		
			let bounds    = $(element).offset();
			bounds.right  = bounds.left + $(element).outerWidth();
			bounds.bottom = bounds.top + $(element).outerHeight();
			return (!(viewport.top > bounds.top));
		},
		doChanges: function() {
			pwfMobileView.changeResponsiveSortText();
			pwfMobileView.addActiveFilterCount();
		},
		changeResponsiveSortText: function() {
			if ( $('.pwf-sorting-text').length ) {
				let orderbySelector = pwfFilterSetting.sorting_selector;
				let text = $('.pwf-sticky-filter').find(orderbySelector).find('select option:selected').text();
				$('.pwf-sorting-text').text(text)
			}
		},
		addActiveFilterCount: function() {
			if ( $('.pwf-sticky-filter-count').length ) {
				if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
					$('.pwf-sticky-filter-count').text( pwfMobileView.countActiveFilterItems() );
				} else {
					$('.pwf-sticky-filter-count').text('');
				}
			}
		},
		countActiveFilterItems: function() {
			let count     = 0;

			for ( let key in pwfActiveFilterItems ) {
				let fieldType = pwfActiveFilterItems[key]['fieldType'];
				let values    = pwfActiveFilterItems[key]['values'];

				let excpetFields = [ 'priceslider', 'date', 'rangeslider', 'search' ];
				if ( excpetFields.includes( fieldType ) ) {
					count++;
				} else {
					// radiolist, checkboxlist, boxlist, colorlist, textlist, dropdownlist
					if ( Array.isArray( values ) ) {
						count += values.length;
					} else {
						count++;
					}					
				}
			}

			return count;
		},
		setPlaceForActiveFilterItems: function() {
			let activeFiltersSelector = pwfFilterSetting.active_filters_selector;
			if ( '' !== activeFiltersSelector && $(activeFiltersSelector).length > 0 ) {
				$(activeFiltersSelector).first().prependTo('.pwf-off-canvas .pwf-woo-filter');
			} else {
				$('.pwf-woo-filter-notes').prependTo('.pwf-off-canvas .pwf-woo-filter');
			}			
		},
		filterDisplayAsButton: function() {
			if ( 'button' === pwfFilterSetting.display_filter_as ) {
				if( $('.pwf-filter-as-button-header').hasClass('pwf-btn-opened') ) {
					$('.pwf-filter-as-button-header').removeClass('pwf-btn-opened').addClass('pwf-btn-closed');
				}
				if ( 'hide' === pwfFilterSetting.filter_button_state ) {
					$('.pwf-woo-filter').hide().removeClass('pwf-hidden');
					$('.pwf-woo-filter').show();
				}
				$('.pwf-filter-as-button-title').unbind();
				$('body').on('click', '.pwf-filter-as-button-title', function( event) {
					event.preventDefault();
					$('.pwf-sticky-filter-button').trigger('click');
				});
			}
		}
	};

	var pwfFilterEvent = {
		initEvent: function() {
			pwfFilterEvent.select2();
			pwfFilterEvent.triggerPostPerPage();
			pwfFilterEvent.checkToggle();
			
			/* For canvas filter */
			/*$(window).resize(function(){
				//pwfWooFilter.onResize();
			});*/
			$('.pwf-off-canvas').on('click', '.pwf-reset-button', function( event ) {
				event.preventDefault();
				
				pwfFilterEvent.resetFilter('canvas-resetButton');
			});
			$('.pwf-off-canvas').on('click', '.pwf-filter-button', function( event ) {
				event.preventDefault();
				pwfFilterEvent.submitFilter('canvas-submitButton');
			});
			/* End of Canvas */

			$('.pwf-woo-filter').on('click', '.pwf-reset-button', function( event ) {
				event.preventDefault();
				pwfFilterEvent.resetFilter('resetButton');
			});
	
			$('.pwf-woo-filter').on('click', '.pwf-filter-button', function( event ) {
				event.preventDefault();
				pwfFilterEvent.submitFilter('submitButton');
			});

			// display filter as button
			if ( 'button' === pwfFilterSetting.display_filter_as && ! pwfMobileView.isMobileView() ) {
				if ( 'hide' === pwfFilterSetting.filter_button_state ) {
					$('.pwf-woo-filter').hide().removeClass('pwf-hidden');
				}
				$('body').on('click', '.pwf-filter-as-button-title', function( event ) {
					if( $(this).closest('.pwf-filter-as-button-header').hasClass('pwf-btn-opened') ) {
						$(this).closest('.pwf-filter-as-button-header').removeClass('pwf-btn-opened').addClass('pwf-btn-closed');
					} else {
						$(this).closest('.pwf-filter-as-button-header').removeClass('pwf-btn-closed').addClass('pwf-btn-opened');
					}
					$('.pwf-woo-filter').slideToggle( pwfCustomization.filterButtonSpeed() );
				});
			}
	
			// pwf-remove-filter
			$('body').on('click', '.pwf-note-item', function( event ) {
				event.preventDefault();

				if ( $(this).hasClass('pwf-clear-all-note') ) {
					pwfFilterEvent.resetFilter();
				} else {
					let note = $( this );
					let name = $( note ).attr('data-item-key');
					let value = '';
					if ( $(note).hasClass('pwf-range-slider-note') || $(note).hasClass('pwf-date-note') || $(note).hasClass('pwf-search-note') ) {
						value = '';
					} else {
						value = $(note).attr('data-item-value');
					}
		
					$(note).slideUp( 'fast', function() {
						$(this).remove();
					});
					
					let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
					if ( ! pwfWooFilter.isFilterStartAuto() ) {
						pwfFilterActions.removeSelectedValueFromHTMLFilter( currentFilter, value );
					}
					pwfFilterActions.processingFilterItem( currentFilter, value, '', '' );
					pwfAjaxQuery.getProducts();
				}
			});
	
			$('.pwf-woo-filter').on('click', '.pwf-toggle', function( event ) {
				event.preventDefault();
				if ( $(this).hasClass('pwf-toggle-widget-title') ) {
					let fieldItem = $(this).closest('.pwf-field-item');
					if ( fieldItem.hasClass('pwf-collapsed-close') ) {
						fieldItem.removeClass('pwf-collapsed-close').addClass('pwf-collapsed-open');
					} else if ( fieldItem.hasClass('pwf-collapsed-open') ) {
						fieldItem.removeClass('pwf-collapsed-open').addClass('pwf-collapsed-close');
					}
					$(fieldItem).find('.pwf-field-item-container').slideToggle();
				} else {
					let fieldItem = $(this).closest('.pwf-item');
					if ( fieldItem.hasClass('pwf-collapsed-close') ) {
						fieldItem.removeClass('pwf-collapsed-close').addClass('pwf-collapsed-open');
					} else if ( fieldItem.hasClass('pwf-collapsed-open') ) {
						fieldItem.removeClass('pwf-collapsed-open').addClass('pwf-collapsed-close');
					}
					$(fieldItem).find('.pwf-children:first').slideToggle();
				}
			});
			
			/*
			 * Prevent events mouseenter, mouseleave
			 * from working on touch screen
			 */
			let deviceHasTouch = ("ontouchstart" in document.documentElement);
			if( ! deviceHasTouch ) {
				// Checkbox list
				$(document).on('mouseenter', '.pwf-checkbox-click-area', function( event ) {
					if ( ! $(this).closest('.pwf-item-label').hasClass('pwf-ui-state-hover') ) {
						$(this).closest('.pwf-item-label').addClass('pwf-ui-state-hover')
					}
				});
				$(document).on('mouseleave', '.pwf-checkbox-click-area', function( event ) {
					if( $(this).closest('.pwf-item-label').hasClass('pwf-ui-state-hover') ) {
						$(this).closest('.pwf-item-label').removeClass('pwf-ui-state-hover')
					}
				});

				// Radio list
				$(document).on('mouseenter', '.pwf-radiolist-label .pwf-input-container, .pwf-radiolist-label .pwf-title-container', function( event ) {
					if( ! $(this).closest('.pwf-item-label').hasClass('pwf-ui-state-hover') ) {
						$(this).closest('.pwf-item-label').addClass('pwf-ui-state-hover')
					}
				});
				$(document).on('mouseleave', '.pwf-radiolist-label .pwf-input-container, .pwf-radiolist-label .pwf-title-container', function( event ) {
					if( $(this).closest('.pwf-item-label').hasClass('pwf-ui-state-hover') ) {
						$(this).closest('.pwf-item-label').removeClass('pwf-ui-state-hover')
					}
				});

				// star rate
				$(document).on('mouseenter', '.pwf-star-rating-item', function( event ) {
					if ( ! $(this).hasClass('pwf-ui-state-hover') ) {
						$(this).addClass('pwf-ui-state-hover')
					}
				});
				$(document).on('mouseleave', '.pwf-star-rating-item', function( event ) {
					if( $(this).hasClass('pwf-ui-state-hover') ) {
						$(this).removeClass('pwf-ui-state-hover')
					}
				});
			}
			
			$('.pwf-woo-filter').on('click', '.pwf-checkbox-click-area', function( event ) {
				event.preventDefault();
	
				// Don't do any thing if this item is disabled
				if ( $(this).closest('.pwf-checkboxlist-item').hasClass('pwf-disabled') ) {
					return false;
				}
	
				let checkbox = $(this).closest('.pwf-checkbox-label').find('.pwf-input-checkbox');
				let label    = $(checkbox).closest('.pwf-item-label').find('.text-title').text();
				let value    = $(checkbox).val();	
				let name     = $(checkbox).attr('name');
				let slug     = $(checkbox).attr('data-slug');
	
				let currentFilter    = pwfFilterActions.getFilterItemDataByUrlKey( name );
				let checkboxlistItem = $(this).closest('.pwf-checkboxlist-item');
				let isChecked     = false;
				if ( $( checkbox ).prop("checked") == true ) {
					$( checkboxlistItem ).removeClass('checked');
					$( checkbox ).prop('checked', false );
				} else {
					isChecked = true;
					$( checkboxlistItem ).addClass('checked');
					$( checkbox ).prop('checked', true );
				}
	
				pwfFilterActions.processingFilterItem( currentFilter, value, label, slug );
	
				if ( isChecked ) {
					$( checkbox ).prop('checked', true );
					let itemparents = $( this ).parents('.pwf-checkboxlist-item');
					let ulchildren  = $( this ).closest('.pwf-checkbox-label').next('.pwf-children');
					if ( itemparents.length > 0 ) {
						$( itemparents ).each( function ( index, currentitem ) {
							if ( 0 == index ) {
								return;
							}
							
							let checkbox = $( currentitem ).find('.pwf-item-label').first().find('.pwf-input-checkbox');
							if ( $(checkbox).prop("checked") == true ) {
								let labelparent  = $(checkbox).closest('.pwf-item-label').find('.text-title').text();
								let valueparent  = $(checkbox).val();
								let nameparent   = $(checkbox).attr('name');
								let filterParent = pwfFilterActions.getFilterItemDataByUrlKey( nameparent );
	
								$( checkbox ).prop('checked', false );
								$( checkbox ).closest('.pwf-checkboxlist-item').removeClass('checked')
								pwfFilterActions.processingFilterItem( filterParent, valueparent, labelparent );
							}
						});
					}
					
					if ( ulchildren.length > 0 ) {
						let itemchildren = $( ulchildren ).find('.pwf-input-checkbox');
						if ( itemchildren.length > 0 ) {
							$( itemchildren ).each( function ( index, currentitem ) {
								let checkbox = $( currentitem );
								if ( $(checkbox).prop("checked") == true ) {
									let labelchild  = $(checkbox).closest('.pwf-item-label').find('.text-title').text();
									let valuechild  = $(checkbox).val();
									let namechild   = $(checkbox).attr('name');
									let filterChild = pwfFilterActions.getFilterItemDataByUrlKey( namechild );
			
									$(checkbox).prop( 'checked', false );
									$(checkbox).closest('.pwf-checkboxlist-item').removeClass('checked')
									pwfFilterActions.processingFilterItem( filterChild, valuechild, labelchild );
								}
							});
						}
					}
				}
				
				pwfAjaxQuery.getProducts();
			});
	
			$('.pwf-woo-filter').on('click', '.pwf-radiolist-label .pwf-input-container, .pwf-radiolist-label .pwf-title-container', function( event ) {
				event.preventDefault();
	
				if ( $(this).closest('.pwf-radiolist-item').hasClass('pwf-disabled') ) {
					return false;
				}
	
				let radio      = $(this).closest('.pwf-radiolist-label').find('.pwf-input-radio');
				let label      = $(radio).closest('.pwf-item-label').find('.text-title').text();
				let value      = $(radio).attr('value');
				let name       = $(radio).attr('name');
				let slug       = $(radio).attr('data-slug');
				let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
	
				$( this ).closest('.pwf-field-item-radiolist').find('.checked').removeClass('checked');
				if ( $( radio ).prop("checked") == true ) {
					$( radio ).prop('checked', false );
					
				} else {
					$( radio ).prop('checked', true );
					$( this ).closest('.pwf-radiolist-label').addClass('checked');
				}
	
				pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
				pwfAjaxQuery.getProducts();
			});
	
			$('.pwf-woo-filter').on('click', '.pwf-boxlist-item', function( event ) {
				event.preventDefault();
				if ( $(this).hasClass('pwf-disabled') ) {
					return false;
				}

				let label      = $(this).find('.text-title').text();
				let value      = $(this).attr('data-item-value');
				let name       = $(this).closest('.pwf-field-item-boxlist').attr('data-item-key');
				let slug       = $(this).attr('data-slug');
				let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
	
				if ( 'on' !== filterItem['multi_select'] ) {
					let fields = $('[data-item-key="'+ name +'"]');
					$(fields).removeClass('selected');
					$(this).addClass('selected');
				} else {
					$(this).toggleClass('selected');
				}
				pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
				pwfAjaxQuery.getProducts();
			});
	
			$('.pwf-woo-filter').on('click', '.pwf-colorlist-item', function( event ) {
				event.preventDefault();
				if ( $(this).hasClass('pwf-disabled') ) {
					return false;
				}
				let label      = $(this).attr('data-item-title');
				let value      = $(this).attr('data-item-value');
				let slug      = $(this).attr('data-slug');
				let name       = $(this).closest('.pwf-field-item-colorlist').attr('data-item-key');
				let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
	
				if ( 'on' !== filterItem['multi_select'] ) {
					let fields = $('[data-item-key="'+ name +'"]');
					$(fields).removeClass('selected');
					$(this).addClass('selected');
				} else {
					$(this).toggleClass('selected');
				}
				pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
				pwfAjaxQuery.getProducts();
			});
	
			$('.pwf-woo-filter').on('click', '.pwf-textlist-item .pwf-item-label .pwf-title-container', function( event ) {
				event.preventDefault();
				if ( $(this).closest('.pwf-textlist-item').hasClass('pwf-disabled') ) {
					return false;
				}
				let label         = $(this).find('.text-title').text();
				let item          = $(this).closest('.pwf-textlist-item');
				let value         = $(item).attr('data-item-value');
				let slug          = $(item).attr('data-slug');
				let name          = $(item).closest('.pwf-field-item-textlist').attr('data-item-key');
				let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
				
				if ( 'on' !== currentFilter['multi_select'] ) {
					// here you need to remove it from active filter if exist
					let textListItems = $(item).closest('.pwf-field-item-textlist').find('.selected');
					if ( textListItems.length > 0 ) {
						$( textListItems ).each( function ( index, currentitem ) {
							let label         = $(currentitem).find('.text-title').text();
							let value         = $(currentitem).attr('data-item-value');
							let slug          = $(item).attr('data-slug');
							let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
	
							$(currentitem).toggleClass('selected');
							pwfFilterActions.processingFilterItem( currentFilter, value, label, slug );
						});
					}
					$(item).addClass('selected');
				} else {
					$(item).toggleClass('selected');
				}
	
				pwfFilterActions.processingFilterItem( currentFilter, value, label, slug );
				
				if( $(item).closest('.pwf-field-item-textlist').hasClass('pwf-items-hierarchical') && 'on' === currentFilter['multi_select'] ) {
					let itemparents = $(this).parents('.pwf-textlist-item');
					let ulchildren = $(this).closest('.pwf-item-label').next('.pwf-children');
					if ( itemparents.length > 0 ) {
						$( itemparents ).each( function ( index, currentitem ) {
	
							if ( 0 == index ) {
								return;
							}
							
							if ( $(currentitem ).hasClass('selected') ) {
								let label         = $(currentitem).find('.text-title').text();
								let value         = $(currentitem).attr('data-item-value');
								let slug          = $(item).attr('data-slug');
								let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
	
								$(currentitem).toggleClass('selected');
								pwfFilterActions.processingFilterItem( currentFilter, value, label, slug );
							}
						});
					}
					
					if ( ulchildren.length > 0 ) {
						let itemchildren = $( ulchildren ).find('.pwf-textlist-item');
						if ( itemchildren.length > 0 ) {
							$( itemchildren ).each( function ( index, currentitem ) {
								if ( $(currentitem ).hasClass('selected') ) {
									let label         = $(currentitem).find('.text-title').text();
									let value         = $(currentitem).attr('data-item-value');
									let slug          = $(item).attr('data-slug');
									let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
	
									$(currentitem).toggleClass('selected');
									pwfFilterActions.processingFilterItem( currentFilter, value, label, slug );
								}
							});
						}
					}
				}
	
				pwfAjaxQuery.getProducts();
			});
	
			$('.pwf-woo-filter').on('change', '.pwf-dropdownlist-item-default', function( event ) {
				pwfFilterEvent.ProcessingSelect( this );
			});
	
			/**
			 * show more button
			 */
			if ( $('.pwf-woo-filter .pwf-more-button-block').length > 0 ) {
				$('.pwf-woo-filter').on('click', '.pwf-more-button', function( event) {
					event.preventDefault();
					if ( $(this).hasClass('pwf-status-active') ) {
						let field = $(this).closest('.pwf-more-button-block');
						$(field).addClass('pwf-more-button-extended-active').removeClass('pwf-more-button-less-active');
						pwfFilterEvent.doMoreButton(field);
						$(this).removeClass('pwf-status-active').addClass('pwf-status-unactive');
					} else if ( $(this).hasClass('pwf-status-unactive') ) {
						let field = $(this).closest('.pwf-more-button-block').removeClass('pwf-more-button-extended-active');
						$(field).addClass('pwf-more-button-less-active');
						pwfFilterEvent.doMoreButton(field);
						$(this).removeClass('pwf-status-unactive').addClass('pwf-status-active');
					}
				});
			}

			$('.pwf-woo-filter').on('click', '.pwf-click-search-icon', function( event ) {
				processingSearchForm( $(this).closest('.pwf-search-field') );
			});
			$('.pwf-woo-filter').on('keypress', '.pwf-search-from', function( event ) {
				if ( event.which == 13 ) {
					processingSearchForm( $(this).closest('.pwf-search-field') )
				}
			});

			function processingSearchForm( searchField ) {
				let value   = $(searchField).find('.pwf-search-from').val();
				let name    = $(searchField).attr('data-item-key');
				let isEmpty = false;

				// when user click search or Enter button after remove search text
				if ( pwfActiveFilterItems.hasOwnProperty( name ) ) {
					let searchField = pwfActiveFilterItems[name];
					let searchValue = searchField.values;
					if ( '' !== searchValue )  {
						isEmpty = true;
					}
				}

				if ( '' !== value || ( '' === value && isEmpty ) ) {
					let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
					
					pwfFilterActions.processingFilterItem( filterItem, value, value, value );
					pwfAjaxQuery.getProducts();
					$( document.body ).trigger( 'pwf_filter_search_field_change', [ { 'searchField' : searchField } ] );
				}
			}
			
			/**
			 * search field
			 * @since 1.1.3
			 */
			$('.pwf-woo-filter').on('focusin', '.pwf-search-from', function( event ) {
				$(this).data( 'val', $(this).val() );
				$(this).closest('.pwf-search-field').find('.pwf-icon-css').addClass('pwf-search-focus');
			});
			$('.pwf-woo-filter').on('focusout', '.pwf-search-from', function( event ) {
				$(this).closest('.pwf-search-field').find('.pwf-icon-css').removeClass('pwf-search-focus');
			});

			$('.pwf-woo-filter').on('click', '.pwf-star-rating-item', function( event ) {
				event.preventDefault();
				if ( $(this).hasClass('pwf-disabled') ) {
					return false;
				}
				let mainField = $(this).closest('.pwf-field-item-rating');
				if ( $(mainField).hasClass('pwf-rating-radio-type') ) {
					$(mainField).find('.checked').removeClass('checked');
					$(this).addClass('checked');
				} else {
					$(this).addClass('checked');
				}

				let label      = $(this).attr('data-item-value');
				let value      = $(this).attr('data-item-value');
				let name       = $(mainField).attr('data-item-key');
				let slug       = $(this).attr('data-slug');
				let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
				
				pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
				pwfAjaxQuery.getProducts();
			});
		},
		dateField: function() {
			let currentHtmlFilter = $('.filter-id-'+ pwfFilterID );
			let dateFields        = $(currentHtmlFilter).find('.pwf-field-item-date');
			let RTL               = ( $('body').hasClass('rtl') ) ? true : false;

			$(dateFields).each( function( index, dateField ) {
				let minDate = $(dateField).find('.pwf-date-field').attr('data-min-date');
				let maxDate = $(dateField).find('.pwf-date-field').attr('data-max-date');
				let currentFromDate = $(dateField).find('.pwf-date-from').attr('data-date-from');
				if ( typeof currentFromDate !== typeof undefined && currentFromDate !== false ) {
					let dateFrom = moment( currentFromDate, dateFormatUsingToSend );
					$(dateField).find('.pwf-date-from').val(dateFrom.format(dateFormatDisplayedInInputField));
				}

				let currentToDate = $(dateField).find('.pwf-date-to').attr('data-date-to');
				if ( typeof currentToDate !== typeof undefined && currentToDate !== false ) {
					let dateTo = moment( currentToDate, dateFormatUsingToSend );
					$(dateField).find('.pwf-date-to').val(dateTo.format(dateFormatDisplayedInInputField));
				}				

				minDate = moment( minDate, 'YYYY-MM-DD' ).format('MM DD, YYYY');
				maxDate = moment( maxDate, 'YYYY-MM-DD' ).format('MM DD, YYYY');

				let dateFormat = "MM dd, yy",
				from = $(dateField).find('.pwf-date-from')
				.datepicker({
					isRTL: RTL,
					autoSize: true,
					dateFormat: dateFormat,
					minDate: new Date( minDate ),
					maxDate: new Date( maxDate ),
					beforeShow: function(input, inst) {
						$('.ui-datepicker').addClass("pwf-date");
					},
					onClose: function(input, inst) {
						$('.ui-datepicker').removeClass("pwf-date");
					},
				})
				.on( "change", function() {
					to.datepicker( "option", "minDate", getDate( this, dateFormat ) );
					pwfFilterEvent.dateFieldChanged( $(this) );
				}),
				to = $(dateField).find('.pwf-date-to').datepicker({
					isRTL: RTL,
					autoSize: true,
					currentText: "Now",
					dateFormat: dateFormat,
					minDate: new Date( minDate ),
					maxDate: new Date( maxDate ),
					beforeShow: function(input, inst) {
						$('.ui-datepicker').addClass("pwf-date");
					},
					onClose: function(input, inst) {
						$('.ui-datepicker').removeClass("pwf-date");
					},
				})
				.on( "change", function() {
					from.datepicker( "option", "maxDate", getDate( this, dateFormat ) );
					pwfFilterEvent.dateFieldChanged( $(this) );
				});
				
			});
			
			function getDate( element, dateFormat ) {
				let date;
				try {
					date = $.datepicker.parseDate( dateFormat, element.value );
				} catch( error ) {
					date = null;
				}
				return date;
			}
		},
		checkToggle: function() {
			let ActiveFiltersKey = [];
			if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
				ActiveFiltersKey = Object.keys(pwfActiveFilterItems);
			}

			let toogleClose = $('.pwf-field-item'); //find('.pwf-collapsed-close');
			toogleClose.each( function( index, current ) {
				let key = $(current).attr('data-item-key');
				if ( $(current).hasClass('pwf-collapsed-close') ) {
					if ( ActiveFiltersKey.length && ActiveFiltersKey.includes( key ) ) {
						$(current).removeClass('pwf-collapsed-close').addClass('pwf-collapsed-open');
					} else {
						$(current).find('.pwf-field-item-container').hide();
					}
				}
			});

			let itemToogleClose = $('.pwf-item');
			itemToogleClose.each( function( index, current ) {
				if ( $(current).hasClass('pwf-collapsed-close') ) {
					let parentField = $(current).closest('.pwf-field-item');
					let key         = $(parentField).attr('data-item-key');
					if ( ActiveFiltersKey.length && ActiveFiltersKey.includes( key ) ) {
						if ( $(current).hasClass('pwf-checkboxlist-item') || $(current).hasClass('pwf-radiolist-item') || $(current).hasClass('pwf-textlist-item') ) {
							let itemChildrenValues = [];

							if ( $(current).hasClass('pwf-checkboxlist-item') ) {

								let checkboxes = $(current).find('.pwf-checkboxlist-item .pwf-input-checkbox'); 
								if ( checkboxes.length ) {
									checkboxes.each( function( index, item ) {
										itemChildrenValues.push( $(item).val() );
									});
								}
							} else if ( $(current).hasClass('pwf-radiolist-item') ) {
								let radioLists = $(current).find('.pwf-children').find('.pwf-input-radio:checked');
								if ( radioLists.length ) {
									radioLists.each( function( index, item ) {
										itemChildrenValues.push( $(item).val() );
									});
								}
							} else if ( $(current).hasClass('pwf-textlist-item') ) {
								let textlists = $(current).find('.pwf-textlist-item');
								if ( textlists.length ) {
									textlists.each( function( index, item ) {
										itemChildrenValues.push( $(item).attr('data-item-value') );
									});
								}
							}

							let childItemActive     = false;
							let currentFilterValues = pwfActiveFilterItems[key]['values'];

							if ( itemChildrenValues.length ) {
								currentFilterValues.every( function( value, index, currentFilterValues ) {
									if ( itemChildrenValues.includes( value ) ) {
										childItemActive = true;
										return true;
									}
								});
							}

							if ( childItemActive ) {
								$(current).removeClass('pwf-collapsed-close').addClass('pwf-collapsed-open');
							} else {
								$( current ).find('.pwf-item-inner:first').find('.pwf-children:first').hide();
							}
						}						
					} else {
						$( current ).find('.pwf-item-inner:first').find('.pwf-children:first').hide();
					}
				}
			});
		},
		wooCatalogSorting: function() {
			let usecomponents  = pwfFilterSetting.usecomponents;
			let orderbySelector = pwfFilterSetting.sorting_selector;

			if ( usecomponents.includes('sorting') && '' !== orderbySelector ) {
				if ( 'on' != pwfFilterSetting.sorting_ajax ) {
					// Ajax disable
					$( orderbySelector ).on( 'submit', function( event ) {
						if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
							event.preventDefault();
						}
					});

					$( orderbySelector ).on( 'change', 'select.orderby', function( event ) {
						if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
							event.preventDefault();
						}
					});

				} else if ( 'on' == pwfFilterSetting.sorting_ajax ) {
					// Sorting Ajax enabled
					$( orderbySelector ).on( 'submit', function( event ) {
						event.preventDefault();
					});

					$( orderbySelector ).on( 'change', 'select.orderby', function( event) {
						event.preventDefault();
						pwfFilterEvent.wooCatalogDoingSorting( $(this).val() );
					});
				}
			}
		},
		wooCatalogDoingSorting: function( currentValue ) {
			if ( '' === currentValue ) {
				return false;
			}

			if ( $('[data-item-key="orderby"]').length > 0 ) {
				let filter = pwfFilterActions.getFilterItemDataByUrlKey( 'orderby' );
					if ( 'radiolist' === filter['item_type'] ) {
						let inputs = $('[data-item-key="orderby"] [name="orderby"]');
						$('[data-item-key="orderby"] [name="orderby"]').prop( "checked", false );
						for ( let index = 0; index < inputs.length; index++ ){
							let value = $(inputs[index]).attr('value');
							if ( value === currentValue ) {
								$( inputs[index] ).prop( "checked", "true" );
								$( inputs[index] ).closest('.pwf-checkboxlist-item').addClass('checked');
							}
						}
						$('[data-item-key="orderby"] [name="orderby"][value="'+ currentValue +'"]').closest('.pwf-input-container').trigger('click');
						$('[data-item-key="orderby"] [name="orderby"][value="'+ currentValue +'"]').closest('.pwf-input-container').trigger('change');
					} else if ( 'dropdownlist' === filter['item_type'] ) {
						$('[data-item-key="orderby"] [name="orderby"] option[value="'+ currentValue +'"]').prop( "selected", "true" );
						$('[data-item-key="orderby"] [name="orderby"]').trigger('change');
					}	
			}

			if ( 'numbers' !== pwfPagination.getType() ) {
				if ( pwfFilterAttributes.hasOwnProperty('page') && 1 < pwfFilterAttributes.page ) {
					delete pwfFilterAttributes['page']; // fix orderby after load more button working
				}
			}
			pwfFilterAttributes['orderby'] = currentValue;
			pwfAjaxQuery.getProducts( 'getproducts', 'sorting');
		},
		wooCatalogTriggerSorting: function() {
			$('body').on('pwfTriggerSorting', function ( event, json ) {
				if ( '' !== json.orderby ) {
					pwfFilterEvent.wooCatalogDoingSorting( json.orderby );
				}
			});
		},
		triggerPostPerPage: function() {
			$('body').on('pwfTriggerPostPerPage', function ( event, postPerPage ) {
				if ( '' !== postPerPage ) {
					pwfFilterAttributes['per_page'] = parseInt( postPerPage );
					pwfAjaxQuery.getProducts( 'getproducts', 'per_page');
				}
			});
		},
		nouiSiderValidNumber: function ( value, usedFun ) {
			if ( 'parseInt' === usedFun ) {
				return parseInt( value );
			} else {
				return parseFloat( value );
			}
		},
		noUiSlider: function() {
			// noUi-target
			if ( $('.pwf-range-slider').length > 0 ) {
				let direction = ( $('body').hasClass('rtl') ) ? 'rtl' : 'ltr';
				$('.pwf-range-slider').each( function() {
					if( ! $(this).hasClass('noUi-target') ) {
						let currentSlider = $(this);
						let rangeSlider   = this;
						let currentMin    = $(rangeSlider).attr('data-current-min');
						let currentMax    = $(rangeSlider).attr('data-current-max');
						let minPrice      = $(rangeSlider).attr('data-min');
						let maxPrice      = $(rangeSlider).attr('data-max');
						let tooltip       = $(rangeSlider).attr('data-tooltip');
						let step          = $(rangeSlider).attr('data-step');

						let usedFun = 'parseInt';
						step = step.toString();
						if ( step.includes('.') ) {
							usedFun = 'parseFloat';
						}

						if ( 'true' === tooltip ) {
							tooltip = true;
						} else {
							tooltip = false;
						}
						
						if ( step <= 0 ) {
							step = 1;
						} else {
							step = pwfFilterEvent.nouiSiderValidNumber(step, usedFun);
						}

						let rangeSliderArgs = {
							step: step,
							behaviour: 'drag',
							direction: direction,
							start: [ pwfFilterEvent.nouiSiderValidNumber(currentMin, usedFun), pwfFilterEvent.nouiSiderValidNumber(currentMax, usedFun) ],
							connect: true,
							tooltips: tooltip,
							range: {
								'min': parseInt(minPrice),
								'max': parseInt(maxPrice),
							},
							format: {
								to: function (value) {
									return pwfFilterEvent.nouiSiderValidNumber( value, usedFun );
								},
								from: function (value) {
									return pwfFilterEvent.nouiSiderValidNumber( value, usedFun );
								}
							}
						};

						let limit = $(rangeSlider).attr('data-limit');
						if ( typeof limit !== typeof undefined && limit !== false ) {
							rangeSliderArgs['limit'] = parseInt( limit );
						}
						
						noUiSlider.create( rangeSlider, rangeSliderArgs );

						rangeSlider.noUiSlider.on( 'end', function( values, handle, unencoded, tap, positions, noUiSlider ) {
							let currentMin    = $(rangeSlider).attr('data-current-min');
							let currentMax    = $(rangeSlider).attr('data-current-max');
							if ( pwfFilterEvent.nouiSiderValidNumber(currentMin, usedFun) !== values[0] || pwfFilterEvent.nouiSiderValidNumber(currentMax, usedFun) !== values[1] ) {
								let name          = $(rangeSlider).closest('.pwf-field-item').attr('data-item-key');
								let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
								let label         = currentFilter['title'];
								pwfFilterActions.processingFilterItem( currentFilter, values, label );
								pwfAjaxQuery.getProducts();
							}
						});

						// when user input number in min and max input fields
						rangeSlider.noUiSlider.on( 'set', function( values, handle, unencoded, tap, positions, noUiSlider ) {
							let currentMin    = $(rangeSlider).attr('data-current-min');
							let currentMax    = $(rangeSlider).attr('data-current-max');

							if ( pwfFilterEvent.nouiSiderValidNumber(currentMin, usedFun) !== values[0] || pwfFilterEvent.nouiSiderValidNumber(currentMax, usedFun) !== values[1] ) {
								let name          = $(rangeSlider).closest('.pwf-field-item').attr('data-item-key');
								let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( name );
								let label         = currentFilter['title'];
								pwfFilterActions.processingFilterItem( currentFilter, values, label );
								pwfAjaxQuery.getProducts();
							}
						});

						let minPriceInput = document.getElementById( $(currentSlider).closest('.pwf-range-slider-wrap').find('.pwf-min-value').attr('id'));
						let maxPriceInput = document.getElementById( $(currentSlider).closest('.pwf-range-slider-wrap').find('.pwf-max-value').attr('id'));
						let labelMinPrice = document.getElementById( $(currentSlider).closest('.pwf-range-slider-wrap').find('.pwf-from').attr('id'));
						let labelMaxPrice = document.getElementById( $(currentSlider).closest('.pwf-range-slider-wrap').find('.pwf-to').attr('id'));

						rangeSlider.noUiSlider.on('update', function ( values, handle ) {
							if ( 0 === handle ) {
								if ( null !== minPriceInput ) {
									minPriceInput.value = values[handle];
								}
								if ( null !== labelMinPrice ) {
									$(labelMinPrice).text( values[handle] );
								}
							} else if ( 1 === handle ){
								if ( null !== maxPriceInput ) {
									maxPriceInput.value = values[handle];
								}
								if ( null !== labelMaxPrice ) {
									$(labelMaxPrice).text( values[handle] );
								}
							} 
							
						});
						if ( null !== minPriceInput ) {
							minPriceInput.addEventListener('change', function () {
								let min = parseInt( $(this).attr('min') );
								let max = parseInt( $(this).attr('max') );
								if ( this.value < min ) {
									this.value = min;
								} else if ( this.value > max ) {
									this.value = max;
								}
								rangeSlider.noUiSlider.set( [ this.value, null ] );
							});
							maxPriceInput.addEventListener('change', function () {
								let min = parseInt( $(this).attr('min') );
								let max = parseInt( $(this).attr('max') );
								if ( this.value < min ) {
									this.value = min;
								} else if  ( this.value > max ) {
									this.value = max;
								}
								rangeSlider.noUiSlider.set( [ null, this.value ] );
							});
						}
					}
				});
			}
		},
		select2: function() {
			if ( $('.pwf-dropdownlist-item-select2').length > 0 ) {
				let allSelect2 = $('.pwf-dropdownlist-item-select2');
				$(allSelect2).each( function() {
					pwfFilterEvent.addSelect2Event(this);
				});
			}
		},
		addSelect2Event: function( item ) {
			let multiple = false;
			let values      = [];
			let cssClass    = 'pwf-customize-select2';
			let selected    = $(item).find('[selected]');
			let placeHolder = '';

			$(selected).each( function() {
				values.push( $(this).val() );
			});

			if ( $(item).hasClass('pwf-has-multiple') ) {
				multiple    = true;
				placeHolder = 'Select ...';
				
			}

			$(item).select2({ width: '100%', multiple: multiple, dropdownCssClass: cssClass, placeholder: placeHolder });

			if ( values.length && values.length > 1 ) {
				$(item).val( values );
				$(item).trigger('change');
			}

			$(item).on('select2:select', function( e ){
				pwfFilterEvent.ProcessingSelect2( this, e.params.data );
			});
			$(item).on('select2:unselect', function( e ){
				pwfFilterEvent.ProcessingSelect2( this, e.params.data );
			});
		},
		ProcessingSelect2: function( item, selected ) {
			let value      = selected.id;
			let name       = $(item).attr('name');
			let label      = $(item).find('option[value="'+ value +'"]').attr('data-title');
			let slug       = $(item).find('option[value="'+ value +'"]').attr('data-slug');
			let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );

			pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
			pwfAjaxQuery.getProducts();
		},
		ProcessingSelect: function( item ) {
			let selectedOption = $(item).find('option:selected');
			
			let label      = $(selectedOption).attr('data-title');
			let slug       = $(selectedOption).attr('data-slug');
			let value      = $(item).val();
			let name       = $(item).attr('name');
			let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );

			pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
			pwfAjaxQuery.getProducts();
		},
		targetDoMoreButton: function() {
			let allMoreButtonFields = $('.pwf-more-button-block');
			$(allMoreButtonFields).each( function( index, field ) {
				pwfFilterEvent.doMoreButton(field);
			});
		},
		doMoreButton: function( field ) {
			let name       = $(field).attr('data-item-key');
			let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
			if ( false !== filterItem ) {
				if ( filterItem.hasOwnProperty('height_of_visible_content') ) {
					let displayed     = 0;
					let displayLength = parseInt( filterItem['height_of_visible_content'] ) - 1 ;
					let fieldChildren = $(field).find('.pwf-field-item-container > .pwf-item') ;
					if ( fieldChildren.length > 0 ) {
						displayed = pwfFilterEvent.excuteMoreButton( fieldChildren, displayed, displayLength );
					}

					if ( displayed < displayLength ) {
						$(field).find('.pwf-more-button').remove();
					}
				}	
			}
		},
		excuteMoreButton: function( fields, displayed, displayLength ) {
			$( fields ).each( function ( index, field ) {
				if ( displayed > displayLength ) {
					$(field).addClass('pwf-item-hidden');
				} else {
					displayed++;
				}
				if ( $(field).hasClass('pwf-collapsed-open') ) {
					let fieldChildren = $(field).find('.pwf-children').first().children();
					displayed = pwfFilterEvent.excuteMoreButton( fieldChildren, displayed, displayLength );
				} else {
					//displayed++;
				}
			});

			return displayed;
		},
		checkFilterItemsHasSortingItem: function( selected = '' ) {
			let filter_has_orderby = false
			let orderbyValue       = '';
			for ( let key in pwfActiveFilterItems ) {
				if ( key === 'orderby' ) {
					filter_has_orderby = true;
					orderbyValue = pwfActiveFilterItems['orderby']['values'][0];
				}
			}

			if ( false === filter_has_orderby && 'default' === selected ) {
				filter_has_orderby = true;
				orderbyValue       = 'menu_order';
			}

			if ( filter_has_orderby ) {
				let usecomponents = pwfFilterSetting.usecomponents;
				if ( usecomponents.includes('sorting') && 'on' == pwfFilterSetting.sorting_ajax && '' !== pwfFilterSetting.sorting_selector ) {
					let orderbySelector = pwfFilterSetting.sorting_selector;
					if ( 'showall' === orderbyValue ) {
						orderbyValue = 'menu_order';
					}
					$( orderbySelector ).find( 'option[value="'+ orderbyValue +'"]' ).prop( "selected", "true" );
				}
			}
		},
		submitFilter: function( from = '' ) {
			if ( ! $.isEmptyObject( pwfActiveFilterItems ) || true === pwfOldActiveFilterItems ) {
				pwfAjaxQuery.getProducts('getproducts', from );
			}
		},
		resetFilter: function( from = '' ) {
			$('.pwf-note-list').empty();
			$('.pwf-note-list').addClass('empty-active-items');
			const currentHtmlFilter = $('.filter-id-'+ pwfFilterID );
			for ( let key in pwCurrentfFilterItems ) {
				let filterItem = pwCurrentfFilterItems[key];

				if ( 'checkboxlist' === filterItem['item_type'] ) {
					let items = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-item.checked');
					$(items).each( function( index, item ) {
						$(item).removeClass('checked');
						$(item).find('input[type=checkbox]').first().prop( "checked", false );
					});
				} else if ( 'radiolist' === filterItem['item_type'] ) {
					let items = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.checked');
					$(items).each( function( index, item ) {
						$(item).removeClass('checked');
						$(item).find('input[type=radio]').first().prop( "checked", false );
					});

					let showAll = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('input[data-slug="showall"]');
					if ( showAll.length ) {
						$(showAll).prop( "checked", true );
						$(showAll).closest('.pwf-item-label').addClass('checked');
					}
				} else if ( 'dropdownlist' === filterItem['item_type'] ) {
					let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('select');
					item.prop('selectedIndex', 0);

					if ( $(item).hasClass('pwf-dropdownlist-item-select2') ) {
						$(item).select2("destroy");
						pwfFilterEvent.addSelect2Event(item);
					}
				} else if ( 'boxlist' === filterItem['item_type'] ) {
					$(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-item.selected').removeClass('selected');
				} else if ( 'colorlist' === filterItem['item_type'] ) {
					$(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-item.selected').removeClass('selected');
				} else if ( 'textlist' === filterItem['item_type'] ) {
					$(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-item.selected').removeClass('selected');
				} else if ( 'date' === filterItem['item_type'] ) {
					$(".pwf-date-from, .pwf-date-to").val('');
				} else if ( 'priceslider' === filterItem['item_type'] ) {
					let priceSlider = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-range-slider');
					if( $(priceSlider).hasClass('noUi-target') ) {
						let rangeSlider   = document.getElementById( $(priceSlider).attr('id') );
						let minPrice      = $(rangeSlider).attr('data-min');
						let maxPrice      = $(rangeSlider).attr('data-max');
						rangeSlider.noUiSlider.updateOptions( {
							start: [ parseInt(minPrice), parseInt(maxPrice) ],
						}, false );
					}
				} else if ( 'rangeslider' === filterItem['item_type'] ) {
					let rangeSliderContainer = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-range-slider');
					if( $(rangeSliderContainer).hasClass('noUi-target') ) {
						let rangeSlider   = document.getElementById( $(rangeSliderContainer).attr('id') );
						let minValue      = $(rangeSlider).attr('data-min');
						let maxValue      = $(rangeSlider).attr('data-max');
						rangeSlider.noUiSlider.updateOptions( {
							start: [ parseInt(minValue), parseInt(maxValue) ],
						}, false );
					}
				}
			}

			pwfActiveFilterItems  = {};
			pwfCurrentUrlQuery    = '';
			pwfResetButtonClicked = true; // used when click reset button to remove orderby

			if ( pwfFilterAttributes.hasOwnProperty('per_page') ) {
				let perPage = pwfFilterAttributes.per_page;
				pwfFilterAttributes = {};
				pwfFilterAttributes['per_page'] = perPage;
			} else {
				pwfFilterAttributes = {};
			}

			pwfFilterEvent.setApplyResetButtonStatus();
			pwfAjaxQuery.getProducts( 'getproducts', from );
		},
		dateFieldChanged: function( dateField ) {
			let parentWrap = $(dateField).closest('.pwf-date-field');
			if ( '' !== $(parentWrap).find('.pwf-date-from').val() && '' !== $(parentWrap).find('.pwf-date-to').val() ) {
				
				let label      = $(parentWrap).find('.pwf-date-from').val() + ' / ' + $(parentWrap).find('.pwf-date-to').val();
				let dateFrom   = moment( $(parentWrap).find('.pwf-date-from').val(), dateFormatDisplayedInInputField );
				let dateTo     = moment( $(parentWrap).find('.pwf-date-to').val(), dateFormatDisplayedInInputField );
				let value      = [ dateFrom.format('YYYY-MM-DD'), dateTo.format(dateFormatUsingToSend) ];
				let name       = $(parentWrap).attr('data-item-key');
				let filterItem = pwfFilterActions.getFilterItemDataByUrlKey( name );
				let slug       = value;

				pwfFilterActions.processingFilterItem( filterItem, value, label, slug );
				pwfAjaxQuery.getProducts();
			}
		},
		disableApplyButton: function() {
			$('.pwf-item-button.filter-button').addClass('pwf-disabled-btn').attr("disabled", true);
			// mobile
		},
		setApplyResetButtonStatus: function() {
			if ( $.isEmptyObject( pwfActiveFilterItems ) && false === pwfOldActiveFilterItems ) {
				$('.pwf-reset-button').addClass('pwf-disabled-btn').prop("disabled", true);
				$('.pwf-filter-button').addClass('pwf-disabled-btn').prop("disabled", true);
			} else if ( $.isEmptyObject( pwfActiveFilterItems ) && true === pwfOldActiveFilterItems ) {
				$('.pwf-reset-button').removeClass('pwf-disabled-btn').prop( 'disabled', false );
				$('.pwf-filter-button').removeClass('pwf-disabled-btn').prop( 'disabled', false );
			} else {
				$('.pwf-reset-button').removeClass('pwf-disabled-btn').prop( 'disabled', false );
				$('.pwf-filter-button').removeClass('pwf-disabled-btn').prop( 'disabled', false );
			}
		}
	};
	
	var pwfFilterActions = {
		getFilterItemDataByUrlKey: function( urlKey ) {
			if ( $.isEmptyObject( pwCurrentfFilterItems ) ) {
				return false;
			}
			for ( let key in pwCurrentfFilterItems ) {
				let filter = pwCurrentfFilterItems[key];
				if ( filter['url_key'] === urlKey ) {
					return filter;
				}
			}
		},
		processingFilterItem: function( filterItem, value, label, slug ) {
			let taxonomy        = '';
			let ignoredTaxonomy = [ 'priceslider', 'date', 'search', 'rangeslider', 'rating' ];

			if ( ! ignoredTaxonomy.includes( filterItem['item_type'] ) ) {
				if ( 'category' === filterItem['source_of_options'] ) {
					taxonomy = 'product_cat';
				} else if ( 'attribute' === filterItem['source_of_options'] ) {
					taxonomy = filterItem['item_source_attribute'];
				} else if ( 'taxonomy' === filterItem['source_of_options'] ) {
					taxonomy = filterItem['item_source_taxonomy'];
				} else if ( 'tag' === filterItem['source_of_options'] ) {
					taxonomy = 'product_tag';
				} else if ( 'stock_status' === filterItem['source_of_options'] ) {
					taxonomy = 'stock_status';
				} if ( 'orderby' === filterItem['source_of_options'] ) {
					pwfFilterAttributes['orderby'] = '';
				}
			}

			let term = {
				'taxonomy':  taxonomy,
				'url_key':   filterItem['url_key'], 
				'value':     value,
				'label':     label,
				'fieldType': filterItem['item_type'],
				'slug':      slug,
			};

			if ( 'priceslider' === filterItem['item_type'] ) {
				if ( 'two' === filterItem['price_url_format'] ) {
					term['priceUrlKey'] = {
						'minPrice': filterItem['url_key_min_price'],
						'maxPrice': filterItem['url_key_max_price'],
					}
				}
			}

			if ( 'rangeslider' === filterItem['item_type'] ) {
				if ( 'two' === filterItem['range_slider_url_format'] ) {
					term['rangeUrlKey'] = {
						'minValue': filterItem['url_key_range_slider_min'],
						'maxValue': filterItem['url_key_range_slider_max'],
					}
				}
			}

			if ( 'date' === filterItem['item_type'] ) {
				term['dateUrlKey'] = {
					'after': filterItem['url_key_date_after'],
					'before': filterItem['url_key_date_before'],
				};
			}

			let multiSelectItem = [ 'boxlist', 'colorlist', 'textlist', 'rating' ];
			if ( multiSelectItem.includes( filterItem['item_type'] ) ) {
				term['multi_select'] = filterItem['multi_select'];
			}

			if ( 'rating' === filterItem['item_type'] ) {
				term['up_text'] = filterItem['up_text'];
			}

			pwfFilterActions.updateActiveFilterItems( term, filterItem );
			
			if ( ! pwfWooFilter.isFilterStartAuto()) {
				pwfFilterActions.updateNotices();
				pwfFilterEvent.setApplyResetButtonStatus();
			}
		},
		checkTermExistInActiveFilterItems: function( filterItem ) {
			for ( let key in pwfActiveFilterItems ) {
				if ( key === filterItem['url_key'] ) {
					return true;
				}
			}
			return false;
		},
		updateActiveFilterItems: function( term, filterItem  ) {
			/**
			 * Used to add/remove active filter
			 * @param {*} term 
			 * @param {*} filterItem 
			*/
			// when update filter attributes page make it empty or make it empty
			delete pwfFilterAttributes['page'];

			let newTerm = {
				'taxonomy':   term['taxonomy'],
				'fieldType': term['fieldType'],
				'notices': [{
					'id'  : term['value'],
					'slug':  term['slug'],
					'label': term['label'],
				}],
			};
			if ( 'priceslider' === term['fieldType'] || 'date' === term['fieldType'] || 'rangeslider' === term['fieldType'] ) {
				// because priceslider value is array by default
				newTerm['values'] = term['value'];
			} else {
				newTerm['values'] = [ term['value'] ];
			}

			if ( term.hasOwnProperty('priceUrlKey') ) {
				newTerm['priceUrlKey'] = term['priceUrlKey'];
			}

			if ( term.hasOwnProperty('rangeUrlKey') ) {
				newTerm['rangeUrlKey'] = term['rangeUrlKey'];
			}
			

			if ( term.hasOwnProperty('dateUrlKey') ) {
				newTerm['dateUrlKey'] = term['dateUrlKey'];
			}

			// Remove old price slider if exist/set before
			let fieldsHasOneValue = [ 'priceslider', 'rangeslider', 'date', 'search' ];
			if ( 'rating' === filterItem['item_type'] && 'on' === term.up_text ) {
				fieldsHasOneValue.push('rating');
			}

			if ( fieldsHasOneValue.includes( term['fieldType'] ) && pwfFilterActions.checkTermExistInActiveFilterItems( term ) && '' === term['value'] ) {
				delete pwfActiveFilterItems[ term['url_key'] ];
			} else if ( ( 'radiolist' === term['fieldType'] || 'dropdownlist' === term['fieldType'] ) && 'showall' === term['value']  ) {
				// for showall only for dropdown and radio
				if ( pwfFilterActions.checkTermExistInActiveFilterItems( term ) ) {
					delete pwfActiveFilterItems[ term['url_key'] ];
				}
				if ( 'orderby' === term['url_key'] && 'orderby' === filterItem['source_of_options'] ) {
					pwfFilterEvent.checkFilterItemsHasSortingItem('default');
				}			
			} else if ( Object.entries(pwfActiveFilterItems).length === 0 ) {
				// check if empty active filters
				pwfActiveFilterItems[ term['url_key'] ] = newTerm;
			} else {
				// check if url_key exists
				if ( pwfFilterActions.checkTermExistInActiveFilterItems( term ) && 'search' !== term['fieldType'] ) {
					// check if value exist remove it or add it
					if ( pwfActiveFilterItems[ term['url_key'] ].values.includes( term['value'] ) ) {
						// remove this values from url-key.values
						for ( let i = 0; i < pwfActiveFilterItems[ term['url_key'] ].values.length; i++ ) {
							if ( pwfActiveFilterItems[ term['url_key'] ].values[i] === term['value'] ) { 
								pwfActiveFilterItems[ term['url_key'] ].values.splice( i, 1 );
							}
						}
						// remove from notic array
						for ( let i = 0; i < pwfActiveFilterItems[ term['url_key'] ].notices.length; i++ ) {
							if ( pwfActiveFilterItems[ term['url_key'] ].notices[i]['id'] === term['value'] ) { 
								pwfActiveFilterItems[ term['url_key'] ].notices.splice( i, 1 );
							}
						}
						// remove this filter url-key if empty
						if ( Array.isArray( pwfActiveFilterItems[ term['url_key'] ].values ) && ! pwfActiveFilterItems[ term['url_key'] ].values.length ) {
							delete pwfActiveFilterItems[ term['url_key'] ];
						}

						if ( 'orderby' === filterItem['source_of_options'] ) {
							delete pwfActiveFilterItems[ term['url_key'] ];
							if ( 'orderby' === term['url_key'] && 'orderby' === filterItem['source_of_options'] ) {
								pwfFilterEvent.checkFilterItemsHasSortingItem('default');
							}
						}

					} else {
						// add this value to url-key
						let emptyItem = [ 'radiolist', 'priceslider', 'date', 'search', 'rangeslider' ];
						if ( 'rating' === filterItem['item_type'] && 'on' === term.up_text ) {
							emptyItem.push('rating');
						}

						if ( 'dropdownlist' === filterItem['item_type'] ) {
							if ( 'orderby' === filterItem['source_of_options'] || 'stock_status' === filterItem['source_of_options'] ) {
								emptyItem.push('dropdownlist');
							} else if ( !  filterItem.hasOwnProperty('multi_select') || ( filterItem.hasOwnProperty('multi_select') && 'on' !== filterItem.multi_select ) ) {
								emptyItem.push('dropdownlist')
							}
						}

						if ( emptyItem.includes( term['fieldType'] ) ) {
							// only active one item filter from this item
							pwfActiveFilterItems[ term['url_key'] ].values  = []; // empty values
							pwfActiveFilterItems[ term['url_key'] ].notices = []; // empty notices
						}

						let multiselectFields = [ 'colorlist', 'boxlist', 'textlist' ];
						if ( multiselectFields.includes( term['fieldType'] ) && term.hasOwnProperty('multi_select') && 'on' !== term.multi_select ) {
							// only active one item filter from this item
							pwfActiveFilterItems[ term['url_key'] ].values  = [];
							pwfActiveFilterItems[ term['url_key'] ].notices = [];
						}

						if ( 'priceslider' === term['fieldType'] || 'date' === term['fieldType'] || 'rangeslider' === term['fieldType'] ) {
							// because value here is array
							pwfActiveFilterItems[ term['url_key'] ].values = term['value'];
						} else {
							pwfActiveFilterItems[ term['url_key'] ].values.push( term['value'] );
						}

						// add this value to notic array
						let notice = {
							'id':  term['value'],
							'label': term['label'],
							'slug':  term['slug'],
						}
						pwfActiveFilterItems[ term['url_key'] ].notices.push( notice );
					}
				} else {
					// if term url_key not exist add it
					pwfActiveFilterItems[ term['url_key'] ] = newTerm;
				}
			}
		},
		updateNotices: function() {
			$('.pwf-note-list').empty();
			$('.pwf-note-list').removeClass('empty-active-items')
			let html = '';
			if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
				for ( let key in pwfActiveFilterItems ) {
					let fieldType = pwfActiveFilterItems[key]['fieldType'];
					let notices   = pwfActiveFilterItems[key]['notices'];
					if ( 'priceslider' === fieldType ) {
						notices = notices[0];
						let label = pwfTranslatedText.price + ': ' + pwfFilterActions.priceNotices( notices['id'][0], 'pwf-from' );
						label    += '-' + pwfFilterActions.priceNotices( notices['id'][1], 'pwf-to' );
						html     += pwfFilterActions.clearTemplate( key, '', label, ' pwf-range-slider-note' );				
					} else if ( 'rangeslider' === fieldType ) {
						notices = notices[0];
						let currentFilter = pwfFilterActions.getFilterItemDataByUrlKey( key );
						let unit = currentFilter['slider_range_unit'];
						unit     = ( '' !== unit ) ?  ' ' + unit : '';

						let label = notices['label'] + ': ' + '<span class="pwf-from">' + notices['id'][0] + '</span>';
						label    += ' - <span class="pwf-to">' + notices['id'][1] + unit + '</span>';
						html     += pwfFilterActions.clearTemplate( key, '', label, ' pwf-range-slider-note' );
					} else if ( 'search' === fieldType ) {
						notices.forEach( function( note ) {
							html += pwfFilterActions.clearTemplate( key, note['id'], pwfTranslatedText.search + ': ' + note['label'], ' pwf-search-note' );
						});
					} else if ( 'date' === fieldType ) {
						notices.forEach( function( note ) {
							html += pwfFilterActions.clearTemplate( key, note['id'], note['label'], ' pwf-date-note' );
						});
					} else if ( 'rating' === fieldType ) {
						notices.forEach( function( note ) {
							html += pwfFilterActions.clearTemplate( key, note['id'], pwfTranslatedText.rate + ' ' + note['label'], ' pwf-rate-note' );
						});
					} else {
						notices.forEach( function( note ) {
							html += pwfFilterActions.clearTemplate( key, note['id'], note['label'] );
						});
					}
				}

				if ( pwfMobileView.countActiveFilterItems() > 1 ) {
					let clear = pwfFilterActions.clearTemplate( 'clearall', 'clearall', pwfTranslatedText.clearall, ' pwf-clear-all-note' );
					html = clear + html;
				}
				$('.pwf-note-list').append( html );
			} else {
				$('.pwf-note-list').addClass('empty-active-items');
			}
		},
		clearTemplate: function( key, id, label, cssClass = '' ) {
			let itemValue = ( '' !== id ) ? ' data-item-value="' + id + '"' : '';

			let html = '<span class="pwf-note-item' + cssClass +  '" data-item-key="' + key + '"' + itemValue + '>';
			html    += '<span class="pwf-remove-filter"><span class="pwf-icon-remove"></span>';
			html    += '<span class="note-text">'+ label +'</span></span></span>';

			return html;
		},
		priceNotices: function( price, css ) {
			let html = '';
			switch( pwfCurrencyPosition ) {
				case 'left':
					html += pwfWooFilter.getCurrencyTemplate() + '<span class="' + css + '">' + price + '</span>';
					break;
				case 'right':
					html += '<span class="' + css + '">' + price + '</span>'+ pwfWooFilter.getCurrencyTemplate();
					break;
				case 'left_space':
					html += pwfWooFilter.getCurrencyTemplate() + '&nbsp;<span class="' + css + '">' + price + '</span>';
					break;
				case 'right_space':
					html += '<span class="' + css + '">' + price + '</span>&nbsp;'+ pwfWooFilter.getCurrencyTemplate();
					break;
			}

			return html;
		},
		removeSelectedValueFromHTMLFilter: function( filterItem, value ) {
			const currentHtmlFilter = $('.filter-id-'+ pwfFilterID );

			if ( 'checkboxlist' === filterItem['item_type'] ) {
				let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('[value="' + value + '"]');
				$(item).prop( "checked", false );
				$(item).closest('.pwf-checkboxlist-item').removeClass('checked');
			} else if ( 'radiolist' === filterItem['item_type'] ) {
				let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('[value="' + value + '"]');
				$(item).prop( "checked", false );
				$(item).closest('.pwf-radiolist-label').removeClass('checked');
			} else if ( 'dropdownlist' === filterItem['item_type'] ) {
				let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('select').find('option[value="'+ value + '"]');
				item.prop("selected", false);
				if ( $(item).hasClass('pwf-dropdownlist-item-select2') ) {
					$(item).select2("destroy");
					pwfFilterEvent.addSelect2Event(item);
				}
			} else if ( 'boxlist' === filterItem['item_type'] ) {
				let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('[data-item-value="'+ value +'"]');
				item.removeClass('selected');
			} else if ( 'colorlist' === filterItem['item_type'] ) {
				let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('[data-item-value="'+ value +'"]');
				item.removeClass('selected');
			} else if ( 'textlist' === filterItem['item_type'] ) {
				let item = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('[data-item-value="'+ value +'"]');
				item.removeClass('selected');
			} else if ( 'date' === filterItem['item_type'] ) {
				let dateField = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]');
				$(dateField).find('.pwf-date-from').val('');
				$(dateField).find('.pwf-date-to').val('');
			} else if ( 'priceslider' === filterItem['item_type'] ) {
				let priceSlider = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-range-slider');
				if( $(priceSlider).hasClass('noUi-target') ) {
					let rangeSlider = document.getElementById( $(priceSlider).attr('id') );
					let minPrice    = $(rangeSlider).attr('data-min');
					let maxPrice    = $(rangeSlider).attr('data-max');
					rangeSlider.noUiSlider.updateOptions( {
						start: [ parseInt(minPrice), parseInt(maxPrice) ],
					}, false );
				}
			} else if ( 'rangeslider' === filterItem['item_type'] ) {
				let rangeSliderContainer = $(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-range-slider');
				if( $(rangeSliderContainer).hasClass('noUi-target') ) {
					let rangeSlider = document.getElementById( $(rangeSliderContainer).attr('id') );
					let minValue    = $(rangeSlider).attr('data-min');
					let maxValue    = $(rangeSlider).attr('data-max');
					rangeSlider.noUiSlider.updateOptions( {
						start: [ parseInt(minValue), parseInt(maxValue) ],
					}, false );
				}
			} else if ( 'search' === filterItem['item_type'] ) {
				$(currentHtmlFilter).find('[data-item-key="' + filterItem['url_key'] + '"]').find('.pwf-search-from').val('');
			}
		},
	};
	
	var pwfAjaxQuery = {
		getDatabaseQuery: function() {
			let queryArgs  = {};
			let attributes = {};
			let usecomponents  = pwfFilterSetting.usecomponents;
			let orderbySelector = pwfFilterSetting.sorting_selector;

			if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
				for ( let key in pwfActiveFilterItems ) {
					let filter = pwfActiveFilterItems[key];
					queryArgs[key] = filter['values'];
				}
			}

			if ( pwfResetButtonClicked ) {
				// if reset button is clicked remove orderby attributes from woo dropdwon menu
				pwfResetButtonClicked = false; // return reset to false
				if ( usecomponents.includes('sorting') && '' !== orderbySelector && $( orderbySelector ).length ) {
					$( orderbySelector ).find('select').prop('selectedIndex', '');
				}
				
			} else {
				// If use component sort disabled check if browser url has orderby
				if ( ! pwfFilterAttributes.hasOwnProperty('orderby') ) {
					
					if ( usecomponents.includes('sorting') && '' !== orderbySelector && $( orderbySelector ).length ) {
						let queryString = window.location.search;
						if ( '' !== queryString ) {
							let urlParams  = new URLSearchParams(queryString);
							if ( urlParams.has('orderby') && '' !== urlParams.get('orderby') ) {
								pwfFilterAttributes['orderby'] = urlParams.get('orderby');
							}
						}
					}				
				}
			}

			if ( ! $.isEmptyObject( pwfFilterAttributes ) ) {
				attributes = pwfFilterAttributes;
			}
			
			let data = {
				'query_vars': queryArgs,
				'attributes': attributes, // page number, number of products
			};

			return data;
		},
		getProducts: function( action = '', from = '' ) {
			let getProducts = false;

			if ( 'sorting' === from || 'per_page' === from || 'getpagenumber' === from ) {
				getProducts = true;
			} else if ( pwfIsResponsiveView ) {
				if ('canvas-submitButton' === from ) {
					$('.pwf-canvas-close-btn').trigger('click');
					getProducts = true;
				} else if ('canvas-resetButton' === from ) { 
					if ( pwfOldActiveFilterItems ) {
						$('.pwf-canvas-close-btn').trigger('click');
						getProducts = true;
					} else {
						getProducts = false;
					}
				} else if ( 'getproducts' === action ) {
					$('.pwf-canvas-close-btn').trigger('click');
					getProducts = true;
				} else if ( '' === action && pwfWooFilter.isFilterStartAuto() ) {
					$('.pwf-canvas-close-btn').trigger('click');
					getProducts = true;
				}
			} else {
				if ( 'resetButton' === from ) {
					if ( pwfOldActiveFilterItems ) {
						getProducts = true;
					} else {
						getProducts = false;
					}
				} else if ( 'getproducts' === action ) {
					getProducts = true;
				} else if ( '' === action && pwfWooFilter.isFilterStartAuto() ) {			
					getProducts = true;
				}
			}

			if ( getProducts ) {
				pwfAjaxQuery.doingAjax();
			}
		},
		prepareAjaxData: function() {
			let queryArgs         = pwfAjaxQuery.getDatabaseQuery();
			let productsContainer = pwfFilterSetting.products_container_selector;

			// how to get number of columns set number of columns
			if ( $(productsContainer).is('[class*="columns-"]') ) {
				let classList  = $(productsContainer).attr('class');
				let cssColumns = classList.match(/columns-\d+/i);
				if ( null !== cssColumns ) {
					cssColumns = cssColumns[0];
					let columns    = cssColumns.match(/\d+/).join('');
					if ( null !== columns ) {
						queryArgs.attributes['columns'] = columns;
					}
				}
			}

			let data = {
				'action':    'get_filter_result', // get_products
				'nonce':      pwf_woocommerce_filter.nonce,
				'query_vars': queryArgs.query_vars,
				'attributes': queryArgs.attributes,
				'filter_id' : pwfFilterID,
			};

			if ( typeof pwffilterVariables !== 'undefined' ) {
				if ( pwffilterVariables.hasOwnProperty('current_taxonomy_id') && pwffilterVariables.hasOwnProperty('current_taxonomy_name') ) {
					data['current_taxonomy_id']   = pwffilterVariables.current_taxonomy_id;
					data['current_taxonomy_name'] = pwffilterVariables.current_taxonomy_name;
				}
			}

			if ( pwfGetProductsOnly && queryArgs.attributes.hasOwnProperty('page') ) { 
				data['get_products_only'] = 'true';
			}

			if ( typeof pwffilterVariables !== 'undefined' && pwffilterVariables.hasOwnProperty('rule_hidden_items') && Array.isArray( pwffilterVariables.rule_hidden_items ) ) {
				data['rule_hidden_items'] = pwffilterVariables.rule_hidden_items;
			}

			let result = {
				'queryArgs': queryArgs,
				'data':       data, // page number, number of products
			};

			return result;
		},
		doingAjax: function() {
			let prepareData = pwfAjaxQuery.prepareAjaxData();
			let queryArgs   = prepareData.queryArgs;

			let request = $.ajax({
				type: 'POST',
				dataType: 'html',
				url: pwf_woocommerce_filter.ajaxurl,
				data: prepareData.data,
				beforeSend: function() {
					pwfAjaxQuery.beforeSendingAjax();
				}
			});

			request.done( function( data ) {
				if ( data.hasOwnProperty('success') && 'false' === data.success ) {
					//console.log( data.message );
				} else {
					let productsContainer = pwfFilterSetting.products_container_selector;

					data = JSON.parse( data );
					let products   = data.products;
					let filterHtml = data.filter_html;

					// Add CSS class to each product;
					products = pwfAjaxQuery.addClassToProduct( products );

					if ( pwfGetProductsOnly ) {
						if ( 'numbers' === pwfPagination.getType() ) {
							$( productsContainer ).empty();
						}
						$( productsContainer ).append( products ).slideDown();
					} else {
						$( productsContainer ).empty();
						$( productsContainer ).append( products ).slideDown();
					}
					
					pwfAjaxQuery.scrollToTop();

					if ( ! pwfGetProductsOnly ) {
						$('.filter-id-' + pwfFilterID + ' .pwf-woo-filter-inner').empty();
						$('.filter-id-' + pwfFilterID + ' .pwf-woo-filter-inner' ).append( filterHtml );

						pwfFilterEvent.checkToggle();
						pwfFilterEvent.targetDoMoreButton();
						pwfFilterEvent.select2();
						pwfFilterEvent.dateField();
						pwfFilterEvent.noUiSlider();
					}

					pwfAjaxQuery.updateBrowserUrlQueryString();
					pwfFilterActions.updateNotices();
					pwfFilterEvent.checkFilterItemsHasSortingItem();

					if ( $.isEmptyObject( queryArgs.query_vars ) ) {
						pwfWooFilter.setOldActiveFilterItems('false');
					} else {
						pwfWooFilter.setOldActiveFilterItems('true');
					}
					pwfFilterEvent.setApplyResetButtonStatus();
					pwfFilterEvent.disableApplyButton();

					pwfAjaxQuery.customizeResultCount( data.attributes.html_result_count );
					pwfAjaxQuery.customizeHTMLPagination( data.attributes.pagination, data.attributes.next_page );

					if ( pwfMobileView.isMobileView() ) {
						pwfMobileView.doChanges();
					}
					
					$( document.body ).trigger( 'pwf_filter_js_ajax_done', [ { 'filterID': pwfFilterID, 'paginationType' : pwfPagination.getType(), 'queryArgs': queryArgs } ] );
				}
			});
			request.always( function() {
				pwfAjaxQuery.alwaysAjax();
				pwfGetProductsOnly = false;
				pwfAjaxQuery.removeClassFromProduct();
			});
			request.fail(function( jqXHR, textStatus ) {
				pwfGetProductsOnly = false;
  				console.log( "Request failed: " + textStatus );
			});
		},
		beforeSendingAjax: function() {
			if ( pwfGetProductsOnly ) {
				if ( 'numbers' === pwfPagination.getType() ) {
					$('body').prepend( pwfAjaxQuery.getHTMLLoaderTemplate() );
				} else {
					pwfPagination.addLoadingToLoadMoreButton();
					$('.filter-id-'+ pwfFilterID ).prepend( pwfAjaxQuery.getHTMLLoaderTemplate() );
				}
			} else {
				$('body').prepend( pwfAjaxQuery.getHTMLLoaderTemplate() );
			}
			$( document.body ).trigger( 'pwf_filter_before_sending_ajax', [ { 'filterID': pwfFilterID, 'paginationType' : pwfPagination.getType() } ] );
		},
		alwaysAjax: function() {
			if ( pwfGetProductsOnly ) {
				if ( 'numbers' === pwfPagination.getType() ) {
					$('body').find('.pwf-overlay').remove();
				} else {
					pwfPagination.removeLoadingToLoadMoreButton();
					$('.filter-id-'+ pwfFilterID ).find('.pwf-overlay').remove();
				}
			} else {
				$('body').find('.pwf-overlay').remove();
			}
	
			$( document.body ).trigger( 'pwf_filter_after_ajax_done', [ { 'filterID': pwfFilterID, 'paginationType' : pwfPagination.getType() } ] );
		},
		getHTMLLoaderTemplate: function() {
			let loader     = '<span class="pwf-loader"></span>';
			let pageLoader = pwfCustomization.getPageLoader();
			if ( '' !== pageLoader ) {
				loader = pageLoader;
			}

			return '<div class="pwf-overlay pwf-active">' + loader + '</div>';
		},
		customizeResultCount: function( htmlResultCount ) {
			let resultCountSelector = pwfFilterSetting.result_count_selector;

			if ( pwfFilterSetting.usecomponents.includes('results_count') && $(resultCountSelector).length ) {
				if ( 'numbers' !== pwfPagination.getType() ) {
					let firstProduct = $(resultCountSelector).first().text();
					if ( null !== firstProduct ) {
						firstProduct    = firstProduct.match(/\d/);
						if ( null !== firstProduct ) {
							htmlResultCount = htmlResultCount.replace( /\d+/, firstProduct[0] );
						}
					}
				}

				$(resultCountSelector).each( function() {
					if ( '.woocommerce-result-count' === resultCountSelector ) {
						$(this).replaceWith(htmlResultCount);
					} else {
						$(this).empty().append(htmlResultCount);
					}
				});
				$( document.body ).trigger( 'pwf_filter_js_ajax_done_result_count', [ { 'filterID': pwfFilterID, 'paginationType' : pwfPagination.getType(), 'htmlResultCount': htmlResultCount } ] );
			}
		},
		customizeHTMLPagination: function( htmlPagination, nextPage = '' ) {
			let paginationSelector  = pwfFilterSetting.pagination_selector;

			if ( pwfFilterSetting.usecomponents.includes('pagination') && '' !== paginationSelector && $(paginationSelector).length > 0) {
				if ( 'numbers' === pwfPagination.getType() ) {
					if ( '' !== htmlPagination ) {
						let pageURL = pwfCurrentPageURL;

						$( paginationSelector ).each( function() {
							$(this).replaceWith( htmlPagination );
						});

						let paginationLinks = $( paginationSelector ).find('a');
						let urlHashQuery    = pwfCurrentUrlQuery;
						$(paginationLinks).each( function() {
							if ( pwfIsShortcodeWoo ) {
								let oldUrl = $(this).attr("href");
								let pageNum    = oldUrl.match(/\d+/);
								if ( null !== pageNum ) {
									let shortcodeLink = '?product-page=' + parseInt( pageNum[0] );
									if ( urlHashQuery.startsWith('?') ) {
										urlHashQuery = '&' + urlHashQuery.substring(1);
									}
									let newUrl = pageURL + shortcodeLink + urlHashQuery;
									$(this).attr("href", newUrl);
								}
							} else {
								let oldUrl = $(this).attr("href"); // Get current url
								oldUrl     = oldUrl.substring(1);
								if ( false === pwfIsURLHasSlash ) {
									oldUrl = oldUrl.slice( 0, -1 );
								}
								let newUrl = pageURL + oldUrl + pwfCurrentUrlQuery;
								$(this).attr("href", newUrl);
							}
						});
					} else {
						$( paginationSelector ).empty();
					}
				} else {
					if ( '' !== nextPage ) {
						pwfPagination.enableLoadMoreButton( nextPage );
					} else {
						pwfPagination.disableLoadMoreButton();
					}
				}
			}
		},
		addClassToProduct: function( products ) {
			// used to add new CSS class when loading new prdouct, useful for load more and infinite scroll with masonry
			products = $( $.parseHTML( products ) );
			products = $('<div class="pwf-loading-wrapper"></div>').append( products );
			return $(products).children().addClass('pwf-new-product-added');
		},
		removeClassFromProduct: function() {
			setTimeout( function() {
				$( pwfFilterSetting.products_container_selector ).find('.pwf-new-product-added').removeClass('pwf-new-product-added');
			}, 3000 );
		},
		scrollToTop: function() {
			let productsContainer = pwfFilterSetting.products_container_selector;
			let scrollTo          = productsContainer;
			let doScroll          = false;
			if ( pwfGetProductsOnly ) {
				if ( 'numbers' === pwfPagination.getType() ) {
					doScroll = true;
				}
			} else {
				doScroll = true;
			}
			if ( pwfFilterSetting.hasOwnProperty('scroll_to') && '' !== pwfFilterSetting.scroll_to ) {
				scrollTo = pwfFilterSetting.scroll_to;
			}
			if ( doScroll && $(scrollTo).length ) {
				if ( ! pwfMobileView.isTopElementIntoView(scrollTo) ) {
					$('html, body').animate({
						scrollTop: $(scrollTo).offset().top - 100
					}, 800, function(){});
				}
			}
		},
		/**
		 * Update URL query string (hash) after doing ajax
		 */
		updateBrowserUrlQueryString: function() {
			if ( 'on' !== pwfFilterSetting.browser_hash ) {
				return;
			}
	
			let hash = '';
	
			if ( ! $.isEmptyObject( pwfActiveFilterItems ) ) {
				for ( let key in pwfActiveFilterItems ) {
					if ( '' !== hash ) {
						hash += '&';
					}
	
					let values  = pwfActiveFilterItems[key]['values'];
					let notices = pwfActiveFilterItems[key]['notices'];
					if ( 'priceslider' === pwfActiveFilterItems[key]['fieldType'] ) {
						if ( pwfActiveFilterItems[key].hasOwnProperty('priceUrlKey') ) {
							hash += pwfActiveFilterItems[key]['priceUrlKey']['minPrice'] + '=' + values[0] + '&' +  pwfActiveFilterItems[key]['priceUrlKey']['maxPrice'] + '=' + values[1];
						} else {
							hash += key + '=' + values[0] + '-' + values[1];
						}
					} else if ( 'rangeslider' === pwfActiveFilterItems[key]['fieldType'] ) {
						if ( pwfActiveFilterItems[key].hasOwnProperty('rangeUrlKey') ) {
							hash += pwfActiveFilterItems[key]['rangeUrlKey']['minValue'] + '=' + values[0] + '&' +  pwfActiveFilterItems[key]['rangeUrlKey']['maxValue'] + '=' + values[1];
						} else {
							hash += key + '=' + values[0] + '-' + values[1];
						}
					} else if ( 'date' === pwfActiveFilterItems[key]['fieldType'] ) {
						if ( pwfActiveFilterItems[key].hasOwnProperty('dateUrlKey') ) {
							hash += pwfActiveFilterItems[key]['dateUrlKey']['after'] + '=' + values[0] + '&' +  pwfActiveFilterItems[key]['dateUrlKey']['before'] + '=' + values[1];
						}
				    } else if ('search' === pwfActiveFilterItems[key]['fieldType'] &&  's' === key && pwffilterVariables.hasOwnProperty('add_posttype') && 'true' === pwffilterVariables.add_posttype ) {
						hash += key + '=' +  notices[0]['slug'] + '&post_type=product';
					} else {
						hash += key + '=';
						for ( let i = 0; i < notices.length; i++ ) {
							hash +=  notices[i]['slug'];
							if ( ( i + 1 ) < notices.length ) {
								hash += ','
							}
						}
					}
				}
			}
	
			let queryString = window.location.search;
			if ( '' === queryString && '' !== hash ) {
				hash = '?' + hash;
			} else {
				let urlParams  = new URLSearchParams(queryString);
				for ( let key in pwCurrentfFilterItems ) {
					let filter = pwCurrentfFilterItems[key];
					if ( 'priceslider' === filter['item_type'] ) {
						if ( urlParams.has( filter['url_key_min_price'] ) ) {
							urlParams.delete( filter['url_key_min_price'] );
						}
						if ( urlParams.has( filter['url_key_max_price'] ) ) {
							urlParams.delete( filter['url_key_max_price'] );
						}
						if ( urlParams.has( filter['url_key'] ) ) {
							urlParams.delete( filter['url_key'] );
						}
					} else if ( 'rangeslider' === filter['item_type'] ) {
						if ( urlParams.has( filter['url_key_range_slider_min'] ) ) {
							urlParams.delete( filter['url_key_range_slider_min'] );
						}
						if ( urlParams.has( filter['url_key_range_slider_max'] ) ) {
							urlParams.delete( filter['url_key_range_slider_max'] );
						}
						if ( urlParams.has( filter['url_key'] ) ) {
							urlParams.delete( filter['url_key'] );
						}
					} else if ( 'date' === filter['item_type'] ) {
						if ( urlParams.has( filter['url_key_date_after'] ) ) {
							urlParams.delete( filter['url_key_date_after'] );
						}
						if ( urlParams.has( filter['url_key_date_before'] ) ) {
							urlParams.delete( filter['url_key_date_before'] );
						}

					} else if ( 'search' === filter['item_type'] && 's' === filter['url_key'] && pwffilterVariables.hasOwnProperty('add_posttype') && 'true' === pwffilterVariables.add_posttype) {
						urlParams.delete( filter['url_key'] );
						if ( urlParams.has('post_type') ) {
							urlParams.delete( 'post_type' );
						}
					} else if ( urlParams.has( filter['url_key'] ) ) {
						urlParams.delete( filter['url_key'] );
					}
				}
	
				if ( urlParams.has('orderby') ) {
					urlParams.delete('orderby');
				}

				if ( urlParams.has('product-page') ) {
					urlParams.delete('product-page'); //product-page
				}
	
				if ( '' != urlParams.toString() ) {
					hash = urlParams.toString() + '&' + hash;
				}
	
				if ( '' !== hash ) {
					hash = '?' + hash;
					if ( hash.endsWith('&') ) {
						hash = hash.slice( 0, -1 );
					}
				}
			}
	
			pwfCurrentUrlQuery = hash;
	
			if ( ! $.isEmptyObject( pwfFilterAttributes ) ) {
				if ( false === hash.includes( 'orderby' ) && pwfFilterAttributes.hasOwnProperty('orderby') && '' !== pwfFilterAttributes.orderby ) {
					if ( '' === hash ) {
						hash = '?';
					} else {
						hash += '&';
					}

					hash = hash + 'orderby=' + pwfFilterAttributes.orderby;
				}
	
				if ( pwfFilterAttributes.hasOwnProperty('page') && '' !== pwfFilterAttributes.page ) {
					if ( pwfCustomization.enablePaginationHash() ) {
						if ( pwfIsShortcodeWoo  ) {
							if ( pwfFilterAttributes.page > 1 ) {
								if ( hash.startsWith('?') ) {
									hash = '&' + hash.substring(1);
								}
								hash = '?product-page=' + pwfFilterAttributes.page + hash;
							}
						} else {
							if ( pwfFilterAttributes.page > 1 ) {
								let slash = '/';
								if ( false === pwfIsURLHasSlash ) {
									slash = '';
								}
	
								hash = 'page/' + pwfFilterAttributes.page + slash + hash;
							}
						}
					}
				}
			}
	
			if ( history.pushState ) {
				let newurl =  pwfCurrentPageURL + hash ;
				//window.history.pushState( { path:newurl }, '', newurl ); // using browser history
				window.history.replaceState( { path:newurl }, '', newurl );
			}
		}
	};
	
	var HtmlEntities = function() {};
	HtmlEntities.map = {
		"'": "&apos;",
		"<": "&lt;",
		">": "&gt;",
		" ": "&nbsp;",
		"¡": "&iexcl;",
		"¢": "&cent;",
		"£": "&pound;",
		"¤": "&curren;",
		"¥": "&yen;",
		"¦": "&brvbar;",
		"§": "&sect;",
		"¨": "&uml;",
		"©": "&copy;",
		"ª": "&ordf;",
		"«": "&laquo;",
		"¬": "&not;",
		"®": "&reg;",
		"¯": "&macr;",
		"°": "&deg;",
		"±": "&plusmn;",
		"²": "&sup2;",
		"³": "&sup3;",
		"´": "&acute;",
		"µ": "&micro;",
		"¶": "&para;",
		"·": "&middot;",
		"¸": "&cedil;",
		"¹": "&sup1;",
		"º": "&ordm;",
		"»": "&raquo;",
		"¼": "&frac14;",
		"½": "&frac12;",
		"¾": "&frac34;",
		"¿": "&iquest;",
		"À": "&Agrave;",
		"Á": "&Aacute;",
		"Â": "&Acirc;",
		"Ã": "&Atilde;",
		"Ä": "&Auml;",
		"Å": "&Aring;",
		"Æ": "&AElig;",
		"Ç": "&Ccedil;",
		"È": "&Egrave;",
		"É": "&Eacute;",
		"Ê": "&Ecirc;",
		"Ë": "&Euml;",
		"Ì": "&Igrave;",
		"Í": "&Iacute;",
		"Î": "&Icirc;",
		"Ï": "&Iuml;",
		"Ð": "&ETH;",
		"Ñ": "&Ntilde;",
		"Ò": "&Ograve;",
		"Ó": "&Oacute;",
		"Ô": "&Ocirc;",
		"Õ": "&Otilde;",
		"Ö": "&Ouml;",
		"×": "&times;",
		"Ø": "&Oslash;",
		"Ù": "&Ugrave;",
		"Ú": "&Uacute;",
		"Û": "&Ucirc;",
		"Ü": "&Uuml;",
		"Ý": "&Yacute;",
		"Þ": "&THORN;",
		"ß": "&szlig;",
		"à": "&agrave;",
		"á": "&aacute;",
		"â": "&acirc;",
		"ã": "&atilde;",
		"ä": "&auml;",
		"å": "&aring;",
		"æ": "&aelig;",
		"ç": "&ccedil;",
		"è": "&egrave;",
		"é": "&eacute;",
		"ê": "&ecirc;",
		"ë": "&euml;",
		"ì": "&igrave;",
		"í": "&iacute;",
		"î": "&icirc;",
		"ï": "&iuml;",
		"ð": "&eth;",
		"ñ": "&ntilde;",
		"ò": "&ograve;",
		"ó": "&oacute;",
		"ô": "&ocirc;",
		"õ": "&otilde;",
		"ö": "&ouml;",
		"÷": "&divide;",
		"ø": "&oslash;",
		"ù": "&ugrave;",
		"ú": "&uacute;",
		"û": "&ucirc;",
		"ü": "&uuml;",
		"ý": "&yacute;",
		"þ": "&thorn;",
		"ÿ": "&yuml;",
		"Œ": "&OElig;",
		"œ": "&oelig;",
		"Š": "&Scaron;",
		"š": "&scaron;",
		"Ÿ": "&Yuml;",
		"ƒ": "&fnof;",
		"ˆ": "&circ;",
		"˜": "&tilde;",
		"Α": "&Alpha;",
		"Β": "&Beta;",
		"Γ": "&Gamma;",
		"Δ": "&Delta;",
		"Ε": "&Epsilon;",
		"Ζ": "&Zeta;",
		"Η": "&Eta;",
		"Θ": "&Theta;",
		"Ι": "&Iota;",
		"Κ": "&Kappa;",
		"Λ": "&Lambda;",
		"Μ": "&Mu;",
		"Ν": "&Nu;",
		"Ξ": "&Xi;",
		"Ο": "&Omicron;",
		"Π": "&Pi;",
		"Ρ": "&Rho;",
		"Σ": "&Sigma;",
		"Τ": "&Tau;",
		"Υ": "&Upsilon;",
		"Φ": "&Phi;",
		"Χ": "&Chi;",
		"Ψ": "&Psi;",
		"Ω": "&Omega;",
		"α": "&alpha;",
		"β": "&beta;",
		"γ": "&gamma;",
		"δ": "&delta;",
		"ε": "&epsilon;",
		"ζ": "&zeta;",
		"η": "&eta;",
		"θ": "&theta;",
		"ι": "&iota;",
		"κ": "&kappa;",
		"λ": "&lambda;",
		"μ": "&mu;",
		"ν": "&nu;",
		"ξ": "&xi;",
		"ο": "&omicron;",
		"π": "&pi;",
		"ρ": "&rho;",
		"ς": "&sigmaf;",
		"σ": "&sigma;",
		"τ": "&tau;",
		"υ": "&upsilon;",
		"φ": "&phi;",
		"χ": "&chi;",
		"ψ": "&psi;",
		"ω": "&omega;",
		"ϑ": "&thetasym;",
		"ϒ": "&Upsih;",
		"ϖ": "&piv;",
		"–": "&ndash;",
		"—": "&mdash;",
		"‘": "&lsquo;",
		"’": "&rsquo;",
		"‚": "&sbquo;",
		"“": "&ldquo;",
		"”": "&rdquo;",
		"„": "&bdquo;",
		"†": "&dagger;",
		"‡": "&Dagger;",
		"•": "&bull;",
		"…": "&hellip;",
		"‰": "&permil;",
		"′": "&prime;",
		"″": "&Prime;",
		"‹": "&lsaquo;",
		"›": "&rsaquo;",
		"‾": "&oline;",
		"⁄": "&frasl;",
		"€": "&euro;",
		"ℑ": "&image;",
		"℘": "&weierp;",
		"ℜ": "&real;",
		"™": "&trade;",
		"ℵ": "&alefsym;",
		"←": "&larr;",
		"↑": "&uarr;",
		"→": "&rarr;",
		"↓": "&darr;",
		"↔": "&harr;",
		"↵": "&crarr;",
		"⇐": "&lArr;",
		"⇑": "&UArr;",
		"⇒": "&rArr;",
		"⇓": "&dArr;",
		"⇔": "&hArr;",
		"∀": "&forall;",
		"∂": "&part;",
		"∃": "&exist;",
		"∅": "&empty;",
		"∇": "&nabla;",
		"∈": "&isin;",
		"∉": "&notin;",
		"∋": "&ni;",
		"∏": "&prod;",
		"∑": "&sum;",
		"−": "&minus;",
		"∗": "&lowast;",
		"√": "&radic;",
		"∝": "&prop;",
		"∞": "&infin;",
		"∠": "&ang;",
		"∧": "&and;",
		"∨": "&or;",
		"∩": "&cap;",
		"∪": "&cup;",
		"∫": "&int;",
		"∴": "&there4;",
		"∼": "&sim;",
		"≅": "&cong;",
		"≈": "&asymp;",
		"≠": "&ne;",
		"≡": "&equiv;",
		"≤": "&le;",
		"≥": "&ge;",
		"⊂": "&sub;",
		"⊃": "&sup;",
		"⊄": "&nsub;",
		"⊆": "&sube;",
		"⊇": "&supe;",
		"⊕": "&oplus;",
		"⊗": "&otimes;",
		"⊥": "&perp;",
		"⋅": "&sdot;",
		"⌈": "&lceil;",
		"⌉": "&rceil;",
		"⌊": "&lfloor;",
		"⌋": "&rfloor;",
		"⟨": "&lang;",
		"⟩": "&rang;",
		"◊": "&loz;",
		"♠": "&spades;",
		"♣": "&clubs;",
		"♥": "&hearts;",
		"♦": "&diams;"
	};
	HtmlEntities.decode = function(string) {
		var entityMap = HtmlEntities.map;
		for (var key in entityMap) {
			var entity = entityMap[key];
			var regex = new RegExp(entity, 'g');
			string = string.replace(regex, key);
		}
		string = string.replace(/&quot;/g, '"');
		string = string.replace(/&amp;/g, '&');
		return string;
	}
	HtmlEntities.encode = function(string) {
		var entityMap = HtmlEntities.map;
		string = string.replace(/&/g, '&amp;');
		string = string.replace(/"/g, '&quot;');
		for (var key in entityMap) {
			var entity = entityMap[key];
			var regex = new RegExp(key, 'g');
			string = string.replace(regex, entity);
		}
		return string;
	}
	
	pwfWooFilter.init();
}(jQuery));