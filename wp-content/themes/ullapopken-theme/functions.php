<?php
/***********************************************************************************************************************/
/***********************************************define constants******************************************************/
/***********************************************************************************************************************/
define('THEME_SITE_URL',home_url());
define('THEME_THEMEROOT',get_stylesheet_directory_uri()); 
define('THEME_IMAGES',THEME_THEMEROOT.'/assets/images');
define('THEME_JS',THEME_THEMEROOT.'/assets/js');
define('THEME_CSS',THEME_THEMEROOT.'/assets/css');
define('THEME_THEMEROOT_PATH',get_template_directory()); 

/**********************************************************************************************************************/
/****************************************defines theme supports*******************************************************/
/**********************************************************************************************************************/
if(function_exists('add_theme_support')){
    add_theme_support('menus');
    add_theme_support( 'post-thumbnails');
    add_theme_support( 'html5', array( 'search-form' ) );
}
//widget positions
if ( function_exists ('register_sidebar')) {
    register_sidebar (array(
        'name'=>'Footer Widget 1',
        'id' => 'footer-widget-1',
        'class' =>'footer-widget',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => "</h3>",
    ));

    register_sidebar (array(
        'name'=>'Footer Widget 2',
        'id' => 'footer-widget-2',
        'class' =>'footer-widget',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => "</h3>",
    ));
}
/*********************************************************************************************************************/
/************************************admin scripts and styles for the theme*******************************************/
/*********************************************************************************************************************/
function theme_front_scripts() {
    wp_enqueue_script('jquery');

    $api_key = ( get_field( 'google_api' , 'option' ) ) ? get_field( 'google_api' , 'option' ) : 'AIzaSyB_DH4yRoGB0aoM3IZFvWOIP2qNbFh_bIs' ;

    wp_enqueue_script('googleapis-js','https://maps.googleapis.com/maps/api/js?key='.$api_key, array('jquery'),'1.0',true);
    wp_enqueue_script('custom-js',THEME_JS.'/custom-combined.min.js', array('jquery'),'1.0',true);

    $customParams = array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    );
    wp_localize_script( 'custom-js', 'CUSTOM_PARAMS', $customParams );

}
add_action('wp_enqueue_scripts', 'theme_front_scripts');

// custom front styles
function theme_front_styles() {
    global $wp_styles;
    wp_enqueue_style('theme-styles',THEME_THEMEROOT.'/style.css', array(),'1.0','screen');
    wp_enqueue_style('master-styles',THEME_CSS.'/master.min.css', array(),'1.0','screen');
}
add_action('wp_print_styles', 'theme_front_styles');


/**********************************************************************************************************************/
/************************************************Body Classes*********************************************************/
/**********************************************************************************************************************/

add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

    if($is_lynx) $classes[] = 'lynx';
    elseif($is_gecko) $classes[] = 'gecko';
    elseif($is_opera) $classes[] = 'opera';
    elseif($is_NS4) $classes[] = 'ns4';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE) $classes[] = 'ie';
    else $classes[] = 'unknown';

    if($is_iphone) $classes[] = 'iphone';
    return $classes;
}

/************************************ Mails Settings *******************************/

add_filter( 'wp_mail_content_type', 'set_content_type' );
function set_content_type( $content_type ) {
    return 'text/html';
}

add_filter("wp_mail_from_name", "theme_filter_wp_mail_from_name");
function theme_filter_wp_mail_from_name($from_name){
    return get_bloginfo( 'name' );
}

add_filter("wp_mail_from", "theme_filter_wp_mail_from");
function theme_filter_wp_mail_from($email){
    /* start of code lifted from wordpress core, at http://svn.automattic.com/wordpress/tags/3.4/wp-includes/pluggable.php */
    $sitename = strtolower( $_SERVER['SERVER_NAME'] );
    if ( substr( $sitename, 0, 4 ) == 'www.' ) {
        $sitename = substr( $sitename, 4 );
    }
    /* end of code lifted from wordpress core */
    $myfront = "noreply@";
    $myback = $sitename;
    $myfrom = $myfront . $myback;
    return $myfrom;
}

/**********************************************************************************************************************/
/*********************************************defines include*********************************************************/
//Required Plugin
require_once ('inc/admin/tgm-plugin-activation/class-tgm-plugin-activation.php');
require_once ('inc/lib/Mobile_Detect.php');
require_once ('assets/vendor/autoload.php');

require_once ('inc/admin/post_types.php');

require_once ('inc/front/theme_misc.php');

add_action( 'tgmpa_register', 'theme_required_plugins_register' );
function theme_required_plugins_register() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        array(
            'name'      => 'Smush Image Compression and Optimization',
            'slug'      => 'wp-smushit',
            'required'  => true,
        ),
        array(
            'name'                  => 'Advanced Custom Fields Pro', // The plugin name
            'slug'                  => 'advanced-custom-fields-pro', // The plugin slug (typically the folder name)
            'source'                => THEME_THEMEROOT . '/inc/admin/tgm-plugin-activation/plugins/advanced-custom-fields-pro.zip', // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),
        array(
            'name'                  => 'Advanced Custom Fields: Theme Code Pro', // The plugin name
            'slug'                  => 'acf-theme-code-pro', // The plugin slug (typically the folder name)
            'source'                => THEME_THEMEROOT . '/inc/admin/tgm-plugin-activation/plugins/acf-theme-code-pro.zip', // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),
    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'page_title'   => __( 'Install Required Plugins', 'maya-creations' ),
        'id'           => 'maya-creations',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'plugins.php',            // Parent menu slug.
        'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        
    );

    tgmpa( $plugins, $config );
}

function theme_remove_cssjs_ver( $src ) {
  if( strpos( $src, '?ver=' ) )
    $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'theme_remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'theme_remove_cssjs_ver', 10, 2 );

//Options Page
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
		'page_title' 	=> 'Global Data',
		'menu_title'	=> 'Global Data',
		'menu_slug' 	=> 'theme-global-data'
	));
    
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'parent_slug'	=> 'theme-global-data',
    ));

    acf_add_options_page(array(
        'page_title'    => 'Shop Settings',
        'menu_title'    => 'Shop Settings',
        'parent_slug'	=> 'theme-global-data',
    ));
    
}

function my_acf_init() {
    $api_key = ( get_field( 'google_api' , 'option' ) ) ? get_field( 'google_api' , 'option' ) : 'AIzaSyB_DH4yRoGB0aoM3IZFvWOIP2qNbFh_bIs' ;
    acf_update_setting('google_api_key', $api_key );
}
add_action('acf/init', 'my_acf_init');

add_action('init' , 'compile_less_to_css');
function compile_less_to_css(){

    $less = new lessc;

    $less->compileFile(THEME_THEMEROOT_PATH.'/assets/css/less/master.less' , THEME_THEMEROOT_PATH.'/assets/css/master.css' );

}

use MatthiasMullie\Minify;

add_action( 'init', 'minify_css_func');
function minify_css_func() {

    $master = get_theme_file_path('assets/css/master.css');
    $minifier = new Minify\CSS($master);

    // save minified file to disk
    $minifiedPath = get_theme_file_path('assets/css/master.min.css');
    $minifier->minify($minifiedPath);

}

add_action( 'init', 'minify_js_func');
function minify_js_func() {

    $bootstrap = get_theme_file_path('assets/js/bootstrap.bundle.min.js');
    $minifier = new Minify\JS($bootstrap);

    // we can even add another file, they'll then be
    // joined in 1 output file

    $acf_map = get_theme_file_path('assets/js/acf_map.js');
    $minifier->add($acf_map);

    $OwlCarousel2 = get_theme_file_path('assets/js/owl.carousel.min.js');
    $minifier->add($OwlCarousel2);

    $custom = get_theme_file_path('assets/js/custom.js');
    $minifier->add($custom);

    // save minified file to disk
    $minifiedPath = get_theme_file_path('assets/js/custom-combined.min.js');
    $minifier->minify($minifiedPath);

}