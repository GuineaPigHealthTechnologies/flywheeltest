<?php
namespace WPStaging\Backend\Pro\Modules\Jobs;

use WPStaging\WPStaging;

//error_reporting( E_ALL );

/**
 * Class Finish
 * @package WPStaging\Backend\Modules\Jobs
 */
class Finish extends \WPStaging\Backend\Modules\Jobs\Job
{

    /**
     * Start Module
     * @return object
     */
    public function start()
    {

        // Do not do anything
        return $this->response = array(
            "status"        => 'finished',
            "percentage"    => 100,
            "total"         => $this->options->totalSteps,
            "step"          => $this->options->currentStep,
            "last_msg"      => $this->logger->getLastLogMsg(),
            "running_time"  => $this->time() - time(),
            "job_done"      => true
        );
    }

}