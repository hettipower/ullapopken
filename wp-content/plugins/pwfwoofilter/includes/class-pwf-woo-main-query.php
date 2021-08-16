<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Woo_Main_Query' ) ) {

	class Pwf_Woo_Main_Query {

		private $filter_id;

		/**
		* The unique instance of the plugin.
		*
		* @var Pwf_Woo_Main_Query
		*/
		private static $instance;

		/**
		* The unique instance of the Pwf_Parse_Query_Vars.
		*
		* @var Pwf_Parse_Query_Vars
		*/
		private static $query_vars;

		/**
		* hook price
		*/
		private static $hook_price;

		/**
		* hook orderby
		*/
		private static $hook_orderby;

		/**
		 * Gets an instance of our plugin.
		 *
		 * @return Pwf_Woo_Main_Query
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * @since 1.0.0, 1.0.6
		 */
		private function __construct() {
			self::$query_vars   = null;
			self::$hook_price   = false;
			self::$hook_orderby = false;

			add_action( 'init', array( $this, 'init' ), 10 );
		}

		public function init() {
			$this->filter_id = get_option( 'pwf_shop_filter_id', '' );
			if ( ! empty( $this->filter_id ) ) {
				$this->filter_id = apply_filters( 'pwf_filter_id', $this->filter_id );
			}
			if ( ! empty( $this->filter_id ) ) {
				add_action( 'woocommerce_product_query', array( $this, 'woocommerce_product_query' ), 1, 10 );
				add_filter( 'the_posts', array( $this, 'remove_product_query_filters' ) );
			}
		}

		public function woocommerce_product_query( $q ) {

			// This code must be before use Pwf_Parse_Query_Vars
			$GLOBALS['pwf_main_query_done']['filter_id'] = absint( $this->filter_id );
			if ( is_tax( get_object_taxonomies( 'product' ) ) ) {
				$GLOBALS['pwf_main_query_done']['current_taxonomy_name'] = get_queried_object()->taxonomy;
				$GLOBALS['pwf_main_query_done']['current_taxonomy_id']   = get_queried_object()->term_id;
			}

			$active_items     = $this->get_active_filter_items();
			self::$query_vars = new Pwf_Parse_Query_Vars( $this->filter_id, $active_items );
			$orderby          = self::$query_vars->get_products_orderby();
			$tax_query        = self::$query_vars->get_tax_query_filter_items();
			$meta_query       = self::$query_vars->get_meta_query();
			$authors_id       = self::$query_vars->get_authors_id();
			$date_query       = self::$query_vars->get_date_query();

			$GLOBALS['pwf_main_query_done']['query_vars'] = self::$query_vars;

			if ( ! empty( $orderby ) && ! isset( $_GET['orderby'] ) ) {
				self::$hook_orderby = true;

				$orderby  = is_array( $orderby ) ? implode( '', $orderby ) : $orderby;
				$ordering = WC()->query->get_catalog_ordering_args( $orderby );

				$q->set( 'orderby', $ordering['orderby'] );
				$q->set( 'order', $ordering['order'] );
				if ( isset( $ordering['meta_key'] ) ) {
					$q->set( 'meta_key', $ordering['meta_key'] );
				}
			}

			if ( self::$query_vars->has_price_item() ) {
				if ( ! isset( $_GET['min_price'] ) && ! isset( $_GET['max_price'] ) ) {
					self::$hook_price = true;
					add_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ), 10, 2 );
				}
			}

			/**
			 * @since 1.1.3
			 */
			if ( ! isset( $_GET['s'] ) && ! empty( self::$query_vars->get_search_query() ) ) {
				$q->set( 's', self::$query_vars->get_search_query() );
				$q->set( 'is_search', true );
			}

			if ( ! empty( $tax_query ) ) {
				$tax_query = array_merge( $q->get( 'tax_query' ), $tax_query );
				$q->set( 'tax_query', $tax_query );
			}

			if ( ! empty( $meta_query ) ) {
				$meta_query = array_merge( $q->get( 'meta_query' ), $meta_query );
				$q->set( 'meta_query', $meta_query );
			}

			if ( ! empty( $authors_id ) ) {
				$authors_id = array_merge( $q->get( 'author__in' ), $authors_id );
				$q->set( 'author__in', $authors_id );
			}

			if ( ! empty( $date_query ) ) {
				$q->set( 'date_query', $date_query );
			}
		}

		/**
		 * Custom query used to filter products by price.
		 *
		 * @since 3.6.0
		 *
		 * @param array    $args Query args.
		 * @param WC_Query $wp_query WC_Query object.
		 *
		 * @return array
		 */
		public function price_filter_post_clauses( $args, $wp_query ) {
			global $wpdb;

			$price     = self::$query_vars->get_current_min_max_price();
			$min_price = $price[0];
			$max_price = $price[1];

			$current_min_price = floatval( wp_unslash( $min_price ) );
			$current_max_price = floatval( wp_unslash( $max_price ) );

			/**
			 * Adjust if the store taxes are not displayed how they are stored.
			 * Kicks in when prices excluding tax are displayed including tax.
			 */
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
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

		/**
		 * @since 1.0.0, 1.1.4
		 */
		private function get_active_filter_items() {
			$data         = array();
			$filter_items = $this->get_filter_items_url_key();
			if ( empty( $filter_items ) ) {
				return $data;
			}

			// check what item is active
			foreach ( $filter_items as $item ) {
				if ( 'priceslider' === $item['item_type'] && 'two' === $item['price_url_format'] ) {
					if ( isset( $_GET[ $item['url_key_min_price'] ] ) && isset( $_GET[ $item['url_key_max_price'] ] ) ) {
						$data[ $item['url_key'] ] = array( $_GET[ $item['url_key_min_price'] ], $_GET[ $item['url_key_max_price'] ] );
					}
				} elseif ( 'rangeslider' === $item['item_type'] && 'two' === $item['range_slider_url_format'] ) {
					if ( isset( $_GET[ $item['url_key_range_slider_min'] ] ) && isset( $_GET[ $item['url_key_range_slider_max'] ] ) ) {
						$data[ $item['url_key'] ] = array( $_GET[ $item['url_key_range_slider_min'] ], $_GET[ $item['url_key_range_slider_max'] ] );
					}
				} elseif ( 'date' === $item['item_type'] ) {
					if ( isset( $_GET[ $item['url_key_date_after'] ] ) && isset( $_GET[ $item['url_key_date_before'] ] ) ) {
						$data[ $item['url_key'] ] = array( $_GET[ $item['url_key_date_after'] ], $_GET[ $item['url_key_date_before'] ] );
					}
				} elseif ( isset( $_GET[ $item['url_key'] ] ) ) {
					$data[ $item['url_key'] ] = $_GET[ $item['url_key'] ];
				}
			}

			return $data;
		}

		private function get_filter_items_url_key() {
			$filter_items = get_post_meta( absint( $this->filter_id ), '_pwf_woo_post_filter', true );
			$filter_items = $filter_items['items'];
			if ( empty( $filter_items ) ) {
				return '';
			}
			$filter_items = Pwf_Parse_Query_Vars::get_filter_items_without_columns( $filter_items );

			return $filter_items;
		}

		public function remove_product_query_filters( $posts ) {
			if ( self::$hook_price ) {
				remove_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ) );
			}
			if ( self::$hook_orderby ) {
				WC()->query->remove_ordering_args();
			}

			return $posts;
		}
	}

	$pwf_woo_main_query = Pwf_Woo_Main_Query::get_instance();
}
