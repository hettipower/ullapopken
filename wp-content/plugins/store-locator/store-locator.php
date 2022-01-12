<?php
/**
 * Plugin Name: Store Locator
 * Plugin URI: 
 * Description: Store Locator React. Use shortcode [store_locator]
 * Version: 1.0.0
 * Author: TharinduH
 * Author URI: 
 * Text Domain: store-locator
 */

define( 'STORE_LOCATOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'STORE_LOCATOR_URL', plugin_dir_url( __FILE__ ) );

require_once STORE_LOCATOR_PATH.'/inc/admin/post_type.php';
require_once STORE_LOCATOR_PATH.'/inc/admin/post_meta.php';
require_once STORE_LOCATOR_PATH.'/inc/admin/settings/stores_settings.php';
require_once STORE_LOCATOR_PATH.'/inc/front/api.php';

// Create the custom pages at plugin activation
function plugin_activated() {
    // Information needed for creating the plugin's pages
    $page_definitions = array(
        'storefinder' => array(
            'title' => __( 'Storefinder', 'store-locator' ),
            'content' => '[store_locator]'
        ),
    );
 
    foreach ( $page_definitions as $slug => $page ) {
        // Check that the page doesn't exist already
        $query = new WP_Query( 'pagename=' . $slug );
        if ( ! $query->have_posts() ) {
            // Add the page using the data from the array above
            wp_insert_post(
                array(
                    'post_content'   => $page['content'],
                    'post_name'      => $slug,
                    'post_title'     => $page['title'],
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'ping_status'    => 'closed',
                    'comment_status' => 'closed',
                )
            );
        }
    }
}
register_activation_hook( __FILE__, 'plugin_activated' );


// First register resources with init 
function store_locator_init() {
    /* $path = "/frontend/static";
    if(getenv('WP_ENV')=="development") {
        $path = "/frontend/build/static";
    } */
    $path = "/frontend/build/static";
    wp_register_script("store_locator_js", plugins_url($path."/js/main.js", __FILE__), array(), "1.0", false);
    wp_register_style("store_locator_css", plugins_url($path."/css/main.css", __FILE__), array(), "1.0", "all");
}
add_action( 'init', 'store_locator_init' );

// Function for the short code that call React app
function store_locator() {
    wp_enqueue_script("store_locator_js", '1.0', true);
    wp_enqueue_style("store_locator_css");
    return "<section class=\"sectionWrap\" id=\"storeFinderWrap\"></section>";
}
add_shortcode('store_locator', 'store_locator');

function stores_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'store' ) {
        if ( file_exists( STORE_LOCATOR_PATH . '/templates/single-store.php' ) ) {
            return STORE_LOCATOR_PATH . '/templates/single-store.php';
        }
    }

    return $single;

}
add_filter('single_template', 'stores_template');

function store_locator_scripts() {

    $stores_settings_options = get_option( 'stores_settings' );
    $google_api = $stores_settings_options['google_api'];
    $api_key = ( $google_api ) ? $google_api : 'AIzaSyC0_cw3dkaXJvqhZ6KHIj7_RF_Cf5Tj5p4' ;

    wp_enqueue_script('googleapis-js','https://maps.googleapis.com/maps/api/js?key='.$api_key.'&libraries=places', array('jquery'),'1.0',true);
    wp_enqueue_script('store-locator-js',STORE_LOCATOR_URL.'/assets/js/store-locator.min.js', array('jquery'),'1.0',true);

    $storesParams = array(
        'storeLocatorUrl' => STORE_LOCATOR_URL
    );
    wp_localize_script( 'store-locator-js', 'STORES_PARAMS', $storesParams );

}
add_action('wp_enqueue_scripts', 'store_locator_scripts');

function store_locator_styles() {
    global $wp_styles;
    wp_enqueue_style('store-locator-styles',STORE_LOCATOR_URL.'/assets/css/store-locator.min.css', array(),'1.0','screen');
}
add_action('wp_print_styles', 'store_locator_styles');