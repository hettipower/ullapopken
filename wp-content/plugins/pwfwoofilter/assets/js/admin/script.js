(function( $ ) {
	"use strict";

	function checkboxTree ( param_name, options, checked_values) {
		let checkedValues = checked_values
		let strToNumuber  = function ( values_arr ) {
			let ValidateValues = [];
			if ( values_arr.length > 0 ) {
				values_arr.forEach( function( id ) {
					ValidateValues.push( parseInt( id ) );
				});
			}
	
			return ValidateValues;
		};
		let getCheckbox  = function ( term  ) {
			let checked = ( checkedValues.includes( parseInt( term.id ) ) ) ? ' checked' : '';
			let html    = '<input type="checkbox" class="form-control checkbox" name="' + param_name + '" value="'+ term.id +'"'+ checked +'>';
			html       += '<label for="'+ term.id +'">'+ term.text +'</label>';
			return html;
		};
		let createLiTree = function( options, depth = 0 ) {
			let str = ( depth === 0 ) ? '<ul class="list '+ param_name +'">' : '<ul class="children">';
			options.forEach( function( item ) {
				let isParent = false;
				if ( item.hasOwnProperty('subcat') && Array.isArray( item.subcat ) && item.subcat.length ) {
					isParent = true;
				}
				str += '<li>';
				str += getCheckbox(item);
				if ( isParent ) {
					str += createLiTree( item.subcat , 1 );
				}
				str += '</li>';
			});
			str += '</ul>';
			
			return str;
		};
	
		checkedValues = strToNumuber( checkedValues ); // important steps because save_values pharse as string
	
		return createLiTree( options );
	}
	
	/*
	 * Global variables
	 *
	 */
	var setting_data              = {}; // cached filter settings to save later in filter post
	var filterItemsData           = {}; // cached filter data to save later in filter post filterItemsData
	var cached_taxonomy           = {}; // cached taxonomy [ cat, tag, attributes, taxonmoy ] without hierarchy
	var cached_taxonomy_rules     = {}; // cached taxonmies come from ajax for display rule field
	var cached_hierarchy_taxonomy = {}; // cached taxonomy [ cat, tag, attributes, taxonmoy ] with hierarchy
	var cached_panel_template     = {}; // cached filters panel for faster display
	var pwfWooFilterData          = pro_woo_filter;
	var pwfFilterSettingFields    = pwfWooFilterData.setting_fields;
	var pwfTranslatedText         = pwfWooFilterData.translated_text

	/*
	 * Used to get type for input field
	 */
	$.fn.getType = function(){ 
		return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase();
	}

	/*
	 * used to animate html panel
	 */
	function pwfAnimateCSS(element, animationName, callback ) {
		const node = document.querySelector(element)
		node.classList.add('animated', animationName)
	
		function handleAnimationEnd() {
			node.classList.remove('animated', animationName)
			node.removeEventListener('animationend', handleAnimationEnd)
	
			if (typeof callback === 'function') callback()
		}
	
		node.addEventListener('animationend', handleAnimationEnd)
	}

	/*
	 * Used for required field
	 */ 
	function get_alert_message() {
		return '<div class="alert-danger" role="alert">' + pwfTranslatedText.required_field + '</div>';
	}

	function get_alert_message_unique_url() {
		return '<div class="alert-danger" role="alert">' + pwfTranslatedText.unique_field + '</div>';
	}

	function display_alert_message() {
		alert(pwfTranslatedText.alert_message);
	}

	function get_ajax_fail_message() {
		return '<span class="alert-message"><strong>Erorr: </strong>' + pwfTranslatedText.ajax_fail + '</span>';
	}

	function updateBrowserUrlLink( postID ) {
		if ( pwfWooFilterData.hasOwnProperty('edit_link') ) { 
			let edit_link = pwfWooFilterData.edit_link;
			edit_link = edit_link.replace('post_id', postID );
			window.location.href = edit_link;
		}
		return false;
	}
	
	/**
	 * return current panel filter item_panel or setting_panel
	 */
	function getCurrentEditPanel() {
		if ( $('.panel-group').find('.item-panel').length !== 0 ) {
			return 'item_panel';
		} else {
			return 'setting_panel';
		}
	}

	/**
	 * Check if url key is Unique
	 * used with these input url_key, url_key_min_price, url_key_max_price
	 *
	 * return Boolean
	 */
	var isUniqueUrlKey = {
		init: function( value, filterID ) {
			if ( $.isEmptyObject( filterItemsData ) ) {
				return true;
			} else {
				let urlKeys = isUniqueUrlKey.getUrlKeys( filterID );
				if ( urlKeys.includes( value ) ) {
					return false;
				} else {
					return true;
				}
			}
		},
		getUrlKeys: function( filterID ) {
			let savedkeys = [];
			const keys    = Object.keys(filterItemsData);
			for ( const key of keys ) {
				let filterItem = filterItemsData[key];
				if ( key !== filterID ) {
					savedkeys.push( filterItem['url_key'] );
					if ( filterItem.hasOwnProperty('url_key_min_price') ) {
						savedkeys.push( filterItem['url_key_min_price'] );
					}
					if ( filterItem.hasOwnProperty('url_key_max_price') ) {
						savedkeys.push( filterItem['url_key_max_price'] );
					}
					if ( filterItem.hasOwnProperty('url_key_date_after') ) {
						savedkeys.push( filterItem['url_key_date_after'] );
					}
					if ( filterItem.hasOwnProperty('url_key_date_before') ) {
						savedkeys.push( filterItem['url_key_date_before'] );
					}
					if ( filterItem.hasOwnProperty('url_key_range_slider_min') ) {
						savedkeys.push( filterItem['url_key_range_slider_min'] );
					}
					if ( filterItem.hasOwnProperty('url_key_range_slider_max') ) {
						savedkeys.push( filterItem['url_key_range_slider_max'] );
					}
				}
			}
			return savedkeys;
		}
	}

	function getFilterItemDataByID( filterID ) {
		/**
		 * Get panel data if exists
		 * check if filter id exists in filters data or inside column
		 * return data object or false
		 */
		return ( filterItemsData.hasOwnProperty( filterID ) ) ? filterItemsData[filterID] : false;
	}

	var pwfcurrentLoopKey = 0;
	var renderHTMLFilterItems = {
		//  Used to get unique filter item id
		generateFilterItemID: function() {
			let filtersNum = $('.filter-item').length;
			let id = 'id-' + ( filtersNum ) ;
			return id;
		},
		generateFilterItems: function( items ) {
			let html   = '';
			const keys = Object.keys(items);
			for ( const key of keys ) {
				let current = items[key];
				filterItemsData[pwfcurrentLoopKey] = current;
				if ( 'column' === current.item_type ) {
					html += renderHTMLFilterItems.newColumnStart(pwfcurrentLoopKey );
					if ( current.hasOwnProperty('children') ) {
						pwfcurrentLoopKey++;
						html += renderHTMLFilterItems.generateFilterItems( current['children'] );
					}
					html += renderHTMLFilterItems.newColumnEnd();
				} else {
					html += renderHTMLFilterItems.addNewItem( current.item_type, pwfcurrentLoopKey, current.title );
					pwfcurrentLoopKey++;
				}
			}

			return html;
		},
		createFilterItems: function( filterItems ) {
			// used it begining this edit filter page and has filter items
			let html   = renderHTMLFilterItems.generateFilterItems(filterItems);
			// re orgnization to be filterID => panel data
			renderHTMLFilterItems.appendItem(html);
		},
		addNewItem: function( ItemType, filterID, title = '' ) {
			let html = '';
			if ( '' === title ) {
				title = 'new filter';
			}
			html += '<div class="filter-item" data-filter-id="'+ filterID +'"><div class="filter-header">';
			html += '<div class="head-title"><span class="filter-title text-bold">';
			html += title;
			html += '</span><span class="filter-type">' + ItemType + '</span>';
			html += '</div>';
			html += '<div class="btn-actions">';
			html += '<span class="btn-link edit-action">' + pwfTranslatedText.edit + '</span>';
			html += '<span class="btn-link remove-action">' + pwfTranslatedText.remove + '</span>';
			html += '</div>';
			html += '</div></div>';
			return html;
		},
		newColumnStart: function( filterID ) {
			let html = '<div class="filter-item column" data-layout="column" data-filter-id="'+ filterID +'">';
			html += '<div class="filter-header">';
			html += '<div class="head-title"><span class="filter-title text-bold">';
			html += 'Column';
			html += '</div>';
			html += '<div class="btn-actions">';
			html += '<span class="btn-link edit-action">Edit</span>';
			html += '<span class="btn-link remove-action">Remove</span>';
			html += '</div>';
			html += '</div>'; // end header
			html += '<div class="container"><div class="column-inner group-list">';

			return html;
		},
		newColumnEnd: function() {
			let html = '</div></div></div>';
			return html;
		},
		addNewColumn: function( filterID, children = '' ) {
			let html = renderHTMLFilterItems.newColumnStart(filterID);
			html    += children;
			html    += renderHTMLFilterItems.newColumnEnd();
			return html;
		},
		removeItem: function( item ) {
			let filters_delete = [];
			let filter_id      = item.closest('.filter-item').attr('data-filter-id');
			filters_delete.push( filter_id );
			delete filterItemsData[filter_id];

			// check if its column layout
			let attrLayout = $(item).closest('.filter-item').attr('data-layout');
			if (typeof attrLayout !== typeof undefined && attrLayout !== false && 'column' === attrLayout ) {
				let children = $(item).closest('.filter-item').find('.column-inner').children('.filter-item');
				$( children ).each( function( index, child ) {
					let filterChildID =  $(child).attr('data-filter-id');
					filters_delete.push( filterChildID );
					delete filterItemsData[filterChildID];
				});
			}
			item.closest('.filter-item').remove();
			renderHTMLFilterItems.initSortEvent();
			pwfFilterItemPanel.displayPanel('removeitem', '', filters_delete);
		},
		appendItem: function( item ) {
			$('.append-filter-items').append(item);
			renderHTMLFilterItems.initSortEvent();
		},
		initSortEvent: function() {
			if ( $('.filters-list').hasClass('ui-sortable') ) {
				$('.filters-list').sortable("refresh");
			} else {
				$('.filters-list').sortable({
					handle: ".filter-header",
					connectWith: '.group-list',
					placeholder: 'ui-state-highlight',
					forcePlaceholderSize: true,
					opacity: 0.8,
					helper: 'clone',
					start: function (event, ui) {
						var width = $(this).width();
						$(ui.helper).width(width);
					}
				});
			}
			if ( $('.append-filter-items .group-list').length > 0 ) {
				$('.group-list').each( function() {
					if ( $(this).hasClass('ui-sortable') ) {
						$(this).sortable("refresh");
					} else {
						$(this).sortable({
							handle: '.filter-header',
							connectWith: '.filters-list',
							placeholder: 'ui-state-highlight',
							forcePlaceholderSize: true,
							opacity: 0.8,
							helper: 'clone',
							start: function (event, ui) {
								var width = $(this).width();
								$(ui.helper).width(width);
							}
						});
					}
				});
			}
		},
		addActiveCssClass: function( filterItemID ) {
			$('.filters-list').find('[data-filter-id='+ filterItemID +']').addClass('active-item');
		},
		removeActiveCssClass: function() {
			$('.filters-list .filter-item').removeClass('active-item');
		}
	};

	function panelfilterItemsTypes() {
		let html  = '';
		let title = pwfTranslatedText.select_filter_item;
		let items = [
			{
				panelLabel: pwfTranslatedText.field,
				panelItems: [
					{
						id:   'checkboxlist',
						text: pwfTranslatedText.checkboxlist,
					},
					{
						id:   'radiolist',
						text: pwfTranslatedText.radiolist,
					},
					{
						id:   'dropdownlist',
						text: pwfTranslatedText.dropdownlist,
					},
					{
						id:   'colorlist',
						text: pwfTranslatedText.colorlist,
					},
					{
						id:   'boxlist',
						text: pwfTranslatedText.boxlist,
					},
					{
						id:   'textlist',
						text: pwfTranslatedText.textlist,
					},
					{
						id:   'date',
						text: pwfTranslatedText.date,
					},
					{
						id:   'priceslider',
						text: pwfTranslatedText.priceslider,
					},
					{
						id:   'rangeslider',
						text: pwfTranslatedText.range_slider,
					},
					{
						id:   'rating',
						text: pwfTranslatedText.rating,
					},
					{
						id:   'search',
						text: pwfTranslatedText.search,
					},
					{
						id:   'button',
						text: pwfTranslatedText.button,
					}
				]
			},
			{
				panelLabel: pwfTranslatedText.preset,
				panelItems: [
					{
						id:   'productcategories',
						text: pwfTranslatedText.categories,
					},
					{
						id:   'stockstatus',
						text: pwfTranslatedText.stockstatus,
					}
				]
			},
			{
				panelLabel: pwfTranslatedText.layout,
				panelItems: [
					{
						id:   'column',
						text: pwfTranslatedText.column,
					}
				]
			},
		];

		html += '<div class="panel-item panel-filter-type postbox status-active-panel">';
		html += '<div class="panel-container">';
		html += '<div class="panel-header">';
		html += '<div class="wrap-title"><span class="link-back">< ' + pwfTranslatedText.btn_back + '</span><h2 class="panel-title">'+ title +'</h2></div>'; 
		html += '</div>';
		
		items.forEach( function( item ) {
			html += '<div class="pwf-items-list">';
			html += '<div class="pwf-items-lable">'+ item.panelLabel +'</div>';
			html += '<div class="pwf-items-content pwf-flex-content">';
			item.panelItems.forEach( function( field ) {
				html += '<div class="pwf-flex-item add-new-element" data-item-type="' + field.id + '"><div class="pwf-item-inner"><div class="filter-type-text">' + field.text + '</div></div></div>';
			});
			html += '</div>';
			html += '</div>';
		});

		html += '</div>';
		html += '</div>';

		return html;
	}
	
	var renderHTMLFormField = {
		config: {},
		init: function( config ) {
			renderHTMLFormField.config = {
				field:     '',
				panelType: '',
				FilterItemID: '',
				defaultValues: [], // saved_values
			};
			$.extend( renderHTMLFormField.config, config );
			
			let html = '';
			switch( renderHTMLFormField.config.field.type ) {
				case "text":
					html = renderHTMLFormField.inputTextField();
					break;
				case "number":
					html = renderHTMLFormField.inputTextNumber();
					break;
				case "radio":
					html = renderHTMLFormField.inputRadioButton();
					break;
				case "dropdown":
					html = renderHTMLFormField.dropdownMenuField();
					break;
				case "multicheckbox":
					html = renderHTMLFormField.multicheckboxField();
					break;
				case "switchbutton":
					html = renderHTMLFormField.switchButtonField();
					break;
				case "ajaxdropdown":
					html = renderHTMLFormField.ajaxdropdown();
					break;
				case "ajaxdropdownselect2":
					html = renderHTMLFormField.ajaxdropdownselect2();
					break;
				case "ajaxmulticheckboxlist":
					html = renderHTMLFormField.ajaxMultiCheckboxList();
					break;
				case "ajaxcolor":
					html = renderHTMLFormField.ajaxColorField();
					break;
				case "displayrules":
					html = renderHTMLFormField.displayRulesField.displayRules( renderHTMLFormField.config.field, renderHTMLFormField.config.defaultValues);
					break;
				case "metafield":
					html = renderHTMLFormField.metaField();
					break;
				case "boxlistlabel":
					html = renderHTMLFormField.boxlistlabel();
					break;
			}
			return html;
		},
		fieldWrapStart: function( field = '' ) {
			if ( '' === field ) {
				field = renderHTMLFormField.config.field;
			}

			let html = '<div class="control-group';
			html    += ( 'radio' === field.type ) ? ' inline-radio-buttons' : '';
			html    += ( 'ajaxcolor' === field.type ) ? ' no-padding' : '';
			html    += '">';

			html += '<div class="control-label">';
			html += '<span class="label-text">';
			html += field.title;

			if ( field.hasOwnProperty('required') && 'true' === field.required ) {
				html += '<abbr class="required" title="required">*</abbr>';
			}
			html += '</span>';

			if ( field.hasOwnProperty('description') && '' !== field.description ) {
				html += '<span class="description">' + field.description + '</span>';
			}
			html += '</div>';

			html += '<div class="control-content">';

			return html;
		},
		fieldWrapEnd: function() {
			return '</div></div>';
		},
		inputTextField: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;

			let html = '<input type="text" name="' + field.param_name + '"';
			html    += ' class="form-control full-width text';

			if ( field.hasOwnProperty('cssclass') && '' !== field.cssclass ) {
				html += ' ' + field.cssclass;
			}
			html += '"';

			if ( defaultValues.hasOwnProperty( field.param_name ) ) {
				html += ' value="' + defaultValues[field.param_name] + '"';
			} else if ( field.hasOwnProperty('default') ) {
				html += ' value="' + field.default + '"';
			} else {
				html += ' value=""';
			}

			if ( field.hasOwnProperty('placeholder') && '' !== field.placeholder ) {
				html += ' placeholder="' + field.placeholder + '"';
			}

			if ( field.hasOwnProperty('required') && 'true' === field.required ) {
				html += ' aria-required="true"';
			}
		
			html += '/>';

			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		inputTextNumber: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;

			let html = '<input type="number" name="' + field.param_name + '"';
			html    += ' class="form-control number';

			if ( field.hasOwnProperty('cssclass') && '' !== field.cssclass ) {
				html += ' ' + field.cssclass;
			}
			html += '"';

			if ( defaultValues.hasOwnProperty( field.param_name ) ) {
				html += ' value="' + defaultValues[ field.param_name ] + '"';
			} else if ( field.hasOwnProperty('default') ) {
				html += ' value="' + field.default + '"';
			} else {
				html += ' value=""';
			}

			if ( field.hasOwnProperty('required') && 'true' === field.required ) {
				html += ' aria-required="true"';
			}
			if ( field.hasOwnProperty('min')  ) {
				html += ' min="' + field.min + '"';
			}
			if ( field.hasOwnProperty('max')  ) {
				html += ' max="' + field.max + '"';
			}
			
			html += '/>';

			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		inputRadioButton: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;
			let html          = '';
			let checked       = '';
			let checked_value = ( defaultValues.hasOwnProperty( field.param_name ) ) ? defaultValues[ field.param_name ] : field.default;
			let display_list  = false;

			if ( field.hasOwnProperty('display') && 'list' === field.display ) {
				display_list = true;
				html += '<ul class="radio-list ' + field.param_name + '">';
			}

			let options = field.options;
			options.forEach( function( current_term ) {
				checked = '';
				if( checked_value === current_term.id ) {
					checked = ' checked';
				}
				if ( display_list ) {
					html += '<li>';
				}
				html += '<span class="pwf-radio-item"><input type="radio" name="' + field.param_name + '" value="'+ current_term.id +'"'+ checked +'> <label for="'+ current_term.id +'">'+ current_term.text +'</label></span>';
				if ( display_list ) {
					html += '</li>';
				}
			});

			if ( display_list ) {
				html += '</ul>';
			}

			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		dropdownMenuField: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;
			let html          = '';
			let checked_value = '';

			if ( defaultValues.hasOwnProperty( field.param_name ) ) {
				checked_value = defaultValues[ field.param_name ];
				
			} else if ( field.hasOwnProperty('default') ) {
				checked_value = field.default;
			}
			
			html += '<select name="' + field.param_name + '"';
			html += ' class="form-control full-width dropdown';
			if ( field.hasOwnProperty('cssclass') && '' !== field.cssclass ) {
				html += ' ' + field.cssclass;
			}
			if ( $.isEmptyObject( field.options ) ) {
				// If there are no attributes or taxonomies
				html += ' pwf-hidden';
			}
			html += '"';

			if ( ! $.isEmptyObject( field.options ) && field.hasOwnProperty('required') && 'true' === field.required ) {
				html += ' aria-required="true"';
			}

			html += '>';

			let options = field.options;
			options.forEach( function( current_term ) {
				html += '<option value ="' + current_term.id + '"';
				html += ( current_term.id == checked_value ) ? ' selected' : '';
				html += '>' + current_term.text + '</option>';
			});
	
			html += '</select>';

			if ( $.isEmptyObject( field.options ) ) {
				html += '<ul><li>'+ pwfTranslatedText.no_option +'</li></ul>';
			}
			
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		multicheckboxField: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;
			let checked       = '';
			let checked_value = [];
	
			let html = '<div class="check-box-list"><ul class="list '+ field.param_name +'">';
	
			if (  defaultValues.hasOwnProperty( field.param_name ) && '' !== defaultValues[field.param_name] ) {
				checked_value = defaultValues[ field.param_name ];
			} else if ( field.hasOwnProperty('default') ) {
				checked_value = field.default;
			}
	
			let options = field.options;
			options.forEach( function( current_term ) {
				checked = ( checked_value.includes( current_term.id ) ) ? ' checked' : '';

				html += '<li><input type="checkbox" class="form-control checkbox" name="' + field.param_name + '" value="' + current_term.id + '"' + checked;
				if ( field.hasOwnProperty('required') && 'true' === field.required ) {
					html += ' aria-required="true"';
				}
				html += '> <label for="' + current_term.id + '">' + current_term.text + '</label></li>';
			});
			
			html += '</ul></div>';
			
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		switchButtonField: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;
			let html          = '';
			let checked       = '';

			if ( defaultValues.hasOwnProperty( field.param_name ) ) {
				checked = ( 'on' === defaultValues[ field.param_name ] ) ? ' checked' : '';
			} else if ( field.hasOwnProperty('default') && field.default === 'on' ) {
				checked = ' checked';
			}

			html += '<div class="ckbx-style-15 switch-btn">';
			html += '<input id="pwf-' + field.param_name + '" type="checkbox" name="' + field.param_name + '" class="form-control checkbox-switch-field';
			if ( field.hasOwnProperty('cssclass') && '' !== field.cssclass ) {
				html += ' ' + field.cssclass;
			}
			html += '"' + checked + '><label for="pwf-' + field.param_name + '" class="slider-btn"></label></div>';
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		ajaxdropdown: function() {
			let field = renderHTMLFormField.config.field;
			let html  = '<div class="dropdown-list ajax-dropdown-field-' + field.param_name + '"></div>';
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		ajaxdropdownselect2: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;
			let html          = '';
			let checked_value = [];
			if (  defaultValues.hasOwnProperty( field.param_name ) && '' !== defaultValues[field.param_name] ) {
				checked_value = defaultValues[ field.param_name ];
			} else if ( field.hasOwnProperty('default') ) {
				checked_value = field.default;
			}
			
			html += '<select name="' + field.param_name + '"';
			html += ' class="form-control full-width dropdown pwf-select2';
			if ( field.hasOwnProperty('cssclass') && '' !== field.cssclass ) {
				html += ' ' + field.cssclass;
			}
			if ( $.isEmptyObject( field.options ) ) {
				// If there are no attributes or taxonomies
				html += ' pwf-hidden';
			}
			html += '"';

			if ( ! $.isEmptyObject( field.options ) && field.hasOwnProperty('required') && 'true' === field.required ) {
				html += ' aria-required="true"';
			}

			html += ' multiple="multiple">';

			let options = field.options;
			options.forEach( function( current_term ) {
				html += '<option value ="' + current_term.id + '"';
				html += ( checked_value.includes( current_term.id ) ) ? ' selected' : '';
				html += '>' + current_term.text + '</option>';
			});
			html += '</select>';

			if ( $.isEmptyObject( field.options ) ) {
				html += '<ul><li>'+ pwfTranslatedText.no_option +'</li></ul>';
			}
			
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		ajaxMultiCheckboxList: function() {
			let field = renderHTMLFormField.config.field;
			let html  = '<div class="check-box-list ajax-field-' + field.param_name + '"></div>';
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		ajaxColorField: function() {
			let field = renderHTMLFormField.config.field;
			let html  = '<div class="color-list ' + field.param_name + '"></div>';
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		metaField: function() {
			let field         = renderHTMLFormField.config.field;
			let defaultValues = renderHTMLFormField.config.defaultValues;

			let html = '<div class="check-box-list pwf-meta-field-box">';
			html += '<div class="inner-meta-field">';
			html += '<div id="meta-field-items" class="meta-field-items">';
			if ( defaultValues.hasOwnProperty( field.param_name ) && '' !== defaultValues[ field.param_name ] ) {
				html += metaFields.createMetaFields( renderHTMLFormField.config.panelType, defaultValues[ field.param_name ] );
			}
			html += '</div>';
			html += '</div>'; // end of inner
			html += '<div class="meta-fields-button"><button class="button pwf-add-new-meta">' + pwfTranslatedText.add_new + '</button></div>';
			html += '</div>';
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		boxlistlabel: function() {
			let html = '<div class="pwf-box-list-label box-list-label-require-ajax"></div>';				
			return renderHTMLFormField.fieldWrapStart() + html + renderHTMLFormField.fieldWrapEnd();
		},
		ajaxdropdownfield: function( taxonomies, savedValues ) {
			let html = '';
			html += '<select name="item_source_taxonomy_sub"';
			html += ' class="form-control full-width dropdown"';			
			html += '>';
	
			taxonomies.forEach( function( term ) {
				html += '<option value ="' + term.id + '"';
				html += ( term.id == savedValues ) ? ' selected' : '';
				html += '>' + term.text + '</option>';
			});
			html += '</select>';
			return html;
		},
		/**
		 * generate custom labels for taxonomies on panel boxlist
		 * @param array taxonomies 
		 * @param array savedValues 
		 */
		boxlistlabelfields: function( taxonomies, savedValues ) {
			//let param_name = 'boxlistlabel';
			let html       = '';
			taxonomies.forEach( function( item ) {
				let value  = '';
				if ( Array.isArray( savedValues ) && savedValues.length ) {
					let obj = savedValues.find( element => element.term_id == item.id );
					if ( obj ) {
						value  = obj.value;
					}
				}

				html += '<div class="lable-item" data-item-label-id="'+item.id+'">';
				html += '<div class="lable-item-text">'+ item.text +'</div>';
				html += '<div class="lable-item-input"><input type="text" class="form-control full-width text item-value" value="' + value + '" /></div>';
				html += '</div>';

			});

			return html;
		},
		displayRulesField: {
			displayRules: function( field, saved_values ) {
				let html = '';
				html += '<div><strong>'+ pwfTranslatedText.rule_text_if +'</strong></div>';
				html += '<div class="display-rules-container">';
				
				if (  saved_values.hasOwnProperty( field.param_name ) ) {
					saved_values = saved_values[ field.param_name ];
				}
				if ( Array.isArray( saved_values ) && saved_values.length ) {
					// there is a rules before
					let hidden_rules = saved_values;
					
					let index_rule  = 0;
					html += renderHTMLFormField.displayRulesField.ruleWrapStart();
					hidden_rules.forEach( function( rule ) {
						var data = {
							'rule_id':  index_rule,
							'param':    rule.param,
							'equal':    rule.equal,
							'value':    rule.value,
						};
						html += renderHTMLFormField.displayRulesField.ruleTemplate( data );
						index_rule++;						
					});
					html+= renderHTMLFormField.displayRulesField.ruleWrapEnd();
				} else {
					// new rule
					html += renderHTMLFormField.displayRulesField.ruleWrapStart();
					var data = {
						'rule_id': 0,
						'param':   'category',
						'equal':   '',
						'value':   '',
					}
					html += renderHTMLFormField.displayRulesField.ruleTemplate (data );
					html+= renderHTMLFormField.displayRulesField.ruleWrapEnd();
				}
				html += '</div>';

				return renderHTMLFormField.fieldWrapStart(field) + html + renderHTMLFormField.fieldWrapEnd();
			},
			ruleTemplate: function( data ) {
				/*
				* data is object
				* contain data.rule_id, data.group_id, data.param, data.equal, data.value
				*/
				let html          = '';
				let param_data    = pwfWooFilterData.display_rule_data.param;
				let customCSS     = '';
				let addSelectData = ''; // used after panel open and load ajax

				if ( 'category' !== data.param && ! cached_taxonomy_rules.hasOwnProperty( data.param ) ) {
					addSelectData = ' data-select2-ajax-param="' + data.param + '" data-select2-ajax-value="'+ data.value  +'"';
					customCSS     = ' transparent';
				}

				html += '<div class="rule pwf-rule five-col-grid'+ customCSS +'" data-rule-id="' + data.rule_id + '">';

				html += '<div class="rule-item rule-param">';
				html += '<select name="rule[rule_' + data.rule_id + '][param]" class="form-control full-width field-rule-param">';
				param_data.forEach( function( current_term ) {
					html += '<option value ="' + current_term.id + '"';
					html += ( current_term.id === data.param ) ? ' selected' : '';
					html += '>' + current_term.text + '</option>';
				});
				html += '</select>';
				html += '</div>';

				html += '<div class="rule-item rule-equal">';
				html += '<span>'+ pwfTranslatedText.equalto +'</span>';
				html += '</div>';

				html += '<div class="rule-item rule-value">';

				html += '<select name="rule[rule_' + data.rule_id + '][value]" class="form-control full-width field-rule-value-select2"'+ addSelectData +'>';
				if ( 'category' === data.param ) {
					let product_categoris = pwfWooFilterData.product_categories;

					product_categoris.forEach( function( current_term ) {
						html += '<option value ="' + current_term.id + '"';
						html += ( current_term.id === data.value ) ? ' selected' : '';
						html += '>' + current_term.text + '</option>';
					});
				} else {
					if ( cached_taxonomy_rules.hasOwnProperty( data.param ) ) {
						let taxonomies = cached_taxonomy_rules[data.param];
						html += renderHTMLFormField.displayRulesField.ruleOptgroup( taxonomies, data.value );
					}
				}
				html += '</select>';
				html += '</div>';

				html += '<div class="rule-item rule-add">';
				html += '<button class="button add-rule">'+ pwfTranslatedText.rule_text_or + '</button>';
				html += '</div>';

				html += '<div class="rule-item rule-delete">';
				if ( ! ( data.rule_id === 0 ) ) {
					html += '<button class="button button-link-delete remove-rule">-</button>';
				}
				html += '</div>';
				html += '</div>'; // End of rule
				return html;
			},
			ruleWrapStart: function() {
				return '<div class="rule-group">';
			},
			ruleWrapEnd: function() {
				return '</div>';
			},
			ruleOptgroup: function( taxonomies, value ) {
				let html = '';
				//error.l
				taxonomies.forEach( function( current_term ) {
					if ( current_term.hasOwnProperty('children') ) {
						html += '<optgroup label="'+ current_term.text +'">';
						let children = current_term.children;
						children.forEach( function( child ) {
							html += '<option value ="' + child.id + '"';
							html += ( child.id == value ) ? ' selected' : '';
							html += '>' + child.text + '</option>';
						});
						html += '</optgroup>';
					} else {
						html += '<option value ="' + current_term.id + '"';
						html += ( current_term.id == value ) ? ' selected' : '';
						html += '>' + current_term.text + '</option>';
					}
				});
				return html;
			},
			addNewRule: function( btnclicked ) {
				let last_rule_id = $(btnclicked).closest('.rule').attr('data-rule-id');
				let next_rule_id = Number(last_rule_id) + 1 ;
				let data = {
					'rule_id': next_rule_id,
					'param': 'category',
					'equal': '',
					'value': '',
				};

				let html = renderHTMLFormField.displayRulesField.ruleTemplate(data);
				$(btnclicked).closest('.rule-group').append(html);
				panelEvents.initEventRuleDisplayField();
			},
			removeRule: function( btnclicked ) {
				let current_rule = $(btnclicked).closest('.rule');
				$(current_rule).remove();
			}
		},
		colorFields: function( param_name, data, savedValues ) {
			let html = '';
			data.forEach( function( item ) {
				let color       = '';
				let image       = '';
				let type        = 'color';
				let bordercolor = '';
				let marker      = 'light';

				if ( Array.isArray( savedValues ) && savedValues.length ) {
					let obj = savedValues.find( element => element.term_id == item.id );
					if ( obj ) {
						color       = obj.color;
						image       = obj.image;
						type        = obj.type;
						bordercolor = obj.bordercolor;
						marker      = obj.marker;
					}
				}
				
				html += '<div class="color-item" term-id="' + item.id + '">';
				html += '<div class="color-inner">';

				html += '<div class="color-term-header">';
				let background_color = ( 'color' === type && '' !== color ) ? ' style=background-color:'+ color : '';
				html += '<div class="preview-term-container"><div class="preview-holder"' + background_color + '>';

				if ( 'image' === type && '' !== image ) {
					html += '<img class="preview-image" src="' + image + '">';
				}
				html += '</div></div>';
				html += '<div class="term-name-container"><span class="term-name">' + item.text + '</span></div>';
				html += '</div>';

				html += '<div class="color-term-content">';

				html += '<div class="inner-fields">';

				let css_hidden = ( type === 'color' ) ? '': ' hidden';
				html += '<div class="container list-container">';
				html += '<div class="color-container'+ css_hidden +'">';
				html += '<div class="header"><span class="text">' + pwfTranslatedText.color + '</span></div>';
				html += '<div class="content">';
				html += '<input type="text" name="color-' + item.id + '" class="color-field" value="' + color + '"/>';
				html += '</div>';
				html += '</div>';
				css_hidden = ( type === 'image' ) ? '': ' hidden';
				html += '<div class="image-container'+ css_hidden +'">';
				html += '<div class="header"><span class="text">' + pwfTranslatedText.image + '</span></div>';
				html += '<div class="content">';
				html += '<input type="text" name="image' + item.id + '" class="text hidden image-field" value="' + image + '"/>';
				html += '<button class="button btn-upload-image">' + pwfTranslatedText.upload_image + '</button>';
				html += '</div>';
				html += '</div>';
				html += '</div>';

				html += '<div class="type-container list-container">';
				html += '<div class="header"><span class="text">' + pwfTranslatedText.type + '</span></div>';
				html += '<div class="content"><div class="radio-as-button">';


				let checked = ( type === 'color' ) ? ' checked': '';
				html += '<span class="field-type"><input type="radio" class="type-field" id="color-' + item.id + '" name="type' + item.id + '" value="color"'+ checked + '/> <label for="color">' + pwfTranslatedText.color + '</label></span>';
				checked = ( type === 'image' ) ? ' checked': '';
				html += '<span class="field-type"><input type="radio" class="type-field" id="thumb-' + item.id + '" name="type' + item.id + '" value="image"'+ checked + '/> <label for="image">' + pwfTranslatedText.image + '</label></span>';
				html += '</div></div>';
				html += '</div>';

				html += '</div>';

				html += '<div class="inner-fields">';

				html += '<div class="border-container list-container">';
				html += '<div class="header"><span class="text">' + pwfTranslatedText.border + '</span></div>';
				html += '<div class="content"><div class="wrap-color">';
				html += '<input type="text" class="bordercolor-field" name="bordercolor' + item.id + '" class="color-field" value="'+ bordercolor + '"/>';
				html += '</div></div>';
				html += '</div>';

				html += '<div class="marker-container list-container">';
				html += '<div class="header"><span class="text">' + pwfTranslatedText.marker + '</span></div>';
				html += '<div class="content"><div class="radio-as-button">';
				checked = ( marker === 'light' ) ? ' checked': '';
				html += '<span class="field-type"><input type="radio" class="marker-field" id="markerlight-' + item.id + '" name="marker' + item.id + '" value="light"'+ checked +'/> <label lass="field-type-text">' + pwfTranslatedText.marker_light + '</label></span>';
				checked = ( marker === 'dark' ) ? ' checked': '';
				html += '<span class="field-type"><input type="radio" class="marker-field" id="markerdark-' + item.id + '" name="marker' + item.id + '" value="dark"'+ checked +'/> <label lass="field-type-text">' + pwfTranslatedText.marker_dark + '</label></span>';
				html += '</div></div>';
				html += '</div>';

				html += '</div>';
				html += '</div>'; // End color-inner
				html += '</div>'; // End color-item
				html += '</div>';
			});
			return html;
		}
	}

	var pwfFilterItemPanel = {
		config: {},
		init: function( config ) {
			// each time initial config
			pwfFilterItemPanel.config = {
				panelType:    '',
				FilterItemID: '',
				defaultValues: [],
				requireValidate: 'false',
				displayVisualTab: false,
				predefinedPanel: '', // used with predefined panel stock status category
			};
			$.extend( pwfFilterItemPanel.config, config );
			let html = pwfFilterItemPanel.panelTemplate();
			return html;
		},
		/**
		 * Return panel fields depend on panel type
		 * like inpput, checkbox, ...
		 * see class-prowoo-filter-metabox-fields.php
		 */
		getPanelFields: function( panelType = '' ) {
			let general   = [];
			let visual    = [];
			let fields    = pro_woo_filter.panel_feilds;

			if ( '' === panelType ) {
				panelType = pwfFilterItemPanel.config.panelType;
			}

			fields.forEach( function( field ) {
				if ( $.inArray( panelType, field.panel ) != -1 ) {
					
					if ( field.group === 'general' ) {
						general.push( field );
					} else if ( field.group === 'visual' ) {
						visual.push( field );
					}
				}
			});
			let data = {
				'general': general,
				'visual':   visual,
			};
			
			return data;
		},
		panelTemplate: function() {
			let html               = '';
			let item_fields        = pwfFilterItemPanel.getPanelFields();
			let general_fields     = item_fields['general'];
			let visual_fields      = item_fields['visual'];
			if ( Array.isArray( visual_fields ) && visual_fields.length ) {
				pwfFilterItemPanel.config.displayVisualTab = true;
			}

			let config = {
				panelType: pwfFilterItemPanel.config.panelType,
				defaultValues: pwfFilterItemPanel.config.defaultValues, // saved_values
			}

			html += pwfFilterItemPanel.panelHeader();
			html += pwfFilterItemPanel.tabStartGeneral();
			general_fields.forEach( function( field ) {
				config['field'] = field;
				config['defaultValues'] = pwfFilterItemPanel.config.defaultValues;

				if ( config.defaultValues.length < 1 ) {
					if ( 'radiolist' === pwfFilterItemPanel.config.panelType && 'stockstatus' === pwfFilterItemPanel.config.predefinedPanel ) {
						if ( 'title' === field.param_name ) {
							config.defaultValues = { title: 'Stock status' };
						}
						if ( 'url_key' === field.param_name ) {
							config.defaultValues = { url_key: 'stock-status' };
						}
						if ( 'source_of_options' === field.param_name ) {
							config.defaultValues = { source_of_options: 'stock_status' };
						}
					}

					if ( 'checkboxlist' === pwfFilterItemPanel.config.panelType && 'productcategories' === pwfFilterItemPanel.config.predefinedPanel ) {
						
						if ( 'title' === field.param_name ) {
							config.defaultValues = { title: 'Product categories' };
						}
						if ( 'url_key' === field.param_name ) {
							config.defaultValues = { url_key: 'product-category' };
						}
					}

					if ( 'priceslider' ===  pwfFilterItemPanel.config.panelType && 'url_key' === field.param_name ) {
						config.defaultValues = { url_key: 'price' };
					}

					if ( 'search' ===  pwfFilterItemPanel.config.panelType ) {
						config.defaultValues = { url_key: 's', title: 'Search' };
					}

					if ( 'rangeslider' ===  pwfFilterItemPanel.config.panelType ) {
						if (  'source_of_options' === config['field']['param_name'] ) {
							// init first time - Change default value from category to attributes 
							config['field']['default'] = 'attribute';
						}
					}
				}

				html += renderHTMLFormField.init( config );
			});
			html += pwfFilterItemPanel.tabEndWrap();
			
			if ( pwfFilterItemPanel.config.displayVisualTab ) {
				html += pwfFilterItemPanel.tabStartVisual();
				visual_fields.forEach( function( field ) {
					config['field'] = field;
					html += renderHTMLFormField.init(config);
				});
				html += pwfFilterItemPanel.tabEndWrap();
			}

			html += pwfFilterItemPanel.panelFooter();

			return html;
		},
		panelHeader: function() {
			let html = '';
			html += '<div class="panel-item item-panel postbox status-active-panel panel-' + pwfFilterItemPanel.config.panelType + 
			        '" data-filter-panel-id="'+ pwfFilterItemPanel.config.FilterItemID +'" data-required-validate="' + 
			        pwfFilterItemPanel.config.requireValidate +'" data-panel-type="'+ pwfFilterItemPanel.config.panelType +'">';
			html += '<div class="panel-container">';

			html += '<div class="panel-header">';
			html += '<div class="wrap-title"><h2 class="panel-title"><span class="link-back">< ' + pwfTranslatedText.btn_back + '</span>' +
			        pwfFilterItemPanel.config.panelType + '</h2></div>';

			html += '<div class="tabs-nav">';
			html += '<span class="nav-tab-heading nav-general active-tab" data-tab-id="general">' + pwfTranslatedText.general + '</span>';
			if ( pwfFilterItemPanel.config.displayVisualTab ) {
				html += '<span class="nav-tab-heading nav-selectors" data-tab-id="visual">' + pwfTranslatedText.visual + '</span>';
			}
			html += '</div>';
			html += '</div>'; // end panel header

			html += '<div class="inside">';
			html += '<form class="option-panel-form">';
			html += '<div class="wrap-tabs">';

			return html;
		},
		panelFooter: function() {
			let html = '';
			
			let disableApplyBtn = ( 'false' === pwfFilterItemPanel.config.requireValidate ) ? ' disabled' : ''

			html  = '</div></form>'; // end of wrap tabs
			html += '<div class="panel-nav">';
			html += '<button class="button reset-panel-button">' + pwfTranslatedText.btn_reset + '</button>';
			html += '<button class="button apply-panel-button"' + disableApplyBtn + '>' + pwfTranslatedText.btn_apply + '</button>';
			html += '</div>'; // end of panel nav
			html += '</div>';
			html += '</div>'; // panel-container
			html += '</div>'; // panel-item
			return html;
		},
		tabStartGeneral: function() {
			return '<div class="tab-content active-tab" data-tab-id="general">';
		},
		tabStartVisual: function() {
			return '<div class="tab-content" data-tab-id="visual">';
		},
		tabEndWrap: function() {
			return '</div>';
		},
		editPanel: function( filterItem ) {
			if ( processingPanelForm.processingPanel() ) {
				let template     = ''; 
				let saved_values = [];
				let filterID     = $(filterItem).closest('.filter-item').attr('data-filter-id');
				
				let filter       = getFilterItemDataByID( filterID );
				if ( filter ) {
					saved_values = filter;
					if ( cached_panel_template.hasOwnProperty( filterID ) ) {
						template = cached_panel_template[filterID];
					} else {
						let args = {
							panelType:    saved_values.item_type,
							FilterItemID: filterID,
							defaultValues: saved_values,
							requireValidate: 'false',
							predefinedPanel: '',
						};
						template = pwfFilterItemPanel.init( args );
					}
					pwfFilterItemPanel.displayPanel('edititem', template, filterID);
				}
			}
		},
		apply_panel_button: function() {
			processingPanelForm.processingPanel();
		},
		backLinkButton: function() {
			pwfFilterItemPanel.displayPanel('backfromeditpanel');
		},
		resetPanel: function() {
			let currentPanel = getCurrentEditPanel();
			if ( currentPanel === 'item_panel' ) {
				let type            = $('.panel-group .item-panel').attr('data-panel-type');
				let filter_id       = $('.panel-group .item-panel').attr('data-filter-panel-id');
				let item_fields     = pwfFilterItemPanel.getPanelFields( type );
				let general_fields  = item_fields['general'];
				let visual_fields   = item_fields['visual'];
				let all_fields      =  $.merge(general_fields, visual_fields);;
				
				all_fields.forEach( function( field ) {
					if ( 'multicheckbox' === field.type || 'ajaxmulticheckboxlist' === field.type ) {
						var checked_value = field.default;
						$( field.options ).each( function( index, current_term ) {
							$('[name='+ field.param_name +']').prop('checked', false);
							if( $.inArray( current_term.id, checked_value ) != -1 ) {
								$('[name="'+ field.param_name +'"]').prop('checked', true).trigger('change');
							}
						});
					} else if ( 'switchbutton' === field.type ) {
						$('[name='+ field.param_name +']').prop('checked', false);
						if ( field.hasOwnProperty('default') && field.default === 'on' ) {
							$('[name="'+ field.param_name +'"]').prop('checked', true).trigger('change');
						}
					} else if ( 'radio' === field.type ) {
						$('[name='+ field.param_name +']').checked = false;
						$( field.options ).each( function( index, current_term ) {					
							if( field.default == current_term.id ) {
								$('[name="'+ field.param_name +'"][value="' + current_term.id + '"]').prop('checked', true).trigger('change');
							}
						});
					} else {
						$('.option-panel-form [name="'+ field.param_name +'"]').val(field.default).trigger('change');
					}
				});
				$('.filter-items-panel').find('[data-filter-id=' + filter_id +']').find('.filter-title').text(all_fields.title);
				$('ul.exclude').closest('.control-group').slideUp('fast');
				$('ul.include').closest('.control-group').slideUp('fast');
				processingPanelForm.addAttributeRequiredValidate( currentPanel, 'true');
			} else {
				// settings panel change
				var all_fields = $('.setting-panel-form :input');
				$(all_fields).each( function( index, currentField ) {
					// text, checkbox dropdown text checkbox-switch-field
					if( $(currentField).hasClass('text') ) {
						$(currentField).val($(currentField).attr('data-default-value'));
					} else if ( $(currentField).hasClass('checkbox') ) {
						if ( Array.isArray( currentField ) && currentField.length ) {
							
							$(currentField).each( function( index, current) {
								if( 'checked' === $(current).attr('data-default-value') ) {
									$(current).prop('checked', true );
								} else {
									$(current).prop('checked', false );
								}
							});
						} else {
							if( 'checked' === $(currentField).attr('data-default-value') ) {
								$(currentField).prop('checked', true );
							} else {
								$(currentField).prop('checked', false );
							}
						}
					} else if ( $(currentField).hasClass('dropdown') ) {
						$(currentField).val($(currentField).attr('data-default-value'));
					} else if ( $(currentField).hasClass('checkbox-switch-field') ) {
						if( 'on' === $(currentField).attr('data-default-value') ) {
							$(currentField).prop('checked', true );
						} else {
							$(currentField).prop('checked', false );
						}
					}
				});
				processingPanelForm.addAttributeRequiredValidate( currentPanel, 'true');
			}
		},
		displayPanel: function( action, template = '', filter_id, li_content ) {
			let outAnimation = 'slideOutRight';
			let inAnimation  = 'slideInRight';
			if ( $('body').hasClass('rtl') ) {
				outAnimation = 'slideOutLeft';
				inAnimation  = 'slideInLeft';
			}
			if ( 'backfromfiltertypepanel' === action ) {
				renderHTMLFilterItems.removeActiveCssClass();
				pwfAnimateCSS('.status-active-panel', outAnimation, function() {
					$('.panel-group').find('.status-active-panel').remove();

					$('.panel-group').find('.status-unactive-panel').removeClass('status-unactive-panel').addClass('status-active-panel');
					pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
				});
			} else if ( 'backfromeditpanel' === action ) {
				if ( processingPanelForm.processingPanel() ) {
					renderHTMLFilterItems.removeActiveCssClass();
					pwfAnimateCSS('.status-active-panel', outAnimation, function() { 
						$('.panel-group').find('.status-active-panel').remove();
						$('.panel-group').find('.status-unactive-panel').addClass('status-active-panel').removeClass('status-unactive-panel');
						pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
					});
				}
			}  else if ( 'removeitem' === action ) {
				// filter_id here is array
				let panelID = '';
				
				if (  Array.isArray( filter_id ) && filter_id.length ) {
					for ( let i = 0; i < filter_id.length; i++ ) {
						if ( $('.panel-group').find('[data-filter-panel-id="'+ filter_id[i] +'"]').length !== 0 ) {
							panelID = filter_id[i];
							break;
						}
					}
				}

				if ( '' !== panelID ) {
					renderHTMLFilterItems.removeActiveCssClass();
					pwfAnimateCSS('.status-active-panel', outAnimation, function() { 
						$('.panel-group').find('.status-active-panel').remove();
						$('.panel-group').find('.status-unactive-panel').addClass('status-active-panel').removeClass('status-unactive-panel');
						pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
					});
				}
			} else if ( 'additem' === action ) {
				// check if panel filter type active
				if ( $('.panel-group').find('.panel-filter-type').length === 0 ) {
					renderHTMLFilterItems.removeActiveCssClass();
					if ( $('.panel-group').find('.item-panel').length > 0 ) {
						pwfAnimateCSS('.status-active-panel', outAnimation, function() {
							$('.panel-group').find('.status-active-panel').remove();
							$('.panel-group').append( template );
							pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
						});
					} else {
						pwfAnimateCSS('.status-active-panel', outAnimation, function() {
							$('.panel-group').find('.status-active-panel').addClass('status-unactive-panel').removeClass('status-active-panel');
							$('.panel-group').append( template );
							pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
						});
					}
				}
			} else if ( 'edititem' === action ) {
				// if its active panel no changes
				if ( $('.panel-group').find('[data-filter-panel-id="'+ filter_id +'"]').length === 0 ) {
					renderHTMLFilterItems.removeActiveCssClass();
					if ( $('.panel-group').find('.item-panel').length > 0 ) {
						pwfAnimateCSS('.status-active-panel', outAnimation, function() {
							$('.panel-group').find('.status-active-panel').remove();
							$('.panel-group').append( template );
							renderHTMLFilterItems.addActiveCssClass(filter_id);
							metaFields.sortEvent();
							metaFields.colorFieldEvent();
							panelEvents.initEditPanelEvent();
							pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
						});
					} else {
						pwfAnimateCSS('.status-active-panel', outAnimation, function() {
							$('.panel-group').find('.status-active-panel').addClass('status-unactive-panel').removeClass('status-active-panel');
							$('.panel-group').append( template );
							renderHTMLFilterItems.addActiveCssClass(filter_id);
							metaFields.sortEvent();
							metaFields.colorFieldEvent();
							panelEvents.initEditPanelEvent();
							pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
						});
					}
				}
			} else if ( 'additemtype' === action ) {
				renderHTMLFilterItems.removeActiveCssClass();
				if ( $('.panel-group').find('.item-panel').length > 0 ) {
					pwfAnimateCSS('.status-active-panel', outAnimation, function() {
						$('.panel-group').find('.status-active-panel').remove();
						renderHTMLFilterItems.appendItem(li_content);
						$('.panel-group').append( template );
						panelEvents.initEditPanelEvent();
						metaFields.sortEvent();
						metaFields.colorFieldEvent();
						pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
					});
				} else {
					pwfAnimateCSS('.status-active-panel', outAnimation, function() {
						$('.panel-group').find('.status-active-panel').remove();
						renderHTMLFilterItems.appendItem(li_content);
						$('.panel-group').append( template );
						panelEvents.initEditPanelEvent();
						metaFields.sortEvent();
						metaFields.colorFieldEvent();
						pwfAnimateCSS('.status-active-panel', inAnimation, function() {});
					});
				}
			}	
		}
	};

	var panelEvents = {
		/**
		 * used for color field and boxlistlabel field
		 * 
		 * return string or array
		 */
		getSourceOptionsData: function() {
			let result = {
				doAjax: false,
				data  : '',
				args  : '',
				cachedTaxonomyName: '',
			};
			
			let parent          = '';
			let taxonomyName    = '';
			let subSourceName   = '';
			let sourceOfOption  = $('[name="source_of_options"]').val();

			if ( 'category' === sourceOfOption ) {
				parent        = $('[name="item_source_category"]').val();
				taxonomyName  = 'product_cat';
				subSourceName = 'item_source_category';
			} else if ( 'attribute' === sourceOfOption) {
				taxonomyName  = $('[name="item_source_attribute"]').val();
				subSourceName = 'item_source_attribute';
			} else if ( 'taxonomy' === sourceOfOption ) {
				parent        = $('[name="item_source_taxonomy_sub"]').val();
				taxonomyName  = $('[name="item_source_taxonomy"]').val();
				subSourceName = 'item_source_taxonomy';
			} else if ( 'author' === sourceOfOption ) {
			} else if ( 'tag' === sourceOfOption ) {
			}
			
			let cachedTaxonomyName = sourceOfOption + parent + taxonomyName + subSourceName;
			result['cachedTaxonomyName'] = cachedTaxonomyName;
			if ( cached_taxonomy.hasOwnProperty( cachedTaxonomyName ) ) {
				result['data'] = cached_taxonomy[ cachedTaxonomyName ];
			} else {
				result['doAjax'] = true;
				result['args']   = {
					'source_of_option': sourceOfOption,
					'taxonomy_name':    taxonomyName,
					'parent'  :         parent,
				}
			}
			return result;
		},
		DisplayOrHideIncludeExludeFields: function() {
			let slected_value = $('[name="item_display"]').val();
			let parentInclude = $('.ajax-field-include').closest('.control-group');
			let parentExclude = $('.ajax-field-exclude').closest('.control-group');
			if ( 'selected' == slected_value ) {
				parentInclude.show();
				parentExclude.hide();
			} else if ( 'except' == slected_value ) {
				parentExclude.show();
				parentInclude.hide();
			} else {
				parentExclude.hide();
				parentInclude.hide();
			}
			$('[name="item_display"]').change(function() {
				let slected_value = $(this).val();
				if ( 'selected' == slected_value ) {
					parentInclude.slideDown('fast');
					parentExclude.slideUp('fast');
				} else if ( 'except' == slected_value ) {
					parentExclude.slideDown('fast');
					parentInclude.slideUp('fast');
				} else {
					parentExclude.slideUp('fast');
					parentInclude.slideUp('fast');
				}
			});
		},
		displayParentOption: function()  {
			/**
			 * only display option parent in item display
			 * for category and taxonomy for now is category
			 */
			let source_of_options = $('[name="source_of_options"]');
			let selected_value    = source_of_options.val();
			if ( 'category' === selected_value || 'taxonomy' === selected_value) {
				$('[name="item_display"] option[value="parent"]').show();
			} else {
				$('[name="item_display"] option[value="parent"]').hide();
				let slected_value = $('[name="item_display"]').val();
				if ( 'parent' == slected_value ) {
					$('[name="item_display"]').val('all');
				}
			}

			// @since 1.1.4
			if ( $('.panel-item.item-panel').hasClass('panel-rangeslider') ) {
				let source_of_options = $('[name="source_of_options"]');
				let selected          = source_of_options.val();
				if ( 'category' === selected ) {
					$(source_of_options).val('attribute');
				}
				$( source_of_options ).find('option[value="category"]').hide();
				$( source_of_options ).find('option[value="tag"]').hide();
				$( source_of_options ).find('option[value="orderby"]').hide();
				$( source_of_options ).find('option[value="author"]').hide();
				$( source_of_options ).find('option[value="stock_status"]').hide();
			}
		},
		colorFieldEvent: function() {
			$('.color-field').wpColorPicker({
				border: true,
				change: function( event, ui ){
					processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
					if ( $(this).closest('.color-item').find('.preview-image').length > 0 ) {
						$(this).closest('.color-item').find('.preview-image').remove();
					}
					$(this).closest('.color-item').find('.preview-holder').css('background-color', ui.color.toString() );
				},
			});
			$('.bordercolor-field').wpColorPicker({
				border: true,
				change: function( event, ui ){
					processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
				},
			});
		},
		fillAjaxColorFields: function() {
			let html           = '';
			let saved_values   = [];
			let source_value   = $('[name="source_of_options"]').val();
			let param_name     = 'colors';

			if ( source_value == 'meta' ) {
				$('.color-list').closest('.control-group').hide();
				return;
			}
			$('.color-list').closest('.control-group').show();

			let panel_type = getCurrentEditPanel();
			if ( panel_type === 'item_panel' ) {
				var filter_id = $('.panel-group .item-panel').attr('data-filter-panel-id');
				let filter    = getFilterItemDataByID( filter_id );
				if ( filter ) {
					saved_values = filter[param_name];
				}
			}

			let taxonmoyData = panelEvents.getSourceOptionsData();

			if ( false === taxonmoyData.doAjax ) {
				let taxonomies = taxonmoyData.data;
				html = renderHTMLFormField.colorFields( param_name, taxonomies, saved_values );
				$('.color-list').empty();
				$('.color-list').append(html);
				panelEvents.colorFieldEvent();
			} else {
				$('.color-list').closest('.control-group').addClass('transparent');
				let get_data = pwfAjaxGetTaxonomiesData( 'get_taxonomies_using_ajax', taxonmoyData.args );
				get_data.done(function(results){
					if ( results.isempty === 'false') {
						cached_taxonomy[taxonmoyData.cachedTaxonomyName] = results.data;
						html = renderHTMLFormField.colorFields( param_name, results.data, saved_values );
					} else {
						html = '<li class="alert-message">'+ results.message +'</li>'
					}
					$('.color-list').closest('.control-group').removeClass('transparent');
					$('.color-list').empty();
					$('.color-list').append(html);
					panelEvents.colorFieldEvent();
				});
				get_data.fail(function(jqXHR, textStatus, errorThrown){
					html = '<li class="alert-message">' + get_ajax_fail_message() + '</li>';
					$('.color-list').empty();
					$('.color-list').append(html);
					$('.color-list').closest('.control-group').removeClass('transparent');
				});
			}
		},
		changeTaxonomyDropdowList: function() {
			let html            = '';
			let saved_values    = '';
			let param_name      = 'item_source_taxonomy_sub';
			let sourceOfOptions = $('[name="source_of_options"]').val();
			let subsource       = $('[name="item_source_taxonomy"]').val();
			let parent          = $('[name="item_source_taxonomy_sub"]').val();
			if ( 'taxonomy' === sourceOfOptions ) {
				let panel_type = getCurrentEditPanel();
				if ( panel_type === 'item_panel' ) {
					let filter_id  = $('.panel-group .item-panel').attr('data-filter-panel-id');
					let filter     = getFilterItemDataByID( filter_id );
					if ( filter ) {
						if ( 'taxonomy' === filter['source_of_options'] ) {
							saved_values = filter[param_name];
						}
					}
				}

				let cached_taxonomy_name = 'taxonomysubchildren' + sourceOfOptions + subsource + parent;

				if ( cached_taxonomy.hasOwnProperty( cached_taxonomy_name ) ) {
					let taxonomy_data = cached_taxonomy[ cached_taxonomy_name ];
					html = renderHTMLFormField.ajaxdropdownfield( taxonomy_data, saved_values );
					$('.ajax-dropdown-field-item_source_taxonomy_sub').empty();
					$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').slideDown('fast');
					$('.ajax-dropdown-field-item_source_taxonomy_sub').append(html);
				} else {
					$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').addClass('transparent');
					let args = {
						'parent':           parent,
						'source_of_option': sourceOfOptions,
						'taxonomy_name':    subsource,
						'add_all_text':     'true',
					};
					let get_data = pwfAjaxGetTaxonomiesData( 'get_taxonomies_using_ajax', args );
					get_data.done(function(results){
						if ( results.isempty === 'false') {
							cached_taxonomy[cached_taxonomy_name] = results.data;
							html = renderHTMLFormField.ajaxdropdownfield( results.data, saved_values );
						} else {
							html = '<li class="alert-message">'+ results.message +'</li>'
						}
						$('.ajax-dropdown-field-item_source_taxonomy_sub').empty();
						$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').slideDown('fast');
						$('.ajax-dropdown-field-item_source_taxonomy_sub').append(html);
						$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').removeClass('transparent');
					});
					get_data.fail(function(jqXHR, textStatus, errorThrown){
						html = '<li class="alert-message">' + get_ajax_fail_message() + '</li>';
						$('.ajax-dropdown-field-item_source_taxonomy_sub').empty();
						$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').slideDown('fast');
						$('.ajax-dropdown-field-item_source_taxonomy_sub').append(html);
						$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').removeClass('transparent');
					});
				}
			} else {
				$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').slideUp('fast');
			}
		},
		changeIncludeExcludeFieldData: function() {
			/**
			 * Ajax to display include / exclude fields
			 *
			 */
			let html           = '';
			let saved_values   = [];
			let parent         = '';
			let subSourceName  = '';
			let taxonomyName   = '';
			let userRoles      = [];
			let sourceOfOption = $('[name="source_of_options"]').val();
			let itemDisplay    = $('[name="item_display"]').val();
			let sourceData     = ['attribute', 'category', 'taxonomy', 'tag', 'author'];

			if ( sourceData.includes( sourceOfOption ) && ( 'selected' == itemDisplay || 'except' == itemDisplay ) ) {
				if ( 'category' === sourceOfOption ) {
					parent        = $('[name="item_source_category"]').val();
					taxonomyName  = 'product_cat';
					subSourceName = 'item_source_category';
				} else if ( 'attribute' === sourceOfOption) {
					taxonomyName  = $('[name="item_source_attribute"]').val();
					subSourceName = 'item_source_attribute';
				} else if ( 'taxonomy' === sourceOfOption ) {
					parent        = $('[name="item_source_taxonomy_sub"]').val();
					taxonomyName  = $('[name="item_source_taxonomy"]').val();
					subSourceName = 'item_source_taxonomy';
				} else if ( 'author' === sourceOfOption ) {
					let values       = [];
					let dataSelected = $('[name="user_roles"]').select2('data');
					$.each( dataSelected, function( index, selected ){
						values.push(selected.id);
					});

					if ( values.length ) {
						userRoles = values;
					}
				} else if ( 'tag' === sourceOfOption ) {
				}

				let param_name = 'include';
				if ( 'except' == itemDisplay ) {
					param_name = 'exclude';
				}

				let panel_type = getCurrentEditPanel();
				if ( panel_type === 'item_panel' ) {
					let filter_id  = $('.panel-group .item-panel').attr('data-filter-panel-id');
					let filter     = getFilterItemDataByID( filter_id );
					if ( filter ) {
						let itemSourceOfOptions = filter['source_of_options'];
						if ( 'category' === itemSourceOfOptions && 'category' === sourceOfOption && parent == filter[ parent ] ) {
							saved_values = filter[param_name];
						} else if ( 'taxonomy' === itemSourceOfOptions && 'taxonomy' === sourceOfOption && taxonomyName == filter[ subSourceName ] && parent == filter[ parent ] ) {
							saved_values = filter[param_name];
						} else if  ( 'attribute' === itemSourceOfOptions && 'attribute' === sourceOfOption && taxonomyName == filter[ subSourceName ] ) {
							saved_values = filter[param_name];
						} else {
							saved_values = filter[param_name];
						}
					}
				}

				let cached_taxonomy_name = 'hierarchy' + sourceOfOption + parent + taxonomyName + subSourceName + userRoles.toString();
				if ( cached_hierarchy_taxonomy.hasOwnProperty( cached_taxonomy_name ) ) {
					let taxonomy_data = cached_hierarchy_taxonomy[ cached_taxonomy_name ];
					html = checkboxTree( param_name, taxonomy_data, saved_values );
					$('.ajax-field-' + param_name ).empty();
					$('.ajax-field-' + param_name ).closest('.control-group').slideDown('fast');
					$('.ajax-field-' + param_name ).append(html);
				} else {
					$('.ajax-field-' + param_name ).closest('.control-group').addClass('transparent');
					let args = {
						'source_of_option': sourceOfOption,
						'taxonomy_name': taxonomyName,
						'parent':     parent,
						'user_roles': userRoles,
					};
					let get_data = pwfAjaxGetTaxonomiesData( 'get_hierarchy_taxonomies_using_ajax', args );
					get_data.done(function(results){
						if ( results.isempty === 'false') {
							cached_hierarchy_taxonomy[cached_taxonomy_name] = results.data;
							html = checkboxTree( param_name, results.data, saved_values );
						} else {
							html = '<li class="alert-message">'+ results.message +'</li>'
						}
						$('.ajax-field-' + param_name ).empty();
						$('.ajax-field-' + param_name ).closest('.control-group').slideDown('fast');
						$('.ajax-field-' + param_name ).append(html);
						$('.ajax-field-' + param_name ).closest('.control-group').removeClass('transparent');
					});
					get_data.fail(function(jqXHR, textStatus, errorThrown){
						html = '<li class="alert-message">' + get_ajax_fail_message() + '</li>';
						$('.ajax-field-' + param_name ).empty();
						$('.ajax-field-' + param_name ).closest('.control-group').slideDown('fast');
						$('.ajax-field-' + param_name ).append(html);
						$('.ajax-field-' + param_name ).closest('.control-group').removeClass('transparent');
					});
				}


			} else {
				$('.ajax-field-exclude').closest('.control-group').slideUp('fast');
				$('.ajax-field-include').closest('.control-group').slideUp('fast');
			}
		},
		changeboxlistlabel: function() {
			let html            = '';
			let saved_values    = [];
			let param_name      = 'boxlistlabel';
			let sourceOfOptopns = $('[name="source_of_options"]').val();

			if ( 'meta' === sourceOfOptopns || 'stock_status' === sourceOfOptopns ) {
				return;
			}

			let panel_type = getCurrentEditPanel();
			if ( panel_type === 'item_panel' ) {
				var filter_id = $('.panel-group .item-panel').attr('data-filter-panel-id');
				let filter    = getFilterItemDataByID( filter_id );
				if ( filter ) {
					saved_values = filter[param_name];
				}
			}

			let taxonmoyData = panelEvents.getSourceOptionsData();
			if ( false === taxonmoyData.doAjax ) {
				let taxonomies = taxonmoyData.data;
				html = renderHTMLFormField.boxlistlabelfields( taxonomies, saved_values );
				$('.box-list-label-require-ajax').empty();
				$('.box-list-label-require-ajax').append(html);
			} else {
				$('.box-list-label-require-ajax').closest('.control-group').addClass('transparent');
				let get_data = pwfAjaxGetTaxonomiesData( 'get_taxonomies_using_ajax', taxonmoyData.args );
				get_data.done(function(results){
					if ( results.isempty === 'false') {
						cached_taxonomy[taxonmoyData.cachedTaxonomyName] = results.data;
						html = renderHTMLFormField.boxlistlabelfields( results.data, saved_values );
					} else {
						html = '<li class="alert-message">'+ results.message +'</li>'
					}
					$('.box-list-label-require-ajax').closest('.control-group').removeClass('transparent');
					$('.box-list-label-require-ajax').empty();
					$('.box-list-label-require-ajax').append(html);
				});
				get_data.fail(function(jqXHR, textStatus, errorThrown){
					html = '<li class="alert-message">' + get_ajax_fail_message() + '</li>';
					$('.box-list-label-require-ajax').empty();
					$('.box-list-label-require-ajax').append(html);
					$('.box-list-label-require-ajax').closest('.control-group').removeClass('transparent');
				});
			}
		},
		ajaxRuleDisplay: function() {
			// this happens after panel is display
			let ruleFieldsRequireUpdate = $('.display-rules-container').find('[data-select2-ajax-param]');
			if( ruleFieldsRequireUpdate.length > 0 ) {
				let ruleNum     = ruleFieldsRequireUpdate.length;
				let indexRule   = 0;
				let currentRule = ruleFieldsRequireUpdate[indexRule];

				let ajaxGetData = function( currentRule ){
					let param  = $(currentRule).attr('data-select2-ajax-param');
					let value = $(currentRule).attr('data-select2-ajax-value');
					
					if ( cached_taxonomy_rules.hasOwnProperty( param ) ) {
						let html = renderHTMLFormField.displayRulesField.ruleOptgroup( cached_taxonomy_rules[param], value );
						$(currentRule).empty().append(html).select2({
							width:'100%',
						});
						$(currentRule).closest('.rule').removeClass('transparent');
						indexRule++;
						if ( indexRule < ruleNum ) {
							ajaxGetData( ruleFieldsRequireUpdate[indexRule] );
						}
					} else {
						let ajax_query = pwfAjaxGetTaxonomiesData( 'get_group_taxonomies_using_ajax', '', param );
						ajax_query.done(function ( ajax_query ) {
							cached_taxonomy_rules[param] = ajax_query.data;
							let html = renderHTMLFormField.displayRulesField.ruleOptgroup( ajax_query.data, value );
							$(currentRule).empty().append(html).select2({
								width:'100%',
							});
							$(currentRule).closest('.rule').removeClass('transparent');
							indexRule++;
							if ( indexRule < ruleNum ) {
								ajaxGetData( ruleFieldsRequireUpdate[indexRule] );
							}
						});
					}
				};

				ajaxGetData( currentRule );
			}
		},
		initEventRuleDisplayField: function() {
			// When display rule field param change or it's display first time
			$('.rule-group').each( function() {
				$(this).find('.rule').removeClass('last-rule').last().addClass('last-rule');
			});		
	
			$('.field-rule-param, .field-rule-equal,.field-rule-value-select2').on('change', function(){
				processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
				var error_message = $(this).closest('.control-content').find('.alert-danger');
				if ( error_message ) {
					error_message.remove();
				} 
			});
	
			$('.field-rule-value-select2').select2({
				width: '100%',
			});
		},
		initEditPanelEvent: function() {

			// Used when add/edit panel
			if( $('.panel-colorlist').length > 0 ) {
				panelEvents.fillAjaxColorFields();
			}

			if ( $('[name="price_url_format"]').length ) {
				let priceFormat = $('[name="price_url_format"]:checked').val();
				if ( 'dash' === priceFormat ) {
					$('[name="url_key_min_price"], [name="url_key_max_price"]').closest('.control-group').hide();
				} else {
					$('[name="url_key_min_price"], [name="url_key_max_price"]').closest('.control-group').show();
				}
			}

			// @sine 1.1.4
			if ( $('[name="range_slider_url_format"]').length ) {
				let priceFormat = $('[name="range_slider_url_format"]:checked').val();
				if ( 'dash' === priceFormat ) {
					$('[name="url_key_range_slider_min"], [name="url_key_range_slider_max"]').closest('.control-group').hide();
				} else {
					$('[name="url_key_range_slider_min"], [name="url_key_range_slider_max"]').closest('.control-group').show();
				}
			}

			if ( $('[name="display_toggle_content"]').is(":checked") ) {
				$('[name="default_toggle_state"]').closest('.control-group').show();
			} else {
				$('[name="default_toggle_state"]').closest('.control-group').hide();
			}

			if ( $('[name="display_title"]').is(":checked") ) {
				/*
				  * Used to hide fields "display_toggle_content" &&  "default_toggle_state"
				  * Depend on field "display_title"
				  */
				$('[name="display_toggle_content"]').closest('.control-group').show();
				if ( $('[name="display_toggle_content"]').is(":checked") ) {
					$('[name="default_toggle_state"]').closest('.control-group').show();
				}
			} else {
				$('[name="display_toggle_content"]').closest('.control-group').hide();
				$('[name="default_toggle_state"]').closest('.control-group').hide();
			}
	
			let default_arr = ['category', 'taxonomy' ]; // sourece of options with hierarchical
			let sourceOfOption = $('[name="source_of_options"]').val();
			if ( 'meta' === sourceOfOption ) {
				$('[name="meta_key"], [name="meta_compare"], [name="meta_type"], .pwf-meta-field-box').closest('.control-group').show();
				if ( $('.panel-item.item-panel').hasClass('panel-rangeslider') ) {
					$('[name="range_slider_meta_source"]').closest('.control-group').show();
				}
			} else {
				$('[name="meta_key"], [name="meta_compare"], [name="meta_type"], .pwf-meta-field-box').closest('.control-group').hide();
				if ( $('.panel-item.item-panel').hasClass('panel-rangeslider') ) {
					$('[name="range_slider_meta_source"]').closest('.control-group').hide();
				}
			}
	
			if ( 'category' === sourceOfOption ) {
				$('[name="item_source_category"]').closest('.control-group').show();
			} else {
				$('[name="item_source_category"]').closest('.control-group').hide()
			}
	
			if ( 'attribute' === sourceOfOption ) {
				$('[name="item_source_attribute"]').closest('.control-group').show();
			} else {
				$('[name="item_source_attribute"]').closest('.control-group').hide()
			}

			if ( 'author' === sourceOfOption ) {
				$('[name="user_roles"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="user_roles"]').closest('.control-group').slideUp('fast')
			}
	
			if ( 'stock_status' === sourceOfOption ) {
				$('[name="item_source_stock_status"]').closest('.control-group').show();
				$('[name="in_stock_text"]').closest('.control-group').show();
				$('[name="out_of_stock_text"]').closest('.control-group').show();
				$('[name="on_backorder_text"]').closest('.control-group').show();
			} else {
				$('[name="item_source_stock_status"]').closest('.control-group').hide()
				$('[name="in_stock_text"]').closest('.control-group').hide();
				$('[name="out_of_stock_text"]').closest('.control-group').hide();
				$('[name="on_backorder_text"]').closest('.control-group').hide();
			}

			if ( 'stock_status' === sourceOfOption || 'meta' === sourceOfOption ) {
				$('.pwf-box-list-label').closest('.control-group').slideUp('fast');
			} else {
				$('.pwf-box-list-label').closest('.control-group').slideDown('fast');
			}
	
			if ( 'orderby' === sourceOfOption ) {
				$('[name="item_source_orderby"]').closest('.control-group').show();
			} else {
				$('[name="item_source_orderby"]').closest('.control-group').hide()
			}
	
			let displayItemViewIn = [ 'category', 'taxonomy', 'tag', 'attribute', 'author' ];
			if( displayItemViewIn.includes( sourceOfOption ) ) {
				$('[name="item_display"]').closest('.control-group').show();
			} else {
				$('[name="item_display"]').closest('.control-group').hide();
			}
			
			if ( 'taxonomy' === sourceOfOption ) {
				$('[name="item_source_taxonomy"]').closest('.control-group').show();
				$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').show();
			} else {
				$('[name="item_source_taxonomy"]').closest('.control-group').hide()
				$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').hide();
			}
	
			let orderByViewIn = [  'attribute', 'category', 'taxonomy', 'tag' ];
			if( orderByViewIn.includes( sourceOfOption ) ) {
				$('[name="order_by"]').closest('.control-group').show();
			} else {
				$('[name="order_by"]').closest('.control-group').hide();
			}
	
			if( $.inArray( sourceOfOption, default_arr ) !== -1 ) {
				$('[name="display_hierarchical"]').closest('.control-group').show();
				if ( $('[name="display_hierarchical"]').is(":checked") ) {
					$('[name="display_hierarchical_collapsed"]').closest('.control-group').show();
				} else {
					$('[name="display_hierarchical_collapsed"]').closest('.control-group').hide();
				}
			} else {
				$('[name="display_hierarchical"]').closest('.control-group').hide();
				$('[name="display_hierarchical_collapsed"]').closest('.control-group').hide();
			}
	
			let multiSelectVal = $('[name="multi_select"]:checked').val();
			if ( 'on' === multiSelectVal ) {
				$('[name="query_type"]').closest('.control-group').show();
			} else {
				$('[name="query_type"]').closest('.control-group').hide();
			}
	
			let displayHeightOfVisibleContentField = [ 'scrollbar', 'morebutton' ];
			let moreOptionsBy = $('[name="more_options_by"]').val();
			if ( displayHeightOfVisibleContentField.includes( moreOptionsBy ) ) {
				$('[name="height_of_visible_content"]').closest('.control-group').show();
			} else {
				$('[name="height_of_visible_content"]').closest('.control-group').hide();
			}
	
			if ( $('[name="dropdown_style"]').length > 0 ) {
				panelEvents.dropdownDisplayFields($('[name="dropdown_style"]').val());
			}
	
			// inline style field - textlist panel
			if ( $('[name="inline_style"]').length > 0 ) {
				if ( $('[name="inline_style"]').is(":checked") ) {
					$('[name="display_hierarchical"]').closest('.control-group').hide();				
				} else {
					$('[name="display_hierarchical"]').closest('.control-group').show();
				}
			}
	
			// panel color disable stock status
			if ( $('.panel-colorlist').length > 0 ) {
				$('[name="source_of_options"] option[value="stock_status"]').hide();
			}
			if ( $('.panel-checkboxlist').length > 0 || $('.panel-boxlist').length > 0 || $('.panel-colorlist').length > 0 || $('.panel-textlist').length > 0 ) {
				$('[name="source_of_options"] option[value="orderby"]').hide();
			}

			// since 1.2.3
			if ( $('.pwf-select2').length > 0 ) {
				$('.pwf-select2').select2();
			}

			// @since 1.1.4
			if ( $('.panel-item.item-panel').hasClass('panel-rangeslider') ) {
				let sourceOfMeta = $('[name="range_slider_meta_source"]').val();
				if ( 'custom' === sourceOfMeta ) {
					$('[name="meta_key"]').closest('.control-group').show();
				} else {
					$('[name="meta_key"]').closest('.control-group').hide()
				}
			}

			panelEvents.displayParentOption();
			panelEvents.changeTaxonomyDropdowList();
			panelEvents.DisplayOrHideIncludeExludeFields();
			panelEvents.changeIncludeExcludeFieldData();
			panelEvents.ajaxRuleDisplay();
			panelEvents.changeboxlistlabel();
		},
		dropdownDisplayFields: function( dropdown_style ) {
			let sourceOfOption = $('[name="source_of_options"]').val();
			if ( 'plugin' === dropdown_style ) {
				if ( 'orderby' === sourceOfOption || 'stock_status' === sourceOfOption ) {
					$('[name="multi_select"]').closest('.control-group').slideUp('fast');
				} else {
					$('[name="multi_select"]').closest('.control-group').slideDown('fast');
				}

				if ( ( 'category' === sourceOfOption || 'taxonomy' === sourceOfOption ) ) {
					$('[name="display_hierarchical"]').closest('.control-group').slideDown('fast');

					let multiSelectVal = $('[name="multi_select"]:checked').val();
					if ( 'on' === multiSelectVal ) {
						$('[name="query_type"]').closest('.control-group').slideDown('fast');
					} else {
						$('[name="query_type"]').closest('.control-group').slideUp('fast');
					}
				} else {
					$('[name="display_hierarchical"]').closest('.control-group').slideUp('fast');
					$('[name="query_type"]').closest('.control-group').slideUp('fast');
				}

			} else {
				$('[name="display_hierarchical"]').closest('.control-group').slideUp();
				$('[name="multi_select"]').closest('.control-group').slideUp('fast');
				$('[name="query_type"]').closest('.control-group').slideUp('fast');
			}
		}
	}

	var metaFields = {
		createMetaFields: function( panelType = '', fields ) {
			if ( ! Array.isArray(fields ) && ! fields.length ) {
				return
			}
			let html = '';
			fields.forEach( function( field ) {
				html += metaFields.addMetaField( panelType, field, false );
			});
			return html;
		},
		addMetaField: function( panelType = '', savedValues = '', appendField = true ) {
			// savedValues is object represnt one meta field
			let cssClass = ''
			let label    = '';
			let value    = '';
			let slug     = '';
			if ( ! $.isEmptyObject( savedValues ) ) {
				label = savedValues['label'];
				value = savedValues['value'];
				slug  = ( savedValues.hasOwnProperty('slug') ) ? savedValues['slug'] : '';
			}
			if ( false === appendField ) {
				cssClass = ' pwf-hide-meta-content';
			}
			let html = '<div class="pwf-meta-item' + cssClass + '">';
			html += '<div class="pwf-meta-header">';
			html += '<div class="meta-title">';
			let title = 'Title';
			if ( '' !== label ) {
				title = label;
			}
			html += title;
			html += '</div>';
			html += '<div class="btn-actions">';
			html += '<span class="btn-link meta-edit pwf-meta-edit-action">' + pwfTranslatedText.edit + '</span>';
			html += '<span class="btn-link meta-close pwf-meta-edit-action">' + pwfTranslatedText.close + '</span>';
			html += '<span class="btn-link pwf-meta-remove-action">' + pwfTranslatedText.remove + '</span>';
			html += '</div>';
			html += '</div>';
			html += '<div class="pwf-meta-content">';
			html += metaFields.addTextInput( pwfTranslatedText.label, 'metalabel', 'label', label );
			html += metaFields.addTextInput( pwfTranslatedText.slug, 'metaslug', '', slug );
			html += metaFields.addTextInput( pwfTranslatedText.value, 'metavalue', 'value', value, pwfTranslatedText.meta_value_desc );

			if ( 'colorlist' === panelType ) {
				html += '<div class="pwf-meta-inner color-list colors">' + metaFields.addColorField( savedValues ) + '</div>';
			}

			html += '</div>';
			html += '</div>';

			if ( appendField ) {
				$('.meta-field-items').append(html);
				metaFields.refreshSort();
				metaFields.colorFieldEvent();
			} else {
				return html;
			}
		},
		addTextInput: function( label , slug, placeholder, value, desc = '' ) {
			let html = '<div class="pwf-meta-inner">';
			html    += '<div class="label-container"><span="label-text">' + label + '</span></div>';
			html    += '<div class="input-container">';
			html    += '<input placeholder="' + placeholder + '" type="text" class="form-control full-width text" name="' + slug + '" value="' + value + '"/>';
			html    += ( '' === desc ) ? '' : '<div class="pwf-meta-desc">' + desc + '</div>';
			html    += '</div>';
			html    += '</div>';
			return html;
		},
		addColorField: function( meta ) {
			let html        = '';
			let color       = ( meta.hasOwnProperty('color') ) ? meta.color : '';
			let image       = ( meta.hasOwnProperty('image') ) ? meta.image : '';
			let type        = ( meta.hasOwnProperty('type') ) ? meta.type : '';
			let bordercolor = ( meta.hasOwnProperty('bordercolor') ) ? meta.bordercolor : '';
			let marker      = ( meta.hasOwnProperty('marker') ) ? meta.marker : 'light';
			let radomID     = Math.floor(Math.random() * 100000) ;
			
			html += '<div class="color-item">';
			html += '<div class="color-inner">';

			html += '<div class="color-term-header">';
			let background_color = ( 'color' === type && '' !== color ) ? ' style=background-color:'+ color : '';
			html += '<div class="preview-term-container"><div class="preview-holder"' + background_color + '>';

			if ( 'image' === type && '' !== image ) {
				html += '<img class="preview-image" src="' + image + '">';
			}
			html += '</div></div>';
			html += '</div>';

			html += '<div class="color-term-content">';
			html += '<div class="inner-fields">';

			let css_hidden = ( type === 'color' ) ? '': ' hidden';
			html += '<div class="container list-container">';
			html += '<div class="color-container'+ css_hidden +'">';
			html += '<div class="header"><span class="text">' + pwfTranslatedText.color + '</span></div>';
			html += '<div class="content">';
			html += '<input type="text" name="color" class="color-field" value="' + color + '"/>';
			html += '</div>';
			html += '</div>';
			css_hidden = ( type === 'image' ) ? '': ' hidden';
			html += '<div class="image-container'+ css_hidden +'">';
			html += '<div class="header"><span class="text">' + pwfTranslatedText.image + '</span></div>';
			html += '<div class="content">';
			html += '<input type="text" name="image" class="text hidden image-field" value="' + image + '"/>';
			html += '<button class="button btn-upload-image">' + pwfTranslatedText.upload_image + '</button>';
			html += '</div>';
			html += '</div>';
			html += '</div>';

			html += '<div class="type-container list-container">';
			html += '<div class="header"><span class="text">' + pwfTranslatedText.type + '</span></div>';
			html += '<div class="content"><div class="radio-as-button">';

			let checked = ( type === 'color' ) ? ' checked': '';
			html += '<span class="field-type"><input type="radio" class="type-field" name="type'+ radomID + '" value="color"'+ checked + '/> <label for="color">' + pwfTranslatedText.color + '</label></span>';
			checked = ( type === 'image' ) ? ' checked': '';
			html += '<span class="field-type"><input type="radio" class="type-field" name="type'+ radomID + '" value="image"'+ checked + '/> <label for="image">' + pwfTranslatedText.image + '</label></span>';
			html += '</div></div>';
			html += '</div>';

			html += '</div>';

			html += '<div class="inner-fields">';

			html += '<div class="border-container list-container">';
			html += '<div class="header"><span class="text">' + pwfTranslatedText.border + '</span></div>';
			html += '<div class="content"><div class="wrap-color">';
			html += '<input type="text" class="bordercolor-field" name="bordercolor" class="color-field" value="'+ bordercolor + '"/>';
			html += '</div></div>';
			html += '</div>';

			html += '<div class="marker-container list-container">';
			html += '<div class="header"><span class="text">' + pwfTranslatedText.marker + '</span></div>';
			html += '<div class="content"><div class="radio-as-button">';
			checked = ( marker === 'light' ) ? ' checked': '';
			html += '<span class="field-type"><input type="radio" class="marker-field" name="marker'+ radomID + '" value="light"'+ checked +'/> <label lass="field-type-text">' + pwfTranslatedText.marker_light + '</label></span>';
			checked = ( marker === 'dark' ) ? ' checked': '';
			html += '<span class="field-type"><input type="radio" class="marker-field" name="marker'+ radomID + '" value="dark"'+ checked +'/> <label lass="field-type-text">' + pwfTranslatedText.marker_dark + '</label></span>';
			html += '</div></div>';
			html += '</div>';

			html += '</div>';
			html += '</div>'; // End color-inner
			html += '</div>'; // End color-item
			html += '</div>';

			return html;
		},
		removeMetaField: function( metaField ) {
			$(metaField).closest('.pwf-meta-item').remove();
		},
		editMetaField: function( metaField ) {
			$(metaField).closest('.pwf-meta-item').toggleClass('pwf-hide-meta-content');
		},
		sortEvent: function() {
			$( ".meta-field-items" ).sortable({
				connectWith: ".meta-field-items",
				handle: ".pwf-meta-header",
				cancel: ".pwf-meta-toggle",
				placeholder: "pwf-meta-placeholder"
			});
		},
		refreshSort: function() {
			$('.meta-field-items').sortable("refresh");
		},
		colorFieldEvent: function() {
			$('.pwf-meta-item .color-field').wpColorPicker({
				border: true,
				change: function(event, ui){
					if ( $(this).closest('.color-item').find('.preview-image').length > 0 ) {
						$(this).closest('.color-item').find('.preview-image').remove();
					}
					$(this).closest('.color-item').find('.preview-holder').css('background-color', ui.color.toString() );
					processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
				},
			});
			$('.pwf-meta-item .bordercolor-field').wpColorPicker({
				border: true,
				change: function(event, ui){
					processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
				},
			});
		},
	};

	var processingPanelForm = {
		addAttributeRequiredValidate: function( panelType = '', attributeData ) {
			if ( '' === panelType ) {
				panelType = getCurrentEditPanel();
			}
			if ( panelType === 'item_panel' ) {
				$('.panel-group .item-panel').attr( 'data-required-validate', attributeData );
			} else {
				$('.panel-group .setting-panel').attr( 'data-required-validate', attributeData );
			}
			if ( 'true' === attributeData ) {
				if ( panelType === 'item_panel' ) {
					$('.panel-group .item-panel').find('.apply-panel-button').prop( 'disabled', false );
				} else {
					$('.panel-group .setting-panel').find('.apply-panel-button').prop( 'disabled', false );
				}
			} else if ( 'false' === attributeData ) {
				if ( panelType === 'item_panel' ) {
					$('.panel-group .item-panel').find('.apply-panel-button').prop( 'disabled', true );
				} else {
					$('.panel-group .setting-panel').find('.apply-panel-button').prop( 'disabled', true );
				}
			}
		},
		hasPanelRequiredValidate( panelType ) {
			/**
			 * return bollean
			 */
			if ( panelType === 'item_panel' ) {
				var required_validate = $('.panel-group').find('.item-panel').attr('data-required-validate');
			} else {
				var required_validate = $('.panel-group').find('.setting-panel').attr('data-required-validate');
			}
			
			if ( required_validate === 'true' ) {
				return true;
			}
			return false;
		},
		checkFieldHasValue: function( value ) {
			if ( typeof value !== undefined && value !== false && value !== "" ) {
				return value;
			} else {
				return '';
			}
		},
		addActiveLoader: function() {
			if ( $('.pwf-overlay').length > 0 ) {
				$('.pwf-overlay').removeClass('pwf-unactive').addClass('pwf-active');
			} else {
				$('body').prepend('<div class="pwf-overlay pwf-active"><span class="pwf-loader"></span></div>');
			}
		},
		removeActiveLoader: function() {
			$('.pwf-overlay').removeClass('pwf-active').addClass('pwf-unactive');
		},
		processingPanel: function() {
			processingPanelForm.addActiveLoader();
			let return_value = false;
			let panel_type = getCurrentEditPanel();
			if ( processingPanelForm.hasPanelRequiredValidate( panel_type ) ) {
				if ( processingPanelForm.validatePanelData( panel_type ) ) {
					processingPanelForm.addAttributeRequiredValidate( panel_type, 'false');
					processingPanelForm.savePanelData( panel_type );
					return_value = true;
				} else {
					// panel doesn't validate, require user to add/change value
					display_alert_message();
					processingPanelForm.addAttributeRequiredValidate( panel_type, 'true');
					return_value = false;
				}
			} else {
				// item edit panel doesn't required validate
				return_value = true;
			}
			processingPanelForm.removeActiveLoader();
			return return_value;
		},
		validatePanelData: function( panelType ) {
			let isValidated    = true;
			let currentFilterItemID = '';

			if ( 'item_panel' === panelType ) {
				if ( $('.option-panel-form .alert-danger').length > 0 ) {
					$('.option-panel-form .alert-danger').remove();
				}
				var allinput        = $('form.option-panel-form :input');
				currentFilterItemID = $('.option-panel-form').closest('.panel-item').attr('data-filter-panel-id');
			} else {
				if ( $('.setting-panel-form .alert-danger').length > 0 ) {
					$('.setting-panel-form .alert-danger').remove();
				}
				var allinput = $('.setting-panel-form :input');
			}
			
			let currentPanel = $('.item-panel').attr('data-panel-type');
			if ( 'rangeslider' === currentPanel ) {
				let urlFormatValue = $('input[name="range_slider_url_format"]:checked').attr('value');
				if ( 'two' !== urlFormatValue) {
					$('input[name="url_key_range_slider_min"]').removeAttr('aria-required');
					$('input[name="url_key_range_slider_max"]').removeAttr('aria-required');
				} else {
					$('input[name="url_key_range_slider_min"]').attr('aria-required', 'true');
					$('input[name="url_key_range_slider_max"]').attr('aria-required', 'true');
				}
			} else if ( 'priceslider' === currentPanel ) {
				let urlFormatValue = $('input[name="price_url_format"]:checked').attr('value');
				if ( 'two' !== urlFormatValue) {
					$('input[name="url_key_min_price"]').removeAttr('aria-required');
					$('input[name="url_key_max_price"]').removeAttr('aria-required');
				} else {
					$('input[name="url_key_min_price"]').attr('aria-required', 'true');
					$('input[name="url_key_max_price"]').attr('aria-required', 'true');
				}
			}

			(allinput).each( function( index, current_field ) {
				let attr = $(current_field).attr('aria-required');

				if ( typeof attr !== typeof undefined && attr !== false ) {
					let value = $(current_field).val();

					if ( ! value ) {
						isValidated = false;
						if ( ! $(current_field).closest('.control-content').find('.alert-danger').length > 0 ) {
							$(current_field).closest('.control-content').append(get_alert_message());
						}
						return isValidated;
					}

					let fieldName = $(current_field).attr('name');
					let urkKeys   = [ 'url_key', 'url_key_min_price', 'url_key_max_price', 'url_key_date_before', 'url_key_date_after', 'url_key_range_slider_min', 'url_key_range_slider_max' ];
					if ( urkKeys.includes( fieldName ) ) {
						let uniqueURL = isUniqueUrlKey.init( value, currentFilterItemID );
						if ( false === uniqueURL ) {
							isValidated = false;
							if ( ! $(current_field).closest('.control-content').find('.alert-danger').length > 0 ) {
								$(current_field).closest('.control-content').append( get_alert_message_unique_url() );
							}
							return isValidated;
						}
					}
				}
			});
			// checkbox stockstatus
			if( $('.option-panel-form').find('[name="item_source_stock_status"]').length && $('[name="item_source_stock_status"]:checked').length <= 0 ) {
				isValidated = false;
				if ( ! $('[name="item_source_stock_status"]').closest('.control-content').find('.alert-danger').length ) {
					$('[name="item_source_stock_status"]').closest('.control-content').append(get_alert_message());
				}
			}
			return isValidated;
		},
		getValidatePanelData: function( fields ) {
			let save_data = {};

			$(fields).each( function( index, field ) {
				if ( 'multicheckbox' === field.type || 'ajaxmulticheckboxlist' === field.type ) {
					let value = [];
					$.each($('input[name="'+ field.param_name +'"]:checked'), function(){
						value.push($(this).val());
					});
					save_data[field.param_name] = value;
				} else if ( 'switchbutton' === field.type ) {
					let value = '';
					if ( $('input[name="'+ field.param_name +'"]').is(":checked") ) {
						value = $('input[name="'+ field.param_name +'"]').val();
					}
					save_data[field.param_name] = value;
				} else if ( 'radio' === field.type ) {
					let value = '';
					value = $('input[name="'+ field.param_name +'"]:checked').attr('value');
					save_data[field.param_name] = value;
				} else if ( 'ajaxdropdownselect2' === field.type ) {
					let values       = [];
					let dataSelected = $('[name="'+ field.param_name +'"]').select2('data');
					$.each( dataSelected, function( index, selected ){
						values.push(selected.id);
					});
					save_data[field.param_name] = values;
			    } else {
					let value = $(".panel-group").find('[name='+ field.param_name +']').val();
					if ( value ) {
						save_data[field.param_name] = value;
					} else {
						save_data[field.param_name] = '';
					}
				}
			});
			return save_data;
		},
		savePanelData: function( panelType ) {
			// save panel data inside variables
			if ( panelType === 'item_panel' ) {
				let type            = $('.panel-group .item-panel').attr('data-panel-type');
				let filter_id       = $('.panel-group .item-panel').attr('data-filter-panel-id');
				let item_fields     = pwfFilterItemPanel.getPanelFields( type );
				let general_fields  = item_fields['general'];
				let visual_fields   = item_fields['visual'];
				let all_fields      =  $.merge(general_fields, visual_fields);
				let save_data       = processingPanelForm.getValidatePanelData( all_fields );

				// metafields
				if ( 'meta' === save_data.source_of_options && $('.meta-field-items').find('.pwf-meta-item').length > 0 ) {
					let metadata  = [];
					let metaItems = $('.pwf-meta-item');
					$(metaItems).each( function( index, metaitem ) {
						let label = $(metaitem).find('[name="metalabel"]').val();
						let slug  = $(metaitem).find('[name="metaslug"]').val();
						let value = $(metaitem).find('[name="metavalue"]').val();
			
						if( label.length && value.length ) {
							let metaObj = {
								'label': label,
								'slug': slug,
								'value': value,
							};
							if ( $('.panel-colorlist').length > 0 ) {
								metaObj['color']       = processingPanelForm.checkFieldHasValue( $(metaitem).find('.color-field').val() );
								metaObj['image']       = processingPanelForm.checkFieldHasValue( $(metaitem).find('.image-field').val() );
								metaObj['type']        = processingPanelForm.checkFieldHasValue( $(metaitem).find('.type-field:checked').attr('value') );
								metaObj['bordercolor'] = processingPanelForm.checkFieldHasValue( $(metaitem).find('.bordercolor-field').val() );
								metaObj['marker']      = processingPanelForm.checkFieldHasValue( $(metaitem).find('.marker-field:checked').attr('value') );
							}
							metadata.push( metaObj );
						}

					});
					save_data['metafield'] = metadata;
				} else {
					save_data['metafield'] = '';
				}
				
				// save color fields
				if( 'meta' !== save_data.source_of_options && $('.panel-colorlist').length > 0 ) {
					let color_data = [];
					$('.color-item').each( function( index, current ) {
						let term_id = $(current).attr('term-id');
						if ( term_id ) {
							let item = {
								'term_id':     term_id,
								'color':       processingPanelForm.checkFieldHasValue( $(current).find('.color-field').val() ),
								'image':       processingPanelForm.checkFieldHasValue( $(current).find('.image-field').val() ),
								'type':        processingPanelForm.checkFieldHasValue( $(current).find('.type-field:checked').attr('value') ),
								'bordercolor': processingPanelForm.checkFieldHasValue( $(current).find('.bordercolor-field').val() ),
								'marker':      processingPanelForm.checkFieldHasValue( $(current).find('.marker-field:checked').attr('value') ),
							};
							color_data.push( item );
						}
					});
					save_data['colors'] = color_data;
				} else {
					save_data['colors'] = '';
				}

				if ( $('.panel-boxlist').length > 0 ) {
					let boxlistlabel = [];
					$('.lable-item').each( function( index, current ) {
						let value  = $(current).find('.item-value').val();
						let termId = $(current).attr('data-item-label-id');
						if ( typeof value !== undefined && value !== false && value !== "" && typeof termId !== undefined && termId !== false && termId !== "") {
							let item = {
								term_id: termId,
								value  : value,
							}
							boxlistlabel.push( item );
						}
					});
					save_data['boxlistlabel'] = boxlistlabel;
				}

				if( $('.pwf-rule').length > 0 ) {
					let rule_groups = [];
					$('.pwf-rule').each( function( index, current_rule ) {
						let param = $(current_rule).find('.field-rule-param').val();
						let value = $(current_rule).find('.field-rule-value-select2').val();
						if ( '' !== value ) {
							let rule = {
								'param': param,
								'value': value,
							};
							rule_groups.push( rule );
						}
					});

					if ( Array.isArray( rule_groups ) && rule_groups.length ) {
						save_data['hidden_rules'] = rule_groups;
					} else {
						save_data['hidden_rules'] = '';
					}
				}
				
				save_data['item_type'] = type;
				filterItemsData[filter_id] = save_data; // cached save data
				// update items list
				$('.filters-list').find('[data-filter-id=' + filter_id +']').find('.filter-title').text(save_data.title);
			} else {
				// setting panel
				let general_fields   = pwfFilterSettingFields['general'];
				let selectors_fields = pwfFilterSettingFields['selectors'];
				let extraFields      = pwfFilterSettingFields['extra'];
				let responsiveFields = pwfFilterSettingFields['responsive'];
				let all_fields       =  $.merge(general_fields, selectors_fields );
				all_fields           =  $.merge(all_fields, extraFields);
				all_fields           =  $.merge(all_fields, responsiveFields);
				let save_data        = processingPanelForm.getValidatePanelData( all_fields );
				setting_data         = save_data;
				// Update filter title
				let postTitle = setting_data['post_title'];
				if ( $('.wp-heading-inline .heading-filter-title').length > 0 ) {
					$('.wp-heading-inline .heading-filter-title').text( postTitle );
				} else {
					$('.wp-heading-inline').append( '<span class="heading-filter-title">' + postTitle + '</span>' );
				}
			}
		}
	}

	var  countForSavedItems = 0;
	function getAllfilterItemsForSaved( filterItems ) {
		let filters = {};
		$( filterItems ).each( function( index, current ) {
			let filterID   =  $(current).attr('data-filter-id');
			let attrLayout = $(current).attr('data-layout');
			if (typeof attrLayout !== typeof undefined && attrLayout !== false && 'column' === attrLayout ) {
				let filter        = filterItemsData[filterID];				
				let children = $(current).find('.column-inner').first().children('.filter-item');				
				let ParentID =  'id-' + countForSavedItems;
				countForSavedItems++;
				if ( ! $.isEmptyObject( children ) && children.length > 0 ) {
					filter['children']  = getAllfilterItemsForSaved( children );
					filters[ ParentID ] = filter;
					
				} else {
					filter['children'] = {};
					filters[ ParentID ] = filter;
				}
			} else {
				filters[ 'id-' + countForSavedItems ] = filterItemsData[ filterID ];
				countForSavedItems++;
			}
		});

		return filters;
	}
	
	function saveFilterPost() {

		if ( ! processingPanelForm.processingPanel() ) {
			return;
		}

		let data           = {};
		let filters        = {};
		let parentFilters  = $('.filters-list').children('.filter-item');
		filters            = getAllfilterItemsForSaved( parentFilters );
		countForSavedItems = 0;

		data = {
			'setting': setting_data,
			'items': filters,
		};

		var post_id = '';
		if ( pwfWooFilterData.current_screen === 'edit' ) {
			post_id = pwfWooFilterData.post_id;
		}

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				'action':  'save_filter_post',
				'nonce':   pwfWooFilterData.nonce,
				'data':    JSON.stringify( data ),
				'post_id': post_id,
			},
			beforeSend : function() {
				processingPanelForm.addActiveLoader();
				if ( $('.filters-btn').find('.message').length > 0 ) {
					$('.filters-btn').find('.message').remove();
				}
			}
		}).done( function( result ) {
			if ( result.success === 'true' ) {
				if ( pwfWooFilterData.current_screen !== 'edit' ) {
					pwfWooFilterData.current_screen = 'edit';
					pwfWooFilterData.post_id = result.post_id;
					updateBrowserUrlLink( result.post_id );
				}
				$('.filters-btn').prepend('<span class="message success-message">' + pwfTranslatedText.filter_post_updated + '</span>');
				setTimeout(function(){ $('.filters-btn').find('.message').remove(); }, 10000);
			} else {
				// post not saved
				$('.filters-btn').prepend('<span class="message error-message">'+  result.message +'</span>');
			}
		}).fail( function(jqXHR, textStatus, errorThrown) {
			$('.filters-btn').prepend('<span class="message error-message">' + pwfTranslatedText.error + ' : '+  errorThrown +'</span>');
		}).always( function() {
			processingPanelForm.removeActiveLoader();
		});
		
		return false;
	}

	function pwfAjaxGetTaxonomiesData( actionName, args, source = '' ) {
		let data  = {
			'action': actionName,
			'nonce':  pwfWooFilterData.nonce,
		}

		if ( 'get_hierarchy_taxonomies_using_ajax' === actionName || 'get_taxonomies_using_ajax' === actionName ) {
			data['source_of_option'] = args.source_of_option;
			data['taxonomy_name']    = args.taxonomy_name;
			data['parent']           = args.parent;
			if ( 'get_taxonomies_using_ajax' === actionName && args.hasOwnProperty('add_all_text') ) {
				data['add_all_text'] = args.add_all_text;
			}
			if ( args.hasOwnProperty('user_roles') ) {
				data['user_roles'] = args.user_roles;
			}
		} else if ( 'get_group_taxonomies_using_ajax' === actionName ) {
			data['source'] = source;
		}
		
		return $.ajax({
			type:     'POST',
			dataType: 'json',
			url:      ajaxurl,
			data:     data,
		});
	}

	function pwfPluginEvents() {
		$('.pro-woo-filter').on('click', '.reset-panel-button', function( event ) {
			event.preventDefault();
			pwfFilterItemPanel.resetPanel();
		});

		$('.pro-woo-filter').on('click', '.apply-panel-button', function( event ) {
			event.preventDefault();
			pwfFilterItemPanel.apply_panel_button();
		});

		/*
		* used to display panel filter type [ checkbox, color, ...]
		*/
		$('.pro-woo-filter').on('click', '.filters-btn .add-item', function( event ) {
			event.preventDefault();
			if ( processingPanelForm.processingPanel() ) {
				let template  = panelfilterItemsTypes();
				pwfFilterItemPanel.displayPanel( 'additem', template );			
			}
		});

		$('.pro-woo-filter').on('click', '.save-project', function( event ) {
			event.preventDefault();
			saveFilterPost();
		});

		$('.pro-woo-filter').on('change input', '.setting-panel-form :input, ul.list input', function( event ) {
			event.preventDefault();
			processingPanelForm.addAttributeRequiredValidate( 'setting_panel', 'true');
			var error_message = $(this).closest('.control-content').find('.alert-danger');
			if ( error_message ) {
				error_message.remove();
			} 
		});

		$('.pro-woo-filter').on('change input click', '.option-panel-form :input, .image-field, .type-field, .bordercolor-field, .marker-field, ul.list input', function( event ) {
			let fieldName  = $(this) .attr('name');
			let ignorethis = [ 'url_key', 'url_key_min_price', 'url_key_max_price', 'url_key_date_before', 'url_key_date_after', 'url_key_range_slider_min', 'url_key_range_slider_max' ];
			if ( ignorethis.includes( fieldName ) ) {
				return;
			}
			processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
			let error_message = $(this).closest('.control-content').find('.alert-danger');
			if ( error_message ) {
				error_message.remove();
			} 
		});

		$('.pro-woo-filter').on('click', '.setting-panel .panel-header .nav-tab-heading', function( event ) {
			event.preventDefault();
			$(this).closest('.panel-container').find('.active-tab').removeClass('active-tab').addClass('active-tab');
			let currentTab = $(this).attr('data-tab-id');
			$(this).closest('.panel-container').find('.tab-content').hide();
			$(this).closest('.panel-container').find('.wrap-tabs').find('[data-tab-id=' + currentTab +']').addClass('active-tab').show();
		});

		$('.pro-woo-filter').on('click', '.btn-actions .edit-action', function( event ) {
			event.preventDefault();
			pwfFilterItemPanel.editPanel( $(this) );
		});
		
		$('.pro-woo-filter').on('click', '.btn-actions .remove-action', function( event ) {
			event.preventDefault();
			let popupAlert = confirm("Are you sure you want to remove this item?");
			if ( true === popupAlert ) {
				let item = $(this);
				renderHTMLFilterItems.removeItem(item);
			}
		});

		$('.pro-woo-filter').on('click', '.link-back', function( event ) {
			event.preventDefault();
			pwfFilterItemPanel.backLinkButton();
		});

		$('.pro-woo-filter').on('click', '.panel-group .add-new-element', function( event ) {
			event.preventDefault();
			let item_type = $(this).attr('data-item-type');
			let filter_id  = renderHTMLFilterItems.generateFilterItemID();
			if ( 'column' === item_type ) {
				let li_content = renderHTMLFilterItems.addNewColumn( filter_id );
				let config = {
					panelType:    item_type,
					FilterItemID: filter_id,
					requireValidate: 'true',
					predefinedPanel: '',
				};

				let template   = pwfFilterItemPanel.init( config );
				pwfFilterItemPanel.displayPanel('additemtype', template, '', li_content );
			} else {
				let config = {
					panelType:    item_type,
					FilterItemID: filter_id,
					requireValidate: 'true',
					predefinedPanel: '',
				};

				if ( 'productcategories' === item_type ) {
					config.panelType       = 'checkboxlist';
					config.predefinedPanel = 'productcategories';
				} else if ( 'stockstatus' === item_type ) {
					config.panelType       = 'radiolist';
					config.predefinedPanel = 'stockstatus';
				}

				let template   = pwfFilterItemPanel.init( config );
				let li_content = renderHTMLFilterItems.addNewItem( config.panelType, filter_id );
				pwfFilterItemPanel.displayPanel('additemtype', template, '', li_content );
			}
		});

		$('.pro-woo-filter').on('change', '.type-field', function( event ) {
			var value =  $(this).val();
			if ( 'color' === value ) {
				$(this).closest('.inner-fields').find('.color-container').show();
				$(this).closest('.inner-fields').find('.image-container').hide();
			} else {
				$(this).closest('.inner-fields').find('.color-container').hide();
				$(this).closest('.inner-fields').find('.image-container').show();
			}
		});

		$('.panel-group').on('change input click', ':input', function( event ) {
			processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
		});
		
		let metaImageFrame;
		$('.pro-woo-filter').on('click', '.btn-upload-image', function( event ) {
			event.preventDefault();
			var current_upload_btn = this;
			metaImageFrame = wp.media.frames.metaImageFrame = wp.media({
				button: { text: pwfTranslatedText.upload_image },
			});
			// Runs when an image is selected.
			metaImageFrame.on('select', function() {
				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attachment = metaImageFrame.state().get('selection').first().toJSON();
				// Sends the attachment URL to our custom image input field.
				$(current_upload_btn).closest('.image-container').find('.image-field').val(media_attachment.url).trigger('input');
				if ( $(current_upload_btn).closest('.color-item').find('.preview-image').length > 0 ) {
					$(current_upload_btn).closest('.color-item').find('.preview-image').remove();
				}
				$(current_upload_btn).closest('.color-item').find('.preview-holder').append('<img class="preview-image" src="' + media_attachment.url + '">');
			});
			// Opens the media library frame.
			metaImageFrame.open();
		});

		$('.pro-woo-filter').on('click', '.panel-container .panel-header .nav-tab-heading', function( event ) {
			event.preventDefault();
			$(this).closest('.panel-container').find('.active-tab').removeClass('active-tab');
			$(this).addClass('active-tab');
			let currentTab = $(this).attr('data-tab-id');
			$(this).closest('.panel-container').find('.tab-content').hide();
			$(this).closest('.panel-container').find('.wrap-tabs').find('[data-tab-id=' + currentTab +']').addClass('active-tab').show();		
		});

		$('.pro-woo-filter').on('click', '.add-rule', function( event ) {
			event.preventDefault();
			processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
			renderHTMLFormField.displayRulesField.addNewRule( $(this) );
		});

		$('.pro-woo-filter').on('click', '.remove-rule', function( event ) {
			event.preventDefault();
			processingPanelForm.addAttributeRequiredValidate( 'item_panel', 'true');
			renderHTMLFormField.displayRulesField.removeRule( $(this) );
		});

		$('.pro-woo-filter').on('change', '[name="user_roles"], [name="item_display"], [name="source_of_options"], [name="item_source_category"], [name="item_source_taxonomy_sub"], [name="item_source_attribute"], [name="item_source_taxonomy"]', function( event ) {
			let name = $(this).attr('name');
			if ( name === 'source_of_options' || name === 'item_source_taxonomy' ) {
				panelEvents.changeTaxonomyDropdowList();
			}
			
			panelEvents.changeIncludeExcludeFieldData();
			panelEvents.changeboxlistlabel();
			if( $('.panel-colorlist').length > 0 ) {
				panelEvents.fillAjaxColorFields();
			}
		});

		$('.pro-woo-filter').on( 'input', '[name="url_key"], [name="url_key_min_price"], [name="url_key_max_price"], [name="url_key_date_before"], [name="url_key_date_after"], [name="url_key_range_slider_min"], [name="url_key_range_slider_max"]', function( event ) {
			event.preventDefault();
			let filterID  = $(this).closest('.panel-item').attr('data-filter-panel-id');
			let uniqueURL = isUniqueUrlKey.init( $(this).val(), filterID );
			if ( ! uniqueURL ) {
				if ( ! $(this).closest('.control-content').find('.alert-danger').length > 0 ) {
					$(this).closest('.control-content').append( get_alert_message_unique_url() );
				}
			} else {
				if ( $(this).closest('.control-content').find('.alert-danger').length > 0 ) {
					$(this).closest('.control-content').find('.alert-danger').remove();
				}
			}
		});

		$('.pro-woo-filter').on('change', '.field-rule-param', function( event ) {
			var current = this;
			var selected_parm = $(this).val();

			if ( 'category' === selected_parm ) {
				var product_categoris = pwfWooFilterData.product_categories;
				$(current).closest('.rule').find('.field-rule-value-select2').empty().select2({
					data: product_categoris,
					width: '100%',
				});
			} else {
				if ( cached_taxonomy_rules.hasOwnProperty( selected_parm ) ) {
					$(current).closest('.rule').find('.field-rule-value-select2').empty().select2({
						data: cached_taxonomy_rules[selected_parm],
						width: '100%',
					});
				} else {
					var ajax_query = pwfAjaxGetTaxonomiesData( 'get_group_taxonomies_using_ajax', '', selected_parm );
					ajax_query.done(function(results){
						cached_taxonomy_rules[selected_parm] = results.data;
						$(current).closest('.rule').find('.field-rule-value-select2').empty().select2({
							data: results.data,
							width: '100%',
						});
					});
					ajax_query.fail(function(jqXHR, textStatus, errorThrown){
					});
				}
			}
		});

		$('.pro-woo-filter').on( 'change', '[name="price_url_format"]', function( event ) {
			var value =  $(this).val();
			if ( 'dash' === value ) {
				$('[name="url_key_min_price"], [name="url_key_max_price"]').closest('.control-group').slideUp('fast');
			} else {
				$('[name="url_key_min_price"], [name="url_key_max_price"]').closest('.control-group').slideDown('fast');
			}
			processingPanelForm.addAttributeRequiredValidate( '', 'true');
		});

		$('.pro-woo-filter').on( 'change', '[name="range_slider_url_format"]', function( event ) {
			var value =  $(this).val();
			if ( 'dash' === value ) {
				$('[name="url_key_range_slider_min"], [name="url_key_range_slider_max"]').closest('.control-group').slideUp('fast');
			} else {
				$('[name="url_key_range_slider_min"], [name="url_key_range_slider_max"]').closest('.control-group').slideDown('fast');
			}
			processingPanelForm.addAttributeRequiredValidate( '', 'true');
		});

		$('.pro-woo-filter').on( 'change', '[name="source_of_options"]', function( event ) {
			let sourceOfOption = $(this).val();
			let default_arr = ['category', 'taxonomy' ];
			if( $.inArray( sourceOfOption, default_arr ) !== -1 ) {
				$('[name="display_hierarchical"]').closest('.control-group').slideDown('fast');
				if ( $('[name="display_hierarchical"]').is(":checked") ) {
					$('[name="display_hierarchical_collapsed"]').closest('.control-group').slideDown('fast');
				}
			} else {
				$('[name="display_hierarchical"]').closest('.control-group').slideUp('fast');
				$('[name="display_hierarchical_collapsed"]').closest('.control-group').slideUp('fast');
			}

			if ( sourceOfOption === 'meta') {
				$('[name="meta_key"], [name="meta_compare"], [name="meta_type"], .pwf-meta-field-box').closest('.control-group').slideDown('fast');
				
				// @ since 1.1.4
				if ( $('.panel-item.item-panel').hasClass('panel-rangeslider') ) {
					$('[name="range_slider_meta_source"]').closest('.control-group').slideDown('fast');
					$('[name="meta_key"]').closest('.control-group').slideUp('fast');
				}
			} else {
				$('[name="meta_key"], [name="meta_compare"], [name="meta_type"], .pwf-meta-field-box').closest('.control-group').slideUp('fast');				
				// @ since 1.1.4
				if ( $('.panel-item.item-panel').hasClass('panel-rangeslider') ) {
					$('[name="range_slider_meta_source"]').closest('.control-group').slideUp('fast');
				}
			}

			if ( sourceOfOption === 'category') {
				$('[name="item_source_category"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="item_source_category"]').closest('.control-group').slideUp('fast')
			}

			if ( 'attribute' === sourceOfOption ) {
				$('[name="item_source_attribute"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="item_source_attribute"]').closest('.control-group').slideUp('fast')
			}

			if ( 'author' === sourceOfOption ) {
				$('[name="user_roles"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="user_roles"]').closest('.control-group').slideUp('fast')
			}

			if ( 'taxonomy' === sourceOfOption ) {
				$('[name="item_source_taxonomy"]').closest('.control-group').slideDown('fast');
				$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').slideDown('fast');
			} else {
				$('[name="item_source_taxonomy"]').closest('.control-group').slideUp('fast')
				$('.ajax-dropdown-field-item_source_taxonomy_sub').closest('.control-group').slideUp('fast');
			}

			if ( 'stock_status' === sourceOfOption ) {
				$('[name="item_source_stock_status"]').closest('.control-group').slideDown('fast');
				$('[name="in_stock_text"]').closest('.control-group').slideDown('fast');
				$('[name="out_of_stock_text"]').closest('.control-group').slideDown('fast');
				$('[name="on_backorder_text"]').closest('.control-group').slideDown('fast');
				$('[name="height_of_visible_content"]').closest('.control-group').hide();
				
			} else {
				$('[name="item_source_stock_status"]').closest('.control-group').slideUp('fast')
				$('[name="in_stock_text"]').closest('.control-group').slideUp('fast');
				$('[name="out_of_stock_text"]').closest('.control-group').slideUp('fast');
				$('[name="on_backorder_text"]').closest('.control-group').slideUp('fast');
				$('[name="height_of_visible_content"]').closest('.control-group').show();
			}

			if ( 'stock_status' === sourceOfOption || 'meta' === sourceOfOption ) {
				$('.pwf-box-list-label').closest('.control-group').slideUp('fast');
			} else {
				$('.pwf-box-list-label').closest('.control-group').slideDown('fast');
			}

			if ( 'orderby' === sourceOfOption ) {
				$('[name="item_source_orderby"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="item_source_orderby"]').closest('.control-group').slideUp('fast')
			}

			if ( $('[name="dropdown_style"]').length > 0 ) {
				panelEvents.dropdownDisplayFields($('[name="dropdown_style"]').val());
			}

			let displayItemViewIn = [ 'category', 'taxonomy', 'tag', 'attribute', 'author' ];
			if( displayItemViewIn.includes( sourceOfOption ) ) {
				$('[name="item_display"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="item_display"]').closest('.control-group').slideUp('fast');
			}

			processingPanelForm.addAttributeRequiredValidate( '', 'true');
			panelEvents.displayParentOption();
		});

		$('.pro-woo-filter').on( 'change', '[name="display_hierarchical"]', function( event ) {
			if ( $(this).is(":checked") ) {
				$('[name="display_hierarchical_collapsed"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="display_hierarchical_collapsed"]').closest('.control-group').slideUp('fast');
			}        
		});

		$('.pro-woo-filter').on('change', '[name="dropdown_style"]', function( event ) {
			panelEvents.dropdownDisplayFields( $(this).val() );
		});

		$('.pro-woo-filter').on('change', '[name="inline_style"]', function( event ) {
			if ( $(this).is(":checked") ) {
				$('[name="display_hierarchical"]').closest('.control-group').slideUp('fast');
			} else {
				$('[name="display_hierarchical"]').closest('.control-group').slideDown('fast');
			}        
		});

		$('.pro-woo-filter').on('change', '[name="display_title"]', function( event ) {
			if ( $(this).is(":checked") ) {
				$('[name="display_toggle_content"]').closest('.control-group').slideDown('fast');
				if ( $('[name="display_toggle_content"]').is(":checked") ) {
					$('[name="default_toggle_state"]').closest('.control-group').slideDown('fast');
				}
			} else {
				$('[name="display_toggle_content"]').closest('.control-group').slideUp('fast');
				$('[name="default_toggle_state"]').closest('.control-group').slideUp('fast');
			}        
		});

		$('.pro-woo-filter').on('change', '[name="display_toggle_content"]', function( event ) {
			if ( $(this).is(":checked") ) {
				$('[name="default_toggle_state"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="default_toggle_state"]').closest('.control-group').slideUp('fast');
			}        
		});

		$('.pro-woo-filter').on('click', '.pwf-add-new-meta', function( event ) {
			event.preventDefault();
			let Paneltype = $('.panel-group .item-panel').attr('data-panel-type');
			metaFields.addMetaField( Paneltype );
		});
		$('.pro-woo-filter').on('click', '.pwf-meta-remove-action', function( event ) {
			event.preventDefault();
			metaFields.removeMetaField( $(this) );
		});

		$('.pro-woo-filter').on('click', '.pwf-meta-edit-action', function( event ) {
			event.preventDefault();
			metaFields.editMetaField( $(this) );
		});

		$('.pro-woo-filter').on('change', '[name="multi_select"]', function( event) {
			let multiSelectVal = $('[name="multi_select"]:checked').val();
			if ( 'on' === multiSelectVal ) {
				$('[name="query_type"]').closest('.control-group').slideDown();
			} else {
				$('[name="query_type"]').closest('.control-group').slideUp();
			}
		});

		$('.pro-woo-filter').on('change', '[name="more_options_by"]', function( event) {
			let moreOptionsBy = $(this).val();
			let displayHeightOfVisibleContentField = [ 'scrollbar', 'morebutton' ];
			if ( displayHeightOfVisibleContentField.includes( moreOptionsBy ) ) {
				$('[name="height_of_visible_content"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="height_of_visible_content"]').closest('.control-group').slideUp('fast');
			}
		});

		$('.pro-woo-filter').on( 'change', '[name="usecomponents"]', function( event ) {
			let checkedValues = [];
			$.each( $('input[name="usecomponents"]:checked'), function(){
				checkedValues.push( $(this).val() );
			});
			checkUseComponents( checkedValues );
		});

		function checkUseComponents( checkedValues ) {
			if ( checkedValues.includes('pagination') ) {
				$('[name="pagination_selector"]').attr('aria-required', 'true' );
				$('[name="pagination_selector"]').closest('.control-group').show();
				$('[name="pagination_ajax"]').closest('.control-group').slideDown();
				$('[name="pagination_type"]').closest('.control-group').slideDown();
			} else {
				$('[name="pagination_selector"]').closest('.control-group').hide();
				$('[name="pagination_selector"]').removeAttr('aria-required');
				$('[name="pagination_ajax"]').closest('.control-group').slideUp();
				$('[name="pagination_type"]').closest('.control-group').slideUp();
			}

			
			if ( checkedValues.includes('sorting') ) {
				$('[name="sorting_selector"]').closest('.control-group').show();
				$('[name="sorting_selector"]').attr('aria-required', 'true' );
				$('[name="sorting_ajax"]').closest('.control-group').slideDown();
			} else {
				$('[name="sorting_selector"]').closest('.control-group').hide();
				$('[name="sorting_selector"]').removeAttr('aria-required');
				$('[name="sorting_ajax"]').closest('.control-group').slideUp();
			}

			if ( checkedValues.includes('results_count') ) {
				$('[name="result_count_selector"]').closest('.control-group').show();
				$('[name="result_count_selector"]').attr('aria-required', 'true' );
			} else {
				$('[name="result_count_selector"]').closest('.control-group').hide();
				$('[name="result_count_selector"]').removeAttr('aria-required');
			}
		}
		let useComponentcheckedValues = [];
		$.each( $('input[name="usecomponents"]:checked'), function(){
			useComponentcheckedValues.push( $(this).val() );
		});
		checkUseComponents( useComponentcheckedValues );

		$('.pro-woo-filter').on('click', 'transparent', function( event ) {
			event.preventDefault();
		});

		// version 1.0.3
		$('.pro-woo-filter').on('change', '[name="display_filter_as"]', function( event ) {
			displayFilterAs($(this).val());
		});
		if ( $('[name="display_filter_as"]').length > 0 ) {
			displayFilterAs( $('[name="display_filter_as"]').val() );
		}
		function displayFilterAs( selectedVal ) {
			if ( 'button' === selectedVal ) {
				$('[name="filter_button_state"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="filter_button_state"]').closest('.control-group').slideUp('fast');
			}
		}

		$('.pro-woo-filter').on('change', '[name="responsive"]', function( event ) {
			PWFResponsive($('[name="responsive"]:checked').val());
		});
		if ( $('[name="responsive"]').length > 0 ) {
			PWFResponsive( $('[name="responsive"]:checked').val() );
		}
		function PWFResponsive( selectedVal ) {
			if ( 'on' === selectedVal ) {
				$('[name="responsive_filtering_starts"]').closest('.control-group').slideDown('fast');
				$('[name="responsive_append_sticky"]').closest('.control-group').slideDown('fast');
				$('[name="responsive_width"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="responsive_filtering_starts"]').closest('.control-group').slideUp('fast');
				$('[name="responsive_append_sticky"]').closest('.control-group').slideUp('fast');
				$('[name="responsive_width"]').closest('.control-group').slideUp('fast');
			}
		}

		$('.pro-woo-filter').on('change', '[name="is_shortcode"]', function( event ) {
			PWFDisplayShortcodeString($('[name="is_shortcode"]:checked').val());
		});
		if ( $('[name="is_shortcode"]').length > 0 ) {
			PWFDisplayShortcodeString( $('[name="is_shortcode"]:checked').val() );
		}
		function PWFDisplayShortcodeString( selectedVal ) {
			if ( 'on' === selectedVal ) {
				$('[name="shortcode_string"]').closest('.control-group').slideDown('fast');
			} else {
				$('[name="shortcode_string"]').closest('.control-group').slideUp('fast');
			}
		}

		// @since 1.1.4
		$('.pro-woo-filter').on('change', '[name="range_slider_meta_source"]', function( event ) {
			let selected = $(this).val();
			if ( 'custom' === selected ) {
				$('[name="meta_key"]').closest('.control-group').slideDown();
			} else {
				$('[name="meta_key"]').closest('.control-group').slideUp()
			}
		});
	}
	
	/*
	 * check if this new/edit post filter
	 * Edit create filter items
	 */
	if ( pwfWooFilterData.hasOwnProperty( 'filter_meta' ) ) {
		var post_filter_meta = JSON.parse( pwfWooFilterData.filter_meta );
		if ( '' !== post_filter_meta.setting && 'false' !== post_filter_meta.setting ) {
			setting_data = post_filter_meta.setting;
		} 
		if ( '' !== post_filter_meta.items && null !== post_filter_meta.items ) {
			renderHTMLFilterItems.createFilterItems( post_filter_meta.items );
		} else {
			renderHTMLFilterItems.initSortEvent();
		}
		panelEvents.initEditPanelEvent();
	}

	// Add filter post title
	if ( pwfWooFilterData.current_screen === 'edit' ) {
		let postTitle = post_filter_meta.setting['post_title'];
		$('.wp-heading-inline').append( '<span class="heading-filter-title">' + postTitle + '</span>' );
	}
	
	pwfPluginEvents();
}(jQuery));