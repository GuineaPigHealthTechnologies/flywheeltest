<?php
/**
 * WooCommerce One Page Checkout functions
 *
 * Functions mainly to take advantage of APIs added to newer versions of WooCommerce while maintaining backward compatibility.
 *
 * @author 	Prospress
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Set the property for a product in a version independent way.
 *
 * @since 1.4.0
 */
function wcopc_set_products_prop( $product, $prop, $value ) {
	if ( is_callable( array( $product, 'update_meta_data' ) ) ) { // WC 3.0+
		$product->update_meta_data( $prop, $value );
	} else {
		$product->{$prop} = $value;
	}
}

/**
 * Get the property for a product in a version independent way.
 *
 * @since 1.4.0
 */
function wcopc_get_products_prop( $product, $prop, $meta_key_prefix = '' ) {
	if ( is_callable( array( $product, 'get_meta' ) ) ) { // WC 3.0+
		$value = $product->get_meta( $meta_key_prefix . $prop );
	} else {
		$value = $product->{$prop};
	}

	return $value;
}

/**
 * Get the name for a product in a version independent way.
 *
 * @since 1.5.4
 */
function wcopc_get_products_name( $product ) {

	if ( is_callable( array( $product, 'get_name' ) ) ) { // WC 3.0+
		$name = $product->get_name();
	} else {
		$name = $product->get_title();
	}

	return $name;
}

/**
 * Get the type of a certain product
 *
 * @since 1.4.0
 */
function wcopc_get_product_type( $product ) {

	if ( $product->is_type( 'variable' ) ) {
		$product_type = 'variable';
	} elseif ( $product->get_type() ) {
		$product_type = $product->get_type();
	} else {
		$product_type = 'simple';
	}

	return $product_type;
}

/**
 * Get the url to remove a cart item from the cart.
 *
 * @since 1.5.4
 */
function wcopc_get_cart_remove_url( $cart_item_key ) {

	if ( is_callable( 'wc_get_cart_remove_url' ) ) {
		$url = wc_get_cart_remove_url( $cart_item_key );
	} else {
		$url = WC()->cart->get_remove_url( $cart_item_key );
	}

	return $url;
}

/**
 * Gets the cart item formatted data in a WC version compatible way.
 *
 * @since 1.5.4
 */
function wcopc_get_formatted_cart_item_data( $cart_item, $flat = false ) {

	if ( is_callable( 'wc_get_formatted_cart_item_data' ) ) {
		$item_data = wc_get_formatted_cart_item_data( $cart_item, $flat );
	} else {
		$item_data = WC()->cart->get_item_data( $cart_item );
	}

	return $item_data;
}
