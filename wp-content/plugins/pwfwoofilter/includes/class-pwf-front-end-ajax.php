<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Front_End_Ajax' ) ) {

	class Pwf_Front_End_Ajax {

		public static function register() {
			$plugin = new self();
			add_action( 'init', array( $plugin, 'init' ) );
		}

		function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 50 );
			add_action( 'wp_ajax_get_filter_result', array( $this, 'get_filter_result' ), 10 );
			add_action( 'wp_ajax_nopriv_get_filter_result', array( $this, 'get_filter_result' ), 10 );
		}

		function wp_enqueue_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'select2', PWF_WOO_FILTER_URI . '/assets/select2/css/select2.min.css', '', '4.0.12' );
			wp_enqueue_style( 'jquery-ui', PWF_WOO_FILTER_URI . '/assets/css/frontend/jquery-ui/jquery-ui.min.css', '', '1.12.1' );
			wp_enqueue_style( 'pwf-woo-filter', PWF_WOO_FILTER_URI . '/assets/css/frontend/style' . $suffix . '.css', '', PWF_WOO_FILTER_VER );

			wp_register_script( 'select2', PWF_WOO_FILTER_URI . '/assets/select2/js/select2.full.min.js', '', '4.0.12', true );
			wp_register_script( 'nouislider', PWF_WOO_FILTER_URI . '/assets/js/frontend/nouislider.min.js', '', '14.2.0', true );
			wp_register_script( 'moment', PWF_WOO_FILTER_URI . '/assets/js/frontend/moment.min.js', '', '2.25.3', true );
			wp_register_script( 'offcanvas', PWF_WOO_FILTER_URI . '/assets/js/frontend/js-offcanvas.pkgd.min.js', '', '1.2.11', true );
			wp_enqueue_script(
				'pwf-woo-filter',
				PWF_WOO_FILTER_URI . '/assets/js/frontend/script.js',
				array( 'jquery', 'select2', 'nouislider', 'jquery-ui-datepicker', 'moment', 'offcanvas' ),
				PWF_WOO_FILTER_VER,
				true
			);

			$currency_symbol = get_woocommerce_currency_symbol();
			$currency_pos    = get_option( 'woocommerce_currency_pos', 'left' );
			if ( empty( $currency_symbol ) ) {
				$currency_symbol = '&#36;';
			}

			$customize = array();


			$localize_args = array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'pwf-woocommerce-filter-nonce' ),
				'translated_text' => self::get_translated_text(),
				'currency_symbol' => $currency_symbol,
				'currency_pos'    => $currency_pos,
				'customize'       => array(
					'pageLoader'     => esc_textarea( get_option( 'pwf_woo_loader_default', '' ) ),
					'buttonLoader'   => esc_textarea( get_option( 'pwf_woo_loader_load_more', '' ) ),
					'infiniteLoader' => esc_textarea( get_option( 'pwf_woo_loader_infinite', '' ) ),
				),
			);

			wp_localize_script( 'pwf-woo-filter', 'pwf_woocommerce_filter', $localize_args );
		}

		// get filter results
		public function get_filter_result() {
			check_ajax_referer( 'pwf-woocommerce-filter-nonce', 'nonce' );

			if ( ! isset( $_POST['filter_id'] ) || ! is_int( absint( $_POST['filter_id'] ) ) ) {
				$ajax_args['success'] = 'false';
				$ajax_args['message'] = esc_html__( 'Filer ID must be integer.', 'pwf-woo-filter' );
				echo json_encode( $ajax_args );
				wp_die();
			}

			/**
			 * Not recomended to use apply_filters using pwf_filter_id
			 * When the filter id come form ajax
			 * because it is already change before created a page
			 */
			$filter_id = absint( $_POST['filter_id'] );
			if ( isset( $_POST['current_taxonomy_name'] ) && isset( $_POST['current_taxonomy_id'] ) ) {
				if ( ! empty( $_POST['current_taxonomy_name'] ) && ! empty( $_POST['current_taxonomy_id'] ) ) {
					$GLOBALS['pwf_main_query_done']['current_taxonomy_name'] = esc_attr( $_POST['current_taxonomy_name'] );
					$GLOBALS['pwf_main_query_done']['current_taxonomy_id']   = esc_attr( $_POST['current_taxonomy_id'] );
				}
			}

			if ( isset( $_POST['rule_hidden_items'] ) && is_array( $_POST['rule_hidden_items'] ) ) {
				$GLOBALS['rule_hidden_items'] = array_map( 'esc_attr', $_POST['rule_hidden_items'] );
			}

			$query_vars = array();
			if ( isset( $_POST['query_vars'] ) && is_array( $_POST['query_vars'] ) && ! empty( $_POST['query_vars'] ) ) {
				foreach ( $_POST['query_vars'] as $key => $values ) {
					if ( ! empty( $values ) ) {
						if ( ! is_array( $values ) ) {
							$values = array( $values );
						}
						$query_vars[ esc_attr( $key ) ] = array_map( 'esc_attr', $values );
					}
				}
			}

			$attributes = array();
			if ( isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) && ! empty( $_POST['attributes'] ) ) {
				foreach ( $_POST['attributes'] as $key => $value ) {
					$attributes[ esc_attr( $key ) ] = esc_attr( $value );
				}
			}

			do_action( 'pwf_before_doing_ajax', $filter_id );

			$query_vars = new Pwf_Parse_Query_Vars( $filter_id, $query_vars );
			$orderby    = $query_vars->get_products_orderby();
			$authors_id = $query_vars->get_authors_id();
			if ( ! empty( $orderby ) ) {
				$attributes['orderby'] = is_array( $orderby ) ? implode( ',', $orderby ) : $orderby;
			}
			if ( ! empty( $authors_id ) ) {
				$attributes['author__in'] = $authors_id;
			}

			$query      = new Pwf_Filter_Products( $query_vars, $attributes );
			$products   = $query->get_content();
			$attributes = $query->get_query_info();

			if ( isset( $_POST['get_products_only'] ) && 'true' === $_POST['get_products_only'] ) {
				$filter_items_html = '';
			} else {
				$render_filter     = new Pwf_Render_Filter( $filter_id, $query_vars );
				$filter_items_html = wp_kses_post( $render_filter->get_html() );
			}

			$results = array(
				'products'    => $products,
				'attributes'  => $attributes,
				'filter_html' => $filter_items_html,
			);
			echo json_encode( $results );
			wp_die();
		}
		private static function get_translated_text() {
			$text = array(
				'apply'     => esc_html__( 'Apply', 'pwf-woo-filter' ),
				'reset'     => esc_html__( 'Reset', 'pwf-woo-filter' ),
				'filter'    => esc_html__( 'Filter', 'pwf-woo-filter' ),
				'price'     => esc_html__( 'Price', 'pwf-woo-filter' ),
				'search'    => esc_html__( 'Search', 'pwf-woo-filter' ),
				'rate'      => esc_html__( 'Rated', 'pwf-woo-filter' ),
				'load_more' => esc_html__( 'Load more', 'pwf-woo-filter' ),
				'clearall'  => esc_html__( 'Clear all', 'pwf-woo-filter' ),
			);

			return $text;
		}
	}

	Pwf_Front_End_Ajax::register();
}
