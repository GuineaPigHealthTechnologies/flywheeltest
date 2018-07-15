<?php
/**
 * WooCommerce Bulk Download Product Options Class
 *
 * @package   WooCommerce Bulk Download
 * @author    allendav <allendav@automattic.com>
 * @license   GPL-2.0+
 * @link      http://automattic.com
 * @copyright 2015 Automattic
 * @since     1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Bulk_Download_Product_Options Class
 *
 * @package  WooCommerce Bulk Download
 * @author   allendav <allendav@automattic.com>
 * @since    1.2.0
 */

if ( ! class_exists( 'WC_Bulk_Download_Product_Options' ) ) {

	class WC_Bulk_Download_Product_Options {

		protected static $instance = null;

		private function __construct() {

			// WCBD Production Options - Downloads
			add_action( 'woocommerce_product_options_downloads', array( $this, 'woocommerce_product_options_downloads' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta' ) );

			// Styles
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.2.0
		 */

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

		/**
		 * Add Download Production Options Notice Box
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.2.0
		 */

		public function woocommerce_product_options_downloads( ) {

			global $post;

			$bulk_download_enabled = 'yes'; // enabled by default

			if ( ! empty( $post->ID ) ) {

				$bulk_download_disabled = get_post_meta( $post->ID, '_bulk_download_disabled', true );

				// Flip it for the UI
				$bulk_download_enabled = ( 'yes' === $bulk_download_disabled ) ? '' : 'yes';

			}

			woocommerce_wp_checkbox(
				array(
					'id' => '_bulk_download_enabled',
					'label' => __( 'Enable Bulk Download', 'woocommerce-bulk-download' ),
					'value' => $bulk_download_enabled,
					'description' => __( 'Allow bulk download of this product.', 'woocommerce-bulk-download' )
				)
			);

			?>
			<div class="form-field _download_notice_field">
				<strong>
					<?php _e( "Note: In order for WooCommerce Bulk Downloads to work properly, be sure to use the 'Choose file' button to add Downloadable Files, or place them in wp-content/uploads.", 'woocommerce-bulk-download' ); ?>
				</strong>
			</div>
			<?php

		}

		/**
		 * Save Download Options
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.2.0
		 */

		public function woocommerce_process_product_meta( $post_id ) {

			$bulk_download_enabled = isset( $_POST['_bulk_download_enabled'] ) ? 'yes' : '';

			// Flip it for the DB
			$bulk_download_disabled = ( 'yes' === $bulk_download_enabled ) ? '' : 'yes';

			update_post_meta( $post_id, '_bulk_download_disabled', $bulk_download_disabled );

		}


		/**
		 * Add Download Production Options Styles
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.2.0
		 */

		public function admin_enqueue_scripts( $hook ) {

			global $post;

			if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {
				if ( 'product' === $post->post_type ) {
					wp_register_style( 'wcbd-admin-css', plugins_url( 'assets/css/admin.css', dirname( __FILE__ ) ) );
					wp_enqueue_style( 'wcbd-admin-css' );
				}
			}

		}

	}

}
