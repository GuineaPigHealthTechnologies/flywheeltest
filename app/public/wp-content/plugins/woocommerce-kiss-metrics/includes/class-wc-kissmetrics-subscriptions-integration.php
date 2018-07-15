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
 * Kissmetrics Subscriptions Integration class
 *
 * Handles settings and tracking functionality for Subscriptions
 *
 * @since 1.6.1
 */
class WC_Kissmetrics_Subscriptions_Integration {


	/**
	 * Bootstrap!
	 *
	 * @since 1.6.1
	 */
	public function __construct() {

		// add subscriptions-specific settings
		add_filter( 'wc_kissmetrics_settings', array( $this, 'add_settings' ) );

		// add subscriptions-specific events
		add_action( 'woocommerce_init', array( $this, 'init_hooks' ) );

		if ( is_admin() && ! is_ajax() ) {
			add_action( 'admin_init', array( $this, 'maybe_add_update_settings_notice' ) );
		}
	}


	/**
	 * Add a notice if Subscriptions is active but Kissmetrics settings haven't
	 * been re-saved yet with the additional subscription-specific event/property
	 * names
	 *
	 * @since 1.6.1
	 */
	public function maybe_add_update_settings_notice() {

		if ( ! isset( $this->get_integration()->settings['renewed_subscription_event_name'] ) ) {

			wc_kissmetrics()->get_admin_notice_handler()->add_admin_notice(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				sprintf( __( 'Please %1$supdate%2$s your Kissmetrics settings in order to start tracking Subscription events.', 'woocommerce-kiss-metrics' ), '<a href="' . esc_url( wc_kissmetrics()->get_settings_url() ) . '">', '</a>' ),
				'subscriptions-update-settings',
				array( 'always_show_on_settings' => true, 'dismissible' => true )
			);
		}
	}


	/**
	 * Add hooks for settings and events
	 *
	 * @since 1.6.1
	 */
	public function init_hooks() {

		if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {

			$event_hooks = array(
				// activated
				'activated_subscription' => array(
					'tag' => 'subscriptions_activated_for_order'
				),
				// reactivated
				'reactivated_subscription' => array(
					'tag' => 'woocommerce_subscription_status_on-hold_to_active'
				),
				// suspended
				'suspended_subscription' => array(
					'tag' => 'woocommerce_subscription_status_on-hold',
				),
				// cancelled
				'cancelled_subscription' => array(
					'tag' => 'woocommerce_subscription_status_cancelled'
				),
				// trial end
				'subscription_trial_ended'            => array(
					'tag' => 'woocommerce_scheduled_subscription_trial_end',
				),
				// pre-paid term end
				'subscription_end_of_prepaid_term'    => array(
					'tag' => 'woocommerce_scheduled_subscription_end_of_prepaid_term',
				),
				// expiration
				'subscription_expired'                => array(
					'tag' => 'woocommerce_scheduled_subscription_expiration',
				),
				// renewal
				'renewed_subscription'                => array(
					'tag' => 'woocommerce_renewal_order_payment_complete',
				),
			);

		} else {

			$event_hooks = array(
				// activation
				'activated_subscription'   => array(
					'tag' => 'subscriptions_activated_for_order',
				),
				// trial end
				'subscription_trial_ended' => array(
					'tag'  => 'subscription_trial_end',
					'args' => 2,
				),
				// expiration
				'subscription_expired'     => array(
					'tag'  => 'subscription_expired',
					'args' => 2,
				),
				// suspension
				'suspended_subscription'   => array(
					'tag'  => 'subscription_put_on-hold',
					'args' => 2,
				),
				// reactivation
				'reactivated_subscription' => array(
					'tag'  => 'reactivated_subscription',
					'args' => 2,
				),
				// cancellation
				'cancelled_subscription'   => array(
					'tag'  => 'cancelled_subscription',
					'args' => 2,
				),
				// renewal
				'renewed_subscription'     => array(
					'tag' => 'woocommerce_renewal_order_payment_complete',
				),
			);
		}

		foreach ( $event_hooks as $event_name => $hook ) {

			if ( $this->get_integration()->has_event( $event_name ) ) {

				$callback = SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ? array( $this, $event_name ) : array( $this, $event_name . '_1_5' );

				add_action( $hook['tag'], $callback, isset( $hook['priority'] ) ? $hook['priority'] : 10, isset( $hook['args'] ) ? $hook['args'] : 1 );
			}
		}
	}


	/**
	 * Track subscription activations (only after successful payment for subscription)
	 *
	 * @since 1.6.1
	 * @param \WC_Order $order order instance
	 */
	public function activated_subscription( $order ) {

		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order );
		}

		$subscriptions = wcs_get_subscriptions_for_order( $order );

		if ( empty( $subscriptions ) ) {
			return;
		}

		$this->enable_tracking();

		foreach ( $subscriptions as $subscription ) {

			$identity = $this->get_integration()->get_identity( $subscription->get_user_id() );

			// subscription properties
			$properties = array(
				$this->get_integration()->get_property_name( 'subscription_id', 'activated_subscription' ) => $subscription->id,
				$this->get_integration()->get_property_name( 'total_initial_payment' )                     => SV_WC_Helper::number_format( $subscription->get_total_initial_payment() ),
				$this->get_integration()->get_property_name( 'initial_sign_up_fee' )                       => SV_WC_Helper::number_format( $subscription->get_sign_up_fee() ),
			);

			// track activated event
			$this->get_integration()->api_record_event( $this->get_integration()->event_name['activated_subscription'], $properties, $identity );

			// used to increment the timestamp for each line item API call, otherwise KM considers them duplicates ಠ_ಠ
			$item_count = 0;

			foreach ( $subscription->get_items() as $line_item ) {

				$product = wc_get_product( ( ! empty( $line_item['variation_id'] ) ? $line_item['variation_id'] : $line_item['product_id'] ) );

				// line item properties
				$properties = array(
					$this->get_integration()->get_property_name( 'subscription_name', 'activated_subscription' ) => $line_item['name'],
					$this->get_integration()->get_property_name( 'subscription_price' )                          => SV_WC_Helper::number_format( $order->get_line_total( $line_item ) ),
					$this->get_integration()->get_property_name( 'subscription_period' )                         => $subscription->billing_period,
					$this->get_integration()->get_property_name( 'subscription_interval' )                       => $subscription->billing_interval,
					$this->get_integration()->get_property_name( 'subscription_length' )                         => WC_Subscriptions_Product::get_length( $product ),
					$this->get_integration()->get_property_name( 'subscription_trial_period' )                   => WC_Subscriptions_Product::get_trial_period( $product ),
					$this->get_integration()->get_property_name( 'subscription_trial_length' )                   => WC_Subscriptions_Product::get_trial_length( $product ),
					'_t'                                                                                         => time() + $item_count,
				);

				// add legacy (unscoped) subscription name property
				if ( $this->get_integration()->is_legacy() ) {
					$properties[ $this->get_integration()->get_property_name( 'subscription_name') ] = $line_item['name'];
				}

				// track individual subscription line items
				$this->get_integration()->api_set_properties( $properties, $identity );

				$item_count++;
			}
		}
	}


	/**
	 * Track subscription re-activations (on-hold to active status)
	 *
	 * @since 1.6.1
	 * @param \WC_Subscription $subscription
	 */
	public function reactivated_subscription( $subscription ) {

		$this->enable_tracking();

		$subscription_name = $this->get_subscription_name( $subscription );

		$properties = array(
			$this->get_integration()->get_property_name( 'subscription_id', 'reactivated_subscription' )   => $subscription->id,
			$this->get_integration()->get_property_name( 'subscription_name', 'reactivated_subscription' ) => $subscription_name,
		);

		// add legacy (unscoped) subscription name property
		if ( $this->get_integration()->is_legacy() ) {
			$properties[ $this->get_integration()->get_property_name( 'subscription_name' ) ] = $subscription_name;
		}

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['reactivated_subscription'], $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
	}


	/**
	 * Track subscription suspensions (on-hold status)
	 *
	 * @since 1.6.1
	 * @param \WC_Subscription $subscription
	 */
	public function suspended_subscription( $subscription ) {

		$this->enable_tracking();

		$subscription_name = $this->get_subscription_name( $subscription );

		$properties = array(
			$this->get_integration()->get_property_name( 'subscription_id', 'suspended_subscription' )   => $subscription->id,
			$this->get_integration()->get_property_name( 'subscription_name', 'suspended_subscription' ) => $subscription_name,
		);

		// add legacy (unscoped) subscription name property
		if ( $this->get_integration()->is_legacy() ) {
			$properties[ $this->get_integration()->get_property_name( 'subscription_name' ) ] = $subscription_name;
		}

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['suspended_subscription'], $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
	}


	/**
	 * Track subscription cancellations (cancelled status)
	 *
	 * @since 1.6.1
	 * @param \WC_Subscription $subscription
	 */
	public function cancelled_subscription( $subscription ) {

		$this->enable_tracking();

		$subscription_name = $this->get_subscription_name( $subscription );

		$properties = array(
			$this->get_integration()->get_property_name( 'subscription_id', 'cancelled_subscription' )   => $subscription->id,
			$this->get_integration()->get_property_name( 'subscription_name', 'cancelled_subscription' ) => $subscription_name,
		);

		// add legacy (unscoped) subscription name property
		if ( $this->get_integration()->is_legacy() ) {
			$properties[ $this->get_integration()->get_property_name( 'subscription_name' ) ] = $subscription_name;
		}

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['cancelled_subscription'], $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
	}


	/**
	 * Track subscription trial end
	 *
	 * @since 1.6.1
	 * @param int|string $subscription_id
	 */
	public function subscription_trial_ended( $subscription_id ) {

		$subscription = wcs_get_subscription( $subscription_id );

		$this->enable_tracking();

		$subscription_name = $this->get_subscription_name( $subscription );

		$properties = array(
			$this->get_integration()->get_property_name( 'subscription_id', 'subscription_trial_ended' )   => $subscription->id,
			$this->get_integration()->get_property_name( 'subscription_name', 'subscription_trial_ended' ) => $subscription_name,
		);

		// add legacy (unscoped) subscription name property
		if ( $this->get_integration()->is_legacy() ) {
			$properties[ $this->get_integration()->get_property_name( 'subscription_name' ) ] = $subscription_name;
		}

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['subscription_trial_ended'], $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );

		// extra event for handling trial conversions to paying customers, check if subscription has more than a single completed payment
		// and assume the trial converted since the customer didn't cancel
		if ( $subscription->get_completed_payment_count() > 1 ) {
			$this->get_integration()->api_record_event( 'subscription trial converted', $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
		} else {
			$this->get_integration()->api_record_event( 'subscription trial cancelled', $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
		}
	}


	/**
	 * Track the end of pre-paid term action for a subscription. This is triggered
	 * when a subscription is cancelled prior to the end date (e.g. cancelled 14 days
	 * into a monthly subscription, and the month has been paid for up-front)
	 *
	 * @since 1.6.1
	 * @param int|string $subscription_id
	 */
	public function subscription_end_of_prepaid_term( $subscription_id ) {

		$subscription = wcs_get_subscription( $subscription_id );

		$this->enable_tracking();

		$subscription_name = $this->get_subscription_name( $subscription );

		$properties = array(
			$this->get_integration()->get_property_name( 'subscription_id', 'subscription_end_of_prepaid_term' )   => $subscription->id,
			$this->get_integration()->get_property_name( 'subscription_name', 'subscription_end_of_prepaid_term' ) => $subscription_name,
		);

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['subscription_end_of_prepaid_term'], $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
	}


	/**
	 * Track subscription expiration
	 *
	 * @since 1.6.1
	 * @param int|string $subscription_id
	 */
	public function subscription_expired( $subscription_id ) {

		$subscription = wcs_get_subscription( $subscription_id );

		$this->enable_tracking();

		$subscription_name = $this->get_subscription_name( $subscription );

		$properties = array(
			$this->get_integration()->get_property_name( 'subscription_id', 'subscription_expired' )   => $subscription->id,
			$this->get_integration()->get_property_name( 'subscription_name', 'subscription_expired' ) => $subscription_name,
		);

		// add legacy (unscoped) subscription name property
		if ( $this->get_integration()->is_legacy() ) {
			$properties[ $this->get_integration()->get_property_name( 'subscription_name' ) ] = $subscription_name;
		}

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['subscription_expired'], $properties, $this->get_integration()->get_identity( $subscription->get_user_id() ) );
	}


	/**
	 * Track subscription renewal payments
	 *
	 * @since 1.6.1
	 * @param int|string $renewal_order_id
	 */
	public function renewed_subscription( $renewal_order_id ) {

		$this->enable_tracking();

		$renewal_order = wc_get_order( $renewal_order_id );
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order );

		if ( empty( $subscriptions ) ) {
			return;
		}

		foreach ( $subscriptions as $subscription ) {

			$properties = array(
				$this->get_integration()->get_property_name( 'billing_amount' ) => SV_WC_Helper::number_format( $renewal_order->get_total() ),
				$this->get_integration()->get_property_name( 'billing_description' ) => $this->get_subscription_name( $subscription ),
			);

			$this->get_integration()->api_record_event( $this->get_integration()->event_name['renewed_subscription'], $properties, $this->get_integration()->get_identity( $renewal_order->get_user_id() ) );
		}
	}


	/**
	 * Get the name for a subscription which is a comma-delimited string of
	 * the line items
	 *
	 * @since 1.6.1
	 * @param \WC_Subscription $subscription
	 * @return string
	 */
	protected function get_subscription_name( $subscription ) {

		$items = array();

		foreach ( $subscription->get_items() as $line_item ) {

			$items[] = $line_item['name'];
		}

		return implode( ', ', $items );
	}


	/** Subscriptions 1.5.x support *******************************************/


	/**
	 * Track subscription activations (only after successful payment for subscription)
	 *
	 * @since 1.1
	 * @param \WC_Order $order
	 */
	public function activated_subscription_1_5( $order ) {

		$this->enable_tracking();

		if ( ! is_object( $order ) ) {
			$order = wc_get_order( $order );
		}

		// set properties
		$properties = apply_filters( 'wc_kissmetrics_activated_subscription_properties',
			array(
				'subscription_name'         => WC_Subscriptions_Order::get_item_name( $order ),
				'total_initial_payment'     => WC_Subscriptions_Order::get_total_initial_payment( $order ),
				'initial_sign_up_fee'       => WC_Subscriptions_Order::get_sign_up_fee( $order ),
				'subscription_period'       => WC_Subscriptions_Order::get_subscription_period( $order ),
				'subscription_interval'     => WC_Subscriptions_Order::get_subscription_interval( $order ),
				'subscription_length'       => WC_Subscriptions_Order::get_subscription_length( $order ),
				'subscription_trial_period' => WC_Subscriptions_Order::get_subscription_trial_period( $order ),
				'subscription_trial_length' => WC_Subscriptions_Order::get_subscription_trial_length( $order )
			), $order, $this
		);

		// record event
		$this->get_integration()->api_record_event( $this->get_integration()->event_name['activated_subscription'],
			array(
				$this->get_integration()->property_name['subscription_name']         => $properties['subscription_name'],
				$this->get_integration()->property_name['total_initial_payment']     => $properties['total_initial_payment'],
				$this->get_integration()->property_name['initial_sign_up_fee']       => $properties['initial_sign_up_fee'],
				$this->get_integration()->property_name['subscription_period']       => $properties['subscription_period'],
				$this->get_integration()->property_name['subscription_interval']     => $properties['subscription_interval'],
				$this->get_integration()->property_name['subscription_length']       => $properties['subscription_length'],
				$this->get_integration()->property_name['subscription_trial_period'] => $properties['subscription_trial_period'],
				$this->get_integration()->property_name['subscription_trial_length'] => $properties['subscription_trial_length'],
			),
			$this->get_integration()->get_identity( $order->get_user_id() )
		);
	}


	/**
	 * Track subscription trial end
	 *
	 * @since  1.1
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public function subscription_trial_ended_1_5( $user_id, $subscription_key ) {

		$this->enable_tracking();

		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );

		// bail if order id isn't available
		if ( ! isset( $subscription['order_id'] ) ) {
			return;
		}

		// Set properties
		$properties = array(
			$this->get_integration()->property_name['subscription_name'] => WC_Subscriptions_Order::get_item_name( $subscription['order_id'] )
		);

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['subscription_trial_ended'], $properties, $this->get_integration()->get_identity( $user_id ) );

		// grab the item so we can check if the payment is completed
		$item = WC_Subscriptions_Order::get_item_by_subscription_key( $subscription_key );

		// extra event for handling trial conversions to paying customers, check if subscription has a single payment completed
		// and assume the trial converted since the customer didn't cancel
		if ( isset( $item['subscription_completed_payments'] ) && 1 === count( $item['subscription_completed_payments'] ) ) {
			$this->get_integration()->api_record_event( 'subscription trial converted', $properties, $this->get_integration()->get_identity( $user_id ) );
		} else {
			$this->get_integration()->api_record_event( 'subscription trial cancelled', $properties, $this->get_integration()->get_identity( $user_id ) );
		}
	}


	/**
	 * Track subscription expiration
	 *
	 * @since 1.1
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public function subscription_expired_1_5( $user_id, $subscription_key ) {

		$this->enable_tracking();

		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );

		// bail if order id isn't available
		if( ! isset( $subscription['order_id'] ) ) {
			return;
		}

		// Set properties
		$properties = array(
			$this->get_integration()->property_name['subscription_name'] => WC_Subscriptions_Order::get_item_name( $subscription['order_id'] )
		);

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['subscription_expired'], $properties, $this->get_integration()->get_identity( $user_id ) );
	}


	/**
	 * Track subscription suspension
	 *
	 * @since 1.1
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public function suspended_subscription_1_5( $user_id, $subscription_key ) {

		$this->enable_tracking();

		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );

		// bail if order id isn't available
		if( ! isset( $subscription['order_id'] ) ) {
			return;
		}

		// Set properties
		$properties = array(
			$this->get_integration()->property_name['subscription_name'] => WC_Subscriptions_Order::get_item_name( $subscription['order_id'] )
		);

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['suspended_subscription'], $properties, $this->get_integration()->get_identity( $user_id ) );
	}


	/**
	 * Track subscription reactivation
	 *
	 * @since 1.1
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public function reactivated_subscription_1_5( $user_id, $subscription_key ) {

		$this->enable_tracking();

		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );

		// bail if order id isn't available
		if( ! isset( $subscription['order_id'] ) ) {
			return;
		}

		// Set properties
		$properties = array(
			$this->get_integration()->property_name['subscription_name'] => WC_Subscriptions_Order::get_item_name( $subscription['order_id'] )
		);

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['reactivated_subscription'], $properties, $this->get_integration()->get_identity( $user_id ) );
	}


	/**
	 * Track subscription cancellation
	 *
	 * @since 1.1
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public function cancelled_subscription_1_5( $user_id, $subscription_key ) {

		$this->enable_tracking();

		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );

		// bail if order id isn't available
		if( ! isset( $subscription['order_id'] ) ) {
			return;
		}

		// Set properties
		$properties = array(
			$this->get_integration()->property_name['subscription_name'] => WC_Subscriptions_Order::get_item_name( $subscription['order_id'] )
		);

		$this->get_integration()->api_record_event( $this->get_integration()->event_name['cancelled_subscription'], $properties, $this->get_integration()->get_identity( $user_id ) );
	}


	/**
	 * Track renewal order generated from active subscription (either automatically or manually from customer payment)
	 *
	 * @since 1.1
	 * @param int $renewal_order_id The renewal order id
	 */
	public function renewed_subscription_1_5( $renewal_order_id ) {

		$this->enable_tracking();

		$renewal_order = wc_get_order( $renewal_order_id );
		$parent_order  = WC_Subscriptions_Renewal_Order::get_parent_order( $renewal_order );

		// there should only be one subscription in the renewal order, but just in case, we loop though all items
		foreach ( $renewal_order->get_items() as $item ) {

			$item_id = WC_Subscriptions_Order::get_items_product_id( $item );

			if ( WC_Subscriptions_Order::is_item_subscription( $parent_order, $item_id ) ) {

				$product = $renewal_order->get_product_from_item( $item );

				// set properties
				$properties = array(
					$this->get_integration()->property_name['billing_amount']      => $renewal_order->get_total(),
					$this->get_integration()->property_name['order_total']         => $renewal_order->get_total(),
					$this->get_integration()->property_name['billing_description'] => WC_Subscriptions_Order::get_item_name( $parent_order, $product->get_id() ),
				);

				$this->get_integration()->api_record_event( $this->get_integration()->event_name['renewed_subscription'], $properties, $this->get_integration()->get_identity( $renewal_order->user_id ) );
			}
		}
	}


	/**
	 * Add subscriptions-specific event & property name settings
	 *
	 * @since 1.6.1
	 * @param array $settings
	 * @return array
	 */
	public function add_settings( $settings ) {

		$subscription_settings = array(

			'subscription_event_names_section'        => array(
				'title'       => __( 'Subscription Event Names', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Customize the event names for Subscription events. Leave a field blank to disable tracking of that event.', 'woocommerce-kiss-metrics' ),
				'type'        => 'section',
			),
			'activated_subscription_event_name'       => array(
				'title'       => __( 'Activated Subscription', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer activates their subscription.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'activated subscription',
			),
			'subscription_trial_ended_event_name'     => array(
				'title'       => __( 'Subscription Free Trial Ended', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a the free trial ends for a subscription.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription trial ended',
			),
			'subscription_end_of_prepaid_term_event_name'         => array(
				'title'       => __( 'Subscription End of Pre-Paid Term', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when the end of a pre-paid term for a previously cancelled subscription is reached.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription prepaid term ended',
			),
			'subscription_expired_event_name'         => array(
				'title'       => __( 'Subscription Expired', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a subscription expires.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription expired',
			),
			'suspended_subscription_event_name'       => array(
				'title'       => __( 'Suspended Subscription', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer suspends their subscription.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'suspended subscription',
			),
			'reactivated_subscription_event_name'     => array(
				'title'       => __( 'Reactivated Subscription', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer reactivates their subscription.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'reactivated subscription',
			),
			'cancelled_subscription_event_name'       => array(
				'title'       => __( 'Cancelled Subscription', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer cancels their subscription.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'cancelled subscription',
			),
			'renewed_subscription_event_name'         => array(
				'title'       => __( 'Renewed Subscription', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Triggered when a customer is automatically billed for a subscription renewal.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription billed',
			),
			'subscription_property_names_section'     => array(
				'title'       => __( 'Subscription Property Names', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Customize the property names for Subscription events. Leave a field blank to disable tracking of that property.', 'woocommerce-kiss-metrics' ),
				'type'        => 'section',
			),
			'subscription_name_property_name'         => array(
				'title'       => __( 'Subscription Name', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked anytime a subscription event occurs.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription name'
			),
			'subscription_id_property_name'         => array(
				'title'       => __( 'Subscription ID', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a subscription is activated.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription id'
			),
			'subscription_price_property_name'         => array(
				'title'       => __( 'Subscription Price', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked when a subscription is activated.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription price'
			),
			'total_initial_payment_property_name'     => array(
				'title'       => __( 'Total Initial Payment', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked for subscription activations. Includes the Recurring amount and Sign Up Fee.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'total initial payment'
			),
			'initial_sign_up_fee_property_name'       => array(
				'title'       => __( 'Initial Sign Up Fee', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracked for subscription activations. This will be zero if the subscription has no sign up fee.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'initial sign up fee'
			),
			'subscription_period_property_name'       => array(
				'title'       => __( 'Subscription Period', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the period (e.g. Day, Month, Year) for subscription activations.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription period'
			),
			'subscription_interval_property_name'     => array(
				'title'       => __( 'Subscription Interval', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the interval (e.g. every 1st, 2nd, 3rd, etc.) for subscription activations.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription interval'
			),
			'subscription_length_property_name'       => array(
				'title'       => __( 'Subscription Length', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the length (e.g. infinite, 12 months, 2 years, etc.) for subscription activations.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription length'
			),
			'subscription_trial_period_property_name' => array(
				'title'       => __( 'Subscription Trial Period', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the trial period (e.g. Day, Month, Year) for subscription activations with a free trial.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription trial period'
			),
			'subscription_trial_length_property_name' => array(
				'title'       => __( 'Subscription Trial Length', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the trial length (e.g. 1-90 periods) for subscription activations with a free trial.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription trial length'
			),
			'billing_amount_property_name'            => array(
				'title'       => __( 'Billing Amount for Subscription Renewal', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the amount billed to the customer when their subscription automatically renews.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription billing amount'
			),
			'billing_description_property_name'       => array(
				'title'       => __( 'Billing Description for Subscription Renewal', 'woocommerce-kiss-metrics' ),
				'description' => __( 'Tracks the name of the subscription billed to the customer when the subscription automatically renews.', 'woocommerce-kiss-metrics' ),
				'type'        => 'text',
				'default'     => 'subscription billing description'
			),
		);

		return array_merge( $settings, $subscription_settings );
	}


	/**
	 * Helper method to enable tracking in situations where tracking would
	 * normally be disabled by WC_Kissmetrics_Integration::disable_tracking(),
	 * like subscription changes by an administrator/shop manager in an admin context
	 *
	 * @since 1.6.1
	 */
	protected function enable_tracking() {

		add_filter( 'wc_kissmetrics_enable_tracking', '__return_true' );
	}


	/**
	 * Get the integration instance
	 *
	 * @since 1.6.1
	 * @return \WC_Kissmetrics_Integration
	 */
	public function get_integration() {

		return wc_kissmetrics()->get_integration();
	}


}
