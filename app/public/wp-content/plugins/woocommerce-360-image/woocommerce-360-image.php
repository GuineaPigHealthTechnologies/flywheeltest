<?php
/**
 * Plugin Name: WooCommerce 360° Image
 * Plugin URI: https://woocommerce.com/products/woocommerce-360-image/
 * Description: Add a 360° image rotation display your product listings in WooCommerce.
 * Version: 1.1.6
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GPL-2.0+
 * Domain: woocommerce-360-image
 * Woo: 512186:24eb2cfa3738a66bf3b2587876668cd2
 * WC tested up to: 3.4
 * WC requires at least: 2.6
 * Copyright (c) 2017 WooCommerce
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
woothemes_queue_update( plugin_basename( __FILE__ ), '24eb2cfa3738a66bf3b2587876668cd2', '512186' );

/**
 * Check if WooCommerce is active
 **/
if ( is_woocommerce_active() ) {

	// Brace Yourself
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc360.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc360-display.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc360-settings.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc360-meta.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc360-shortcode.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc360-utils.php' );

	// Start the Engines
	register_activation_hook( __FILE__, array( 'WC_360_Image', 'activate' ) );

	// Vroom.. Vroom..
	add_action( 'plugins_loaded', array( 'WC_360_Image', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'WC_360_Image_Settings', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'WC_360_Image_Meta', 'get_instance' ) );
	add_action( 'wp', array( 'WC_360_Image_Display', 'get_instance' ) );
	add_action( 'wp', array( 'WC_360_Image_Shortcode', 'get_instance' ) );

} else {

	add_action( 'admin_notices', 'wc360_woocoommerce_deactivated' );

}


/**
* WooCommerce Deactivated Notice
**/
if ( ! function_exists( 'wc360_woocoommerce_deactivated' ) ) {

	function wc360_woocoommerce_deactivated() {

		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce 360 Image requires %s to be installed and active.', 'woocommerce-360-image' ), '<a href="https://www.woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';

	}

}
