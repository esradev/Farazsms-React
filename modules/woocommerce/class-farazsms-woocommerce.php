<?php

/**
 * Farazsms woocommerce.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class Farazsms_Woocommerce.
 */
class Farazsms_Woocommerce
{
    /**
     * Instance
     *
     * @access private
     * @var object Class object.
     * @since 2.0.0
     */
    private static $instance;

    /**
     * Initiator
     *
     * @since 2.0.0
     * @return object initialized object of class.
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
    }
}
Farazsms_Woocommerce::get_instance();
