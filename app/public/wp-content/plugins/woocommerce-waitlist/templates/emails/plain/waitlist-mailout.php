<?php
/**
 * Waitlist Mailout email (plain text)
 *
 * @author		Neil Pie
 * @package		WooCommerce_Waitlist/Templates/Emails/Plain
 * @version		1.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo _x( "Hi There,", 'Email salutation', 'woocommerce-waitlist' ) . "\n\n";

echo sprintf( $back_in_stock_text, $product_title, get_bloginfo( 'title' ) ) . ". ";
echo $you_have_been_sent_text . "\n\n";
echo sprintf( $purchase_text, $product_title, '<a href="' . $product_link . '">' . $product_link . '<a>'  );

if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $product_id ) && ! $triggered_manually )
	echo "\n\n" . $remove_text;

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );