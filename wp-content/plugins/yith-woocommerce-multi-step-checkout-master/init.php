<?php
/*
Plugin Name: YITH WooCommerce Multi-step Checkout
Plugin URI: http://yithemes.com/themes/plugins/yith-woocommerce-multi-step-checkout/
Description: YITH WooCommerce Multi-step checkout
Author: yithemes
Text Domain: yith_wcms
Version: 1.0.10
Author URI: http://yithemes.com/
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! function_exists( 'install_premium_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if woocommerce is deactivated
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function install_premium_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php _ex( 'YITH WooCommerce Multi-step Chekcout is enabled but not effective. It requires WooCommerce in order to work.', 'Alert Message: WooCommerce require', 'yith_wcms' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'WC' ) ) {
    add_action( 'admin_notices', 'install_premium_woocommerce_admin_notice' );
    return;
}

/* === DEFINE === */
! defined( 'YITH_WCMS_VERSION' )            && define( 'YITH_WCMS_VERSION', '1.0.10' );
! defined( 'YITH_WCMS_FREE_INIT' )          && define( 'YITH_WCMS_FREE_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCMS_SLUG' )               && define( 'YITH_WCMS_SLUG', 'yith-woocommerce-multi-step-checkout' );
! defined( 'YITH_WCMS_FILE' )               && define( 'YITH_WCMS_FILE', __FILE__ );
! defined( 'YITH_WCMS_PATH' )               && define( 'YITH_WCMS_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCMS_URL' )                && define( 'YITH_WCMS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCMS_ASSETS_URL' )         && define( 'YITH_WCMS_ASSETS_URL', YITH_WCMS_URL . 'assets/' );
! defined( 'YITH_WCMS_TEMPLATE_PATH' )      && define( 'YITH_WCMS_TEMPLATE_PATH', YITH_WCMS_PATH . 'templates/' );
! defined( 'YITH_WCMS_WC_TEMPLATE_PATH' )   && define( 'YITH_WCMS_WC_TEMPLATE_PATH', YITH_WCMS_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCMS_OPTIONS_PATH' )       && define( 'YITH_WCMS_OPTIONS_PATH', YITH_WCMS_PATH . 'panel' );

/* Plugin Framework Version Check */
! function_exists( 'yit_maybe_plugin_fw_loader' ) && require_once( YITH_WCMS_PATH . 'plugin-fw/init.php' );
yit_maybe_plugin_fw_loader( YITH_WCMS_PATH  );

/* Load YWCM text domain */
load_plugin_textdomain( 'yith_wcms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if ( ! function_exists( 'YITH_Multistep_Checkout' ) ) {
	/**
	 * Unique access to instance of YITH_Vendors class
	 *
	 * @return YITH_Multistep_Checkout|YITH_Multistep_Checkout_Premium
	 * @since 1.0.0
	 */
	function YITH_Multistep_Checkout() {
		// Load required classes and functions
		require_once( YITH_WCMS_PATH . 'includes/class.yith-multistep-checkout.php' );

		if ( defined( 'YITH_WCMS_PREMIUM' ) && file_exists( YITH_WCMS_PATH . 'includes/class.yith-multistep-checkout-premium.php' ) ) {
			require_once( YITH_WCMS_PATH . 'includes/class.yith-multistep-checkout-premium.php' );
			return YITH_Multistep_Checkout_Premium::instance();
		}

		return YITH_Multistep_Checkout::instance();
	}
}

/**
 * Instance main plugin class
 */
YITH_Multistep_Checkout();
