<?php defined( 'ABSPATH' ) or die( 'No direct access allowed' );
/*
Plugin Name: WordPress Job Board
Plugin URI: https://thephpx.wordpress.org/plugins/wordpress-job-board/
Description: Free wordpress job board
Version: 0.1.0
Author: Faisal Ahmed
Author URI: http://www.faisalbd.com
Text Domain: wordpress-job-board
*/

define('APPL_PLUGINFILE', __FILE__);
define('APPL_PLUGINDIR', dirname(__FILE__));

require_once('vendor/autoload.php');
global $wpdb;

use App\Plugin as Application;

# Register job type post
# Register application type post
# Create new job post
# Create new draft application post + link job post

$plugin = Application::getInstance();
$plugin->setDB($wpdb);
$plugin->initialize();
$plugin->setAction('job_post_action','job_post_action_do');
register_activation_hook(__FILE__, array($plugin, 'activate'));
register_deactivation_hook(__FILE__, array($plugin, 'deactivate'));
