<?php

/**
 * Cron relevant stuff
 */

namespace WPStaging\apps\Core\Activation;

// No Direct Access
if( !defined( "WPINC" ) ) {
   die;
}

class Activation {

   public function __construct() {
      //wp_die('dfghjk');
      //register_activation_hook( __FILE__, array($this, 'wpstg_activation') );
      register_activation_hook( WPSTGPRO_PLUGIN_DIR, array($this, 'activation') );
   }

   /**
    * Activation method
    */
   public function activation() {
      // Register cron job
      $cron = new \WPStaging\apps\Core\Cron\Cron();
      $cron->wpstg_activation();
      
      // Install Optimizer
      $optimizer = new \WPStaging\apps\Core\Optimizer\Optimizer;
      $optimizer->installOptimizer();
   }
  

}
// Run
$activation = new Activation();
