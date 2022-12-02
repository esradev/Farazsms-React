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
        add_action('admin_menu', array($this, 'init_menu'));
        add_filter('plugin_action_links_' . FARAZSMS_PLUGIN_NAME, array($this, 'settings_link'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    /**
     * Init Admin Menu.
     *
     * @return void
     */
    public function init_menu()
    {
        add_menu_page(__('Farazsms', 'farazsms'), __('Farazsms', 'farazsms'), 'manage_options', FARAZSMS_SLUG, array($this, 'admin_page'), 'dashicons-testimonial', 100);
    }

    /**
     * Init Admin Page.
     *
     * @return void
     */
    public function admin_page()
    {
        require_once FARAZSMS_INC_PATH . 'farazsms-admin.php';
    }

    /**
     * Enqueue scripts and styles.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style('farazsms-style', FARAZSMS_URL . 'build/index.css');
        wp_enqueue_script('farazsms-script', FARAZSMS_URL . 'build/index.js', array('wp-element'), '1.0.0', true);
    }

    /**
     * Plguin settings link on all plugins page.
     */

    public function settings_link($links)
    {
        // Add settings link
        $settings_link = '<a href="' . FARAZSMS_SETTINGS_LINK . '">Settings</a>';

        // Add document link
        $doc_link = '<a href="' . FARAZSMS_WEB_MAIN_DOC . '" target="_blank" rel="noopener noreferrer">Docs</a>';
        array_push($links, $settings_link, $doc_link);
        return $links;
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
