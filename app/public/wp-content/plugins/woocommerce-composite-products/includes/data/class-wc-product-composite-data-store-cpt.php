<?php
/**
 * WC_Product_Composite_Data_Store_CPT class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Composite Products
 * @since    3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Composite Product Data Store class
 *
 * Composite data stored as Custom Post Type. For use with the WC 3.0+ CRUD API.
 *
 * @class    WC_Product_Composite_Data_Store_CPT
 * @version  3.13.0
 */
class WC_Product_Composite_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Data stored in meta keys, but not considered "meta" for the Composite type.
	 * @var array
	 */
	protected $extended_internal_meta_keys = array(
		'_bto_data',
		'_bto_scenario_data',
		'_bto_base_price',
		'_bto_base_regular_price',
		'_bto_base_sale_price',
		'_bto_shop_price_calc',
		'_bto_style',
		'_bto_add_to_cart_form_location',
		'_bto_edit_in_cart',
		'_bto_sold_individually',
		'_wc_sw_max_price'
	);

	/**
	 * Maps extended properties to meta keys.
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'price'                     => '_bto_base_price',
		'regular_price'             => '_bto_base_regular_price',
		'sale_price'                => '_bto_base_sale_price',
		'layout'                    => '_bto_style',
		'add_to_cart_form_location' => '_bto_add_to_cart_form_location',
		'shop_price_calc'           => '_bto_shop_price_calc',
		'editable_in_cart'          => '_bto_edit_in_cart',
		'sold_individually_context' => '_bto_sold_individually',
		'min_raw_price'             => '_price',
		'max_raw_price'             => '_wc_sw_max_price'
	);

	/**
	 * Callback to exclude composite-specific meta data.
	 *
	 * @param  object  $meta
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return parent::exclude_internal_meta_keys( $meta ) && ! in_array( $meta->meta_key, $this->extended_internal_meta_keys );
	}

	/**
	 * Reads all composite-specific post meta.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	protected function read_product_data( &$product ) {

		parent::read_product_data( $product );

		$id           = $product->get_id();
		$props_to_set = array();

		foreach ( $this->props_to_meta_keys as $property => $meta_key ) {

			// Get meta value.
			$meta_value = get_post_meta( $id, $meta_key, true );

			// Back compat.
			if ( 'shop_price_calc' === $property && '' === $meta_value ) {
				if ( 'yes' === get_post_meta( $id, '_bto_hide_shop_price', true ) ) {
					$meta_value = 'hidden';
				} elseif ( '' !== $props_to_set[ 'layout' ] ) {
					$meta_value = 'min_max';
				}
			}

			// Add to props array.
			$props_to_set[ $property ] = $meta_value;
		}

		// Base prices are overridden by NYP min price.
		if ( $product->is_nyp() ) {
			$props_to_set[ 'price' ]      = $props_to_set[ 'regular_price' ] = get_post_meta( $id, '_min_price', true );
			$props_to_set[ 'sale_price' ] = '';
		}

		$product->set_props( $props_to_set );

		// Load component/scenario meta.
		$composite_meta = get_post_meta( $id, '_bto_data', true );
		$scenario_meta  = get_post_meta( $id, '_bto_scenario_data', true );

		$product->set_composite_data( $composite_meta );
		$product->set_scenario_data( $scenario_meta );
	}

	/**
	 * Writes all composite-specific post meta.
	 *
	 * @param  WC_Product_Composite  $product
	 * @param  boolean               $force
	 */
	protected function update_post_meta( &$product, $force = false ) {

		parent::update_post_meta( $product, $force );

		$id                 = $product->get_id();
		$meta_keys_to_props = array_flip( array_diff_key( $this->props_to_meta_keys, array( 'price' => 1, 'min_raw_price' => 1, 'max_raw_price' => 1 ) ) );
		$props_to_update    = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;

			// Get meta value.
			$meta_value = $product->$property_get_fn( 'edit' );

			// Sanitize it for storage.
			if ( 'editable_in_cart' === $property ) {
				$meta_value = wc_bool_to_string( $meta_value );
			}

			$updated = update_post_meta( $id, $meta_key, $meta_value );

			if ( $updated && ! in_array( $property, $this->updated_props ) ) {
				$this->updated_props[] = $property;
			}
		}

		// Save components/scenarios.
		update_post_meta( $id, '_bto_data', $product->get_composite_data( 'edit' ) );
		update_post_meta( $id, '_bto_scenario_data', $product->get_scenario_data( 'edit' ) );
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	protected function handle_updated_props( &$product ) {

		$id = $product->get_id();

		if ( in_array( 'date_on_sale_from', $this->updated_props ) || in_array( 'date_on_sale_to', $this->updated_props ) || in_array( 'regular_price', $this->updated_props ) || in_array( 'sale_price', $this->updated_props ) ) {
			if ( $product->is_on_sale( 'update-price' ) ) {
				update_post_meta( $id, '_bto_base_price', $product->get_sale_price( 'edit' ) );
				$product->set_price( $product->get_sale_price( 'edit' ) );
			} else {
				update_post_meta( $id, '_bto_base_price', $product->get_regular_price( 'edit' ) );
				$product->set_price( $product->get_regular_price( 'edit' ) );
			}
		}

		if ( in_array( 'stock_quantity', $this->updated_props ) ) {
			do_action( 'woocommerce_product_set_stock', $product );
		}

		if ( in_array( 'stock_status', $this->updated_props ) ) {
			do_action( 'woocommerce_product_set_stock_status', $product->get_id(), $product->get_stock_status(), $product );
		}

		// Trigger action so 3rd parties can deal with updated props.
		do_action( 'woocommerce_product_object_updated_props', $product, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Writes composite raw price meta to the DB.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	public function save_raw_prices( &$product ) {

		if ( defined( 'WC_CP_UPDATING' ) ) {
			return;
		}

		/**
		 * 'woocommerce_composite_update_price_meta' filter.
		 *
		 * Use this to prevent composite min/max raw price meta from being updated.
		 *
		 * @param  boolean               $update
		 * @param  WC_Product_Composite  $this
		 */
		$update_raw_price_meta = apply_filters( 'woocommerce_composite_update_price_meta', true, $product );

		if ( ! $update_raw_price_meta ) {
			return;
		}

		$id = $product->get_id();

		$updated_props   = array();
		$props_to_update = array_intersect( array_flip( $this->props_to_meta_keys ), array( 'min_raw_price', 'max_raw_price' ) );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;
			$meta_value      = $product->$property_get_fn( 'edit' );

			if ( update_post_meta( $id, $meta_key, $meta_value ) ) {
				$updated_props[] = $property;
			}
		}

		if ( ! empty( $updated_props ) ) {

			$sale_price_changed = false;

			if ( $product->is_on_sale( 'edit' ) ) {
				$sale_price_changed = update_post_meta( $id, '_sale_price', $product->get_min_raw_price( 'edit' ) );
			} else {
				$sale_price_changed = update_post_meta( $id, '_sale_price', '' );
			}

			if ( $sale_price_changed ) {
				delete_transient( 'wc_products_onsale' );
			}

			do_action( 'woocommerce_product_object_updated_props', $product, $updated_props );
		}
	}

	/**
	 * Calculates and returns:
	 *
	 * - The permutations that correspond to the minimum & maximum configuration price.
	 * - The minimum & maximum raw price.
	 *
	 * @param  WC_Product_Composite  $product
	 * @return array
	 */
	public function read_price_data( &$product ) {
		return WC_CP_Products::read_price_data( $product );
	}

	/**
	 * Get raw product prices straight from the DB.
	 *
	 * @param  array $ids
	 * @return array
	 */
	public function get_raw_component_option_prices( $ids ) {
		return WC_CP_Products::get_raw_component_option_prices( $ids );
	}

	/**
	 * Use 'WP_Query' to preload product data from the 'posts' table.
	 * Useful when we know we are going to call 'wc_get_product' against a list of IDs.
	 *
	 * @since  3.13.2
	 *
	 * @param  array  $ids
	 * @return void
	 */
	public function preload_component_options_data( $ids ) {

		if ( empty( $ids ) ) {
			return;
		}

		$cache_key = 'wc_component_options_db_data_' . md5( json_encode( $ids ) );
		$data      = WC_CP_Helpers::cache_get( $cache_key );

		if ( null === $data ) {

			$data = new WP_Query( array(
				'post_type' => 'product',
				'nopaging'  => true,
				'post__in'  => $ids
			) );

			WC_CP_Helpers::cache_set( $cache_key, $data );
		}
	}
}
