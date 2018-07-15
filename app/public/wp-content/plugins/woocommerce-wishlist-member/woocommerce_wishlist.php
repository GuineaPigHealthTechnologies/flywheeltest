<?php
/*
Plugin Name: WooCommerce Wishlist Member Integration
Description: Integrates Wishlist Member with WooCommerce
Plugin URI: https://woocommerce.com/products/wishlist-member-integration/
Author: WooCommerce
Author URI: https://woocommerce.com/
Version: 2.5.2
Requires at least: 3.8
Tested up to: 4.2

	Copyright (c) 2017 WooCommerce

	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html

	Original Author Attribution: Radomir van Dalen ( http://www.webshop112.nl )
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'c98d3776d16cc97a10bd8814154103e6', '18653' );

/**
 * Check if WooCommerce is active
 */
if ( is_woocommerce_active() ) {

	if ( ! class_exists( 'WC_WishlistMember' ) ) {

		define( 'WC_WISHLISTMEMBER_VERSION', '2.5.2' );

		class WC_WishlistMember {

			/** @var Settings Tab ID */
			private $settings_tab_id = 'wishlist';

			/**
			 * Constructor
			 */
			public function __construct() {
				// Load in the new settings tabs.
				add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
				add_action( 'woocommerce_settings_tabs_array', array( $this, 'add_woocommerce_settings_tab' ), 50 );

				// Run these actions when generating the settings tabs.
				add_action( 'woocommerce_settings_tabs_' . $this->settings_tab_id, array( $this, 'woocommerce_settings_tab_action' ), 10 );
				add_action( 'woocommerce_update_options_' . $this->settings_tab_id, array( $this, 'woocommerce_settings_save' ), 10 );

				// Add Wishlist Member Creation
				add_action( 'woocommerce_order_status_processing', array( $this, 'process_orders_payment' ), 100, 10 );
				add_action( 'woocommerce_order_status_completed', array( $this, 'process_orders_payment' ), 1000, 1 );

				$this->wlskuprefix       = get_option( 'wlskuprefix' );
				$this->wlmailfrom        = get_option( 'wlmailfrom' );
				$this->wlmailfromname    = get_option( 'wlmailfromname' );
				$this->wlmailsubject     = get_option( 'wlmailsubject' );
				$this->wlmailbody        = get_option( 'wlmailbody' );
				$this->wlloginurl_custom = get_option( 'wlloginurl_custom' );

				register_activation_hook( __FILE__, array( $this, 'install' ) );
			}

			/**
			 * Localisation
			 */
			public function load_plugin_textdomain() {
				load_plugin_textdomain( 'woocommerce-wishlist-member', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}

			/**
			 * Install
			 */
			public function install() {
				add_option( 'wlskuprefix', 'WL#' );
				add_option( 'wlmailfrom', get_option( 'admin_email' ) );
				add_option( 'wlmailfromname', get_bloginfo( 'name' ) );
				add_option( 'wlmailsubject', 'Activate your membership' );
				add_option( 'wlmailbody', "Hello [first_name],\n\nBelow you\'ll find your login details\n\n[login_details]" );
				add_option( 'wlloginurl_custom', '' );
			}

			/**
			 * Returns settings array.
			 * @return array settings
			 */
			public function get_settings() {
				return apply_filters( 'woocommerce_wishlist_member_get_settings',
					array(
						array(
							'name' => __( 'Wishlist Member Configuration', 'woocommerce-wishlist-member' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'wishlist_title'
						),
						array( 'name' => __( 'SKU Prefix', 'woocommerce-wishlist-member' ),
							'type'    => 'text',
							'desc'    => __( 'The SKU prefix which you want to use for Wishlist products (e.g. "WL-","WL","WL#")', 'woocommerce-wishlist-member' ),
							'default' => __( 'WL#', 'woocommerce-wishlist-member' ),
							'id'      => 'wlskuprefix',
							'desc_tip' => true
						),

						array( 'type' => 'sectionend', 'id' => 'wishlistend' ),

						array('name' => __( 'Email Notification Settings', 'woocommerce-wishlist-member' ), 'type' => 'title', 'desc' => '', 'id' => 'mailfromstart' ),

						array(
							'name'     => __( 'Mail From', 'woocommerce-wishlist-member' ),
							'type'     => 'text',
							'desc'     => __( 'The email address from which email will be sent', 'woocommerce-wishlist-member' ),
							'id'       => 'wlmailfrom',
							'desc_tip' => true
						),
						array(
							'name'     => __( 'Mail From Name', 'woocommerce-wishlist-member' ),
							'type'     => 'text',
							'desc'     => __( 'The name from which email will be sent', 'woocommerce-wishlist-member' ),
							'id'       => 'wlmailfromname',
							'desc_tip' => true
						),
						array(
							'name'    => __( 'Mail Subject', 'woocommerce-wishlist-member' ),
							'type'    => 'text',
							'desc'    => __( 'Subject of membership confirmation mail', 'woocommerce-wishlist-member' ),
							'id'      => 'wlmailsubject',
							'desc_tip' => true
						),
						array(
							'name'     => __( 'Custom Login URL', 'woocommerce-wishlist-member' ),
							'id'       => 'wlloginurl_custom',
							'type'     => 'text',
							'desc'     => __( 'Enter a custom login URL to include in the mail, or leave empty to use the default WordPress login URL.', 'woocommerce-wishlist-member' ),
							'desc_tip' => true
						),
						array(
							'name'     => __( 'Mail Content', 'woocommerce-wishlist-member' ),
							'type'     => 'textarea',
							'desc'     => __( 'Content of the confirmation email, be sure to include "[login_details]" to place the membership details.Other placeholders supported are: "[first_name]" and "[last_name]"', 'woocommerce-wishlist-member' ),
							'id'       => 'wlmailbody',
							'css'      => 'width:500px; height: 150px;',
							'desc_tip' => true
						),

						array( 'type' => 'sectionend', 'id' => 'mailfromend' )
					)
				);
			}

			/**
			 * Add settings tab to woocommerce
			 */
			public function add_woocommerce_settings_tab( $settings_tabs ) {
				$settings_tabs[ $this->settings_tab_id ] = __( 'Wishlist Member', 'woocommerce-wishlist-member' );
				return $settings_tabs;
			}

			/**
			 * Do this when viewing our custom settings tab(s). One function for all tabs.
			 */
			public function woocommerce_settings_tab_action() {
				woocommerce_admin_fields( $this->get_settings() );
			}

			/**
			 * Save settings in a single field in the database for each tab's fields (one field per tab).
			 */
			public function woocommerce_settings_save() {
				woocommerce_update_options( $this->get_settings() );
			}

			/**
			 * Get current user IP Address
			 * @return string
			 */
			private function get_ip_address() {
				if ( isset( $_SERVER['X-Real-IP'] ) ) {
					return $_SERVER['X-Real-IP'];
				} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
					// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
					// Make sure we always only send through the first IP in the list which should always be the client IP.
					return trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
				} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
					return $_SERVER['REMOTE_ADDR'];
				}
				return '';
			}

			/**
			 * process_orders_payment function.
			 * @param int $order_id
			 */
			public function process_orders_payment( $order_id ) {
				$order = wc_get_order( $order_id );

				// put sku's of all ordered memberships into one array
				$skulevels    = array();
				$wlm_in_order = false;

				foreach ( $order->get_items() as $order_itm_sku ) {
					$product = $order->get_product_from_item( $order_itm_sku );
					if ( $product && strpos( $product->get_sku(), $this->wlskuprefix ) === 0 ) {
						$skulevels[]  = str_replace( $this->wlskuprefix, '', $product->get_sku() );
						$wlm_in_order = true;
					}
				}

				if ( $wlm_in_order && get_post_meta( $order_id, 'wlm_processed', true ) != '1' ) {

					require_once( 'wlmapiclass.php' );

					$pre_wc_30   = version_compare( WC_VERSION, '3.0', '<');
					$trans_id    = $order_id;
					$email       = $pre_wc_30 ? $order->billing_email : $order->get_billing_email();
					$first       = str_replace( ' ', '', $pre_wc_30 ? $order->billing_first_name : $order->get_billing_first_name() );
					$last        = str_replace( ' ', '', $pre_wc_30 ? $order->billing_last_name : $order->get_billing_last_name() );

					// check if customer email already exists / customer already is a member
					if ( $existing_user_id = email_exists( $email ) ) {
						// use internal call to WLM API to add levels to user
						$data = array(
							'id'         => $existing_user_id,
							'Levels'     => $skulevels,
							'Sequential' => true
						);

						wlmapi_update_member( $existing_user_id, $data );
						update_post_meta( $order_id, '_customer_user', $existing_user_id );

						$order->add_order_note( __( "Existing user - added new level(s)", 'woocommerce-wishlist-member' ) );

					} else {
						// Ensure username is unique
						$append     = 1;
						$o_username = $first . $last;
						$username   = $o_username;

						while ( username_exists( $username ) ) {
							$username = $o_username . $append;
							$append ++;
						}

						// add new member
						$pwd  = wp_generate_password();
						$data = array(
							'user_login'          => $username,
							'user_email'          => $email,
							'user_pass'           => $pwd,
							'first_name'          => $first,
							'last_name'           => $last,
							'user_nicename'       => $first . $last,
							'display_name'        => $first . $last,
							'nickname'            => $first . $last,
							'Levels'              => $skulevels,
							'Txid'                => '',
							'Autoresponder'       => true,
							'Sequential'          => true,
							'wpm_registration_ip' => $this->get_ip_address()
						);

						$response = wlmapi_add_member( $data );

						// prepare membership details email
						if ( ! empty( $this->wlloginurl_custom ) ) {
							$login_url = $this->wlloginurl_custom;
						} else {
							$login_url = wp_login_url();
						}

						$message = "
								Username: " . $username . "\n\r
								Temporary Password: " . $pwd . "\n\r
								Login url: " . $login_url . "\n\r
						";

						$wl_pholders = array( '[first_name]', '[login_details]' );
						$wl_subs     = array( $first, $message );
						$m_subject   = $this->wlmailsubject;
						$m_content   = str_replace( $wl_pholders, $wl_subs, $this->wlmailbody );
						$m_headers   = 'From:' . $this->wlmailfromname;
						$m_headers   .= ' <' . $this->wlmailfrom . '>';

						// send email with WC layout
						$mailer = WC()->mailer();
						$mailer->send( $email, $m_subject, $mailer->wrap_message( $m_subject, $m_content ) );

						$order->add_order_note( __( "New user added to Wishlist Member", 'woocommerce-wishlist-member' ) );

						$user_update = get_user_by( 'email', $email );

						if ( $user_update ) {
							update_post_meta( $order_id, '_customer_user', $user_update->ID );
						}

						// add meta value to indicate WLM processing has taken place
						update_post_meta( $order_id, 'wlm_processed', '1' );
					}
				}
			}
		}
	}

	new WC_WishlistMember();
}
