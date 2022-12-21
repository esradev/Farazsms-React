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
                 * Farazsms Routes.
                 *
                 * @since 1.0.0
                 */
                do_action('farazsms_routes');
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
                    'permission_callback' => array($this, 'permissions_check'),
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_settings_options'),
                    'permission_callback' => array($this, 'permissions_check'),
                ),
            ));

            //Register login_notify_options rest route
            register_rest_route($namespace, '/' . 'login_notify_options', array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_login_notify_options'),
                    'permission_callback' => array($this, 'permissions_check'),

                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_login_notify_options'),
                    'permission_callback' => array($this, 'permissions_check'),
                ),
            ));

            //Register comments_options rest route
            register_rest_route($namespace, '/' . 'comments_options', array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_comments_options'),
                    'permission_callback' => array($this, 'permissions_check'),
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_comments_options'),
                    'permission_callback' => array($this, 'permissions_check'),
                ),
            ));

            //Register newsletter_options rest route
            register_rest_route($namespace, '/' . 'newsletter_options', array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_newsletter_options'),
                    'permission_callback' => array($this, 'permissions_check'),
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'add_newsletter_options'),
                    'permission_callback' => array($this, 'permissions_check'),
                ),
            ));
        }

        /**
         * Get settings options.
         * 
         * @since 2.0.0
         */
        public function get_settings_options()
        {
            $farazsms_settings_options = get_option('farazsms_settings_options');
            if (empty($farazsms_settings_options)) {
                return new WP_Error('no_option', 'Invalid options', array('status' => 404));
            }
            return $farazsms_settings_options;
        }

        /**
         * Add settings options.
         *
         * @since 2.0.0
         */
        public function add_settings_options($data)
        {
            $option = array(
                'apikey'            => $data['apikey'],
                'username'          => $data['username'] ? $data['username'] : '',
                'password'          => $data['password'] ? $data['password'] : '',
                'admin_number'      => $data['admin_number'] ? $data['admin_number'] : '',
                'from_number'       => $data['from_number'] ? $data['from_number'] : '3000505',
                'from_number_adver' => $data['from_number_adver'] ? $data['from_number_adver'] : '',
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_settings_options', $option_json);
            return $result;
        }

        /**
         * Get login notify options.
         * 
         * @since 2.0.0
         */
        public function get_login_notify_options()
        {
            $farazsms_login_notify_options = get_option('farazsms_login_notify_options');
            if (empty($farazsms_login_notify_options)) {
                return new WP_Error('no_option', 'Invalid options', array('status' => 404));
            }
            return $farazsms_login_notify_options;
        }

        /**
         * Add login notify options.
         * 
         * @since 2.0.0
         */
        public function add_login_notify_options($data)
        {
            $option = array(
                'welcome_sms'          => $data['welcome_sms'] ? $data['welcome_sms'] : false,
                'welcome_sms_use_p'    => $data['welcome_sms_use_p'] ? $data['welcome_sms_use_p'] : false,
                'welcome_sms_p'        => $data['welcome_sms_p'] ? $data['welcome_sms_p'] : '',
                'welcome_sms_msg'      => $data['welcome_sms_msg'] ? $data['welcome_sms_msg'] : '',
                'admin_login_notify'   => $data['admin_login_notify'] ? $data['admin_login_notify'] : '',
                'admin_login_notify_p' => $data['admin_login_notify_p'] ? $data['admin_login_notify_p'] : '',
                'select_roles'         => $data['select_roles'] ? $data['select_roles'] : '',
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_login_notify_options', $option_json);
            return $result;
        }

        /**
         * Get comments options.
         * 
         * @since 2.0.0
         */
        public function get_comments_options()
        {
            $farazsms_comments_options = get_option('farazsms_comments_options');
            if (empty($farazsms_comments_options)) {
                return new WP_Error('no_option', 'Invalid options', array('status' => 404));
            }
            return $farazsms_comments_options;
        }

        /**
         * Add comments options.
         * 
         * @since 2.0.0
         */
        public function add_comments_options($data)
        {
            $option = array(
                'add_mobile_field'            => $data['add_mobile_field'] ? $data['add_mobile_field'] : '',
                'required_mobile_field'       => $data['required_mobile_field'] ? $data['required_mobile_field'] : false,
                'notify_admin_for_comment'    => $data['notify_admin_for_comment'] ? $data['notify_admin_for_comment'] : false,
                'approved_comment_p'          => $data['approved_comment_p'] ? $data['approved_comment_p'] : '',
                'comment_p'                   => $data['comment_p'] ? $data['comment_p'] : '',
                'notify_admin_for_comment_p'  => $data['notify_admin_for_comment_p'] ? $data['notify_admin_for_comment_p'] : '',
                'comment_phone_book'          => $data['comment_phone_book'] ? $data['comment_phone_book'] : [],
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_comments_options', $option_json);
            return $result;
        }


        /**
         * Get newsletter options.
         * 
         * @since 2.0.0
         */
        public function get_newsletter_options()
        {
            $farazsms_newsletter_options = get_option('farazsms_newsletter_options');
            if (empty($farazsms_newsletter_options)) {
                return new WP_Error('no_option', 'Invalid options', array('status' => 404));
            }
            return $farazsms_newsletter_options;
        }

        /**
         * Add newsletter options.
         * 
         * @since 2.0.0
         */
        public function add_newsletter_options($data)
        {
            $option = array(
                'news_phonebooks'         => $data['news_phonebooks'] ? $data['news_phonebooks'] : [],
                'news_send_verify_code'   => $data['news_send_verify_code'] ? $data['news_send_verify_code'] : false,
                'news_send_verify_code_p' => $data['news_send_verify_code_p'] ? $data['news_send_verify_code_p'] : false,
                'news_welcome'            => $data['news_welcome'] ? $data['news_welcome'] : '',
                'news_welcome_p'          => $data['news_welcome_p'] ? $data['news_welcome_p'] : '',
                'news_post_notify'        => $data['news_post_notify'] ? $data['news_post_notify'] : '',
                'news_post_notify_msg'    => $data['news_post_notify_msg'] ? $data['news_post_notify_msg'] : '',
                'news_product_notify'     => $data['news_product_notify'] ? $data['news_product_notify'] : '',
                'news_product_notify_msg' => $data['news_product_notify_msg'] ? $data['news_product_notify_msg'] : '',
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_newsletter_options', $option_json);
            return $result;
        }

        /**
         * Check if a given request has access to options
         *
         * @return WP_Error|bool
         */
        public function permissions_check($request)
        {
            //return true; <--use to make readable by all
            return true;
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
