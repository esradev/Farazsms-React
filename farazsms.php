<?php

/**
 * @wordpress-plugin
 * Plugin Name: FarazSMS
 * Plugin URI: https://farazsms.com/
 * Description: By using the Farazsms plugin, you can professionally equip your site with a powerful SMS tool for information and marketing. Saving customers numbers in the phone book, sending welcome SMS, sending reply SMS to comments, etc. are part of the features of this powerful SMS plugin.
 * Version: 2.0.0
 * Requires at least: 5.5
 * Requires PHP: 7.1
 * Author: FarazSMS
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

define('FARAZSMS_VERSION',               '2.0.0');
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

function activate_farazsms()
{
    require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-activator.php';
    Farazsms_Activator::activate();
}

register_activation_hook(__FILE__, 'activate_farazsms');

function farazsms_activation_redirect($plugin)
{
    if (get_option('farazsms_do_activation_redirect', false)) {
        delete_option('farazsms_do_activation_redirect');
        exit(wp_redirect(FARAZSMS_SETTINGS_LINK));
    }
}
add_action('activated_plugin', 'farazsms_activation_redirect');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require FARAZSMS_CLASSES_PATH . 'class-farazsms.php';

