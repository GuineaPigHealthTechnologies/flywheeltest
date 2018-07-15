<?php
/**
 * WooCommerce Bulk Download Settings Class
 *
 * @package   WooCommerce Bulk Download
 * @author    Captain Theme <info@captaintheme.com>
 * @license   GPL-2.0+
 * @link      http://captaintheme.com
 * @copyright 2014 Bryce Adams
 * @since     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Bulk_Download_Settings Class
 *
 * @package  WooCommerce Bulk Download
 * @author   Captain Theme <info@captaintheme.com>
 * @since    1.1.0
 */

if ( ! class_exists( 'WC_Bulk_Download_Settings' ) ) {

	class WC_Bulk_Download_Settings {

		protected static $instance = null;

		private function __construct() {

			// WCBD Settings
			add_filter( 'woocommerce_downloadable_products_settings', array( $this, 'download_zip_button_text' ) );

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.1.0
		 */

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}


		/**
		 * Add Zip Button Text Settings
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.1.0
		 */

		public function download_zip_button_text( $settings ) {

			$settings[] = array(

				'name'     => __( 'WooCommerce Bulk Download', 'woocommerce-bulk-download' ),
				'type'     => 'title',
				'desc'     => 'The following options are used to configure the Bulk Download extension.',
				'id'       => 'woocommerce_bulk_download'

			);

			$settings[] = array(

				'name'     => __( 'Download Zip Button Text', 'woocommerce-bulk-download' ),
				'desc_tip' => __( 'The label for the download zip button.', 'woocommerce-bulk-download' ),
				'id'       => 'wcbd_download_zip_button_text',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'desc'     => __( 'Default (if blank): Download All Files (.zip)', 'woocommerce-bulk-download' ),

			);

			$settings[] = array(

				'name'     => __( 'Select All Text', 'woocommerce-bulk-download' ),
				'desc_tip' => __( 'The label for the Select All checkbox.', 'woocommerce-bulk-download' ),
				'id'       => 'wcbd_select_all_text',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'desc'     => __( 'Default (if blank): Select All', 'woocommerce-bulk-download' ),

			);

			$settings[] = array(

				'name'     => __( 'Download All Order Downloads Text', 'woocommerce-bulk-download' ),
				'id'       => 'wcbd_download_order_downloads_text',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'desc'     => __( 'Default (if blank): Download Order Files (.zip)', 'woocommerce-bulk-download' ),

			);

			$settings[] = array( 'type' => 'sectionend', 'id' => 'woocommerce_bulk_download' );

			return $settings;

		}


	}

}
