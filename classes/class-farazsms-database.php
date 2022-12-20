<?php

/**
 * Farazsms DB
 *
 * @package Farazsms
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Farazsms DB class.
 */
class Farazsms_Database
{

    /**
     * Member Variable
     *
     * @var object instance
     */
    private static $instance;

    /**
     *  Initiator
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *  Create tables
     */
    public function create_tables()
    {
        $this->create_newsletter_table();
    }

    /**
     *  Create newsletter table.
     */
    public function create_newsletter_table()
    {
        global $wpdb;
        $collate = $wpdb->get_charset_collate();
        $newsletter_table_name = $wpdb->prefix . 'farazsms_newsletter';
        $verify_code_table_name = $wpdb->prefix . 'farazsms_vcode';
        $query   = "CREATE TABLE IF NOT EXISTS `$newsletter_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `name` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `phone_book` int(10) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";
        $query2   = "CREATE TABLE IF NOT EXISTS `$verify_code_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `code` int(4) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
        dbDelta($query2);
    }
}

Farazsms_Database::get_instance();
