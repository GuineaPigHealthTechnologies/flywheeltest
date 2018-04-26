<?php
namespace WPStaging\Backend\Pro\Modules\Jobs;

// No Direct Access
if (!defined("WPINC"))
{
    die;
}

use WPStaging\WPStaging;

/**
 * Class Database
 * @package WPStaging\Backend\Modules\Jobs
 */
class DatabaseTmp extends \WPStaging\Backend\Modules\Jobs\JobExecutable
{

    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var \WPDB
     */
    private $db;
    
    /**
     * The prefix of the new database tables which are used for the live site after updating tables
     * @var string 
     */
    
    public $tmpPrefix;
    
    /**
     * Tables to operate on
     * @var array 
     */
    //private $includedTables;

    /**
     * Initialize
     */
    public function initialize()
    {
        // Variables
        $this->total                = count($this->options->tables);
        $this->db                   = WPStaging::getInstance()->get("wpdb");
//        $this->options->prefix      = isset($this->options->existingClones[strtolower($this->options->clone)]['prefix']) ? 
//                                        $this->options->existingClones[strtolower($this->options->clone)]['prefix'] : 
//                                        'unknownprefix';  
        $this->tmpPrefix            = 'wpstgtmp_';
        
        //$this->getTables();

    }
    
    
    /**
     * Get Database Tables of the current staging site
     * deprecated
     */
//    protected function getTmpTables() {
//        
//        // Calculate this once
//        if (isset($this->options->includedTables) && !empty($this->options->includedTables)){
//            return;
//        }
//        
//        $tables = $this->options->tables;
//
//        $currentTables = array();
//
//        //$this->convertExcludedTables();
//
//        // Reset excluded Tables than loop through all tables
//        //$this->options->excludedTables = array();
//        foreach ($tables as $table) {
//            
//            if (in_array($table->name, $this->options->excludedTables)){
//                continue;
//            }
//
//            $currentTables[] = array(
//                "name" => $table->name,
//                "size" => ($table->Data_length + $table->Index_length)
//            );
//        }
//        
//        //$cloneTables = array_diff($tables, (array)$this->options->excludedTables);
//
//        $this->options->includedTables = json_decode(json_encode($currentTables));
//        //$this->options->tables = json_decode(json_encode($cloneTables));
//    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps()
    {
        $this->options->totalSteps  = $this->total;
    }

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute()
    {
        // Over limits threshold
        if ($this->isOverThreshold())
        {
            // Prepare response and save current progress
            $this->prepareResponse(false, false);
            $this->saveOptions();
            return false;
        }

        // No more steps, finished
        if ($this->options->currentStep > $this->total || !isset($this->options->tables[$this->options->currentStep]))
        {
            $this->prepareResponse(true, false);
            return false;
        }

        // Table is excluded
        if (in_array($this->options->tables[$this->options->currentStep]->name, $this->options->excludedTables))
        {
            $this->prepareResponse();
            return true;
        }

        // Copy table
        if (!$this->stopExecution() && !$this->copyTable($this->options->tables[$this->options->currentStep]->name))
        {
            // Prepare Response
            $this->prepareResponse(false, false);

            // Not finished
            return true;
        }

        // Prepare Response
        $this->prepareResponse();

        // Not finished
        return true;
    }

    /**
     * Set tmp prefix for the new database tables which are used later for the new live site
     * @return string
     */
//    private function setTmpPrefix(){
//        $tmpPrefix = "wpstgtmp{$this->options->cloneNumber}";
//        
//        // Check that $tmpPrefix is not already used for other tables
//        $exists = $this->db->get_results(
//            "SHOW TABLES LIKE '%".$tmpPrefix."%'"
//        );
//    
//        if ( $exists ){
//            // Change prefix slightly to make sure it is no longer the same
//            $tmpPrefix = $tmpPrefix . 'a';
//        }  
//        
//        // Check that $tmpPrefix is not the same as the prefix of the current live site
//        if ($this->db->prefix == $tmpPrefix){
//            // Change prefix slightly to make sure it is no longer the same
//            $tmpPrefix = $tmpPrefix . 'b';         
//        }
//        
//        $clone = get_option('wpstg_existing_clones_beta', array());
//        $clone[$this->options->current]['tmpPrefix'] = $tmpPrefix;
//        
//        $this->tmpPrefix = $tmpPrefix . '_';
//        return $tmpPrefix . '_';
//    }
//    
        
    /**
     * Get prefix of the staging site the current process is running for
     * @return string
     */
//    private function getStagingPrefix(){
//        // Make sure prefix of staging site is NEVER identical to prefix of live site! 
//        if ( $this->options->prefix == $this->db->prefix ){
//            //wp_die('Fatal error 7: The database table prefix '. $this->options->prefix .' would be identical to the table prefix of the live site. Please open a support ticket at support@wp-staging.com'); 
//            $this->returnException('Fatal error 7: The database table prefix '. $this->options->prefix .' would be identical to the table prefix of the live site. Please open a support ticket at support@wp-staging.com'); 
//        }  
//        return $this->options->prefix;
//    }
   
    
    /**
     * Stop Execution immediately
     * return mixed bool | json
     */
    private function stopExecution(){
        if ($this->db->prefix == $this->tmpPrefix){
            $this->returnException('Fatal Error 9: Prefix ' . $this->db->prefix . ' is used for the live site hence it can not be used for the staging site as well. Please ask support@wp-staging.com how to resolve this.');
        }
        return false;
    }

    /**
     * Copy Tables
     * @param string $tableName
     * @return bool
     */
    private function copyTable($tableName)
    {
        $strings = new \WPStaging\Utils\Strings();
        //$tableName = is_object($tableName) ? $tableName->name : $tableName;
        //$newTableName = $this->getStagingPrefix() . $strings->str_replace_first($this->db->prefix, null, $tableName);
       
       
        //$newTableName = $this->tmpPrefix . str_replace($this->options->prefix, null, $tableName);
        $newTableName = $this->tmpPrefix . $strings->str_replace_first($this->options->prefix, null, $tableName);

        // Drop table if necessary
        $this->dropTable($newTableName);

        // Save current job
        $this->setJob($newTableName);

        // Beginning of the job
        if (!$this->startJob($newTableName, $tableName))
        {
            return true;
        }

        // Copy data
        $this->copyData($newTableName, $tableName);

        // Finish the step
        return $this->finishStep();
    }

    /**
     * Copy data from old table to new table
     * @param string $new
     * @param string $old
     */
    private function copyData($new, $old)
    {
        $rows = $this->options->job->start+$this->settings->queryLimit;
        $this->log(
            "DB tmp table: {$old} as {$new} between {$this->options->job->start} to {$rows} records"
        );

        $limitation = '';

        if (0 < (int) $this->settings->queryLimit)
        {
            $limitation = " LIMIT {$this->settings->queryLimit} OFFSET {$this->options->job->start}";
        }

        $this->db->query(
            "INSERT INTO {$new} SELECT * FROM {$old} {$limitation}"
        );

        // Set new offset
        $this->options->job->start += $this->settings->queryLimit;
    }

    /**
     * Set the job
     * @param string $table
     */
    private function setJob($table)
    {
        if (isset($this->options->job->current))
        {
            return;
        }

        $this->options->job->current = $table;
        $this->options->job->start   = 0;
    }

    /**
     * Start Job
     * @param string $new
     * @param string $old
     * @return bool
     */
    private function startJob($new, $old)
    {
        if (0 != $this->options->job->start)
        {
            return true;
        }

        $this->log("DB tmp table: Creating table {$new} like {$old}");

        $this->db->query("CREATE TABLE {$new} LIKE {$old}");

        $this->options->job->total = (int) $this->db->get_var("SELECT COUNT(1) FROM {$old}");

        if (0 == $this->options->job->total)
        {
            $this->finishStep();
            return false;
        }

        return true;
    }

    /**
     * Finish the step
     */
    private function finishStep()
    {
        // This job is not finished yet
        if ($this->options->job->total > $this->options->job->start)
        {
            return false;
        }

        // Add it to cloned tables listing
        $this->options->clonedTables[]  = $this->options->tables[$this->options->currentStep];

        // Reset job
        $this->options->job             = new \stdClass();

        return true;
    }

    /**
     * Drop table if necessary
     * @param string $new
     */
    private function dropTable($new)
    {
        $old = $this->db->get_var($this->db->prepare("SHOW TABLES LIKE %s", $new));

        if (!$this->shouldDropTable($new, $old))
        {
            return;
        }

        $this->log("DB tmp table: {$new} already exists, dropping it first");
        $this->db->query("DROP TABLE {$new}");
    }

    /**
     * Check if table needs to be dropped
     * @param string $new
     * @param string $old
     * @return bool
     */
    private function shouldDropTable($new, $old)
    {
        return (
            $old == $new &&
            (
                !isset($this->options->job->current) ||
                !isset($this->options->job->start) ||
                0 == $this->options->job->start
            )
        );
    }
}