<?php
/**
 * Stores Settings
 */

class Stores_Settings {
	private $stores_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'stores_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'stores_settings_page_init' ) );
	}

	public function stores_settings_add_plugin_page() {
		add_menu_page(
			'Stores Settings', // page_title
			'Stores Settings', // menu_title
			'manage_options', // capability
			'stores-settings', // menu_slug
			array( $this, 'stores_settings_create_admin_page' ), // function
			'dashicons-art', // icon_url
			80 // position
		);
	}

	public function stores_settings_create_admin_page() {
		$this->stores_settings_options = get_option( 'stores_settings' ); ?>

		<div class="wrap">
			<h2>Stores Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'stores_settings_option_group' );
					do_settings_sections( 'stores-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function stores_settings_page_init() {
		register_setting(
			'stores_settings_option_group', // option_group
			'stores_settings', // option_name
			array( $this, 'stores_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'stores_settings_setting_section', // id
			'Settings', // title
			array( $this, 'stores_settings_section_info' ), // callback
			'stores-settings-admin' // page
		);

		add_settings_field(
			'google_api', // id
			'Google API', // title
			array( $this, 'google_api_callback' ), // callback
			'stores-settings-admin', // page
			'stores_settings_setting_section' // section
		);
	}

	public function stores_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['google_api'] ) ) {
			$sanitary_values['google_api'] = sanitize_text_field( $input['google_api'] );
		}

		return $sanitary_values;
	}

	public function stores_settings_section_info() {
		
	}

	public function google_api_callback() {
		printf(
			'<input class="regular-text" type="text" name="stores_settings[google_api]" id="google_api" value="%s">',
			isset( $this->stores_settings_options['google_api'] ) ? esc_attr( $this->stores_settings_options['google_api']) : ''
		);
	}

}
if ( is_admin() )
	$stores_settings = new Stores_Settings();

/* 
 * Retrieve this value with:
 * $stores_settings_options = get_option( 'stores_settings' ); // Array of All Options
 * $google_api = $stores_settings_options['google_api']; // Google API
 */
