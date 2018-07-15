<?php
/**
 * WC_Product_Composite class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Composite Products
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Product Class.
 *
 * @class    WC_Product_Composite
 * @version  3.13.11
 */
class WC_Product_Composite extends WC_Product {

	/**
	 * Array of composite-type extended product data fields used in CRUD and runtime operations.
	 * @var array
	 */
	private $extended_data = array(
		'add_to_cart_form_location' => 'default',  // "Form Location" option.
		'shop_price_calc'           => 'defaults', // "Catalog Price" option.
		'layout'                    => 'default',  // "Composite Layout" option.
		'editable_in_cart'          => false,      // "Edit in cart" option.
		'sold_individually_context' => 'product',  // Provides context when the "Sold Individually" option is set to 'yes': 'product' or 'configuration'.
		'min_raw_price'             => '',         // Min raw price of the composite based on raw prices, as stored in the DB.
		'max_raw_price'             => ''          // Max raw price of the composite based on raw prices, as stored in the DB.
	);

	/**
	 * Raw meta where all component data is saved.
	 * A shamefully simple way to store/manage data that just works, but can't be used for any complex operations on the DB side.
	 * @var array
	 */
	private $composite_meta = array();

	/**
	 * Indicates whether composite meta have been changed.
	 * @var array
	 */
	private $composite_meta_save_pending = false;

	/**
	 * Raw meta where all scenario data is saved.
	 * A shamefully simple way to store/manage data that just works, but can't be used for any complex operations on the DB side.
	 * @var array
	 */
	private $scenario_meta = array();

	/**
	 * Configurations with lowest/highest composite prices.
	 * Used in 'get_composite_price', 'get_composite_regular_price', 'get_composite_price_including_tax' and 'get_composite_price_excluding_tax methods'.
	 * @var array
	 */
	private $permutations = array();

	/**
	 * Array of composite price data for consumption by the front-end script.
	 * @var array
	 */
	private $composite_price_data = array();

	/**
	 * Array of cached composite prices.
	 * @var array
	 */
	private $composite_price_cache = array();

	/**
	 * Storage of 'contains' keys, most set during sync.
	 * @var array
	 */
	private $contains = array();

	/**
	 * Indicates whether the product has been synced with component data.
	 * @var boolean
	 */
	private $is_synced = false;

	/**
	 * Constructor.
	 *
	 * @param  mixed $composite
	 */
	public function __construct( $composite ) {

		// Initialize private properties.
		$this->load_defaults();

		// Define/load type-specific data.
		$this->load_extended_data();

		// Load product data.
		parent::__construct( $composite );
	}

	/**
	 * Get internal type.
	 *
	 * @since  3.9.0
	 *
	 * @return string
	 */
	public function get_type() {
		return 'composite';
	}

	/**
	 * Load property and runtime cache defaults to trigger a re-sync.
	 *
	 * @since 3.9.0
	 */
	public function load_defaults() {

		$this->permutations = array(
			'min' => array(),
			'max' => array()
		);

		$this->contains = array(
			'priced_individually'  => null,
			'shipped_individually' => null,
			'priced_indefinitely'  => false,
			'optional'             => false,
			'mandatory'            => false,
			'discounted'           => false
		);

		$this->is_synced                 = false;
		$this->composite_price_data      = array();
		$this->composite_price_cache     = array();
	}

	/**
	 * Define type-specific data.
	 *
	 * @since  3.9.0
	 */
	private function load_extended_data() {

		// Back-compat.
		$this->product_type = 'composite';

		// Define type-specific fields and let WC use our data store to read the data.
		$this->data = array_merge( $this->data, $this->extended_data );
	}

	/**
	 * Sync composite props with component objects.
	 *
	 * @since  3.12.0
	 *
	 * @param  bool  $force
	 * @return bool
	 */
	public function sync( $force = false ) {

		if ( $this->is_synced && false === $force ) {
			return false;
		}

		$components = $this->get_components();

		if ( ! empty( $components ) ) {

			// Initialize 'contains' data.
			foreach ( $components as $component_id => $component ) {

				if ( $component->is_optional() ) {
					$this->contains[ 'priced_indefinitely' ] = true;
					$this->contains[ 'optional' ]            = true;
				} else {
					$this->contains[ 'mandatory' ] = true;
				}

				if ( $component->is_priced_individually() && $component->get_discount() ) {
					$this->contains[ 'discounted' ] = true;
				}

				$quantity_min = $component->get_quantity( 'min' );
				$quantity_max = $component->get_quantity( 'max' );

				if ( $quantity_min !== $quantity_max ) {
					$this->contains[ 'priced_indefinitely' ] = true;
				}
			}
		}

		// Set synced flag.
		$this->is_synced = true;

		/*
		 * Sync min/max raw prices.
		 */

		if ( $this->sync_raw_prices() ) {
			$this->data_store->save_raw_prices( $this );
		}

		/**
		 * 'woocommerce_composite_synced' action.
		 *
		 * @param  WC_Product_Composite  $this
		 */
		do_action( 'woocommerce_composite_synced', $this );

		return true;
	}

	/**
	 * Sync product bundle raw price meta.
	 *
	 * @since  3.12.0
	 *
	 * @return bool
	 */
	private function sync_raw_prices() {

		// Initialize min/max raw prices.
		$min_raw_price = $max_raw_price = $this->get_price( 'sync' );

		$components = $this->get_components();

		// NYP products have infinite max price.
		if ( $this->is_nyp() ) {
			$max_raw_price = INF;
		}

		if ( ! empty( $components ) ) {
			foreach ( $components as $component_id => $component ) {
				// Infinite max quantity.
				if ( '' === $component->get_quantity( 'max' ) ) {
					$max_raw_price = INF;
				}
			}
		}

		// Price calculations.
		if ( $this->contains( 'priced_individually' ) ) {

			if ( 'hidden' !== $this->get_shop_price_calc() ) {

				$price_data = WC_CP_Products::read_price_data( $this );

				/*
				 * Store cheapest/most expensive permutation.
				 */
				if ( ! empty( $price_data[ 'permutations' ] ) ) {
					$this->permutations[ 'min' ] = $price_data[ 'permutations' ][ 'min' ];
					$this->permutations[ 'max' ] = $price_data[ 'permutations' ][ 'max' ];
				}

				$calc_from_permutations = false === isset( $price_data[ 'raw_prices' ] );

				// Permutations calculated from prices obtained in FAST mode directly from meta: Calculate min/max raw prices from permutations.
				if ( $calc_from_permutations ) {

					// Min raw price.
					foreach ( $components as $component_id => $component ) {

						if ( empty( $this->permutations[ 'min' ][ $component_id ] ) ) {
							continue;
						}

						$min_component_raw_price_option = $component->get_option( $this->permutations[ 'min' ][ $component_id ] );

						if ( $min_component_raw_price_option ) {
							$min_component_raw_price = $min_component_raw_price_option->min_price;
							$quantity_min            = $component->is_optional() ? 0 : $component->get_quantity( 'min' );
							$min_raw_price          += $quantity_min * $min_component_raw_price;
						}
					}

					// Max raw price.

					// Infinite.
					if ( empty( $this->permutations[ 'max' ] ) ) {

						$max_raw_price = INF;

					// Finite.
					} elseif ( INF !== $max_raw_price ) {

						foreach ( $components as $component_id => $component ) {

							if ( empty( $this->permutations[ 'max' ][ $component_id ] ) ) {
								continue;
							}

							$max_component_raw_price_option = $component->get_option( $this->permutations[ 'max' ][ $component_id ] );

							if ( $max_component_raw_price_option ) {
								$max_component_raw_price = $max_component_raw_price_option->max_price;
								$quantity_max            = $component->get_quantity( 'max' );
								$max_raw_price          += $quantity_max * $max_component_raw_price;
							}
						}
					}

				// Permutations calculated from prices obtained in ACCURATE mode from objects: Min/max permutations may vary for different users, but we need to store a single, consistent composite min/max raw price.
				// In this case, ignore the min/max permutations data and calculate best/worst-case values ignoring scenarios.
				} else {

					foreach ( $components as $component_id => $component ) {

						if ( empty( $price_data[ 'raw_prices' ][ $component_id ] ) ) {
							continue;
						}

						$component_option_raw_prices_min = $price_data[ 'raw_prices' ][ $component_id ][ 'min' ];
						asort( $component_option_raw_prices_min );

						$component_option_raw_prices_max = $price_data[ 'raw_prices' ][ $component_id ][ 'max' ];
						asort( $component_option_raw_prices_max );

						$min_component_raw_price = current( $component_option_raw_prices_min );
						$max_component_raw_price = end( $component_option_raw_prices_max );

						$quantity_min = $component->is_optional() ? 0 : $component->get_quantity( 'min' );
						$quantity_max = $component->get_quantity( 'max' );

						$min_raw_price += $quantity_min * (double) $min_component_raw_price;

						if ( INF !== $max_raw_price ) {
							if ( INF !== $max_component_raw_price && '' !== $quantity_max ) {
								$max_raw_price = $max_raw_price + $quantity_max * (double) $max_component_raw_price;
							} else {
								$max_raw_price = INF;
							}
						}
					}
				}
			}
		}

		// Filter raw prices.
		$min_raw_price = apply_filters( 'woocommerce_min_composite_price', $min_raw_price, $this );
		$max_raw_price = apply_filters( 'woocommerce_max_composite_price', $max_raw_price, $this );

		// Filter min/max price index.
		$this->permutations[ 'min' ] = apply_filters( 'woocommerce_min_composite_price_index', $this->permutations[ 'min' ], $this );
		$this->permutations[ 'max' ] = apply_filters( 'woocommerce_max_composite_price_index', $this->permutations[ 'max' ], $this );

		$raw_price_meta_changed = false;

		if ( $this->get_min_raw_price( 'sync' ) !== $min_raw_price || $this->get_max_raw_price( 'sync' ) !== $max_raw_price ) {
			$raw_price_meta_changed = true;
		}

		$this->set_min_raw_price( $min_raw_price );
		$this->set_max_raw_price( $max_raw_price );

		if ( $raw_price_meta_changed ) {
			return true;
		}

		return false;
	}

	/**
	 * Stores composite pricing strategy data that is passed to JS.
	 *
	 * @return void
	 */
	public function load_price_data() {

		$this->composite_price_data[ 'is_purchasable' ]   = $this->is_purchasable() ? 'yes' : 'no';
		$this->composite_price_data[ 'has_price_range' ]  = $this->contains( 'priced_individually' ) && $this->get_composite_price( 'min', true ) !== $this->get_composite_price( 'max', true ) ? 'yes' : 'no';
		$this->composite_price_data[ 'show_free_string' ] = ( $this->contains( 'priced_individually' ) ? apply_filters( 'woocommerce_composite_show_free_string', false, $this ) : true ) ? 'yes' : 'no';

		$this->composite_price_data[ 'is_priced_individually' ] = array();

		$components = $this->get_components();

		if ( ! empty( $components ) ) {
			foreach ( $components as $component_id => $component ) {
				$this->composite_price_data[ 'is_priced_individually' ][ $component_id ] = $component->is_priced_individually() ? 'yes' : 'no';
			}
		}

		$this->composite_price_data[ 'prices' ]         = new stdClass;
		$this->composite_price_data[ 'regular_prices' ] = new stdClass;
		$this->composite_price_data[ 'prices_tax' ]     = new stdClass;
		$this->composite_price_data[ 'addons_prices' ]  = new stdClass;
		$this->composite_price_data[ 'quantities' ]     = new stdClass;

		$this->composite_price_data[ 'base_price' ]         = $this->get_price();
		$this->composite_price_data[ 'base_regular_price' ] = $this->get_regular_price();
		$this->composite_price_data[ 'base_price_tax' ]     = WC_CP_Products::get_tax_ratios( $this );
	}

	/**
	 * Calculates composite prices.
	 *
	 * @since  3.12.0
	 *
	 * @param  array  $args
	 * @return mixed
	 */
	public function calculate_price( $args ) {

		$min_or_max = isset( $args[ 'min_or_max' ] ) && in_array( $args[ 'min_or_max' ], array( 'min', 'max' ) ) ? $args[ 'min_or_max' ] : 'min';
		$qty        = isset( $args[ 'qty' ] ) ? absint( $args[ 'qty' ] ) : 1;
		$price_prop = isset( $args[ 'prop' ] ) && in_array( $args[ 'prop' ] , array( 'price', 'regular_price' ) ) ? $args[ 'prop' ] : 'price';
		$price_calc = isset( $args[ 'calc' ] ) && in_array( $args[ 'calc' ] , array( 'incl_tax', 'excl_tax', 'display', '' ) ) ? $args[ 'calc' ] : '';
		$strict     = isset( $args[ 'strict' ] ) && $args[ 'strict' ] && 'regular_price' === $price_prop;

		if ( $this->contains( 'priced_individually' ) ) {

			$this->sync();

			$cache_key = md5( json_encode( apply_filters( 'woocommerce_composite_prices_hash', array(
				'prop'       => $price_prop,
				'min_or_max' => $min_or_max,
				'calc'       => $price_calc,
				'qty'        => $qty,
				'strict'     => $strict,
			), $this ) ) );


			if ( isset( $this->composite_price_cache[ $cache_key ] ) ) {
				$price = $this->composite_price_cache[ $cache_key ];
			} else {

				$raw_price_fn = 'get_' . $min_or_max . '_raw_price';

				if ( '' === $this->$raw_price_fn() || INF === $this->$raw_price_fn() || empty( $this->permutations[ $min_or_max ] ) ) {
					$price = '';
				} else {

					$price_fn = 'get_' . $price_prop;
					$price    = WC_CP_Products::get_product_price( $this, array(
						'price' => $this->$price_fn(),
						'qty'   => $qty,
						'calc'  => $price_calc,
					) );

					foreach ( $this->permutations[ $min_or_max ] as $component_id => $product_id ) {

						if ( ! $product_id ) {
							continue;
						}

						$component = $this->get_component( $component_id );
						$item_qty  = 'min' === $min_or_max && $component->is_optional() ? 0 : $qty * $component->get_quantity( $min_or_max );

						if ( $item_qty ) {

							$composited_product = $this->get_component_option( $component_id, $product_id );

							if ( ! $composited_product ) {
								continue;
							}

							if ( false === $composited_product->is_purchasable() ) {
								continue;
							}

							$price += $composited_product->calculate_price( array(
								'min_or_max' => $min_or_max,
								'qty'        => $item_qty,
								'strict'     => $strict,
								'calc'       => $price_calc,
								'prop'       => $price_prop
							) );
						}
					}
				}
			}

		} else {

			$price_fn = 'get_' . $price_prop;
			$price    = WC_CP_Products::get_product_price( $this, array(
				'price' => $this->$price_fn(),
				'qty'   => $qty,
				'calc'  => $price_calc,
			) );
		}

		return $price;
	}

	/**
	 * Get min/max composite price.
	 *
	 * @param  string  $min_or_max
	 * @param  boolean $display
	 * @return mixed
	 */
	public function get_composite_price( $min_or_max = 'min', $display = false ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'calc'       => $display ? 'display' : '',
			'prop'       => 'price'
		) );
	}

	/**
	 * Get min/max composite regular price.
	 *
	 * @param  string   $min_or_max
	 * @param  boolean  $display
	 * @return mixed
	 */
	public function get_composite_regular_price( $min_or_max = 'min', $display = false ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'calc'       => $display ? 'display' : '',
			'prop'       => 'regular_price',
			'strict'     => true
		) );
	}

	/**
	 * Get min/max composite price including tax.
	 *
	 * @return mixed
	 */
	public function get_composite_price_including_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'incl_tax',
			'prop'       => 'price'
		) );
	}

	/**
	 * Get min/max composite price excluding tax.
	 *
	 * @return double
	 */
	public function get_composite_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'excl_tax',
			'prop'       => 'price'
		) );
	}

	/**
	 * Get min/max regular composite price including tax.
	 *
	 * @since  3.12.0
	 *
	 * @param  string   $min_or_max
	 * @param  integer  $qty
	 * @return mixed
	 */
	public function get_composite_regular_price_including_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'incl_tax',
			'prop'       => 'regular_price',
			'strict'     => true
		) );
	}

	/**
	 * Get min/max regular composite price excluding tax.
	 *
	 * @since  3.12.0
	 *
	 * @param  string   $min_or_max
	 * @param  integer  $qty
	 * @return mixed
	 */
	public function get_composite_regular_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'excl_tax',
			'prop'       => 'regular_price',
			'strict'     => true
		) );
	}

	/**
	 * Returns range style html price string without min and max.
	 *
	 * @param  mixed  $price
	 * @return string
	 */
	public function get_price_html( $price = '' ) {

		$this->sync();

		$components = $this->get_components();

		if ( $this->contains( 'priced_individually' ) && ! empty( $components ) ) {

			// Get the price.
			if ( 'hidden' === $this->get_shop_price_calc() || '' === $this->get_composite_price( 'min' ) ) {

				$price = apply_filters( 'woocommerce_composite_empty_price_html', '', $this );

			} else {

				$has_indefinite_max_price = 'defaults' === $this->get_shop_price_calc() || $this->contains( 'priced_indefinitely' ) || INF === $this->get_max_raw_price();

				/**
				 * 'woocommerce_composite_force_old_style_price_html' filter.
				 *
				 * Used to suppress the range-style display of bundle price html strings.
				 *
				 * @param  boolean            $suppress_range_price_html
				 * @param  WC_Product_Bundle  $this
				 */
				$suppress_range_price_html = $has_indefinite_max_price || apply_filters( 'woocommerce_composite_force_old_style_price_html', false, $this );

				$price_min = $this->get_composite_price( 'min', true );
				$price_max = $this->get_composite_price( 'max', true );

				if ( $suppress_range_price_html ) {

					$price = wc_price( $price_min );

					$regular_price_min = $this->get_composite_regular_price( 'min', true );

					if ( $regular_price_min !== $price_min ) {

						$regular_price = wc_price( $regular_price_min );

						if ( $price_min !== $price_max ) {
							$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-composite-products' ), wc_get_price_html_from_text(), wc_format_sale_price( $regular_price, $price ) . $this->get_price_suffix() );
						} else {
							$price = wc_format_sale_price( $regular_price, $price ) . $this->get_price_suffix();
						}

						$price = apply_filters( 'woocommerce_composite_sale_price_html', $price, $this );

					} elseif ( 0.0 === $price_min && 0.0 === $price_max ) {

						$free_string = apply_filters( 'woocommerce_composite_show_free_string', false, $this ) ? __( 'Free!', 'woocommerce' ) : $price;
						$price       = apply_filters( 'woocommerce_composite_free_price_html', $free_string, $this );

					} else {

						if ( $price_min !== $price_max ) {
							$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-composite-products' ), wc_get_price_html_from_text(), $price . $this->get_price_suffix() );
						} else {
							$price = $price . $this->get_price_suffix();
						}

						$price = apply_filters( 'woocommerce_composite_price_html', $price, $this );
					}

				} else {

					$is_range = false;

					if ( $price_min !== $price_max ) {
						$price    = wc_format_price_range( $price_min, $price_max );
						$is_range = true;
					} else {
						$price = wc_price( $price_min );
					}

					$regular_price_min = $this->get_composite_regular_price( 'min', true );
					$regular_price_max = $this->get_composite_regular_price( 'max', true );

					if ( $regular_price_min !== $price_min || $regular_price_max > $price_max ) {

						if ( $regular_price_min !== $regular_price_max ) {
							$regular_price = wc_format_price_range( min( $regular_price_min, $regular_price_max ), max( $regular_price_min, $regular_price_max ) );
						} else {
							$regular_price = wc_price( $regular_price_min );
						}

						$price = apply_filters( 'woocommerce_composite_sale_price_html', wc_format_sale_price( $regular_price, $price ) . ( $is_range ? '' : $this->get_price_suffix() ), $this );

					} elseif ( 0.0 === $price_min && 0.0 === $price_max ) {

						$free_string = apply_filters( 'woocommerce_composite_show_free_string', false, $this ) ? __( 'Free!', 'woocommerce' ) : $price;
						$price       = apply_filters( 'woocommerce_composite_free_price_html', $free_string, $this );

					} else {
						$price = apply_filters( 'woocommerce_composite_price_html', $price . ( $is_range ? '' : $this->get_price_suffix() ), $this );
					}
				}
			}

			return apply_filters( 'woocommerce_get_price_html', $price, $this );

		} else {

			return parent::get_price_html();
		}
	}

	/**
	 * Prices incl. or excl. tax are calculated based on the bundled products prices, so get_price_suffix() must be overridden when individually-priced items exist.
	 *
	 * @return string
	 */
	public function get_price_suffix( $price = '', $qty = 1 ) {

		if ( $this->contains( 'priced_individually' ) ) {

			$price_suffix = '';

			if ( ( $suffix = get_option( 'woocommerce_price_display_suffix' ) ) && wc_tax_enabled() ) {

				$replacements = array(
					'{price_including_tax}' => wc_price( $this->get_composite_price_including_tax( 'min', $qty ) ),
					'{price_excluding_tax}' => wc_price( $this->get_composite_price_excluding_tax( 'min', $qty ) )
				);

				$price_suffix = str_replace( array_keys( $replacements ), array_values( $replacements ), ' <small class="woocommerce-price-suffix">' . wp_kses_post( $suffix ) . '</small>' );
			}

			/**
			 * 'woocommerce_get_price_suffix' filter.
			 *
			 * @param  string                $price_suffix
			 * @param  WC_Product_Composite  $this
			 * @param  mixed                 $price
			 * @param  int                   $qty
			 */
			return apply_filters( 'woocommerce_get_price_suffix', $price_suffix, $this, $price, $qty );

		} else {
			return parent::get_price_suffix();
		}
	}

	/**
	 * Gets price data array. Contains localized strings and price data passed to JS.
	 *
	 * @return array
	 */
	public function get_composite_price_data() {

		if ( empty( $this->composite_price_data ) ) {
			$this->load_price_data();
		}

		return $this->composite_price_data;
	}

	/**
	 * Wrapper for get_permalink that adds composite configuration data to the URL.
	 *
	 * @return string
	 */
	public function get_permalink() {

		$permalink             = get_permalink( $this->get_id() );
		$composite_config_data = false;
		$fn_args_count         = func_num_args();

		if ( 1 === $fn_args_count ) {

			$cart_item = func_get_arg( 0 );

			if ( isset( $cart_item[ 'composite_data' ] ) && is_array( $cart_item[ 'composite_data' ] ) ) {

				$composite_config_data = $cart_item[ 'composite_data' ];
				$args                  = array();

				foreach ( $composite_config_data as $component_id => $component_config_data ) {

					if ( isset( $component_config_data[ 'product_id' ] ) ) {
						$args[ 'wccp_component_selection' ][ $component_id ] = $component_config_data[ 'product_id' ];
					}

					if ( isset( $component_config_data[ 'quantity' ] ) ) {
						$args[ 'wccp_component_quantity' ][ $component_id ] = $component_config_data[ 'quantity' ];
					}

					if ( isset( $component_config_data[ 'variation_id' ] ) ) {
						$args[ 'wccp_variation_id' ][ $component_id ] = $component_config_data[ 'variation_id' ];
					}

					if ( isset( $component_config_data[ 'attributes' ] ) && is_array( $component_config_data[ 'attributes' ] ) ) {
						foreach ( $component_config_data[ 'attributes' ] as $tax => $val ) {
							$args[ 'wccp_' . $tax ][ $component_id ] = sanitize_title( $val );
						}
					}
				}

				if ( $this->is_editable_in_cart() ) {

					// Find the cart id we are updating.

					$cart_id = '';

					foreach ( WC()->cart->cart_contents as $item_key => $item_values ) {
						if ( wc_cp_is_composite_container_cart_item( $item_values ) && $item_values[ 'composite_data' ] === $cart_item[ 'composite_data' ] ) {
							$cart_id = $item_key;
						}
					}

					if ( $cart_id ) {
						$args[ 'update-composite' ] = $cart_id;
					}
				}

				$args = apply_filters( 'woocommerce_composite_cart_permalink_args', $args, $cart_item, $this );

				if ( ! empty( $args ) ) {
					$permalink = esc_url( add_query_arg( $args, $permalink ) );
				}
			}
		}

		return $permalink;
	}

	/**
	 * Generate component slugs based on component titles. Used to generate routes.
	 *
	 * @return array
	 */
	private function get_component_slugs() {

		$components = $this->get_components();
		$slugs      = array();

		if ( ! empty( $components ) ) {
			foreach ( $components as $component_id => $component ) {

				$sanitized_title = sanitize_title( $component->get_title( true ) );
				$component_slug  = $sanitized_title;
				$loop            = 0;

				while ( in_array( $component_slug, $slugs ) ) {
					$loop++;
					$component_slug = $sanitized_title . '-' . $loop;
				}

				$slugs[ $component_id ] = $component_slug;
			}

			$review_slug       = 'componentized' === $this->get_composite_layout_style_variation() ? __( 'configuration', 'woocommerce-composite-products' ) : __( 'review', 'woocommerce-composite-products' );
			$slugs[ 'review' ] = sanitize_title( $review_slug );
		}

		return $slugs;
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return  string
	 */
	public function add_to_cart_text() {

		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Select options', 'woocommerce' ) : __( 'Read More', 'woocommerce' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get the add to cart button text for the single page.
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {

		$text = __( 'Add to cart', 'woocommerce' );

		if ( isset( $_GET[ 'update-composite' ] ) ) {
			$updating_cart_key = wc_clean( $_GET[ 'update-composite' ] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				$text = __( 'Update Cart', 'woocommerce-composite-products' );
			}
		}

		return apply_filters( 'woocommerce_product_single_add_to_cart_text', $text, $this );
	}

	/**
	 * Get composite-specific add to cart form settings.
	 *
	 * @return  string
	 */
	public function add_to_cart_form_settings() {

		$image_data               = array();
		$pagination_data          = array();
		$placeholder_option       = array();
		$product_price_visibility = array();
		$subtotal_visibility      = array();
		$price_display_format     = array();

		$components = $this->get_components();

		if ( ! empty( $components ) ) {
			foreach ( $components as $component_id => $component ) {
				$image_data[ $component_id ]               = $component->get_image_data();
				$pagination_data[ $component_id ]          = $component->get_pagination_data();
				$placeholder_option[ $component_id ]       = $component->show_placeholder_option() ? 'yes' : 'no';
				$product_price_visibility[ $component_id ] = $component->hide_selected_option_price() ? 'no' : 'yes';
				$subtotal_visibility[ $component_id ]      = $component->is_subtotal_visible() ? 'yes' : 'no';
				$price_display_format[ $component_id ]     = $component->get_price_display_format();
			}
		}

		$settings = array(
			// Apply a sequential configuration process when using the 'componentized' layout.
			// When set to 'yes', a component can be configured only if all previous components have been configured.
			'sequential_componentized_progress'      => apply_filters( 'woocommerce_composite_sequential_comp_progress', 'no', $this ), /* yes | no */
			// Hide or disable the add-to-cart button if the composite has any components pending user input.
			'button_behaviour'                       => apply_filters( 'woocommerce_composite_button_behaviour', 'new', $this ), /* new | old */
			'layout'                                 => $this->get_composite_layout_style(),
			'layout_variation'                       => $this->get_composite_layout_style_variation(),
			'update_browser_history'                 => $this->get_composite_layout_style() !== 'single' ? 'yes' : 'no',
			'show_placeholder_option'                => $placeholder_option,
			'slugs'                                  => $this->get_component_slugs(),
			'image_data'                             => $image_data,
			'pagination_data'                        => $pagination_data,
			'selected_product_price_visibility_data' => $product_price_visibility,
			'subtotal_visibility_data'               => $subtotal_visibility,
			'price_display_format_data'              => $price_display_format,
			'component_qty_restore'                  => apply_filters( 'woocommerce_composite_component_qty_restore', 'yes', $this ) /* yes | no */
		);

		/**
		 * Filter composite-level JS app settings.
		 *
		 * @param  array                 $settings
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_add_to_cart_form_settings', $settings, $this );
	}

	/**
	 * Container of scenarios-related functionality - @see WC_CP_Scenarios_Manager.
	 *
	 * @param  string  $context
	 * @return WC_CP_Scenarios_Manager
	 */
	public function scenarios( $context = 'view' ) {

		$prop = 'scenarios_manager_' . $context;

		if ( ! isset( $this->$prop ) ) {
			$this->$prop = new WC_CP_Scenarios_Manager( $this, $context );
		}

		return $this->$prop;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the product object.
	*/

	/**
	 * Returns the base active price of the composite.
	 *
	 * @since  3.9.0
	 *
	 * @param  string $context
	 * @return mixed
	 */
	public function get_price( $context = 'view' ) {
		$value = $this->get_prop( 'price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) ? (double) $value : $value;
	}

	/**
	 * Returns the base regular price of the composite.
	 *
	 * @since  3.9.0
	 *
	 * @param  string $context
	 * @return mixed
	 */
	public function get_regular_price( $context = 'view' ) {
		$value = $this->get_prop( 'regular_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) ? (double) $value : $value;
	}

	/**
	 * Returns the base sale price of the composite.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $context
	 * @return mixed
	 */
	public function get_sale_price( $context = 'view' ) {
		$value = $this->get_prop( 'sale_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) && '' !== $value ? (double) $value : $value;
	}

	/**
	 * Catalog Price getter.
	 *
	 * @since  3.12.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_shop_price_calc( $context = 'any' ) {

		$value = $this->get_prop( 'shop_price_calc', $context );

		// Back compat with 'hide_price_html'.
		if ( has_filter( 'woocommerce_composite_hide_price_html' ) ) {
			_deprecated_function( 'The "woocommerce_composite_hide_price_html" filter', '3.12.0' );
			$is_hidden = apply_filters( 'woocommerce_composite_hide_price_html', 'hidden' === $value, $this );
			$value     = $is_hidden ? 'hidden' : $value;
		}

		return $value;
	}

	/**
	 * Form Location getter.
	 *
	 * @since  3.13.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_add_to_cart_form_location( $context = 'view' ) {
		return $this->get_prop( 'add_to_cart_form_location', $context );
	}

	/**
	 * Layout getter.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_layout( $context = 'any' ) {
		return $this->get_prop( 'layout', $context );
	}

	/**
	 * Editable-in-cart getter.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_editable_in_cart( $context = 'any' ) {
		return $this->get_prop( 'editable_in_cart', $context );
	}

	/**
	 * "Sold Individually" option context.
	 * Returns 'product' or 'configuration'.
	 *
	 * @since  3.6.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_sold_individually_context( $context = 'any' ) {
		return $this->get_prop( 'sold_individually_context', $context );
	}

	/**
	 * Minimum raw composite price getter.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_min_raw_price( $context = 'view' ) {
		if ( 'sync' !== $context ) {
			$this->sync();
		}
		$value = $this->get_prop( 'min_raw_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) && '' !== $value ? (double) $value : $value;
	}

	/**
	 * Maximum raw composite price getter.
	 * INF is 9999999999.0 in 'edit' (DB) context.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_max_raw_price( $context = 'view' ) {
		if ( 'sync' !== $context ) {
			$this->sync();
		}
		$value = $this->get_prop( 'max_raw_price', $context );
		$value = 'edit' !== $context && $this->contains( 'priced_individually' ) && '' !== $value && INF !== $value ? (double) $value : $value;
		$value = 'edit' === $context && INF === $value ? 9999999999.0 : $value;
		return $value;
	}

	/**
	 * Get data for all Components, indexed by component ID.
	 *
	 * @return array
	 */
	public function get_composite_data( $context = 'any' ) {

		$components = $this->get_components();

		if ( empty( $components ) ) {
			return false;
		}

		$composite_data = array();

		foreach ( $components as $component_id => $component ) {

			if ( 'rest' === $context ) {

				$thumbnail_id  = '';
				$thumbnail_src = '';

				if ( ! empty( $component[ 'thumbnail_id' ] ) ) {

					$thumbnail_id = absint( $component[ 'thumbnail_id' ] );
					$image        = wp_get_attachment_image_src( $thumbnail_id, 'full' );

					if ( $image ) {
						$thumbnail_src = $image[0];
					}
				}

				$composite_data[ $component_id ] = array(
					'id'                   => (string) $component->get_id(),
					'title'                => $component->get_title(),
					'description'          => $component->get_description(),
					'query_type'           => isset( $component[ 'query_type' ] ) ? $component[ 'query_type' ] : 'product_ids',
					'query_ids'            => 'category_ids' === $component[ 'query_type' ] ? (array) $component[ 'assigned_category_ids' ] : (array) $component[ 'assigned_ids' ],
					'default_option_id'    => $component->get_default_option(),
					'thumbnail_id'         => $thumbnail_id,
					'thumbnail_src'        => $thumbnail_src,
					'quantity_min'         => $component->get_quantity( 'min' ),
					'quantity_max'         => $component->get_quantity( 'max' ),
					'priced_individually'  => $component->is_priced_individually(),
					'shipped_individually' => $component->is_shipped_individually(),
					'optional'             => $component->is_optional(),
					'discount'             => $component->get_discount(),
					'options_style'        => $component->get_options_style(),
					'pagination_style'     => $component->get_pagination_style(),
					'display_prices'       => $component->get_price_display_format()
				);

			} else {
				$composite_data[ $component_id ] = $component->get_data();
			}
		}

		return $composite_data;
	}

	/**
	 * Get raw scenario data, indexed by scenario ID.
	 *
	 * @return array
	 */
	public function get_scenario_data( $context = 'view' ) {

		$scenario_meta = $this->scenario_meta;

		if ( empty( $scenario_meta ) ) {
			$scenario_meta = array();
		}

		if ( 'rest' === $context ) {

			$rest_api_scenario_meta = array();

			if ( ! empty( $scenario_meta ) ) {
				foreach ( $scenario_meta as $id => $data ) {

					$configuration = array();
					$actions       = array();

					if ( ! empty( $data[ 'component_data' ] ) && is_array( $data[ 'component_data' ] ) ) {
						foreach ( $data[ 'component_data' ] as $component_id => $component_data ) {
							$configuration[] = array(
								'component_id'      => strval( $component_id ),
								'component_options' => $component_data,
								'options_modifier'  => isset( $data[ 'modifier' ][ $component_id ] ) ? $data[ 'modifier' ][ $component_id ] : 'in'
							);
						}
					}

					if ( ! empty( $data[ 'scenario_actions' ] ) && is_array( $data[ 'scenario_actions' ] ) ) {
						foreach ( $data[ 'scenario_actions' ] as $action_id => $action_data ) {
							$actions[] = array(
								'action_id'   => strval( $action_id ),
								'is_active'   => isset( $action_data[ 'is_active' ] ) && 'yes' === $action_data[ 'is_active' ],
								'action_data' => array_diff_key( $action_data, array( 'is_active' => 1 ) )
							);
						}
					}

					$rest_api_scenario_meta[ $id ] = array(
						'id'            => (string) $id,
						'name'          => $data[ 'title' ],
						'description'   => $data[ 'description' ],
						'configuration' => $configuration,
						'actions'       => $actions
					);
				}
			}

			$scenario_meta = $rest_api_scenario_meta;
		}

		/**
		 * Filter raw scenario metadata.
		 *
		 * @param  array                 $scenario_meta
		 * @param  WC_Product_Composite  $product
		 */
		return 'view' === $context ? apply_filters( 'woocommerce_composite_scenario_meta', $scenario_meta, $this ) : $scenario_meta;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting product data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Shop price calc setter.
	 *
	 * @since  3.12.0
	 *
	 * @param  string  $value
	 */
	public function set_shop_price_calc( $value ) {
		$value = in_array( $value, array_keys( self::get_shop_price_calc_options() ) ) ? $value : 'defaults';
		$this->set_prop( 'shop_price_calc', $value );
	}

	/**
	 * Form Location setter.
	 *
	 * @since  3.13.0
	 *
	 * @param  string  $value
	 */
	public function set_add_to_cart_form_location( $value ) {
		$value = in_array( $value, array_keys( self::get_add_to_cart_form_location_options() ) ) ? $value : 'default';
		return $this->set_prop( 'add_to_cart_form_location', $value );
	}

	/**
	 * Layout setter.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $layout
	 */
	public function set_layout( $layout ) {
		$layout = self::get_layout_option( $layout );
		$this->set_prop( 'layout', $layout );
	}

	/**
	 * Edtiable-in-cart setter.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $editable_in_cart
	 */
	public function set_editable_in_cart( $editable_in_cart ) {

		$editable_in_cart = wc_string_to_bool( $editable_in_cart );
		$this->set_prop( 'editable_in_cart', $editable_in_cart );

		if ( $editable_in_cart ) {
			if ( ! in_array( 'edit_in_cart', $this->supports ) ) {
				$this->supports[] = 'edit_in_cart';
			}
		} else {
			foreach ( $this->supports as $key => $value ) {
				if ( 'edit_in_cart' === $value ) {
					unset( $this->supports[ $key ] );
				}
			}
		}
	}

	/**
	 * Sold-individually context setter.
	 *
	 * @since  3.9.0
	 *
	 * @param  string  $context
	 */
	public function set_sold_individually_context( $context ) {
		$context = in_array( $context, array( 'product', 'configuration' ) ) ? $context : 'product';
		$this->set_prop( 'sold_individually_context', $context );
	}

	/**
	 * Minimum raw composite price setter.
	 *
	 * @since  3.9.0
	 *
	 * @param  mixed  $value
	 */
	public function set_min_raw_price( $value ) {
		$value = wc_format_decimal( $value );
		$this->set_prop( 'min_raw_price', $value );
	}

	/**
	 * Maximum raw composite price setter.
	 * Convert 9999999999.0 to INF.
	 *
	 * @since  3.9.0
	 *
	 * @param  mixed  $value
	 */
	public function set_max_raw_price( $value ) {
		$value = INF !== $value ? wc_format_decimal( $value ) : INF;
		$value = 9999999999.0 === (double) $value ? INF : $value;
		$this->set_prop( 'max_raw_price', $value );
	}

	/**
	 * Set raw components data using internal schema.
	 *
	 * @internal
	 *
	 * @since 3.9.0
	 */
	public function set_composite_data( $data ) {

		$validated_data = array();

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $values ) {

				if ( empty( $values[ 'title' ] ) ) {
					continue;
				}

				if ( empty( $values[ 'query_type' ] ) ) {
					$values[ 'query_type' ] = 'product_ids';
				}

				if ( ( 'product_ids' === $values[ 'query_type' ] && empty( $values[ 'assigned_ids' ] ) ) || ( $values[ 'query_type' ] === 'category_ids' && empty( $values[ 'assigned_category_ids' ] ) ) ) {
					continue;
				}

				$validated_data[ $key ] = $values;

				if ( $this->get_object_read() ) {
					$this->composite_meta_save_pending = true;
				}
			}
		}

		$this->composite_meta = $validated_data;
		$this->load_defaults();
	}

	/**
	 * Set raw scenario data using internal schema.
	 *
	 * @internal
	 *
	 * @since 3.9.0
	 */
	public function set_scenario_data( $data ) {

		$validated_data = array();

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $values ) {

				if ( empty( $values[ 'title' ] ) ) {
					continue;
				}

				$validated_data[ $key ] = $values;
			}
		}

		$this->scenario_meta = $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Other CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Alias for 'set_props'.
	 *
	 * @since 3.9.0
	 */
	public function set( $properties ) {
		return $this->set_props( $properties );
	}

	/**
	 * Override 'save' to invalidate component runtime cache.
	 *
	 * @since 3.9.0
	 */
	public function save() {

		parent::save();

		// Save composite props that depend on components.
		$this->sync( true );

		if ( $this->composite_meta_save_pending ) {
			$this->composite_meta_save_pending = false;
			foreach ( $this->get_component_ids() as $component_id ) {
				WC_CP_Helpers::cache_delete( 'wc_cp_component_' . $component_id . '_' . $this->get_id() );
			}
		}

		return $this->get_id();
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Getter of composite 'contains' properties.
	 *
	 * @since  3.7.0
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function contains( $key ) {

		if ( 'priced_individually' === $key ) {

			if ( is_null( $this->contains[ $key ] ) ) {

				$this->contains[ 'priced_individually' ] = false;

				// Any components priced individually?
				$components = $this->get_components();

				if ( ! empty( $components ) ) {

					foreach ( $components as $component ) {
						if ( $component->is_priced_individually() ) {
							$this->contains[ 'priced_individually' ] = true;
						}
					}
				}
			}

		} elseif ( 'shipped_individually' === $key ) {

			if ( is_null( $this->contains[ $key ] ) ) {

				$this->contains[ 'shipped_individually' ] = false;

				// Any components shipped individually?
				$components = $this->get_components();

				if ( ! empty( $components ) ) {

					foreach ( $components as $component ) {
						if ( $component->is_shipped_individually() ) {
							$this->contains[ 'shipped_individually' ] = true;
						}
					}
				}
			}

		} else {
			$this->sync();
		}

		return isset( $this->contains[ $key ] ) ? $this->contains[ $key ] : null;
	}

	/**
	 * True if the composite is in sync with its contents.
	 *
	 * @return boolean
	 */
	public function is_synced() {
		return $this->is_synced;
	}

	/**
	 * Composite is a NYP product.
	 *
	 * @since  3.8.0
	 *
	 * @return boolean
	 */
	public function is_nyp() {

		if ( ! isset( $this->is_nyp ) ) {
			$this->is_nyp = WC_CP()->compatibility->is_nyp( $this );
		}

		return $this->is_nyp;
	}

	/**
	 * True if a one of the composited products has a component discount, or if there is a base sale price defined.
	 *
	 * @param  string  $context
	 * @return boolean
	 */
	public function is_on_sale( $context = 'view' ) {

		if ( 'update-price' !== $context && $this->contains( 'priced_individually' ) ) {
			$this->sync();
			$composite_on_sale = parent::is_on_sale( $context ) || ( $this->contains( 'discounted' ) && $this->get_composite_regular_price() > 0 );
		} else {
			$composite_on_sale = parent::is_on_sale( $context );
		}

		/**
		 * Filter composite on sale status.
		 *
		 * @param   boolean               $composite_on_sale
		 * @param   WC_Product_Composite  $this
		 */
		return 'view' === $context ? apply_filters( 'woocommerce_product_is_on_sale', $composite_on_sale, $this ) : $composite_on_sale;
	}

	/**
	 * Override purchasable method to account for empty price meta being allowed when individually-priced components exist.
	 *
	 * @return boolean
	 */
	public function is_purchasable() {

		$purchasable = true;

		// Products must exist of course.
		if ( ! $this->exists() ) {
			$purchasable = false;

		// When priced statically a price needs to be set.
		} elseif ( ! $this->contains( 'priced_individually' ) && '' === $this->get_price() ) {
			$purchasable = false;

		// Check the product is published.
		} elseif ( 'publish' !== $this->get_status() && ! current_user_can( 'edit_post', $this->get_id() ) ) {
			$purchasable = false;
		}

		/**
		 * Filter composite purchasable status.
		 *
		 * @param   boolean               $is_purchasable
		 * @param   WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
	}

	/**
	 * True if the composite is editable in cart.
	 *
	 * @return boolean
	 */
	public function is_editable_in_cart() {
		return $this->supports( 'edit_in_cart' );
	}

	/*
	|--------------------------------------------------------------------------
	| Layout.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Composite base layout.
	 *
	 * @return string
	 */
	public function get_composite_layout_style() {

		if ( isset( $this->base_layout ) ) {
			return $this->base_layout;
		}

		$composite_layout = $this->get_layout();
		$layout           = explode( '-', $composite_layout, 2 );

		$this->base_layout = $layout[0];

		return $this->base_layout;
	}

	/**
	 * Composite base layout variation.
	 *
	 * @return string
	 */
	public function get_composite_layout_style_variation() {

		if ( isset( $this->base_layout_variation ) ) {
			return $this->base_layout_variation;
		}

		$composite_layout = $this->get_layout();

		$layout = explode( '-', $composite_layout, 2 );

		if ( ! empty( $layout[1] ) ) {
			$this->base_layout_variation = $layout[1];
		} else {
			$this->base_layout_variation = 'standard';
		}

		return $this->base_layout_variation;
	}

	/*
	|--------------------------------------------------------------------------
	| Scenarios.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Build scenario data arrays for specific components, adapted to the data present in the current component options queries.
	 * Make sure this is always called after component options queries have run, otherwise component options queries will be populated with results for the initial composite state.
	 *
	 * @param  array    $component_ids
	 * @param  boolean  $use_current_query
	 * @return array
	 */
	public function get_current_scenario_data( $component_ids = array() ) {

		$component_options_subset = array();

		foreach ( $this->get_components() as $component_id => $component ) {

			if ( empty( $component_ids ) || in_array( $component_id, $component_ids ) ) {

				$current_component_options = $this->get_current_component_options( $component_id );
				$default_option            = $this->get_current_component_selection( $component_id );

				if ( $default_option && ! in_array( $default_option, $current_component_options ) ) {
					$current_component_options[] = $default_option;
				}

				$component_options_subset[ $component_id ] = $current_component_options;
			}
		}

		$this->composite_scenario_data = $this->scenarios()->get_data( $component_options_subset );

		return $this->composite_scenario_data;
	}

	/*
	|--------------------------------------------------------------------------
	| Component methods: Instantiation and data.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get component raw meta array by component id.
	 * All component data is currently lumped in a single meta field, which should hopefully change at some point.
	 *
	 * @param  string  $component_id
	 * @return array
	 */
	public function get_component_meta( $component_id ) {

		if ( ! isset( $this->composite_meta[ $component_id ] ) ) {
			return false;
		}

		return $this->composite_meta[ $component_id ];
	}

	/**
	 * Component object getter.
	 *
	 * @param  string  $component_id
	 * @return WC_CP_Component
	 */
	public function get_component( $component_id ) {

		$component = false;

		if ( $this->has_component( $component_id ) ) {

			$component = WC_CP_Helpers::cache_get( 'wc_cp_component_' . $component_id . '_' . $this->get_id() );

			if ( $this->composite_meta_save_pending || defined( 'WC_CP_DEBUG_RUNTIME_CACHE' ) || null === $component ) {
				$component = new WC_CP_Component( $component_id, $this );
				WC_CP_Helpers::cache_set( 'wc_cp_component_' . $component_id . '_' . $this->get_id(), $component );
			}
		}

		return $component;
	}

	/**
	 * Checks if a specific component ID exists.
	 *
	 * @param  string  $component_id
	 * @return boolean
	 */
	public function has_component( $component_id ) {

		$has_component = false;
		$component_ids = $this->get_component_ids();

		if ( in_array( $component_id, $component_ids ) ) {
			$has_component = true;
		}

		return $has_component;
	}

	/**
	 * Get all component ids.
	 *
	 * @return array
	 */
	public function get_component_ids() {
		return ! empty( $this->composite_meta ) ? array_keys( $this->composite_meta ) : array();
	}

	/**
	 * Gets all components.
	 *
	 * @return array
	 */
	public function get_components() {

		$components    = array();
		$component_ids = $this->get_component_ids();

		foreach ( $component_ids as $component_id ) {
			if ( $component = $this->get_component( $component_id ) ) {
				$components[ $component_id ] = $component;
			}
		}

		return $components;
	}

	/**
	 * Get component data array by component id.
	 *
	 * @param  string  $component_id
	 * @return array
	 */
	public function get_component_data( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_data() : false;
	}


	/*
	|--------------------------------------------------------------------------
	| Component methods: Options and properties.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get all component options (product IDs) available in a component.
	 *
	 * @param  string  $component_id
	 * @return array|null
	 */
	public function get_component_options( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_options() : null;
	}

	/**
	 * Get composited product.
	 *
	 * @param  string  $component_id
	 * @param  int     $product_id
	 * @return WC_CP_Product|null
	 */
	public function get_component_option( $component_id, $product_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_option( $product_id ) : null;
	}

	/**
	 * Grab component discount by component id.
	 *
	 * @param  string  $component_id
	 * @return string|null
	 */
	public function get_component_discount( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_discount() : null;
	}

	/**
	 * True if a component has only one option and is not optional.
	 *
	 * @param  string  $component_id
	 * @return boolean|null
	 */
	public function is_component_static( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->is_static() : null;
	}

	/**
	 * True if a component is optional.
	 *
	 * @param  string  $component_id
	 * @return boolean|null
	 */
	public function is_component_optional( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->is_optional() : null;
	}

	/**
	 * Get the default method to sort the options of a component.
	 *
	 * @param  int  $component_id
	 * @return string|null
	 */
	public function get_component_default_sorting_order( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_default_sorting_order() : null;
	}

	/**
	 * Get component sorting options, if enabled.
	 *
	 * @param  int  $component_id
	 * @return array|null
	 */
	public function get_component_sorting_options( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_sorting_options() : null;
	}

	/**
	 * Get component filtering options, if enabled.
	 *
	 * @param  int  $component_id
	 * @return array|null
	 */
	public function get_component_filtering_options( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_filtering_options() : null;
	}

	/*
	|--------------------------------------------------------------------------
	| Component methods: Templating.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Component options selection style.
	 *
	 * @param  string  $component_id
	 * @return string|null
	 */
	public function get_component_options_style( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_options_style() : null;
	}

	/**
	 * Thumbnail loop columns count.
	 *
	 * @param  string  $component_id
	 * @return int|null
	 */
	public function get_component_columns( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_columns() : null;
	}

	/**
	 * Thumbnail loop results per page.
	 *
	 * @param  string  $component_id
	 * @return int|null
	 */
	public function get_component_results_per_page( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_results_per_page() : null;
	}

	/**
	 * Controls whether component options loaded via ajax will be appended or paginated.
	 * When incompatible component options are set to be hidden, pagination cannot be used since results are filtered via js on the client side.
	 *
	 * @param  string  $component_id
	 * @return boolean
	 */
	public function paginate_component_options( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->paginate_options() : null;
	}

	/**
	 * Controls whether disabled component options will be hidden instead of greyed-out.
	 *
	 * @param  string  $component_id
	 * @return boolean|null
	 */
	public function hide_disabled_component_options( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->hide_disabled_options() : null;
	}

	/**
	 * Create an array of classes to use in the component layout templates.
	 *
	 * @param  string  $component_id
	 * @return array|null
	 */
	public function get_component_classes( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->get_classes() : null;
	}

	/*
	|--------------------------------------------------------------------------
	| Component methods: View state.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the current query object that was used to build the component options view of a component.
	 * Should be called after 'WC_CP_Component_View::get_options()' has been used to set its view state.
	 *
	 * @param  int  $component_id
	 * @return WC_CP_Query|null|false
	 */
	public function get_current_component_options_query( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->view->get_options_query() : null;
	}

	/**
	 * Get component options to display. Fetched using a WP Query wrapper to allow advanced component options filtering / ordering / pagination.
	 *
	 * @param  string $component_id
	 * @param  array  $args
	 * @return array|null
	 */
	public function get_current_component_options( $component_id, $args = array() ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->view->get_options( $args ) : null;
	}

	/**
	 * Get the currently selected option (product id) for a component.
	 *
	 * @since  3.6.0
	 *
	 * @param  string $component_id
	 * @return int
	 */
	public function get_current_component_selection( $component_id ) {
		return $this->has_component( $component_id ) ? $this->get_component( $component_id )->view->get_selected_option() : null;
	}

	/*
	|--------------------------------------------------------------------------
	| Static methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Supported types for use as Component Options.
	 *
	 * @return array
	 */
	public static function get_supported_component_option_types( $expanded = false ) {

		$supported_types = array( 'simple', 'variable', 'bundle' );

		if ( $expanded ) {
			$supported_types = array_merge( $supported_types, array( 'variation' ) );
		}

		return apply_filters( 'woocommerce_composite_products_supported_types', $supported_types, $expanded );
	}

	/**
	 * Get "Catalog Price" options.
	 *
	 * @return array
	 */
	public static function get_shop_price_calc_options() {

		$shop_price_calc_options = array(
			'defaults'      => array(
				'title'       => __( 'Use defaults', 'woocommerce-composite-products' ),
				'description' => __( 'Displays the price of the default configuration. Requires <strong>Default Option</strong> to be set in all non-optional Components.', 'woocommerce-composite-products' )
			),
			'min_max' => array(
				'title'       => __( 'Calculate from/to', 'woocommerce-composite-products' ),
				'description' => __( 'Builds a price string based on the configuration with the lowest/highest price.', 'woocommerce-composite-products' )
			),
			'hidden'       => array(
				'title'       => __( 'Hide', 'woocommerce-composite-products' ),
				'description' => __( 'Hides the catalog price.', 'woocommerce-composite-products' )
			)
		);

		return $shop_price_calc_options;
	}

	/**
	 * Get "Form Location" options.
	 *
	 * @since  3.13.0
	 *
	 * @return array
	 */
	public static function get_add_to_cart_form_location_options() {

		$options = array(
			'default'      => array(
				'title'       => __( 'Default', 'woocommerce-composite-products' ),
				'description' => __( 'The add-to-cart form is displayed inside the single-product summary.', 'woocommerce-composite-products' )
			),
			'after_summary' => array(
				'title'       => __( 'After summary', 'woocommerce-composite-products' ),
				'description' => __( 'The add-to-cart form is displayed after the single-product summary. Usually allocates the entire page width for displaying form content. Note that some themes may not support this option.', 'woocommerce-composite-products' )
			)
		);

		return apply_filters( 'woocommerce_composite_add_to_cart_form_location_options', $options );
	}

	/**
	 * Get "Layout" options.
	 *
	 * @return array
	 */
	public static function get_layout_options() {

		$sanitized_custom_layouts = array();

		$base_layouts = array(
			'single'      => array(
				'title'       => __( 'Stacked', 'woocommerce-composite-products' ),
				'description' => __( 'Components are stacked.', 'woocommerce-composite-products' ),
				'image_src'   => WC_CP()->plugin_url() . '/assets/images/single.png'
			),
			'progressive' => array(
				'title'       => __( 'Progressive', 'woocommerce-composite-products' ),
				'description' => __( 'Components are stacked, wrapped in toggle-boxes and configured in sequence.', 'woocommerce-composite-products' ),
				'image_src'   => WC_CP()->plugin_url() . '/assets/images/progressive.png'
			),
			'paged'       => array(
				'title'       => __( 'Stepped', 'woocommerce-composite-products' ),
				'description' => __( 'Components are viewed and configured step-by-step. The configuration is summarized in a final Review step.', 'woocommerce-composite-products' ),
				'image_src'   => WC_CP()->plugin_url() . '/assets/images/paged.png'
			)
		);

		$custom_layouts = array(
			'paged-componentized' => array(
				'title'       => __( 'Componentized', 'woocommerce-composite-products' ),
				'description' => __( 'A variation of the Stepped layout that starts with a configuration Summary. Components can be configured in any sequence.', 'woocommerce-composite-products' ),
				'image_src'   => WC_CP()->plugin_url() . '/assets/images/paged-componentized.png'
			)
		);

		/**
		 * Filter layout variations array to add custom layout variations.
		 *
		 * @param  array  $custom_layouts
		 */
		$custom_layouts = apply_filters( 'woocommerce_composite_product_layout_variations', $custom_layouts );

		foreach ( $custom_layouts as $layout_id => $layout_data ) {

			$sanitized_layout_id = esc_attr( sanitize_title( $layout_id ) );

			if ( array_key_exists( $sanitized_layout_id, $base_layouts ) ) {
				continue;
			}

			$sanitized_layout_id_parts = explode( '-', $sanitized_layout_id, 2 );

			if ( ! empty( $sanitized_layout_id_parts[0] ) && array_key_exists( $sanitized_layout_id_parts[0], $base_layouts ) ) {
				$sanitized_custom_layouts[ $sanitized_layout_id ] = $layout_data;
			}
		}

		return array_merge( $base_layouts, $sanitized_custom_layouts );
	}

	/**
	 * Get composite layout descriptions.
	 *
	 * @param  string  $layout_id
	 * @return string
	 */
	public static function get_layout_description( $layout_id ) {

		$layout_descriptions = wp_list_pluck( self::get_layout_options(), 'description' );

		return isset( $layout_descriptions[ $layout_id ] ) ? $layout_descriptions[ $layout_id ] : '';
	}

	/**
	 * Get selected layout option.
	 *
	 * @param  string  $layout
	 * @return string
	 */
	public static function get_layout_option( $layout ) {

		if ( ! $layout ) {
			return 'single';
		}

		$layouts         = self::get_layout_options();
		$layout_id_parts = explode( '-', $layout, 2 );

		if ( array_key_exists( $layout, $layouts ) ) {
			return $layout;
		} elseif ( array_key_exists( $layout_id_parts[0], $layouts ) ) {
			return $layout_id_parts[0];
		}

		return 'single';
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Bypass pricing calculations.
	 *
	 * @return boolean
	 */
	public function hide_price_html() {
		_deprecated_function( __METHOD__ . '()', '3.12.0', __CLASS__ . '::get_shop_price_calc()' );
		return 'hidden' === $this->get_shop_price_calc( $context );
	}
	public function get_hide_shop_price( $context = 'any' ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', __CLASS__ . '::get_shop_price_calc()' );
		return 'hidden' === $this->get_shop_price_calc( $context );
	}
	public function set_hide_shop_price( $value ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', __CLASS__ . '::set_shop_price_calc()' );
		return $this->set_shop_price_calc( 'hidden' );
	}
	public function maybe_sync_composite() {
		_deprecated_function( __METHOD__ . '()', '3.12.0', __CLASS__ . '::sync()' );
		$this->sync();
	}
	public function sync_composite() {
		_deprecated_function( __METHOD__ . '()', '3.12.0', __CLASS__ . '::sync( true )' );
		$this->sync( true );
	}
	public function get_scenario_meta() {
		_deprecated_function( __METHOD__ . '()', '3.9.0', __CLASS__ . '::get_scenario_data()' );
		return $this->get_scenario_data();
	}
	public function get_base_price() {
		_deprecated_function( __METHOD__ . '()', '3.8.0', __CLASS__ . '::get_price()' );
		return $this->get_price( 'edit' );
	}
	public function get_base_regular_price() {
		_deprecated_function( __METHOD__ . '()', '3.8.0', __CLASS__ . '::get_regular_price()' );
		return $this->get_regular_price( 'edit' );
	}
	public function get_base_sale_price() {
		_deprecated_function( __METHOD__ . '()', '3.8.0', __CLASS__ . '::get_sale_price()' );
		return $this->get_sale_price( 'edit' );
	}
	public function is_shipped_per_product() {
		_deprecated_function( __METHOD__ . '()', '3.7.0', __CLASS__ . '::contains()' );
		return $this->contains( 'shipped_individually' );
	}
	public function is_priced_per_product() {
		_deprecated_function( __METHOD__ . '()', '3.7.0', __CLASS__ . '::contains()' );
		return $this->contains( 'priced_individually' );
	}
	public function get_component_ordering_options( $component_id ) {
		_deprecated_function( __METHOD__ . '()', '3.7.0', __CLASS__ . '::get_component_sorting_options()' );
		return $this->get_component_sorting_options( $component_id );
	}
	public function get_component_default_ordering_option( $component_id ) {
		_deprecated_function( __METHOD__ . '()', '3.7.0', __CLASS__ . '::get_component_default_sorting_order()' );
		return $this->get_component_default_sorting_order( $component_id );
	}
	public function get_composited_product( $component_id, $product_id ) {
		_deprecated_function( __METHOD__ . '()', '3.7.0', __CLASS__ . '::get_component_option()' );
		return $this->get_component_option( $component_id, $product_id );
	}
	public function get_composite_selections_style() {
		_deprecated_function( __METHOD__ . '()', '3.6.0', __CLASS__ . '::get_component_options_style()' );

		$selections_style = $this->bto_selection_mode;

		if ( empty( $selections_style ) ) {
			$selections_style = 'dropdowns';
		}

		return $selections_style;
	}
	public function get_component_default_option( $component_id ) {
		_deprecated_function( __METHOD__ . '()', '3.6.0', __CLASS__ . '::get_current_component_selection()' );
		return $this->get_current_component_selection( $component_id );
	}
	public function get_current_component_scenarios( $component_id, $current_component_options ) {
		_deprecated_function( __METHOD__ . '()', '3.6.0', __CLASS__ . '::get_current_scenario_data()' );
		return $this->get_current_scenario_data( array( $component_id ) );
	}
	public function get_composite_scenario_data() {
		_deprecated_function( __METHOD__ . '()', '3.6.0', __CLASS__ . '::get_current_scenario_data()' );
		return $this->get_current_scenario_data();
	}
}