<?php

/**
 * Farazsms Loader.
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly.


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
            /* Load Farazsms Options. */
            include_once FARAZSMS_INC_PATH . 'farazsms-options.php';

            /* Load Farazsms Sttings. */
            include_once FARAZSMS_INC_PATH . 'farazsms-settings.php';
        }

        /**
         * Activation Reset
         */
        public function activation_reset()
        {
            $this->create_default_tabls();
            add_option('farazsms_do_activation_redirect', true);
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
         * Redirect user to faraz settings page after plugin activated.
         */
        public function farazsms_activation_redirect($plugin)
        {
            if (get_option('farazsms_do_activation_redirect', false)) {
                delete_option('farazsms_do_activation_redirect');
                exit(wp_redirect(admin_url('admin.php?page=farazsms-plugin-react')));
            }
        }



        /**
         * Deactivation Reset
         */
        public function deactivation_reset()
        {
        }

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
