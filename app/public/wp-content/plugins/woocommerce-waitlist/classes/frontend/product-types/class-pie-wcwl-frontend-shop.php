<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Shop' ) ) {
	/**
	 * Loads up the waitlist for shop page
	 *
	 * @package  WooCommerce Waitlist
	 * @since    1.8.0
	 */
	class Pie_WCWL_Frontend_Shop extends Pie_WCWL_Frontend_Product {

		/**
		 * Pie_WCWL_Frontend_Simple constructor.
		 *
		 * @since  1.8.0
		 */
		public function __construct() {
			parent::__construct();
			$this->init();
		}

		/**
		 * Load up hooks if product is out of stock
		 *
		 * @since  1.8.0
		 */
		private function init() {
			if ( ! isset( $_POST['add-to-cart'] ) && $this->user_modified_waitlist ) {
				$this->process_waitlist_update();
			}
			$this->output_waitlist_elements();
		}

		/**
		 * If the user has attempted to modify the waitlist process the request
		 *
		 * @since  1.8.0
		 */
		private function process_waitlist_update() {
			if ( ! $this->user ) {
				$this->handle_single_waitlist_when_new_user();
			} else {
				$this->toggle_single_waitlist_action();
			}
		}

		/**
		 * Toggle waitlist action for new user
		 *
		 * @since 1.8.0
		 */
		private function handle_single_waitlist_when_new_user() {
			$product = wc_get_product( absint( $_GET[ WCWL_SLUG ] ) );
			if ( $product ) {
				$this->setup_waitlist( $product->get_id() );
				$this->handle_waitlist_when_new_user( $this->product->waitlist );
			}
		}

		/**
		 * Toggle waitlist action for single product on shop page
		 *
		 * @since  1.8.0
		 */
		private function toggle_single_waitlist_action() {
			$product = wc_get_product( absint( $_GET[ WCWL_SLUG ] ) );
			if ( $product ) {
				$waitlist = new Pie_WCWL_Waitlist( $product );
				$this->toggle_waitlist_action( $waitlist );
			}
		}

		/**
		 * Output waitlist elements on appropriate hooks
		 *
		 * @since  1.8.0
		 */
		private function output_waitlist_elements() {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'append_waitlist_control' ), 15 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'output_waitlist_email_field' ) );
		}

		/**
		 * Appends the waitlist button HTML to text string
		 *
		 * @access public
		 * @return string HTML with waitlist button appended if product is out of stock
		 * @since  1.8.0
		 */
		public function append_waitlist_control() {
			global $post;
			$product = wc_get_product( $post );
			if ( $product && WooCommerce_Waitlist_Plugin::is_simple( $product ) &&
			     ! $product->is_in_stock() &&
				 $this->waitlist_is_enabled_for_product( $product->get_id() ) ) {
				$this->setup_waitlist( $product->get_id() );
				$product_id = $this->product->get_id();
				if ( ! $this->user ) {
					$string = $this->append_waitlist_control_for_logged_out_user( $product_id );
				} else {
					$string = $this->append_waitlist_control_for_logged_in_user( $product_id );
				}
				echo $string;
			}
		}

		/**
		 * Return HTML to display waitlist elements when user is logged in
		 *
		 * @param $product_id
		 *
		 * @return string
		 * @since 1.8.0
		 */
		public function append_waitlist_control_for_logged_in_user( $product_id ) {
			if ( 'yes' == get_option( 'woocommerce_waitlist_registered_user_opt-in' ) ) {
				$string = $this->get_control_html_when_optin_enabled( $product_id );
			} else {
				$string = $this->get_control_html_when_optin_disabled( $product_id );
			}

			return $string;
		}

		/**
		 * Get HTML for waitlist control when option for optin for logged in users is enabled
		 *
		 * @param $product_id
		 *
		 * @return string
		 */
		protected function get_control_html_when_optin_enabled( $product_id ) {
			if ( ! $this->product->waitlist->user_is_registered( $this->user->ID ) ) {
				$string = '<div class="wcwl_frontend_wrap">';
				$notice = apply_filters( 'wcwl_registered_user_opt-in_text', $this->registered_opt_in_text );
				$string .= $this->get_waitlist_opt_in_html( $notice );
				$string .= $this->get_waitlist_control( 'join', $product_id );
				$string .= '</div>';
				$string .= $this->get_waitlist_toggle_button( 'join' );
			} else {
				$string = $this->get_waitlist_control( 'leave', $product_id, false );
			}

			return $string;
		}

		/**
		 * Get HTML for waitlist control when option for optin for logged in users is disabled
		 *
		 * @param $product_id
		 *
		 * @return string
		 */
		protected function get_control_html_when_optin_disabled( $product_id ) {
			if ( ! $this->product->waitlist->user_is_registered( $this->user->ID ) ) {
				$string = $this->get_waitlist_control( 'join', $product_id, false );
			} else {
				$string = $this->get_waitlist_control( 'leave', $product_id, false );
			}

			return $string;
		}

		/**
		 * Return HTML to display waitlist elements when user is logged out
		 *
		 * @param $product_id
		 *
		 * @return string
		 * @since 1.8.0
		 */
		public function append_waitlist_control_for_logged_out_user( $product_id ) {
			if ( WooCommerce_Waitlist_Plugin::users_must_be_logged_in_to_join_waitlist() ) {
				$string = $this->get_waitlist_control( 'join', $product_id, false );
			} else {
				$string = '<div class="wcwl_frontend_wrap">';
				$string .= $this->get_waitlist_elements_for_logged_out_user( $product_id );
				$string .= '</div>';
				$string .= $this->get_waitlist_toggle_button( 'join' );
			}

			return $string;
		}

		/**
		 * Return HTML for a button to toggle the display of waitlist elements
		 *
		 * @return string
		 */
		public function get_waitlist_toggle_button( $context ) {
			$html = '<a href="#" class="button alt wcwl_toggle_email">';
			if ( 'join' === $context ) {
				$html .= apply_filters( 'wcwl_join_waitlist_button_text', $this->join_waitlist_button_text );
			} else {
				$html .= apply_filters( 'wcwl_leave_waitlist_button_text', $this->leave_waitlist_button_text );
			}
			$html .= '</a>';

			return $html;
		}

		/**
		 * Appends the email input field and waitlist button HTML to text string for simple products
		 *
		 * @access public
		 * @return string HTML with email field and waitlist button appended
		 * @since  1.8.0
		 */
		private function get_waitlist_elements_for_logged_out_user( $product_id ) {
			$url    = $this->create_button_url( 'join', $product_id );
			$string = '<form name="wcwl_add_user_form" action="' . esc_url( $url ) . '" method="post">';
			$string .= $this->get_waitlist_control( 'join', $product_id ) . '</form>';

			return $string;
		}

		/**
		 * Get HTML for waitlist elements depending on product type
		 *
		 * @param string $context    the context in which the button should be generated (join|leave)
		 * @param        $product_id
		 * @param bool   $is_confirm whether this is a "confirm" button or not
		 *
		 * @return string HTML for join waitlist button
		 * @internal param string $url
		 *
		 * @access   public
		 * @since    1.8.0
		 */
		private function get_waitlist_control( $context, $product_id, $is_confirm = true ) {
			$url = $this->create_button_url( $context, $product_id );
			if ( $is_confirm ) {
				$context = 'confirm';
			}
			$text_string = $context . '_waitlist_button_text';
			$classes     = implode( ' ', apply_filters( 'wcwl_' . $context . '_waitlist_button_classes', array( 'button', 'alt', WCWL_SLUG, $context, ) ) );
			$text        = apply_filters( 'wcwl_' . $context . '_waitlist_button_text', $this->$text_string );

			return apply_filters( 'wcwl_' . $context . '_waitlist_button_html', '<div class="wcwl_control"><a href="' . esc_url( $url ) . '" class="' . esc_attr( $classes ) . '" data-id="' . $product_id . '" id="wcwl-product-' . esc_attr( $product_id ) . '">' . esc_html( $text ) . '</a></div>' );
		}

		/**
		 * Output a single email field for use on the shop page
		 */
		public function output_waitlist_email_field() {
			echo $this->get_waitlist_email_field();
		}
	}
}