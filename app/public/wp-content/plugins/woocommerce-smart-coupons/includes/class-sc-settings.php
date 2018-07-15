<?php
/**
 * Smart Coupons Storewide Settings
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Settings' ) ) {

	/**
	 * Class for handling storewide settings for Smart Coupons
	 */
	class WC_SC_Settings {

		/**
		 * The WooCommerce settings tab name
		 *
		 * @since 3.4.0
		 */
		public static $tab_slug = 'wc-smart-coupons';

		/**
		 * Variable to hold instance of WC_SC_Settings
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * @var $sc_general_settings Array of Smart Coupons General Settings
		 */
		var $sc_general_settings;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'add_smart_coupon_admin_settings' ) );
			add_action( 'admin_init', array( $this, 'add_delete_credit_after_usage_notice' ) );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_smart_coupon_settings_tab' ), 50 );
			add_action( 'woocommerce_settings_tabs_' . self::$tab_slug, array( $this, 'smart_coupon_settings_page' ) );
			add_action( 'woocommerce_update_options_' . self::$tab_slug, array( $this, 'save_smart_coupon_admin_settings' ) );
		}

		/**
		 * Get single instance of WC_SC_Settings
		 *
		 * @return WC_SC_Settings Singleton object of WC_SC_Settings
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
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
		 * Function to add setting tab for Smart Coupons
		 * 
		 * @param array $settings_tabs 
		 */
		public function add_smart_coupon_settings_tab( $settings_tabs ) {

			$settings_tabs[ self::$tab_slug ] = __( 'Smart Coupons', WC_SC_TEXT_DOMAIN );

			return $settings_tabs;
		}

		/**
		 * Function to add styles and script for Smart Coupons settings page
		 */
		public function sc_settings_page_styles_scripts() {
			?>
			<style type="text/css">
				#TB_window img#TB_Image {
					border: none !important;
				}
				.form-table th {
					width: 25% !important;
				}
			</style>
			<?php
		}

		/**
		 * Function to display Smart Coupons settings
		 */
		public function smart_coupon_settings_page() {
			add_thickbox();
			woocommerce_admin_fields( $this->sc_general_settings );
			wp_nonce_field( 'wc_smart_coupons_settings', 'sc_security', false );
			$this->sc_settings_page_styles_scripts();
		}

		/**
		 * Function to add smart coupons admin settings
		 */
		public function add_smart_coupon_admin_settings() {
			$this->sc_general_settings = array(
					array(
						'title' 			=> __( 'Smart Coupons Settings', WC_SC_TEXT_DOMAIN ),
						'type'  			=> 'title',
						'desc'				=> __( 'Set up Smart Coupons the way you like. Use these options to configure/change the way Smart Coupons works.', WC_SC_TEXT_DOMAIN ),
						'id'    			=> 'sc_display_coupon_settings',
					),
					array(
						'name'              => __( 'Number of coupons to show', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( 'How many coupons (at max) should be shown on cart/checkout page?', WC_SC_TEXT_DOMAIN ),
						'id'                => 'wc_sc_setting_max_coupon_to_show',
						'type'              => 'number',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'Subject for Coupon emails', WC_SC_TEXT_DOMAIN ),
						'desc'              => sprintf(__( 'Subject for coupon details emails to customers. Default: %s.', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'Congratulations! You\'ve received a coupon', WC_SC_TEXT_DOMAIN ) . '</strong>' ),
						'id'                => 'smart_coupon_email_subject',
						'type'              => 'textarea',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'Displaying Coupons', WC_SC_TEXT_DOMAIN ),
						'desc'              => sprintf(__( 'Include coupon details on product\'s page, for products that issue coupons %s', WC_SC_TEXT_DOMAIN ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-associated-coupons.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>' ),
						'id'                => 'smart_coupons_is_show_associated_coupons',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => 'start',
					),
					array(
						'desc'              => sprintf(__( 'Show coupons available to customers on their My Account > Coupons page %s', WC_SC_TEXT_DOMAIN ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-myaccount.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>' ),
						'id'                => 'woocommerce_smart_coupon_show_my_account',
						'type'              => 'checkbox',
						'default'           => 'yes',
						'checkboxgroup'     => '',
					),
					array(
						'desc'              => sprintf(__( 'Include coupons received from other people on My Account > Coupons page %s', WC_SC_TEXT_DOMAIN ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-coupon-received.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>' ),
						'id'                => 'show_coupon_received_on_my_account',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => '',
					),
					array(
						'desc'              => sprintf(__( 'Show invalid or used coupons in My Account > Coupons %s', WC_SC_TEXT_DOMAIN ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-invalid-used-coupons.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>' ),
						'id'                => 'smart_coupons_show_invalid_coupons_on_myaccount',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => '',
					),
					array(
						'desc'              => sprintf(__( 'Display coupon description along with coupon code (on site as well as in emails) %s', WC_SC_TEXT_DOMAIN ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-coupon-description.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>' ),
						'id'                => 'smart_coupons_show_coupon_description',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => 'end',
					),
					array(
						'name'              => __( 'Automatic Deletion', WC_SC_TEXT_DOMAIN ),
						'desc'              => sprintf(__( 'Delete the store credit/gift coupon when entire credit amount is used up %s', WC_SC_TEXT_DOMAIN ), '<small>' . __('(Note: It\'s recommended to keep it Disabled)', WC_SC_TEXT_DOMAIN ) . '</small>' ),
						'id'                => 'woocommerce_delete_smart_coupon_after_usage',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => 'start',
					),
					array(
						'name'              => __( 'Coupon Emails', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( 'Email auto generated coupons to recipients', WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupons_is_send_email',
						'type'              => 'checkbox',
						'default'           => 'yes',
						'checkboxgroup'     => 'start',
					),
					array(
						'type' 				=> 'sectionend',
						'id' 				=> 'sc_display_coupon_settings',
					),
					array(
						'title' 			=> __( 'Labels', WC_SC_TEXT_DOMAIN ),
						'type'  			=> 'title',
						'desc'				=> __( 'Call it something else! Use these to quickly change coupon text labels through your store. Use translations for complete control.', WC_SC_TEXT_DOMAIN ),
						'id'    			=> 'sc_setting_labels',
					),
					array(
						'name'              => __( 'Store Credit Product CTA', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-purchase-credit-shop-text.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'sc_gift_certificate_shop_loop_button_text',
						'type'              => 'text',
						'desc_tip'          => sprintf(__( 'This is what will show instead of "Add to Cart" for products that sell store credits. Leave empty to show: %s', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'Select options', WC_SC_TEXT_DOMAIN ) . '<strong>' ),
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'While purchasing Store Credits', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-purchase-credit-product-page-text.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'smart_coupon_store_gift_page_text',
						'type'              => 'text',
						'desc_tip'          => sprintf(__( 'When you opt to allow people to buy store credits of any amount, this label will be used. Leave empty to show: %s', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'Purchase Credit worth', WC_SC_TEXT_DOMAIN ) . '</strong>' ),
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( '"Coupons with Product" description', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-associated-coupon-description-front.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'smart_coupon_product_page_text',
						'type'              => 'text',
						'desc_tip'          => sprintf(__( 'This is the heading above coupon details displayed on products that issue coupons. Leave empty to show: %s', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'You will get following coupon(s) when you buy this item', WC_SC_TEXT_DOMAIN ) . '</strong>' ),
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'On Cart/Checkout pages', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-coupon-cart-checkout-title.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'smart_coupon_cart_page_text',
						'type'              => 'text',
						'desc_tip'          => sprintf(__( 'This is the title for the list of available coupons, shown on Cart and Checkout pages. Leave empty to show: %s', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'Available Coupons (click on a coupon to use it)', WC_SC_TEXT_DOMAIN ) . '</strong>' ),
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'My Account page', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-myaccount-title.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'smart_coupon_myaccount_page_text',
						'type'              => 'text',
						'desc_tip'          => sprintf(__( 'Title of available coupons list on My Account page. Leave empty to show: %s', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'Available Coupons & Store Credits', WC_SC_TEXT_DOMAIN ) . '</strong>' ),
						'css'               => 'min-width:300px;',
					),
					array(
						'type' 				=> 'sectionend',
						'id' 				=> 'sc_setting_labels',
					),
					array(
						'title' 			=> __( 'Coupon Receiver Details during Checkout', WC_SC_TEXT_DOMAIN ),
						'type'  			=> 'title',
						'desc'				=> __( 'Buyers can send purchased coupons to anyone – right while they\'re checking out.', WC_SC_TEXT_DOMAIN ),
						'id'    			=> 'sc_coupon_receiver_settings',
					),
					array(
						'name'              => __( 'Title', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-title-coupon-receiver-form.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'smart_coupon_gift_certificate_form_page_text',
						'type'              => 'text',
						'desc_tip'          => sprintf(__( 'The title for coupon receiver details block. Leave empty to show: %s', WC_SC_TEXT_DOMAIN ), '<br/><strong>' . __( 'Send Coupons to...', WC_SC_TEXT_DOMAIN ) . '</strong>' ),
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'Description', WC_SC_TEXT_DOMAIN ),
						'desc'              => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://docs.woocommerce.com/wp-content/uploads/2012/08/sc-coupon-receiver-form-description.png' ) . '"><small>' . __( '[Preview]', WC_SC_TEXT_DOMAIN ) . '</small></a>',
						'id'                => 'smart_coupon_gift_certificate_form_details_text',
						'type'              => 'text',
						'desc_tip'          => __( 'Additional text below the title.', WC_SC_TEXT_DOMAIN ),
						'css'               => 'min-width:300px;',
					),
					array(
						'type' 				=> 'sectionend',
						'id' 				=> 'sc_coupon_receiver_settings',
					),
				);

			if ( $this->is_wc_gte_30() && wc_tax_enabled() ) {
				$before_tax_option[] = array(
					'name'     		=> __( 'Apply Before Tax', WC_SC_TEXT_DOMAIN ),
					'desc'     		=> __( 'Deduct credit/gift before doing tax calculations', WC_SC_TEXT_DOMAIN ),
					'id'       		=> 'woocommerce_smart_coupon_apply_before_tax',
					'type'     		=> 'checkbox',
					'default'  		=> 'no',
					'checkboxgroup' => 'start',

				);

				array_splice( $this->sc_general_settings, 9, 0, $before_tax_option ) ;
			}

			$this->sc_general_settings = apply_filters( 'wc_smart_coupons_settings', $this->sc_general_settings );

		}

		/**
		 * Function for saving settings for Gift Certificate
		 */
		public function save_smart_coupon_admin_settings() {

			if ( empty( $_POST['sc_security'] ) || ! wp_verify_nonce( $_POST['sc_security'], 'wc_smart_coupons_settings' ) ) {
				return;
			}

			woocommerce_update_options( $this->sc_general_settings );
		}

		/**
		 * Function to Add Delete Credit After Usage Notice
		 */
		public function add_delete_credit_after_usage_notice() {

			$is_delete_smart_coupon_after_usage = get_option( 'woocommerce_delete_smart_coupon_after_usage' );

			if ( $is_delete_smart_coupon_after_usage != 'yes' ) { return;
			}

			$admin_email = get_option( 'admin_email' );

			$user = get_user_by( 'email', $admin_email );

			$current_user_id = get_current_user_id();

			if ( ! empty( $current_user_id ) && ! empty( $user->ID ) && $current_user_id == $user->ID ) {
				add_action( 'admin_notices', array( $this, 'delete_credit_after_usage_notice' ) );
				add_action( 'admin_footer', array( $this, 'ignore_delete_credit_after_usage_notice' ) );
			}

		}

		/**
		 * Function to Delete Credit After Usage Notice
		 */
		public function delete_credit_after_usage_notice() {

			$current_user_id = get_current_user_id();
			$is_hide_delete_after_usage_notice = get_user_meta( $current_user_id, 'hide_delete_credit_after_usage_notice', true );
			if ( $is_hide_delete_after_usage_notice !== 'yes' ) {
				echo '<div class="error"><p>';
				if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'wc-settings' && empty( $_GET['tab'] ) ) {
					$page_based_text = __( 'Uncheck', WC_SC_TEXT_DOMAIN ) . ' &quot;<strong>' . __( 'Delete Gift / Credit, when credit is used up', WC_SC_TEXT_DOMAIN ) . '</strong>&quot;';
					$page_position = '#woocommerce_smart_coupon_show_my_account';
				} else {
					$page_based_text = '<strong>' . __( 'Important setting', WC_SC_TEXT_DOMAIN ) . '</strong>';
					$page_position = '';
				}
				echo sprintf( __( '%1$s: %2$s to avoid issues related to missing data for store credits. %3$s', WC_SC_TEXT_DOMAIN ), '<strong>' . __( 'WooCommerce Smart Coupons', WC_SC_TEXT_DOMAIN ) . '</strong>', $page_based_text, '<a href="' . admin_url( 'admin.php?page=wc-settings' . $page_position ) . '">' . __( 'Setting', WC_SC_TEXT_DOMAIN ) . '</a>' ) . ' <button type="button" class="button" id="hide_notice_delete_credit_after_usage">' . __( 'Hide this notice', WC_SC_TEXT_DOMAIN ) . '</button>';
				echo '</p></div>';
			}

		}

		/**
		 * Function to Ignore Delete Credit After Usage Notice
		 */
		public function ignore_delete_credit_after_usage_notice() {

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('body').on('click', 'button#hide_notice_delete_credit_after_usage', function(){
						jQuery.ajax({
							url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
							type: 'post',
							dataType: 'json',
							data: {
								action: 'hide_notice_delete_after_usage',
								security: '<?php echo wp_create_nonce( 'hide-smart-coupons-notice' ); ?>'
							},
							success: function( response ) {
								if ( response.message == 'success' ) {
									jQuery('button#hide_notice_delete_credit_after_usage').parent().parent().remove();
								}
							}
						});
					});
				});
			</script>
			<?php

		}



	}

}

WC_SC_Settings::get_instance();
