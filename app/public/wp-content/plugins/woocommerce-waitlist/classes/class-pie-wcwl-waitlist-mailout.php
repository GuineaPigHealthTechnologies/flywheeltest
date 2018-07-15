<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist_Mailout' ) ) {
	/**
	 * Waitlist Mailout
	 *
	 * An email sent to the admin when a new order is received/paid for.
	 *
	 * @class    Pie_WCWL_Waitlist_Mailout
	 * @extends  WC_Email
	 */
	class Pie_WCWL_Waitlist_Mailout extends WC_Email {

		/**
		 * Language code to send the email out in
		 *
		 * @var string
		 */
		public $language = '';

		/**
		 * Hooks up the functions for Waitlist Mailout
		 *
		 * @access public
		 */
		public function __construct() {
			$this->setup_text_strings();
			$this->customer_email = true;
			$this->wcwl_setup_mailout();
			add_action( 'wcwl_mailout_send_email', array( $this, 'trigger' ), 10, 3 );
			parent::__construct();
		}

		/**
		 * Setup required variables for mailout class
		 *
		 * @access public
		 * @return void
		 */
		public function wcwl_setup_mailout() {
			$this->id                 = WCWL_SLUG . '_mailout';
			$this->title              = __( 'Waitlist Mailout', 'woocommerce-waitlist' );
			$this->description        = __( 'When a product changes from being Out-of-Stock to being In-Stock, this email is sent to all users registered on the waitlist for that product.', 'woocommerce-waitlist' );
			$this->template_base      = WooCommerce_Waitlist_Plugin::$path . 'templates/emails/';
			$this->template_html      = 'waitlist-mailout.php';
			$this->template_plain     = 'plain/waitlist-mailout.php';
			$this->subject            = $this->get_option( 'subject', $this->get_default_subject() );
			$this->heading            = $this->get_option( 'heading', $this->get_default_heading() );
			$this->triggered_manually = false;
			global $sitepress;
			if ( isset( $sitepress ) ) {
				$this->language = wpml_get_default_language();
			}
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'A product you are waiting for is back in stock', 'woocommerce-waitlist' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( '{product_title} is now back in stock at {blogname}', 'woocommerce-waitlist' );
		}

		/**
		 * Trigger function for the mailout class
		 *
		 * @param int  $user_id    ID of user to send the mail to
		 * @param int  $product_id ID of product that email refers to
		 * @param bool $manual     was the email triggered manually?
		 *
		 * @return bool success
		 *
		 * @access public
		 */
		public function trigger( $user_id, $product_id, $manual = false ) {
			$this->triggered_manually = $manual;
			$user                     = get_user_by( 'id', $user_id );
			$languages                = get_user_meta( $user_id, 'wcwl_languages', true );
			global $woocommerce_wpml;
			if ( is_array( $languages ) && $woocommerce_wpml ) {
				$this->language = $languages[ $product_id ];
				$product        = wc_get_product( wpml_object_id_filter( $product_id, 'product', true, $languages[ $product_id ] ) );
				$this->setup_wpml_email( $languages[ $product_id ] );
			} else {
				$product = wc_get_product( $product_id );
			}
			$this->setup_required_data( $product, $user );
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}
			return $this->send_email( $product_id, $user->ID );
		}

		/**
		 * Setup all required translations
		 *
		 * @param $wcml_emails
		 * @param $language
		 */
		private function setup_wpml_email( $language ) {
			global $woocommerce_wpml, $sitepress, $woocommerce, $wpdb;
			if ( $language && $woocommerce_wpml && $sitepress ) {
				$wcml_emails = new WCML_Emails( $woocommerce_wpml, $sitepress, $woocommerce, $wpdb );
				$wcml_emails->change_email_language( $language );
			}
		}

		/**
		 * Load generic data into the email class
		 *
		 * @param $product
		 * @param $user
		 */
		private function setup_required_data( $product, $user ) {
			$this->object                          = $product;
			$this->placeholders['{product_title}'] = $product->get_title();
			$this->placeholders['{blogname}']      = $this->get_blogname();
			$this->recipient                       = $user->user_email;
		}

		/**
		 * Send the email and store the record in the archive if required
		 *
		 * @param $product_id
		 * @param $user_id
		 *
		 * @return bool success
		 */
		private function send_email( $product_id, $user_id ) {
			$subject = apply_filters( 'woocommerce_email_subject_' . $this->id, $this->get_translated_string( $this->subject, $this->language ) );
			$result  = $this->send( $this->get_recipient(), $subject, $this->get_content(), $this->get_headers(), $this->get_attachments() );
			if ( $result && 'yes' == get_option( 'woocommerce_waitlist_archive_on' ) ) {
				$this->add_user_to_archive( $product_id, $user_id );
			}
			return $result;
		}

		/**
		 * Translate the given string to the given language if a translation exists
		 *
		 * @param $string
		 * @param $language_code
		 *
		 * @return false|string
		 */
		private function get_translated_string( $string, $language_code ) {
			if ( $language_code ) {
				$string_id   = icl_get_string_id( $string, 'woocommerce-waitlist' );
				$translation = icl_get_string_by_id( $string_id, $language_code );
				if ( $translation ) {
					$string = $translation;
				}
			}

			return $this->format_string( $string );
		}

		/**
		 * Add a user to the archive for the current product
		 * This occurs when the user has been emailed and appends their ID to the list of users emailed today
		 *
		 * @param $product_id
		 * @param $user_id
		 */
		public function add_user_to_archive( $product_id, $user_id ) {
			$existing_archives = get_post_meta( $product_id, 'wcwl_waitlist_archive', true );
			if ( ! is_array( $existing_archives ) ) {
				$existing_archives = array();
			}
			$today = strtotime( date( "Ymd" ) );
			if ( ! isset( $existing_archives[ $today ] ) ) {
				$existing_archives[ $today ] = array();
			}
			$existing_archives[ $today ][ $user_id ] = $user_id;
			update_post_meta( $product_id, 'wcwl_waitlist_archive', $existing_archives );
		}

		/**
		 * Returns the html string needed to create an email to send out to user
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			ob_start();
			$product_id = $this->object->get_id();
			wc_get_template( $this->template_html, array(
				'product_title'           => get_the_title( $product_id ),
				'product_link'            => $this->generate_product_link( $this->object ),
				'email_heading'           => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->get_translated_string( $this->heading, $this->language ) ),
				'product_id'              => $product_id,
				'triggered_manually'      => $this->triggered_manually,
				'back_in_stock_text'      => $this->back_in_stock_text,
				'you_have_been_sent_text' => $this->you_have_been_sent_text,
				'purchase_text'           => $this->purchase_text,
				'remove_text'             => $this->remove_text,
			), false, $this->template_base );

			return ob_get_clean();
		}

		/**
		 * Returns the plain text needed to create an email to send out to user
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();
			$product_id = $this->object->get_id();
			wc_get_template( $this->template_plain, array(
				'product_title'           => get_the_title( $product_id ),
				'product_link'            => $this->generate_product_link( $this->object ),
				'product_id'              => $product_id,
				'triggered_manually'      => $this->triggered_manually,
				'back_in_stock_text'      => $this->back_in_stock_text,
				'you_have_been_sent_text' => $this->you_have_been_sent_text,
				'purchase_text'           => $this->purchase_text,
				'remove_text'             => $this->remove_text,
			), false, $this->template_base );

			return ob_get_clean();
		}

		/**
		 * Generate URL for the given product including UTM codes if required
		 *
		 * @param $product
		 *
		 * @return false|string
		 * @since 1.8.0
		 */
		public function generate_product_link( $product ) {
			$link = get_permalink( $product->get_id() );
			if ( 'yes' == $this->get_option( 'waitlist_add_analytics' ) ) {
				$tracking_codes = $this->validate_tracking_codes( $this->get_option( 'waitlist_utm_codes' ), $product );
				$link           = add_query_arg( $tracking_codes, $link );
			}

			return $link;
		}

		/**
		 * Format the UTM codes ready to be added to the URL
		 *
		 * @param $codes
		 * @param $product
		 *
		 * @return array
		 * @since 1.8.0
		 */
		public function validate_tracking_codes( $codes, $product ) {
			$codes      = explode( ';', $codes );
			$query_args = array();
			foreach ( $codes as $code ) {
				$code                            = str_replace( '{product_id}', $product->get_id(), $code );
				$code                            = str_replace( '{product_sku}', $product->get_sku(), $code );
				$query                           = explode( '=', $code );
				$query_args[ trim( $query[0] ) ] = trim( $query[1] );
			}

			return $query_args;
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'                => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'                => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'                => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type'             => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
				'waitlist_add_analytics' => array(
					'title'       => __( 'Add analytic tracking', 'woocommerce-waitlist' ),
					'description' => __( 'Add UTM tracking information to waitlist mailouts', 'woocommerce-waitlist' ),
					'default'     => 'no',
					'type'        => 'checkbox',
				),
				'waitlist_utm_codes'     => array(
					'title'       => __( 'Provide analytic tracking parameters', 'woocommerce-waitlist' ),
					'description' => __( 'Enter the parameters to add to the waitlist back in stock link. Enter each parameter on a new line in the format "utm_source=waitlist;" (without quotes). You can also use {product_id} and {product_sku} as placeholders for the ID and SKU of the product', 'woocommerce-waitlist' ),
					'default'     => "utm_source=waitlist;\nutm_medium=email;\nutm_campaign={product_id};",
					'type'        => 'textarea',
				),
			);
		}

		/**
		 * Setup text strings
		 */
		private function setup_text_strings() {
			$this->back_in_stock_text      = __( '%s is now back in stock at %s', 'woocommerce-waitlist' );
			$this->you_have_been_sent_text = __( 'You have been sent this email because your email address was registered on a waitlist for this product.', 'woocommerce-waitlist' );
			$this->purchase_text           = __( 'If you would like to purchase %s please visit the following link: %s', 'woocommerce-waitlist' );
			$this->remove_text             = __( 'You have been removed from the waitlist for this product', 'woocommerce-waitlist' );
		}
	}
}
return new Pie_WCWL_Waitlist_Mailout();