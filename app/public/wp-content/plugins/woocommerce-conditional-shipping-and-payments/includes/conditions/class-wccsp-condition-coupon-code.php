<?php
/**
 * WC_CSP_Condition_Coupon_Code class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coupon Code Condition.
 *
 * @class    WC_CSP_Condition_Coupon_Code
 * @version  1.3.1
 */
class WC_CSP_Condition_Coupon_Code extends WC_CSP_Condition {

	public function __construct() {

		$this->id                             = 'coupon_code_used';
		$this->title                          = __( 'Coupon code', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$active_coupons = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_coupons() : WC()->cart->coupons;
		$resolution     = false;

		foreach ( $active_coupons as $coupon ) {
			if ( $data[ 'modifier' ] === 'used' && in_array( $coupon->get_code(), $data[ 'value' ] ) ) {
				$resolution = sprintf( __( 'remove coupon %s', 'woocommerce-conditional-shipping-and-payments' ), $coupon->get_code() );
			} elseif ( $data[ 'modifier' ] === 'not-used' && false === in_array( $coupon->get_code(), $data[ 'value' ] ) ) {
				$resolution = __( 'use a qualifying coupon', 'woocommerce-conditional-shipping-and-payments' );
			}
		}

		return $resolution;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  string $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$active_coupons = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_coupons() : WC()->cart->coupons;

		if ( empty( $active_coupons ) && $data[ 'modifier' ] === 'not-used' ) {
			return true;
		}

		$condition_matching = false;

		foreach ( $active_coupons as $coupon ) {

			if ( $data[ 'modifier' ] === 'used' && in_array( $coupon->get_code(), $data[ 'value' ] ) ) {
				$condition_matching = true;
				break;
			} elseif ( $data[ 'modifier' ] === 'not-used' && false === in_array( $coupon->get_code(), $data[ 'value' ] ) ) {
				$condition_matching = true;
				break;
			}
		}

		return $condition_matching;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( isset( $posted_condition_data[ 'value' ] ) ) {

			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_filter( array_map( 'wc_clean', explode( ",", $posted_condition_data[ 'value' ] ) ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_ndex
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier     = '';
		$coupon_codes = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$coupon_codes = implode( ",", $condition_data[ 'value' ] );
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_modifier">
			<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
				<option value="used" <?php selected( $modifier, 'used', true ) ?>><?php echo __( 'used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
				<option value="not-used" <?php selected( $modifier, 'not-used', true ) ?>><?php echo __( 'is not used', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
			</select>
		</div>
		<div class="condition_value">
			<input type="text"  name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" value="<?php echo $coupon_codes; ?>" placeholder="" step="any" min="0"/>
		</div>
		<?php
	}
}
