<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	/**
	 * Settings for API.
	 */
	if ( class_exists( 'Woo_Variation_Gallery_Settings' ) ) {
		return new Woo_Variation_Gallery_Settings();
	}
	
	class Woo_Variation_Gallery_Settings extends WC_Settings_Page {
	 
		public function __construct() {
			$this->id    = 'woo-variation-gallery';
			$this->label = esc_html__( 'Additional Variation Images Gallery', 'woo-variation-gallery' );
			parent::__construct();
		}
		
		public function get_sections() {
			
			$sections = array(
				''          => esc_html__( 'General', 'woo-variation-gallery' ),
				'configure' => esc_html__( 'Configuration', 'woo-variation-gallery' ),
				'advanced'  => esc_html__( 'Advanced', 'woo-variation-gallery' ),
				'migration'  => esc_html__( 'Migration', 'woo-variation-gallery' ),
				'tutorials' => esc_html__( 'Tutorials', 'woo-variation-gallery' )
			);
			
			return apply_filters( 'woocommerce_get_sections_woo-variation-gallery', $sections );
		}
		
		public function output() {
			
			global $current_section, $hide_save_button;
			
            switch ($current_section){
                case 'tutorials':
                $hide_save_button = true;
                $this->tutorial_section($current_section);
                break;
                
                case 'migration':
                $hide_save_button = true;
                $this->migration_section($current_section);
                break;
                
                default:
                $settings = $this->get_settings( $current_section );
                // WC_Admin_Settings::output_fields( $settings );
                $this->output_fields( $settings );
                break;
            }
		}

		public function tutorial_section($current_section) {
			ob_start();
			$settings = $this->get_settings( $current_section );
			include_once 'html-tutorials.php';
			echo ob_get_clean();
		}
		
		public function migration_section($current_section) {
			ob_start();
			$settings = $this->get_settings( $current_section );
			include_once 'html-migrations.php';
			echo ob_get_clean();
		}
		
		public function get_settings( $current_section = '' ) {
			
			$settings = array();
			
			switch ( $current_section ) {
			
			case 'configure':
				$settings = apply_filters( 'woo_variation_gallery_configure_settings', array(
					
					array(
						'name' => esc_html__( 'Gallery Configure', 'woo-variation-gallery-pro' ),
						'type' => 'title',
						'desc' => '',
						'id'   => 'woo_variation_gallery_configure_settings',
					),
					
					array(
						'title'   => esc_html__( 'Gallery Auto play', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'no',
						'desc'    => esc_html__( 'Gallery Auto Slide / Auto Play', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_slider_autoplay'
					),
					
					array(
						'title'             => esc_html__( 'Gallery Auto Play Speed', 'woo-variation-gallery-pro' ),
						'type'              => 'number',
						'default'           => 5000,
						'css'               => 'width:70px;',
						'desc'              => esc_html__( 'Slider gallery autoplay speed. Default is 3000 means 3 seconds', 'woo-variation-gallery-pro' ),
						'id'                => 'woo_variation_gallery_slider_autoplay_speed',
						'custom_attributes' => array(
							'min'  => 500,
							'max'  => 10000,
							'step' => 500,
						),
					),
					
					array(
						'title'             => esc_html__( 'Gallery Slide / Fade Speed', 'woo-variation-gallery-pro' ),
						'type'              => 'number',
						'default'           => 300,
						'css'               => 'width:60px;',
						'desc'              => esc_html__( 'Gallery sliding speed. Default is 300 means 300 milliseconds', 'woo-variation-gallery-pro' ),
						'id'                => 'woo_variation_gallery_slide_speed',
						'custom_attributes' => array(
							'min'  => 100,
							'max'  => 1000,
							'step' => 100,
						),
					),
					
					array(
						'title'   => esc_html__( 'Fade Slide', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'no',
						'desc'    => esc_html__( 'Gallery will change by fade not slide', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_slider_fade'
					),
					
					array(
						'title'   => esc_html__( 'Show Slider Arrow', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'yes',
						'desc'    => esc_html__( 'Show Gallery Slider Arrow', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_slider_arrow'
					),
					
					array(
						'title'   => esc_html__( 'Enable Image Zoom', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'yes',
						'desc'    => esc_html__( 'Enable Gallery Image Zoom', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_zoom'
					),
					
					array(
						'title'   => esc_html__( 'Enable Image Popup', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'yes',
						'desc'    => esc_html__( 'Enable Gallery Image Popup', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_lightbox'
					),
					
					array(
						'title'   => esc_html__( 'Enable Thumbnail Slide', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'yes',
						'desc'    => esc_html__( 'Enable Gallery Thumbnail Slide', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_thumbnail_slide'
					),
					
					array(
						'title'   => esc_html__( 'Show Thumbnail Arrow', 'woo-variation-gallery-pro' ),
						'type'    => 'checkbox',
						'default' => 'yes',
						'desc'    => esc_html__( 'Show Gallery Thumbnail Arrow', 'woo-variation-gallery-pro' ),
						'id'      => 'woo_variation_gallery_thumbnail_arrow'
					),
					
					array(
						'title'    => esc_html__( 'Zoom Icon Display Position', 'woo-variation-gallery-pro' ),
						'id'       => 'woo_variation_gallery_zoom_position',
						'default'  => 'top-right',
						//'type'     => 'radio',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'desc_tip' => esc_html__( 'Product Gallery Zoom Icon Display Position', 'woo-variation-gallery-pro' ),
						'options'  => array(
							'top-right'    => esc_html__( 'Top Right', 'woo-variation-gallery-pro' ),
							'top-left'     => esc_html__( 'Top Left', 'woo-variation-gallery-pro' ),
							'bottom-right' => esc_html__( 'Bottom Right', 'woo-variation-gallery-pro' ),
							'bottom-left'  => esc_html__( 'Bottom Left', 'woo-variation-gallery-pro' ),
						),
					),
					
					array(
						'title'    => esc_html__( 'Thumbnail Display Position', 'woo-variation-gallery-pro' ),
						'id'       => 'woo_variation_gallery_thumbnail_position',
						'default'  => 'bottom',
						//'type'     => 'radio',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'desc_tip' => esc_html__( 'Product Gallery Thumbnail Display Position', 'woo-variation-gallery-pro' ),
						'options'  => array(
							'left'   => esc_html__( 'Left', 'woo-variation-gallery-pro' ),
							'right'  => esc_html__( 'Right', 'woo-variation-gallery-pro' ),
							'bottom' => esc_html__( 'Bottom', 'woo-variation-gallery-pro' ),
						),
					),
					
					
					array(
						'type' => 'sectionend',
						'id'   => 'woo_variation_gallery_configure_settings'
					),
				
				) );
				break;
				
			case 'advanced':
				$settings = apply_filters( 'woo_variation_gallery_advanced_settings', array(
						
						array(
							'name'  => __( 'Advanced Options', 'woo-variation-gallery' ),
							'type'  => 'title',
							'desc'  => '',
							'class' => 'woo-variation-gallery-options',
							'id'    => 'woo_variation_gallery_advanced_options',
						),
						
						// Disable on Specific Product type
						array(
							'title'   => esc_html__( 'Disable on Product Type', 'woo-variation-gallery' ),
							'type'    => 'multiselect',
							'options' => wc_get_product_types(),
							'class'   => 'wc-enhanced-select',
							'default' => array('gift-card', 'bundle'),
							'desc_tip' => esc_html__( 'Disable Gallery on Specific Product type like: simple product / variable product / bundle product etc.', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_disabled_product_type',
							'custom_attributes' =>array(
							        'data-placeholder'=>esc_html__( 'Choose specific product type(s).', 'woo-variation-gallery' ),
							)
						),
						
							// Thumbnails Image Width
						array(
							'title'             => esc_html__( 'Thumbnails Image Width', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( wc_get_theme_support( 'gallery_thumbnail_image_width', 100 ) ),
							'css'               => 'width:65px;',
							'suffix'               => 'px',
							'desc_tip'          => esc_html__( 'Product Thumbnails Image Width In Pixel to fix blurry thumbnail image.', 'woo-variation-gallery' ),
							'desc'              => sprintf( esc_html__( 'Product Thumbnails Image Width In Pixel to fix blurry thumbnail image. Default value is: %1$d. Limit: 80-300. %2$sRecommended: To Regenerate shop thumbnails after change this setting.%3$s', 'woo-variation-gallery' ),
							absint( wc_get_theme_support( 'gallery_thumbnail_image_width', 100 ) ),
							sprintf('<a target="_blank" href="%s">', esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-status&tab=tools&action=regenerate_thumbnails' ), 'debug_action' ) )),
							'</a>'
							),
							'id'                => 'woo_variation_gallery_thumbnail_width',
							'custom_attributes' => array(
								'min'  => 80,
								'max'  => 300,
								'step' => 5,
							),
						),
						
						// Gallery and Variation Wrapper
						/*array(
							'title'   => esc_html__( 'Wrapper Class', 'woo-variation-gallery' ),
							'type'    => 'text',
							'default' => '.product',
							'css'=>'width:150px',
							'desc'    => __( 'Gallery and Variation Wrapper Class. Default is: <code>.product</code>. If you have multiple gallery on a page.', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_and_variation_wrapper'
						),*/
						
						// Reset Variation Gallery
						array(
							'title'   => esc_html__( 'Reset Variation Gallery', 'woo-variation-gallery' ),
							'type'    => 'checkbox',
							'default' => 'no',
							'desc'    => esc_html__( 'Always Reset Gallery After Variation Select', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_reset_on_variation_change'
						),
						
						// Gallery Image Preload
						array(
							'title'   => esc_html__( 'Gallery Image Preload', 'woo-variation-gallery' ),
							'type'    => 'checkbox',
							'default' => 'yes',
							'desc'    => esc_html__( 'Variation Gallery Image Preload', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_image_preload'
						),
						// Defer JS Load
						array(
							'title'   => esc_html__( 'Load Defer Gallery JS', 'woo-variation-gallery' ),
							'type'    => 'checkbox',
							'default' => 'yes',
							'desc'    => esc_html__( 'Variation Gallery JS Loading', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_defer_js'
						),
						
						array(
							'type' => 'sectionend',
							'id'   => 'woo_variation_gallery_advanced_options'
						),
					
					) );
				break;
				
			case 'migration':
				$settings = apply_filters( 'woo_variation_gallery_migration_settings', array(
					
					array(
						'name' => esc_html__( 'Gallery Migration', 'woo-variation-gallery' ),
						'type' => 'title',
						'desc' => esc_html__('Migrate gallery from other plugins. Migration process will run on background.', 'woo-variation-gallery'),
						'id'   => 'woo_variation_gallery_migration_settings',
					)
				
				) );
				break;
				
			default:
					$settings = apply_filters( 'woo_variation_gallery_default_settings', array(
						
						// Thumbnails Section Start
						array(
							'name' => esc_html__( 'Thumbnail Options', 'woo-variation-gallery' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'woo_variation_gallery_thumbnail_options',
						),
						
						// Thumbnails Item
						array(
							'title'             => esc_html__( 'Thumbnails Item', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_default_thumbnails_columns', 4 ) ),
							'css'               => 'width:50px;',
							'desc_tip'          => esc_html__( 'Product Thumbnails Item Image', 'woo-variation-gallery' ),
							'desc'              =>  sprintf( esc_html__( 'Product Thumbnails Item Image. Default value is: %d. Limit: 2-8.', 'woo-variation-gallery' ), absint( apply_filters( 'woo_variation_gallery_default_thumbnails_columns', 4 ) ) ),
							'id'                => 'woo_variation_gallery_thumbnails_columns',
							'custom_attributes' => array(
								'min'  => 2,
								'max'  => 8,
								'step' => 1,
							),
						),
						
						// Thumbnails Gap
						array(
							'title'             => esc_html__( 'Thumbnails Gap', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_default_thumbnails_gap', 0 ) ),
							'css'               => 'width:50px;',
							'suffix'               => 'px',
							'desc_tip'          => esc_html__( 'Product Thumbnails Gap In Pixel', 'woo-variation-gallery' ),
							'desc'              => sprintf( esc_html__( 'Product Thumbnails Gap In Pixel. Default value is: %d. Limit: 0-20.', 'woo-variation-gallery' ), apply_filters( 'woo_variation_gallery_default_thumbnails_gap', 0 ) ),
							'id'                => 'woo_variation_gallery_thumbnails_gap',
							'custom_attributes' => array(
								'min'  => 0,
								'max'  => 20,
								'step' => 1,
							),
						),
						
						// Section End
						array(
							'type' => 'sectionend',
							'id'   => 'woo_variation_gallery_thumbnail_options'
						),
						
						// Gallery Section Start
						array(
							'name' => __( 'Gallery Options', 'woo-variation-gallery' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'woo_variation_gallery_main_options',
						),
						
						// Default Gallery Width
						array(
							'title'             => esc_html__( 'Gallery Width', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_default_width', 30 ) ),
							'css'               => 'width:60px;',
							'suffix'               => '%. ' . esc_html__('For large devices.', 'woo-variation-gallery'),
							'desc_tip'          => esc_html__( 'Slider gallery width in % for large devices.', 'woo-variation-gallery' ),
							'desc'              =>  sprintf( __( 'Slider Gallery Width in %%. Default value is: %d. Limit: 10-100. Please check this <a target="_blank" href="%s">how to video to configure it.</a>', 'woo-variation-gallery' ), absint( apply_filters( 'woo_variation_gallery_default_width', 30 ) ), 'https://www.youtube.com/watch?v=IPRZnHy3nuQ&list=PLjkiDGg3ul_IX0tgkHNKtTyGhywFhU2J1&index=1' ),
							'id'                => 'woo_variation_gallery_width',
							'custom_attributes' => array(
								'min'  => 10,
								'max'  => 100,
								'step' => 1,
							),
						),
						
						// Medium Devices, Desktop
						array(
							'title'             => esc_html__( 'Gallery Width', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_medium_device_width', 0 ) ),
							'css'               => 'width:60px;',
							'suffix'               => 'px. ' . esc_html__('For medium devices.', 'woo-variation-gallery'),
							'desc_tip'          => esc_html__( 'Slider gallery width in px for medium devices, small desktop', 'woo-variation-gallery' ),
							'desc'              =>  esc_html__( 'Slider gallery width in pixel for medium devices, small desktop. Default value is: 0. Limit: 0-1000. Media query (max-width : 992px)', 'woo-variation-gallery' ),
							'id'                => 'woo_variation_gallery_medium_device_width',
							'custom_attributes' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 1,
							),
						),
						
						// Small Devices, Tablets
						array(
							'title'             => esc_html__( 'Gallery Width', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_small_device_width', 720 ) ),
							'css'               => 'width:60px;',
							'suffix'            => 'px. ' . esc_html__('For small devices, tablets.', 'woo-variation-gallery'),
							'desc_tip'          => esc_html__( 'Slider gallery width in px for small devices, tablets', 'woo-variation-gallery' ),
							'desc'              => esc_html__( 'Slider gallery width in pixel for medium devices, small desktop. Default value is: 720. Limit: 0-1000. Media query (max-width : 768px)', 'woo-variation-gallery' ),
							'id'                => 'woo_variation_gallery_small_device_width',
							'custom_attributes' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 1,
							),
						),
						
						// Clear float for Small Devices, Tablets
						array(
							'title'   => esc_html__( 'Clear float', 'woo-variation-gallery' ),
							'type'    => 'checkbox',
							'default' => 'no',
							'desc'    => esc_html__( 'Clear float for small devices, tablets.', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_small_device_clear_float'
						),
						
						// Extra Small Devices, Phones
						array(
							'title'             => esc_html__( 'Gallery Width', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_extra_small_device_width', 320 ) ),
							'css'               => 'width:60px;',
							'suffix'            => 'px. ' . esc_html__('For extra small devices, mobile.', 'woo-variation-gallery'),
							'desc_tip'          => esc_html__( 'Slider gallery width in px for extra small devices, phones', 'woo-variation-gallery' ),
							'desc'              => esc_html__( 'Slider gallery width in pixel for extra small devices, phones. Default value is: 320. Limit: 0-1000. Media query (max-width : 480px)', 'woo-variation-gallery' ),
							'id'                => 'woo_variation_gallery_extra_small_device_width',
							'custom_attributes' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 1,
							),
						),
						
						// Clear float for Extra Small Devices, Phones
						array(
							'title'   => esc_html__( 'Clear float', 'woo-variation-gallery' ),
							'type'    => 'checkbox',
							'default' => 'no',
							'desc'    => esc_html__( 'Clear float for extra small devices, mobile.', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_extra_small_device_clear_float'
						),
						
						
						
						// Gallery Bottom GAP
						array(
							'title'             => esc_html__( 'Gallery Bottom Gap', 'woo-variation-gallery' ),
							'type'              => 'number',
							'default'           => absint( apply_filters( 'woo_variation_gallery_default_margin', 30 ) ),
							'css'               => 'width:60px;',
							'desc_tip'          => esc_html__( 'Slider gallery gottom margin in pixel', 'woo-variation-gallery' ),
							'suffix'            => 'px',
							'desc'              => sprintf( esc_html__( 'Slider gallery bottom margin in pixel. Default value is: %d. Limit: 10-100.', 'woo-variation-gallery' ), apply_filters( 'woo_variation_gallery_default_margin', 30 ) ),
							'id'                => 'woo_variation_gallery_margin',
							'custom_attributes' => array(
								'min'  => 10,
								'max'  => 100,
								'step' => 1,
							),
						),
						
						// Disable Preloader
						array(
							'title'   => esc_html__( 'Disable Preloader', 'woo-variation-gallery' ),
							'type'    => 'checkbox',
							'default' => 'no',
							'desc'    => esc_html__( 'Disable preloader on change variation', 'woo-variation-gallery' ),
							'id'      => 'woo_variation_gallery_preloader_disable'
						),
						
						// Preload Style
						array(
							'title'   => esc_html__( 'Preload Style', 'woo-variation-gallery' ),
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'default' => 'blur',
							'id'      => 'woo_variation_gallery_preload_style',
							'options' => array(
								'fade' => esc_html__( 'Fade', 'woo-variation-gallery' ),
								'blur' => esc_html__( 'Blur', 'woo-variation-gallery' ),
								'gray' => esc_html__( 'Gray', 'woo-variation-gallery' ),
							)
						),
						
						
						// End
						array(
							'type' => 'sectionend',
							'id'   => 'woo_variation_gallery_main_options'
						),
					
					) );
					break;
			
			}
			
			return apply_filters( 'woocommerce_get_settings_woo-variation-gallery', $settings, $current_section );
		}
		
		public function output_fields( $options ) {
			foreach ( $options as $value ) {
				if ( ! isset( $value['type'] ) ) {
					continue;
				}
				if ( ! isset( $value['id'] ) ) {
					$value['id'] = '';
				}
				if ( ! isset( $value['title'] ) ) {
					$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
				}
				if ( ! isset( $value['class'] ) ) {
					$value['class'] = '';
				}
				if ( ! isset( $value['css'] ) ) {
					$value['css'] = '';
				}
				if ( ! isset( $value['default'] ) ) {
					$value['default'] = '';
				}
				if ( ! isset( $value['desc'] ) ) {
					$value['desc'] = '';
				}
				if ( ! isset( $value['desc_tip'] ) ) {
					$value['desc_tip'] = false;
				}
				if ( ! isset( $value['placeholder'] ) ) {
					$value['placeholder'] = '';
				}
				if ( ! isset( $value['suffix'] ) ) {
					$value['suffix'] = '';
				}
				
				if ( ! isset( $value['value'] ) ) {
					$value['value'] = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
				}

				// Custom attribute handling.
				$custom_attributes = array();

				if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
					foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
						$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
					}
				}

				// Description handling.
				$field_description = WC_Admin_Settings::get_field_description( $value );
				$description       = $field_description['description'];
				$tooltip_html      = $field_description['tooltip_html'];

				// Switch based on type.
				switch ( $value['type'] ) {

					// Section Titles.
					case 'title':
						if ( ! empty( $value['title'] ) ) {
							echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
						}
						if ( ! empty( $value['desc'] ) ) {
							echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
							echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
							echo '</div>';
						}
						echo '<table class="form-table woo-variation-gallery-form-table">' . "\n\n";
						if ( ! empty( $value['id'] ) ) {
							do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) );
						}
						break;

					// Section Ends.
					case 'sectionend':
						if ( ! empty( $value['id'] ) ) {
							do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
						}
						echo '</table>';
						if ( ! empty( $value['id'] ) ) {
							do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_after' );
						}
						break;

					// Standard text inputs and subtypes like 'number'.
					case 'text':
					case 'password':
					case 'datetime':
					case 'datetime-local':
					case 'date':
					case 'month':
					case 'time':
					case 'week':
					case 'number':
					case 'email':
					case 'url':
					case 'tel':
						$option_value = $value['value'];

						?><tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="<?php echo esc_attr( $value['type'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
									/><span class="suffix"><?php echo esc_html( $value['suffix'] ); ?></span> <?php echo $description; // WPCS: XSS ok. ?>
							</td>
						</tr>
						<?php
						break;

					// Color picker.
					case 'color':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">&lrm;
								<span class="colorpickpreview" style="background: <?php echo esc_attr( $option_value ); ?>">&nbsp;</span>
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="text"
									dir="ltr"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>colorpick"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
									/>&lrm; <?php echo $description; // WPCS: XSS ok. ?>
									<div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
							</td>
						</tr>
						<?php
						break;

					// Textarea.
					case 'textarea':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<?php echo $description; // WPCS: XSS ok. ?>

								<textarea
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
									><?php echo esc_textarea( $option_value ); // WPCS: XSS ok. ?></textarea>
							</td>
						</tr>
						<?php
						break;

					// Select boxes.
					case 'select':
					case 'multiselect':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<select
									name="<?php echo esc_attr( $value['id'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
									<?php echo 'multiselect' === $value['type'] ? 'multiple="multiple"' : ''; ?>
									>
									<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<option value="<?php echo esc_attr( $key ); ?>"
											<?php

											if ( is_array( $option_value ) ) {
												selected( in_array( (string) $key, $option_value, true ), true );
											} else {
												selected( $option_value, (string) $key );
											}

											?>
										><?php echo esc_html( $val ); ?></option>
										<?php
									}
									?>
								</select><span class="suffix"><?php echo esc_html( $value['suffix'] ); ?></span> <?php echo $description; // WPCS: XSS ok. ?>
							</td>
						</tr>
						<?php
						break;

					// Radio inputs.
					case 'radio':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<fieldset>
									<?php echo $description; // WPCS: XSS ok. ?>
									<ul>
									<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<li>
											<label><input
												name="<?php echo esc_attr( $value['id'] ); ?>"
												value="<?php echo esc_attr( $key ); ?>"
												type="radio"
												style="<?php echo esc_attr( $value['css'] ); ?>"
												class="<?php echo esc_attr( $value['class'] ); ?>"
												<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
												<?php checked( $key, $option_value ); ?>
												/> <?php echo esc_html( $val ); ?></label>
										</li>
										<?php
									}
									?>
									</ul>
								</fieldset>
							</td>
						</tr>
						<?php
						break;

					// Checkbox input.
					case 'checkbox':
						$option_value     = $value['value'];
						$visibility_class = array();

						if ( ! isset( $value['hide_if_checked'] ) ) {
							$value['hide_if_checked'] = false;
						}
						if ( ! isset( $value['show_if_checked'] ) ) {
							$value['show_if_checked'] = false;
						}
						if ( 'yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked'] ) {
							$visibility_class[] = 'hidden_option';
						}
						if ( 'option' === $value['hide_if_checked'] ) {
							$visibility_class[] = 'hide_options_if_checked';
						}
						if ( 'option' === $value['show_if_checked'] ) {
							$visibility_class[] = 'show_options_if_checked';
						}

						if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) {
							?>
								<tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
									<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
									<td class="forminp forminp-checkbox">
										<fieldset>
							<?php
						} else {
							?>
								<fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
							<?php
						}

						if ( ! empty( $value['title'] ) ) {
							?>
								<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
							<?php
						}

						?>
							<label for="<?php echo esc_attr( $value['id'] ); ?>">
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="checkbox"
									class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
									value="1"
									<?php checked( $option_value, 'yes' ); ?>
									<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
								/> <?php echo $description; // WPCS: XSS ok. ?>
							</label> <?php echo $tooltip_html; // WPCS: XSS ok. ?>
						<?php

						if ( ! isset( $value['checkboxgroup'] ) || 'end' === $value['checkboxgroup'] ) {
							?>
										</fieldset>
									</td>
								</tr>
							<?php
						} else {
							?>
								</fieldset>
							<?php
						}
						break;

					// Image width settings. @todo deprecate and remove in 4.0. No longer needed by core.
					case 'image_width':
						$image_size       = str_replace( '_image_size', '', $value['id'] );
						$size             = wc_get_image_size( $image_size );
						$width            = isset( $size['width'] ) ? $size['width'] : $value['default']['width'];
						$height           = isset( $size['height'] ) ? $size['height'] : $value['default']['height'];
						$crop             = isset( $size['crop'] ) ? $size['crop'] : $value['default']['crop'];
						$disabled_attr    = '';
						$disabled_message = '';

						if ( has_filter( 'woocommerce_get_image_size_' . $image_size ) ) {
							$disabled_attr    = 'disabled="disabled"';
							$disabled_message = '<p><small>' . esc_html__( 'The settings of this image size have been disabled because its values are being overwritten by a filter.', 'woocommerce' ) . '</small></p>';
						}

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
							<label><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html . $disabled_message; // WPCS: XSS ok. ?></label>
						</th>
							<td class="forminp image_width_settings">

								<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" <?php echo $disabled_attr; // WPCS: XSS ok. ?> id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo esc_attr( $width ); ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" <?php echo $disabled_attr; // WPCS: XSS ok. ?> id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo esc_attr( $height ); ?>" />px

								<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" <?php echo $disabled_attr; // WPCS: XSS ok. ?> id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" value="1" <?php checked( 1, $crop ); ?> /> <?php esc_html_e( 'Hard crop?', 'woocommerce' ); ?></label>

								</td>
						</tr>
						<?php
						break;

					// Single page selects.
					case 'single_select_page':
						$args = array(
							'name'             => $value['id'],
							'id'               => $value['id'],
							'sort_column'      => 'menu_order',
							'sort_order'       => 'ASC',
							'show_option_none' => ' ',
							'class'            => $value['class'],
							'echo'             => false,
							'selected'         => absint( $value['value'] ),
							'post_status'      => 'publish,private,draft',
						);

						if ( isset( $value['args'] ) ) {
							$args = wp_parse_args( $value['args'], $args );
						}

						?>
						<tr valign="top" class="single_select_page">
							<th scope="row" class="titledesc">
								<label><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp">
								<?php echo str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'woocommerce' ) . "' style='" . $value['css'] . "' class='" . $value['class'] . "' id=", wp_dropdown_pages( $args ) ); // WPCS: XSS ok. ?> <?php echo $description; // WPCS: XSS ok. ?>
							</td>
						</tr>
						<?php
						break;

					// Single country selects.
					case 'single_select_country':
						$country_setting = (string) $value['value'];

						if ( strstr( $country_setting, ':' ) ) {
							$country_setting = explode( ':', $country_setting );
							$country         = current( $country_setting );
							$state           = end( $country_setting );
						} else {
							$country = $country_setting;
							$state   = '*';
						}
						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'woocommerce' ); ?>" class="wc-enhanced-select">
								<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
							</select> <?php echo $description; // WPCS: XSS ok. ?>
							</td>
						</tr>
						<?php
						break;

					// Country multiselects.
					case 'multi_select_countries':
						$selections = (array) $value['value'];

						if ( ! empty( $value['options'] ) ) {
							$countries = $value['options'];
						} else {
							$countries = WC()->countries->countries;
						}

						asort( $countries );
						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp">
								<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose countries / regions&hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'woocommerce' ); ?>" class="wc-enhanced-select">
									<?php
									if ( ! empty( $countries ) ) {
										foreach ( $countries as $key => $val ) {
											echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $selections ) . '>' . esc_html( $val ) . '</option>'; // WPCS: XSS ok.
										}
									}
									?>
								</select> <?php echo ( $description ) ? $description : ''; // WPCS: XSS ok. ?> <br /><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></a> <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></a>
							</td>
						</tr>
						<?php
						break;

					// Days/months/years selector.
					case 'relative_date_selector':
						$periods      = array(
							'days'   => __( 'Day(s)', 'woocommerce' ),
							'weeks'  => __( 'Week(s)', 'woocommerce' ),
							'months' => __( 'Month(s)', 'woocommerce' ),
							'years'  => __( 'Year(s)', 'woocommerce' ),
						);
						$option_value = wc_parse_relative_date_option( $value['value'] );
						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
							</th>
							<td class="forminp">
							<input
									name="<?php echo esc_attr( $value['id'] ); ?>[number]"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="number"
									style="width: 80px;"
									value="<?php echo esc_attr( $option_value['number'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									step="1"
									min="1"
									<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
								/>&nbsp;
								<select name="<?php echo esc_attr( $value['id'] ); ?>[unit]" style="width: auto;">
									<?php
									foreach ( $periods as $value => $label ) {
										echo '<option value="' . esc_attr( $value ) . '"' . selected( $option_value['unit'], $value, false ) . '>' . esc_html( $label ) . '</option>';
									}
									?>
								</select> <?php echo ( $description ) ? $description : ''; // WPCS: XSS ok. ?>
							</td>
						</tr>
						<?php
						break;

					// Default: run an action.
					default:
						do_action( 'woocommerce_admin_field_' . $value['type'], $value );
						do_action( 'woo-variation-gallery_admin_field', $value );
						break;
				}
			}
		}
		
		public function save() {
			
			global $current_section;
			
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );
			
			if ( $current_section ) {
			    do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
				do_action( 'woocommerce_update_options_woo-variation-gallery', $current_section );
			}
		}
	}
	
	return new Woo_Variation_Gallery_Settings();
	
