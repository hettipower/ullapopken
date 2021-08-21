<?php
	/**
	 * Plugin Name: Additional Variation Images Gallery for WooCommerce
	 * Plugin URI: https://wordpress.org/plugins/woo-variation-gallery/
	 * Description: Allows to insert multiple images for per variation to let visitors to see a different images when WooCommerce product variations are switched. Requires WooCommerce 3.2+
	 * Author: Emran Ahmed
	 * Version: 1.2.6
	 * Domain Path: /languages
	 * Requires at least: 4.8
	 * WC requires at least: 4.5
	 * Tested up to: 5.7
	 * WC tested up to: 5.2
	 * Text Domain: woo-variation-gallery
	 * Author URI: https://getwooplugins.com/
	 */
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	if ( ! class_exists( 'Woo_Variation_Gallery' ) ):
		
		final class Woo_Variation_Gallery {
			
			protected        $_version  = '1.2.6';
			protected static $_instance = null;
			
			public static function instance() {
				if ( is_null( self::$_instance ) ) {
					self::$_instance = new self();
				}
				
				return self::$_instance;
			}
			
			public function __construct() {
				$this->constants();
				$this->language();
				$this->includes();
				$this->hooks();
				do_action( 'woo_variation_gallery_loaded', $this );
			}
			
			public function define( $name, $value, $case_insensitive = false ) {
				if ( ! defined( $name ) ) {
					define( $name, $value, $case_insensitive );
				}
			}
			
			public function constants() {
				$this->define( 'WOO_VG_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
				$this->define( 'WOO_VG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
				$this->define( 'WOO_VG_VERSION', $this->version() );
				$this->define( 'WOO_VG_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
				$this->define( 'WOO_VG_PLUGIN_TEMPLATE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'templates' ) );
				$this->define( 'WOO_VG_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
				$this->define( 'WOO_VG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
				$this->define( 'WOO_VG_PLUGIN_FILE', __FILE__ );
				$this->define( 'WOO_VG_IMAGES_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'images' ) );
				$this->define( 'WOO_VG_ASSETS_URI', trailingslashit( plugin_dir_url( __FILE__ ) . 'assets' ) );
			}
			
			public function includes() {
				
				if ( $this->is_required_php_version() && $this->is_wc_active() ) {
					require_once $this->include_path( 'settings.php' );
					require_once $this->include_path( 'functions.php' );
					require_once $this->include_path( 'migrator.php' );
					require_once $this->include_path( 'hooks.php' );
					require_once $this->include_path( 'theme-supports.php' );
					require_once $this->include_path( 'rest-api-response.php' );
					if ( is_admin() ) {
						require_once $this->include_path( 'class-woo-variation-gallery-migrate.php' );
						require_once $this->include_path( 'class-woo-variation-gallery-export-import.php' );
					}
				}
			}
			
			public function is_pro_active() {
				return class_exists( 'Woo_Variation_Gallery_Pro' );
			}
			
			public function get_options() {
				return array();
			}
			
			private function deactivate_feedback_reasons() {
				
				$current_user = wp_get_current_user();
				
				return array(
					'temporary_deactivation' => array(
						'title'             => esc_html__( 'It\'s a temporary deactivation.', 'woo-variation-gallery' ),
						'input_placeholder' => '',
					),
					
					'dont_know_about' => array(
						'title'             => esc_html__( 'I couldn\'t understand how to make it work.', 'woo-variation-gallery' ),
						'input_placeholder' => '',
						'alert'             => __( 'It converts single variation image to multiple variation image gallery. <br><a target="_blank" href="http://bit.ly/demo-dea-dilogue">Please check live demo</a>.', 'woo-variation-gallery' ),
					),
					
					'gallery_too_small' => array(
						'title'             => __( 'My Gallery looks <strong>too small</strong>.', 'woo-variation-gallery' ),
						'input_placeholder' => '',
						'alert'             => __( '<a target="_blank" href="http://bit.ly/video-tuts-for-deactivate-dialogue">Please check this video to configure it.</a>.', 'woo-variation-gallery' ),
					),
					
					'no_longer_needed' => array(
						'title'             => esc_html__( 'I no longer need the plugin', 'woo-variation-gallery' ),
						'input_placeholder' => '',
					),
					
					'found_a_better_plugin' => array(
						'title'             => esc_html__( 'I found a better plugin', 'woo-variation-gallery' ),
						'input_placeholder' => esc_html__( 'Please share which plugin', 'woo-variation-gallery' ),
					),
					
					'broke_site_layout' => array(
						'title'             => __( 'The plugin <strong>broke my layout</strong> or some functionality.', 'woo-variation-gallery' ),
						'input_placeholder' => '',
						'alert'             => __( '<a target="_blank" href="https://getwooplugins.com/tickets/">Please open a support ticket</a>, we will fix it immediately.', 'woo-variation-gallery' ),
					),
					
					'plugin_setup_help' => array(
						'title'             => __( 'I need someone to <strong>setup this plugin.</strong>', 'woo-variation-gallery' ),
						'input_placeholder' => esc_html__( 'Your email address.', 'woo-variation-gallery' ),
						'input_value'       => sanitize_email( $current_user->user_email ),
						'alert'             => __( 'Please provide your email address to contact with you <br>and help you to setup and configure this plugin.', 'woo-variation-gallery' ),
					),
					
					'plugin_config_too_complicated' => array(
						'title'             => __( 'The plugin is <strong>too complicated to configure.</strong>', 'woo-variation-gallery' ),
						'input_placeholder' => '',
						'alert'             => __( '<a target="_blank" href="https://getwooplugins.com/documentation/woocommerce-variation-gallery/">Have you checked our documentation?</a>.', 'woo-variation-gallery' ),
					),
					
					'need_specific_feature' => array(
						'title'             => esc_html__( 'I need specific feature that you don\'t support.', 'woo-variation-gallery' ),
						'input_placeholder' => esc_html__( 'Please share with us.', 'woo-variation-gallery' ),
						//'alert'             => __( '<a target="_blank" href="https://getwooplugins.com/tickets/">Please open a ticket</a>, we will try to fix it immediately.', 'woo-variation-gallery' ),
					),
					
					'other' => array(
						'title'             => esc_html__( 'Other', 'woo-variation-gallery' ),
						'input_placeholder' => esc_html__( 'Please share the reason', 'woo-variation-gallery' ),
					)
				);
			}
			
			public function deactivate_feedback_dialog() {
				
				if ( in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ), true ) ) {
					
					$deactivate_reasons = $this->deactivate_feedback_reasons();
					$slug               = 'woo-variation-gallery';
					$version            = $this->version();
					
					include_once $this->include_path( 'deactive-feedback-dialog.php' );
				}
			}
			
			public function deactivate_feedback() {
				
				$api_url = 'https://stats.storepress.com/wp-json/storepress/deactivation';
				
				$deactivate_reasons = $this->deactivate_feedback_reasons();
				
				$plugin         = sanitize_title( $_POST[ 'plugin' ] );
				$reason_id      = sanitize_title( $_POST[ 'reason_type' ] );
				$reason_title   = $deactivate_reasons[ $reason_id ][ 'title' ];
				$reason_text    = ( isset( $_POST[ 'reason_text' ] ) ? sanitize_text_field( $_POST[ 'reason_text' ] ) : '' );
				$plugin_version = sanitize_text_field( $_POST[ 'version' ] );
				
				if ( 'temporary_deactivation' === $reason_id ) {
					wp_send_json_success( true );
					
					return;
				}
				
				$theme = array(
					'is_child_theme'   => is_child_theme(),
					'parent_theme'     => $this->get_parent_theme_name(),
					'theme_name'       => $this->get_theme_name(),
					'theme_version'    => $this->get_theme_version(),
					'theme_uri'        => esc_url( wp_get_theme( get_template() )->get( 'ThemeURI' ) ),
					'theme_author'     => esc_html( wp_get_theme( get_template() )->get( 'Author' ) ),
					'theme_author_uri' => esc_url( wp_get_theme( get_template() )->get( 'AuthorURI' ) ),
				);
				
				$database_version = wc_get_server_database_version();
				$active_plugins   = (array) get_option( 'active_plugins', array() );
				$plugins          = array();
				
				if ( is_multisite() ) {
					$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
					$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
				}
				
				foreach ( $active_plugins as $active_plugin ) {
					
					if ( $active_plugin === 'woo-variation-gallery/woo-variation-gallery.php' ) {
						continue;
					}
					
					$plugins[ $active_plugin ] = get_plugin_data( WP_PLUGIN_DIR . '/' . $active_plugin, false, false );
				}
				
				$environment = array(
					'is_multisite'         => is_multisite(),
					'site_url'             => esc_url( get_option( 'siteurl' ) ),
					'home_url'             => esc_url( get_option( 'home' ) ),
					'php_version'          => phpversion(),
					'mysql_version'        => $database_version[ 'number' ],
					'mysql_version_string' => $database_version[ 'string' ],
					'wc_version'           => WC()->version,
					'wp_version'           => get_bloginfo( 'version' ),
					'server_info'          => isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) ? wc_clean( wp_unslash( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) : '',
				);
				
				$request_body = array(
					'plugin'       => $plugin,
					'version'      => $plugin_version,
					'reason_id'    => $reason_id,
					'reason_title' => $reason_title,
					'reason_text'  => $reason_text,
					'settings'     => $this->get_options(),
					'theme'        => $theme,
					'plugins'      => $plugins,
					'environment'  => $environment
				);
				
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					$logger  = wc_get_logger();
					$context = array( 'source' => 'woo-variation-gallery' );
					$logger->info( sprintf( 'Deactivate log: %s', print_r( $request_body, true ) ), $context );
				}
				
				$response = wp_remote_post( $api_url, $args = array(
					'sslverify' => false,
					'timeout'   => 60,
					'body'      => $request_body
				) );
				
				if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
					wp_send_json_success( wp_remote_retrieve_body( $response ) );
				} else {
					wp_send_json_error( wp_remote_retrieve_response_message( $response ) );
				}
			}
			
			public function get_pro_link( $medium = 'go-pro' ) {
				
				$affiliate_id = apply_filters( 'gwp_affiliate_id', 0 );
				
				$link_args = array();
				
				if ( ! empty( $affiliate_id ) ) {
					$link_args[ 'ref' ] = esc_html( $affiliate_id );
				}
				
				$link_args = apply_filters( 'wvg_get_pro_link_args', $link_args );
				
				return add_query_arg( $link_args, 'https://getwooplugins.com/plugins/woocommerce-variation-gallery/' );
			}
			
			public function include_path( $file = '' ) {
				$file = ltrim( $file, '/' );
				
				return WOO_VG_PLUGIN_INCLUDE_PATH . $file;
			}
			
			public function template_path( $file = '' ) {
				$file = ltrim( $file, '/' );
				
				return WOO_VG_PLUGIN_TEMPLATE_PATH . $file;
			}
			
			public function enqueue_scripts() {
				
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
				// Disable gallery on scripts
				if ( apply_filters( 'disable_wvg_enqueue_scripts', false ) ) {
					return false;
				}
				
				if ( apply_filters( 'disable_woo_variation_gallery', false ) ) {
					return false;
				}
				
				
				$single_image_width     = absint( wc_get_theme_support( 'single_image_width', get_option( 'woocommerce_single_image_width', 600 ) ) );
				$gallery_thumbnails_gap = absint( get_option( 'woo_variation_gallery_thumbnails_gap', apply_filters( 'woo_variation_gallery_default_thumbnails_gap', 0 ) ) );
				$gallery_width          = absint( get_option( 'woo_variation_gallery_width', apply_filters( 'woo_variation_gallery_default_width', 30 ) ) );
				$gallery_margin         = absint( get_option( 'woo_variation_gallery_margin', apply_filters( 'woo_variation_gallery_default_margin', 30 ) ) );
				
				$gallery_medium_device_width      = absint( get_option( 'woo_variation_gallery_medium_device_width', apply_filters( 'woo_variation_gallery_medium_device_width', 0 ) ) );
				$gallery_small_device_width       = absint( get_option( 'woo_variation_gallery_small_device_width', apply_filters( 'woo_variation_gallery_small_device_width', 720 ) ) );
				$gallery_extra_small_device_width = absint( get_option( 'woo_variation_gallery_extra_small_device_width', apply_filters( 'woo_variation_gallery_extra_small_device_width', 320 ) ) );
				$thumbnail_position               = get_option( 'woo_variation_gallery_thumbnail_position', 'bottom' );
				
				if ( wvg_is_ie11() ) {
					wp_enqueue_script( 'bluebird', $this->assets_uri( "/js/bluebird{$suffix}.js" ), array(), '3.5.3' );
				}
				
				wp_enqueue_script( 'woo-variation-gallery-slider', esc_url( $this->assets_uri( "/js/slick{$suffix}.js" ) ), array( 'jquery' ), '1.8.1', true );
				
				wp_enqueue_style( 'woo-variation-gallery-slider', esc_url( $this->assets_uri( "/css/slick{$suffix}.css" ) ), array(), '1.8.1' );
				
				wp_enqueue_script( 'woo-variation-gallery', esc_url( $this->assets_uri( "/js/frontend{$suffix}.js" ) ), array( 'jquery', 'wp-util', 'woo-variation-gallery-slider', 'imagesloaded', 'wc-add-to-cart-variation' ), $this->version(), true );
				
				wp_localize_script( 'woo-variation-gallery', 'woo_variation_gallery_options', apply_filters( 'woo_variation_gallery_js_options', array(
					'gallery_reset_on_variation_change' => wc_string_to_bool( get_option( 'woo_variation_gallery_reset_on_variation_change', 'no' ) ),
					'enable_gallery_zoom'               => wc_string_to_bool( get_option( 'woo_variation_gallery_zoom', 'yes' ) ),
					'enable_gallery_lightbox'           => wc_string_to_bool( get_option( 'woo_variation_gallery_lightbox', 'yes' ) ),
					'enable_gallery_preload'            => wc_string_to_bool( get_option( 'woo_variation_gallery_image_preload', 'yes' ) ),
					'preloader_disable'                 => wc_string_to_bool( get_option( 'woo_variation_gallery_preloader_disable', 'no' ) ),
					'enable_thumbnail_slide'            => wc_string_to_bool( get_option( 'woo_variation_gallery_thumbnail_slide', 'yes' ) ),
					'gallery_thumbnails_columns'        => absint( get_option( 'woo_variation_gallery_thumbnails_columns', apply_filters( 'woo_variation_gallery_default_thumbnails_columns', 4 ) ) ),
					'is_vertical'                       => in_array( $thumbnail_position, array( 'left', 'right' ) ),
					'thumbnail_position'                => trim( $thumbnail_position ),
					'thumbnail_position_class_prefix'   => 'woo-variation-gallery-thumbnail-position-',
					// 'wrapper'                           => sanitize_text_field( get_option( 'woo_variation_gallery_and_variation_wrapper', apply_filters( 'woo_variation_gallery_and_variation_default_wrapper', '.product' ) ) ),
					'is_mobile'                         => wp_is_mobile(),
					'gallery_default_device_width'      => $gallery_width,
					'gallery_medium_device_width'       => $gallery_medium_device_width,
					'gallery_small_device_width'        => $gallery_small_device_width,
					'gallery_extra_small_device_width'  => $gallery_extra_small_device_width,
				
				) ) );
				
				// Stylesheet
				wp_enqueue_style( 'woo-variation-gallery', esc_url( $this->assets_uri( "/css/frontend{$suffix}.css" ) ), array( 'dashicons' ), $this->version() );
				
				wp_enqueue_style( 'woo-variation-gallery-theme-support', esc_url( $this->assets_uri( "/css/theme-support{$suffix}.css" ) ), array( 'woo-variation-gallery' ), $this->version() );
				
				$this->add_inline_style();
				
				do_action( 'woo_variation_gallery_enqueue_scripts', $this );
				
				$this->dokan_support_admin_scripts();
			}
			
			public function dokan_support_admin_scripts() {
				if ( current_user_can( 'dokan_edit_product' ) && function_exists( 'dokan_is_product_edit_page' ) && dokan_is_product_edit_page() ) {
					
					$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
					
					wp_enqueue_style( 'woo-variation-gallery-admin', esc_url( $this->assets_uri( "/css/admin{$suffix}.css" ) ), array(), $this->version() );
					
					if ( ! $this->is_pro_active() ) {
						wp_enqueue_script( 'woo-variation-gallery-admin', esc_url( $this->assets_uri( "/js/admin{$suffix}.js" ) ), array( 'jquery', 'jquery-ui-sortable', 'wp-util' ), $this->version(), true );
						wp_localize_script( 'woo-variation-gallery-admin', 'woo_variation_gallery_admin', array(
							'choose_image' => esc_html__( 'Choose Image', 'woo-variation-gallery' ),
							'add_image'    => esc_html__( 'Add Images', 'woo-variation-gallery' )
						) );
					}
				}
			}
			
			public function add_inline_style() {
				
				if ( apply_filters( 'disable_wvg_inline_style', false ) ) {
					return false;
				}
				
				$single_image_width     = absint( wc_get_theme_support( 'single_image_width', get_option( 'woocommerce_single_image_width', 600 ) ) );
				$gallery_thumbnails_gap = absint( get_option( 'woo_variation_gallery_thumbnails_gap', apply_filters( 'woo_variation_gallery_default_thumbnails_gap', 0 ) ) );
				$gallery_width          = absint( get_option( 'woo_variation_gallery_width', apply_filters( 'woo_variation_gallery_default_width', 30 ) ) );
				$gallery_margin         = absint( get_option( 'woo_variation_gallery_margin', apply_filters( 'woo_variation_gallery_default_margin', 30 ) ) );
				
				$gallery_medium_device_width      = absint( get_option( 'woo_variation_gallery_medium_device_width', apply_filters( 'woo_variation_gallery_medium_device_width', 0 ) ) );
				$gallery_small_device_width       = absint( get_option( 'woo_variation_gallery_small_device_width', apply_filters( 'woo_variation_gallery_small_device_width', 720 ) ) );
				$gallery_small_device_clear_float = wc_string_to_bool( get_option( 'woo_variation_gallery_small_device_clear_float', apply_filters( 'woo_variation_gallery_small_device_clear_float', 'no' ) ) );
				
				
				$gallery_extra_small_device_width       = absint( get_option( 'woo_variation_gallery_extra_small_device_width', apply_filters( 'woo_variation_gallery_extra_small_device_width', 320 ) ) );
				$gallery_extra_small_device_clear_float = wc_string_to_bool( get_option( 'woo_variation_gallery_extra_small_device_clear_float', apply_filters( 'woo_variation_gallery_extra_small_device_clear_float', 'no' ) ) );
				
				
				ob_start();
				include_once $this->include_path( 'stylesheet.php' );
				$css = ob_get_clean();
				$css = $this->clean_css( $css );
				
				$css = apply_filters( 'woo_variation_gallery_inline_style', $css );
				
				wp_add_inline_style( 'woo-variation-gallery', $css );
			}
			
			public function clean_css( $inline_css ) {
				
				$inline_css = str_ireplace( array( '<style type="text/css">', '</style>', "\r\n", "\r", "\n", "\t" ), '', $inline_css );
				// Normalize whitespace
				$inline_css = preg_replace( "/\s+/", ' ', $inline_css );
				
				return trim( $inline_css );
			}
			
			public function admin_enqueue_scripts() {
				
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
				if ( ! apply_filters( 'stop_gwp_live_feed', false ) ) {
					wp_enqueue_style( 'gwp-feed', esc_url( $this->feed_css_uri() ), array( 'dashicons' ) );
				}
				
				
				/*
				$screen    = get_current_screen();
				$screen_id = $screen ? $screen->id : '';
				
				if ( in_array( $screen_id, array( 'product', 'edit-product' ) ) ) {
					wp_deregister_script( 'wc-admin-variation-meta-boxes' );
					wp_register_script( 'wc-admin-variation-meta-boxes', $this->assets_uri( '/js/meta-boxes-product-variation' . $suffix . '.js' ), array( 'wc-admin-meta-boxes', 'serializejson', 'media-models' ), $this->version() );
				}*/
				
				// GWP Admin Helper
				wp_enqueue_script( 'gwp-backbone-modal', $this->assets_uri( "/js/gwp-backbone-modal{$suffix}.js" ), array( 'jquery', 'underscore', 'backbone', 'wp-util' ), $this->version(), true );
				wp_enqueue_script( 'gwp-admin', $this->assets_uri( "/js/gwp-admin{$suffix}.js" ), array( 'gwp-backbone-modal' ), $this->version(), true );
				
				wp_enqueue_style( 'gwp-admin', $this->assets_uri( "/css/gwp-admin{$suffix}.css" ), array( 'dashicons' ), $this->version() );
				
				wp_enqueue_style( 'woo-variation-gallery-admin', esc_url( $this->assets_uri( "/css/admin{$suffix}.css" ) ), array(), $this->version() );
				
				if ( ! $this->is_pro_active() ) {
					wp_enqueue_script( 'woo-variation-gallery-admin', esc_url( $this->assets_uri( "/js/admin{$suffix}.js" ) ), array( 'jquery', 'jquery-ui-sortable', 'wp-util' ), $this->version(), true );
					
					wp_localize_script( 'woo-variation-gallery-admin', 'woo_variation_gallery_admin', array(
						'choose_image' => esc_html__( 'Choose Image', 'woo-variation-gallery' ),
						'add_image'    => esc_html__( 'Add Images', 'woo-variation-gallery' )
					) );
				}
				do_action( 'woo_variation_gallery_admin_enqueue_scripts', $this );
			}
			
			public function admin_template_js() {
				ob_start();
				require_once $this->include_path( 'admin-template-js.php' );
				$data = ob_get_clean();
				echo apply_filters( 'woo_variation_gallery_admin_template_js', $data );
			}
			
			public function slider_template_js() {
				ob_start();
				require_once $this->include_path( 'slider-template-js.php' );
				$data = ob_get_clean();
				echo apply_filters( 'woo_variation_gallery_slider_template_js', $data );
			}
			
			public function thumbnail_template_js() {
				ob_start();
				require_once $this->include_path( 'thumbnail-template-js.php' );
				$data = ob_get_clean();
				echo apply_filters( 'woo_variation_gallery_thumbnail_template_js', $data );
			}
			
			public function hooks() {
				
				add_action( 'admin_notices', array( $this, 'php_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'wc_requirement_notice' ) );
				add_action( 'admin_notices', array( $this, 'wc_version_requirement_notice' ) );
				add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
				
				if ( $this->is_required_php_version() && $this->is_wc_active() ) {
					add_action( 'admin_footer', array( $this, 'deactivate_feedback_dialog' ) );
					add_action( 'admin_init', array( $this, 'after_plugin_active' ) );
					
					add_filter( 'body_class', array( $this, 'body_class' ) );
					add_action( 'admin_footer', array( $this, 'admin_template_js' ) );
					
					// Dokan Support
					add_action( 'wp_footer', function () {
						if ( current_user_can( 'dokan_edit_product' ) && function_exists( 'dokan_is_product_edit_page' ) && dokan_is_product_edit_page() ) {
							$this->admin_template_js();
						}
					} );
					
					add_action( 'wp_footer', array( $this, 'slider_template_js' ) );
					add_action( 'wp_footer', array( $this, 'thumbnail_template_js' ) );
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 25 );
					add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
					// add_action( 'admin_notices', array( $this, 'feed' ) );
					add_action( 'wp_ajax_gwp_live_feed_close', array( $this, 'feed_close' ) );
					add_filter( 'wp_ajax_gwp_deactivate_feedback', array( $this, 'deactivate_feedback' ) );
					
					add_filter( 'plugin_action_links_' . $this->basename(), array( $this, 'plugin_action_links' ) );
					
					do_action( 'woo_variation_gallery_hooks', $this );
				}
			}
			
			public function body_class( $classes ) {
				
				array_push( $classes, 'woo-variation-gallery' );
				array_push( $classes, sprintf( 'woo-variation-gallery-theme-%s', $this->get_parent_theme_dir() ) );
				array_push( $classes, sprintf( 'woo-variation-gallery-theme-child-%s', $this->get_theme_dir() ) );
				
				if ( is_rtl() ) {
					array_push( $classes, 'woo-variation-gallery-rtl' );
				}
				
				return array_unique( $classes );
			}
			
			public function plugin_action_links( $links ) {
				
				$new_links = array();
				
				$settings_link = esc_url( add_query_arg( array(
					                                         'page' => 'wc-settings',
					                                         'tab'  => 'woo-variation-gallery',
					                                         // 'section' => 'woo-variation-gallery'
				                                         ), admin_url( 'admin.php' ) ) );
				
				$new_links[ 'settings' ] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', $settings_link, esc_attr__( 'Settings', 'woo-variation-gallery' ) );
				
				
				$pro_link = esc_url( $this->get_pro_link() );
				
				
				if ( ! $this->is_pro_active() ):
					$new_links[ 'go-pro' ] = sprintf( '<a target="_blank" style="color: #45b450; font-weight: bold;" href="%1$s" title="%2$s">%2$s</a>', $pro_link, esc_attr__( 'Go Pro', 'woo-variation-gallery' ) );
				endif;
				
				
				return array_merge( $links, $new_links );
			}
			
			public function is_required_php_version() {
				return version_compare( PHP_VERSION, '5.6.0', '>=' );
			}
			
			public function is_required_wc_version() {
				return version_compare( WC_VERSION, '4.5', '>' );
			}
			
			public function wc_version_requirement_notice() {
				if ( $this->is_wc_active() && ! $this->is_required_wc_version() ) {
					$class   = 'notice notice-error';
					$message = sprintf( esc_html__( "Currently, you are using older version of WooCommerce. It's recommended to use latest version of WooCommerce to work with %s.", 'woo-variation-gallery' ), esc_html__( 'WooCommerce Variation Gallery', 'woo-variation-gallery' ) );
					printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message );
				}
			}
			
			public function php_requirement_notice() {
				if ( ! $this->is_required_php_version() ) {
					$class   = 'notice notice-error';
					$text    = esc_html__( 'Please check PHP version requirement.', 'woo-variation-gallery' );
					$link    = esc_url( 'https://docs.woocommerce.com/document/server-requirements/' );
					$message = wp_kses( __( "It's required to use latest version of PHP to use <strong>Additional Variation Images Gallery for WooCommerce</strong>.", 'woo-variation-gallery' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%4$s</a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function wc_requirement_notice() {
				
				if ( ! $this->is_wc_active() ) {
					
					$class = 'notice notice-error';
					
					$text    = esc_html__( 'WooCommerce', 'woo-variation-gallery' );
					$link    = esc_url( add_query_arg( array(
						                                   'tab'       => 'plugin-information',
						                                   'plugin'    => 'woocommerce',
						                                   'TB_iframe' => 'true',
						                                   'width'     => '640',
						                                   'height'    => '500',
					                                   ), admin_url( 'plugin-install.php' ) ) );
					$message = wp_kses( __( "<strong>Additional Variation Images Gallery for WooCommerce</strong> is an add-on of ", 'woo-variation-gallery' ), array( 'strong' => array() ) );
					
					printf( '<div class="%1$s"><p>%2$s <a class="thickbox open-plugin-details-modal" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
				}
			}
			
			public function language() {
				load_plugin_textdomain( 'woo-variation-gallery', false, trailingslashit( WOO_VG_PLUGIN_DIRNAME ) . 'languages' );
			}
			
			public function is_wc_active() {
				return class_exists( 'WooCommerce' );
			}
			
			public function basename() {
				return WOO_VG_PLUGIN_BASENAME;
			}
			
			public function dirname() {
				return WOO_VG_PLUGIN_DIRNAME;
			}
			
			public function version() {
				return esc_attr( $this->_version );
			}
			
			public function plugin_path() {
				return untrailingslashit( plugin_dir_path( __FILE__ ) );
			}
			
			public function plugin_uri() {
				return untrailingslashit( plugins_url( '/', __FILE__ ) );
			}
			
			public function images_uri( $file ) {
				$file = ltrim( $file, '/' );
				
				return WOO_VG_IMAGES_URI . $file;
			}
			
			public function assets_uri( $file ) {
				$file = ltrim( $file, '/' );
				
				return WOO_VG_ASSETS_URI . $file;
			}
			
			public function plugin_row_meta( $links, $file ) {
				if ( $file == $this->basename() ) {
					
					$report_url = 'https://getwooplugins.com/tickets/';
					
					$documentation_url = 'https://getwooplugins.com/documentation/woocommerce-variation-gallery/';
					
					$row_meta[ 'documentation' ] = sprintf( '<a target="_blank" href="%1$s" title="%2$s">%2$s</a>', esc_url( $documentation_url ), esc_html__( 'Read Documentation', 'woo-variation-gallery' ) );
					$row_meta[ 'issues' ]        = sprintf( '%2$s <a target="_blank" href="%1$s">%3$s</a>', esc_url( $report_url ), esc_html__( 'Facing issue?', 'woo-variation-gallery' ), '<span style="color: red">' . esc_html__( 'Please open a support ticket.', 'woo-variation-gallery' ) . '</span>' );
					
					return array_merge( $links, $row_meta );
				}
				
				return (array) $links;
			}
			
			public function get_theme_name() {
				return wp_get_theme()->get( 'Name' );
			}
			
			public function get_theme_version() {
				return wp_get_theme()->get( 'Version' );
			}
			
			public function get_parent_theme_dir() {
				return strtolower( basename( get_template_directory() ) );
			}
			
			public function get_parent_theme_name() {
				return wp_get_theme( get_template() )->get( 'Name' );
			}
			
			public function get_theme_dir() {
				return strtolower( basename( get_stylesheet_directory() ) );
			}
			
			public function after_plugin_active() {
				if ( get_option( 'activate-woo-variation-gallery' ) === 'yes' ) {
					delete_option( 'activate-woo-variation-gallery' );
					wp_safe_redirect( add_query_arg( array(
						                                 'page'    => 'wc-settings',
						                                 'tab'     => 'woo-variation-gallery',
						                                 'section' => 'tutorials'
					                                 ), admin_url( 'admin.php' ) ) );
				}
			}
			
			public static function plugin_activated() {
				update_option( 'activate-woo-variation-gallery', 'yes' );
				update_option( 'woocommerce_show_marketplace_suggestions', 'no' );
			}
			
			public static function plugin_deactivated() {
				delete_option( 'activate-woo-variation-gallery' );
			}
			
			// Feed API
			public function feed() {
				
				$feed_transient_id = "gwp_live_feed_wvg";
				
				$api_url = 'https://getwooplugins.com/wp-json/getwooplugins/v1/fetch-feed';
				
				// For Dev Mode
				if ( $feed_api_uri = apply_filters( 'gwp_feed_api_uri', false ) ) {
					$api_url = $feed_api_uri;
				}
				
				if ( apply_filters( 'stop_gwp_live_feed', false ) ) {
					return;
				}
				
				if ( isset( $_GET[ 'raw_gwp_live_feed' ] ) ) {
					delete_transient( $feed_transient_id );
				}
				
				if ( false === ( $body = get_transient( $feed_transient_id ) ) ) {
					$response = wp_remote_get( $api_url, $args = array(
						'timeout' => 60,
						'body'    => array(
							'item' => 'woo-variation-gallery',
						)
					) );
					
					if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 ) {
						$body = json_decode( wp_remote_retrieve_body( $response ), true );
						set_transient( $feed_transient_id, $body, 6 * HOUR_IN_SECONDS );
						
						if ( isset( $_GET[ 'raw_gwp_live_feed' ] ) && isset( $body[ 'id' ] ) ) {
							delete_transient( "gwp_live_feed_seen_{$body[ 'id' ]}" );
						}
					}
				}
				
				if ( isset( $body[ 'id' ] ) && false !== get_transient( "gwp_live_feed_seen_{$body[ 'id' ]}" ) ) {
					return;
				}
				
				if ( isset( $body[ 'version' ] ) && ! empty( $body[ 'version' ] ) && $body[ 'version' ] != $this->version() ) {
					return;
				}
				
				if ( isset( $body[ 'skip_pro' ] ) && ! empty( $body[ 'skip_pro' ] ) && $this->is_pro_active() ) {
					return;
				}
				
				if ( isset( $body[ 'only_pro' ] ) && ! empty( $body[ 'only_pro' ] ) && ! $this->is_pro_active() ) {
					return;
				}
				
				if ( isset( $body[ 'theme' ] ) && ! empty( $body[ 'theme' ] ) && $body[ 'theme' ] != $this->get_parent_theme_dir() ) {
					return;
				}
				
				// Skip If Some Plugin Activated
				if ( isset( $body[ 'skip_plugins' ] ) && ! empty( $body[ 'skip_plugins' ] ) ) {
					
					$active_plugins = (array) get_option( 'active_plugins', array() );
					
					if ( is_multisite() ) {
						$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
						$active_plugins            = array_unique( array_merge( $active_plugins, $network_activated_plugins ) );
					}
					
					$skip_plugins = (array) array_unique( explode( ',', trim( $body[ 'skip_plugins' ] ) ) );
					
					$intersected_plugins = array_intersect( $active_plugins, $skip_plugins );
					if ( is_array( $intersected_plugins ) && ! empty( $intersected_plugins ) ) {
						return;
					}
				}
				
				// Must Active Some Plugins
				if ( isset( $body[ 'only_plugins' ] ) && ! empty( $body[ 'only_plugins' ] ) ) {
					
					$active_plugins = (array) get_option( 'active_plugins', array() );
					
					if ( is_multisite() ) {
						$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
						$active_plugins            = array_unique( array_merge( $active_plugins, $network_activated_plugins ) );
					}
					
					$only_plugins = (array) array_unique( explode( ',', trim( $body[ 'only_plugins' ] ) ) );
					
					$intersected_plugins = array_intersect( $active_plugins, $only_plugins );
					
					if ( is_array( $intersected_plugins ) && empty( $intersected_plugins ) ) {
						return;
					}
				}
				
				if ( isset( $body[ 'message' ] ) && ! empty( $body[ 'message' ] ) ) {
					$user    = wp_get_current_user();
					$search  = array( '{pro_link}', '{user_login}', '{user_email}', '{user_firstname}', '{user_lastname}', '{display_name}', '{nickname}' );
					$replace = array(
						esc_url( $this->get_pro_link( 'product-feed' ) ),
						$user->user_login ? $user->user_login : 'there',
						$user->user_email,
						$user->user_firstname ? $user->user_firstname : 'there',
						$user->user_lastname ? $user->user_lastname : 'there',
						$user->display_name ? $user->display_name : 'there',
						$user->nickname ? $user->nickname : 'there',
					);
					
					$message = str_ireplace( $search, $replace, $body[ 'message' ] );
					
					echo wp_kses_post( $message );
				}
			}
			
			public function feed_css_uri() {
				
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
				// For Dev Mode
				if ( $feed_css_uri = apply_filters( 'gwp_feed_css_uri', false ) ) {
					return $feed_css_uri;
				}
				
				return $this->assets_uri( "/css/gwp-admin-notice{$suffix}.css" );
			}
			
			public function feed_close() {
				$id = absint( $_POST[ 'id' ] );
				set_transient( "gwp_live_feed_seen_{$id}", true, 1 * WEEK_IN_SECONDS );
			}
		}
		
		function woo_variation_gallery() {
			return Woo_Variation_Gallery::instance();
		}
		
		add_action( 'plugins_loaded', 'woo_variation_gallery', 20 );
		
		register_activation_hook( __FILE__, array( 'Woo_Variation_Gallery', 'plugin_activated' ) );
		register_deactivation_hook( __FILE__, array( 'Woo_Variation_Gallery', 'plugin_deactivated' ) );
	
	endif;