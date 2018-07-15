<?php
/**
 * WC_CP_Products class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Composite Products
 * @since    3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API functions to support product modifications when contained in Composites.
 *
 * @class    WC_CP_Products
 * @version  3.13.10
 */
class WC_CP_Products {

	/**
	 * Composited product being filtered - @see 'add_filters'.
	 * @var WC_CP_Product|false
	 */
	public static $filtered_component_option = false;

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Reset CP query cache + price sync cache when clearing product transients.
		add_action( 'woocommerce_delete_product_transients', array( __CLASS__, 'flush_cp_cache' ) );

		// Reset CP query cache + price sync cache during post status transitions.
		add_action( 'delete_post', array( __CLASS__, 'post_status_transition' ) );
		add_action( 'wp_trash_post', array( __CLASS__, 'post_status_transition' ) );
		add_action( 'untrashed_post', array( __CLASS__, 'post_status_transition' ) );

		// Delete meta reserved to the composite/bundle types.
		add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'delete_reserved_price_meta' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| API Methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add filters to modify products when contained in Composites.
	 *
	 * @param  WC_CP_Product  $product
	 * @return void
	 */
	public static function add_filters( $component_option ) {

		self::$filtered_component_option = $component_option;

		add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_show_product_get_price' ), 16, 2 );
		add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_show_product_get_sale_price' ), 16, 2 );
		add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_show_product_get_regular_price' ), 16, 2 );
		add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_show_product_get_price' ), 16, 2 );
		add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_show_product_get_sale_price' ), 16, 2 );
		add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_show_product_get_regular_price' ), 16, 2 );

		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'filter_show_product_get_price_html' ), 5, 2 );

		add_filter( 'woocommerce_variation_prices', array( __CLASS__, 'filter_get_variation_prices' ), 16, 2 );
		add_filter( 'woocommerce_available_variation', array( __CLASS__, 'filter_available_variation' ), 10, 3 );
		add_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'filter_show_variation_price' ), 10, 3 );

		add_filter( 'woocommerce_bundles_update_price_meta', array( __CLASS__, 'filter_show_product_bundles_update_price_meta' ), 10, 2 );
		add_filter( 'woocommerce_bundle_contains_priced_items', array( __CLASS__, 'filter_bundle_contains_priced_items' ), 10, 2 );
		add_filter( 'woocommerce_bundled_item_is_priced_individually', array( __CLASS__, 'filter_bundled_item_is_priced_individually' ), 10, 2 );
		add_filter( 'woocommerce_bundled_item_raw_price_cart', array( __CLASS__, 'filter_bundled_item_raw_price_cart' ), 10, 4 );

		add_filter( 'woocommerce_nyp_html', array( __CLASS__, 'filter_show_product_get_nyp_price_html' ), 15, 2 );

		/**
		 * Action 'woocommerce_composite_products_apply_product_filters'.
		 *
		 * @param  WC_Product            $product
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $composite
		 */
		do_action( 'woocommerce_composite_products_apply_product_filters', $component_option->get_product(), $component_option->get_component_id(), $component_option->get_composite() );
	}

	/**
	 * Remove filters - @see 'add_filters'.
	 *
	 * @return void
	 */
	public static function remove_filters() {

		/**
		 * Action 'woocommerce_composite_products_remove_product_filters'.
		 */
		do_action( 'woocommerce_composite_products_remove_product_filters' );

		self::$filtered_component_option = false;

		remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_show_product_get_price' ), 16, 2 );
		remove_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_show_product_get_sale_price' ), 16, 2 );
		remove_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_show_product_get_regular_price' ), 16, 2 );
		remove_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_show_product_get_price' ), 16, 2 );
		remove_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_show_product_get_sale_price' ), 16, 2 );
		remove_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_show_product_get_regular_price' ), 16, 2 );

		remove_filter( 'woocommerce_get_price_html', array( __CLASS__, 'filter_show_product_get_price_html' ), 5, 2 );

		remove_filter( 'woocommerce_variation_prices', array( __CLASS__, 'filter_get_variation_prices' ), 16, 2 );
		remove_filter( 'woocommerce_available_variation', array( __CLASS__, 'filter_available_variation' ), 10, 3 );
		remove_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'filter_show_variation_price' ), 10, 3 );

		remove_filter( 'woocommerce_bundles_update_price_meta', array( __CLASS__, 'filter_show_product_bundles_update_price_meta' ), 10, 2 );
		remove_filter( 'woocommerce_bundle_contains_priced_items', array( __CLASS__, 'filter_bundle_contains_priced_items' ), 10, 2 );
		remove_filter( 'woocommerce_bundled_item_is_priced_individually', array( __CLASS__, 'filter_bundled_item_is_priced_individually' ), 10, 2 );
		remove_filter( 'woocommerce_bundled_item_raw_price_cart', array( __CLASS__, 'filter_bundled_item_raw_price_cart' ), 10, 4 );

		remove_filter( 'woocommerce_nyp_html', array( __CLASS__, 'filter_show_product_get_nyp_price_html' ), 15, 2 );
	}

	/**
	 * Returns the incl/excl tax coefficients for calculating prices incl/excl tax on the client side.
	 *
	 * @since  3.13.6
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_tax_ratios( $product ) {

		WC_CP_Helpers::extend_price_display_precision();

		$ref_price      = 1000.0;
		$ref_price_incl = wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );
		$ref_price_excl = wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );

		WC_CP_Helpers::reset_price_display_precision();

		return array(
			'incl' => $ref_price_incl / $ref_price,
			'excl' => $ref_price_excl / $ref_price
		);
	}

	/**
	 * Calculates product prices.
	 *
	 * @since  3.12.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function get_product_price( $product, $args ) {

		$defaults = array(
			'price' => '',
			'qty'   => 1,
			'calc'  => ''
		);

		$args  = wp_parse_args( $args, $defaults );
		$price = $args[ 'price' ];
		$qty   = $args[ 'qty' ];
		$calc  = $args[ 'calc' ];

		if ( $price ) {

			if ( 'display' === $calc ) {
				$calc = 'excl' === wc_cp_tax_display_shop() ? 'excl_tax' : 'incl_tax';
			}

			if ( 'incl_tax' === $calc ) {
				$price = wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} elseif ( 'excl_tax' === $calc ) {
				$price = wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} else {
				$price = $price * $qty;
			}
		}

		return $price;
	}

	/**
	 * Discounted price getter.
	 *
	 * @param  mixed  $price
	 * @param  mixed  $discount
	 * @return mixed
	 */
	public static function get_discounted_price( $price, $discount ) {

		$discounted_price = $price;

		if ( ! empty( $price ) && ! empty( $discount ) ) {
			$discounted_price = round( ( double ) $price * ( 100 - $discount ) / 100, wc_cp_price_num_decimals() );
		}

		return $discounted_price;
	}


	/*
	|--------------------------------------------------------------------------
	| Hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter get_variation_prices() calls to include discounts when displaying composited variable product prices.
	 *
	 * @param  array                $prices_array
	 * @param  WC_Product_Variable  $product
	 * @return array
	 */
	public static function filter_get_variation_prices( $prices_array, $product ) {

		$filtered_component_option = self::$filtered_component_option;

		if ( ! empty( $filtered_component_option  ) ) {

			$prices         = array();
			$regular_prices = array();
			$sale_prices    = array();

			$discount           = $filtered_component_option->get_discount();
			$priced_per_product = $filtered_component_option->is_priced_individually();

			// Filter regular prices.
			foreach ( $prices_array[ 'regular_price' ] as $variation_id => $regular_price ) {

				if ( $priced_per_product ) {
					$regular_prices[ $variation_id ] = '' === $regular_price ? $prices_array[ 'price' ][ $variation_id ] : $regular_price;
				} else {
					$regular_prices[ $variation_id ] = 0;
				}
			}

			// Filter prices.
			foreach ( $prices_array[ 'price' ] as $variation_id => $price ) {

				if ( $priced_per_product ) {
					if ( false === $filtered_component_option->is_discount_allowed_on_sale_price() ) {
						$regular_price = $regular_prices[ $variation_id ];
					} else {
						$regular_price = $price;
					}
					$price                   = empty( $discount ) ? $price : round( ( double ) $regular_price * ( 100 - $discount ) / 100, wc_cp_price_num_decimals() );
					$prices[ $variation_id ] = apply_filters( 'woocommerce_composited_variation_price', $price, $variation_id, $discount, $filtered_component_option );
				} else {
					$prices[ $variation_id ] = 0;
				}
			}

			// Filter sale prices.
			foreach ( $prices_array[ 'sale_price' ] as $variation_id => $sale_price ) {

				if ( $priced_per_product ) {
					$sale_prices[ $variation_id ] = empty( $discount ) ? $sale_price : $prices[ $variation_id ];
				} else {
					$sale_prices[ $variation_id ] = 0;
				}
			}

			if ( false === $filtered_component_option->is_discount_allowed_on_sale_price() ) {
				asort( $prices );
			}

			$prices_array = array(
				'price'         => $prices,
				'regular_price' => $regular_prices,
				'sale_price'    => $sale_prices
			);
		}

		return $prices_array;
	}


	/**
	 * Filters variation data in the show_product function.
	 *
	 * @param  mixed                 $variation_data
	 * @param  WC_Product            $bundled_product
	 * @param  WC_Product_Variation  $bundled_variation
	 * @return mixed
	 */
	public static function filter_available_variation( $variation_data, $product, $variation ) {

		$filtered_component_option = self::$filtered_component_option;

		if ( ! empty( $filtered_component_option  ) ) {

			// Add/modify price data.

			$variation_data[ 'price' ]         = $variation->get_price();
			$variation_data[ 'regular_price' ] = $variation->get_regular_price();

			$variation_data[ 'price_tax' ] = self::get_tax_ratios( $variation );

			$variation_data[ 'min_qty' ] = self::$filtered_component_option->get_quantity_min();
			$variation_data[ 'max_qty' ] = self::$filtered_component_option->get_quantity_max( true, $variation );

			// Add/modify availability data.
			$variation_data[ 'availability_html' ] = $filtered_component_option->get_availability_html( $variation );

			if ( ! $variation->is_in_stock() || ! $variation->has_enough_stock( $variation_data[ 'min_qty' ] ) ) {
				$variation_data[ 'is_in_stock' ] = false;
			}

			// Add flag for 3-p code.
			$variation_data[ 'is_composited' ] = true;

			// Modify variation images as we don't want the single-product sizes here.

			$variation_thumbnail_size = self::$filtered_component_option->get_selection_thumbnail_size();

			if ( ! in_array( $variation_thumbnail_size, array( 'single', 'shop_single', 'woocommerce_single' ) ) ) {

				if ( $variation_data[ 'image' ][ 'src' ] ) {

					$src = wp_get_attachment_image_src( $variation_data[ 'image_id' ], $variation_thumbnail_size );

					$variation_data[ 'image' ][ 'src' ]    = $src[0];
					$variation_data[ 'image' ][ 'src_w' ]  = $src[1];
					$variation_data[ 'image' ][ 'src_h' ]  = $src[2];
					$variation_data[ 'image' ][ 'srcset' ] = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $variation_data[ 'image_id' ], $variation_thumbnail_size ) : false;
					$variation_data[ 'image' ][ 'sizes' ]  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $variation_data[ 'image_id' ], $variation_thumbnail_size ) : false;
				}
			}
		}

		return $variation_data;
	}

	/**
	 * Filter condition that allows WC to calculate variation price_html.
	 *
	 * @param  boolean               $show
	 * @param  WC_Product_Variable   $product
	 * @param  WC_Product_Variation  $variation
	 * @return boolean
	 */
	public static function filter_show_variation_price( $show, $product, $variation ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			$show = false;

			if ( self::$filtered_component_option->is_priced_individually() && false === self::$filtered_component_option->get_component()->hide_selected_option_price() ) {
				$show = true;
			}
		}

		return $show;
	}

	/**
	 * Components discounts should not trigger bundle price updates.
	 *
	 * @param  boolean            $is
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	public static function filter_show_product_bundles_update_price_meta( $update, $bundle ) {
		return false;
	}

	/**
	 * Filter 'woocommerce_bundle_is_composited'.
	 *
	 * @param  boolean            $is
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	public static function filter_bundle_is_composited( $is, $bundle ) {
		return true;
	}

	/**
	 * If a component is not priced individually, this should force bundled items to return a zero price.
	 *
	 * @param  boolean          $is
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return boolean
	 */
	public static function filter_bundled_item_is_priced_individually( $is_priced_individually, $bundled_item ) {

		if ( ! empty( self::$filtered_component_option ) ) {
			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				$is_priced_individually = false;
			}
		}

		return $is_priced_individually;
	}

	/**
	 * If a component is not priced individually, this should force bundled items to return a zero price.
	 *
	 * @param  boolean            $is
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	public static function filter_bundle_contains_priced_items( $contains, $bundle ) {

		if ( ! empty( self::$filtered_component_option ) ) {
			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				$contains = false;
			}
		}

		return $contains;
	}

	/**
	 * Filters get_price_html to include component discounts.
	 *
	 * @param  string      $price_html
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_show_product_get_price_html( $price_html, $product ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			// Tells NYP to back off.
			$product->is_filtered_price_html = 'yes';

			if ( ! self::$filtered_component_option->is_priced_individually() ) {

				$price_html = '';

			} else {

				$add_suffix = true;

				// Don't add /pc suffix to products in composited bundles (possibly duplicate).
				$filtered_product = self::$filtered_component_option->get_product();
				$product_id       = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

				if ( $filtered_product->get_id() !== $product_id ) {
					$add_suffix = false;
				}

				if ( $add_suffix ) {
					$suffix     = self::$filtered_component_option->get_quantity_min() > 1 ? ' ' . __( '/ pc.', 'woocommerce-composite-products' ) : '';
					$price_html = $price_html . $suffix;
				}
			}

			$price_html = apply_filters( 'woocommerce_composited_item_price_html', $price_html, $product, self::$filtered_component_option->get_component_id(), self::$filtered_component_option->get_composite_id() );
		}

		return $price_html;
	}

	/**
	 * Filters get_price_html to hide nyp prices in static pricing mode.
	 *
	 * @param  string      $price_html
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_show_product_get_nyp_price_html( $price_html, $product ) {

		if ( ! empty( self::$filtered_component_option ) ) {
			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				$price_html = '';
			}
		}

		return $price_html;
	}

	/**
	 * Filters get_price to include component discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_show_product_get_price( $price, $product ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			if ( '' === $price ) {
				return $price;
			}

			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				return 0.0;
			}

			if ( false === self::$filtered_component_option->is_discount_allowed_on_sale_price() ) {
				$regular_price = $product->get_regular_price();
			} else {
				$regular_price = $price;
			}

			if ( $discount = self::$filtered_component_option->get_discount() ) {
				$price = empty( $regular_price ) ? $regular_price : self::get_discounted_price( $regular_price, $discount );
			}
		}

		return $price;
	}

	/**
	 * Filters get_regular_price to include component discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_show_product_get_regular_price( $price, $product ) {

		$filtered_component_option = self::$filtered_component_option;

		if ( ! empty( $filtered_component_option  ) ) {

			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				return 0.0;
			}

			if ( empty( $price ) ) {
				self::$filtered_component_option = false;
				$price = $product->get_price();
				self::$filtered_component_option = $filtered_component_option;
			}
		}

		return $price;
	}

	/**
	 * Filters get_sale_price to include component discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_show_product_get_sale_price( $price, $product ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				return 0.0;
			}

			if ( '' === $price || false === self::$filtered_component_option->is_discount_allowed_on_sale_price() ) {
				$regular_price = $product->get_regular_price();
			} else {
				$regular_price = $price;
			}

			if ( $discount = self::$filtered_component_option->get_discount() ) {
				$price = empty( $regular_price ) ? $regular_price : self::get_discounted_price( $regular_price, $discount );
			}
		}

		return $price;
	}

	/**
	 * Filters 'woocommerce_bundled_item_raw_price_cart' to include component + bundled item discounts.
	 *
	 * @param  double           $price
	 * @param  WC_Product       $product
	 * @param  mixed            $bundled_discount
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return string
	 */
	public static function filter_bundled_item_raw_price_cart( $price, $product, $bundled_discount, $bundled_item ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			if ( '' === $price ) {
				return $price;
			}

			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				return 0.0;
			}

			if ( false === self::$filtered_component_option->is_discount_allowed_on_sale_price() ) {
				$regular_price = $product->get_regular_price( 'edit' );
			} else {
				$regular_price = $price;
			}

			if ( $discount = self::$filtered_component_option->get_discount() ) {
				$price = empty( $regular_price ) ? $regular_price : round( (double) $regular_price * ( 100 - $discount ) / 100, wc_cp_price_num_decimals() );
			}
		}

		return $price;
	}

	/**
	 * Delete component options query cache + composite product price sync cache.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public static function post_status_transition( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'product' === $post_type ) {
			self::flush_cp_cache();
		}
	}

	/**
	 * Delete component options query cache + composite product price sync cache.
	 *
	 * @param  int   $post_id
	 * @return void
	 */
	public static function flush_cp_cache( $post_id = 0 ) {
		if ( $post_id > 0 ) {
			delete_transient( 'wc_cp_query_results_' . $post_id );
			delete_transient( 'wc_cp_permutation_data_' . $post_id );
		} else {
			// Invalidate all CP query cache entries.
			WC_Cache_Helper::get_transient_version( 'product', true );
		}
	}

	/**
	 * Delete price meta reserved to bundles/composites (legacy).
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public static function delete_reserved_price_post_meta( $post_id ) {

		// Get product type.
		$product_type = WC_Product_Factory::get_product_type( $post_id );

		if ( false === in_array( $product_type, array( 'bundle', 'composite' ) ) ) {
			delete_post_meta( $post_id, '_wc_sw_max_price' );
			delete_post_meta( $post_id, '_wc_sw_max_regular_price' );
		}
	}

	/**
	 * Delete price meta reserved to bundles/composites.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function delete_reserved_price_meta( $product ) {

		$product->delete_meta_data( '_wc_cp_composited_value' );
		$product->delete_meta_data( '_wc_cp_composited_weight' );

		if ( false === in_array( $product->get_type(), array( 'bundle', 'composite' ) ) ) {
			$product->delete_meta_data( '_wc_sw_max_price' );
			$product->delete_meta_data( '_wc_sw_max_regular_price' );
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
	public static function read_price_data( $product ) {

		$components      = $product->get_components();
		$shop_price_calc = $product->get_shop_price_calc();

		$price_data = array();

		$permutations = array(
			'min' => array(),
			'max' => array()
		);

		$component_option_prices          = array();
		$component_option_raw_prices      = array();
		$component_options_count          = 0;
		$component_options_count_expanded = 0;

		/**
		 * 'woocommerce_composite_price_data_permutation_vectors' filter.
		 *
		 * When searching for the permutations with the min/max price, use this filter to narrow down the initial search vectors and speed up the search.
		 * Typically you would use this filter to populate each Component vector only with product IDs that belong in the min/max price permutations.
		 * Of course this assumes that you know the min/max price permutations already.
		 *
		 * @param  array                 $vectors
		 * @param  WC_Product_Composite  $product
		 */
		$permutation_vectors          = apply_filters( 'woocommerce_composite_price_data_permutation_vectors', array(), $product );
		$permutation_vectors_expanded = array();
		$permutations_count           = 1;
		$permutations_count_expanded  = 1;

		$permutations_calc_mode = empty( $permutation_vectors ) ? '' : 'fast';

		$permutations_calc_scenarios          = $product->scenarios()->exist() && function_exists( 'wc_cp_cartesian' );
		$permutations_calc_scenarios_expanded = false;

		$has_conditional_component_scenarios = sizeof( $product->scenarios()->get_ids_by_action( 'conditional_components' ) );

		/*
		 * Set up permutation vectors.
		 */
		if ( empty( $permutation_vectors ) ) {

			foreach ( $components as $component_id => $component ) {

				// Skip component if not priced individually.
				if ( $has_conditional_component_scenarios || $component->is_priced_individually() ) {

					$default_option = $component->get_default_option();

					if ( 'defaults' === $shop_price_calc && $default_option ) {
						$component_options = array( $default_option );
					} else {
						$component_options = $component->get_options();
					}

					if ( ! empty( $component_options ) ) {

						// Validate whether the component can be skipped.
						$permutation_vectors[ $component_id ] = array_merge( $component_options, array( 0 ) );

						// Build expanded set.
						if ( $permutations_calc_scenarios ) {

							$component_options_in_scenarios = array();

							foreach ( $product->scenarios()->get_scenarios() as $scenario ) {
								$component_options_in_scenarios = array_merge( $component_options_in_scenarios, $scenario->get_ids( $component_id ) );
							}

							if ( sizeof( array_diff( $component_options_in_scenarios, $component_options, array( 0, -1 ) ) ) ) {
								// Expand variations.
								$component_options_expanded = array_unique( array_merge( $component_options, self::get_expanded_component_options( $component_options ) ) );
								// Validate whether the component can be skipped.
								$permutation_vectors_expanded[ $component_id ] = array_merge( $component_options_expanded, array( 0 ) );
							} else {
								$permutation_vectors_expanded[ $component_id ] = $permutation_vectors[ $component_id ];
							}
						}
					}
				}
			}

		} else {
			$permutation_vectors_expanded = $permutation_vectors;
		}

		// Count.
		if ( ! empty( $permutation_vectors ) ) {
			foreach ( $permutation_vectors as $component_id => $component_options ) {
				// Add permutations.
				$permutations_count = $permutations_count * sizeof( $component_options );
				// Add options.
				$component_options_count += sizeof( $component_options );
			}
		}

		// Count expanded.
		if ( ! empty( $permutation_vectors_expanded ) ) {
			foreach ( $permutation_vectors_expanded as $component_id => $component_options_expanded ) {
				// Add permutations.
				$permutations_count_expanded = $permutations_count_expanded * sizeof( $component_options_expanded );
				// Add options.
				$component_options_count_expanded += sizeof( $component_options_expanded );
			}
		}

		/**
		 * 'woocommerce_composite_price_data_permutation_search_complexity_threshold' filter.
		 *
		 * When searching for the min/max price permutations, scenarios will be taken into account only if the calculation is reasonably simple/fast.
		 * The complexity of the calculation is evaluated using 'WC_CP_Scenarios_Manager::get_validation_complexity_index'.
		 * If the index is above a threshold value, scenarios will be ignored and the min/max price permutations will be based on prices only.
		 *
		 * @param  int                   $threshold
		 * @param  WC_Product_Composite  $product
		 */
		$permutation_search_complexity_threshold = apply_filters( 'woocommerce_composite_price_data_permutation_search_complexity_threshold', 10, $product );

		if ( $permutations_calc_scenarios ) {
			// Find cheapest/most expensive permutation taking scenarios into account?
			if ( $product->scenarios()->get_validation_complexity_index( $permutations_count, sizeof( $permutation_vectors ) ) < $permutation_search_complexity_threshold ) {
				// Only use expanded vectors if calculation complexity is low.
				if ( $product->scenarios()->get_validation_complexity_index( $permutations_count_expanded, sizeof( $permutation_vectors_expanded ) ) < $permutation_search_complexity_threshold ) {
					$permutations_calc_scenarios_expanded = true;
				}
			} else {
				$permutations_calc_scenarios = false;
			}
		}

		// Use expanded vectors?
		if ( $permutations_calc_scenarios_expanded ) {
			$permutation_vectors     = $permutation_vectors_expanded;
			$component_options_count = $component_options_count_expanded;
		}

		if ( ! empty( $permutation_vectors ) ) {

			if ( empty( $permutations_calc_mode ) ) {

				/**
				 * 'woocommerce_composite_price_data_fast_read_threshold' filter.
				 *
				 * If the total number of component options is above this threshold, the min/max price permutations search will be based on raw prices, obtained directly from the DB.
				 *
				 * @param  int                   $threshold
				 * @param  WC_Product_Composite  $product
				 */
				$fast_read_threshold = apply_filters( 'woocommerce_composite_price_data_fast_read_threshold', 100, $product );

				// Variable products have multiple '_price' meta since WC 2.6.
				$permutations_calc_mode = $component_options_count > $fast_read_threshold || 'hidden' === $shop_price_calc ? 'fast' : 'accurate';
			}

			/*
			 * Set up prices.
			 */
			foreach ( $components as $component_id => $component ) {

				if ( ! isset( $permutation_vectors[ $component_id ] ) ) {
					continue;
				}

				$component_options = $permutation_vectors[ $component_id ];

				$component_option_prices[ $component_id ]     = array();
				$component_option_raw_prices[ $component_id ] = array();

				if ( 'fast' === $permutations_calc_mode ) {

					$component_option_prices[ $component_id ] = $component_option_raw_prices[ $component_id ] = self::get_raw_component_option_prices( $component_options );

				} else {

					foreach ( $component_options as $component_option_id ) {

						$component_option = $component->get_option( $component_option_id );

						if ( $component_option ) {

							if ( $component_option->is_purchasable() ) {

								// Display prices after applying filters.
								$component_option_prices[ $component_id ][ 'min' ][ $component_option_id ] = $component_option->get_price( 'min', true );
								$component_option_prices[ $component_id ][ 'max' ][ $component_option_id ] = $component_option->get_price( 'max', true );

								// Raw prices.
								$component_option_raw_prices[ $component_id ][ 'min' ][ $component_option_id ] = $component_option->min_price;
								$component_option_raw_prices[ $component_id ][ 'max' ][ $component_option_id ] = $component_option->max_price;
							}
						}
					}
				}

				if ( $permutations_calc_scenarios ) {

					$component_option_prices[ $component_id ][ 'min' ][ 0 ]     = 0.0;
					$component_option_raw_prices[ $component_id ][ 'min' ][ 0 ] = 0.0;

					$component_option_prices[ $component_id ][ 'max' ][ 0 ]     = 0.0;
					$component_option_raw_prices[ $component_id ][ 'max' ][ 0 ] = 0.0;
				}
			}

			/*
			 * Find cheapest/most expensive permutation taking scenarios into account.
			 */
			if ( $permutations_calc_scenarios ) {

				// Build a hash based on component option prices and products cache version, which should change when composite data is modified.
				$transient_hash   = md5( json_encode( array( $component_option_prices, WC_Cache_Helper::get_transient_version( 'product' ) ) ) );
				$transient_name   = 'wc_cp_permutation_data_' . $product->get_id();
				$permutation_data = get_transient( $transient_name );

				if ( ! defined( 'WC_CP_DEBUG_PERMUTATION_TRANSIENTS' ) && is_array( $permutation_data ) && isset( $permutation_data[ 'hash' ] ) && $permutation_data[ 'hash' ] === $transient_hash ) {

					$permutations[ 'min' ] = $permutation_data[ 'min' ];
					$permutations[ 'max' ] = $permutation_data[ 'max' ];

				} else {

					$min_price = $max_price = '';

					$invalid_permutation_part = false;

					foreach ( wc_cp_cartesian( $permutation_vectors ) as $permutation ) {

						// Skip permutation if already found invalid.
						if ( is_array( $invalid_permutation_part ) ) {

							$validate_permutation = false;

							foreach ( $invalid_permutation_part as $invalid_permutation_part_key => $invalid_permutation_part_value ) {
								if ( $invalid_permutation_part_value !== $permutation[ $invalid_permutation_part_key ] ) {
									$validate_permutation = true;
									break;
								}
							}

							if ( ! $validate_permutation ) {
								continue;
							} else {
								$invalid_permutation_part = false;
							}
						}

						$configuration = array();

						foreach ( $permutation as $component_id => $component_option_id ) {
							$configuration[ $component_id ] = array(
								'product_id' => $component_option_id
							);
						}

						$validation_result = $product->scenarios()->validate_configuration( $configuration );

						if ( is_wp_error( $validation_result ) ) {

							$error_data               = $validation_result->get_error_data( $validation_result->get_error_code() );
							$invalid_permutation_part = array();

							// Keep a copy of the invalid permutation up to the offending component.
							foreach ( $permutation as $component_id => $component_option_id ) {
								$invalid_permutation_part[ $component_id ] = $component_option_id;
								if ( $component_id === $error_data[ 'component_id' ] ) {
									break;
								}
							}

						} else {

							/*
							 * Find the permutation with the min/max price.
							 */
							$min_permutation_price = $max_permutation_price = 0.0;

							foreach ( $components as $component_id => $component ) {

								// Skip component if not relevant for price calculations.
								if ( ! isset( $permutation[ $component_id ] ) ) {
									continue;
								}

								$component_option_id = $permutation[ $component_id ];

								$component_option_price_min = 0.0;
								$component_option_price_max = 0.0;

								if ( $component_option_id > 0 ) {

									// Empty price.
									if ( ! isset( $component_option_prices[ $component_id ][ 'min' ][ $component_option_id ] ) ) {
										if ( $component->is_priced_individually() ) {
											continue 2;
										} else {
											continue;
										}
									}

									$component_option_price_min = $component_option_prices[ $component_id ][ 'min' ][ $component_option_id ];
									$component_option_price_max = $component_option_prices[ $component_id ][ 'max' ][ $component_option_id ];
								}

								$quantity_min = $component->get_quantity( 'min' );
								$quantity_max = $component->get_quantity( 'max' );

								$min_permutation_price += $quantity_min * (double) $component_option_price_min;

								if ( INF !== $max_permutation_price ) {
									if ( INF !== $component_option_price_max && '' !== $quantity_max ) {
										$max_permutation_price += $quantity_max * (double) $component_option_price_max;
									} else {
										$max_permutation_price = INF;
									}
								}
							}

							if ( $min_permutation_price < $min_price || '' === $min_price ) {
								$permutations[ 'min' ] = $permutation;
								$min_price             = $min_permutation_price;
							}

							if ( INF !== $max_permutation_price ) {
								if ( $max_permutation_price > $max_price || '' === $max_price ) {
									$permutations[ 'max' ] = $permutation;
									$max_price             = $max_permutation_price;
								}
							} else {
								$permutations[ 'max' ] = array();
							}
						}
					}

					$permutation_data = array(
						'min'  => $permutations[ 'min' ],
						'max'  => $permutations[ 'max' ],
						'hash' => $transient_hash
					);

					delete_transient( $transient_name );
					set_transient( $transient_name, $permutation_data, DAY_IN_SECONDS * 30 );
				}

			/*
			 * Find cheapest/most expensive permutation without considering scenarios.
			 */
			} else {

				$has_inf_max_price = false;

				/*
				 * Use filtered prices to find the permutation with the min/max price.
				 */
				foreach ( $components as $component_id => $component ) {

					if ( ! isset( $permutation_vectors[ $component_id ] ) ) {
						continue;
					}

					if ( empty( $component_option_prices[ $component_id ] ) ) {
						continue;
					}

					$component_option_prices_min = $component_option_prices[ $component_id ][ 'min' ];
					asort( $component_option_prices_min );

					$component_option_prices_max = $component_option_prices[ $component_id ][ 'max' ];
					asort( $component_option_prices_max );

					$min_component_price = current( $component_option_prices_min );
					$max_component_price = end( $component_option_prices_max );

					$min_component_price_ids = array_keys( $component_option_prices_min );
					$max_component_price_ids = array_keys( $component_option_prices_max );

					$min_component_price_id  = current( $min_component_price_ids );
					$max_component_price_id  = end( $max_component_price_ids );

					$quantity_min = $component->get_quantity( 'min' );
					$quantity_max = $component->get_quantity( 'max' );

					$permutations[ 'min' ][ $component_id ] = $component->is_optional() || 0 === $quantity_min ? 0 : $min_component_price_id;

					if ( ! $has_inf_max_price ) {
						if ( INF !== $max_component_price && '' !== $quantity_max ) {
							$permutations[ 'max' ][ $component_id ] = $max_component_price_id;
						} else {
							$permutations[ 'max' ] = array();
							$has_inf_max_price     = true;
						}
					}
				}
			}
		}

		$price_data[ 'permutations' ] = $permutations;

		/*
		 * When permutations are calculated in FAST mode, the calculated min/max permutations are static since they are obtained from the DB.
		 * In this case, conditional pricing plugins cannot influence the result, and min/max composite raw prices can be calculated from these static min/max permutations.
		 *
		 * When permutations are calculated in ACCURATE mode, conditional pricing plugins can influence the result, which may vary for different users.
		 * As a result, the min/max composite raw prices cannot be calculated from these variable min/max permutations since we need to store a single min and max composite raw price in the DB.
		 * In this case, the calculated min/max permutations data will not be used to compute the min/max raw composite prices, which will be calculated ignoring scenarios.
		 */
		if ( 'accurate' === $permutations_calc_mode && ! empty( $permutations[ 'min' ] ) ) {
			$price_data[ 'raw_prices' ] = $component_option_raw_prices;
		}

		return $price_data;
	}

	/**
	 * Get expanded component options to include variations straight from the DB.
	 *
	 * @param  array $ids
	 * @return array
	 */
	public static function get_expanded_component_options( $ids ) {

		global $wpdb;

		if ( empty( $ids ) ) {
			return array();
		}

		$results_cache_key = 'expanded_component_options_' . md5( json_encode( $ids ) );
		$results = WC_CP_Helpers::cache_get( $results_cache_key );

		if ( null === $results ) {

			$results = $wpdb->get_results( "
				SELECT posts.ID AS id, posts.post_parent as parent_id FROM {$wpdb->posts} AS posts
				WHERE posts.post_type = 'product_variation'
				AND post_parent IN ( " . implode( ',', $ids ) . " )
				AND posts.post_status = 'publish'
			", ARRAY_A );

			WC_CP_Helpers::cache_set( $results_cache_key, $results );
		}

		if ( ! empty( $results ) ) {
			$ids = array_diff( $ids, wp_list_pluck( $results, 'parent_id' ) );
			$ids = array_merge( $ids, wp_list_pluck( $results, 'id' ) );
		}

		return $ids;
	}

	/**
	 * Get raw product prices straight from the DB.
	 *
	 * @param  array $ids
	 * @return array
	 */
	public static function get_raw_component_option_prices( $ids ) {

		global $wpdb;

		$results_cache_key = 'raw_component_option_prices_' . md5( json_encode( $ids ) );
		$results = WC_CP_Helpers::cache_get( $results_cache_key );

		if ( null === $results ) {

			$results = $wpdb->get_results( "
				SELECT posts.ID AS id, postmeta.meta_value as price FROM {$wpdb->posts} AS posts
				LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_price'
				WHERE posts.post_type IN ( 'product', 'product_variation' )
				AND id IN ( " . implode( ',', $ids ) . " )
				AND posts.post_status = 'publish'
			", ARRAY_A );

			WC_CP_Helpers::cache_set( $results_cache_key, $results );
		}

		$prices = array(
			'min' => array(),
			'max' => array()
		);

		if ( class_exists( 'WC_Name_Your_Price_Helpers' ) ) {

			$nyp_results_cache_key = $results_cache_key . '_nyp';
			$nyp_results           = WC_CP_Helpers::cache_get( $nyp_results_cache_key );

			if ( null === $nyp_results ) {

				$nyp_results = $wpdb->get_results( "
					SELECT posts.ID AS id, postmeta2.meta_value AS min_price FROM {$wpdb->posts} AS posts
					LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_nyp'
					LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id AND postmeta2.meta_key = '_min_price'
					WHERE posts.post_type IN ( 'product', 'product_variation' )
					AND postmeta.meta_value IS NOT NULL
					AND postmeta.meta_value = 'yes'
					AND id IN ( " . implode( ',', $ids ) . " )
					AND posts.post_status = 'publish'
				", ARRAY_A );

				WC_CP_Helpers::cache_set( $nyp_results_cache_key, $nyp_results );
			}

			foreach ( $nyp_results as $nyp_result ) {

				$id = $nyp_result[ 'id' ];

				$price_min = '' === $nyp_result[ 'min_price' ] ? 0.0 : (double) $nyp_result[ 'min_price' ];
				$price_max = INF;

				$prices[ 'min' ][ $id ] = $price_min;
				$prices[ 'max' ][ $id ] = $price_max;
			}
		}

		// Multiple '_price' meta may exist.
		foreach ( $results as $result ) {

			if ( '' === $result[ 'price' ] ) {
				continue;
			}

			$id = $result[ 'id' ];

			$price_min = isset( $prices[ 'min' ][ $id ] ) ? min( (double) $result[ 'price' ], $prices[ 'min' ][ $id ] ) : (double) $result[ 'price' ];
			$price_max = isset( $prices[ 'max' ][ $id ] ) ? max( (double) $result[ 'price' ], $prices[ 'max' ][ $id ] ) : (double) $result[ 'price' ];

			$prices[ 'min' ][ $id ] = $price_min;
			$prices[ 'max' ][ $id ] = $price_max;
		}

		return $prices;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Calculates bundled product prices incl. or excl. tax depending on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @deprecated  3.12.0
	 */
	public static function get_product_display_price( $product, $price, $qty = 1 ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Products::get_product_price()' );
		return self::get_product_price( $product, array(
			'price' => $price,
			'qty'   => $qty,
			'calc'  => 'display'
		) );
	}
}

WC_CP_Products::init();
