<?php
/**
 * Smart Coupons fields in coupons
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Coupon_Fields' ) ) {

	/**
	 * Class for handling Smart Coupons' field in coupons
	 */
	class WC_SC_Coupon_Fields {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Fields
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'woocommerce_coupon_options', array( $this, 'woocommerce_smart_coupon_options' ) );
			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'sc_woocommerce_coupon_options_usage_restriction' ) );
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_smart_coupon_discount_type' ) );
			add_action( 'save_post', array( $this, 'woocommerce_process_smart_coupon_meta' ), 10, 2 );

		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param $function_name string
		 * @param $arguments array of arguments passed while calling $function_name
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) { return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Get single instance of WC_SC_Coupon_Fields
		 *
		 * @return WC_SC_Coupon_Fields Singleton object of WC_SC_Coupon_Fields
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * function to display the coupon data meta box.
		 */
		public function woocommerce_smart_coupon_options() {
			global $post;

			$is_page_bulk_generate = false;
			if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'wc-smart-coupons' ) {
				$is_page_bulk_generate = true;
			}

			?>
			<style type="text/css">
				.smart-coupons-field {
					background-color: #f0fff0;
				}
				.coupon_title_prefix_suffix_field input {
					height: 2em;
				}
			</style>
			<script type="text/javascript">
				jQuery(function(){
					var customerEmails;
					var showHideSmartCouponsOptions = function() {
						if ( jQuery('select#discount_type').val() == 'smart_coupon' ) {
							jQuery('input#is_pick_price_of_product').parent('p').show();
							jQuery('input#auto_generate_coupon').attr('checked', 'checked');
							jQuery('div#for_prefix_suffix').show();
							jQuery('div#sc_is_visible_storewide').hide();
							jQuery("p.auto_generate_coupon_field").hide();
							jQuery('p.sc_coupon_validity').show();
						} else {
							jQuery('input#is_pick_price_of_product').parent('p').hide();
							jQuery('div#sc_is_visible_storewide').show();
							customerEmails = jQuery('input#customer_email').val();
							if ( customerEmails != undefined || customerEmails != '' ) {
								customerEmails = customerEmails.trim();
								if ( customerEmails == '' ) {
									jQuery('input#sc_is_visible_storewide').parent('p').show();
								} else {
									jQuery('input#sc_is_visible_storewide').parent('p').hide();
								}
							}
							jQuery("p.auto_generate_coupon_field").show();
							if (jQuery("input#auto_generate_coupon").is(":checked")){
								jQuery('p.sc_coupon_validity').show();
							} else {
								jQuery('p.sc_coupon_validity').hide();
							}
						}
					};

					var showHidePrefixSuffix = function() {
						<?php if ( ! $is_page_bulk_generate ) { ?>
							if (jQuery("#auto_generate_coupon").is(":checked")){
								//show the hidden div
								jQuery("div#for_prefix_suffix").show("slow");
								jQuery("div#sc_is_visible_storewide").hide("slow");
								jQuery('p.sc_coupon_validity').show("slow");
							} else {
								//otherwise, hide it
								jQuery("div#for_prefix_suffix").hide("slow");
								jQuery("div#sc_is_visible_storewide").show("slow");
								jQuery('p.sc_coupon_validity').hide("slow");
							}
						<?php } ?>
					}

					setTimeout(function(){
						showHideSmartCouponsOptions();
						showHidePrefixSuffix();
					}, 100);

					jQuery("#auto_generate_coupon").on('change', function(){
						showHidePrefixSuffix();
					});

					jQuery('select#discount_type').on('change', function(){
						showHideSmartCouponsOptions();
						showHidePrefixSuffix();
					});

					jQuery('input#customer_email').on('keyup', function(){
						showHideSmartCouponsOptions();
					});

				});
			</script>
			<div class="options_group smart-coupons-field" style="border-top: 1px solid #eee;">
				<p class="form-field sc_coupon_validity ">
					<label for="sc_coupon_validity"><?php _e( 'Valid for', WC_SC_TEXT_DOMAIN ); ?></label>
					<input type="number" class="short" style="width: 15%;" name="sc_coupon_validity" id="sc_coupon_validity" value="<?php echo get_post_meta( $post->ID, 'sc_coupon_validity', true ); ?>" placeholder="0" min="1">&nbsp;
					<select name="validity_suffix" style="float: none;">
						<option value="days" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'days' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Days', WC_SC_TEXT_DOMAIN ); ?></option>
						<option value="weeks" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'weeks' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Weeks', WC_SC_TEXT_DOMAIN ); ?></option>
						<option value="months" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'months' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Months', WC_SC_TEXT_DOMAIN ); ?></option>
						<option value="years" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'years' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Years', WC_SC_TEXT_DOMAIN ); ?></option>
					</select>
					<span class="description"><?php echo __( '(Used only for auto-generated coupons)', WC_SC_TEXT_DOMAIN ); ?></span>
				</p>
				<?php 
					if ( ! $is_page_bulk_generate ) {
						woocommerce_wp_checkbox( array( 'id' => 'is_pick_price_of_product', 'label' => __( 'Coupon Value Same as Product\'s Price?', WC_SC_TEXT_DOMAIN ), 'description' => __( 'When checked, generated coupon\'s value will be same as product\'s price', WC_SC_TEXT_DOMAIN ) ) );
						woocommerce_wp_checkbox( array( 'id' => 'auto_generate_coupon', 'label' => __( 'Auto Generate New Coupons with each item', WC_SC_TEXT_DOMAIN ), 'description' => __( 'Generate exact copy of this coupon with unique coupon code for each purchased product (needs this coupon to be linked with that product)', WC_SC_TEXT_DOMAIN ) ) );
					}

				echo '<div id="for_prefix_suffix">';

				?>
				<p class="form-field coupon_title_prefix_suffix_field ">
					<label for="coupon_title_prefix"><?php echo __( 'Coupon Code Format', WC_SC_TEXT_DOMAIN ); ?></label>
					<input type="text" class="short" style="width: 15%;" name="coupon_title_prefix" id="coupon_title_prefix" value="<?php echo ( ! empty( $post->ID ) ) ? get_post_meta( $post->ID, 'coupon_title_prefix', true ) : ''; ?>" placeholder="Prefix">&nbsp;
					<code>coupon_code</code>&nbsp;
					<input type="text" class="short" style="float: initial; width: 15%;" name="coupon_title_suffix" id="coupon_title_suffix" value="<?php echo ( ! empty( $post->ID ) ) ? get_post_meta( $post->ID, 'coupon_title_suffix', true ) : ''; ?>" placeholder="Suffix"> 
					<span class="description"><?php echo __( '(We recommend up to three letters for prefix/suffix)', WC_SC_TEXT_DOMAIN ); ?></span>
				</p>
				<?php

				echo '</div>';

				if ( ! $is_page_bulk_generate ) {

					echo '<div id="sc_is_visible_storewide">';
					// for disabling e-mail restriction
					woocommerce_wp_checkbox( array( 'id' => 'sc_is_visible_storewide', 'label' => __( 'Show on cart/checkout?', WC_SC_TEXT_DOMAIN ), 'description' => __( 'When checked, this coupon will be visible on cart/checkout page for everyone', WC_SC_TEXT_DOMAIN ) ) );

					echo '</div>';

				}
				?>
			</div>
			<?php

		}

		/**
		 * function add additional field to disable email restriction
		 */
		public function sc_woocommerce_coupon_options_usage_restriction() {

			?>
			<div class="options_group smart-coupons-field">
				<?php
					woocommerce_wp_checkbox( array( 'id' => 'sc_disable_email_restriction', 'label' => __( 'Disable Email restriction?', WC_SC_TEXT_DOMAIN ), 'description' => __( 'Do not restrict auto-generated coupons to buyer/receiver email, anyone with coupon code can use it', WC_SC_TEXT_DOMAIN ) ) );
				?>
			</div>
			<?php

		}

		/**
		 * function to process smart coupon meta
		 *
		 * @param int    $post_id
		 * @param object $post
		 */
		public function woocommerce_process_smart_coupon_meta( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) { return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) { return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) { return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) { return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) { return;
			}
			if ( $post->post_type != 'shop_coupon' ) { return;
			}

			if ( isset( $_POST['auto_generate_coupon'] ) ) {
				update_post_meta( $post_id, 'auto_generate_coupon', $_POST['auto_generate_coupon'] );
			} else {
				if ( get_post_meta( $post_id, 'discount_type', true ) == 'smart_coupon' ) {
					update_post_meta( $post_id, 'auto_generate_coupon', 'yes' );
				} else {
					update_post_meta( $post_id, 'auto_generate_coupon', 'no' );
				}
			}

			if ( isset( $_POST['usage_limit_per_user'] ) ) {
				update_post_meta( $post_id, 'usage_limit_per_user', $_POST['usage_limit_per_user'] );
			}

			if ( isset( $_POST['limit_usage_to_x_items'] ) ) {
				update_post_meta( $post_id, 'limit_usage_to_x_items', $_POST['limit_usage_to_x_items'] );
			}

			if ( get_post_meta( $post_id, 'discount_type', true ) == 'smart_coupon' ) {
				update_post_meta( $post_id, 'apply_before_tax', 'no' );
			}

			if ( isset( $_POST['coupon_title_prefix'] ) ) {
				update_post_meta( $post_id, 'coupon_title_prefix', $_POST['coupon_title_prefix'] );
			}

			if ( isset( $_POST['coupon_title_suffix'] ) ) {
				update_post_meta( $post_id, 'coupon_title_suffix', $_POST['coupon_title_suffix'] );
			}

			if ( isset( $_POST['sc_coupon_validity'] ) ) {
				update_post_meta( $post_id, 'sc_coupon_validity', $_POST['sc_coupon_validity'] );
				update_post_meta( $post_id, 'validity_suffix', $_POST['validity_suffix'] );
			}

			if ( isset( $_POST['sc_is_visible_storewide'] ) ) {
				update_post_meta( $post_id, 'sc_is_visible_storewide', $_POST['sc_is_visible_storewide'] );
			} else {
				update_post_meta( $post_id, 'sc_is_visible_storewide', 'no' );
			}

			if ( isset( $_POST['sc_disable_email_restriction'] ) ) {
				update_post_meta( $post_id, 'sc_disable_email_restriction', $_POST['sc_disable_email_restriction'] );
			} else {
				update_post_meta( $post_id, 'sc_disable_email_restriction', 'no' );
			}

			if ( isset( $_POST['is_pick_price_of_product'] ) ) {
				update_post_meta( $post_id, 'is_pick_price_of_product', $_POST['is_pick_price_of_product'] );
			} else {
				update_post_meta( $post_id, 'is_pick_price_of_product', 'no' );
			}

		}

		/**
		 * Function to add new discount type 'smart_coupon'
		 *
		 * @param array $discount_types existing discount types
		 * @return array $discount_types including smart coupon discount type
		 */
		public function add_smart_coupon_discount_type( $discount_types ) {
			$discount_types['smart_coupon'] = __( 'Store Credit / Gift Certificate', WC_SC_TEXT_DOMAIN );
			return $discount_types;
		}

	}

}

WC_SC_Coupon_Fields::get_instance();
