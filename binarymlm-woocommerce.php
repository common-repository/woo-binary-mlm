<?php

/**
 * Plugin Name: Woo-Binary-MLM
 * Description: Binary MLM Business Software - A Binary MLM (Multi-Level Marketing) structure is a specific type of compensation plan in the MLM business model. In this structure, each member or distributor recruits and sponsors two other distributors, forming two "legs" or "downlines." These distributors further recruit their two distributors, and the process continues, creating a tree-like structure.
 * Version: 2.0
 * Author: Letscms
 * Author URI: https://www.letscms.com/
 * Text Domain: BMW
 * Domain Path: /i18n/languages/
 * Requires at least: 6.2
 * Requires PHP:      8.0
 *
 * @package woo-binary-mlm
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define BMW_PLUGIN_FILE.
if (!defined('BMW_PLUGIN_FILE')) {
    define('BMW_PLUGIN_FILE', __FILE__);
}

// Define BME_ABSPATH
if (!defined('BMW_ABSPATH')) {
    define('BMW_ABSPATH', dirname(__FILE__));
}

// Define BME_URL.
if (!defined('BMW_URL')) {
    define('BMW_URL', plugins_url('', __FILE__));
}

// Include the main Letscms_BMW class.
if (!class_exists('Letscms_BMW')) {
    include_once dirname(__FILE__) . '/includes/class-bmw.php';
}

/**
 * Main instance of Letscms_BMW.
 *
 */
function letscms_bmw()
{
    return Letscms_BMW::instance();
}

// Global for backwards compatibility.
$GLOBALS['bmw'] = letscms_bmw();
