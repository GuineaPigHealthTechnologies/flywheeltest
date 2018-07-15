<?php
/**
 * woocommerce-group-coupons.php
 *
 * Copyright (c) 2013-2018 "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package woocommerce-groupons
 * @since woocommerce-groupons 1.0.0
 *
 * Plugin Name: WooCommerce Group Coupons
 * Plugin URI: https://woocommerce.com/products/group-coupons/
 * Description: Coupons for groups. Provides the option to have coupons that are restricted to group members or roles. Works with the free <a href="http://www.itthinx.com/plugins/groups/">Groups</a> plugin.
 * Version: 1.8.1
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 * WC requires at least: 2.6
 * WC tested up to: 3.4
 * Woo: 216795:6a8e3f1b65027f729645a0e48952cfbf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '6a8e3f1b65027f729645a0e48952cfbf', '216795' );

// Check if WooCommerce is active
if ( ! is_woocommerce_active() ) {
	return;
}

define( 'WOO_GROUPONS_PLUGIN_VERSION', '1.8.1' );
define( 'WOO_GROUPONS_FILE', __FILE__ );
define( 'WOO_GROUPONS_PLUGIN_DOMAIN', 'woocommerce-group-coupons' );
define( 'WOO_GROUPONS_LOG', false );
define( 'WOO_GROUPONS_CORE_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WOO_GROUPONS_PLUGIN_URL', plugins_url( 'woocommerce-group-coupons' ) );

/**
 * Plugin boot.
 */
function woocommerce_group_coupons_plugins_loaded() {
	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0' ) >= 0 ) {
		$lib = '/lib';
	} else {
		$lib = '/lib-2';
	}
	define( 'WOO_GROUPONS_CORE_LIB', WOO_GROUPONS_CORE_DIR . $lib . '/core' );
	define( 'WOO_GROUPONS_ADMIN_LIB', WOO_GROUPONS_CORE_DIR . $lib . '/admin' );
	define( 'WOO_GROUPONS_VIEWS_LIB', WOO_GROUPONS_CORE_DIR . $lib . '/views' );
	require_once( WOO_GROUPONS_CORE_LIB . '/class-woocommerce-groupons.php');
}
add_action( 'plugins_loaded', 'woocommerce_group_coupons_plugins_loaded' );

/**
 * Adds links to documentation and support to the plugin's row meta.
 *
 * @param array $plugin_meta plugin row meta entries
 * @param string $plugin_file path to the plugin file - relative to the plugins directory
 * @param array $plugin_data plugin data entries
 * @param string $status current status of the plugin
 *
 * @return array[string]
 */
function woocommerce_group_coupons_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( $plugin_file == plugin_basename( WOO_GROUPONS_FILE ) ) {
		$plugin_meta[] = '<a href="https://woocommerce.com/products/group-coupons/">' . esc_html__( 'Extension', 'woocommerce-group-coupons' ) . '</a>';
		$plugin_meta[] = '<a style="font-weight:bold" href="https://docs.woocommerce.com/document/group-coupons/">' . esc_html__( 'Documentation', 'woocommerce-group-coupons' ) . '</a>';
		$plugin_meta[] = '<a style="font-weight:bold" href="https://woocommerce.com/my-account/create-a-ticket/">' . esc_html__( 'Support', 'woocommerce-group-coupons' ) . '</a>';
	}
	return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'woocommerce_group_coupons_plugin_row_meta', 10, 4 );
