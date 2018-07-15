<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Settings' ) ) {
	class WC_AF_Settings {

		const SETTINGS_NAMESPACE = 'anti_fraud';

		/**
		 * Get the setting fields
		 *
		 * @since  1.0.0
		 * @access private
		 *
		 * @return array $setting_fields
		 */
		private function get_fields() {

			$score_options = array();
			for ( $i = 100; $i > - 1; $i -- ) {
				if ( ( $i % 5 ) == 0 ) {
					$score_options[$i] = $i;
				}
			}

			$setting_fields = array(
				'section_title' => array(
					'name' => __( 'Fraud Detection Automated Actions', 'woocommerce-anti-fraud' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_title'
				),
				'cancel_score'  => array(
					'name'     => __( 'Cancel Score', 'woocommerce-anti-fraud' ),
					'type'     => 'select',
					'options'  => $score_options,
					'desc'     => __( 'Orders with a score equal to or greater than this number will be automatically cancelled. Select 0 to disable.', 'woocommerce-anti-fraud' ),
					'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_cancel_score',
					'css'         => 'display: block;',
					'default' => '90',
				),
				'hold_score'    => array(
					'name'     => __( 'On-hold Score', 'woocommerce-anti-fraud' ),
					'type'     => 'select',
					'options'  => $score_options,
					'desc'     => __( 'Orders with a score equal to or greater than this number will be automatically set on hold. Select 0 to disable.', 'woocommerce-anti-fraud' ),
					'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_hold_score',
					'css'         => 'display: block;',
					'default' => '70',
				),
				'email_score'   => array(
					'name'     => __( 'Email Notification Score', 'woocommerce-anti-fraud' ),
					'type'     => 'select',
					'options'  => $score_options,
					'desc'     => __( 'An admin email notification will be sent if an orders scores equal to or greater than this number. Select 0 to disable.', 'woocommerce-anti-fraud' ),
					'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_email_score',
					'css'         => 'display: block;',
					'default' => '50',
				),
				'whitelist'     => array(
					'name'        => __( 'Email Whitelist', 'woocommerce-anti-fraud' ),
					'type'        => 'textarea',
					'desc'        => __( "Above automated actions don't apply to orders from customers with email addresses entered here. Enter one email address per line.", 'woocommerce-anti-fraud' ),
					'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_whitelist',
					'css'         => 'width:100%; height: 100px;',
					'default'     => '',
				),
				'section_end'   => array(
					'type' => 'sectionend',
					'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_sectionend'
				)
			);

			/**
			 * Filter: 'wc_settings_tab_anti_fraud' - Allow altering extension setting fields
			 *
			 * @api array $setting_fields The fields
			 */

			return apply_filters( 'wc_settings_tab_' . self::SETTINGS_NAMESPACE, $setting_fields );
		}

		/**
		 * Get an option set in our settings tab
		 *
		 * @param $key
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return String
		 */
		public function get_option( $key ) {
			$fields = $this->get_fields();

			/**
			 * Filter: 'wc_settings_$key' - Allow altering one option
			 *
			 * @api array $value The option value
			 */

			return apply_filters( 'wc_option_' . $key, get_option( 'wc_settings_' . self::SETTINGS_NAMESPACE . '_' . $key, ( ( isset( $fields[$key] ) && isset( $fields[$key]['default'] ) ) ? $fields[$key]['default'] : '' ) ) );
		}

		/**
		 * Setup the WooCommerce settings
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function setup() {
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 70 );
			add_action( 'woocommerce_settings_tabs_' . self::SETTINGS_NAMESPACE, array( $this, 'tab_content' ) );
			add_action( 'woocommerce_update_options_' . self::SETTINGS_NAMESPACE, array( $this, 'update_settings' ) );
		}

		/**
		 * Add a settings tab to the settings page
		 *
		 * @param array $settings_tabs
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function add_settings_tab( $settings_tabs ) {
			$settings_tabs[self::SETTINGS_NAMESPACE] = __( 'Anti Fraud', 'woocommerce-anti-fraud' );

			return $settings_tabs;
		}

		/**
		 * Output the tab content
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function tab_content() {
			woocommerce_admin_fields( $this->get_fields() );
		}

		/**
		 * Update the settings
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function update_settings() {
			woocommerce_update_options( $this->get_fields() );
		}

	}
}
