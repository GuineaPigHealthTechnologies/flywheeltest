<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Compatibility' ) ) {
	/**
	 * Adds compatibility functions for ensuring Waitlist will work with different versions of WooCommerce
	 */
	class Pie_WCWL_Compatibility {

		/**
		 * Retrieves an array of parent product IDs based on current WooCommerce version
		 *
		 * @param \WC_Product $product product object
		 *
		 * @since 1.8.0
		 *
		 * @return array parent IDs
		 */
		public static function get_parent_id( WC_Product $product ) {
			$parent_ids = array();
			if ( $parent_id = $product->get_parent_id() ) {
				$parent_ids[] = $parent_id;
			}
			$parent_ids = array_merge( $parent_ids, self::get_grouped_parent_id( $product ) );

			return $parent_ids;
		}

		/**
		 * Check all grouped products to see if they have this product as a child product
		 *
		 * @param WC_Product $product
		 *
		 * @return array
		 */
		public static function get_grouped_parent_id( WC_Product $product ) {
			$parent_products  = array();
			$args             = array(
				'type'  => 'grouped',
				'limit' => - 1,
			);
			$grouped_products = wc_get_products( $args );
			foreach ( $grouped_products as $grouped_product ) {
				foreach ( $grouped_product->get_children() as $child_id ) {
					if ( $child_id == $product->get_id() ) {
						$parent_products[] = $grouped_product->get_id();
					}
				}
			}

			return $parent_products;
		}

		/**
		 * Template locator function
		 *
		 * @param        $template_name
		 * @param string $template_path
		 * @param string $default_path
		 *
		 * @return mixed|void
		 */
		public static function wcwl_locate_template( $template_name, $template_path = '', $default_path = '' ) {
			if ( ! $template_path ) {
				$template_path = 'woocommerce-waitlist/';
			}
			if ( ! $default_path ) {
				$default_path = dirname( plugin_dir_path( __FILE__ ) ) . '/templates/'; // Path to the template folder
			}
			$template = locate_template( array(
				$template_path . $template_name,
				$template_name,
			) );
			if ( ! $template ) {
				$template = $default_path . $template_name;
			}

			return apply_filters( 'wcwl_locate_template', $template, $template_name, $template_path, $default_path );
		}

		/**
		 * Template getter method
		 *
		 * @param        $template_name
		 * @param array  $args
		 * @param string $template_path
		 * @param string $default_path
		 */
		public static function wcwl_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
			if ( is_array( $args ) && isset( $args ) ) {
				extract( $args );
			}
			$template_file = Pie_WCWL_Compatibility::wcwl_locate_template( $template_name, $template_path, $default_path );
			if ( ! file_exists( $template_file ) ) {
				_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'woocommerce-waitlist' ), '<code>' . $template_file . '</code>' ), '1.6.2' );

				return;
			}
			include $template_file;
		}

		/**
		 * Return the current version of WooCommerce
		 *
		 * @since 1.5.0
		 *
		 * @return string woocommerce version number/null
		 */
		protected static function get_wc_version() {
			global $woocommerce;

			return isset( $woocommerce->version ) ? $woocommerce->version : null;
		}

		/**
		 * Returns true if the current version of WooCommerce is at least 3.0
		 *
		 * @since 1.5.0
		 *
		 * @return boolean
		 */
		public static function wc_is_at_least_3_0() {
			return self::get_wc_version() && version_compare( self::get_wc_version(), '3.0', '>=' );
		}
	}
}