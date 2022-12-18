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
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Phone Book', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/phonebook', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Synchronization', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/synchronization', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Comment Settings', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/comments', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Woocommerce Settings', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/sendsms', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Edd Settings', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/woocommerce', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Newslatter Settings', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/edd', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Other Plugins', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/newsletters', [$this, 'admin_page']);
        add_submenu_page(FARAZSMS_SLUG, __('FarazSMS', 'farazsms'), __('Send SMS', 'farazsms'), 'manage_options', FARAZSMS_SLUG . '#/otherplugins', [$this, 'admin_page']);
    }

    /**
     * Add bar menu. show some links for farazsms plugin on the admin bar.
     *
     * @since    1.0.0
     */

    public function fsms_admin_add_bar_menu()
    {
        $fsms_base = class_farazsms_base::getInstance();
        global $wp_admin_bar;
        if (!is_super_admin() || !is_admin_bar_showing()) {
            return;
        }
        $wp_admin_bar->add_menu(array(
            'id' => 'farazsms', 'parent' => null, 'group' => null, 'title' => __('FarazSms ', 'farazsms') . '<img style="padding-top: 10px" src="' . plugin_dir_url(__FILE__) . '/img/logo.png"/>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?farazsms', 'meta' => [
                'title' => __('FarazSms', 'textdomain'), //This title will show on hover
            ]
        ));
        $credit = $fsms_base::get_credit();
        if ($credit) {
            $wp_admin_bar->add_menu(array('parent' => 'farazsms', 'id'     => 'farazsms-admin-bar-credit', 'title' => __('Account credit: ', 'farazsms') . number_format($credit) . __(' $IR_T', 'farazsms'), 'href' => get_bloginfo('url') . '/wp-admin/admin.php?farazsms'));
        }
        $wp_admin_bar->add_menu(array('parent' => 'farazsms', 'title' => __('Send Sms', 'farazsms'), 'id' => 'farazsms-admin-bar-send-sms', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=farazsms&tab=tab-5'));
        $wp_admin_bar->add_menu(array('parent' => 'farazsms', 'title' => __('FarazSms', 'farazsms'), 'id' => 'farazsms-admin-bar-go-to-site', 'href' => 'https://farazsms.com/'));
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
        wp_enqueue_script('farazsms-script', FARAZSMS_URL . 'build/index.js', array('wp-element', 'wp-i18n'), '1.0.0', true);
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
