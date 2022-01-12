<?php

/**
 * Clear the caches for menus.
 */
function ccfm_clear_cache_for_menus() {
    ccfm_clear_cache_for_me( 'menu' );
}

/**
 * Clear the caches for widgets.
 */
function ccfm_clear_cache_for_widgets( $not_used = null ) {
    ccfm_clear_cache_for_me( 'widget' );
    return $not_used;
}

/**
 * Setup a filter to be called just before wp dies.
 */
function ccfm_clear_cache_for_widgets_wp_ajax_action() {
    // 'widgets-order', 'save-widget'
    add_filter( 'wp_die_ajax_handler', 'ccfm_clear_cache_for_widgets' );
}

/**
 * Clear cache for widgets when saving without js/ajax.
 */
function ccfm_clear_cache_for_widgets_sidebar_admin_setup() {
    // wp-admin/widgets.php
    // We're saving/deleting a widget without js/ajax
    if ( !empty( $_POST ) && ( isset($_POST['savewidget']) || isset($_POST['removewidget']) ) ) {
        ccfm_clear_cache_for_me( 'widget' );
    }
}

/**
 * Clear the cache when a theme customizations are saved (in Appearance->Customize)
 */
function ccfm_clear_cache_for_customized_theme() {
    ccfm_clear_cache_for_me( 'customize' );   
}

/**
 * Clear the cache when a settings are updated from any settings page.
 * Not ideal to do this in a filter, but one of the only available hooks to do this for just settings pages.
 */
function ccfm_clear_cache_for_settings( $settings_errors ) {
    //only clear cache when successfully updated
    if ( count( $settings_errors ) == 1 ) {
        $settings_error = $settings_errors[0];
        if ( isset( $settings_error['code'] ) && $settings_error['code'] == 'settings_updated' ) {
            ccfm_clear_cache_for_me( 'settings' );
        }
    }
    return $settings_errors;
}

/**
 * Clear cache when Contact Form 7 forms are saved.
 */
function ccfm_clear_cache_for_cf7() {
    ccfm_clear_cache_for_me( 'cf7' );
}

/**
 * Clear cache when WooThemes options are updated.
 */
function ccfm_clear_cache_for_woo_options() {
    ccfm_clear_cache_for_me( 'woo_options' );
}

/**
 * Clear cache when NextGen Gallery galleries and albums are updated.
 */
function ccfm_clear_cache_for_ngg() {
    ccfm_clear_cache_for_me( 'ngg' );
}

/**
 * Clear cache when Formdiable forms and settings are updated.
 */
function ccfm_clear_cache_for_formidable() {
    ccfm_clear_cache_for_me( 'formidable' );
}

/**
 * Clear cache when WPForms contact forms are updated.
 */
function ccfm_clear_cache_for_wpforms() {
    ccfm_clear_cache_for_me( 'wpforms' );
}

/**
 * Clear cache when WooCommerce is updated.
 */
function ccfm_clear_cache_for_woocommerce() {
    ccfm_clear_cache_for_me( 'woocommerce' );
}

/**
 * Clear cache when Insert Headers and Footers is updated.
 */
function ccfm_insert_headers_and_footers() {
    ccfm_clear_cache_for_me( 'insert-headers-and-footers' );
}

/**
 * Clear cache when ACF fields are updated
 */
function ccfm_acf_update_fields( $post_id, $post ) {
    if ( $post->post_type == 'acf-field-group' || $post->post_type == 'acf-field' ) {
        ccfm_clear_cache_for_me( 'acf_update_fields' );
        remove_action( 'save_post', 'ccfm_acf_update_fields', 10, 2 );
    }
}


/**
 * Add all urls to be purged and purge it in GoDaddy Cache.
 */
function ccfm_godaddy_purge() {
    if ( ! class_exists( '\WPaaS\Cache' ) ) {
        return;
    }

    if ( \WPaaS\Cache::has_ban() ) {

        return;

    }
    remove_action( 'shutdown', [ '\WPaaS\Cache', 'purge' ], PHP_INT_MAX );
    add_action( 'shutdown', [ '\WPaaS\Cache', 'ban' ], PHP_INT_MAX );
}

/**
 * Clear cache for wp activities like plugin updates, deletes, activations, core updates.
 */
function ccfm_clear_cache_for_wp_activity() {
    ccfm_clear_cache_for_me( 'wp_activity' );
}

/*** code for if qode was outside of theme and not saved by saving options. ***/
/**
 * "clear cache" for custom qode js and css files.
 */
/*
function ccfm_clear_cache_for_css_js_qode( $time ) {
    update_option( '_ccfm_style_timestamp_theme_qode', $time );
}
add_action( 'ccfm_clear_cache_for_css_js', 'ccfm_clear_cache_for_css_js_qode' );
*/
/**
 * Save a timestamp whenever the qode options are saved.
 */
/*
function ccfm_save_clear_cache_for_qode( $option_name, $old_value, $value ) {
    if ( stripos( $option_name, 'qode_options_' ) === 0 ) {
        ccfm_clear_cache_for_css_js_qode();
    }
}
*/
/**
 * Return all urls belonging to qode.
 */
/*
function ccfm_custom_src_urls_qode( $arr ) {
    if ( defined( 'QODE_ROOT' ) ) {
        $arr[QODE_ROOT."/css/custom_css.php"] = 'theme_qode';
        $arr[QODE_ROOT."/js/custom_js.php"] = 'theme_qode';
    }
    return $arr;
}
add_filter( 'ccfm_custom_src_urls', 'ccfm_custom_src_urls_qode' );
*/

