<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist' ) ) {
	/**
	 * Pie_WCWL_Waitlist
	 *
	 * @package WooCommerce Waitlist
	 */
	class Pie_WCWL_Waitlist {

		/**
		 * Array of user IDs on the current waitlist
		 *
		 * @var array
		 */
		public $waitlist;
		/**
		 * An array of user objects
		 *
		 * @var array
		 */
		public $users;
		/**
		 * Current product object
		 *
		 * @var WC_Product
		 */
		public $product;
		/**
		 * Product unique ID
		 *
		 * @var int
		 * @access public
		 */
		public $product_id;
		/**
		 * Array of the products parents. This could be variable/grouped or both
		 *
		 * @var array
		 * @access public
		 */
		public $parent_ids;

		/**
		 * Constructor function to hook up actions and filters and class properties
		 *
		 * @param $product
		 *
		 * @access   public
		 */
		public function __construct( $product ) {
			$this->product = $product;
			$this->setup_product_ids( $product );
			$this->setup_waitlist();
		}

		/**
		 * Setup product class variables
		 *
		 * @param $product
		 *
		 * @access   public
		 */
		public function setup_product_ids( $product ) {
			$this->product_id = $product->get_id();
			$this->parent_ids = Pie_WCWL_Compatibility::get_parent_id( $product );
		}

		/**
		 * Setup waitlist array
		 *
		 * Adjust old meta to new format ( $waitlist[user_id] = date_added )
		 *
		 * @access public
		 * @return void
		 */
		public function setup_waitlist() {
			$waitlist = get_post_meta( $this->product_id, WCWL_SLUG, true );
			if ( ! is_array( $waitlist ) || empty( $waitlist ) ) {
				$this->waitlist = array();
			} else {
				if ( $this->waitlist_has_new_meta() ) {
					$this->load_waitlist( $waitlist, 'new' );
				} else {
					$this->load_waitlist( $waitlist, 'old' );
				}
			}
		}

		/**
		 * Check if waitlist has been updated to the new meta format
		 *
		 * @return bool
		 */
		public function waitlist_has_new_meta() {
			$has_dates = get_post_meta( $this->product_id, WCWL_SLUG . '_has_dates', true );
			if ( $has_dates ) {
				return true;
			}

			return false;
		}

		/**
		 * Load up waitlist
		 *
		 * Meta has changed to incorporate the date added for each user so a check is required
		 * If waitlist has old meta we want to bring this up to speed
		 *
		 * @param $waitlist
		 * @param $type
		 */
		public function load_waitlist( $waitlist, $type ) {
			if ( 'old' == $type ) {
				foreach ( $waitlist as $user_id ) {
					$this->waitlist[ $user_id ] = 'unknown';
				}
			} else {
				$this->waitlist = $waitlist;
			}
		}

		/**
		 * Save the current waitlist into the database
		 *
		 * Update meta to notify us that meta format has been updated
		 *
		 * @return void
		 */
		public function save_waitlist() {
			update_post_meta( $this->product_id, WCWL_SLUG, $this->waitlist );
			update_post_meta( $this->product_id, WCWL_SLUG . '_has_dates', true );
		}

		/**
		 * For some bizarre reason around 1.2.0, this function has started emitting notices. It is caused by the original
		 * assignment of WCWL_Frontend_UI->User being set to false when a user is not logged in. All around the application,
		 * this is now being called on as an object.
		 *
		 * @param $user_id
		 *
		 * @return bool Whether or not the User is registered to this waitlist, if they are a valid user
		 *
		 * @access   public
		 */
		public function user_is_registered( $user_id ) {
			return $user_id && array_key_exists( $user_id, $this->waitlist );
		}

		/**
		 * Remove user from the current waitlist
		 *
		 * @param $user
		 *
		 * @return bool true|false depending on success of removal
		 *
		 * @access   public
		 */
		public function unregister_user( $user ) {
			if ( $this->user_is_registered( $user->ID ) ) {
				do_action( 'wcwl_before_remove_user_from_waitlist', $this->product_id, $user );
				unset( $this->waitlist[ $user->ID ] );
				do_action( 'wcwl_after_remove_user_from_waitlist', $this->product_id, $user );
				$this->save_waitlist();
				$this->update_waitlist_count( 'remove' );

				return true;
			}

			return false;
		}

		/**
		 * For some bizarre reason around 1.2.0, this function has started emitting notices. It is caused by the original
		 * assignment of WCWL_Frontend_UI->User being set to false when a user is not logged in. All around the application,
		 * this is now being called on as an object.
		 *
		 * @param $user
		 *
		 * @return bool
		 *
		 * @access   public
		 */
		public function register_user( $user ) {
			if ( $user && ! $this->user_is_registered( $user->ID ) ) {
				do_action( 'wcwl_before_add_user_to_waitlist', $this->product_id, $user );
				$this->waitlist[ $user->ID ] = strtotime( 'now' );
				do_action( 'wcwl_after_add_user_to_waitlist', $this->product_id, $user );
				$this->update_user_chosen_language_for_product( $user->ID );
				$this->save_waitlist();
				$this->update_waitlist_count( 'add' );

				return true;
			}

			return false;
		}

		/**
		 * Update the usermeta for the current user to show which language they joined this products waitlist in
		 *
		 * This is used to show the language of the user on the waitlist in the admin and to determine which language the waitlist email should be
		 *
		 * @param $user_id
		 */
		private function update_user_chosen_language_for_product( $user_id ) {
			if ( function_exists( 'wpml_get_current_language' ) ) {
				$waitlist_languages = get_user_meta( $user_id, 'wcwl_languages', true );
				if ( ! is_array( $waitlist_languages ) ) {
					$waitlist_languages = array();
				}
				$waitlist_languages[ $this->product_id ] = wpml_get_current_language();
				update_user_meta( $user_id, 'wcwl_languages', $waitlist_languages );
			}
		}

		/**
		 * Adjust waitlist count in database when a user is registered/unregistered
		 *
		 * @param $type
		 */
		private function update_waitlist_count( $type ) {
			update_post_meta( $this->product_id, '_' . WCWL_SLUG . '_count', count( $this->waitlist ) );
			if ( ! empty( $this->parent_ids ) ) {
				$this->update_parent_count( $type );
			}
		}

		/**
		 * Update waitlist counts for all parents of current product
		 */
		private function update_parent_count( $type ) {
			foreach ( $this->parent_ids as $parent_id ) {
				$count = get_post_meta( $parent_id, '_' . WCWL_SLUG . '_count', true );
				if ( 'add' == $type ) {
					$new_count = intval( $count ) + 1;
				} else {
					if ( $count < 1 ) {
						$new_count = 0;
					} else {
						$new_count = intval( $count ) - 1;
					}
				}
				update_post_meta( $parent_id, '_' . WCWL_SLUG . '_count', $new_count );
			}
		}

		/**
		 * Return an array of the users on the current waitlist
		 *
		 * @access public
		 * @return array user_ids
		 */
		public function get_registered_users() {
			$users = array();
			foreach ( $this->waitlist as $user_id => $timestamp ) {
				if ( false != get_user_by( 'id', $user_id ) ) {
					$users[] = get_user_by( 'id', $user_id );
				}
			}

			return $users;
		}

		/**
		 * Return an array of users emails from current waitlist
		 *
		 * @access public
		 * @return array user_emails
		 * @since  1.0.2
		 */
		public function get_registered_users_email_addresses() {
			return wp_list_pluck( $this->get_registered_users(), 'user_email' );
		}

		/**
		 * Triggers instock notification email to each user on the waitlist for a product
		 *
		 * @access public
		 * @return void
		 */
		public function waitlist_mailout() {
			if ( ! empty( $this->waitlist ) ) {
				global $woocommerce, $sitepress;
				if ( $sitepress ) {
					$this->check_translations_for_waitlist_entries( $this->product_id );
				}
				$woocommerce->mailer();
				$stock_level = $this->get_minimum_stock_level();
				foreach ( $this->waitlist as $user_id => $date_added ) {
					if ( ! $this->minimum_stock_requirement_met( $stock_level ) ) {
						continue;
					}
					$this->maybe_do_mailout( $user_id );
					$this->maybe_remove_user( $user_id );
				}
			}
		}

		/**
		 * Return minimum required stock level before we email waitlist users
		 *
		 * @return int
		 * @since  1.8.0
		 */
		private function get_minimum_stock_level() {
			$options = get_post_meta( $this->product_id, 'wcwl_options', true );
			if ( isset( $options['enable_stock_trigger'] ) && 'true' == $options['enable_stock_trigger'] && isset( $options['minimum_stock'] ) ) {
				return absint( $options['minimum_stock'] );
			} else {
				return absint( get_option( 'woocommece_waitlist_minimum_stock' ) );
			}
		}

		/**
		 * Check the minimum stock requirements are met for the current waitlist before processing mailouts
		 *
		 * @param $stock_level
		 *
		 * @return bool
		 * @since  1.8.0
		 */
		private function minimum_stock_requirement_met( $stock_level ) {
			if ( WooCommerce_Waitlist_Plugin::is_simple( $this->product ) && ! $this->product->get_manage_stock() ) {
				return true;
			}
			$product_stock = $this->product->get_stock_quantity();
			if ( WooCommerce_Waitlist_Plugin::is_variation( $this->product ) && ! $this->product->get_manage_stock() ) {
				$parent = wc_get_product( $this->parent_ids[0] );
				if ( ! $parent->get_manage_stock() ) {
					return true;
				} else {
					$product_stock = $parent->get_stock_quantity();
				}
			}
			if ( $product_stock >= $stock_level ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * If required, perform the waitlist mailout for the given user
		 *
		 * @param $user_id
		 */
		private function maybe_do_mailout( $user_id ) {
			if ( WooCommerce_Waitlist_Plugin::automatic_mailouts_are_disabled( $this->product_id ) ) {
				return;
			} elseif ( $this->user_has_been_emailed( $user_id, $this->product_id ) ) {
				return;
			}
			do_action( 'wcwl_mailout_send_email', $user_id, $this->product_id );
		}

		/**
		 * Check whether the user has just been mailed for this product
		 *
		 * @param $user_id
		 * @param $product_id
		 *
		 * @return mixed
		 */
		protected function user_has_been_emailed( $user_id, $product_id ) {
			return get_transient( 'wcwl_done_mailout_' . $user_id . '_' . $product_id );
		}

		/**
		 * If required, remove the given user from the current waitlist
		 *
		 * @param $user_id
		 */
		private function maybe_remove_user( $user_id ) {
			if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $this->product_id ) ) {
				$user = get_user_by( 'id', $user_id );
				$this->unregister_user( $user );
			}
		}

		/**
		 * Check that no translation products contain waitlist entries and log a notice if they do
		 *
		 * @param $product_id
		 */
		private function check_translations_for_waitlist_entries( $product_id ) {
			global $sitepress;
			$translated_products = $sitepress->get_element_translations( $product_id, 'post_product' );
			foreach ( $translated_products as $translated_product ) {
				if ( $product_id == $translated_product->element_id ) {
					continue;
				} else {
					$waitlist = get_post_meta( $translated_product->element_id, WCWL_SLUG, true );
					if ( is_array( $waitlist ) && ! empty( $waitlist ) ) {
						$logger = new WC_Logger();
						$logger->log( 'warning', sprintf( __( 'Woocommerce Waitlist data found for translated product %d (main product ID = %d)' ), $translated_product->element_id, $product_id ) );
						update_option( '_' . WCWL_SLUG . '_corrupt_data', true );
					}
				}
			}
		}
	}
}