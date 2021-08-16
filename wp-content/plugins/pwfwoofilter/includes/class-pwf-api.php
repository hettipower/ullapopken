<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Api' ) ) {

	class Pwf_Api {

		const NAMESPACE = 'wc-pwfwoofilter/v1';
		const RESETBASE = 'filterproducts';

		/**
		 * Instance of Pwf_Parse_Query_Vars
		 */
		protected static $query_vars;

		protected $filter_id;
		protected $attributes;
		protected $request_url_has_orderby;

		function __construct() {}

		/**
		 * Registers our plugin with WordPress.
		 */
		public static function register() {
			$plugin = new self();
			add_action( 'rest_api_init', array( $plugin, 'register_routes' ) );
		}

		public function register_routes() {
			register_rest_route(
				self::NAMESPACE,
				'/' . self::RESETBASE,
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(
						'filter_id' => array(
							'default'           => false,
							'sanitize_callback' => 'absint',
						),
					),
				)
			);
		}

		/**
		 * Get a collection of posts.
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_items( $request ) {
			$filter_id = absint( $request['filter_id'] );
			if ( ! $filter_id ) {
				return new WP_Error( 'no_posts', esc_html__( 'Missing filter ID', 'pwf-woo-filter' ), array( 'status' => 404 ) );
			}

			if ( false === $this->check_filter_id_exist( $request['filter_id'] ) ) {
				return new WP_Error( 'no_posts', esc_html__( 'Filter post ID not exists', 'pwf-woo-filter' ), array( 'status' => 404 ) );
			}

			$this->filter_id  = $filter_id;
			self::$query_vars = new Pwf_Parse_Query_Vars( $filter_id, $request->get_params() );
			$this->customize_request_api_to_woo_api( $request );

			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 1, 10 );
			add_filter( 'woocommerce_rest_product_object_query', array( $this, 'customize_woocommerce_query' ), 2, 10 );

			$response = new WC_REST_Products_Controller();
			$response = $response->get_items( $request );

			$this->remove_parms_add_to_request( $request );
			$this->set_response_header_links( $response, $request );

			$render_filter = new Pwf_Render_Filter( $filter_id, self::$query_vars );
			$filter_items  = new Pwf_Api_Prepare_Filter_Post( $render_filter->get_filter_items_data(), $render_filter->get_filter_setting(), true );
			$filter_items  = $filter_items->get_filter_items();

			$data = array(
				'products'     => $response->get_data(),
				'filter_items' => $filter_items,
			);
			$response->set_data( $data );

			return $response;
		}

		public function customize_request_api_to_woo_api( &$request ) {
			$query_vars = self::$query_vars;
			$attributes = array();

			if ( $request->has_param( 'orderby' ) && ! empty( $request->get_param( 'orderby' ) ) ) {
				$this->request_url_has_orderby = true;
				$request->set_param( 'orderby', sanitize_text_field( wp_unslash( $request->get_param( 'orderby' ) ) ) );
			} elseif ( ! empty( $query_vars->get_products_orderby() ) ) {
				$orderby               = $query_vars->get_products_orderby();
				$attributes['orderby'] = is_array( $orderby ) ? implode( ',', $orderby ) : $orderby;
				$request->set_param( 'orderby', $attributes['orderby'] );
			} else {
				$request->set_param( 'orderby', 'date' );
			}

			if ( $request->has_param( 'page' ) && ! empty( $request->get_param( 'page' ) ) ) {
				$attributes['page'] = absint( $request->get_param( 'page' ) );
			} else {
				$attributes['page'] = 1;
			}
			$request->set_param( 'page', absint( $attributes['page'] ) );

			if ( ! ( $request->has_param( 'per_page' ) && ! empty( $request->get_param( 'per_page' ) ) ) ) {
				$filter_setting = $query_vars->get_filter_setting();
				if ( ! empty( $filter_setting['posts_per_page'] ) ) {
					$attributes['per_page'] = $filter_setting['posts_per_page'];
				} else {
					$attributes['per_page'] = get_option( 'posts_per_page' );
				}
				$request->set_param( 'per_page', absint( $attributes['per_page'] ) );
			}

			$request->offsetUnset( 'filter_id' );
			foreach ( $query_vars->get_filter_items_key() as $key ) {
				if ( $request->has_param( $key ) ) {
					$request->offsetUnset( $key );
				}
			}

			$request->set_param( 'status', 'publish' );

			if ( $query_vars->has_price_item() ) {
				$price = $query_vars->get_current_min_max_price();


				$request->set_param( 'min_price', absint( $price[0] ) );
				$request->set_param( 'max_price', absint( $price[1] ) );
				$attributes['min_price'] = $price[0];
				$attributes['max_price'] = $price[1];
			}

			if ( ! empty( $query_vars->get_date_query() ) ) {
				$date_query  = $query_vars->get_date_query();
				$date_before = $date_query[0]['before'];
				$before      = new DateTime();
				$before->setDate( $date_before['year'], $date_before['month'], $date_before['day'] );
				$before->setTime( 23, 59, 59 );

				$date_after = $date_query[0]['after'];
				$after      = new DateTime();
				$after->setDate( $date_after['year'], $date_after['month'], $date_after['day'] );
				$after->setTime( 0, 0, 0 );

				$before = $before->format( DateTime::ATOM );
				$after  = $after->format( DateTime::ATOM );
				$request->set_param( 'after', $after );
				$request->set_param( 'before', $before );
				$attributes['after']  = $after;
				$attributes['before'] = $before;
			}

			/**
			 * @since 1.1.3
			 */
			if ( ! $request->has_param( 'search' ) && ! empty( $query_vars->get_search_query() ) ) {
				$request->set_param( 'search', esc_attr( $query_vars->get_search_query() ) );
			}

			$this->attributes = $attributes;
		}

		public function remove_parms_add_to_request( &$request ) {
			$request->offsetUnset( 'status' );
			foreach ( $this->attributes as $key => $value ) {
				$request->offsetUnset( $key );
			}

			if ( ! $this->request_url_has_orderby ) {
				$request->offsetUnset( 'orderby' );
			}
		}

		public function set_response_header_links( &$response, $request ) {
			if ( ! isset( $response->headers ) || ! isset( $response->headers['Link'] ) ) {
				return;
			}

			unset( $response->headers['Link'] );
			$headers_link   = $response->headers;
			$filter_items   = self::$query_vars->get_filter_items();
			$selected_items = self::$query_vars->selected_items();

			$attr      = array();
			$base_link = '?filter_id=' . absint( $this->filter_id );
			foreach ( $selected_items as $url_key => $values ) {
				$filter_item = $this->get_filter_item_data_by_url_key( $url_key, $filter_items );
				if ( 'price' === $filter_item['item_type'] ) {
					if ( 'two' === $item['price_url_format'] ) {
						$attr[ $item['url_key_min_price'] ] = $values[0];
						$attr[ $item['url_key_max_price'] ] = $values[1];
					} else {
						$attr[ $url_key ] = implode( '-', $values );
					}
				} elseif ( 'date' === $filter_item['item_type'] ) {
					$attr[ $url_key ] = implode( ',', $values );
				} else {
					if ( is_array( $values ) ) {
						$values = implode( ',', $values );
					}
					$attr[ $url_key ] = $values;
				}
			}

			foreach ( $attr as $url_key => $value ) {
				$base_link .= '&' . esc_attr( $url_key ) . '=' . esc_attr( $value );
			}

			$base      = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s/%s', self::NAMESPACE, self::RESETBASE, $base_link ) ) );
			$page      = absint( $this->attributes['page'] );
			$max_pages = absint( $headers_link['X-WP-TotalPages'] );

			if ( $page > 1 ) {
				$prev_page = $page - 1;
				if ( $prev_page > $max_pages ) {
					$prev_page = $max_pages;
				}
				$prev_link = add_query_arg( 'page', $prev_page, $base );
				$response->link_header( 'prev', $prev_link );
			}

			if ( $max_pages > $page ) {
				$next_page = $page + 1;
				$next_link = add_query_arg( 'page', $next_page, $base );
				$response->link_header( 'next', $next_link );
			}
		}

		public function pre_get_posts( $q ) {
			/**
			 * hook for author__in filter item
			 * because it's not working with customize_woocommerce_query()
			 */
			if ( $q->is_main_query() ) {
				return;
			}
			if ( ! empty( self::$query_vars->get_authors_id() ) ) {
				$q->set( 'author__in', array_map( 'absint', self::$query_vars->get_authors_id() ) );
			}
		}

		public function customize_woocommerce_query( $args, $request ) {
			if ( ! empty( self::$query_vars->get_tax_query() ) ) {
				$args['tax_query'] = self::$query_vars->get_tax_query();
			}

			if ( ! empty( self::$query_vars->get_meta_query() ) ) {
				$args['meta_query'] = self::$query_vars->get_meta_query();
			}

			return $args;
		}

		/**
		 * Check if a given request has access to post items.
		 */
		public function get_items_permissions_check( $request ) {
			return true;
		}

		private function check_filter_id_exist( $filter_id ) {
			$filter_id = get_post_type( absint( $filter_id ) );
			if ( false === $filter_id || 'pwf_woofilter' !== $filter_id ) {
				return false;
			}
			return true;
		}

		public function get_filter_item_data_by_url_key( $url_key, $filter_items ) {
			foreach ( $filter_items as $item ) {
				if ( $url_key === $item['url_key'] ) {
					return $item;
				}
			}
			return false;
		}
	}

	Pwf_Api::register();
}
