<?php
/**
 * Order admin handler.
 *
 * @package WooCommerce_Gateway_GoCardless
 */

/**
 * Manage order admin UI for orders paid via GoCardless.
 *
 * @since 2.4.0
 */
class WC_GoCardless_Order_Admin {

	/**
	 * Register webhook events meta box.
	 *
	 * @since 2.4.0
	 */
	public function add_meta_box() {
		add_action( 'add_meta_boxes', array( $this, 'add_webhook_events_meta_box' ), 11 );
	}

	/**
	 * Add the webhook events meta box.
	 *
	 * @since 2.4.0
	 */
	public function add_webhook_events_meta_box() {
		global $post;

		$order_id = absint( $post->ID );
		$order    = wc_get_order( $order_id );
		if ( ! $order ) {
			return false;
		}

		if ( 'gocardless' !== wc_gocardless_get_order_prop( $order, 'payment_method' ) ) {
			return false;
		}

		add_meta_box( 'woocommerce-gocardless-webhook-events', __( 'GoCardless Webhook Events', 'woocommerce-gateway-gocardless' ), array( $this, 'webhook_events_meta_box' ), 'shop_order', 'side' );
	}

	/**
	 * Output the content for webhook events meta box.
	 *
	 * @since 2.4.0
	 */
	public function webhook_events_meta_box() {
		global $post;

		$order_id = absint( $post->ID );
		$events   = get_post_meta( $order_id, '_gocardless_webhook_events' );

		// TODO(gedex): This will makes the list long for subscription payments,
		// paginate should be implemented.
		if ( ! empty( $events ) ) {
			echo wpautop( __( 'This list may contains duplicated events.', 'woocommerce-gateway-gocardless' ) );
			echo '<ul class="order_notes">';
			foreach ( $events as $event ) {
				printf( '<li rel="%s">%s</li>', $event['id'], $this->get_formatted_event_item( $event ) );
			}
			echo '</ul>';
		} else {
			echo wpautop( __( 'No recent events.', 'woocommerce-gateway-gocardless' ) );
		}
	}

	/**
	 * Get formatted event item.
	 *
	 * @since 2.4.0
	 *
	 * @param array $event Event array.
	 *
	 * @return string Formatted event item.
	 */
	public function get_formatted_event_item( $event ) {
		$resource_id = '';
		if ( ! empty( $event['links']['mandate'] ) ) {
			$resource_id = $event['links']['mandate'];
		} elseif ( ! empty( $event['links']['payment'] ) ) {
			$resource_id = $event['links']['payment'];
		} elseif ( ! empty( $event['links']['subscription'] ) ) {
			$resource_id = $event['links']['subscription'];
		} elseif ( ! empty( $event['links']['refund'] ) ) {
			$resource_id = $event['links']['refund'];
		}

		return sprintf(
			'
			<div class="note_content"><p><strong>%s%s</strong></p>%s</div>
			<p class="meta">
				<abbr class="exact-date">%s</abbr>
			</p>
			',
			$event['resource_type'] . ' ' . $event['action'],
			! empty( $resource_id ) ? ' → ' . $resource_id : '',
			! empty( $event['details']['description'] ) ? wpautop( $event['details']['description'] ) : '',
			sprintf( __( 'Logged on %s', 'woocommerce-gateway-gocardless' ), $event['created_at'] )
		);
	}

	/**
	 * Add GoCardless specific order actions.
	 *
	 * @since 2.4.0
	 */
	public function add_order_actions() {
		add_action( 'woocommerce_order_actions', array( $this, 'gocardless_actions' ), 10, 2 );
		add_action( 'woocommerce_order_action_gocardless_cancel_payment', array( $this, 'cancel_payment' ) );
		add_action( 'woocommerce_order_action_gocardless_retry_payment', array( $this, 'retry_payment' ) );
	}

	/**
	 * GoCardless order actions.
	 *
	 * @since 2.4.0
	 *
	 * @param array $actions The actions available for the order.
	 *
	 * @return array Order actions
	 */
	public function gocardless_actions( $actions ) {
		global $post;

		$order = wc_get_order( $post->ID );

		if ( 'gocardless' !== wc_gocardless_get_order_prop( $order, 'payment_method' ) ) {
			return $actions;
		}

		$gateway = wc_gocardless()->gateway_instance();
		if ( ! $gateway ) {
			return $actions;
		}

		$order_id = wc_gocardless_get_order_prop( $order, 'id' );

		// For payment with status pending_submission, merchant can cancel it.
		$payment_status = $gateway->get_order_resource( $order_id, 'payment', 'status' );
		switch ( $payment_status ) {
			case 'pending_submission':
				$actions['gocardless_cancel_payment'] = __( 'GoCardless &rsaquo; Cancel payment', 'woocommerce-gateway-gocardless' );
				break;
			case 'failed':
				$actions['gocardless_retry_payment'] = __( 'GoCardless &rsaquo; Retry payment', 'woocommerce-gateway-gocardless' );
				break;
		}

		return $actions;
	}

	/**
	 * Order action to cancel payment.
	 *
	 * @since 2.4.0
	 */
	public function cancel_payment() {
		global $post;

		$order = wc_get_order( $post->ID );
		if ( 'gocardless' !== wc_gocardless_get_order_prop( $order, 'payment_method' ) ) {
			return;
		}

		$gateway = wc_gocardless()->gateway_instance();
		if ( ! $gateway ) {
			return;
		}

		$order_id   = wc_gocardless_get_order_prop( $order, 'id' );
		$payment_id = $gateway->get_order_resource( $order_id, 'payment', 'id' );
		$payment    = WC_GoCardless_API::cancel_payment( $payment_id );

		if ( class_exists( 'WC_Admin_Notices' ) ) {
			WC_Admin_Notices::add_custom_notice( 'gocardless_cancel_payment', $this->get_cancel_payment_notice( $order_id, $payment ) );
		}

		if ( ! is_wp_error( $payment ) ) {
			$order->update_status( 'cancelled', __( 'Cancelled GoCardless payment.', 'woocommerce-gateway-gocardless' ) );
		}
	}

	/**
	 * Get notice for cancel payment action.
	 *
	 * @since 2.4.0
	 *
	 * @param int            $order_id Order ID.
	 * @param WP_Error|array $payment  Payment array of WP_Error.
	 *
	 * @return string Notice message
	 */
	public function get_cancel_payment_notice( $order_id, $payment ) {
		if ( is_wp_error( $payment ) ) {
			return sprintf( __( 'Failed to cancel GoCardless payment in order #%1$s: %2$s', 'woocommerce-gateway-gocardless' ), $order_id, $payment->get_error_message() );
		}

		return sprintf( __( 'GoCardless payment in order #%s is cancelled.', 'woocommerce-gateway-gocardless' ), $order_id );
	}

	/**
	 * Order action to retry payment.
	 *
	 * @since 2.4.0
	 */
	public function retry_payment() {
		global $post;

		$order = wc_get_order( $post->ID );
		if ( 'gocardless' !== wc_gocardless_get_order_prop( $order, 'payment_method' ) ) {
			return;
		}

		$gateway = wc_gocardless()->gateway_instance();
		if ( ! $gateway ) {
			return;
		}

		$order_id   = wc_gocardless_get_order_prop( $order, 'id' );
		$payment_id = $gateway->get_order_resource( $order_id, 'payment', 'id' );
		$payment    = WC_GoCardless_API::retry_payment( $payment_id );

		if ( class_exists( 'WC_Admin_Notices' ) ) {
			WC_Admin_Notices::add_custom_notice( 'gocardless_retry_payment', $this->get_retry_payment_notice( $order_id, $payment ) );
		}

		if ( ! is_wp_error( $payment ) ) {
			$order->update_status( 'processing', __( 'Retried GoCardless payment.', 'woocommerce-gateway-gocardless' ) );
		}
	}

	/**
	 * Get notice for retry payment action.
	 *
	 * @since 2.4.0
	 *
	 * @param int            $order_id Order ID.
	 * @param WP_Error|array $payment  Payment array of WP_Error.
	 *
	 * @return string Notice message
	 */
	public function get_retry_payment_notice( $order_id, $payment ) {
		if ( is_wp_error( $payment ) ) {
			return sprintf( __( 'Failed to retry GoCardless payment in order #%1$s: %2$s', 'woocommerce-gateway-gocardless' ), $order_id, $payment->get_error_message() );
		}

		return sprintf( __( 'Retried GoCardless payment in order #%s.', 'woocommerce-gateway-gocardless' ), $order_id );
	}
}
