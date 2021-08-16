<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Admin_Setting' ) ) {

	class Pwf_Admin_Setting {

		public static function register() {
			$plugin = new self();
			add_action( 'init', array( $plugin, 'init' ) );
		}

		public function init() {
			add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ), 10, 1 );
			add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
		}

		public function add_section( $sections ) {
			$sections['pwfwoofilter'] = esc_html__( 'Filter', 'pwf-woo-filter' );
			return $sections;
		}

		public function add_settings( $settings, $current_section ) {
			if ( 'pwfwoofilter' === $current_section ) {
				$settings_filter   = array();
				$settings_filter[] = array(
					'name' => esc_html__( 'Select a filter to integrate with shop archive page', 'pwf-woo-filter' ),
					'type' => 'title',
					'id'   => 'pwf_title',
				);
				$settings_filter[] = array(
					'name'    => esc_html__( 'Filter ID', 'pwf-woo-filter' ),
					'id'      => 'pwf_shop_filter_id',
					'type'    => 'select',
					'options' => $this->get_filter_posts(),
				);
				$settings_filter[] = array(
					'name'              => esc_html__( 'Transient time', 'pwf-woo-filter' ),
					'desc'              => esc_html__( 'Set transient time in seconds.', 'pwf-woo-filter' ),
					'id'                => 'pwf_transient_time',
					'autoload'          => false,
					'default'           => '86400',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 60,
						'step' => 60,
					),
				);
				$settings_filter[] = array(
					'name'    => esc_html__( 'Theme Comitable', 'pwf-woo-filter' ),
					'id'      => 'pwf_shop_theme_compitable',
					'type'    => 'select',
					'options' => array(
						'enable'  => esc_html__( 'Enable', 'pwf-woo-filter' ),
						'disable' => esc_html__( 'Disable', 'pwf-woo-filter' ),
					),
				);
				$settings_filter[] = array(
					'name'        => esc_html__( 'Default loader', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Default HTML loader template uses by Ajax.', 'pwf-woo-filter' ),
					'id'          => 'pwf_woo_loader_default',
					'type'        => 'textarea',
				);
				$settings_filter[] = array(
					'name'        => esc_html__( 'Button loader', 'pwf-woo-filter' ),
					'description' => esc_html__( 'HTML loader template uses by load more button.', 'pwf-woo-filter' ),
					'id'          => 'pwf_woo_loader_load_more',
					'type'        => 'textarea',
				);
				$settings_filter[] = array(
					'name'        => esc_html__( 'Infinite Loader', 'pwf-woo-filter' ),
					'description' => esc_html__( 'HTML loader template uses by infinite scroll.', 'pwf-woo-filter' ),
					'id'          => 'pwf_woo_loader_infinite',
					'type'        => 'textarea',
				);
				$settings_filter[] = array(
					'type' => 'sectionend',
					'id'   => 'pwfwoofilter',
				);

				return $settings_filter;
			} else {
				return $settings;
			}
		}

		private function get_filter_posts() {
			$results = array();
			$filters = Pwf_Filter_Widget::get_filters();
			if ( is_array( $filters ) && '' === $filters[0]['id'] ) {
				$results[ $filters[0]['id'] ] = $filters[0]['title'];
			} else {
				$results[] = esc_html__( 'None', 'pwf-woo-filter' );
				foreach ( $filters as $filter ) {
					$results[ absint( $filter['id'] ) ] = esc_html( $filter['title'] );
				}
			}

			return $results;
		}
	}

	Pwf_Admin_Setting::register();
}
