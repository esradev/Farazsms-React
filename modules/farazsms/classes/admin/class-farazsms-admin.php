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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function admin_enqueue_styles()
    {
        wp_enqueue_style('farazsms-style', FARAZSMS_URL . 'build/index.css');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function admin_enqueue_scripts($hook)
    {
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

        global $post;

        wp_enqueue_script('jquery-validate', plugin_dir_url(__FILE__) . 'js/jquery.validate.min.js', ['jquery'], $this->version, TRUE);
        wp_enqueue_script('select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery-validate'), '1.0', true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/farazsms-admin.js', ['select2'], $this->version, TRUE);
        //wp_enqueue_script( 'select2' );

        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if ('shop_order' === $post->post_type) {
                wp_enqueue_style('farazsms-tracking-code', plugin_dir_url(__FILE__) . 'css/farazsms-tracking-code.css', [], $this->version, 'all');
                wp_enqueue_script('jquery-validate', plugin_dir_url(__FILE__) . 'js/jquery.validate.min.js', ['jquery'], $this->version, TRUE);
                wp_enqueue_script('farazsms-tracking-code', plugin_dir_url(__FILE__) . 'js/farazsms-tracking-code.js', ['jquery-validate'], $this->version, TRUE);
            }
        }
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
     * Plugin settings link on all plugins page.
     * 
     * @since 2.0.0
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
     * Synchronization Operator whit a phone book.
     *
     * @since    1.0.0
     */

    public function ajax_sync_operate()
    {
        $sync_operation = $_POST['sync_operation'] ?? '';
        if ($sync_operation === 'fsms_digits-sync') {
            $result = $this->sync_digits();
        } elseif ($sync_operation === 'fsms_woo-sync') {
            $result = $this->sync_woo();
        } elseif ($sync_operation === 'fsms_bookly-sync') {
            $result = $this->sync_bookly();
        }
        if ($result === 'empty_phonebook') {
            wp_send_json_error(__('Please select a phonebook first.', 'farazsms'));
        }
        if ($result === 'error_happened') {
            wp_send_json_error(__('An error occurred. Please try again later.', 'farazsms'));
        }
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    /**
     * Digits Synchronization whit a phone book.
     *
     * @since    1.0.0
     */

    private function sync_digits()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $users = get_users(array('fields' =>  'ID'));
        $digits_phone_books = get_option('fsms_digits_phone_books', []);
        if (empty($digits_phone_books)) {
            return "empty_phonebook";
        }
        foreach ($digits_phone_books as $phone_bookId) {
            $data = [];
            foreach ($users as $userid) {
                $number_info = [];
                $user_digits_phone = get_user_meta($userid, 'digits_phone', true);
                if (empty($user_digits_phone)) {
                    continue;
                }
                $user_info = get_userdata($userid);
                $number_info["number"] = $user_digits_phone;
                $number_info["name"] = $user_info->display_name ?? $user_info->first_name .  " " . $user_info->last_name;
                $number_info["phonebook_id"] = (int) $phone_bookId;
                array_push($data, $number_info);
            }
            $result = $fsms_base::save_to_phonebookv3($data);
        }
        if (!$result) {
            return "error_happened";
        }
        return true;
    }

    /**
     * Woocommerce Synchronization whit a phone book.
     *
     * @since    1.0.0
     */

    private function sync_woo()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $query = new WC_Order_Query(array('limit' => 9999, 'type' => 'shop_order', 'return' => 'ids'));
        $order_ids = $query->get_orders();
        $woo_phone_books = get_option('fsms_woo_phone_books', []);
        if (empty($woo_phone_books)) {
            return "empty_phonebook";
        }
        foreach ($woo_phone_books as $phone_bookId) {
            $data = [];
            foreach ($order_ids as $order_id) {
                $number_info = [];
                $order = wc_get_order($order_id);
                $number = $order->get_billing_phone();
                $name = $order->get_formatted_billing_full_name();
                $number_info["number"] = $number;
                $number_info["name"] = $name;
                $number_info["phonebook_id"] = (int)$phone_bookId;
                array_push($data, $number_info);
            }
            $result = $fsms_base::save_to_phonebookv3($data);
        }
        if (!$result) {
            return "error_happened";
        }
        return TRUE;
    }

    /**
     * Bookly Synchronization whit a phone book.
     *
     * @since    1.0.0
     */

    private function sync_bookly()
    {
        $fsms_base = class_farazsms_base::getInstance();
        global $wpdb;
        $bookly_customers = $wpdb->get_results("SELECT phone,full_name FROM " . $wpdb->prefix . "bookly_customers");
        $bookly_phone_books     = get_option('fsms_bookly_phone_books', []);
        if (empty($bookly_phone_books)) {
            return "empty_phonebook";
        }
        foreach ($bookly_phone_books as $phone_bookId) {
            $data = [];
            foreach ($bookly_customers as $customer) {
                $number_info = [];
                $number_info["number"] = substr($customer->phone, -10);
                $number_info["name"] = $customer->full_name;
                $number_info["phonebook_id"] = (int)$phone_bookId;
                array_push($data, $number_info);
            }
            $result = $fsms_base::save_to_phonebookv3($data);
        }
        if (!$result) {
            return "error_happened";
        }
        return true;
    }

    /**
     * Comments table coulums.
     *
     * @since    1.0.0
     */

    public function comments_fsms_table_columns($my_cols)
    {
        $temp_columns = array(
            'mobile' => __('Phone number', 'farazsms')
        );
        $my_cols = array_slice($my_cols, 0, 3, true) + $temp_columns + array_slice($my_cols, 3, NULL, true);
        return $my_cols;
    }

    public function comments_fsms_table_columns_content($column, $comment_ID)
    {
        global $comment;
        switch ($column):
            case 'mobile': {
                    echo get_comment_meta($comment_ID, 'mobile', true);
                    break;
                }
        endswitch;
    }

    /**
     * Send SMS to phonebooks.
     *
     * @since    1.0.0
     */

    public function ajax_send_message_to_phonebooks()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $message = $_POST['message'] ?? '';
        $phonebooks = $_POST['phonebooks'] ?? [];
        $send_to_subscribers = $_POST['send_to_subscribers'] ?? '';
        $send_formnum_choice = $_POST['send_formnum_choice'] ?? '';
        if ($send_formnum_choice == '2' && !strpos($_POST['phones'], ',')) {
            wp_send_json_error(__('Please enter manual numbers in the correct format', 'farazsms'));
        }
        if ($send_formnum_choice == '1') {
            $send_formnum_choice = $fsms_base::get_service_sender_number();
        } else {
            $send_formnum_choice = $fsms_base::get_adver_sender_number();
        }
        $phones = explode(',', $_POST['phones'] ?? '');
        foreach ($phones as $phone) {
            if ($fsms_base::validate_mobile_number($phone)) {
                $fixed_phones[] = $fsms_base::validate_mobile_number($phone);
            }
        }
        if (empty($phonebooks) && empty($fixed_phones) && $send_to_subscribers == "false") {
            wp_send_json_error(__('Please select at least one phonebook or manual number or newsletter members', 'farazsms'));
            wp_die();
        }
        if (!empty($fixed_phones)) {
            $fsms_base::send_message($fixed_phones, $message, $send_formnum_choice);
        }
        foreach ($phonebooks as $phonebook) {
            $phonebook_numbers = $fsms_base::get_phonebook_numbers($phonebook);
            $fsms_base::send_message($phonebook_numbers, $message, $send_formnum_choice);
        }
        wp_send_json_success();
    }

    /**
     * Save Newsletter settings.
     *
     * @since    1.0.0
     */

    public function fsms_save_newsletter_settings()
    {
        $newsletter_phonebooks = $_POST['newsletter_phonebooks'] ?? [];
        $newsletter_send_ver_code = $_POST['newsletter_send_ver_code'] ?? '';
        $newsletter_pcode = $_POST['newsletter_pcode'] ?? '';
        $newsletter_welcome = $_POST['newsletter_welcome'] ?? '';
        $newsletter_welcomep = $_POST['newsletter_welcomep'] ?? '';
        $newsletter_new_post_notification = $_POST['newsletter_new_post_notification'] ?? '';
        $newsletter_post_notification_message = $_POST['newsletter_post_notification_message'] ?? '';
        $newsletter_new_product_notification = $_POST['newsletter_new_product_notification'] ?? '';
        $newsletter_product_notification_message = $_POST['newsletter_product_notification_message'] ?? '';
        if ($newsletter_send_ver_code == 'true' && empty($newsletter_pcode)) {
            wp_send_json_error(__('Please enter the pattern code', 'farazsms'));
            wp_die();
        }
        if ($newsletter_welcome == 'true' && empty($newsletter_welcomep)) {
            wp_send_json_error(__('Please enter the pattern code of the welcome SMS', 'farazsms'));
            wp_die();
        }
        update_option('fsms_newsletter_phonebooks', $newsletter_phonebooks);
        update_option('fsms_newsletter_send_ver_code', $newsletter_send_ver_code);
        update_option('fsms_newsletter_newsletter_pcode', $newsletter_pcode);
        update_option('fsms_newsletter_welcome', $newsletter_welcome);
        update_option('fsms_newsletter_welcomep', $newsletter_welcomep);
        update_option('fsms_newsletter_new_post_notification', $newsletter_new_post_notification);
        update_option('fsms_newsletter_post_notification_message', $newsletter_post_notification_message);
        update_option('fsms_newsletter_new_product_notification', $newsletter_new_product_notification);
        update_option('fsms_newsletter_product_notification_message', $newsletter_product_notification_message);
        wp_send_json_success();
    }

    /**
     * Delete user from subscribers.
     *
     * @since    1.0.0
     */

    public function fsms_delete_user_from_subscribers()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $subscriber_id = $_POST['subscriber_id'] ?? '';
        $fsms_base::delete_subscriber($subscriber_id);
        wp_send_json_success();
    }

    /**
     * Send message to subscribers of newsletter.
     *
     * @since    1.0.0
     */

    public function send_message_to_subscribers()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $message = $_POST['message'] ?? '';
        $subscribers = $fsms_base::get_subscribers();
        if (!$fsms_base::isAPIKeyEntered()) {
            wp_send_json_error(__('Please enter the api key first in the Settings tab.', 'farazsms'));
        }
        if (empty($subscribers)) {
            wp_send_json_error(__('No one is a subscriber of the newsletter yet', 'farazsms'));
        }
        if (strpos($message, '%name%') !== false) {
            foreach ($subscribers as $subscriber) {
                $message_fixed = str_replace('%name%', $subscriber->name, $message);
                $fsms_base::send_message([$subscriber->phone], $message_fixed, "+98club");
            }
        } else {
            $phones = [];
            foreach ($subscribers as $subscriber) {
                $phones[] = $subscriber->phone;
            }
            $fsms_base::send_message($phones, $message, "+98club");
        }
        wp_send_json_success();
    }

    /**
     * Send Tracking code for orders.
     *
     * @since    1.0.0
     */

    public function fsms_tracking_code_order_postbox()
    {
        add_meta_box(
            'fsms-tracking_send_sms',
            __('Send tracking code.', 'farazsms'),
            [$this, 'add_order_tracking_box'],
            'shop_order',
            'side',
            'core'
        );
    }

    public function add_order_tracking_box($post)
    {
        echo '<div id="fsms-tracking-code-input"><input type="text" name="tracking_code" id="fsms_racking_code" /></div>';
        echo '<div id="fsms-tracking-code-button"><div class="fsms_button" id="send_tracking_code_button"><span class="button__text">ارسال پیامک</span></div></div>';
        echo ' <input type="hidden" id="fsms-tracking-code-order_id" value="' . $post->ID . '">';
        echo '<div id="send_tracking_code_response" style="display: none;"></div>';
    }

    public function fsms_send_tracking_code_sms()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $tacking_code = $_POST['tacking_code'] ?? '';
        $order_id = $_POST['order_id'] ?? '';
        try {
            if (empty($tacking_code) || strlen($tacking_code) < 20) {
                throw new Exception(__('Please enter the tracking code correctly.', 'farazsms'));
            }
            $order = wc_get_order($order_id);
            $phone = $order->get_billing_phone();
            if (empty($phone)) {
                throw new Exception(__('Customer phone number not entered.', 'farazsms'));
            }
            $order_date['order_id'] = $order->get_id();
            $order_date['order_status'] = wc_get_order_status_name($order->get_status());
            $order_date['billing_full_name'] = $order->get_formatted_billing_full_name();
            $order_date['shipping_full_name'] = $order->get_formatted_shipping_full_name();
            $fsms_base::send_tracking_code($phone, $tacking_code, $order_date);
            wp_send_json_success();
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Save other settings.
     *
     * @since    1.0.0
     */

    public function fsms_save_other_settings()
    {
        update_option('fsms_ihc_send_first_noti_sms', $_POST['ihc_send_first_noti_sms'] ?? '');
        update_option('fsms_ihc_send_second_noti_sms', $_POST['ihc_send_second_noti_sms'] ?? '');
        update_option('fsms_ihc_send_third_noti_sms', $_POST['ihc_send_third_noti_sms'] ?? '');
        update_option('fsms_first_noti_sms_message', $_POST['ihc_first_noti_sms_message'] ?? '');

        update_option('fsms_pmp_send_expire_noti_sms', $_POST['pmp_send_expire_noti_sms'] ?? '');
        update_option('fsms_pmp_expire_noti_sms_message', $_POST['pmp_expire_noti_sms_message'] ?? '');

        update_option('fsms_aff_user_mobile_field', $_POST['aff_user_mobile_field'] ?? '');
        update_option('fsms_aff_user_register', $_POST['aff_user_register'] ?? '');
        update_option('fsms_aff_user_register_message', $_POST['aff_user_register_message'] ?? '');
        update_option('fsms_aff_user_new_ref', $_POST['aff_user_new_ref'] ?? '');
        update_option('fsms_aff_user_new_ref_message', $_POST['aff_user_new_ref_message'] ?? '');
        update_option('fsms_aff_user_on_approval', $_POST['aff_user_on_approval'] ?? '');
        update_option('fsms_aff_user_on_approval_message', $_POST['aff_user_on_approval_message'] ?? '');
        update_option('fsms_aff_admin_user_register', $_POST['aff_admin_user_register'] ?? '');
        update_option('fsms_aff_admin_user_register_message', $_POST['aff_admin_user_register_message'] ?? '');
        update_option('fsms_aff_admin_user_new_ref', $_POST['aff_admin_user_new_ref'] ?? '');
        update_option('fsms_aff_admin_user_new_ref_message', $_POST['aff_admin_user_new_ref_message'] ?? '');
        update_option('fsms_aff_admin_user_on_approval', $_POST['aff_admin_user_on_approval'] ?? '');
        update_option('fsms_aff_admin_user_on_approval_message', $_POST['aff_admin_user_on_approval_message'] ?? '');
        wp_send_json_success();
    }

    /**
     * Send feedback.
     *
     * @since    1.0.0
     */

    public function fsms_send_feedback()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $feedback_message = $_POST['feedback_message'];
        if (empty($feedback_message)) {
            wp_send_json_error(__('Message text must not be empty', 'farazsms'));
        }
        $result = $fsms_base::send_feedback_message_to_server($feedback_message);
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('An error occurred, try again later.', 'farazsms'));
        }
    }
}
