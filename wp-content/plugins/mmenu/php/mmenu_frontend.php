<?php

class MmenuFrontend {

	public function __construct()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'wp_nav_menu_args', array( $this, 'nav_menu_args' ) );
	}

	public function nav_menu_args( $args )
	{
		if ( !$args[ 'container_id' ] )
		{
			$args[ 'container_id' ] = 'menu-location-' . $args[ 'theme_location' ];
		}
		return $args;
	}

	public function enqueue_scripts()
	{
		$version = get_option( 'mm_setup', array() );
		$version = isset( $version[ 'version' ] ) ? $version[ 'version' ] : 0;

		wp_enqueue_style( 'dashicons' );

		if ( current_user_can( 'manage_options' ) &&
			isset( $_GET[ 'mmenu' ] )
		) {
			if ( $_GET[ 'mmenu' ] == 'locate' )
			{
				$translation_array = array(
					'click_to_select'	=> __( 'Click to select', 'mmenu' ),
					'sorry_0_found'		=> __( 'Sorry, no elements found.', 'mmenu' )
				);
				wp_enqueue_script( 	'mm-locate', plugin_dir_url( dirname( __FILE__ ) ) . 'lib/locate/admin-locate.js', array( 'jquery' ) );
		   		wp_enqueue_style( 	'mm-locate', plugin_dir_url( dirname( __FILE__ ) ) . 'lib/locate/admin-locate.css' );
				wp_localize_script( 'mm-locate', 'mmenu_i18n', $translation_array );
			}
			else if ( $_GET[ 'mmenu' ] == 'breakpoint' )
			{
				wp_enqueue_script( 	'mm-breakpoint', plugin_dir_url( dirname( __FILE__ ) ) . 'lib/breakpoint/admin-breakpoint.js', array( 'jquery' ) );
			}
			else if ( $_GET[ 'mmenu' ] == 'preview' )
			{
		   		wp_enqueue_script( 	'mmenu', plugin_dir_url( dirname( __FILE__ ) ) . 'js/mmenu-preview.js', array( 'jquery' ), $version );
		   		wp_enqueue_style( 	'mmenu', plugin_dir_url( dirname( __FILE__ ) ) . 'css/mmenu-preview.css', '', $version );
			}

			add_action( 'wp_footer', array( $this, 'echo_mmenu_js' ) );
		}
		else
		{
			$menu = get_option( 'mm_menu', false );
			if ( $menu )
			{
			   	wp_enqueue_script( 	'mmenu', plugin_dir_url( dirname( __FILE__ ) ) . 'js/mmenu.js', array( 'jquery' ), $version );
			   	wp_enqueue_style( 	'mmenu', plugin_dir_url( dirname( __FILE__ ) ) . 'css/mmenu.css', '', $version );
			}
		}
    }
	public function echo_mmenu_js()
	{
		echo '
<script type="text/javascript">
	window.mmenu = {};
</script>';
	}
}
