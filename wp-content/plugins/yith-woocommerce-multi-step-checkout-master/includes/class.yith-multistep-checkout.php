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
 * @class      YITH_Multistep_Checkout
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Multistep_Checkout' ) ) {
	/**
	 * Class YITH_Multistep_Checkout
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	class YITH_Multistep_Checkout {
        /**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WCMS_VERSION;

        /**
		 * Main Instance
		 *
		 * @var YITH_Multistep_Checkout
		 * @since 1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Multistep_Checkout_Admin
		 * @since 1.0
		 */
		public $admin = null;

        /**
		 * Main Frontpage Instance
		 *
		 * @var YITH_Multistep_Checkout_Frontend
		 * @since 1.0
		 */
		public $frontend = null;


        /**
         * Construct
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         */
        public function __construct(){

			/* === Require Main Files === */
			$require = apply_filters( 'yith_wcms_require_class',
				array(
					'common'    => array(
                        'includes/functions.yith-wcms.php'
                    ),
					'admin'     => array(
                        'includes/class.yith-multistep-checkout-admin.php'
                    ),
                    'frontend'  => array(
                        'includes/class.yith-multistep-checkout-frontend.php'
                    ),
				)
			);

			$this->_require( $require );

            /* === Load Plugin Framework === */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            /* == Plugins Init === */
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
		 * Main plugin Instance
		 *
		 * @return YITH_Multistep_Checkout Main instance
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

        /**
		 * Add the main classes file
		 *
		 * Include the admin and frontend classes
		 *
		 * @param $main_classes array The require classes file path
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 *
		 * @return void
		 * @access protected
		 */
		protected function _require( $main_classes ) {
			foreach ( $main_classes as $section => $classes ) {
				foreach ( $classes as $class ) {
					if ( 'common' == $section  || ( 'frontend' == $section && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' == $section && is_admin() ) && file_exists( YITH_WCMS_PATH . $class ) ) {
						require_once( YITH_WCMS_PATH . $class );
					}
				}
			}
		}

		/**
		 * Load plugin framework
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( !empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

        /**
		 * Class Initializzation
		 *
		 * Instance the admin or frontend classes
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @access protected
		 */
		public function init() {
            if ( is_admin() ) {
				$this->admin = new YITH_Multistep_Checkout_Admin();
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Multistep_Checkout_Frontend();
			}
		}
    }
}