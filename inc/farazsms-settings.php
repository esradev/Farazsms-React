<?php

/**
 * Settings.
 *
 * @package Farazsms
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class Farazsms_Settings
 */
class Farazsms_Settings
{


    /**
     * Member Variable
     *
     * @var instance
     */
    private static $instance;


    /**
     * FarazSmsSettings constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'farazsms_init_menu'));
        add_action('admin_enqueue_scripts', array($this, 'farazsms_admin_enqueue_scripts'));
    }

    /**
     * Init Admin Menu.
     *
     * @return void
     */
    public function farazsms_init_menu()
    {
        add_menu_page(__('Farazsms', 'farazsms'), __('Farazsms', 'farazsms'), 'manage_options', 'farazsms-plugin-react', array($this, 'farazsms_admin_page'), 'dashicons-testimonial', 100);
    }

    /**
     * Init Admin Page.
     *
     * @return void
     */
    public function farazsms_admin_page()
    {
        require_once FARAZSMS_DIR . 'templates/app.php';
    }

    /**
     * Enqueue scripts and styles.
     *
     * @return void
     */
    public function farazsms_admin_enqueue_scripts()
    {
        wp_enqueue_style('farazsms-style', FARAZSMS_URL . 'build/index.css');
        wp_enqueue_script('farazsms-script', FARAZSMS_URL . 'build/index.js', array('wp-element'), '1.0.0', true);
    }

    /**
     *  Initiator
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
Farazsms_Settings::get_instance();
