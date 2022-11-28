<?php

/**
 * Farazsms Loader.
 *
 * @package Farazsms
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('Farazsms_Loader')) {

    /**
     * Class Farazsms_Loader.
     */
    final class Farazsms_Loader
    {

        /**
         * Member Variable
         *
         * @var instance
         */
        private static $instance = null;

        /**
         *  Initiator
         */
        public static function get_instance()
        {

            if (is_null(self::$instance)) {

                self::$instance = new self();

                /**
                 * Farazsms loaded.
                 *
                 * Fires when Farazsms was fully loaded and instantiated.
                 *
                 * @since 1.0.0
                 */
                do_action('farazsms_loaded');
            }

            return self::$instance;
        }

        /**
         * Constructor
         */
        public function __construct()
        {

            $this->define_constants();

            // Activation hook.
            register_activation_hook(FARAZSMS_FILE, array($this, 'activation_reset'));

            // deActivation hook.
            register_deactivation_hook(FARAZSMS_FILE, array($this, 'deactivation_reset'));

            add_action('plugins_loaded', array($this, 'load_plugin'), 99);

            /* add_action('plugins_loaded', array($this, 'load_faraz_textdomain')); */
        }

        /**
         * Defines all constants
         *
         * @since 1.0.0
         */
        public function define_constants()
        {
            define('FARAZSMS_BASE', plugin_basename(FARAZSMS_FILE));
            define('FARAZSMS_DIR', plugin_dir_path(FARAZSMS_FILE));
            define('FARAZSMS_URL', plugins_url('/', FARAZSMS_FILE));
            define('FARAZSMS_VER', '2.0.0');

            define('FARAZSMS_SLUG', 'farazsms');
        }

        /**
         * Loads plugin files.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function load_plugin()
        {
            $this->load_core_files();
            /**
             * Farazsms Init.
             *
             * Fires when Farazsms is instantiated.
             *
             * @since 2.0.0
             */
            do_action('farazsms_init');
        }

        /**
         * Load core files.
         */
        public function load_core_files()
        {
            /* Load Farazsms Sttings. */

            include_once FARAZSMS_DIR . 'inc/farazsms-settings.php';
        }

        /**
         * Activation Reset
         */
        public function activation_reset()
        {
            $this->create_default_tabls();
        }

        /**
         *  Set the default Farazsms newsletter Table.
         */
        public function create_default_tabls()
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

        /**
         * Deactivation Reset
         */
        public function deactivation_reset()
        {
        }
    }

    /**
     *  Prepare if class 'Farazsms_Loader' exist.
     *  Kicking this off by calling 'get_instance()' method
     */
    Farazsms_Loader::get_instance();
}


if (!function_exists('farazsms')) {
    /**
     * Get global class.
     *
     * @return object
     */
    function farazsms()
    {
        return Farazsms_Loader::get_instance();
    }
}
