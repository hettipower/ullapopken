<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Database_Query' ) ) {
	/**
	 * Used for metafields
	 */

	class Pwf_Database_Query {

		function __construct() {

		}

		public function get_proudct_categories() {
			$data = $this->proudct_taxonomies( 'product_cat' );

			if ( ! empty( $data ) ) {
				$data = array_merge( array( self::not_selected_text() ), $data );
			}

			return $data;
		}

		public function get_proudct_tags() {
			$data = $this->proudct_taxonomies( 'product_tag' );

			if ( ! empty( $data ) ) {
				$data = array_merge( array( self::not_selected_text() ), $data );
			}

			return $data;
		}

		public function get_pages() {

			$data = array();

			$pages = get_pages(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'sort_column' => 'post_title',
				)
			);

			if ( ! is_wp_error( $pages ) ) {
				$data[] = self::not_selected_text();
				foreach ( $pages as $page ) {
					$data[] = array(
						'id'   => absint( $page->ID ),
						'text' => esc_attr( $page->post_title ),
					);
				}
			}

			return $data;
		}

		/**
		 *
		 * return array
		 */
		public function proudct_all_taxonomies() {

			$data               = array();
			$exclude            = array( 'product_type', 'product_visibility', 'product_shipping_class', 'product_tag', 'product_cat' );
			$exclude_attributes = array_column( $this->product_attributes(), 'name' );
			$exclude            = array_merge( $exclude, $exclude_attributes );
			$taxonomies         = $this->get_all_taxonomies( $exclude );

			foreach ( $taxonomies as $taxomomy ) {
				$childrens = self::proudct_taxonomies( $taxomomy['name'] );
				if ( $childrens ) {
					foreach ( $childrens as $key => $child ) {
						$childrens[ $key ]['id'] = esc_attr( $taxomomy['name'] ) . '__' . absint( $child['id'] );
					}
					$childrens = array_merge( self::get_all_text( $taxomomy['name'], $taxomomy['label'] ), $childrens );

					$data[] = array(
						'text'     => esc_attr( $taxomomy['label'] ),
						'children' => $childrens,
					);
				}
			}

			if ( ! empty( $data ) ) {
				$data = array_merge( array( self::not_selected_text() ), $data );
			}

			return $data;
		}

		public function proudct_all_attributes() {

			$data       = array();
			$attributes = $this->product_attributes();
			foreach ( $attributes as $attribute ) {
				$childrens = $this->proudct_taxonomies( $attribute['name'] );
				if ( $childrens ) {
					foreach ( $childrens as $key => $child ) {
						$childrens[ $key ]['id'] = esc_attr( $attribute['name'] ) . '__' . absint( $child['id'] );
					}
					$childrens = array_merge( self::get_all_text( $attribute['name'], $attribute['label'] ), $childrens );

					$data[] = array(
						'text'     => esc_attr( $attribute['label'] ),
						'children' => $childrens,
					);
				}
			}

			if ( ! empty( $data ) ) {
				$data = array_merge( array( self::not_selected_text() ), $data );
			}
			return $data;
		}

		public function product_attributes() {

			$attributes = array();

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $tax ) {
					$attributes[] = array(
						'name'  => wc_attribute_taxonomy_name( $tax->attribute_name ),
						'label' => esc_attr( $tax->attribute_label ),
					);
				}
			}

			return $attributes;
		}

		public function get_users( $is_ajax = false, $roles = array() ) {

			$users = array();

			if ( ! empty( $roles ) ) {
				$roles = array_map( 'esc_attr', $roles );
				if ( in_array( 'all', $roles, true ) ) {
					$roles = array();
				}
			}

			$user_args = array(
				'hide_empty' => true,
				'fields'     => array( 'ID', 'display_name' ),
			);

			if ( ! empty( $roles ) ) {
				$user_args['role__in'] = $roles;
			}

			$get_users = get_users( $user_args );

			if ( ! empty( $get_users ) ) {
				foreach ( $get_users as $user ) {
					if ( $is_ajax ) {
						$users[] = array(
							'id'   => absint( $user->ID ),
							'text' => esc_attr( $user->display_name ),
						);
					} else {
						$users[] = array(
							'label' => esc_attr( $user->display_name ),
							'value' => absint( $user->ID ),
						);
					}
				}
			}

			return $users;
		}

		private function get_all_taxonomies( $exclude = array() ) {
			$taxonomies         = array();
			$woo_taxonomies     = get_object_taxonomies( 'product', 'objects' );
			$exclude_taxonomies = array( 'product_type', 'product_visibility' );
			if ( $exclude ) {
				$exclude_taxonomies = array_merge( $exclude_taxonomies, $exclude );
			}
			foreach ( $woo_taxonomies as $taxonomy ) {
				if ( ! in_array( $taxonomy->name, $exclude_taxonomies, true ) ) {
					$taxonomies[] = array(
						'label' => esc_attr( $taxonomy->label ),
						'name'  => esc_attr( $taxonomy->name ),
					);
				}
			}

			return $taxonomies;
		}

		public function proudct_taxonomies( $taxonomy_name, $parent = 0, $add_all_text = false ) {
			$data = array();
			$args = array(
				'taxonomy'   => $taxonomy_name,
				'hide_empty' => false,
			);

			$terms = get_terms( $args );
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$data[] = array(
						'id'   => absint( $term->term_id ),
						'text' => esc_attr( $term->name ),
					);
				}
			}

			if ( ! empty( $data ) && $add_all_text ) {
				$all_text = array(
					'id'   => 'all',
					'text' => esc_html__( 'All', 'pwf-woo-filter' ),
				);

				$data = array_merge( array( $all_text ), $data );
			}

			return $data;
		}

		public function get_ajax_product_taxonomies( $taxonomy_name, $parent = '' ) {
			$data = array();
			$args = array(
				'taxonomy'   => esc_attr( $taxonomy_name ),
				'hide_empty' => false,
			);

			if ( ! empty( $parent ) && 'all' !== $parent ) {
				$args['parent'] = absint( $parent );
			} else {
				$args['parent'] = 0;
			}

			$terms = get_terms( $args );
			if ( ! is_wp_error( $terms ) ) {
				$data = self::build_hierarchy_taxonomyies( $terms, $taxonomy_name );
			}

			return $data;
		}

		// backend only
		private static function build_hierarchy_taxonomyies( $terms, $taxonomy_name ) {
			$data            = array();
			$is_hierarchical = is_taxonomy_hierarchical( $taxonomy_name );

			foreach ( $terms as $term ) {
				$term_data         = array();
				$term_data['id']   = absint( $term->term_id );
				$term_data['text'] = esc_attr( $term->name );

				if ( $is_hierarchical ) {
					$children = get_terms( self::get_default_child_taxonomy_argments( $taxonomy_name, $term->term_id ) );
					if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
						$term_data['subcat'] = self::build_hierarchy_taxonomyies( $children, $taxonomy_name );
					}
				}
				$data[] = $term_data;
			}

			return $data;
		}

		private static function get_default_child_taxonomy_argments( $taxonomy, $taxonomy_id ) {
			$data = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'parent'     => $taxonomy_id,
			);
			return $data;
		}

		private static function get_all_text( $name, $label ) {
			$all_text = array(
				array(
					'id'   => $name . '__all',
					'text' => esc_html__( 'All', 'pwf-woo-filter' ) . ' ' . $label,
				),
			);

			return $all_text;
		}

		private static function not_selected_text() {
			return array(
				'id'   => '',
				'text' => esc_html__( 'No selected', 'pwf-woo-filter' ),
			);
		}
	}
}
