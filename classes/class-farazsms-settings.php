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
        add_filter('plugin_action_links_' . FARAZSMS_BASE, array($this, 'settings_link'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_dashboard_setup', array($this, 'rss_meta_box'));
    }

    /**
     * Add Admin Menu.
     *
     * @return void
     */
    public function init_menu()
    {
        add_menu_page(__('Farazsms', 'farazsms'), __('Farazsms', 'farazsms'), 'manage_options', FARAZSMS_SLUG, array($this, 'admin_page'), 'dashicons-testimonial', 100);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Settings', 'farazsms'), 'manage_options', FARAZSMS_SLUG, [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Login Notify', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/login_notify', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Phone Book', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/phonebook', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Synchronization', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/synchronization', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Comments', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/comments', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Send SMS', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/sendsms', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Woocommerce', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/woocommerce', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Edd', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/edd', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Newslatter', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/newsletter', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Integrations', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/integrations', [$this, 'admin_page']);
    }

    /**
     * Init Admin Page.
     *
     * @return void
     */
    public function admin_page()
    {
        require_once FARAZSMS_MODULES_PATH . 'farazsms/includes/farazsms-admin.php';
    }

    /**
     * Enqueue scripts and styles.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style('farazsms-style', FARAZSMS_URL . 'build/index.css');
        wp_enqueue_script('farazsms-script', FARAZSMS_URL . 'build/index.js', array('wp-element', 'wp-i18n'), '1.0.0', true);
        /**
         * Add a localization object ,The base rest api url and a security nonce
         * @see https://since1979.dev/snippet-014-setup-axios-for-the-wordpress-rest-api/
         * */
        wp_localize_script('farazsms-script', 'farazsmsJsObject', array(
            'rootapiurl' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
        //Load Farazsms languages for JavaScript files. 
        wp_set_script_translations('farazsms-script', 'farazsms', FARAZSMS_PATH . '/languages');
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
     * Show the leatst posts from https://farazsms.com/ on dashboard widget
     *
     * @since    1.0.0
     */

    public function rss_meta_box()
    {

        if (get_option('fsms_rss_meta_box', '1') == '1') {
            add_meta_box(
                'FarazSMS_RSS',
                __('FarazSMS latest news', 'farazsms'),
                [$this, 'rss_postbox_container'],
                'dashboard',
                'side',
                'low'
            );
        }
    }

    public function rss_postbox_container()
    {
?>
        <div class="fsms-rss-widget">
            <?php wp_widget_rss_output(
                'https://farazsms.com/feed/',
                [
                    'items'        => 3,
                    'show_summary' => 1,
                    'show_author'  => 1,
                    'show_date'    => 1,
                ]
            ); ?>
        </div>
<?php
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
