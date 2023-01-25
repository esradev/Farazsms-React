<?php

/**
 * Farazsms elementor.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class Farazsms_Elementor.
 */
class Farazsms_Elementor
{
    private static $elementorPro;

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
        $integrations_options = json_decode(get_option('farazsms_integrations_options'), true);
        if ($integrations_options) {
            self::$elementorPro = $integrations_options['elementorPro'] ?? '';
        }

        if (self::$elementorPro) {
            add_action('elementor_pro/forms/actions/register', array($this, 'add_new_farazsms_newsletter_form_action'));
        }
    }

    /**
     *  Add new action
     */
    public function add_new_farazsms_newsletter_form_action($form_actions_registrar)
    {

        include_once(__DIR__ .  '/form-actions/class-farazsms-newsletter-action-after-submit.php');

        $form_actions_registrar->register(new Farazsms_Newsletter_Action_After_Submit());
    }
}
Farazsms_Elementor::get_instance();
