<?php
/**
 * Plugin Name: WooCommerce Product Retailers
 * Plugin URI: http://www.woocommerce.com/products/product-retailers/
 * Description: Allow customers to purchase products from external retailers
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 1.10.0
 * Text Domain: woocommerce-product-retailers
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2018, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Product-Retailers
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 187888:9766af75222eed8f4fcdf56263685d41
 * WC requires at least: 2.6.14
 * WC tested up to: 3.3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '9766af75222eed8f4fcdf56263685d41', '187888' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.9.0', __( 'WooCommerce Product Retailers', 'woocommerce-product-retailers' ), __FILE__, 'init_woocommerce_product_retailers', array(
	'minimum_wc_version'   => '2.6.14',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_product_retailers() {

/**
 * ## WooCommerce Product Retailers Plugin Class
 *
 * ### Plugin Overview
 *
 * This plugin allows admins to create a list of retailers which can then be
 * assigned to products along with a URL, to be displayed on the frontend
 * product page as a button or dropdown list as an affiliate/external purchase
 * option.  Products can be configured to be purchasable both on site and through
 * the retailers, as well as only through the retailers, resulting in a more full
 * featured "affiliate/external" product functionality that can be used with
 * simple as well as variable product types
 *
 * ### Terminology
 *
 * Despite the plugin name of **product retailers**, **retailers** is used internally
 * to refer to the retailers.
 *
 * ### Admin Considerations
 *
 * This plugin adds a **Retailers** menu item to the "WooCommerce" top level menu
 * where the retailers post type is managed.
 *
 * Global settings for this plugin are added to the Catalog tab under **Product
 * Retailers**
 *
 * Within the product admin a new admin panel named **Retailers** is added to the
 * Product Data panel with overrides for the global settings, and a retailers
 * list for managing retailers for the product.
 *
 * ### Frontend Considerations
 *
 * On the catalog page the **add to cart** button for simple products which are
 * sold only through retailers is altered to link directly to the product page
 * like a variable product, rather than performing an AJAX add to cart.
 *
 * On the product page if there is a single retailer it is displayed as a button
 * with configurable text.  If there is more than one retailer, they are displayed
 * as a dropdown list, which when selected redirects the client on to the
 * configured URL for purchase.
 *
 * Product retailers for a product can also be displayed anywhere on the frontend
 * with a shortcode named [woocommerce_product_retailers] and a widget.
 *
 * Variations for variable products which are sold only through retailers are
 * displayed regardless of whether a price is configured (usually they are not
 * shown if there is no price set).
 *
 * ### Database
 *
 * #### Options table
 *
 * `wc_product_retailers_version` - the current plugin version, set on install/upgrade
 *
 * `wc_product_retailers_product_button_text` - Text shown on the dropdown/
 *   button linking to the external URL, unless overridden at the product level
 *
 * `wc_product_retailers_catalog_button_text` - Text shown on the catalog page
 *   "Add to Cart" button for simple products which are sold only through retailers,
 *   unless overridden at the product levle.
 *
 * #### Custom Post Type
 *
 * `wc_product_retailer` - A Custom Post Type which represents a retailer
 *
 * #### Retailer CPT Postmeta
 *
 * `_product_retailer_default_url` - (string) optional retailer URL, used
 *   unless overridden at the product level
 *
 * #### Product Postmeta
 *
 * `wc_product_retailers_retailer_availability` - Indicates whether the product
 *   is available for purchase from retailers and when
 *
 * `_wc_product_retailers_product_button_text` - Optionally overrides the
 *   global `wc_product_retailers_product_button_text` setting
 *
 * `_wc_product_retailers_catalog_button_text` - Optionally overrides the
 *   global `wc_product_retailers_catalog_button_text` setting
 *
 * `_wc_product_retailers` - array of assigned retailers, with the following
 * data structure:
 * ```php
 * Array(
 *   id          => (int) retailer id,
 *   product_url => (string) optional product url,
 * )
 * ```
 */
class WC_Product_Retailers extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.10.0';

	/** @var WC_Product_Retailers single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'product_retailers';

	/** plugin text domain, DEPRECATED as of 1.7.0 */
	const TEXT_DOMAIN = 'woocommerce-product-retailers';

	/** @var \WC_Product_Retailers_Admin instance */
	protected $admin;

	/** @var \WC_Product_Retailers_List the admin retailers list screen */
	private $admin_retailers_list;

	/** @var \WC_Product_Retailers_Edit the admin retailers edit screen */
	private $admin_retailers_edit;

	/** @var boolean set to try after the retailer dropdown is rendered on the product page */
	private $retailer_dropdown_rendered = false;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::__construct()
	*/
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'        => 'woocommerce-product-retailers',
				'display_php_notice' => true,
			)
		);

		// include required files
		$this->includes();

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'include_template_functions' ), 25 );

		// render frontend embedded styles
		add_action( 'wp_print_styles',                array( $this, 'render_embedded_styles' ), 1 );

		// control the loop add to cart buttons for the product retailer products
		add_filter( 'woocommerce_is_purchasable',     array( $this, 'product_is_purchasable' ), 10, 2 );

		add_action( 'woocommerce_init',               array( $this, 'woocommerce_init' ) );
		add_filter( 'woocommerce_product_is_visible', array( $this, 'product_variation_is_visible' ), 1, 2 );

		// register widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// add the product retailers dropdown on the single product page (next to the 'add to cart' button if available)
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_retailer_dropdown' ) );
		add_action( 'woocommerce_single_product_summary',   array( $this, 'add_retailer_dropdown' ), 35 );
	}


	/**
	 * Initialize translation and taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( ! is_admin() || ! is_ajax() ) {

			// add accordion shortcode
			add_shortcode( 'woocommerce_product_retailers', array( $this, 'product_retailers_shortcode' ) );
		}

		WC_Product_Retailers_Taxonomy::initialize();
	}


	/**
	 * Register product retailers widgets.
	 *
	 * @since 1.4.0
	 */
	public function register_widgets() {

		// load widget
		require_once( $this->get_plugin_path() . '/includes/widgets/class-wc-product-retailers-widget.php' );

		// register widget
		register_widget( 'WC_Product_Retailers_Widget' );
	}


	/**
	 * Product Retailers shortcode.  Renders the product retailers UI element.
	 *
	 * @since 1.4.0
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public function product_retailers_shortcode( $atts ) {

		require_once( $this->get_plugin_path() . '/includes/shortcodes/class-wc-product-retailers-shortcode.php' );

		return WC_Shortcodes::shortcode_wrapper( array( 'WC_Product_Retailers_Shortcode', 'output' ), $atts );
	}


	/**
	 * Setup after WooCommerce is initialized.
	 *
	 * @since 1.2.0
	 */
	public function woocommerce_init() {

		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 2 );
	}


	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::includes()
	 */
	private function includes() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-product-retailers-product.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-product-retailers-taxonomy.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-product-retailers-retailer.php' );

		if ( is_admin() ) {

			$this->admin_includes();
		}
	}


	/**
	 * Include required admin files.
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		$this->admin                = $this->load_class( '/includes/admin/class-wc-product-retailers-admin.php', 'WC_Product_Retailers_Admin' );
		$this->admin_retailers_list = $this->load_class( '/includes/admin/class-wc-product-retailers-list.php',  'WC_Product_Retailers_List' );
		$this->admin_retailers_edit = $this->load_class( '/includes/admin/class-wc-product-retailers-edit.php',  'WC_Product_Retailers_Edit' );
	}


	/**
	 * Function used to Init WooCommerce Product Retailers Template Functions
	 * This makes them pluggable by plugins and themes.
	 *
	 * @since 1.0.0
	 */
	public function include_template_functions() {
		require_once( $this->get_plugin_path() . '/includes/wc-product-retailers-template-functions.php' );
	}


	/** Admin methods ******************************************************/


	/**
	 * Gets the plugin configuration URL
	 *
	 * @since 1.1.0
	 * @see SV_WC_Plugin::get_settings_url()
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products&section=display' );
	}


	/** Frontend methods ******************************************************/


	/**
	 * Renders the product retailers frontend button/select box styles.
	 *
	 * @since 1.0.0
	 */
	public function render_embedded_styles() {
		global $post;

		if ( is_product() ) {

			$product = wc_get_product( $post->ID );

			if ( WC_Product_Retailers_Product::has_retailers( $product ) ) :

				?>
				<style type="text/css">
					.wc-product-retailers-wrap {
						clear:both;
						padding: 1em 0;
					}
					.wc-product-retailers-wrap ul {
						list-style: none;
						margin-left: 0;
					}
					.wc-product-retailers-wrap ul.wc-product-retailers li {
						margin-bottom: 5px;
						margin-right: 5px;
						overflow: auto;
						zoom: 1;
					}
				</style>
				<?php

			endif;
		}
	}


	/**
	 * Make product variations visible even if they don't have a price, as long
	 * as they are sold only through retailers.
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 1.0.0
	 * @param boolean $visible whether the product is visible
	 * @param int $product_id the product id
	 * @return boolean true if the product is visible, false otherwise.
	 */
	public function product_variation_is_visible( $visible, $product_id ) {

		$product = wc_get_product( $product_id );

		if ( $product->is_type( 'variable' ) &&  WC_Product_Retailers_Product::is_retailer_only_purchase( $product ) ) {
			$visible = true;
		}

		return $visible;
	}


	/**
	 * Marks "retailer only" products as not purchasable.
	 *
	 * @since 1.0.0
	 * @param boolean $purchasable whether the product is purchasable
	 * @param WC_Product $product the product
	 * @return boolean true if $product is purchasable, false otherwise
	 */
	public function product_is_purchasable( $purchasable, $product ) {

		if ( WC_Product_Retailers_Product::is_retailer_only_purchase( $product ) ) {
			$purchasable = false;
		}

		return $purchasable;
	}


	/**
	 * Modify the 'add to cart' text for simple product retailer products which
	 * are sold only through retailers to display the catalog button text.
	 * This is because the customer must select a retailer to purchase.
	 *
	 * @since 1.0.0
	 * @param string $label the 'add to cart' label
	 * @param \WC_Product $product WC product object
	 * @return string the 'add to cart' label
	 */
	public function add_to_cart_text( $label, $product ) {

		if ( $product->is_type( array( 'simple', 'subscription' ) ) && WC_Product_Retailers_Product::is_retailer_only_purchase( $product ) && WC_Product_Retailers_Product::has_retailers( $product ) ) {
			$label = __( WC_Product_Retailers_Product::get_catalog_button_text( $product ), 'woocommerce-product-retailers' );
		}

		return $label;
	}


	/**
	 * Display the product retailers drop down box.
	 *
	 * @since 1.0.0
	 */
	public function add_retailer_dropdown() {
		global $product;

		// get any product retailers
		$retailers = WC_Product_Retailers_Product::get_product_retailers( $product );

		// only add dropdown if retailers have been assigned and it hasn't already been displayed
		if ( $this->retailer_dropdown_rendered || empty( $retailers ) || WC_Product_Retailers_Product::product_retailers_hidden( $product ) || WC_Product_Retailers_Product::product_retailers_hidden_if_in_stock( $product ) ) {
			return;
		}

		$this->retailer_dropdown_rendered = true;

		woocommerce_single_product_product_retailers( $product, $retailers );
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Product Retailers Instance, ensures only one instance is/can be loaded.
	 *
	 * @since 1.5.0
	 * @see wc_product_retailers()
	 * @return WC_Product_Retailers
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Get the Admin instance.
	 *
	 * @since 1.8.0
	 * @return \WC_Product_Retailers_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.2.0
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Product Retailers', 'woocommerce-product-retailers' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.2.0
	 * @see SV_WC_Plugin::get_file
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the global default Product Button text default.
	 *
	 * @since 1.0.0
	 * @return string the default product button text
	 */
	public function get_product_button_text() {
		return get_option( 'wc_product_retailers_product_button_text' );
	}


	/**
	 * Gets the global default Catalog Button text default.
	 *
	 * @since 1.0.0
	 * @return string the default product button text
	 */
	public function get_catalog_button_text() {
		return get_option( 'wc_product_retailers_catalog_button_text' );
	}


	/**
	 * Gets the plugin documentation url.
	 *
	 * @since 1.6.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/woocommerce-product-retailers/';
	}

	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.6.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/** Lifecycle methods ******************************************************/

	/**
	 * Run every time. Used since the activation hook is not executed when updating a plugin.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::install
	 */
	protected function install() {

		$this->admin_includes();

		// install default settings
		foreach ( WC_Product_Retailers_Admin::get_global_settings() as $setting ) {

			if ( isset( $setting['default'] ) ) {
				update_option( $setting['id'], $setting['default'] );
			}
		}
	}


	/**
	 * Perform any version-related changes.
	 *
	 * @since 1.8.2
	 * @param int $installed_version the currently installed version of the plugin
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to 1.8.2
		if ( version_compare( $installed_version, '1.8.2', '<' ) ) {

			$this->log( 'Starting upgrade to 1.8.2' );

			/** Update product meta key for retailers only purchasing and hiding retailers if in stock */

			global $wpdb;

			$hide_if_in_stock_count       = 0;
			$retailer_only_purchase_count = 0;
			$retailer_with_store_count    = 0;

			$product_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type IN ( 'product','product_variation' )" );

			foreach ( (array) $product_ids as $id ) {

				// ensure this is a real live ID
				if ( ! is_numeric( $id ) ) {
					continue;
				}

				$id = (int) $id;

				$hide_if_in_stock       = get_post_meta( $id, '_wc_product_retailers_hide_if_in_stock', true );
				$retailer_only_purchase = get_post_meta( $id, '_wc_product_retailers_retailer_only_purchase', true );

				// skip products that don't have this meta set
				if ( ! $hide_if_in_stock && ! $retailer_only_purchase ) {
					continue;
				}

				// products that hide retailers if in stock should always do so
				if ( 'yes' === $hide_if_in_stock ) {
					update_post_meta( $id, '_wc_product_retailers_retailer_availability', 'out_of_stock' );
					$hide_if_in_stock_count++;
				}

				// products marked 'retailer only' should remain that way
				elseif ( 'yes' === $retailer_only_purchase ) {
					update_post_meta( $id, '_wc_product_retailers_retailer_availability', 'replace_store' );
					$retailer_only_purchase_count++;
				}

				// products that disable 'retailers only' should show retailers and the 'add to cart' button
				elseif ( 'no' === $retailer_only_purchase ) {
					update_post_meta( $id, '_wc_product_retailers_retailer_availability', 'with_store' );
					$retailer_with_store_count++;
				}
			}

			$this->log( sprintf( '%s products updated for "Hide retailers if in stock".', $hide_if_in_stock_count ) );
			$this->log( sprintf( '%s products updated for "Retailer only purchase".', $retailer_only_purchase_count ) );
			$this->log( sprintf( '%s products updated for "Retailer or store purchase".', $retailer_with_store_count ) );

			$this->log( 'Completed upgrade for 1.8.2' );
		}
	}


} // end \WC_Product_Retailers class


/**
 * Returns the One True Instance of <plugin>
 *
 * @since 1.5.0
 * @return WC_Product_Retailers
 */
function wc_product_retailers() {
	return WC_Product_Retailers::instance();
}

// fire it up!
wc_product_retailers();

} // init_woocommerce_product_retailers()
