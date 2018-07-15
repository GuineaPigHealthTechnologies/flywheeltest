<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_First_Order extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'first_order', "This is user's first order.", 5 );
	}

	/**
	 * Do the required check in this method. The method must return a boolean.
	 * Check if this is user's first order.
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

		// Get the amount
		$order_amount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(P.`ID`)
 			FROM $wpdb->postmeta PM
 			INNER JOIN $wpdb->posts P ON P.`ID` = PM.`post_id`
 			WHERE PM.`meta_key` = '_billing_email' AND PM.`meta_value` = %s AND P.`post_type` = 'shop_order'
			AND P.`post_status` IN ( 'wc-" . implode( "','wc-", apply_filters( 'wc_af_high_value_value_order_statuses', array( 'completed' ) ) ) . "' ) ;", ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email() ) ) );

		// Risk is true if order amount is smaller than 2
		if ( $order_amount < 2 ) {
			$risk = true;
		}

		return $risk;
	}

}
