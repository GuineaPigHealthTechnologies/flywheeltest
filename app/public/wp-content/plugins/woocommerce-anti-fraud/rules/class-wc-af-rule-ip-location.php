<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Ip_Location extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'ip_location', 'Customer IP address did not match given billing country.', 50 );
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
		global $wpdb;

		// Default risk is false
		$risk = false;

		// Set IP address in var
		$ip_address = WC_AF_Score_Helper::get_ip_address();

		// We can only do this check if there is an IP address
		if ( empty( $ip_address ) ) {
			return false;
		}

		// Do the API request
		$request = wp_remote_get( 'http://freegeoip.net/json/' . $ip_address );

		// Check for WP error
		if ( ! is_wp_error( $request ) ) {

			// Get the HTTP response
			$response = json_decode( wp_remote_retrieve_body( $request ) );

			// Check if the response is an array
			if ( is_object( $response ) ) {

				// Check billing and shipping country with IP country
				if ( $response->country_code != ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_country : $order->get_billing_country() ) ) {
					$risk = true;
				}

			}

		}

		return $risk;
	}

}
