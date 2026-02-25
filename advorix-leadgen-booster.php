<?php
/*
Plugin Name: Advorix LeadGen Booster - Popup Lead Capture & Chat Button
Plugin URI: https://advorixtechnologies.42web.io/advorix-leadgen-booster.php
Description: Free WordPress popup lead capture plugin with floating chat button integration. Collect leads, grow your email list, and boost conversions instantly.
Version: 1.0
Author: Advorix Technologies
Author URI: https://advorixtechnologies.42web.io/
License: GPLv2 or later
Text Domain: advorix-leadgen-booster
Domain Path: /languages
*/

defined('ABSPATH') || exit;

define('ADV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADV_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ADV_PLUGIN_VERSION', '1.0');

// Includes
require_once ADV_PLUGIN_DIR . 'includes/class-loader.php';
require_once ADV_PLUGIN_DIR . 'includes/class-activator.php';
require_once ADV_PLUGIN_DIR . 'includes/class-deactivator.php';

// Activation & Deactivation
register_activation_hook(__FILE__, array('Adv_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Adv_Deactivator', 'deactivate'));

// Run Plugin
function run_adv_plugin() {
    $plugin = new Adv_Loader();
    $plugin->run();
}
run_adv_plugin();