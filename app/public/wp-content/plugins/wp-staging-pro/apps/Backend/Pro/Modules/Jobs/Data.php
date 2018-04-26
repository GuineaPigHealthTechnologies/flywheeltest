<?php

namespace WPStaging\Backend\Pro\Modules\Jobs;

// No Direct Access
if (!defined("WPINC")) {
    die;
}

use WPStaging\Utils\Logger;
use WPStaging\WPStaging;

/**
 * Class Data
 * @package WPStaging\Backend\Pro\Modules\Jobs
 */
class Data extends \WPStaging\Backend\Modules\Jobs\JobExecutable {

    /**
     * @var \wpdb
     */
    private $db;

    /**
     * @var string
     */
    private $prefix;

    /**
     * Initialize
     */
    public function initialize() {
        $this->db = WPStaging::getInstance()->get("wpdb");      
        
        // Prefix of the tmp database tables
        $this->tmpPrefix = 'wpstgtmp_';

        // Fix current step
        if (0 == $this->options->currentStep) {
            $this->options->currentStep = 1;
        }
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps() {
        $this->options->totalSteps = 8;
    }

    /**
     * Start Module
     * @return object
     */
    public function start() {
        // Execute steps
        $this->run();

        // Save option, progress
        $this->saveOptions();

        // Prepare response
        $this->response = array(
            "status" => true,
            "percentage" => 0,
            "total" => $this->options->totalSteps,
            "step" => $this->options->currentStep,
            "last_msg" => $this->logger->getLastLogMsg(),
            "running_time" => $this->time() - time(),
            "method" => "step" . $this->options->currentStep
        );

        return (object) $this->response;
    }

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute() {

        // Over limits threshold
//        if ($this->isOverThreshold()) {
//            // Prepare response and save current progress
//            $this->prepareResponse(false, false);
//            $this->saveOptions();
//            return false;
//        }

        // No more steps, finished
        if ($this->isFinished()) {
            $this->prepareResponse(true, false);
            return false;
        }

        // Execute step
        $stepMethodName = "step" . $this->options->currentStep;
        if (!$this->{$stepMethodName}()) {
            $this->prepareResponse(false, false);
            return false;
        }

        // Prepare Response
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
                $this->options->currentStep > $this->options->totalSteps ||
                !method_exists($this, "step" . $this->options->currentStep)
                );
    }

    /**
     * Check if table exists
     * @param string $table
     * @return boolean
     */
    protected function isTable($table) {
        if ($this->db->get_var("SHOW TABLES LIKE '{$table}'") != $table) {
            $this->log("Table {$table} does not exists", Logger::TYPE_ERROR);
            return false;
        }
        return true;
    }
    
    /**
     * Check if table is exluded
     * @param string $table
     * @return boolean
     */
    protected function isTableExcluded($table){
        if (in_array($table, $this->options->excludedTables)){
            return true;
        }
        return false;
    }

    /**
     * Update several entries in options table
     * @return bool
     */
    protected function step1() {
        
        
        $optionsTableTmp = $this->tmpPrefix . 'options';
        
        $optionsTable = $this->db->prefix . 'options';
        
        // Options table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'options')){
            $this->log("Data Crunching Step 1: Skipping table {$this->options->prefix}options");
            return true;
        }

        $this->log("Data Crunching: Move staging sites to " . $optionsTableTmp);

        // Get staging sites data from live site - WP Staging 2.0 and higher
        $wpstg_existing_clones_beta = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'wpstg_existing_clones_beta' ");

        if (!$wpstg_existing_clones_beta) {
            $this->log("Data Crunching: Can not get data wpstg_existing_clones_beta from" . $optionsTableTmp);
            $this->returnException('Data Crunching: Can not get data wpstg_existing_clones_beta from' . $optionsTableTmp);
        }

        // do some escaping
        $serialized = $this->mysql_escape_mimic($wpstg_existing_clones_beta);
        
        // Get staging sites data from tmp table
        $wpstg_existing_clones_beta_tmp_tbl = $this->db->get_var("SELECT option_value FROM {$optionsTableTmp} options WHERE option_name = 'wpstg_existing_clones_beta' ");
        
         // Copy staging sites data to tmp table
        if( !$wpstg_existing_clones_beta_tmp_tbl ) {
         $query = $this->db->query(
                 "INSERT INTO {$optionsTableTmp} (option_name, option_value) VALUES ('wpstg_existing_clones_beta', '{$serialized}')"
         );
      } else {
         $query = $this->db->query(
                 "UPDATE {$optionsTableTmp} SET option_value = '" . $serialized . "' WHERE option_name = 'wpstg_existing_clones_beta' "
         );
      }



      if (false === $query) {
            $this->log("Data Crunching: Can not update table wpstg_existing_clones_beta in" . $optionsTableTmp);
            $this->returnException("Data Crunching: Can not update table wpstg_existing_clones_beta in" . $optionsTableTmp . ' - db error: ' . $this->db->last_error);
            return false;
        }

        // Delete wpstg_is_staging_site entry
        $resultDeleteIsStagingSite = $this->db->query(
                "DELETE FROM {$optionsTableTmp} WHERE option_name = 'wpstg_is_staging_site' "
        );

        if (false === $resultDeleteIsStagingSite) {
            $this->log("Data Crunching: Can not delete table row wpstg_is_staging_site from " . $optionsTableTmp);
            $this->returnException("Data Crunching: Can not delete table row wpstg_is_staging_site from " . $optionsTableTmp . " - db error: " . $this->db->last_error);
            return false;
        }

        // Copy license data
        $wpstgLicense = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'wpstg_license_key' ");

        $resultWpstgLicense = $this->db->replace(
                $this->tmpPrefix . 'options', array(
            'option_name' => 'wpstg_license_key',
            'option_value' => $wpstgLicense ? $wpstgLicense : ''
                ), array(
            '%s',
            '%s'
                )
        );

        if (false === $resultWpstgLicense) {
            $this->log('Data Crunching: Warning - Can not copy license key from live site');
        }


        // Copy wpstg settings
        $wpstgSettings = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'wpstg_settings' ");

        $resultWpstgSettings = $this->db->replace(
                $this->tmpPrefix . 'options', array(
            'option_name' => 'wpstg_settings',
            //'option_value' => $wpstgSettings ? $this->mysql_escape_mimic($wpstgSettings) : ''
            'option_value' => $wpstgSettings ? $wpstgSettings : ''
                ), array(
            '%s',
            '%s'
                )
        );

        if (false === $resultWpstgSettings) {
            $this->log('Data Crunching: Error - Can not copy WP Staging settings from live site');
            $this->returnException('Data Crunching: Error - Can not copy WP Staging settings from live site - db error: ' . $this->db->last_error);
        }

        // Copy wpstg license status
        $wpstg_license_status = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'wpstg_license_status' ");

        $resultWpstg_license_status = $this->db->replace(
                $this->tmpPrefix . 'options', array(
            'option_name' => 'wpstg_license_status',
            //'option_value' => $wpstg_license_status ? $this->mysql_escape_mimic($wpstg_license_status) : ''
            'option_value' => $wpstg_license_status ? $wpstg_license_status : ''
                ), array(
            '%s',
            '%s'
                )
        );

        if (false === $resultWpstg_license_status) {
            $this->log('Data Crunching: Warning: Can not copy WP Staging license status from live site');
            $this->returnException('Data Crunching: Warning: Can not copy WP Staging license status from live site');
        }
        
        $this->log("Data Crunching Step 1: Successfull!");

        return true;
    }

    /**
     * Update table wp_options
     * Change table prefix
     * @return bool
     */
    protected function step2() {
        
        
        // options table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'options')){
            $this->log("Data Crunching Step 2: Skipping table {$this->options->prefix}options");
            return true;
        }

        $this->log("Data Crunching Step 2: Updating {$this->tmpPrefix}options table prefix to {$this->db->prefix}. db error: {$this->db->last_error}");
        $this->debugLog("Data Crunching Step 2: SQL - UPDATE {$this->tmpPrefix}options SET option_name = replace(option_name, {$this->options->prefix}, {$this->db->prefix}) WHERE option_name LIKE {$this->options->prefix}_%");

        $resultOptions = $this->db->query(
                $this->db->prepare(
                        "UPDATE IGNORE {$this->tmpPrefix}options SET option_name= replace(option_name, %s, %s) WHERE option_name LIKE %s", $this->options->prefix, $this->db->prefix, $this->options->prefix . "_%"
                )
        );

        if (!$resultOptions) {
            $this->log("Data Crunching Step 2: Failed to update {$this->tmpPrefix}options with table prefixes. DB Error: {$this->db->last_error}");
            $this->returnException("Data Crunching Step 2: Failed to update {$this->tmpPrefix}options with table prefixes {$this->db->prefix}. DB Error: {$this->db->last_error}");
            return false;
        }
        $this->log("Data Crunching Step 2: Successfull!");

        return true;
    }
    /**
     * Update table user_meta
     * Change table prefix
     * @return bool
     */
    protected function step3() {
        
        
        // usermeta table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'usermeta')){
            $this->log("Data Crunching Step 3: Skipping table {$this->options->prefix}user_meta");
            return true;
        }

        $this->log("Data Crunching Step 3: Updating {$this->tmpPrefix}usermeta db prefix to {$this->db->prefix} {$this->db->last_error}");

        if (false === $this->isTable($this->tmpPrefix . 'usermeta')) {
            $this->log('Data Crunching Step 3: Fatal Error ' . $this->tmpPrefix . 'usermeta does not exist');
            $this->returnException('Data Crunching Step 3: Fatal Error ' . $this->tmpPrefix . 'usermeta does not exist');
            return false;
        }

        $resultMetaKeys = $this->db->query(
                $this->db->prepare(
                        "UPDATE {$this->tmpPrefix}usermeta SET meta_key = replace(meta_key, %s, %s) WHERE meta_key LIKE %s", $this->options->prefix, $this->db->prefix, $this->options->prefix . "_%"
                )
        );

        if (!$resultMetaKeys) {
            $this->log("Data Crunching Step 3: SQL - UPDATE {$this->tmpPrefix}usermeta SET meta_key = replace(meta_key, {$this->options->prefix}, {$this->db->prefix}) WHERE meta_key LIKE {$this->options->prefix}_%");
            $this->log("Data Crunching Step 3: Failed to update usermeta meta_key database table prefixes {$this->db->last_error}");
            $this->returnException("Data Crunching Step 3: Failed to update {$this->tmpPrefix}usermeta meta_key database table prefixes {$this->db->last_error}");
            return false;
        }

        $this->log("Data Crunching Step 3: Successfull!");

        return true;
    }
    
    /**
     * Update table options 
     * Update active plugins
     * @return bool
     */
    protected function step4() {
        
        
        // Options table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'options')){
            return true;
        }

        $this->log("Data Crunching Step 4: Updating {$this->tmpPrefix}options active_plugins");

        if (false === $this->isTable($this->tmpPrefix . 'options')) {
            $this->log('Data Crunching Step 4: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            $this->returnException('Data Crunching Step 4: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            return false;
        }

        
        // Get active_plugins from tmp tables
        $activePlugins = $this->db->get_var("SELECT option_value FROM {$this->tmpPrefix}options WHERE option_name = 'active_plugins' ");

        $activePlugins = unserialize($activePlugins);
        
        if (!$activePlugins) {
            $this->log("Data Crunching Step 4: Can not get list of active plugins from from {$this->tmpPrefix}options ");
            $this->returnException("Data Crunching Step 4: Can not get active_plugins from {$this->tmpPrefix}options");
        }
        

        // Delete wp-staging/wp-staging.php from array
        if (($key = array_search('wp-staging/wp-staging.php', $activePlugins)) !== false) {
            unset($activePlugins[$key]);  
        }
        if (($key = array_search('wp-staging/wp-staging.php', $activePlugins)) !== false) {
            unset($activePlugins[$key]);  
        }
        if (($key = array_search('wp-staging-pro/wp-staging-pro.php', $activePlugins)) === false) {
            $activePlugins[] = 'wp-staging-pro/wp-staging-pro.php';
        }

        
        // Update active_plugins
        $resultActivePlugins = $this->db->query(
                "UPDATE {$this->tmpPrefix}options SET option_value = '" . serialize($activePlugins) . "' WHERE option_name = 'active_plugins' "
        );

        if (false === $resultActivePlugins) {
            $this->log("Data Crunching Step 4: Can not update table active_plugins in {$this->tmpPrefix}options");
            $this->returnException("Data Crunching Step 4: Can not update table active_plugins in {$this->tmpPrefix}options - db error: " . $this->db->last_error);
            return false;
        }

        $this->log("Data Crunching Step 4: Successfull!");
        return true;
    }
    
    /**
     * Update table tmp_usermeta session token
     * @return bool
     */
    protected function step5() {
        
        
        // usermeta table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'usermeta')){
            return true;
        }

        $this->log("Data Crunching Step 5: Updating {$this->tmpPrefix}usermeta session_tokens");

        if (false === $this->isTable($this->tmpPrefix . 'usermeta')) {
            $this->log('Data Crunching Step 5: Fatal Error ' . $this->tmpPrefix . 'usermeta does not exist', Logger::TYPE_ERROR);
            $this->returnException('Data Crunching Step 5: Fatal Error ' . $this->tmpPrefix . 'usermeta does not exist');
            return false;
        }

        $userId = get_current_user_id();
        // Get session token for current user from live site usermeta table
        $sessionToken = $this->db->get_var("SELECT meta_value FROM {$this->db->prefix}usermeta WHERE meta_key = 'session_tokens' AND user_id = '{$userId}'");

        $sessionToken = unserialize($sessionToken);
        
        if (!$sessionToken) {
            $this->log("Data Crunching Step 5: Can not get session token of current user from {$this->db->prefix}usermeta ", Logger::TYPE_WARNING);
            //$this->returnException("Data Crunching Step 5: Can not get session token from {$this->db->prefix}usermeta");
        }
        // Update session_tokens
        $resultSessionToken = $this->db->query(
                "UPDATE {$this->tmpPrefix}usermeta SET meta_value = '" . serialize($sessionToken) . "' WHERE meta_key = 'session_tokens' AND user_id = {$userId}"
        );

        if (false === $resultSessionToken) {
            $this->log("Data Crunching Step 5: Can not update row session_tokens in {$this->tmpPrefix}usermeta", Logger::TYPE_WARNING);
            //$this->returnException("Data Crunching Step 5: Can not update row session_tokens in {$this->tmpPrefix}usermeta - db error: " . $this->db->last_error);
            return false;
        }

        $this->log("Data Crunching Step 5: Successfull!");
        return true;
    }
    
    /**
     * Get permalink_structure from live site and copy it to the migrating tmp_tables to keep the current permalink structure
     * Update permalink_structure
     * @return bool
     */
    protected function step6() {
        
        
        // options table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'options')){
            return true;
        }

        $this->log("Data Crunching Step 6: Updating {$this->tmpPrefix}options permalink_structure");

        if (false === $this->isTable($this->tmpPrefix . 'options')) {
            $this->log('Data Crunching Step 6: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            $this->returnException('Data Crunching Step 6: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            return false;
        }

        // Get permalink_structure value from live site options table
        $permalink = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'permalink_structure' ");

        //$permalink = unserialize($permalink);
        
        if (!$permalink) {
            $this->log("Data Crunching Step 6: Can not get permalink_structure from {$this->db->prefix}options ");
            //$this->returnException("Data Crunching Step 6: Can not get permalink_structure from {$this->db->prefix}options");
            $permalink = '/%postname%/';
        }
        // Update permalink_structure
        $resultPermalink = $this->db->query(
                "UPDATE {$this->tmpPrefix}options SET option_value = '" . $permalink . "' WHERE option_name = 'permalink_structure'"
        );

        if (false === $resultPermalink) {
            $this->log("Data Crunching Step 6: Can not update row permalink_structure in {$this->tmpPrefix}options");
            $this->returnException("Data Crunching Step 6: Can not update row permalink_structure in {$this->tmpPrefix}options - db error: " . $this->db->last_error);
            return false;
        }

        $this->log("Data Crunching Step 6: Successfull!");
        return true;
    }
    /**
     * Get original siteurl and home path and copy it to the wpstgtmp table
     * Update permalink_structure
     * @return bool
     */
    protected function step7() {
        
        
        // options table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'options')){
            return true;
        }

        $this->log("Data Crunching Step 7: Updating {$this->tmpPrefix}options siteurl");

        if (false === $this->isTable($this->tmpPrefix . 'options')) {
            $this->log('Data Crunching Step 7: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            $this->returnException('Data Crunching Step 7: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            return false;
        }

        // Get siteurl value from live site options table
        $siteUrl = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'siteurl' ");
        
        if (!$siteUrl) {
            $this->log("Data Crunching Step 7: Can not get siteurl from {$this->db->prefix}options ");
            $this->returnException("Data Crunching Step 7: Can not find siteurl in {$this->db->prefix}options");
        }
        // Update siteurl
        $resultSiteUrl = $this->db->query(
                "UPDATE {$this->tmpPrefix}options SET option_value = '" . $siteUrl . "' WHERE option_name = 'siteurl'"
        );

        if (false === $resultSiteUrl) {
            $this->log("Data Crunching Step 7: Can not update row siteurl in {$this->tmpPrefix}options");
            $this->returnException("Data Crunching Step 7: Can not update row siteurl in {$this->tmpPrefix}options - db error: " . $this->db->last_error);
            return false;
        }
        
        // Get home value from live site options table
        $home = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'home' ");
        
        if (!$home) {
            $this->log("Data Crunching Step 7: Can not get home from {$this->db->prefix}options ");
            $this->returnException("Data Crunching Step 7: Can not find fome in {$this->db->prefix}options");
        }
        // Update home
        $resultHome = $this->db->query(
                "UPDATE {$this->tmpPrefix}options SET option_value = '" . $home . "' WHERE option_name = 'home'"
        );

        if (false === $resultHome) {
            $this->log("Data Crunching Step 7: Can not update row home in {$this->tmpPrefix}options");
            $this->returnException("Data Crunching Step 7: Can not update row home in {$this->tmpPrefix}options - db error: " . $this->db->last_error);
            return false;
        }

        $this->log("Data Crunching Step 7: Successfull!");
        return true;
    }
    
    /**
     * Get field wpstg_version from live site and copy it to wpstgtmp_options
     */
    protected function step8() {
        // options table has been exluded from pushing process so exit here
        if ($this->isTableExcluded($this->options->prefix . 'options')){
            return true;
        }
        
        $this->log("Data Crunching Step 8: Updating {$this->tmpPrefix}options wpstg_version");

        if (false === $this->isTable($this->tmpPrefix . 'options')) {
            $this->log('Data Crunching Step 8: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            $this->returnException('Data Crunching Step 8: Fatal Error ' . $this->tmpPrefix . 'options does not exist');
            return false;
        }

        // Get wpstg_version value from live site options table
        $select = $this->db->get_var("SELECT option_value FROM {$this->db->prefix}options WHERE option_name = 'wpstg_version' ");
        
        if (!$select) {
            $this->log("Data Crunching Step 8: Can not get wpstg_version from {$this->db->prefix}options ");
            $this->returnException("Data Crunching Step 8: Can not find wpstg_version in {$this->db->prefix}options");
        }
        // Update wpstg_version
        $update = $this->db->query(
                "UPDATE {$this->tmpPrefix}options SET option_value = '" . $select . "' WHERE option_name = 'wpstg_version'"
        );

        if (false === $update) {
            $this->log("Data Crunching Step 8: Can not update row wpstg_version in {$this->tmpPrefix}options");
            $this->returnException("Data Crunching Step 8: Can not update row wpstg_version in {$this->tmpPrefix}options - db error: " . $this->db->last_error);
            return false;
        }

        $this->log("Data Crunching Step 8: Successfull!");
        return true;        
    }
    

    /**
     * Mimics the mysql_real_escape_string function. Adapted from a post by 'feedr' on php.net.
     * @link   http://php.net/manual/en/function.mysql-real-escape-string.php#101248
     * @access public
     * @param  string $input The string to escape.
     * @return string
     */
    public function mysql_escape_mimic($input) {
        if (is_array($input)) {
            return array_map(__METHOD__, $input);
        }
        if (!empty($input) && is_string($input)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $input);
        }

        return $input;
    }
}
