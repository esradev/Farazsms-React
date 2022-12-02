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
 * Domain Path: languages
 * 
 * WC requires at least: 3.0
 * WC tested up to: 7.1
 *
 */

/**
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

defined('ABSPATH') || exit; // Exit if accessed directly

/**
 * Set defines.
 */

define('FARAZSMS_VER',                   '3.11.3');
define('FARAZSMS_PLUGIN_NAME',           plugin_basename(__FILE__));
define('FARAZSMS_SLUG',                  'farazsms_settings');
define('FARAZSMS_SETTINGS_LINK',         admin_url('admin.php?page=' . FARAZSMS_SLUG));

define('FARAZSMS_WEB_MAIN',              'https://farazsms.com/');
define('FARAZSMS_WEB_MAIN_DOC',          'https://farazsms.com/farazsms-wordpress-plugin/');
define('FARAZSMS_WEB_API',               FARAZSMS_WEB_MAIN . 'api/wp-faraz/');
define('FARAZSMS_WEB_CHECK',             FARAZSMS_WEB_MAIN . 'check_update.php');
define('FARAZSMS_WEB_VALID',             FARAZSMS_WEB_MAIN . 'valid_key.php');
define('FARAZSMS_WEB_INFO',              FARAZSMS_WEB_MAIN . 'plugin_information.php');
define('FARAZSMS_FILE',                  __FILE__);
define('FARAZSMS_PATH',                  plugin_dir_path(FARAZSMS_FILE));
define('FARAZSMS_INC_PATH',              FARAZSMS_PATH . 'inc/');

define('FARAZSMS_DEPRECATED_PATH',       realpath(FARAZSMS_INC_PATH . 'deprecated/') . '/');
define('FARAZSMS_FRONT_PATH',            realpath(FARAZSMS_INC_PATH . 'front/') . '/');
define('FARAZSMS_ADMIN_PATH',            realpath(FARAZSMS_INC_PATH . 'admin') . '/');
define('FARAZSMS_ADMIN_UI_PATH',         realpath(FARAZSMS_ADMIN_PATH . 'ui') . '/');
define('FARAZSMS_ADMIN_UI_MODULES_PATH', realpath(FARAZSMS_ADMIN_UI_PATH . 'modules') . '/');
define('FARAZSMS_COMMON_PATH',           realpath(FARAZSMS_INC_PATH . 'common') . '/');
define('FARAZSMS_FUNCTIONS_PATH',        realpath(FARAZSMS_INC_PATH . 'functions') . '/');
define('FARAZSMS_VENDORS_PATH',          realpath(FARAZSMS_INC_PATH . 'vendors') . '/');
define('FARAZSMS_3RD_PARTY_PATH',        realpath(FARAZSMS_INC_PATH . '3rd-party') . '/');

define('FARAZSMS_URL',                   plugin_dir_url(FARAZSMS_FILE));
define('FARAZSMS_INC_URL',               FARAZSMS_URL . 'inc/');
define('FARAZSMS_ADMIN_URL',             FARAZSMS_INC_URL . 'admin/');
define('FARAZSMS_ASSETS_URL',            FARAZSMS_URL . 'assets/');
define('FARAZSMS_ASSETS_JS_URL',         FARAZSMS_ASSETS_URL . 'js/');
define('FARAZSMS_ASSETS_CSS_URL',        FARAZSMS_ASSETS_URL . 'css/');
define('FARAZSMS_ASSETS_IMG_URL',        FARAZSMS_ASSETS_URL . 'img/');

/**
 * Loads WP faraz translations
 *
 * @since 3.0
 * @author Wpstorm
 *
 * @return void
 */
function farazsms_load_textdomain()
{
    // Load translations from the languages directory.
    $locale = get_locale();

    // This filter is documented in /wp-includes/l10n.php.
    $locale = apply_filters('plugin_locale', $locale, 'farazsms'); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
    load_textdomain('farazsms', WP_LANG_DIR . '/plugins/wp-farazsms-' . $locale . '.mo');

    load_plugin_textdomain('farazsms', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'farazsms_load_textdomain');


require FARAZSMS_INC_PATH . 'farazsms-loader.php';
