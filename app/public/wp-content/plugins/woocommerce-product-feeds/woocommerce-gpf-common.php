<?php

/**
 * Common class.
 *
 * Holds the config about what fields are available.
 */
class WoocommerceGpfCommon {

	/**
	* The field config array.
	*
	* The full list of available fields.
	*
	* @var array
	*/
	public $product_fields = array();

	/**
	 * The plugin settings.
	 *
	 * What settings have been chosen for each field, as well as non-field
	 * specific settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * The array of available feed types.
	 *
	 * @var array.
	 */
	private $feed_types = array();

	/**
	 * Initialise the class.
	 *
	 * Load the settings.
	 * Set up the available product fields.
	 * Set up the available feed types.
	 */
	public function initialise() {
		$this->settings = get_option( 'woocommerce_gpf_config' );
		$this->product_fields = apply_filters(
			'woocommerce_gpf_all_product_fields',
			array(
				'availability' => array(
					'desc'        => __( 'Availability', 'woocommerce_gpf' ),
					'full_desc'   => __( 'What status to send for in stock items. Out of stock products will always show as "Out of stock" irrespective of this setting.', 'woocommerce_gpf' ),
					'callback'    => 'render_availability',
					'can_default' => true,
					'feed_types'  => array( 'google', 'googleinventory', 'bing' ),
				),
				'is_bundle' => array(
					'desc'        => __( 'Bundle indicator (is_bundle)', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Allows you to indicate whether a product is a "bundle" of products.', 'woocommerce_gpf' ),
					'callback'    => 'render_is_bundle',
					'feed_types'  => array( 'google' ),
				),
				'availability_date' => array(
					'desc'        => __( 'Availability date', 'woocommerce_gpf' ),
					'full_desc'   => __( 'If you are accepting orders for products that are available for preorder, use this attribute to indicate when the product becomes available for delivery.', 'woocommerce_gpf' ),
					'callback'    => 'render_availability_date',
					'feed_types'  => array( 'google' ),
				 ),
				'condition' => array(
					'desc'        => __( 'Condition', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Condition or state of items', 'woocommerce_gpf' ),
					'callback'    => 'render_condition',
					'can_default' => true,
					'feed_types'  => array( 'google', 'bing' ),
				 ),
				'brand' => array(
					'desc'            => __( 'Brand', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Brand of the items', 'woocommerce_gpf' ),
					'can_default'     => true,
					'can_prepopulate' => true,
					'feed_types'      => array( 'google', 'bing' ),
					'google_len'      => 70,
					'max_values'      => 1,
				),
				'mpn' => array(
					'desc'            => __( 'Manufacturer Part Number (MPN)', 'woocommerce_gpf' ),
					'full_desc'       => __( 'This code uniquely identifies the product to its manufacturer', 'woocommerce_gpf' ),
					'feed_types'      => array( 'google', 'bing' ),
					'can_prepopulate' => true,
					'google_len'      => 70,
					'max_values'      => 1,
				),
				'product_type' => array(
					'desc'            => __( 'Product Type', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Your category of the items', 'woocommerce_gpf' ),
					'callback'        => 'render_product_type',
					'can_default'     => true,
					'can_prepopulate' => true,
					'feed_types'      => array( 'google' ),
					'google_len'      => 750,
				),
				'google_product_category' => array(
					'desc'        => __( 'Google Product Category', 'woocommerce_gpf' ),
					'full_desc'   => __( "Google's category of the item", 'woocommerce_gpf' ),
					'callback'    => 'render_product_type',
					'can_default' => true,
					'feed_types'  => array( 'google' ),
					'google_len'  => 750,
				),
				'gtin' => array(
					'desc'            => __( 'Global Trade Item Number (GTIN)', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Global Trade Item Numbers (GTINs) for your items. These identifiers include UPC (in North America), EAN (in Europe), JAN (in Japan), and ISBN (for books)', 'woocommerce_gpf' ),
					'feed_types'      => array( 'google', 'bing' ),
					'can_prepopulate' => true,
					'google_len'      => 50,
					'multiple'        => true,
				),
				'gender' => array(
					'desc'        => __( 'Gender', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Target gender for the item', 'woocommerce_gpf' ),
					'callback'    => 'render_gender',
					'can_default' => true,
					'feed_types'  => array( 'google' ),
				),
				'age_group' => array(
					'desc'        => __( 'Age Group', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Target age group for the item', 'woocommerce_gpf' ),
					'callback'    => 'render_age_group',
					'can_default' => true,
					'feed_types'  => array( 'google' ),
				),
				'color' => array(
					'desc'            => __( 'Colour', 'woocommerce_gpf' ),
					'full_desc'       => __( "Item's Colour", 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_prepopulate' => true,
					'google_len'      => 100,
				),
				'size' => array(
					'desc'            => __( 'Size', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Size of the items', 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_prepopulate' => true,
					'google_len'      => 100,
				),
				'size_type' => array(
					'desc'        => __( 'Size type', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Size type of the items', 'woocommerce_gpf' ),
					'feed_types'  => array( 'google' ),
					'can_default' => true,
					'callback'    => 'render_size_type',
				),
				'size_system' => array(
					'desc'        => __( 'Size system', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Size system', 'woocommerce_gpf' ),
					'feed_types'  => array( 'google' ),
					'can_default' => true,
					'callback'    => 'render_size_system',
				),
				'unit_pricing_measure' => array(
					'desc'            => __( 'Unit pricing measure', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Use this to define the measure and dimension of your product.', 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_default'     => true,
					'can_prepopulate' => true,
					'callback'        => 'render_textfield',
				),
				'unit_pricing_base_measure' => array(
					'desc'            => __( 'Unit pricing base measure', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Use this to include the denominator for your unit price. For example, you might be selling 150ml of perfume, but users are interested in seeing the price per 100ml.', 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_default'     => true,
					'can_prepopulate' => true,
					'callback'        => 'render_textfield',
				),
				'installment' => array(
					'desc'            => __( 'Instalment', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Use this to tell users the details of a monthly instalment plan that you offer to pay for your product.', 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_prepopulate' => false,
					'callback'        => 'render_installment',
				),
				'material' => array(
					'desc'            => __( 'Material', 'woocommerce_gpf' ),
					'full_desc'       => __( "Item's material", 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_prepopulate' => true,
					'google_len'      => 200,
				),
				'pattern' => array(
					'desc'            => __( 'Pattern', 'woocommerce_gpf' ),
					'full_desc'       => __( "Item's pattern", 'woocommerce_gpf' ),
					'feed_types'      => array( 'google' ),
					'can_prepopulate' => true,
					'google_len'      => 100,
				),
				'adult' => array(
					'desc'        => __( 'Adult content', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Whether the product contains nudity or sexually suggestive content', 'woocommerce_gpf' ),
					'callback'    => 'render_adult',
					'can_default' => true,
					'feed_types'  => array( 'google' ),
				),
				'identifier_exists' => array(
					'desc'        => __( 'Identifier exists flag', 'woocommerce_gpf' ),
					'full_desc'   => __( "Whether to include 'Identifier exists - false' when products don't have the relevant identifiers", 'woocommerce_gpf' ),
					'callback'    => 'render_i_exists',
					'can_default' => true,
					'feed_types'  => array( 'google' ),
				),
				'adwords_grouping' => array(
					'desc'        => __( 'Adwords grouping filter', 'woocommerce_gpf' ),
					'full_desc'   => __( 'Used to group products in an arbitrary way. It can be used for Product Filters to limit a campaign to a group of products or Product Targets, to bid differently for a group of products. This is a required field if the advertiser wants to bid differently to different sub-sets of products in the CPC or CPA % version. It can only hold one value.', 'woocommerce_gpf' ),
					'can_default' => true,
					'feed_types'  => array( 'google' ),
				),
				'adwords_labels' => array(
					'desc'            => __( 'Adwords labels', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Very similar to adwords_grouping, but it will only work on CPC. You can enter multiple values here, separating them with a comma (,). e.g. "widget,box".', 'woocommerce_gpf' ),
					'can_default'     => true,
					'can_prepopulate' => true,
					'feed_types'      => array( 'google' ),
					'multiple'        => true,
				),
				'bing_category' => array(
					'desc'            => __( 'Bing Category', 'woocommerce_gpf' ),
					'full_desc'       => __( "Bing's category of the item", 'woocommerce_gpf' ),
					'callback'        => 'render_b_category',
					'can_default'     => true,
					'can_prepopulate' => true,
					'feed_types'      => array( 'bing' ),
				),
				'delivery_label' => array(
					'desc'            => __( 'Delivery label', 'woocommerce_gpf' ),
					'full_desc'       => __( 'You can use this to control which shipping rules from your Merchant Centre account are applied to this product.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'can_prepopulate' => true,
					'callback'        => 'render_textfield',
					'feed_types'      => array( 'google' ),
					'google_len'      => 100,
				),
				'min_handling_time' => array(
					'desc'            => __( 'Minimum handling time', 'woocommerce_gpf' ),
					'full_desc'       => __( 'The minimum handling time is the shortest amount of time (in days) between when an order is placed and when the product is dispatched.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'feed_types'      => array( 'google' ),
					'google_len'      => 5,
				),
				'max_handling_time' => array(
					'desc'            => __( 'Maximum handling time', 'woocommerce_gpf' ),
					'full_desc'       => __( 'The maximum handling time is the longest amount of time (in days) between when an order is placed and when the product is dispatched.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'feed_types'      => array( 'google' ),
					'google_len'      => 5,
				),
				'custom_label_0' => array(
					'desc'            => __( 'Custom label 0', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Can be used to segment your products when setting up shopping campaigns in Adwords.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => true,
					'feed_types'      => array( 'google', 'bing' ),
					'google_len'      => 100,
					'max_values'      => 1,
				),
				'custom_label_1' => array(
					'desc'            => __( 'Custom label 1', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Can be used to segment your products when setting up shopping campaigns in Adwords.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => true,
					'feed_types'      => array( 'google', 'bing' ),
					'google_len'      => 100,
					'max_values'      => 1,
				),
				'custom_label_2' => array(
					'desc'            => __( 'Custom label 2', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Can be used to segment your products when setting up shopping campaigns in Adwords.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => true,
					'feed_types'      => array( 'google', 'bing' ),
					'google_len'      => 100,
					'max_values'      => 1,
				),
				'custom_label_3' => array(
					'desc'            => __( 'Custom label 3', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Can be used to segment your products when setting up shopping campaigns in Adwords.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => true,
					'feed_types'      => array( 'google', 'bing' ),
					'google_len'      => 100,
					'max_values'      => 1,
				),
				'custom_label_4' => array(
					'desc'            => __( 'Custom label 4', 'woocommerce_gpf' ),
					'full_desc'       => __( 'Can be used to segment your products when setting up shopping campaigns in Adwords.', 'woocommerce_gpf' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => true,
					'feed_types'      => array( 'google', 'bing' ),
					'google_len'      => 100,
					'max_values'      => 1,
				),
				'promotion_id' => array(
					'desc'            => __( 'Promotion ID', 'woocommerce_gpf' ),
					'full_desc'       => __( 'The unique ID of a promotion.' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => true,
					'feed_types'      => array( 'google' ),
				),
				'shippingprice' => array(
					'desc'            => __( 'Bing shipping info (price only)', 'woocommerce_gpf' ),
					'full_desc'       => __( 'The shipping field is required for Bing feed for Germany only. The format of the value is &quot;price&quot;, for example : 6.49' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => false,
					'feed_types'      => array( 'bing' ),
				),
				'shippingcountryprice' => array(
					'desc'            => __( 'Bing shipping info (country and price)', 'woocommerce_gpf' ),
					'full_desc'       => __( 'The shipping field is required for Bing feed for Germany only. The format of the value is &quot;country:price&quot;, for example : DE:6.49' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => false,
					'feed_types'      => array( 'bing' ),
				),
				'shippingcountryserviceprice' => array(
					'desc'            => __( 'Bing shipping info (country, service and price)', 'woocommerce_gpf' ),
					'full_desc'       => __( 'The shipping field is required for Bing feed for Germany only. The format of the value is &quot;country:service:price&quot;, for example : DE:Standard:6.49' ),
					'can_default'     => true,
					'callback'        => 'render_textfield',
					'can_prepopulate' => false,
					'feed_types'      => array( 'bing' ),
				),
			)
		);
		$this->feed_types = apply_filters(
			'woocommerce_gpf_feed_types',
			array(
				'google' => array(
					'name'    => __( 'Google merchant centre product feed', 'woocommerce_gpf' ),
					'icon'    => plugins_url( 'images/google.png', __FILE__ ),
					'class'   => 'WoocommerceGpfFeedGoogle',
					'url_args' => array(),
				),
				'googleinventory' => array(
					'name'    => __( 'Google merchant centre product inventory feed', 'woocommerce_gpf' ),
					'icon'    => plugins_url( 'images/google.png', __FILE__ ),
					'class'   => 'WoocommerceGpfFeedGoogleInventory',
					'url_args' => array(),
				),
				'bing' => array(
					'name'    => __( 'Bing merchant centre feed', 'woocommerce_gpf' ),
					'icon'    => plugins_url( 'images/bing.png', __FILE__ ),
					'class'   => 'WoocommerceGpfFeedBing',
					'url_args' => array(
						'f' => 'f.txt',
					),
				),
			)
		);
	}

	/**
	 * Get all of the configured feed types.
	 *
	 * @return array  The feed type configs for all feed types, keyed by feed
	 *                type identifier.
	 */
	public function get_feed_types() {
		return $this->feed_types;
	}

	/**
	 * Get the configured prepopulations.
	 * @return array
	 */
	public function get_prepopulations() {
		return isset( $this->settings['product_prepopulate'] ) ? $this->settings['product_prepopulate'] : array();
	}

	/**
	 * Get the defaults that apply to a product.
	 *
	 * @param int    $product_id   The product ID.
	 * @param string $feed_format  The feed type being generated.
	 *
	 * @return array
	 */
	public function get_defaults_for_product( $product_id, $feed_format = 'all' ) {
		$defaults = array_merge(
			$this->get_store_default_values(),
			$this->get_category_values_for_product( $product_id )
		);
		$defaults = $this->remove_blanks( $defaults );
		if ( 'all' !== $feed_format ) {
			$defaults = $this->remove_other_feeds( $defaults, $feed_format );
		}
		return $defaults;
	}

	/**
	 * Generate a list of choices for the "prepopulate" options.
	 *
	 * @return array  An array of preopulate choices.
	 */
	public function get_prepopulate_options() {
		$options = array();
		$options = array_merge( $options, $this->get_available_taxonomies() );
		$options = array_merge( $options, $this->get_prepopulate_fields() );
		$options = array_merge( $options, $this->get_prepopulate_meta() );
		return $options;
	}

	/**
	 * Helper function to remove blank array elements
	 *
	 * @access private
	 * @param array $array The array of elements to filter
	 * @return array The array with blank elements removed
	 */
	public function remove_blanks( $array ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return $array;
		}
		foreach ( array_keys( $array ) as $key ) {
			if ( '' === $array[ $key ] ||
				 empty( $this->settings['product_fields'][ $key ] ) ) {
				unset( $array[ $key ] );
			}
		}
		return $array;
	}

	/**
	 * Helper function to remove items not needed in this feed type
	 *
	 * @access private
	 * @param array $array The list of fields to be filtered
	 * @param string $feed_format The feed format that should have its fields maintained
	 * @return array The list of fields filtered to only contain elements that apply to the selected $feed_format
	 */
	public function remove_other_feeds( $array, $feed_format ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return $array;
		}
		foreach ( array_keys( $array ) as $key ) {
			if ( empty( $this->product_fields[ $key ] ) || ! in_array( $feed_format, $this->product_fields[ $key ]['feed_types'] ) ) {
				unset( $array[ $key ] );
			}
		}
		return $array;
	}

	/**
	 * Get the store defaults.
	 */
	public  function get_store_default_values() {
		if ( ! isset( $this->settings['product_defaults'] ) ) {
			$this->settings['product_defaults'] = array();
		}
		return $this->remove_blanks( $this->settings['product_defaults'] );
	}

	/**
	 * Retrieve the category level defaults for a product.
	 *
	 * @param int  $product_id  The product ID.
	 *
	 * @return array
	 */
	public function get_category_values_for_product( $product_id ) {
		$categories = get_the_terms( $product_id, 'product_cat' );
		if ( false === $categories ) {
			return array();
		}
		$values = array();
		foreach ( $categories as $category ) {
			$category_id = $category->term_id;
			$category_settings = $this->get_values_for_category( $category_id );
			$category_settings = $this->remove_blanks( $category_settings );
			$values = array_merge( $values, $category_settings );
		}
		return $this->remove_blanks( $values );
	}

	/**
	 * Make sure that each element does not contain more values than it should.
	 *
	 * @param   array   $data  The data for a product / category.
	 * @return  array          The modified data array.
	 */
	public function limit_max_values( $data ) {
		foreach ( $this->product_fields as $key => $element_settings ) {
			if ( empty( $element_settings['max_values'] ) ||
				 empty( $data[ $key ] ) ||
				 ! is_array( $data[ $key ] ) ) {
				continue;
			}
			$limit = intval( $element_settings['max_values'] );
			$data[ $key ] = array_slice( $data[ $key ], 0, $limit );
		}
		return $data;
	}

	/**
	 * Retrieve category defaults for a specific category
	 *
	 * @access public
	 * @param  int $category_id The category ID to retrieve information for
	 * @return array            The category data
	 */
	private function get_values_for_category( $category_id ) {
		if ( ! $category_id ) {
			return array();
		}
		$values = get_woocommerce_term_meta( $category_id, '_woocommerce_gpf_data', true );
		if ( ! is_array( $values ) ) {
			return array();
		}
		return $values;
	}

	/**
	 * Get a list of the available fields to use for prepopulation.
	 *
	 * @return array  Array of the available fields.
	 */
	private function get_prepopulate_fields() {
		$fields = array(
			'field:sku' => 'SKU',
		);
		asort( $fields );
		return array_merge( array( 'disabled:fields' => __( '- Product fields -', 'woo_gpf' ) ), $fields );
	}

	/**
	 * Get a list of the available taxonomies.
	 *
	 * @return array Array of available product taxonomies.
	 */
	private function get_available_taxonomies() {
		$taxonomies = get_object_taxonomies( 'product' );
		$taxes = array();
		foreach ( $taxonomies as $taxonomy ) {
			$tax_details = get_taxonomy( $taxonomy );
			$taxes[ 'tax:' . $taxonomy ] = $tax_details->labels->name;
		}
		asort( $taxes );
		return array_merge( array( 'disabled:taxes' => __( '- Taxonomies -', 'woo_gpf' ) ), $taxes );
	}

	private function get_prepopulate_meta() {
		global $wpdb, $table_prefix;
		// Try and get it from the transient if possible.
		$fields = get_transient( 'woocommerce_gpf_meta_prepopulate_options' );
		if ( false !== $fields ) {
			return $fields;
		}
		// If not, query for it and store it for later.
		$fields = array();
		$sql = "SELECT DISTINCT( {$table_prefix}postmeta.meta_key ) FROM {$table_prefix}posts LEFT JOIN {$table_prefix}postmeta ON {$table_prefix}posts.ID = {$table_prefix}postmeta.post_id WHERE {$table_prefix}posts.post_type = 'product'";
		$meta_keys = $wpdb->get_col( $sql );
		foreach ( $meta_keys as $meta_key ) {
			// Skip internal meta values that start with an _
			if ( stripos( $meta_key, '_' ) === 0 ) {
				continue;
			}
			$fields[ 'meta:' . $meta_key ] = $meta_key;
		}
		// Add a grouping header if we have some results.
		if ( ! empty( $fields ) ) {
			$fields = array_merge( array( 'disabled:meta' => __( '- Custom fields -', 'woo_gpf' ) ), $fields);
		}
		set_transient( 'woocommerce_gpf_meta_prepopulate_options', $fields,  MONTH_IN_SECONDS );
		return $fields;
	}

	public function is_feed_enabled( $feed_type ) {
		return ! empty( $this->settings['gpf_enabled_feeds'][ $feed_type ] );
	}
}
