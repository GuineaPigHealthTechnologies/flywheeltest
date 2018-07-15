<?php
/**
 * WC_CSP_Condition_Package_Category class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category in Package Condition.
 *
 * @class   WC_CSP_Condition_Package_Category
 * @version 1.3.3
 */
class WC_CSP_Condition_Package_Category extends WC_CSP_Condition {

	public function __construct() {

		$this->id                            = 'category_in_package';
		$this->title                         = __( 'Category', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions = array( 'shipping_methods', 'shipping_countries' );
	}

	/**
	 * Categories condition matching relationship. Values 'or' | 'and'.
	 *
	 * @since  1.3.3
	 *
	 * @return string
	 */
	protected function get_term_relationship() {
		return apply_filters( 'woocommerce_csp_package_category_matching_relationship', 'or' );
	}

	/**
	 * Checks a package.
	 *
	 * @since  1.3.3
	 *
	 * @param  array   $package_contents
	 * @param  string  $modifier
	 * @return bool
	 */
	protected function check_package( $package_contents, $category_ids, $modifier ) {

		$result = in_array( $modifier, array( 'all-in', 'not-all-in' ) );

		foreach ( $package_contents as $cart_item_key => $cart_item_data ) {

			$product_category_terms = get_the_terms( $cart_item_data[ 'product_id' ], 'product_cat' );

			if ( $product_category_terms && ! is_wp_error( $product_category_terms ) ) {

				$categories_matching = 0;

				foreach ( $product_category_terms as $product_category_term ) {
					if ( in_array( $product_category_term->term_id, $category_ids ) ) {
						$categories_matching++;
					}
				}

				$term_relationship = $this->get_term_relationship();

				if ( $modifier === 'in' || $modifier === 'not-in' ) {

					if ( 'or' === $term_relationship && $categories_matching ) {
						$result = true;
					} elseif ( 'and' === $term_relationship && $categories_matching === sizeof( $category_ids ) ) {
						$result = true;
					}

					if ( $result ) {
						break;
					}

				} elseif ( $modifier === 'all-in' || $modifier === 'not-all-in' ) {

					if ( 'or' === $term_relationship && ! $categories_matching ) {
						$result = false;
					} elseif ( 'and' === $term_relationship && $categories_matching !== sizeof( $category_ids ) ) {
						$result = false;
					}

					if ( ! $result ) {
						break;
					}
				}
			}
		}

		if ( $result ) {
			$result = in_array( $modifier, array( 'in', 'all-in' ) );
		} else {
			$result = in_array( $modifier, array( 'not-in', 'not-all-in' ) );
		}

		return $result;
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return false;
		}

		$package_count = ! empty( $args[ 'package_count' ] ) ? absint( $args[ 'package_count' ] ) : 1;

		if ( $this->check_package( $package[ 'contents' ], $data[ 'value' ], $data[ 'modifier' ] ) ) {

			$term_names = array();

			foreach ( $data[ 'value' ] as $term_id ) {

				$term = get_term_by( 'id', $term_id, 'product_cat' );

				if ( $term ) {
					$term_names[] = $term->name;
				}
			}

			$string  = WC_CSP_Condition::merge_titles( $term_names );
			$message = false;

			if ( 'in' === $data[ 'modifier' ] ) {

				if ( sizeof( $term_names ) > 1 ) {
					$message = sprintf( __( 'remove all products in the %s categories from your cart', 'woocommerce-conditional-shipping-and-payments' ), $string );
				} else {
					$message = sprintf( __( 'remove all products in the %s category from your cart', 'woocommerce-conditional-shipping-and-payments' ), $string );
				}

			} elseif ( in_array( $data[ 'modifier' ], array( 'all-in', 'not-in' ) ) ) {

				if ( $package_count > 1 ) {
					$message = __( 'add some qualifying products to the affected shipping package(s)', 'woocommerce-conditional-shipping-and-payments' );
				} else {
					$message = __( 'add some qualifying products to your cart', 'woocommerce-conditional-shipping-and-payments' );
				}

			} elseif ( 'not-all-in' === $data[ 'modifier' ] ) {

				if ( sizeof( $term_names ) > 1 ) {
					$message = sprintf( __( 'make sure that your cart contains only products from the %s categories', 'woocommerce-conditional-shipping-and-payments' ), $string );
				} else {
					$message = sprintf( __( 'make sure that your cart contains only products from the %s category', 'woocommerce-conditional-shipping-and-payments' ), $string );
				}
			}

			return $message;
		}

		return false;
	}

	/**
	 * Evaluate if a condition field is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		if ( ! empty( $args[ 'package' ] ) ) {
			$package = $args[ 'package' ];
		} else {
			return true;
		}

		return $this->check_package( $package[ 'contents' ], $data[ 'value' ], $data[ 'modifier' ] );
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( ! empty( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_map( 'intval', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get categories-in-package condition content for global restrictions.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$categories = array();
		$modifier   = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$categories = $condition_data[ 'value' ];
		}

		$product_categories = ( array ) get_terms( 'product_cat', array( 'get' => 'all' ) );

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'not in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="all-in" <?php selected( $modifier, 'all-in', true ) ?>><?php echo __( 'all items in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ) ?>><?php echo __( 'not all items in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" style="width:80%;" class="multiselect <?php echo WC_CSP_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Select categories&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
				<?php
					foreach ( $product_categories as $product_category )
						echo '<option value="' . $product_category->term_id . '" ' . selected( in_array( $product_category->term_id, $categories ), true, false ).'>' . $product_category->name . '</option>';
				?>
			</select>
		</div><?php
	}

}
