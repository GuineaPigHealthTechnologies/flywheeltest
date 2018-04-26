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
 * Class Database
 * @package WPStaging\Backend\Modules\Jobs
 */
class DatabaseTmpRename extends \WPStaging\Backend\Modules\Jobs\JobExecutable
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
    
    private $tmpPrefix;

    /**
     * Initialize
     */
    public function initialize()
    {
        // Variables
        $this->db                   = WPStaging::getInstance()->get("wpdb");
//        $this->options->prefix      = isset($this->options->existingClones[$this->options->clone]['prefix']) ? 
//                                        $this->options->existingClones[$this->options->clone]['prefix'] : 
//                                        'unknownprefix';  
        $this->total                = count($this->options->tables);
        $this->tmpPrefix            = 'wpstgtmp_';
        
        $this->checkFatalError();

    }
    
    
    protected function checkFatalError(){
        if ($this->tmpPrefix == $this->db->prefix){
            $this->returnException('Fatal Error: Can not replace live tables with staging ones because table prefix of both is the same. Please try again or contact support@wp-staging.com');
        }
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
            $this->flush();
            $this->isFinished();
            return false;
        }

        // Table is excluded
        if (in_array($this->options->tables[$this->options->currentStep]->name, $this->options->excludedTables))
        {
            $this->prepareResponse();
            return true;
        }

        // Rename tables
        if (!$this->stopExecution() && !$this->renameTable($this->options->tables[$this->options->currentStep]->name))
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
     * Push is finished
     * @return boolean
     */
    protected function isFinished() {
        $this->log('Pushing Process has been finished successfully');
        $this->prepareResponse(true, false);
        return false;
    }
    
    /**
     * Flush wpdb cache and permalinks
     * @global type $wp_rewrite
     */
    protected function flush() {
        // flush rewrite rules to prevent 404s 
        // and other oddities
        wp_cache_flush();
        global $wp_rewrite;
        $wp_rewrite->init();
        flush_rewrite_rules(true); // true = hard refresh, recreates the .htaccess file
    }

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
     * Switch over tmp tables to live one
     * @param string $tmp table name
     * @return bool true
     */
    protected function renameTable($tmp)
    {
        $strings = new Strings();
        // Table name without prefix
        $table = $strings->str_replace_first($this->options->prefix, '', $tmp);
        
        $backupTable = 'wpstgbak_' . $table;
        
        $tmpTable = $this->tmpPrefix . $table;
        
        $liveTable = $this->db->prefix . $table;
        
        // Drop first if table already exists
        $this->dropTable($backupTable);
        
        $this->log('DB Rename: ' . $tmpTable . ' to ' . $liveTable);
        
        // Check if table already exists
        if ($this->isTable($liveTable)) {
            if (false === $this->db->query("RENAME TABLE {$liveTable} TO {$backupTable}, {$tmpTable} TO {$liveTable}")) {
                $this->log("DB Rename: Error - Can not rename table {$liveTable} TO {$backupTable} and {$tmpTable} TO {$liveTable} ");
                $this->returnException("DB Rename: Error - Can not rename table {$liveTable} TO {$backupTable} and {$tmpTable} TO {$liveTable} db error - " . $this->db->last_error);
            }
        } else {
            if (false === $this->db->query("RENAME TABLE {$tmpTable} TO {$liveTable}")) {
                $this->log("DB Rename: Error - Can not rename table {$tmpTable} TO {$liveTable}");
                $this->returnException("DB Rename: Error - Can not rename table {$tmpTable} TO {$liveTable} db error - " . $this->db->last_error);
            }
        }

        return true;
    }
    
    

    /**
     * Drop table if necessary
     * @param string $new
     */
    protected function dropTable($table)
    {
        // Check if table already exists
        $old = $this->db->get_var($this->db->prepare("SHOW TABLES LIKE %s", $table));

        if (!$this->shouldDropTable($table, $old))
        {
            return;
        }

        $this->log("DB Rename: {$table} already exists, dropping it first");
        if (false === $this->db->query("DROP TABLE {$table}")){
            $this->log("DB Rename: Can not drop table {$table}");
            $this->returnException("DB Rename: Can not drop table {$table}");
        }
    }

    /**
     * Check if table needs to be dropped first
     * @param string $new
     * @param string $old
     * @return bool
     */
    protected function shouldDropTable($new, $old)
    {
//                
//        // Never drop any table if it belongs to live site
//        if ($new == $this->db->prefix || $old == $this->db->prefix){
//            return false;
//        }
        
        return ( $old == $new );
    }
    
    /**
     * Check if table exists
     * @param string $table
     * @return boolean
     */
    protected function isTable($table) {
        if ($this->db->get_var("SHOW TABLES LIKE '{$table}'") != $table) {
            //$this->log("DB Rename: Table {$table} does not exists");
            return false;
        }
        return true;
    }
}