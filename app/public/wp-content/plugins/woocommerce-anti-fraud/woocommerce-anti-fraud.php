<?php
/*
 * Plugin Name: WooCommerce Anti Fraud
 * Plugin URI: https://woocommerce.com/products/woocommerce-anti-fraud/
 * Description: Score each of your transactions, checking for possible fraud, using a set of advanced scoring rules.
 * Version: 1.0.15
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GPL v3
 * WC tested up to: 3.4
 * WC requires at least: 2.6
 * Woo: 500217:955da0ce83ea5a44fc268eb185e46c41
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright (c) 2017 WooCommerce.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '955da0ce83ea5a44fc268eb185e46c41', '500217' );

define( 'WOOCOMMERCE_ANTI_FRAUD_VERSION', '1.0.15' );

class WooCommerce_Anti_Fraud {

	/**
	 * Get the plugin file
	 *
	 * @static
	 * @since  1.0.0
	 * @access public
	 *
	 * @return String
	 */
	public static function get_plugin_file() {
		return __FILE__;
	}

	/**
	 * A static method that will setup the autoloader
	 *
	 * @static
	 * @since  1.0.0
	 * @access private
	 */
	private static function setup_autoloader() {
		require_once( plugin_dir_path( self::get_plugin_file() ) . '/includes/class-wc-af-privacy.php' );
		require_once( plugin_dir_path( self::get_plugin_file() ) . '/includes/class-wc-af-autoloader.php' );

		// Core loader
		$core_autoloader = new WC_AF_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'anti-fraud-core/' );
		spl_autoload_register( array( $core_autoloader, 'load' ) );

		// Rule loader
		$rule_autoloader = new WC_AF_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'rules/' );
		spl_autoload_register( array( $rule_autoloader, 'load' ) );
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// Check if WC is activated
		if ( $this->is_wc_active() ) {
			$this->init();
		}
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	private function is_wc_active() {

		$is_active = WC_Dependencies::woocommerce_active_check();

		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}

		return $is_active;
	}

	/**
	 * Display the notice
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p><?php printf( __( 'Please install and activate %sWooCommerce%s in order for the WooCommerce Anti Fraud extension to work!', 'woocommerce-anti-fraud' ), '<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">', '</a>' ); ?></p>
		</div>
	<?php
	}

	/**
	 * Init the plugin
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 */
	private function init() {

		// Load plugin textdomain
		load_plugin_textdomain( 'woocommerce-anti-fraud', false, plugin_dir_path( self::get_plugin_file() ) . 'languages/' );

		// Setup the autoloader
		self::setup_autoloader();

		// Setup the required WooCommerce hooks
		WC_AF_Hook_Manager::setup();

		// Add base rules
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Country() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Billing_Matches_Shipping() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Temporary_Email() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Free_Email() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_International_Order() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_High_Value() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Detect_Proxy() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Ip_Location() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_First_Order() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Ip_Multiple_Order_Details() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Velocities() );

		// Check if admin
		if ( is_admin() ) {

			// Setup Settings
			$settings = new WC_AF_Settings();
			$settings->setup();

		}

	}

}

function __woocommerce_anti_fraud_main() {
	new WooCommerce_Anti_Fraud();
}

// Create object - Plugin init
add_action( 'plugins_loaded', '__woocommerce_anti_fraud_main' );
