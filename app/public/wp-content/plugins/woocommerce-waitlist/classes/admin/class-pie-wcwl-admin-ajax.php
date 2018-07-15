<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Admin_Ajax' ) ) {
	/**
	 * Class Pie_WCWL_Admin_Ajax
	 */
	class Pie_WCWL_Admin_Ajax {

		/**
		 * Initialise ajax class
		 */
		public function init() {
			$this->setup_text_strings();
			$this->load_ajax();
		}

		/**
		 * Hook up ajax
		 */
		public function load_ajax() {
			//Admin
			add_action( 'wp_ajax_wcwl_get_products', array( $this, 'get_all_products_ajax' ) );
			add_action( 'wp_ajax_wcwl_update_counts', array( $this, 'update_waitlist_counts_ajax' ) );
			add_action( 'wp_ajax_wcwl_update_meta', array( $this, 'update_waitlist_meta_ajax' ) );
			add_action( 'wp_ajax_wcwl_add_user_to_waitlist', array( $this, 'process_add_user_request_ajax' ) );
			add_action( 'wp_ajax_wcwl_remove_waitlist', array( $this, 'process_waitlist_remove_users_request_ajax' ) );
			add_action( 'wp_ajax_wcwl_email_instock', array( $this, 'process_send_instock_mail_request_ajax' ) );
			add_action( 'wp_ajax_wcwl_dismiss_archive_notice', array( $this, 'permanently_dismiss_archive_notice_for_user_ajax' ) );
			add_action( 'wp_ajax_wcwl_remove_archive', array( $this, 'process_archive_remove_users_request_ajax' ) );
			add_action( 'wp_ajax_wcwl_return_to_waitlist', array( $this, 'process_return_users_to_waitlist_request_ajax' ) );
			add_action( 'wp_ajax_wcwl_update_waitlist_options', array( $this, 'update_waitlist_options_ajax' ) );
			//Frontend
			add_action( 'wp_ajax_wcwl_user_remove_self_waitlist', array( $this, 'remove_user_from_waitlist' ) );
			add_action( 'wp_ajax_nopriv_wcwl_user_remove_self_waitlist', array( $this, 'remove_user_from_waitlist' ) );
			add_action( 'wp_ajax_wcwl_user_remove_self_archives', array( $this, 'remove_user_from_archives' ) );
			add_action( 'wp_ajax_nopriv_wcwl_user_remove_self_archives', array( $this, 'remove_user_from_archives' ) );
		}

		/**
		 * Return all product IDs
		 */
		public function get_all_products_ajax() {
			if ( ! wp_verify_nonce( $_POST['wcwl_get_products'], 'wcwl-ajax-get-products-nonce' ) ) {
				die( $this->nonce_not_verified_text );
			}
			$products = WooCommerce_Waitlist_Plugin::return_all_product_ids();
			echo json_encode( $products );
			die();
		}

		/**
		 * Update waitlists for the given products - 10 at a time
		 */
		public function update_waitlist_counts_ajax() {
			if ( ! wp_verify_nonce( $_POST['wcwl_update_counts'], 'wcwl-ajax-update-counts-nonce' ) ) {
				die( $this->nonce_not_verified_text );
			}
			$products = $_POST['products'];
			foreach ( $products as $product ) {
				$count = $this->get_waitlist_count( absint( $product ) );
				echo sprintf( __( 'Product %d - count updated to %d | ', 'woocommerce-waitlist' ), $product, $count );
			}
			update_option( '_' . WCWL_SLUG . '_counts_updated', true );
			die();
		}

		/**
		 * Return number of users on requested waitlist and update meta so it can be quickly retrieved in the future
		 *
		 * @param  int $product product ID
		 *
		 * @access private
		 * @static
		 * @return int
		 */
		private function get_waitlist_count( $product ) {
			$product  = wc_get_product( $product );
			$waitlist = array();
			if ( $product->has_child() ) {
				foreach ( $product->get_children() as $child_id ) {
					$current_waitlist = get_post_meta( $child_id, WCWL_SLUG, true );
					$current_waitlist = is_array( $current_waitlist ) ? $current_waitlist : array();
					$waitlist         = array_merge( $waitlist, $current_waitlist );
				}
			} else {
				$waitlist = get_post_meta( $product->get_id(), WCWL_SLUG, true );
			}
			$count = empty( $waitlist ) ? 0 : count( $waitlist );
			update_post_meta( $product->get_id(), '_' . WCWL_SLUG . '_count', $count );
			delete_post_meta( $product->get_id(), WCWL_SLUG . '_count' );

			return $count;
		}

		/**
		 * Update all metadata relating to waitlists
		 */
		public function update_waitlist_meta_ajax() {
			if ( ! wp_verify_nonce( $_POST['wcwl_update_meta'], 'wcwl-ajax-update-meta-nonce' ) ) {
				die( $this->nonce_not_verified_text );
			}
			$products = $_POST['products'];
			foreach ( $products as $product ) {
				$product_id = absint( $product );
				$archives   = get_post_meta( $product_id, 'wcwl_waitlist_archive', true );
				self::fix_multiple_entries_for_days( $archives, $product_id );
				$product  = wc_get_product( $product_id );
				$waitlist = new Pie_WCWL_Waitlist( $product );
				$waitlist->save_waitlist();
				echo sprintf( __( 'Meta updated for Product %d | ', 'woocommerce-waitlist' ), $product->get_id() );
			}
			update_option( '_' . WCWL_SLUG . '_metadata_updated', true );
			die();
		}

		/**
		 * Fix any duplicate entries for certain days when displaying the waitlist archives
		 * We check for the old timestamp as array key. If meta is old we adjust it over to the new dates
		 * Update meta afterwards to make sure everything remains updated
		 *
		 * @param $archives
		 * @param $product_id
		 *
		 * @return array
		 */
		public static function fix_multiple_entries_for_days( $archives, $product_id ) {
			$updated_archives = array();
			foreach ( $archives as $date => $archive ) {
				$date = strtotime( date( "Ymd", $date ) );
				if ( ! empty( $archive ) ) {
					foreach ( $archive as $user_id ) {
						$updated_archives[ $date ][ $user_id ] = $user_id;
					}
					$updated_archives[ $date ] = array_unique( $updated_archives[ $date ] );
				}
			}
			krsort( $updated_archives );
			update_post_meta( $product_id, 'wcwl_waitlist_archive', $updated_archives );

			return $updated_archives;
		}

		/**
		 * Handle the request to add user to waitlist
		 */
		public function process_add_user_request_ajax() {
			$this->verify_nonce( $_POST['wcwl_add_user_nonce'], 'wcwl-add-user-nonce' );
			$product  = $this->setup_product( absint( $_POST['product_id'] ) );
			$waitlist = new Pie_WCWL_Waitlist( $product );
			$emails   = $this->organise_emails( $_POST['emails'] );
			$users    = array();
			foreach ( $emails as $email ) {
				$user = get_user_by( 'id', WooCommerce_Waitlist_Plugin::create_new_customer_from_email( $email ) );
				$waitlist->register_user( $user );
				$users[] = $this->generate_required_userdata( $user, 'waitlist' );
			}
			die( $this->generate_response( 'success', __( 'The waitlist has been updated', 'woocommerce-waitlist' ), $users ) );
		}

		/**
		 * Process the given emails to add user to the waitlist
		 *
		 * @param $emails
		 *
		 * @return array
		 */
		public function organise_emails( $emails ) {
			$processed_emails = array();
			if ( is_array( $emails ) ) {
				foreach ( $emails as $email ) {
					$processed_emails[] = sanitize_email( $email );
				}
			} else {
				$processed_emails[] = sanitize_email( $emails );
			}

			return $processed_emails;
		}

		/**
		 * Return users from the archive to the waitlist
		 */
		public function process_return_users_to_waitlist_request_ajax() {
			$this->verify_action_request();
			$product  = $this->setup_product( absint( $_POST['product_id'] ) );
			$waitlist = new Pie_WCWL_Waitlist( $product );
			$users    = array();
			foreach ( $_POST['users'] as $user ) {
				if ( $user ) {
					$user_object = get_user_by( 'id', absint( $user['id'] ) );
					$waitlist->register_user( $user_object );
					$users[] = $this->generate_required_userdata( $user_object, 'waitlist' );
				}
			}
			if ( count( $_POST['users'] ) > 1 ) {
				die( $this->generate_response( 'success', __( 'The selected users have been added to the waitlist', 'woocommerce-waitlist' ), $users ) );
			} else {
				die( $this->generate_response( 'success', __( 'The selected user has been added to the waitlist', 'woocommerce-waitlist' ), $users ) );
			}
		}

		/**
		 * Handle the request to remove users from the waitlist
		 */
		public function process_waitlist_remove_users_request_ajax() {
			$this->verify_action_request();
			$product  = $this->setup_product( absint( $_POST['product_id'] ) );
			$waitlist = new Pie_WCWL_Waitlist( $product );
			foreach ( $_POST['users'] as $user ) {
				$user_object = get_user_by( 'id', absint( $user['id'] ) );
				$response    = $waitlist->unregister_user( $user_object );
				if ( ! $response ) {
					die( $this->generate_response( 'error', sprintf( __( 'There was an error when trying to remove %s from the waitlist', 'woocommerce-waitlist' ), $user->user_email ) ) );
				}
			}
			if ( count( $_POST['users'] ) > 1 ) {
				die( $this->generate_response( 'success', __( 'The selected users have been removed from the waitlist', 'woocommerce-waitlist' ), $_POST['users'] ) );
			} else {
				die( $this->generate_response( 'success', __( 'The selected user has been removed from the waitlist', 'woocommerce-waitlist' ), $_POST['users'] ) );
			}
		}

		/**
		 * Handle the request to email in stock notifications to given users
		 */
		public function process_send_instock_mail_request_ajax() {
			$this->verify_action_request();
			$product = $this->setup_product( absint( $_POST['product_id'] ) );
			$users   = array();
			foreach ( $_POST['users'] as $user ) {
				WC_Emails::instance();
				$user_id = absint( $user['id'] );
				do_action( 'wcwl_mailout_send_email', $user_id, $product->get_id(), true );
				$user_object = get_user_by( 'id', $user_id );
				$users[]     = $this->generate_required_userdata( $user_object, 'archive' );
			}
			die( $this->generate_response( 'success', __( 'The selected users have been sent an in stock notification', 'woocommerce-waitlist' ), $users ) );
		}

		/**
		 * Remove selected users from given archive
		 */
		public function process_archive_remove_users_request_ajax() {
			$this->verify_action_request();
			$product_id = absint( $_POST['product_id'] );
			$archive    = get_post_meta( $product_id, 'wcwl_waitlist_archive', true );
			foreach ( $_POST['users'] as $user ) {
				$user_id = absint( $user['id'] );
				$date    = absint( $user['date'] );
				if ( ! $user_id ) {
					$key = array_search( $user_id, $archive[ $date ] );
					unset( $archive[ $date ][ $key ] );
				} else {
					unset( $archive[ $date ][ $user_id ] );
					if ( empty( $archive[ $date ] ) ) {
						unset( $archive[ $date ] );
					}
				}
			}
			update_post_meta( $product_id, 'wcwl_waitlist_archive', $archive );
			die( $this->generate_response( 'success', __( 'Selected users have been removed', 'woocommerce-waitlist' ), $_POST['users'] ) );
		}

		/**
		 * Update waitlist options
		 */
		public function update_waitlist_options_ajax() {
			$this->verify_nonce( $_POST['wcwl_update_nonce'], 'wcwl-update-nonce' );
			if ( is_array( $_POST['options'] ) ) {
				update_post_meta( absint( $_POST['product_id'] ), 'wcwl_options', $_POST['options'] );
				die( $this->generate_response( 'success', __( 'Waitlist options have been updated for this product', 'woocommerce-waitlist' ) ) );
			} else {
				die( $this->generate_response( 'error', __( 'Something went wrong with your request. Options not recognised', 'woocommerce-waitlist' ) ) );
			}
		}

		/**
		 * Verify request is valid by checking posted users and nonce
		 */
		private function verify_action_request() {
			$this->verify_nonce( $_POST['wcwl_action_nonce'], 'wcwl-action-nonce' );
			if ( ! isset( $_POST['users'] ) || empty( $_POST['users'] ) ) {
				die( $this->generate_response( 'error', __( 'No users selected', 'woocommerce-waitlist' ) ) );
			}
		}

		/**
		 * Retrieve the product from the given ID and output an error notice if not found
		 *
		 * @param $product_id
		 *
		 * @return false|null|WC_Product
		 */
		private function setup_product( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				die( $this->generate_response( 'error', __( 'Invalid product ID', 'woocommerce-waitlist' ) ) );
			}

			return $product;
		}

		/**
		 * Verify the given nonce is valid and output error message if not
		 *
		 * @param $nonce
		 * @param $nonce_name
		 *
		 * @return bool
		 */
		private function verify_nonce( $nonce, $nonce_name ) {
			if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
				die( $this->generate_response( 'error', $this->nonce_not_verified_text ) );
			}

			return true;
		}

		/**
		 * Gather required information for user
		 *
		 * @param $user
		 * @param $table
		 *
		 * @return array
		 */
		private function generate_required_userdata( $user, $table ) {
			if ( $user ) {
				$data = array(
					'id'        => $user->ID,
					'link'      => get_edit_user_link( $user->ID ),
					'email'     => $user->user_email,
					'join_date' => date( 'd M, y' ),
				);
			} else {
				$data = array(
					'id'        => 0,
					'link'      => '',
					'email'     => '',
					'join_date' => '',
				);
			}
			if ( 'archive' == $table ) {
				$data['date'] = strtotime( date( 'Ymd' ) );
			}

			return $data;
		}

		/**
		 * Generate a meaningful response to easily handle the ajax request
		 *
		 * @param        $type
		 * @param        $message
		 * @param array  $users
		 *
		 * @return mixed|string|void
		 */
		private function generate_response( $type, $message, $users = array() ) {
			$data = array(
				'type'    => $type,
				'message' => $message,
			);
			if ( 'success' == $type ) {
				$data['users'] = $users;
			}

			return json_encode( $data );
		}

		/**
		 * Process ajax request for user removing themselves from a waitlist
		 */
		public function remove_user_from_waitlist() {
			if ( ! wp_verify_nonce( $_POST['wcwl_remove_user_nonce'], 'wcwl-ajax-remove-user-nonce' ) ) {
				die( 'invalid nonce' );
			}
			$product = wc_get_product( absint( $_POST['product_id'] ) );
			$user    = get_user_by( 'id', absint( $_POST['user_id'] ) );
			if ( ! $product ) {
				die( 'invalid product' );
			}
			if ( ! $user ) {
				die( 'invalid user' );
			}
			$waitlist = new Pie_WCWL_Waitlist( $product );
			$waitlist->unregister_user( $user );
			die( 'user removed from product ' . $product->get_id() );
		}

		/**
		 * Process ajax request for user removing themself from all archives
		 */
		public function remove_user_from_archives() {
			if ( ! wp_verify_nonce( $_POST['wcwl_remove_user_archive_nonce'], 'wcwl-ajax-remove-user-archive-nonce' ) ) {
				die( 'invalid nonce' );
			}
			$user_id = absint( $_POST['user_id'] );
			if ( ! $user_id ) {
				die( 'invalid user' );
			}
			$archives = WooCommerce_Waitlist_Plugin::get_waitlist_archives_by_user_id( $user_id );
			WooCommerce_Waitlist_Plugin::remove_user_from_archives( $archives, $user_id );
			die( 'user removed from archives' );
		}

		/**
		 * Required text for ajax requests
		 */
		private function setup_text_strings() {
			$this->nonce_not_verified_text = __( 'Nonce Not Verified', 'woocommerce-waitlist' );
		}
	}
}
