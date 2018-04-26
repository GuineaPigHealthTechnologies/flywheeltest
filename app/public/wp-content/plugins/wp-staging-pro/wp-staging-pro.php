<?php
/**
 * Plugin Name: WP Staging Pro
 * Plugin URI: https://wp-staging.com
 * Description: Create a staging clone site for testing & developing
 * Author: WP-Staging, René Hermenau
 * Author URI: https://wordpress.org/plugins/wp-staging
 * Version: 2.3.8 
 * Text Domain: wpstg
 * Domain Path: /languages/
 *
 * @package WPSTG
 * @category Core
 * @author René Hermenau, Ilgıt Yıldırım
 */

// No Direct Access
if (!defined("WPINC"))
{
    die;
}


// Plugin Folder Path
if( !defined( 'WPSTGPRO_VERSION' ) ) {
   define( 'WPSTGPRO_VERSION', '2.3.8' );
}

// Plugin Folder Path
if( !defined( 'WPSTGPRO_PLUGIN_DIR' ) ) {
   define( 'WPSTGPRO_PLUGIN_DIR', plugin_dir_path(  __FILE__ ) );
}
// Plugin Folder Path
if( !defined( 'WPSTGPRO_PLUGIN_FILE' ) ) {
   define( 'WPSTGPRO_PLUGIN_FILE', __FILE__ );
}

/**
 * Fix nonce check
 * Bug: https://core.trac.wordpress.org/ticket/41617#ticket
 * @param int $seconds
 * @return int
 */
function wpstgpro_overwrite_nonce($seconds){
	 return 86400;
 }
 add_filter('nonce_life', 'wpstgpro_overwrite_nonce', 99999);

/**
 * Path to main WP Staging class
 * Make sure to not redeclare class in case free version has been installed previosly
 */
if (!class_exists( 'WPStaging\WPStaging' )){
   require_once plugin_dir_path(__FILE__) . "apps/Core/WPStaging.php";
}

$wpStaging = \WPStaging\WPStaging::getInstance();


/**
 * Load a few important WP globals into WPStaging class to make them available via dependancy injection
 */

// Wordpress DB Object
/**
 * @var wpdb thats the loaded var
 */
if (isset($wpdb))
{
    $wpStaging->set("wpdb", $wpdb);
}

// WordPress Filter Object
if (isset($wp_filter))
{ 
    $wpStaging->set("wp_filter", function() use(&$wp_filter) {
        return $wp_filter;
    });
}

/**
 * Inititalize WPStaging
 */
$wpStaging->run();


//add_action('plugins_loaded', array($wpStaging, 'run'));