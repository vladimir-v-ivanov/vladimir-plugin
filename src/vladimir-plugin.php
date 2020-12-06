<?php

/**
 * Plugin Name: Vladimir-Plugin
 * Version: 1.0.0
 * Author: Vladimir Ivanov
 * Author URI: mailto:vladimir@ivanov.click
 */

if(!defined( 'WPINC' )) {
	die;
}

define('VLADIMIR_PLUGIN_VERSION', '1.0.0');
define('VLADIMIR_PLUGIN_ROOT', __FILE__);

/* Hardcoded because no requirements for settings page */
define('VLADIMIR_PLUGIN_API_URL', 'https://jsonplaceholder.typicode.com');
define('VALDIMIR_PLUGIN_LIST_URL', 'user_list');
define('VLADIMIR_PLUGIN_DETAILS_URL', 'user_details');
define('VLADIMIR_PLUGIN_LIST_TEMPLATE', 'table.php');

function activate_vladimir_plugin()
{

}

register_activation_hook(VLADIMIR_PLUGIN_ROOT, 'activate_vladimir_plugin');

function deactivate_vladimir_plugin()
{

}

register_deactivation_hook(VLADIMIR_PLUGIN_ROOT, 'deactivate_vladimir_plugin');

require_once plugin_dir_path(VLADIMIR_PLUGIN_ROOT) . 'includes/class-vladimir-plugin.php';

$vladimir_plugin = new Vladimir_Plugin;
$vladimir_plugin->run();