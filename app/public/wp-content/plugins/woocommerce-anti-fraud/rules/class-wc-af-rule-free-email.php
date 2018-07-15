<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Free_Email extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'free_email', 'Email is a known free email address.', 5 );
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

		$free_email_domains = apply_filters( 'wc_af_temporary_email_domains', array(
			'hotmail',
			'live',
			'gmail',
			'yahoo',
			'mail',
			'123vn',
			'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk',
			'aaemail.com',
			'webmail.aol',
			'postmaster.info.aol',
			'personal',
			'atgratis',
			'aventuremail',
			'byke',
			'lycos',
			'computermail',
			'dodgeit',
			'thedoghousemail',
			'doramail',
			'e-mailanywhere',
			'eo.yifan',
			'earthlink',
			'emailaccount',
			'zzn',
			'everymail',
			'excite',
			'expatmail',
			'fastmail',
			'flashmail',
			'fuzzmail',
			'galacmail',
			'godmail',
			'gurlmail',
			'howlermonkey',
			'hushmail',
			'icqmail',
			'indiatimes',
			'juno',
			'katchup',
			'kukamail',
			'mail',
			'mail2web',
			'mail2world',
			'mailandnews',
			'mailinator',
			'mauimail',
			'meowmail',
			'merawalaemail',
			'muchomail',
			'MyPersonalEmail',
			'myrealbox',
			'nameplanet',
			'netaddress',
			'nz11',
			'orgoo',
			'phat.co',
			'probemail',
			'prontomail',
			'rediff',
			'returnreceipt',
			'synacor',
			'walkerware',
			'walla',
			'wongfaye',
			'xasamail',
			'zapak',
			'zappo',
		) );

		// Default risk is false
		$risk = false;

		// Do the regex
		$regex_result = preg_match( "`@([a-zA-z0-9\-\_]+)(?:\.[a-zA-Z]{0,5}){0,2}$`", ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email() ), $email_domain );

		// Check if we've got a result
		if ( 1 === $regex_result ) {

			// Check if domain is in free domain array
			if ( in_array( $email_domain[1], $free_email_domains ) ) {
				$risk = true;
			}

		}

		return $risk;
	}

}
