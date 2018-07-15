<?php
/**
 * Plugin Name: WooCommerce Product Reviews Pro
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-product-reviews-pro/
 * Description: Extend WooCommerce product reviews to add video, photo, comment, and question contribution types, as well as review filtering, voting, and flagging.
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 1.11.0
 * Text Domain: woocommerce-product-reviews-pro
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2015-2018, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Product-Reviews-Pro
 * @author    SkyVerge
 * @category  Reviews
 * @copyright Copyright (c) 2015-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 570800:43662c2508f9242c6ba1da8c535510a0
 * WC requires at least: 2.6.14
 * WC tested up to: 3.3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '43662c2508f9242c6ba1da8c535510a0', '570800' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.9.0', __( 'WooCommerce Product Reviews Pro', 'woocommerce-product-reviews-pro' ), __FILE__, 'init_woocommerce_product_reviews_pro', array(
	'minimum_wc_version'   => '2.6.14',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_product_reviews_pro() {


/**
 * WooCommerce Product Reviews Pro Main Plugin Class.
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.11.0';

	/** @var WC_Product_Reviews_Pro single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'product_reviews_pro';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_product_reviews_pro_';

	/** @var \WC_Product_Reviews_Pro_Admin instance */
	protected $admin;

	/** @var \WC_Product_Reviews_Pro_Frontend instance */
	protected $frontend;

	/** @var \WC_Product_Reviews_Pro_AJAX instance */
	protected $ajax;

	/** @var \WC_Product_Reviews_Pro_Review_Qualifiers instance */
	protected $review_qualifiers;

	/** @var \WC_Product_Reviews_Pro_Contribution_Factory instance */
	protected $contribution_factory;

	/** @var \WC_Product_Reviews_Pro_Query instance */
	protected $query;

	/** @var \WC_Product_Reviews_Pro_Emails instance */
	private $emails;

	/** @var \WC_Product_Reviews_Pro_Widgets instance */
	private $widgets;

	/** @var \WC_Product_Reviews_Pro_Integrations instance */
	private $integrations;


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'        => 'woocommerce-product-reviews-pro',
				'display_php_notice' => true,
			)
		);

		// delay standard install so we can use get_comments()
		if ( is_admin() && ! is_ajax() ) {
			remove_action( 'wp_loaded', array( $this, 'do_install' ) );
			add_action( 'admin_init', array( $this, 'do_install' ) );
		}

		// include required files on plugins loaded
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'includes' ) );

		// make sure template files are searched for in our plugin
		add_filter( 'woocommerce_locate_template',      array( $this, 'locate_template' ), 20, 3 );
		add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_template' ), 20, 3 );
	}


	/**
	 * Loads additional files upon plugins loaded.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// utility, template, helper functions
		require_once( $this->get_plugin_path() . '/includes/functions/wc-product-reviews-pro-functions.php' );

		// query handler
		$this->query = $this->load_class( '/includes/class-wc-product-reviews-pro-query.php', 'WC_Product_Reviews_Pro_Query' );

		// emails handler
		$this->emails = $this->load_class( '/includes/class-wc-product-reviews-pro-emails.php', 'WC_Product_Reviews_Pro_Emails' );

		// main objects handlers
		$this->review_qualifiers    = $this->load_class( '/includes/class-wc-product-reviews-pro-review-qualifiers.php', 'WC_Product_Reviews_Pro_Review_Qualifiers' );
		$this->contribution_factory = $this->load_class( '/includes/class-wc-product-reviews-pro-contribution-factory.php', 'WC_Product_Reviews_Pro_Contribution_Factory' );

		// frontend handler
		if ( ! is_admin() || is_ajax() ) {
			$this->frontend = $this->load_class( '/includes/frontend/class-wc-product-reviews-pro-frontend.php', 'WC_Product_Reviews_Pro_Frontend' );
		}

		// admin includes
		if ( is_admin() && ! is_ajax() ) {
			$this->admin = $this->load_class( '/includes/admin/class-wc-product-reviews-pro-admin.php', 'WC_Product_Reviews_Pro_Admin' );
		}

		// ajax handler
		$this->ajax = $this->load_class( '/includes/class-wc-product-reviews-pro-ajax.php', 'WC_Product_Reviews_Pro_AJAX' );

		// widgets handler
		$this->widgets = $this->load_class( '/includes/class-wc-product-reviews-pro-widgets.php', 'WC_Product_Reviews_Pro_Widgets' );

		// integrations handler
		$this->integrations = $this->load_class( '/includes/integrations/class-wc-product-reviews-pro-integrations.php', 'WC_Product_Reviews_Pro_Integrations' );
	}


	/**
	 * Returns the Admin handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the Frontend handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the Ajax handler instance.
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_AJAX
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the Review Qualifiers instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Review_Qualifiers
	 */
	public function get_review_qualifiers_instance() {

		return $this->review_qualifiers;
	}


	/**
	 * Returns the Contribution Factory instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Contribution_Factory
	 */
	public function get_contribution_factory_instance() {

		return $this->contribution_factory;
	}


	/**
	 * Returns the Query handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Query
	 */
	public function get_query_instance() {

		return $this->query;
	}


	/**
	 * Returns the Emails handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Product_Reviews_Pro_Emails
	 */
	public function get_emails_instance() {

		return $this->emails;
	}


	/**
	 * Returns the Widgets handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Product_Reviews_Pro_Widgets
	 */
	public function get_widgets_instance() {

		return $this->widgets;
	}


	/**
	 * Returns the Integrations handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Product_Reviews_Pro_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Locates the WooCommerce template files from Product Reviews Pro templates directory.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $template already found template
	 * @param string $template_name searchable template name
	 * @param string $template_path template path
	 * @return string search result for the template
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		// only keep looking if no custom theme template was found,
		// or if a default WooCommerce template was found
		if ( ! $template || SV_WC_Helper::str_starts_with( $template, WC()->plugin_path() ) ) {

			// set the path to our templates directory
			$plugin_path = $this->get_plugin_path() . '/templates/';

			// if a template is found, make it so
			if ( is_readable( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}
		}

		return $template;
	}


	/** Admin methods ******************************************************/


	/**
	 * Render a notice for the user to read the docs before adding add-ons
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$this->get_admin_notice_handler()->add_admin_notice(
			/* translators: Placeholders: %1$s opening <a> html tag - %2$s closing </a> html tag - %3$s opening <a> html tag - %4$s closing </a> html tag - %5$s opening <a> html tag - %6$s closing </a> html tag */
			sprintf(
				__( 'Thanks for installing Product Reviews Pro! Before getting started, please take a moment to %1$sread the documentation%2$s, configure %3$ssettings%4$s or %5$semails%6$s :) ', 'woocommerce-product-reviews-pro' ),
				'<a href="http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/" target="_blank">',
				'</a>',
				'<a href="' . admin_url( "admin.php?page=wc-settings&tab=products" ) . '">',
				'</a>',
				'<a href="' . admin_url( "admin.php?page=wc-settings&tab=email&section=wc_product_reviews_pro_emails_new_comment" ) . '">',
				'</a>'
			),
			'read-the-docs-notice',
			array( 'always_show_on_settings' => false, 'notice_class' => 'updated' )
		);
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Product Reviews Pro Instance, ensures only one instance is/can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @see \wc_product_reviews_pro()
	 *
	 * @return \WC_Product_Reviews_Pro
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @see \SV_WC_Plugin::get_plugin_name()
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Product Reviews Pro', 'woocommerce-product-reviews-pro' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.0.0
	 *
	 * @see \SV_WC_Plugin::get_file()
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the URL to the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @see \SV_WC_Plugin::is_plugin_settings()
	 *
	 * @param string|null $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Returns true if we are on the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @see \SV_WC_Plugin::is_plugin_settings()
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'] ) && 'reviews' === $_GET['page'];
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @since 1.1.0
	 *
	 * @see \SV_WC_Plugin::get_documentation_url()
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-product-reviews-pro/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.1.0
	 *
	 * @see \SV_WC_Plugin::get_support_url()
	 *
	 * @return string support URL
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Flushes rewrite rules upon activation.
	 *
	 * @since 1.6.0
	 *
	 * @see \SV_WC_Plugin::activate()
	 */
	public function activate() {

		flush_rewrite_rules();
	}


	/**
	 * Flushes rewrite rules upon deactivation.
	 *
	 * @since 1.6.0
	 *
	 * @see \SV_WC_Plugin::deactivate()
	 */
	public function deactivate() {

		flush_rewrite_rules();
	}


	/**
	 * Flushes rewrite rules upon upgrade.
	 *
	 * @since 1.6.0
	 *
	 * @see \SV_WC_Plugin::do_install()
	 * @see \SV_WC_Plugin::upgrade()
	 *
	 * @param string $installed_version
	 */
	protected function upgrade( $installed_version ) {

		if ( version_compare( $installed_version, '1.11.0', '<' ) ) {
			update_option( 'wc_product_reviews_pro_contribution_threshold',     get_option( 'wc_product_reviews_pro_contribution_threshold',    1 ) );
			update_option( 'wc_product_reviews_pro_contribution_badge',         get_option( 'wc_product_reviews_pro_contribution_badge',        __( 'Admin', 'woocommerce-product-reviews-pro' ) ) );
			update_option( 'wc_product_reviews_pro_contribution_badge_vendor',  get_option( 'wc_product_reviews_pro_contribution_badge_vendor', __( 'Vendor', 'woocommerce-product-reviews-pro' ) ) );
		}

		flush_rewrite_rules();
	}


	/**
	 * Handles installation routine.
	 *
	 * @since 1.0.0
	 *
	 * @see \SV_WC_Plugin::do_install()
	 * @see \SV_WC_Plugin::install()
	 */
	protected function install() {
		global $wpdb;

		// Default settings
		update_option( 'wc_product_reviews_pro_enabled_contribution_types', 'all' );
		update_option( 'wc_product_reviews_pro_contributions_orderby',      'most_helpful' );
		update_option( 'wc_product_reviews_pro_contribution_moderation',    get_option( 'comment_moderation' ) ? 'yes' : 'no' );
		update_option( 'wc_product_reviews_pro_contribution_threshold',     get_option( 'wc_product_reviews_pro_contribution_threshold', 5 ) );
		update_option( 'wc_product_reviews_pro_contribution_badge',         get_option( 'wc_product_reviews_pro_contribution_badge', __( 'Admin', 'woocommerce-product-reviews-pro' ) ) );
		update_option( 'wc_product_reviews_pro_contribution_badge_vendor',  get_option( 'wc_product_reviews_pro_contribution_badge_vendor', __( 'Vendor', 'woocommerce-product-reviews-pro' ) ) );

		// Set comment_type to 'review' on all comments that have a product as
		// their parent and no type set.  Page through comments in blocks to
		// avoid out of memory errors
		$offset           = (int) get_option( 'wc_product_reviews_pro_install_offset', 0 );
		$records_per_page = 500;

		do {

			$record_ids = get_comments( array(
				'post_type' => 'product',
				'type'      => '',
				'fields'    => 'ids',
				'offset'    => $offset,
				'number'    => $records_per_page,
			) );

			// some sort of bad database error: deactivate the plugin and display an error
			if ( is_wp_error( $record_ids ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( 'woocommerce-product-reviews-pro/woocommerce-product-reviews-pro.php' );

				wp_die(
					sprintf( /* translators: Placeholders: %1$s - plugin name, %2$s - error message(s) */
						__( 'Error activating and installing %1$s: %2$s', 'woocommerce-product-reviews-pro' ),
						$this->get_plugin_name(),
						'<ul><li>' . implode( '</li><li>', $record_ids->get_error_messages() ) . '</li></ul>' ) .
					'<a href="' . admin_url( 'plugins.php' ) . '">' . esc_html__( '&laquo; Go Back', 'woocommerce-product-reviews-pro' ) . '</a>'
				);
			}

			if ( is_array( $record_ids ) ) {
				foreach ( $record_ids as $id ) {
					$wpdb->query( "UPDATE {$wpdb->comments} SET comment_type = 'review' WHERE comment_type = '' AND comment_ID = {$id}" );
				}
			}

			// increment offset
			$offset += $records_per_page;
			// and keep track of how far we made it in case we hit a script timeout
			update_option( 'wc_product_reviews_pro_install_offset', $offset );

		// while full set of results returned (meaning there may be more results still to retrieve)
		} while( count( $record_ids ) === $records_per_page );

		flush_rewrite_rules();
	}


	/** Deprecated methods ******************************************************/


	/**
	 * Handles deprecated methods for backward compatibility.
	 *
	 * TODO progressively remove deprecated methods once they are at least 3 version older than the current minor x.Y.z version {FN 2018-01-25}
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $method method name being called
	 * @param array $args optional args passed to called $method
	 * @return null|mixed
	 */
	public function __call( $method, $args ) {

		$deprecated = __CLASS__ . '::' . $method . '()';

		switch ( $method ) {

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'filter_enable_review_rating' :
				_deprecated_function( $deprecated, '1.10.0', 'wc_product_reviews_pro()->get_contribution_factory_instance()->is_review_rating_enabled()' );
				return $this->get_contribution_factory_instance()->is_review_rating_enabled( isset( $args[0] ) ? $args[0] : $args );

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'get_contribution_types' :
			case 'get_enabled_contribution_types' :
				_deprecated_function( $deprecated, '1.10.0', "wc_product_reviews_pro()->get_contribution_factory_instance()->{$method}()" );
				return $this->get_contribution_factory_instance()->$method();

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'get_reviews_tab_title' :
				_deprecated_function( $deprecated, '1.10.0' );
				return '';

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'register_widgets' :
				_deprecated_function( $deprecated, '1.10.0', 'wc_product_reviews_pro()->get_widgets_instance()->register_widgets()' );
				$this->get_widgets_instance()->register_widgets();
				return null;

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'points_rewards_review_get_comments_args' :
				_deprecated_function( $deprecated, '1.10.0', 'wc_product_reviews_pro()->get_integrations_instance()->get_points_and_rewards_instance()->review_get_comments_args()' );
				$points_and_rewards = $this->get_integrations_instance()->get_points_and_rewards_instance();
				return $points_and_rewards ? $points_and_rewards->review_get_comments_args( isset( $args[0] ) ? $args[0] : $args ) : array();

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'points_rewards_review_add_product_review_points' :
				_deprecated_function( $deprecated, '1.10.0', 'wc_product_reviews_pro()->get_integrations_instance()->get_points_and_rewards_instance()->review_add_product_review_points()' );
				$points_and_rewards = $this->get_integrations_instance()->get_points_and_rewards_instance();
				return $points_and_rewards && isset( $args[0], $args[1] ) ? $points_and_rewards->review_add_product_review_points( $args[0], $args[1] ) : false;

			/** @deprecated since 1.10.0 - TODO remove this by version 1.13.0 {FN 2018-01-25} */
			case 'tab_manager_set_reviews_tab_title_review_count' :
				_deprecated_function( $deprecated, '1.10.0', 'wc_product_reviews_pro()->get_integrations_instance()->get_tab_manager_instance()->set_reviews_tab_title_review_count()' );
				$tab_manager = $this->get_integrations_instance()->get_tab_manager_instance();
				return $tab_manager && isset( $args[0], $args[1] ) ? $tab_manager->set_reviews_tab_title_review_count( $args[0], $args[1] ) : 0;

			default :
				// you're probably doing it wrong
				trigger_error( "Call to undefined method $deprecated", E_USER_ERROR );
				return null;
		}
	}


}

/**
 * Returns the One True Instance of Product Reviews Pro
 *
 * @since 1.0.0
 * @return \WC_Product_Reviews_Pro
 */
function wc_product_reviews_pro() {
	return WC_Product_Reviews_Pro::instance();
}

// fire it up!
wc_product_reviews_pro();

} // init_woocommerce_product_reviews_pro()
