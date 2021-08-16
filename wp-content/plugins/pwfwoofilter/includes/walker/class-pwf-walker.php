<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Walker' ) ) {

	class Pwf_Walker extends Walker_Nav_Menu {

		protected $filter_item;
		protected $is_hierarchy;
		protected $item_type;
		protected $name;
		protected $selected_values;
		protected $items_visibilty;

		var $db_fields = array(
			'parent' => 'parent',
			'id'     => 'term_id',
			'slug'   => 'slug',
		);

		public function start_walk( $filter_item, $terms, $is_hierarchy = false, $selected_values = array() ) {
			$this->items_visibilty = array();
			$this->filter_item     = $filter_item;
			$this->name            = $filter_item['url_key'];
			$this->item_type       = $filter_item['item_type'];
			$this->is_hierarchy    = $is_hierarchy;
			$this->selected_values = $selected_values;

			if ( $is_hierarchy ) {
				$depth = 0;
			} else {
				$depth = -1;
			}

			return $this->walk( (array) $terms, $depth, array() );
		}

		protected function get_item_css( $has_children ) {
			$css = 'pwf-item pwf-' . $this->item_type . '-item';
			if ( $this->is_hierarchy && $has_children ) {
				$css .= ' pwf-items-hierarchical pwf-item-hierarchical-collapsed';
				// Not all fields has display_hierarchical_collapsed option
				if ( isset( $this->filter_item['display_hierarchical_collapsed'] ) && 'on' === $this->filter_item['display_hierarchical_collapsed'] ) {
					$css .= ' pwf-collapsed-close';
				}
			}

			return $css;
		}

		protected function get_html_toggle( $has_children ) {
			$output = '';
			if ( $this->is_hierarchy && $has_children ) {
				if ( isset( $this->filter_item['display_hierarchical_collapsed'] ) && ( 'on' === $this->filter_item['display_hierarchical_collapsed'] ) ) {
					$output = '<span class="pwf-toggle"></span>';
				}
			}

			return $output;
		}
	}
}
