<?php
/**
 * Plugin Name: WooCommerce Bulk Download
 * Plugin URI: https://woocommerce.com/products/woocommerce-bulk-download/
 * Description: Download all or several purchased product download files at once in a ZIP on the My Account page
 * Version: 1.2.10
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * WC requires at least: 2.6
 * WC tested up to: 3.4
 *
 * Copyright: 2009-2017 WooCommerce.
 * License: GPL-2.0+
 * Domain: woocommerce-bulk-download
 * Woo: 508262:d20cd2f8a6bcd37ebea61f949718bdde
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Required Functions (Woo Updater)
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'd20cd2f8a6bcd37ebea61f949718bdde', '508262' );

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// Check if ZipArchive is available
	if ( class_exists( 'ZipArchive' ) ) {

		define( 'WC_BULK_DOWNLOAD_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

		// Brace Yourself
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc-bulk-download.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc-bulk-download-settings.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc-bulk-download-product-options.php' );

		// Start the Engines
		register_activation_hook( __FILE__, array( 'WC_Bulk_Download', 'activate' ) );
		register_deactivation_hook( __FILE__, array( 'WC_Bulk_Download', 'deactivate' ) );

		add_action( 'plugins_loaded', array( 'WC_Bulk_Download', 'get_instance' ) );
		add_action( 'plugins_loaded', array( 'WC_Bulk_Download_Settings', 'get_instance' ) );
		add_action( 'plugins_loaded', array( 'WC_Bulk_Download_Product_Options', 'get_instance' ) );

	} else {
		add_action( 'admin_notices', 'wcbd_ziparchive_missing' );

	}
} else {

	add_action( 'admin_notices', 'wcbd_woocoommerce_deactivated' );

}


/**
 * WooCommerce Deactivated Notice
 */
if ( ! function_exists( 'wcbd_woocoommerce_deactivated' ) ) {

	function wcbd_woocoommerce_deactivated() {

		/* translators: 1: href link to WooCommerce */
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Bulk Download requires %s to be installed and active.', 'woocommerce-bulk-download' ), '<a href="https://www.woocommerce.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';

	}
}

/**
 * WooCommerce ZipArchive Missing Notice
 */
if ( ! function_exists( 'wcbd_ziparchive_missing' ) ) {

	function wcbd_ziparchive_missing() {

		/* translators: 1: href link to ZipArchive php doc */
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Bulk Download requires %s to be installed.', 'woocommerce-bulk-download' ), '<a href="http://php.net/manual/class.ziparchive.php" target="_blank">ZipArchive</a>' ) . '</p></div>';

	}
}

