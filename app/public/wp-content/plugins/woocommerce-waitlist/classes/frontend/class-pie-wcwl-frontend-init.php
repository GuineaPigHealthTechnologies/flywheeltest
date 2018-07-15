<?php
/**
 * Initialise waitlist on the frontend product pages and load shortcode.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Init' ) ) {
	class Pie_WCWL_Frontend_Init {

		/**
		 * Hooks up the frontend initialisation and any functions that need to run before the 'init' hook
		 *
		 * @access public
		 */
		public function init() {
			add_action( 'wp', array( $this, 'frontend_init' ) );
			add_action( 'wc_quick_view_before_single_product', array( $this, 'quickview_init' ) );
			add_shortcode( 'woocommerce_my_waitlist', array( $this, 'display_users_waitlists' ) );
			if ( ! is_admin() && 'yes' == get_option( 'woocommerce_waitlist_notify_admin' ) ) {
				add_action( 'wcwl_after_add_user_to_waitlist', array( $this, 'email_admin_user_joined_waitlist' ), 10, 2 );
			}
		}

		/**
		 * Check requirements and run initialise if waitlist is enabled
		 */
		public function frontend_init() {
			$product_id = $this->return_product_id();
			$this->initialise_waitlist_for_product( $product_id );
			$this->load_user_waitlist_classes();
		}

		/**
		 * Retrieve the product ID for the current page/request
		 *
		 * @return int|mixed|string
		 */
		private function return_product_id() {
			if ( self::is_ajax_variation_request() && ( isset( $_REQUEST['product_id'] ) && '' != $_REQUEST['product_id'] ) ) {
				return absint( $_REQUEST['product_id'] );
			}
			global $post;
			if ( is_product() ) {
				return $post->ID;
			}
			if ( $this->post_has_shortcode( $post, 'product_page' ) ) {
				return $this->get_product_id_from_post_content( $post->post_content );
			}

			return '';
		}

		/**
		 * Is WooCommerce performing an ajax request to return a child variation
		 *
		 * @return bool
		 */
		public static function is_ajax_variation_request() {
			if ( isset( $_REQUEST['wc-ajax'] ) && 'get_variation' == $_REQUEST['wc-ajax'] ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if current page is using the given shortcode (checks for product_page or woocommerce_my_waitlist)
		 *
		 * @param $post
		 *
		 * @return bool
		 */
		private function post_has_shortcode( $post, $shortcode ) {
			if ( ! empty( $post->post_content ) && strstr( $post->post_content, '[' . $shortcode ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Look fpr the product ID in the provided post content
		 *
		 * @param $content
		 *
		 * @return mixed
		 */
		public static function get_product_id_from_post_content( $content ) {
			$content_after_shortcode    = substr( $content, strpos( $content, '[product_page' ) + 1 );
			$content_before_closing_tag = strtok( $content_after_shortcode, ']' );
			$product_id                 = filter_var( $content_before_closing_tag, FILTER_SANITIZE_NUMBER_INT );

			return $product_id;
		}

		/**
		 * Load required files and classes for frontend user waitlist
		 */
		private function load_user_waitlist_classes() {
			global $post;
			if ( $this->post_has_shortcode( $post, 'woocommerce_my_waitlist' ) ) {
				require_once 'account/class-pie-wcwl-frontend-user-waitlist.php';
				require_once 'account/class-pie-wcwl-frontend-shortcode.php';
				new Pie_WCWL_Frontend_Shortcode();
			}
			if ( is_account_page() && apply_filters( 'wcwl_enable_waitlist_account_tab', true ) ) {
				require_once 'account/class-pie-wcwl-frontend-user-waitlist.php';
				require_once 'account/class-pie-wcwl-frontend-account.php';
				new Pie_WCWL_Frontend_Account();
			}
			if ( 'yes' == get_option( 'woocommerce_waitlist_show_on_shop' ) && ( is_shop() || is_product_category() ) ) {
				$this->load_class( 'shop' );
			}
		}

		/**
		 * Load everything required for the product on the frontend
		 *
		 * @param $product_id
		 */
		private function initialise_waitlist_for_product( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product && array_key_exists( $product->get_type(), WooCommerce_Waitlist_Plugin::$allowed_product_types ) ) {
				$this->load_class( $product );
			}
		}

		/**
		 * Check requirements and run initialise if required
		 */
		public function quickview_init() {
			global $post;
			$product = wc_get_product( $post );
			if ( array_key_exists( $product->get_type(), WooCommerce_Waitlist_Plugin::$allowed_product_types ) ) {
				$this->load_class( $product );
				wp_enqueue_script( 'wcwl_frontend', WCWL_ENQUEUE_PATH . '/includes/js/wcwl_frontend.js', array(), WCWL_VERSION, true );
			}
		}

		/**
		 * Load required class for product type
		 */
		private function load_class( $product ) {
			require_once 'class-pie-wcwl-frontend-product.php';
			if ( 'shop' == $product ) {
				require_once 'product-types/class-pie-wcwl-frontend-shop.php';
				new Pie_WCWL_Frontend_Shop();
			} else {
				$class = WooCommerce_Waitlist_Plugin::$allowed_product_types[ $product->get_type() ];
				require_once $class['filepath'];
				new $class['class'];
			}
		}

		/**
		 * Email the site admin when a user joins a waitlist
		 *
		 * @param $product_id
		 * @param $user
		 *
		 * @since 1.8.0
		 */
		public function email_admin_user_joined_waitlist( $product_id, $user ) {
			$email = get_option( 'woocommerce_waitlist_admin_email' );
			if ( ! is_email( $email ) ) {
				$email = get_option( 'admin_email' );
			}
			$product = wc_get_product( $product_id );
			if ( $product ) {
				$subject = apply_filters( 'wcwl_admin_notification_email_subject', __( 'A user has just joined a waitlist!', 'woocommerce-waitlist' ) );
				$message = sprintf( __( '%s%s has just joined the waitlist for %s%s', 'woocommerce-waitlist' ), '<p>', $user->user_email, $product->get_name(), '</p>' );
				$message .= sprintf( __( '%sTo view or make adjustments to the waitlist %splease visit the edit screen for this product%s.%s', 'woocommerce-waitlist' ), '<p>', '<a href="' . get_edit_post_link( $product_id ) . '">', '</a>', '</p>' );
				add_filter( 'wp_mail_content_type', array( $this, 'set_email_content_type' ) );
				wp_mail( $email, $subject, apply_filters( 'wcwl_admin_notification_email_message', $message ) );
				remove_filter( 'wp_mail_content_type', array( $this, 'set_email_content_type' ) );
			}
		}

		/**
		 * Set our email to go out as HTML
		 *
		 * @return string
		 * @since  1.8.0
		 */
		public function set_email_content_type() {
			return "text/html";
		}
	}
}