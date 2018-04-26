<?php
/**
 * Plugin Name: Boss for Sensei
 * Plugin URI:  https://www.buddyboss.com/landing/promos-social/social-learner.php
 * Description: Makes Sensei look beautiful with Boss. theme
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.1.2
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if (!defined( 'BOSS_SENSEI_PLUGIN_VERSION' ) ) {
  define( 'BOSS_SENSEI_PLUGIN_VERSION', '1.1.2' );
}

// Database version
if (!defined( 'BOSS_SENSEI_PLUGIN_DB_VERSION' ) ) {
  define( 'BOSS_SENSEI_PLUGIN_DB_VERSION', 1 );
}

// Directory
if (!defined( 'BOSS_SENSEI_PLUGIN_DIR' ) ) {
  define( 'BOSS_SENSEI_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if (!defined( 'BOSS_SENSEI_PLUGIN_URL' ) ) {
  $plugin_url = plugin_dir_url( __FILE__ );

  // If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
  if ( is_ssl() )
    $plugin_url = str_replace( 'http://', 'https://', $plugin_url );

  define( 'BOSS_SENSEI_PLUGIN_URL', $plugin_url );
}

// File
if (!defined( 'BOSS_SENSEI_PLUGIN_FILE' ) ) {
  define( 'BOSS_SENSEI_PLUGIN_FILE', __FILE__ );
}

/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Check whether
 * it meets all requirements
 * @return void
 */
function buddypress_creative_portfolio_pro_requirements()
{

    global $Plugin_Requirements_Check;

    $requirements_Check_include  = BOSS_SENSEI_PLUGIN_DIR  . 'includes/requirements-class.php';

    try
    {
        if ( file_exists( $requirements_Check_include ) )
        {
            require( $requirements_Check_include );
        }
        else{
            $msg = sprintf( __( "Couldn't load BPCP_Plugin_Check class at:<br/>%s", 'boss-sensei' ), $requirements_Check_include );
            throw new Exception( $msg, 404 );
        }
    }
    catch( Exception $e )
    {
        $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'boss-sensei' ), $e->getMessage() );
        echo $msg;
    }

    $Plugin_Requirements_Check = new Plugin_Requirements_Check();
    $Plugin_Requirements_Check->activation_check();

}
register_activation_hook( __FILE__, 'buddypress_creative_portfolio_pro_requirements' );


/**
 * Main
 *
 * @return void
 */

function boss_sensei_init()
{
  global $bp, $boss_sensei, $learner;

    //Check Sensei Plugin is install and active
    if (  ! function_exists( 'Sensei' ) || ! function_exists( 'bp_is_active' )) {
        add_action( 'admin_notices', 'boss_sensei_install_plugin_notice' );
        return;
    }

    $sensei_version = Sensei()->version;

    if ( version_compare( $sensei_version , '1.9', '<' ) ) {
        add_action( 'admin_notices', 'boss_sensei_upgrade_notice' );
        return;
    }

    $main_include  = BOSS_SENSEI_PLUGIN_DIR  . 'includes/main-class.php';

  try
  {
    if ( file_exists( $main_include ) )
    {
      require( $main_include );
    }
    else{
      $msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'boss-sensei' ), $main_include );
      throw new Exception( $msg, 404 );
    }
  }
  catch( Exception $e )
  {
    $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'boss-sensei' ), $e->getMessage() );
    echo $msg;
  }

  $boss_sensei = Boss_Sensei_Plugin::instance();
  $learner = $boss_sensei;

}
add_action( 'plugins_loaded', 'boss_sensei_init' );

/** Temprorary fix for lesson archive **/
function bp_sensei_lesson_template( $archive_template ) {

     if ( is_post_type_archive ( 'lesson' ) ) {
          $archive_template = BOSS_SENSEI_PLUGIN_DIR  . 'includes/archive-lesson.php';
     }
     return $archive_template;
}

add_filter( 'archive_template', 'bp_sensei_lesson_template' ) ;

/** Temprorary fix for learner profile **/
function bp_sensei_learner_profile( $template ) {
    global $wp_query;

     if ( isset( $wp_query->query_vars['learner_profile'] ) ) {
          $template = BOSS_SENSEI_PLUGIN_DIR  . 'includes/learner-profile.php';
     }

     return $template;
}

add_filter( 'template_include', 'bp_sensei_learner_profile',11 );

/**
 * Must be called after hook 'plugins_loaded'
 * @return Boss for Sensei Plugin main controller object
 */
function boss_sensei()
{
  global $boss_sensei;

  return $boss_sensei;
}

/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once('includes/buddyboss-plugin-updater.php');
//new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 91);

/**
 * Show the admin notice to install/activate Sensei first
 */
function boss_sensei_install_plugin_notice() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>Boss for Sensei</strong> requires the <a href="https://www.woothemes.com/products/sensei/">Sensei</a> and <a href="https://wordpress.org/plugins/buddypress/">BuddyPress</a> plugins to work  first.', 'boss-sensei');
    echo '</p></div>';
}

/**
 * Show the admin notice to update Sensei first
 */
function boss_sensei_upgrade_notice() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>Boss for Sensei</strong> requires Sensei version 1.9 or above to work. Please <a href="https://www.woothemes.com/products/sensei/">update Sensei</a> first.', 'boss-sensei');
    echo '</p></div>';
}
