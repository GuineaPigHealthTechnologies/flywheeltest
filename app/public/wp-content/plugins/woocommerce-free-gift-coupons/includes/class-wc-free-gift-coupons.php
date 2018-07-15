<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Free_Gift_Coupons' ) ) :

/**
 * Main WC_Free_Gift_Coupons Class
 *
 * @class WC_Free_Gift_Coupons
 * @category Class
 * @author   Kathy Darling
 * @version	2.0.0
 */
class WC_Free_Gift_Coupons extends WC_Free_Gift_Coupons_Legacy {

	/**
	 * @var string
	 */
	public static $version = '2.1.0-beta';
	public static $required_woo = '3.0.0';

	/**
	 * Free Gift Coupons pseudo constructor
	 * @access public
	 * @return WC_Free_Gift_Coupons
	 * @since 1.0
	 */
	public static function init() {

		self::includes();

		// Make translation-ready.
		add_action( 'init', array( __CLASS__, 'load_textdomain_files' ) );

		// Check we're running the required version of WC.
		if ( ! self::wc_is_version( self::$required_woo ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
			return false;
		}

		// Add the free_gift coupon type.
		add_filter( 'woocommerce_coupon_discount_types', array( __CLASS__, 'discount_types' ) );

		// Add the gift item when coupon is applied.
		add_action( 'woocommerce_applied_coupon', array( __CLASS__, 'apply_coupon' ) );

		// Add compatibility for Subscriptions.
		add_filter( 'woocommerce_subscriptions_validate_coupon_type', array( __CLASS__, 'ignore_free_gift' ), 10, 2 );

		// Change the price to ZERO/Free on gift item.
		add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'add_cart_item' ), 15 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ), 15, 2 );

		// Disable multiple quantities of free item.
		add_filter( 'woocommerce_cart_item_quantity', array( __CLASS__, 'cart_item_quantity' ), 5, 3 );

		// Remove Bonus item when coupon code is removed.
		add_action( 'woocommerce_removed_coupon', array( __CLASS__, 'remove_free_gift_from_cart' ) );

		// Remove Bonus item if coupon code conditions are no longer valid.
		add_action( 'woocommerce_check_cart_items', array( __CLASS__, 'check_cart_items' ) );

		// Free Gifts should not count one way or the other towards product validations.
		add_action( 'woocommerce_coupon_get_items_to_validate', array( __CLASS__, 'exclude_free_gifts_from_coupon_validation' ), 10, 2 );

		// Display as Free! in cart and in orders.
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_subtotal', array( __CLASS__, 'cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( __CLASS__, 'cart_item_price' ), 10, 2 );

		// Remove free gifts from shipping calcs & enable free shipping if required.
		add_filter( 'woocommerce_cart_shipping_packages', array( __CLASS__, 'remove_free_shipping_items' ) );
		add_filter( 'woocommerce_shipping_free_shipping_is_available', array( __CLASS__, 'enable_free_shipping'), 20, 2 );
		add_filter( 'woocommerce_shipping_legacy_free_shipping_is_available', array( __CLASS__, 'enable_free_shipping'), 20, 2 );

		// Add order item meta.
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'add_order_item_meta' ), 10, 3 );

	}
	

	/**
	 * Includes.
	 * since 2.0.0
	 */
	public static function includes() {

		// Install.
		require_once( 'updates/class-wc-free-gift-coupons-install.php' );

		// Compatibility.
		require_once( 'compatibility/class-wc-fgc-compatibility.php' );

		// Admin includes.
		if ( is_admin() ) {
			self::admin_includes();
		}

	}


	/**
	 * Admin & AJAX functions and hooks.
	 */
	public static function admin_includes() {

		// Admin notices handling.
		require_once( 'admin/class-wc-free-gift-coupons-admin-notices.php' );

		// Admin functions and hooks.
		require_once( 'admin/class-wc-free-gift-coupons-admin.php' );
	}

	/**
	 * Load localisation files
	 *
	 * Preferred language file location is: /wp-content/languages/plugins/wc_free_gift_coupons-$locale.mo
	 * @access public
	 * @return void
	 * @since 1.0
	 */
	public static function load_textdomain_files() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wc_free_gift_coupons' );

		load_textdomain( 'wc_free_gift_coupons', WP_LANG_DIR . '/wc_free_gift_coupons/wc_free_gift_coupons-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_free_gift_coupons', false, 'woocommerce-free-gift-coupons/languages' );
	}


	/**
	 * Displays a warning message if version check fails.
	 * @return string
	 */
	public static function admin_notice() {
	    echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Free Gift Coupons requires at least WooCommerce %s in order to function. Please upgrade WooCommerce.', 'wc_free_gift_coupons' ), self::$required_woo ) . '</p></div>';
	}


	/**
	 * Add a new coupon type
	 *
	 * @access public
	 * @param array $types - available coupon types
	 * @return array
	 * @since 1.0
	 */
	public static function discount_types( $types ){
		$types['free_gift'] = __( 'Free Gift', 'wc_free_gift_coupons' );
		return $types;
	}


	/**
	 * Add the gift item to the cart when coupon is applied
	 * @access public
	 * @param string $coupon_code
	 * @return void
	 * @since 1.0
	 */
	public static function apply_coupon( $coupon_code ){

		// Get the Gift IDs.
		$gift_data = self::get_gift_data( $coupon_code );

		if ( ! empty ( $gift_data ) ) {
			
			foreach ( $gift_data as $gift_id => $data ){
				WC()->cart->add_to_cart( $data['product_id'], $data['quantity'], $data['variation_id'], array(), array( 'free_gift' => $coupon_code ) );			
			}

			do_action( 'woocommerce_free_gift_coupon_applied', $coupon_code );

		}

	}


	/**
	 * Prevent Subscriptions validating free gift coupons
	 * @access public
	 * @param bool $validate
	 * @param obj $coupon
	 * @return bool
	 * @since 1.0.7
	 */
	public static function ignore_free_gift( $validate, $coupon ) {

	    if ( $coupon->is_type( 'free_gift' ) ) {
	        $validate = false;
	    }

	    return $validate;
	}


	/**
	 * Change the price on the gift item to be zero
	 * @access public
	 * @param array $cart_item
	 * @return array
	 * @since 1.0
	 */
	public static function add_cart_item( $cart_item ) {

		// Adjust price in cart if bonus item.
		if ( ! empty ( $cart_item['free_gift'] ) ){
			$cart_item['data']->set_price( 0 );
			$cart_item['data']->set_regular_price( 0 );
			$cart_item['data']->set_sale_price( 0 );
		}
			
		return $cart_item;
	}

	/**
	 * Adjust session values on the gift item
	 * @access public
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 * @since 1.0
	 */
	public static function get_cart_item_from_session( $cart_item, $values ) {

		if ( ! empty( $values['free_gift'] ) ) {
			$cart_item['free_gift'] = $values['free_gift'];
			$cart_item = self::add_cart_item( $cart_item );
		}

		return $cart_item;

	}

	/**
	 * Disable quantity inputs in cart
	 * @access public
	 * @param string $product_quantity
	 * @param string $cart_item_key
	 * @param array $cart_item
	 * @return string
	 * @since 1.0
	 */
	public static function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ){

		if ( ! empty ( $cart_item['free_gift'] ) ) {
			$product_quantity = sprintf( '%1$s <input type="hidden" name="cart[%2$s][qty]" value="%1$s" />', $cart_item['quantity'], $cart_item_key );
		}

		return $product_quantity;
	}


	/**
	 * Removes gift item from cart when coupon is removed
	 * @access public
	 * @param string $coupon
	 * @return void
	 * @since 1.2.0
	 */
	public static function remove_free_gift_from_cart( $coupon ) {

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			if( isset( $values['free_gift'] ) && $values['free_gift'] == $coupon ){

				WC()->cart->set_quantity( $cart_item_key, 0 );

			}
		}

	}


	/**
	 * Removes gift item from cart if coupon is invalidated
	 * @access public
	 * @return void
	 * @since 1.0
	 */
	public static function check_cart_items() {

		$cart_coupons = (array) WC()->cart->applied_coupons;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			if( isset( $values['free_gift'] ) && ! in_array( $values['free_gift'], $cart_coupons ) ){

				WC()->cart->set_quantity( $cart_item_key, 0 );

				wc_add_notice( __( 'A gift item which is no longer available was removed from your cart.', 'wc_free_gift_coupons' ), 'error' );

			}
		}

	}

	/**
	 * Removes gift item from cart if coupon is invalidated
	 * 
	 * @param  object[] $items | $item properties:  key, object (cart item or order item), product, quantity, price
	 * @return object[]
	 * @since 2.0.0
	 */
	public static function exclude_free_gifts_from_coupon_validation( $items, $discount ) {
		return array_filter( $items, array( __CLASS__, 'exclude_free_gifts' ) );
	}


	/**
	 * Array_filter callback
	 * 
	 * @param  object $item | properties:  key, object (cart item or order item), product, quantity, price
	 * @return object[]
	 * @since 2.0.0
	 */
	public static function exclude_free_gifts( $item ) {
		return ! ( is_array( $item->object ) && isset( $item->object[ 'free_gift' ] ) ) || ( is_a( $item->object, 'WC_Order_Item' ) && $item->object->get_meta( '_free_gift' ) );
	}


	/**
	 * Instead of $0, show Free! in the cart/order summary
	 * @access public
	 * @param string $price
	 * @param mixed array|WC_Order_Item $cart_item
	 * @return string
	 * @since 1.0
	 */
	public static function cart_item_price( $price, $cart_item ){

		// WC 2.7 passes a $cart_item object to order item subtotal.
		if( ( is_array( $cart_item ) && isset( $cart_item['free_gift' ] ) ) || ( is_object( $cart_item ) && $cart_item->get_meta( '_free_gift' ) ) ) {
			$price = __( 'Free!', 'wc_free_gift_coupons' );
		}

		return $price;
	}


	/**
	 * Unset the free items from the packages needing shipping calculations
	 * @access public
	 * @param array $packages
	 * @return array
	 * @since 1.0.7
	 */
	public static function remove_free_shipping_items( $packages ) {

		if( $packages ) foreach( $packages as $i => $package ){ 

			$free_shipping_count = 0;
			$remove_items = array();
			$total_count = count( $package['contents'] );

			foreach ( $package['contents'] as $key => $item ) {
				
				// If the item is a free gift item get free shipping status.
				if( isset( $item['free_gift'] ) ){

					if ( self::has_free_shipping( $item['free_gift'] ) ) {
						$remove_items[$key] = $item;
						$free_shipping_count++;
					} 

				} 

				// If the free gift with free shipping is the only item then switch 
				// shipping to free shipping. otherwise delete free gift from package calcs.
				if ( $total_count == $free_shipping_count ){
					$packages[$i]['ship_via'] = array( 'free_shipping' );					
				} else {
					$remaining_packages = array_diff_key( $packages[$i]['contents'], $remove_items );
					$packages[$i]['contents'] = $remaining_packages;
				}

			}

		}

		return $packages;
	}


	/**
	 * If the free gift w/ free shipping is the only item in the cart, enable free shipping
	 * @access public
	 * @param array $packages
	 * @return array
	 * @since 1.0.7
	 */
	public static function enable_free_shipping( $is_available, $package ) { 

		if( count( $package['contents'] ) == 1 && self::check_for_free_gift_with_free_shipping( $package ) ){
			$is_available = true;
		}
	 
		return $is_available;
	}


	/**
	 * Check shipping package for a free gift with free shipping
	 * @access public
	 * @param array $package
	 * @return boolean
	 * @since 1.1.0
	 */
	public static function check_for_free_gift_with_free_shipping( $package ) { 

		$has_free_gift_with_free_shipping = false;

		// Loop through the items looking for one in the eligible array.
		foreach ( $package['contents'] as $item ) {

			// if the item is a free gift item get free shipping status
			if( isset( $item['free_gift'] ) ){ 

				if( self::has_free_shipping( $item['free_gift'] ) ) {
					$has_free_gift_with_free_shipping = true;
					break;
				} 

			} 

		}
	 
		return $has_free_gift_with_free_shipping;
	}

	/**
	 * When a new order is inserted, add item meta noting this item was a free gift
	 * @access public
	 * @param WC_Order_Item $item
	 * @param str $cart_item_key
	 * @param array $values
	 * @return void
	 * @since 1.1.1
	 */
	public static function add_order_item_meta( $item, $cart_item_key, $values ) {
		if ( isset( $values['free_gift'] ) ){ 	
			$item->add_meta_data( '_free_gift', $values['free_gift'], true );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Check the installed version of WooCommerce is greater than $version argument
	 *
	 * @param   $version
	 * @return	boolean
	 * @access 	public
	 * @since   1.1.0
	 */
	public static function wc_is_version( $version = '2.6' ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, $version ) >= 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Free Gift Data from a coupon's ID.
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @param   bool $add_titles add product titles
	 * @return	array
	 * @access 	public
	 * @since   2.0.0
	 */
	public static function get_gift_data( $code, $add_titles = false ) {

		$gift_data = array();

		// Sanitize coupon code.
		$code = wc_format_coupon_code( $code );

		// Get the coupon object.
		$coupon = new WC_Coupon( $code );
		
		if ( ! is_wp_error( $coupon ) && $coupon->is_type( 'free_gift' ) ) {
			$gift_data = (array) $coupon->get_meta( '_wc_free_gift_coupon_data' );
		}

		// Get the title of each product.
		if( $add_titles && ! empty( $gift_data ) ) {
			foreach( $gift_data as $gift_id => $gift ){

				$gift_product = wc_get_product( $gift_id );

				if( is_a( $gift_product, 'WC_Product' ) ) {
					$gift_data[$gift_id]['title'] = $gift_product->get_formatted_name();
				}
			}
		}

		return $gift_data;

	}

	/**
	 * Is free shipping enabled for free gift?
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @return	bool
	 * @access 	public
	 * @since   1.1.1
	 */
	public static function has_free_shipping( $code ) {

		$has_free_shipping = false;

		// Sanitize coupon code.
		$code = wc_format_coupon_code( $code );

		// Get the coupon object.
		$coupon = new WC_Coupon( $code );

		$gift_ids = array();

		if ( ! is_wp_error( $coupon ) && $coupon->is_type( 'free_gift' ) ) {
			$has_free_shipping = wc_string_to_bool( $coupon->get_meta( '_wc_free_gift_coupon_free_shipping' ) );
		}

		return $has_free_shipping;

	}

} // End class.

endif; // Class exists check.