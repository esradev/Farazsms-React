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
                'properties' => [],
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
    return get_option('credentials_option');
}

function farazsms_add_option($data)
{
    $option      = array(
        'username' => $data['username'] ? $data['username'] : '',
        'password' => $data['password'] ? $data['password'] : '',

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
