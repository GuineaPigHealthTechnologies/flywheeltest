<?php
/**
 * GoCardless gateway.
 *
 * @package WC_Gateway_GoCardless
 */

/**
 * Gateway class
 */
class WC_Gateway_GoCardless extends WC_Payment_Gateway {

	/**
	 * Notices to display.
	 *
	 * @since 2.4.0
	 *
	 * @var array
	 */
	private $_notices = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                 = 'gocardless';
		$this->method_title       = __( 'Direct Debit (GoCardless)', 'woocommerce-gateway-gocardless' );
		$this->method_description = __( 'GoCardless takes payment using direct debit from the bank account customers in the UK, the Eurozone and Sweden.', 'woocommerce-gateway-gocardless' );
		$this->icon               = wc_gocardless()->plugin_url . '/images/dd.png';
		$this->supports           = array(
			'products',
			'refunds',
			'tokenization',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_date_changes',
			'subscription_amount_changes',
			'multiple_subscriptions',
			'pre-orders',
		);

		// Maybe merchant just connected from GoCardless or discarded saved access token.
		$this->_check_access_token();

		// Load saved settings.
		$this->load_settings();

		// Load the form fields.
		$this->init_form_fields();

		// Initialize settings.
		$this->init_settings();

		$this->view_transaction_url = $this->get_transaction_url_format();

		// Endpoint handler. Handling request such as webhook.
		add_action( 'woocommerce_api_wc_gateway_gocardless', array( $this, 'gocardless_endpoint_handler' ) );

		// Save admin options.
		add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// Notices.
		add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );

		// Payment-token-API related hook.
		add_filter( 'woocommerce_payment_methods_list_item', array( $this, 'saved_payment_methods_list_item' ), 10, 2 );
		add_action( 'woocommerce_account_payment_methods_column_method', array( $this, 'saved_payment_methods_column_method' ) );
	}

	/**
	 * Check access token action (just retrieved or discarded) by user.
	 *
	 * @since 2.4.0
	 */
	protected function _check_access_token() {
		if ( ! is_admin() ) {
			return;
		}
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Don't check on every screen.
		$screen = is_callable( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return;
		}
		if ( false === strpos( $screen->id, 'page_wc-settings' ) ) {
			return;
		}

		if ( $this->_maybe_save_access_token() ) {
			wc_gocardless()->log( sprintf( '%s - Connected to GoCardless successfully', __METHOD__ ) );
		} elseif ( $this->_maybe_discard_access_token() ) {
			wc_gocardless()->log( sprintf( '%s - Disconnected from GoCardless', __METHOD__ ) );
		}
	}


	/**
	 * If we see an access token, save it and add a notice. Returns true on
	 * successful connection.
	 *
	 * @since 2.4.0
	 *
	 * @return bool Returns true if connected successfully
	 */
	protected function _maybe_save_access_token() {
		// Require the access token.
		if ( empty( $_GET['gocardless_access_token'] ) ) {
			return false;
		}

		// Require the nonce.
		if ( empty( $_GET['wc_gocardless_connect_nonce'] ) ) {
			return false;
		}

		// Verify the nonce.
		if ( ! wp_verify_nonce( $_GET['wc_gocardless_connect_nonce'], 'connect_gocardless' ) ) {
			wp_die( __( 'Invalid connection request.', 'woocommerce-gateway-gocardless' ) );
		}

		// If we already have a token, ignore this request.
		$existing_access_token = $this->get_option( 'access_token', '' );
		if ( ! empty( $existing_access_token ) ) {
			return false;
		}
		$access_token = sanitize_text_field( urldecode( $_GET['gocardless_access_token'] ) );
		if ( empty( $access_token ) ) {
			return false;
		}
		$testmode = ( ! empty( $_GET['sandbox'] ) && 'true' === $_GET['sandbox'] ) ? 'yes' : 'no';

		$settings = get_option( 'woocommerce_gocardless_settings' );

		$settings['access_token'] = $access_token;
		$settings['testmode']     = $testmode;

		update_option( 'woocommerce_gocardless_settings', $settings );

		// Delete notice that informs merchant to connect with GoCardless.
		WC_Admin_Notices::remove_notice( 'gocardless_connect_prompt' );

		$this->_add_admin_notice( 'connected_successfully', 'updated', __( 'Connected to GoCardless successfully.', 'woocommerce-gateway-gocardless' ) );

		return true;
	}

	/**
	 * If the user asks, discard the access token and add a notice.
	 *
	 * @since 2.4.0
	 *
	 * @return bool Returns true if disconnected successfully
	 */
	protected function _maybe_discard_access_token() {
		if ( empty( $_GET['disconnect_gocardless'] ) ) {
			return false;
		}

		// Require the nonce.
		if ( empty( $_GET['wc_gocardless_disconnect_nonce'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $_GET['wc_gocardless_disconnect_nonce'], 'disconnect_gocardless' ) ) {
			wp_die( __( 'Invalid disconnection request.', 'woocommerce-gateway-gocardless' ) );
		}

		// If we don't have a token, ignore this request.
		$existing_access_token = $this->get_option( 'access_token', '' );
		if ( empty( $existing_access_token ) ) {
			return false;
		}

		$settings = get_option( 'woocommerce_gocardless_settings' );

		$settings['access_token'] = '';

		update_option( 'woocommerce_gocardless_settings', $settings );

		$this->_add_admin_notice( 'disconnected_successfully', 'updated', __( 'Disconnected from GoCardless successfully.', 'woocommerce-gateway-gocardless' ) );

		return true;
	}

	/**
	 * Get connection HTML.
	 *
	 * @since 2.4.0
	 *
	 * @param mixed $key  Field's key.
	 * @param mixed $data Field's data.
	 *
	 * @return string Connection HTML.
	 */
	public function generate_connection_html( $key, $data ) {
		$access_token = $this->get_option( 'access_token', '' );
		$field_key    = $this->get_field_key( $key );

		$data['description'] = empty( $access_token ) ? $data['connect_description'] : $data['disconnect_description'];
		$data['action_link'] = empty( $access_token ) ? $this->get_connect_url() : $this->get_disconnect_url();

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<a href="<?php echo esc_url( $data['action_link'] ); ?>" class="button-primary">
					<?php if ( ! empty( $access_token ) ) : ?>
						<?php echo esc_html( $data['disconnect_button_label'] ); ?>
					<?php else : ?>
						<?php echo esc_html( $data['connect_button_label'] ); ?>
					<?php endif; ?>
				</a>

				<?php if ( empty( $access_token ) ) : ?>
					<p style="padding-top: 20px">
						<a href="<?php echo esc_url( $this->get_connect_url( array( 'sandbox' => true ) ) ); ?>">
							<?php echo esc_html( $data['use_sandbox_link_text'] ); ?>
						</a>
					</p>
				<?php endif; ?>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get hidden field HTML.
	 *
	 * @since 2.4.0
	 *
	 * @param string $key Field's key.
	 *
	 * @return string Hidden field HTML.
	 */
	public function generate_hidden_html( $key ) {
		$field_key = $this->get_field_key( $key );

		return sprintf(
			'<input type="hidden" name="%s" value="%s" />',
			esc_attr( $field_key ),
			esc_attr( $this->get_option( $key ) )
		);
	}

	/**
	 * Get connect URL.
	 *
	 * @since 2.4.0
	 *
	 * @param array $args Arguments to connect URL.
	 *
	 * @return string Connect URL.
	 */
	public function get_connect_url( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'base_url' => 'https://connect.woocommerce.com/login/gocardless',
				'sandbox'  => false,
				'redirect' => '',
			)
		);

		if ( $args['sandbox'] ) {
			$args['base_url'] = 'https://connect.woocommerce.com/login/gocardlesssandbox';
		}

		if ( empty( $args['redirect'] ) ) {
			$args['redirect'] = add_query_arg(
				array(
					'wc_gocardless_connect_nonce' => wp_create_nonce( 'connect_gocardless' ),
					'sandbox'                     => $args['sandbox'] ? 'true' : 'false',
				),
				wc_gocardless()->get_setting_url()
			);
		}
		$args['redirect'] = urlencode( $args['redirect'] );

		$base_url = $args['base_url'];
		unset( $args['base_url'] );

		return add_query_arg( $args, $base_url );
	}

	/**
	 * Get disconnect URL.
	 *
	 * @since 2.4.0
	 *
	 * @return string Disconnect URL
	 */
	public function get_disconnect_url() {
		return add_query_arg(
			array(
				'disconnect_gocardless'          => 'true',
				'wc_gocardless_disconnect_nonce' => wp_create_nonce( 'disconnect_gocardless' ),
			),
			wc_gocardless()->get_setting_url()
		);
	}

	/**
	 * Add admin notice.
	 *
	 * @since 2.4.0
	 *
	 * @param string $slug    Slug.
	 * @param string $class   Notice class.
	 * @param string $message Notice message.
	 */
	protected function _add_admin_notice( $slug, $class, $message ) {
		$this->_notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message,
		);
	}

	/**
	 * Display any notices we've collected thus far (e.g. for connection, disconnection).
	 *
	 * @since 2.4.0
	 */
	public function display_admin_notices() {
		foreach ( (array) $this->_notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice_key ) . ' ' . esc_attr( $notice['class'] ) . '"><p>';
			echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
			echo '</p></div>';
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'connection' => array(
				'type'                    => 'connection',
				'title'                   => __( 'Connect / Disconnect', 'woocommerce-gateway-gocardless' ),
				'connect_button_label'    => __( 'Connect with GoCardless', 'woocommerce-gateway-gocardless' ),
				'connect_description'     => __( 'Click button to connect with GoCardless and start transacting.', 'woocommerce-gateway-gocardless' ),
				'disconnect_button_label' => __( 'Disconnect from GoCardless', 'woocommerce-gateway-gocardless' ),
				'disconnect_description'  => __( 'You just connected your GoCardless account to WooCommerce. You can start taking payments now.', 'woocommerce-gateway-gocardless' ),
				'use_sandbox_link_text'   => __( 'Not ready to accept live payments? Click here to connect using sandbox mode.', 'woocommerce-gateway-gocardless' ),
				'desc_tip'                => true,
			),
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-gocardless' ),
				'label'       => __( 'Enable Direct Debit (GoCardless)', 'woocommerce-gateway-gocardless' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-gocardless' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-gocardless' ),
				'default'     => __( 'Direct Debit', 'woocommerce-gateway-gocardless' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-gocardless' ),
				'type'        => 'text',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-gocardless' ),
				'default'     => 'Pay securely via your bank account.',
				'desc_tip'    => true,
			),
			'access_token' => array(
				'type'        => 'hidden',
				'default'     => '',
			),
			'webhook_secret' => array(
				'title'       => __( 'Webhook Secret', 'woocommerce-gateway-gocardless' ),
				'type'        => 'text',
				'description' => sprintf( __( 'To receive webhooks, add <code>%1$s</code> as webhook URL and set the secret the same as this Webhook Secret field from <a href="%2$s" target="_blank">here</a>.', 'woocommerce-gateway-gocardless' ), $this->get_webhook_url(), $this->get_create_webhook_url() ),
				'default'     => '',
			),
			'saved_bank_accounts' => array(
				'title'       => __( 'Saved Bank Accounts', 'woocommerce-gateway-gocardless' ),
				'label'       => __( 'Enable Payment via Saved Bank Accounts', 'woocommerce-gateway-gocardless' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved bank accounts during checkout. Bank account details are stored on GoCardless servers, not on your store.', 'woocommerce-gateway-gocardless' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'scheme' => array(
				'title'       => __( 'Direct Debit Scheme', 'woocommerce-gateway-gocardless' ),
				'type'        => 'select',
				'options'     => array(
					''          => __( 'Automatically detected from the customer\'s bank account', 'woocommerce-gateway-gocardless' ),
					'autogiro'  => __( 'Autogiro', 'woocommerce-gateway-gocardless' ),
					'bacs'      => __( 'Bacs', 'woocommerce-gateway-gocardless' ),
					'sepa_core' => __( 'SEPA Core', 'woocommerce-gateway-gocardless' ),
					'sepa_cor1' => __( 'SEPA COR1', 'woocommerce-gateway-gocardless' ),
				),
				'default'     => '',
				'description' => sprintf( __( 'The Direct Debit scheme of the mandate. See <a target="_blank" href="%s">this page</a> for  scheme and its supported countries. If Autogiro, Bacs, SEPA Core, or SEPA COR1 is specified, the payment pages will only allow the set-up of a mandate for the specified scheme. If auto detect is specified, failed validation may occur in case currency in the order is not supported by the scheme.', 'woocommerce-gateway-gocardless' ), 'https://developer.gocardless.com/api-reference/2015-07-06/#overview-supported-direct-debit-schemes' ),
			),
			'testmode' => array(
				'type'    => 'hidden',
				'default' => 'no',
			),
			'logging' => array(
				'title'       => __( 'Logging', 'woocommerce-gateway-gocardless' ),
				'label'       => __( 'Log debug messages', 'woocommerce-gateway-gocardless' ),
				'type'        => 'checkbox',
				'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-gateway-gocardless' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Load saved settings.
	 *
	 * @since 2.4.0
	 *
	 * @return void
	 */
	public function load_settings() {
		$this->title               = $this->get_option( 'title', __( 'Direct Debit', 'woocommerce-gateway-gocardless' ) );
		$this->description         = $this->get_option( 'description', '' );
		$this->enabled             = $this->get_option( 'enabled', 'no' );
		$this->access_token        = $this->get_option( 'access_token', '' );
		$this->webhook_secret      = $this->get_option( 'webhook_secret', '' );
		$this->saved_bank_accounts = $this->get_option( 'saved_bank_accounts', 'yes' ) === 'yes';
		$this->scheme              = $this->get_option( 'scheme', '' );
		$this->testmode            = $this->get_option( 'testmode', 'yes' ) === 'yes';
	}

	/**
	 * Check if this gateway is enabled.
	 *
	 * @return bool Check if gateway is available.
	 */
	public function is_available() {
		// Subscription checks availability in checkout settings to display
		// available gateway that supports recurring payments.
		//
		// @see https://github.com/woocommerce/woocommerce-gateway-gocardless/issues/57.
		if ( is_admin() && $this->is_checkout_settings_page() ) {
			return parent::is_available();
		}

		if ( ! $this->access_token ) {
			return false;
		}

		$country = '';
		if ( null !== WC()->customer ) {
			// WC 3.0 will deprecates get_country.
			if ( is_callable( array( WC()->customer, 'get_billing_country' ) ) ) {
				$country = WC()->customer->get_billing_country();
			} else {
				$country = WC()->customer->get_country();
			}
		}

		if ( ! WC_GoCardless_API::is_country_supported( $country ) ) {
			return false;
		}

		// Disable the option in add-payment-method page. Will enable this in
		// the future.
		//
		// @see https://github.com/woocommerce/woocommerce-gateway-gocardless/issues/74.
		if ( function_exists( 'is_add_payment_method_page' ) && is_add_payment_method_page() ) {
			return false;
		}

		return parent::is_available();
	}

	/**
	 * Check if current admin page is checkout settings page.
	 *
	 * @since 2.4.2
	 *
	 * @return bool Returns true if in checkout settings page.
	 */
	private function is_checkout_settings_page() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		return (
			'woocommerce_page_wc-settings' === $screen->id
			&&
			! empty( $_GET['tab'] )
			&&
			'checkout' === $_GET['tab']
		);
	}

	/**
	 * Payment form on checkout page.
	 *
	 * @since 2.4.0
	 */
	public function payment_fields() {
		if ( $this->description ) {
			echo apply_filters( 'woocommerce_gocardless_description', wpautop( wp_kses_post( $this->description ) ) );
		}

		$display_tokenization = (
			$this->supports( 'tokenization' )
			&& is_checkout()
			&& $this->saved_bank_accounts
		);

		if ( $display_tokenization ) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->save_payment_method_checkbox();
		}
	}

	/**
	 * Checks whether user requsting checkout to use saved token.
	 *
	 * @since 2.4.0
	 *
	 * @return bool Returns true if processing payment with saved token / mandate
	 */
	protected function _is_processing_payment_with_saved_token() {
		return (
			isset( $_POST['wc-gocardless-payment-token'] )
			&&
			'new' !== $_POST['wc-gocardless-payment-token']
		);
	}

	/**
	 * Process payment with saved token.
	 *
	 * The stored token contains mandate ID that can be used to take payment
	 * from the customer.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return array|WP_Error Returns array if succeed, otherwise WP_Error is returned.
	 */
	protected function _process_payment_with_saved_token( WC_Order $order ) {
		try {
			$token_id = wc_clean( $_POST['wc-gocardless-payment-token'] );
			$token    = WC_Payment_Tokens::get( $token_id );
			if ( ! $token || $token->get_user_id() !== get_current_user_id() ) {
				throw new Exception( __( 'Invalid payment method. Please setup a new direct debit account.', 'woocommerce-gateway-gocardless' ) );
			}

			$mandate_id = $token->get_token();
			$mandate    = WC_GoCardless_API::get_mandate( $mandate_id );
			if ( is_wp_error( $mandate ) ) {
				throw new Exception( __( 'Failed to retrieve mandate.', 'woocommerce-gateway-gocardless' ) );
			}

			$order_id = wc_gocardless_get_order_prop( $order, 'id' );

			$this->update_order_resource( $order_id, 'mandate', $mandate['mandates'] );

			$this->_maybe_create_payment( $order_id, $mandate_id );

			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order ),
			);
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return false;
		}
	}

	/**
	 * Process payment with redirect flow.
	 *
	 * Buyer will be redirected to GoCardless page to fill bank account details.
	 * Upon successful, buyer will be redirected back to merchant's site with
	 * redirect flow ID as query string. Once redirect flow is completed,
	 * GoCardless creates customer, customer bank account, and mandate.
	 *
	 * @see https://developer.gocardless.com/api-reference/#core-endpoints-redirect-flows
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return array|bool Returns array if succeed, otherwise false is returned
	 */
	protected function _process_payment_with_redirect_flow( WC_Order $order ) {
		$order_id = wc_gocardless_get_order_prop( $order, 'id' );

		wc_gocardless()->log( sprintf( '%s - Creating redirect flow for order #%s', __METHOD__, $order_id ) );

		$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

		$redirect_flow_params = array(
			'description'          => $this->_get_description_from_order( $order ),
			'session_token'        => $pre_wc_30 ? $order->order_key : $order->get_order_key(),
			'success_redirect_url' => $this->get_success_redirect_url( $order ),
			'prefilled_customer'   => array(
				'given_name'    => $pre_wc_30 ? $order->billing_first_name : $order->get_billing_first_name(),
				'family_name'   => $pre_wc_30 ? $order->billing_last_name : $order->get_billing_last_name(),
				'email'         => $pre_wc_30 ? $order->billing_email : $order->get_billing_email(),
				'company_name'  => $pre_wc_30 ? $order->billing_company : $order->get_billing_company(),
				'address_line1' => $pre_wc_30 ? $order->billing_address_1 : $order->get_billing_address_1(),
				'address_line2' => $pre_wc_30 ? $order->billing_address_2 : $order->get_billing_address_2(),
				'city'          => $pre_wc_30 ? $order->billing_city : $order->get_billing_city(),
				'postal_code'   => $pre_wc_30 ? $order->billing_postcode : $order->get_billing_postcode(),
			),
		);
		if ( ! empty( $this->scheme ) ) {
			$redirect_flow_params['scheme'] = $this->scheme;
		}

		$redirect_flow_params = apply_filters( 'woocommerce_gocardless_create_redirect_flow_params', $redirect_flow_params );
		$redirect_flow        = WC_GoCardless_API::create_redirect_flow( $redirect_flow_params );
		if ( is_wp_error( $redirect_flow ) ) {
			return false;
		}
		if ( empty( $redirect_flow['redirect_flows']['redirect_url'] ) ) {
			return false;
		}
		$this->update_order_resource( $order_id, 'redirect_flow', $redirect_flow['redirect_flows'] );

		wc_gocardless()->log( sprintf( '%s - Redirect flow created: %s', __METHOD__, print_r( $redirect_flow, true ) ) );

		return array(
			'result' 	=> 'success',
			'redirect'	=> $redirect_flow['redirect_flows']['redirect_url'],
		);
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array|bool Returns array if succeed, otherwise false is returned
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $this->_is_processing_payment_with_saved_token() ) {
			return $this->_process_payment_with_saved_token( $order );
		}

		return $this->_process_payment_with_redirect_flow( $order );
	}

	/**
	 * Process refund.
	 *
	 * Refund is disabled by default. Merchant needs to contact GoCardless to
	 * enable refund.
	 *
	 * @since 2.4.0
	 *
	 * @param  int    $order_id      Order ID.
	 * @param  float  $refund_amount Amount to refund.
	 * @param  string $reason        Reason to refund.
	 *
	 * @return WP_Error|boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $refund_amount = null, $reason = '' ) {
		wc_gocardless()->log( sprintf( '%s - Refunding order #%s', __METHOD__, $order_id ) );

		$payment_id = $this->get_order_resource( $order_id, 'payment', 'id' );
		if ( ! $payment_id ) {
			return new WP_Error( 'missing_payment', sprintf( __( 'Unable to refund order #%s. Order does not have payment ID. Make sure payment has been created.', 'woocommerce-gateway-gocardless' ), $order_id ) );
		}

		$order = wc_get_order( $order_id );

		$amount_in_cents       = intval( 100 * $refund_amount );
		$total_amount_in_cents = intval( 100 * $order->get_total_refunded() );

		$refund_params = apply_filters( 'woocommerce_gocardless_refund_params', array(
			'amount'                    => $amount_in_cents,
			'total_amount_confirmation' => $total_amount_in_cents,
			'links'                     => array( 'payment' => $payment_id ),
			'metadata'                  => array(
				'order_id'    => (string) $order_id,
				'order_total' => (string) $order->get_total(),
				'reason'      => $reason,
			),
		) );

		$refund = WC_GoCardless_API::create_refund( $refund_params );
		if ( is_wp_error( $refund ) ) {
			$order->add_order_note( sprintf( __( 'Unable to refund via GoCardless: %s', 'woocommerce-gateway-gocardless' ), $refund->get_error_message() ) );
			return $refund;
		}

		if ( empty( $refund['refunds']['id'] ) ) {
			$order->add_order_note( __( 'Unable to refund via GoCardless. GoCardless returns unexpected refund response.', 'woocommerce-gateway-gocardless' ) );
			return new WP_Error( 'unexpected_refund_response', __( 'Unexpected refund response from GoCardless.', 'woocommerce-gateway-gocardless' ) );
		}

		$this->update_order_resource( $order_id, 'refund', $refund['refunds'] );

		$order->add_order_note( sprintf( __( 'Refunded %1$s (%2$s)', 'woocommerce-gateway-gocardless' ), wc_price( $refund['refunds']['amount'] * 0.01 ), $reason ? $reason : __( 'No reason provided', 'woocommerce-gateway-gocardless' ) ) );

		return true;
	}

	/**
	 * Get success redirect URL.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order|int $order Order object or ID.
	 *
	 * @return string Success redirect URL.
	 */
	public function get_success_redirect_url( $order ) {
		$order    = wc_get_order( $order );
		$order_id = wc_gocardless_get_order_prop( $order, 'id' );
		$params   = array(
			'request'  => 'redirect_flow',
			'order_id' => $order_id,
		);

		$save_token = (
			! empty( $_POST['wc-gocardless-new-payment-method'] )
			&& $this->saved_bank_accounts
			&& get_current_user_id()
		);

		if ( $save_token ) {
			$params['save_customer_token'] = 'true';
		}

		$url = add_query_arg( $params, WC()->api_request_url( __CLASS__, true ) );

		return $url;
	}

	/**
	 * Get webhook URL.
	 *
	 * @since 2.4.0
	 *
	 * @return string Webhook URL
	 */
	public function get_webhook_url() {
		return add_query_arg( array( 'request' => 'webhook' ), WC()->api_request_url( 'WC_Gateway_GoCardless', true ) );
	}

	/**
	 * Get create webhook URL via GoCardless dashboard.
	 *
	 * @since 2.4.0
	 *
	 * @return string Dashboard URL
	 */
	public function get_create_webhook_url() {
		return sprintf( 'https://%s.gocardless.com/developers/webhook-endpoints/create', $this->testmode ? 'manage-sandbox' : 'manage' );
	}

	/**
	 * Handler for GoCardless endpoint.
	 *
	 * @since 2.4.0
	 */
	public function gocardless_endpoint_handler() {
		try {
			if ( empty( $_GET['request'] ) ) {
				throw new Exception( __( 'Missing request type.', 'woocommerce-gateway-gocardless' ) );
			}

			switch ( $_GET['request'] ) {
				case 'redirect_flow':
					$this->_handle_redirect_flow();
					break;
				case 'webhook':
					$this->_handle_webhook();
					break;
				default:
					throw new Exception( __( 'Unknown request type.', 'woocommerce-gateway-gocardless' ) );
					break;
			}
		} catch ( Exception $e ) {
			header( 'HTTP/1.1 400 Bad request' );
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	/**
	 * Handle redirect flow from GoCardless.
	 *
	 * @since 2.4.0
	 *
	 * @return void
	 */
	protected function _handle_redirect_flow() {
		try {
			if ( empty( $_GET['redirect_flow_id'] ) || empty( $_GET['order_id'] ) ) {
				throw new Exception( __( 'Invalid redirect flow request.', 'woocommerce-gateway-gocardless' ) );
			}
			$order_id         = absint( $_GET['order_id'] );
			$redirect_flow_id = $_GET['redirect_flow_id'];

			wc_gocardless()->log( sprintf( '%s - Maybe redirected from GoCardless with redirect_flow_id "%s" and order ID %s', __METHOD__, $redirect_flow_id, $order_id ) );

			$redirect_flow = $this->_maybe_complete_redirect_flow( $order_id, $redirect_flow_id );
			$mandate_id    = $redirect_flow['links']['mandate'];

			wc_gocardless()->log( sprintf( '%s - Redirect flow completed', __METHOD__ ) );
			wc_gocardless()->log( sprintf( '%s - Creating GoCardless mandate for order %s', __METHOD__, $order_id ) );

			$this->_after_mandate_created( $order_id, $mandate_id );

			wp_redirect( $this->get_return_url( wc_get_order( $order_id ) ) );
			exit;
		} catch ( Exception $e ) {
			wc_gocardless()->log( sprintf( '%s - Error when handling redirect flow request: %s', __METHOD__, $e->getMessage() ) );

			$order = wc_get_order( $order_id );

			// TODO(gedex): Maybe cancel the mandate?
			$error_message = sprintf(
				'%1$s %2$s<br><br>%3$s',
				sprintf( __( 'We were unable to process your order, <a href="%s">click here to try again</a>.', 'woocommerce-gateway-gocardless' ), $order->get_cancel_order_url() ),
				__( 'If the problem still persists please contact us with the details below.', 'woocommerce-gateway-gocardless' ),
				$e->getMessage()
			);

			wc_add_notice( $error_message, 'error' );

			wp_redirect( wc_get_page_permalink( 'checkout' ) );
		}
	}

	/**
	 * Maybe complete the GoCardless redirect flow.
	 *
	 * @since 2.4.0
	 *
	 * @throws \Exception Exception.
	 *
	 * @param int    $order_id         Order ID.
	 * @param string $redirect_flow_id Redirect flow ID.
	 *
	 * @return array Redirect flow
	 */
	protected function _maybe_complete_redirect_flow( $order_id, $redirect_flow_id ) {
		$redirect_flow = $this->get_order_resource( $order_id, 'redirect_flow' );
		if ( empty( $redirect_flow['session_token'] ) ) {
			throw new Exception( __( 'Order does not have redirect flow session token.', 'woocommerce-gateway-gocardless' ) );
		}

		if ( $redirect_flow_id !== $redirect_flow['id'] ) {
			throw new Exception( __( 'Invalid redirect flow ID.', 'woocommerce-gateway-gocardless' ) );
		}

		if ( intval( wc_get_order_id_by_order_key( $redirect_flow['session_token'] ) ) !== $order_id ) {
			throw new Exception( __( 'Order ID mismatch with redirect flow session token.', 'woocommerce-gateway-gocardless' ) );
		}

		$resp = WC_GoCardless_API::complete_redirect_flow( $redirect_flow_id, array( 'session_token' => $redirect_flow['session_token'] ) );
		if ( is_wp_error( $resp ) ) {
			throw new Exception( sprintf( __( 'Unable to complete redirect flow: %s.', 'woocommerce-gateway-gocardless' ), $resp->get_error_message() ) );
		}

		if ( empty( $resp['redirect_flows']['links']['mandate'] ) ) {
			throw new Exception( __( 'Mandate is missing from redirect flow response.', 'woocommerce-gateway-gocardless' ) );
		}

		$this->update_order_resource( $order_id, 'redirect_flow', $resp['redirect_flows'] );

		$mandate_id = $resp['redirect_flows']['links']['mandate'];
		$mandate    = WC_GoCardless_API::get_mandate( $mandate_id );

		if ( is_wp_error( $mandate ) ) {
			throw new Exception( __( 'Failed to retrieve mandate.', 'woocommerce-gateway-gocardless' ) );
		}

		$this->update_order_resource( $order_id, 'mandate', $mandate['mandates'] );

		// Maybe save customer token.
		$save_customer_token = (
			! is_wp_error( $mandate )
			&& ! empty( $mandate['mandates'] )
			&& ! empty( $_GET['save_customer_token'] )
			&& $this->saved_bank_accounts
		);

		if ( $save_customer_token ) {
			$order = wc_get_order( $order_id );
			$this->_save_customer_token( $order->get_user_id(), $mandate['mandates'] );
		}

		do_action( 'woocommerce_gocardless_after_success_redirect_flow', $resp['redirect_flows'] );

		return $resp['redirect_flows'];
	}

	/**
	 * Handler after mandate is created.
	 *
	 * @since 2.4.0
	 *
	 * @param int    $order_id   Order ID.
	 * @param string $mandate_id Mandate ID.
	 *
	 * @throws \Exception Exception.
	 */
	protected function _after_mandate_created( $order_id, $mandate_id ) {
		wc_gocardless()->log( sprintf( '%s - Mandate created', __METHOD__ ) );
		wc_gocardless()->log( sprintf( '%s - Creating GoCardless payment for order %s', __METHOD__, $order_id ) );

		$this->_maybe_create_payment( $order_id, $mandate_id );

		wc_gocardless()->log( sprintf( '%s - Payment created', __METHOD__ ) );
	}

	/**
	 * Maybe create the payment of a given order_id and mandate_id.
	 *
	 * @since 2.4.0
	 *
	 * @throws \Exception Exception.
	 *
	 * @param int    $order_id   Order ID.
	 * @param string $mandate_id Mandate ID.
	 * @param float  $amount     Amount to charge.
	 *
	 * @return void
	 */
	protected function _maybe_create_payment( $order_id, $mandate_id, $amount = null ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			throw new Exception( __( 'Invalid order.', 'woocommerce-gateway-gocardless' ) );
		}

		if ( ! $amount ) {
			$amount = $order->get_total();
		}

		// Save it here in case we need to call this method again if mandate
		// is replaced.
		$original_amount = $amount;

		// Amount in pence (GBP), cents (EUR), or öre (SEK).
		$amount = intval( $amount * 100 );

		// Maybe free products, pre-order, or switching payment method for
		// subscriptions.
		if ( ! $amount ) {
			$order->payment_complete();

			if ( function_exists( 'wc_empty_cart' ) ) {
				wc_empty_cart();
			}

			return;
		}

		$payment_params = apply_filters( 'woocommerce_gocardless_create_payment_params', array(
			'amount'      => $amount,
			'description' => $this->_get_description_from_order( $order ),
			'currency'    => wc_gocardless_get_order_prop( $order, 'order_currency' ),
			'links'       => array(
				'mandate' => $mandate_id,
			),
			'metadata'    => array(
				'order_id' => (string) $order_id,
			),
		) );

		$payment = WC_GoCardless_API::create_payment( $payment_params );
		if ( is_wp_error( $payment ) ) {
			if ( $this->is_mandate_replaced( $payment ) ) {
				$new_mandate = $payment->get_error_data( 'new_mandate' );

				$this->_update_mandate( $mandate_id, $new_mandate );

				// Retry again.
				wc_gocardless()->log( sprintf( '%s - Retry create payment with new mandate', __METHOD__ ) );
				return $this->_maybe_create_payment( $order_id, $new_mandate, $original_amount );
			} else {
				throw new Exception( sprintf( __( 'Unable to create payment: %s.', 'woocommerce-gateway-gocardless' ), $payment->get_error_message() ) );
			}
		}

		if ( empty( $payment['payments']['id'] ) ) {
			throw new Exception( sprintf( __( 'Unexpected payment response from GoCardless.', 'woocommerce-gateway-gocardless' ) ) );
		}

		/**
		 * For Subscriptions, set the order status to processing.
		 *
		 * Direct debit takes time around a week, so would be useless for
		 * membership sites.
		 *
		 * @see https://github.com/woocommerce/woocommerce-gateway-gocardless/issues/75
		 */
		if ( function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order_id ) ) {
			$status = apply_filters( 'woocommerce_gocardless_create_payment_subscription_order_status', 'processing', $order_id );
		} elseif ( function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order_id ) ) {
			$status = apply_filters( 'woocommerce_gocardless_create_payment_subscription_renewal_order_status', 'processing', $order_id );
		} else {
			$status = apply_filters( 'woocommerce_gocardless_create_payment_order_status', 'on-hold', $order_id );
		}

		$order->update_status( $status );

		// Reduce stock levels.
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			wc_reduce_stock_levels( $order_id );
		} else {
			$order->reduce_order_stock();
		}

		if ( function_exists( 'wc_empty_cart' ) ) {
			wc_empty_cart();
		}

		$order->add_order_note( sprintf( __( 'GoCardless payment created with ID %1$s and status "%2$s"', 'woocommerce-gateway-gocardless' ), $payment['payments']['id'], $payment['payments']['status'] ) );

		$this->update_order_resource( $order_id, 'payment', $payment['payments'] );
	}

	/**
	 * Checks whether a response specifies if mandate has been replaced.
	 *
	 * @since 2.4.5
	 * @version 2.4.5
	 *
	 * @param array|WP_Error $resp Response from create payments.
	 *
	 * @return bool Returns true if response specifies mandate has been replaced.
	 */
	protected function is_mandate_replaced( $resp ) {
		return (
			is_wp_error( $resp )
			&&
			422 === (int) $resp->get_error_code()
			&&
			false !== strstr( $resp->get_error_message(), 'mandate_replaced' )
			&&
			$resp->get_error_data( 'new_mandate' )
		);
	}
	/**
	 * Handle redirect flow from GoCardless.
	 *
	 * @since 2.4.0
	 *
	 * @return void
	 */
	protected function _handle_webhook() {
		$this->load_settings();

		$raw_payload = file_get_contents( 'php://input' );
		$signature   = ! empty( $_SERVER['HTTP_WEBHOOK_SIGNATURE'] ) ? $_SERVER['HTTP_WEBHOOK_SIGNATURE'] : '';

		$calc_signature = hash_hmac( 'sha256', $raw_payload, $this->webhook_secret );

		try {
			if ( $signature !== $calc_signature ) {
				header( 'HTTP/1.1 498 Invalid signature' );
				throw new Exception( __( 'Invalid signature.', 'woocommerce-gateway-gocardless' ) );
			}

			$payload = json_decode( $raw_payload, true );
			if ( empty( $payload['events'] ) ) {
				header( 'HTTP/1.1 400 Bad request' );
				throw new Exception( __( 'Missing events in payload.', 'woocommerce-gateway-gocardless' ) );
			}

			$this->_process_webhook_payload( $payload );

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	/**
	 * Process webhook payload.
	 *
	 * @since 2.4.0
	 * @version 2.4.5
	 *
	 * @param array $payload Payload.
	 */
	protected function _process_webhook_payload( array $payload ) {
		foreach ( $payload['events'] as $event ) {
			switch ( $event['resource_type'] ) {
				case 'mandates':
					$this->_process_mandate_event( $event );
					break;
				case 'payments':
					$this->_process_payment_event( $event );
					break;
				case 'refunds':
					$this->_process_refund_event( $event );
					break;
				case 'subscriptions':
					// Since 2.4.0, subscriptions on the GoCardless side is not
					// used anymore. This handler helps to process subscriptions
					// created before 2.4.0.
					$this->_process_subscription_event( $event );
					break;
				default:
					wc_gocardless()->log( sprintf( '%s - Unhandled webhook event %s', __METHOD__, $event['resource_type'] ) );
			}
		}
	}

	/**
	 * Process mandate event from webhook.
	 *
	 * @since 2.4.5
	 * @version 2.4.5
	 *
	 * @param array $event Event payload.
	 */
	protected function _process_mandate_event( array $event ) {
		wc_gocardless()->log( sprintf( '%1$s - Handling mandate event with action "%2$s"', __METHOD__, $event['action'] ) );

		switch ( $event['action'] ) {
			case 'replaced':
				$this->_process_mandate_event_replaced( $event );
				break;
			default:
				// Only log other mandate events at this time.
				wc_gocardless()->log( sprintf( '%1$s - Unhandled mandate event with action "%2$s": %3$s', __METHOD__, $event['action'], print_r( $event, true ) ) );
		}
	}
	/**
	 * Process mandate event 'replaced' from webhook.
	 *
	 * @since 2.4.5
	 * @version 2.4.5
	 *
	 * @param array $event Event payload.
	 */
	protected function _process_mandate_event_replaced( array $event ) {
		$old_mandate = $event['links']['mandate'];
		$new_mandate = $event['links']['new_mandate'];

		$this->_update_mandate( $old_mandate, $new_mandate );
	}

	/**
	 * Update mandate with new mandate.
	 *
	 * @since 2.4.5
	 * @version 2.4.5
	 *
	 * @param string $old_mandate Old mandate to replace.
	 * @param string $new_mandate New mandate.
	 */
	protected function _update_mandate( $old_mandate, $new_mandate ) {
		if ( empty( $old_mandate ) || empty( $new_mandate ) ) {
			wc_gocardless()->log(
				sprintf( '%s - Old mandate or new mandate empty, skip updating payment tokens and orders', __METHOD__ )
			);
			return;
		}

		// Update mandates that stored as payment tokens.
		$token_ids = $this->_get_token_ids_by_mandate( $old_mandate );
		foreach ( $token_ids as $token_id ) {
			$token = WC_Payment_Tokens::get( absint( $token_id ) );

			$token->set_token( $new_mandate );
			$token->save();

			wc_gocardless()->log(
				sprintf(
					'%1$s - Updated payment token with ID %2$s from old mandate (%3$s) to new mandate (%4$s)',
					__METHOD__,
					$token_id,
					$old_mandate,
					$new_mandate
				)
			);
		}

		global $wpdb;

		// Update meta in posts (orders or subscriptions) that store mandates.
		$updated = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = %s " .
				"WHERE meta_key = '_gocardless_mandate' AND meta_value = %s",
				maybe_serialize( array( 'id' => $new_mandate ) ),
				maybe_serialize( array( 'id' => $old_mandate ) )
			)
		);
		wc_gocardless()->log(
			sprintf(
				'%1$s - Updated %2$s "_gocardless_mandate" metas from old mandate (%3$s) to new mandate (%4$s)',
				__METHOD__,
				$updated,
				$old_mandate,
				$new_mandate
			)
		);

		$updated = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = %s " .
				"WHERE meta_key = '_gocardless_mandate_id' AND meta_value = %s",
				$new_mandate,
				$old_mandate
			)
		);
		wc_gocardless()->log(
			sprintf(
				'%1$s - Updated %2$s "_gocardless_mandate_id" metas from old mandate (%3$s) to new mandate (%4$s)',
				__METHOD__,
				$updated,
				$old_mandate,
				$new_mandate
			)
		);
	}


	/**
	 * Process payment event from webhook.
	 *
	 * @since 2.4.0
	 *
	 * @param array $event Event payload.
	 *
	 * @return bool|int On success, returns the ID of the inserted row, which
	 *                  validates to true
	 */
	protected function _process_payment_event( array $event ) {
		$order = $this->get_order_from_resource( 'payment', 'id', $event['links']['payment'] );
		if ( ! $order ) {
			wc_gocardless()->log( sprintf( '%s - Could not found order with payment ID "%s" with payload: %s', __METHOD__, $event['links']['payment'], print_r( $event, true ) ) );
			return false;
		}

		$order_id       = wc_gocardless_get_order_prop( $order, 'id' );
		$payment_method = wc_gocardless_get_order_prop( $order, 'payment_method' );
		if ( 'gocardless' !== $payment_method ) {
			wc_gocardless()->log( sprintf( '%s - Order #%s is not paid via GoCardless', __METHOD__, $order_id ) );
			return false;
		}

		wc_gocardless()->log( sprintf( '%s - Handling payment event with action "%s" for order #%s', __METHOD__, $event['action'], $order_id ) );
		$new_status = '';
		switch ( $event['action'] ) {
			case 'paid_out':
			case 'confirmed':
				$order->payment_complete( $event['links']['payment'] );
				break;
			case 'failed':
				$new_status = 'failed';
				break;
			case 'cancelled':
				$new_status = 'cancelled';
				break;
			case 'charged_back':
				$new_status = 'on-hold';
				break;
		}

		if ( ! empty( $new_status ) ) {
			$note = ! empty( $event['details']['description'] ) ? $event['details']['description'] : '';
			$order->update_status( $new_status, $note );
		}

		$payment = WC_GoCardless_API::get_payment( $event['links']['payment'] );
		if ( ! is_wp_error( $payment ) && ! empty( $payment['payments'] ) ) {
			$this->update_order_resource( $order_id, 'payment', $payment['payments'] );
		}

		return add_post_meta( $order_id, '_gocardless_webhook_events', $event, false );
	}

	/**
	 * Process refund event from webhook.
	 *
	 * @since 2.4.0
	 *
	 * @param array $event Event payload.
	 *
	 * @return bool|int On success, returns the ID of the inserted row, which
	 *                  validates to true.
	 */
	protected function _process_refund_event( array $event ) {
		$order = $this->get_order_from_resource( 'refund', 'id', $event['links']['refund'] );
		if ( ! $order ) {
			wc_gocardless()->log( sprintf( '%s - Could not found order with refund ID "%s"', __METHOD__, $event['links']['refund'] ) );
			return false;
		}

		$order_id = wc_gocardless_get_order_prop( $order, 'id' );

		wc_gocardless()->log( sprintf( '%s - Handling refund event with action "%s" for order #%s', __METHOD__, $event['action'], $order_id ) );

		$refund = WC_GoCardless_API::get_refund( $event['links']['refund'] );
		if ( ! is_wp_error( $refund ) && ! empty( $refund['refunds'] ) ) {
			$this->update_order_resource( $order_id, 'refund', $refund['refunds'] );
		}

		return add_post_meta( $order_id, '_gocardless_webhook_events', $event, false );
	}

	/**
	 * Process subscription event from webhook.
	 *
	 * Subscriptions created before 2.4.0 still use GoCardless subscriptions
	 * to handle payment scheduling. This handler will cancels the subscriptions
	 * at GoCardless then store the mandate to let WCS handles future payments.
	 *
	 * @since 2.4.0
	 *
	 * @param array $event Webhook event.
	 */
	protected function _process_subscription_event( array $event ) {
		$subscription_id = '';
		if ( ! empty( $event['links']['subscription'] ) ) {
			$subscription_id = $event['links']['subscription'];
		}
		if ( empty( $subscription_id ) ) {
			return;
		}

		global $wpdb;

		// Orders created before 2.4.0 store subscription ID in `_gocardless_id`.
		// The meta is not cloned to subscriptions and renewal orders because
		// it was saved during post-process_payment.
		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_gocardless_id' AND meta_value='%s' ORDER BY post_id ASC LIMIT 1", $subscription_id ) );

		$subscriptions = array();
		if ( function_exists( 'wcs_get_subscriptions_for_order' ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $order_id, array(
				'subscription_status' => 'active',
			) );
		}
		if ( empty( $subscriptions ) ) {
			return;
		}

		wc_gocardless()->log( sprintf( '%s - Handling subscription event with action "%s" for order #%s', __METHOD__, $event['action'], $order_id ) );

		switch ( $event['action'] ) {
			case 'payment_created':
				$this->_maybe_update_subscriptions_with_mandate( $subscriptions, $subscription_id );
				break;
			case 'cancelled':
				// Only cancel WCS subscriptions when the order missing mandate.
				$mandate_in_order = $this->get_order_resource( $order_id, 'mandate', 'id' );
				if ( ! $mandate_in_order && class_exists( 'WC_Subscriptions_Manager' ) ) {
					WC_Subscriptions_Manager::cancel_subscriptions_for_order( $order_id );
				}
				break;
		}
	}

	/**
	 * Maybe update subscriptions created before 2.4.0 with mandate.
	 *
	 * @since 2.4.0
	 *
	 * @param array  $subscriptions              Array of subscription objects.
	 * @param string $gocardless_subscription_id GoCardless subscription ID.
	 */
	protected function _maybe_update_subscriptions_with_mandate( $subscriptions, $gocardless_subscription_id ) {
		// Cancel the GoCardless subscription.
		$gocardless_subscription = WC_GoCardless_API::cancel_subscription( $gocardless_subscription_id );

		if ( is_wp_error( $gocardless_subscription ) ) {
			wc_gocardless()->log( sprintf( '%s - Failed to cancel GoCardless subscription: %s', __METHOD__, $gocardless_subscription->get_error_message() ) );
			return;
		}

		if ( empty( $gocardless_subscription['subscriptions']['links']['mandate'] ) ) {
			wc_gocardless()->log( sprintf( '%s - Unexpected GoCardless subscription response. Missing mandate information.', __METHOD__ ) );
			return;
		}
		$mandate_id = $gocardless_subscription['subscriptions']['links']['mandate'];

		$mandate = WC_GoCardless_API::get_mandate( $mandate_id );
		if ( is_wp_error( $mandate ) ) {
			wc_gocardless()->log( sprintf( '%s - Failed to retrieve mandate.', __METHOD__ ) );
			return;
		}
		if ( empty( $mandate['mandates'] ) ) {
			wc_gocardless()->log( sprintf( '%s - Unexpected mandate response.', __METHOD__ ) );
		}

		foreach ( $subscriptions as $subscription ) {
			$this->update_order_resource( $subscription->id, 'mandate', $mandate['mandates'] );
		}

		$this->update_order_resource( $subscription->post->post_parent, 'mandate', $mandate['mandates'] );
	}

	/**
	 * Update GoCardless resource in order meta.
	 *
	 * @since 2.4.0
	 *
	 * @param int    $order_id      Order ID.
	 * @param string $resource_type GoCardless resource type ('payment', 'refund' etc)
	 *                              in singular noun.
	 * @param array  $resource      Resource data.
	 */
	public function update_order_resource( $order_id, $resource_type, $resource = array() ) {
		switch ( $resource_type ) {
			case 'redirect_flow':
				update_post_meta( $order_id, '_gocardless_redirect_flow',    $resource );
				update_post_meta( $order_id, '_gocardless_redirect_flow_id', $resource['id'] );
				break;
			case 'mandate':
				// Don't save other mandate information as it subject to changes
				// over time. The same mandate can be used by more than one order,
				// so it'd be too much to sync all orders if webhook pushes
				// mandate events.
				update_post_meta( $order_id, '_gocardless_mandate',    array( 'id' => $resource['id'] ) );
				update_post_meta( $order_id, '_gocardless_mandate_id', $resource['id'] );
				break;
			case 'payment':
				update_post_meta( $order_id, '_gocardless_payment',        $resource );
				update_post_meta( $order_id, '_gocardless_payment_id',     $resource['id'] );
				update_post_meta( $order_id, '_gocardless_payment_status', $resource['status'] );
				break;
			case 'refund':
				update_post_meta( $order_id, '_gocardless_refund',    $resource );
				update_post_meta( $order_id, '_gocardless_refund_id', $resource['id'] );
				break;
		}
	}

	/**
	 * Get GoCardless resource from order meta.
	 *
	 * @since 2.4.0
	 *
	 * @param int         $order_id      Order ID.
	 * @param string      $resource_type GoCardless resource type ('mandate', 'payment', etc)
	 *                                   in singular noun. See self::update_order_resource.
	 * @param null|string $key           Key in resource array (e.g. 'id', 'status', etc).
	 *                                   If null then resource array is returned.
	 *
	 * @return mixed See get_post_meta return value.
	 */
	public function get_order_resource( $order_id, $resource_type, $key = null ) {
		if ( is_null( $key ) ) {
			$meta_key = sprintf( '_gocardless_%s', $resource_type );
		} else {
			$meta_key = sprintf( '_gocardless_%s_%s', $resource_type, $key );
		}

		return get_post_meta( $order_id, $meta_key, true );
	}

	/**
	 * Get order from given resource value.
	 *
	 * @since 2.4.0
	 *
	 * @param string $resource_type GoCardless resource type ('payment', 'refund' etc)
	 *                              in singular noun. Mandate is excluded because
	 *                              one mandate can be used in more than one order.
	 * @param string $key           Meta Key in resource array (e.g. 'id', 'status', etc).
	 * @param string $value         Value of a given resource key.
	 *
	 * @return WC_Order|bool Order object or false
	 */
	public function get_order_from_resource( $resource_type, $key, $value ) {
		global $wpdb;

		$meta_key = sprintf( '_gocardless_%s_%s', $resource_type, $key );

		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $value ) );
		if ( ! $order_id ) {
			return false;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return false;
		}

		// Since metas from parent order is copied to subscription and renewal
		// orders, the query might return subscription instead of order.
		$order_id = wc_gocardless_get_order_prop( $order, 'id' );
		if ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ) ) {
			$subscription = wcs_get_subscription( $order_id );
			$last_order   = is_callable( array( $subscription, 'get_last_order' ) )
				? $subscription->get_last_order()
				: null;

			$order = $last_order ? wc_get_order( $last_order ) : $order;
		}

		return $order;
	}

	/**
	 * Get description to be sent to GoCardless from order.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order|int $order Order object or order ID.
	 *
	 * @return string Order items description
	 */
	protected function _get_description_from_order( $order ) {
		$order = wc_get_order( $order );
		if ( ! $order ) {
			return '';
		}

		$order_id = wc_gocardless_get_order_prop( $order, 'id' );

		$items = array();
		foreach ( $order->get_items() as $item ) {
			/* translators: product item x qty to send to GoCardless */
			$items[] = sprintf( __( '%1$s × %2$s', 'woocommerce-gateway-gocardless' ), $item['name'], $item['qty'] );
		}
		$description = sprintf( __( 'Order #%s', 'woocommerce-gateway-gocardless' ), $order_id );

		if ( ! empty( $items ) ) {
			$description .= ' (' . implode( ', ', $items ) . ')';
		}

		// Truncate description due to 100 character GoCardless API limit.
		$description = html_entity_decode( wc_trim_string( $description, 100 ), ENT_NOQUOTES, 'UTF-8' );

		// Deprecated hook because of gocardless typo. Since apply_filters_deprecated
		// was introduced in 4.6, we need to support older WP version with the
		// cost of no warning being thrown.
		if ( function_exists( 'apply_filters_deprecated' ) ) {
			$description = apply_filters_deprecated(
				'woocommerce_gocarldess_payment_description',
				array( $description, $order ),
				'2.4.2',
				'woocommerce_gocardless_payment_description'
			);
		} else {
			$description = apply_filters( 'woocommerce_gocarldess_payment_description', $description, $order );
		}

		return apply_filters( 'woocommerce_gocardless_payment_description', $description, $order );
	}

	/**
	 * Get transaction URL format.
	 *
	 * @since 2.4.0
	 *
	 * @return string URL format
	 */
	public function get_transaction_url_format() {
		return $this->testmode ? 'https://manage-sandbox.gocardless.com/payments/%s' : 'https://manage.gocardless.com/payments/%s';
	}

	/**
	 * Save customer token (mandate and basic bank account info).
	 *
	 * @see https://github.com/woocommerce/woocommerce/wiki/Payment-Token-API.
	 *
	 * @since 2.4.0
	 *
	 * @param int   $customer_id Customer ID.
	 * @param array $mandate     Direct debit mandate.
	 *
	 * @return bool True if saved successfully.
	 */
	protected function _save_customer_token( $customer_id, $mandate ) {
		if ( ! $customer_id ) {
			return false;
		}

		if ( ! class_exists( 'WC_Payment_Token_Direct_Debit' ) ) {
			return false;
		}

		// Retrieves bank account associated with the given mandate. This will
		// be used as saved payment method in checkout page so buyer has context
		// of their saved bank account.
		$bank_account_id = $mandate['links']['customer_bank_account'];
		$bank_account    = WC_GoCardless_API::get_customer_bank_account( $bank_account_id );
		if ( is_wp_error( $bank_account ) || empty( $bank_account['customer_bank_accounts'] ) ) {
			return false;
		}
		$bank_account = $bank_account['customer_bank_accounts'];

		$token = new WC_Payment_Token_Direct_Debit();

		// Set basic info required by token API.
		$token->set_token( $mandate['id'] );
		$token->set_gateway_id( $this->id );
		$token->set_user_id( $customer_id );

		// Save bank account info for display purpose.
		$token->set_scheme( $mandate['scheme'] );
		$token->set_account_holder_name( $bank_account['account_holder_name'] );
		$token->set_account_number_ending( $bank_account['account_number_ending'] );
		$token->set_bank_name( $bank_account['bank_name'] );

		return $token->save();
	}

	/**
	 * Alter saved payment method item for direct debit method.
	 *
	 * @since 2.4.0
	 *
	 * @param array            $item          Item of payment method.
	 * @param WC_Payment_Token $payment_token The payment token associated with
	 *                                        this method entry.
	 *
	 * @return array Filtered item for direct debit.
	 */
	public function saved_payment_methods_list_item( $item, $payment_token ) {
		if ( 'direct_debit' !== strtolower( $payment_token->get_type() ) ) {
			return $item;
		}

		$item['method']['display_name'] = $payment_token->get_display_name();
		$item['method']['brand']        = '';
		$item['expires']                = '';

		return $item;
	}

	/**
	 * Alter display of method column in customer's saved payment methods.
	 *
	 * @since 2.4.0
	 *
	 * @param array $method Item of payment method.
	 */
	public function saved_payment_methods_column_method( $method ) {
		if ( ! empty( $method['method']['gateway'] ) && 'gocardless' === $method['method']['gateway'] ) {
			echo esc_html( $method['method']['display_name'] );
		} else {
			echo $this->_get_default_column_method_display( $method );
		}
	}

	/**
	 * This is needed because defalut template of payment methods will run the
	 * callback to other saved methods.
	 *
	 * @since 2.4.0
	 *
	 * @param array $method Item of payment method.
	 */
	protected function _get_default_column_method_display( $method ) {
		if ( ! empty( $method['method']['last4'] ) ) {
			return sprintf( __( '%1$s ending in %2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
		}
		return esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
	}

	/**
	 * Get all payment token IDs by mandate ID.
	 *
	 * @since 2.4.5
	 * @version 2.4.5
	 *
	 * @param string $mandate_id GoCardless mandate.
	 *
	 * @return array List of token IDs.
	 */
	protected function _get_token_ids_by_mandate( $mandate_id ) {
		global $wpdb;

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT token_id FROM {$wpdb->prefix}woocommerce_payment_tokens WHERE token = %s", $mandate_id
			)
		);
	}
}
