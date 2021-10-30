<?php
require_once( dirname( __FILE__ ) . '/mm_html.php' );

class MmenuBackend extends MmHtml {

	/**	Plugin name */
	protected $name	= 'mmenu';

	/**	Plugin version */
	protected $version = '2.9.2';

	/**	Array for the help tabs */
	protected $helptabs = array();

	/**	URL to check the license */
	private $license_check_url = 'https://www.mmenujs.com/inc/__wp__update.php';

	/**	The text explaining how to get a license */
	private $get_license_text = '';

	/**	All options */
	protected $options = array(
		'mm_setup' => array(
			'plugin_version',
			'version',
			'menu',
			'button',
			'license',
			'license_valid'
		),
		'mm_menu' => array(
			'position',
			'backgroundcolor',
			'theme',
			'additional_menus',
			'additional_titles',
			'item_size',
			'breakpoint'
		),
		'mm_header'	=> array(
			'navigate',
			'navigate_title',
			'navigate_close',

			'image',
			'image_src',
			'image_scale',
			'image_height',

			'searchfield',
			'searchfield_sitesearch',
			'searchfield_placeholder',
			'searchfield_noresults',

			'buttons_html',
			'buttons_selector',
			'button_1_icon', 'button_1_href', 'button_1_target',
			'button_2_icon', 'button_2_href', 'button_2_target',
			'button_3_icon', 'button_3_href', 'button_3_target',
			'button_4_icon', 'button_4_href', 'button_4_target',
			'button_5_icon', 'button_5_href', 'button_5_target'
		),
		'mm_footer'	=> array(
			'buttons_html',
			'buttons_selector',
			'button_1_icon', 'button_1_href', 'button_1_target',
			'button_2_icon', 'button_2_href', 'button_2_target',
			'button_3_icon', 'button_3_href', 'button_3_target',
			'button_4_icon', 'button_4_href', 'button_4_target',
			'button_5_icon', 'button_5_href', 'button_5_target'
		),
		'mm_accessibility' => array(
			'keyboard'
		),
		'mm_advanced' => array(
			'class_panel',
			'class_noPanel',
			'class_vertical',
			'class_noListview',
			'class_inset',
			'class_selected',
			'class_divider',
			'class_spacer',

			'pagedim',
			'pageshadow',
			'slidemenu',
			'indentborder',
			'truncatelistitems',
			'counters',
			'fullsubopen',
			'fullscreen'
		)
	);


	public function __construct() {

		//	Add the page to the menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

		//	Register the settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		//	Initiate the auto-update
		add_action( 'admin_init', array( $this, 'autoupdate_init' ) );

		//	Check the version to create the JS file
		add_action( 'plugins_loaded', array( $this, 'update_check' ) );

		//	Load the translations
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}



	/**
	 * Register the settings.
	 */
	public function register_settings() {
		foreach( $this->options as $option => $suboptions ) {
			register_setting( 'mmenu-settings', $option );
		}
	}



	/**
	 * Add the menu item + the page.
	 */
	public function add_menu_page() {
		$page = add_menu_page(
			'mmenu',
			'mmenu',
			'manage_options',
			'mmenu',
			array( $this, 'create_admin_page' ),
			'dashicons-menu'
		);

		//	Add the help tabs
		add_action( 'load-' . $page, array( $this, 'plugin_help' ) );

		add_action( 'load-' . $page, array( $this, 'admin_page_loaded' ) );
	}

	public function admin_page_loaded() {
		//	Load the admin CSS and JS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_css_js' ) );
	}



	/**
	 * Add WP help tabs.
	 */
	public function plugin_help() {
		$screen = get_current_screen();

		$this->helptabs = array(
			array(
				'id'        => 'mmenu-help-menu',
				'title'     => __( 'Locate the menu', 'mmenu' ),
				'content'   => '
					<p><strong>'
						. __( 'Locate the menu', 'mmenu' ) . '</strong><br />'
						. __( 'All menus used by your theme are automatically listed in the "selector" combobox. If the menu you\'re looking for isn\'t listed, you can either specify it manually or use the "locate on website" button.', 'mmenu' ) . '</p>
					<p>' . __( 'The "locate on website" popup makes an educated guess about what HTML element might be the menu, using common HTML patterns used by WordPress.<br />'
						. 'If that also does not find the menu, you can still specify it manually by typing the selector (for example "#my-menu") in the "selector" combobox.', 'mmenu' ) . '</p>'
			),
			array(
				'id'        => 'mmenu-help-button',
				'title'     => __( 'Locate the button', 'mmenu' ),
				'content'   => '
					<p><strong>'
						. __( 'Locate the menu button', 'mmenu' ) . '</strong><br />'
						. __( 'All menu buttons used by your theme are automatically listed in the "selector" combobox. If the button you\'re looking for isn\'t listed, you can either specify it manually or use the "locate on website" button.', 'mmenu' ) . '</p>
					<p>' . __( 'The "locate on website" popup makes an educated guess about what HTML element might be the menu button, using common HTML patterns used by WordPress.<br />'
						. 'If that also does not find the button, you can still specify it manually by typing the selector (for example "#my-button") in the "selector" combobox', 'mmenu' ) . '.</p>'
			),
			array(
				'id'        => 'mmenu-help-icons',
				'title'     => __( 'Help and suboptions', 'mmenu' ) ,
				'content'   => '
					<p><strong>'
						. __( 'Help', 'mmenu' ) . '</strong><br />'
						. __( 'Click on the "help"-icon next to an option (if present) to reveal additional information about the option.', 'mmenu' ) . '</p>
					<p><strong>'
						. __( 'Suboptions', 'mmenu' ) . '</strong><br />'
						. __( 'Click on the "gear"-icon next to an option (if present) to reveal additional (sub)options.', 'mmenu' ) . '</p>'
			),
			array(
				'id'        => 'mmenu-help-styling',
				'title'     => __( 'Additional styling', 'mmenu' ),
				'content'   => '
					<p><strong>'
						. __( 'Vertical submenu', 'mmenu' ) . '</strong><br />'
						. __( 'Add the classname "Vertical" to a menu item if you want its submenu to expand below it.', 'mmenu' ) . '</p>
					<p><strong>'
						. __( 'Spacers', 'mmenu' ) . '</strong><br />'
						. __( 'Add the classname "Spacer" to a menu item if you want it to have more whitespace at the top.', 'mmenu' ) . '</p>
					<p><strong>'
						. __( 'Fixed elements', 'mmenu' ) . '</strong><br />'
						. __( 'Add the classname "Fixed" to a fixed element on your webpage if you want it to move out of view when opening the menu.', 'mmenu' ) . '</p>'
			)
	    );

		if ( count( $this->helptabs ) > 0 ) {
            foreach ( $this->helptabs as $tab ) {
                $screen->add_help_tab( $tab );
            }
		}
	}



	/**
	 * Create the admin page
	 */
	public function create_admin_page() {
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	    }

	    //	Add thickbox for the "locate on website" popup
	    add_thickbox();

	    //	Check if the .js and .css files are writable
	    $this->checkWritable();


	    $updated 	= isset( $_POST[ 'submit' ] );
		$preview 	= isset( $_POST[ 'preview' ] );
		$export 	= isset( $_POST[ 'export' ] );
		$import 	= isset( $_POST[ 'mm_import' ] ) ? json_decode(stripslashes($_POST[ 'mm_import' ]), true) : false;

		$old_setup 	= get_option( 'mm_setup', array() );

		//	Import options
		if ($import) {
			foreach( $this->options as $option => $suboptions ) {
				if ( isset( $import[ substr( $option, 3 ) ] ) ) {
					update_option( $option, $import[ substr( $option, 3 ) ] );
				}
			}
		}

	    //	Save updated options
		else if ( $updated || $preview || $export ) {
			foreach( $this->options as $option => $suboptions ) {
				if ( isset( $_POST[ $option ] ) ) {
					update_option( $option, $_POST[ $option ] );
				}
			}
		}

		//	Get options
		$mm_setup 			= get_option( 'mm_setup'			, array() );
		$mm_menu 			= get_option( 'mm_menu'				, array() );
		$mm_header 			= get_option( 'mm_header'			, array() );
		$mm_footer 			= get_option( 'mm_footer'			, array() );
		$mm_accessibility	= get_option( 'mm_accessibility'	, array() );
		$mm_advanced 		= get_option( 'mm_advanced'			, array() );

		$first 	 = !isset( $mm_setup[ 'version' ] );
		$version =  isset( $mm_setup[ 'version' ] ) ? $mm_setup[ 'version' ] : 0;

		//	Force license check after import.
		if (!isset($mm_setup[ 'license_valid' ])) {
			$mm_setup[ 'license_valid' ] = '';
			$old_setup[ 'license' ] = '';
		}
		//	Check whether the license is valid (only when it changed)
		$check_license = false;
		if ( isset( $mm_setup[ 'license' ] ) ) {
			if ( isset( $old_setup[ 'license' ] ) ) {
				if ( $old_setup[ 'license' ] != $mm_setup[ 'license' ] ) {
					$check_license = true;
				}
			} else {
				$check_license = true;
			}

			if ( $check_license ) {
				$remote = new mm_auto_update(
			    	$this->license_check_url,
			    	plugin_basename( dirname( __FILE__ ) ),
			    	$this->version,
			    	$mm_setup[ 'license' ],
			    	false
			    );

			    $mm_setup[ 'license_valid' ] = ( $remote->getRemote_license() === 'true' ) ? 'yes' : '';
				update_option( 'mm_setup', $mm_setup );
			}
		}

		if ( !isset( $mm_setup[ 'license' ] ) || strlen( $mm_setup[ 'license' ] ) < 1 ) {
			$this->get_license_text = '
				<p class="intro">'
					. __( '<a href="#mm_opt_setup">Upgrade to PRO</a> to enable more advanced options.', 'mmenu' ) . '</p>';

		} else {
			$this->get_license_text = '
				<p class="intro">'
					. __( '<a href="#mm_opt_setup">Enter a valid license key</a> to enable more advanced options.', 'mmenu' ) . '</p>';
		}


		//	Options were imported
		if ( $import ) {
			$this->echo_updated( __( 'Settings have been imported and saved.', 'mmenu' ) );
		}

		//	Options were updated and saved
		else if ( $updated ) {
			$this->saveFrontend();
			$this->echo_updated( __( 'Settings have been saved and published.', 'mmenu' ) );
		}

		//	Options were updated for a preview
		else if ( $preview ) {
			$this->saveFrontend( '-preview' );
		}

		echo '
		<div class="wrap' . ( $preview ? ' mmenu-preview' : '' ) . ( $export ? ' mmenu-export' : '' ) . ( $first ? ' mmenu-setup' : '' ) . '">';

		$this->echo_title( '<span>mmenu</span> ' . __( 'App look-alike menu for WordPress', 'mmenu' ) . ' <small>'
			. __( 'Version', 'mmenu' ) . ' ' . $this->version . '</small>', 'mmenu' );

		$this->echo_form_opener( 'mmenu-settings' );

		//	Preview tonen
		if ( $preview ) {
			echo '
			<div class="phone">
				<iframe src="' . get_home_url() . '?mmenu=preview"></iframe>
			</div>
			<p class="submit-preview">
				<strong>' . __( 'Happy with the result?', 'mmenu' ) . '</strong><br />
				<input type="submit" name="submit" value="' . __( 'Yes, publish it!', 'mmenu' ) . '" class="button button-primary button-large" /><br />
				<br />
				<a href="#mmenu-settings">' . __( 'Not yet, I need to make some more changes.', 'mmenu' ) . '</a>
			</p>';

		//	Export van de settings tonen
		} else if ( $export) {

			//	Get stored options
			$exp = array();
			foreach( $this->options as $option => $suboptions ) {
				$exp[ substr($option, 3) ] = get_option( $option );
			}

			//	Override some
			unset($exp['setup']['license_valid']);
			$exp['setup']['version'] = 1;

			echo '
			<div class="submit-export">
				<p><strong>' . __( 'Your settings', 'mmenu' ) . '</strong></p>
				<pre>' . json_encode( $exp ) . '</pre>
				<p><a href="#mmenu-settings" class="button">' . __( 'Close', 'mmenu' ) . '</a></p>
			</div>';
		}

		echo '
			<input name="mm_setup[plugin_version]" value="' . $this->version . '" type="hidden" />
			<input name="mm_setup[version]" value="' . ( $version + 1 ) . '" type="hidden" />';


		$this->opt_setup( 			$first, $mm_setup  			, $mm_setup );
		$this->opt_menu( 			$first, $mm_menu   			, $mm_setup );
		$this->opt_header( 			$first, $mm_header 			, $mm_setup );
		$this->opt_footer( 			$first, $mm_footer			, $mm_setup );
		$this->opt_accessibility( 	$first, $mm_accessibility 	, $mm_setup );
		$this->opt_advanced(	 	$first, $mm_advanced 		, $mm_setup );


		if ( $first ) {
			echo '
				<p class="submit">
					<input type="submit" name="submit" value="' . __( 'Save & proceed', 'mmenu' ) . '" class="button button-primary button-large" />
					<a href="#" class="cancel button">' . __( 'Cancel', 'mmenu' ) . '</a>
					<a href="#" class="next button button-primary button-large">' . __( 'Next', 'mmenu' ) . '</a>
					<a href="#" class="import button">' . __( 'Import settings', 'mmenu' ) . '</a>
				</p>';
		} else {
			echo '
				<p class="submit-fixed">
					<input type="submit" name="submit" value="' 	. __( 'Save & publish', 'mmenu' ) 	. '" class="button button-primary button-large" />
					<input type="submit" name="preview" value="' 	. __( 'Save & preview', 'mmenu' ) 	. '" class="button" />';

			if ( $mm_setup[ 'license_valid' ] ) {
				echo '
					<input type="submit" name="export" value="' 	. __( 'Save & export', 'mmenu' ) 	. '" class="button" />';
			}

			echo '
				</p>';
		}

		$this->echo_form_closer();
		$this->locate_popup();

		echo '
		</div>';
	}

	/**
	 * Html for all setup options.
	 */
	protected function opt_setup( $first, $mm_setup ) {
		echo '
			<h2 id="mm_opt_setup">' . __( 'Setup', 'mmenu' ) . '</h2>';

		if ( $first ) {
			$this->echo_section_opener( 'intro' );
			echo '
			<p class="intro"><strong>' . __( 'Great!', 'mmenu' ) . '</strong> ' . __( 'You\'ve successfully downloaded and installed the mmenu WordPress plugin.', 'mmenu' ) . '</p>
			<p>' . __( 'You are only a few clicks away from creating an app look-alike menu.<br />'
				. 'But first, we need to locate some elements on the website.', 'mmenu' ) . '</p>';
			$this->echo_section_closer();

		} else {
			$this->echo_section_opener( 'section-toggle' );
			echo '
				<a href="#mm_opt_setup">' . __( 'Click to show the setup options.', 'mmenu' ) . '</a>';
			$this->echo_section_closer();
		}

		$this->opt_setup_menu_selector( 	$first, $mm_setup );
		$this->opt_setup_button_selector( 	$first, $mm_setup );
		$this->opt_setup_license( 			$first, $mm_setup );
		$this->opt_setup_import( 			$first, $mm_setup );
	}

	/**
	 * HTML for the setup option: import
	 */
	protected function opt_setup_import( $first, $mm_setup ) {

		$submt = $first ? '' : '<br /><br /><input type="submit" name="submit" value="' . __( 'Import settings', 'mmenu' ) . '" class="button button-primary button-large" /> ';

		$this->echo_section_opener( 'closed import' );
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Import settings', 'mmenu' ) . '<br />
				<small>' . __( 'Note that existing settings will only be overwritten if new settings are present in the import.', 'mmenu' ) . '</small>',
			'<textarea name="mm_import"></textarea>' . $submt,
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'Use this field to import settings (for example when you\'ve exported them from another site).<br />'
				. 'Settings are exported as JSON and should look something like this.', 'mmenu' ) . '</p>
<pre>{
  "setup": { ... },
  "menu": { ... },
  "header": { ... },
  "footer": { ... },
  "accessibility": { ... },
  "advanced": { ... }
}</pre>',
			'explanation'
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the setup option: Locate the menu.
	 */
	protected function opt_setup_menu_selector( $first, $mm_setup ) {
		$inp = $this->selectorInput( 'menu', $mm_setup, 'mm_setup', 'menu', __( 'Locate the menu', 'mmenu' ) );
		if ( $first ) {
			$inp .= '
				<span class="fvh fvh-top fvh-1">' 		. __( 'Type a selector.'				, 'mmenu' ) . '</span>
				<span class="fvh fvh-bottom fvh-2">' 	. __( 'Choose one from the combobox.'	, 'mmenu' ) . '</span>
				<span class="fvh fvh-top fvh-3">' 		. __( 'Or locate it on the website.'	, 'mmenu' ) . '</span>
				<span class="fvh fvh-right fvh-4">' 	. __( 'Click the "help"-icon for help.'	, 'mmenu' ) . '</span>';
		}

		$this->echo_section_opener( 'closed' );
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Locate the menu', 'mmenu' ) . '<br />
				<small>' . __( 'Specify a CSS selector that targets the menu container.', 'mmenu' ) . '</small>',
			$inp,
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'The selector should target the element surrounding the main <code>UL</code>.<br />'
				. 'For example, the HTML below results in the selector <code>#my-menu</code>.', 'mmenu' ) . '</p>
<pre>&lt;nav id="my-menu"&gt;
   &lt;ul&gt;
      &lt;li&gt;&lt;a href="/"&gt;Home&lt;/a&gt;&lt;/li&gt;
   &lt;/ul&gt;
&lt;/nav&gt;</pre>',
			'explanation'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the setup option: Locate the button.
	 */
	protected function opt_setup_button_selector( $first, $mm_setup ) {
		$inp = $this->selectorInput( 'button', $mm_setup, 'mm_setup', 'button', __( 'Locate the button', 'mmenu' ) );
		if ( $first )
		{
			$inp .= '
				<span class="fvh fvh-top fvh-1">' 		. __( 'Type a selector.'				, 'mmenu' ) . '</span>
				<span class="fvh fvh-bottom fvh-2">' 	. __( 'Choose one from the combobox.'	, 'mmenu' ) . '</span>
				<span class="fvh fvh-top fvh-3">' 		. __( 'Or locate it on the website.'	, 'mmenu' ) . '</span>
				<span class="fvh fvh-right fvh-4">' 	. __( 'Click the "help"-icon for help.'	, 'mmenu' ) . '</span>';
		}
		$this->echo_section_opener( 'closed' );
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Locate the menu button', 'mmenu' ) . '<br />
				<small>' . __( 'Specify a CSS selector that targets an anchor or a button.', 'mmenu' ) . '</small>',
			$inp,
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'The selector should target an anchor (<code>&lt;a /&gt;</code>) or a button (<code>&lt;button /&gt;</code>) for opening the menu.<br />'
				. 'For example, the HTML below results in the selector <code>#my-button</code>.', 'mmenu' ) . '</p>

				<pre>&lt;a id="my-button" href="#my-menu"&gt;open menu&lt;/a&gt;</pre>

				<p>' . __( 'If it doesn\'t yet look like a hamburger icon, you\'ll have to <a target="_blank" rel="noopener" href="http://css-tricks.com/three-line-menu-navicon">do that yourself</a>.', 'mmenu' ) . '</p>',
			'explanation'
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the setup option: lLcense key.
	 */
	protected function opt_setup_license( $first, $mm_setup ) {
		if ( $first ) {
			echo $this->html_input( array( $mm_setup, 'mm_setup', 'license' ), 'hidden' ) .
				 $this->html_input( array( $mm_setup, 'mm_setup', 'license_valid' ), 'hidden' );

		} else {
			$has_license = (isset( $mm_setup[ 'license' ] )) && (strlen( $mm_setup[ 'license' ] ) > 0);
			$has_valid_license = $mm_setup[ 'license_valid' ] == 'yes';

			$check = '';
			$title = __( 'Upgrade to PRO', 'mmenu' );
			$text = __( 'Enter a valid license key to enable more advanced options.', 'mmenu' );

			if ( $has_license ) {
				$title = __( 'License key', 'mmenu' );
				$check = ' <i class="dashicons dashicons-' . ( $has_valid_license ? 'yes' : 'no' ) . '"></i>';
			}
			if ( $has_valid_license ) {
				$text = __( 'Thank you for using the PRO version, happy coding :)', 'mmenu' );
			}

			$this->echo_section_opener( $has_valid_license ? 'closed' : null );

			$this->echo_form_table_opener();
			$this->echo_form_table_row(
				$title . '<br />
					<small>' . $text . '</small>',
				$this->html_input( array( $mm_setup, 'mm_setup', 'license' ) ) . $check .
				$this->html_input( array( $mm_setup, 'mm_setup', 'license_valid' ), 'hidden' ),
				'license',
				'help'
			);

			//	explanation
			$this->echo_form_table_row(
				'',
				'<p>' . __( 'If you do not yet have a license, please purchase one from <a href="https://mmenujs.com/wordpress-plugin/download.html"  target="_blank">mmenujs.com</a>.', 'mmenu' ) . '</p>',
				'explanation'
			);
			$this->echo_form_table_closer();
			$this->echo_section_closer();
		}
	}

	/**
	 * HTML for all menu options.
	 */
	protected function opt_menu( $first, $mm_menu, $mm_setup ) {
		if ( $first ) {
			echo '
				<input type="hidden" name="mm_menu[position]" value="left" />
				<input type="hidden" name="mm_menu[backgroundcolor]" value="#f3f3f3" />
				<input type="hidden" name="mm_menu[breakpoint]" value="Never" />
				<input type="hidden" name="mm_menu[item_size]" value="44" />';

		} else {
			echo '
			<h2 id="mm_opt_menu">Menu options</h2>';

			if ( !$mm_setup[ 'license_valid' ] ) {
				$this->echo_section_opener();
				echo $this->get_license_text;
				$this->echo_section_closer();
			}

			$this->opt_menu_position( 	$mm_menu );
			$this->opt_menu_background( $mm_menu );

			if ( $mm_setup[ 'license_valid' ] ) {
				$this->opt_menu_menus( 	$mm_menu );
			}

			$this->opt_menu_sizes( 		$mm_menu );
			$this->opt_menu_breakpoint( $mm_menu );
		}
	}

	/**
	 * HTML for the menu option: Positioning the menu.
	 */
	protected function opt_menu_position( $mm_menu ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Position the menu', 'mmenu' ) . '<br />
				<small>' . __( 'Select how to position the menu; at the left, at the bottom, at the right or at the top side of the page.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_menu, 'mm_menu', 'position' ),
				array(
					'left' 		=> __( 'At the left'	, 'mmenu' ),
					'bottom'	=> __( 'At the bottom'	, 'mmenu' ),
					'right'		=> __( 'At the right'	, 'mmenu' ),
					'top'		=> __( 'At the top'		, 'mmenu' )
				)
			)
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the menu option: Choose a background color.
	 */
	protected function opt_menu_background( $mm_menu ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Choose a background color', 'mmenu' ) . '<br />
				<small>' . __( 'Any color will do. Dark, light, black or white, the menu will always look good.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_menu,'mm_menu', 'backgroundcolor' ) ) .
			$this->html_input( array( $mm_menu,'mm_menu', 'theme' ), 'hidden' )
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the menu option: Additional menus.
	 */
	protected function opt_menu_menus( $mm_menu ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Add additional menus', 'mmenu' ) . '<br />
				<small>' . __( 'For example a secondary menu, shopping cart or language select.', 'mmenu' ) . '</small>',
			$this->selectorInput( 'additional_menus', $mm_menu, 'mm_menu', 'menus', __( 'Locate the menu', 'mmenu' ), '#my-second-menu' ) .
				'<div class="menus-titles"><label for="mm_menu_menu_title">' . __( 'Specify the title above the main <code>UL</code>:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_menu, 'mm_menu', 'additional_titles' ), 'text', 'placeholder="Menu"' ) . '</div>',
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'The selector should target the element surrounding the main <code>UL</code>.<br />'
				. 'For example, the HTML below results in the selector <code>#my-menu</code>.', 'mmenu' ) . '</p>
<pre>&lt;nav id="my-menu"&gt;
   &lt;ul&gt;
      &lt;li&gt;&lt;a href="/"&gt;Home&lt;/a&gt;&lt;/li&gt;
   &lt;/ul&gt;
&lt;/nav&gt;</pre>
			<br />
			<p>Add multiple selectors and titles by separating them with a comma.
				Keep in mind that more menus mean less space for their titles.</p>
<pre>#secondary-menu, #language-select</pre>
<pre>Cart, Language</pre>',
			'explanation'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the menu option: Listitem size.
	 */
	protected function opt_menu_sizes( $mm_menu ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Item size', 'mmenu' ) . '<br />
				<small>' . __( 'Specify the size for the listitems in the menu.', 'mmenu' ) . '</small>',
			$this->html_select( array( $mm_menu, 'mm_menu', 'item_size' ),
				array(
					'40'	=> '40 pixels',
					'44' 	=> '44 pixels',
					'48' 	=> '48 pixels',
					'52' 	=> '52 pixels',
					'56' 	=> '56 pixels',
					'60' 	=> '60 pixels',
					'64' 	=> '64 pixels',
					'68' 	=> '68 pixels',
					'72' 	=> '72 pixels',
					'76' 	=> '76 pixels',
					'80' 	=> '80 pixels'
				)
			)
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the menu option: Sidebar breakpoint.
	 */
	protected function opt_menu_breakpoint( $mm_menu ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'The menu on wider screens', 'mmenu' ) . '<br />
				<small>' . __( 'As of what screen width should the mobile menu always be visible?', 'mmenu' ) . '</small>',
			'<span class="combobox is-combobox">' . $this->html_input( array( $mm_menu, 'mm_menu', 'breakpoint' ), 'text', 'placeholder="1400px"' ) . '
				<select id="breakpoint_select">
					<option value=""></option>
					<option value="' . __( 'Never', 'mmenu' ) . '">' . __( 'Never show the mobile menu on wider screens', 'mmenu' ) . '</option>
					<optgroup label="' . __( 'Common breakpoints', 'mmenu' ) . '">
						<option value="768px">768px</option>
						<option value="1224px">1224px</option>
						<option value="1824px">1824px</option>
					</optgroup>
					<optgroup id="theme_breakpoints_list" label="' . __( 'Defined by the theme', 'mmenu' ) . '"></optgroup>
				</select>
			</span>',
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'Type or select a <code>min-width</code> (in pixels). When the visitor resizes his screen larger than the given width, the mobile menu will become visible on the left next to the page.<br />'
				. 'For example, typing <code>1400</code> will result in the media query <code>screen and (min-width: 1400px)</code>.', 'mmenu' ) . '</p>
			<br />
			<p>' . __( 'If you don\'t want the mobile menu to always be visible on wider screens, empty the value or select the "Never" option.', 'mmenu' ) . '</p>',
			'explanation'
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for all header options.
	 */
	protected function opt_header( $first, $mm_header ) {
		if ( $first ) {
			echo '
				<input type="hidden" name="mm_header[navigate]" value="button" />
				<input type="hidden" name="mm_header[image]" value="no" />
				<input type="hidden" name="mm_header[searchfield]" value="no" />';

		} else {
			echo '
			<h2 id="mm_opt_header">' . __( 'Header options', 'mmenu' ) . '</h2>';

			$this->opt_header_navigate( 	$mm_header );
			$this->opt_header_image( 		$mm_header );
			$this->opt_header_searchfield( 	$mm_header );
			$this->opt_header_buttons( 		$mm_header );
		}
	}

	/**
	 * HTML for the header option: How to navigate.
	 */
	protected function opt_header_navigate( $mm_header ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Select how to navigate', 'mmenu' ) . '<br />
				<small>' . __( 'Select how to navigate between different levels in the menu.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_header, 'mm_header', 'navigate' ),
				array(
					'button'		=> __( 'With a back-button'							, 'mmenu' ),
					'breadcrumbs'	=> __( 'With breadcrumbs'							, 'mmenu' ),
					'iconpanels'	=> __( 'By showing a small part of the parent level', 'mmenu' ),
					'dropdown'		=> __( 'With dropdown submenus'						, 'mmenu' )
				), 'button'
			),
			null,
			true
		);

		//	header close option
		$this->echo_form_table_row(
			'',
			$this->html_checkbox( array( $mm_header, 'mm_header', 'navigate_close' ) ) .
				'<label for="mm_header_navigate_close">'
				. __( 'Add a button that closes the menu.', 'mmenu' ) . '</label>',
			'suboptions'
		);

		//	header title option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_navigate_title">'
				. __( 'Specify the title above the main <code>UL</code>:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_header, 'mm_header', 'navigate_title' ), 'text', 'placeholder="Menu"' ),
			'suboptions'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the header option: Add an image.
	 */
	protected function opt_header_image( $mm_header ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Add a logo or photo', 'mmenu' ) . '<br />
				<small>' . __( 'Personalize the menu by prepending it with a company logo or maybe a photo of yourself.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_header, 'mm_header', 'image' ),
				array(
					'no'	=> __( 'No thanks'		, 'mmenu' ),
					'yes'	=> __( 'Yes please!'	, 'mmenu' )
				), 'yes'
			) . $this->html_input( array( $mm_header, 'mm_header', 'image_src' ), 'hidden' ),
			null,
			true
		);

		//	header image size option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_image_scale">'
				. __( 'Specify how the image should be scaled.', 'mmenu' ) . '</label><br />' .
				$this->html_select( array( $mm_header, 'mm_header', 'image_scale' ),
					array(
						'contain'	=> __( 'Scale down the image to fit in the available space.'	, 'mmenu' ),
						'cover' 	=> __( 'Strech out the image to cover up the available space.'	, 'mmenu' )
					)
				),
			'suboptions'
		);

		//	header image height option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_image_height">'
				.__( 'Specify the preferred height for the image.', 'mmenu' ) . '<br />
				<small>' . __( 'Note that adding a searchfield or buttons will decrease the available height.', 'mmenu' ) . '</small></label><br />' .
				$this->html_select( array( $mm_header, 'mm_header', 'image_height' ),
					array(
						'4' => '160px',
						'3' => '120px',
						'2' => '80px',
						'1'	=> '40px'
					)
				),
			'suboptions'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the header option: Add a searchfield.
	 */
	protected function opt_header_searchfield( $mm_header ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Add a searchfield', 'mmenu' ) . '<br />
				<small>' . __( 'Enable your visitors to search through the menu items by prepending a searchfield to the menu.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_header, 'mm_header', 'searchfield' ),
				array(
					'no'	=> __( 'No thanks'		, 'mmenu' ),
					'yes'	=> __( 'Yes please!'	, 'mmenu' )
				), 'yes'
			),
			null,
			true
		);

		//	search site option
		$this->echo_form_table_row(
			'',
			$this->html_checkbox( array( $mm_header, 'mm_header', 'searchfield_sitesearch' ) ) .
				'<label for="mm_header_searchfield_sitesearch">'
					. __( 'Add a submit button to search the website.', 'mmenu' ) . '</label>',
			'suboptions'
		);

		//	placeholder option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_searchfield_placeholder">'
				. __( 'Specify the placeholder text for the searchfield:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_header,'mm_header', 'searchfield_placeholder' ), 'text', 'placeholder="Search"' ),
			'suboptions'
		);

		//	no results option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_searchfield_noresults">'
				. __( 'Specify the text (or HTML) to show when no results are found:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_header, 'mm_header', 'searchfield_noresults' ), 'text', 'placeholder="No results found."' ),
			'suboptions'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for the header option: Add buttons.
	 */
	protected function opt_header_buttons( $mm_header ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();

		$this->opt_buttons( $mm_header, 'mm_header' );

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for all footer options.
	 */
	protected function opt_footer( $first, $mm_footer, $mm_setup ) {
		if ( !$first ) {
			echo '
			<h2 id="mm_opt_footer">' . __( 'Footer options', 'mmenu' ) . '</h2>';

			if ( $mm_setup[ 'license_valid' ] ) {
				$this->opt_footer_buttons( $mm_footer );

			} else {
				$this->echo_section_opener();
				echo $this->get_license_text;
				$this->echo_section_closer();
			}
		}
	}

	/**
	 * HTML for the footer option: Add buttons.
	 */
	protected function opt_footer_buttons( $mm_footer ) {
		$this->echo_section_opener();
		$this->echo_form_table_opener();

		$this->opt_buttons( $mm_footer, 'mm_footer' );

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	/**
	 * HTML for all accessibility options.
	 */
	protected function opt_accessibility( $first, $mm_accessibility, $mm_setup ) {
		if ( $first ) {
			echo '
				<input type="hidden" name="mm_accessibility[keyboard]" value="yes" />';

		} else {
			echo '
			<h2 id="mm_opt_accessibility">Accessibility</h2>';

			$this->echo_section_opener();

			echo '
			<p class="intro">'
				. __( 'Some options to increase the accessibility of your menu.', 'mmenu' ) . '</p>';

			echo '
			<div class="advanced-options">';

			$this->opt_accessibility_keyboard( $mm_accessibility );

			echo '
			</div>';

			$this->echo_section_closer();
		}
	}

	/**
	 * HTML for the accessibility option: Keyboard navigation.
	 */
	protected function opt_accessibility_keyboard( $mm_accessibility ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_accessibility, 'mm_accessibility', 'keyboard' ) ) .
					'<label for="mm_accessibility_keyboard">'
						. __( 'Enable navigating with a keyboard.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for all advanced options.
	 */
	protected function opt_advanced( $first, $mm_advanced, $mm_setup ) {
		$hidden = '
			<input type="hidden" name="mm_advanced[indentborder]" value="yes" />
			<input type="hidden" name="mm_advanced[truncatelistitems]" value="yes" />
			<input type="hidden" name="mm_advanced[class_selected]" value="li.current-menu-item" />';

		if ( $first ) {
			echo $hidden;

		} else {
			echo '
			<h2 id="mm_opt_advanced">' . __( 'Advanced options', 'mmenu' ) . '</h2>';

			$this->echo_section_opener();

			if ( $mm_setup[ 'license_valid' ] ) {
				echo '
				<p class="intro">'
					. __( 'Some options to finetune the look and feel of your menu.', 'mmenu' ) . '</p>';

				echo '
				<div class="advanced-options">';

				$this->opt_advanced_pageshadow( $mm_advanced );
				$this->opt_advanced_pagedim( $mm_advanced );
				$this->opt_advanced_slidemenu( $mm_advanced );
				$this->opt_advanced_indentborder( $mm_advanced );
				$this->opt_advanced_truncatelistitems( $mm_advanced );
				$this->opt_advanced_counters( $mm_advanced );
				$this->opt_advanced_fullsubopen( $mm_advanced );
				$this->opt_advanced_fullscreen( $mm_advanced );

				echo '
				</div>';


				echo '
				<p class="intro">'
					. __( 'Specify selectors for detailed configuration of the menu.', 'mmenu' ) . '</p>';

				$this->opt_advanced_selectors( $mm_advanced );

			} else {
				echo $this->get_license_text;
				echo $hidden;
			}

			$this->echo_section_closer();
		}
	}

	/**
	 * HTML for the advanced option: Add a shadow to the page.
	 */
	protected function opt_advanced_pageshadow( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'pageshadow' ) ) .
					'<label for="mm_advanced_pageshadow">'
						. __( 'Add a shadow to the page.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Dim out the page.
	 */
	protected function opt_advanced_pagedim( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'pagedim' ) ) .
					'<label for="mm_advanced_pagedim">'
						. __( 'Dim out the page to black.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Slide out the menu.
	 */
	protected function opt_advanced_slidemenu( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'slidemenu' ) ) .
					'<label for="mm_advanced_slidemenu">'
						. __( 'Slide out the menu a bit.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Indent the borders.
	 */
	protected function opt_advanced_indentborder( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'indentborder' ) ) .
					'<label for="mm_advanced_indentborder">'
						. __( 'Indent the menu item borders.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Truncate listitems.
	 */
	protected function opt_advanced_truncatelistitems( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'truncatelistitems' ) ) .
					'<label for="mm_advanced_truncatelistitems">'
						. __( 'Truncate menu items to a single line.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Add counters.
	 */
	protected function opt_advanced_counters( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'counters' ) ) .
					'<label for="mm_advanced_counters">'
						. __( 'Add a counter for submenus.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Anchor href opens submenu.
	 */
	protected function opt_advanced_fullsubopen( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'fullsubopen' ) ) .
					'<label for="mm_advanced_fullsubopen">'
						. __( '&lt;a href="#"&gt; opens submenu', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Open the menu fullscreen.
	 */
	protected function opt_advanced_fullscreen( $mm_advanced ) {
		echo '
				<div>' .
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'fullscreen' ) ) .
					'<label for="mm_advanced_fullscreen">'
						. __( 'Open the menu fullscreen.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	/**
	 * HTML for the advanced option: Selectors.
	 */
	protected function opt_advanced_selectors( $mm_advanced ) {
		$this->echo_form_table_opener( 'compact' );

		$this->echo_form_table_row(
			'<small>' . __( 'Nodes to include as panels.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_panel' ), 'text', 'placeholder="ul.Panel"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Nodes to exclude as panels.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_noPanel' ), 'text', 'placeholder="ul.NoPanel"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Panels to expand below the parent.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_vertical' ), 'text', 'placeholder="ul.Vertical"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Lists to not display as a listview.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_noListview' ), 'text', 'placeholder="ul.NoListview"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Lists to display as an "inset" list.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_inset' ), 'text', 'placeholder="ul.Inset"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Listitems to display as "selected".', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_selected' ), 'text', 'placeholder="li.current-menu-item"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Listitems to display as a "divider".', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_divider' ), 'text', 'placeholder="li.Divider"' )
		);
		$this->echo_form_table_row(
			'<small>' . __( 'Listitems to display as a "spacer".', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_advanced, 'mm_advanced', 'class_spacer' ), 'text', 'placeholder="li.Spacer"' )
		);

		$this->echo_form_table_closer();

		echo '<br /><br />';
	}

	/**
	 * Include the locate popup.
	 */
	protected function locate_popup() {
		require_once dirname( dirname( __FILE__ ) ) . '/lib/locate/locate-popup.php';
	}

	/**
	 * A combobox for finding a CSS selector.
	 */
	protected function selectorInput( $id, $optn, $ostr = 'mm_setup', $type = null, $titl = null, $plch = null ) {
		$plch = ( $plch ) ? $plch : '#my-' . $id;
		$type = ( $type ) ? $type : $id;
		$titl = ( $titl ) ? $titl : sprintf( __( 'Locate the %s', 'mmenu' ), $id );

		$result = '
			<p class="combobox_locate">
				<span class="combobox">'
					. $this->html_input( array( $optn, $ostr, $id ), 'text', 'placeholder="' . $plch . '"' ) . '
					<select id="' . $ostr . '_' . $id . '_select"></select>
				</span>
				<a href="#TB_inline?width=600&height=500&inlineId=locate" class="button locate thickbox" data-type="' . $type . '" data-title="' . $titl . '">'
					. __( 'Locate on the website', 'mmenu' ) . '</a>';

		$result .= '</p>
			<p class="selector-warning">
				<strong>'
					. __( 'Uh oh...', 'mmenu' ) . '</strong><br />'
					. __( 'This selector targets more than one element, only the first will be used.', 'mmenu' ) . '<br />
				<a class="button dismiss" href="#">'
					. __( 'OK, I understand', 'mmenu' ) . '</a></p>
			<p class="selector-error">
				<strong>'
					. __( 'Oops!', 'mmenu' ) . '</strong><br />'
					. __( 'No element found with this selector! Are you sure it is correct?', 'mmenu' ) . '<br />
				<a class="button dismiss" href="#">'
					. __( 'Yes, I\'m sure', 'mmenu' ) . '</a></p>';

		return $result;
	}

	/**
	 * HTML for adding buttons.
	 */
	protected function opt_buttons( $optn, $ostr = 'mm_header' ) {
		$this->echo_form_table_row(
			__( 'Add buttons', 'mmenu' ) . '<br />
				<small>' . __( 'Type some HTML, specify a jQuery selector that targets a single or multiple anchors and/or create buttons manually.', 'mmenu' ) . '</small>',

			'<div class="buttons_html">' .
				$this->html_input( array( $optn, $ostr, 'buttons_html' ), 'text', 'placeholder="&lt;a href=&quot;/&quot;&gt;Home&lt;/a&gt;"' ) .
			'</div>' .
			$this->selectorInput( 'buttons_selector', $optn, $ostr, 'anchors', __( 'Locate the buttons', 'mmenu' ), 'ul.buttons a' ) .
			'<div class="buttons">
				<div class="buttons-head">
					<span>href:</span>
					<span>target:</span>
				</div>' .
				$this->createButton( 'button_1', $optn, $ostr ) .
				$this->createButton( 'button_2', $optn, $ostr ) .
				$this->createButton( 'button_3', $optn, $ostr ) .
				$this->createButton( 'button_4', $optn, $ostr ) .
				$this->createButton( 'button_5', $optn, $ostr ) . '
				<div class="buttons-foot">
					<a href="#" class="button">'
						. __( 'Add button', 'mmenu' ) . '</a>
				</div>
			</div>'
		);
	}

	/**
	 * HTML for adding a button.
	 */
	protected function createButton( $id, $optn, $ostr ) {
		$icn = ( isset( $optn[ $id . '_icon' ] ) )
			? ' dashicons ' . $optn[ $id . '_icon' ]
			: '';

		return '
			<div class="buttons-button">
				<div data-target="#' . $ostr . '_' . $id . '_icon' . '" class="button dashicons-picker' . $icn . '"></div>
				' . $this->html_input( 	array( $optn, $ostr, $id . '_icon' )	, 'hidden' ) . '
				' . $this->html_input( 	array( $optn, $ostr, $id . '_href' )	, 'text', 'placeholder="http://website.com"' ) . '
				' . $this->html_select( array( $optn, $ostr, $id . '_target' )	,
						array(
							'_self'		=> '_self',
							'_blank' 	=> '_blank'
						)
					) . '
				<a href="#" class="dashicons dashicons-no"></a>
			</div>';
	}

	/**
	 * Check if the needed files are writable.
	 */
	protected function checkWritable() {
		$dir = dirname( dirname( __FILE__ ) ) . '/';
		$str = 'wp-content/plugins/mmenu/';
		$err = array();

		foreach(
			array(
				'css/mmenu.css',
				'css/mmenu-preview.css',
				'js/mmenu.js',
				'js/mmenu-preview.js'
			) as $file
		) {
			if ( !is_writable( $dir . $file ) ) {
				$err[] = '<p>' . sprintf( __( 'The file <strong>%s</strong> is not writable, you need to chmod its permissions to at least 664.', 'mmenu' ), $str . $file ) . '</p>';
			}
		}

		if ( count( $err ) > 0 ) {
	        echo '
	        	<div class="error">' . implode( '', $err ) . '</div>';
		}
	}


	/**
	 * Initiate the auto update API.
	 */
	public function autoupdate_init() {
		$mm_setup = get_option( 'mm_setup', array() );

		if ( isset( $mm_setup[ 'license' ] ) &&
			isset( $mm_setup[ 'license_valid' ] ) &&
			$mm_setup[ 'license_valid' ] === 'yes'
		) {
		    new mm_auto_update(
				$this->license_check_url,
				$this->name . '/' . $this->name . '.php',
		    	$this->version,
		    	$mm_setup[ 'license' ]
		    );
		}
	}


	/**
	 * Check if plugin has been updated
	 */
	public function update_check() {
		//	Get the stored "setup" options
		$mm_setup = get_option( 'mm_setup', array() );

		//	Get the stored "plugin_version" option
		$version = isset( $mm_setup[ 'plugin_version' ] ) ? $mm_setup[ 'plugin_version' ] : '0';

		//	If the stored "plugin_version" does not equal the current plugin version
    	if ( $this->version != $version) {
    		//	Update the stored "plugin_version" to the current plugin version
    		$mm_setup[ 'plugin_version' ] = $this->version;
    		update_option( 'mm_setup', $mm_setup );

    		//	Save the frontend JS
        	$this->saveFrontend();
    	}
	}


	/**
	 * Save the frontend .js and .css file
	 */
	protected function saveFrontend( $fileAffix = '' ) {
		$mm = array();
		foreach( $this->options as $option => $suboptions ) {
			$k = substr( $option, 3 );
			$mm[ $k ] = get_option( $option );
			if ( !isset( $mm[ $k ] ) ) {
				$mm[ $k ] = array();
			}
			foreach( $suboptions as $suboption ) {
				if ( !isset( $mm[ $k ][ $suboption ] ) ) {
					$mm[ $k ][ $suboption ] = '';
				}
			}
		}


		//	Create onDocumentReady script
		$o = array();
		$c = array();


		//	Counters
		if ( $mm[ 'advanced' ][ 'counters' ] == 'yes' ) {
			$o[] = '
			counters: true';
		}


		//	Extensions
		$x = array();

		if ( $mm[ 'menu' ][ 'position' ] != 'left' ) {
			$x[] = 'position-' . $mm[ 'menu' ][ 'position' ];
		}

		if ( $mm[ 'menu' ][ 'theme' ] != 'light' ) {
			$x[] = 'theme-' . $mm[ 'menu' ][ 'theme' ];
		}

		if ( $mm[ 'advanced' ][ 'pageshadow' ] == 'yes' ) {
			$x[] = 'shadow-page';
		}

		if ( $mm[ 'advanced' ][ 'pagedim' ] == 'yes' ) {
			$x[] = 'pagedim-black';
		}

		if ( $mm[ 'advanced' ][ 'slidemenu' ] == 'yes' &&
			$mm[ 'menu' ][ 'position' ] != 'bottom' &&
			$mm[ 'menu' ][ 'position' ] != 'top'
		) {
			$x[] = 'fx-menu-slide';
		}

		if ( $mm[ 'advanced' ][ 'indentborder' ] != 'yes' ) {
			$x[] = 'border-full';
		}

		if ( $mm[ 'advanced' ][ 'truncatelistitems' ] != 'yes' ) {
			$x[] = 'multiline';
		}

		if ( $mm[ 'advanced' ][ 'fullscreen' ] == 'yes' ) {
			$x[] = 'fullscreen';
		}

		if ( count( $x ) > 0 ) {
			$o[] = '
			extensions: {
				"all": ["' . implode( '", "', $x ) . '"]
			}';
		}


		//	IconPanels
		if ( $mm[ 'header' ][ 'navigate' ] == 'iconpanels' ) {
			$o[] = '
			iconPanels: true';
		}


		//	OffCanvas
		$o[] = '
			offCanvas: {
				moveBackground: false
			}';


		//	Sidebar
		if ( $mm[ 'menu' ][ 'breakpoint' ] == __( 'Never', 'mmenu' ) ||
			 $mm[ 'menu' ][ 'position' ] != 'left'
		) {
			 $mm[ 'menu' ][ 'breakpoint' ] = '';
		}

		if ( $mm[ 'menu' ][ 'breakpoint' ] ) {
			$o[] = '
			sidebar: {
				expanded: "(min-width: ' . $mm[ 'menu' ][ 'breakpoint' ] . ')"
			}';
		}


		//	Searchfield
		$x = array();
		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' ) {
			if ( strlen( $mm[ 'header' ][ 'searchfield_placeholder' ] ) > 0 ) {
				$x[] = 'placeholder: "' . $mm[ 'header' ][ 'searchfield_placeholder' ] . '"';
			}

			if ( strlen( $mm[ 'header' ][ 'searchfield_noresults' ] ) > 0 ) {
				$x[] = 'noResults: "' . $mm[ 'header' ][ 'searchfield_noresults' ] . '"';
			}
		}

		if ( count( $x ) > 0 ) {
			$o[] = '
			searchfield: {
				' . implode( ",\n\t\t\t\t", $x ) . '
			}';
		}


		//	Navbar
		$x = array();
		if ( $mm[ 'header' ][ 'navigate' ] == 'button' ) {
			if ( strlen( $mm[ 'header' ][ 'navigate_title' ] ) > 0 ) {
				$x[] = 'title: "' . $mm[ 'header' ][ 'navigate_title' ] . '"';
			}
		}
		if ( $mm[ 'header' ][ 'navigate' ] == 'iconpanels' ||
			 $mm[ 'header' ][ 'navigate' ] == 'dropdown'
		) {
			$x[] = 'add: false';
		}

		if ( count( $x ) > 0 ) {
			$o[] = '
			navbar: {
				' . implode( ",\n\t\t\t\t", $x ) . '
			}';
		}


		//	Navbars
		$n = array();
		$tabs   = array();
		$o_tabs = explode( ',', $mm[ 'menu' ][ 'additional_menus'  ] );
		$titles = explode( ',', $mm[ 'menu' ][ 'additional_titles' ] );

		foreach( $o_tabs as $tab ) {
			if ( strlen( trim( $tab ) ) > 0 ) {
				$tabs[] = $tab;
			}
		}

		//	Header image
		if ( $mm[ 'header' ][ 'image' ] == 'yes' ) {
			if ( $mm[ 'header' ][ 'navigate' ] == 'iconpanels' ) {
				$available = 4;
			} else {
				$available = 3;
			}

			if ( count( $tabs ) > 0 ) {
				$available--;
			}

			if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' ) {
				$available--;
			}

			if ( strlen( $mm[ 'header' ][ 'buttons_html' ] ) > 0 ) {
				$available--;
			} else if ( strlen( $mm[ 'header' ][ 'buttons_selector' ] ) > 0 ) {
				$available--;
			} else {
				for ( $i = 1; $i <= 5; $i++ ) {
					if ( strlen( $mm[ 'header' ][ 'button_' . $i . '_icon' ] ) > 0 ) {
						$available--;
						break;
					}
				}
			}
			$height = intval( $mm[ 'header' ][ 'image_height' ] );
			if ( $available < $height ) {
				$height = $available;
			}

			if ( $height > 0 ) {
				$n[] = '{
					height: ' . $height . ',
					content: [ "<div class=\"wpmm-header-image\" />" ]
				}';
			}
		}


		//	Header searchfield
		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' ) {
			$n[] = '{
					content: [ "searchfield" ]
				}';
		}

		//	Additional menus
		if ( count( $tabs ) > 0 ) {
			$title = $mm[ 'header' ][ 'navigate_title' ];
			$title = strlen( $title ) > 0 ? trim( $title ) : 'Menu';
			$t = array( '<a href="#wpmm-panel-main">' . $title . '</a>' );

			for( $i = 0; $i < count( $tabs ); $i++ ) {
				$title = isset( $titles[ $i ] ) ? $titles[ $i ]  : '';
				$title = strlen( $title ) > 0   ? trim( $title ) : 'Menu ' . ( $i + 2 );
				$t[] = '<a href="#wpmm-panel-' . $i . '">' . $title . '</a>';
			}

			$n[] = '{
					content: [ \'' . implode( '\', \'', $t ) . '\' ],
					type: "tabs"
				}';
		}

		//	Header breadcrumbs
		if ( $mm[ 'header' ][ 'navigate' ] == 'breadcrumbs' ) {
			$n[] = '{
					content: [ "breadcrumbs" ]
				}';
		}

		//	Header back, title, close
		else if ( $mm[ 'header' ][ 'navigate' ] == 'button' ) {
			$x = array();
			$x[] = 'prev';
			$x[] = 'title';

			if ( $mm[ 'header' ][ 'navigate_close' ] == 'yes' ) {
				$x[] = 'close';
			}

			$n[] = '{
					content: [ "' . implode( '", "', $x ) . '" ]
				}';
		}

		//	Header + footer buttons
		foreach( array( 'header', 'footer' ) as $bar ) {
			$x = array();
			if ( strlen( $mm[ $bar ][ 'buttons_html' ] ) > 0 ) {
				$x[] = $mm[ $bar ][ 'buttons_html' ];
			}

			if ( strlen( $mm[ $bar ][ 'buttons_selector' ] ) > 0 ) {
				$x[] = $mm[ $bar ][ 'buttons_selector' ];
			}

			for ( $i = 1; $i <= 5; $i++ ) {
				if ( strlen( $mm[ $bar ][ 'button_' . $i . '_icon' ] ) > 0 ) {
					$href 	= $mm[ $bar ][ 'button_' . $i . '_href' ];
					$target = $mm[ $bar ][ 'button_' . $i . '_target' ];
					$icon 	= $mm[ $bar ][ 'button_' . $i . '_icon' ];

					if ( $target == '_blank'  ) {
						$target .= '" rel="noopener';
					}
					$x[] = '<a href="' 	. $href . '" target="' 	. $target . '">'
						. '<span class=" dashicons ' . $icon . '" >&nbsp;</span>'
						. '</a>';
				}
			}

			if ( count( $x ) > 0 ) {
				$n[] = '{
						position: "' . ( $bar == 'header' ? 'top' : 'bottom' ) . '",
						content: [ \'' . implode( '\', \'', $x ) . '\' ]
					}';
			}
		}

		if ( count( $n ) > 0 ) {
			$o[] = '
			navbars: [
				' . implode( ",", $n ) . '
			]';
		}

		//	Sliding submenus
		if ( $mm[ 'header' ][ 'navigate' ] == 'dropdown' ) {
			$o[] = '
			slidingSubmenus: false';
		}

		//	Accessibility
		if ( $mm[ 'accessibility' ][ 'keyboard' ] == 'yes' ) {
			$o[] = '
			keyboardNavigation: true';
		}


		//	Conf
		$c[] = '
			offCanvas: {
				pageSelector: "> div:not(#wpadminbar)"
			}';

		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' &&
			$mm[ 'header' ][ 'searchfield_sitesearch' ] == 'yes'
		) {
			$c[] = '
			searchfield: {
				form: {
					method: $sform.attr( \'method\' ) || \'get\',
					action: $sform.attr( \'action\' ) || \'/\'
				},
				input: {
					name: \'s\'
				},
				submit: true
			}';
		}


		//	Concatenate mmenu JS and CSS from originals

		$dir = dirname( dirname( __FILE__ ) ) . '/';
  		$src = $dir . 'mmenu/';

  		$js = @file_get_contents( $src . 'jquery.mmenu.all.js' );
  		$js .= '
jQuery(document).ready(function($) {
	$("#wpadminbar")
		.css( "position", "fixed" )
		.addClass( "mm-slideout" );

	var $menu 	= $("' . $mm[ 'setup' ][ 'menu' ] . '").first().clone(),
		$button = $("' . $mm[ 'setup' ][ 'button' ] . '");';

		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' &&
			$mm[ 'header' ][ 'searchfield_sitesearch' ] == 'yes'
		) {
			$js .= '

	var $sform = $(\'input[name="s"]\').closest( \'form\' ).first();';
		}

		if ( count( $tabs ) > 0 ) {
			$js .= '
	$menu.children( "ul" ).first().attr( "id", "wpmm-panel-main" );

	var $tabs  = $("' . implode( ', '	, $tabs 	) . '");
	var titles =  ["' . implode( '", "'	, $titles 	) . '"];
	$tabs.each(function( i ) {
		var $tab = $(this);
		if ( $tab.is( "div, ul, ol" ) )
		{
			$tab = $tab.clone();
		}
		else
		{
			$tab = $("<div />").html( $tab.children( "ul, ol" ).clone() );
		}

		$tab.attr( "id", "wpmm-panel-" + i );
		$tab.attr( "data-mm-title", titles[ i ] || "Menu " + ( i + 2 ) );
		$menu.append( $tab );
	});';
		}


		$c_panel 		= $mm[ 'advanced' ][ 'class_panel' ];
		$c_nopanel 		= $mm[ 'advanced' ][ 'class_noPanel' ];
		$c_vertical		= $mm[ 'advanced' ][ 'class_vertical' ];
		$c_nolistview	= $mm[ 'advanced' ][ 'class_noListview' ];
		$c_inset		= $mm[ 'advanced' ][ 'class_inset' ];
		$c_selected		= $mm[ 'advanced' ][ 'class_selected' ];
		$c_divider		= $mm[ 'advanced' ][ 'class_divider' ];
		$c_spacer		= $mm[ 'advanced' ][ 'class_spacer' ];

		$js .= '
	var $panels 		= ' . ( strlen( $c_panel 		) > 0 ? '$menu.find( "' . $c_panel 		. '" );' : '$();' ) . '
	var $nopanels 		= ' . ( strlen( $c_nopanel 		) > 0 ? '$menu.find( "' . $c_nopanel 	. '" );' : '$();' ) . '
	var $vertical 		= ' . ( strlen( $c_vertical 	) > 0 ? '$menu.find( "' . $c_vertical 	. '" );' : '$();' ) . '
	var $nolistview		= ' . ( strlen( $c_nolistview 	) > 0 ? '$menu.find( "' . $c_nolistview	. '" );' : '$();' ) . '
	var $inset			= ' . ( strlen( $c_inset 		) > 0 ? '$menu.find( "' . $c_inset		. '" );' : '$();' ) . '
	var $selected		= ' . ( strlen( $c_selected		) > 0 ? '$menu.find( "' . $c_selected	. '" );' : '$menu.find( "li.current-menu-item" );' ) . '
	var $divider		= ' . ( strlen( $c_divider 		) > 0 ? '$menu.find( "' . $c_divider	. '" );' : '$();' ) . '
	var $spacer			= ' . ( strlen( $c_spacer 		) > 0 ? '$menu.find( "' . $c_spacer		. '" );' : '$();' ) . '
	$menu.children().not( "div, ul, ol" ).remove();
	$menu.add( $menu.find( "div, ul, ol, li, a, span" ) )
		.off( "click" )
		.removeAttr( "class" )
		.not( "[id^=\'wpmm-\']" )
		.removeAttr( "id" );

	$menu.addClass( "wpmm-menu" );

	$panels.addClass( "Panel" );
	$nopanels.addClass( "NoPanel" );
	$vertical.addClass( "Vertical" );
	$nolistview.addClass( "NoListview" );
	$inset.addClass( "Inset" );
	$selected.addClass( "Selected" );
	$divider.addClass( "Divider" );
	$spacer.addClass( "Spacer" );


	$menu.mmenu(
		{' . implode( ",", $o ) . '
		}, {' . implode( ",", $c ) . '
		}
	);';

	if ( $mm[ 'advanced' ][ 'fullsubopen' ] == 'yes' ) {
		$js .= '

	$menu.find( "a.mm-listitem__text[href=\'#\']" )
		.each(function() {
			var $t = $(this);
			var $b = $t.parent().children( ".mm-listitem__btn" );

			$b.addClass( "mm-listitem__text" );
			$b.prepend( $t.text() );
			$t.remove();
		});';
	}

		$js .= '

	var api = $menu.data( "mmenu" );

	$button
		.addClass( "wpmm-button" )
		.off( "click" )
		.on( "click", function( e ) {
			e.preventDefault();
			e.stopImmediatePropagation();
			api.open();
		});';

		if ( $fileAffix == '-preview' ) {
			$js .= '
	$("body").on(
		"click",
		"a",
		function( e ) {
			if ( !e.isDefaultPrevented() )
			{
				if ( !confirm( "You are leaving the preview, changes you\'ve made to the mobile menu will no longer take effect." ) ) {
					e.preventDefault();
				}
			}
		}
	);
	setTimeout(function() {
		api.open();
	}, 1000);';
		}

		$js .= '

	function mm_hasBg( $e )
	{
		var bg = true;
		switch( $e.css( "background-color" ) )
		{
			case "":
			case "none":
			case "inherit":
			case "undefined":
			case "transparent":
			case "rgba(0,0,0,0)":
			case "rgba( 0,0,0,0 )":
			case "rgba(0, 0, 0, 0)":
			case "rgba( 0, 0, 0, 0 )":
				bg = false;
				break;
		}
		return bg;
	}

	var $node = $(".mm-page");
	if ( !mm_hasBg( $node ) )
	{
		$node.addClass( "wpmm-force-bg" );
		$node = $("body");
		if ( !mm_hasBg( $node ) )
		{
			$node.addClass( "wpmm-force-bg" );
			$node = $("html");
			if ( !mm_hasBg( $node ) )
			{
				$node.addClass( "wpmm-force-bg" );
			}
		}
	}
});';


  		$css = @file_get_contents( $src . 'jquery.mmenu.all.css' );
  		$css .= '
.wpmm-menu
{
	background-color: ' . $mm[ 'menu' ][ 'backgroundcolor' ] . ' !important;
	--mm-color-background: ' . $mm[ 'menu' ][ 'backgroundcolor' ] . ';
}
.wpmm-force-bg
{
	background-color: inherit;
}
html.wpmm-force-bg
{
	background-color: #fff;
}

.wpmm-menu .mm-navbar .dashicons
{
	font-size: 20px;
	line-height: 20px;
	height: 20px
}

.wpmm-menu .mm-listitem .dropdown-toggle,
.wpmm-menu .mm-listitem .icon-angle-down
{
	display: none;
}';

		//	Custom sizes
		if ( $mm[ 'menu' ][ 'item_size' ] != '48' ) {
			$css .= '
.wpmm-menu
{
	--mm-listitem-size:' . $mm[ 'menu' ][ 'item_size' ] . 'px;
	--mm-navbar-size:' . $mm[ 'menu' ][ 'item_size' ] . 'px;
	--mm-iconpanel-size: ' . $mm[ 'menu' ][ 'item_size' ] . 'px;
}';
		}

		//	Header
		if ( $mm[ 'header' ][ 'image' ] == 'yes' ) {
			$pos = ( $mm[ 'header' ][ 'image_scale' ] == 'cover' )
				? '0'
				: '20px';

			$css .= '

.wpmm-header-image
{
	background: url(' . $mm[ 'header' ][ 'image_src' ] . ') center center / ' . $mm[ 'header' ][ 'image_scale' ] . ' no-repeat transparent;
	padding: 0 !important;
	position: absolute;
	top: ' . $pos . ';
	right: ' . $pos . ';
	bottom: ' . $pos . ';
	left: ' . $pos . ';
}';
		}

  		//	Widescreen
  		if ( $mm[ 'menu' ][ 'breakpoint' ] ) {
			$css .= '
@media (min-width:' . $mm[ 'menu' ][ 'breakpoint' ] . 'px) {
	.wpmm-button ,
	' . $mm[ 'setup' ][ 'menu' ] . '
	{
		display: none !important;
	}
}
';
		}

		@file_put_contents( $dir . 'js/mmenu' . $fileAffix . '.js', $js );
		@file_put_contents( $dir . 'css/mmenu' . $fileAffix . '.css', $css );
	}

	/**
	 * Add the CSS and JS for the admin page.
	 */
	public function admin_css_js() {

	    wp_enqueue_style(	'wp-color-picker' );
		wp_enqueue_script(	'wp-color-picker' );
		wp_enqueue_style( 	'dashicons' );
		wp_enqueue_media();

		$translation_array = array(
			'save' 				=> __( 'Save', 'mmenu' ),
			'cancel'			=> __( 'Cancel', 'mmenu' ),
			'choose_an_image'	=> __( 'Choose an image', 'mmenu' ),
			'use_this_image' 	=> __( 'Use this image', 'mmenu' )
		);

		wp_register_style(	'mmenu', plugin_dir_url( dirname( __FILE__ ) ) . 'mmenu/jquery.mmenu.all.css' );
		wp_enqueue_style( 	'mmenu' );

		wp_register_script( 'mmenu', plugin_dir_url( dirname( __FILE__ ) ) . 'mmenu/jquery.mmenu.all.js' );
		wp_localize_script( 'mmenu', 'mmenu_i18n', $translation_array );
		wp_enqueue_script(	'mmenu' );

		wp_enqueue_style( 	'mm-admin', plugin_dir_url( dirname( __FILE__ ) ) . 'css/admin.css.php' );
		wp_enqueue_script(	'mm-admin', plugin_dir_url( dirname( __FILE__ ) ) . 'js/admin.js.php', array( 'jquery' ) );

		echo '
			<script type="text/javascript">
		    	var ' . $this->name . ' = {
		    		version: "' . $this->version . '",
		    		home_url: "' . get_home_url(). '"
		    	};
			</script>';
	}

	/** Load the textdomain for translations. */
	public function load_textdomain() {
		load_plugin_textdomain( 'mmenu', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
	}

}
