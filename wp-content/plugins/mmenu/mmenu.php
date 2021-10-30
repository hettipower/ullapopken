<?php
/**
 * Plugin Name: mmenu - App look-alike menus
 * Plugin URI: http://mmenujs.com/wordpress-plugin
 * Description: The best WordPress plugin for app look-alike off-canvas mega menus with sliding submenus for your WordPress website.
 * Version: 2.9.2
 * Author: Fred Heusschen
 * Author URI: http://www.frebsite.nl
 */

require_once( dirname( __FILE__ ) . '/php/mm_autoupdate.php' );

if ( is_admin() )
{
	require_once( dirname( __FILE__ ) . '/php/mmenu_backend.php' );
	new MmenuBackend();
}
else
{
	require_once( dirname( __FILE__ ) . '/php/mmenu_frontend.php' );
	new MmenuFrontend();
}

