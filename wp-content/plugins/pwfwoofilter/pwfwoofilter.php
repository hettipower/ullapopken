<?php
/*------------------------------------------------------------------------------
Plugin Name: PWF WooCommerce Product Filters
Description: WooCommerce Products Filter. Flexible, easy and robust product filters for WooCommerce plugin!
Plugin URI:  https://codecanyon.net/item/pwf-woocommerce-product-filters/28181010
Version:     1.2.6
Author:      Mostafa
Author URI:  https://mostafaa.net/
License:     Codecanyon Split Licence
text domain: pwf-woo-filter
Domain Path: /languages
WC requires at least: 4.3.0
WC tested up to: 5.0.0
------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) || exit; // exit if file is called directly

const PWF_WOO_FILTER_VER = '1.2.6';
define( 'PWF_WOO_FILTER_URI', plugins_url( '', __FILE__ ) );
define( 'PWF_WOO_FILTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'PWF_WOO_FILTER_DIR_DOMAIN', dirname( plugin_basename( __FILE__ ) ) );

if ( pwf_is_woocommerce_active() ) {

	require_once( 'includes/class-pwf-autoloader.php' );
	require_once( 'includes/class-pwf-filter-post-type.php' );
	require_once( 'includes/widgets/class-pwf-filter-widget.php' );
	require_once( 'includes/class-pwf-render-filter.php' );
	require_once( 'includes/class-pwf-front-end-ajax.php' );
	require_once( 'includes/class-pwf-api.php' );
	if ( ! is_admin() ) {
		require_once( 'includes/class-pwf-woo-main-query.php' );
	}
	if ( is_admin() ) {
		require_once( 'includes/admin/class-pwf-meta.php' );
		require_once( 'includes/admin/class-pwf-clear-transients.php' );
		require_once( 'includes/admin/class-pwf-admin-setting.php' );
	}
}

/**
 * check if woocommerce is active
 * @return boolean
 */
function pwf_is_woocommerce_active() {
	// Makes sure the plugin is defined before trying to use it
	$is_woo_active = false;

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		$is_woo_active = true;
	}

	if ( is_multisite() && ! $is_woo_active ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
			$is_woo_active = true;
		}
	}

	if ( false === $is_woo_active ) {
		add_action( 'admin_notices', 'pwf_woocommerce_plugin_requires' );
	}

	return $is_woo_active;
}

function pwf_woocommerce_plugin_requires() {
	$error   = esc_html( 'plugin requires WooCommerce to run. Please, install and active WooCommerce plugin.', 'pwf-woo-filter' );
	$message = '<div class="notice notice-error pwf-admin-error"><p><strong>The PWF </strong>' . $error . '</p></div>';
	echo wp_kses_post( $message );
}
