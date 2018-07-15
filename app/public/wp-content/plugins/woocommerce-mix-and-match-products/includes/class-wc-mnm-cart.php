<?php
/**
 * Cart Functions and Filters
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Cart
 * @since    1.0.0
 * @version  1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Cart Class.
 *
 * Functions and filters for adding Mix and Match type products to cart.
 */
class WC_Mix_and_Match_Cart {

	/**
	 * __construct function.
	 *
	 * @return object
	 */
	public function __construct() {

		// Validate mnm add-to-cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 6 );

		// validate mnm cart update.
		add_filter( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 10, 4 );

		// Add mnm configuration data to all mnm items.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

		// Add mnm items to the cart.
		add_action( 'woocommerce_add_to_cart', array( $this, 'add_mnm_items_to_cart' ), 10, 6 );

		// Modify price and shipping details for bundled items.
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item_filter' ), 10, 2 );

		// Preserve data in cart.
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_data_from_session' ), 10, 3 );

		// Sync quantities of packed items with container quantity.
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_quantity_in_cart' ), 10, 2 );
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_quantity_in_cart' ) );

		// Put back cart item data to allow re-ordering of container.
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again' ), 10, 3 );

		// Filter cart widget items.
		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_widget_filter' ), 10, 3 );

		// Filter cart item count.
		add_filter( 'woocommerce_cart_contents_count',  array( $this, 'cart_contents_count' ) );

		// Control modification of packed items' quantity.
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_item_remove_link' ), 10, 2 );

		// change packed item quantity output.
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 2 );

		// hide packed item price.
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );

		// remove/restore children cart items when parent is removed/restored.
		add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ), 10, 2 );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ), 10, 2 );

		// Shipping fix - ensure that non-virtual containers/children, which are shipped, have a valid price that can be used for insurance calculations.
		// Additionally, allow bundled item weights to be added to the container weight.
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'cart_shipping_packages' ), 1, 5 );
	}


	/**
	 * Adds mnm contents to the cart.
	 *
	 * @param  string 	$item_cart_key
	 * @param  int 		$product_id
	 * @param  int 		$quantity
	 * @param  int 		$variation_id
	 * @param  array 	$variation
	 * @param  array 	$cart_item_data
	 */
	function add_mnm_items_to_cart( $item_cart_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		if ( isset( $cart_item_data[ 'mnm_config' ] ) ) {

			$mnm_cart_item_data = array(
				'mnm_container' => $item_cart_key,
			);


			// Now add all items - yay!
			foreach ( $cart_item_data[ 'mnm_config' ] as $item_id => $mnm_item_data ) {

				$mnm_product_id     = $mnm_item_data[ 'product_id' ];
				$mnm_variation_id   = $mnm_item_data[ 'variation_id' ];
				$mnm_variations     = $mnm_item_data[ 'variation' ];

				$item_quantity      = $mnm_item_data[ 'quantity' ];
				$mnm_quantity       = $item_quantity * $quantity;

				/**
				 * Allow filtering child cart item data.
				 *
				 * Example: If the parent cart item data array already contains extension-specific configuration info.
				 *
				 * @param array $mnm_cart_item_data Configuration of each Mix and Match bundled item.
				 * @param array $cart_item_data Container product data.
				 * @param int $item_id The bundled item product or variation ID.
				 * @param int $product_id The Mix and Match container product ID.
				 */
				$mnm_cart_item_data = (array) apply_filters( 'woocommerce_mnm_child_cart_item_data', $mnm_cart_item_data, $cart_item_data, $item_id, $product_id );

				/**
				 * Before child item is added to cart.
				 *
				 * @param int $mnm_product_id The child item product ID.
				 * @param int $mnm_quantity The quantity of the child item in the container.
				 * @param int $mnm_variation_id The child item variation ID.
				 * @param array $mnm_variations Attributes of specific variation being added to cart.
				 * @param array $mnm_cart_item_data Child item product data.
				 */
				do_action( 'woocommerce_mnm_before_mnm_add_to_cart', $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

				// Add to cart.
				$mnm_item_cart_key = $this->mnm_add_to_cart( $product_id, $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

				if ( $mnm_item_cart_key ) {

					if ( ! isset( WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ] ) ) {

						WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ] = array();

					} elseif ( ! in_array( $mnm_item_cart_key, WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ] ) ) {

						WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ][] = $mnm_item_cart_key;
					}
				}

				/**
				 * After child item is added to cart.
				 *
				 * @param int $mnm_product_id The child item product ID.
				 * @param int $mnm_quantity The quantity of the child item in the container.
				 * @param int $mnm_variation_id The child item variation ID.
				 * @param array $mnm_variations Attributes of specific variation being added to cart.
				 * @param array $mnm_cart_item_data Child item product data.
				 */
				do_action( 'woocommerce_mnm_after_mnm_add_to_cart', $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

			}

		}

	}


	/**
	 * Add a mnm child to the cart. Must be done without updating session data, recalculating totals or calling 'woocommerce_add_to_cart' recursively.
	 * For the recursion issue, see: https://core.trac.wordpress.org/ticket/17817.
	 *
	 * @param int          $container_id
	 * @param int          $product_id
	 * @param string       $quantity
	 * @param int          $variation_id
	 * @param array        $variation
	 * @param array        $cart_item_data
	 * @return string|false
	 */
	public function mnm_add_to_cart( $container_id, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data ) {

		/**
		 * Load cart item data for bundled child items.
		 *
		 * @param array $cart_item_data Child item's cart data.
		 * @param int $product_id Child item's product ID.
		 * @param int $variation_id Child item's variation ID.
		 */
		$cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart.
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// Get the product.
		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart().
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;
		
			/**
			 * Add item after merging with $cart_item_data
			 * 
			 * Allow plugins and wc_cp_add_cart_item_filter to modify cart item.
			 *
			 * @param array $cart_item_data Child item's cart data.
			 * @param str $cart_item_key Key in the WooCommerce cart array.
			 */
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'product_id'   => absint( $product_id ),
				'variation_id' => absint( $variation_id ),
				'variation'    => $variation,
				'quantity'     => $quantity,
				'data'         => $product_data
			) ), $cart_item_key );

		}

		/**
		 * Add bundled items to cart.
		 *
		 * Use this hook for compatibility instead of the 'woocommerce_add_to_cart' action hook to work around the recursion issue (solved in WP 4.7).
		 * When the recursion issue is solved, we can simply replace calls to 'mnm_add_to_cart()' with direct calls to 'WC_Cart::add_to_cart()' and delete this function.
		 *
		 * @param str $cart_item_key
		 * @param int $product_id
		 * @param int $quantity
		 * @param int $variation_id
		 * @param array $cart_item_data
		 * @param int $container_id
		 */
		do_action( 'woocommerce_mnm_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $container_id );

		return $cart_item_key;
	}


	/**
	 * Validates that all MnM items chosen can be added-to-cart before actually starting to add items.
	 *
	 * @param  bool 	$add
	 * @param  int 	$product_id
	 * @param  int 	$product_quantity
	 * @param  int  $variation_id
	 * @param array $variation - selected attribues
	 * @param array $cart_item_data - data from session
	 * @return bool
	 */
	public function add_to_cart_validation( $passed_validation, $product_id, $product_quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		// The container product.
		$product = wc_get_product( $product_id );

		// Prevent bundled items from getting added two times when ordering again.
		if ( isset( $cart_item_data[ 'mnm_container' ] ) && false === $cart_item_data[ 'mnm_container' ] ) {
			return false;
		}

		if ( $product->is_type( 'mix-and-match' ) ) {

			// Grab bundled items.
			$mnm_items = $product->get_children();

			if ( empty( $mnm_items ) ) {
				return false;
			}

			// Count the total bundled items.
			$total_items_in_container = 0;

			// If a stock-managed product / variation exists in the bundle multiple times, its stock will be checked only once for the sum of all bundled quantities.
			// The stock manager class keeps a record of stock-managed product / variation ids.
			$mnm_stock = new WC_Mix_and_Match_Stock_Manager( $product );

			// Loop through the items.
			foreach ( $mnm_items as $id => $mnm_item ) {

				// Check that a product has been selected.
				if ( isset( $_REQUEST[ 'mnm_quantity' ][ $id ] ) && $_REQUEST[ 'mnm_quantity' ][ $id ] !== '' ) {
					$item_quantity = $_REQUEST[ 'mnm_quantity' ][ $id ];
				// For ordering again get the quantity from the config array.
				} elseif ( isset( $cart_item_data[ 'mnm_config' ][ $id ][ 'quantity' ] ) ) {
					$item_quantity = $cart_item_data[ 'mnm_config' ][ $id ][ 'quantity' ];
				// If the ID isn't in the posted data something is rotten in Denmark.
				} else {
					continue;
				}

				// Total quantity in single container.
				$total_items_in_container += $item_quantity;

				// Total quantity of items in all containers: for stock purposes.
				$quantity = $item_quantity * $product_quantity;

				// Product is_purchasable - only for per item pricing.
				if ( $product->is_priced_per_product() && ! $mnm_item->is_purchasable() ) {
					wc_add_notice( sprintf( __( 'The configuration you have selected cannot be added to the cart since &quot;%s&quot; cannot be purchased.', 'woocommerce-mix-and-match-products' ), $mnm_item->get_title() ), 'error' );
					return false;
				}

				// Stock management.
				if ( $mnm_item->is_type( 'variation' ) ) {
					$mnm_stock->add_item( $mnm_item->get_parent_id(), $id, $quantity );
				} else {
					$mnm_stock->add_item( $id, false, $quantity );
				}

				/**
				 * Individual item validation.
				 *
				 * @param bool $is_valid
				 * @param obj $product WC_Product of parent container.
				 * @param obj $mnm_item WC_Product of child item.
				 * @param int $item_quantity Quantity of child item.
				 * @param int $product_quantity Quantity of parent container.
				 */
				if ( ! apply_filters( 'woocommerce_mnm_item_add_to_cart_validation', true, $product, $mnm_item, $item_quantity, $product_quantity ) ) {
					return false;
				}

			} // End foreach.

			// The number of items allowed to be in the container.
			$min_container_size = $product->get_min_container_size();
			$max_container_size = $product->get_max_container_size();

			$error_message = false;

			// Validate the number of items in the container matches exactly.
			if ( $max_container_size > 0 && $total_items_in_container > $max_container_size ) {
				$error_message = sprintf( _n( 'You have selected too many items. Please choose %d item for &quot;%s&quot;.', 'You have selected too many items. Please choose %d items for &quot;%s&quot;.', $max_container_size, 'woocommerce-mix-and-match-products' ), $max_container_size, $product->get_title() );
			} elseif ( $min_container_size > 0 && $total_items_in_container < $min_container_size ) {
				$error_message = sprintf( _n( 'You have selected too few items. Please choose %d item for &quot;%s&quot;.', 'You have selected too few items. Please choose %d items for &quot;%s&quot;.', $min_container_size, 'woocommerce-mix-and-match-products' ), $min_container_size, $product->get_title() );
			}

			/**
			 * Change the quantity error message.
			 *
			 * @param str $error_message
			 * @param obj WC_Mix_and_Match_Stock_Manager $cart_item_data
			 * @param obj WC_Product $product
			 */ 
			$error_message = apply_filters( 'woocommerce_mnm_container_quantity_error_message', $error_message, $mnm_stock, $product );

			if( $error_message ){
				wc_add_notice( $error_message, 'error' );
				return false;
			}

			/**
			 * Validate the container.
			 *
			 * Check stock for stock-managed bundled items, allowing extensions to also validate the container.
			 * If out of stock, don't proceed.
			 *
			 * @param bool $is_valid
			 * @param obj WC_Mix_and_Match_Stock_Manager $mnm_stock
			 * @param obj WC_Product  $var Description $product
			 */
			if ( ! apply_filters( 'woocommerce_mnm_add_to_cart_validation', $mnm_stock->validate_stock(), $mnm_stock, $product ) ) {
				return false;
			}
		}

		return $passed_validation;
	}


	/**
	 * Validates that all MnM items can be updated before updating the container.
	 *
	 * @param  bool 	$passed_validation
	 * @param  int 		$cart_item_key
	 * @param  array 	$values
	 * @param  int 		$quantity
	 * @return bool
	 */
	public function update_cart_validation( $passed_validation, $cart_item_key, $values, $product_quantity ) {

		$product = $values[ 'data' ];

		if ( ! $product ) {
			return false;
		}

		$existing_quantity   = $values[ 'quantity' ];
		$additional_quantity = $product_quantity - $existing_quantity;

		// Don't check bundled items individually, will be checked by parent container.
		if ( isset( $values[ 'mnm_container' ] ) && $values[ 'mnm_container' ] != '' ) {
			return $passed_validation;
		}

		if ( $product->is_type( 'mix-and-match' ) && isset( $values[ 'mnm_config' ] ) && is_array( $values[ 'mnm_config' ] ) ) {

			// Grab bundled items.
			$mnm_items = $product->get_children();

			if ( empty( $mnm_items ) ) {
				return false;
			}

			// If a stock-managed product / variation exists in the bundle multiple times, its stock will be checked only once for the sum of all bundled quantities.
			// The stock manager class keeps a record of stock-managed product / variation ids.
			$mnm_stock = new WC_Mix_and_Match_Stock_Manager( $product );

			// Loop through the items.
			foreach ( $values[ 'mnm_config' ] as $id => $data ) {

				// Double check it is an allowed item - is this needed? Wasn't it checked on its way into the cart?
				if ( ! array_key_exists( $id, $mnm_items ) ) {
					return false;
				}

				// Quantity per container.
				$item_quantity = $data[ 'quantity' ];

				// Total quantity.
				$quantity = $item_quantity * $additional_quantity;

				// Get the bundled product/variation.
				$mnm_item = wc_get_product( $id );

				// Must be some kinda fake product.
				if ( ! $mnm_item ) {
					return false;
				}

				// Stock management.
				if ( $mnm_item->is_type( 'variation' ) ) {
					$mnm_stock->add_item( $mnm_item->get_parent_id(), $id, $quantity );
				} else {
					$mnm_stock->add_item( $id, false, $quantity );
				}

			} // End foreach.


			// Check stock for stock-managed bundled items.
			// If out of stock, don't proceed.
			if ( ! $mnm_stock->validate_stock( true ) ) {
				return false;
			}
		}

		return $passed_validation;
	}


	/**
	 * Adds configuration-specific cart-item data.
	 *
	 * @param  array  $cart_item_data
	 * @param  int 	  $product_id
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id ) {

		// Get product type.
		$terms        = get_the_terms( $product_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

		if ( $product_type === 'mix-and-match' && isset( $_REQUEST[ 'mnm_quantity' ] ) && is_array( $_REQUEST[ 'mnm_quantity' ] ) ) {

			// Create a unique array with the mnm configuration.
			$config = array();

			foreach ( $_REQUEST[ 'mnm_quantity' ] as $id => $mnm_quantity ) {

				if ( (int) $mnm_quantity <= 0 ) {
					continue;
				}

				$mnm_item = wc_get_product( $id );

				if ( ! $mnm_item ) {
					continue;
				}

				// Going to need this info for updating cart and ordering again.
				$mnm_product_id   = $mnm_item->get_id();
				$mnm_variation_id = '';
				$mnm_variation    = '';
				$mnm_variation    = array();

				if ( $mnm_item->is_type( 'variation' ) ) {
					$mnm_variation_id = $mnm_product_id;
					$mnm_product_id   = $mnm_item->get_parent_id();;
					$mnm_variation    = $mnm_item->get_variation_attributes();
				}

				$config[ $id ] = array(
					'product_id'   => $mnm_product_id ,
					'variation_id' => $mnm_variation_id,
					'quantity'     => (int) $mnm_quantity,
					'variation'    => $mnm_variation,
				);
			}

			// Add the array to the container item's data.
			$cart_item_data[ 'mnm_config' ] = $config;

			// Add an empty contents array to the item's data.
			$cart_item_data[ 'mnm_contents' ] = array();
		}

		return $cart_item_data;

	}


	/**
	 * Modifies mnm cart item virtual status and price depending on pricing and shipping options.
	 *
	 * @param  array                     $cart_item
	 * @param  WC_Product_Mix_and_Match  $parent
	 * @return array
	 */
	private function set_mnm_cart_item( $cart_item, $parent ) {

		$per_product_pricing  = $parent->is_priced_per_product();
		$per_product_shipping = $parent->is_shipped_per_product();

		// If the container has a static price, set its price to zero.
		if ( false === $per_product_pricing ) {
			$cart_item[ 'data' ]->set_price( 0 );
			$cart_item[ 'data' ]->set_regular_price( 0 );
			$cart_item[ 'data' ]->set_sale_price( '' );
		}

		// If is not shipped individually, mark it as virtual and save weight to be optionally added to the container.
		if ( $cart_item[ 'data' ]->needs_shipping() ) {

			$item_id = $cart_item[ 'variation_id' ] > 0 ? $cart_item[ 'variation_id' ] : $cart_item[ 'product_id' ];


			/**
			 * Is child item shipped individually or as part of container.
			 *
			 * @param bool $per_product_shipping
			 * @param obj WC_Product  $cart_item['data']
			 * @param  int $item_id Product or Variation ID of child item.
			 * @param obj WC_Product_Mix_and_Match $parent Product object of parent container.
			 */
			if ( false === apply_filters( 'woocommerce_mnm_item_shipped_individually', $per_product_shipping, $cart_item[ 'data' ], $item_id, $parent ) ) {

				/**
				 * Does the bundled item have weight?
				 *
				 * @param bool $has_weight
				 * @param obj WC_Product  $cart_item['data']
				 * @param  int $item_id Product or Variation ID of child item.
				 * @param obj WC_Product_Mix_and_Match $parent Product object of parent container.
				 */	
				if ( apply_filters( 'woocommerce_mnm_item_has_bundled_weight', false, $cart_item[ 'data' ], $item_id, $parent ) ) {
					$cart_item[ 'data' ]->bundled_weight = $cart_item[ 'data' ]->get_weight( 'edit' );
				}

				$cart_item[ 'data' ]->bundled_value = $cart_item[ 'data' ]->get_price( 'edit' );

				$cart_item[ 'data' ]->set_virtual( 'yes' );
				$cart_item[ 'data' ]->set_weight( '' );
			}
		}

		/**
		 * Allow the child item to be modified by other plugins.
		 *
		 * @param array $cart_item
		 * @param obj WC_Product_Mix_and_Match $parent Product object of parent container.
		 */
		return apply_filters( 'woocommerce_mnm_cart_item', $cart_item, $parent );
	}


	/**
	 * Modifies MNM cart item data. Container price is equal to the base price in Per-Item Pricing mode.
	 *
	 * @param array $cart_item
	 * @return array $cart_item
	 */
	private function set_mnm_container_cart_item( $cart_item ) {
		$container = $cart_item[ 'data' ];
		/**
		 * Allow MNM container cart item data to be modified
		 *
		 * @param array $cart_item
		 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
		 */	
		return apply_filters( 'woocommerce_mnm_container_cart_item', $cart_item, $container );
	}


	/**
	 * Modifies MNM cart item data.
	 * Important for the first calculation of totals only.
	 *
	 * @param  array 	$cart_item
	 * @param  string 	$cart_item_key
	 * @return array
	 */
	public function add_cart_item_filter( $cart_item, $cart_item_key ) {

		$cart_contents = WC()->cart->get_cart();

		// If item is mnm container.
		if ( isset( $cart_item[ 'mnm_contents' ] ) ) {
			$cart_item = $this->set_mnm_container_cart_item( $cart_item );
		}

		// If part of mnm container.
		if ( ! empty( $cart_item[ 'mnm_container' ] ) ) {

			$container_cart_key = $cart_item[ 'mnm_container' ];

			if ( WC()->cart->find_product_in_cart( $container_cart_key ) ) {

				$parent    = $cart_contents[ $container_cart_key ][ 'data' ];
				$cart_item = $this->set_mnm_cart_item( $cart_item, $parent );

				// Add item key to parent items.
				array_push( WC()->cart->cart_contents[ $container_cart_key ][ 'mnm_contents' ], $cart_item_key );
			}
		}

		return $cart_item;
	}


	/**
	 * Load all MnM-related session data.
	 *
	 * @param  array 	$cart_item
	 * @param  array 	$item_session_values
	 * @param  string 	$key
	 */
	public function get_cart_data_from_session( $cart_item, $session_values, $key ) {

		// Parent container config.
		if ( isset( $session_values[ 'mnm_config' ] ) ) {
			$cart_item[ 'mnm_config' ] = $session_values[ 'mnm_config' ];
		}

		// Cart keys of items in parent container.
		if ( isset( $session_values[ 'mnm_contents' ] ) ) {

			if ( $cart_item[ 'data' ]->is_type( 'mix-and-match' ) ) {

				if ( ! isset( $cart_item[ 'mnm_contents' ] ) ) {
					$cart_item[ 'mnm_contents' ] = $item_session_values[ 'mnm_contents' ];
				}

				$cart_item = $this->set_mnm_container_cart_item( $cart_item );

			} else {

				if ( isset( $cart_item[ 'mnm_contents' ] ) ) {
					unset( $cart_item[ 'mnm_contents' ] );
				}
			}
		}

		// Bundled items.
		if ( isset( $session_values[ 'mnm_container' ] ) ) {

			$container_cart_key = $session_values[ 'mnm_container' ];
			$cart_contents      = WC()->cart->cart_contents;

			if ( WC()->cart->find_product_in_cart( $container_cart_key ) && isset( $cart_contents[ $container_cart_key ][ 'mnm_contents' ] ) ) {

				$cart_item[ 'mnm_container' ] = $container_cart_key;

				$parent        = $cart_contents[ $container_cart_key ][ 'data' ];
				$cart_item     = $this->set_mnm_cart_item( $cart_item, $parent );

			} else {

				if ( isset( $cart_item[ 'mnm_container' ] ) ) {
					unset( $cart_item[ 'mnm_container' ] );
				}
			}
		}

		return $cart_item;
	}


	/**
	 * Keeps MNM item quantities in sync with container item.
	 *
	 * @param  string  $cart_item_key
	 * @param  integer $quantity
	 */
	public function update_quantity_in_cart( $cart_item_key, $quantity = 0 ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$mnm_container = WC()->cart->cart_contents[ $cart_item_key ];

			$mnm_contents = ! empty( $mnm_container[ 'mnm_contents' ] ) ? $mnm_container[ 'mnm_contents' ] : '';

			if ( ! empty( $mnm_contents ) ) {

				$container_quantity = ( $quantity == 0 || $quantity < 0 ) ? 0 : $mnm_container[ 'quantity' ];

				// Change the quantity of all MnM items that belong to the same config.
				foreach ( $mnm_contents as $mnm_child_key ) {

					$mnm_item = WC()->cart->cart_contents[ $mnm_child_key ];

					if ( ! $mnm_item ) {
						continue;
					}

					if ( $mnm_item[ 'data' ]->is_sold_individually() && $quantity > 0 ) {

						WC()->cart->set_quantity( $mnm_child_key, 1 );

					} else {

						// Get quantity per container from parent container config.
						$mnm_id = ! empty( $mnm_item[ 'variation_id' ] ) ? $mnm_item[ 'variation_id' ] : $mnm_item[ 'product_id' ];

						$child_qty_per_container = isset( $mnm_container[ 'mnm_config' ][ $mnm_id ][ 'quantity' ] ) ? $mnm_container[ 'mnm_config' ][ $mnm_id ][ 'quantity' ] : 0;

						WC()->cart->set_quantity( $mnm_child_key, $child_qty_per_container * $container_quantity  );
					}
				}
			}
		}
	}


	/**
	 * Reinitialize cart item data for re-ordering purchased orders.
	 * @param  mixed 		$cart_item_data
	 * @param  mixed 		$order_item
	 * @param  WC_Order 	$order
	 * @return mixed
	 */
	public function order_again( $cart_item_data, $order_item, $order ) {

		// Add data to container.
		if ( isset( $order_item[ 'mnm_config' ] ) && isset( $order_item[ 'mnm_cart_key' ] ) ) {

			$cart_item_data[ 'mnm_config' ]   = maybe_unserialize( $order_item[ 'mnm_config' ] );
			$cart_item_data[ 'mnm_cart_key' ] = $order_item[ 'mnm_cart_key' ];
			$cart_item_data[ 'mnm_contents' ] = array();
		}

		// Will be added by parent.
		if ( isset( $order_item[ 'mnm_container' ] ) ) {

			$cart_item_data[ 'mnm_container' ] = false;

			$order_item_product_id = $order_item[ 'variation_id' ] > 0 ? $order_item[ 'variation_id' ] : $order_item[ 'product_id' ];

			// Copy all cart data of the "orphaned" bundled cart item into the one already added along with the container.
			foreach ( WC()->cart->cart_contents as $check_cart_item_key => $check_cart_item_data ) {

				$check_cart_item_parent = wc_mnm_get_mnm_cart_item_container( $check_cart_item_data );

				if ( ! $check_cart_item_parent ) {
					continue;
				}

				$check_cart_item_product_id = $check_cart_item_data[ 'variation_id' ] > 0 ? $check_cart_item_data[ 'variation_id' ] : $check_cart_item_data[ 'product_id' ];

				if ( isset( $check_cart_item_parent[ 'mnm_cart_key' ] ) && isset( $order_item[ 'mnm_container' ] ) && $check_cart_item_parent[ 'mnm_cart_key' ] === $order_item[ 'mnm_container' ] ) {

					$existing_bundled_cart_item_data = $check_cart_item_data;
					$existing_bundled_cart_item_key  = $check_cart_item_key;

					foreach ( $cart_item_data as $key => $value ) {
						if ( ! isset( $existing_bundled_cart_item_data[ $key ] ) ) {
							WC()->cart->cart_contents[ $existing_bundled_cart_item_key ][ $key ] = $value;
						}
					}
				}
			}
		}

		return $cart_item_data;
	}


	/**
	 * Find the parent of a bundled item in a cart.
	 *
	 * @param  array  $item
	 * @return array
	 */
	public function get_bundled_cart_item_container_key( $item ) {
		return wc_mnm_get_mnm_cart_item_container( $item, false, true );
	}

	/**
	 * Do not show mix and matched items in cart widget.
	 *
	 * @param  bool 	$show
	 * @param  array 	$cart_item
	 * @param  string 	$cart_item_key
	 * @return bool
	 */
	public function cart_widget_filter( $show, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item[ 'mnm_container' ] ) ) {
			$show = false;
		}

		return $show;
	}


	/**
	 * Filters the reported number of cart items.
	 * Counts only MnM containers.
	 *
	 * @param  int 	$count
	 * @return int
	 */
	public function cart_contents_count( $count ) {

		$cart_items = WC()->cart->get_cart();
		$subtract 	= 0;

		foreach ( $cart_items as $key => $value ) {

			if ( isset( $value[ 'mnm_container' ] ) ) {
				$subtract += $value[ 'quantity' ];
			}
		}

		return $count - $subtract;
	}


	/**
	 * MnM items can't be removed individually from the cart.
	 *
	 * @param  string  $link
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_remove_link( $link, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'mnm_container' ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ][ 'mnm_container' ] ) ) {
			$link = '';
		}

		return $link;
	}


	/**
	 * Modifies the cart.php formatted quantity for items in the container.
	 *
	 * @param  string  $quantity
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_quantity( $quantity, $cart_item_key ) {

		$cart = WC()->cart->get_cart();

		if ( isset( $cart[ $cart_item_key ][ 'mnm_container' ] ) && ! empty( $cart[ $cart_item_key ][ 'mnm_container' ] ) ) {

			if ( WC()->cart->find_product_in_cart( $cart[ $cart_item_key ][ 'mnm_container' ] ) ) {
				$quantity = $cart[ $cart_item_key ][ 'quantity' ];
			}
		}

		return $quantity;
	}


	/**
	 * Modifies the cart.php formatted html prices visibility for items in the container.
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_price( $price, $cart_item, $cart_item_key ) {

		// Bundled child items.
		if ( isset( $cart_item[ 'mnm_container' ] ) && ! empty( $cart_item[ 'mnm_container' ] ) ) {

			$container_cart_key = $cart_item[ 'mnm_container' ];

			$cart = WC()->cart->get_cart();

			if ( WC()->cart->find_product_in_cart( $container_cart_key ) && WC()->cart->cart_contents[ $container_cart_key ][ 'data' ]->is_priced_per_product() == false && $cart_item[ 'data' ]->get_price( 'edit' ) == 0 ) {
				$price = '';
			}
		}

		// Parent container.
		if ( isset( $cart_item[ 'mnm_contents' ] ) && ! empty( $cart_item[ 'mnm_contents' ] ) ) {

			if ( $cart_item[ 'data' ]->is_priced_per_product() == true ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item[ 'data' ], array( 'qty'   => $cart_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $cart_item[ 'data' ], array( 'qty'   => $cart_item[ 'quantity' ] ) );

				foreach ( $cart_item[ 'mnm_contents' ] as $mnm_item_key ) {

					$item_values = WC()->cart->cart_contents[ $mnm_item_key ];
					$item_id     = isset( $item_values[ 'variation_id' ] ) ? $item_values[ 'variation_id' ] : $item_values[ 'product_id' ];
					$product     = $item_values[ 'data' ];

					$bundled_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $product, array( 'qty'   => $item_values[ 'quantity' ] ) ) : wc_get_price_including_tax( $product, array( 'qty'   => $item_values[ 'quantity' ] ) );
					$mnm_items_price    += $bundled_item_price;

				}

				$price = $mnm_container_price + $mnm_items_price / $cart_item[ 'quantity' ];
				return wc_price( $price );
			}
		}

		return $price;
	}


	/**
	 * Modifies the cart.php template formatted subtotal appearance.
	 *
	 * @param  string  $subtotal
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item[ 'mnm_container' ] ) && ! empty( $cart_item[ 'mnm_container' ] ) ) {

			$container_cart_key = $cart_item[ 'mnm_container' ];

			if ( WC()->cart->find_product_in_cart( $container_cart_key ) && WC()->cart->cart_contents[ $container_cart_key ][ 'data' ]->is_priced_per_product() == false && $cart_item[ 'data' ]->get_price( 'edit' ) == 0 ) {
				$subtotal = '';
			} else {
				return __( 'Subtotal', 'woocommerce-mix-and-match-products' ) . ': ' . $subtotal;
			}
		}

		if ( isset( $cart_item[ 'mnm_contents' ] ) && ! empty( $cart_item[ 'mnm_contents' ] ) ) {

			if ( $cart_item[ 'data' ]->is_priced_per_product() == true ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item[ 'data' ], array( 'qty' => $cart_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $cart_item[ 'data' ], array( 'qty' => $cart_item[ 'quantity' ] ) );

				foreach ( $cart_item[ 'mnm_contents' ] as $mnm_item_key ) {

					$item_values = WC()->cart->cart_contents[ $mnm_item_key ];
					$item_id     = isset( $item_values[ 'variation_id' ] ) ? $item_values[ 'variation_id' ] : $item_values[ 'product_id' ];
					$product     = $item_values[ 'data' ];

					$bundled_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $product, array( 'qty' => $item_values[ 'quantity' ] ) ) : wc_get_price_including_tax( $product, array( 'qty' => $item_values[ 'quantity' ] ) );
					$mnm_items_price    += (double) $bundled_item_price;

				}

				$subtotal = (double) $mnm_container_price + $mnm_items_price;

				return $this->format_product_subtotal( $cart_item[ 'data' ], $subtotal );
			}
		}

		return $subtotal;
	}

	/**
	 * Outputs a formatted subtotal ( @see cart_item_subtotal() ).
	 * @static
	 * @param  obj     $product   The WC_Product.
	 * @param  string  $subtotal  Formatted subtotal.
	 * @return string             Modified formatted subtotal.
	 */
	public static function format_product_subtotal( $product, $subtotal ) {

		$cart = WC()->cart;

		$taxable = $product->is_taxable();

		// Taxable.
		if ( $taxable ) {

			if ( $cart->tax_display_cart == 'excl' ) {

				$product_subtotal = wc_price( $subtotal );

				if ( $cart->prices_include_tax && $cart->tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}

			} else {

				$product_subtotal = wc_price( $subtotal );

				if ( ! $cart->prices_include_tax && $cart->tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			}

		// Non-taxable.
		} else {

			$product_subtotal = wc_price( $subtotal );
		}

		return $product_subtotal;
	}


	/**
	 * Remove bundled cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  obj WC_Cart $cart
	 */
	function cart_item_removed( $cart_item_key, $cart ) {

		if ( ! empty( $cart->removed_cart_contents[ $cart_item_key ][ 'mnm_contents' ] ) ) {

			$mnm_item_cart_keys = $cart->removed_cart_contents[ $cart_item_key ][ 'mnm_contents' ];

			foreach ( $mnm_item_cart_keys as $mnm_item_cart_key ) {

				if ( ! empty( $cart->cart_contents[ $mnm_item_cart_key ] ) ) {
					$remove = $cart->cart_contents[ $mnm_item_cart_key ];
					$cart->removed_cart_contents[ $mnm_item_cart_key ] = $remove;
					unset( $cart->cart_contents[ $mnm_item_cart_key ] );

					/**
					 * Remove item from cart.
					 *
					 * @param str $mnm_cart_item_key
					 * @param obj WC_Cart $cart
					 */
					do_action( 'woocommerce_cart_item_removed', $mnm_item_cart_key, $cart );
				}
			}
		}
	}


	/**
	 * Restore bundled cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  WC_Cart $cart
	 */
	function cart_item_restored( $cart_item_key, $cart ) {

		if ( ! empty( $cart->cart_contents[ $cart_item_key ][ 'mnm_contents' ] ) ) {

			$mnm_item_cart_keys = $cart->cart_contents[ $cart_item_key ][ 'mnm_contents' ];

			foreach ( $mnm_item_cart_keys as $mnm_item_cart_key ) {

				if ( ! empty( $cart->removed_cart_contents[ $mnm_item_cart_key ] ) ) {
					$remove = $cart->removed_cart_contents[ $mnm_item_cart_key ];
					$cart->cart_contents[ $mnm_item_cart_key ] = $remove;
					unset( $cart->removed_cart_contents[ $mnm_item_cart_key ] );

					/**
					 * Restore item tor cart.
					 *
					 * @param str $mnm_cart_item_key
					 * @param obj WC_Cart $cart
					 */
					do_action( 'woocommerce_cart_item_restored', $mnm_item_cart_key, $cart );
				}
			}
		}
	}


	/**
	 * Shipping fix - add the value of any children that are not shipped individually to the container value and, optionally, add their weight to the container weight, as well.
	 *
	 * @param  array  $packages
	 * @return array
	 */
	public function cart_shipping_packages( $packages ) {

		if ( ! empty( $packages ) ) {

			foreach ( $packages as $package_key => $package ) {

				if ( ! empty( $package[ 'contents' ] ) ) {
					foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

						if ( wc_mnm_is_mnm_container_cart_item( $cart_item_data ) ) {

							$bundle     = unserialize( serialize( $cart_item_data[ 'data' ] ) );
							$bundle_qty = $cart_item_data[ 'quantity' ];

							/*
							 * Container needs shipping: Sum the prices of any children that are not shipped individually into the parent and, optionally, add their weight to the parent weight.
							 */

							if ( $bundle->needs_shipping() ) {

								// Aggregate weights.

								$bundled_weight = 0.0;

								// Aggregate prices.

								$bundled_value = 0.0;

								$bundle_totals = array(
									'line_subtotal'     => $cart_item_data[ 'line_subtotal' ],
									'line_total'        => $cart_item_data[ 'line_total' ],
									'line_subtotal_tax' => $cart_item_data[ 'line_subtotal_tax' ],
									'line_tax'          => $cart_item_data[ 'line_tax' ],
									'line_tax_data'     => $cart_item_data[ 'line_tax_data' ]
								);

								foreach ( wc_mnm_get_mnm_cart_items( $cart_item_data, WC()->cart->cart_contents, true ) as $child_item_key ) {

									$child_cart_item_data = WC()->cart->cart_contents[ $child_item_key ];
									$bundled_product      = $child_cart_item_data[ 'data' ];
									$bundled_product_qty  = $child_cart_item_data[ 'quantity' ];

									// Aggregate price.
									if ( isset( $bundled_product->bundled_value ) && $bundled_product->bundled_value ) {

										$bundled_value += $bundled_product->bundled_value * $bundled_product_qty;

										$bundle_totals[ 'line_subtotal' ]     += $child_cart_item_data[ 'line_subtotal' ];
										$bundle_totals[ 'line_total' ]        += $child_cart_item_data[ 'line_total' ];
										$bundle_totals[ 'line_subtotal_tax' ] += $child_cart_item_data[ 'line_subtotal_tax' ];
										$bundle_totals[ 'line_tax' ]          += $child_cart_item_data[ 'line_tax' ];

										$packages[ $package_key ][ 'contents_cost' ] += $child_cart_item_data[ 'line_total' ];

										$child_item_line_tax_data = $child_cart_item_data[ 'line_tax_data' ];

										$bundle_totals[ 'line_tax_data' ][ 'total' ]    = array_merge( $bundle_totals[ 'line_tax_data' ][ 'total' ], $child_item_line_tax_data[ 'total' ] );
										$bundle_totals[ 'line_tax_data' ][ 'subtotal' ] = array_merge( $bundle_totals[ 'line_tax_data' ][ 'subtotal' ], $child_item_line_tax_data[ 'subtotal' ] );
									}

									// Aggregate weight.
									if ( isset( $bundled_product->bundled_weight ) && $bundled_product->bundled_weight ) {
										$bundled_weight += $bundled_product->bundled_weight * $bundled_product_qty;
									}
								}

								if ( $bundled_value > 0 ) {
									$bundle_price = $bundle->get_price( 'edit' );
									$bundle->set_price( (double) $bundle_price + $bundled_value / $bundle_qty );
								}

								if ( $bundled_weight > 0 ) {
									$bundle_weight = $bundle->get_weight( 'edit' );
									$bundle->set_weight( (double) $bundle_weight + $bundled_weight / $bundle_qty );
								}

								$packages[ $package_key ][ 'contents' ][ $cart_item_key ]           = array_merge( $cart_item_data, $bundle_totals );
								$packages[ $package_key ][ 'contents' ][ $cart_item_key ][ 'data' ] = $bundle;
							}
						}
					}
				}
			}
		}

		return $packages;
	}

} //End class.
