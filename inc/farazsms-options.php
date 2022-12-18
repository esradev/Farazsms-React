<?php

/**
 * Create plugin options schema as an object and prepare it for the rest api.
 * WP >=5.3
 */
function farazsms_register_settings()
{

    $credentials_option = [
        'show_in_rest' => [
            'schema' => [
                'type' => 'object',
                'properties' => array(),
            ],
        ],
    ];

    add_option('credentials_option', $credentials_option);
}

add_action('init', 'farazsms_register_settings');

add_option('username', 'credentials_option');


/**
 * Build farazsms options REST API
 */
function farazsms_get_options()
{
    $credentials_option = get_option('credentials_option');
    if (empty($credentials_option)) {
        return new WP_Error('no_option', 'Invalid options', array('status' => 404));
    }
    return $credentials_option;
}

function farazsms_add_option($data)
{
    $option      = array(
        'apikey' =>                     $data['apikey'] ? $data['apikey'] : '',
        'username' =>                   $data['username'] ? $data['username'] : '',
        'password' =>                   $data['password'] ? $data['password'] : '',
        'admin_number' =>               $data['admin_number'] ? $data['admin_number'] : '',
        'from_number' =>                $data['from_number'] ? $data['from_number'] : '3000505',
        'from_number_adver' =>          $data['from_number_adver'] ? $data['from_number_adver'] : '',
        'welcome_sms' =>                $data['welcome_sms'] ? $data['welcome_sms'] : '',
        'welcome_sms_with_pattern' =>   $data['welcome_sms_with_pattern'] ? $data['welcome_sms_with_pattern'] : '',
        'welcome_sms_pattern' =>        $data['welcome_sms_pattern'] ? $data['welcome_sms_pattern'] : '',
        'admin_login_notify' =>         $data['admin_login_notify'] ? $data['admin_login_notify'] : '',
        'admin_login_notify_roles' =>   $data['admin_login_notify_roles'] ? $data['admin_login_notify_roles'] : '',
        'admin_login_notify_pattern' => $data['admin_login_notify_pattern'] ? $data['admin_login_notify_pattern'] : '',
        'add_mobile_field' =>           $data['add_mobile_field'] ? $data['add_mobile_field'] : '',
        'required_mobile_field' =>      $data['required_mobile_field'] ? $data['required_mobile_field'] : false,
        'notify_admin_for_comment' =>   $data['notify_admin_for_comment'] ? $data['notify_admin_for_comment'] : false,
        'comment_pattern' =>            $data['comment_pattern'] ? $data['comment_pattern'] : '',
        'notify_admin_pattern' =>       $data['notify_admin_pattern'] ? $data['notify_admin_pattern'] : '',
        'approved_comment_pattern' =>   $data['approved_comment_pattern'] ? $data['approved_comment_pattern'] : '',

    );
    $option_json = wp_json_encode($option);
    $result      = update_option('credentials_option', $option_json);
    return $result;
}

function farazsms_regsiter_api()
{
    register_rest_route('farazsms/v1', '/credentials_options', array(
        'methods' => 'GET',
        'callback' => 'farazsms_get_options',
    ));
    register_rest_route('farazsms/v1', '/credentials_options', array(
        'methods' => 'POST',
        'callback' => 'farazsms_add_option',
    ));
}


add_action('rest_api_init', 'farazsms_regsiter_api');
