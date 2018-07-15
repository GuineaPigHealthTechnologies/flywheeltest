<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Grouped' ) ) {
	/**
	 * Loads up the waitlist for grouped products
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Grouped extends Pie_WCWL_Frontend_Product {

		/**
		 * Does this grouped product contain any waitlist enabled products?
		 *
		 * @var bool
		 */
		public $has_waitlist_enabled_products = false;

		/**
		 * Pie_WCWL_Frontend_Grouped constructor.
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
			if ( ! $this->user ) {
				if ( $this->new_user_can_join_waitlist() ) {
					$this->toggle_waitlist_action_grouped();
				}
				$this->user = false;
			} else {
				$this->toggle_waitlist_action_grouped();
			}
		}

		/**
		 * Run the process to update the required product waitlists
		 */
		private function toggle_waitlist_action_grouped() {
			if ( isset( $_REQUEST['wcwl_join'] ) && ! empty( $_REQUEST['wcwl_join'] ) ) {
				$this->process_user_join_waitlists( explode( ',', $_REQUEST['wcwl_join'] ) );
			}
			if ( isset( $_REQUEST['wcwl_leave'] ) && ! empty( $_REQUEST['wcwl_leave'] ) ) {
				$this->process_user_leave_waitlists( explode( ',', $_REQUEST['wcwl_leave'] ) );
			}
			wc_add_notice( apply_filters( 'wcwl_grouped_product_joined_message_text', $this->grouped_product_joined_message_text ), 'success' );
		}

		/**
		 * Add user to required products
		 *
		 * @param $products
		 */
		private function process_user_join_waitlists( $products ) {
			foreach ( $products as $product_id ) {
				$waitlist = new Pie_WCWL_Waitlist( wc_get_product( absint( $product_id ) ) );
				$waitlist->register_user( $this->user );
			}
		}

		/**
		 * Remove user from required products
		 *
		 * @param $products
		 */
		private function process_user_leave_waitlists( $products ) {
			foreach ( $products as $product_id ) {
				$waitlist = new Pie_WCWL_Waitlist( wc_get_product( absint( $product_id ) ) );
				$waitlist->unregister_user( $this->user );
			}
		}

		/**
		 * Check if grouped product has out of stock child products
		 *
		 * @return bool
		 */
		private function has_out_of_stock_children() {
			foreach ( $this->product->get_children() as $child ) {
				$child = wc_get_product( $child );
				if ( ! $child->is_in_stock() ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Check version of WC and output waitlist elements on appropriate hooks
		 */
		private function output_waitlist_elements() {
			if ( $this->has_out_of_stock_children() ) {
				add_filter( 'woocommerce_get_stock_html', array( $this, 'append_checkboxes', ), 20 );
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'output_waitlist_message', ) );
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'output_waitlist_control' ), 20 );
				add_action( 'wp_print_styles', array( $this, 'print_grouped_product_style_block' ) );
			}
		}

		/**
		 * Appends the waitlist button HTML to text string
		 *
		 * A new waitlist object is instantiated for each child product to ensure updates are shown on page reload
		 *
		 * @hooked   filter woocommerce_stock_html
		 *
		 * @param $string
		 *
		 * @return string HTML with waitlist button appended if product is out of stock
		 *
		 * @access   public
		 * @since    1.0
		 */
		public function append_checkboxes( $string ) {
			if ( false === strpos( $string, 'out-of-stock' ) ) {
				return $string;
			}
			global $product;
			if ( WooCommerce_Waitlist_Plugin::is_variable( $product ) ) {
				return $string;
			}
			if ( $this->has_wpml ) {
				$product = wc_get_product( $this->get_main_product_id( $product->get_id() ) );
			}
			if ( ! $this->waitlist_is_enabled_for_product( $product->get_id() ) ) {
				return $string;
			}
			$waitlist = new Pie_WCWL_Waitlist( $product );
			if ( $this->user && $waitlist->user_is_registered( $this->user->ID ) ) {
				$context = 'leave';
				$checked = 'checked';
			} else {
				$context = 'join';
				$checked = '';
			}
			$string                              = '<p class="stock out-of-stock">' . __( 'Out of stock ', 'woocommerce-waitlist' ) . '<label class="' . WCWL_SLUG . '_label" > - ' . apply_filters( 'wcwl_' . $context . '_waitlist_button_text', $this->join_waitlist_button_text ) . '<input id="wcwl_checked_' . $product->get_id() . '" class="wcwl_checkbox" type="checkbox" name="' . ( 'join' == $context ? $context : WCWL_SLUG . '_product_id' . '[]' ) . '" ' . $checked . '/></label></p>';
			$this->has_waitlist_enabled_products = true;

			return $string;
		}

		/**
		 * Outputs the appropriate Grouped Product message HTML
		 *
		 * @hooked action woocommerce_after_add_to_cart_form
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function output_waitlist_message() {
			if ( ! $this->has_waitlist_enabled_products ) {
				return;
			}
			$classes = implode( ' ', apply_filters( 'wcwl_grouped_product_message_classes', array(
				'out-of-stock',
				WCWL_SLUG,
			) ) );
			if ( $this->user ) {
				$text = apply_filters( 'wcwl_grouped_product_message_registered_user_text', $this->grouped_product_message_text );
			} else {
				$text = apply_filters( 'wcwl_grouped_product_message_unregistered_user_text', $this->no_user_grouped_product_message_text );
			}
			echo apply_filters( 'wcwl_grouped_product_message_html', '<p class="' . esc_attr( $classes ) . '">' . $text . '</p>' );
		}

		/**
		 * This function modifies the string in place of the 'add to cart' option, adding in an email field when the user
		 * is not logged in.
		 *
		 * @access public
		 * @return string $string modified string
		 *
		 * @since  1.3
		 */
		public function output_waitlist_control() {
			if ( ! $this->has_waitlist_enabled_products ) {
				return;
			}
			$string = '<div class="wcwl_frontend_wrap">';
			if ( ! $this->user && WooCommerce_Waitlist_Plugin::users_must_be_logged_in_to_join_waitlist() ) {
				$string .= $this->get_waitlist_control( 'join' );
			} elseif ( ! $this->user ) {
				$string .= $this->get_waitlist_email_field();
				$string .= $this->get_waitlist_control( 'join' );
			} else {
				if ( 'yes' == get_option( 'woocommerce_waitlist_registered_user_opt-in' ) ) {
					$notice = apply_filters( 'wcwl_registered_user_opt-in_text', $this->registered_opt_in_text );
					$string .= $this->get_waitlist_opt_in_html( $notice );
				}
				$string .= $this->get_waitlist_control( 'update' );
			}
			$string .= '</div>';
			echo $string;
		}

		/**
		 * Get HTML for waitlist elements depending on product type
		 *
		 * @param string $context the context in which the button should be generated (join|leave)
		 *
		 * @return string HTML for join waitlist button
		 * @access public
		 * @since  1.0
		 */
		private function get_waitlist_control( $context ) {
			$product_id  = $this->product->get_id();
			$text_string = $context . '_waitlist_button_text';
			$classes     = implode( ' ', apply_filters( 'wcwl_' . $context . '_waitlist_button_classes', array( 'button', 'alt', WCWL_SLUG, $context, ) ) );
			$text        = apply_filters( 'wcwl_' . $context . '_waitlist_button_text', $this->$text_string );
			$url         = $this->create_button_url( $context, $product_id );

			return apply_filters( 'wcwl_' . $context . '_waitlist_submit_button_html', '<div class="wcwl_control"><a href="' . esc_url( $url ) . '" class="' . esc_attr( $classes ) . '" data-id="' . $product_id . '" id="wcwl-product-' . esc_attr( $product_id ) . '">' . esc_html( $text ) . '</a></div>' );
		}

		/**
		 * Output style block for class "group_table" on Grouped Product
		 *
		 * @hooked action wp_print_styles
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function print_grouped_product_style_block() {
			$css = apply_filters( 'wcwl_grouped_product_style_block_css', 'p.' . WCWL_SLUG . '{padding-top:20px;clear:both;margin-bottom:10px;}' );
			echo apply_filters( 'wcwl_grouped_product_style_block', '<style type="text/css">' . $css . '</style>' );
		}
	}
}