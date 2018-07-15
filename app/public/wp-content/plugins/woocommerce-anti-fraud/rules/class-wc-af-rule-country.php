<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Country extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'country', 'Ordered from a risk country', 25 );
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

		// Orders from these countries are considered a risk unless the shop is located in the same country
		$risk_countries = apply_filters( 'wc_af_rule_countries', array( 'CN', 'NG', 'KP' ) );

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$billing_country = $order->billing_country;
			$shipping_country = $order->shipping_country;
		} else {
			$billing_country = $order->get_billing_country();
			$shipping_country = $order->get_shipping_country();
		}

		// Default risk is false
		$risk = false;
		// Check if the billing or shipping country is considered a risk country
		if ( ( true === in_array( $billing_country, $risk_countries ) ) || ( true === in_array( $shipping_country, $risk_countries ) ) ) {
			$risk = true;
		}

		if ( true === $risk ) {

			// Get store country
			$store_country = WC()->countries->get_base_country();

			// There is no risk if the billing and shipping country are equal to the store country
			if ( $store_country == $billing_country && $store_country == $shipping_country ) {
				$risk = false;
			}
		}

		return $risk;
	}

}
