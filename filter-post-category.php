<?php
/*
Plugin Name: FILTER POST CATEGORY
Plugin URI: https://github.com/kvnZero/WordPress-Estimate-Read-Time
Description:
Version: 1.0
Author: abigeater
Author URI: https://abigeater.com
*/

define('AB_FILTER_POST_CATEGORY_PLUGIN_URL', plugins_url('', __FILE__));
define('AB_FILTER_POST_CATEGORY_PLUGIN_DIR', plugin_dir_path(__FILE__));

include AB_FILTER_POST_CATEGORY_PLUGIN_DIR . 'public/hooks.php';