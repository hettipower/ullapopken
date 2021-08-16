<?php

defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'Pwf_Api_Prepare_Filter_Post' ) ) {

	class Pwf_Api_Prepare_Filter_Post {
		protected $filter_settings;
		protected $filter_items;

		/**
		 * used to hide selected items for each filter items
		 * If request come from root /v2/pwf_woofilter
		 */
		protected $display_selected;

		function __construct( $filter_items, $filter_settings, $display_selected = false ) {
			$this->filter_items     = $filter_items;
			$this->filter_settings  = $filter_settings;
			$this->display_selected = $display_selected;
		}

		public function get_filter_settings() {
			return $this->prepare_filter_settings();
		}

		public function get_filter_items() {
			return $this->prepare_filter_items( $this->filter_items );
		}

		protected function prepare_filter_items( $filter_items ) {
			$result = array();

			foreach ( $filter_items as $key => $filter_item ) {
				if ( 'column' === $filter_item['item_type'] ) {
					$filter_item['children'] = $this->prepare_filter_items( $filter_item['children'] );
					array_push( $result, $filter_item );
				} else {
					if ( 'button' !== $filter_item['item_type'] ) {
						$filter_item = $this->prepare_single_filter_item( $filter_item );
						array_push( $result, $filter_item );
					}
				}
			}

			return $result;
		}

		/**
		 * @since 1.0.0
		 * @since 1.1.6 Add range slider
		 */
		protected function prepare_single_filter_item( $filter_item ) {
			$item = array();

			$item['type']  = esc_attr( $filter_item['item_type'] );
			$item['title'] = esc_attr( $filter_item['title'] );
			$defined_terms = array( 'attribute', 'category', 'tag', 'taxonomy' );

			if ( isset( $filter_item['url_key'] ) ) {
				$item['url_key'] = esc_attr( $filter_item['url_key'] );
			}

			if ( 'priceslider' === $filter_item['item_type'] ) {
				if ( 'two' === $filter_item['price_url_format'] ) {
					$item['url_key_min'] = esc_attr( $filter_item['url_key_min_price'] );
					$item['url_key_max'] = esc_attr( $filter_item['url_key_max_price'] );
				}

				$item['url_format'] = esc_attr( $filter_item['price_url_format'] );
			}

			if ( 'rangeslider' === $filter_item['item_type'] ) {
				if ( 'two' === $filter_item['range_slider_url_format'] ) {
					$item['url_key_min'] = esc_attr( $filter_item['url_key_range_slider_min'] );
					$item['url_key_max'] = esc_attr( $filter_item['url_key_range_slider_max'] );
				}

				$item['url_format'] = esc_attr( $filter_item['range_slider_url_format'] );
			}

			if ( 'date' === $filter_item['item_type'] ) {
				unset( $item['url_key'] );
				$item['url_key_date_after']  = esc_attr( $filter_item['url_key_date_after'] );
				$item['url_key_date_before'] = esc_attr( $filter_item['url_key_date_before'] );
				$item['interaction']         = isset( $filter_item['interaction'] ) ? esc_attr( $filter_item['interaction'] ) : '';
			}

			if ( isset( $filter_item['source_of_options'] ) ) {
				$item['source']   = esc_attr( $filter_item['source_of_options'] );
				$item['taxonomy'] = '';

				if ( 'rangeslider' === $filter_item['item_type'] ) {
					if ( 'attribute' === $filter_item['source_of_options'] ) {
						$item['taxonomy'] = esc_attr( $filter_item['item_source_attribute'] );
					} elseif ( 'attribute' === $filter_item['source_of_options'] ) {
						$item['taxonomy'] = esc_attr( $filter_item['item_source_taxonomy'] );
					}
				} elseif ( in_array( $filter_item['source_of_options'], $defined_terms, true ) ) {
					if ( isset( $filter_item['data_display'] ) && isset( $filter_item['data_display'][0] ) ) {
						// if show all exists there is no taxonomy
						if ( isset( $filter_item['data_display'][0]->taxonomy ) ) {
							$item['taxonomy'] = esc_attr( $filter_item['data_display'][0]->taxonomy );
						} elseif ( isset( $filter_item['data_display'][1]->taxonomy ) ) {
							$item['taxonomy'] = esc_attr( $filter_item['data_display'][1]->taxonomy );
						}
					}
				}
			}

			if ( 'rangeslider' === $filter_item['item_type'] ) {
				$item['step']        = esc_attr( $filter_item['step'] );
				$item['interaction'] = esc_attr( $filter_item['interaction'] );
				$item['unit']        = esc_attr( $filter_item['slider_range_unit'] );
			}

			if ( 'priceslider' === $filter_item['item_type'] ) {
				$item['step']        = esc_attr( $filter_item['price_step'] );
				$item['interaction'] = isset( $filter_item['interaction'] ) ? esc_attr( $filter_item['interaction'] ) : '';
			}

			$fileds_has_multiselect = array( 'textlist', 'colorlist', 'boxlist', 'dropdownlist' );
			if ( in_array( $filter_item['item_type'], $fileds_has_multiselect, true ) ) {
				$item['multi_select'] = ( 'on' === $filter_item['multi_select'] ) ? 'on' : 'off';
			}

			$exclude_fields = array( 'button', 'date', 'rangeslider', 'priceslider', 'rating' );
			if ( isset( $filter_item['data_display'] ) && ! in_array( $filter_item['item_type'], $exclude_fields, true ) ) {
				$item['data_display'] = $this->prepare_data_display( $filter_item );
			} elseif ( isset( $filter_item['data_display'] ) && 'rating' !== $filter_item['item_type'] ) {
				$item['data_display'] = $filter_item['data_display'];
			}

			if ( 'rating' === $filter_item['item_type'] ) {
				$up_text         = apply_filters( 'pwf_and_up_text', esc_html__( 'and up', 'pwf-woo-filter' ) );
				$item['up_text'] = ( 'on' === $filter_item['up_text'] ) ? 'on' : 'off';
				$data            = array();
				foreach ( $filter_item['data_display'] as $term ) {
					$rate        = new stdClass();
					$rate->id    = ( 'on' === $filter_item['up_text'] ) ? esc_attr( $term['value'] ) : absint( $term['value'] );
					$rate->name  = ( 'on' === $filter_item['up_text'] ) ? esc_attr( $term['label'] ) . ' ' . $up_text : esc_attr( $term['label'] );
					$rate->count = absint( $term['count'] );
					array_push( $data, $rate );
				}
				$item['data_display'] = $data;
			}

			if ( isset( $filter_item['data_selected'] ) && $this->display_selected ) {
				$term_is_id = array( 'attribute', 'category', 'tag', 'taxonomy', 'author' );
				if ( 'rangeslider' === $filter_item['item_type'] ) {
					$item['data_select'] = $filter_item['data_selected'];
				} elseif ( isset( $filter_item['source_of_options'] ) && in_array( $filter_item['source_of_options'], $term_is_id, true ) ) {
					$item['data_select'] = array_map( 'absint', $filter_item['data_selected'] );
				} elseif ( 'rating' === $filter_item['item_type'] ) {
					$item['data_select'] = ( 'on' === $filter_item['up_text'] ) ? array_map( 'esc_attr', $filter_item['data_selected'] ) : array_map( 'absint', $filter_item['data_selected'] );
				} else {
					$item['data_select'] = array_map( 'esc_attr', $filter_item['data_selected'] );
				}
			} elseif ( isset( $filter_item['data_selected'] ) ) {
				unset( $item['data_select'] );
			}

			return $item;
		}

		protected function prepare_data_display( $filter_item ) {
			$data         = array();
			$data_display = $filter_item['data_display'];

			if ( isset( $filter_item['source_of_options'] ) ) {
				$defined_terms = array( 'attribute', 'category', 'tag', 'taxonomy' );
				$other_terms   = array( 'meta', 'stock_status', 'orderby' );
				if ( in_array( $filter_item['source_of_options'], $defined_terms, true ) ) {
					foreach ( $data_display as $key => $term ) {
						$item         = new stdClass();
						$item->id     = absint( $term->term_id );
						$item->name   = esc_attr( $term->name );
						$item->parent = absint( $term->parent );
						$item->count  = absint( $term->count );
						array_push( $data, $item );
					}
				} elseif ( 'author' === $filter_item['source_of_options'] ) {
					foreach ( $data_display as $key => $author ) {
						$item        = new stdClass();
						$item->id    = absint( $author['ID'] );
						$item->name  = esc_attr( $author['display_name'] );
						$item->count = absint( $author['count'] );
						array_push( $data, $item );
					}
				} elseif ( in_array( $filter_item['source_of_options'], $other_terms, true ) ) {
					foreach ( $data_display as $key => $term ) {
						$item       = new stdClass();
						$item->id   = esc_attr( $term['value'] );
						$item->name = esc_attr( $term['label'] );
						if ( 'orderby' !== $filter_item['source_of_options'] ) {
							$item->count = absint( $term['count'] );
						}
						if ( 'colorlist' === $filter_item['item_type'] && 'meta' === $filter_item['source_of_options'] ) {
							$item->type        = esc_attr( $term['type'] );
							$item->color       = esc_attr( $term['color'] );
							$item->image       = esc_url( $term['image'] );
							$item->bordercolor = esc_attr( $term['bordercolor'] );
							$item->marker      = esc_attr( $term['marker'] );
						}
						array_push( $data, $item );
					}
				}

				if ( 'boxlist' === $filter_item['item_type'] ) {
					if ( ! in_array( $filter_item['source_of_options'], array( 'meta', 'stock_status' ), true ) && ! empty( $filter_item['boxlistlabel'] ) ) {
						$boxlistlabel = $filter_item['boxlistlabel'];
						foreach ( $data as $key => $item ) {
							//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							$id_exist = array_search( $item->id, array_column( $boxlistlabel, 'term_id' ) );
							if ( false !== $id_exist && ! empty( $boxlistlabel[ $id_exist ]['value'] ) ) {
								$data[ $key ]->name = esc_attr( $boxlistlabel[ $id_exist ]['value'] );
							}
						}
					}
				} elseif ( 'meta' !== $filter_item['source_of_options'] && 'colorlist' === $filter_item['item_type'] ) {
					$color_list = $filter_item['colors'];
					foreach ( $data as $key => $item ) {
						//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						$id_exist = array_search( $item->id, array_column( $color_list, 'term_id' ) );
						if ( false !== $id_exist ) {
							$item_color = $color_list[ $id_exist ];

							$data[ $key ]->type        = esc_attr( $item_color['type'] );
							$data[ $key ]->color       = esc_attr( $item_color['color'] );
							$data[ $key ]->image       = esc_attr( $item_color['image'] );
							$data[ $key ]->bordercolor = esc_attr( $item_color['bordercolor'] );
							$data[ $key ]->marker      = esc_attr( $item_color['marker'] );
						}
					}
				}
			} else {
				$data = $data_display;
			}

			return $data;
		}
	}
}
