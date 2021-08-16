<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Parse_Query_Vars' ) ) {

	class Pwf_Parse_Query_Vars {
		protected $filter_id;
		protected $filter_setting;
		protected $filter_items;
		protected $tax_query         = array();
		protected $meta_query        = array();
		protected $has_price_item    = false;
		protected $price_item_values = '';
		protected $selected_items    = array(); // hold url key as key and selected values for item selected
		protected $orderby           = '';
		protected $authors_id        = array();
		protected $date_query        = array();
		protected $custom_tax_query  = array();
		protected $custom_meta_query = array();
		protected $tax_query_items   = array();
		protected $filter_items_key  = array(); // this include date and price hold active filter items keys used in api
		protected $search_query      = '';

		public function __construct( $filter_id, $query_vars ) {
			$this->filter_id      = $filter_id;
			$meta                 = get_post_meta( absint( $this->filter_id ), '_pwf_woo_post_filter', true );
			$this->filter_items   = $meta['items'];
			$this->filter_setting = $meta['setting'];

			add_action( 'pwf_init_parse_query', array( $this, 'integrate_shortcode' ), 10, 1 );

			do_action( 'pwf_init_parse_query', $this->filter_id );

			$this->parse_query_vars( $query_vars );
		}

		public function get_filter_items_key() {
			return $this->filter_items_key;
		}
		public function get_filter_id() {
			return $this->filter_id;
		}

		public function get_tax_query() {
			return $this->tax_query;
		}

		public function get_meta_query() {
			return $this->meta_query;
		}

		public function get_date_query() {
			return $this->date_query;
		}

		public function get_filter_items() {
			return $this->filter_items;
		}

		public function get_filter_setting() {
			return $this->filter_setting;
		}

		public function selected_items() {
			return $this->selected_items;
		}

		public function has_price_item() {
			return $this->has_price_item;
		}

		public function get_current_min_max_price() {
			return $this->price_item_values;
		}

		public function get_products_orderby() {
			return $this->orderby;
		}

		public function get_authors_id() {
			return $this->authors_id;
		}

		public function get_tax_query_filter_items() {
			return $this->tax_query_items;
		}

		/**
		 * Used to get tax query with product visibilty
		 * and add current archive product page like category, tag, taxonomy
		 */
		public function get_custom_tax_query() {
			return $this->custom_tax_query;
		}

		public function get_custom_meta_query() {
			return $this->custom_meta_query;
		}

		public function get_search_query() {
			return $this->search_query;
		}

		private function get_current_page_tax_query() {
			$tax_query = array();
			if ( isset( $GLOBALS['pwf_main_query_done'] ) && isset( $GLOBALS['pwf_main_query_done']['current_taxonomy_name'] ) ) {
				$taxonomy_id   = absint( $GLOBALS['pwf_main_query_done']['current_taxonomy_id'] );
				$taxonomy_name = $GLOBALS['pwf_main_query_done']['current_taxonomy_name'];
				$tax_query[]   = array(
					'taxonomy'         => esc_attr( $taxonomy_name ),
					'field'            => 'term_id',
					'terms'            => $taxonomy_id,
					'operator'         => 'IN',
					'include_children' => true,
				);
			}

			return $tax_query;
		}

		private function append_custom_tax_query( $filter_tax_query ) {
			$product_visibility   = self::get_product_visibility();
			$current_shop_archive = $this->get_current_page_tax_query();

			$this->custom_tax_query = apply_filters( 'pwf_parse_taxonomy_query', array_merge( $product_visibility, $current_shop_archive ), $this->filter_id );

			$tax_query = array_merge( $this->custom_tax_query, $filter_tax_query );

			return $tax_query;
		}

		private function append_custom_meta_query( $filter_meta_query ) {
			$meta_query = array();
			$meta_query = apply_filters( 'pwf_parse_meta_query', $meta_query, $this->filter_id );
			if ( ! empty( $meta_query ) ) {
				$meta_query['relation'] = 'AND';
			}
			$this->custom_meta_query = $meta_query;
			$filter_meta_query       = array_merge( $this->custom_meta_query, $filter_meta_query );
			if ( ! empty( $filter_meta_query ) ) {
				if ( ! isset( $filter_meta_query['relation'] ) ) {
					$filter_meta_query['relation'] = 'AND';
				}
			}

			return $filter_meta_query;
		}

		public static function get_product_visibility() {

			$tax_query['relation'] = 'AND';

			$exclude_from_catalog = array(
				'taxonomy'         => 'product_visibility',
				'terms'            => array( 'exclude-from-catalog' ),
				'field'            => 'slug',
				'operator'         => 'NOT IN',
				'include_children' => true,
			);

			$tax_query[] = $exclude_from_catalog;

			/* this doesnt do any thing
			 * if yes to get option
			 */
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$tax_query[] = array(
					'taxonomy'         => 'product_visibility',
					'terms'            => array( 'outofstock' ),
					'field'            => 'slug',
					'operator'         => 'NOT IN',
					'include_children' => true,
				);
			}

			return $tax_query;
		}

		/**
		 * parse query get by frontend
		 *
		 * set $meta_query
		 * set $tax_query
		 * set price_query
		 */
		private function parse_query_vars( $query_vars ) {
			$tax_query    = array();
			$meta_query   = array();
			$filter_items = self::get_filter_items_without_columns( $this->filter_items );

			if ( ! empty( $query_vars ) ) {
				foreach ( $filter_items as $item ) {
					if ( ! isset( $item['url_key'] ) || empty( $item['url_key'] ) ) {
						continue;
					}

					// used if request come from api
					$is_price_item   = false;
					$is_date_item    = false;
					$is_range_slider = false;
					if ( 'priceslider' === $item['item_type'] && 'two' === $item['price_url_format'] ) {
						if ( array_key_exists( $item['url_key_min_price'], $query_vars ) || array_key_exists( $item['url_key_max_price'], $query_vars ) ) {
							$is_price_item = true;
						}
					}

					if ( 'date' === $item['item_type'] ) {
						if ( array_key_exists( $item['url_key_date_after'], $query_vars ) || array_key_exists( $item['url_key_date_before'], $query_vars ) ) {
							$is_date_item = true;
						}
					}

					if ( 'rangeslider' === $item['item_type'] && 'two' === $item['range_slider_url_format'] ) {
						if ( array_key_exists( $item['url_key_range_slider_min'], $query_vars ) || array_key_exists( $item['url_key_range_slider_max'], $query_vars ) ) {
							$is_range_slider = true;
						}
					}

					if ( ( array_key_exists( $item['url_key'], $query_vars ) && ! empty( $query_vars[ $item['url_key'] ] ) ) || $is_price_item || $is_date_item || $is_range_slider ) {
						$url_key = $item['url_key'];
						if ( 'priceslider' !== $item['item_type'] && 'date' !== $item['item_type'] ) {
							$values = $query_vars[ $url_key ];
						}

						if ( 'priceslider' === $item['item_type'] ) {
							if ( 'two' === $item['price_url_format'] ) {
								if ( array_key_exists( $item['url_key_min_price'], $query_vars ) || array_key_exists( $item['url_key_max_price'], $query_vars ) ) {
									$values    = array();
									$min_price = ( $query_vars[ $item['url_key_min_price'] ] ) ?? 0;
									$max_price = ( $query_vars[ $item['url_key_max_price'] ] ) ?? PHP_INT_MAX;
									$values    = array( $min_price, $max_price );
								} elseif ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( ',', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							} else {
								if ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( '-', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							}
						} elseif ( 'rangeslider' === $item['item_type'] ) {
							if ( 'two' === $item['range_slider_url_format'] ) {
								if ( array_key_exists( $item['url_key_range_slider_min'], $query_vars ) || array_key_exists( $item['url_key_range_slider_max'], $query_vars ) ) {
									$values    = array();
									$min_value = $query_vars[ $item['url_key_range_slider_min'] ];
									$max_value = $query_vars[ $item['url_key_range_slider_max'] ];
									$values    = array( $min_value, $max_value );
								} elseif ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( ',', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							} else {
								if ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( '-', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							}
						} elseif ( 'date' === $item['item_type'] ) {
							// need to add if request come from ajax or API
							if ( array_key_exists( $item['url_key_date_after'], $query_vars ) || array_key_exists( $item['url_key_date_before'], $query_vars ) ) {
								$after  = $query_vars[ $item['url_key_date_after'] ];
								$before = $query_vars[ $item['url_key_date_before'] ];
								$values = array( $after, $before );
							} elseif ( is_array( $query_vars[ $url_key ] ) ) {
								// from ajax
								$values = $query_vars[ $url_key ];
							}
						} elseif ( ! is_array( $values ) ) {
							$values = explode( ',', $query_vars[ $url_key ] );
						}

						/**
						 * check item price slider with dash split it into array
						 */
						$values = array_map( 'esc_attr', $values );

						if ( 'priceslider' === $item['item_type'] ) {
							if ( count( $values ) === 2 ) {
								$this->has_price_item             = true;
								$values                           = array_map( 'absint', $values );
								$this->price_item_values          = $values;
								$this->selected_items[ $url_key ] = $values;
								if ( 'two' === $item['price_url_format'] ) {
									array_push( $this->filter_items_key, $item['url_key_min_price'] );
									array_push( $this->filter_items_key, $item['url_key_max_price'] );
								} else {
									array_push( $this->filter_items_key, $url_key );
								}
							}
						} elseif ( 'rangeslider' === $item['item_type'] ) {
							if ( count( $values ) === 2 ) {
								$values = array( floatval( wc_clean( wp_unslash( $values[0] ) ) ), floatval( wc_clean( wp_unslash( $values[1] ) ) ) );
								if ( $values[0] && $values[1] ) {
									if ( 'meta' === $item['source_of_options'] ) {

										if ( 'custom' === $item['range_slider_meta_source'] ) {
											$meta_key = $item['meta_key'];
										} else {
											$meta_key = $item['range_slider_meta_source'];
										}

										$meta = array(
											'key'     => esc_attr( $meta_key ),
											'value'   => $values,
											'compare' => 'BETWEEN',
											'type'    => 'NUMERIC',
										);

										array_push( $meta_query, $meta );
									} else {
										$tax = self::get_range_slider_tax_query( $item, $values );
										array_push( $tax_query, $tax );
									}

									$this->selected_items[ $url_key ] = $values;
									if ( 'two' === $item['range_slider_url_format'] ) {
										array_push( $this->filter_items_key, $item['url_key_range_slider_min'] );
										array_push( $this->filter_items_key, $item['url_key_range_slider_max'] );
									} else {
										array_push( $this->filter_items_key, $url_key );
									}
								}
							}
						} elseif ( 'date' === $item['item_type'] && count( $values ) === 2 ) {
							$date_from = $values[0];
							$date_to   = $values[1];
							if ( self::check_is_date( $date_from ) && self::check_is_date( $date_to ) ) {
								$year_form  = gmdate( 'Y', strtotime( $date_from ) );
								$month_form = gmdate( 'm', strtotime( $date_from ) );
								$day_form   = gmdate( 'd', strtotime( $date_from ) );
								$year_to    = gmdate( 'Y', strtotime( $date_to ) );
								$month_to   = gmdate( 'm', strtotime( $date_to ) );
								$day_to     = gmdate( 'd', strtotime( $date_to ) );
								$date_query = array(
									'relation' => 'AND',
									array(
										'after'     => array(
											'year'  => absint( $year_form ),
											'month' => absint( $month_form ),
											'day'   => absint( $day_form ),
										),
										'before'    => array(
											'year'  => absint( $year_to ),
											'month' => absint( $month_to ),
											'day'   => absint( $day_to ),
										),
										'inclusive' => true,
									),
								);

								$this->date_query                 = $date_query;
								$this->selected_items[ $url_key ] = $values;
								array_push( $this->filter_items_key, $item['url_key_date_after'] );
								array_push( $this->filter_items_key, $item['url_key_date_before'] );
							}
						} elseif ( 'search' === $item['item_type'] ) {
							$this->search_query               = esc_attr( implode( ' ', $values ) );
							$this->selected_items[ $url_key ] = array_map( 'esc_attr', $values );
							array_push( $this->filter_items_key, $url_key );
						} elseif ( 'rating' === $item['item_type'] ) {
							$terms                    = array();
							$product_visibility_terms = wc_get_product_visibility_term_ids();
							if ( 'on' === $item['up_text'] ) {
								$values = explode( '-', $values[0] );
								$values = array_map( 'absint', $values );
								if ( 2 === count( $values ) ) {
									for ( $index = $values[0]; $index <= $values[1]; $index++ ) {
										array_push( $terms, $product_visibility_terms[ 'rated-' . $index ] );
									}
								} else {
									continue;
								}
							} else {
								$values = array_map( 'absint', $values );
								foreach ( $values as $value ) {
									array_push( $terms, $product_visibility_terms[ 'rated-' . $value ] );
								}
							}

							$tax = array(
								'taxonomy'      => 'product_visibility',
								'field'         => 'term_taxonomy_id',
								'terms'         => $terms,
								'operator'      => 'IN',
								'rating_filter' => true,
							);
							array_push( $tax_query, $tax );

							if ( 'on' === $item['up_text'] ) {
								$values = array( implode( '-', $values ) );
							}
							$this->selected_items[ $url_key ] = $values;
							array_push( $this->filter_items_key, $url_key );
						} elseif ( 'stock_status' === $item['source_of_options'] ) {
							$meta = array(
								'key'     => '_stock_status',
								'value'   => $values,
								'compare' => 'IN',
								'type'    => 'CHAR',
							);

							$this->selected_items[ $url_key ] = $values;
							array_push( $meta_query, $meta );
							array_push( $this->filter_items_key, $url_key );
						} elseif ( 'meta' === $item['source_of_options'] ) {
							$meta_values = $this->get_meta_values( $values, $item );
							foreach ( $meta_values as $meta_option ) {
								$meta = array(
									'key'     => $item['meta_key'],
									'value'   => $meta_option['value'],
									'compare' => $item['meta_compare'],
									'type'    => $item['meta_type'],
								);
								array_push( $meta_query, $meta );
							}

							$this->selected_items[ $url_key ] = $values;
							array_push( $meta_query, $meta );
							array_push( $this->filter_items_key, $url_key );
						} elseif ( 'orderby' === $item['source_of_options'] ) {
							$this->orderby                    = $values;
							$this->selected_items[ $url_key ] = $values;
							array_push( $this->filter_items_key, $url_key );
						} elseif ( 'author' === $item['source_of_options'] ) {
							$values                           = array_map( 'absint', $values );
							$this->authors_id                 = array_merge( $this->authors_id, $values );
							$this->selected_items[ $url_key ] = $values;
							array_push( $this->filter_items_key, $url_key );
						} else {
							$operator = 'IN';
							if ( isset( $item['query_type'] ) && 'or' !== $item['query_type'] ) {
								$operator = 'AND';
							}

							if ( 'category' === $item['source_of_options'] ) {
								$taxonomy = 'product_cat';
							} elseif ( 'attribute' === $item['source_of_options'] ) {
								$taxonomy = $item['item_source_attribute'];
							} elseif ( 'taxonomy' === $item['source_of_options'] ) {
								$taxonomy = $item['item_source_taxonomy'];
							} elseif ( 'tag' === $item['source_of_options'] ) {
								$taxonomy = 'product_tag';
							}

							$values = $this->check_is_multiselect( $item, $values );
							$values = array_map( 'absint', $this->convert_terms_slug_to_id( $values, $taxonomy ) );
							$tax    = array(
								'taxonomy'         => $taxonomy,
								'field'            => 'term_id',
								'terms'            => $values,
								'operator'         => $operator,
								'include_children' => true,
							);

							$this->selected_items[ $url_key ] = $values;
							array_push( $this->filter_items_key, $url_key );
							array_push( $tax_query, $tax );
						}
					}
				}
			}

			$this->tax_query_items = $tax_query;
			$this->tax_query       = $this->append_custom_tax_query( $tax_query );
			$this->meta_query      = $this->append_custom_meta_query( $meta_query );
		}

		/**
		 * Count number of values and return one or more depend on field type and multi select
		 * Reutn values array depend on filter type and multi select
		 */
		private function check_is_multiselect( $item, $values ) {
			if ( 'radiolist' === $item['item_type'] ) {
				// return array contain one value
				if ( is_array( $values ) ) {
					if ( 1 === count( $values ) ) {
						return $values;
					} else {
						return array( $values[0] );
					}
				} else {
					return array( $values );
				}
			}

			$multiselect_fields = array( 'colorlist', 'boxlist', 'textlist' );
			if ( in_array( $item['item_type'], $multiselect_fields, true ) && 'on' !== $item['multi_select'] ) {
				if ( is_array( $values ) ) {
					return array( $values[0] );
				} else {
					return array( $values );
				}
			}

			return $values;
		}

		private function convert_terms_slug_to_id( $terms, $taxonomy ) {
			$the_terms = array();
			if ( ! is_numeric( $terms[0] ) ) {
				foreach ( $terms as $term ) {
					$the_term = get_term_by( 'slug', $term, $taxonomy );
					if ( false !== $the_term ) {
						$the_terms[] = $the_term->term_id;
					}
				}
				$terms = $the_terms;
			} else {
				// check if the term slug is number not string useful for size taxonomy is number
				$check_term_exist = get_term_by( 'slug', $terms[0], $taxonomy );
				if ( false !== $check_term_exist ) {
					foreach ( $terms as $term ) {
						$the_term = get_term_by( 'slug', $term, $taxonomy );
						if ( false !== $the_term ) {
							$the_terms[] = $the_term->term_id;
						}
					}
					$terms = $the_terms;
				}
			}

			return $terms;
		}

		/**
		 * return items in filter post
		 * without columns or button that hasn't url_key
		 *
		 * return array
		 */
		public static function get_filter_items_without_columns( $filter_items ) {
			$items = array();
			foreach ( $filter_items as $item ) {
				if ( 'column' === $item['item_type'] ) {
					if ( ! empty( $item['children'] ) ) {
						$children = self::get_filter_items_without_columns( $item['children'] );
						$items    = array_merge( $items, $children );
					}
				} elseif ( 'button' !== $item['item_type'] ) {
					array_push( $items, $item );
				}
			}
			return $items;
		}

		/**
		* @param string
		*
		* @return bool
		*/
		private static function check_is_date( $date ) {
			if ( false !== DateTime::createFromFormat( 'Y-m-d', $date ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * @since version 1.1.2
		 */
		public function integrate_shortcode( $filter_id ) {

			$defaults = array(
				'limit'          => '',
				'columns'        => '',
				'paginate'       => false,
				'skus'           => '',
				'ids'            => '',
				'on_sale'        => false,
				'best_selling'   => false,
				'top_rated'      => false,
				'category'       => '',
				'cat_operator'   => 'IN',
				'tag'            => '',
				'tag_operator'   => 'IN',
				'attribute'      => '',
				'terms'          => '',
				'terms_operator' => 'IN',
				'visibility'     => '',
				'order'          => '',
				'orderby'        => '',
			);

			if ( isset( $this->filter_setting['is_shortcode'] ) && 'on' === $this->filter_setting['is_shortcode'] ) {
				if ( ! empty( $this->filter_setting['shortcode_string'] ) ) {
					$shortcode  = $this->filter_setting['shortcode_string'];
					$first_char = substr( $shortcode, 0, 1 );
					if ( '[' === $first_char ) {
						$shortcode = substr( $shortcode, 1 );
					}
					$last_char = substr( $shortcode, -1, 1 );
					if ( ']' === $last_char ) {
						$len       = strlen( $shortcode ) - 1;
						$shortcode = substr( $shortcode, 0, $len );
					}

					$shortcode = str_replace( 'products', '', $shortcode );
					$atts      = shortcode_parse_atts( $shortcode );

					if ( ! empty( $atts ) && is_array( $atts ) ) {
						$atts = wp_parse_args( $atts, $defaults );

						$customize_shortcode = new Pwf_Integrate_Shortcode( $filter_id, $atts );
					}
				}
			}
		}

		/**
		 * Get taxonmy query for range slider
		 *
		 * @since 1.1.4
		 *
		 * @param array $filteritem filter item options
		 * @param array $values selected values for filter item
		 *
		 * @return array taxonomy query
		 */
		public static function get_range_slider_tax_query( $filter_item, $values ) {
			$used_terms   = array();
			$min_value    = $values[0];
			$max_value    = $values[1];
			$item_display = $filter_item['item_display'] ?? '';

			if ( 'attribute' === $filter_item['source_of_options'] ) {
				$taxonomy = $filter_item['item_source_attribute'];
			} elseif ( 'taxonomy' === $filter_item['source_of_options'] ) {
				$taxonomy = $filter_item['item_source_taxonomy'];
			}

			$args = array(
				'taxonomy'   => esc_attr( $taxonomy ),
				'hide_empty' => true,
			);

			if ( 'selected' === $item_display && ! empty( $item['include'] ) ) {
				$args['include'] = array_map( 'absint', $item['include'] );
			} elseif ( 'except' === $item_display && ! empty( $item['exclude'] ) ) {
				$args['include'] = array_map( 'absint', $item['exclude'] );

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

			$terms = get_terms( $args );
			foreach ( $terms as $term ) {
				if ( $term->name >= $min_value && $term->name <= $max_value ) {
					array_push( $used_terms, $term->term_id );
				}
			}

			$tax_query = array(
				'taxonomy' => esc_attr( $taxonomy ),
				'field'    => 'term_id',
				'terms'    => $used_terms,
				'operator' => 'IN',
			);

			return $tax_query;
		}

		/**
		 * Processing the user selected metas to Understandable values to WordPress
		 *
		 * @param array $slugs realted to enduser selected values
		 * @param array $item represnt meta field
		 *
		 * @since 1.2.4
		 * @return array foreach meta option with values, value maybe string or array
		 */
		private function get_meta_values( $slugs, $item ) {
			$result         = array();
			$value_is_array = array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );
			$numeric_tyepe  = array( 'NUMERIC', 'DECIMAL', 'SIGNED', 'UNSIGNED' );
			$meta_data      = $item['metafield'];

			foreach ( $meta_data as $meta ) {
				if ( ! isset( $meta['slug'] ) ) {
					$meta['slug'] = $meta['value']; // fix version before 1.2.2
				}
				if ( in_array( $meta['slug'], $slugs, true ) ) {
					if ( in_array( $item['meta_compare'], $value_is_array, true ) ) {
						$value = explode( ',', $meta['value'] );

						if ( in_array( $item['meta_type'], $numeric_tyepe, true ) ) {
							$value = array_map( 'floatval', $value );
						} else {
							$value = array_map( 'esc_attr', $value );
						}
					} else {
						if ( in_array( $item['meta_type'], $numeric_tyepe, true ) ) {
							$value = floatval( $meta['value'] );
						} else {
							$value = esc_attr( $meta['value'] );
						}
					}

					$result[] = array(
						'slug'  => $meta['slug'],
						'value' => $value,
					);
				}
			}
			return $result;
		}
	}
}
