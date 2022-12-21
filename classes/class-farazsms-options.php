<?php

/**
 * Farazsms Options.
 * 
 * @package Farazsms
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('Farazsms_Options')) {

    /**
     * Class Farazsms_Options.
     */
    class Farazsms_Options
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
                 * Farazsms Options.
                 *
                 * @since 1.0.0
                 */
                do_action('farazsms_options');
            }

            return self::$instance;
        }

        /**
         * Constructor
         */
        public function __construct()
        {
            // Regsiter all farazsms options
            add_action('init', array($this, 'register_settings_options'));
            add_action('init', array($this, 'register_login_notify_options'));
            add_action('init', array($this, 'register_comments_options'));
            add_action('init', array($this, 'register_newsletter_options'));
        }
        /**
         * Register settings options.
         * 
         * @since 2.0.0
         */
        public function register_settings_options()
        {
            $farazsms_settings_options = [
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => array(),
                    ],
                ],
            ];
            add_option('farazsms_settings_options', $farazsms_settings_options);
        }

        /**
         * Register login notify options
         *
         * @since 2.0.0
         */
        public function register_login_notify_options()
        {

            $farazsms_login_notify_options = [
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => array(),
                    ],
                ],
            ];

            add_option('farazsms_login_notify_options', $farazsms_login_notify_options);
        }

        /**
         * Register comments options.
         *
         * @since 2.0.0
         */
        public function register_comments_options()
        {
            $farazsms_comments_options = [
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => array(),
                    ],
                ],
            ];

            add_option('farazsms_comments_options', $farazsms_comments_options);
        }

        /**
         * Register newsletter options.
         *
         * @since 2.0.0
         */
        public function register_newsletter_options()
        {
            $farazsms_newsletter_options = [
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => array(),
                    ],
                ],
            ];

            add_option('farazsms_newsletter_options', $farazsms_newsletter_options);
        }
    }
    /**
     *  Prepare if class 'Farazsms_Options' exist.
     *  Kicking this off by calling 'get_instance()' method
     */
    Farazsms_Options::get_instance();
}


if (!function_exists('farazsms_options')) {
    /**
     * Get global class.
     *
     * @return object
     */
    function farazsms_options()
    {
        return Farazsms_Options::get_instance();
    }
}
