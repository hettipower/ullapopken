<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Walker_Dropdown_List' ) ) {

	class Pwf_Walker_Dropdown_List extends Pwf_Walker {

		// Displays start of a level. E.g '<ul>'
		function start_lvl( &$output, $depth = 0, $args = array() ) {
			return;
		}

		// Displays end of a level. E.g '</ul>'
		function end_lvl( &$output, $depth = 0, $args = array() ) {
			return;
		}

		// @see Walker::start_el()
		function start_el( &$output, $object, $depth = 0, $args = array(), $id = 0 ) {
			$visibilty    = true;
			$emptyspace   = '';
			$selected     = '';
			$disabled     = '';
			$multi_select = $this->filter_item['multi_select'] ?? '';

			if ( $this->is_hierarchy ) {
				$emptyspace = str_repeat( '&nbsp;', $depth * 2 ) . ' ';
			}

			if ( ! empty( $this->selected_values ) ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $object->term_id, $this->selected_values ) ) {
					$selected = ' selected';
				}
			} else {
				if ( 'showall' === $object->term_id && 'on' !== $multi_select ) {
					$selected = ' selected';
				}
			}

			if ( 'showall' !== $object->term_id ) {
				$slug = $object->slug;
				if ( 'hide' === $this->filter_item['action_for_empty_options'] && 1 > $object->count ) {
					$visibilty = false;
				} elseif ( 'markasdisable' === $this->filter_item['action_for_empty_options'] && 1 > $object->count ) {
					$disabled = ' disabled';
				}
			} else {
				$slug = 'showall';
			}


			if ( $visibilty ) {
				$output .= '<option data-slug="' . $slug . '" data-title="' . esc_attr( $object->name ) . '" value="' . $object->term_id . '"' . $selected . $disabled . '>' . $emptyspace . $object->name;
				if ( 'on' === $this->filter_item['display_product_counts'] && $object->count > 0 ) {
					$output .= ' - ' . $object->count;
				}
				$output .= '</option>';
			}
		}

		function end_el( &$output, $item, $depth = 0, $args = array() ) {

		}
	}
}
