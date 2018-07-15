<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Detect_Proxy extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'detect_proxy', 'Customer ordered from behind a proxy.', 50 );
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

		// The easiest proxy check, obviously this header isn't sent by proxies that claim to 'anonymize' you
		if ( isset( $_SERVER['X-Forwarded-For'] ) ) {
			$risk = true;
		}

		// Only do DNSBL check if proxy risk not detected yet
		if ( false == $risk ) {

			// User IP address
			$ip = WC_AF_Score_Helper::get_ip_address();

			//list of DNSBL's
			$dnsbl_lookup = array( "dnsbl-1.uceprotect.net", "dnsbl-2.uceprotect.net", "dnsbl-3.uceprotect.net", "dnsbl.dronebl.org", "dnsbl.sorbs.net", "zen.spamhaus.org" );

			// Revert IP address
			$reverse_ip = implode( ".", array_reverse( explode( ".", $ip ) ) );

			// Loop DNS blacklists
			foreach ( $dnsbl_lookup as $host ) {

				// Check DNS records corresponding to a given Internet host name or IP address
				if ( checkdnsrr( $reverse_ip . "." . $host . ".", "A" ) ) {
					$risk = true;
				}

			}

		}

		return $risk;
	}

}