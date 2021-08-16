<?php
defined( 'ABSPATH' ) || exit;

use \DgoraWcas\Helpers;

// https://wordpress.org/plugins/ajax-search-for-woocommerce/

if ( ! class_exists( 'Pwf_Integrate_Ajax_Search_For_Woo' ) ) {

	/**
	 * @since 1.2.3
	 */
	class Pwf_Integrate_Ajax_Search_For_Woo {

		protected $post_ids = null;

		public function __construct() {
			add_action( 'pwf_before_doing_ajax', array( $this, 'search' ), 10 );
		}

		protected function post_ids( $phrase ) {
			if ( null === $this->post_ids ) {
				if ( dgoraAsfwFs()->is_premium() ) {
					$this->post_ids = Helpers::searchProducts__premium_only( $phrase );
				} else {
					$this->post_ids = Helpers::searchProducts( $phrase );
				}
			}

			return $this->post_ids;
		}

		public function search() {
			add_filter( 'pwf_woo_products_loop', array( $this, 'product_loop_set_search_post_ids' ), 10, 4 );

			add_filter( 'pwf_woo_search_where_string', array( $this, 'pwf_woo_remove_search' ), 10, 1 );
			add_filter( 'pwf_woo_get_filter_term_product_counts_query', array( $this, 'set_search_post_ids' ), 10, 3 );
			add_filter( 'pwf_woo_get_filter_term_product_sum_query', array( $this, 'set_search_post_ids' ), 10, 3 );
			add_filter( 'pwf_woo_get_filter_author_product_counts_query', array( $this, 'set_search_post_ids' ), 10, 3 );
			add_filter( 'pwf_woo_get_filter_stock_staus_product_counts_query', array( $this, 'set_search_post_ids' ), 10, 3 );
			add_filter( 'pwf_woo_get_filter_meta_field_product_counts_query', array( $this, 'set_search_post_ids' ), 10, 3 );
			add_filter( 'pwf_woo_price_filter_sql', array( $this, 'set_search_post_ids_for_min_max_price' ), 10, 3 );
			add_filter( 'pwf_woo_meta_range_filter_sql', array( $this, 'set_search_post_ids_for_min_max_price' ), 10, 3 );
			add_filter( 'pwf_woo_date_filter_sql', array( $this, 'set_search_post_ids_for_min_max_price' ), 10, 3 );
		}

		public function pwf_woo_remove_search( $search_text ) {
			return '';
		}

		public function product_loop_set_search_post_ids( $query_args, $filter_id, $active_filters, $attributes ) {
			if ( isset( $query_args['s'] ) ) {
				$post_ids = $this->post_ids( $query_args['s'] );
				unset( $query_args['s'] );

				if ( empty( $attributes['orderby'] ) ) {
					$query_args['orderby'] = 'relevance';
					$query_args['order']   = 'desc';
				}

				if ( ! empty( $post_ids ) ) {
					// not use the filter posts_where for caching, and this confict with onsale
					$query_args['post__in'] = array_map( 'absint', $post_ids );
				}
			}

			return $query_args;
		}

		public function set_search_post_ids( $query, $filter_id, $query_parse ) {
			if ( ! empty( $query_parse->get_search_query() ) ) {
				global $wpdb;
				$post_ids = $this->post_ids( $query_parse->get_search_query() );
				if ( ! empty( $post_ids ) ) {
					$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $post_ids ) ) . ')';
				}
			}

			return $query;
		}

		public function set_search_post_ids_for_min_max_price( $query, $filter_id, $query_parse ) {
			if ( ! empty( $query_parse->get_search_query() ) ) {
				global $wpdb;

				$post_ids = $this->post_ids( $query_parse->get_search_query() );
				if ( ! empty( $post_ids ) ) {
					$query['where']  = substr( $query['where'], 0, -1 );
					$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $post_ids ) ) . ')';
					$query['where'] .= ')';
				}
			}

			return $query;
		}
	}
}
