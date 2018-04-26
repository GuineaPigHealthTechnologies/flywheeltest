<?php
/**
 * Plugin Name: Review Builder
 * Plugin URI: https://review-builder.com
 * Description: Review Builder (Review & Product Review) will allow you to add reviews section to your site. Build a reviews section so customers can leave reviews for your products.
 * Version: 2.0.20
 * Author: Review Builder By Sygnoos
 * Author URI: https://review-builder.com
 * Text Domain: sgrb
 * License: GPLv3
 */

if (!defined('WPINC')) {
	die();
}

require_once(dirname(__FILE__).'/com/config/bootstrap.php');
require_once(dirname(__FILE__).'/com/core/SGRB.php');

global $sgrb;

$sgrb = new SGRB();
$sgrb->app_path = realpath(dirname(__FILE__)).'/';
$sgrb->app_url = plugin_dir_url(__FILE__);
$sgrb->run();
