<?php

namespace WPStaging\Backend\Pro\Modules\Jobs;

// No Direct Access
if( !defined( "WPINC" ) ) {
   die;
}

use WPStaging\Utils\Directories;
use WPStaging\WPStaging;

/**
 * Class Scan
 * @package WPStaging\Backend\Modules\Jobs
 */
class Scan extends \WPStaging\Backend\Modules\Jobs\Job {

   /**
    * @var array
    */
   private $directories = array();

   /**
    * @var Directories
    */
   private $objDirectories;

   /**
    * @var database 
    */
   private $db;

   /**
    * Job options
    * @var type 
    */
   //protected $options;

   /**
    * Upon class initialization
    */
   protected function initialize() {

      $this->db = WPStaging::getInstance()->get( "wpdb" );

      $this->start();

      $this->objDirectories = new Directories;

      // Database Tables
      $this->getStagingTables();

      // Get directories
      $this->directories();
   }

   /**
    * Start Module
    * @return $this
    */
   public function start() {

      // Basic Options
      $this->options->root = str_replace( array("\\", '/'), DIRECTORY_SEPARATOR, ABSPATH );
      $this->options->existingClones = array_change_key_case( get_option( "wpstg_existing_clones_beta", array() ), CASE_LOWER );
      $this->options->current = null;


      if( isset( $_POST["clone"] ) && array_key_exists( strtolower( $_POST["clone"] ), $this->options->existingClones ) ) {
         $this->options->current = $_POST["clone"];
         $this->options->clone = strtolower( $_POST["clone"] );
         $this->options->cloneDirectoryName = preg_replace( "#\W+#", '-', strtolower( $this->options->clone ) );
         $this->options->cloneNumber = $this->options->existingClones[$this->options->clone]['number'];
         $this->options->directoryName = $this->options->existingClones[$this->options->clone]['directoryName'];
         $this->options->prefix = $this->options->existingClones[$this->options->clone]['prefix'];
      } else {
         wp_die( 'Fatal error - The clone does not exist in database. This should not happen.' );
      }

      // Tables
      //$this->options->tables                  = array();
      $this->options->excludedTables = array();
      $this->options->clonedTables = array();

      // Excluded Tables POST
      if( isset( $_POST["excludedTables"] ) && is_array( $_POST["excludedTables"] ) ) {
         $this->options->excludedTables = $_POST["excludedTables"];
      }


      // Files
      $this->options->totalFiles = 0;
      $this->options->copiedFiles = 0;
      $this->options->totalFileSize = 0;


      // Directories
      $this->options->includedDirectories = array();
      $this->options->excludedDirectories = array();

//        // Excluded Directories POST
//        if (isset($_POST["excludedDirectories"]) && is_array($_POST["excludedDirectories"]))
//        {
//            $this->options->excludedDirectories = $_POST["excludedDirectories"];
//        } 


      $this->options->extraDirectories = array();
      $this->options->directoriesToCopy = array();
      $this->options->scannedDirectories = array();

      // Excluded Directories TOTAL
      // Do not copy these folders
      $excludedDirectories = array(
          ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'wp-staging',
          ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cache',
          ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'wp-staging-pro',
          ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'wp-staging'
      );
      $this->options->excludedDirectories = array_merge( $excludedDirectories, $this->options->excludedDirectories );

      // Job
      //$this->options->currentJob              = "jobCopyDatabase";
      $this->options->currentJob = "";
      //$this->options->currentJob              = "jobCopyDatabaseTmp";
      $this->options->currentStep = 0;
      $this->options->totalSteps = 0;


      // Delete old job files initially
      $this->cache->delete( 'clone_options' );
      $this->cache->delete( 'files_to_copy' );

      // Save options
      $this->saveOptions();

      return $this;
   }

   /**
    * @param null|string $directories
    * @param bool $forceDisabled
    * @return string
    */
   public function directoryListing( $directories = null, $forceDisabled = false ) {
      if( null == $directories ) {
         $directories = $this->directories;
      }

      $output = '';
      foreach ( $directories as $name => $directory ) {

         // Not a directory, possibly a symlink, therefore we will skip it
         if( !is_array( $directory ) ) {
            continue;
         }

         // Need to preserve keys so no array_shift()
         $data = reset( $directory );
         unset( $directory[key( $directory )] );

         $isChecked = (
                 empty( $this->options->includedDirectories ) ||
                 in_array( $data["path"], $this->options->includedDirectories )
                 );

         // Check if folder is unchecked
         if( $this->options->existingClones && isset( $this->options->existingClones[$name] ) ||
                 in_array( $data["path"], $this->options->excludedDirectories )
         ) {
            $isDisabled = true;
         } else {
            $isDisabled = false;
         }
         //$isDisabled = ($this->options->existingClones && isset($this->options->existingClones[$name]));

         $output .= "<div class='wpstg-dir'>";
         $output .= "<input type='checkbox' class='wpstg-check-dir'";

         if( $isChecked && !$isDisabled && !$forceDisabled )
            $output .= " checked";
         if( $forceDisabled || $isDisabled )
            $output .= " disabled";

         $output .= " name='selectedDirectories[]' value='{$data["path"]}'>";

         $output .= "<a href='#' class='wpstg-expand-dirs";
         if( !$isChecked || $isDisabled )
            $output .= " disabled";
         $output .= "'>{$name}";
         $output .= "</a>";

         if( isset( $data["size"] ) ) {
            $output .= "<span class='wpstg-size-info'>{$this->formatSize( $data["size"] )}</span>";
         }

         if( !empty( $directory ) ) {
            $output .= "<div class='wpstg-dir wpstg-subdir wpstg-push'>";
            $output .= $this->directoryListing( $directory, $isDisabled );
            $output .= "</div>";
         }

         $output .= "</div>";
      }

      return $output;
   }

   /**
    * Checks if there is enough free disk space to create staging site
    * Returns null when can't run disk_free_space function one way or another
    * @return bool|null
    */
   public function hasFreeDiskSpace() {
      if( !function_exists( "disk_free_space" ) ) {
         return null;
      }

      $freeSpace = @disk_free_space( ABSPATH );

      if( false === $freeSpace ) {
         $data = array(
             'freespace' => false,
             'usedspace' => $this->formatSize( $this->getDirectorySizeInclSubdirs( ABSPATH ) )
         );
         echo json_encode( $data );
         die();
      }


      $data = array(
          'freespace' => $this->formatSize( $freeSpace ),
          'usedspace' => $this->formatSize( $this->getDirectorySizeInclSubdirs( ABSPATH ) )
      );

      echo json_encode( $data );
      die();
   }

   /**
    * Get directories and main meta data recursively
    */
   protected function directories() {
      $wpcontentDir = ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content';
      // check if dir exists
      if( !file_exists( $wpcontentDir ) ) {
         wp_die( __( 'Fatal error! Path ' . $wpcontentDir . ' does not exists.  <br> The staging site "' . $this->options->current . '" seems to be broken. <br>Create another staging site and try again.', 'wpstg' ) );
      }

      $directories = new \DirectoryIterator( $wpcontentDir );

      foreach ( $directories as $directory ) {
         //Not a valid directory. Continue iteration but do not loop through the current further and look for subdirectores
         if( false === ($path = $this->getPath( $directory )) ) {

            continue;
         }

         $this->handleDirectory( $path );

         // Get Sub-directories
         $this->getSubDirectories( $directory->getRealPath() );
      }

      // Gather Plugins
      //$this->getSubDirectories(ABSPATH . $this->options->current . DIRECTORY_SEPARATOR .'wp-content' . DIRECTORY_SEPARATOR . 'plugins');
      // Gather Themes
      //$this->getSubDirectories(ABSPATH . $this->options->current . DIRECTORY_SEPARATOR .'wp-content' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR);
      // Gather Uploads
      //$this->getSubDirectories(ABSPATH . $this->options->current . DIRECTORY_SEPARATOR .'wp-content' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
   }

   /**
    * Get relative Path from $directory and check if some dirs are excluded
    * Example: src/var/www/wordpress/root/staging/wp-content/ returns /staging/wp-content
    * 
    * @param \SplFileInfo $directory
    * @return string|false
    */
   protected function getPath( $directory ) {

      /*
       * Do not follow root path like src/web/..
       * This must be done before \SplFileInfo->isDir() is used!
       * Prevents open base dir restriction fatal errors
       */
      if( strpos( $directory->getRealPath(), ABSPATH ) !== 0 ) {
         return false;
      }

      $path = str_replace( ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR, null, $directory->getRealPath() );
      //$excludedPath = ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' .  DIRECTORY_SEPARATOR . 'uploads';
      // Using strpos() for symbolic links as they could create nasty stuff in nix stuff for directory structures
      if( !$directory->isDir() ||
              strlen( $path ) < 1 ||
              (strpos( $directory->getRealPath(), ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' ) !== 0 &&
              strpos( $directory->getRealPath(), ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'themes' ) !== 0 &&
              strpos( $directory->getRealPath(), ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' ) !== 0 )
      ) {
         return false;
      }

      return $path;
   }

   /**
    * @param string $path
    */
   protected function getSubDirectories( $path ) {
      if( !is_dir( $path ) ) {
         return;
      }
      $directories = new \DirectoryIterator( $path );

      //var_dump($directories);
      foreach ( $directories as $directory ) {
         // Not a valid directory
         if( false === ($path = $this->getPath( $directory )) ) {
            continue;
         }

         $this->handleDirectory( $path );
      }
   }

   /**
    * Organizes $this->directories
    * @param string $path
    */
   protected function handleDirectory( $path ) {

      $directoryArray = explode( DIRECTORY_SEPARATOR, $path );
      $total = count( $directoryArray );

      if( count( $total ) < 1 ) {
         return;
      }

      $total = $total - 1;
      $currentArray = &$this->directories;

      for ( $i = 0; $i <= $total; $i++ ) {
         if( !isset( $currentArray[$directoryArray[$i]] ) ) {
            $currentArray[$directoryArray[$i]] = array();
         }

         $currentArray = &$currentArray[$directoryArray[$i]];

         // Attach meta data to the end
         if( $i < $total ) {
            continue;
         }

         $fullPath = ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . $path;
         $size = $this->getDirectorySize( $fullPath );

         $currentArray["metaData"] = array(
             "size" => $size,
             "path" => ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . $path,
         );
      }
   }

   /**
    * Gets size of given directory
    * @param string $path
    * @return int|null
    */
   protected function getDirectorySize( $path ) {
      if( !isset( $this->settings->checkDirectorySize ) || '1' !== $this->settings->checkDirectorySize ) {
         return null;
      }

      return $this->objDirectories->size( $path );
   }

   /**
    * Get total size of a directory including all its subdirectories
    * @param string $dir
    * @return int
    */
   function getDirectorySizeInclSubdirs( $dir ) {
      $size = 0;
      foreach ( glob( rtrim( $dir, '/' ) . '/*', GLOB_NOSORT ) as $each ) {
         $size += is_file( $each ) ? filesize( $each ) : $this->getDirectorySizeInclSubdirs( $each );
      }
      return $size;
   }

   /**
    * Format bytes into human readable form
    * @param int $bytes
    * @param int $precision
    * @return string
    */
   public function formatSize( $bytes, $precision = 2 ) {
      if( ( double ) $bytes < 1 ) {
         return '';
      }

      $units = array('B', "KB", "MB", "GB", "TB");

      $bytes = ( double ) $bytes;
      $base = log( $bytes ) / log( 1000 ); // 1024 would be for MiB KiB etc
      $pow = pow( 1000, $base - floor( $base ) ); // Same rule for 1000

      return round( $pow, $precision ) . ' ' . $units[( int ) floor( $base )];
   }

   /**
    * Get Database Tables of the current staging site
    * deprecated
    */
   protected function getStagingTables() {
      $wpDB = WPStaging::getInstance()->get( "wpdb" );

      //$prefix = $this->getStagingPrefix();
      $prefix = $this->options->prefix;
      //$sql = "SHOW TABLE STATUS";

      if( strlen( $prefix ) > 0 ) {
         $sql = "SHOW TABLE STATUS LIKE '{$prefix}%'";
      } else {
         $sql = "SHOW TABLE STATUS";
      }

      //wp_die($sql);

      $tables = $wpDB->get_results( $sql );

      $currentTables = array();

      // Reset excluded Tables than loop through all tables
      $this->options->excludedTables = array();
      foreach ( $tables as $table ) {
         // Exclude WP Staging Tables
//            if (0 === strpos($table->Name, "wpstg"))
//            {
//                continue;
//            }
         // Create array of unchecked tables
         if( 0 !== strpos( $table->Name, $wpDB->prefix ) ) {
            $this->options->excludedTables[] = $table->Name;
         }

         $currentTables[] = array(
             "name" => $table->Name,
             "size" => ($table->Data_length + $table->Index_length)
         );
      }

      $this->options->tables = json_decode( json_encode( $currentTables ) );
   }

   /**
    * Check and return prefix of the staging site
    */
   public function getStagingPrefix() {
      if( !empty( $this->options->existingClones[$this->options->clone]['prefix'] ) ) {
         // Return result: Check first if staging prefix is the same as the live prefix
         if( $this->db->prefix != $this->options->existingClones[$this->options->clone]['prefix'] ) {
            return $this->options->existingClones[$this->options->clone]['prefix'];
         } else {
            $this->log( "Fatal Error: Can not push staging site. Prefix. '{$this->options->prefix}' is used for the live site. Creating a new staging site will likely resolve this the next time. Stopping for security reasons. Contact support@wp-staging.com" );
            wp_die( "Fatal Error: Can not push staging site. Prefix. '{$this->options->prefix}' is used for the live site. Creating a new staging site will likely resolve this the next time. Stopping for security reasons. Contact support@wp-staging.com" );
         }
      }

      // If prefix is not defined! Happens if staging site has ben generated with older version of wpstg
      // Try to get staging prefix from wp-config.php of staging site

      $path = ABSPATH . $this->options->directoryName . "/wp-config.php";
      if( false === ($content = @file_get_contents( $path )) ) {
         $this->log( "Can not open {$path}. Can't read contents", Logger::TYPE_ERROR );
         // Create a random prefix which hopefully never exists.
         $this->options->prefix = rand( 7, 15 ) . '_';
      } else {
         // Get prefix from wp-config.php
         preg_match( "/table_prefix\s*=\s*'(\w*)';/", $content, $matches );

         if( !empty( $matches[1] ) ) {
            $this->options->prefix = $matches[1];
         } else {
            $this->log( "Fatal Error: Can not push staging site. Can not find Prefix. '{$matches[1]}'. Stopping for security reasons. Creating a new staging site will likely resolve this the next time. Contact support@wp-staging.com" );
            // Create a random prefix which hopefully never exists.
            $this->options->prefix = rand( 7, 15 ) . '_';
         }
      }
      // return result: Check first if staging prefix is the same as the live prefix
      if( $this->db->prefix != $this->options->prefix ) {
         return $this->options->prefix;
      } else {
         $this->log( "Fatal Error: Can not push staging site. Prefix. '{$this->options->prefix}' is used for the live site. Creating a new staging site will likely resolve this the next time. Stopping for security reasons. Contact support@wp-staging.com" );
         wp_die( "Fatal Error: Can not push staging site. Prefix. '{$this->options->prefix}' is used for the live site. Creating a new staging site will likely resolve this the next time. Stopping for security reasons. Contact support@wp-staging.com" );
      }
   }

}
