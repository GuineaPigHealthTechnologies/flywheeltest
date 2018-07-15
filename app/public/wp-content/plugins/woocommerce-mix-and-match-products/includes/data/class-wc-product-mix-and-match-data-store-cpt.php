<?php
/**
 * Mix and Match Product Data Store
 *
 * @author   SomewhereWarm
 * @category Class
 * @package  WooCommerce Mix and Match Products/Data
 * @since    1.2.0
 * @version  1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_MNM_Data_Store_CPT Class.
 *
 * MnM data stored as Custom Post Type. For use with the WC 3.0+ CRUD API.
 *
 * @uses  WC_Product_Data_Store_CPT
 */
class WC_Product_MNM_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Data stored in meta keys, but not considered "meta" for the MnM type.
	 * 
	 * @var array
	 */
	protected $extended_internal_meta_keys = array(
		'_mnm_base_price',
		'_mnm_base_regular_price',
		'_mnm_base_sale_price',
		'_mnm_layout_style',
		'_mnm_add_to_cart_form_location',
		'_mnm_min_container_size',
		'_mnm_max_container_size',
		'_mnm_data',
		'_mnm_per_product_pricing',
		'_mnm_per_product_shipping'
	);

	/**
	 * Maps extended properties to meta keys.
	 * 
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'min_raw_price'         => '_price',
		'min_raw_regular_price' => '_regular_price',
		'price'                 => '_mnm_base_price',
		'regular_price'         => '_mnm_base_regular_price',
		'sale_price'            => '_mnm_base_sale_price',
		'layout'                     => '_mnm_layout_style',
		'add_to_cart_form_location'  => '_mnm_add_to_cart_form_location',
		'min_container_size'    => '_mnm_min_container_size',
		'max_container_size'    => '_mnm_max_container_size',
		'contents'              => '_mnm_data',
		'priced_per_product'    => '_mnm_per_product_pricing',
		'shipped_per_product'   => '_mnm_per_product_shipping'
	);

	/**
	 * Callback to exclude MnM-specific meta data.
	 *
	 * @param  object  $meta
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return parent::exclude_internal_meta_keys( $meta ) && ! in_array( $meta->meta_key, $this->extended_internal_meta_keys );
	}

	/**
	 * Reads all MnM-specific post meta.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	protected function read_extra_data( &$product ) {

		foreach ( $this->props_to_meta_keys as $property => $meta_key ) {

			// Get meta value.
			$function = 'set_' . $property;
			if ( is_callable( array( $product, $function ) ) ) {
				$product->{$function}( get_post_meta( $product->get_id(), $meta_key, true ) );
			}

		}

		// Base prices are overridden by NYP min price.
		if ( $product->is_nyp() ) {
			$min_price = $product->get_meta( '_min_price', true, 'edit' );
			$product->set_price( $min_price );
			$product->set_regular_price( $min_price );
			$product->set_sale_price( '' );
		}

	}

	/**
	 * Writes all MnM-specific post meta.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 * @param  bool                   $force
	 */
	protected function update_post_meta( &$product, $force = false ) {

		$this->extra_data_saved = true;

		parent::update_post_meta( $product, $force );

		$id                 = $product->get_id();
		$meta_keys_to_props = array_flip( array_diff_key( $this->props_to_meta_keys, array( 'price' => 1, 'min_raw_price' => 1, 'min_raw_regular_price' => 1 ) ) );
		$props_to_update    = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;

			// Get meta value.
			$meta_value = $product->$property_get_fn( 'edit' );

			// Sanitize bool for storage.
			if ( is_bool( $meta_value ) ) {
				$meta_value = wc_bool_to_string( $meta_value );
			}

			if ( update_post_meta( $id, $meta_key, $meta_value ) && ! in_array( $property, $this->updated_props ) ) {
				$this->updated_props[] = $meta_key;
			}
		}
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	protected function handle_updated_props( &$product ) {

		$id = $product->get_id();

		if ( in_array( 'date_on_sale_from', $this->updated_props ) || in_array( 'date_on_sale_to', $this->updated_props ) || in_array( 'regular_price', $this->updated_props ) || in_array( 'sale_price', $this->updated_props ) ) {
			if ( $product->is_on_sale( 'update-price' ) ) {
				update_post_meta( $id, '_mnm_base_price', $product->get_sale_price( 'edit' ) );
				$product->set_price( $product->get_sale_price( 'edit' ) );
			} else {
				update_post_meta( $id, '_mnm_base_price', $product->get_regular_price( 'edit' ) );
				$product->set_price( $product->get_regular_price( 'edit' ) );
			}
		}

		if ( in_array( 'stock_quantity', $this->updated_props ) ) {
			/**
			 * woocommerce_product_set_stock hook.
			 *
			 * @param  obj $product WC_Product
			 */
			do_action( 'woocommerce_product_set_stock', $product );
		}

		if ( in_array( 'stock_status', $this->updated_props ) ) {
			/**
			 * woocommerce_product_set_stock_status hook.
			 *
			 * @param  int $product_id
			 * @param  str $stock_status
			 * @param  obj $product WC_Product
			 */
			do_action( 'woocommerce_product_set_stock_status', $product->get_id(), $product->get_stock_status(), $product );
		}

		// 
			/**
			 * woocommerce_product_object_updated_props hook.
			 *
			 * Trigger action so 3rd parties can deal with updated props.
			 *
			 * @param  obj $product WC_Product
			 * @param  array $updated_props
			 */
			do_action( 'woocommerce_product_object_updated_props', $product, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Writes MnM raw price meta to the DB.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	public function update_raw_prices( &$product ) {

		$id = $product->get_id();

		update_post_meta( $id, '_price', $product->get_min_raw_price( 'edit' ) );
		update_post_meta( $id, '_regular_price', $product->get_min_raw_regular_price( 'edit' ) );

		if ( $product->is_on_sale( 'edit' ) ) {
			update_post_meta( $id, '_sale_price', $product->get_min_raw_price( 'edit' ) );
		} else {
			update_post_meta( $id, '_sale_price', '' );
		}
	}
}
