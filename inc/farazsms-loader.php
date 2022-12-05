<?php

/**
 * Farazsms Loader.
 * 
 * @package Farazsms
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly.


if (!class_exists('FARAZSMS_Loader')) {

    /**
     * Class FARAZSMS_Loader.
     */
    final class FARAZSMS_Loader
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

            // load faraz textdomain..
            add_action('plugins_loaded', array($this, 'load_farazsms_textdomain'));
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
            include_once FARAZSMS_PATH . 'modules/farazsms/classes/class-farazsms-database.php';
            $db = Farazsms_Database::get_instance();
            $db->create_tables();
        }

        /**
         * Load core files.
         */
        public function load_core_files()
        {
            // Load Farazsms Options.
            include_once FARAZSMS_INC_PATH . 'farazsms-options.php';

            // Load Farazsms Sttings.
            include_once FARAZSMS_INC_PATH . 'farazsms-settings.php';
        }

        /**
         * Activation Reset
         */
        public function activation_reset()
        {
            $this->define_default_options();
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
         * This will load the translation textdomain depending on the file priorities.
         *      1. Global Languages /wp-content/languages/%plugin-folder-name%/ folder
         *      2. Local dorectory /wp-content/plugins/%plugin-folder-name%/languages/ folder
         *
         * @since  2.0.0
         * @return void
         */
        public function load_farazsms_textdomain()
        {

            // Default languages directory for Farazsms.
            $lang_dir = FARAZSMS_PATH . 'languages/';

            /**
             * Filters the languages directory path to use for Farazsms.
             *
             * @param string $lang_dir The languages directory path.
             */
            $lang_dir = apply_filters('farazsms_languages_directory', $lang_dir);

            // Traditional WordPress plugin locale filter.
            global $wp_version;

            $get_locale = get_locale();

            if ($wp_version >= 4.7) {
                $get_locale = get_user_locale();
            }

            /**
             * Language Locale for Farazsms
             *
             * @var $get_locale The locale to use.
             * Uses get_user_locale()` in WordPress 4.7 or greater,
             * otherwise uses `get_locale()`.
             */
            $locale = apply_filters('plugin_locale', $get_locale, 'farazsms');
            $mofile = sprintf('%1$s-%2$s.mo', 'farazsms', $locale);

            // Setup paths to current locale file.
            $mofile_local  = $lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

            if (file_exists($mofile_global)) {
                // Look in global /wp-content/languages/%plugin-folder-name%/ folder.
                load_textdomain('farazsms', $mofile_global);
            } elseif (file_exists($mofile_local)) {
                // Look in local /wp-content/plugins/%plugin-folder-name%/languages/ folder.
                load_textdomain('farazsms', $mofile_local);
            } else {
                // Load the default language files.
                load_plugin_textdomain('farazsms', false, $lang_dir);
            }
        }
    }

    /**
     *  Prepare if class 'Farazsms_Loader' exist.
     *  Kicking this off by calling 'get_instance()' method
     */
    FARAZSMS_Loader::get_instance();
}


if (!function_exists('farazsms')) {
    /**
     * Get global class.
     *
     * @return object
     */
    function farazsms()
    {
        return FARAZSMS_Loader::get_instance();
    }
}
