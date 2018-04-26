<?php

namespace WPStaging\Backend\Pro\Modules\Jobs;

// No Direct Access
if( !defined( "WPINC" ) ) {
    die;
}

use WPStaging\WPStaging;
use WPStaging\Utils\Logger;
use WPStaging\Iterators\RecursiveDirectoryIterator;
use WPStaging\Iterators\RecursiveFilterNewLine;
use WPStaging\Iterators\RecursiveFilterExclude;

/**
 * Class ScanDirectories
 * Scan the file system for all files and folders to copy
 * @package WPStaging\Backend\Modules\Directories
 */
class ScanDirectories extends \WPStaging\Backend\Modules\Jobs\JobExecutable {

    /**
     * @var array
     */
    private $files = array();

    /**
     * Total steps to do
     * @var int
     */
    private $total = 4;
    private $fileHandle;
    
    private $filename;

    /**
     * Initialize
     */
    public function initialize() {
        $this->filename = $this->cache->getCacheDir() . "files_to_copy." . $this->cache->getCacheExtension();
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps() {
        $this->options->totalSteps = $this->total;
    }

    /**
     * Start Module
     * @return object
     */
    public function start() {

        // Execute steps
        $this->run();

        // Save option, progress
        $this->saveProgress();

        return (object) $this->response;
    }
    
    /**
     * Get Root Files
     * Plugin and Theme folder
     */
    protected function getRootFiles() {
        if( isset ($this->options->totalFiles) &&  $this->options->totalFiles > 1 || !isset($this->options->clone) ) {
            return;
        }
        $this->getFilesFromDirectory( ABSPATH . $this->options->clone . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR );
    }

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute() {
        // No job left to execute
        if( $this->isFinished() ) {
            $this->prepareResponse( true, false );
            return false;
        }
        // Get current directory
        $directory = $this->options->directoriesToCopy[$this->options->currentStep];

        // Get files recursively
        if( !$this->getFilesFromSubDirectories( $directory ) ) {
            $this->prepareResponse( false, false );
            return false;
        }

        // Add directory to scanned directories listing
        $this->options->scannedDirectories[] = $directory;

        // Prepare response
        $this->prepareResponse();

        // Not finished
        return true;
    }
   

    /**
     * Checks Whether There is Any Job to Execute or Not
     * @return bool
     */
    private function isFinished() {       
        return (
                empty( $this->options->directoriesToCopy ) ||
                $this->options->currentStep > $this->total ||
                !isset( $this->options->directoriesToCopy[$this->options->currentStep] )
                );
    }

    /**
     * @param $path
     * @return bool
     */
    protected function getFilesFromSubDirectories( $path ) {
        $this->totalRecursion++;

        if( $this->isOverThreshold() ) {
            return false;
        }

        $this->log( "Scanning {$path} for its sub-directories and files" );

        $directories = new \DirectoryIterator( $path );

        foreach ( $directories as $directory ) {

            
            // Not a valid directory
            if( false === ($path = $this->getPath( $directory )) ) {
                //$this->debugLog('Not a valid dir ' . $directory);
                continue;
            }

            // Skip Excluded directory
            if( $this->isDirectoryExcluded( $directory->getRealPath() ) ) {
                //$this->debugLog('Skip excluded dir ' . $directory->getRealPath());
                continue;
            }

            // This directory is already scanned
            if( in_array( $path, $this->options->scannedDirectories ) ) {
                continue;
            }

            // Save all files
            $dir = ABSPATH . $path . DIRECTORY_SEPARATOR;
            $this->getFilesFromDirectory( $dir );

            // Add scanned directory listing
            $this->options->scannedDirectories[] = $dir;
        }

        $this->saveOptions();

        // Not finished
        return true;
    }

    /**
     * @param $directory
     * @return bool
     */
    protected function getFilesFromDirectory( $directory ) {
        $this->totalRecursion++;
        
        if (!is_readable($directory)){
            $this->returnException('Fatal Error: Directory or file ' . $directory . ' is not readable or does not exist.');
        }

        // Save all files
        $files = array_diff( scandir( $directory ), array('.', "..") );

        foreach ( $files as $file ) {
            $fullPath = $directory . $file;

            if( is_dir( $fullPath ) && !in_array( $fullPath, $this->options->directoriesToCopy ) && !$this->isDirectoryExcluded( $fullPath ) ) {
                $this->options->directoriesToCopy[] = $fullPath;
                return $this->getFilesFromSubDirectories( $fullPath );
                //continue;
            }

            if( !is_file( $fullPath ) || in_array( $fullPath, $this->files ) ) {
                continue;
            }

            $this->options->totalFiles++;

            $this->files[] = $fullPath;
           
        }
    }

    /**
     * Get Path from $directory
     * @param \SplFileInfo $directory
     * @return string|false
     */
    protected function getPath( $directory ) {
        $path = str_replace( ABSPATH, null, $directory->getRealPath() );

        // Using strpos() for symbolic links as they could create nasty stuff in nix stuff for directory structures
        // Only scan plugins and theme folder
        if (!$directory->isDir() || strlen($path) < 1 ||
                (strpos($directory->getRealPath(), ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins') !== 0 &&
                strpos($directory->getRealPath(), ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'themes') !== 0 &&
                strpos($directory->getRealPath(), ABSPATH . $this->options->current . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads') !== 0
                )) {
            return false;
        }

        return $path;
    }

    /**
     * Check if directory is excluded from copying
     * @param string $directory
     * @return bool
     */
    protected function isDirectoryExcluded( $directory ) {
        foreach ( $this->options->excludedDirectories as $excludedDirectory ) {

            if( strpos( $directory, $excludedDirectory ) === 0 && !$this->isExtraDirectory( $directory ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if directory is an extra directory and should be copied
     * @param string $directory
     * @return boolean
     */
    protected function isExtraDirectory( $directory ) {
        foreach ( $this->options->extraDirectories as $extraDirectory ) {
            if( strpos( $directory, $extraDirectory ) === 0 ) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Save files
     * @return bool
     */
    protected function saveProgress() {
        $this->saveOptions();

        $fileName = $this->cache->getCacheDir() . "files_to_copy." . $this->cache->getCacheExtension();
        $files = implode( PHP_EOL, $this->files );

        return (false !== @file_put_contents( $fileName, $files ));
    }

    /**
     * Get files
     * @return void
     */
    protected function getFiles() {
        $fileName = $this->cache->getCacheDir() . "files_to_copy." . $this->cache->getCacheExtension();

        if( false === ($this->files = @file_get_contents( $fileName )) ) {
            $this->files = array();
            return;
        }

        $this->files = explode( PHP_EOL, $this->files );
    }

}
