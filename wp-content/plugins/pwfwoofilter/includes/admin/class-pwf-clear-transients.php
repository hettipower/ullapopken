<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Clear_Transients' ) ) {

	class Pwf_Clear_Transients {

		function __construct() {
			add_filter( 'woocommerce_debug_tools', array( $this, 'transient_woo_debug_button' ) );
		}

		/**
		 * debug_button function.
		 *
		 * @access public
		 * @param mixed $old
		 * @return void
		 */
		public function transient_woo_debug_button( $old ) {
			$new = array(
				'pwf_delete_terms_count_transients' => array(
					'name'     => 'PWF: ' . esc_html__( 'WooCommerce filter transients', 'pwf-woo-filter' ),
					'button'   => esc_html__( 'Clear transients', 'pwf-woo-filter' ),
					'desc'     => esc_html__( 'This tool will clear the plugin professional Woocommerce filter term counts transients cache.', 'pwf-woo-filter' ),
					'callback' => array( $this, 'delete_terms_cache' ),
				),
			);

			$tools = array_merge( $old, $new );

			return $tools;
		}

		public function delete_terms_cache() {
			self::delete_transients();
			$this->update_message();
		}

		/**
		 * debug_button_action function.
		 *
		 * @access public
		 * @return void
		 */
		public static function delete_transients() {
			// do what you want here
			global $wpdb;

			$str_search = '%' . $wpdb->esc_like( '_transient_timeout_pwf_woo_filter_' ) . '%';
			$transients = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
					$str_search
				)
			);

			foreach ( $transients as $transient ) {
				$key = str_replace( '_transient_timeout_', '', $transient );
				delete_transient( $key );
			}

			// But guess what?  Sometimes transients are not in the DB, so we have to do this too:
			wp_cache_flush();
		}

		protected function update_message() {
			echo '<div class="updated"><p>' . esc_html__( 'Cached terms count cleared', 'pwf-woo-filter' ) . '</p></div>';
		}
	}

	$GLOBALS['WC_Tools_Custom_Button'] = new Pwf_Clear_Transients();
}
