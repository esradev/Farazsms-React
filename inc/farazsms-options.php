<?php

/**
 * Create plugin options schema as an object and prepare it for the rest api.
 * WP >=5.3
 */
function farazsms_register_settings()
{

    $farazsms_general_options = [
        'show_in_rest' => [
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'apikey' => [
                        'type' => 'string',
                        'default' => 'LWqlmg6mE2zdxATLB4s8sd0cQdG0a4mmXIh9mfE1GNU=',
                    ],
                    'username' => [
                        'type' => 'string',
                        'default' => 'farazsms',
                    ]
                ],
            ],
        ],
    ];

    add_option('farazsms_general_options', $farazsms_general_options);
}

add_action('init', 'farazsms_register_settings');

add_option('username', 'farazsms_general_options');


/**
 * Build farazsms options REST API
 */
function farazsms_get_options()
{
    return get_option('farazsms_general_options');
}

// function farazsms_add_option($data)
// {
//     $option      = array(
//         'key1' => $data['key1'],
//         'key2' => $data['key2'],
//     );
//     $option_json = wp_json_encode($option);
//     $result      = update_option('farazsms_general_options', $option_json);
//     return $result;
// }

function farazsms_regsiter_api()
{
    register_rest_route('farazsms/v1', '/options', array(
        'methods' => 'GET',
        'callback' => 'farazsms_get_options',
    ));
    // register_rest_route('farazsms/v1', '/options', array(
    //     'methods' => 'POST',
    //     'callback' => array('farazsms_add_option'),
    // ));
}


add_action('rest_api_init', 'farazsms_regsiter_api');
