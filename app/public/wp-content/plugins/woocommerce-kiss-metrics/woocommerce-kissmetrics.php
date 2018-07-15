<?php
/**
 * Plugin Name: WooCommerce Kissmetrics
 * Plugin URI: http://www.woocommerce.com/products/kiss-metrics/
 * Description: Adds Kissmetrics tracking to WooCommerce with one click!
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 1.11.3
 * Text Domain: woocommerce-kiss-metrics
 * Domain Path: /i18n/languages
 *
 * Copyright: (c) 2012-2018, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-KISSmetrics
 * @author    SkyVerge
 * @category  Integration
 * @copyright Copyright (c) 2012-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 27146:d4e3376922b693659e176e8ebc834104
 * WC requires at least: 2.6.14
 * WC tested up to: 3.4.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'd4e3376922b693659e176e8ebc834104', '27146' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.9.0', __( 'WooCommerce KISSmetrics', 'woocommerce-kiss-metrics' ), __FILE__, 'init_woocommerce_kiss_metrics', array(
	'minimum_wc_version'   => '2.6.14',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_kiss_metrics() {


/**
 * # WooCommerce Kissmetrics Main Plugin Class
 *
 * ## Plugin Overview
 *
 * This plugin adds Kissmetrics tracking to many different WooCommerce events, like adding a product to the cart or completing
 * a purchase. Admins can control the name of the events and properties sent to Kissmetrics in the integration settings section.
 *
 * ## Admin Considerations
 *
 * The plugin is added as an integration, so all settings exist inside the integrations section (WooCommerce > Settings > Integrations)
 *
 * ## Frontend Considerations
 *
 * The Kissmetrics tracking javascript is added to the <head> of every page load
 *
 * ## Database
 *
 * ### Global Settings
 *
 * + `wc_kissmetrics_settings` - a serialized array of Kissmetrics integration settings, include API credentials and event/property names
 *
 * ### Options table
 *
 * + `wc_kissmetrics_version` - the current plugin version, set on install/upgrade
 *
 */
class WC_Kissmetrics extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.11.3';

	/** @var WC_Kissmetrics single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'kiss_metrics';

	/** @var \WC_Kissmetrics_Subscriptions_Integration instance */
	protected $subscriptions_integration;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.2
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'        => 'woocommerce-kiss-metrics',
				'display_php_notice' => true,
			)
		);

		// load integration
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'includes' ) );
	}


	/**
	 * Include required files
	 *
	 * @since 1.2
	 */
	public function includes() {

		require_once( $this->get_plugin_path(). '/includes/class-wc-kissmetrics-integration.php' );

		if ( $this->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {
			$this->subscriptions_integration = $this->load_class( '/includes/class-wc-kissmetrics-subscriptions-integration.php', 'WC_Kissmetrics_Subscriptions_Integration' );
		}

		add_filter( 'woocommerce_integrations', array( $this, 'load_integration' ) );
	}


	/**
	 * Add Kissmetrics to the list of integrations WooCommerce loads
	 *
	 * @since 1.2
	 */
	public function load_integration( $integrations ) {

		$integrations[] = 'WC_Kissmetrics_Integration';

		return $integrations;
	}


	/**
	 * Return Subscriptions integration class instance
	 *
	 * @since 1.8.0
	 * @return \WC_Kissmetrics_Subscriptions_Integration
	 */
	public function get_subscriptions_integration_instance() {
		return $this->subscriptions_integration;
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Kissmetrics Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.4.0
	 * @see wc_kissmetrics()
	 * @return WC_Kissmetrics
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.3
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Kissmetrics', 'woocommerce-kiss-metrics' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.3
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation url, which for Customer/Order CSV Export is non-standard
	 *
	 * @since 1.3.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'http://docs.woocommerce.com/document/kiss-metrics/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.6.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 1.3
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=integration&section=kissmetrics');
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 1.3
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'] ) && 'wc-settings' == $_GET['page'] &&
		isset( $_GET['tab'] ) && 'integration' == $_GET['tab'] &&
		isset( $_GET['section'] ) && 'kissmetrics' == $_GET['section'];
	}


	/**
	 * Returns the instance of WC_KissMetrics_Integration, the integration class
	 *
	 * @since 1.6.0
	 * @return WC_KissMetrics_Integration The integration class instance
	 */
	public function get_integration() {

		$integrations = WC()->integrations->get_integrations();

		return $integrations['kissmetrics'];
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Perform any version-related changes.
	 *
	 * @since 1.5.0
	 * @see SV_WC_Plugin::upgrade()
	 * @param int $installed_version the currently installed version of the plugin
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to 1.5.0
		if ( version_compare( $installed_version, '1.5.0', '<' ) ) {

			// get settings
			$settings = get_option( 'woocommerce_kissmetrics_settings', array() );

			// set option defaults to avoid notices ;(
			$settings['purchased_product_sku_property_name']      = 'purchased product sku';
			$settings['purchased_product_name_property_name']     = 'purchased product name';
			$settings['purchased_product_category_property_name'] = 'purchased product category';
			$settings['purchased_product_price_property_name']    = 'purchased product price';
			$settings['purchased_product_qty_property_name']      = 'purchased product quantity';

			update_option( 'woocommerce_kissmetrics_settings', $settings );
		}

		// upgrade to 1.5.1
		if ( version_compare( $installed_version, '1.5.1', '<' ) ) {

			// get settings
			$settings = get_option( 'woocommerce_kissmetrics_settings', array() );

			// ensure total_initial_payment_property_name is not set to "subscription name"
			if ( 'subscription name' === $settings['total_initial_payment_property_name'] ) {

				$settings['total_initial_payment_property_name'] = 'total initial payment';

				update_option( 'woocommerce_kissmetrics_settings', $settings );
			}
		}

		// upgrade to 1.6.0
		if ( version_compare( $installed_version, '1.6.0', '<' ) ) {

			// get settings
			$settings = get_option( 'woocommerce_kissmetrics_settings', array() );

			$settings['completed_payment_event_name'] = 'completed payment';

			update_option( 'woocommerce_kissmetrics_settings', $settings );
		}

		// upgrade to 1.6.1
		if ( version_compare( $installed_version, '1.6.1', '<' ) ) {

			// add product price property name
			$settings = get_option( 'woocommerce_kissmetrics_settings', array() );

			$settings['product_price_property_name'] = 'product price';

			if ( $this->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {
				$settings['subscription_id_property_name']               = 'subscription id';
				$settings['subscription_price_property_name']            = 'subscription price';
				$settings['subscription_end_of_prepaid_term_event_name'] = 'subscription prepaid term ended';
			}

			update_option( 'woocommerce_kissmetrics_settings', $settings );

			// indicate legacy mode
			update_option( 'wc_kissmetrics_is_legacy', 1 );
		}
	}

} // end \WC_Kissmetrics


/**
 * Returns the One True Instance of Kissmetrics
 *
 * @since 1.4.0
 * @return WC_Kissmetrics
 */
function wc_kissmetrics() {
	return WC_Kissmetrics::instance();
}


// fire it up!
wc_kissmetrics();

} // init_woocommerce_kiss_metrics()
