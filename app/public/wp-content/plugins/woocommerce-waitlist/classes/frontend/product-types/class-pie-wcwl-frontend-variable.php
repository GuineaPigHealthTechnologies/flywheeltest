<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Variable' ) ) {
	/**
	 * Loads up the waitlist for variable products
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Variable extends Pie_WCWL_Frontend_Product {

		/**
		 * Pie_WCWL_Frontend_Variable constructor.
		 */
		public function __construct() {
			parent::__construct();
			$this->init();
		}

		/**
		 * Load up hooks if product is out of stock
		 */
		private function init() {
			if ( ! isset( $_POST['add-to-cart'] ) && $this->user_modified_waitlist ) {
				$this->process_waitlist_update();
			}
			$this->output_waitlist_elements();
		}

		/**
		 * If the user has attempted to modify the waitlist process the request
		 */
		private function process_waitlist_update() {
			$product = wc_get_product( $_REQUEST[ WCWL_SLUG ] );
			if ( ! $product ) {
				return;
			}
			$waitlist = $this->get_child_waitlist( $product );
			if ( ! $this->user ) {
				$this->handle_waitlist_when_new_user( $waitlist );
			} else {
				$this->toggle_waitlist_action( $waitlist );
			}
		}

		/**
		 * Check version of WC and output waitlist elements on appropriate hooks
		 */
		private function output_waitlist_elements() {
			add_filter( 'woocommerce_get_availability', array( $this, 'append_waitlist_message' ), 20, 2 );
			if ( $this->user ) {
				add_action( 'woocommerce_get_availability', array( $this, 'append_waitlist_control', ), 21, 2 );
				if ( 'yes' == get_option( 'woocommerce_waitlist_registered_user_opt-in' ) ) {
					add_filter( 'woocommerce_get_stock_html', array( $this, 'append_waitlist_optin' ), 20, 2 );
                }
			} else {
				add_filter( 'woocommerce_get_stock_html', array( $this, 'append_waitlist_control_if_user_unknown' ), 20, 2 );
			}
		}

		/**
		 * Appends the waitlist button HTML to text string
		 *
		 * @hooked   filter woocommerce_stock_html
		 *
		 * @param $array
		 * @param $product
		 *
		 * @return string HTML with waitlist button appended if product is out of stock
		 *
		 * @access   public
		 * @since    1.0
		 */
		public function append_waitlist_control( $array, $product ) {
			if ( $this->has_wpml ) {
				$product = wc_get_product( $this->get_main_product_id( $product->get_id() ) );
			}
			if ( ! $this->waitlist_is_enabled_for_product( $product->get_id() ) ) {
				return $array;
			}
			if ( ! $product->is_in_stock() ) {
				$waitlist = $this->get_child_waitlist( $product );
				if ( $waitlist ) {
					$array['availability'] .= '<div class="wcwl_frontend_wrap">';
					if ( ! $waitlist->user_is_registered( $this->user->ID ) ) {
						$array['availability'] .= $this->get_waitlist_control( 'join', $product );
					} else {
						$array['availability'] .= $this->get_waitlist_control( 'leave', $product );
					}
					$array['availability'] .= '</div>';
				}
			}

			return $array;
		}

		/**
		 * Append waitlist optin HTML to
		 * @param $string
		 * @param $product
		 *
		 * @return string
		 */
		public function append_waitlist_optin( $string, $product ) {
			if ( false !== strpos( $string, 'woocommerce_waitlist join' ) ) {
				$notice = apply_filters( 'wcwl_registered_user_opt-in_text', $this->registered_opt_in_text );
				$string = str_replace( '<div class="wcwl_control">', $this->get_waitlist_opt_in_html( $notice ) . '<div class="wcwl_control">', $string );
			}
			return $string;
		}

		/**
		 * This function modifies the string in place of the 'add to cart' option, adding in an email field when the user
		 * is not logged in.
		 *
		 * @param string $string current waitlist string
		 *
		 * @param        $product
		 *
		 * @return string $string modified string
		 *
		 * @access public
		 * @since  1.8.0
		 */
		public function append_waitlist_control_if_user_unknown( $string, $product ) {
			if ( ! $product->is_in_stock() ) {
				$string .= '<div class="wcwl_frontend_wrap">';
				if ( ! WooCommerce_Waitlist_Plugin::users_must_be_logged_in_to_join_waitlist() ) {
					$string .= $this->get_waitlist_email_field();
				}
				$string .= $this->get_waitlist_control( 'join', $product );
				$string .= '</div>';
			}

			return $string;
		}

		/**
		 * Get HTML for waitlist elements depending on product type
		 *
		 * @param string $context the context in which the button should be generated (join|leave)
		 * @param        $child
		 *
		 * @return string HTML for join waitlist button
		 * @access public
		 * @since  1.0
		 */
		private function get_waitlist_control( $context, $child ) {
			$child_id    = $child->get_id();
			$text_string = $context . '_waitlist_button_text';
			$classes     = implode( ' ', apply_filters( 'wcwl_' . $context . '_waitlist_button_classes', array( 'button', 'alt', WCWL_SLUG, $context, ) ) );
			$text        = apply_filters( 'wcwl_' . $context . '_waitlist_button_text', $this->$text_string );
			$url         = $this->create_button_url( $context, $child_id );

			return apply_filters( 'wcwl_' . $context . '_waitlist_button_html', '<div class="wcwl_control"><a href="' . esc_url( $url ) . '" class="' . esc_attr( $classes ) . '" data-id="' . $child_id . '" id="wcwl-product-' . esc_attr( $child_id ) . '">' . esc_html( $text ) . '</a></div>' );
		}

		/**
		 * Checks whether product is in stock and if not, appends the waitlist message of 'join/leave waitlist' to the 'out
		 * of stock' message
		 *
		 * @param array  $array   stock details
		 * @param object $product the current product
		 *
		 * @access public
		 * @return array
		 * @since  1.4.12
		 */
		public function append_waitlist_message( $array, $child ) {
			if ( $this->has_wpml ) {
				$child = wc_get_product( $this->get_main_product_id( $child->get_id() ) );
			}
			if ( ! $this->waitlist_is_enabled_for_product( $child->get_id() ) ) {
				return $array;
			}
			if ( ! $child->is_in_stock() ) {
				$waitlist = $this->get_child_waitlist( $child );
				if ( $waitlist ) {
					if ( ! $this->user || ! $waitlist->user_is_registered( $this->user->ID ) ) {
						$array['availability'] .= apply_filters( 'wcwl_join_waitlist_message_text', ' - ' . $this->join_waitlist_message_text );
					} else {
						$array['availability'] .= apply_filters( 'wcwl_leave_waitlist_message_text', ' - ' . $this->leave_waitlist_message_text );
					}
				}
			}

			return $array;
		}
	}
}