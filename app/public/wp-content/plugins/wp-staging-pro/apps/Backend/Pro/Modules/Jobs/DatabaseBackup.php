<?php
namespace WPStaging\Backend\Pro\Modules\Jobs;

// No Direct Access
if (!defined("WPINC"))
{
    die;
}

use WPStaging\WPStaging;
use WPStaging\Utils\Strings;

/**
 * Class DatabaseBackup
 * @package WPStaging\Backend\Modules\Jobs
 */
class DatabaseBackup extends \WPStaging\Backend\Modules\Jobs\JobExecutable
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
     * Initialize
     */
    public function initialize()
    {
        // Reset tables
        //unset($this->options->tables);

        // Variables
        $this->db                   = WPStaging::getInstance()->get("wpdb");
        $this->getTables();
        $this->total                = count($this->options->tables);
        
        if ($this->total <= 0){
        $this->returnException('Fatal Error: No tables to backup. Stoping for security reasons');
        }

        // Save job params
        $this->saveOptions();


    }
    
    /**
     * Get Database Tables of the current staging site
     * deprecated
     */
    public function getTables()
    {
        //$wpDB = WPStaging::getInstance()->get("wpdb");
        
        $prefix = $this->db->prefix;

        if (strlen($prefix) > 0)
        {
            $sql = "SHOW TABLE STATUS LIKE '{$prefix}%'";
        }
        else
        {
            $sql = "SHOW TABLE STATUS";
        }
        
        $tables = $this->db->get_results($sql);
        
        $currentTables = array();

        foreach ($tables as $table)
        {
            //Exclude WP Staging Tables
            if (false === strpos($table->Name, $this->db->prefix))
            {
                continue;
            }

            $currentTables[] = array(
                "name"  => $table->Name,
                "size"  => ($table->Data_length + $table->Index_length)
            );
        }

        $this->options->tables = json_decode(json_encode($currentTables));

    }

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


        // Copy table
        if ( !$this->copyTable($this->options->tables[$this->options->currentStep]->name))
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
     * Copy DB table
     * @param string $tableName
     * @return bool
     */
    private function copyTable($tableName)
    {
        $strings = new Strings();
       
        //$newTableName = 'wpstgbak_' . str_replace($this->db->prefix, null, $tableName);
        $newTableName = 'wpstgbak_' . $strings->str_replace_first($this->db->prefix, null, $tableName);

        if($newTableName == $tableName){
                $this->returnException('Fatal Error 10: Prefix ' . $this->db->prefix . ' is used for the live site hence it can not be used for backup tables as well. Please ask support@wp-staging.com how to resolve this.');
        }

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

        // Finis the step
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
            "Backup: Copy {$old} as {$new} between {$this->options->job->start} to {$rows} records"
        );

        $limitation = '';

        if (0 < (int) $this->settings->queryLimit)
        {
            $limitation = " LIMIT {$this->settings->queryLimit} OFFSET {$this->options->job->start}";
        }

        $this->db->query(
            "INSERT INTO {$new} SELECT * FROM {$old} {$limitation}"
        );
            
        if ($this->db->last_error){
        $this->returnException('Backup: Can not backup all tables. Stoping for security reasons');
        }

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

        $this->log("Backup: Creating table {$new} like {$old}");

        $this->db->query("CREATE TABLE {$new} LIKE {$old}");
        
        if ($this->db->last_error){
        $this->returnException('Fatal Error 12: Can not backup all tables. Stoping for security reasons');
        }

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
                  
                
        // Delete array of tables for next job
        //$this->options->tables = array();
        $this->options->excludedTables = array();

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

        $this->log("Backup: table {$new} already exists, dropping it first");
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
            $old === $new &&
            (
                !isset($this->options->job->current) ||
                !isset($this->options->job->start) ||
                0 == $this->options->job->start
            )
        );
    }
}