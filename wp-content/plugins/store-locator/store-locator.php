<?php
/**
 * Plugin Name: Store Locator
 * Plugin URI: 
 * Description: Store Locator React
 * Version: 1.0.0
 * Author: TharinduH
 * Author URI: 
 */

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