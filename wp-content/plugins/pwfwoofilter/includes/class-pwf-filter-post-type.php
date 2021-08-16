<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Filter_Post_Type' ) ) {

	class Pwf_Filter_Post_Type {

		/**
		* The unique instance of the plugin.
		*
		* @var Pwf_Filter_Post_Type
		*/
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 *
		 * @return WP_Kickass_Plugin
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
			add_action( 'init', array( $this, 'init' ), 10 );
			add_action( 'init', array( $this, 'register_filter_post_type' ), 10 );
			add_action( 'init', array( $this, 'add_compitable_to__current_theme' ), 10 );
			add_action( 'rest_api_init', array( $this, 'prepare_filter_post_for_api' ), 10 );
			add_action( 'rest_prepare_pwf_woofilter', array( $this, 'remove_extra_data_from_filter_post_api' ), 12, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'remove_auto_save_script' ) );
			add_action( 'manage_pwf_woofilter_posts_custom_column', array( $this, 'fill_filter_post_type_columns' ), 10, 2 );

			// Update plugin cached
			add_action( 'edit_post', array( $this, 'update_terms_cached' ), 10, 2 );
			add_action( 'rest_delete_product', array( $this, 'update_terms_cached_api_delete' ), 10, 1 );

			add_filter( 'manage_pwf_woofilter_posts_columns', array( $this, 'render_filter_post_columns' ) );
			add_filter( 'wp_kses_allowed_html', array( $this, 'wp_kses_allowed_html_tag' ), 10, 1 );
		}

		public function init() {
			load_plugin_textdomain( 'pwf-woo-filter', false, PWF_WOO_FILTER_DIR_DOMAIN . '/languages/' );
			add_shortcode( 'pwf_filter', array( $this, 'add_shortcode' ) );
			if ( class_exists( 'DGWT_WC_Ajax_Search' ) ) {
				new Pwf_Integrate_Ajax_Search_For_Woo();
			}
		}

		public function register_filter_post_type() {
			$labels = array(
				'name'               => esc_html_x( 'Filter', 'post type general name', 'pwf-woo-filter' ),
				'singular_name'      => esc_html_x( 'Filter', 'post type singular name', 'pwf-woo-filter' ),
				'menu_name'          => esc_html_x( 'Filters', 'admin menu', 'pwf-woo-filter' ),
				'name_admin_bar'     => esc_html_x( 'Filter', 'add new on admin bar', 'pwf-woo-filter' ),
				'add_new'            => esc_html_x( 'Add new', 'Filter', 'pwf-woo-filter' ),
				'add_new_item'       => esc_html__( 'Add new Filter', 'pwf-woo-filter' ),
				'new_item'           => esc_html__( 'New Filter', 'pwf-woo-filter' ),
				'edit_item'          => esc_html__( 'Edit Filter', 'pwf-woo-filter' ),
				'view_item'          => esc_html__( 'View Filter', 'pwf-woo-filter' ),
				'all_items'          => esc_html__( 'Filters', 'pwf-woo-filter' ),
				'search_items'       => esc_html__( 'Search Filters', 'pwf-woo-filter' ),
				'parent_item_colon'  => esc_html__( 'Parent Filters:', 'pwf-woo-filter' ),
				'not_found'          => esc_html__( 'No Filters found.', 'pwf-woo-filter' ),
				'not_found_in_trash' => esc_html__( 'No Filters found in trash.', 'pwf-woo-filter' ),
			);

			$post_type_args = array(
				'public'              => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_nav_menus'   => true,
				'hierarchical'        => false,
				'supports'            => false,
				'capability_type'     => 'post',
				'rewrite'             => false,
				'query_var'           => false,
				'has_archive'         => false,
				'label'               => 'Filter',
				'labels'              => $labels,
				'show_in_menu'        => 'woocommerce',
				'show_in_rest'        => true,
			);

			register_post_type( 'pwf_woofilter', $post_type_args );
		}

		public function add_shortcode( $atts ) {
			// phpcs:ignore
			extract( shortcode_atts( array( 'id' => '' ), $atts ) );
			if ( ! absint( $id ) ) {
				return;
			}
			$filter_id     = apply_filters( 'pwf_filter_id', $id );
			$render_filter = new Pwf_Render_Filter( $filter_id );
			$output        = $render_filter->get_html();
			return wp_kses_post( $output );
		}

		public function prepare_filter_post_for_api() {

			register_rest_field(
				'pwf_woofilter',
				'title',
				array(
					'get_callback' => array( $this, 'get_title' ),
					'schema'       => null,
				)
			);

			register_rest_field(
				'pwf_woofilter',
				'filter_items',
				array(
					'get_callback' => array( $this, 'get_filter_items' ),
					'schema'       => null,
				)
			);
		}

		public function get_title( $object ) {
			return esc_attr( get_the_title( $object['id'] ) );
		}

		public function get_filter_items( $object ) {
			$render_filter = new Pwf_Render_Filter( $object['id'] );
			$filter_items  = new Pwf_Api_Prepare_Filter_Post( $render_filter->get_filter_items_data(), $render_filter->get_filter_setting() );
			$filter_items  = $filter_items->get_filter_items();

			return $filter_items;
		}

		public function remove_extra_data_from_filter_post_api( $data, $post, $context ) {
			if ( ! is_wp_error( $data ) ) {
				unset( $data->data['type'] );
				unset( $data->data['status'] );
				unset( $data->data['slug'] );
				unset( $data->data['date'] );
				unset( $data->data['date_gmt'] );
				unset( $data->data['modified'] );
				unset( $data->data['modified_gmt'] );
				unset( $data->data['link'] );
				unset( $data->data['template'] );
				unset( $data->data['guid'] );
			}
			return $data;
		}

		public function remove_auto_save_script() {
			switch ( get_post_type() ) {
				case 'pwf_woofilter':
					wp_dequeue_script( 'autosave' );
					break;
			}
		}

		public function fill_filter_post_type_columns( $column, $post_id ) {
			if ( 'pwfshortcode' === $column ) {
				echo '[pwf_filter id="' . absint( $post_id ) . '"]';
			}
		}

		public function render_filter_post_columns( $columns ) {
			$date = $columns['date'];
			unset( $columns['date'] );
			$columns['pwfshortcode'] = 'Shortcode';
			$columns['date']         = $date;

			return $columns;
		}

		public function wp_kses_allowed_html_tag( $tags ) {
			$div = array(
				'aria-required' => true,
			);

			$input = array(
				'id'            => true,
				'type'          => true,
				'name'          => true,
				'class'         => true,
				'value'         => true,
				'placeholder'   => true,
				'aria-required' => true,
				'checked'       => true,
				'disabled'      => true,
				'min'           => true,
				'max'           => true,
				'data-*'        => true,
			);

			$select = array(
				'id'                 => true,
				'name'               => true,
				'class'              => true,
				'aria-required'      => true,
				'data-default-value' => true,
			);

			$option = array(
				'value'      => true,
				'selected'   => true,
				'data-slug'  => true,
				'data-title' => true,
				'disabled'   => true,
			);

			if ( isset( $tags['div'] ) ) {
				$tags['div'] = array_merge( $tags['div'], $div );
			} else {
				$tags['div'] = $div;
			}

			if ( isset( $tags['input'] ) ) {
				$tags['input'] = array_merge( $tags['input'], $input );
			} else {
				$tags['input'] = $input;
			}

			if ( isset( $tags['select'] ) ) {
				$tags['select'] = array_merge( $tags['select'], $select );
			} else {
				$tags['select'] = $select;
			}

			if ( isset( $tags['option'] ) ) {
				$tags['option'] = array_merge( $tags['option'], $option );
			} else {
				$tags['option'] = $option;
			}

			return $tags;
		}

		public function update_terms_cached( $post_id, $post ) {
			if ( 'product' === $post->post_type ) {
				self::delete_terms_cached();
			}
		}

		public function update_terms_cached_api_delete( $post ) {
			if ( 'product' === $post->post_type ) {
				self::delete_terms_cached();
			}
		}

		private static function delete_terms_cached() {
			$delete = Pwf_Clear_Transients::delete_transients();
		}

		/**
		 * WordPress Themes require to add specific code
		 */
		protected function compitable_theme() {
			$themes = array(
				'Avada',
				'dt-the7',
				'astra',
				'brooklyn',
				'consulting',
				'ekommart',
				'enfold',
				'freshio',
				'greenmart',
				'kallyas',
				'legenda',
				'merchandiser',
				'oceanwp',
				'upstore',
				'medizin',
				'puca',
				'konte',
				'atelier',
				'thefox',
				'dfd-ronneby',
				'martfury',
				'urna',
				'mediacenter',
				'stockie',
				'ark',
				'theretailer',
				'ninezeroseven',
				'stockholm',
				'hongo',
				'movedo',
				'royal',
				'ciyashop',
				'x',
				'exponent',
				'pofo',
				'cartzilla',
				'pinkmart',
				'flatsome',
			);

			return $themes;
		}
		public function add_compitable_to__current_theme() {
			$compitable = get_option( 'pwf_shop_theme_compitable', 'enable' );
			if ( 'disable' === $compitable ) {
				return;
			}
			$current_theme = get_template();
			if ( in_array( $current_theme, $this->compitable_theme(), true ) ) {
				$path = PWF_WOO_FILTER_DIR . 'includes/compitable-themes/' . $current_theme . '/theme.php';
				Pwf_Autoloader::load_file( $path );
			}
		}
	}

	$pwf_core = Pwf_Filter_Post_Type::get_instance();
}
