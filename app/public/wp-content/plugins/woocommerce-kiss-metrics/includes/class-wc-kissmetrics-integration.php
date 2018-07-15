<?php
/**
* WooCommerce Kissmetrics
*
* This source file is subject to the GNU General Public License v3.0
* that is bundled with this package in the file license.txt.
* It is also available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/gpl-3.0.html
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@skyverge.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade WooCommerce Kissmetrics to newer
* versions in the future. If you wish to customize WooCommerce Kissmetrics for your
* needs please refer to http://docs.woocommerce.com/document/kiss-metrics/ for more information.
*
* @package     WC-Kissmetrics/Classes
* @author      SkyVerge
* @copyright   Copyright (c) 2012-2018, SkyVerge, Inc.
* @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
*/

defined( 'ABSPATH' ) or exit;

/**
* Kissmetrics Integration class
*
* Handles settings and tracking functionality
*
* @since 1.0
* @extends \WC_Integration
*/
class WC_KissMetrics_Integration extends WC_Integration {


	/** @var string KM API Key */
	public $api_key;

	/** @var string how to identify visitors, either WP username or email */
	public $identity_pref;

	/** @var array of event names */
	public $event_name = array();

	/** @var array of property names */
	public $property_name = array();

	/** @var \WC_Kissmetrics_API instance */
	protected $api;

	/** @var array API options */
	protected $api_options;

	/** @var \WC_Kissmetrics_Subscriptions_Integration instance */
	protected $subscriptions;


	/**
	 * load settings and setup hooks
	 *
	 * @since 1.0
	 * @return \WC_KissMetrics_Integration
	 */
	public function __construct() {

		// Setup plugin
		$this->id                 = 'kissmetrics';
		$this->method_title       = __( 'Kissmetrics', 'woocommerce-kiss-metrics' );
		$this->method_description = __( 'Web analytics tool that tracks visitors to your site as people, not pageviews. Visualize your online sales funnels and find out which ones are driving revenue and which are not.', 'woocommerce-kiss-metrics' );

		// Load admin form
		$this->init_form_fields();

		// Load settings
		$this->init_settings();

		// Set API Key / Identity Preference
		$this->api_key       = $this->settings['api_key'];
		$this->identity_pref = $this->settings['identity_pref'];
		$this->logging       = $this->settings['logging'];

		// Load event / property names
		foreach ( $this->settings as $key => $value ) {

			if ( strpos( $key, 'event_name' ) !== false ) {

				// event name setting, remove '_event_name' and use as key
				$key = str_replace( '_event_name', '', $key );
				$this->event_name[ $key ] = $value;

			} elseif ( strpos( $key, 'property_name' ) !== false ) {

				// property name setting, remove '_property_name' and use as key
				$key = str_replace( '_property_name', '', $key );
				$this->property_name[ $key ] = $value;
			}
		}

		// Setup API options
		$this->api_options = array();

		// Logging Preference
		switch ( $this->logging ) {

			case 'queries':
				$this->api_options = array_merge( $this->api_options, array( 'log_queries' => true ) );
				break;

			case 'errors':
				$this->api_options = array_merge( $this->api_options, array( 'log_errors' => true ) );
				break;

			case 'queries_and_errors':
				$this->api_options = array_merge( $this->api_options, array( 'log_queries' => true, 'log_errors' => true ) );
				break;

			default:
				break;
		}

		// sanitize admin options before saving
		add_filter( 'woocommerce_settings_api_sanitized_fields_kissmetrics', array( $this, 'filter_admin_options' ) );

		// Add hooks to record events - only add hook if event name is populated

		// Header Javascript Code, only add is API key is populated
		if ( $this->api_key ) {
			add_action( 'wp_head',    array( $this, 'output_head' ) );
			add_action( 'login_head', array( $this, 'output_head' ) );
		}

		// Signed in
		if ( $this->event_name['signed_in'] ) {
			add_action( 'wp_login', array( $this, 'signed_in' ), 10, 2 );
		}

		// Signed out
		if ( $this->event_name['signed_out'] ) {
			add_action( 'wp_logout', array( $this, 'signed_out' ) );
		}

		// Viewed Signup page (on my account page, if enabled)
		if ( $this->event_name['viewed_signup'] ) {
			add_action( 'register_form', array( $this, 'viewed_signup' ) );
		}

		// Signed up for new account (on my account page if enabled OR during checkout)
		if ( $this->event_name['signed_up'] ) {
			add_action( 'user_register', array( $this, 'signed_up' ) );
		}

		// Viewed Product (Properties: Name)
		if ( $this->event_name['viewed_product'] ) {
			add_action( 'woocommerce_after_single_product', array( $this, 'viewed_product' ) );
		}

		// Added Product to Cart (Properties: Product Name, Quantity)
		if ( $this->event_name['added_to_cart'] ) {
			// single product add to cart button
			add_action( 'woocommerce_add_to_cart', array( $this, 'added_to_cart' ), 10, 6 );

			// AJAX add to cart
			if ( is_ajax() ) {
				add_action( 'woocommerce_ajax_added_to_cart', array( $this, 'ajax_added_to_cart' ) );
			}
		}

		// Removed Product from Cart (Properties: Product Name)
		if ( $this->event_name['removed_from_cart'] ) {
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'removed_from_cart' ) );
			add_action( 'woocommerce_remove_cart_item',               array( $this, 'removed_from_cart' ) );
		}

		// Changed Quantity of Product in Cart (Properties: Product Name, Quantity )
		if ( $this->event_name['changed_cart_quantity'] ) {
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'changed_cart_quantity' ), 10, 3 );
		}

		// Viewed Cart
		if ( $this->event_name['viewed_cart'] ) {
			add_action( 'woocommerce_after_cart_contents', array( $this, 'viewed_cart' ) );
			add_action( 'woocommerce_cart_is_empty', array( $this, 'viewed_cart' ) );
		}

		// Started Checkout
		if ( $this->event_name['started_checkout'] ) {
			add_action( 'woocommerce_after_checkout_form', array( $this, 'started_checkout' ) );
		}

		// Started Payment (for gateways that direct post from payment page, eg: Braintree TR, Authorize.net AIM, etc
		if ( $this->event_name['started_payment'] ) {
			add_action( 'after_woocommerce_pay', array( $this, 'started_payment' ) );
		}

		// Completed Purchase
		if ( $this->event_name['completed_purchase'] ) {

			// most orders will call payment complete
			add_action( 'woocommerce_payment_complete', array( $this, 'completed_purchase' ) );

			// catch orders where the order is placed but not yet paid
			add_action( 'woocommerce_order_status_on-hold', array( $this, 'completed_purchase' ) );

			// catch orders where the payment previously failed and was manually changed by the admin
			add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'completed_purchase' ) );
			add_action( 'woocommerce_order_status_failed_to_completed',  array( $this, 'completed_purchase' ) );

			// finally, catch orders processed through payment gateways such as COD
			add_action( 'woocommerce_thankyou', array( $this, 'completed_purchase' ) );
		}

		// Completed Payment
		if ( $this->event_name['completed_payment'] ) {

			add_action( 'woocommerce_order_status_processing',           array( $this, 'completed_payment' ) );
			add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'completed_payment' ) );
		}

		// Wrote Review or Commented (Properties: Product Name if review, Post Title if blog post)
		if ( $this->event_name['wrote_review'] || $this->event_name['commented'] ) {
			add_action( 'comment_post', array( $this, 'wrote_review_or_commented' ) );
		}

		// Viewed Account
		if ( $this->event_name['viewed_account'] ) {
			add_action( 'woocommerce_after_my_account', array( $this, 'viewed_account' ) );
		}

		// Viewed Order
		if ( $this->event_name['viewed_order'] ) {
			add_action( 'woocommerce_view_order', array( $this, 'viewed_order' ) );
		}

		// Updated Address
		if ( $this->event_name['updated_address'] ) {
			add_action( 'woocommerce_customer_save_address', array( $this, 'updated_address' ), 10, 2 );
		}

		// Changed Password
		if ( $this->event_name['changed_password'] && ! empty( $_POST['password_1'] ) ) {
			add_action( 'woocommerce_save_account_details', array( $this, 'changed_password' ) );
		}

		// Applied Coupon
		if ( $this->event_name['applied_coupon'] ) {
			add_action( 'woocommerce_applied_coupon', array( $this, 'applied_coupon' ) );
		}

		// Tracked Order
		if ( $this->event_name['tracked_order'] ) {
			add_action( 'woocommerce_track_order', array( $this, 'tracked_order' ) );
		}

		// Estimated Shipping
		if ( $this->event_name['estimated_shipping'] ) {
			add_action( 'woocommerce_calculated_shipping', array( $this, 'estimated_shipping' ) );
		}

		// Cancelled Order
		if ( $this->event_name['cancelled_order'] ) {
			add_action( 'woocommerce_cancelled_order', array( $this, 'cancelled_order' ) );
		}

		// Reordered Previous Order
		if ( $this->event_name['reordered'] ) {
			add_action( 'woocommerce_ordered_again', array( $this, 'reordered' ) );
		}

		// Save admin options
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_integration_kissmetrics', array( $this, 'process_admin_options' ) );
		}
	}


	/**
	 * Track login event
	 *
	 * @since 1.0
	 * @param string $user_login
	 * @param object $user WP_User instance
	 */
	public function signed_in( $user_login, $user ) {

		if ( in_array( $user->roles[0], apply_filters( 'wc_kissmetrics_signed_in_user_roles', array( 'subscriber', 'customer' ) ) ) ) {

			$identity = $this->get_identity( $user );

			$this->api_record_event( $this->event_name['signed_in'], array(), $identity );

			// track customer properties
			$this->api_set_properties( array_merge( $this->get_user_properties( $user ), array( '$last_login' => date( 'r' ) ) ), $identity );
		}
	}


	/**
	 * Track sign out
	 *
	 * @since 1.0
	 */
	public function signed_out() {

		$this->api_record_event( $this->event_name['signed_out'] );
	}


	/**
	 * Track sign up
	 *
	 * @since 1.0
	 */
	public function signed_up( $user_id ) {

		$user = get_user_by( 'id', $user_id );

		$identity = $this->get_identity( $user );

		$this->api_set_properties( array_merge( $this->get_user_properties( $user ), array( '$last_login' => date( 'r' ) ) ), $identity );

		if ( $identity ) {
			$this->get_api()->alias( $this->get_named_identity(), $identity );
			$this->set_named_identity( $identity );
		}

		$this->api_record_event( $this->event_name['signed_up'], array( 'email' => $user->user_email, 'username' => $user->user_login ) );
	}


	/**
	 * Track sign up view
	 *
	 * @since 1.0
	 */
	public function viewed_signup() {
		if ( $this->not_page_reload() ) {
			$this->js_record_event( $this->event_name['viewed_signup'] );
		}
	}


	/**
	 * Track product view
	 *
	 * @since 1.0
	 */
	public function viewed_product() {
		if ( $this->not_page_reload() ) {

			$properties = array( $this->event_name['viewed_product'] . ' ' . $this->property_name['product_name'] => get_the_title() );

			if ( $this->is_legacy() ) {
				$properties[ $this->property_name['product_name'] ] = get_the_title();
			}

			$this->js_record_event( $this->event_name['viewed_product'], $properties );
		}
	}


	/**
	 * Track add to cart
	 *
	 * @since 1.0
	 * @param string $cart_item_key
	 * @param int $product_id
	 * @param int $quantity
	 * @param int $variation_id
	 * @param array $variation
	 * @param array $cart_item_data
	 */
	public function added_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		// don't track add to cart from AJAX POST here
		if ( is_ajax() ) {
			return;
		}

		$product            = wc_get_product( $product_id );
		$product_name       = htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' );
		$product_categories = trim( strip_tags( SV_WC_Product_Compatibility::wc_get_product_category_list( $product ) ) );

		$properties = array(
			$this->get_property_name( 'product_name', 'added_to_cart' )  => $product_name,
			$this->get_property_name( 'quantity', 'added_to_cart' )      => $quantity,
			$this->get_property_name( 'category' )                       => $product_categories,
			$this->get_property_name( 'product_price', 'added_to_cart' ) => SV_WC_Helper::number_format( $product->get_price() ),
		);

		// add legacy (unscoped) properties
		if ( $this->is_legacy() ) {
			$properties[ $this->get_property_name( 'product_name' ) ] = $product_name;
			$properties[ $this->get_property_name( 'quantity' ) ]     = $quantity;
		}

		if ( ! empty( $variation ) ) {
			// Added a variable product to cart, set attributes as properties
			// Remove 'pa_' from keys to keep property names consistent
			$variation = array_flip( str_replace( 'attribute_', '', array_flip( $variation ) ) );

			$properties = array_merge( $properties, $variation );
		}

		/**
		 * Filters the add to cart event properties.
		 *
		 * @since 1.10.0
		 *
		 * @param string[] $properties {
		 *   @type string 'product_name'
		 *   @type string 'quantity'
		 *   @type string 'category'
		 *   @type string 'product_price'
		 * }
		 * @param \WC_KissMetrics_Integration $this integration instance
		 */
		$this->api_record_event( $this->event_name['added_to_cart'], apply_filters( 'wc_kissmetrics_add_to_cart_properties', $properties, $this ) );
	}


	/**
	 * Track AJAX add to cart
	 *
	 * @since 1.0
	 * @param int $product_id
	 */
	public function ajax_added_to_cart( $product_id ) {

		$product            = wc_get_product( $product_id );
		$product_name       = htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' );
		$product_categories = trim( strip_tags( SV_WC_Product_Compatibility::wc_get_product_category_list( $product ) ) );
		$quantity = 1;

		$properties = array(
			$this->get_property_name( 'product_name', 'added_to_cart' )  => $product_name,
			$this->get_property_name( 'quantity', 'added_to_cart' )      => $quantity,
			$this->get_property_name( 'category' )                       => $product_categories,
			$this->get_property_name( 'product_price', 'added_to_cart' ) => SV_WC_Helper::number_format( $product->get_price() ),
		);

		// add legacy (unscoped) properties
		if ( $this->is_legacy() ) {
			$properties[ $this->get_property_name( 'product_name' ) ] = $product_name;
			$properties[ $this->get_property_name( 'quantity' ) ]     = $quantity;
		}

		/** This filter is documented above */
		$this->api_record_event( $this->event_name['added_to_cart'], apply_filters( 'wc_kissmetrics_add_to_cart_properties', $properties, $this ) );
	}


	/**
	 * Track remove from cart
	 *
	 * @since 1.0
	 * @param string $cart_item_key
	 */
	public function removed_from_cart( $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['product_id'] ) ) {

			$product_name = get_the_title( WC()->cart->cart_contents[ $cart_item_key ]['product_id'] );

			$properties = array( $this->get_property_name( 'product_name', 'removed_from_cart' ) => $product_name );

			// add legacy (unscoped) property
			if ( $this->is_legacy() ) {
				$properties['product name'] = $product_name;
			}

			$this->api_record_event( $this->event_name['removed_from_cart'], $properties );
		}
	}


	/**
	 * Track quantity change in cart
	 *
	 * @since 1.0
	 * @param string $cart_item_key
	 * @param int $quantity
	 * @param int $old_quantity
	 */
	public function changed_cart_quantity( $cart_item_key, $quantity, $old_quantity ) {;

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['product_id'] ) ) {

			$product_name = get_the_title( WC()->cart->cart_contents[ $cart_item_key ]['product_id'] );

			// delta
			$quantity -= $old_quantity;

			$properties = array(
				$this->get_property_name( 'product_name', 'changed_cart_quantity' ) => $product_name,
				$this->get_property_name( 'quantity', 'changed_cart_quantity' )     => $quantity,
			);

			// add legacy (unscoped) property
			if ( $this->is_legacy() ) {
				$properties[ $this->get_property_name( 'product_name' ) ] = $product_name;
				$properties['quantity'] = $quantity;
			}

			$this->api_record_event( $this->event_name['changed_cart_quantity'], $properties );
		}
	}


	/**
	 * Track cart view
	 *
	 * @since 1.0
	 */
	public function viewed_cart() {

		if ( $this->not_page_reload() ) {
			$this->js_record_event( $this->event_name['viewed_cart'] );
		}
	}


	/**
	 * Track checkout start
	 *
	 * @since 1.0
	 */
	public function started_checkout() {

		if ( $this->not_page_reload() ) {
			$this->js_record_event( $this->event_name['started_checkout'] );
		}
	}

	/**
	 * Track payment start
	 *
	 * @since 1.0
	 */
	public function started_payment() {

		if ( $this->not_page_reload() ) {
			$this->js_record_event( $this->event_name['started_payment'] );
		}
	}


	/**
	 * Track commenting (either post or product review)
	 *
	 * @since 1.0
	 */
	public function wrote_review_or_commented() {

		$post_type = get_post_type();

		if ( 'product' === $post_type && $this->has_event( 'wrote_review' ) ) {

			$properties = array( $this->get_property_name( 'product_name', 'wrote_review' ) => get_the_title() );

			if ( $this->is_legacy() ) {
				$properties[ $this->get_property_name( 'product_name' ) ] = get_the_title();
			}

			$this->api_record_event( $this->event_name['wrote_review'], $properties );

		} elseif ( 'post' === $post_type && $this->has_event( 'commented' ) ) {

			$this->api_record_event( $this->event_name['commented'], array( $this->property_name['post_title'] => get_the_title() ) );
		}
	}


	/**
	 * Track completed purchase, note this is triggered regardless of the
	 * payment status for the order. Payments are handled below in completed_payment()
	 *
	 * @since 1.0
	 * @param int $order_id
	 */
	public function completed_purchase( $order_id ) {

		if ( metadata_exists( 'post', $order_id, '_wc_kiss_metrics_tracked' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		// aliasing
		if ( $order->get_user_id() ) {

			$new_user = strtotime( $order->get_user()->user_registered ) >= ( time() - 10 * MINUTE_IN_SECONDS );

			// alias newly-created users who just created an account and whose existing identity does not match that which we're aliasing to
			if ( $new_user && $this->get_named_identity() && ( $this->get_named_identity() != $this->get_identity( $order->get_user() ) ) ) {
				$this->get_api()->alias( $this->get_named_identity(), $this->get_identity( $order->get_user() ) );
			}

		} else {

			// alias guest users who were previously anonymous to their billing email
			if ( $this->get_named_identity() ) {
				$this->get_api()->alias( $this->get_named_identity(), SV_WC_Order_Compatibility::get_prop( $order, 'billing_email' ) );
			}
		}

		// identify under billing email for guest users, get_identity() for new/existing users
		$identity = $order->get_user_id() ? $this->get_identity( $order->get_user() ) : SV_WC_Order_Compatibility::get_prop( $order, 'billing_email' );

		/**
		 * Completed Purchase Properties.
		 *
		 * Filter the properties set on the "completed purchase" event
		 *
		 * @since 1.2.2
		 * @param array $properties {
		 *   @type string $order_id
		 *   @type string $order_total
		 *   @type string $shipping_total
		 *   @type string $total_quantity
		 *   @type string $payment_method
		 * }
		 * @param \WC_Order $order order instance
		 * @param \WC_KissMetrics_Integration $this integration instance
		 */
		$properties = apply_filters( 'wc_kissmetrics_completed_purchase_properties',
			array(
				'order_id'       => $order->get_order_number(),
				'order_total'    => $order->get_total(),
				'shipping_total' => $order->get_total_shipping(),
				'total_quantity' => $order->get_item_count(),
				'payment_method' => SV_WC_Order_Compatibility::get_prop( $order, 'payment_method_title' ),
				'paid'           => $order->needs_payment() ? 'no' : 'yes',
			), $order, $this
		);

		// track purchase event
		$this->api_record_event( $this->event_name['completed_purchase'], array(
			$this->property_name['order_id']       => $properties['order_id'],
			$this->property_name['order_total']    => $properties['order_total'],
			$this->property_name['shipping_total'] => $properties['shipping_total'],
			$this->property_name['total_quantity'] => $properties['total_quantity'],
			$this->property_name['payment_method'] => $properties['payment_method'],
			'paid'                                 => $properties['paid'],
		), $identity );


		// used to increment the timestamp for each line item API call, otherwise KM considers them duplicates ಠ_ಠ
		$item_count = 0;

		foreach ( $order->get_items() as $item ) {

			$product = $order->get_product_from_item( $item );

			// use product data when available, otherwise fall back to line item data
			if ( $product ) {
				$sku  = $product->get_sku() ? $product->get_sku() : $product->get_id();
				$name = $product->get_title() . ( $product->is_type( 'variation' ) ? ' - ' . implode( ', ', $product->get_variation_attributes() ) : '' );
			} else {
				$sku  = $item['product_id'] ? $item['product_id'] : '';
				$name = $item['name'];
			}

			$properties = array(
				'purchased_product_sku'      => $sku,
				'purchased_product_name'     => $name,
				'purchased_product_category' => $product ? trim( strip_tags( SV_WC_Product_Compatibility::wc_get_product_category_list( $product ) ) ) : '',
				'purchased_product_price'    => $order->get_item_total( $item ),
				'purchased_product_qty'      => $item['qty'],
			);

			// track order line items
			$this->api_record_event( 'item purchased', array(
				$this->property_name['purchased_product_sku']      => $properties['purchased_product_sku'],
				$this->property_name['purchased_product_name']     => $properties['purchased_product_name'],
				$this->property_name['purchased_product_category'] => $properties['purchased_product_category'],
				$this->property_name['purchased_product_price']    => $properties['purchased_product_price'],
				$this->property_name['purchased_product_qty']      => $properties['purchased_product_qty'],
				'_t'                                               => time() + $item_count,
			), $identity );

			$item_count++;
		}

		/**
		 * Completed Purchase User Properties.
		 *
		 * Filter the properties set for a registered user during the
		 * "completed purchase" event.
		 *
		 * @since 1.2.2
		 * @param array $properties {
		 *   @type string $created date user was created
		 *   @type string $email user's email
		 *   @type string 'first name' user's first name
		 *   @type string 'last name' user's last name
		 *   @type string 'phone number' user's phone number
		 * }
		 * @param \WC_Order $order order instance
		 * @param \WC_KissMetrics_Integration $this integration instance
		 */
		$properties = apply_filters( 'wc_kissmetrics_completed_purchase_user_properties', array(
			'created'      => SV_WC_Order_Compatibility::get_date_created( $order )->date( 'r' ),
			'email'        => SV_WC_Order_Compatibility::get_prop( $order, 'billing_email' ),
			'first name'   => SV_WC_Order_Compatibility::get_prop( $order, 'billing_first_name' ),
			'last name'    => SV_WC_Order_Compatibility::get_prop( $order, 'billing_last_name' ),
			'phone number' => SV_WC_Order_Compatibility::get_prop( $order, 'billing_phone' ),
			'guest'        => $order->get_user_id() ? 'no' : 'yes',
		), $order, $this );

		// add username for registered users
		if ( $order->get_user_id() ) {
			$properties['username'] = $order->get_user()->user_login;
		}

		// track customer properties
		$this->api_set_properties( $properties, $identity );

		// mark order as tracked
		SV_WC_Order_Compatibility::update_meta_data( $order, '_wc_kiss_metrics_tracked', 1 );
	}


	/**
	 * Track completed payment for orders that were previously on-hold, like
	 * BACS or cheque orders
	 *
	 * @since 1.6.0
	 * @param int $order_id
	 */
	public function completed_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// orders marked as paid will be tracked in completed_purchase()
		if ( $order->is_paid() || metadata_exists( 'post', $order_id, '_wc_kiss_metrics_completed_payment_tracked' ) ) {
			return;
		}

		add_filter( 'wc_kissmetrics_enable_tracking', '__return_true' );

		$identity = $order->get_user_id() ? $this->get_identity( $order->get_user() ) : SV_WC_Order_Compatibility::get_prop( $order, 'billing_email' );

		$properties = array( $this->get_property_name( 'order_id', 'completed_payment' ) => $order->get_order_number() );

		if ( $this->is_legacy() ) {
			$properties[ $this->get_property_name( 'order_id' ) ] = $order->get_order_number();
		}

		$this->api_record_event( $this->event_name['completed_payment'], $properties, $identity );

		// mark order as tracked
		SV_WC_Order_Compatibility::update_meta_data( $order, '_wc_kiss_metrics_completed_payment_tracked', 1 );
	}


	/**
	 * Track account view
	 *
	 * @since 1.0
	 */
	public function viewed_account() {

		$this->js_record_event( $this->event_name['viewed_account'] );
	}


	/**
	 * Track order view
	 *
	 * @since 1.0
	 */
	public function viewed_order() {

		$this->api_record_event( $this->event_name['viewed_order'] );
	}


	/**
	 * Track address update
	 *
	 * @since 1.0
	 */
	public function updated_address( $user_id, $type ) {

		$this->api_record_event( $this->event_name['updated_address'], array( 'address type' => $type ) );
	}


	/**
	 * Track password change
	 *
	 * @since  1.0
	 */
	public function changed_password() {

		$this->api_record_event( $this->event_name['changed_password'] );
	}


	/**
	 * Track successful coupon apply
	 *
	 * @since 1.0
	 * @param string $coupon_code
	 */
	public function applied_coupon( $coupon_code ) {

		$this->api_record_event( $this->event_name['applied_coupon'], array( $this->property_name['coupon_code'] => $coupon_code ) );
	}


	/**
	 * Track order track
	 *
	 * @since 1.0
	 * @param int $order_id
	 */
	public function tracked_order( $order_id ) {

		$order = wc_get_order( $order_id );

		$properties = array( $this->get_property_name( 'order_id', 'tracked_order' ) => $order->get_order_number() );

		if ( $this->is_legacy() ) {
			$properties[ $this->get_property_name( 'order_id' ) ] = $order->get_order_number();
		}

		$this->api_record_event( $this->event_name['tracked_order'], $properties );
	}


	/**
	 * Track shipping estimate on cart page
	 *
	 * @since 1.0
	 */
	public function estimated_shipping() {

		$this->api_record_event( $this->event_name['estimated_shipping'] );
	}


	/**
	 * Track order cancel from My Account area
	 *
	 * @since 1.0
	 * @param int $order_id
	 */
	public function cancelled_order( $order_id ) {

		$order = wc_get_order( $order_id );

		$properties = array( $this->get_property_name( 'order_id', 'cancelled_order' ) => $order->get_order_number() );

		if ( $this->is_legacy() ) {
			$properties[ $this->get_property_name( 'order_id' ) ] = $order->get_order_number();
		}

		$this->api_record_event( $this->event_name['cancelled_order'], $properties );
	}


	/**
	 * Track re-order from My Account area
	 *
	 * @since 1.0
	 * @param int $order_id
	 */
	public function reordered( $order_id ) {

		$order = wc_get_order( $order_id );

		$properties = array( $this->get_property_name( 'order_id', 'reordered' ) => $order->get_order_number() );

		if ( $this->is_legacy() ) {
			$properties[ $this->get_property_name( 'order_id' ) ] = $order->get_order_number();
		}

		$this->api_record_event( $this->event_name['reordered'], $properties );
	}


	/**
	 * Track custom event
	 *
	 * Contains excess checks to account for any kind of user input
	 *
	 * @since 1.0
	 * @param bool $event_name
	 * @param bool $properties
	 */
	public function custom_event( $event_name = false, $properties = false ) {

		if ( ! empty( $event_name ) ) {

			$this->js_record_event( $event_name, $properties );
		}
	}


	/**
	 * Output tracking javascript in <head>
	 *
	 * @since 1.0
	 */
	public function output_head() {

		// Verify tracking status
		if ( $this->disable_tracking() ) {
			return;
		}

		// no indentation on purpose
		?>
<!-- Start WooCommerce Kissmetrics -->
<script type="text/javascript">var _kmq = _kmq || [];
	var _kmk = _kmk || '<?php echo esc_js( $this->api_key ); ?>';
	function _kms(u){
		setTimeout(function(){
			var d = document, f = d.getElementsByTagName('script')[0],
					s = d.createElement('script');
			s.type = 'text/javascript'; s.async = true; s.src = u;
			f.parentNode.insertBefore(s, f);
		}, 1);
	}
	_kms('//i.kissmetrics.com/i.js');
	_kms('//scripts.kissmetrics.com/' + _kmk + '.2.js');
<?php
$identity = $this->get_identity();

if ( $identity ) {

	$user = wp_get_current_user();

	if ( ! get_user_meta( $user->ID, 'wc_kissmetrics_user_aliased', true ) && strtotime( $user->user_registered ) >= 1496248913 ) { // 1496248913 is the time stamp representing the date this change was introduced (May 31, 2017)

		echo "_kmq.push(['alias', '" . esc_js( $identity ) . "' ]);\n";

		update_user_meta( $user->ID, 'wc_kissmetrics_user_aliased', true );
	}

	echo "_kmq.push(['identify', '" . esc_js( $identity ) . "' ]);\n";
	echo "_kmq.push(['set', " . json_encode( $this->get_user_properties( $user ) ) . "]);";
}

if ( is_front_page() && $this->event_name['viewed_homepage'] ) {

	echo "_kmq.push(['record', '" . esc_js( $this->event_name['viewed_homepage'] ) . "' ]);\n";
}
?>
</script>
<!-- end WooCommerce Kissmetrics -->
		<?php
	}


	/**
	 * Output event tracking javascript
	 *
	 * @param string $event_name Name of Event to be set
	 * @param array|string $properties Properties to be set with event.
	 * @since 1.0
	 */
	private function js_record_event( $event_name, $properties = '' ) {

		// Verify tracking status
		if ( $this->disable_tracking() ) {
			return;
		}

		// json encode properties if they exist
		if ( is_array( $properties ) ) {

			// remove blank properties
			if( isset( $properties[''] ) )
				unset( $properties[''] );

			$properties = ', ' . json_encode( $properties );
		}

		wc_enqueue_js( "_kmq.push(['record', '" . esc_js( $event_name ) ."'{$properties}]);" );
	}


	/**
	 * Output user property setting javascript
	 *
	 * @param array|string $properties Properties to be set with event.
	 * @since 1.0
	 */
	private function js_set_properties( $properties = array() ) {

		// Verify tracking status
		if ( $this->disable_tracking() ) {
			return;
		}

		// remove blank properties
		if( isset( $properties[''] ) ) {
			unset( $properties[''] );
		}

		$properties = json_encode( $properties );

		echo '<script type="text/javascript">' . "_kmq.push(['set', {$properties}]);" . "</script>";
	}


	/**
	 * Record event via HTTP API
	 *
	 * @since 1.0
	 * @param string $event_name Name of Event to be set
	 * @param array $properties Properties to be set with event.
	 * @param string $identity KM identity for visitor
	 */
	public function api_record_event( $event_name, $properties = array(), $identity = null ) {

		// Verify tracking status
		if ( $this->disable_tracking() ) {
			return;
		}

		// identify user first
		$this->set_named_identity( $identity );

		// remove blank properties
		if( isset( $properties[''] ) ) {
			unset( $properties[''] );
		}

		// record the event
		$this->get_api()->record( $event_name, $properties );
	}


	/**
	 * Set properties for user via HTTP API
	 *
	 * @since 1.0
	 * @param array $properties Properties to be set on user
	 * @param string $identity KM identity for visitor
	 */
	public function api_set_properties( $properties, $identity = null ) {

		// Verify tracking status
		if ( $this->disable_tracking() ) {
			return;
		}

		// identify user first
		$this->set_named_identity( $identity );

		// remove blank properties
		unset( $properties[''] );

		// record the properties
		$this->get_api()->set( $properties );
	}


	/**
	 * Lazy load the API object
	 *
	 * @since 1.1.1
	 */
	private function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		// Load Kiss Metrics API wrapper class
		require_once( wc_kissmetrics()->get_plugin_path() . '/includes/class-wc-kissmetrics-api.php' );

		// Init KM API
		return $this->api = new WC_Kissmetrics_API( $this->api_key, $this->api_options );
	}


	/**
	 * Get the identity set by the KM JS for use with server-side API calls
	 *
	 * @since 1.6.0
	 * @return string|null
	 */
	protected function get_named_identity() {

		$id = null;

		if ( ! empty( $_COOKIE['km_ni'] ) ) {
			$id = $_COOKIE['km_ni'];
		} elseif ( ! empty( $_COOKIE['km_ai'] ) ) {
			$id = $_COOKIE['km_ai'];
		}

		return $id;
	}


	/**
	 * Verify that tracking cookie is set and get preferred identity
	 * When logging events via API, prefer named identity first, then anonymous
	 *
	 * @since 1.0
	 * @param string $identity KM identity for visitor
	 */
	private function set_named_identity( $identity ) {

		if ( isset( $identity ) ) {

			// Use passed identity
			$this->get_api()->identify( $identity );

		} elseif ( isset ( $_COOKIE['km_ni'] ) ) {

			// Use named identity
			$this->get_api()->identify( $_COOKIE['km_ni'] );

		} elseif ( isset ( $_COOKIE['km_ai'] ) ) {

			// Use anonymous identity
			$this->get_api()->identify( $_COOKIE['km_ai'] );

		} else {

			// Neither cookie set and named identity not passed, don't track request and log error
			// Cookies are probably disabled for visitor
			if ( 'errors' === $this->logging || 'queries_and_errors' === $this->logging ) {

				wc_kissmetrics()->log( "No identity found! Cannot send event via API" );
			}
		}
	}


	/**
	 * Disable tracking if admin, privileged user, or API key is blank
	 *
	 * @TODO: preventing tracking of admins via this method is fairly blunt
	 * and challenging for admin-only actions like refunds, subscriptions, etc. to
	 * work properly. Should refactor to be action/event-specific, since some
	 * actions should be tracked (e.g. a refund) but they happen in both an admin
	 * context *and* are performed by an administrator or shop manager ಠ_ಠ MR 2015-09-17
	 *
	 * @since 1.0
	 * @return bool true if tracking should be disabled, otherwise false
	 */
	private function disable_tracking() {

		// workaround until this method is refactored
		if ( apply_filters( 'wc_kissmetrics_enable_tracking', false ) ) {
			return false;
		}

		// don't disable tracking on AJAX requests
		if ( is_admin() && is_ajax() ) {
			return false;
		}

		// disable tracking if admin, shop manager, or API key is blank
		if ( is_admin() || current_user_can( 'manage_options' ) || ( ! $this->api_key ) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Get named identity of user
	 *
	 * @since 1.0
	 * @param mixed $user
	 * @return string|null visitor email or username
	 */
	public function get_identity( $user = null ) {

		// WP_User or user_id
		if ( isset ( $user ) ) {

			// instantiate new user if not WP_User object
			if ( ! is_object( $user ) ) {
				$user = new WP_User( $user );
			}

			return ( $this->identity_pref == 'email' ? $user->user_email : $user->user_login );
		}

		// user is logged in
		if ( is_user_logged_in() ) {

			$user = get_user_by( 'id', get_current_user_id() );
			return ( $this->identity_pref == 'email' ? $user->user_email : $user->user_login );

		} else {

			//nothing to identify on
			return null;
		}
	}


	/**
	 * Returns user properties to send to Kissmetrics.
	 *
	 * @since 1.10.0
	 * @param \WP_User $user the user to return properties for
	 * @return string[] associative array of user properties
	 */
	private function get_user_properties( $user ) {

		return array(
			'$first_name' => $user->first_name,
			'$last_name'  => $user->last_name,
			'$email'      => $user->user_email,
			'$created'    => date( 'Y-m-d\TH:i:s', strtotime( $user->user_registered ) ),
			'Username'    => $user->user_login,
			'$ip'         => $this->get_client_ip(),
		);
	}


	/**
	 * Checks HTTP referer to see if request was a page reload
	 * Prevents duplication of tracking events when user reloads page or submits a form
	 * e.g applying a coupon on the cart page
	 *
	 * @since 1.0
	 * @return bool true if not a page reload, false if page reload
	 */
	private function not_page_reload() {

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {

			// return portion before query string
			$request_uri = str_replace( strstr( $_SERVER['REQUEST_URI'], '?' ), '', $_SERVER['REQUEST_URI'] );

			if ( stripos( $_SERVER['HTTP_REFERER'], $request_uri ) === false )
				return true;
		}

		return true;
	}


	/**
	 * Returns the visitor's IP
	 *
	 * @since 1.10.0
	 * @return string client IP
	 */
	private function get_client_ip() {

		return isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	}


	/**
	 * Return true if the event name is not empty, indicating that it should
	 * be tracked
	 *
	 * @since 1.6.1
	 * @param string $event_name
	 * @return bool
	 */
	public function has_event( $event_name ) {

		return ! empty( $this->event_name[ $event_name ] );
	}


	/**
	 * Helper to get a property name, optionally scoped by the event name
	 * if provided
	 *
	 * @since 1.6.1
	 * @param string $property_name
	 * @param null|string $event_name
	 * @return string
	 */
	public function get_property_name( $property_name, $event_name = null ) {

		return is_null( $event_name ) ? $this->property_name[ $property_name ] : $this->event_name[ $event_name ] . ' - ' . $this->property_name[ $property_name ];
	}


	/**
	 * Returns true if the current site sent data under pre-1.6.1 standards
	 * (no scoped property names) and therefore those legacy property names
	 * should continue to be sent so as to not affect reports that expect
	 * the old property names
	 *
	 * @since 1.6.1
	 * @return bool
	 */
	public function is_legacy() {

		return get_option( 'wc_kissmetrics_is_legacy', false );
	}


	/** Admin Methods **********************************************/


	/**
	 * Initializes form fields in the format required by WC_Integration
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {

		$settings = array(

			'api_settings_section' => array(
				'title'       => __( 'API Settings', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Enter your API key to start tracking.', 'woocommerce-kiss-metrics' ),
				'type'        => 'section',
				'default'     => ''
			),

			'api_key' => array(
				'title'       => __( 'API Key', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Log into your account and go to Site Settings. Leave blank to disable tracking.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => ''
			),

			'identity_pref' => array(
				'title'       => __( 'Identity Preference', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Select how to identify logged in users.', 'woocommerce-kiss-metrics' ),
				'type'        => 'select',
				'default'     => '',
				'options'     => array(
					'email'    => __( 'Email Address', 'woocommerce-kiss-metrics' ),
					'username' => __( 'Wordpress Username', 'woocommerce-kiss-metrics' )
				)
			),

			'logging' => array(
				'title'       => __( 'Logging', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Select whether to log nothing, queries, errors, or both queries and errors to the WooCommerce log. Careful, this can fill up log files very quickly on a busy site.', 'woocommerce-kiss-metrics' ),
				'type'        => 'select',
				'default'     => '',
				'options'     => array(
					'off'                => __( 'Off', 'woocommerce-kiss-metrics' ),
					'queries'            => __( 'Queries', 'woocommerce-kiss-metrics' ),
					'errors'             => __( 'Errors', 'woocommerce-kiss-metrics' ),
					'queries_and_errors' => __( 'Queries & Errors', 'woocommerce-kiss-metrics' )
				)
			),

			'event_names_section' => array(
				'title'       => __( 'Event Names', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Customize the event names. Leave a field blank to disable tracking of that event.', 'woocommerce-kiss-metrics' ),
				'type'        => 'section',
				'default'     => ''
			),

			'signed_in_event_name' => array(
				'title'       => __( 'Signed In', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer signs in.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'signed in'
			),

			'signed_out_event_name' => array(
				'title'       => __( 'Signed Out', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer signs out.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'signed out'
			),

			'viewed_signup_event_name' => array(
				'title'       => __( 'Viewed Signup', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views the registration form.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'viewed signup'
			),

			'signed_up_event_name' => array(
				'title'       => __( 'Signed Up', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer registers a new account.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'signed up'
			),

			'viewed_homepage_event_name' => array(
				'title'       => __( 'Viewed Homepage', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views the homepage.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'viewed homepage'
			),

			'viewed_product_event_name' => array(
				'title'       => __( 'Viewed Product', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views a single product', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'viewed product'
			),

			'added_to_cart_event_name' => array(
				'title'       => __( 'Added to Cart', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer adds an item to the cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'added to cart'
			),

			'removed_from_cart_event_name' => array(
				'title'       => __( 'Removed from Cart', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer removes an item from the cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'removed from cart'
			),

			'changed_cart_quantity_event_name' => array(
				'title'       => __( 'Changed Cart Quantity', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer changes the quantity of an item in the cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'changed cart quantity'
			),

			'viewed_cart_event_name' => array(
				'title'       => __( 'Viewed Cart', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views the cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'viewed cart'
			),

			'applied_coupon_event_name' => array(
				'title'       => __( 'Applied Coupon', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer applies a coupon', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'applied coupon'
			),

			'started_checkout_event_name' => array(
				'title'       => __( 'Started Checkout', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer starts the checkout.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'started checkout'
			),

			'started_payment_event_name' => array(
				'title'       => __( 'Started Payment', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views the payment page (used with direct post payment gateways)', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'started payment'
			),

			'completed_purchase_event_name' => array(
				'title'       => __( 'Completed Purchase', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer completes a purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'completed purchase'
			),

			'completed_payment_event_name' => array(
				'title'       => __( 'Completed Payment', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer completes payment for their purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'completed payment',
			),

			'wrote_review_event_name' => array(
				'title'       => __( 'Wrote Review', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer writes a review.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'wrote review'
			),

			'commented_event_name' => array(
				'title'       => __( 'Commented', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer write a comment.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'commented'
			),

			'viewed_account_event_name' => array(
				'title'       => __( 'Viewed Account', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views the My Account page.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'viewed account'
			),

			'viewed_order_event_name' => array(
				'title'       => __( 'Viewed Order', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer views an order', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'viewed order'
			),

			'updated_address_event_name' => array(
				'title'       => __( 'Updated Address', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer updates their address.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'updated address'
			),

			'changed_password_event_name' => array(
				'title'       => __( 'Changed Password', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer changes their password.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'changed password'
			),

			'estimated_shipping_event_name' => array(
				'title'       => __( 'Estimated Shipping', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer estimates shipping.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'estimated shipping'
			),

			'tracked_order_event_name' => array(
				'title'       => __( 'Tracked Order', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer tracks an order.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'tracked order'
			),

			'cancelled_order_event_name' => array(
				'title'       => __( 'Cancelled Order', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer cancels an order.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'cancelled order'
			),

			'reordered_event_name' => array(
				'title'       => __( 'Reordered', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer reorders a previous order.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'reordered'
			),

			'property_names_section' => array(
				'title'       => __( 'Property Names', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Customize the property names. Leave a field blank to disable tracking of that property.', 'woocommerce-kiss-metrics' ),
				'type'        => 'section',
				'default'     => ''
			),

			'product_name_property_name' => array(
				'title'       => __( 'Product Name', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer views a product, adds / removes / changes quantities in the cart, or writes a review.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'product name'
			),

			'quantity_property_name' => array(
				'title'       => __( 'Product Quantity', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer adds a product to their cart or changes the quantity in their cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'quantity'
			),

			'product_price_property_name' => array(
				'title'       => __( 'Product Price', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer adds a product to their cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'product price'
			),

			'category_property_name' => array(
				'title'       => __( 'Product Category', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer adds a product to their cart.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'category'
			),

			'coupon_code_property_name' => array(
				'title'       => __( 'Coupon Code', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer applies a coupon.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'coupon code'
			),

			'order_id_property_name' => array(
				'title'       => __( 'Order ID', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'order id'
			),

			'order_total_property_name' => array(
				'title'       => __( 'Order Total', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'order total'
			),

			'shipping_total_property_name' => array(
				'title'       => __( 'Shipping Total', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'shipping total'
			),

			'total_quantity_property_name' => array(
				'title'       => __( 'Total Quantity', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'total quantity'
			),

			'payment_method_property_name' => array(
				'title'       => __( 'Payment Method', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'payment method'
			),

			'post_title_property_name' => array(
				'title'       => __( 'Post Title', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer leaves a comment on a post.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'post title'
			),

			'country_property_name' => array( // TODO: this is currently unused?
				'title'       => __( 'Shipping Country', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer estimates shipping.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'country'
			),

			'purchased_product_sku_property_name' => array(
				'title'       => __( 'Purchased SKU', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'purchased product sku'
			),

			'purchased_product_name_property_name' => array(
				'title'       => __( 'Purchased Product Name', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'purchased product name'
			),

			'purchased_product_category_property_name' => array(
				'title'       => __( 'Purchased Category', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'purchased product category'
			),

			'purchased_product_price_property_name' => array(
				'title'       => __( 'Purchased Price', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'purchased product price'
			),

			'purchased_product_qty_property_name' => array(
				'title'       => __( 'Purchased Quantity', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'purchased product quantity'
			),
		);

		/**
		 * Kissmetrics Settings Filter.
		 *
		 * Filter the settings for Kissmetrics
		 *
		 * @since 1.6.1
		 * @param array $settings settings fields
		 * @param \WC_Kissmetrics_Integration $this instance
		 */
		return $this->form_fields = apply_filters( 'wc_kissmetrics_settings', $settings, $this );
	}


	/**
	 * Generate Section HTML so we can divide the settings page up into sections
	 *
	 * @since 1.0
	 * @param string $key
	 * @param string $data
	 * @return string section HTML
	 */
	public function generate_section_html( $key, $data ) {
		$html = '';

		if ( isset( $data['title'] ) && $data['title'] != '' ) $title = $data['title']; else $title = '';
		$data['class'] = ( isset( $data['class'] ) ) ? $data['class'] : '';
		$data['css']   = ( isset( $data['css'] ) ) ? $data['css'] : '';

		$html .= '<tr valign="top">' . "\n";
		$html .= '<th scope="row" colspan="2">';
		$html .= '<h3 style="margin:0;">' . $data['title'] . '</h3>';
		if ( $data['description'] ) $html .= '<p>' . $data['description'] . '</p>';
		$html .= '</th>' . "\n";
		$html .= '</tr>' . "\n";

		return $html;
	}


	/**
	 * Filter admin options before saving to remove section fields so they are
	 * not saved in the database
	 *
	 * @since 1.8.0
	 * @param array $sanitized_fields
	 * @return array
	 */
	public function filter_admin_options( $sanitized_fields ) {


		// remove our section 'field' so it doesn't get saved to the database
		foreach ( $this->form_fields as $id => $data ) {

			if ( isset( $data['type'] ) && $data['type'] == 'section' ) {

				unset( $sanitized_fields[ $id ] );
			}
		}

		return $sanitized_fields;
	}


}
