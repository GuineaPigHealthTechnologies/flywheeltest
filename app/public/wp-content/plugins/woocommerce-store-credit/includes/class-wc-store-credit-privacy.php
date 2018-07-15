<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Store_Credit_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Store Credit', 'woocommerce-store-credit' ) );

		$this->add_exporter( 'woocommerce-store-credit-coupon-data', __( 'WooCommerce Store Credit Coupon Data', 'woocommerce-store-credit' ), array( $this, 'coupon_data_exporter' ) );

		$this->add_eraser( 'woocommerce-store-credit-coupon-shipment-tracking-order-data', __( 'WooCommerce Store Credit Coupon Data', 'woocommerce-store-credit' ), array( $this, 'coupon_data_eraser' ) );

		add_filter( 'woocommerce_get_settings_account', array( $this, 'account_settings' ) );
	}

	/**
	 * Add retention settings to account tab.
	 *
	 * @param array $settings
	 * @return array $settings Updated
	 */
	public function account_settings( $settings ) {
		$insert_setting = array(
			array(
				'title'       => __( 'Retain store credit coupons', 'woocommerce-store-credit' ),
				'desc_tip'    => __( 'Store credit that are stored for customers via coupons. If erased, the customer will not be able to use the coupons.', 'woocommerce-store-credit' ),
				'id'          => 'woocommerce_store_credit_coupons_retention',
				'type'        => 'relative_date_selector',
				'placeholder' => __( 'N/A', 'woocommerce-store-credit' ),
				'default'     => '',
				'autoload'    => false,
			),
		);

		array_splice( $settings, ( count( $settings ) - 1 ), 0, $insert_setting );

		return $settings;
	}

	/**
	 * Returns a list of coupons based on email.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WP_Post
	 */
	protected function get_coupons( $email_address, $page ) {
		$posts = array(
			'posts_per_page' => 10,
			'offset'         => $page,
			'post_type'      => 'shop_coupon',
			'meta_query'     => array(
				array(
					'key'   => 'discount_type',
					'value' => 'store_credit',
				),
			),
		);	

		$posts = get_posts( $posts );

		$data = array();

		foreach ( $posts as $post ) {
			$emails = get_post_meta( $post->ID, 'customer_email', true );

			if ( ! empty( $emails ) && in_array( $email_address, $emails ) ) {
				$data[] = array(
					'id'          => $post->ID,
					'coupon_name' => $post->post_title,
					'email'       => $email_address,
					'post_date'   => $post->post_date,
				);
			};
		}

		return $data;
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-store-credit' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-store-credit' ) );
	}

	/**
	 * Handle exporting data for coupons.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function coupon_data_exporter( $email_address, $page = 0 ) {
		$done           = false;
		$data_to_export = array();

		$coupons = $this->get_coupons( $email_address, (int) $page );
		$done = true;

		if ( 0 < count( $coupons ) ) {
			foreach ( $coupons as $coupon ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_coupons',
					'group_label' => __( 'Coupons', 'woocommerce-store-credit' ),
					'item_id'     => 'coupon-' . $coupon['id'],
					'data'        => array(
						array(
							'name'  => sprintf( __( 'Store credit coupon email %s', 'woocommerce-store-credit' ), $coupon['coupon_name'] ),
							'value' => $coupon['email'],
						),
					),
				);
			}

			$done = 10 > count( $coupons );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function coupon_data_eraser( $email_address, $page ) {
		$coupons = $this->get_coupons( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( $coupons as $coupon ) {
			list( $removed, $retained, $msgs ) = $this->maybe_handle_coupon( $coupon );
			$items_removed  |= $removed;
			$items_retained |= $retained;
			$messages        = array_merge( $messages, $msgs );
		}

		// Tell core if we have more coupons to work on still
		$done = count( $coupons ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Checks if create date is passed retention duration.
	 *
	 */
	public function is_retention_expired( $created_date ) {
		$retention  = wc_parse_relative_date_option( get_option( 'woocommerce_store_credit_coupons_retention' ) );
		$is_expired = false;
		$time_span  = time() - strtotime( $created_date );

		if ( empty( $retention ) || empty( $created_date ) ) {
			return false;
		}

		switch ( $retention['unit'] ) {
			case 'days':
				$retention = $retention['number'] * DAY_IN_SECONDS;

				if ( $time_span > $retention ) {
					$is_expired = true;
				}
				break;
			case 'weeks':
				$retention = $retention['number'] * WEEK_IN_SECONDS;

				if ( $time_span > $retention ) {
					$is_expired = true;
				}
				break;
			case 'months':
				$retention = $retention['number'] * MONTH_IN_SECONDS;

				if ( $time_span > $retention ) {
					$is_expired = true;
				}
				break;
			case 'years':
				$retention = $retention['number'] * YEAR_IN_SECONDS;

				if ( $time_span > $retention ) {
					$is_expired = true;
				}
				break;
		}

		return $is_expired;
	}

	/**
	 * Handle eraser of data tied to coupon.
	 *
	 * @param array $coupon
	 * @return array
	 */
	protected function maybe_handle_coupon( $coupon ) {
		if ( empty( $coupon ) || ! $this->is_retention_expired( $coupon['post_date'] ) ) {
			return array( false, false, array() );
		}

		delete_post_meta( $coupon['id'], 'customer_email' );
		wp_delete_post( $coupon['id'], true );

		return array( true, false, array( __( 'Store Credit Coupon Personal Data Erased.', 'woocommerce-store-credit' ) ) );
	}
}

new WC_Store_Credit_Privacy();
