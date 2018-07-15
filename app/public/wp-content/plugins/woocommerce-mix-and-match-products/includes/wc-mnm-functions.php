<?php
/**
 * Core Functions
 *
 * Cart/order item relationship functions.
 *
 * @author   SomewhereWarm
 * @category Core
 * @package  WooCommerce Mix and Match Products/Functions
 * @since    1.2.0
 * @version  1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*---------------*/
/*  Cart.        */
/*---------------*/

/**
 * Given a bundled MnM cart item, find and return its container cart item or its cart ID when the $return_id arg is true.
 *
 * @since  1.2.0
 *
 * @param  array    $bundled_cart_item
 * @param  array    $cart_contents
 * @param  bool  $return_id
 * @return mixed
 */
function wc_mnm_get_mnm_cart_item_container( $bundled_cart_item, $cart_contents = false, $return_id = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = WC()->cart->cart_contents;
	}

	$container = false;

	if ( wc_mnm_maybe_is_mnm_cart_item( $bundled_cart_item ) ) {

		$bundled_by = $bundled_cart_item[ 'mnm_container' ];

		if ( isset( $cart_contents[ $bundled_by ] ) ) {
			$container = $return_id ? $bundled_by : $cart_contents[ $bundled_by ];
		}
	}

	return $container;
}

/**
 * Given a MnM container cart item, find and return its child cart items - or their cart IDs when the $return_ids arg is true.
 *
 * @since  1.2.0
 *
 * @param  array    $container_cart_item
 * @param  array    $cart_contents
 * @param  bool  $return_ids
 * @return mixed
 */
function wc_mnm_get_mnm_cart_items( $container_cart_item, $cart_contents = false, $return_ids = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = WC()->cart->cart_contents;
	}

	$bundled_cart_items = array();

	if ( wc_mnm_is_mnm_container_cart_item( $container_cart_item ) ) {

		$bundled_items = $container_cart_item[ 'mnm_contents' ];

		if ( ! empty( $bundled_items ) && is_array( $bundled_items ) ) {
			foreach ( $bundled_items as $bundled_cart_item_key ) {
				if ( isset( $cart_contents[ $bundled_cart_item_key ] ) ) {
					$bundled_cart_items[ $bundled_cart_item_key ] = $cart_contents[ $bundled_cart_item_key ];
				}
			}
		}
	}

	return $return_ids ? array_keys( $bundled_cart_items ) : $bundled_cart_items;
}

/**
 * True if a cart item is bundled in a MnM bundle.
 * Instead of relying solely on cart item data, the function also checks that the alleged parent item actually exists.
 *
 * @since  1.2.0
 *
 * @param  array  $cart_item
 * @param  array  $cart_contents
 * @return bool
 */
function wc_mnm_is_mnm_cart_item( $cart_item, $cart_contents = false ) {

	$is_bundled = false;

	if ( wc_mnm_get_mnm_cart_item_container( $cart_item, $cart_contents ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if a cart item appears to be part of a MnM bundle.
 * The result is purely based on cart item data - the function does not check that a valid parent item actually exists.
 *
 * @since  1.2.0
 *
 * @param  array  $cart_item
 * @return bool
 */
function wc_mnm_maybe_is_mnm_cart_item( $cart_item ) {

	$is_bundled = false;

	if ( ! empty( $cart_item[ 'mnm_container' ] ) && ! empty( $cart_item[ 'mnm_container' ] ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if a cart item appears to be a MnM container item.
 *
 * @since  1.2.0
 *
 * @param  array  $cart_item
 * @return bool
 */
function wc_mnm_is_mnm_container_cart_item( $cart_item ) {

	$is_bundle = false;

	if ( isset( $cart_item[ 'mnm_contents' ] ) && isset( $cart_item[ 'mnm_config' ] ) ) {
		$is_bundle = true;
	}

	return $is_bundle;
}


/*---------------*/
/*  Orders.      */
/*---------------*/

/**
 * Given a MnM child order item, find and return its container order item or its order item ID when the $return_id arg is true.
 *
 * @since  1.2.0
 *
 * @param  array     $bundled_order_item
 * @param  mixed 	 array|object $order array of order items or WC_Order
 * @param  bool   	 $return_id
 * @return mixed
 */
function wc_mnm_get_mnm_order_item_container( $bundled_order_item, $order = false, $return_id = false ) {

	$container = false;

	if ( wc_mnm_maybe_is_mnm_order_item( $bundled_order_item ) ) {

		if ( false === $order ) {
			if ( is_callable( array( $bundled_order_item, 'get_order' ) ) ) {

				$order_id = $bundled_order_item->get_order_id();
				$order    = WC_Mix_and_Match_Helpers::cache_get( 'order_' . $order_id );

				if ( null === $order ) {
					$order = $bundled_order_item->get_order();
					WC_Mix_and_Match_Helpers::cache_set( 'order_' . $order_id, $order );
				}

			} else {
				$msg = 'get_order() is not callable on the supplied $order_item. No $order object given.';
				_doing_it_wrong( __FUNCTION__ . '()', $msg, '1.3.0' );
			}
		}

		$order_items = is_object( $order ) ? $order->get_items( 'line_item' ) : $order;

		if ( ! empty( $order_items ) ) {
			foreach ( $order_items as $order_item_id => $order_item ) {

				$is_container = isset( $order_item[ 'mnm_cart_key' ] ) && $bundled_order_item[ 'mnm_container' ] === $order_item[ 'mnm_cart_key' ];

				if ( $is_container ) {
					$container = $return_id ? $order_item_id : $order_item;
				}
			}
		}
	}

	return $container;
}

/**
 * Given a MnM container order item, find and return its child order items - or their order item IDs when the $return_ids arg is true.
 *
 * @since  1.2.0
 *
 * @param  array     	$container_order_item
 * @param  array|object $order array of order items or WC_Order
 * @param  bool   		$return_ids
 * @return mixed
 */
function wc_mnm_get_mnm_order_items( $container_order_item, $order = false, $return_ids = false ) {

	$bundled_order_items = array();

	if ( wc_mnm_is_mnm_container_order_item( $container_order_item ) ) {

		if ( false === $order ) {
			if ( is_callable( array( $container_order_item, 'get_order' ) ) ) {

				$order_id = $container_order_item->get_order_id();
				$order    = WC_Mix_and_Match_Helpers::cache_get( 'order_' . $order_id );

				if ( null === $order ) {
					$order = $container_order_item->get_order();
					WC_Mix_and_Match_Helpers::cache_set( 'order_' . $order_id, $order );
				}

			} else {
				$msg = 'get_order() is not callable on the supplied $order_item. No $order object given.';
				_doing_it_wrong( __FUNCTION__ . '()', $msg, '5.3.0' );
			}
		}

		$order_items = is_object( $order ) ? $order->get_items( 'line_item' ) : $order;

		if ( ! empty( $order_items ) ) {
			foreach ( $order_items as $order_item_id => $order_item ) {

				$is_child = ! empty( $order_item[ 'mnm_container' ] ) && isset( $container_order_item[ 'mnm_cart_key' ] ) && $order_item[ 'mnm_container' ] === $container_order_item[ 'mnm_cart_key' ];

				if ( $is_child ) {
					$bundled_order_items[ $order_item_id ] = $order_item;
				}
			}
		}
	}

	return $return_ids ? array_keys( $bundled_order_items ) : $bundled_order_items;
}

/**
 * True if an order item is part of a MnM bundle.
 * Instead of relying solely on the existence of item meta, the function also checks that the alleged parent item actually exists.
 *
 * @since  1.2.0
 *
 * @param  array     $order_item
 * @param  mixed 	 array|object $order array of order items or WC_Order
 * @return bool
 */
function wc_mnm_is_mnm_order_item( $order_item, $order = false ) {

	$is_bundled = false;

	if ( wc_mnm_get_mnm_order_item_container( $order_item, $order ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if an order item appears to be part of a MnM bundle.
 * The result is purely based on item meta - the function does not check that a valid parent item actually exists.
 *
 * @since  1.2.0
 *
 * @param  array  $order_item
 * @return bool
 */
function wc_mnm_maybe_is_mnm_order_item( $order_item ) {

	$is_bundled = false;

	if ( ! empty( $order_item[ 'mnm_container' ] ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if an order item appears to be a MnM container item.
 *
 * @since  1.2.0
 *
 * @param  array  $order_item
 * @return bool
 */
function wc_mnm_is_mnm_container_order_item( $order_item ) {

	$is_bundle = false;

	if ( isset( $order_item[ 'mnm_config' ] ) ) {
		$is_bundle = true;
	}

	return $is_bundle;
}


/**
 * Given a MnM container, return the prompt for properly filling a container.
 *
 * @since  1.2.0
 *
 * @param  obj    $product
 * @return string
 */
function wc_mnm_get_quantity_message( $product ) {

	$min_container_size = $product->get_min_container_size();
	$max_container_size = $product->get_max_container_size();
	$message = '';

	// No items required.
	if( $min_container_size === 0 ){
		$message = '';
	// Fixed container size.
	} else if ( $min_container_size > 0 && $max_container_size > 0 && $min_container_size == $max_container_size ){
		$message = sprintf( _n( 'Please select %s item to continue&hellip;', 'Please select %s items to continue&hellip;', $min_container_size, 'woocommerce-mix-and-match-products' ), $min_container_size );
	// Required minimum and required maximum, but unequal min/max.
	} else if ( $min_container_size > 0 && $max_container_size > 0 ){
		$message = sprintf( __( 'Please choose between %d and %d items to continue&hellip;', 'woocommerce-mix-and-match-products' ), $min_container_size, $max_container_size );
	// Required minimum.
	} else if ( $min_container_size > 0 ){
		$message = sprintf( _n( 'Please choose at least %d item to continue&hellip;', 'Please choose at least %d items to continue&hellip;', $min_container_size, 'woocommerce-mix-and-match-products' ), $min_container_size );
	// Required maximum.
	} else if ( $max_container_size > 0 ){
		$message = sprintf( _n( 'Please choose fewer than %d item to continue&hellip;', 'Please choose fewer than %d items to continue&hellip;', $max_container_size, 'woocommerce-mix-and-match-products' ), $max_container_size );
	}

	/**
	 * Container quantity error message.
	 *
	 * @param  str $message
	 * @param  obj $product WC_Product.
	 */
	return apply_filters( 'woocommerce_mnm_container_quantity_message', $message, $product );

}
