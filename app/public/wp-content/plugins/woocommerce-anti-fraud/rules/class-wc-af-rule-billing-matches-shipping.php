<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Rule_Billing_Matches_Shipping' ) ) {
	class WC_AF_Rule_Billing_Matches_Shipping extends WC_AF_Rule {

		/**
		 * The constructor
		 */
		public function __construct() {
			parent::__construct( 'billing_matches_shipping', 'Billing address does not match shipping address', 20 );
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

			// Check if the billing address does not match shipping address
			if ( $order->get_formatted_billing_address() != $order->get_formatted_shipping_address() ) {
				$risk = true;
			}

			return $risk;
		}

	}
}