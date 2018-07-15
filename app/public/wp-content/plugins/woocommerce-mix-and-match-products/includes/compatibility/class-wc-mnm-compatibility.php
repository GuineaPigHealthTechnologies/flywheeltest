<?php
/**
 * Extension Compatibilty
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.0
 * @version  1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Compatibility Class.
 *
 * Load classes for making Mix and Match compatible with other plugins.
 */
class WC_Mix_and_Match_Compatibility {

	function __construct() {

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( $this, 'add_compatibility_notices' ) );
		}

		// Deactivate functionality from mini-extensions.
		$this->unload();

		// Initialize.
		add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
	}

	/**
	 * Unload mini-extensions.
	 */
	public function unload() {
		// Deactivate functionality added by the min/max quantities mini-extension.
		if ( class_exists( 'WC_MNM_Min_Max_Quantities' ) ) {
			remove_action( 'woocommerce_mnm_loaded', 'WC_MNM_Min_Max_Quantities' );
		}

		// Deactivate functionality added by the min/max quantities mini-extension.
		if ( class_exists( 'WC_MNM_Grid' ) ) {
			remove_action( 'init', array( '\WC_MNM_Grid\Display', 'init' ) );
		}
	}

	/**
	 * Init compatibility classes.
	 */
	public function init() {

		// Multiple Shipping Addresses support.
		if ( class_exists( 'WC_Ship_Multiple' ) ) {
			require_once( 'modules/class-wc-ship-multiple-compatibility.php' );
		}

		// Points and Rewards support.
		if ( class_exists( 'WC_Points_Rewards_Product' ) ) {
			require_once( 'modules/class-wc-pnr-compatibility.php' );
		}

		// Pre-orders support.
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			require_once( 'modules/class-wc-po-compatibility.php' );
		}

		// Cost of Goods support.
		if ( class_exists( 'WC_COG' ) ) {
			require_once( 'modules/class-wc-cog-compatibility.php' );
		}

		// One Page Checkout support.
		if ( function_exists( 'is_wcopc_checkout' ) ) {
			require_once( 'modules/class-wc-opc-compatibility.php' );
		}

		// Wishlists support.
		if ( class_exists( 'WC_Wishlists_Plugin' ) ) {
			require_once( 'modules/class-wc-wl-compatibility.php' );
		}

		// Shipstation integration.
		require_once( 'modules/class-wc-shipstation-compatibility.php' );
	}

	/**
	 * Checks versions of compatible/integrated/deprecated extensions.
	 */
	public function add_compatibility_notices() {

		// Min/max mini-extension check.
		if ( class_exists( 'WC_MNM_Min_Max_Quantities' ) ) {
			$notice = sprintf( __( 'The <strong>WooCommerce Mix and Match: Min/Max Quantities</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the <strong>WooCommerce Mix and Match: Min/Max Quantities</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_notice( $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Grid' ) ) {
			$notice = sprintf( __( 'The <strong>WC Mix and Match Grid</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong> and should be deactivated and removed. Please enable the Grid layout (in the Mix and Match product options) for any product you\'d like to use it with.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_notice( $notice, 'warning' );
		}
	}

	/**
	 * Tells if a product is a Name Your Price product, provided that the extension is installed.
	 *
	 * @param  mixed  $product
	 * @return bool
	 */
	public function is_nyp( $product ) {

		if ( ! class_exists( 'WC_Name_Your_Price_Helpers' ) ) {
			return false;
		}

		if ( WC_Name_Your_Price_Helpers::is_nyp( $product ) ) {
			return true;
		}

		return false;
	}
}
