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

define('FARAZSMS_VER',                   '3.11.3');
define('FARAZSMS_FILE',                  __FILE__);
define('FARAZSMS_PATH',                  plugin_dir_path(FARAZSMS_FILE));
define('FARAZSMS_BASE',                  plugin_basename(FARAZSMS_FILE));
define('FARAZSMS_SLUG',                  'farazsms_settings');
define('FARAZSMS_SETTINGS_LINK',         admin_url('admin.php?page=' . FARAZSMS_SLUG));
define('FARAZSMS_INC_PATH',              FARAZSMS_PATH . 'inc/');
define('FARAZSMS_URL',                   plugins_url('/', FARAZSMS_FILE));
define('FARAZSMS_INC_URL',               FARAZSMS_URL . 'inc/');
define('FARAZSMS_ADMIN_URL',             FARAZSMS_INC_URL . 'admin/');
define('FARAZSMS_WEB_MAIN',              'https://farazsms.com/');
define('FARAZSMS_WEB_MAIN_DOC',          FARAZSMS_WEB_MAIN . 'farazsms-wordpress-plugin/');

require FARAZSMS_INC_PATH . 'farazsms-loader.php';
