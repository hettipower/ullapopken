<?php
/**
 * Autoloader class.
 */
class Pwf_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @var string
	 */
	private $php_classes = '';

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}
		add_action( 'init', array( $this, 'woocommerce_loaded' ) );
	}

	/**
	 * load_file( $path )
	 *
	 * @param  string $path File path.
	 * @return bool Successful or not.
	 */
	public static function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;
			return true;
		}
		return false;
	}

	public function woocommerce_loaded() {
		$this->classes = $this->get_autoload_classes();
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption.
	 *
	 * @param string $class Class name.
	 */
	public function autoload( $class ) {
		$path  = '';
		$class = strtolower( $class );
		if ( ! array_key_exists( $class, $this->classes ) ) {
			return;
		}

		$path = $this->classes[ $class ];
		if ( ! empty( $path ) ) {
			self::load_file( $path );
		}
	}

	private function get_autoload_classes() {
		$classes = array(
			'pwf_walker'                        => PWF_WOO_FILTER_DIR . 'includes/walker/class-pwf-walker.php',
			'pwf_meta_data'                     => PWF_WOO_FILTER_DIR . 'includes/admin/class-pwf-meta-data.php',
			'pwf_meta_fields'                   => PWF_WOO_FILTER_DIR . 'includes/admin/class-pwf-meta-fields.php',
			'pwf_filter_products'               => PWF_WOO_FILTER_DIR . 'includes/class-pwf-filter-products.php',
			'pwf_database_query'                => PWF_WOO_FILTER_DIR . 'includes/admin/class-pwf-database-query.php',
			'pwf_parse_query_vars'              => PWF_WOO_FILTER_DIR . 'includes/class-pwf-parse-query-vars.php',
			'pwf_render_filter_fields'          => PWF_WOO_FILTER_DIR . 'includes/class-pwf-render-filter-fields.php',
			'pwf_walker_radio'                  => PWF_WOO_FILTER_DIR . 'includes/walker/class-pwf-walker-radio.php',
			'pwf_walker_checkbox'               => PWF_WOO_FILTER_DIR . 'includes/walker/class-pwf-walker-checkbox.php',
			'pwf_walker_textlist'               => PWF_WOO_FILTER_DIR . 'includes/walker/class-pwf-walker-textlist.php',
			'pwf_walker_dropdown_list'          => PWF_WOO_FILTER_DIR . 'includes/walker/class-pwf-walker-dropdow-list.php',
			'pwf_api_prepare_filter_post'       => PWF_WOO_FILTER_DIR . 'includes/class-pwf-api-prepare-filter-post.php',
			'pwf_integrate_shortcode'           => PWF_WOO_FILTER_DIR . 'includes/class-pwf-integrate-shortcode.php',
			'pwf_clear_transients'              => PWF_WOO_FILTER_DIR . 'includes/admin/class-pwf-clear-transients.php',
			'pwf_integrate_ajax_search_for_woo' => PWF_WOO_FILTER_DIR . 'includes/compitable-plugins/ajax-search-for-woocommerce/class-pwf-integrate-ajax-search-for-woo.php',
		);

		return $classes;
	}
}

new Pwf_Autoloader();
