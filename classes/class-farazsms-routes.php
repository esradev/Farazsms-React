<?php

class Farazsms_Routes
{
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

        //Register phonebook_options rest route
        register_rest_route($namespace, '/' . 'phonebook_options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_phonebook_options'),
                'permission_callback' => array($this, 'permissions_check'),

            ),
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_phonebook_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
        ));

        //Register usermeta rest route
        register_rest_route($namespace, '/' . 'usermeta', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_usermeta'),
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

        //Register woocommerce_options rest route
        register_rest_route($namespace, '/' . 'woocommerce_options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_woocommerce_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_woocommerce_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
        ));

        //Register edd_options rest route
        register_rest_route($namespace, '/' . 'edd_options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_edd_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_edd_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
        ));

        //Register aff_options rest route
        register_rest_route($namespace, '/' . 'aff_options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_aff_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_aff_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
        ));

        //Register membership_options rest route
        register_rest_route($namespace, '/' . 'membership_options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_membership_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_membership_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
        ));

        //Register integrations_options rest route
        register_rest_route($namespace, '/' . 'integrations_options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_integrations_options'),
                'permission_callback' => array($this, 'permissions_check'),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'add_integrations_options'),
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
            'welcome_sms'                => $data['welcome_sms'] ? $data['welcome_sms'] : '',
            'welcome_sms_use_pattern'    => $data['welcome_sms_use_pattern'] ? $data['welcome_sms_use_pattern'] : '',
            'welcome_sms_pattern'        => $data['welcome_sms_pattern'] ? $data['welcome_sms_pattern'] : '',
            'welcome_sms_msg'            => $data['welcome_sms_msg'] ? $data['welcome_sms_msg'] : '',
            'admin_login_notify'         => $data['admin_login_notify'] ? $data['admin_login_notify'] : '',
            'admin_login_notify_pattern' => $data['admin_login_notify_pattern'] ? $data['admin_login_notify_pattern'] : '',
            'select_roles'               => $data['select_roles'] ? $data['select_roles'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_login_notify_options', $option_json);
        return $result;
    }

    /**
     * Get phonebook options.
     * 
     * @since 2.0.0
     */
    public function get_phonebook_options()
    {
        $farazsms_phonebook_options = get_option('farazsms_phonebook_options');
        if (empty($farazsms_phonebook_options)) {
            return new WP_Error('no_option', 'Invalid options', array('status' => 404));
        }
        return $farazsms_phonebook_options;
    }

    /**
     * Add login notify options.
     * 
     * @since 2.0.0
     */
    public function add_phonebook_options($data)
    {
        $option = array(
            'custom_phonebook'          => $data['custom_phonebook'] ? $data['custom_phonebook'] : [],
            'custom_phone_meta_keys'    => $data['custom_phone_meta_keys'] ? $data['custom_phone_meta_keys'] : [],
            'digits_phonebook'          => $data['digits_phonebook'] ? $data['digits_phonebook'] : [],
            'woo_phonebook'             => $data['woo_phonebook'] ? $data['woo_phonebook'] : [],
            'bookly_phonebook'          => $data['bookly_phonebook'] ? $data['bookly_phonebook'] : [],
            'gf_phonebook'              => $data['gf_phonebook'] ? $data['gf_phonebook'] : [],
            'gf_selected_field'         => $data['gf_selected_field'] ? $data['gf_selected_field'] : [],
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_phonebook_options', $option_json);
        return $result;
    }

    /**
     * Get usermeta.
     * 
     * @since 2.0.0
     */
    public function get_usermeta()
    {
        global $wpdb;
        $farazsms_usermeta_keys = $wpdb->get_results("SELECT DISTINCT meta_key FROM `" . $wpdb->prefix . "usermeta`");
        return $farazsms_usermeta_keys;
    }

    /**
     * Get comments.
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
            'add_mobile_field'                  => $data['add_mobile_field'] ? $data['add_mobile_field'] : '',
            'required_mobile_field'             => $data['required_mobile_field'] ? $data['required_mobile_field'] : '',
            'notify_admin_for_comment'          => $data['notify_admin_for_comment'] ? $data['notify_admin_for_comment'] : '',
            'notify_admin_for_comment_pattern'  => $data['notify_admin_for_comment_pattern'] ? $data['notify_admin_for_comment_pattern'] : '',
            'comment_pattern'                   => $data['comment_pattern'] ? $data['comment_pattern'] : '',
            'approved_comment_pattern'          => $data['approved_comment_pattern'] ? $data['approved_comment_pattern'] : '',
            'comment_phonebook'                 => $data['comment_phonebook'] ? $data['comment_phonebook'] : [],
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
            'news_phonebook'               => $data['news_phonebook'] ? $data['news_phonebook'] : [],
            'news_send_verify_via_pattern' => $data['news_send_verify_via_pattern'] ? $data['news_send_verify_via_pattern'] : '',
            'news_send_verify_pattern'     => $data['news_send_verify_pattern'] ? $data['news_send_verify_pattern'] : '',
            'news_welcome'                 => $data['news_welcome'] ? $data['news_welcome'] : '',
            'news_welcome_pattern'         => $data['news_welcome_pattern'] ? $data['news_welcome_pattern'] : '',
            'news_post_notify'             => $data['news_post_notify'] ? $data['news_post_notify'] : '',
            'news_post_notify_msg'         => $data['news_post_notify_msg'] ? $data['news_post_notify_msg'] : '',
            'news_product_notify'          => $data['news_product_notify'] ? $data['news_product_notify'] : '',
            'news_product_notify_msg'      => $data['news_product_notify_msg'] ? $data['news_product_notify_msg'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_newsletter_options', $option_json);
        return $result;
    }

    /**
     * Get woocommerce options.
     * 
     * @since 2.0.0
     */
    public function get_woocommerce_options()
    {
        $farazsms_woocommerce_options = get_option('farazsms_woocommerce_options');
        if (empty($farazsms_woocommerce_options)) {
            return new WP_Error('no_option', 'Invalid options', array('status' => 404));
        }
        return $farazsms_woocommerce_options;
    }

    /**
     * Add woocommerce options.
     * 
     * @since 2.0.0
     */
    public function add_woocommerce_options($data)
    {
        $option = array(
            'woo_checkout_otp'                 => $data['woo_checkout_otp'] ? $data['woo_checkout_otp'] : '',
            'woo_checkout_otp_pattern'         => $data['woo_checkout_otp_pattern'] ? $data['woo_checkout_otp_pattern'] : '',
            'woo_poll'                         => $data['woo_poll'] ? $data['woo_poll'] : '',
            'woo_poll_time'                    => $data['woo_poll_time'] ? $data['woo_poll_time'] : '',
            'woo_poll_msg'                     => $data['woo_poll_msg'] ? $data['woo_poll_msg'] : '',
            'woo_tracking_pattern'             => $data['woo_tracking_pattern'] ? $data['woo_tracking_pattern'] : '',
            'woo_retention_order_no'           => $data['woo_retention_order_no'] ? $data['woo_retention_order_no'] : '',
            'woo_retention_order_month'        => $data['woo_retention_order_month'] ? $data['woo_retention_order_month'] : '',
            'woo_retention_msg'                => $data['woo_retention_msg'] ? $data['woo_retention_msg'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_woocommerce_options', $option_json);
        return $result;
    }

    /**
     * Get Edd options.
     * 
     * @since 2.0.0
     */
    public function get_edd_options()
    {
        $farazsms_edd_options = get_option('farazsms_edd_options');
        if (empty($farazsms_edd_options)) {
            return new WP_Error('no_option', 'Invalid options', array('status' => 404));
        }
        return $farazsms_edd_options;
    }

    /**
     * Add Edd options.
     * 
     * @since 2.0.0
     */
    public function add_edd_options($data)
    {
        $option = array(
            'edd_phonebook'            => $data['edd_phonebook'] ? $data['edd_phonebook'] : [],
            'edd_send_to_user'         => $data['edd_send_to_user'] ? $data['edd_send_to_user'] : '',
            'edd_user_pattern'         => $data['edd_user_pattern'] ? $data['edd_user_pattern'] : '',
            'edd_send_to_admin'        => $data['edd_send_to_admin'] ? $data['edd_send_to_admin'] : '',
            'edd_admin_pattern'        => $data['edd_admin_pattern'] ? $data['edd_admin_pattern'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_edd_options', $option_json);
        return $result;
    }
    /**
     * Get aff options.
     * 
     * @since 2.0.0
     */
    public function get_aff_options()
    {
        $farazsms_aff_options = get_option('farazsms_aff_options');
        if (empty($farazsms_aff_options)) {
            return new WP_Error('no_option', 'Invalid options', array('status' => 404));
        }
        return $farazsms_aff_options;
    }

    /**
     * Add aff options.
     * 
     * @since 2.0.0
     */
    public function add_aff_options($data)
    {
        $option = array(
            'aff_user_mobile_field'               => $data['aff_user_mobile_field'] ? $data['aff_user_mobile_field'] : '',
            'aff_user_register'                   => $data['aff_user_register'] ? $data['aff_user_register'] : '',
            'aff_user_register_pattern'           => $data['aff_user_register_pattern'] ? $data['aff_user_register_pattern'] : '',
            'aff_user_new_ref'                    => $data['aff_user_new_ref'] ? $data['aff_user_new_ref'] : '',
            'aff_user_new_ref_pattern'            => $data['aff_user_new_ref_pattern'] ? $data['aff_user_new_ref_pattern'] : '',
            'aff_user_on_approval'                => $data['aff_user_on_approval'] ? $data['aff_user_on_approval'] : '',
            'aff_user_on_approval_pattern'        => $data['aff_user_on_approval_pattern'] ? $data['aff_user_on_approval_pattern'] : '',
            'aff_admin_user_register'             => $data['aff_admin_user_register'] ? $data['aff_admin_user_register'] : '',
            'aff_admin_user_register_pattern'     => $data['aff_admin_user_register_pattern'] ? $data['aff_admin_user_register_pattern'] : '',
            'aff_admin_user_new_ref'              => $data['aff_admin_user_new_ref'] ? $data['aff_admin_user_new_ref'] : '',
            'aff_admin_user_new_ref_pattern'      => $data['aff_admin_user_new_ref_pattern'] ? $data['aff_admin_user_new_ref_pattern'] : '',
            'aff_admin_user_on_approval'          => $data['aff_admin_user_on_approval'] ? $data['aff_admin_user_on_approval'] : '',
            'aff_admin_user_on_approval_pattern'  => $data['aff_admin_user_on_approval_pattern'] ? $data['aff_admin_user_on_approval_pattern'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_aff_options', $option_json);
        return $result;
    }

    /**
     * Get membership options.
     * 
     * @since 2.0.0
     */
    public function get_membership_options()
    {
        $farazsms_membership_options = get_option('farazsms_membership_options');
        if (empty($farazsms_membership_options)) {
            return new WP_Error('no_option', 'Invalid options', array('status' => 404));
        }
        return $farazsms_membership_options;
    }

    /**
     * Add membership options.
     * 
     * @since 2.0.0
     */
    public function add_membership_options($data)
    {
        $option = array(
            'ihc_send_first_notify'     => $data['ihc_send_first_notify'] ? $data['ihc_send_first_notify'] : '',
            'ihc_send_second_notify'    => $data['ihc_send_second_notify'] ? $data['ihc_send_second_notify'] : '',
            'ihc_send_third_notify'     => $data['ihc_send_third_notify'] ? $data['ihc_send_third_notify'] : '',
            'ihc_first_notify_msg'      => $data['ihc_first_notify_msg'] ? $data['ihc_first_notify_msg'] : '',
            'ihc_notify_before_time'    => $data['ihc_notify_before_time'] ? $data['ihc_notify_before_time'] : '5',
            'pmp_send_expire_notify'    => $data['pmp_send_expire_notify'] ? $data['pmp_send_expire_notify'] : '',
            'pmp_expire_notify_msg'     => $data['pmp_expire_notify_msg'] ? $data['pmp_expire_notify_msg'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_membership_options', $option_json);
        return $result;
    }

    /**
     * Get integrations options.
     * 
     * @since 2.0.0
     */
    public function get_integrations_options()
    {
        $farazsms_integrations_options = get_option('farazsms_integrations_options');
        if (empty($farazsms_integrations_options)) {
            return new WP_Error('no_option', 'Invalid options', array('status' => 404));
        }
        return $farazsms_integrations_options;
    }

    /**
     * Add integrations options.
     * 
     * @since 2.0.0
     */
    public function add_integrations_options($data)
    {
        $option = array(
            'woocommerce'                  => $data['woocommerce'] ? $data['woocommerce'] : '',
            'elementorPro'                 => $data['elementorPro'] ? $data['elementorPro'] : '',
            'digits'                       => $data['digits'] ? $data['digits'] : '',
            'edd'                          => $data['edd'] ? $data['edd'] : '',
            'bookly'                       => $data['bookly'] ? $data['bookly'] : '',
            'gravityForms'                 => $data['gravityForms'] ? $data['gravityForms'] : '',
            'indeedMembershipPro'          => $data['indeedMembershipPro'] ? $data['indeedMembershipPro'] : '',
            'paidMembershipsPro'           => $data['paidMembershipsPro'] ? $data['paidMembershipsPro'] : '',
            'affiliateWp'                  => $data['affiliateWp'] ? $data['affiliateWp'] : '',
            'indeedAffiliatePro'           => $data['indeedAffiliatePro'] ? $data['indeedAffiliatePro'] : '',
            'yithWoocommerceAffiliates'    => $data['yithWoocommerceAffiliates'] ? $data['yithWoocommerceAffiliates'] : '',
        );
        $option_json = wp_json_encode($option);
        $result = update_option('farazsms_integrations_options', $option_json);
        return $result;
    }


    /**
     * Check if a given request has permissions
     *
     * @return WP_Error|bool
     */
    public function permissions_check($request)
    {
        //return true; <--use to make readable by all
        return true;
    }
}
