<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Meta_Data' ) ) {

	class Pwf_Meta_Data {

		function __construct() {
		}

		public function query_type() {
			$data = array(
				array(
					'id'   => 'and',
					'text' => esc_html__( 'AND', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'or',
					'text' => esc_html__( 'OR', 'pwf-woo-filter' ),
				),
			);

			return $data;
		}

		public function action_button() {
			$data = array(
				array(
					'id'   => 'reset',
					'text' => esc_html__( 'Reset', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'filter',
					'text' => esc_html__( 'Filter', 'pwf-woo-filter' ),
				),
			);

			return $data;
		}

		public function source_of_options() {

			$source_of_options = array(
				array(
					'id'   => 'attribute',
					'text' => esc_html__( 'Attribute', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'category',
					'text' => esc_html__( 'Category', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'tag',
					'text' => esc_html__( 'Tag', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'taxonomy',
					'text' => esc_html__( 'Taxonomy', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'stock_status',
					'text' => esc_html__( 'Stock status', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'orderby',
					'text' => esc_html__( 'Order by', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'author',
					'text' => esc_html__( 'Author', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'meta',
					'text' => esc_html__( 'Meta', 'pwf-woo-filter' ),
				),
			);

			return $source_of_options;
		}

		public function rules_parameter() {

			$param = array(
				array(
					'id'   => 'attribute',
					'text' => esc_html__( 'Attribute', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'category',
					'text' => esc_html__( 'Category', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'tag',
					'text' => esc_html__( 'Tag', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'taxonomy',
					'text' => esc_html__( 'Taxonomy', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'page',
					'text' => esc_html__( 'Page', 'pwf-woo-filter' ),
				),
			);

			return $param;
		}

		public function rule_equal() {

			$equal = array(
				array(
					'id'   => 'equalto',
					'text' => esc_html__( 'Equal to', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'notequalto',
					'text' => esc_html__( 'Not Equal to', 'pwf-woo-filter' ),
				),
			);

			return $equal;
		}


		public function proudct_categories() {
			$data         = array();
			$product_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);

			if ( ! empty( $product_cats ) ) {
				$data[] = array(
					'id'   => 'all',
					'text' => esc_html__( 'All', 'pwf-woo-filter' ),
				);
				foreach ( $product_cats as $cat ) {
					$data[] = array(
						'id'   => absint( $cat->term_id ),
						'text' => esc_attr( $cat->name ),
					);
				}
			}

			return $data;
		}

		public function proudct_attributes() {
			$data       = array();
			$attributes = wc_get_attribute_taxonomies();
			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $attribute ) {
					$term_name = wc_attribute_taxonomy_name( $attribute->attribute_name );

					$data[] = array(
						'id'   => esc_attr( $term_name ),
						'text' => esc_attr( $attribute->attribute_label ),
					);
				}
			}

			return $data;
		}

		public function proudct_taxonomies() {
			$data               = array();
			$exclude_taxonomies = array( 'product_type', 'product_visibility', 'product_shipping_class', 'product_tag', 'product_cat' );
			$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
			if ( ! empty( $product_taxonomies ) ) {
				foreach ( $product_taxonomies as $taxonomy ) {
					if ( ! in_array( $taxonomy->name, $exclude_taxonomies, true ) ) {
						$data[] = array(
							'id'   => esc_attr( $taxonomy->name ),
							'text' => esc_attr( $taxonomy->label ),
						);
					}
				}
			}

			return $data;
		}

		public function item_display() {
			$display = array(
				array(
					'id'   => 'all',
					'text' => esc_html__( 'All', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'parent',
					'text' => esc_html__( 'Only Parent', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'selected',
					'text' => esc_html__( 'Only Selected', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'except',
					'text' => esc_html__( 'Except Selected', 'pwf-woo-filter' ),
				),
			);

			return $display;
		}

		public function stock_status() {
			$stock_status = array(
				array(
					'id'   => 'instock',
					'text' => esc_html__( 'In stock', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'outofstock',
					'text' => esc_html__( 'Out of stock', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'onbackorder',
					'text' => esc_html__( 'On backorder', 'pwf-woo-filter' ),
				),
			);

			return $stock_status;
		}

		public function products_orderby() {

			$orderby = array(
				array(
					'id'   => 'menu_order',
					'text' => esc_html__( 'Default sorting', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'popularity',
					'text' => esc_html__( 'Sort by popularity', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'rating',
					'text' => esc_html__( 'Sort by average rating', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'date',
					'text' => esc_html__( 'Sort by latest', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'price',
					'text' => esc_html__( 'Sort by price: low to high', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'price-desc',
					'text' => esc_html__( 'Sort by price: high to low', 'pwf-woo-filter' ),
				),
			);

			return $orderby;
		}

		public function order_by() {
			$order_by = array(
				array(
					'id'   => 'name',
					'text' => esc_html__( 'Name', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'order',
					'text' => esc_html__( 'Order', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'count',
					'text' => esc_html__( 'Count', 'pwf-woo-filter' ),
				),
			);

			return $order_by;
		}

		public function default_toggle_state() {
			$toggle_state = array(
				array(
					'id'   => 'show',
					'text' => esc_html__( 'Show content', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'hide',
					'text' => esc_html__( 'Hide content', 'pwf-woo-filter' ),
				),
			);

			return $toggle_state;
		}

		public function action_for_empty_options() {
			$action = array(
				array(
					'id'   => 'showall',
					'text' => esc_html__( 'Show all', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'hide',
					'text' => esc_html__( 'Hide', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'markasdisable',
					'text' => esc_html__( 'Mark as disabled', 'pwf-woo-filter' ),
				),
			);

			return $action;
		}

		public function more_options_by() {

			$data = array(
				array(
					'id'   => 'disabled',
					'text' => esc_html__( 'Disabled', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'scrollbar',
					'text' => esc_html__( 'Scrollbar', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'morebutton',
					'text' => esc_html__( 'More button', 'pwf-woo-filter' ),
				),
			);

			return $data;
		}

		public function dropdown_style() {
			$data = array(
				array(
					'id'   => 'default',
					'text' => esc_html__( 'Default', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'plugin',
					'text' => esc_html__( 'Plugin style', 'pwf-woo-filter' ),
				),
			);

			return $data;
		}

		public function price_url_format() {
			$data = array(
				array(
					'id'   => 'dash',
					'text' => esc_html__( 'Parameters through a dash', 'pwf-woo-filter' ),
				),
				array(
					'id'   => 'two',
					'text' => esc_html__( 'Two parameters', 'pwf-woo-filter' ),
				),
			);

			return $data;
		}

		public function meta_key_compare_data() {

			$data = array(
				array(
					'text' => esc_html__( '=', 'pwf-woo-filter' ),
					'id'   => '=',
				),
				array(
					'text' => esc_html__( '!=', 'pwf-woo-filter' ),
					'id'   => '!=',
				),
				array(
					'text' => esc_html__( '&gt;', 'pwf-woo-filter' ),
					'id'   => '>',
				),
				array(
					'text' => esc_html__( '&lt;', 'pwf-woo-filter' ),
					'id'   => '<',
				),
				array(
					'text' => esc_html__( '&gt;=', 'pwf-woo-filter' ),
					'id'   => '>=',
				),
				array(
					'text' => esc_html__( '&lt;=', 'pwf-woo-filter' ),
					'id'   => '<=',
				),
				array(
					'text' => esc_html__( 'LIKE', 'pwf-woo-filter' ),
					'id'   => 'LIKE',
				),
				array(
					'text' => esc_html__( 'NOT LIKE', 'pwf-woo-filter' ),
					'id'   => 'NOT LIKE',
				),
				array(
					'text' => esc_html__( 'IN', 'pwf-woo-filter' ),
					'id'   => 'IN',
				),
				array(
					'text' => esc_html__( 'NOT IN', 'pwf-woo-filter' ),
					'id'   => 'NOT IN',
				),
				array(
					'text' => esc_html__( 'BETWEEN', 'pwf-woo-filter' ),
					'id'   => 'BETWEEN',
				),
				array(
					'text' => esc_html__( 'NOT BETWEEN', 'pwf-woo-filter' ),
					'id'   => 'NOT BETWEEN',
				),
			);

			return $data;
		}

		public function meta_key_type_data() {

			$data = array(
				array(
					'text' => esc_html__( 'CHAR', 'pwf-woo-filter' ),
					'id'   => 'CHAR',
				),
				array(
					'text' => esc_html__( 'NUMERIC', 'pwf-woo-filter' ),
					'id'   => 'NUMERIC',
				),
				array(
					'text' => esc_html__( 'TIME', 'pwf-woo-filter' ),
					'id'   => 'TIME',
				),
				array(
					'text' => esc_html__( 'DATE', 'pwf-woo-filter' ),
					'id'   => 'DATE',
				),
				array(
					'text' => esc_html__( 'DATETIME', 'pwf-woo-filter' ),
					'id'   => 'DATETIME',
				),
				array(
					'text' => esc_html__( 'DECIMAL', 'pwf-woo-filter' ),
					'id'   => 'DECIMAL',
				),
				array(
					'text' => esc_html__( 'SIGNED', 'pwf-woo-filter' ),
					'id'   => 'SIGNED',
				),
				array(
					'text' => esc_html__( 'UNSIGNED', 'pwf-woo-filter' ),
					'id'   => 'UNSIGNED',
				),
			);

			return $data;
		}

		public function range_slider_meta_source() {
			$data = array(
				array(
					'text' => esc_html__( 'Custom', 'pwf-woo-filter' ),
					'id'   => 'custom',
				),
				array(
					'text' => esc_html__( 'Weight', 'pwf-woo-filter' ),
					'id'   => '_weight',
				),
				array(
					'text' => esc_html__( 'Height', 'pwf-woo-filter' ),
					'id'   => '_height',
				),
				array(
					'text' => esc_html__( 'Length', 'pwf-woo-filter' ),
					'id'   => '_length',
				),
				array(
					'text' => esc_html__( 'Width', 'pwf-woo-filter' ),
					'id'   => '_width',
				),
			);

			return $data;
		}

		public function user_roles() {
			$wp_roles = wp_roles();
			$roles    = $wp_roles->roles;
			$data     = array();

			foreach ( $roles as $key => $role ) {
				$data[] = array(
					'id'   => esc_attr( $key ),
					'text' => esc_attr( $role['name'] ),
				);
			}

			return $data;
		}
	}
}
