<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Render_Filter' ) ) {

	class Pwf_Render_Filter {

		protected $filter_id;
		protected $filter_setting;
		protected $filter_items;
		protected $tax_query      = array(); // this depend on user filter on front end
		protected $meta_query     = array();
		protected $selected_items = array();
		protected $has_price_item = false; // if price slider is active
		protected $current_min_price; // if price item is active
		protected $current_max_price; // if price item is active
		protected $current_filter_item = null;
		protected $authors_id          = array();
		protected $date_query;
		protected $custom_tax_query  = array();
		protected $custom_meta_query = array();
		protected static $transient_time;
		protected $rule_hidden_items = array();
		protected static $query_parse;

		/**
		 * hold tax_query and meta_query when is_only_one_filter true
		 * @sine 1.1.3
		 */
		protected $one_filter_args = array();

		/**
		 * @since 1.0.0, 1.1.3, 1.1.6
		 */
		protected $search_query     = '';
		protected $search_query_sql = '';
		protected $date_query_sql   = '';
		protected $author_query_sql = '';
		protected $price_query_sql  = array();

		/**
		* used inside plugin JS to disable JS update query
		* see JS function checkIfPagehasFilter()
		*/
		protected $filter_done = 'no';

		/**
		 * @since 1.0.0, 1.1.6
		 */
		public function __construct( $filter_id, Pwf_Parse_Query_Vars $query_parse = null ) {
			$this->filter_id = $filter_id;

			if ( isset( $GLOBALS['pwf_main_query_done'] ) && isset( $GLOBALS['pwf_main_query_done']['filter_id'] ) && $this->filter_id === $GLOBALS['pwf_main_query_done']['filter_id'] && isset( $GLOBALS['pwf_main_query_done']['query_vars'] ) ) {
				$this->filter_done = 'yes';
				$query_parse       = $GLOBALS['pwf_main_query_done']['query_vars'];
			} elseif ( null === $query_parse ) {
				$query_parse = new Pwf_Parse_Query_Vars( $filter_id, array() );
			}

			self::$query_parse = $query_parse;

			$this->set_query_parse( $query_parse );

			$this->date_query_sql   = $this->get_where_date_query_sql();
			$this->search_query_sql = $this->get_search_query_sql();
			$this->price_query_sql  = $this->get_sql_price_filter();
			$this->author_query_sql = $this->get_authors_where_sql_query();
		}

		public function get_filter_setting() {
			return $this->filter_setting;
		}
		public function get_html() {
			$output = '';

			if ( ! wp_doing_ajax() ) {
				$css       = '';
				$css_class = '';

				if ( ! empty( $this->filter_setting['cssclass'] ) ) {
					$css_class = ' ' . esc_attr( $this->filter_setting['cssclass'] );
				}

				if ( isset( $this->filter_setting['is_shortcode'] ) && 'on' === $this->filter_setting['is_shortcode'] ) {
					$css_class .= ' pwf-woo-shortcode';
				}

				if ( 'button' === $this->filter_setting['display_filter_as'] ) {
					$css = ' pwf-filter-as-button-container';
				}

				$output .= '<div class="pwf-filter-container' . $css . '">';

				if ( 'button' === $this->filter_setting['display_filter_as'] ) {
					if ( 'hide' === $this->filter_setting['filter_button_state'] ) {
						$css_class .= ' pwf-hidden';
						$css_btn    = ' pwf-btn-closed';
					} else {
						$css_btn = ' pwf-btn-opened';
					}

					$output .= '<div class="pwf-filter-as-button-header' . $css_btn . '">';
					$output .= '<div class="pwf-filter-as-button-title">';
					$output .= '<span class="pwf-filter-as-button-icon"></span>';
					$output .= '<span class="pwf-filter-as-button-text">' . esc_html__( 'Filter', 'pwf-woo-filter' ) . '</span>';
					$output .= '</div>';
					$output .= '<div class="button-more-wrap"></div>';
					$output .= '</div>';
				}

				$output .= '<div id="filter-id-' . $this->filter_id . '" class="pwf-woo-filter filter-id-' . $this->filter_id . $css_class . '">';
				$output .= '<div class="pwf-woo-filter-notes pwf-filter-notes-' . $this->filter_id . '"><div class="pwf-note-list"></div></div>';
				$output .= '<div class="pwf-woo-filter-inner">';
			}

			$output .= $this->get_filter_items_html( $this->filter_items );

			if ( ! wp_doing_ajax() ) {
				$output .= '</div>'; // End of pro-woo-filter inner
				$output .= '</div>'; // End of pro-woo-filter
				$output .= '</div>'; // End of pwf-filter-container
				$this->add_js();
			}

			return $output;
		}

		/**
		 * used in class Pwf_Filter_Post_Type and Pwf_Api
		 * display filter post meta in API
		 *
		 * @return array
		 */
		public function get_filter_items_data() {
			return $this->prepare_filter_items_for_api( $this->filter_items );
		}

		/**
		 * @since 1.0.0, 1.2.0
		 */
		protected function add_js() {
			$filter_setting = $this->filter_setting;
			if ( isset( $filter_setting['is_shortcode'] ) ) {
				unset( $filter_setting['shortcode_string'] );
			}

			$filter_js_variables = array(
				'filter_setting' => $filter_setting,
				'filter_id'      => $this->filter_id,
				'filter_done'    => $this->filter_done,
				'selected_items' => $this->selected_items,
			);

			if ( ! empty( $this->rule_hidden_items ) ) { 
				$filter_js_variables['rule_hidden_items'] = $this->rule_hidden_items;
			}

			if ( isset( $GLOBALS['pwf_main_query_done'] ) && isset( $GLOBALS['pwf_main_query_done']['current_taxonomy_name'] ) ) {
				$filter_js_variables['current_taxonomy_id']   = $GLOBALS['pwf_main_query_done']['current_taxonomy_id'];
				$filter_js_variables['current_taxonomy_name'] = $GLOBALS['pwf_main_query_done']['current_taxonomy_name'];
			}

			if ( is_shop() && absint( get_option( 'page_on_front' ) ) === absint( wc_get_page_id( 'shop' ) ) ) {
				// Add post type to url hash if home page == shop page, this force WordPress to use template woo archive
				$filter_js_variables['add_posttype'] = apply_filters( 'pwf_add_posttype_to_url_hash', 'false' );
			}

			$script = 'var pwffilterVariables = ' . json_encode( $filter_js_variables ) . '; var pwfFilterJSItems = ' . json_encode( $this->filter_items ) . ';';
			wp_add_inline_script( 'pwf-woo-filter', $script, 'before' );
		}

		/**
		 *
		 * @since  1.0.0, 1.1.3
		 */
		protected function set_query_parse( Pwf_Parse_Query_Vars $query_parse ) {
			$this->filter_items      = $query_parse->get_filter_items();
			$this->filter_setting    = $query_parse->get_filter_setting();
			$this->tax_query         = $query_parse->get_tax_query();
			$this->meta_query        = $query_parse->get_meta_query();
			$this->selected_items    = $query_parse->selected_items();
			$this->authors_id        = $query_parse->get_authors_id();
			$this->date_query        = $query_parse->get_date_query();
			$this->custom_tax_query  = $query_parse->get_custom_tax_query();
			$this->custom_meta_query = $query_parse->get_custom_meta_query();
			$this->search_query      = $query_parse->get_search_query();

			$this->has_price_item = $query_parse->has_price_item();
			if ( $this->has_price_item ) {
				$min_max_price           = $query_parse->get_current_min_max_price();
				$this->current_min_price = $min_max_price[0];
				$this->current_max_price = $min_max_price[1];
			}
		}

		/**
		 * check if this filter item can display in current page
		 * Default return false if no condition matches hidden rules
		 * @return Bol
		 */
		private function is_hidden_filter_item() {
			$hide_rules = $this->current_filter_item['hidden_rules'] ?? ''; // for item type [ button, range slider ]  they are havn't rule
			if ( empty( $hide_rules ) || ! is_array( $hide_rules ) ) {
				return false;
			}

			/**
			 * If request comes from ajax
			 */
			$url_key = $this->current_filter_item['url_key'];
			if ( isset( $GLOBALS['rule_hidden_items'] ) && in_array( $url_key, $GLOBALS['rule_hidden_items'], true ) ) {
				return true;
			}

			foreach ( $hide_rules as $rule ) {
				if ( empty( $rule['value'] ) ) {
					continue;
				}

				if ( is_tax() ) {
					if ( 'category' === $rule['param'] && is_product_category() ) {
						if ( is_tax( 'product_cat', absint( $rule['value'] ) ) ) {
							if ( 'equalto' === $rule['equal'] ) {
								$this->set_hidden_rule_items( $url_key );
								return true;
							}
						}
					} elseif ( 'tag' === $rule['param'] && is_product_tag() ) {
						if ( is_tax( 'product_tag', absint( $rule['value'] ) ) ) {
							if ( 'equalto' === $rule['equal'] ) {
								$this->set_hidden_rule_items( $url_key );
								return true;
							}
						}
					} elseif ( 'taxonomy' === $rule['param'] ) {
						$split     = explode( '__', $rule['value'] );
						$tax_name  = $split[0];
						$tax_value = $split[1];
						if ( is_tax( $tax_name ) ) {
							if ( 'all' === $tax_value && 'equalto' === $rule['equal'] ) {
								array_push( $this->rule_hidden_items, $this->current_filter_item['url_key'] );
								return true;
							}

							if ( is_tax( $tax_name, absint( $tax_value ) ) ) {
								if ( 'equalto' === $rule['equal'] ) {
									$this->set_hidden_rule_items( $url_key );
									return true;
								}
							}
						}
					} elseif ( 'attribute' === $rule['param'] ) {
						$split     = explode( '__', $rule['value'] );
						$tax_name  = $split[0];
						$tax_value = $split[1];
						if ( is_tax( $tax_name ) ) {
							if ( 'all' === $tax_value && 'equalto' === $rule['equal'] ) {
								$this->set_hidden_rule_items( $url_key );
								return true;
							}

							if ( is_tax( $tax_name, absint( $tax_value ) ) ) {
								if ( 'equalto' === $rule['equal'] ) {
									$this->set_hidden_rule_items( $url_key );
									return true;
								}
							}
						}
					}
				} elseif ( 'page' === $rule['param'] ) {
					$current_page = '';
					if ( is_front_page() && is_home() ) {
						$current_page = get_option( 'page_for_posts' );
					} elseif ( is_front_page() ) {
						$current_page = get_option( 'page_on_front' );
					} elseif ( is_home() ) {
						$current_page = get_option( 'page_for_posts' );
					} elseif ( is_shop() ) {
						$current_page = get_option( 'woocommerce_shop_page_id' );
					} elseif ( is_page() ) {
						$current_page = get_queried_object_id();
					}

					if ( ! empty( $current_page ) ) {
						if ( absint( $rule['value'] ) === absint( $current_page ) ) {
							if ( 'equalto' === $rule['equal'] ) {
								$this->set_hidden_rule_items( $url_key );
								return true;
							}
						}
					}
				}
			}

			return false;
		}

		private function set_hidden_rule_items( $url_key ) {
			array_push( $this->rule_hidden_items, $url_key );
		}

		private function get_filter_items_html( $filter_items, $index = 0 ) {
			$output = '';
			foreach ( $filter_items as $filter_item ) {
				if ( 'column' === $filter_item['item_type'] ) {
					if ( ! empty( $filter_item['children'] ) ) {
						$width = absint( $filter_item['width'] ) ?? 100;
						$unit  = $filter_item['width_unit'];
						$css   = $filter_item['css_class'];
						if ( ! empty( $css ) ) {
							$css = ' ' . $css;
						}
						$width   = ' style="width:' . absint( $width ) . esc_attr( $unit ) . '"';
						$output .= '<div class="pwf-column pwf-column-' . $index . esc_attr( $css ) . '"' . $width . '>';
						$output .= $this->get_filter_items_html( $filter_item['children'], ++$index );
						$output .= '</div>';
					}
				} else {
					$this->current_filter_item = $filter_item;
					$output                   .= $this->get_filter_item_html( $index );
				}

				$this->current_filter_item = null;
				$index++;
			}

			return $output;
		}

		/**
		 * @since 1.0.0, 1.0.6
		 */
		protected function get_filter_item_html( $index = 0 ) {

			if ( null === $this->current_filter_item ) {
				return;
			}

			if ( $this->is_hidden_filter_item() ) {
				return '';
			}

			$args        = array(); // used to add more data like min and max price
			$filter_item = $this->current_filter_item;
			$terms       = $this->get_filter_item_data_display();

			if ( 'priceslider' === $filter_item['item_type'] ) {
				$filter_item['min_max_price'] = $terms;
			} elseif ( 'date' === $filter_item['item_type'] ) {
				$filter_item['min_max_date'] = $terms;
			} elseif ( 'rangeslider' === $filter_item['item_type'] ) {
				$filter_item['min_max_range'] = $terms;
			}

			$render_item = new Pwf_Render_Filter_Fields( $filter_item, $index, $terms, $this->get_item_values( $filter_item ), $args );

			return $render_item->get_html_template();
		}

		private function prepare_filter_items_for_api( $filter_items ) {
			$result = array();

			foreach ( $filter_items as $key => $filter_item ) {
				if ( 'column' === $filter_item['item_type'] ) {
					if ( ! empty( $filter_item['children'] ) ) {
						if ( 'on' === $this->filter_setting['api_remove_columns_layout'] ) {
							$childern = $this->prepare_filter_items_for_api( $filter_item['children'] );
							$result   = array_merge( $result, $childern );
						} else {
							$filter_item['children'] = $this->prepare_filter_items_for_api( $filter_item['children'] );
							array_push( $result, $filter_item );
						}
					}
				} else {
					$this->current_filter_item    = $filter_item;
					$filter_item['data_display']  = $this->get_filter_item_data_display();
					$filter_item['data_selected'] = $this->get_item_values( $filter_item );
					array_push( $result, $filter_item );
					$this->current_filter_item = null;
				}
			}

			return $result;
		}

		/**
		 * since 1.0.0, 1.1.6
		 */
		private function get_filter_item_data_display() {
			if ( null === $this->current_filter_item ) {
				return;
			}

			$terms       = array();
			$filter_item = $this->current_filter_item;
			// fields has source_of_options
			$fields = array( 'checkboxlist', 'radiolist', 'dropdownlist', 'colorlist', 'boxlist', 'textlist' );
			if ( in_array( $filter_item['item_type'], $fields, true ) ) {
				if ( 'stock_status' === $filter_item['source_of_options'] ) {
					$terms = $this->get_stock_status_data();
				} elseif ( 'orderby' === $filter_item['source_of_options'] ) {
					$terms = $this->get_products_orderby_data();
				} elseif ( 'meta' === $filter_item['source_of_options'] ) {
					$terms = $this->get_meta_field_items();
				} elseif ( 'author' === $filter_item['source_of_options'] ) {
					$terms = $this->get_author_items();
				} else {
					$terms = $this->get_filter_item_terms();
				}
			} elseif ( 'rangeslider' === $filter_item['item_type'] ) {
				$terms = $this->get_min_max_range_slider();
			} elseif ( 'priceslider' === $filter_item['item_type'] ) {
				$terms = $this->get_min_max_price();
			} elseif ( 'date' === $filter_item['item_type'] ) {
				$terms = $this->get_min_max_date();
			} elseif ( 'rating' === $filter_item['item_type'] ) {
				$terms = $this->get_rating();
			}

			if ( 'radiolist' === $filter_item['item_type'] || 'dropdownlist' === $filter_item['item_type'] ) {
				if ( ! empty( $terms ) && ! empty( $filter_item['show_all_text'] ) && 'orderby' !== $filter_item['source_of_options'] ) {
					$all_text = $this->get_show_all_text();
					$terms    = array_merge( $all_text, $terms );
				}
			}

			return $terms;
		}

		/**
		 * return string
		 */
		protected function get_where_date_query_sql() {
			$sql = '';
			if ( ! empty( $this->date_query ) ) {
				$date_query = new WP_Date_Query( $this->date_query );
				$sql        = $date_query->get_sql();
			}
			return $sql;
		}

		protected static function transient_time() {
			if ( empty( self::$transient_time ) ) {
				self::$transient_time = get_option( 'pwf_transient_time', 3600 );
			}

			return self::$transient_time;
		}

		/**
		 * @since 1.0.0, 1.1.6
		 */
		protected function get_item_values( $filter_item ) {
			$selected = array();
			if ( isset( $filter_item['url_key'] ) ) {
				if ( array_key_exists( $filter_item['url_key'], $this->selected_items ) ) {
					$selected = $this->selected_items[ $filter_item['url_key'] ];
				}
			}
			return $selected;
		}

		/**
		 * check if only one filter item is active
		 * Used to display all elements in this filter item with count
		 *
		 * @return bool
		 */
		private function is_only_one_filter_item_active() {
			if ( null === $this->current_filter_item || empty( $this->selected_items ) || ! empty( $this->search_query_sql ) || $this->has_price_item ) {
				return false;
			}

			/**
			 * get first key on the selected_items
			 * this code replace array_key_first( $this->selected_items )
			 */
			$selected_items = $this->selected_items;
			reset( $selected_items );
			$first_key = key( $selected_items );

			if ( 1 === count( $this->selected_items ) && $first_key === $this->current_filter_item['url_key'] ) {
				return true;
			}

			return false;
		}

		/**
		 * @since 1.0.0, 1.1.3
		 */
		protected function get_filter_item_terms() {
			if ( null === $this->current_filter_item ) {
				return;
			}
			$filter_item = $this->current_filter_item;
			$args        = $this->get_database_query_args();
			if ( empty( $args ) ) {
				return array();
			}

			/**
			 * only display all terms for current term
			 * if there is one item filter
			 * and this item not price filter
			 */
			if ( $this->is_only_one_filter_item_active() ) {
				$this->before_only_one_filter();
			}

			$terms = $this->get_counted_terms( $args );

			if ( $this->is_only_one_filter_item_active() ) {
				$this->after_only_one_filter();
			}

			return $terms;
		}

		/**
		 * @since 1.0.0, 1.1.3
		 */
		protected function get_author_items() {
			if ( null === $this->current_filter_item ) {
				return;
			}

			$filter_item = $this->current_filter_item;
			$args        = array(
				'orderby'    => esc_attr( $filter_item['order_by'] ),
				'hide_empty' => true,
				'fields'     => array( 'ID', 'display_name', 'user_nicename' ),
			);

			if ( isset( $get_users_id['include'] ) && ! empty( $get_users_id['include'] ) ) {
				$user_query_args['include'] = $get_users_id['include'];
			} elseif ( isset( $get_users_id['exclude'] ) && ! empty( $get_users_id['exclude'] ) ) {
				$user_query_args['exclude'] = $get_users_id['exclude'];
			} else {
				if ( isset( $filter_item['user_roles'] ) && ! empty( $filter_item['user_roles'] ) ) {
					$args['role__in'] = array_map( 'esc_attr', $filter_item['user_roles'] );
				}
			}

			$get_users = get_users( $args );
			if ( empty( $get_users ) ) {
				return array();
			}
			$users_id = array_column( $get_users, 'ID' );

			/**
			 * only display all terms for current term
			 * if there is one item filter
			 * and this item not price filter
			 */
			if ( $this->is_only_one_filter_item_active() ) {
				$this->before_only_one_filter();
			}

			$terms = $this->get_users_count( $users_id );

			if ( $this->is_only_one_filter_item_active() ) {
				$this->after_only_one_filter();
			}

			$users = array();
			foreach ( $get_users as $user ) {
				$users[] = (array) $user;
			}

			foreach ( $users as $key => $user ) {
				if ( isset( $terms[ $user['ID'] ] ) ) {
					$users[ $key ]['count'] = $terms[ $user['ID'] ];
				} else {
					$users[ $key ]['count'] = '';
				}
				$users[ $key ]['label'] = $users[ $key ]['display_name'];
				$users[ $key ]['value'] = $users[ $key ]['ID'];
			}

			return $users;
		}

		/**
		 * get database query for count terms and get terms
		 */
		protected function get_database_query_args() {
			if ( null === $this->current_filter_item ) {
				return array();
			}

			$args              = array();
			$return_empty      = false;
			$filter_item       = $this->current_filter_item;
			$is_exclude        = false;
			$args['orderby']   = $filter_item['order_by'] ?? '';
			$item_display      = $filter_item['item_display'] ?? '';
			$source_of_options = $filter_item['source_of_options'];

			if ( 'category' === $source_of_options ) {
				$args['taxonomy'] = 'product_cat';
			} elseif ( 'attribute' === $source_of_options ) {
				$args['taxonomy'] = $filter_item['item_source_attribute'];
			} elseif ( 'tag' === $source_of_options ) {
				$args['taxonomy'] = 'product_tag';
			} elseif ( 'taxonomy' === $source_of_options ) {
				$args['taxonomy'] = $filter_item['item_source_taxonomy'];
			} elseif ( 'author' !== $source_of_options ) {
				$args['taxonomy'] = $filter_item['source_of_options'];
			}

			if ( ( 'category' === $source_of_options || 'taxonomy' === $source_of_options ) && is_taxonomy_hierarchical( $args['taxonomy'] ) ) {
				/**
				* Related to current Page Taxonomy Tag, Taxonomy, category & ex clothing
				*/
				$current_taxonomy_id   = '';
				$current_taxonomy_name = '';

				if ( isset( $GLOBALS['pwf_main_query_done']['current_taxonomy_id'] ) && $GLOBALS['pwf_main_query_done']['current_taxonomy_name'] ) {
					$current_taxonomy_id   = $GLOBALS['pwf_main_query_done']['current_taxonomy_id'];
					$current_taxonomy_name = $GLOBALS['pwf_main_query_done']['current_taxonomy_name'];
				}

				if ( 'category' === $source_of_options ) {
					$item_source = esc_attr( $filter_item['item_source_category'] );
				} else {
					$item_source = esc_attr( $filter_item['item_source_taxonomy_sub'] );
				}

				if ( 'all' === $item_display && 'all' === $item_source ) {
					if ( $current_taxonomy_name === $args['taxonomy'] ) {
						$args['child_of'] = $current_taxonomy_id;
					}
				} elseif ( 'all' === $item_display && 'all' !== $item_source ) {
					if ( $current_taxonomy_name === $args['taxonomy'] ) {
						if ( absint( $item_source ) === $current_taxonomy_id ) {
							$args['child_of'] = $item_source;
						} else {
							$return_empty = true;
						}
					} else {
						$args['child_of'] = $item_source;
					}
				} elseif ( 'parent' === $item_display ) {
					if ( $current_taxonomy_name === $args['taxonomy'] ) {
						if ( 'all' === $item_source || absint( $item_source ) === $current_taxonomy_id ) {
							$args['parent'] = $current_taxonomy_id;
						} else {
							$return_empty = true;
						}
					} else {
						if ( 'all' === $item_source ) {
							$args['parent'] = 0;
						} else {
							$args['parent'] = $item_source;
						}
					}
				} elseif ( 'selected' === $item_display && ! empty( $filter_item['include'] ) ) {
					$item_includes = array_map( 'absint', $filter_item['include'] );
					if ( $current_taxonomy_name === $args['taxonomy'] ) {
						$include_ids = array();
						$term_ids    = get_terms(
							array(
								'taxonomy'   => esc_attr( $args['taxonomy'] ),
								'hide_empty' => false,
								'fields'     => 'ids',
								'child_of'   => absint( $current_taxonomy_id ),
							)
						);
						foreach ( $term_ids as $id ) {
							if ( in_array( $id, $item_includes, true ) ) {
								array_push( $include_ids, $id );
							}
						}

						if ( empty( $include_ids ) ) {
							$return_empty = true;
						} else {
							$args['include'] = $include_ids;
						}
					} else {
						$args['include'] = $item_includes;
					}
				} elseif ( 'except' === $item_display && ! empty( $filter_item['exclude'] ) ) {
					$filter_item['exclude'] = array_map( 'absint', $filter_item['exclude'] );
					if ( $current_taxonomy_name === $args['taxonomy'] ) {
						$term_ids = get_terms(
							array(
								'taxonomy'   => $args['taxonomy'],
								'hide_empty' => false,
								'fields'     => 'ids',
								'child_of'   => absint( $current_taxonomy_id ),
							)
						);
						foreach ( $term_ids as $key => $term_id ) {
							if ( in_array( $term_id, $filter_item['exclude'], true ) ) {
								unset( $term_ids[ $key ] );
							}
						}
						if ( empty( $term_ids ) ) {
							$return_empty = true;
						} else {
							$args['include'] = $term_ids;
						}
					} else {
						// If not work good
						$term_args = array(
							'taxonomy'   => $args['taxonomy'],
							'hide_empty' => false,
							'fields'     => 'ids',
						);
						if ( 'all' !== $item_source ) {
							$term_args['child_of'] = $item_source;
						}
						$term_ids = get_terms( $term_args );
						foreach ( $term_ids as $key => $term_id ) {
							if ( in_array( $term_id, $filter_item['exclude'], true ) ) {
								unset( $term_ids[ $key ] );
							}
						}
						$args['include'] = $term_ids;
					}
				}
			} else {
				if ( 'selected' === $item_display && ! empty( $filter_item['include'] ) ) {
					$args['include'] = array_map( 'absint', $filter_item['include'] );
				} elseif ( 'except' === $item_display && ! empty( $filter_item['exclude'] ) ) {
					$args['include'] = array_map( 'absint', $filter_item['exclude'] );

					$term_ids = get_terms(
						array(
							'taxonomy'   => $args['taxonomy'],
							'hide_empty' => false,
							'fields'     => 'ids',
						)
					);
					foreach ( $term_ids as $key => $term_id ) {
						if ( in_array( $term_id, $args['exclude'], true ) ) {
							unset( $term_ids[ $key ] );
						}
					}
					$args['include'] = $term_ids;
				}
			}

			if ( $return_empty ) {
				$args = array();
			}

			return $args;
		}

		/**
		 *
		 * @since  1.0.0, 1.1.3
		 */
		protected function get_stock_status_data() {
			$result       = array();
			$stockstausid = array();
			$filter_item  = $this->current_filter_item;
			$stock_status = $filter_item['item_source_stock_status'];

			if ( empty( $stock_status ) ) {
				return array();
			}

			foreach ( $stock_status as $status ) {
				if ( 'instock' === $status ) {
					$label = $filter_item['in_stock_text'];
					array_push( $stockstausid, 'instock' );
				} elseif ( 'outofstock' === $status ) {
					$label = $filter_item['out_of_stock_text'];
					array_push( $stockstausid, 'outofstock' );
				} elseif ( 'onbackorder' === $status ) {
					$label = $filter_item['on_backorder_text'];
					array_push( $stockstausid, 'onbackorder' );
				}

				$result[] = array(
					'label' => $label,
					'value' => $status,
					'count' => '',
				);
			}

			if ( $this->is_only_one_filter_item_active() ) {
				$this->before_only_one_filter();
			}

			$stock_status_count = $this->get_stock_status_counts( $stockstausid );

			if ( $this->is_only_one_filter_item_active() ) {
				$this->after_only_one_filter();
			}

			foreach ( $result as $key => $stock_status ) {
				if ( isset( $stock_status_count[ $stock_status['value'] ] ) ) {
					$result[ $key ]['count'] = $stock_status_count[ $stock_status['value'] ];
				}
			}

			return $result;
		}

		/**
		 * @since  1.0.0, 1.1.3
		 */
		protected function get_meta_field_items() {
			/**
			 * return array
			 * label, value, count
			 */
			$result         = array();
			$value_is_array = array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );
			$numeric_tyepe  = array( 'NUMERIC', 'DECIMAL', 'SIGNED', 'UNSIGNED' );
			$meta_items     = $this->current_filter_item['metafield'];

			if ( empty( $meta_items ) ) {
				return array();
			}

			if ( $this->is_only_one_filter_item_active() ) {
				$this->before_only_one_filter();
			}

			foreach ( $meta_items as $key => $meta_item ) {
				$meta = array(
					'key'     => $this->current_filter_item['meta_key'],
					'type'    => esc_attr( $this->current_filter_item['meta_type'] ),
					'compare' => esc_attr( $this->current_filter_item['meta_compare'] ),
				);

				// require to remove this code and update the database
				if ( ! isset( $meta_item['slug'] ) ) {
					$meta['slug'] = $meta_item['value']; // fix version before 1.2.2
				}

				if ( in_array( $this->current_filter_item['meta_compare'], $value_is_array, true ) ) {
					if ( ! is_array( $meta_item['value'] ) ) {
						$meta['value'] = explode( ',', $meta_item['value'] );
					} else {
						$meta['value'] = $meta_item['value'];
					}
					if ( in_array( $meta['type'], $numeric_tyepe, true ) ) {
						$meta['value'] = array_map( 'floatval', $meta['value'] );
					} else {
						$meta['value'] = array_map( 'esc_attr', $meta['value'] );
					}
				} else {
					if ( in_array( $meta['type'], $numeric_tyepe, true ) ) {
						$meta['value'] = floatval( $meta['value'] );
					} else {
						$meta['value'] = esc_attr( $meta['value'] );
					}
				}

				$count                       = $this->get_meta_field_items_count( $meta );
				$meta_items[ $key ]['count'] = ( ! empty( $count ) ) ? $count : '';
				$meta_items[ $key ]['value'] = $meta_item['slug'];
			}

			if ( $this->is_only_one_filter_item_active() ) {
				$this->after_only_one_filter();
			}

			return $meta_items;
		}

		protected function get_products_orderby_data() {
			if ( null === $this->current_filter_item ) {
				return;
			}

			$filter_item = $this->current_filter_item;
			$result      = array();
			$order_list  = new Pwf_Meta_Data();
			$order_list  = $order_list->products_orderby();
			$orderby     = $filter_item['item_source_orderby'];
			if ( ! empty( $orderby ) ) {
				foreach ( $orderby as $value ) {
					//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					$item_exist = array_search( $value, array_column( $order_list, 'id' ) );
					if ( false !== $item_exist ) {
						$item_exist = $order_list[ $item_exist ];
						$result[]   = array(
							'label' => $item_exist['text'],
							'value' => $value,
						);
					}
				}
			}
			return $result;
		}

		protected function get_show_all_text() {
			if ( null === $this->current_filter_item ) {
				return;
			}

			$filter_item = $this->current_filter_item;
			$type        = $filter_item['source_of_options'];
			if ( 'stock_status' === $type || 'orderby' === $type || 'meta' === $type || 'author' === $type ) {

				$args = array(
					'slug'  => 'showall',
					'label' => esc_attr( $filter_item['show_all_text'] ),
					'value' => 'showall',
					'count' => 0,
				);
				if ( 'author' === $type ) {
					$args['ID']            = 'showall';
					$args['user_nicename'] = $args['slug'];
					$args['display_name']  = $args['label'];
				}

				return array( $args );
			} else {
				return array(
					(object) array(
						'term_id'          => 'showall',
						'slug'             => 'showall',
						'parent'           => 0,
						'name'             => esc_attr( $filter_item['show_all_text'] ),
						'count'            => '',
						'term_taxonomy_id' => -1,
					),
				);
			}
		}

		/**
		 * @since 1.1.6
		 */
		protected function get_min_max_price() {
			$this->before_only_one_filter();
			$min_max = (array) $this->get_filtered_price();
			$this->after_only_one_filter();

			$active_price          = (array) $this->get_filtered_price();
			$min_max['min_price']  = floatval( $min_max['min_price'] );
			$min_max['max_price']  = floatval( $min_max['max_price'] );
			$min_max['active_min'] = floatval( $active_price['min_price'] );
			$min_max['active_max'] = floatval( $active_price['max_price'] );

			return $min_max;
		}

		/**
		 * Get filtered min price for current products.
		 *
		 * @return int
		 */
		protected function get_filtered_price() {
			global $wpdb;

			$tax_query  = $this->tax_query;
			$meta_query = $this->meta_query;

			// unused code
			foreach ( $meta_query + $tax_query as $key => $query ) {
				if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
					unset( $meta_query[ $key ] );
				}
			}

			$meta_query     = new WP_Meta_Query( $meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$query           = array();
			$query['select'] = 'SELECT MIN( min_price ) as min_price, MAX( max_price ) as max_price';
			$query['from']   = "FROM {$wpdb->wc_product_meta_lookup}";
			$query['where']  = "
				WHERE product_id IN (
					SELECT ID FROM {$wpdb->posts}
					" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
					WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
					AND {$wpdb->posts}.post_status = 'publish' "
					. $this->search_query_sql
					. $this->date_query_sql
					. $this->author_query_sql
					. $tax_query_sql['where']
					. $meta_query_sql['where']
					.
				')';

			$query      = apply_filters( 'pwf_woo_price_filter_sql', $query, $this->filter_id, self::$query_parse );
			$query      = implode( ' ', $query );
			$query_hash = md5( $query );

			$cache = apply_filters( 'pwf_woo_filter_price_range_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_price_range_maybe_cache' );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_row( $query ); // phpcs:ignore
				$cached_counts[ $query_hash ] = $results;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_price_range_maybe_cache', $cached_counts, absint( self::transient_time() ) );
				}
			}

			return $cached_counts[ $query_hash ];
		}

		/**
		 * Get min and max number for range slider
		 * @since 1.1.4, 1.1.6
		 *
		 * @return array
		 */
		protected function get_min_max_range_slider() {
			$min_max     = array();
			$filter_item = $this->current_filter_item;

			if ( 'meta' === $filter_item['source_of_options'] ) {
				if ( 'custom' === $filter_item['range_slider_meta_source'] ) {
					$meta_key = $filter_item['meta_key'];
				} else {
					$meta_key = $filter_item['range_slider_meta_source'];
				}

				if ( $this->is_only_one_filter_item_active() ) {
					$this->before_only_one_filter();
				}

				$min_max = (array) $this->get_min_max_meta_range_slider( $meta_key );

				if ( $this->is_only_one_filter_item_active() ) {
					$this->after_only_one_filter();
				}

				// get default min and max value
				$this->before_only_one_filter();
				$default_min_max = (array) $this->get_min_max_meta_range_slider( $meta_key );
				$this->after_only_one_filter();
				$min_max = array(
					'min_value'  => floatval( $default_min_max['min_value'] ),
					'max_value'  => floatval( $default_min_max['max_value'] ),
					'active_min' => floatval( $min_max['min_value'] ),
					'active_max' => floatval( $min_max['max_value'] ),
				);
			} else {
				// Not working with heriachy taxonomy
				$args = $this->get_database_query_args();

				$args['count']      = true;
				$args['orderby']    = 'name';
				$args['hide_empty'] = true;

				$terms      = get_terms( $args );
				$terms_copy = $terms;
				if ( ! is_wp_error( $terms ) ) {
					$term_ids   = wp_list_pluck( $terms, 'term_id' );
					$term_names = wp_list_pluck( $terms, 'name' );
					sort( $term_names );
					if ( is_numeric( $term_names[0] ) ) {

						if ( $this->is_only_one_filter_item_active() ) {
							$this->before_only_one_filter();
						}

						$term_counts = $this->get_filtered_term_product_counts( $term_ids, $args['taxonomy'] );

						if ( $this->is_only_one_filter_item_active() ) {
							$this->after_only_one_filter();
						}

						foreach ( $terms as $key => $term ) {
							$terms[ $key ]->count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
							if ( $terms[ $key ]->count < 1 ) {
								unset( $terms[ $key ] );
							}
						}

						$active_names = array_map( 'floatval', wp_list_pluck( $terms, 'name' ) );
						sort( $active_names );

						if ( $this->is_only_one_filter_item_active() ) {
							if ( ! empty( $active_names ) ) {
								$min_max = array(
									'min_value'  => floatval( min( $active_names ) ),
									'max_value'  => floatval( max( $active_names ) ),
									'active_min' => floatval( min( $active_names ) ),
									'active_max' => floatval( max( $active_names ) ),
								);
							}
						} else {
							$this->before_only_one_filter();
							$term_counts = $this->get_filtered_term_product_counts( $term_ids, $args['taxonomy'] );
							$this->after_only_one_filter();

							foreach ( $terms_copy as $key => $term ) {
								$terms_copy[ $key ]->count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
								if ( $terms_copy[ $key ]->count < 1 ) {
									unset( $terms_copy[ $key ] );
								}
							}

							$default_names = array_map( 'floatval', wp_list_pluck( $terms_copy, 'name' ) );
							sort( $default_names );
							if ( ! empty( $default_names ) ) {
								$min_max = array(
									'min_value'  => floatval( min( $default_names ) ),
									'max_value'  => floatval( max( $default_names ) ),
									'active_min' => floatval( min( $active_names ) ),
									'active_max' => floatval( max( $active_names ) ),
								);
							}
						}
					}
				}
			}

			return $min_max;
		}

		/**
		 * @since 1.1.4
		 */
		protected function get_min_max_meta_range_slider( $meta_key ) {
			global $wpdb;
			if ( empty( $meta_key ) ) {
				return array();
			}

			$meta_query     = new WP_Meta_Query( $this->meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$query           = array();
			$query['select'] = 'SELECT MIN( cast( meta_value AS SIGNED ) ) as min_value, MAX( cast( meta_value AS SIGNED ) ) as max_value';
			$query['from']   = "FROM {$wpdb->postmeta}";
			$query['where']  = "
				Where {$wpdb->postmeta}.meta_key = '" . esc_sql( $meta_key ) . "'
				AND post_id IN (
					SELECT ID FROM {$wpdb->posts}
					" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'] . "
					WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
					AND {$wpdb->posts}.post_status = 'publish' "
					. $this->search_query_sql
					. $this->date_query_sql
					. $this->author_query_sql
					. $tax_query_sql['where']
					. $meta_query_sql['where']
					. $this->price_query_sql['where']
				. ')';

			$query      = apply_filters( 'pwf_woo_meta_range_filter_sql', $query, $this->filter_id, self::$query_parse );
			$query      = implode( ' ', $query );
			$query_hash = md5( $query );

			$cache = apply_filters( 'pwf_woo_filter_meta_range_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_meta_range' . $meta_key );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_row( $query ); // phpcs:ignore
				$cached_counts[ $query_hash ] = $results;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_meta_range' . $meta_key, $cached_counts, absint( self::transient_time() ) );
				}
			}

			return $cached_counts[ $query_hash ];
		}

		/**
		 * Special count for terms depend on filter items
		 *
		 * @since 1.0.0
		 * @since 3.0.4 Fixed hierarchical taxonomy count
		 */
		protected function get_counted_terms( $query_args ) {
			if ( empty( $query_args ) ) {
				return array();
			}
			$source_of_options = $this->current_filter_item['source_of_options'];

			$defaults = array(
				'hide_empty' => false,
				'orderby'    => 'name',
				'count'      => true,
			);

			$query_args = wp_parse_args( $query_args, $defaults );

			if ( 'order' === $query_args['orderby'] ) {
				unset( $query_args['orderby'] );
			}

			/*
			 * Using is_taxonomy_hierarchical is more powferfull than filter_item['display_hierarchical']
			 * This get exactly parent term count
			 */
			$terms = get_terms( $query_args );
			if ( is_wp_error( $terms ) ) {
				return array();
			}

			$term_ids    = wp_list_pluck( $terms, 'term_id' );
			$term_counts = array();
			if ( is_taxonomy_hierarchical( $query_args['taxonomy'] ) ) {
				$query_args['fields'] = 'ids'; // used inside loop only
				$t_has_children       = array();
				$t_no_children        = array();
				foreach ( $term_ids as $id ) {
					$children = get_term_children( $id, $query_args['taxonomy'] );
					if ( ! empty( $children ) ) {
						// this require special count
						$t_has_children[ $id ] = $children;
					} else {
						array_push( $t_no_children, $id );
					}
				}
				unset( $query_args['fields'] );

				$parent_count   = array();
				$children_count = array();
				if ( ! empty( $t_has_children ) ) {
					foreach ( $t_has_children as $key => $children_term ) {
						array_push( $children_term, absint( $key ) );
						$get_counted          = $this->get_filtered_term_product_count( $children_term, $query_args['taxonomy'] );
						$parent_count[ $key ] = $get_counted;
					}
				}

				if ( ! empty( $t_no_children ) ) {
					$children_count = $this->get_filtered_term_product_counts( $t_no_children, $query_args['taxonomy'] );
				}

				$term_counts = $parent_count + $children_count;
			} else {
				$term_counts = $this->get_filtered_term_product_counts( $term_ids, $query_args['taxonomy'] );
			}

			foreach ( $terms as $key => $term ) {
				$terms[ $key ]->count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
			}

			if ( isset( $query_args['orderby'] ) && 'count' === $query_args['orderby'] ) {
				usort( $terms, array( $this, 'sort_term_counts' ) );
			}

			return $terms;
		}

		private function sort_term_counts( $term1, $term2 ) {
			return $term1->count < $term2->count;
		}

		protected function get_sql_price_filter() {
			global $wpdb;
			$args = array(
				'join'  => ' ',
				'where' => ' ',
			);

			if ( $this->has_price_item ) {
				$args['join']   = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
				$args['where'] .= $wpdb->prepare(
					' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
					$this->current_min_price,
					$this->current_max_price
				);
			}

			return $args;
		}

		/**
		 * @since 1.0.0, 1.1.3
		 */
		protected function get_users_count( $user_id ) {
			global $wpdb;

			$meta_query     = new WP_Meta_Query( $this->meta_query ); // Use this when you need to filter with post meta
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			// Generate query. wp_posts.post_author
			$query           = array();
			$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, wp_posts.post_author as term_count_id";
			$query['from']   = "FROM {$wpdb->posts}";
			$query['join']   = "
				INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
				INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
				INNER JOIN {$wpdb->terms} AS terms USING( term_id )
				" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];

			$query['where'] = "
				WHERE {$wpdb->posts}.post_type IN ( 'product' )
				AND {$wpdb->posts}.post_status = 'publish'"
				. $this->search_query_sql
				. $this->date_query_sql
				. " AND {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $user_id ) ) . ')'
				. $this->price_query_sql['where'] . $tax_query_sql['where'] . $meta_query_sql['where'];

			$query['group_by'] = 'GROUP BY wp_posts.post_author';
			$query             = apply_filters( 'pwf_woo_get_filter_author_product_counts_query', $query, $this->filter_id, self::$query_parse );
			$query             = implode( ' ', $query );

			// We have a query - let's see if cached results of this query already exist.
			$query_hash = md5( $query );

			// Maybe store a transient of the count values.
			$cache = apply_filters( 'pwf_woo_filter_count_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_author_counts' );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
				$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
				$cached_counts[ $query_hash ] = $counts;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_author_counts', $cached_counts, absint( self::transient_time() ) );
				}
			}

			return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
		}

		/**
		* Count products within certain terms, taking the main WP query into consideration.
		*
		* This query allows counts to be generated based on the viewed products, not all products.
		*
		* see class-wc-widget-layered-nav
		* @since 1.0.0, 1.1.3, 1.1.6
		*
		* @param  array  $term_ids Term IDs.
		* @param  string $taxonomy Taxonomy.
		* @param  string $query_type Query Type.
		* @return array
		*/
		protected function get_filtered_term_product_counts( $term_ids, $taxonomy ) {
			global $wpdb;

			$meta_query     = new WP_Meta_Query( $this->meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			// Generate query.
			$query           = array();
			$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
			$query['from']   = "FROM {$wpdb->posts}";
			$query['join']   = "
				INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
				INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
				INNER JOIN {$wpdb->terms} AS terms USING( term_id )
				" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];

			$query['where'] = "
				WHERE {$wpdb->posts}.post_type IN ( 'product' )
				AND {$wpdb->posts}.post_status = 'publish'"
				. $this->search_query_sql
				. $this->date_query_sql
				. $this->author_query_sql
				. $this->price_query_sql['where'] . $tax_query_sql['where'] . $meta_query_sql['where']
				. ' AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

			$query['group_by'] = 'GROUP BY terms.term_id';
			$query             = apply_filters( 'pwf_woo_get_filter_term_product_counts_query', $query, $this->filter_id, self::$query_parse );
			$query             = implode( ' ', $query );

			// We have a query - let's see if cached results of this query already exist.
			$query_hash = md5( $query );

			// Maybe store a transient of the count values.
			$cache = apply_filters( 'pwf_woo_filter_count_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_term_counts_' . sanitize_title( $taxonomy ) );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore
				$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
				$cached_counts[ $query_hash ] = $counts;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_term_counts_' . sanitize_title( $taxonomy ), $cached_counts, self::transient_time() );
				}
			}

			return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
		}

		// only count term with childrens
		/**
		 * @since 1.0.0, 1.1.3
		 */
		protected function get_filtered_term_product_count( $term_ids, $taxonomy ) {
			global $wpdb;

			$meta_query     = new WP_Meta_Query( $this->meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			// Generate query.
			$query           = array();
			$query['select'] = "SELECT count( DISTINCT {$wpdb->posts}.ID )";
			$query['from']   = "FROM {$wpdb->posts}";
			$query['join']   = "
				INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
				INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
				INNER JOIN {$wpdb->terms} AS terms USING( term_id )
				" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];

			$query['where'] = "
				WHERE {$wpdb->posts}.post_type IN ( 'product' )
				AND {$wpdb->posts}.post_status = 'publish'"
				. $this->search_query_sql
				. $this->date_query_sql
				. $this->author_query_sql
				. $this->price_query_sql['where'] . $tax_query_sql['where'] . $meta_query_sql['where']
				. ' AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

			$query = apply_filters( 'pwf_woo_get_filter_term_product_sum_query', $query, $this->filter_id, self::$query_parse );
			$query = implode( ' ', $query );

			// We have a query - let's see if cached results of this query already exist.
			$query_hash = md5( $query );

			// Maybe store a transient of the count values.
			$cache = apply_filters( 'pwf_woo_filter_sum_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_term_count_' . sanitize_title( $taxonomy ) );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_var( $query ); // @codingStandardsIgnoreLine
				$cached_counts[ $query_hash ] = $results;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_term_count_' . sanitize_title( $taxonomy ), $cached_counts, self::transient_time() );
				}
			}
			return $cached_counts[ $query_hash ];
		}

		/**
		* Count products within certain terms, taking the main WP query into consideration.
		*
		* This query allows counts to be generated based on the viewed products, not all products.
		*
		* see class-wc-widget-layered-nav
		*
		* @since 1.0.0, 1.1.3
		*
		* @param  array  $term_ids Term IDs.
		* @param  string $taxonomy Taxonomy.
		* @param  string $query_type Query Type.
		* @return array
		*/
		protected function get_stock_status_counts( $term_ids ) {
			global $wpdb;

			$meta_query     = new WP_Meta_Query( $this->meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			// Generate query.
			$query           = array();
			$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, pwf_wc_product_meta_lookup.stock_status as term_count_id";
			$query['from']   = "FROM {$wpdb->posts}";
			$query['join']   = "
				INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
				INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
				INNER JOIN {$wpdb->terms} AS terms USING( term_id )
				INNER JOIN {$wpdb->wc_product_meta_lookup} AS pwf_wc_product_meta_lookup ON {$wpdb->posts}.ID = pwf_wc_product_meta_lookup.product_id
				" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];

			$query['where'] = "
				WHERE {$wpdb->posts}.post_type IN ( 'product' )
				AND {$wpdb->posts}.post_status = 'publish'"
				. $this->search_query_sql
				. $this->date_query_sql
				. $this->author_query_sql
				. $this->price_query_sql['where'] . $tax_query_sql['where'] . $meta_query_sql['where'] .
				' AND pwf_wc_product_meta_lookup.stock_status IN ("' . implode( '","', array_map( 'esc_attr', $term_ids ) ) . '")';

			$query['group_by'] = 'GROUP BY pwf_wc_product_meta_lookup.stock_status';
			$query             = apply_filters( 'pwf_woo_get_filter_stock_staus_product_counts_query', $query, $this->filter_id, self::$query_parse );
			$query             = implode( ' ', $query );

			// We have a query - let's see if cached results of this query already exist.
			$query_hash = md5( $query );

			// Maybe store a transient of the count values.
			$cache = apply_filters( 'pwf_woo_filter_count_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_stock_status_counts' );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
				$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
				$cached_counts[ $query_hash ] = $counts;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_stock_status_counts', $cached_counts, self::transient_time() );
				}
			}

			return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
		}

		protected function get_authors_where_sql_query() {
			global $wpdb;
			$authors_where_query_sql = '';
			if ( ! empty( $this->authors_id ) ) {
				$authors_where_query_sql = " AND {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $this->authors_id ) ) . ')';
			}

			return $authors_where_query_sql;
		}

		/**
		 * @since 1.2.3
		 */
		protected function get_meta_where_sql_query( $meta ) {
			global $wpdb;
			$meta_class = new WP_Meta_Query();
			$meta_type  = $meta_class->get_cast_for_type( $meta['type'] );

			switch ( $meta['compare'] ) {
				case 'IN':
				case 'NOT IN':
					$meta_compare_string = '(' . substr( str_repeat( ',%s', count( $meta['value'] ) ), 1 ) . ')';
					$where               = $wpdb->prepare( $meta_compare_string, $meta['value'] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					break;
				case 'BETWEEN':
				case 'NOT BETWEEN':
					$where = $wpdb->prepare( '%s AND %s', $meta['value'][0], $meta['value'][1] );
					break;

				case 'LIKE':
				case 'NOT LIKE':
					$meta_value = '%' . $wpdb->esc_like( $meta['value'] ) . '%';
					$where      = $wpdb->prepare( '%s', $meta['value'] );
					break;
				default:
					$where = $wpdb->prepare( '%s', $meta['value'] );
					break;
			}

			if ( 'CHAR' === $meta_type ) {
				$sql_where = " AND cc_wp_postmeta.meta_value {$meta['compare']} {$where}";
			} else {
				$sql_where = " AND CAST(cc_wp_postmeta.meta_value AS {$meta_type}) {$meta['compare']} {$where}";
			}

			return $sql_where;
		}

		/**
		 * @since 1.0.0, 1.2.3
		 */
		protected function get_meta_field_items_count( $meta_item ) {
			global $wpdb;

			$meta_key       = $this->current_filter_item['meta_key'];
			$meta_query     = new WP_Meta_Query( $this->meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql_where_meta = $this->get_meta_where_sql_query( $meta_item );

			// Generate query.
			$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, cc_wp_postmeta.meta_value as term_count_id";
			$query['from']   = "FROM {$wpdb->posts}";
			$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			INNER JOIN {$wpdb->postmeta} AS cc_wp_postmeta ON {$wpdb->posts}.ID = cc_wp_postmeta.post_id
			" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'];

			$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'"
			. $this->search_query_sql
			. $this->date_query_sql
			. $this->author_query_sql
			. $this->price_query_sql['where'] . $tax_query_sql['where'] . $meta_query_sql['where'] .
			' AND cc_wp_postmeta.meta_key = "' . esc_attr( $meta_key ) . '"' .
			$sql_where_meta;

			$query = apply_filters( 'pwf_woo_get_filter_meta_field_product_counts_query', $query, $this->filter_id );
			$query = implode( ' ', $query );

			// We have a query - let's see if cached results of this query already exist.
			$query_hash = md5( $query );

			// Maybe store a transient of the count values.
			$cache = apply_filters( 'pwf_woo_filter_count_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_meta_counts' );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
				$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
				$cached_counts[ $query_hash ] = $counts;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_meta_counts', $cached_counts, self::transient_time() );
				}
			}

			$counted = array_values( $cached_counts[ $query_hash ] )[0];

			return $counted;
		}

		/**
		 * @since 1.1.6
		 */
		protected function get_min_max_date() {
			$this->before_only_one_filter();
			$min_max = (array) $this->get_min_max_date_range();
			$this->after_only_one_filter();

			$active_date           = (array) $this->get_min_max_date_range();
			$min_max['active_min'] = $active_date['min_date'];
			$min_max['active_max'] = $active_date['max_date'];

			return $min_max;
		}

		/**
		 *
		 * @since 1.0.6
		 */
		protected function get_min_max_date_range() {
			global $wpdb;

			$meta_query     = new WP_Meta_Query( $this->meta_query );
			$tax_query      = new WP_Tax_Query( $this->tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$query           = array();
			$query['select'] = 'SELECT MIN( post_date ) as min_date, MAX( post_date ) as max_date';
			$query['from']   = "FROM {$wpdb->posts}";
			$query['where']  = "
				WHERE ID IN (
					SELECT ID FROM {$wpdb->posts}
					" . $this->price_query_sql['join'] . $tax_query_sql['join'] . $meta_query_sql['join'] . "
					WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
					AND {$wpdb->posts}.post_status = 'publish' "
					. $this->search_query_sql
					. $this->date_query_sql
					. $this->author_query_sql
					. $tax_query_sql['where']
					. $meta_query_sql['where']
					. $this->price_query_sql['where']
					.
				')';

			$query      = apply_filters( 'pwf_woo_date_filter_sql', $query, $this->filter_id, self::$query_parse );
			$query      = implode( ' ', $query );
			$query_hash = md5( $query );

			$cache = apply_filters( 'pwf_woo_filter_date_maybe_cache', true );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_date_range' . $meta_key );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_row( $query ); // phpcs:ignore
				$cached_counts[ $query_hash ] = array(
					'min_date' => gmdate( 'Y-m-d', strtotime( $results->min_date ) ),
					'max_date' => gmdate( 'Y-m-d', strtotime( $results->max_date ) ),
				);
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_date_range', $cached_counts, absint( self::transient_time() ) );
				}
			}

			return $cached_counts[ $query_hash ];
		}

		/**
		 *
		 * @since 1.2.2
		 */
		protected function get_rating() {

			$product_visibility_terms = wc_get_product_visibility_term_ids();

			$rating = array(
				array(
					'label' => esc_html__( 'Rate 5', 'pwf-woo-filter' ),
				),
				array(
					'label' => esc_html__( 'Rate 4', 'pwf-woo-filter' ),
				),
				array(
					'label' => esc_html__( 'Rate 3', 'pwf-woo-filter' ),
				),
				array(
					'label' => esc_html__( 'Rate 2', 'pwf-woo-filter' ),
				),
				array(
					'label' => esc_html__( 'Rate 1', 'pwf-woo-filter' ),
				),
			);

			for ( $index = 0, $i = 5; $i >= 1; $index++, $i-- ) {
				$rating[ $index ]['rate']  = $i;
				$rating[ $index ]['count'] = 0;

				if ( 'on' !== $this->current_filter_item['up_text'] ) {
					$rating[ $index ]['value']   = $i;
					$rating[ $index ]['slug']    = $i;
					$rating[ $index ]['term_id'] = $product_visibility_terms[ 'rated-' . $i ];
				} else {
					$rate_terms   = array();
					$rating_index = $i;
					for ( $rating_index; $rating_index <= 5; $rating_index++ ) {
						array_push( $rate_terms, $product_visibility_terms[ 'rated-' . $rating_index ] );
					}

					$rating[ $index ]['term_id'] = $rate_terms;
					$rating[ $index ]['slug']    = $i . '-' . 5;
					$rating[ $index ]['value']   = $i . '-' . 5;
				}
			}

			if ( 'on' === $this->current_filter_item['up_text'] ) {
				array_shift( $rating );
			}

			if ( $this->is_only_one_filter_item_active() ) {
				$this->before_only_one_filter();
			}

			if ( 'on' === $this->current_filter_item['up_text'] ) {
				foreach ( $rating as $key => $rate ) {
					$rating[ $key ]['count'] = $this->get_filtered_term_product_count( $rate['term_id'], 'rating-' . $key . '-up-text' );
				}
			} else {
				$rate_values = array_column( $rating, 'term_id' );
				$terms       = $this->get_filtered_term_product_counts( $rate_values, 'rating' );

				foreach ( $rating as $key => $rate ) {
					if ( isset( $terms[ $rate['term_id'] ] ) ) {
						$rating[ $key ]['count'] = $terms[ $rate['term_id'] ];
					}
				}
			}

			if ( $this->is_only_one_filter_item_active() ) {
				$this->after_only_one_filter();
			}

			return $rating;
		}

		/**
		 * Based on WP_Query::parse_search since 3.7.0 && WC_Query::get_main_search_query_sql
		 * @since  1.1.3
		 * @return string
		 */
		protected function get_search_query_sql() {
			global $wpdb;

			if ( empty( $this->search_query ) ) {
				return '';
			}

			// Equal to args['sentence'] in WP_Query::parse_search
			$sentence     = apply_filters( 'pwf_terms_count_search_sentence_parm', '' );
			$exact        = apply_filters( 'pwf_terms_count_search_exact_parm', '' );
			$search_terms = stripslashes( $this->search_query );
			$search_terms = str_replace( array( "\r", "\n" ), '', $search_terms );

			if ( ! empty( $sentence ) ) {
				$search_terms = array( $search_terms );
			} else {
				$search_string = $search_terms;
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $search_terms, $matches ) ) {

					$query        = new WP_Query();
					$search_terms = $query->parse_search_terms( $matches[0] );
					// If the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
					if ( empty( $search_terms ) || count( $search_terms ) > 9 ) {
						$search_terms = array( $search_string );
					}
				} else {
					$search_terms = array( $search_terms );
				}
			}

			$n = ! empty( $exact ) ? '' : '%'; // used with order by title doesn't require here

			$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );

			foreach ( $search_terms as $term ) {
				// If there is an $exclusion_prefix, terms prefixed with it should be excluded.
				$exclude = $exclusion_prefix && ( substr( $term, 0, 1 ) === $exclusion_prefix );
				if ( $exclude ) {
					$like_op  = 'NOT LIKE';
					$andor_op = 'AND';
					$term     = substr( $term, 1 );
				} else {
					$like_op  = 'LIKE';
					$andor_op = 'OR';
				}

				$like = $n . $wpdb->esc_like( $term ) . $n;
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$sql[] = $wpdb->prepare( "(({$wpdb->posts}.post_title $like_op %s) $andor_op ({$wpdb->posts}.post_excerpt $like_op %s) $andor_op ({$wpdb->posts}.post_content $like_op %s))", $like, $like, $like );
			}

			if ( ! empty( $sql ) && ! is_user_logged_in() ) {
				$sql[] = "($wpdb->posts.post_password = '')";
			}

			$search = ' AND ' . implode( ' AND ', $sql );

			return apply_filters( 'pwf_woo_search_where_string', $search, $this->filter_id, self::$query_parse );
		}

		/**
		 * @since 1.1.3, 1.1.6
		 */
		protected function before_only_one_filter() {
			$this->one_filter_args['tax_query']        = $this->tax_query;
			$this->one_filter_args['meta_query']       = $this->meta_query;
			$this->one_filter_args['date_query_sql']   = $this->date_query_sql;
			$this->one_filter_args['price_query_sql']  = $this->price_query_sql;
			$this->one_filter_args['author_query_sql'] = $this->author_query_sql;

			$this->tax_query        = $this->custom_tax_query;
			$this->meta_query       = $this->custom_meta_query;
			$this->date_query_sql   = '';
			$this->author_query_sql = '';
			$this->price_query_sql  = array(
				'join'  => ' ',
				'where' => ' ',
			);
		}

		/**
		 * @since 1.1.3, 1.1.6
		 */
		protected function after_only_one_filter() {
			$this->tax_query        = $this->one_filter_args['tax_query'];
			$this->meta_query       = $this->one_filter_args['meta_query'];
			$this->date_query_sql   = $this->one_filter_args['date_query_sql'];
			$this->price_query_sql  = $this->one_filter_args['price_query_sql'];
			$this->author_query_sql = $this->one_filter_args['author_query_sql'];
			$this->one_filter_args  = array();
		}
	}
}
