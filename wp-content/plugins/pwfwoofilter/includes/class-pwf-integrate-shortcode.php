<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Integrate_Shortcode' ) ) {

	class Pwf_Integrate_Shortcode {

		/**
		 * init
		 */
		protected $filter_id;

		/**
		 * args hold shortcode arguments
		 * array
		 */
		protected $args;


		public function __construct( $filter_id, $args ) {
			if ( ! is_int( absint( $filter_id ) ) || ! is_array( $args ) || empty( $args ) ) {
				return;
			}
			$this->filter_id = $filter_id;
			$this->args      = $this->parse_args( $args );
			$this->init();
		}

		public function parse_args( $args ) {
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

			$args = wp_parse_args( $args, $defaults );

			return $args;
		}

		protected function init() {
			$args = $this->args;

			add_filter( 'pwf_woo_filter_loop_products_attributes', array( $this, 'loop_product_attributes' ), 10, 2 );

			if ( wc_string_to_bool( $args['on_sale'] ) ) {
				add_filter( 'pwf_woo_get_filter_term_product_counts_query', array( $this, 'set_on_sale_products_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_term_product_sum_query', array( $this, 'set_on_sale_products_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_author_product_counts_query', array( $this, 'set_on_sale_products_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_stock_staus_product_counts_query', array( $this, 'set_on_sale_products_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_meta_field_product_counts_query', array( $this, 'set_on_sale_products_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_price_filter_sql', array( $this, 'set_min_max_price' ), 10, 2 );
				add_filter( 'pwf_woo_meta_range_filter_sql', array( $this, 'set_min_max_price' ), 10, 2 );
				add_filter( 'pwf_woo_date_filter_sql', array( $this, 'set_min_max_price' ), 10, 2 );
				add_filter( 'pwf_woo_products_loop', array( $this, 'set_sale_products_loop' ), 10, 2 );
			}

			if ( ! empty( $args['ids'] ) ) {
				add_filter( 'pwf_woo_get_filter_term_product_counts_query', array( $this, 'set_ids_query_args_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_term_product_sum_query', array( $this, 'set_ids_query_args_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_author_product_counts_query', array( $this, 'set_ids_query_args_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_stock_staus_product_counts_query', array( $this, 'set_ids_query_args_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_get_filter_meta_field_product_counts_query', array( $this, 'set_ids_query_args_terms_count' ), 10, 2 );
				add_filter( 'pwf_woo_price_filter_sql', array( $this, 'set_ids_query_args_min_max_price' ), 10, 2 );
				add_filter( 'pwf_woo_meta_range_filter_sql', array( $this, 'set_ids_query_args_min_max_price' ), 10, 2 );
				add_filter( 'pwf_woo_date_filter_sql', array( $this, 'set_ids_query_args_min_max_price' ), 10, 2 );
				add_filter( 'pwf_woo_products_loop', array( $this, 'set_ids_query_args_products_loop' ), 10, 2 );
			}

			if ( ! empty( $args['category'] ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'set_categories_query_args' ), 10, 2 );
			}

			if ( ! empty( $args['tag'] ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'set_tags_query_args' ), 10, 2 );
			}

			if ( ! empty( $args['attribute'] ) && ! empty( $args['terms'] ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'set_attributes_query_args' ), 10, 2 );
			}

			if ( wc_string_to_bool( $args['best_selling'] ) ) {
				add_filter( 'pwf_woo_products_loop', array( $this, 'set_best_selling_products_query_args' ), 10, 4 );
			}

			if ( wc_string_to_bool( $args['top_rated'] ) ) {
				add_filter( 'pwf_woo_products_loop', array( $this, 'set_top_rated_products_query_args' ), 10, 4 );
			}

			if ( ! empty( $args['skus'] ) ) {
				add_filter( 'pwf_parse_meta_query', array( $this, 'set_skus_meta_query' ), 10, 2 );
			}

			if ( ! empty( $args['visibility'] ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'set_visibility_query_args' ), 10, 2 );
				if ( in_array( $args['visibility'], array( 'hidden', 'search', 'catalog' ), true ) ) {
					add_action( 'pwf_before_shop_loop', array( $this, 'set_visibility_products_loop' ), 10, 1 );
					add_action( 'pwf_after_shop_loop', array( $this, 'remove_visibility_products_loop' ), 10, 1 );
				}
			}
		}

		protected function is_match_filter_id( $filter_id ) {
			return $this->filter_id === $filter_id;
		}

		public function loop_product_attributes( $attributes, $filter_id ) {
			$args = $this->args;
			if ( $this->is_match_filter_id( $filter_id ) ) {
				if ( ! empty( $args['limit'] ) ) {
					$attributes['posts_per_page'] = intval( $args['limit'] );
				}

				if ( ! empty( $args['columns'] ) ) {
					$attributes['columns'] = absint( $args['columns'] );
				}

				if ( ! empty( $args['order'] ) ) {
					$attributes['order'] = esc_attr( strtoupper( $args['order'] ) );
				}

				if ( ! empty( $args['orderby'] ) ) {
					$attributes['orderby'] = esc_attr( $args['orderby'] );
				}

				$attributes['paginate'] = $args['paginate'];
			}

			return $attributes;
		}
		public function set_on_sale_products_terms_count( $query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				global $wpdb;
				$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) ) ) . ')';
			}

			return $query;
		}

		public function set_min_max_price( $query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				global $wpdb;
				$query['where']  = substr( $query['where'], 0, -1 );
				$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) ) ) . ')';
				$query['where'] .= ')';
			}

			return $query;
		}

		public function set_sale_products_loop( $query_args, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			}

			return $query_args;
		}

		public function set_ids_query_args_terms_count( $query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				global $wpdb;
				$ids = array_map( 'trim', explode( ',', $this->args['ids'] ) );

				$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $ids ) ) . ')';
			}

			return $query;
		}

		public function set_ids_query_args_min_max_price( $query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				global $wpdb;
				$ids             = array_map( 'trim', explode( ',', $this->args['ids'] ) );
				$query['where']  = substr( $query['where'], 0, -1 );
				$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $ids ) ) . ')';
				$query['where'] .= ')';
			}

			return $query;
		}

		public function set_ids_query_args_products_loop( $query_args, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$ids                    = array_map( 'trim', explode( ',', $this->args['ids'] ) );
				$query_args['post__in'] = array_map( 'absint', $ids );
			}

			return $query_args;
		}

		/**
		 * see WC_Shortcode_Products
		 */
		public function set_categories_query_args( $tax_query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$args       = $this->args;
				$categories = array_map( 'sanitize_title', explode( ',', $args['category'] ) );
				$field      = 'slug';

				if ( is_numeric( $categories[0] ) ) {
					$field      = 'term_id';
					$categories = array_map( 'absint', $categories );
					// Check numeric slugs.
					foreach ( $categories as $cat ) {
						$the_cat = get_term_by( 'slug', $cat, 'product_cat' );
						if ( false !== $the_cat ) {
							$categories[] = $the_cat->term_id;
						}
					}
				}

				$tax_query[] = array(
					'taxonomy'         => 'product_cat',
					'terms'            => $categories,
					'field'            => $field,
					'operator'         => $args['cat_operator'],
					'include_children' => 'AND' === $args['cat_operator'] ? false : true,
				);
			}

			return $tax_query;
		}

		/**
		 * see WC_Shortcode_Products
		 */
		public function set_tags_query_args( $tax_query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$tax_query[] = array(
					'taxonomy' => 'product_tag',
					'terms'    => array_map( 'sanitize_title', explode( ',', $this->args['tag'] ) ),
					'field'    => 'slug',
					'operator' => $this->args['tag_operator'],
				);
			}

			return $tax_query;
		}

		/**
		 * see WC_Shortcode_Products
		 */
		public function set_attributes_query_args( $tax_query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$taxonomy = strstr( $this->args['attribute'], 'pa_' ) ? sanitize_title( $this->args['attribute'] ) : 'pa_' . sanitize_title( $this->args['attribute'] );
				$terms    = $this->args['terms'] ? array_map( 'sanitize_title', explode( ',', $this->args['terms'] ) ) : array();
				$field    = 'slug';

				if ( $terms && is_numeric( $terms[0] ) ) {
					$field = 'term_id';
					$terms = array_map( 'absint', $terms );
					// Check numeric slugs.
					foreach ( $terms as $term ) {
						$the_term = get_term_by( 'slug', $term, $taxonomy );
						if ( false !== $the_term ) {
							$terms[] = $the_term->term_id;
						}
					}
				}

				// If no terms were specified get all products that are in the attribute taxonomy.
				if ( ! $terms ) {
					$terms = get_terms(
						array(
							'taxonomy' => $taxonomy,
							'fields'   => 'ids',
						)
					);
					$field = 'term_id';
				}

				// We always need to search based on the slug as well, this is to accommodate numeric slugs.
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'terms'    => $terms,
					'field'    => $field,
					'operator' => $this->args['terms_operator'],
				);
			}

			return $tax_query;
		}

		public function set_best_selling_products_query_args( $query_args, $filter_id, $selected_items, $attributes ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$query_args['meta_key'] = 'total_sales';
				$query_args['order']    = 'DESC';
				$query_args['orderby']  = 'meta_value_num';
			}

			return $query_args;
		}

		public function set_top_rated_products_query_args( $query_args, $filter_id, $selected_items, $attributes ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$query_args['meta_key'] = '_wc_average_rating';
				$query_args['order']    = 'DESC';
				$query_args['orderby']  = 'meta_value_num';
			}

			return $query_args;
		}

		public function set_skus_meta_query( $meta_query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				$skus         = array_map( 'trim', explode( ',', $this->args['skus'] ) );
				$meta_query[] = array(
					'key'     => '_sku',
					'value'   => 1 === count( $skus ) ? $skus[0] : $skus,
					'compare' => 1 === count( $skus ) ? '=' : 'IN',
				);
			}

			return $meta_query;
		}

		public function set_visibility_query_args( $tax_query, $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {

				if ( 'featured' === $this->args['visibility'] ) {
					$tax_query[] = array(
						'taxonomy'         => 'product_visibility',
						'terms'            => 'featured',
						'field'            => 'name',
						'operator'         => 'IN',
						'include_children' => false,
					);
				} elseif ( 'hidden' === $this->args['visibility'] ) {
					$tax_query   = $this->unset_default_visibilty( $tax_query );
					$tax_query[] = array(
						'taxonomy'         => 'product_visibility',
						'terms'            => array( 'exclude-from-catalog', 'exclude-from-search' ),
						'field'            => 'name',
						'operator'         => 'AND',
						'include_children' => false,
					);
				} elseif ( 'search' === $this->args['visibility'] ) {
					$tax_query   = $this->unset_default_visibilty( $tax_query );
					$tax_query[] = array(
						'taxonomy'         => 'product_visibility',
						'terms'            => 'exclude-from-catalog',
						'field'            => 'name',
						'operator'         => 'IN',
						'include_children' => false,
					);
					$tax_query[] = array(
						'taxonomy'         => 'product_visibility',
						'terms'            => 'exclude-from-search',
						'field'            => 'name',
						'operator'         => 'NOT IN',
						'include_children' => false,
					);
				} elseif ( 'catalog' === $this->args['visibility'] ) {
					$tax_query   = $this->unset_default_visibilty( $tax_query );
					$tax_query[] = array(
						'taxonomy'         => 'product_visibility',
						'terms'            => 'exclude-from-search',
						'field'            => 'name',
						'operator'         => 'IN',
						'include_children' => false,
					);
					$tax_query[] = array(
						'taxonomy'         => 'product_visibility',
						'terms'            => 'exclude-from-catalog',
						'field'            => 'name',
						'operator'         => 'NOT IN',
						'include_children' => false,
					);
				}
			}

			return $tax_query;
		}

		/**
		 * Used to remove taxonomy product_visibility if exist
		 */
		protected function unset_default_visibilty( $tax_query ) {
			foreach ( $tax_query as $key => $value ) {
				if ( is_array( $value ) && isset( $value['taxonomy'] ) && 'product_visibility' === $value['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
			return $tax_query;
		}

		public function set_visibility_products_loop( $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				// Set custom product visibility when quering hidden products.
				add_filter( 'woocommerce_product_is_visible', array( $this, 'woocommerce_product_is_visible' ), 10, 1 );
			}
		}

		public function remove_visibility_products_loop( $filter_id ) {
			if ( $this->is_match_filter_id( $filter_id ) ) {
				remove_filter( 'woocommerce_product_is_visible', array( $this, 'woocommerce_product_is_visible' ), 10, 1 );
			}
		}

		public function woocommerce_product_is_visible( $visible ) {
			return true;
		}

	}
}
