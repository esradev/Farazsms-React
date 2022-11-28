<?php

/**
 * Plugin Name: Farazsms
 * Plugin URI: https://farazsms.com/
 * Description: By using the Farazsms plugin, you can professionally equip your site with a powerful SMS tool for information and marketing. Saving customers numbers in the phone book, sending welcome SMS, sending reply SMS to comments, etc. are part of the features of this powerful SMS plugin.
 * Version: 2.0.0
 * Author: Farazsms
 * Author URI: https://farazsms.com/
 * Text Domain: farazsms
 * WC requires at least: 3.0
 * WC tested up to: 7.1.0
 *
 * @package Farazsms
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Set constants.
 */
define('FARAZSMS_FILE', __FILE__);

/**
 * Loader
 */
require_once 'inc/farazsms-loader.php';
