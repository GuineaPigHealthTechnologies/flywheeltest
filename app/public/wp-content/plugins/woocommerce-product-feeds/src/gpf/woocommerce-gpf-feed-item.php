<?php

class WoocommerceGpfFeedItem {

	/**
	 * The specific WC_Product that this item represents.
	 *
	 * @var WC_Product|WC_Product_Variation
	 */
	private $specific_product;

	/**
	 * The "general" WC_Product. Either the product, or its parent in the case of a variation.
	 *
	 * @var WC_Product
	 */
	private $general_product;

	/**
	 * The feed format that the item is being prepared for.
	 *
	 * @var string
	 */
	private $feed_format;

	/**
	 * Image style to be used when generated the image URLs.
	 *
	 * Override by using the filter 'woocommerce_gpf_image_style'
	 *
	 * @var string
	 */
	private $image_style = 'full';

	/**
	 * Which description to use, "full" or "short".
	 *
	 * @var string
	 */
	private $description_type = 'full';

	/**
	 * Unit of measurement to use when generating shipping height/width/length.
	 *
	 * Override by using the filter 'woocommerce_gpf_shipping_dimension_unit'.
	 * Valid values are 'in', or 'cm'.
	 *
	 * @var string
	 */
	private $shipping_dimension_unit = 'cm';

	/**
	 * Whether this item represents a variation.
	 *
	 * @var boolean
	 */
	private $is_variation;

	/**
	 * The specific ID represented by this item.
	 *
	 * For variations, this will be the variation ID. For simple products, it
	 * will be the product ID.
	 *
	 * @var int
	 */
	private $specific_id;

	/**
	 * The post ID of the most general product represented by this item.
	 *
	 * For variations, this will be the parent product ID. For simple products,
	 * it will be the product ID.
	 *
	 * @var int
	 */
	private $general_id;

	/**
	 * Required instance of WoocommerceGpfCommon.
	 *
	 * @var WoocommerceGpfCommon
	 */
	private $woocommerce_gpf_common;

	/**
	 * Additional elements that apply to this item.
	 *
	 * @var array
	 */
	public $additional_elements;

	/**
	 * The ID of the feed item.
	 *
	 * @var string
	 */
	public $ID;

	/**
	 * The GUID of the feed item.
	 *
	 * @var string
	 */
	public $guid;

	/**
	 * The title of the item.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The item_group_id of the feed item.
	 *
	 * @var string
	 */
	public $item_group_id;

	/**
	 * The description of the feed item.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The image link for this feed item.
	 *
	 * @var string
	 */
	public $image_link;

	/**
	 * Array of additional images that apply to this feed item.
	 *
	 * @var array
	 */
	public $additional_images;

	/**
	 * The purchase link for this feed item.
	 *
	 * @var string
	 */
	public $purchase_link;

	/**
	 * The sale price exclusive of taxes.
	 *
	 * @var string
	 */
	public $sale_price_ex_tax;

	/**
	 * The sale price including taxes.
	 *
	 * @var string
	 */
	public $sale_price_inc_tax;

	/**
	 * The regular price exclusive of taxes.
	 *
	 * @var string
	 */
	public $regular_price_ex_tax;

	/**
	 * The regular price including taxes.
	 *
	 * @var string
	 */
	public $regular_price_inc_tax;

	/**
	 * The start date that the sale price applies.
	 *
	 * @var string
	 */
	public $sale_price_start_date;

	/**
	 * The end date that the sale price applies.
	 *
	 * @var string
	 */
	public $sale_price_end_date;

	/**
	 * The current price of the item excluding taxes.
	 *
	 * @var string
	 */
	public $price_ex_tax;

	/**
	 * The current price of the item including taxes.
	 *
	 * @var string
	 */
	public $price_inc_tax;

	/**
	 * The SKU of the feed item.
	 *
	 * @var string
	 */
	public $sku;

	/**
	 * The shipping weight of the item.
	 *
	 * @var float
	 */
	public $shipping_weight;

	/**
	 * Whether the product is in stock.
	 *
	 * @var bool
	 */
	public $is_in_stock;

	/**
	 * Constructor.
	 *
	 * Store dependencies.
	 *
	 * @param WC_Product $specific_product       The specific product being output.
	 * @param WC_Product $general_product        The "general" product being processed.
	 * @param string     $feed_format            The feed format being output.
	 * @param [type]     $woocommerce_gpf_common The WoocommerceGpfCommon instance.
	 */
	public function __construct( WC_Product $specific_product, WC_Product $general_product, $feed_format = 'all', $woocommerce_gpf_common ) {
		$this->specific_product        = $specific_product;
		$this->general_product         = $general_product;
		$this->feed_format             = $feed_format;
		$this->woocommerce_gpf_common  = $woocommerce_gpf_common;
		$this->additional_images       = array();
		$this->image_style             = apply_filters(
			'woocommerce_gpf_image_style',
			$this->image_style
		);
		$this->description_type        = apply_filters(
			'woocommerce_gpf_description_type',
			$this->description_type
		);
		$this->shipping_dimension_unit = apply_filters(
			'woocommerce_gpf_shipping_dimension_unit',
			'cm'
		);
		$this->is_variation            = $this->specific_product instanceof WC_Product_Variation;
		if ( is_callable( array( $this->specific_product, 'get_id' ) ) ) {
			// WC > 2.7.0
			$this->specific_id = $this->specific_product->get_id();
			$this->general_id  = $this->general_product->get_id();
		} else {
			// WC < 2.7.0
			$this->specific_id = $this->specific_product->variation_id;
			$this->general_id  = $this->general_product->id;
		}
		$this->build_item();
	}

	/**
	 * Work out if a WC Product should be excluded from the feed.
	 *
	 * @param WC_Product|WC_Product_Variation $wc_product   The product to check.
	 * @param string                          $feed_format  The feed being produced.
	 *
	 * @return bool True if the product should be excluded. False otherwise.
	 */
	public static function should_exclude( $wc_product, $feed_format ) {
		$excluded   = false;
		$visibility = is_callable( array( $wc_product, 'get_catalog_visibility' ) ) ?
					  $wc_product->get_catalog_visibility() :
					  $wc_product->visibility;
		// Check to see if the product is set as Hidden within WooCommerce.
		if ( 'hidden' === $visibility ) {
			$excluded = true;
		}
		// Check to see if the product has been excluded in the feed config.
		if ( is_callable( array( $wc_product, 'get_meta' ) ) ) {
			// WC > 2.7.0.
			$gpf_data = get_post_meta( $wc_product->get_id(), '_woocommerce_gpf_data', true );
		} else {
			// WC < 2.7.0.
			if ( $gpf_data = $wc_product->woocommerce_gpf_data ) {
				$gpf_data = maybe_unserialize( $gpf_data );
			} else {
				$gpf_data = array();
			}
		}
		if ( ! empty( $gpf_data ) ) {
			$gpf_data = maybe_unserialize( $gpf_data );
		}
		if ( ! empty( $gpf_data['exclude_product'] ) ) {
			$excluded = true;
		}
		if ( $wc_product instanceof WC_Product_Variation ) {
			if ( is_callable( array( $wc_product, 'get_parent_id' ) ) ) {
				// WC > 2.7.0
				$id = $wc_product->get_parent_id();
			} else {
				// WC < 2.7.0
				$id = $wc_product->id;
			}
		} else {
			if ( is_callable( array( $wc_product, 'get_id' ) ) ) {
				// WC > 2.7.0
				$id = $wc_product->get_id();
			} else {
				// WC < 2.7.0
				$id = $wc_product->id;
			}
		}
		return apply_filters( 'woocommerce_gpf_exclude_product', $excluded, $id, $feed_format );
	}

	/**
	 * Work out if a feed item should be excluded from the feed based on.
	 *
	 * @return bool  True if the product should be excluded. False otherwise.
	 */
	public function is_excluded() {
		return self::should_exclude( $this->specific_product, $this->feed_format );
	}

	/**
	 * Allow private properties to be accessed read-only.
	 */
	public function __get( $key ) {
		if ( isset( $this->$key ) ) {
			return $this->$key;
		}
		return null;
	}

	/**
	 * Calculates the data for the feed item.
	 */
	private function build_item() {

		// Calculate the various prices we need.
		$this->get_product_prices();

		// Get main item information
		$this->ID            = $this->specific_id;
		$this->guid          = 'woocommerce_gpf_' . $this->ID;
		$this->item_group_id = 'woocommerce_gpf_' . $this->general_id;
		$this->title         = $this->specific_product->get_title();
		if ( $this->is_variation ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.9', '>' ) ) {
				// WC >= 3.0.0
				$include_labels = apply_filters( 'woocommerce_gpf_include_attribute_labels_in_title', true );
				$suffix         = wc_get_formatted_variation( $this->specific_product, true, $include_labels );
			} else {
				// WC < 3.0.0
				$suffix = $this->specific_product->get_formatted_variation_attributes( true );
			}
			if ( ! empty( $suffix ) ) {
				$this->title .= ' (' . $suffix . ')';
			}
		}
		// Include item_group_id.
		$this->title       = apply_filters(
			'woocommerce_gpf_title',
			$this->title,
			$this->specific_id
		);
		$this->description = $this->get_product_description();
		$this->description = apply_filters(
			'woocommerce_gpf_description',
			$this->description,
			$this->general_id,
			$this->is_variation ? $this->specific_id : null
		);

		$this->image_link = $this->get_the_post_thumbnail_src( $this->ID, $this->image_style );
		if ( $this->is_variation && empty( $this->image_link ) ) {
			$this->image_link = $this->get_the_post_thumbnail_src( $this->general_id, $this->image_style );
		}
		$this->purchase_link       = $this->specific_product->get_permalink();
		$this->is_in_stock         = $this->specific_product->is_in_stock();
		$this->sku                 = $this->specific_product->get_sku();
		$this->shipping_weight     = apply_filters(
			'woocommerce_gpf_shipping_weight',
			$this->specific_product->get_weight(),
			$this->ID
		);
		$this->additional_elements = array();

		// Add other elements.
		$this->general_elements();

		$this->get_additional_images();
		if ( 'google' === $this->feed_format ) {
			$this->shipping_height_elements();
			$this->shipping_width_elements();
			$this->shipping_length_elements();
			$this->all_or_nothing_shipping_elements();
		}
		$this->force_stock_status();

		// General, or feed-specific items
		$this->additional_elements = apply_filters( 'woocommerce_gpf_elements', $this->additional_elements, $this->general_id, ( $this->specific_id !== $this->general_id ) ? $this->specific_id : null );
		$this->additional_elements = apply_filters( 'woocommerce_gpf_elements_' . $this->feed_format, $this->additional_elements, $this->general_id, ( $this->specific_id !== $this->general_id ) ? $this->specific_id : null );
	}

	/**
	 * Legacy code for fetching a product description.
	 *
	 * For WooCommerce < 3.0.
	 *
	 * @return string   The product description.
	 */
	private function get_product_description_legacy() {
		$description = null;
		$description = $this->specific_product->post->post_content;
		if ( empty( $description ) ) {
			$description = apply_filters(
				'the_content',
				$description
			);
		}
		return $description;
	}

	/**
	 * Get the description for a product.
	 *
	 * @return string  The product description.
	 */
	private function get_product_description() {
		$description = null;
		if ( ! is_callable( array( $this->specific_product, 'get_description' ) ) ) {
			// WC < 2.7.0
			$description = $this->get_product_description_legacy();
		} else {
			// WC >= 2.7.0
			if ( $this->is_variation ) {
				// Use the variation description if possible
				if ( 'short' === $this->description_type ) {
					$description = $this->specific_product->get_short_description();
				} else {
					$description = $this->specific_product->get_description();
				}
				// Use the main product description if there was no variation specific
				// description.
				if ( empty( $description ) ) {
					$parent_id = $this->specific_product->get_parent_id();
					$parent    = wc_get_product( $parent_id );
					if ( 'short' === $this->description_type ) {
						$description = $parent->get_short_description();
					} else {
						$description = $parent->get_description();
					}
				}
			} else {
				if ( 'short' === $this->description_type ) {
					$description = $this->specific_product->get_short_description();
				} else {
					$description = $this->specific_product->get_description();
				}
			}
		}
		return apply_filters(
			'the_content',
			$description
		);
	}

	/**
	 * Determines the lowest price (inc & ex. VAT) for a product, taking into
	 * account its child products as well as the main product price.
	 */
	private function get_product_prices() {
		// Grab the price of the main product.
		$prices = $this->generate_prices_for_product();
		// Adjust the price if there are cheaper child products.
		$prices = $this->adjust_prices_for_children( $prices );
		// Set the prices into the object.
		$this->sale_price_ex_tax     = $prices['sale_price_ex_tax'];
		$this->sale_price_inc_tax    = $prices['sale_price_inc_tax'];
		$this->regular_price_ex_tax  = $prices['regular_price_ex_tax'];
		$this->regular_price_inc_tax = $prices['regular_price_inc_tax'];
		$this->sale_price_start_date = $prices['sale_price_start_date'];
		$this->sale_price_end_date   = $prices['sale_price_end_date'];
		$this->price_ex_tax          = $prices['price_ex_tax'];
		$this->price_inc_tax         = $prices['price_inc_tax'];
	}

	/**
	 * Generates the inc, and ex. tax prices for both the regular, and sale
	 * price for a specific product, and returns them.
	 *
	 * @param WC_Product $product  Optional product to use. If not provided then
	 *                             $this->specific_product is used.
	 */
	private function generate_prices_for_product( $product = null ) {
		// Default to the current product if none passed in.
		if ( is_null( $product ) ) {
			$product = $this->specific_product;
		}
		// Initialise defaults.
		$prices                          = array();
		$prices['sale_price_ex_tax']     = null;
		$prices['sale_price_inc_tax']    = null;
		$prices['regular_price_ex_tax']  = null;
		$prices['regular_price_inc_tax'] = null;
		$prices['sale_price_start_date'] = null;
		$prices['sale_price_end_date']   = null;
		$prices['price_ex_tax']          = null;
		$prices['price_inc_tax']         = null;

		// Find out the product type.
		if ( is_callable( array( $product, 'get_type' ) ) ) {
			$product_type = $product->get_type();
		} else {
			$product_type = $product->product_type;
		}

		if ( 'variable' === $product_type ) {
			// Variable products shouldn't have prices. Works around issue in WC
			// core : https://github.com/woocommerce/woocommerce/issues/16145
			return $prices;
		} elseif ( 'bundle' === $product_type ) {
			// Bundle products require their own logic.
			return $this->generate_prices_for_bundle_product( $product, $prices );
		} elseif ( 'composite' === $product_type ) {
			return $this->generate_prices_for_composite_product( $product, $prices );
		}

		// Grab the regular price of the base product.
		$regular_price = $product->get_regular_price();
		if ( '' !== $regular_price ) {
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$prices['regular_price_ex_tax']  = wc_get_price_excluding_tax( $product, array( 'price' => $regular_price ) );
				$prices['regular_price_inc_tax'] = wc_get_price_including_tax( $product, array( 'price' => $regular_price ) );
			} else {
				$prices['regular_price_ex_tax']  = $product->get_price_excluding_tax( 1, $regular_price );
				$prices['regular_price_inc_tax'] = $product->get_price_including_tax( 1, $regular_price );
			}
		}

		// Grab the sale price of the base product. Some plugins (Dyanmic
		// pricing as an example) filter the active price, but not the sale
		// price. If the active price < the regular price treat it as a sale
		// price.
		$sale_price   = $product->get_sale_price();
		$active_price = $product->get_price();
		if ( ( empty( $sale_price ) && $active_price < $regular_price ) ||
		   ( ! empty( $sale_price ) && $active_price < $sale_price ) ) {
			$sale_price = $active_price;
		}
		if ( '' !== $sale_price ) {
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$prices['sale_price_ex_tax']  = wc_get_price_excluding_tax( $product, array( 'price' => $sale_price ) );
				$prices['sale_price_inc_tax'] = wc_get_price_including_tax( $product, array( 'price' => $sale_price ) );
			} else {
				$prices['sale_price_ex_tax']  = $product->get_price_excluding_tax( 1, $sale_price );
				$prices['sale_price_inc_tax'] = $product->get_price_including_tax( 1, $sale_price );
			}
			if ( is_callable( array( $product, 'get_date_on_sale_from' ) ) ) {
				// WC > 2.7.0
				$prices['sale_price_start_date'] = $product->get_date_on_sale_from();
				$prices['sale_price_end_date']   = $product->get_date_on_sale_to();
			} else {
				// WC < 2.7.0
				$prices['sale_price_start_date'] = $product->sale_price_dates_from;
				$prices['sale_price_end_date']   = $product->sale_price_dates_to;
			}
		}

		// Populate a "price", using the sale price if there is one, the actual price if not.
		if ( null !== $prices['sale_price_ex_tax'] ) {
			$prices['price_ex_tax']  = $prices['sale_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['sale_price_inc_tax'];
		} else {
			$prices['price_ex_tax']  = $prices['regular_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['regular_price_inc_tax'];
		}
		return $prices;
	}

	private function generate_prices_for_bundle_product( $product, $prices ) {
		// Use tax-specific functions if available.
		if ( is_callable( array( $product, 'get_bundle_regular_price_including_tax' ) ) ) {
			$prices['regular_price_ex_tax']  = $product->get_bundle_regular_price_excluding_tax();
			$prices['regular_price_inc_tax'] = $product->get_bundle_regular_price_including_tax();
			$current_price_ex_tax            = $product->get_bundle_price_excluding_tax();
			if ( $current_price_ex_tax < $prices['regular_price_ex_tax'] ) {
				$prices['sale_price_ex_tax']  = $product->get_bundle_price_excluding_tax();
				$prices['sale_price_inc_tax'] = $product->get_bundle_price_including_tax();
			}
		} else {
			// Just take the current price as the regular price since its
			// the only one we can reliably get.
			$prices['regular_price_ex_tax']  = $product->get_bundle_price_excluding_tax();
			$prices['regular_price_inc_tax'] = $product->get_bundle_price_including_tax();
		}
		// Populate a "price", using the sale price if there is one, the actual price if not.
		if ( null !== $prices['sale_price_ex_tax'] ) {
			$prices['price_ex_tax']  = $prices['sale_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['sale_price_inc_tax'];
		} else {
			$prices['price_ex_tax']  = $prices['regular_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['regular_price_inc_tax'];
		}
		return $prices;
	}

	private function generate_prices_for_composite_product( $product, $prices ) {
		// Use tax-specific functions if available.
		if ( is_callable( array( $product, 'get_composite_regular_price_including_tax' ) ) ) {
			$prices['regular_price_ex_tax']  = $product->get_composite_regular_price_excluding_tax();
			$prices['regular_price_inc_tax'] = $product->get_composite_regular_price_including_tax();
			$current_price_ex_tax            = $product->get_composite_price_excluding_tax();
			if ( $current_price_ex_tax < $prices['regular_price_ex_tax'] ) {
				$prices['sale_price_ex_tax']  = $product->get_composite_price_excluding_tax();
				$prices['sale_price_inc_tax'] = $product->get_composite_price_including_tax();
			}
		} else {
			// Just take the current price as the regular price since its
			// the only one we can reliably get.
			$prices['regular_price_ex_tax']  = $product->get_composite_price_excluding_tax();
			$prices['regular_price_inc_tax'] = $product->get_composite_price_including_tax();
		}
		// Populate a "price", using the sale price if there is one, the actual price if not.
		if ( null !== $prices['sale_price_ex_tax'] ) {
			$prices['price_ex_tax']  = $prices['sale_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['sale_price_inc_tax'];
		} else {
			$prices['price_ex_tax']  = $prices['regular_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['regular_price_inc_tax'];
		}
		return $prices;
	}

	/**
	 * Adjusts the prices of the feed item according to child products.
	 */
	private function adjust_prices_for_children( $current_prices ) {
		if ( ! $this->specific_product->has_child() ) {
			return $current_prices;
		}
		$children = $this->specific_product->get_children();
		foreach ( $children as $child ) {
			$child_product = wc_get_product( $child );
			if ( ! $child_product ) {
				continue;
			}
			if ( is_callable( array( $child_product, 'get_type' ) ) ) {
				$product_type = $child_product->get_type();
			} else {
				$product_type = $child_product->product_type;
			}
			if ( 'variation' === $product_type ) {
				$child_is_visible = $this->variation_is_visible( $child_product );
			} else {
				$child_is_visible = $child_product->is_visible();
			}
			if ( ! $child_is_visible ) {
				continue;
			}
			$child_prices = $this->generate_prices_for_product( $child_product );
			if ( ( 0 == $current_prices['price_inc_tax'] ) && ( $child_prices['price_inc_tax'] > 0 ) ) {
				$current_prices = $child_prices;
			} elseif ( ( $child_prices['price_inc_tax'] > 0 ) && ( $child_prices['price_inc_tax'] < $current_prices['price_inc_tax'] ) ) {
				$current_prices = $child_prices;
			}
		}
		return $current_prices;
	}

	/**
	 * Helper function for WooCommerce v2.0.x
	 * Checks if a variation is visible or not.
	 */
	private function variation_is_visible( $variation ) {
		if ( method_exists( $variation, 'variation_is_visible' ) ) {
			return $variation->variation_is_visible();
		}
		$visible = true;
		if ( 'publish' !== get_post_status( $variation->variation_id ) ) {
			// Published == enabled checkbox
			$visible = false;
		} elseif ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) {
			// Out of stock visibility
			$visible = false;
		} elseif ( $variation->get_price() === '' ) {
			// Price not set.
			$visible = false;
		}
		return $visible;
	}

	/**
	 * Retrieve Post Thumbnail URL
	 *
	 * @param int     $post_id (optional) Optional. Post ID.
	 * @param string  $size    (optional) Optional. Image size.  Defaults to 'post-thumbnail'.
	 * @return string|bool Image src, or false if the post does not have a thumbnail.
	 */
	private function get_the_post_thumbnail_src( $post_id = null, $size = 'post-thumbnail' ) {
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( ! $post_thumbnail_id ) {
			return false;
		}
		list( $src ) = wp_get_attachment_image_src( $post_thumbnail_id, $size, false );
		return $src;
	}

	/**
	 * Add the "advanced" information to the field based on either the
	 * per-product settings, category settings, or store defaults.
	 *
	 * @access private
	 */
	private function general_elements() {
		$elements       = array();
		$product_values = $this->calculate_values_for_product();
		if ( ! empty( $product_values ) ) {
			foreach ( $product_values as $key => $value ) {
				// Deal with fields that can have multiple, comma separated values
				if ( isset( $this->woocommerce_gpf_common->product_fields[ $key ]['multiple'] ) && $this->woocommerce_gpf_common->product_fields[ $key ]['multiple'] && ! is_array( $value ) ) {
					$value = explode( ',', $value );
				}
				$elements[ $key ] = (array) $value;
			}
		}
		$this->additional_elements = $elements;
	}

	/**
	 * Retrieve a measurement for a product in inches.
	 *
	 * @param  int     $product_id  The product ID to retrieve the measurement for.
	 * @param  string  $dimension   The dimension to retrieve. "length", "width" or "height"
	 * @return float                The requested dimension for the given product.
	 */
	private function get_shipping_dimension( $dimension ) {
		if ( 'width' !== $dimension &&
			 'length' !== $dimension &&
			 'height' !== $dimension ) {
				 return null;
		}
		$function = 'get_' . $dimension;
		if ( is_callable( array( $this->specific_product, $function ) ) ) {
			$measurement = $this->specific_product->{$function}();
		} else {
			$measurement = $this->specific_product->$dimension;
		}
		if ( empty( $measurement ) ) {
			return null;
		}
		$measurement = wc_get_dimension( $measurement, $this->shipping_dimension_unit );
		return $measurement;
	}

	/**
	 * Add shipping_length to the elements array if the product has a length
	 * configured.
	 */
	private function shipping_length_elements() {
		$length = $this->get_shipping_dimension( 'length' );
		if ( empty( $length ) ) {
			return;
		}
		$this->additional_elements['shipping_length'] = array( (int) ceil( $length ) . ' ' . $this->shipping_dimension_unit );
	}

	/**
	 * Add shipping_width to the elements array if the product has a width
	 * configured.
	 */
	private function shipping_width_elements() {
		$width = $this->get_shipping_dimension( 'width' );
		if ( empty( $width ) ) {
			return;
		}
		$this->additional_elements['shipping_width'] = array( (int) ceil( $width ) . ' ' . $this->shipping_dimension_unit );
	}

	/**
	 * Add shipping_height to the elements array if the product has a height
	 * configured.
	 */
	private function shipping_height_elements() {
		$height = $this->get_shipping_dimension( 'height' );
		if ( empty( $height ) ) {
			return;
		}
		$this->additional_elements['shipping_height'] = array( (int) ceil( $height ) . ' ' . $this->shipping_dimension_unit );
	}

	/**
	 * Send all shipping measurements, or none.
	 *
	 * Make sure that *if* we have length, width or height, that we send all three. If we're
	 * missing any then we send none of them.
	 *
	 * @param  array  $elements   The current feed item elements.
	 * @param  int    $product_id The product to get the length of.
	 *
	 * @return void
	 */
	private function all_or_nothing_shipping_elements() {
		if ( empty( $this->additional_elements['shipping_width'] ) &&
			 empty( $this->additional_elements['shipping_length'] ) &&
			 empty( $this->additional_elements['shipping_height'] ) ) {
			return;
		}
		if ( empty( $this->additional_elements['shipping_width'] ) ||
			 empty( $this->additional_elements['shipping_length'] ) ||
			 empty( $this->additional_elements['shipping_height'] ) ) {
			unset( $this->additional_elements['shipping_length'] );
			unset( $this->additional_elements['shipping_width'] );
			unset( $this->additional_elements['shipping_height'] );
		}
	}

	/**
	 * Make sure we always send a stock value.
	 */
	private function force_stock_status() {
		if ( ! $this->is_in_stock && empty( $this->additional_elements['availability'] ) ) {
			$this->additional_elements['availability'] = array( 'out of stock' );
		}
	}

	/**
	 * Add additional images to the feed item.
	 */
	private function get_additional_images() {

		// Get the product ID to inspect for additional images.
		$product_id = $this->specific_id;

		// Work out whether to include additional images on variations. Bail if not.
		$include_on_variations = apply_filters( 'woocommerce_gpf_include_additional_images_on_variations', true, $product_id );
		if ( $this->is_variation && ! $include_on_variations ) {
			return;
		}

		// When processing additional images on variations, grab them from the main product.
		if ( $this->is_variation ) {
			$product_id = $this->general_id;
		}

		// Look for additional images.
		$excluded_ids[] = get_post_meta( $this->general_id, '_thumbnail_id', true );

		// List product gallery images first.
		if ( apply_filters( 'woocommerce_gpf_include_product_gallery_images', true ) ) {
			$product_gallery_images = get_post_meta( $product_id, '_product_image_gallery', true );
			if ( ! empty( $product_gallery_images ) ) {
				$product_gallery_images = explode( ',', $product_gallery_images );
				foreach ( $product_gallery_images as $product_gallery_image_id ) {
					if ( in_array( $product_gallery_image_id, $excluded_ids ) ) {
						continue;
					}
					$full_image_src            = wp_get_attachment_image_src( $product_gallery_image_id, $this->image_style, false );
					$this->additional_images[] = $full_image_src[0];
					$excluded_ids[]            = $product_gallery_image_id;
				}
			}
		}
		if ( apply_filters( 'woocommerce_gpf_include_attached_images', true ) ) {
			$found = false;
			if ( $this->is_variation ) {
				$images = wp_cache_get( 'children_' . $product_id, 'woocommerce_gpf', false, $found );
			}
			if ( $found === false ) {
				$images = get_children(
					array(
						'post_parent'    => $product_id,
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => 'ASC',
						'orderby'        => 'menu_order',
					)
				);
				if ( $this->is_variation ) {
					wp_cache_set( 'children_' . $product_id, $images, 'woocommerce_gpf', 10 );
				}
			}

			if ( is_array( $images ) && count( $images ) ) {
				foreach ( $images as $image ) {
					if ( in_array( $image->ID, $excluded_ids ) ) {
						continue;
					}
					$full_image_src            = wp_get_attachment_image_src( $image->ID, $this->image_style, false );
					$this->additional_images[] = $full_image_src[0];
				}
			}
		}
	}

	/**
	 * Calculate the values for a product / variation.
	 *
	 * Takes into account:
	 * - values set specifically against the variation
	 * - Values set specifically against the product
	 * - pre-populations that may apply to the variation
	 * - pre-populations that may apply to the variation
	 * - category defaults
	 * - store wide defaults
	 */
	private function calculate_values_for_product() {
		// Grab the values against the product.
		$product_values         = $this->get_specific_values_for_product( 'general' );
		$product_prepopulations = $this->get_prepopulations_for_product( 'general' );

		// Grab the values against the variation if different from product ID.
		if ( $this->specific_id !== $this->general_id ) {
			$variation_values         = $this->get_specific_values_for_product( 'specific' );
			$variation_prepopulations = $this->get_prepopulations_for_product( 'specific' );
		} else {
			$variation_values = $variation_prepopulations = array();
		}

		$category_values = $this->woocommerce_gpf_common->get_category_values_for_product( $this->general_id );
		$store_values    = $this->woocommerce_gpf_common->get_store_default_values();

		// If child.specific then use that
		// elseif parent.specific then use that
		// elseif child.prepulate then use that
		// elseif parent.prepopulate then use that
		// else use category defaults
		// else use store defaults
		$calculated = array_merge(
			$store_values,
			$category_values,
			$product_prepopulations,
			$variation_prepopulations,
			$product_values,
			$variation_values
		);
		if ( 'all' !== $this->feed_format ) {
			$calculated = $this->woocommerce_gpf_common->remove_other_feeds( $calculated, $this->feed_format );
		}
		return $this->woocommerce_gpf_common->limit_max_values( $calculated );
	}

	/**
	 * Retrieve specific values set against a product.
	 *
	 * @param $which_product string  Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array
	 */
	private function get_specific_values_for_product( $which_product ) {
		if ( 'general' === $which_product ) {
			$product_settings = get_post_meta( $this->general_id, '_woocommerce_gpf_data', true );
		} else {
			$product_settings = get_post_meta( $this->specific_id, '_woocommerce_gpf_data', true );
		}
		if ( ! is_array( $product_settings ) ) {
			return array();
		}
		return $this->woocommerce_gpf_common->remove_blanks( $product_settings );
	}

	/**
	 * Get the information that would be pre-populated for a product.
	 *
	 * @param $which_product string  Whether to pull info for the 'general' or 'specific' product being generated.
	 */
	private function get_prepopulations_for_product( $which_product ) {
		$results        = array();
		$prepopulations = $this->woocommerce_gpf_common->get_prepopulations();
		if ( empty( $prepopulations ) ) {
			return $results;
		}
		foreach ( $prepopulations as $gpf_key => $prepopulate ) {
			if ( empty( $prepopulate ) ) {
				continue;
			}
			$value = $this->get_prepopulate_value_for_product( $prepopulate, $which_product );
			if ( ! empty( $value ) ) {
				$results[ $gpf_key ] = $value;
			}
		}
		return $this->woocommerce_gpf_common->remove_blanks( $results );
	}

	/**
	 * Gets a specific prepopulated value for a product.
	 *
	 * @param string $prepopulate    The prepopulation value for a product.
	 * @param string $which_product  Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array                The prepopulated value for this product.
	 */
	private function get_prepopulate_value_for_product( $prepopulate, $which_product ) {
		$result               = array();
		list( $type, $value ) = explode( ':', $prepopulate );
		switch ( $type ) {
			case 'tax':
				$result = $this->get_tax_prepopulate_value_for_product( $value, $which_product );
				break;
			case 'field':
				$result = $this->get_field_prepopulate_value_for_product( $value, $which_product );
				break;
			case 'meta':
				$result = $this->get_meta_prepopulate_value_for_product( $value, $which_product );
				break;
		}
		return $result;
	}

	/**
	 * Gets a taxonomy value for a product to prepopulate.
	 *
	 * @param string $value          The taxonomy to grab values for.
	 * @param string $which_product  Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array              Array of values to use.
	 */
	private function get_tax_prepopulate_value_for_product( $value, $which_product ) {
		$result = array();

		if ( 'general' === $which_product ) {
			$product    = $this->general_product;
			$product_id = $this->general_id;
		} else {
			$product    = $this->specific_product;
			$product_id = $this->specific_id;
		}

		if ( is_callable( array( $product, 'get_type' ) ) ) {
			$product_type = $product->get_type();
		} else {
			$product_type = $product->product_type;
		}
		if ( 'variation' === $product_type ) {
			// Get the attributes.
			$attributes = $product->get_variation_attributes();
			// If the requested taxonomy is used as an attribute, grab it's value for this variation.
			if ( ! empty( $attributes[ 'attribute_' . $value ] ) ) {
				$terms = get_terms(
					array(
						'taxonomy' => $value,
						'slug'     => $attributes[ 'attribute_' . $value ],
					)
				);
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					$result = array();
				} else {
					$result = array( $terms[0]->name );
				}
			} else {
				// Otherwise grab the values to use direct from the term relationships.
				$terms = get_the_terms( $product_id, $value );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$result = wp_list_pluck( $terms, 'name' );
				} else {
					// Couldn't find it against the variation - grab the parent product value.
					if ( is_callable( array( $product, 'get_parent_id' ) ) ) {
						$terms = get_the_terms( $product->get_parent_id(), $value );
					} else {
						$terms = get_the_terms( $product->parent->id, $value );
					}
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						$result = wp_list_pluck( $terms, 'name' );
					}
				}
			}
		} else {
			// Get the term(s) tagged against the main product.
			$terms = get_the_terms( $product_id, $value );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$result = wp_list_pluck( $terms, 'name' );
			}
		}
		return $result;
	}

	/**
	 * Get a prepopulate value for a specific field for a product.
	 *
	 * @param string $field          Details of the field we want.
	 * @param string $which_product  Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array                The value for this field on this product.
	 */
	private function get_field_prepopulate_value_for_product( $field, $which_product ) {
		if ( 'general' === $which_product ) {
			$product = $this->general_product;
		} else {
			$product = $this->specific_product;
		}
		if ( ! $product ) {
			return array();
		}
		if ( 'sku' == $field ) {
			$sku = $product->get_sku();
			if ( ! empty( $sku ) ) {
				return array( $sku );
			}
		}
		if ( 'product_title' === $field ) {
			return array( $this->general_product->get_title() );
		}
		if ( 'variation_title' === $field ) {
			return array( $this->title );
		}
		return array();
	}

	/**
	 * Get a prepopulate value for a specific meta key for a product.
	 *
	 * @param string $meta_key       Details of the meta key we're interested in.
	 * @param string $which_product  Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array                The value for the requested meta key on this product.
	 */
	private function get_meta_prepopulate_value_for_product( $meta_key, $which_product ) {
		if ( 'general' === $which_product ) {
			$product_id = $this->general_id;
		} else {
			$product_id = $this->specific_id;
		}
		return get_post_meta( $product_id, $meta_key, false );
	}
}
