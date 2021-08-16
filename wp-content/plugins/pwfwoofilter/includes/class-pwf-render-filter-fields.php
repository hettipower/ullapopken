<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Render_Filter_Fields' ) ) {

	class Pwf_Render_Filter_Fields {

		protected $filter_item;
		protected $filter_item_index;
		protected $terms;
		protected $filter_item_type;
		protected $selected_values;

		public function __construct( $filter_item, $filter_item_index, $terms, $selected_values = array(), $args = array() ) {
			$this->filter_item       = $filter_item;
			$this->filter_item_index = $filter_item_index;
			$this->terms             = $terms;
			$this->filter_item_type  = $this->filter_item['item_type'];
			$this->selected_values   = $selected_values;
		}

		public function get_html_template() {
			$output = '';
			switch ( $this->filter_item_type ) {
				case 'checkboxlist':
					$output = $this->get_checkboxlist();
					break;
				case 'radiolist':
					$output = $this->render_radiolist();
					break;
				case 'dropdownlist':
					$output = self::render_dropdownlist();
					break;
				case 'textlist':
					$output = self::render_textlist();
					break;
				case 'boxlist':
					$output = self::render_boxlist();
					break;
				case 'colorlist':
					$output = self::render_colorlist();
					break;
				case 'button':
					$output = self::render_button();
					break;
				case 'priceslider':
					$output .= self::render_priceslider();
					break;
				case 'rangeslider':
					$output .= self::render_range_slider();
					break;
				case 'date':
					$output .= self::render_date();
					break;
				case 'search':
					$output .= self::render_search();
					break;
				case 'rating':
					$output .= self::render_rating();
					break;
			}

			return $output;
		}

		protected function get_filter_item_name() {
			return $this->filter_item['url_key'];
		}

		protected function get_hierarchy_args() {
			$css_class    = '';
			$is_hierarchy = false;
			$source       = $this->filter_item['source_of_options'];

			if ( 'category' === $source || 'taxonomy' === $source ) {
				if ( 'checkboxlist' === $this->filter_item_type || 'radiolist' === $this->filter_item_type ) {
					if ( 'on' === $this->filter_item['display_hierarchical'] ) {
						$is_hierarchy = true;
						$css_class    = ' pwf-items-hierarchical';
					}
				} elseif ( 'textlist' === $this->filter_item_type ) {
					if ( 'on' === $this->filter_item['display_hierarchical'] && 'on' !== $this->filter_item['inline_style'] ) {
						$is_hierarchy = true;
						$css_class    = ' pwf-items-hierarchical';
					} elseif ( 'on' === $this->filter_item['inline_style'] ) {
						$css_class = ' pwf-textlist-inline-style';
					}
				} elseif ( 'dropdownlist' === $this->filter_item_type ) {
					if ( 'on' === $this->filter_item['display_hierarchical'] && 'plugin' === $this->filter_item['dropdown_style'] ) {
						$is_hierarchy = true;
						$css_class    = ' pwf-items-hierarchical';
					}
				}
			} else {
				if ( 'textlist' === $this->filter_item_type ) {
					if ( 'on' === $this->filter_item['inline_style'] ) {
						$css_class = ' pwf-textlist-inline-style';
					}
				}
			}

			$args = array(
				'css_class'    => $css_class,
				'is_hierarchy' => $is_hierarchy,
			);

			return $args;
		}

		protected function get_html_filter_item( $css_class, $inner_content ) {
			$output  = $this->get_html_filter_item_header( $css_class );
			$output .= $this->get_filter_item_title();
			$output .= $this->get_html_filter_item_container();
			$output .= $inner_content;
			$output .= $this->get_html_filter_item_container_end();
			$output .= $this->get_html_filter_item_footer();

			return $output;
		}

		protected function has_more_option_button() {
			$fields_has_more_option = array( 'checkboxlist', 'radiolist', 'textlist' );
			if ( 'textlist' === $this->filter_item_type && 'on' === $this->filter_item['inline_style'] ) {
				return false;
			}

			if ( in_array( $this->filter_item_type, $fields_has_more_option, true ) ) {
				return true;
			}

			return false;
		}

		protected function get_html_filter_item_header( string $css_class ) {
			$data_item_key = '';
			if ( 'button' !== $this->filter_item_type ) {
				$data_item_key = ' data-item-key="' . $this->get_filter_item_name() . '"';
			}

			if ( $this->has_more_option_button() ) {
				if ( isset( $this->filter_item['more_options_by'] ) ) {
					if ( 'scrollbar' === $this->filter_item['more_options_by'] ) {
						$css_class .= ' pwf-scrollbar';
					} elseif ( 'morebutton' === $this->filter_item['more_options_by'] ) {
						$css_class .= ' pwf-more-button-block';
					}
				}
			}

			if ( isset( $this->filter_item['display_tooltip'] ) && 'on' === $this->filter_item['display_tooltip'] ) {
				$css_class .= ' range-slider-has-tooltip';
			}

			return '<div class="pwf-field-item pwf-item-id-' . $this->filter_item_index . ' pwf-field-item-' . $this->filter_item_type . esc_attr( $css_class ) . '"' . $data_item_key . '><div class="pwf-field-inner">';
		}

		protected function get_html_filter_item_footer() {
			return '</div></div>';
		}

		protected function get_html_filter_item_container() {
			$style = '';

			if ( $this->has_more_option_button() ) {
				if ( isset( $this->filter_item['more_options_by'] ) && 'scrollbar' === $this->filter_item['more_options_by'] ) {
					if ( ! empty( $this->filter_item['height_of_visible_content'] ) ) {
						$style = ' style="max-height:' . absint( $this->filter_item['height_of_visible_content'] ) . 'px;"';
					}
				}
			}
			$output = '<div class="pwf-field-item-container"' . $style . '>';

			return $output;
		}

		protected function get_html_filter_item_container_end() {
			$output = '';
			if ( $this->has_more_option_button() ) {
				if ( isset( $this->filter_item['more_options_by'] ) && 'morebutton' === $this->filter_item['more_options_by'] ) {
					$show_more = apply_filters( 'pwf_show_more_text', esc_html__( 'Show more', 'pwf-woo-filter' ) );
					$show_less = apply_filters( 'pwf_show_less_text', esc_html__( 'Show less', 'pwf-woo-filter' ) );

					$output .= '<div class="pwf-more-button pwf-status-active">';
					$output .= '<div class="pwf-more-button-inner"><span class="pwf-icon-more"></span>';
					$output .= '<span class="pwf-more-text">' . $show_more . '</span>';
					$output .= '<span class="pwf-less-text">' . $show_less . '</span>';
					$output .= '</div></div>';
				}
			}

			$output .= '</div>';

			return $output;
		}

		protected function get_custom_css_class() {
			$css_class = '';

			if ( ! empty( $this->selected_values ) ) {
				$css_class .= ' pwf-has-selected-option';
			}

			if ( 'button' !== $this->filter_item_type && 'column' !== $this->filter_item_type ) {
				if ( 'on' === $this->filter_item['display_title'] && 'on' === $this->filter_item['display_toggle_content'] ) {
					$css_class .= ( 'show' === $this->filter_item['default_toggle_state'] ) ? ' pwf-collapsed-open' : ' pwf-collapsed-close';
				}
			}

			if ( ! empty( $this->filter_item['css_class'] ) ) {
				$css_class .= ' ' . esc_attr( $this->filter_item['css_class'] );
			}

			return $css_class;
		}

		protected function get_filter_item_title() {
			$output = '';

			if ( 'on' === $this->filter_item['display_title'] ) {
				$output .= '<div class="pwf-field-item-title"><span class="text-title">';
				$output .= $this->filter_item['title'] . '</span>';
				if ( 'on' === $this->filter_item['display_toggle_content'] ) {
					$output .= '<span class="pwf-toggle pwf-toggle-widget-title"></span>';
				}
				$output .= '</div>';
			}

			return $output;
		}

		protected function get_checkboxlist() {
			$css_class      = '';
			$output         = '';
			$hierarchy_args = $this->get_hierarchy_args();
			$is_hierarchy   = $hierarchy_args['is_hierarchy'];
			$css_class     .= $hierarchy_args['css_class'] . $this->get_custom_css_class();

			$custom_source = array( 'stock_status', 'meta', 'author' );
			if ( in_array( $this->filter_item['source_of_options'], $custom_source, true ) ) {
				if ( ! empty( $this->terms ) ) {
					foreach ( $this->terms as $term ) {
						$css       = '';
						$checked   = '';
						$disabled  = '';
						$visibilty = true;

						if ( 'author' === $this->filter_item['source_of_options'] ) {
							$slug = $term['user_nicename'];
						} else {
							$slug = $term['value'];
						}

						if ( ! empty( $this->selected_values ) ) {
							//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							if ( in_array( $term['value'], $this->selected_values ) ) {
								$css    .= ' checked';
								$checked = ' checked';
							}
						}

						if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
							$visibilty = false;
						} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
							$css     .= ' pwf-disabled';
							$disabled = ' disabled';
						}

						if ( $visibilty ) {
							$output .= '<div class="pwf-item pwf-checkboxlist-item' . $css . '">';
							$output .= '<div class="pwf-item-inner">';
							$output .= '<div class="pwf-item-label pwf-checkbox-label">';
							$output .= '<div class="pwf-input-container pwf-checkbox-click-area"><input type="checkbox" class="pwf-input pwf-input-checkbox" name="' . $this->get_filter_item_name() . '" data-slug="' . $slug . '" value="' . $term['value'] . '"' . $checked . $disabled . '></div>';
							$output .= '<div class="pwf-title-container pwf-checkbox-click-area"><span class="text-title">' . $term['label'] . '</span>';
							if ( 'on' === $this->filter_item['display_product_counts'] && isset( $term['count'] ) && ! empty( $term['count'] ) ) {
								$output .= self::get_html_template_item_count( $term['count'] );
							}
							$output .= '</div>';
							$output .= '</div>';
							$output .= '</div>';
							$output .= '</div>';
						}
					}
				}
			} else {
				$walker  = new Pwf_Walker_Checkbox();
				$output .= $walker->start_walk( $this->filter_item, $this->terms, $is_hierarchy, $this->selected_values );
			}

			if ( '' !== $output ) {
				$output = $this->get_html_filter_item( $css_class, $output );
			}
			return $output;
		}

		protected function render_radiolist() {
			$hierarchy_args = $this->get_hierarchy_args();
			$is_hierarchy   = $hierarchy_args['is_hierarchy'];
			$css_class      = $hierarchy_args['css_class'] . $this->get_custom_css_class();
			$output         = '';

			$custom_source = array( 'stock_status', 'meta', 'orderby', 'author' );
			if ( in_array( $this->filter_item['source_of_options'], $custom_source, true ) ) {
				if ( ! empty( $this->terms ) ) {
					if ( 'orderby' === $this->filter_item['source_of_options'] || $this->should_display_filter_item( $this->terms ) ) {
						foreach ( $this->terms as $term ) {
							$css       = '';
							$checked   = '';
							$disabled  = '';
							$visibilty = true;

							if ( 'author' === $this->filter_item['source_of_options'] ) {
								$slug = $term['user_nicename'];
							} else {
								$slug = $term['value'];
							}

							if ( ! empty( $this->selected_values ) ) {
								//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
								if ( in_array( $term['value'], $this->selected_values ) ) {
									$checked = ' checked';
								}
							} elseif ( 'showall' === $term['value'] ) {
								$checked = ' checked';
							}

							$hide_only = array( 'stock_status', 'meta', 'author' );
							if ( 'showall' !== $term['value'] && in_array( $this->filter_item['source_of_options'], $hide_only, true ) ) {
								if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
									$visibilty = false;
								} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
									$css      = ' pwf-disabled';
									$disabled = ' disabled';
								}
							}

							if ( $visibilty ) {
								$output .= '<div class="pwf-item pwf-radiolist-item' . $css . '">';
								$output .= '<div class="pwf-item-inner">';
								$output .= '<div class="pwf-item-label pwf-radiolist-label' . $checked . '">';
								$output .= '<div class="pwf-input-container">';
								$output .= '<input type="radio" class="pwf-input pwf-input-radio" name="' . esc_attr( $this->get_filter_item_name() ) . '" data-slug="' . $term['value'] . '" value="' . esc_attr( $term['value'] ) . '"' . $checked . $disabled . '>';
								$output .= '</div>';
								$output .= '<div class="pwf-title-container"><span class="text-title">' . esc_attr( $term['label'] ) . '</span>';
								if ( 'on' === $this->filter_item['display_product_counts'] && isset( $term['count'] ) && ! empty( $term['count'] ) ) {
									$output .= self::get_html_template_item_count( $term['count'] );
								}
								$output .= '</div>';
								$output .= '</div>';
								$output .= '</div>';
								$output .= '</div>';
							}
						}
					}
				}
			} else {
				if ( $this->should_display_filter_item( $this->terms ) ) {
					$walker  = new Pwf_Walker_Radio();
					$output .= $walker->start_walk( $this->filter_item, $this->terms, $is_hierarchy, $this->selected_values );
				}
			}

			if ( '' !== $output ) {
				$output = $this->get_html_filter_item( $css_class, $output );
			}

			return $output;
		}

		protected function render_dropdownlist() {
			$css_class      = '';
			$multi_select   = $this->filter_item['multi_select'] ?? '';
			$hierarchy_args = $this->get_hierarchy_args();
			$is_hierarchy   = $hierarchy_args['is_hierarchy'];
			$css_class      = $hierarchy_args['css_class'] . ' ' . $this->get_custom_css_class();
			$output         = '';

			if ( 'plugin' === $this->filter_item['dropdown_style'] ) {
				$css_class .= ' pwf-items-dropdown-has-select2';
			}

			$custom_source = array( 'stock_status', 'meta', 'orderby', 'author' );
			if ( in_array( $this->filter_item['source_of_options'], $custom_source, true ) ) {
				if ( ! empty( $this->terms ) ) {
					if ( 'orderby' === $this->filter_item['source_of_options'] || $this->should_display_filter_item( $this->terms ) ) {
						foreach ( $this->terms as $term ) {
							$disabled  = '';
							$visibilty = true;
							$selected  = '';
							if ( ! empty( $this->selected_values ) ) {
								//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
								if ( in_array( $term['value'], $this->selected_values ) ) {
									$selected = ' selected';
								}
							} elseif ( 'showall' === $term['value'] && 'on' !== $multi_select ) {
								$selected = ' selected';
							}

							if ( 'showall' === $term['value'] ) {
								$slug = $term['slug'];
							} elseif ( 'author' === $this->filter_item['source_of_options'] ) {
								$slug = $term['user_nicename'];
							} else {
								$slug = $term['value'];
							}

							$hide_only = array( 'stock_status', 'meta', 'author' );
							if ( 'showall' !== $term['value'] && in_array( $this->filter_item['source_of_options'], $hide_only, true ) ) {
								if ( 'stock_status' === $this->filter_item['source_of_options'] || 'meta' === $this->filter_item['source_of_options'] ) {
									if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
										$visibilty = false;
									} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
										$disabled = ' disabled';
									}
								}
							}

							if ( $visibilty ) {
								$output .= '<option data-slug="' . $slug . '" data-title="' . esc_attr( $term['label'] ) . '" value="' . esc_attr( $term['value'] ) . '"' . $selected . $disabled . '>' . esc_attr( $term['label'] );
								if ( 'on' === $this->filter_item['display_product_counts'] && isset( $term['count'] ) && ! empty( $term['count'] ) ) {
									$output .= ' - ' . absint( $term['count'] );
								}
								$output .= '</option>';
							}
						}
					}
				}
			} else {
				if ( $this->should_display_filter_item( $this->terms ) ) {
					$walker  = new Pwf_Walker_Dropdown_List();
					$output .= $walker->start_walk( $this->filter_item, $this->terms, $is_hierarchy, $this->selected_values );
				}
			}

			if ( '' !== $output ) {
				$select_css = ' pwf-dropdownlist-item-default';

				if ( 'plugin' === $this->filter_item['dropdown_style'] ) {
					$select_css = ' pwf-dropdownlist-item-select2';

					$except_multiple = array( 'stock_status', 'orderby' );
					if ( ( 'on' === $multi_select ) && ! in_array( $this->filter_item['source_of_options'], $except_multiple, true ) ) {
						$select_css .= ' pwf-has-multiple';
						$css_class  .= ' pwf-has-multiple-select';
					}
				}

				$start_select = '<div class="pwf-select"><select name="' . esc_attr( $this->get_filter_item_name() ) . '" class="pwf-item pwf-dropdownlist-item' . $select_css . '"' . '>';
				$end_select   = '</select></div>';
				$output       = $start_select . $output . $end_select;
				$output       = $this->get_html_filter_item( $css_class, $output );
			}

			return $output;
		}

		protected function render_textlist() {
			$css_class      = '';
			$hierarchy_args = $this->get_hierarchy_args();
			$is_hierarchy   = $hierarchy_args['is_hierarchy'];
			$css_class      = $hierarchy_args['css_class'] . $this->get_custom_css_class();
			$output         = '';

			$custom_source = array( 'stock_status', 'meta', 'author' );
			if ( in_array( $this->filter_item['source_of_options'], $custom_source, true ) ) {
				if ( ! empty( $this->terms ) ) {
					foreach ( $this->terms as $term ) {
						$css       = '';
						$disabled  = '';
						$visibilty = true;

						if ( 'author' === $this->filter_item['source_of_options'] ) {
							$slug = $term['user_nicename'];
						} else {
							$slug = $term['value'];
						}

						//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						if ( in_array( $term['value'], $this->selected_values ) ) {
							$css .= ' selected';
						}
						if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
							$visibilty = false;
						} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
							$css .= ' pwf-disabled';
						}

						if ( $visibilty ) {
							$output .= '<div class="pwf-item pwf-textlist-item' . $css . '" data-slug="' . $slug . '" data-item-value="' . $term['value'] . '">';
							$output .= '<div class="pwf-item-inner">';
							$output .= '<div class="pwf-item-label pwf-textlist-label">';
							$output .= '<div class="pwf-title-container"><span class="text-title">' . $term['label'] . '</span>';
							if ( 'on' === $this->filter_item['display_product_counts'] && isset( $term['count'] ) && ! empty( $term['count'] ) ) {
								$output .= self::get_html_template_item_count( $term['count'] );
							}
							$output .= '</div>';
							$output .= '</div>';
							$output .= '</div>';
							$output .= '</div>';
						}
					}
				}
			} else {
				$walker  = new Pwf_Walker_Textlist();
				$output .= $walker->start_walk( $this->filter_item, $this->terms, $is_hierarchy, $this->selected_values );
			}

			if ( '' !== $output ) {
				$output = $this->get_html_filter_item( $css_class, $output );
			}

			return $output;
		}

		protected function render_boxlist() {
			$css_class = $this->get_custom_css_class();
			$output    = '';
			$style     = '';
			$size      = absint( $this->filter_item['size'] );

			if ( 45 !== $size ) {
				$style = ' style="width:' . $size . 'px; height:' . $size . 'px; line-height:' . $size . 'px"';
			}

			if ( ! empty( $this->terms ) ) {
				foreach ( $this->terms as $term ) {
					$css       = '';
					$visibilty = true;
					$term      = (array) $term;
					$source    = array( 'meta', 'stock_status' );
					if ( in_array( $this->filter_item['source_of_options'], $source, true ) ) {
						$term_id = $term['value'];
						$label   = $term['label'];
						$slug    = $term['value'];
						$count   = $term['count'];
					} elseif ( 'author' === $this->filter_item['source_of_options'] ) {
						$term_id = $term['ID'];
						$label   = $term['label'];
						$slug    = $term['user_nicename'];
						$count   = $term['count'];
					} else {
						$term_id = $term['term_id'];
						$label   = $term['name'];
						$slug    = $term['slug'];
						$count   = $term['count'];
					}

					if ( ! empty( $this->filter_item['boxlistlabel'] ) ) {
						$boxlistlabel = $this->filter_item['boxlistlabel'];
						//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						$id_exist = array_search( $term_id, array_column( $boxlistlabel, 'term_id' ) );
						if ( false !== $id_exist ) {
							$label = $boxlistlabel[ $id_exist ]['value'];
						}
					}

					//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					if ( in_array( $term_id, $this->selected_values ) ) {
						$css .= ' selected';
					}
					if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $count ) {
						$visibilty = false;
					} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $count ) {
						$css .= ' pwf-disabled';
					}

					if ( $visibilty ) {
						$output .= '<div class="pwf-item pwf-boxlist-item' . $css . '" data-slug="' . $slug . '" data-item-value="' . $term_id . '"' . $style . '>';
						$output .= '<div class="pwf-item-inner">';
						$output .= '<div class="pwf-item-label">';
						$output .= '<div class="pwf-title-container"><span class="text-title">' . $label . '</span>';
						if ( 'on' === $this->filter_item['display_product_counts'] && $count > 0 ) {
							$output .= self::get_html_template_item_count( $count );
						}
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</div></div>';
					}
				}
			}

			if ( '' !== $output ) {
				$output = $this->get_html_filter_item( $css_class, $output );
			}

			return $output;
		}

		protected function render_colorlist() {
			$css_class  = $this->get_custom_css_class();
			$output     = '';
			$color_list = $this->filter_item['colors'];
			$style      = '';

			if ( 'rounded' === $this->filter_item['box_style'] ) {
				$css_class .= ' pwf-rounded';
			}

			$size     = '';
			$box_size = absint( $this->filter_item['size'] );
			if ( 45 !== $box_size && $box_size > 0 ) {
				$size = 'width:' . $box_size . 'px; height:' . $box_size . 'px; line-height:' . $box_size . 'px;';
				if ( 'rounded' === $this->filter_item['box_style'] ) {
					$size .= 'border-radius:' . $box_size . 'px;-webkit-border-radius:' . $box_size . 'px;';
				}
			}

			foreach ( $this->terms as $term ) {
				$css          = '';
				$selected     = '';
				$css_style    = '';
				$visibilty    = true;
				$border_color = '';

				if ( ! is_array( $term ) ) {
					$term = (array) $term;
				}

				if ( 'meta' !== $this->filter_item['source_of_options'] ) {
					if ( 'author' === $this->filter_item['source_of_options'] ) {
						$term_id = $term['ID'];
						$label   = $term['label'];
						$slug    = $term['user_nicename'];
						$count   = $term['count'];
					} else {
						$term_id = $term['term_id'];
						$label   = $term['name'];
						$slug    = $term['slug'];
						$count   = $term['count'];
					}
					//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					$id_exist = array_search( $term_id, array_column( $color_list, 'term_id' ) );
					if ( false !== $id_exist ) {
						$item = $color_list[ $id_exist ];
						$term = array_merge( $term, $item );
					} else {
						$default = array(
							'color'       => '',
							'image'       => '',
							'type'        => 'color',
							'bordercolor' => '',
							'marker'      => 'light',
						);

						$term = array_merge( $term, $default );
					}
				} else {
					$term_id = $term['value'];
					$label   = $term['label'];
					$slug    = $term['value'];
					$count   = $term['count'];
				}

				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $term_id, $this->selected_values ) ) {
					$css .= ' selected';
				}

				if ( ! empty( $term['bordercolor'] ) ) {
					$border_color = 'border-color:' . esc_attr( $term['bordercolor'] ) . ';';
				}
				if ( 'image' === $term['type'] ) {
					$css       .= ' pwf-type-image';
					$background = 'background-image:url(' . esc_url( $term['image'] ) . ');';
				} else {
					$css       .= ' pwf-type-color';
					$background = 'background-color:' . esc_attr( $term['color'] ) . ';';
				}
				if ( 'light' === $term['marker'] ) {
					$css .= ' light-marker';
				} else {
					$css .= ' dark-marker';
				}

				if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
					$visibilty = false;
				} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
					$css .= ' pwf-disabled';
				}

				if ( ! empty( $size ) || ! empty( $border_color ) || ! empty( $background ) ) {
					$style = ' style="' . $size . $background . $border_color . '"';
				}

				if ( $visibilty ) {
					$output .= '<div data-slug="' . $slug . '" data-item-value="' . $term_id . '" class="pwf-item pwf-colorlist-item' . esc_attr( $css ) . '"' . $style . ' data-item-title="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '">';
					if ( 'on' === $this->filter_item['display_product_counts'] && $count > 0 ) {
						$output .= self::get_html_template_item_count( $count );
					}
					$output .= '</div>';
				}
			}

			if ( '' !== $output ) {
				$output = $this->get_html_filter_item( $css_class, $output );
			}

			return $output;
		}

		/**
		 * @since 1.0.0, 1.0.5, 1.1.6
		 */
		protected function render_priceslider() {
			if ( empty( $this->filter_item['min_max_price'] ) ) {
				return;
			}

			$output    = '';
			$limit     = '';
			$css_class = $this->get_custom_css_class();
			$random_id = rand( 1, 1000 );

			$min_price = $this->filter_item['min_max_price']['min_price'];
			$max_price = $this->filter_item['min_max_price']['max_price'];

			// Check to see if we should add taxes to the prices if store are excl tax but display incl.
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

			if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
				$tax_class = apply_filters( 'pwf_woocommerce_price_filter_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$min_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
					$max_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
				}
			}

			$min_price = apply_filters( 'pwf_woocommerce_price_filter_min_amount', floor( $min_price ) );
			$max_price = apply_filters( 'pwf_woocommerce_price_filter_max_amount', ceil( $max_price ) );

			// If both min and max are equal, we don't need a slider.
			if ( $min_price === $max_price ) {
				return;
			}

			if ( ! empty( $this->selected_values ) ) {
				$current_min_price = absint( $this->selected_values[0] );
				$current_max_price = absint( $this->selected_values[1] );
			} else {
				$current_min_price = ( $this->filter_item['price_start'] > 0 ) ? $this->filter_item['price_start'] : $min_price;
				$current_max_price = ( $this->filter_item['price_end'] > 0 ) ? $this->filter_item['price_end'] : $max_price;
			}

			$limit       = '';
			$interactive = $this->filter_item['interaction'] ?? '';
			if ( empty( $this->selected_values ) && 'on' === $interactive ) {
				$active_min = floor( $this->filter_item['min_max_price']['active_min'] );
				$active_max = ceil( $this->filter_item['min_max_price']['active_max'] );

				/**
				 * If active_min Equal to active_max and this item is interactive
				 * Don't display this item
				 */
				if ( $active_min === $active_max ) {
					return;
				}

				if ( ! ( $min_price === $active_min && $max_price === $active_max ) ) {
					$current_min_price = $active_min;
					$current_max_price = $active_max;

					$limit .= ' data-limit="' . ( $active_max - $active_min ) . '"';
				}
			}

			if ( ! empty( $this->filter_item['price_step'] ) && $this->filter_item['price_step'] > 0 ) {
				$step = $this->filter_item['price_step'];
			} else {
				$step = 1;
			}

			$tooltip = 'false';
			if ( 'on' === $this->filter_item['display_tooltip'] ) {
				$tooltip = 'true';
			}

			$output .= '<div class="pwf-range-slider-wrap pwf-price-slider-wrap">';
			$output .= '<div class="pwf-wrap-range-slider"><div id="pwf-range-slider-' . absint( $this->filter_item_index ) . '-' . $random_id . '" class="pwf-range-slider pwf-price-slider"';
			$output .= ' data-current-min="' . $current_min_price . '" data-current-max="' . $current_max_price . '"';
			$output .= ' data-min="' . $min_price . '" data-max="' . $max_price . '"';
			$output .= ' data-step="' . absint( $step ) . '"';
			$output .= $limit;
			$output .= ' data-tooltip="' . $tooltip . '"';
			$output .= '></div></div>';

			if ( 'on' === $this->filter_item['display_max_min_inputs'] ) {
				$output .= '<div class="pwf-price-slider-min-max-inputs">';
				$output .= '<input type="number" id="pwf-min-price-' . $random_id . '" class="pwf-min-value" value="' . $current_min_price . '" min="' . $min_price . '" max="' . $max_price . '" data-min="' . $min_price . '" placeholder="' . esc_attr__( 'Min price', 'pwf-woo-filter' ) . '" />';
				$output .= '<input type="number" id="pwf-max-price-' . $random_id . '" class="pwf-max-value" value="' . $current_max_price . '" min="' . $min_price . '" max="' . $max_price . '" data-max="' . $max_price . '" placeholder="' . esc_attr__( 'Max price', 'pwf-woo-filter' ) . '" />';
				$output .= '</div>';
			}

			if ( 'on' === $this->filter_item['display_price_label'] ) {
				$currency_symbol = get_woocommerce_currency_symbol();
				$price_format    = get_woocommerce_price_format();
				if ( empty( $currency_symbol ) ) {
					$currency_symbol = '&#36;';
				}
				$output .= '<div class="pwf-range-slider-labels pwf-price-labels">';
				$output .= '<span class="text-title">' . esc_html__( 'Price:', 'woocommerce' ) . '</span> ';
				$output .= '<span class="pwf-wrap-price">' . sprintf( $price_format, '<span class="pwf-currency-symbol">' . $currency_symbol . '</span>', '<span id="pwf-from-' . absint( $this->filter_item_index ) . '" class="pwf-from">' . $current_min_price . '</span>' ) . '</span>';
				$output .= '<span class="price-delimiter"> &mdash; </span>';
				$output .= '<span class="pwf-wrap-price">' . sprintf( $price_format, '<span class="pwf-currency-symbol">' . $currency_symbol . '</span>', '<span id="pwf-to-' . absint( $this->filter_item_index ) . '" class="pwf-to">' . $current_max_price . '</span>' ) . '</span>';
				$output .= '</div>';
			}

			$output .= '</div>';
			$output  = $this->get_html_filter_item( $css_class, $output );

			return $output;
		}

		/**
		 * Since 1.1.4, 1.1.6
		 */
		protected function render_range_slider() {
			$output    = '';
			$css_class = $this->get_custom_css_class();
			$random_id = rand( 1, 1000 );
			if ( empty( $this->filter_item['min_max_range'] ) ) {
				return;
			}

			$min_value = floor( $this->filter_item['min_max_range']['min_value'] );
			$max_value = ceil( $this->filter_item['min_max_range']['max_value'] );

			if ( $min_value === $max_value ) {
				return;
			}

			if ( ! empty( $this->selected_values ) ) {
				$current_min_value = $this->selected_values[0];
				$current_max_value = $this->selected_values[1];
			} else {
				$range_start = floatval( $this->filter_item['start'] );
				$range_end   = floatval( $this->filter_item['end'] );
				if ( $range_start && $range_end ) {
					$current_min_value = ( $range_start < $min_value ) ? $min_value : $range_start;
					$current_max_value = ( $range_end > $max_value ) ? $max_value : $range_end;
				} else {
					$current_min_value = $min_value;
					$current_max_value = $max_value;
				}
			}

			if ( ! empty( $this->filter_item['step'] ) && $this->filter_item['step'] > 0 ) {
				$step = floatval( $this->filter_item['step'] );
			} else {
				$step = 1;
			}

			$tooltip = 'false';
			if ( 'on' === $this->filter_item['display_tooltip'] ) {
				$tooltip = 'true';
			}

			$limit       = '';
			$interactive = $this->filter_item['interaction'] ?? '';
			if ( empty( $this->selected_values ) && 'on' === $interactive ) {
				$active_min = floor( $this->filter_item['min_max_range']['active_min'] );
				$active_max = ceil( $this->filter_item['min_max_range']['active_max'] );

				/**
				 * If active_min Equal to active_max and this item is interactive
				 * Don't display this item
				 */
				if ( $active_min === $active_max ) {
					return;
				}

				if ( ! ( $min_value === $active_min && $max_value === $active_max ) ) {
					$current_min_value = $active_min;
					$current_max_value = $active_max;

					$limit .= ' data-limit="' . ( $active_max - $active_min ) . '"';
				}
			}

			$output .= '<div class="pwf-range-slider-wrap">';
			$output .= '<div class="pwf-wrap-range-slider"><div id="pwf-range-slider-' . absint( $this->filter_item_index ) . '-' . $random_id . '" class="pwf-range-slider pwf-range-slider-field"';
			$output .= ' data-current-min="' . $current_min_value . '" data-current-max="' . $current_max_value . '"';
			$output .= ' data-min="' . $min_value . '" data-max="' . $max_value . '"';
			$output .= ' data-step="' . floatval( $step ) . '"';
			$output .= $limit;
			$output .= ' data-tooltip="' . $tooltip . '"';
			if ( ! empty( $this->filter_item['slider_range_unit'] ) ) {
				$output .= ' data-unit="' . esc_html( $this->filter_item['slider_range_unit'] ) . '"';
			}
			$output .= '></div></div>';

			if ( 'on' === $this->filter_item['display_max_min_inputs'] ) {
				$output .= '<div class="pwf-price-slider-min-max-inputs">';
				$output .= '<input type="number" id="pwf-range-min-value-' . $random_id . '" class="pwf-min-value" value="' . $current_min_value . '" min="' . $min_value . '" max="' . $max_value . '" data-min="' . $min_value . '" placeholder="' . esc_attr__( 'Min value', 'pwf-woo-filter' ) . '" />';
				$output .= '<input type="number" id="pwf-range-max-value-' . $random_id . '" class="pwf-max-value" value="' . $current_max_value . '" min="' . $min_value . '" max="' . $max_value . '" data-max="' . $max_value . '" placeholder="' . esc_attr__( 'Max value', 'pwf-woo-filter' ) . '" />';
				$output .= '</div>';
			}

			if ( 'on' === $this->filter_item['display_label'] ) {
				$unit  = '';
				$title = '';
				if ( ! empty( $this->filter_item['slider_range_unit'] ) ) {
					$unit = ' ' . $this->filter_item['slider_range_unit'];
				}
				if ( ! empty( $this->filter_item['title'] ) ) {
					$title = $this->filter_item['title'] . ': ';
				}

				$output .= '<div class="pwf-range-slider-labels">';
				$output .= '<span class="text-title">' . $title . '</span> ';
				$output .= '<span class="pwf-wrap-range-slider-label"><span id="pwf-from-' . absint( $this->filter_item_index ) . '" class="pwf-from">' . $current_min_value . '</span><span class="pwf-range-slider-unit pwf-hidden">' . esc_attr( $unit ) . '</span></span>';
				$output .= '<span class="price-delimiter"> &mdash; </span>';
				$output .= '<span class="pwf-wrap-range-slider-label"><span id="pwf-to-' . absint( $this->filter_item_index ) . '" class="pwf-to">' . $current_max_value . '</span><span class="pwf-range-slider-unit">' . esc_attr( $unit ) . '</span></span>';
				$output .= '</div>';
			}

			$output .= '</div>';
			$output  = $this->get_html_filter_item( $css_class, $output );

			return $output;
		}

		protected function render_button() {
			$css_class  = '';
			$css_class .= $this->get_custom_css_class();
			$output     = $this->get_html_filter_item_header( $css_class );

			if ( 'reset' === $this->filter_item['button_action'] ) {
				$css = ' pwf-reset-button';
			} else {
				$css = ' pwf-filter-button';
			}

			$output .= '<button class="pwf-item pwf-item-button' . esc_attr( $css ) . '"><span class="button-text">' . esc_attr( $this->filter_item['title'] ) . '</span></button>';
			$output .= $this->get_html_filter_item_footer();

			return $output;
		}

		protected function render_date() {
			$random_id = rand( 1, 1000 );
			$css_class = $this->get_custom_css_class();

			$date      = $this->filter_item['min_max_date'];
			$min_date  = $date['min_date'];
			$max_date  = $date['max_date'];
			$date_from = '';
			$date_to   = '';

			if ( ! empty( $this->selected_values ) ) {
				$date_from = ' data-date-from="' . $this->selected_values[0] . '"';
				$date_to   = ' data-date-to="' . $this->selected_values[1] . '"';
			}

			$interactive = $this->filter_item['interaction'] ?? '';
			if ( empty( $this->selected_values ) && 'on' === $interactive ) {
				if ( $date['active_min'] === $date['active_max'] ) {
					return;
				}
				$min_date = $date['active_min'];
				$max_date = $date['active_max'];
			}

			$output  = $this->get_html_filter_item_header( $css_class );
			$output .= $this->get_filter_item_title();
			$output .= $this->get_html_filter_item_container();

			$output .= '<div class="pwf-date-field" data-item-key="' . $this->get_filter_item_name() . '" data-min-date="' . esc_attr( $min_date ) . '" data-max-date="' . esc_attr( $max_date ) . '">';
			$output .= '<input type="text" id="pwf-date-from-' . $random_id . '" class="pwf-date-from" value="" placeholder="' . esc_attr__( 'From', 'pwf-woo-filter' ) . '"' . $date_from . '/>';
			$output .= '<input type="text" id="pwf-date-to-' . $random_id . '" class="pwf-date-to" value="" placeholder="' . esc_attr__( 'To', 'pwf-woo-filter' ) . '"' . $date_to . '/>';
			$output .= '</div>';

			$output .= $this->get_html_filter_item_container_end();
			$output .= $this->get_html_filter_item_footer();

			return $output;
		}

		/**
		 * @since 1.1.3
		 */
		protected function render_search() {
			$css_class    = $this->get_custom_css_class();
			$output       = '';
			$search_value = '';

			if ( ! empty( $this->selected_values ) ) {
				$search_value = implode( ' ', $this->selected_values );
			}

			$output  = $this->get_html_filter_item_header( $css_class );
			$output .= $this->get_filter_item_title();
			$output .= $this->get_html_filter_item_container();

			$output .= '<div class="pwf-search-field" data-item-key="' . $this->get_filter_item_name() . '">';
			$output .= '<span class="pwf-icon-css pwf-click-search-icon pwf-search-icon"></span>';
			$output .= '<input type="text" class="pwf-search-from pwf-input-text" value="' . esc_attr( $search_value ) . '" placeholder="' . esc_attr( $this->filter_item['place_holder'] ) . '"/>';
			$output .= '</div>';

			$output .= $this->get_html_filter_item_container_end();
			$output .= $this->get_html_filter_item_footer();

			return $output;
		}

		/**
		 * @since 1.2.2
		 */
		protected function render_rating() {
			$output    = '';
			$css_class = $this->get_custom_css_class();

			if ( 'on' === $this->filter_item['up_text'] ) {
				$css_class .= ' pwf-rating-radio-type rating-has-up-text';
			} else {
				$css_class .= ' pwf-rating-checkbox-type';
			}

			foreach ( $this->terms as $term ) {
				$css       = '';
				$visibilty = true;
				$up_text   = apply_filters( 'pwf_up_text', esc_html__( 'up', 'pwf-woo-filter' ) );

				if ( 'on' === $this->filter_item['up_text'] ) {
					$term['label'] = $term['label'] . ' ' . esc_html__( 'and', 'pwf-woo-filter' ) . ' ' . $up_text;
				}

				if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
					$visibilty = false;
				} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $term['count'] ) {
					$css .= ' pwf-disabled';
				}

				if ( in_array( $term['value'], $this->selected_values, true ) ) {
					$css .= ' checked';
				}

				if ( $visibilty ) {
					$output .= '<div data-slug="' . $term['slug'] . '" data-item-value="' . $term['value'] . '" class="pwf-item pwf-star-rating-item' . $css . '" title="' . esc_attr( $term['label'] ) . '">';

					$output .= '<div class="pwf-input-container"></div>';

					$output .= '<span class="pwf-star-rating star-rating">' . $this->get_star_rating_html( $term['rate'] ) . '</span>';
					if ( 'on' === $this->filter_item['up_text'] ) {
						$output .= '<span class="pwf-up-text">& ' . $up_text . '</span>';
					}
					if ( 'on' === $this->filter_item['display_product_counts'] && $term['count'] > 0 ) {
						$output .= self::get_html_template_item_count( $term['count'] );
					}
					$output .= '</div>';
				}
			}

			if ( '' !== $output ) {
				$output = $this->get_html_filter_item( $css_class, $output );
			}

			return $output;
		}

		/**
		 * @since 1.2.2
		 */
		protected function get_star_rating_html( $rating ) {
			$html = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%">';

			if ( 'on' !== $this->filter_item['up_text'] ) {
				$html .= esc_html__( 'Rated out of', 'pwf-woo-filter' ) . ' <strong>' . absint( $rating ) . '</strong>';
			} else {
				$html .= esc_html__( 'Rated', 'pwf-woo-filter' ) . ' <strong>' . absint( $rating ) . '</strong>';
				$html .= ' ' . esc_html__( 'and up', 'pwf-woo-filter' );
			}

			$html .= '</span>';

			return $html;
		}

		/**
		 * Check if is there one option or more has count great than 0
		 *
		 * Used with radio and dropown item
		 * fix issue if all options inside filter item has count 0
		 * And has show all text, and filter item option set to hide
		 *
		 * @param $var terms
		 * @since 1.0.6
		 * @return bool
		 */
		protected function should_display_filter_item( $terms ) {
			$terms   = json_decode( json_encode( $terms ), true );
			$display = true;
			if ( 'hide' === $this->filter_item['action_for_empty_options'] ) {
				if ( isset( $terms[0]['slug'] ) && 'showall' === $terms[0]['slug'] ) {
					unset( $terms[0] );
				}
				$display = false;
				foreach ( $terms as $term ) {
					if ( $term['count'] > 0 ) {
						$display = true;
						break;
					}
				}
			}

			return $display;
		}

		public static function get_html_template_item_count( $count ) {
			return '<span class="pwf-product-counts"><span class="pwf-wrap-count">' . absint( $count ) . '</span></span>';
		}
	}
}
