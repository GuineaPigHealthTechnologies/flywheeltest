<?php
/*
	Plugin Name: WooCommerce Waitlist
	Plugin URI: http://www.woothemes.com/products/woocommerce-waitlist/
	Description: This plugin enables registered users to request an email notification when an out-of-stock product comes back into stock. It tallies these registrations in the admin panel for review and provides details.
	Version: 1.8.1
	Author: WooCommerce
	Author URI: http://woocommerce.com/
	Developer: Neil Pie
	Developer URI: https://pie.co.de/
	Woo: 122144:55d9643a241ecf5ad501808c0787483f
	WC requires at least: 2.4
    WC tested up to: 3.4.1
	Requires at least: 4.2
	Tested up to: 4.9.6
	Text Domain: woocommerce-waitlist
	Domain Path: /assets/languages/
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	Copyright: © 2015-2018 WooCommerce
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}
/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '55d9643a241ecf5ad501808c0787483f', '122144' );
if ( ! class_exists( 'WooCommerce_Waitlist_Plugin' ) ) {
	/**
	 * Activate when WC starts
	 *
	 * Only start us up if WC is running
	 */
	add_action( 'woocommerce_init', 'WooCommerce_Waitlist_Plugin::instance' );

	/**
	 * Namespace class for functions non-specific to any object within the plugin
	 *
	 * @package  WooCommerce Waitlist
	 */
	class WooCommerce_Waitlist_Plugin {

		/**
		 * Main plugin class instance
		 *
		 * @var object
		 */
		protected static $instance;
		/**
		 * Path to plugin directory
		 *
		 * @var string
		 */
		public static $path;
		/**
		 * Supported product types
		 *
		 * @var array
		 */
		public static $allowed_product_types;
		/**
		 * $Pie_WCWL_Admin_Init
		 */
		public static $Pie_WCWL_Admin_Init;

		/**
		 * WooCommerce_Waitlist_Plugin constructor
		 */
		public function __construct() {
			self::$path                  = plugin_dir_path( __FILE__ );
			self::$allowed_product_types = $this->get_product_types();
			require_once 'definitions.php';
			if ( ! $this->minimum_woocommerce_version_is_loaded() ) {
				return;
			}
			$this->include_files();
			$this->load_hooks();
		}

		/**
		 * Check users version of WooCommerce is high enough for our plugin
		 *
		 * @return bool
		 */
		public function minimum_woocommerce_version_is_loaded() {
			global $woocommerce;
			if ( version_compare( $woocommerce->version, '3.0', '<' ) ) {
				if ( is_admin() && ! is_ajax() ) {
					add_action( 'admin_notices', array( $this, 'output_waitlist_not_active_notice' ) );
				}

				return false;
			}

			return true;
		}

		/**
		 * Display an admin notice notifying users their version of WooCommerce is too low
		 *
		 * @return void
		 */
		public function output_waitlist_not_active_notice() {
			?>
			<div class="error">
				<p><?php _e( 'WooCommerce Waitlist is active but is not functional. This extension is not available with your version of WooCommerce. Please install and activate WooCommerce version 2.4 or higher.', 'woocommerce-waitlist' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Load required files and instantiate classes where needed
		 */
		public function include_files() {
			require_once 'classes/class-pie-wcwl-compatibility.php';
			require_once 'classes/class-pie-wcwl-waitlist.php';
			if ( is_admin() ) {
				require_once 'classes/admin/class-pie-wcwl-admin-init.php';
				$admin = new Pie_WCWL_Admin_Init();
				$admin->init();
			} else {
				require_once 'classes/frontend/class-pie-wcwl-frontend-init.php';
				$frontend = new Pie_WCWL_Frontend_Init();
				$frontend->init();
			}
		}

		/**
		 * All other hooks pertinent to the main plugin class
		 *
		 * @todo factor out hooks into appropriate classes
		 */
		public function load_hooks() {
			add_action( 'admin_init', array( $this, 'version_check' ) );
			add_action( 'init', array( $this, 'set_default_localization_directory' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'initialise_waitlist_email_class' ) );
			add_action( 'init', array( $this, 'register_custom_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
			// Global
			add_action( 'woocommerce_reduce_order_stock', array( $this, 'remove_user_from_waitlist_on_product_purchase' ) );
			add_action( 'delete_user', array( $this, 'unregister_user_when_deleted' ) );
			// Mailout hooks
			add_action( 'woocommerce_product_set_stock_status', array( $this, 'perform_api_mailout_stock_status' ), 10, 2 );
			add_action( 'woocommerce_product_set_stock', array( $this, 'perform_api_mailout_stock' ) );
			add_action( 'woocommerce_variation_set_stock_status', array( $this, 'perform_api_mailout_stock_status' ), 10, 2 );
			add_action( 'woocommerce_variation_set_stock', array( $this, 'perform_api_mailout_stock' ) );
		}

		/**
		 * Define the product types we want to load waitlist into
		 *
		 * @todo add notice for deprecated hook 'woocommerce_waitlist_supported_products'
		 *
		 * @return mixed|void
		 */
		public function get_product_types() {
			$product_types = apply_filters( 'woocommerce_waitlist_supported_products', array(
				'simple'                => array(
					'filepath' => 'product-types/class-pie-wcwl-frontend-simple.php',
					'class'    => 'Pie_WCWL_Frontend_Simple',
				),
				'variable'              => array(
					'filepath' => 'product-types/class-pie-wcwl-frontend-variable.php',
					'class'    => 'Pie_WCWL_Frontend_Variable',
				),
				'grouped'               => array(
					'filepath' => 'product-types/class-pie-wcwl-frontend-grouped.php',
					'class'    => 'Pie_WCWL_Frontend_Grouped',
				),
				'subscription'          => array(
					'filepath' => 'product-types/class-pie-wcwl-frontend-simple.php',
					'class'    => 'Pie_WCWL_Frontend_Simple',
				),
				'variable-subscription' => array(
					'filepath' => 'product-types/class-pie-wcwl-frontend-variable.php',
					'class'    => 'Pie_WCWL_Frontend_Variable',
				),
			) );
			return apply_filters( 'wcwl_supported_products', $product_types );
		}

		/**
		 * Add custom endpoint for the waitlist tab on the user account page
		 */
		public function register_custom_endpoints() {
			add_rewrite_endpoint( apply_filters( 'wcwl_waitlist_endpoint', 'woocommerce-waitlist' ), EP_ROOT | EP_PAGES );
		}

		/**
		 * Perform mailouts when stock status is updated and product is in stock
		 * We only want to do this for variations and simple products NOT variable (parent) products
		 *
		 * @todo factor to waitlist class
		 *
		 * @param $product_id
		 * @param $stock_status
		 */
		public function perform_api_mailout_stock_status( $product_id, $stock_status ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				if ( self::is_variable( $product ) && $product->managing_stock() ) {
					foreach ( $product->get_available_variations() as $variation ) {
						$variation = wc_get_product( $variation['variation_id'] );
						if ( 'parent' === $variation->managing_stock() && ( 'instock' == $stock_status || $product->is_in_stock() ) ) {
							$this->do_mailout( $variation );
						}
					}
				} else {
					if ( 'instock' == $stock_status || $product->is_in_stock() ) {
						$this->do_mailout( $product );
					}
				}
			}
		}

		/**
		 * Perform mailouts when stock quantity is updated and product registers as in stock
		 * We only want to do this for variations and simple products NOT variable (parent) products
		 *
		 * @todo factor to waitlist class
		 *
		 * @param $product
		 */
		public function perform_api_mailout_stock( $product ) {
			$product = wc_get_product( $product );
			if ( $product ) {
				if ( self::is_variable( $product ) && $product->managing_stock() ) {
					foreach ( $product->get_available_variations() as $variation ) {
						$variation = wc_get_product( $variation['variation_id'] );
						if ( 'parent' === $variation->managing_stock() && $product->is_in_stock() ) {
							$this->do_mailout( $variation );
						}
					}
				} else {
					if ( $product->is_in_stock() ) {
						$this->do_mailout( $product );
					}
				}
			}
		}

		/**
		 * Fire a call to perform the mailout for the given product
		 *
		 * @param $product
		 */
		private function do_mailout( $product ) {
			$product->waitlist = new Pie_WCWL_Waitlist( $product );
			$product->waitlist->waitlist_mailout();
		}

		/**
		 * Check to see if product is of type "variable"
		 *
		 * @param $product
		 *
		 * @return bool
		 */
		public static function is_variable( $product ) {
			if ( $product->is_type( 'variable' ) || $product->is_type( 'variable-subscription' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "variation"
		 *
		 * @param $product
		 *
		 * @return bool
		 */
		public static function is_variation( $product ) {
			if ( $product->is_type( 'variation' ) || $product->is_type( 'subscription_variation' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "simple"
		 *
		 * @param $product
		 *
		 * @return bool
		 */
		public static function is_simple( $product ) {
			if ( $product->is_type( 'simple' ) || $product->is_type( 'subscription' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get the user object, check which products they are on the waitlist for and unregister them from each one when deleted
		 *
		 * @param  int $user_id id of the user that is being deleted
		 *
		 * @access public
		 * @return void
		 * @since  1.3
		 */
		public function unregister_user_when_deleted( $user_id ) {
			$waitlists = self::get_waitlist_products_by_user_id( $user_id );
			$user      = get_user_by( 'id', $user_id );
			if ( $user && $waitlists ) {
				foreach ( $waitlists as $product ) {
					if ( $product ) {
						$waitlist = new Pie_WCWL_Waitlist( $product );
						$waitlist->unregister_user( $user );
					}
				}
			}
			$archives = self::get_waitlist_archives_by_user_id( $user_id );
			self::remove_user_from_archives( $archives, $user_id );
		}

		/**
		 * Return all the products that the user is on the waitlist for
		 *
		 * @access public
		 * @return array
		 *
		 * @since  1.6.2
		 */
		public static function get_waitlist_products_by_user_id( $user_id ) {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = '" . WCWL_SLUG . "' AND meta_value LIKE '%i:{$user_id};%'", OBJECT );
			$results = self::return_products_user_is_registered_on( $results, $user_id );

			return $results;
		}

		/**
		 * Integrity check on data to ensure users are on the waitlists for the returned products
		 *
		 * @param $products
		 *
		 * @return array
		 */
		public static function return_products_user_is_registered_on( $products, $user_id ) {
			$waitlist_products = array();
			foreach ( $products as $product ) {
				$product  = wc_get_product( $product->post_id );
				$waitlist = new Pie_WCWL_Waitlist( $product );
				if ( $waitlist->user_is_registered( $user_id ) ) {
					$waitlist_products[] = $product;
				}
			}

			return $waitlist_products;
		}

		/**
		 * Return all the products that the user is on a waitlist archive for
		 *
		 * @access public
		 *
		 * @param $user_id
		 *
		 * @return array
		 * @since  1.6.2
		 */
		public static function get_waitlist_archives_by_user_id( $user_id ) {
			if ( ! get_option( '_' . WCWL_SLUG . '_metadata_updated' ) ) {
				return array();
			}
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'wcwl_waitlist_archive' AND meta_value LIKE '%i:{$user_id};i:{$user_id};%'", OBJECT );

			return $results;
		}

		/**
		 * Remove user from all archives
		 */
		public static function remove_user_from_archives( $archives, $user_id ) {
			if ( ! $user_id || empty( $archives ) ) {
				return;
			}
			foreach ( $archives as $archive ) {
				$product_id  = $archive->post_id;
				$old_archive = unserialize( $archive->meta_value );
				$new_archive = $old_archive;
				foreach ( $old_archive as $timestamp => $users ) {
					if ( empty( $users ) ) {
						unset( $new_archive[ $timestamp ] );
					} else {
						if ( get_current_user_id() != $user_id ) {
							unset( $new_archive[ $timestamp ][ $user_id ] );
						} else {
							$new_archive[ $timestamp ][ $user_id ] = 0;
						}
					}
				}
				update_post_meta( $product_id, 'wcwl_waitlist_archive', $new_archive );
			}
		}

		/**
		 * Return all product posts
		 *
		 * @static
		 * @access public
		 * @return array all product posts
		 * @since  1.7.0
		 */
		public static function return_all_product_ids() {
			$args = array(
				'post_type'      => array( 'product', 'product_variation' ),
				'post_status'    => get_post_stati(),
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			);

			return get_posts( $args );
		}

		/**
		 * Checks if user is registered, if not creates a new customer and sends welcome email
		 *
		 * This function overrides woocommerce options to ensure that the user is created when joining the waitlist,
		 * options are reset afterwards
		 *
		 * @param  string $email users email address
		 *
		 * @access public
		 * @return object $current_user the customer's user object
		 * @since  1.3
		 */
		public static function create_new_customer_from_email( $email ) {
			if ( email_exists( $email ) ) {
				$current_user = email_exists( $email );
			} else {
				$class = new WooCommerce_Waitlist_Plugin();
				add_filter( 'pre_option_woocommerce_registration_generate_password', array( $class, 'return_option_setting_yes' ), 10 );
				add_filter( 'pre_option_woocommerce_registration_generate_username', array( $class, 'return_option_setting_yes', ), 10 );
				$current_user = self::create_new_customer( $email );
				remove_filter( 'pre_option_woocommerce_registration_generate_password', array( $class, 'return_option_setting_yes', ), 10 );
				remove_filter( 'pre_option_woocommerce_registration_generate_username', array( $class, 'return_option_setting_yes', ), 10 );
			}

			return $current_user;
		}

		/**
		 * A function to easily add and remove hooks pertaining to creating a user and forcing options
		 *
		 * @return string
		 */
		public function return_option_setting_yes() {
			return 'yes';
		}

		/**
		 * Create new customer using the given email and send user a welcome email with login details
		 *
		 * This function is required before woocommerce v2.1 as handling user creation is handled differently from then
		 *
		 * @access     public
		 *
		 * @param  string $email users email address
		 *
		 * @return int $user_id current user ID
		 * @since      1.3
		 */
		private static function create_new_customer( $email ) {
			$username = sanitize_user( current( explode( '@', $email ) ) );
			// Ensure username is unique
			$append     = 1;
			$o_username = $username;
			while ( username_exists( $username ) ) {
				$username = $o_username . $append;
				$append ++;
			}
			$password = wp_generate_password();
			$userdata = array(
				'user_login' => $username,
				'user_email' => $email,
				'user_pass'  => $password,
				'role'       => 'customer',
			);
			$user_id  = wp_insert_user( $userdata );
			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}
			do_action( 'woocommerce_created_customer', $user_id, $userdata, true );

			return $user_id;
		}

		/**
		 * Appends our Pie_WCWL_Waitlist_Mailout class to the array of WC_Email objects.
		 *
		 * @static
		 *
		 * @param  array $emails the woocommerce array of email objects
		 *
		 * @access public
		 * @return array         the woocommerce array of email objects with our email appended
		 */
		public static function initialise_waitlist_email_class( $emails ) {
			$emails['Pie_WCWL_Waitlist_Mailout'] = require 'classes/class-pie-wcwl-waitlist-mailout.php';

			return $emails;
		}

		/**
		 * Setup localization for plugin
		 *
		 * @access public
		 * @return void
		 */
		public function set_default_localization_directory() {
			load_plugin_textdomain( 'woocommerce-waitlist', false, plugin_basename( dirname( __FILE__ ) ) . '/assets/languages/' );
		}

		/**
		 * Check plugin version in DB and call required upgrade functions
		 *
		 * @hooked action admin_init
		 * @access public
		 * @return void
		 * @since  1.0.1
		 */
		public function version_check() {
			$options = get_option( WCWL_SLUG );
			if ( ! isset( $options['version'] ) ) {
				$this->set_default_options();
				update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
				update_option( '_' . WCWL_SLUG . '_metadata_updated', true );
				update_option( '_' . WCWL_SLUG . '_counts_updated', true );
			}
			if ( version_compare( $options['version'], '1.1.0' ) < 0 ) {
				$this->move_variable_product_waitlist_entries_to_first_out_of_stock_variation();
			}
			if ( version_compare( $options['version'], '1.7.0' ) < 0 ) {
				update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
			}
			$options['version'] = WCWL_VERSION;
			update_option( WCWL_SLUG, $options );
		}

		/**
		 * Set default waitlist options
		 */
		private function set_default_options() {
			update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
			update_option( '_' . WCWL_SLUG . '_metadata_updated', true );
			update_option( WCWL_SLUG . '_archive_on', 'yes' );
			update_option( WCWL_SLUG . '_registration_needed', 'no' );
		}

		/**
		 * Moves all waitlist entries on variable products to one of their variations
		 *
		 * This function is necessary when upgrading to version 1.1.0 - Prior to 1.1.0, waitlists for variable
		 * products were tracked against the parent product, and it was not possible to register for a waitlist on
		 * a product variation. This missing feature caused problems when one variation was out of stock and another
		 * in stock.
		 *
		 * In version 1.1.0, this feature has been added. Product variations can now hold their own waitlist, and
		 * the variable product parents now hold a waitlist containing all registrations for their child products.
		 * To bridge this upgrade gap, any waitlist registrations for a variable product will be moved to the first
		 * product variation that is out of stock.
		 *
		 * @access public
		 * @return void
		 * @since  1.1.0
		 */
		public function move_variable_product_waitlist_entries_to_first_out_of_stock_variation() {
			global $wpdb;
			$products                         = $wpdb->get_col( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '" . WCWL_SLUG . "' and meta_value <> 'a:0:{}'" );
			$moved_waitlists_at_1_0_4_upgrade = array();
			foreach ( $products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product->is_type( 'variable' ) ) {
					$waitlist                                        = get_post_meta( $product_id, WCWL_SLUG, true );
					$moved_waitlists_at_1_0_4_upgrade[ $product_id ] = array(
						'origin'   => $product_id,
						'user_ids' => $waitlist,
						'target'   => 0,
					);
					foreach ( $product->get_children() as $variation_id ) {
						$variation = wc_get_product( $variation_id );
						if ( $variation && ! $variation->is_in_stock() ) {
							$variation->waitlist = new Pie_WCWL_Waitlist( $variation );
							foreach ( $waitlist as $user_id ) {
								$variation->waitlist->register_user( get_user_by( 'id', $user_id ) );
							}
							$moved_waitlists_at_1_0_4_upgrade[ $product_id ]['target'] = $variation_id;
							break;
						}
					}
				}
			}
			if ( ! empty( $moved_waitlists_at_1_0_4_upgrade ) ) {
				$options                                     = get_option( WCWL_SLUG );
				$options['moved_waitlists_at_1_0_4_upgrade'] = $moved_waitlists_at_1_0_4_upgrade;
				update_option( WCWL_SLUG, $options );
				add_action( 'admin_notices', self::$Pie_WCWL_Admin_Init->alert_user_of_moved_waitlists_at_1_0_4_upgrade() );
			}
		}

		/**
		 * Check if users must log in to join waitlist
		 *
		 * This function is only returning true because the registration of logged out users onto waitlists is not
		 * currently being supported but may be added in a future version.
		 *
		 *
		 * @static
		 * @access public
		 * @return bool
		 * @since  1.0.1
		 */
		public static function users_must_be_logged_in_to_join_waitlist() {
			if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if persistent waitlists are disabled
		 *
		 * Filterable function to switch on persistent waitlists. Persistent waitlists will prevent users from being
		 * removed from a waitlist after email is sent, instead being removed when they purchase an item.
		 *
		 * @static
		 * @access public
		 *
		 * @param $product_id
		 *
		 * @return bool
		 * @since  1.1.1
		 */
		public static function persistent_waitlists_are_disabled( $product_id ) {
			return apply_filters( 'wcwl_persistent_waitlists_are_disabled', true, $product_id );
		}

		/**
		 * Check if automatic mailouts are disabled. If so, no email will be sent to waitlisted users when a product
		 * returns to stock and as such they will remain on the waitlist.
		 *
		 * @static
		 * @access public
		 *
		 * @param $product_id
		 *
		 * @return bool
		 * @since  1.1.8
		 */
		public static function automatic_mailouts_are_disabled( $product_id ) {
			return apply_filters( 'wcwl_automatic_mailouts_are_disabled', false, $product_id );
		}

		/**
		 * Removes user from waitlist on purchase if persistent waitlists are enabled
		 *
		 *
		 * @todo   test it's working
		 *
		 * @param  object $order WC_Order object
		 *
		 * @access public
		 * @return void
		 */
		public function remove_user_from_waitlist_on_product_purchase( $order ) {
			$user = get_user_by( 'id', $order->get_user_id() );
			foreach ( $order->get_items() as $item ) {
				if ( $item['id'] > 0 ) {
					$_product = $order->get_product_from_item( $item );
					$product  = wc_get_product( $_product );
					if ( ! self::persistent_waitlists_are_disabled( $product->get_id() ) ) {
						continue;
					}
					if ( $product ) {
						$waitlist = new Pie_WCWL_Waitlist( $product );
						$waitlist->unregister_user( $user );
					}
				}
			}
		}

		/**
		 * Register any required query variables. Currently, just the account tab endpoint is required
		 *
		 * @param $vars
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			$vars[] = apply_filters( 'wcwl_waitlist_endpoint', 'woocommerce-waitlist' );

			return $vars;
		}

		/**
		 * Include links to the documentation and settings page on the plugin screen
		 *
		 * @param mixed $links
		 *
		 * @since 1.7.3
		 * @return array
		 */
		public function plugin_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=waitlist' ) . '">' . __( 'Settings', 'woocommerce-waitlist' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/woocommerce-waitlist/">' . _x( 'Docs', 'short for documents', 'woocommerce-waitlist' ) . '</a>',
				'<a href="https://woocommerce.com/my-account/marketplace-ticket-form/">' . __( 'Support', 'woocommerce-waitlist' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Waitlist main instance, ensures only one instance is loaded
		 *
		 * @since 1.5.0
		 * @return WooCommerce_Waitlist_Plugin
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}
