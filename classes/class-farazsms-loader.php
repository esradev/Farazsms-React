<?php

/**
 * Farazsms Loader.
 * 
 * @package Farazsms
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly.


if (!class_exists('Farazsms_Loader')) {

    /**
     * Class Farazsms_Loader.
     */
    class Farazsms_Loader
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
            // Activation hook.
            register_activation_hook(FARAZSMS_FILE, array($this, 'activation_reset'));

            // Redirect after plugin is activated.
            add_action('admin_init', array($this, 'farazsms_activation_redirect'));

            // deActivation hook.
            register_deactivation_hook(FARAZSMS_FILE, array($this, 'deactivation_reset'));

            // Load core files of the plugin
            add_action('plugins_loaded', array($this, 'load_plugin'), 99);

            // Load farazsms textdomain.
            add_action('init', array($this, 'load_farazsms_textdomain'));
        }

        /**
         * Loads plugin files.
         *
         * @since 2.0.0
         *
         * @return void
         */
        public function load_plugin()
        {
            $this->load_core_files();
        }

        /**
         * Create new database tables for plugin updates.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function initialize_farazsms_tables()
        {
            include_once FARAZSMS_CLASSES_PATH . 'class-farazsms-database.php';
            $db = Farazsms_Database::get_instance();
            $db->create_tables();
        }

        /**
         * Load core files.
         */
        public function load_core_files()
        {
            // Load Farazsms options.
            include_once FARAZSMS_CLASSES_PATH . 'class-farazsms-options.php';

            // Load Farazsms Routes.
            include_once FARAZSMS_CLASSES_PATH . 'class-farazsms-routes.php';

            // Load Farazsms Sttings.
            include_once FARAZSMS_CLASSES_PATH . 'class-farazsms-settings.php';
        }

        /**
         * Activation Reset
         */
        public function activation_reset()
        {
            $this->initialize_farazsms_tables();
            add_option('farazsms_do_activation_redirect', true);
        }

        /**
         * Redirect user to faraz settings page after plugin activated.
         */
        public function farazsms_activation_redirect($plugin)
        {
            if (get_option('farazsms_do_activation_redirect', false)) {
                delete_option('farazsms_do_activation_redirect');
                exit(wp_redirect(FARAZSMS_SETTINGS_LINK));
            }
        }

        /**
         * Deactivation Reset
         */
        public function deactivation_reset()
        {
        }

        /**
         * Load Farazsms Text Domain.
         * 
         * @since  2.0.0
         * @return void
         */

        public function load_farazsms_textdomain()
        {
            // Default languages directory for Farazsms.
            $lang_dir = dirname(FARAZSMS_BASE) . '/languages';

            //Load Farazsms languages for PHP files.
            load_plugin_textdomain('farazsms', false, $lang_dir);
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
