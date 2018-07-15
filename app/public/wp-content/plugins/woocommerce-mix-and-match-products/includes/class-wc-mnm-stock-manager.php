<?php
/**
 * Stock Manager
 *
 * @author   SomewhereWarm
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Stock
 * @since    1.0.5
 * @version  1.0.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Stock_Manager Class.
 *
 * Used to create and store a product_id / variation_id representation of a product collection based on the included items' inventory requirements.
 */
class WC_Mix_and_Match_Stock_Manager {

	/**
	 * The collection of items in the container.
	 * @var str
	 */
	private $items;

	/**
	 * Total quantity of items in the container.
	 * @var str
	 */
	private $total_qty;
	
	/**
	 * The Mix and Match Product Object.
	 * @var obj WC_Product
	 */
	public $product;

	public function __construct( $product ) {

		$this->items  = array();
		$this->total_qty = 0;
		$this->product = $product;
	}

	/**
	 * Add a product to the collection.
	 *
	 * @param int          $product_id
	 * @param false|int    $variation_id
	 * @param int      	   $quantity
	 */
	public function add_item( $product_id, $variation_id = false, $quantity = 1 ) {

		$this->items[] = new WC_Mix_and_Match_Stock_Manager_Item( $product_id, $variation_id, $quantity );

		// update the total of items in the container
		$this->total_qty += $quantity;
	}

	/**
	 * Return the items of this collection.
	 *
	 * @return array
	 */
	public function get_items() {

		if ( ! empty( $this->items ) ) {
			return $this->items;
		}

		return array();
	}

	/**
	 * Return the items of this collection.
	 *
	 * @return array
	 */
	public function get_total_quantity() {

		return $this->total_qty;

	}

	/**
	 * Merge another collection with this one.
	 *
	 * @param WC_Mix_and_Match_Stock_Manager  $stock
	 * @return bool | Whether successfully added
	 */
	public function add_stock( $stock ) {

		if ( ! is_object( $stock ) ) {
			return false;
		}

		$items_to_add = $stock->get_items();

		if ( ! empty( $items_to_add ) ) {
			foreach ( $items_to_add as $item ) {
				$this->items[] = $item;

				// Update the total of items in the container.
				$this->total_qty += $item->quantity;
			}
			return true;
		}

		return false;
	}

	/**
	 * Return the stock requirements of the items in this collection.
	 * To validate stock accurately, this method is used to add quantities and build a list of product/variation ids to check.
	 * Note that in some cases, stock for a variation might be managed by the parent - this is tracked by the managed_by_id property in WC_Mix_and_Match_Stock_Manager_Item.
	 *
	 * @return array
	 */
	public function get_managed_items() {

		$managed_items = array();

		if ( ! empty( $this->items ) ) {

			foreach ( $this->items as $purchased_item ) {

				$managed_by_id = $purchased_item->managed_by_id;

				if ( isset( $managed_items[ $managed_by_id ] ) ) {

					$managed_items[ $managed_by_id ][ 'quantity' ] += $purchased_item->quantity;

				} else {

					$managed_items[ $managed_by_id ][ 'quantity' ] = $purchased_item->quantity;

					if ( $purchased_item->variation_id && $purchased_item->variation_id == $managed_by_id ) {
						$managed_items[ $managed_by_id ][ 'is_variation' ] = true;
						$managed_items[ $managed_by_id ][ 'product_id' ]   = $purchased_item->product_id;
					} else {
						$managed_items[ $managed_by_id ][ 'is_variation' ] = false;
					}
				}
			}
		}

		return $managed_items;
	}

	/**
	 * Validate that all managed items in the collection are in stock.
	 *
	 * @param  bool $updating_cart
	 * @return bool
	 */
	public function validate_stock( $updating_cart = false ) {

		$managed_items = $this->get_managed_items();

		if ( empty( $managed_items ) ) {
			return true;
		}

		if ( ! isset( $this->product ) || ! is_object( $this->product ) ) {
			if ( WP_DEBUG ) {
				trigger_error( 'WC_Mix_and_Match_Stock_Manager class instantiated with invalid constructor arguments.' );
			}
			return false;
		}

		// Get quantities of items already in cart: returns array of IDs => quantity.
		$cart_quantities = WC()->cart->get_cart_item_quantities();

		foreach ( $managed_items as $managed_item_id => $managed_item ) {

			$quantity_in_cart      = isset( $cart_quantities[ $managed_item_id ] ) ? $cart_quantities[ $managed_item_id ] : 0;
			$quantity_in_container = $managed_item[ 'quantity' ];
			$error_message         = false;

			$managed_product       = wc_get_product( $managed_item_id );
			$item_title            = $managed_product->get_title();

			if ( $managed_product->is_type( 'variation' ) && $managed_product->managing_stock() ) {
				$item_title .= ' &ndash; ' . wc_get_formatted_variation( $managed_product, true );
			}

			// Check if product is_sold_individually.
			if ( $managed_product->is_sold_individually() ) {
				if ( ( ! $updating_cart && $quantity_in_container > 1 ) ) {
					wc_add_notice( sprintf( __( 'The configuration you have selected cannot be added to the cart &mdash; only 1 &quot;%s&quot; may be purchased.', 'woocommerce-mix-and-match-products' ), $item_title ), 'error' );
					return false;
				} else if ( $updating_cart && ( $quantity_in_cart + $quantity_in_container ) > 1 ) {
					wc_add_notice( sprintf( __( '&quot;%s&quot; cannot be purchased as configured &mdash; only 1 &quot;%s&quot; may be purchased.', 'woocommerce-mix-and-match-products' ), $this->product->get_title(), $item_title ), 'error' );
					return false;
				}
			}

			// In-stock check: a product may be marked as "Out of stock", but has_enough_stock() may still return a number.
			// We also need to take into account the 'woocommerce_notify_no_stock_amount' setting.
			if ( ! $managed_product->is_in_stock() ) {

				if ( $updating_cart ) {
					$error_message = sprintf( __( '&quot;%s&quot; cannot be purchased as configured since &quot;%s&quot; is out of stock.', 'woocommerce-mix-and-match-products' ), $this->product->get_title(), $item_title );
				} else {
					$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart &mdash; &quot;%s&quot; is out of stock.', 'woocommerce-mix-and-match-products' ), $item_title );
				}

			// Not enough stock for this configuration.
			} elseif ( ! $managed_product->has_enough_stock( $quantity_in_cart + $quantity_in_container ) ) {

				if ( $updating_cart ) {
					$error_message = sprintf( __( 'Your &quot;%s&quot; cannot be purchased as configured since there is not enough stock of &quot;%s&quot; &mdash; we have %s in stock and you are trying to add %s to your cart.', 'woocommerce-mix-and-match-products' ), $this->product->get_title(), $item_title, $managed_product->get_stock_quantity(), $quantity_in_cart + $quantity_in_container );
				} else {
					if ( $quantity_in_cart && $quantity_in_container ) {
						$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart since there is not enough stock of &quot;%s&quot; &mdash; we have %s in stock and you already have %s in your cart.', 'woocommerce-mix-and-match-products' ), $item_title, $managed_product->get_stock_quantity(), $quantity_in_cart );
					} elseif ( $quantity_in_container ) {
						$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart since there is not enough stock of &quot;%s&quot; &mdash; we have %s in stock and you are trying to add %s to your cart.', 'woocommerce-mix-and-match-products' ), $item_title, $managed_product->get_stock_quantity(), $quantity_in_container );
					}
				}
			}

			if ( $error_message ) {
				// If there are items in the cart - add a link to the cart.
				if ( ! empty( $cart_quantities ) ) {
					sprintf(
					'<a href="%s" class="button wc-forward">%s</a> %s',
					wc_get_cart_url(),
					__( 'View Cart', 'woocommerce-mix-and-match-products' ),
					$error_message
					);
				}

				wc_add_notice( $error_message, 'error' );
				return false;
			}
		}

		return true;
	}
}

/**
 * Maps a product/variation in the collection to the item managing stock for it.
 * These 2 will differ only if stock for a variation is managed by its parent.
 *
 * @class    WC_Mix_and_Match_Stock_Manager_Item
 * @version  1.0.5
 * @since    1.0.5
 */
class WC_Mix_and_Match_Stock_Manager_Item {

	/**
	 * Product ID.
	 * 
	 * @var int
	 */
	public $product_id;
	
	/**
	 * Varitation ID.
	 * 
	 * @var int
	 */
	public $variation_id;
	
	/**
	 * Quantity of Item in Container.
	 * 
	 * @var int
	 */
	public $quantity;

	/**
	 * The variation or product ID that manages the stock for this item.
	 * 
	 * @var int
	 */	
	public $managed_by_id;

	/**
	 * __construct function.
	 * 
	 * @param int $product_id
	 * @param int $variation_id
	 * @param int $quantity
	 */
	
	public function __construct( $product_id, $variation_id = false, $quantity = 1 ) {

		$this->product_id   = $product_id;
		$this->variation_id = $variation_id;
		$this->quantity     = $quantity;

		if ( $variation_id ) {

			$variation_stock = get_post_meta( $variation_id, '_stock', true );

			// If stock is managed at variation level.
			if ( isset( $variation_stock ) && $variation_stock !== '' ) {
				$this->managed_by_id = $variation_id;
			// Otherwise stock is managed by the parent.
			} else {
				$this->managed_by_id = $product_id;
			}

		} else {
			$this->managed_by_id = $product_id;
		}
	}
}
