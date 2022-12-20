<?php

/**
 * Plugin Name: Farazsms-React
 * Plugin URI: https://farazsms.com/
 * Description: By using the Farazsms plugin, you can professionally equip your site with a powerful SMS tool for information and marketing. Saving customers numbers in the phone book, sending welcome SMS, sending reply SMS to comments, etc. are part of the features of this powerful SMS plugin.
 * Version: 2.0.0
 * Requires at least: 5.5
 * Requires PHP: 7.1
 * Author: Farazsms
 * Author URI: https://farazsms.com/
 * Text Domain: farazsms
 * Domain Path: /languages
 * 
 * WC requires at least: 3.0
 * WC tested up to: 7.1
 * 
 * @package Farazsms
 */

defined('ABSPATH') || exit; // Exit if accessed directly

/**
 * Defines all constants
 *
 * @since 2.0.0
 */

define('FARAZSMS_VER',                   '2.0.0');
define('FARAZSMS_FILE',                  __FILE__);
define('FARAZSMS_PATH',                  plugin_dir_path(FARAZSMS_FILE));
define('FARAZSMS_BASE',                  plugin_basename(FARAZSMS_FILE));
define('FARAZSMS_SLUG',                  'farazsms_settings');
define('FARAZSMS_SETTINGS_LINK',         admin_url('admin.php?page=' . FARAZSMS_SLUG));
define('FARAZSMS_CLASSES_PATH',          FARAZSMS_PATH . 'classes/');
define('FARAZSMS_MODULES_PATH',          FARAZSMS_PATH . 'modules/');
define('FARAZSMS_URL',                   plugins_url('/', FARAZSMS_FILE));
define('FARAZSMS_CLASSES_URL',           FARAZSMS_URL . 'classes/');
define('FARAZSMS_ADMIN_URL',             FARAZSMS_CLASSES_URL . 'admin/');
define('FARAZSMS_WEB_MAIN',              'https://farazsms.com/');
define('FARAZSMS_WEB_MAIN_DOC',          FARAZSMS_WEB_MAIN . 'farazsms-wordpress-plugin/');

require FARAZSMS_CLASSES_PATH . 'class-farazsms-loader.php';
