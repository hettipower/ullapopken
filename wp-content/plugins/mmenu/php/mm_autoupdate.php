<?php

class mm_auto_update
{
    /**
     * The plugin current version
     * @var string
     */
    public $current_version;
 
    /**
     * The plugin remote update path
     * @var string
     */
    public $update_path;
 
    /**
     * Plugin Slug (mmenu/mmenu.php)
     * @var string
     */
    public $plugin_slug;
 
    /**
     * Plugin name (mmenu)
     * @var string
     */
    public $slug;
 
    /**
     * License key
     * @var string
     */
    public $license_key;
 
    /**
     * Initialize a new instance of the WordPress Auto-Update class
     * @param string $update_path
     * @param string $plugin_slug
     * @param string $current_version
     * @param string $license_key
     * @param boolean $init
     */
    function __construct( $update_path, $plugin_slug, $current_version, $license_key, $init = true )
    {
        // Set the class public variables
        $this->update_path      = $update_path;
        $this->plugin_slug      = $plugin_slug;
        $this->current_version  = $current_version;
        $this->license_key      = $license_key;
        
        list( $t1, $t2 )        = explode( '/', $plugin_slug );
        $this->slug             = str_replace( '.php', '', $t2 );
        
        if ( $init )
        {
            // Define the alternative API for updating checking
            add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );
     
            // Define the alternative response for information checking
            add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
        }
    }
 
    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     * @return object $ transient
     */
    public function check_update( $transient )
    {

        // Get the remote version
        $remote_version = $this->getRemote_version();

        // If a newer version is available, add the update
        if ( version_compare( $this->current_version, $remote_version, '<' ) )
        {
            $obj = new stdClass();

            $obj->slug          = $this->slug;
            $obj->url           = $this->update_path;
            $obj->package       = $this->update_path;
            $obj->new_version   = $remote_version;
            $transient->response[ $this->plugin_slug ] = $obj;
        }

        return $transient;
    }
 
    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $false
     * @param array $action
     * @param object $arg
     * @return bool|object
     */
    public function check_info( $false, $action, $arg )
    {
        if ( isset( $arg->slug ) && $arg->slug === $this->slug )
        {
            $information = $this->getRemote_information();
            return $information;
        }
        return false;
    }
 
    /**
     * Return the remote version
     * @return string $remote_version
     */
    public function getRemote_version()
    {    
        $request = wp_remote_post( 
            $this->update_path, 
            array(
                'body' => array(
                    'action'  => 'version',
                    'license' => $this->license_key
                )
            )
        );

        if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 )
        {
            return $request[ 'body' ];
        }
        return false;
    }
 
    /**
     * Get information about the remote version
     * @return bool|object
     */
    public function getRemote_information()
    {
        $request = wp_remote_post(
            $this->update_path, 
            array(
                'body' => array(
                    'action'  => 'info',
                    'license' => $this->license_key
                )
            )
        );
        if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 )
        {
            return unserialize( $request[ 'body' ] );
        }
        return false;
    }
 
    /**
     * Return the status of the plugin licensing
     * @return boolean $remote_license
     */
    public function getRemote_license()
    {
        $request = wp_remote_post(
            $this->update_path, 
            array(
                'body' => array(
                    'action'  => 'license',
                    'license' => $this->license_key
                )
            )
        );
        if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 )
        {
            return $request[ 'body' ];
        }
        return false;
    }
}