<?php
/**
 * WooCommerce Bulk Download Class
 *
 * @package   WooCommerce Bulk Download
 * @author    Captain Theme <info@captaintheme.com>
 * @license   GPL-2.0+
 * @link      http://captaintheme.com
 * @copyright 2014 Bryce Adams
 * @since     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Bulk_Download Class
 *
 * @package  WooCommerce Bulk Download
 * @author   Captain Theme <info@captaintheme.com>
 * @since    1.1.1
 */

if ( ! class_exists( 'WC_Bulk_Download' ) ) {
	class WC_Bulk_Download {

		const VERSION = '1.2.5';

		protected static $instance = null;

		private $user_has_a_bulk_downloadable_purchase = false;

		private function __construct() {
			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Run create_downloads_zip - @TODO add at a better time like only when this specific page loads
			add_action( 'init', array( $this, 'create_downloads_zip' ) );
			add_action( 'init', array( $this, 'create_order_download_zip' ) );

			// Scripts & Styles
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			// Delete zip file
			add_action( 'wcbd_hourly_event_hook', array( $this, 'delete_zip' ) );

			// Activate plugin when new blog is added
			add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

			// Add 'Download' Checkboxes
			add_action( 'woocommerce_available_download_start', array( $this, 'add_checkbox' ), 15 );
			add_action( 'woocommerce_account_downloads_column_download-file', array( $this, 'add_checkbox' ), 15 );

			// Add 'Download Files' button form
			add_action( 'woocommerce_before_available_downloads', array( $this, 'before_zip_button_form' ), 15 );
			add_action( 'woocommerce_after_available_downloads', array( $this, 'after_zip_button_form' ), 15 );

			// Add downloads section on order received page
			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'order_received_form' ) );

			// Add an action link pointing to the WC settings page where the WCBD settings are
			$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'woocommerce-bulk-download.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

			// Add a shortcode
			add_shortcode( 'my_downloads', array( $this, 'my_downloads_shortcode' ) );

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

		/**
		 * Add permalinks settings action link to the plugins page.
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function add_action_links( $links ) {
			return array_merge(
				array(
					'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings' ) . '">' . __( 'Settings', 'woocommerce-bulk-download' ) . '</a>',
				),
				$links
			);
		}

		/**
		 * Load Scripts
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function load_scripts( $force = false ) {
			if ( $force || is_account_page() || is_view_order_page() || is_order_received_page() ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				// Enqueue jQuery for the 'select all' script
				wp_enqueue_script( 'jquery' );

				// Register Scripts / Styles
				wp_register_script( 'wcbd-js', plugins_url( 'assets/js/wcbd' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ) );
				wp_register_style( 'wcbd-css', plugins_url( 'assets/css/wcbd.css', dirname( __FILE__ ) ) );

				// Enqueue Scripts / Styles
				wp_enqueue_script( 'wcbd-js' );
				wp_enqueue_style( 'wcbd-css' );
			}
		}

		/**
		 * Add a checkbox next to each download
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.1.1
		 */
		public function add_checkbox( $download ) {
			$this->check_for_any_bulk_downloadable_purchase();

			if ( $this->user_has_a_bulk_downloadable_purchase ) {
				wc_get_template(
					'myaccount/available-downloads-zip-checkbox.php',
					array(
						'disabled'    => self::is_bulk_download_permitted( $download ) ? '' : 'disabled',
						'download'    => $download,
						'show_cb'     => ! is_view_order_page(),
					),
					'woocommerce-bulk-download/',
					WC_BULK_DOWNLOAD_TEMPLATE_PATH
				);
			}
		}

		/**
		 * Start up the zip button form
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function before_zip_button_form() {
			$this->check_for_any_bulk_downloadable_purchase();

			if ( $this->user_has_a_bulk_downloadable_purchase ) {
				wc_get_template(
					'myaccount/before-available-downloads-zip-form.php',
					array(),
					'woocommerce-bulk-download/',
					WC_BULK_DOWNLOAD_TEMPLATE_PATH
				);
			}
		}

		/**
		 * Add the button that will create the zip file
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function after_zip_button_form() {
			if ( $this->user_has_a_bulk_downloadable_purchase ) {
				// Download Zip Button Text
				$zip_button_text = __( 'Download All Files (.zip)', 'woocommerce-bulk-download' );

				if ( get_option( 'wcbd_download_zip_button_text' ) ) {
					$zip_button_text = get_option( 'wcbd_download_zip_button_text', __( 'Download All Files (.zip)', 'woocommerce-bulk-download' ) );
				}

				// Select All Text
				$select_all_text = __( 'Select All', 'woocommerce-bulk-download' );

				if ( get_option( 'wcbd_select_all_text' ) ) {
					$select_all_text = esc_html( get_option( 'wcbd_select_all_text', __( 'Select All', 'woocommerce-bulk-download' ) ) );
				}

				wc_get_template(
					'myaccount/after-available-downloads-zip-form.php',
					array(
						'zip_button_text' => $zip_button_text,
						'select_all_text' => $select_all_text,
					),
					'woocommerce-bulk-download/',
					WC_BULK_DOWNLOAD_TEMPLATE_PATH
				);
			}
		}

		/**
		 * Downloads section on order received page
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.1.0
		 */
		public function order_received_form( $order ) {
			if ( sizeof( $order->get_items() ) > 0 ) {
				$has_downloadable_file = false;

				foreach ( (array) $order->get_items() as $item ) {
					$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );

					if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {
						$downloadable_files = version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_item_downloads( $item ) : $item->get_item_downloads();

						// Check each downloadable in the item separately
						foreach ( (array) $downloadable_files as $download_id => $file ) {
							$product_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();

							$download_data = array(
								'order_id'    => version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(),
								'product_id'  => $product_id,
								'download_id' => $download_id,
							);

							if ( self::is_bulk_download_permitted( $download_data ) ) {
								$has_downloadable_file = true;
								break;
							}
						}
					}

					if ( $has_downloadable_file ) {
						break; // no need to keep looking at other items in the order
					}
				}

				// If the order has at least one downloadable file
				if ( $has_downloadable_file ) {
					$zip_button_text = __( 'Download Order Files (.zip)', 'woocommerce-bulk-download' );

					if ( get_option( 'wcbd_download_order_downloads_text' ) ) {
						$zip_button_text = get_option( 'wcbd_download_order_downloads_text', __( 'Download Order Files (.zip)', 'woocommerce-bulk-download' ) );
					}

					wc_get_template(
						'myaccount/order-zip-form.php',
						array(
							'order_id' => version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(),
							'zip_button_text' => $zip_button_text,
						),
						'woocommerce-bulk-download/',
						WC_BULK_DOWNLOAD_TEMPLATE_PATH
					);
				}
			}
		}

		/**
		 * Add the my_downloads shortcode
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.1.x
		 */
		public function my_downloads_shortcode( $atts ) {
			$this->load_scripts( true );

			ob_start();
			wc_get_template( 'myaccount/my-downloads.php' );
			return ob_get_clean();
		}

		/**
		 * Put order files into the create_zip method on ordr received page
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.1.0
		 */
		public function create_order_download_zip() {
			if ( isset( $_POST['create-order-zip'] ) && '1' == $_POST['create-order-zip'] ) {
				$order_id = intval( sanitize_text_field( $_POST['wcbd-download-data'] ) );

				$user_id = get_current_user_id();

				$order = wc_get_order( $order_id );

				if ( ! $order || $user_id !== $order->get_user_id() ) {
					wp_die( 'Cheatin&#8217; huh?', 'woocommerce-bulk-download' );

				}

				$zip_location = $this->zip_location( '/' ) . $this->zip_name() . '.zip';

				$result = $this->create_zip( $this->get_order_downloads( $order ), $zip_location, false );

				echo $this->download_file( $zip_location );
			}
		}

		/**
		 * Gets whether the user has anything that can be bulk downloaded
		 * since it is possible every downloadable product they've bought
		 * has had bulk download disabled.  ONLY checks the product meta
		 * _bulk_download_disabled to avoid redundant queries (for count
		 * and expiry) that the available downloads zip form code calls
		 * anyways
		 *
		 * We are storing this in an instance variable of this class to
		 * to avoid having each template part (before, after, checkboxes)
		 * run this potentially expensive query
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.1.2
		 */
		public function check_for_any_bulk_downloadable_purchase() {
			$this->user_has_a_bulk_downloadable_purchase = false;

			$products = WC()->customer->get_downloadable_products();

			foreach ( (array) $products as $product ) {

				$bulk_download_disabled = get_post_meta( $product['product_id'], '_bulk_download_disabled', true );

				if ( 'yes' !== $bulk_download_disabled ) {

					$this->user_has_a_bulk_downloadable_purchase = true;

					break; // we found one, no need to keep looking
				}
			}
		}

		/**
		 * Get all downloads available for a customer
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 * @return  array()
		 */
		public function get_order_downloads( $order ) {
			$upload_dir = wp_upload_dir();

			$all_downloads = array();

			foreach ( (array) $order->get_items() as $item ) {
				$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );

				if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {
					$download_files = version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_item_downloads( $item ) : $item->get_item_downloads();

					foreach ( (array) $download_files as $download_id => $file ) {
						$product_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();

						$download_data = array(
							'order_id'    => version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(),
							'product_id'  => $product_id,
							'download_id' => $download_id,
						);

						if ( self::is_bulk_download_permitted( $download_data ) ) {
							// get download file's URL and URL decode it
							$download_file_url = urldecode( $file['file'] );

							// convert download file URL to download file directory path
							$all_downloads[] = [
								'path'       => str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $download_file_url ),
								'order_id'   => $download_data['order_id'],
								'product_id' => $download_data['product_id'],
							];

							$this->update_download_count( $download_data );
						}
					}
				}
			}

			if ( 0 === count( $all_downloads ) ) {
				wp_die( 'Sorry, all downloads have expired, have reached their download limit, or are no longer available for bulk download.', 'woocommerce-bulk-download' );
			}

			return $all_downloads;
		}

		/**
		 * Put download files into the create_zip method
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function create_downloads_zip() {
			if ( isset( $_POST['create-zip'] ) && '1' == $_POST['create-zip'] ) {
				$user_id = get_current_user_id();

				$zip_location = $this->zip_location( '/' ) . $this->zip_name() . '.zip';

				$result = $this->create_zip( $this->get_all_downloads(), $zip_location, false );

				echo $this->download_file( $zip_location );
			}
		}

		/**
		 * Get all downloads available for a customer
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.1.1
		 * @return  array()
		 */
		public function get_all_downloads() {
			$upload_dir = wp_upload_dir();

			$all_downloads = array();

			$downloads = WC()->customer->get_downloadable_products();
			if ( $downloads ) {
				foreach ( $downloads as $download ) :
					if ( isset( $_POST[ 'dwn-' . $download['download_id'] ] ) ) {
						// make sure that particular downloadable file is still downloadable
						if ( self::is_bulk_download_permitted( $download ) ) {
							// get download file's URL and URL decode it
							$download_file_url = urldecode( $download['file']['file'] );

							// convert download file URL to download file directory path
							$all_downloads[] = [
								'path'       => str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $download_file_url ),
								'order_id'   => $download['order_id'],
								'product_id' => $download['product_id'],
							];

							// increment the file's download count
							$this->update_download_count( $download );
						}
					}

				endforeach;
			}

			if ( 0 === count( $all_downloads ) ) {
				wp_die( 'Sorry, all downloads have expired, have reached their download limit, or are no longer available for bulk download.', 'woocommerce-bulk-download' );
			}

			return $all_downloads;
		}

		/**
		 * Get WooCommerce Bulk Download Directory
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 * @return  string
		 */
		public static function zip_location( $slash = '' ) {
			$upload = wp_upload_dir();
			$upload_dir = $upload['basedir'] . '/wcbd_zips' . $slash;

			return $upload_dir;
		}

		/**
		 * Make Zip Name
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 * @return  string
		 */

		public function zip_name() {
			// Site title (in slug form)
			$site = sanitize_title_with_dashes( get_bloginfo( 'name' ) );

			// Current user ID
			$user_id = get_current_user_id();

			// Random number
			$rand = wp_rand();

			// Put it all together
			$zip_name = $site . '-' . $user_id . '-' . $rand;

			// Return it all
			return $zip_name;
		}

		/**
		 * Creates a compressed ZIP File (credit: http://davidwalsh.name/create-zip-php)
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function create_zip( $files = array(), $destination = '', $overwrite = false ) {
			//if the zip file already exists and overwrite is false, return false
			if ( file_exists( $destination ) && ! $overwrite ) {
				return false;
			}

			//vars
			$valid_files = array();

			//if files were passed in...
			if ( is_array( $files ) ) {

				//cycle through each file
				foreach ( $files as $file ) {

					//make sure the file exists
					if ( file_exists( $file['path'] ) ) {

						$valid_files[] = $file;
					}
				}
			}

			//if we have good files...
			if ( count( $valid_files ) ) {
				//create the archive
				$zip = new ZipArchive();

				if ( $zip->open( $destination, $overwrite ? ZipArchive::OVERWRITE : ZipArchive::CREATE ) !== true ) {
					return false;
				}

				//add the files
				foreach ( $valid_files as $file ) {
					if ( 'application/pdf' === mime_content_type( $file['path'] ) ) {
						$file['path'] = apply_filters( 'woocommerce_pdf_watermark_file', $file['path'], $file['order_id'], $file['product_id'] );
					}

					$zip->addFile( $file['path'], basename( $file['path'] ) );
				}

				//close the zip -- done!
				$zip->close();

				//check to make sure the file exists
				return file_exists( $destination );

			} else {
				return false;
			}
		}

		/**
		 * Gets whether a specific downloadable can still be downloaded
		 * Similar to WC_Download_Handler private static functions check_download_expiry
		 * and check_downloads_remaining but also checks for product
		 * meta _bulk_download_disabled
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.1.2
		 */
		public static function is_bulk_download_permitted( $download ) {
			if ( ! array_key_exists( 'product_id', $download ) || empty( $download['product_id'] ) ) {
				return false;
			}

			$bulk_download_disabled = get_post_meta( $download['product_id'], '_bulk_download_disabled', true );

			if ( 'yes' === $bulk_download_disabled ) {
				return false;
			}

			$row = self::get_downloadable_product_permissions( $download );

			if ( false === $row ) {
				return false;
			}

			if ( '0' == $row->downloads_remaining ) {
				return false;
			}

			if ( $row->access_expires > 0 && strtotime( $row->access_expires ) < strtotime( 'midnight', current_time( 'timestamp' ) ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Gets the downloadable product permssions
		 * Similar to WC_Download_Handler private static function get_download_data
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.1.2
		 */

		public static function get_downloadable_product_permissions( $download ) {
			global $wpdb;

			$required_keys = array( 'order_id', 'product_id', 'download_id' );

			foreach ( (array) $required_keys as $required_key ) {
				if ( ! array_key_exists( $required_key, $download ) ) {
					return false;
				}

				if ( empty( $download[ $required_key ] ) ) {
					return false;
				}
			}

			$order_id = $download['order_id'];
			$product_id = $download['product_id'];
			$download_id = $download['download_id'];

			// given the order_id, product_id and download_id, get the permission_id
			$row = $wpdb->get_row(
				$wpdb->prepare( "
					SELECT *
					FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
					WHERE order_id = %d
					AND product_id = %d
					AND download_id = %s",
					$order_id, $product_id, $download_id
				)
			);

			if ( null == $row ) {
				return false;
			}

			return $row;
		}

		/**
		 * Updates the download_count and, if needed, the downloads_remaining
		 * for a given order ID + product ID + download ID
		 * Similar to WC_Download_Handler private static function count_download
		 *
		 * @package WooCommerce Bulk Download
		 * @author  allendav <allendav@automattic.com>
		 * @since   1.1.2
		 */
		public static function update_download_count( $download ) {
			global $wpdb;

			$permissions_table = $wpdb->prefix . 'woocommerce_downloadable_product_permissions';

			$row = self::get_downloadable_product_permissions( $download );

			if ( false === $row ) {
				return false;
			}

			$wpdb->update(
				$permissions_table,
				array(
					'download_count'      => $row->download_count + 1,
					'downloads_remaining' => $row->downloads_remaining > 0 ? $row->downloads_remaining - 1 : $row->downloads_remaining,
				),
				array(
					'permission_id' => absint( $row->permission_id ),
				),
				array( '%d', '%s' ),
				array( '%d' )
			);

			return $row->permission_id;
		}

		/**
		 * Download the ZIP File (credit: http://davidwalsh.name/php-force-download)
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function download_file( $file = '' ) {
			// grab the requested file's name
			$file_name = $file;

			// make sure it's a file before doing anything!
			if ( is_file( $file_name ) ) {
				// required for IE
				if ( ini_get( 'zlib.output_compression' ) ) {

					ini_set( 'zlib.output_compression', 'Off' );

				}

				// get the file mime type using the file extension
				switch ( strtolower( substr( strrchr( $file_name, '.' ), 1 ) ) ) {
					case 'pdf':
						$mime = 'application/pdf';
						break;
					case 'zip':
						$mime = 'application/zip';
						break;
					case 'jpeg':
					case 'jpg':
						$mime = 'image/jpg';
						break;
					default:
						$mime = 'application/force-download';
				}

				header( 'Pragma: public' ); 	// required
				header( 'Expires: 0' );		// no cache
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $file_name ) ) . ' GMT' );
				header( 'Cache-Control: private', false );
				header( 'Content-Type: ' . $mime );
				header( 'Content-Disposition: attachment; filename="' . basename( $file_name ) . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Length: ' . filesize( $file_name ) );	// provide file size
				header( 'Connection: close' );
				readfile( $file_name );		// push it out
				exit();
			}
		}

		/**
		 * Delete Zip File after 1 Hour
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public function delete_zip() {
			$hours = 1;
			$path = $this->zip_location( '/' );

			// Open the directory
			$handle = opendir( $path );
			if ( $handle ) {
				// Loop through the directory
				while ( false !== ( $file = readdir( $handle ) ) ) {
					// Check the file we're doing is actually a file
					if ( is_file( $path . $file ) ) {
						// Check if the file is older than X days old
						if ( filemtime( $path . $file ) < ( time() - ( $hours * 60 * 60 ) ) ) {
							// Do the deletion
							unlink( $path . $file );
						}
					}
				}
			}
		}

		/**
		 * Fire when plugin is activated
		 *
		 * @package WooCommerce Bulk Download
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */
		public static function activate() {
			$upload_dir = self::zip_location();

			// Make WCBD Zips Folder in /uploads/ directory
			if ( ! is_dir( $upload_dir ) ) {
				wp_mkdir_p( $upload_dir );
			}

			// Schedule Hourly event to check if there are old zips to be deleted
			wp_schedule_event( time(), 'hourly', 'wcbd_hourly_event_hook' );
		}

		/**
		  * Fire when plugin is deactivated
		  *
		  * @package WooCommerce Bulk Download
		  * @author  Captain Theme <info@captaintheme.com>
		  * @since   1.0.0
		  */
		public static function deactivate() {
			// Delete Scheduled Hourly event on plugin deactivation
			wp_clear_scheduled_hook( 'wcbd_hourly_event_hook' );
		}

		/**
		  * Load plugin textdomain for i18n
		  *
		  * @package WooCommerce Bulk Download
		  * @author  Captain Theme <info@captaintheme.com>
		  * @since   1.0.0
		  */
		public function load_plugin_textdomain() {
			$domain = 'woocommerce-bulk-download';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
		}
	}
}
