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
            add_action('init', array($this, 'register_newsletters_options'));
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
                'apikey' => $data['apikey'],
                'username' => $data['username'] ? $data['username'] : '',
                'password' => $data['password'] ? $data['password'] : '',
                'admin_number' => $data['admin_number'] ? $data['admin_number'] : '',
                'from_number' => $data['from_number'] ? $data['from_number'] : '3000505',
                'from_number_adver' => $data['from_number_adver'] ? $data['from_number_adver'] : '',
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_settings_options', $option_json);
            return $result;
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
                'welcome_sms' => $data['welcome_sms'] ? $data['welcome_sms'] : false,
                'welcome_sms_use_p' => $data['welcome_sms_use_p'] ? $data['welcome_sms_use_p'] : false,
                'welcome_sms_p' => $data['welcome_sms_p'] ? $data['welcome_sms_p'] : '',
                'welcome_sms_message' => $data['welcome_sms_message'] ? $data['welcome_sms_message'] : '',
                'admin_login_notify' => $data['admin_login_notify'] ? $data['admin_login_notify'] : '',
                'admin_login_notify_p' => $data['admin_login_notify_p'] ? $data['admin_login_notify_p'] : '',
                'select_roles' => $data['select_roles'] ? $data['select_roles'] : '',
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_login_notify_options', $option_json);
            return $result;
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
         * Get comments options.
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
         * Register newsletters options.
         *
         * @since 2.0.0
         */
        public function register_newsletters_options()
        {

            $farazsms_newsletters_options = [
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => array(),
                    ],
                ],
            ];

            add_option('farazsms_newsletters_options', $farazsms_newsletters_options);
        }

        /**
         * Get newsletters options.
         * 
         * @since 2.0.0
         */
        public function get_newsletters_options()
        {
            $farazsms_newsletters_options = get_option('farazsms_newsletters_options');
            if (empty($farazsms_newsletters_options)) {
                return new WP_Error('no_option', 'Invalid options', array('status' => 404));
            }
            return $farazsms_newsletters_options;
        }

        /**
         * Get newsletters options.
         * 
         * @since 2.0.0
         */
        public function add_newsletters_options($data)
        {
            $option = array(
                'news_phonebooks'                    => $data['news_phonebooks'] ? $data['news_phonebooks'] : [],
                'news_send_verify_code'              => $data['news_send_verify_code'] ? $data['news_send_verify_code'] : false,
                'news_send_verify_code_p'            => $data['news_send_verify_code_p'] ? $data['news_send_verify_code_p'] : false,
                'news_welcome'                       => $data['news_welcome'] ? $data['news_welcome'] : '',
                'news_welcome_p'                     => $data['news_welcome_p'] ? $data['news_welcome_p'] : '',
                'news_post_notification'             => $data['news_post_notification'] ? $data['news_post_notification'] : '',
                'news_post_notification_message'     => $data['news_post_notification_message'] ? $data['news_post_notification_message'] : '',
                'news_product_notification'          => $data['news_product_notification'] ? $data['news_product_notification'] : '',
                'news_product_notification_message'  => $data['news_product_notification_message'] ? $data['news_product_notification_message'] : '',
            );
            $option_json = wp_json_encode($option);
            $result = update_option('farazsms_newsletters_options', $option_json);
            return $result;
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
