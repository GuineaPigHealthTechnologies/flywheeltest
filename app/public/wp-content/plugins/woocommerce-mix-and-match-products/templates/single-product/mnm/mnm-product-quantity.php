<?php
/**
 * Mix and Match Product Quantity
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-quantity.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 1.3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

global $product;

$mnm_id = $mnm_item->get_id();

if ( $mnm_item->is_purchasable() && $mnm_item->is_in_stock() ) {
			
	/**
	 * The quantity input value.
	 *
	 * @param int $quantity
	 * @param obj WC_Product
	 */
	$quantity = apply_filters( 'woocommerce_mnm_quantity_input', 0, $mnm_item );
	$quantity = isset( $_REQUEST[ 'mnm_quantity' ] ) && isset( $_REQUEST[ 'mnm_quantity' ][ $mnm_id ] ) && ! empty ( $_REQUEST[ 'mnm_quantity' ][ $mnm_id ] ) ? intval( $_REQUEST[ 'mnm_quantity' ][ $mnm_id ] ) : $quantity;

	ob_start();
	woocommerce_quantity_input( array(
		'input_name'  => 'mnm_quantity[' . $mnm_id . ']',
		'input_value' => $quantity,
		'min_value'   => $product->get_child_quantity( 'min', $mnm_id ),
		'max_value'   => $product->get_child_quantity( 'max', $mnm_id )
	) );
	echo str_replace( 'class="quantity"', 'class="quantity mnm-quantity"', ob_get_clean() );

} else {
	/**
	 * Bundled child item availability message.
	 *
	 * @param str $availability
	 * @param obj WC_Product
	 */
	echo apply_filters( 'woocommerce_mnm_availability_html', $product->get_child_availability_html( $mnm_id ), $mnm_item );
}
