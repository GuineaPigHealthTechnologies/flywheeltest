<?php
/**
 * Plugin Name: WooCommerce GoCardless Gateway
 * Plugin URI: https://www.woocommerce.com/products/gocardless/
 * Description: Extends both WooCommerce and WooCommerce Subscriptions with the GoCardless Payment Gateway. A GoCardless merchant account is required.
 * Version: 2.4.7
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * WC tested up to: 3.3
 * WC requires at least: 2.6
 *
 * Copyright: © 2009-2017 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 18681:249e4aba039ba8a822cae7b20a79b380
 *
 * @package WC_GoCardless
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '249e4aba039ba8a822cae7b20a79b380', '18681' );

/**
 * Plugin main class.
 */
class WC_GoCardless {

	/**
	 * Plugin's version.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	public $version = '2.4.7';

	/**
	 * Plugin's absolute path.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Plugin's URL.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Plugin's settings.
	 *
	 * @since 2.4.0
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger
	 */
	private $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		define( 'WC_GOCARDLESS_MAIN_FILE', __FILE__ );

		// Actions.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_action( 'admin_notices', array( $this, 'environment_check' ) );

		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		$this->plugin_url  = untrailingslashit( plugins_url( '/', __FILE__ ) );

		require_once( $this->plugin_path . '/includes/class-wc-gocardless-api.php' );

		$this->settings = WC_GoCardless_API::get_settings();
	}

	/**
	 * Add relevant links to plugins page
	 *
	 * @since 1.0.0
	 * @version 2.4.6
	 *
	 * @param  array $links Plugin action links.
	 * @return array Plugin action links
	 */
	public function plugin_action_links( $links ) {
		if ( ! function_exists( 'WC' ) ) {
			return $links;
		}

		$setting_url = $this->get_setting_url();

		$plugin_links = array(
			'<a href="' . esc_url( $setting_url ) . '">' . esc_html__( 'Settings', 'woocommerce-gateway-gocardless' ) . '</a>',
			'<a href="https://support.woocommerce.com/">' . esc_html__( 'Support', 'woocommerce-gateway-gocardless' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/gocardless/">' . esc_html__( 'Docs', 'woocommerce-gateway-gocardless' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Get setting URL.
	 *
	 * @since 2.3.8
	 *
	 * @return string Setting URL
	 */
	public function get_setting_url() {
		$section = 'gocardless';

		// Legacy.
		if ( version_compare( WC()->version, '2.6', '<' ) ) {
			$section = class_exists( 'WC_Subscriptions_Order' ) ? 'wc_gateway_gocardless_subscription' : 'wc_gateway_gocardless';
		}

		return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $section );
	}

	/**
	 * Checks whether gateway addons can be used.
	 *
	 * @since 2.4.0
	 *
	 * @return bool Returns true if gateway addons can be used
	 */
	public function can_use_gateway_addons() {
		return (
			( class_exists( 'WC_Subscriptions_Order' ) && function_exists( 'wcs_create_renewal_order' ) )
			||
			class_exists( 'WC_Pre_Orders_Order' )
		);
	}

	/**
	 * Init localisations and files.
	 */
	public function init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		$this->_maybe_display_connect_notice();

		// Includes.
		require_once( $this->plugin_path . '/includes/class-wc-payment-token-direct-debit.php' );
		require_once( $this->plugin_path . '/includes/class-wc-gateway-gocardless.php' );

		if ( $this->can_use_gateway_addons() ) {
			include_once( $this->plugin_path . '/includes/class-wc-gateway-gocardless-addons.php' );
		}

		// Localisation.
		load_plugin_textdomain( 'woocommerce-gateway-gocardless', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		add_action( 'init', array( $this, 'init_order_admin' ) );
	}

	/**
	 * Maybe display connect notice for merchants who have access token for
	 * legacy API and merchants that have the extension activated but never set
	 * API credentials before.
	 *
	 * @since 2.4.0
	 */
	protected function _maybe_display_connect_notice() {
		$current_version = get_option( 'woocommerce_gocardless_version' );

		if ( version_compare( $current_version, '2.4.0', '<' ) ) {
			$this->_display_connect_notice();
			$this->_migrate_old_settings();
		}

		update_option( 'woocommerce_gocardless_version', $this->version );
	}

	/**
	 * Display connect notice.
	 *
	 * @since 2.4.0
	 */
	protected function _display_connect_notice() {
		// For merchants that don't have access token yet.
		$message = sprintf( __( 'GoCardless is almost ready. To get started, please <a href="%s">connect your GoCardless account</a>.', 'woocommerce-gateway-gocardless' ), $this->get_setting_url() );

		// For merchants that, maybe, have access token from legacy API.
		if ( ! empty( $this->settings['access_token'] ) ) {
			/* translators: Plugin's version (as in x.y.z) and settings URL */
			$message = sprintf( __( 'GoCardless %1$s requires new access token to work with the latest API. To upgrade your account with the latest API, please contact api@gocardless.com for assistance. Once upgraded, access token from legacy API can be renewed by clicking the connect button in <a href="%2$s">settings</a> page.', 'woocommerce-gateway-gocardless' ), $this->version, $this->get_setting_url() );
		}

		WC_Admin_Notices::add_custom_notice( 'gocardless_connect_prompt', $message );
	}

	/**
	 * Migrate old settings prior 2.4.0.
	 *
	 * @since 2.4.0
	 */
	protected function _migrate_old_settings() {
		$has_old_settings = (
			! empty( $this->settings['app_id'] ) &&
			! empty( $this->settings['app_secret'] ) &&
			! empty( $this->settings['merchant_id'] )
		);

		if ( $has_old_settings ) {
			// Backup the old settings in case merchant needs it.
			add_option( 'woocommerce_gocardless_settings_deprecated', $this->settings );

			unset(
				$this->settings['app_id'],
				$this->settings['app_secret'],
				$this->settings['merchant_id'],
				$this->settings['payment_action']
			);

			$this->settings['access_token'] = '';

			update_option( 'woocommerce_gocardless_settings', $this->settings );
		}
	}

	/**
	 * Init order admin.
	 *
	 * @since 2.4.0
	 */
	public function init_order_admin() {
		require_once( $this->plugin_path . '/includes/class-wc-gocardless-order-admin.php' );

		$order_admin = new WC_GoCardless_Order_Admin();
		$order_admin->add_meta_box();
		$order_admin->add_order_actions();
	}

	/**
	 * Register the gateway for use.
	 *
	 * @param array $methods Registered payment methods.
	 *
	 * @return array Payment methods
	 */
	public function register_gateway( $methods ) {
		if ( $this->can_use_gateway_addons() ) {
			$methods[] = 'WC_Gateway_GoCardless_Addons';
		} else {
			$methods[] = 'WC_Gateway_GoCardless';
		}

		return $methods;
	}

	/**
	 * Check environment and maybe show notice in admin if requirements are
	 * not satisified.
	 *
	 * @see https://gocardless.com/faq/merchants/international-payments/
	 * @see https://github.com/woocommerce/woocommerce-gateway-gocardless/issues/59
	 *
	 * @since 2.4.6
	 * @version 2.4.6
	 */
	public function environment_check() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		if ( ! in_array( get_woocommerce_currency(), array( 'GBP', 'EUR', 'SEK' ) ) ) {
			printf(
				'<div class="error"><p>%s</p></div>',
				sprintf(
					__( 'GoCardless requires that the WooCommerce <a href="%s">currency</a> is set to GBP, EUR, or SEK.', 'woocommerce-gateway-gocardless' ),
					add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'general' ), admin_url( 'admin.php' ) )
				)
			);
		}
	}

	/**
	 * Get GoCardless gateway instance.
	 *
	 * @since 2.4.0
	 *
	 * @return WC_Gateway_GoCardless|bool Returns gateway instance of false if not found
	 */
	public function gateway_instance() {
		$gateways = WC()->payment_gateways->payment_gateways();

		return ! empty( $gateways['gocardless'] ) ? $gateways['gocardless'] : false;
	}

	/**
	 * Log message.
	 *
	 * @since 2.3.7
	 *
	 * @param string $message Message to log.
	 *
	 * @return void
	 */
	public function log( $message ) {
		if ( 'yes' !== $this->settings['logging'] ) {
			return;
		}

		if ( empty( $this->logger ) ) {
			$this->logger = new WC_Logger();
		}

		$this->logger->add( 'woocommerce-gateway-gocardless', $message );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( $message );
		}
	}

}

/**
 * Return instance of WC_GoCardless.
 *
 * @since 2.3.7
 *
 * @return WC_GoCardless
 */
function wc_gocardless() {
	static $instance;

	if ( ! isset( $instance ) ) {
		$instance = new WC_GoCardless();
	}

	return $instance;
}

/**
 * Get order property with compat check for WC 3.0.
 *
 * @since 2.4.2
 *
 * @param WC_Order $order Order object.
 * @param string   $prop  Order property.
 *
 * @return mixed Order property value.
 */
function wc_gocardless_get_order_prop( $order, $prop ) {
	$value = null;
	switch ( $prop ) {
		case 'order_currency':
			$getter = array( $order, 'get_currency' );
			$value  = is_callable( $getter ) ? call_user_func( $getter ) : $order->get_order_currency();
			break;
		case 'type':
		case 'order_type':
			$getter = array( $order, 'get_type' );
			$value  = is_callable( $getter ) ? call_user_func( $getter ) : $order->order_type;
			break;
		default:
			$getter = array( $order, 'get_' . $prop );
			$value  = is_callable( $getter ) ? call_user_func( $getter ) : $order->{ $prop };
	}

	return $value;
}

wc_gocardless();
