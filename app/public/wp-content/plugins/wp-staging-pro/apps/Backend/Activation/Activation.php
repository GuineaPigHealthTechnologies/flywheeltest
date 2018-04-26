<?php

namespace WPStaging\Backend\Activation;

// No Direct Access
if( !defined( "WPINC" ) ) {
   die;
}

use WPStaging\WPStaging;
use WPStaging\Backend\Optimizer\Optimizer;
use WPStaging\Cron\Cron;

class Activation {

   /**
    * Checks if another version of WPSTG (Pro) is active and deactivates it.
    * To be hooked on `activated_plugin` so other plugin is deactivated when current plugin is activated.
    *
    * @param string $plugin
    *
    */
   public static function deactivate_other_instances( $plugin ) {
      if( !in_array( basename( $plugin ), array('wp-staging-pro.php', 'wp-staging.php') ) ) {
         return;
      }
      $plugin_to_deactivate = 'wp-staging.php';
      $deactivated_notice_id = '1';
      if( basename( $plugin ) == $plugin_to_deactivate ) {
         $plugin_to_deactivate = 'wp-staging-pro.php';
         $deactivated_notice_id = '2';
      }
      if( is_multisite() ) {
         $active_plugins = ( array ) get_site_option( 'active_sitewide_plugins', array() );
         $active_plugins = array_keys( $active_plugins );
      } else {
         $active_plugins = ( array ) get_option( 'active_plugins', array() );
      }
      foreach ( $active_plugins as $basename ) {
         if( false !== strpos( $basename, $plugin_to_deactivate ) ) {
            set_transient( 'wp_staging_deactivated_notice_id', $deactivated_notice_id, 1 * HOUR_IN_SECONDS );
            deactivate_plugins( $basename );
            return;
         }
      }
   }

   public static function install_dependancies() {
      // add wpstg_weekly_event to cron events
      $cron = new \WPStaging\Cron\Cron;
      // Register cron job.
      $cron->schedule_event();

      // Install Optimizer 
      $optimizer = new Optimizer();
      $optimizer->installOptimizer();
   }

}