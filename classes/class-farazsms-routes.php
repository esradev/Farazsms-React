<?php

/**
 * Farazsms Routes.
 * 
 * @package Farazsms
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly.

if (!class_exists('Farazsms_Routes')) {
    /**
     * Class Farazsms_Routes.
     */
    class Farazsms_Routes
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
            // Regsiter farazsms api
            add_action('rest_api_init', array($this, 'register_routes'));
        }

        /**
         * Register the routes for the objects of the controller.
         */
        public function register_routes()
        {
            $version = '1';
            $namespace = 'farazsms/v' . $version;

            //Register settings_options rest route
            register_rest_route($namespace, '/' . 'settings_options', array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_settings_options'),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_settings_options'),
                    'permission_callback' => '__return_true',
                ),
            ));

            //Register login_notify_options rest route
            register_rest_route($namespace, '/' . 'login_notify_options', array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_login_notify_options'),
                    'permission_callback' => '__return_true',

                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_login_notify_options'),
                    'permission_callback' => '__return_true',

                ),
            ));

            //Register comments_options rest route
            register_rest_route($namespace, '/' . 'comments_options', array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_comments_options'),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_comments_options'),
                    'permission_callback' => '__return_true',
                ),
            ));
        }
    }
    /**
     *  Prepare if class 'Farazsms_Routes' exist.
     *  Kicking this off by calling 'get_instance()' method
     */
    Farazsms_Routes::get_instance();
}

if (!function_exists('farazsms_routes')) {
    /**
     * Get global class.
     *
     * @return object
     */
    function farazsms_routes()
    {
        return Farazsms_Routes::get_instance();
    }
}
