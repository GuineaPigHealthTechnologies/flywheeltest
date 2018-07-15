<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_International_Order extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'international_order', 'Order is an international order.', 10 );
	}

	/**
	 * Do the required check in this method. The method must return a boolean.
	 *
	 * @param WC_Order $order
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_risk( WC_Order $order ) {

		// Default risk is false
		$risk = false;

		// Get store country
		$store_country = WC()->countries->get_base_country();

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$billing_country = $order->billing_country;
			$shipping_country = $order->shipping_country;
		} else {
			$billing_country = $order->get_billing_country();
			$shipping_country = $order->get_shipping_country();
		}

		// Check if store country differs from billing or shipping country
		if ( $store_country != $billing_country || $store_country != $shipping_country ) {
			$risk = true;
		}

		return $risk;
	}

}
