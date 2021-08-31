<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multistep_Checkout_Frontend
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Andrea Grillo <andrea.grillo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Multistep_Checkout_Frontend' ) ) {
	/**
	 * Class YITH_Multistep_Checkout_Frontend
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	class YITH_Multistep_Checkout_Frontend {

        /**
         * Construct
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         */
        public function __construct(){

            if( 'no' == get_option( 'yith_wcms_enable_multistep', 'no' ) ){
                return false;
            }

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            /* === Change Checkout Template === */
            add_filter( 'woocommerce_locate_template', array( $this, 'multistep_checkout' ), 10, 3 );

            /* === Checkout Hack === */
            remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
            remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
            remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
            remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
            add_action( 'yith_woocommerce_checkout_coupon', 'woocommerce_checkout_coupon_form', 10 );
            add_action( 'yith_woocommerce_checkout_order_review', 'woocommerce_order_review', 20 );
            add_action( 'yith_woocommerce_checkout_payment', 'woocommerce_checkout_payment', 10 );
        }

        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Frontend
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         * @return void
         */
        public function enqueue_scripts(){
            /* === Style === */
            wp_enqueue_style( 'yith-wcms-checkout', YITH_WCMS_ASSETS_URL . 'css/frontend.css', array(), '1.0.0' );

            /* === Script === */
            $script = apply_filters( 'yith_wcms_main_script', 'multistep.js' );
            $script = function_exists( 'yit_load_js_file' ) ? yit_load_js_file( $script ) : str_replace( '.js', '.min.js', $script );

            wp_register_script( 'yith-wcms-step', YITH_WCMS_ASSETS_URL . 'js/' . $script, array( 'wc-checkout' ), '1.0.1', true );
            wp_enqueue_script( 'yith-wcms-step' );

            do_action( 'yith_wcms_enqueue_scripts' );
        }

        /**
         * Enable multistep checkout
         *
         * Check if you want to load classic checkout or multistep checkout
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         *
         * @param $template
         * @param $template_name
         * @param $template_path
         *
         * @return void
         */
        public function multistep_checkout( $template, $template_name, $template_path ){
            if( 'yes' == get_option( 'yith_wcms_enable_multistep', 'no' ) && 'checkout/form-checkout.php' == $template_name ){
                $template = YITH_WCMS_WC_TEMPLATE_PATH . 'checkout/form-checkout.php';
            }

            return $template;
        }
    }
}