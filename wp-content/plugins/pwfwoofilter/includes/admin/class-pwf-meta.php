<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Meta' ) ) {

	class Pwf_Meta {

		function __construct() {}

		/**
		 * Registers our plugin with WordPress.
		 */
		public static function register() {
			$plugin = new self();
			add_action( 'init', array( $plugin, 'init' ) );
		}

		public function init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
			add_action( 'wp_ajax_get_hierarchy_taxonomies_using_ajax', array( $this, 'get_hierarchy_taxonomies_using_ajax' ) );
			add_action( 'wp_ajax_get_group_taxonomies_using_ajax', array( $this, 'get_group_taxonomies_using_ajax' ) );
			add_action( 'wp_ajax_get_taxonomies_using_ajax', array( $this, 'get_taxonomies_using_ajax' ) );
			add_action( 'wp_ajax_save_filter_post', array( $this, 'save_filter_post' ) );
			add_filter( 'add_meta_boxes', array( $this, 'remove_publish_metabox' ) );
			add_filter( 'screen_options_show_screen', array( $this, 'remove_screen_options' ) );
			add_filter( 'get_user_option_screen_layout_pwf_woofilter', array( $this, 'screen_layout' ), 10, 1 );
			add_filter( 'post_row_actions', array( $this, 'remove_quick_edit_button' ), 10, 1 );
			add_action( 'edit_form_after_title', array( $this, 'add_filter_meta_box' ) );
		}

		public function remove_quick_edit_button( $row_actions ) {
			if ( get_post_type() === 'pwf_woofilter' ) {
				unset( $row_actions['inline hide-if-no-js'] );
			}
			return $row_actions;
		}

		public function remove_screen_options() {
			global $pagenow, $post;
			if ( ! empty( $pagenow ) && 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {
				if ( 'pwf_woofilter' === $post->post_type ) {
					return false;
				}
			}
			return true;
		}

		public function remove_publish_metabox() {
			remove_meta_box( 'submitdiv', 'pwf_woofilter', 'side' );
		}

		public function screen_layout( $columns ) {
			return 1;
		}

		public function admin_enqueue_scripts_styles() {
			global $pagenow, $post;

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( ! empty( $pagenow ) && 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {

				if ( 'pwf_woofilter' === $post->post_type ) {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_style( 'select2', PWF_WOO_FILTER_URI . '/assets/select2/css/select2' . $suffix . '.css', '', '4.0.12' );
					wp_enqueue_style( 'prowoofilteradmin', PWF_WOO_FILTER_URI . '/assets/css/admin/admin' . $suffix . '.css', '', PWF_WOO_FILTER_VER );

					wp_register_script( 'select2', PWF_WOO_FILTER_URI . '/assets/select2/js/select2.full' . $suffix . '.js', '', '4.0.12', true );
					wp_enqueue_media();
					wp_enqueue_script(
						'pwf-woo-filter',
						PWF_WOO_FILTER_URI . '/assets/js/admin/script' . $suffix . '.js',
						array( 'jquery', 'select2', 'jquery-ui-sortable', 'wp-color-picker' ),
						PWF_WOO_FILTER_VER,
						true
					);

					$current_screen = 'new';
					if ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
						$current_screen = 'edit';
					}

					$meta        = get_post_meta( get_the_ID(), '_pwf_woo_post_filter', true );
					$filter_meta = array(
						'setting' => $meta['setting'] ?? '',
						'items'   => $meta['items'] ?? '',
					);

					$pwf_databas      = new Pwf_Database_Query();
					$get_data_options = new Pwf_Meta_Data();
					$panel_fields     = new Pwf_Meta_Fields();
					$localize_args    = array(
						'nonce'              => wp_create_nonce( 'ajax_pro_woo_filter_nonce' ),
						'post_id'            => get_the_ID(),
						'current_screen'     => $current_screen,
						'filter_meta'        => json_encode( $filter_meta ),
						'setting_fields'     => self::get_filter_post_setting_fields(),
						'panel_feilds'       => $panel_fields->get_panel_meta_fields(),
						'product_categories' => $pwf_databas->get_proudct_categories(),
						'translated_text'    => self::get_translated_text(),
						'display_rule_data'  => array(
							'param' => $get_data_options->rules_parameter(),
						),
					);

					if ( 'new' === $current_screen ) {
						$localize_args['edit_link'] = self::get_edit_link();
					}

					wp_localize_script( 'pwf-woo-filter', 'pro_woo_filter', $localize_args );
				}
			}
		}

		public function get_taxonomies_using_ajax() {

			$pwf_databas = new Pwf_Database_Query();

			$ajax_args = array(
				'isempty' => 'true',
				'message' => '',
				'data'    => '',
			);

			check_admin_referer( 'ajax_pro_woo_filter_nonce', 'nonce' );

			if ( empty( $_POST['source_of_option'] ) ) {
				$ajax_args['message'] = esc_html__( 'Please select option from source of options', 'pwf-woo-filter' );
				echo json_encode( $ajax_args );
				wp_die();
			}

			$source_of_options = esc_attr( $_POST['source_of_option'] );
			$taxonomy_name     = $_POST['taxonomy_name'] ?? '';
			$parent            = $_POST['parent'] ?? '';
			$add_all_text      = $_POST['add_all_text'] ?? false;

			if ( 'category' === $source_of_options ) {
				if ( 'all' === $parent || ! is_int( $parent ) ) {
					$parent = 0;
				}
				$data = $pwf_databas->proudct_taxonomies( 'product_cat', $parent );
			} elseif ( 'taxonomy' === $source_of_options ) {
				if ( 'all' === $parent || ! is_int( $parent ) ) {
					$parent = 0;
				}
				if ( 'true' === $add_all_text ) {
					$add_all_text = true;
				}
				$data = $pwf_databas->proudct_taxonomies( $taxonomy_name, $parent, $add_all_text );
			} elseif ( 'attribute' === $source_of_options ) {
				$data = $pwf_databas->proudct_taxonomies( $taxonomy_name );
			} elseif ( 'tag' === $source_of_options ) {
				$data = $pwf_databas->proudct_taxonomies( 'product_tag' );
			} elseif ( 'author' === $source_of_options ) {
				$data = $pwf_databas->get_users( true );
			}

			if ( ! empty( $data ) ) {
				$ajax_args['isempty'] = 'false';
				$ajax_args['data']    = $data;
			} else {
				$ajax_args['message'] = esc_html__( 'There is no option list', 'pwf-woo-filter' );
			}
			echo json_encode( $ajax_args );
			wp_die();
		}

		public function get_group_taxonomies_using_ajax() {
			$pwf_databas = new Pwf_Database_Query();
			// used in rule
			$ajax_args = array(
				'isempty' => 'true',
				'message' => '',
				'data'    => '',
			);

			check_admin_referer( 'ajax_pro_woo_filter_nonce', 'nonce' );

			if ( empty( $_POST['source'] ) ) {
				$ajax_args['message'] = esc_html__( 'Please select option from source of options', 'pwf-woo-filter' );
				echo json_encode( $ajax_args );
				wp_die();
			}

			$source = esc_attr( $_POST['source'] );

			if ( 'attribute' === $source ) {
				$data = $pwf_databas->proudct_all_attributes();
			} elseif ( 'tag' === $source ) {
				$data = $pwf_databas->get_proudct_tags( 'product_tag' );
			} elseif ( 'taxonomy' === $source ) {
				$data = $pwf_databas->proudct_all_taxonomies();
			} elseif ( 'page' === $source ) {
				$data = $pwf_databas->get_pages();
			}

			if ( ! empty( $data ) ) {
				$ajax_args['isempty'] = 'false';
				$ajax_args['data']    = $data;
			} else {
				$ajax_args['message'] = esc_html__( 'There is no option list', 'pwf-woo-filter' );
			}
			echo json_encode( $ajax_args );
			wp_die();
		}

		public function get_hierarchy_taxonomies_using_ajax() {

			$pwf_databas = new Pwf_Database_Query();

			$ajax_args = array(
				'isempty' => 'true',
				'message' => '',
				'data'    => '',
			);

			check_admin_referer( 'ajax_pro_woo_filter_nonce', 'nonce' );

			if ( empty( $_POST['source_of_option'] ) ) {
				$ajax_args['message'] = esc_html__( 'Please select option from source of options', 'pwf-woo-filter' );
				echo json_encode( $ajax_args );
				wp_die();
			}

			$source_of_options = esc_attr( $_POST['source_of_option'] );
			$taxonomy_name     = $_POST['taxonomy_name'] ?? '';
			$parent            = $_POST['parent'] ?? '';
			$user_roles        = $_POST['user_roles'] ?? '';

			if ( 'category' === $source_of_options ) {
				$data = $pwf_databas->get_ajax_product_taxonomies( 'product_cat', $parent );
			} elseif ( 'taxonomy' === $source_of_options ) {
				$data = $pwf_databas->get_ajax_product_taxonomies( $taxonomy_name, $parent );
			} elseif ( 'attribute' === $source_of_options ) {
				$data = $pwf_databas->get_ajax_product_taxonomies( $taxonomy_name );
			} elseif ( 'tag' === $source_of_options ) {
				$data = $pwf_databas->get_ajax_product_taxonomies( 'product_tag' );
			} elseif ( 'author' === $source_of_options ) {
				$data = $pwf_databas->get_users( true, $user_roles );
			}

			if ( ! empty( $data ) ) {
				$ajax_args['isempty'] = 'false';
				$ajax_args['data']    = $data;
			} else {
				$ajax_args['message'] = esc_html__( 'There is no option list', 'pwf-woo-filter' );
			}
			echo json_encode( $ajax_args );
			wp_die();
		}

		private function sanitize_multi_select_field( $data ) : array {
			$return_data = array();
			if ( ! empty( $data ) ) {
				if ( is_array( $data ) ) {
					foreach ( $data as $item ) {
						$return_data[] = sanitize_text_field( $item );
					}
				} else {
					$return_data = array( sanitize_text_field( $data ) );
				}
			}

			return $return_data;
		}

		private static function get_filtering_starts() {
			return array(
				array(
					'id'   => 'auto',
					'text' => esc_html__( 'Automatically', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'send_button',
					'text' => esc_html__( 'When on click send button', 'pwf-woo-filter' ),
				),
			);
		}

		private function get_filter_post_setting_fields() {
			$get_data_options = new Pwf_Meta_Data();

			$general_fields = array(
				array(
					'type'       => 'text',
					'param_name' => 'post_title',
					'title'      => esc_html__( 'Title', 'pwf-woo-filter' ),
					'default'    => esc_html__( 'Shop Filter', 'pwf-woo-filter' ),
					'required'   => true,
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'filtering_starts',
					'title'      => esc_html__( 'Filtering starts', 'pwf-woo-filter' ),
					'default'    => 'auto',
					'required'   => true,
					'options'    => self::get_filtering_starts(),
				),
				array(
					'type'        => 'text',
					'param_name'  => 'cssclass',
					'title'       => esc_html__( 'CSS class', 'pwf-woo-filter' ),
					'placeholder' => 'class-name',
					'required'    => false,
				),
				array(
					'type'        => 'multicheckbox',
					'param_name'  => 'usecomponents',
					'title'       => esc_html__( 'Which components to use', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Content of components will be updated when filtering', 'pwf-woo-filter' ),
					'default'     => array( 'pagination', 'sorting', 'results_count', 'page_title', 'breadcrumb' ),
					'options'     => array(
						array(
							'id'      => 'pagination',
							'text'    => esc_html__( 'Pagination', 'pwf-woo-filter' ),
							'default' => 'checked',
						),
						array(
							'id'      => 'sorting',
							'text'    => esc_html__( 'Sorting', 'pwf-woo-filter' ),
							'default' => 'checked',
						),
						array(
							'id'      => 'results_count',
							'text'    => esc_html__( 'Results count', 'pwf-woo-filter' ),
							'default' => 'checked',
						),
					),
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'pagination_type',
					'title'      => esc_html__( 'Pagination type', 'pwf-woo-filter' ),
					'default'    => 'auto',
					'required'   => true,
					'options'    => array(
						array(
							'id'   => 'numbers',
							'text' => esc_html__( 'Numbers', 'pwf-woo-filter' ),
						),
						array(
							'id'   => 'load_more',
							'text' => esc_html__( 'Load more button', 'pwf-woo-filter' ),
						),
						array(
							'id'   => 'infinite_scroll',
							'text' => esc_html__( 'Infinite scrolling', 'pwf-woo-filter' ),
						),
					),
				),
				array(
					'type'       => 'switchbutton',
					'param_name' => 'pagination_ajax',
					'title'      => esc_html__( 'Pagination ajax', 'pwf-woo-filter' ),
					'default'    => 'on',
				),
				array(
					'type'       => 'switchbutton',
					'param_name' => 'sorting_ajax',
					'title'      => esc_html__( 'Sorting ajax', 'pwf-woo-filter' ),
					'default'    => 'on',
				),
			);

			$selectors_fields = array(
				array(
					'type'       => 'text',
					'param_name' => 'products_container_selector',
					'title'      => esc_html__( 'Products container selector', 'pwf-woo-filter' ),
					'default'    => '.products',
					'required'   => true,
				),
				array(
					'type'       => 'text',
					'param_name' => 'pagination_selector',
					'title'      => esc_html__( 'Pagination selector', 'pwf-woo-filter' ),
					'default'    => '.woocommerce-pagination',
					'required'   => true,
				),
				array(
					'type'       => 'text',
					'param_name' => 'result_count_selector',
					'title'      => esc_html__( 'Result count selector', 'pwf-woo-filter' ),
					'default'    => '.woocommerce-result-count',
					'required'   => true,
				),
				array(
					'type'       => 'text',
					'param_name' => 'sorting_selector',
					'title'      => esc_html__( 'Sorting selector', 'pwf-woo-filter' ),
					'default'    => '.woocommerce-ordering',
					'required'   => true,
				),
				array(
					'type'        => 'text',
					'param_name'  => 'active_filters_selector',
					'title'       => esc_html__( 'active Filters selector', 'pwf-woo-filter' ),
					'placeholder' => 'class-name',
				),
				array(
					'type'        => 'text',
					'param_name'  => 'scroll_to',
					'title'       => esc_html__( 'Scroller to', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Use to scroll top when Ajax finished, default is Products container selector.', 'pwf-woo-filter' ),
				),
			);

			$extra_fields = array(
				array(
					'type'       => 'dropdown',
					'param_name' => 'display_filter_as',
					'title'      => esc_html__( 'Display Filter', 'pwf-woo-filter' ),
					'default'    => '',
					'options'    => array(
						array(
							'id'   => '',
							'text' => esc_html__( 'Default', 'pwf-woo-filter' ),
						),
						array(
							'id'   => 'button',
							'text' => esc_html__( 'Button', 'pwf-woo-filter' ),
						),
					),
				),
				array(
					'type'        => 'dropdown',
					'param_name'  => 'filter_button_state',
					'title'       => esc_html__( 'Button toggle state', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Default state (show)', 'pwf-woo-filter' ),
					'default'     => 'show',
					'options'     => $get_data_options->default_toggle_state(),
				),
				array(
					'type'       => 'text',
					'param_name' => 'posts_per_page',
					'title'      => esc_html__( 'Number of products', 'pwf-woo-filter' ),
				),
				array(
					'type'       => 'switchbutton',
					'param_name' => 'browser_hash',
					'title'      => esc_html__( 'Enable browser hash', 'pwf-woo-filter' ),
					'default'    => 'on',
				),
				array(
					'type'        => 'switchbutton',
					'param_name'  => 'api_remove_columns_layout',
					'title'       => esc_html__( 'API remove columns layout', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Remove columns layout in API', 'pwf-woo-filter' ),
					'default'     => 'on',
				),
				array(
					'type'       => 'switchbutton',
					'param_name' => 'is_shortcode',
					'title'      => esc_html__( 'Is shortcode', 'pwf-woo-filter' ),
					'default'    => '',
				),
				array(
					'type'        => 'text_area',
					'param_name'  => 'shortcode_string',
					'title'       => esc_html__( 'Shortcode', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Copy your shortcode you add in the post and paste it here', 'pwf-woo-filter' ),
				),
			);

			$responsive_fields = array(
				array(
					'type'        => 'switchbutton',
					'param_name'  => 'responsive',
					'title'       => esc_html__( 'Responsive', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Convert the filter to responsive', 'pwf-woo-filter' ),
					'default'     => 'on',
					'required'    => true,
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'responsive_filtering_starts',
					'title'      => esc_html__( 'Filtering starts', 'pwf-woo-filter' ),
					'default'    => 'send_button',
					'options'    => self::get_filtering_starts(),
				),
				array(
					'type'        => 'text',
					'param_name'  => 'responsive_append_sticky',
					'title'       => esc_html__( 'Append the filter to', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Append the filter sticky navigation, default to the HTML body tag', 'pwf-woo-filter' ),
					'default'     => 'body',
				),
				array(
					'type'        => 'text',
					'param_name'  => 'responsive_width',
					'title'       => esc_html__( 'Screen width', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Assign width in pixel to convert the filter to sticky navigation', 'pwf-woo-filter' ),
					'default'     => 768,
				),
			);

			$fields = array(
				'general'    => $general_fields,
				'selectors'  => $selectors_fields,
				'extra'      => $extra_fields,
				'responsive' => $responsive_fields,
			);

			return $fields;
		}

		private function get_template_field_start( $field ) {
			$output = '<div class="control-group';
			if ( 'radio' === $field['type'] ) {
				$output .= ' inline-radio-buttons';
			}
			$output .= '">';
			$output .= '<div class="control-label">';
			$output .= '<span class="label-text">';
			$output .= $field['title'];

			if ( isset( $field['required'] ) && true === $field['required'] ) {
				$output .= '<abbr class="required" title="required">*</abbr>';
			}
			$output .= '</span>';

			if ( isset( $field['description'] ) && '' !== $field['description'] ) {
				$output .= '<span class="description">' . $field['description'] . '</span>';
			}
			$output .= '</div>';
			$output .= '<div class="control-content">';

			return $output;
		}

		private function get_template_field_end() {
			return '</div></div>';
		}

		private function render_html_form( $field, $saved_values ) {
			$field_type    = $field['type'];
			$html_template = '';
			switch ( $field_type ) {
				case 'text':
					$html_template = self::render_form_text_template( $field, $saved_values );
					break;
				case 'text_area':
					$html_template = self::render_form_text_area_template( $field, $saved_values );
					break;
				case 'dropdown':
					$html_template = self::render_form_dropdown_template( $field, $saved_values );
					break;
				case 'multicheckbox':
					$html_template = self::render_form_multicheckbox_template( $field, $saved_values );
					break;
				case 'switchbutton':
					$html_template = self::render_form_switchbutton_template( $field, $saved_values );
					break;
			}

			return $html_template;
		}

		private function render_form_text_template( $field, $saved_values ) {
			$param_name = $field['param_name'];

			$output  = '<input type="text" name="' . esc_attr( $field['param_name'] ) . '"';
			$output .= ' class="form-control full-width text';
			if ( isset( $field['cssclass'] ) && '' !== $field['cssclass'] ) {
				$output .= ' ' . esc_attr( $field['cssclass'] );
			}
			$output .= '"';

			if ( isset( $saved_values[ $param_name ] ) ) {
				$output .= ' value="' . esc_attr( $saved_values[ $param_name ] ) . '"';
			} elseif ( isset( $field['default'] ) ) {
				$output .= ' value="' . esc_attr( $field['default'] ) . '"';
			} else {
				$output .= ' value=""';
			}

			if ( isset( $field['placeholder'] ) && '' !== $field['placeholder'] ) {
				$output .= ' placeholder="' . esc_attr( $field['placeholder'] ) . '"';
			}

			if ( isset( $field['required'] ) && true === $field['required'] ) {
				$output .= ' aria-required="true"';
			}
			if ( isset( $field['default'] ) ) {
				$output .= ' data-default-value="' . esc_attr( $field['default'] ) . '"';
			}
			$output .= '/>';

			return self::get_template_field_start( $field ) . $output . self::get_template_field_end();
		}

		private function render_form_text_area_template( $field, $saved_values ) {
			$param_name = $field['param_name'];

			$output  = '<textarea name="' . esc_attr( $field['param_name'] ) . '"';
			$output .= ' class="form-control full-width text';
			if ( isset( $field['cssclass'] ) && '' !== $field['cssclass'] ) {
				$output .= ' ' . esc_attr( $field['cssclass'] );
			}
			$output .= '"';
			$output .= ' rows="3"/>';

			if ( isset( $saved_values[ $param_name ] ) ) {
				$output .= esc_textarea( $saved_values[ $param_name ] );
			}
			$output .= '</textarea>';

			return self::get_template_field_start( $field ) . $output . self::get_template_field_end();
		}

		private function render_form_multicheckbox_template( $field, $saved_values ) {
			$param_name    = $field['param_name'];
			$checked_value = array();

			$output = '<div class="check-box-list"><ul class="list ' . esc_attr( $field['param_name'] ) . '">';

			if ( isset( $saved_values[ $param_name ] ) ) {
				$checked_value = $saved_values[ $param_name ];
			} elseif ( isset( $field['default'] ) ) {
				$checked_value = $field['default'];
			}

			foreach ( $field['options'] as $option ) {
				$checked = '';

				// phpcs:ignore
				if ( in_array( $option['id'], $checked_value, false ) ) {
					$checked = ' checked';
				}
				$output .= '<li><input type="checkbox" class="form-control checkbox" name="' . esc_attr( $field['param_name'] ) . '" value="' . esc_attr( $option['id'] ) . '"' . $checked;
				if ( isset( $field['required'] ) && $field['required'] ) {
					$output .= ' aria-required="true"';
				}
				if ( isset( $field['default'] ) ) {
					$output .= ' data-default-value="' . esc_attr( $option['default'] ) . '"';
				}
				$output .= '><label for="' . esc_attr( $option['id'] ) . '">' . esc_attr( $option['text'] ) . '</label></li>';
			}
			$output .= '</ul></div>';

			return self::get_template_field_start( $field ) . $output . self::get_template_field_end();
		}

		private function render_form_dropdown_template( $field, $saved_values ) {
			$param_name    = $field['param_name'];
			$checked_value = array();

			if ( isset( $saved_values[ $param_name ] ) ) {
				$checked_value = $saved_values[ $param_name ];
			} elseif ( isset( $field['param_name'] ) ) {
				$checked_value = $field['default'];
			}

			$output  = '<select name="' . esc_attr( $field['param_name'] ) . '"';
			$output .= ' class="form-control full-width dropdown';

			if ( isset( $field['cssclass'] ) && '' !== $field['cssclass'] ) {
				$output .= ' ' . esc_attr( $field['cssclass'] );
			}
			$output .= '"';
			if ( isset( $field['required'] ) && $field['required'] ) {
				$output .= ' aria-required="true"';
			}
			if ( isset( $field['default'] ) ) {
				$output .= ' data-default-value="' . esc_attr( $field['default'] ) . '"';
			}
			$output .= '>';

			foreach ( $field['options'] as $option ) {
				$output .= '<option value ="' . $option['id'] . '"';
				if ( $option['id'] === $checked_value ) {
					$output .= ' selected';
				}
				$output .= '>' . esc_attr( $option['text'] ) . '</option>';
			}
			$output .= '</select>';

			return self::get_template_field_start( $field ) . $output . self::get_template_field_end();
		}

		private function render_form_switchbutton_template( $field, $saved_values ) {
			$param_name = $field['param_name'];
			$checked    = '';

			if ( isset( $saved_values[ $param_name ] ) ) {
				if ( 'on' === $saved_values[ $param_name ] ) {
					$checked = ' checked';
				}
			} elseif ( isset( $field['default'] ) && 'on' === $field['default'] ) {
				$checked = ' checked';
			}

			$output  = '<div class="ckbx-style-15 switch-btn">';
			$output .= '<input id="pwf-' . esc_attr( $field['param_name'] ) . '" type="checkbox" name="' . esc_attr( $field['param_name'] ) . '" class="form-control checkbox-switch-field';
			if ( isset( $field['cssclass'] ) && '' !== $field['cssclass'] ) {
				$output .= ' ' . esc_attr( $field['cssclass'] );
			}
			$output .= '"';
			if ( isset( $field['default'] ) ) {
				$output .= ' data-default-value="' . esc_attr( $field['default'] ) . '"';
			}
			$output .= $checked . '><label for="pwf-' . esc_attr( $field['param_name'] ) . '" class="slider-btn"></label></div>';

			return self::get_template_field_start( $field ) . $output . self::get_template_field_end();
		}

		private function get_html_items_panel() {
			$output  = '<div class="filter-items-panel panel postbox">';
			$output .= '<h2 class="panel-title">' . esc_html__( 'Items', 'pwf-woo-filter' ) . '</h2>';
			$output .= '<div class="inside">';
			$output .= '<div id="filters-list" class="filters-list append-filter-items">';
			// Here we Create filter items using Juquery
			$output .= '</div>'; // end filters-list
			$output .= '<div class="filters-btn">';
			$output .= '<button class="button button-primary save-project">' . esc_html__( 'Save', 'pwf-woo-filter' ) . '</button>';
			$output .= '<button class="button add-item">' . esc_html__( 'Add item', 'pwf-woo-filter' ) . '</button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';

			return $output;
		}

		function add_filter_meta_box() {
			global $pagenow, $post;

			if ( ! empty( $pagenow ) && 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {
				$fields            = self::get_filter_post_setting_fields();
				$general_fields    = $fields['general'];
				$selectors_fields  = $fields['selectors'];
				$responsive_fields = $fields['responsive'];
				$extra_fields      = $fields['extra'];
				$saved_values      = '';
				$meta              = get_post_meta( get_the_ID(), '_pwf_woo_post_filter', true ); //array();
				if ( $meta ) {
					$saved_values = $meta['setting'];
				}
				$disable_apply_btn       = '';
				$panel_required_validate = 'true';
				if ( $saved_values ) {
					$disable_apply_btn       = ' disabled';
					$panel_required_validate = 'false';
				}

				if ( 'pwf_woofilter' === $post->post_type ) {
					$output  = '<div class="pro-woo-filter">';
					$output .= self::get_html_items_panel();
					$output .= '<div class="filter-options panel-group">';
					$output .= '<div class="panel-item setting-panel postbox status-active-panel" data-required-validate="' . $panel_required_validate . '">';
					$output .= '<div class="panel-container">';
					$output .= '<div class="panel-header">';
					$output .= '<div class="wrap-title"><h2 class="panel-title">' . esc_html__( 'Filter setting', 'pwf-woo-filter' ) . '</h2></div>';
					$output .= '<div class="tabs-nav">';
					$output .= '<span class="nav-tab-heading nav-general active-tab" data-tab-id="general">' . esc_html__( 'General', 'pwf-woo-filter' ) . '</span>';
					$output .= '<span class="nav-tab-heading nav-selectors" data-tab-id="selectors">' . esc_html__( 'Selectors', 'pwf-woo-filter' ) . '</span>';
					$output .= '<span class="nav-tab-heading nav-selectors" data-tab-id="extra">' . esc_html__( 'Extra', 'pwf-woo-filter' ) . '</span>';
					$output .= '<span class="nav-tab-heading nav-responsive" data-tab-id="responsive">' . esc_html__( 'Responsive', 'pwf-woo-filter' ) . '</span>';
					$output .= '</div>';
					$output .= '</div>'; // end panel header
					$output .= '<div class="inside">';
					$output .= '<div class="setting-panel-form"><div class="wrap-tabs">';
					$output .= '<div class="tab-content active-tab" data-tab-id="general">';

					foreach ( $general_fields as $field ) {
						$output .= self::render_html_form( $field, $saved_values );
					}
					$output .= '</div>';

					$output .= '<div class="tab-content" data-tab-id="selectors">'; // tab2
					foreach ( $selectors_fields as $field ) {
						$output .= self::render_html_form( $field, $saved_values );
					}
					$output .= '</div>'; // end of tab2

					$output .= '<div class="tab-content" data-tab-id="extra">'; // tab3
					foreach ( $extra_fields as $field ) {
						$output .= self::render_html_form( $field, $saved_values );
					}
					$output .= '</div>'; // end of tab3

					$output .= '<div class="tab-content" data-tab-id="responsive">'; // tab3
					foreach ( $responsive_fields as $field ) {
						$output .= self::render_html_form( $field, $saved_values );
					}
					$output .= '</div>'; // end of tab3

					$output .= '</div></div>'; // end of wrap tabs
					$output .= '<div class="panel-nav">';
					$output .= '<button class="button reset-panel-button">' . esc_html__( 'Reset', 'pwf-woo-filter' ) . '</button>';
					$output .= '<button class="button apply-panel-button"' . $disable_apply_btn . '>' . esc_html__( 'Apply', 'pwf-woo-filter' ) . '</button>';
					$output .= '</div>'; // end of panel nav
					$output .= '</div>';
					$output .= '</div>'; // panel-container
					$output .= '</div>'; // panel-item
					$output .= '</div>'; // end of panel group
					$output .= '</div>';

					echo wp_kses_post( $output );
				}
			}
		}

		/**
		 * @since 1.0.0, 1.1.6
		 */
		public function save_filter_post() {

			check_admin_referer( 'ajax_pro_woo_filter_nonce', 'nonce' );

			$ajax_args = array(
				'success' => 'false',
				'message' => '',
				'data'    => '',
			);

			if ( empty( $_POST['data'] ) ) {
				$ajax_args['message'] = esc_html__( 'There is no data to save', 'pwf-woo-filter' );
				echo json_encode( $ajax_args );
				wp_die();
			}

			$data         = json_decode( stripslashes( $_POST['data'] ), true );
			$setting      = $this->validate_filter_setting( $data['setting'] );
			$filter_items = $this->validate_filter_items( $data['items'] );

			$filter_post = array(
				'post_title'  => sanitize_text_field( $setting['post_title'] ),
				'post_type'   => 'pwf_woofilter',
				'post_status' => 'publish',
			);

			if ( isset( $_POST['post_id'] ) && is_numeric( $_POST['post_id'] ) ) {
				$filter_post['ID'] = absint( $_POST['post_id'] );
				$post_id           = wp_update_post( $filter_post, true );
			} else {
				$post_id = wp_insert_post( $filter_post );
			}

			if ( ! is_wp_error( $post_id ) ) {
				$ajax_args['success'] = 'true';
				$ajax_args['post_id'] = $post_id;

				$saved_meta = array(
					'setting' => $setting,
					'items'   => $filter_items,
				);
				update_post_meta( $post_id, '_pwf_woo_post_filter', $saved_meta ); // _pwf_woo_post_filter
			} else {
				$ajax_args['message'] = esc_html__( 'Filter post not saved', 'pwf-woo-filter' ) . $post_id->get_error_message();
			}

			echo json_encode( $ajax_args );
			wp_die();
		}

		private function validate_filter_setting( $data ) {
			$validated = array();
			foreach ( $data as $key => $value ) {
				if ( ! is_array( $value ) ) {
					if ( 'shortcode_string' === $key ) {
						$validated[ esc_attr( $key ) ] = sanitize_textarea_field( $value );
					} else {
						$validated[ esc_attr( $key ) ] = esc_attr( $value );
					}
				} else {
					$validated[ esc_attr( $key ) ] = array_map( 'esc_attr', $value );
				}
			}

			if ( isset( $validated['is_shortcode'] ) && empty( $validated['is_shortcode'] ) ) {
				$validated['shortcode_string'] = '';
			}

			return $validated;
		}

		private function validate_filter_items( $data, &$key_id = 0 ) {
			$validated             = array();
			$items_without_url_key = array( 'column', 'button' );
			foreach ( $data as $key => $item ) {
				$key = esc_attr( $key );

				if ( ! in_array( $item['item_type'], $items_without_url_key, true ) && empty( $item['url_key'] ) ) {
					continue;
				}

				if ( 'column' === $item['item_type'] ) {
					$item_validate = array(
						'item_type'  => 'column',
						'width'      => esc_attr( $item['width'] ),
						'width_unit' => esc_attr( $item['width_unit'] ),
						'css_class'  => esc_attr( $item['css_class'] ),
					);

					$parent_key = $key_id;
					$key_id++;
					if ( ! empty( $item['children'] ) ) {
						$item_validate['children'] = $this->validate_filter_items( $item['children'], $key_id );
					} else {
						$item_validate['children'] = array();
					}

					$validated[ 'id-' . $parent_key ] = $item_validate;
				} else {
					$item_validate = array();
					foreach ( $item as $item_key => $value ) {
						$item_key = esc_attr( $item_key );

						if ( 'metafield' === $item_key || 'colors' === $item_key || 'boxlistlabel' === $item_key ) {
							if ( empty( $value ) ) {
								$item_validate[ $item_key ] = '';
							} else {
								$meta_fields = array();
								foreach ( $value as $meta ) {
									$meta_field = array();
									foreach ( $meta as $meta_key => $meta_value ) {
										if ( 'image' === $meta_key ) {
											$meta_field['image'] = esc_url( $meta_value );
										} elseif ( 'term_id' === $meta_key ) {
											$meta_field['term_id'] = absint( $meta_value );
										} elseif ( 'slug' === $meta_key ) {
											$meta_field['slug'] = esc_attr( str_replace( ' ', '', $meta_value ) );
										} else {
											$meta_field[ esc_attr( $meta_key ) ] = esc_attr( $meta_value );
										}
									}
									$meta_fields[] = $meta_field;
								}
								$item_validate[ $item_key ] = $meta_fields;
							}
						} elseif ( 'hidden_rules' === $item_key ) {
							if ( empty( $value ) ) {
								$item_validate[ $item_key ] = '';
							} else {
								$hidden_rules = array();
								foreach ( $value as $rule ) {

									if ( empty( $rule ) || empty( $rule['value'] ) ) {
										continue;
									}
									$hidden_rules[] = array(
										'param' => esc_attr( $rule['param'] ),
										'equal' => 'equalto',
										'value' => esc_attr( $rule['value'] ),
									);
								}

								if ( ! empty( $hidden_rules ) ) {
									$item_validate['hidden_rules'] = $hidden_rules;
								} else {
									$item_validate['hidden_rules'] = '';
								}
							}
						} else {
							if ( 'url_key' === $item_key ) {
								$item_validate[ $item_key ] = esc_attr( str_replace( ' ', '', $value ) );
							} elseif ( ! is_array( $value ) ) {
								$item_validate[ $item_key ] = esc_attr( $value );
							} else {
								$item_validate[ $item_key ] = array_map( 'esc_attr', $value );
							}
						}
					}

					// make empty for fields
					if ( isset( $item_validate['source_of_options'] ) ) {
						if ( 'author' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_category', 'item_source_taxonomy_sub', 'item_source_attribute', 'item_source_taxonomy', 'item_source_stock_status', 'item_source_orderby' );
						} elseif ( 'category' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_attribute', 'item_source_taxonomy', 'item_source_taxonomy_sub', 'item_source_stock_status', 'item_source_orderby', 'user_roles' );
						} elseif ( 'attribute' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_category', 'item_source_taxonomy', 'item_source_taxonomy_sub', 'item_source_stock_status', 'item_source_orderby', 'user_roles' );
						} elseif ( 'tag' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_category', 'item_source_attribute', 'item_source_taxonomy', 'item_source_taxonomy_sub', 'item_source_stock_status', 'item_source_orderby', 'user_roles' );
						} elseif ( 'taxonomy' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_category', 'item_source_attribute', 'item_source_stock_status', 'item_source_orderby', 'user_roles' );
						} elseif ( 'stock_status' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_category', 'item_source_attribute', 'item_source_taxonomy', 'item_source_taxonomy_sub', 'item_source_orderby', 'user_roles' );
						} elseif ( 'orderby' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'meta_key', 'meta_compare', 'meta_type', 'metafield', 'item_source_category', 'item_source_attribute', 'item_source_taxonomy', 'item_source_stock_status', 'item_source_taxonomy_sub', 'user_roles' );
						} elseif ( 'meta' === $item_validate['source_of_options'] ) {
							$empty_fields = array( 'item_source_category', 'item_source_attribute', 'item_source_taxonomy', 'item_source_stock_status', 'item_source_orderby', 'item_source_taxonomy_sub', 'user_roles' );
						}

						if ( isset( $empty_fields ) && is_array( $empty_fields ) ) {
							foreach ( $empty_fields as $field_key ) {
								$item_validate[ $field_key ] = '';
							}
						}
					}

					if ( 'rangeslider' === $item['item_type'] ) {
						if ( 'custom' !== $item_validate['range_slider_meta_source'] ) {
							$item_validate['meta_key'] = '';
						}
					}

					if ( isset( $item_validate['item_display'] ) ) {
						if ( 'all' === $item_validate['item_display'] || 'parent' === $item_validate['item_display'] ) {
							$item_validate['include'] = '';
							$item_validate['exclude'] = '';
						} elseif ( 'selected' === $item_validate['item_display'] ) {
							$item_validate['exclude'] = '';
						} elseif ( 'except' === $item_validate['item_display'] ) {
							$item_validate['include'] = '';
						}
					}

					if ( isset( $item_validate['display_title'] ) && isset( $item_validate['display_toggle_content'] ) && empty( $item_validate['display_title'] ) ) {
						$item_validate['display_toggle_content'] = '';
					}

					if ( isset( $item['display_hierarchical'] ) && isset( $item['display_hierarchical_collapsed'] ) && empty( $item['display_hierarchical'] ) ) {
						$item_validate['display_hierarchical_collapsed'] = '';
					}

					$remove_meta_field = array( 'date', 'column', 'button', 'priceslider' );
					if ( in_array( $item_validate['item_type'], $remove_meta_field, true ) ) {
						unset( $item_validate['metafield'] );
					}

					if ( 'colorlist' !== $item_validate['item_type'] ) {
						unset( $item_validate['colors'] );
					}

					if ( 'dropdownlist' === $item['item_type'] ) {
						if ( in_array( $item_validate['source_of_options'], array( 'orderby', 'stock_status' ), true ) ) {
							$item_validate['query_type']           = 'or';
						} elseif ( 'default' === $item_validate['dropdown_style'] ) {
							$item_validate['multi_select']         = '';
							$item_validate['query_type']           = 'or';
							$item_validate['display_hierarchical'] = '';
						}

						if ( ! in_array( $item_validate['source_of_options'], array( 'category', 'taxonomy' ), true ) ) {
							$item_validate['display_hierarchical'] = '';
						}
					}

					$validated[ 'id-' . $key_id ] = $item_validate;
					$key_id++;
				}
			}

			return $validated;
		}

		private function get_edit_link() {
			$schema = 'http';
			if ( force_ssl_admin() ) {
				$schema = 'https';
			}
			$admin_url = get_admin_url( null, '', $schema );

			$url = $admin_url . 'post.php?post=post_id&action=edit';

			return $url;
		}

		private static function get_translated_text() {
			$text = array(
				'checkboxlist'        => esc_html__( 'Checkbox list', 'pwf-woo-filter' ),
				'radiolist'           => esc_html__( 'Radio list', 'pwf-woo-filter' ),
				'dropdownlist'        => esc_html__( 'Dropdown list', 'pwf-woo-filter' ),
				'colorlist'           => esc_html__( 'Color list', 'pwf-woo-filter' ),
				'boxlist'             => esc_html__( 'Box list', 'pwf-woo-filter' ),
				'textlist'            => esc_html__( 'Text list', 'pwf-woo-filter' ),
				'date'                => esc_html__( 'Date', 'pwf-woo-filter' ),
				'priceslider'         => esc_html__( 'Price slider', 'pwf-woo-filter' ),
				'button'              => esc_html__( 'Button', 'pwf-woo-filter' ),
				'preset'              => esc_html__( 'Preset', 'pwf-woo-filter' ),
				'categories'          => esc_html__( 'Categories', 'pwf-woo-filter' ),
				'stockstatus'         => esc_html__( 'Stock status', 'pwf-woo-filter' ),
				'layout'              => esc_html__( 'Layout', 'pwf-woo-filter' ),
				'column'              => esc_html__( 'Column', 'pwf-woo-filter' ),
				'field'               => esc_html__( 'Field', 'pwf-woo-filter' ),
				'required_field'      => esc_html__( 'This field is required', 'pwf-woo-filter' ),
				'unique_field'        => esc_html__( 'This field must be unique', 'pwf-woo-filter' ),
				'ajax_fail'           => esc_html__( 'Ajax can not get data from your site', 'pwf-woo-filter' ),
				'rule_text_or'        => esc_html_x( 'OR', 'OR text for display rules', 'pwf-woo-filter' ),
				'rule_text_if'        => esc_html__( 'Hide this filter if', 'pwf-woo-filter' ),
				'color'               => esc_html__( 'Color', 'pwf-woo-filter' ),
				'image'               => esc_html__( 'Image', 'pwf-woo-filter' ),
				'upload_image'        => esc_html__( 'Upload image', 'pwf-woo-filter' ),
				'type'                => esc_html__( 'Type', 'pwf-woo-filter' ),
				'border'              => esc_html__( 'Border', 'pwf-woo-filter' ),
				'marker'              => esc_html__( 'Marker', 'pwf-woo-filter' ),
				'marker_light'        => esc_html__( 'Light', 'pwf-woo-filter' ),
				'marker_dark'         => esc_html__( 'Dark', 'pwf-woo-filter' ),
				'general'             => esc_html__( 'General', 'pwf-woo-filter' ),
				'visual'              => esc_html__( 'Visual', 'pwf-woo-filter' ),
				'edit'                => esc_html__( 'Edit', 'pwf-woo-filter' ),
				'close'               => esc_html__( 'Close', 'pwf-woo-filter' ),
				'remove'              => esc_html__( 'Remove', 'pwf-woo-filter' ),
				'label'               => esc_html__( 'Label', 'pwf-woo-filter' ),
				'value'               => esc_html__( 'Value', 'pwf-woo-filter' ),
				'error'               => esc_html__( 'Error', 'pwf-woo-filter' ),
				'btn_reset'           => esc_html__( 'Reset', 'pwf-woo-filter' ),
				'btn_apply'           => esc_html__( 'Apply', 'pwf-woo-filter' ),
				'equalto'             => esc_html__( 'Equal to', 'pwf-woo-filter' ),
				'btn_back'            => esc_html__( 'Back', 'pwf-woo-filter' ),
				'add_new'             => esc_html_x( 'Add new', 'Add new meta', 'pwf-woo-filter' ),
				'alert_message'       => esc_html__( 'Please fill the required field', 'pwf-woo-filter' ),
				'select_filter_item'  => esc_html__( 'Select item', 'pwf-woo-filter' ),
				'filter_post_updated' => esc_html__( 'Filter updated', 'pwf-woo-filter' ),
				'no_option'           => esc_html__( 'There are no options list to display', 'pwf-woo-filter' ),
				'search'              => esc_html__( 'Search', 'pwf-woo-filter' ),
				'range_slider'        => esc_html__( 'Range slider', 'pwf-woo-filter' ),
				'rating'              => esc_html__( 'Rating', 'pwf-woo-filter' ),
				'slug'                => esc_html__( 'Slug', 'pwf-woo-filter' ),
				'meta_value_desc'     => wp_sprintf(
					'%1$s %2$s %3$s %4$s',
					esc_html__( 'String or array, for the array separate values with commas, for more information read', 'pwf-woo-filter' ),
					'<a href="https://developer.wordpress.org/reference/classes/wp_meta_query/#usage" target="_blank">',
					esc_html__( 'documentation', 'pwf-woo-filter' ),
					'</a>'
				),
			);

			return $text;
		}
	}

	Pwf_Meta::register();
}
