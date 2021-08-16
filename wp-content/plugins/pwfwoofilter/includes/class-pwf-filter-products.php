<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Filter_Products' ) ) {

	class Pwf_Filter_Products {

		protected $filter_id;

		protected $active_filter_items;

		/**
		 * @since 1.0.0
		 * @var   array
		 */
		protected $attributes;

		/**
		 * @since 1.0.0
		 * @var   array
		 */
		protected $tax_query = array();

		/**
		 * @since 1.0.0
		 * @var   array
		 */
		protected $meta_query = array();

		/**
		 * @since 1.0.0
		 * @var   array
		 */
		protected $date_query = array();

		/**
		 * @since 1.1.3
		 * @var   string
		 */
		protected $search_query = '';

		/**
		 * hold database query results.
		 * Like total, total_pages, per_page, current_page.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected $query_info = array();

		/**
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected $query_args = array();

		protected $has_price_item = false;

		protected $filter_setting;

		protected $current_min_price;
		protected $current_max_price;

		/**
		 * @since 1.0.0, 1.1.3
		 */
		public function __construct( Pwf_Parse_Query_Vars $query_vars, $attributes = array() ) {
			$this->filter_id           = $query_vars->get_filter_id();
			$this->tax_query           = $query_vars->get_tax_query();
			$this->meta_query          = $query_vars->get_meta_query();
			$this->has_price_item      = $query_vars->has_price_item();
			$this->filter_setting      = $query_vars->get_filter_setting();
			$this->date_query          = $query_vars->get_date_query();
			$this->active_filter_items = $query_vars->get_date_query();
			$this->search_query        = $query_vars->get_search_query();

			if ( $this->has_price_item ) {
				$min_max_price           = $query_vars->get_current_min_max_price();
				$this->current_min_price = $min_max_price[0];
				$this->current_max_price = $min_max_price[1];
			}

			$this->attributes = $this->parse_attributes( $attributes );
			$this->query_args = $this->parse_query_args(); // build query taxonomy
		}

		protected function set_query_info( $results ) {
			$next_page = '';
			if ( $results->total_pages > $results->current_page ) {
				$next_page = $results->current_page + 1;
			}
			$data = array(
				'total'             => $results->total,
				'total_pages'       => $results->total_pages,
				'per_page'          => $results->per_page,
				'current_page'      => $results->current_page,
				'html_result_count' => $this->get_html_result_count( $results ),
				'pagination'        => $this->get_html_woocommerce_pagination( $results ),
				'next_page'         => $next_page,
			);

			$this->query_info = $data;
		}

		public function get_query_info() {
			return $this->query_info;
		}
		/**
		 * Get attributes.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_attributes() {
			return $this->attributes;
		}

		/**
		 * Get products.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_content() {
			return $this->product_loop();
		}

		protected function get_html_result_count( $results ) {
			$args = array(
				'total'    => $results->total,
				'per_page' => $results->per_page,
				'current'  => $results->current_page,
			);

			ob_start();
			wc_get_template( 'loop/result-count.php', $args );

			return apply_filters( 'pwf_html_result_count', ob_get_clean(), $this->filter_id, $args );
		}

		protected function get_html_woocommerce_pagination( $results ) {
			$args = array(
				'total'   => $results->total_pages,
				'current' => $results->current_page,
				'base'    => str_replace( 999999999, '%#%', '/page/%#%/' ),
			);

			ob_start();
			wc_get_template( 'loop/pagination.php', $args );

			return apply_filters( 'pwf_html_pagination', ob_get_clean(), $this->filter_id, $args );
		}

		/**
		 * Parse attributes.
		 *
		 * @since  1.0.0, 1.1.7 Add per_page
		 * @param  array $attributes attributes.
		 * @return array
		 */
		protected function parse_attributes( $attributes ) {
			if ( isset( $attributes['per_page'] ) && $attributes['per_page'] > 0 ) {
				$posts_per_page = absint( $attributes['per_page'] );
			} elseif ( ! empty( $this->filter_setting['posts_per_page'] ) ) {
				$posts_per_page = $this->filter_setting['posts_per_page'];
			} else {
				$posts_per_page = get_option( 'posts_per_page' );
			}

			$defaults = array(
				'orderby'        => '',
				'order'          => '',
				'page'           => 1,
				'paginate'       => true,
				'posts_per_page' => $posts_per_page,
				'author__in'     => array(),
				'columns'        => 4,
			);

			$attributes = wp_parse_args( $attributes, $defaults );

			return apply_filters( 'pwf_woo_filter_loop_products_attributes', $attributes, $this->filter_id );
		}

		/**
		 * Parse query args.
		 *
		 * @since  1.0.0, 1.1.3
		 * @return array
		 */
		protected function parse_query_args() {
			$query_args = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'fields'              => 'ids',
				'no_found_rows'       => false === wc_string_to_bool( $this->attributes['paginate'] ),
				'orderby'             => $this->attributes['orderby'],
			);

			if ( wc_string_to_bool( $this->attributes['paginate'] ) && 1 < $this->attributes['page'] ) {
				$query_args['paged'] = absint( $this->attributes['page'] );
			}

			$orderby_value         = explode( '-', $query_args['orderby'] );
			$orderby               = esc_attr( $orderby_value[0] );
			$order                 = ! empty( $orderby_value[1] ) ? $orderby_value[1] : strtoupper( $this->attributes['order'] );
			$query_args['orderby'] = $orderby;
			$query_args['order']   = $order;

			$ordering_args         = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );
			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];
			if ( $ordering_args['meta_key'] ) {
				$query_args['meta_key'] = $ordering_args['meta_key'];
			}

			if ( ! empty( $this->attributes['author__in'] ) ) {
				$query_args['author__in'] = $this->attributes['author__in'];
			}

			$query_args['posts_per_page'] = intval( $this->attributes['posts_per_page'] );

			$query_args['tax_query']  = $this->tax_query;
			$query_args['meta_query'] = $this->meta_query;

			if ( ! empty( $this->date_query ) ) {
				$query_args['date_query'] = $this->date_query;
			}

			if ( ! empty( $this->search_query ) ) {
				$query_args['s'] = $this->search_query;
			}

			return $query_args;
		}

		/**
		 * Loop over found products.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		protected function product_loop() {
			$products = $this->get_query_results();

			ob_start();

			if ( $products && $products->ids ) {

				// Setup the loop.
				$loop_args = apply_filters(
					'pwf_wc_setup_loop_args',
					array(
						'columns'      => $this->attributes['columns'],
						'name'         => 'pwf_filter',
						'is_shortcode' => false,
						'is_search'    => false,
						'is_paginated' => wc_string_to_bool( $this->attributes['paginate'] ),
						'total'        => $products->total,
						'total_pages'  => $products->total_pages,
						'per_page'     => $products->per_page,
						'current_page' => $products->current_page,
					),
					$this->filter_id
				);

				wc_setup_loop( $loop_args );

				do_action( 'pwf_before_shop_loop', $this->filter_id );

				foreach ( $products->ids as $product_id ) {
					$GLOBALS['post'] = get_post( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $GLOBALS['post'] );

					do_action( 'pwf_before_shop_loop_item', $this->filter_id );

					$content_template = apply_filters( 'pwf_woo_filter_product_loop_template', array( 'content', 'product' ), $this->filter_id );

					wc_get_template_part( esc_attr( $content_template[0] ), esc_attr( $content_template[1] ) );

					do_action( 'pwf_after_shop_loop_item', $this->filter_id );
				}

				wp_reset_postdata();
				wc_reset_loop();

				do_action( 'pwf_after_shop_loop', $this->filter_id );
			} else {
				do_action( 'woocommerce_no_products_found' );
			}

			return ob_get_clean();
		}

		/**
		 * Join wc_product_meta_lookup to posts if not already joined.
		 *
		 * @param string $sql SQL join.
		 * @return string
		 */
		private function append_product_sorting_table_join( $sql ) {
			global $wpdb;
			if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
				$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
			}

			return $sql;
		}

		public function price_filter_post_clauses( $args, $wp_query ) {
			global $wpdb;

			$current_min_price = $this->current_min_price;
			$current_max_price = $this->current_max_price;

			/**
			 * Adjust if the store taxes are not displayed how they are stored.
			 * Kicks in when prices excluding tax are displayed including tax.
			 */
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				$tax_class = apply_filters( 'pwf_woocommerce_price_filter_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
					$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
				}
			}

			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= $wpdb->prepare(
				' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
				$current_min_price,
				$current_max_price
			);

			return $args;
		}

		/**
		 * Run the query and return an array of data, including queried ids and pagination information.
		 *
		 * @since  1.0.0, 1.2.2
		 * @return object Object with the following props; ids, per_page, found_posts, max_num_pages, current_page
		 */
		public function get_query_results() {

			$this->query_args = apply_filters( 'pwf_woo_products_loop', $this->query_args, $this->filter_id, $this->active_filter_items, $this->attributes );

			$transient_name    = $this->get_transient_name();
			$transient_version = WC_Cache_Helper::get_transient_version( 'pwf_woo_filter_product_query' );
			$cache             = apply_filters( 'pwf_woo_filter_product_loop_maybe_cache', true );
			$transient_value   = $cache ? get_transient( $transient_name ) : false;

			if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
				$results = $transient_value['value'];
			} else {
				if ( $this->has_price_item ) {
					add_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ), 10, 2 );
				}

				$query = new WP_Query( $this->query_args );

				$paginated = ! $query->get( 'no_found_rows' );

				$results = (object) array(
					'ids'          => wp_parse_id_list( $query->posts ),
					'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
					'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
					'per_page'     => (int) $query->get( 'posts_per_page' ),
					'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
				);

				if ( $this->has_price_item ) {
					remove_filter( 'posts_where', array( $this, 'price_filter_post_clauses' ), 10, 2 );
				}

				// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
				WC()->query->remove_ordering_args();

				if ( $cache ) {
					$transient_time  = get_option( 'pwf_transient_time', 86400 );
					$transient_value = array(
						'version' => $transient_version,
						'value'   => $results,
					);
					set_transient( $transient_name, $transient_value, $transient_time );
				}
			}

			$this->set_query_info( $results );

			return $results;
		}

		/**
		 * Generate and return the transient name for this shortcode based on the query args.
		 *
		 * @since 1.2.2
		 * @return string
		 */
		protected function get_transient_name() {
			$query_args = $this->query_args;
			if ( $this->has_price_item ) {
				// Fixed cache when query has price
				$query_args['price'] = array(
					'min_price' => $this->current_min_price,
					'max_price' => $this->current_max_price,
				);
			}
			$transient_name = 'pwf_woo_filter_product_loop_' . md5( wp_json_encode( $query_args ) );

			if ( 'rand' === $this->query_args['orderby'] ) {
				// When using rand, we'll cache a number of random queries and pull those to avoid querying rand on each page load.
				$rand_index      = wp_rand( 0, max( 1, absint( apply_filters( 'pwf_woocommerce_product_query_max_rand_cache_count', 5 ) ) ) );
				$transient_name .= $rand_index;
			}

			return $transient_name;
		}
	}
}
