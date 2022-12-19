<?php

/**
 * Register farazsms settings option rest routes.
 * 
 * @since 2.0.0
 */

function register_farazsms_settings_option()
{

    $farazsms_settings_option = [
        'show_in_rest' => [
            'schema' => [
                'type' => 'object',
                'properties' => array(),
            ],
        ],
    ];

    add_option('farazsms_settings_option', $farazsms_settings_option);
}

add_action('init', 'register_farazsms_settings_option');


/**
 * Build farazsms settings options REST API
 */
function get_farazsms_settings_options()
{
    $farazsms_settings_option = get_option('farazsms_settings_option');
    if (empty($farazsms_settings_option)) {
        return new WP_Error('no_option', 'Invalid options', array('status' => 404));
    }
    return $farazsms_settings_option;
}

function add_farazsms_settings_option($data)
{
    $option      = array(
        'apikey' =>                     $data['apikey'],
        'username' =>                   $data['username'] ? $data['username'] : '',
        'password' =>                   $data['password'] ? $data['password'] : '',
        'admin_number' =>               $data['admin_number'] ? $data['admin_number'] : '',
        'from_number' =>                $data['from_number'] ? $data['from_number'] : '3000505',
        'from_number_adver' =>          $data['from_number_adver'] ? $data['from_number_adver'] : '',
    );
    $option_json = wp_json_encode($option);
    $result      = update_option('farazsms_settings_option', $option_json);
    return $result;
}

/**
 * Register farazsms login notify option rest routes.
 * 
 * @since 2.0.0
 */

function register_farazsms_login_notify_option()
{

    $farazsms_login_notify_option = [
        'show_in_rest' => [
            'schema' => [
                'type' => 'object',
                'properties' => array(),
            ],
        ],
    ];

    add_option('farazsms_login_notify_option', $farazsms_login_notify_option);
}

add_action('init', 'register_farazsms_login_notify_option');


function get_farazsms_login_notify_options()
{
    $farazsms_login_notify_option = get_option('farazsms_login_notify_option');
    if (empty($farazsms_login_notify_option)) {
        return new WP_Error('no_option', 'Invalid options', array('status' => 404));
    }
    return $farazsms_login_notify_option;
}

function add_farazsms_login_notify_option($data)
{
    $option      = array(
        'welcome_sms' =>                $data['welcome_sms'] ? $data['welcome_sms'] : false,
        'welcome_sms_use_p' =>          $data['welcome_sms_use_p'] ? $data['welcome_sms_use_p'] : false,
        'welcome_sms_p' =>              $data['welcome_sms_p'] ? $data['welcome_sms_p'] : '',
        'welcome_sms_message' =>        $data['welcome_sms_message'] ? $data['welcome_sms_message'] : '',
        'admin_login_notify' =>         $data['admin_login_notify'] ? $data['admin_login_notify'] : '',
        'select_roles' =>               $data['select_roles'] ? $data['select_roles'] : '',
    );
    $option_json = wp_json_encode($option);
    $result      = update_option('farazsms_login_notify_option', $option_json);
    return $result;
}

/**
 * Register farazsms comments option rest routes.
 * 
 * @since 2.0.0
 */

function register_farazsms_comments_option()
{

    $farazsms_comments_option = [
        'show_in_rest' => [
            'schema' => [
                'type' => 'object',
                'properties' => array(),
            ],
        ],
    ];

    add_option('farazsms_comments_option', $farazsms_comments_option);
}

add_action('init', 'register_farazsms_comments_option');


function get_farazsms_comments_options()
{
    $farazsms_comments_option = get_option('farazsms_comments_option');
    if (empty($farazsms_comments_option)) {
        return new WP_Error('no_option', 'Invalid options', array('status' => 404));
    }
    return $farazsms_comments_option;
}

function add_farazsms_comments_option($data)
{
    $option      = array(
        'add_mobile_field' =>           $data['add_mobile_field'] ? $data['add_mobile_field'] : '',
        'required_mobile_field' =>      $data['required_mobile_field'] ? $data['required_mobile_field'] : false,
        'notify_admin_for_comment' =>   $data['notify_admin_for_comment'] ? $data['notify_admin_for_comment'] : false,
        'comment_p' =>                  $data['comment_p'] ? $data['comment_p'] : '',
        'approved_comment_p' =>         $data['approved_comment_p'] ? $data['approved_comment_p'] : '',
    );
    $option_json = wp_json_encode($option);
    $result      = update_option('farazsms_comments_option', $option_json);
    return $result;
}

/**
 * Register all option rest routes with rest-api-init.
 * 
 * @since 2.0.0
 */

function farazsms_regsiter_api()
{
    //Register farazsms_settings_option rest route
    register_rest_route('farazsms/v1', '/farazsms_settings_options', array(
        'methods' => 'GET',
        'callback' => 'get_farazsms_settings_options',
    ));
    register_rest_route('farazsms/v1', '/farazsms_settings_options', array(
        'methods' => 'POST',
        'callback' => 'add_farazsms_settings_option',
    ));
    //Register farazsms_login_notify_option rest route
    register_rest_route('farazsms/v1', '/farazsms_login_notify_options', array(
        'methods' => 'GET',
        'callback' =>  'get_farazsms_login_notify_options',
    ));
    register_rest_route('farazsms/v1', '/farazsms_login_notify_options', array(
        'methods' => 'POST',
        'callback' => 'add_farazsms_login_notify_option',
    ));
    //Register farazsms_comments_option rest route
    register_rest_route('farazsms/v1', '/farazsms_comments_options', array(
        'methods' => 'GET',
        'callback' =>  'get_farazsms_comments_options',
    ));
    register_rest_route('farazsms/v1', '/farazsms_comments_options', array(
        'methods' => 'POST',
        'callback' => 'add_farazsms_comments_option',
    ));
}



add_action('rest_api_init', 'farazsms_regsiter_api');
