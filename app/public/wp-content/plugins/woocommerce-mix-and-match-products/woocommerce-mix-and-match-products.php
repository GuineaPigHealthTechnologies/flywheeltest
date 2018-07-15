<?php
/**
 * Plugin Name: WooCommerce Mix and Match
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Allow customers to choose products in any combination to fill a "container" of a specific size.
 * Version: 1.3.2
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Woo: 853021:e59883891b7bcd535025486721e4c09f
 * WC requires at least: 3.0.0
 * WC tested up to: 3.3.4
 *
 * Text Domain: woocommerce-mix-and-match-products
 * Domain Path: /languages
 *
 * @author Kathy Darling
 * @category Core
 * @package WooCommerce Mix and Match
 *
 * Copyright: © 2015 Kathy Darling and Manos Psychogyiopoulos
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ){
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'e59883891b7bcd535025486721e4c09f', '853021' );

// Quit right now if WooCommerce is not active.
if ( ! is_woocommerce_active() ){
	return;
}

/**
 * Main WC_Mix_and_Match class.
 *
 * The main instance of the plugin.
 * 
 * @since  1.0.0
 */
if ( ! class_exists( 'WC_Mix_and_Match' ) ) :

class WC_Mix_and_Match {

	/**
	 * The single instance of the class.
	 * 
	 * @var obj The WC_Mix_and_Match object
	 */
	protected static $_instance = null;

	/**
	 * Plugin Version.
	 * 
	 * @var str
	 */
	public $version      = '1.3.2';

	/**
	 * Required Version of WooCommerce.
	 * 
	 * @var str
	 */
	public $required_woo = '3.0.0';

	/**
	 * Main WC_Mix_and_Match instance.
	 *
	 * Ensures only one instance of WC_Mix_and_Match is loaded or can be loaded.
	 *
	 * @return WC_Mix_and_Match Single instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'woocommerce-mix-and-match-products' ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce-mix-and-match-products' ) );
	}

	/**
	 * WC_Mix_and_Match Constructor
	 *
     * @return 	WC_Mix_and_Match
	 */
	public function __construct() {

		// Load translation files.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Include required files.
		add_action( 'woocommerce_loaded', array( $this, 'includes' ) );

    }

	/*-----------------------------------------------------------------------------------*/
	/*  Helper Functions                                                                 */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the plugin base path name.
	 *
	 * @since  1.2.0
	 * 
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Load Files                                                                       */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes(){

		// Check we're running the required version of WC.
		if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, $this->required_woo, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			return false;
		}

		// Core compatibility functions and hooks.
		require_once( 'includes/compatibility/core/class-wc-mnm-core-compatibility.php' );

		// Install.
		require_once( 'includes/updates/class-wc-mnm-install.php' );

		// Include admin class to handle all back-end functions.
		if( is_admin() ){
			$this->admin_includes();
		}

		// Fucntions.
		require_once( 'includes/wc-mnm-functions.php' );

		// Data class.
		require_once( 'includes/data/class-wc-mnm-data.php' );

		// Display class.
		require_once( 'includes/class-wc-mnm-display.php' );
		$this->display = new WC_Mix_and_Match_Display();

		// Include the front-end functions.
		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {

			require_once( 'includes/class-wc-mnm-cart.php' );
			$this->cart = new WC_Mix_and_Match_Cart();

			require_once( 'includes/class-wc-mnm-stock-manager.php' );
		}

		// Product class.
		require_once( 'includes/class-wc-product-mix-and-match.php' );

		// Helpers.
		require_once( 'includes/class-wc-mnm-helpers.php' );

		// Include order-related functions.
		require_once( 'includes/class-wc-mnm-order.php' );

		$this->order = new WC_Mix_and_Match_Order();

		// Class containing extenstions compatibility functions and filters.
		require_once( 'includes/compatibility/class-wc-mnm-compatibility.php' );
		$this->compatibility = new WC_Mix_and_Match_Compatibility();

		// Include theme-level hooks and actions files.
		add_action( 'after_setup_theme', array( $this, 'theme_includes' ) );

		/**
		 * WooCommerce Mix and Match is fully loaded.
		 */
		do_action( 'woocommerce_mnm_loaded' );
	}

	/**
	 * Admin & AJAX functions and hooks.
	 *
	 * @since 1.2.0
	 */
	public function admin_includes() {

		// Admin notices handling.
		require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );

		// Admin menus and hooks.
		require_once( 'includes/admin/class-wc-mnm-admin.php' );
	}


	/**
	 * Displays a warning message if version check fails.
	 *
	 * @return string
	 */
	public function admin_notice() {
	    echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Mix & Match requires at least WooCommerce %s in order to function. Please upgrade WooCommerce.', 'woocommerce-mix-and-match-products' ), $this->required_woo ) . '</p></div>';
	}

	/**
	 * Include template functions and hooks.
	 */
	public function theme_includes(){
		require_once( 'includes/wc-mnm-template-functions.php' );
		require_once( 'includes/wc-mnm-template-hooks.php' );
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Localization                                                                     */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Make the plugin translation ready.
	 *
	 * Translations should be added in the WordPress language directory:
	 *      - WP_LANG_DIR/plugins/woocommerce-mix-and-match-products-LOCALE.mo
	 *
	 * @since  1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-mix-and-match-products' , false , dirname( plugin_basename( __FILE__ ) ) .  '/languages/' );
	}

} // End class: do not remove or there will be no more guacamole for you.

endif; // End class_exists check.


/**
 * Returns the main instance of WC_Mix_and_Match to prevent the need to use globals.
 *
 * @return WooCommerce
 */
function WC_Mix_and_Match() {
	return WC_Mix_and_Match::instance();
}

// Launch the whole plugin.
WC_Mix_and_Match();
