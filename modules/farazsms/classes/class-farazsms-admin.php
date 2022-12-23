<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://farazsms.com/
 * @since      1.0.0
 *
 * @package    Farazsms
 * @subpackage Farazsms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Farazsms
 * @subpackage Farazsms/admin
 * @author     FarazSMS <info@farazsms.com>
 */
class Farazsms_Admin extends class_farazsms_base
{
    /**
     * The ID of this plugin.
     *
     * @var      string $plugin_name The ID of this plugin.
     * @since    1.0.0
     * @access   private
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @var      string $version The current version of this plugin.
     * @since    1.0.0
     * @access   private
     */
    private $version;


    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
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
     * Enqueue scripts and styles for admin area.
     *
     * @return void
     */
    public function enqueue_scripts()
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
     * Add bar menu. show some links for farazsms plugin on the admin bar.
     *
     * @since    1.0.0
     */

    public function admin_bar_menu()
    {
        $fsms_base = class_farazsms_base::getInstance();
        global $wp_admin_bar;
        if (!is_super_admin() || !is_admin_bar_showing()) {
            return;
        }
        $wp_admin_bar->add_menu(array(
            'id' => 'farazsms', 'parent' => null, 'group' => null, 'title' => __('FarazSms ', 'farazsms'), 'meta' => [
                'title' => __('FarazSMS', 'textdomain'), //This title will show on hover
            ]
        ));
        $credit = $fsms_base::get_credit();
        if ($credit) {
            $wp_admin_bar->add_menu(array('parent' => 'farazsms', 'id'     => 'farazsms-admin-bar-credit', 'title' => __('Account credit: ', 'farazsms') . number_format($credit) . __(' $IR_T', 'farazsms'), 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=farazsms_settings'));
        }
        $wp_admin_bar->add_menu(array('parent' => 'farazsms', 'title' => __('Send Sms', 'farazsms'), 'id' => 'farazsms-admin-bar-send-sms', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=farazsms_settings#/sendsms'));
        $wp_admin_bar->add_menu(array('parent' => 'farazsms', 'title' => __('FarazSMS', 'farazsms'), 'id' => 'farazsms-admin-bar-go-to-site', 'href' => 'https://farazsms.com/'));
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
}
