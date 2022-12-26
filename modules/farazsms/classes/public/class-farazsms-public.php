<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://farazsms.com/
 * @since      1.0.7
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Farazsms
 * @subpackage Farazsms/public
 * @author     FarazSMS <info@farazsms.com>
 */
class Farazsms_Public extends class_farazsms_base
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $username;
    private $password;
    private $fromNum;
    private $_woo_installed = false;
    private $_digits_installed = false;

    private static $fsms_addmf;
    private static $fsms_requiredmf;
    private static $fsms_notify_admin;
    private static $approved_commentp;
    private static $user_pattern;
    private static $admin_pattern;
    private $comment_phone_book;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_shortcode('farazsms', [$this, 'farazsms_newsletter']);

        $comments_options = json_decode(get_option('farazsms_comments_options'), true);
        if ($comments_options) {
            self::$fsms_addmf = $comments_options['add_mobile_field'];
            self::$fsms_requiredmf = $comments_options['required_mobile_field'];
            self::$fsms_notify_admin = $comments_options['notify_admin_for_comment'];
            self::$approved_commentp = $comments_options['approved_comment_p'];
            self::$user_pattern = $comments_options['comment_p'];
            self::$admin_pattern = $comments_options['notify_admin_for_comment_p'];
            // $fsms_sendwm_with_pattern = $comments_options['comment_phone_book'];
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        //wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/farazsms-public.css', array(), $this->version, 'all' );
        wp_register_style("farazsms-newsletter", plugin_dir_url(__FILE__) . 'assets/css/farazsms-newsletter.css', array(), $this->version, 'all');

        $woo_checkout_otp = get_option('fsms_woo_checkout_otp', 'false');
        if ($woo_checkout_otp === 'true' && is_checkout()) {
            wp_enqueue_style("farazsms-woo-otp", plugin_dir_url(__FILE__) . 'assets/css/farazsms-woo-otp.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/farazsms-public.js', array( 'jquery' ), $this->version, false );
        wp_register_script("farazsms-newsletter", plugin_dir_url(__FILE__) . 'js/farazsms-newsletter.js', array('jquery'), $this->version, false);

        $woo_checkout_otp = get_option('fsms_woo_checkout_otp', 'false');
        if ($woo_checkout_otp === 'true' && is_checkout()) {
            wp_enqueue_script("farazsms-woo-otp", plugin_dir_url(__FILE__) . 'js/farazsms-woo-otp.js', array('jquery'), $this->version, TRUE);
            wp_localize_script(
                'farazsms-woo-otp',
                'fsms_ajax_url',
                array('ajax_url' => admin_url('admin-ajax.php'))
            );
        }
    }


    /**
     * 
     * Monitor update user metadata.
     *
     */

    public function monitor_update_user_metadata($check, $object_id, $meta_key, $meta_value)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $selected_meta_keys = get_option('fsms_custom_phone_meta_keys', []);
        if (!in_array($meta_key, $selected_meta_keys)) {
            return $check;
        }
        $phone = $fsms_base::validate_mobile_number($meta_value);
        if (!$phone) {
            return $check;
        }
        $custom_phone_books = get_option('fsms_custom_phone_books', []);
        if (empty($custom_phone_books)) {
            return $check;
        }
        $user_info = get_userdata($object_id);
        foreach ($custom_phone_books as $phone_bookId) {
            //$fsms_base::save_to_phonebook($phone, $phone_bookId);
            $data[] = [
                "number" => $phone,
                "name"   => $user_info->display_name ?? '',
                "phonebook_id" => (int) $phone_bookId
            ];
            $fsms_base::save_to_phonebookv3($data);
        }
        $fsms_base::send_welcome_message($phone, $object_id);
        return $check;
    }

    /**
     * 
     * User profile updated.
     *
     */

    public function fsms_user_profile_updated($user_id, $old_user_data)
    {
        $digits_phone = get_user_meta($user_id, 'digits_phone', TRUE);
        if (empty($digits_phone)) {
            return;
        }
        $fsms_base = class_farazsms_base::getInstance();
        $digits_phone_books = get_option('fsms_digits_phone_books', []);
        //if(empty($digits_phone_books)){return;}
        $user_info = get_userdata($user_id);
        foreach ($digits_phone_books as $phone_bookId) {
            //$fsms_base::save_to_phonebook($phone, $phone_bookId);
            $data[] = [
                "number" => $digits_phone,
                "name"   => $user_info->display_name ?? '',
                "phonebook_id" => (int) $phone_bookId
            ];
            $fsms_base::save_to_phonebookv3($data);
        }
        $already_sent_one = get_user_meta($user_id, 'sent_welcome_message', TRUE);
        if (!empty($already_sent_one) && $already_sent_one == '1') {
            return;
        }
        $fsms_base::send_welcome_message($digits_phone, $user_id);
        update_user_meta($user_id, 'sent_welcome_message', '1');
    }

    /**
     * 
     * User created action.
     *
     */

    public function fsms_user_created_action($userID)
    {
        global $wpdb;
        $posts = $wpdb->get_row("SELECT phone FROM " . $wpdb->prefix . "bookly_customers WHERE wp_user_id = '" . $userID . "'");
    }

    /**
     * 
     * Woocommerce payment finished.
     *
     */

    public function woo_payment_finished($id)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $order = wc_get_order($id);
        $phone = $order->get_billing_phone();
        $name = $order->get_formatted_billing_full_name() ?? '';
        if (empty($phone)) {
            return;
        }
        //		$phone = substr($phone,-10);
        $woo_phone_books = get_option('fsms_woo_phone_books', []);
        foreach ($woo_phone_books as $phone_bookId) {
            //$fsms_base::save_to_phonebook($phone, $phone_bookId);
            $data[] = [
                "number" => $phone,
                "name"   => $name,
                "phonebook_id" => (int) $phone_bookId
            ];
            $fsms_base::save_to_phonebookv3($data);
        }
        //$fsms_base::send_welcome_message($phone, $order->get_billing_first_name().' '.$order->get_billing_last_name());
        return true;
    }

    /**
     * 
     * Add mobile field to comment form
     *
     */

    public function add_mobile_field_to_comment_form()
    {
        if (self::$fsms_addmf === 0) {
            return;
        }
        $r = '';
        $res = '<p class="comment-form-phone">'
            . '<label for="mobile">شماره موبایل';
        if (self::$fsms_requiredmf === 1) {
            $res .= ' <span class="required">*</span></label>';
            $r = 'required="required"';
        }
        $res .= '<input class="uk-input uk-width-large uk-display-block" oninput="javascript: if (this.value.length > 11) this.value = this.value.slice(0, 11);" type="number"  name="mobile" id="mobile"' . '' . $r . '/></p>';
        echo $res;
    }

    // Save mobile field.
    public function save_mobile_field($comment_id)
    {
        if (isset($_POST['mobile'])) {
            $mobile = class_farazsms_base::validate_mobile_number(esc_attr($_POST['mobile']));
            update_comment_meta($comment_id, 'mobile', $mobile);
        }
        $this->response_to_comment($comment_id);
    }

    // Verify comment input
    public function verify_comment_input($commentdata)
    {
        if (empty($commentdata['comment_parent']) && self::$fsms_requiredmf === 1) {
            if (!isset($_POST['mobile']) or empty($_POST['mobile'])) wp_die(__('Error: Mobile number is required.', 'farazsms'));
        }
        return $commentdata;
    }

    // Response to comment
    public function response_to_comment($comment_id)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $comment = get_comment($comment_id);
        $data = $this->comments_farazsms_shortcode($comment, $comment_id);
        $mobile = get_comment_meta($data['parent'])['mobile'][0] ?? '';
        $user = get_user_by('id', $comment->user_id);
        $is_admin = in_array('administrator', (array) $user->roles);
        if ($comment->comment_parent == 0) {
            $mobile = get_comment_meta($comment_id)['mobile'][0] ?? '';
            if (!empty(self::$approved_commentp) || !empty($mobile)) {
                $fsms_base::send_comment_reply_sms($mobile, self::$approved_commentp, $data);
            }
        }
        if (self::$fsms_notify_admin == 1 && !$is_admin) {
            $fsms_base::send_comment_reply_sms($fsms_base::getAdminNumber(), self::$admin_pattern, $data);
        }
        if ($is_admin) {
            if (empty($mobile)) return false;
            if ($comment->comment_parent == 0) {
                return false;
            }
            $comment = get_comment($comment->comment_parent);
            $data = $this->comments_farazsms_shortcode($comment, $comment_id);
            $fsms_base::send_comment_reply_sms($mobile, self::$user_pattern, $data);
            $fsms_base::save_comment_mobile_to_phonebook($mobile);
        }
    }

    /**
     * 
     * Comments farazsms shortcode.
     *
     */

    public function comments_farazsms_shortcode($comment, $comment_id)
    {
        $post = get_post($comment->comment_post_ID);
        return [
            'title' => $post->post_title,
            'name' => $comment->comment_author,
            'email' => $comment->comment_author_email,
            //'link' => get_comment_link($comment_id),
            'link' => wp_get_shortlink($post->ID) . '#comment-' . $comment->comment_ID,
            'content' => $comment->comment_content,
            'parent' => $comment->comment_parent
        ];
    }

    /**
     * 
     * EDD show phone.
     *
     */

    public function fsms_edd_show_phone($payment_meta, $user_info)
    {
        $phone = isset($payment_meta['phone']) ? $payment_meta['phone'] : __('User not logged in', 'farazsms');
?>
        <li style="list-style: none; margin-top: 5px;">'<?php esc_attr_e('Phone number: ', 'farazsms') ?>'<?php echo $phone ?></li>
    <?php
    }

    /**
     * 
     * EDD complete purchase action.
     *
     */

    public function fsms_edd_complete_purchase_action($payment_id, $payment, $customer)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $payment_meta = edd_get_payment_meta($payment_id);
        $mobile = $payment_meta['phone'];
        $data = $this->fsms_get_edd_order_data($payment_meta);
        if (get_option('edd_send_to_user', 'false') === 'true') {
            $fsms_base::send_edd_sms($mobile, get_option('edd_user_pcode', ''), $data);
        }
        if (get_option('edd_send_to_admin', 'false') === 'true') {
            $fsms_base::send_edd_sms($fsms_base::getAdminNumber(), get_option('edd_admin_pcode', ''), $data);
        }
        $edd_phonebooks = get_option('edd_phonebooks_choice', []);
        if (!empty($edd_phonebooks)) {
            foreach ($edd_phonebooks as $phonebookID) {
                //$fsms_base::save_to_phonebook($mobile, $phonebookID);
                $datae[] = [
                    "number" => $mobile,
                    "name"   => '',
                    "phonebook_id" => (int) $phonebookID
                ];
                $fsms_base::save_to_phonebookv3($datae);
            }
        }
    }

    /**
     * 
     * Get edd order data.
     *
     */

    public function fsms_get_edd_order_data($payment_meta)
    {
        $result = null;
        $result['phone'] = $payment_meta['phone'];
        $result['email'] = $payment_meta['email'];
        $result['first_name'] = $payment_meta['user_info']['first_name'];
        $result['last_name'] = $payment_meta['user_info']['last_name'];

        for ($i = 0; $i < count($payment_meta['cart_details']); $i++) {
            if (isset($result['product']))
                $result['product'] .= ' - ' . $payment_meta['cart_details'][$i]['name'] . '(' . $payment_meta['cart_details'][$i]['quantity'] . ')';
            else $result['product'] = $payment_meta['cart_details'][$i]['name'] . '(' . $payment_meta['cart_details'][$i]['quantity'] . ')';
            if (isset($result['price']))
                $result['price'] = intval($result['price']) + intval($payment_meta['cart_details'][$i]['subtotal']);
            else   $result['price'] = intval($payment_meta['cart_details'][$i]['subtotal']);
            if (isset($result['discount']))
                $result['discount'] = intval($result['discount']) + intval($payment_meta['cart_details'][$i]['discount']);
            else   $result['discount'] = intval($payment_meta['cart_details'][$i]['discount']);
            if (isset($result['total_price']))
                $result['total_price'] = intval($result['total_price']) + intval($payment_meta['cart_details'][$i]['price']);
            else  $result['total_price'] = intval($payment_meta['cart_details'][$i]['price']);
        }

        for ($i = 0; $i < count($payment_meta['downloads']); $i++) {
            $files = edd_get_download_files($payment_meta['downloads'][$i]['id'], $variable_price_id = null);
            if ($files)
                foreach ($files as $key => $file)
                    if (!isset($result['link'])) $result['link'] .= $file['file'] . ' ';
                    else $result['link'] = $file['file'] . ' | ';
        }

        $result['price'] = number_format(intval($result['price']));
        $result['discount'] = number_format(intval($result['discount']));
        $result['total_price'] = number_format(intval($result['total_price']));

        return $result;
    }

    /**
     * 
     * Show mobile field checkout field.
     *
     */

    public function fsms_show_mobile_field_checkout_field()
    {
        $user = wp_get_current_user();
        $mobile = get_user_meta($user->ID, 'digits_phone')[0];
        if (FALSE !== strpos($mobile, '+98')) {
            $mobile = str_replace('+98', '0', $mobile);
        } else $mobile = '';
    ?>
        <p id="edd-phone-wrap">
            <label class="edd-label" for="edd-phone"><?php esc_attr_e('Phone number: ', 'farazsms') ?></label>
            <span class="edd-description">
                <?php esc_attr_e('We use this to send order information.', 'farazsms') ?>
            </span>
            <input class="edd-input" type="text" name="edd_phone" id="edd-phone" placeholder="<?php esc_attr_e('Phone number: ', 'farazsms') ?>" value="<?php echo $mobile ?>" />
        </p>
    <?php
    }

    /**
     * 
     * Show mobile meta.
     *
     */

    public function fsms_show_mobile_meta($payment_meta)
    {
        if (0 !== did_action('edd_pre_process_purchase')) {
            $payment_meta['phone'] = isset($_POST['edd_phone']) ? sanitize_text_field($_POST['edd_phone']) : '';
        }

        return $payment_meta;
    }

    /**
     * 
     * Show validate mobile field.
     *
     */

    public function fsms_validate_mobile_field($valid_data, $data)
    {
        if (empty($data['edd_phone']) or !preg_match("/^09[0-9]{9}$/", $data['edd_phone']))
            edd_set_error('empty_phone', __('Enter a valid phone number.'));
    }

    /**
     * 
     * Gravity Form post update entry.
     *
     */

    public function fsms_club_gform_post_update_entry($entry)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $fsc_gravity_forms_fields = get_option('fsms_gf_selected_field', []);
        $form_ids = array();
        $field_ids = array();
        foreach ($fsc_gravity_forms_fields as $field) {
            $exploded = explode("-", $field);
            $form_ids[] = $exploded[0];
            $field_ids[] = $exploded[1];
        }
        $form_id = $entry['form_id'];
        if (!in_array($form_id, $form_ids)) {
            return;
        }
        foreach ($field_ids as $field_id) {
            $value = $entry[$field_id];
            $phone = $fsms_base::validate_mobile_number($value);
            if (!$phone) {
                return;
            }
            if ($value !== null) {
                $woo_gf_books = get_option('fsms_gf_phone_books', []);
                foreach ($woo_gf_books as $phone_bookId) {
                    //$fsms_base::save_to_phonebook($phone, $phone_bookId);
                    $data[] = [
                        "number" => $phone,
                        "name"   => '',
                        "phonebook_id" => (int) $phone_bookId
                    ];
                    $fsms_base::save_to_phonebookv3($data);
                }
            }
        }
    }

    /**
     * 
     * Temp init kook.
     *
     */

    function temp_init_kook()
    {
        //print_r(wp_get_shortlink(50));
        return;
        //$fsms_base = class_farazsms_base::getInstance();
        //$fsms_base::save_to_phonebook(['09132789372','09038430716','09145236589'], '300536');
        //$this->fsms_first_notification_before_expire(FALSE, 5, null, 'before_expire');
        //$this->monitor_update_user_metadata(null, 5,'digits_phone', "09132789372");
        //$this->fsms_first_notification_before_expire(false, 5,'', "before_expire");
        $fsms_base = class_farazsms_base::getInstance();
        $fsms_base::send_welcome_message("09038430716", 31);
    }

    /**
     * 
     * Farazsms newsletter.
     *
     */

    public function farazsms_newsletter($atts = array())
    {
        wp_enqueue_style('farazsms-newsletter');
        wp_enqueue_script('farazsms-newsletter');
        wp_localize_script(
            'farazsms-newsletter',
            'fsms_ajax_object',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
        $newsletter_send_ver_code = get_option('fsms_newsletter_send_ver_code', 'false');
        return '<div id="fsms_newsletter">
                  <form id="fsms_newsletter_form">
                    <div class="fsms_newsletter_input a">
                      <input id="fsms_newsletter_name" type="text" class="fsms_newsletter_text" placeholder=""نام و نام خانوادگی"">
                    </div>
                    <div class="fsms_newsletter_input a">
                      <input id="fsms_newsletter_mobile" type="text" class="fsms_newsletter_text" placeholder="شماره موبایل">
                    </div>
                    <div class="fsms_newsletter_input b" style="display: none;">
                      <input id="fsms_newsletter_verify_code" type="text" class="fsms_newsletter_text" placeholder="کد تایید">
                    </div>
                    <input id="newsletter_send_ver_code" type="hidden" value="' . $newsletter_send_ver_code . '">
                  </form>
                    <div id="fsms_newsletter_message" style="display: none;">
                    </div>
                    <div class="fsms_newsletter_submit">
                      <button id="fsms_newsletter_submit_button" class="fsms_newsletter_button"><span class="button__text">اشتراک</span></button>
                    </div>
                    <div id="fsms_newsletter_completion" class="fsms_newsletter_submit" style="display: none;">
                      <button id="fsms_newsletter_submit_code" class="fsms_newsletter_button"><span class="button__text">ارسال کد</span></button>
                      <button id="fsms_newsletter_resend_code" class="fsms_newsletter_button"><span class="button__text">ارسال مجدد کد</span></button>
                    </div>
                </div>';
    }

    /**
     * 
     * Newsletter send verification code
     *
     */

    public function fsms_newsletter_send_verification_code()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $mobile = $_POST['mobile'];
        $name = $_POST['name'];
        $newsletter_send_ver_code = get_option('fsms_newsletter_send_ver_code', 'false');
        if ($fsms_base::check_if_phone_already_exist($mobile)) {
            wp_send_json_error();
        }
        if ($newsletter_send_ver_code == "false") {
            $data = array(
                'phone' => $mobile,
                'name' => $name,
            );
            $fsms_base::save_subscriber_to_db($data);
            $newsletter_phonebooks = get_option('fsms_newsletter_phonebooks', []);
            foreach ($newsletter_phonebooks as $phonebookID) {
                //$fsms_base::save_to_phonebook($mobile, $phonebookID);
                $datap[] = [
                    "number" => $mobile,
                    "name"   => $name,
                    "phonebook_id" => (int) $phonebookID
                ];
                $fsms_base::save_to_phonebookv3($datap);
            }
            $fsms_base::send_newsletter_welcome_message($mobile, $name);
            wp_send_json_success();
        } else {
            $generated_code = rand(1000, 9999);
            $fsms_base::save_generated_code_to_db($mobile, $generated_code);
            $data = array(
                "code" => $generated_code,
                "name" => $name
            );
            $fsms_base::send_newsletter_verification_code($mobile, $data);
            wp_send_json_success();
        }
    }

    /**
     * 
     * Add phone to newsletter.
     *
     */

    public function fsms_add_phone_to_newsletter()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $code = $_POST['code'];
        $name = $_POST['name'];
        $mobile = $_POST['mobile'];
        $is_valid = $fsms_base::check_if_code_is_valid($mobile, $code);
        if ($is_valid) {
            $data = array(
                'phone' => $mobile,
                'name' => $name
            );
            $fsms_base::save_subscriber_to_db($data);
            $newsletter_phonebooks = get_option('fsms_newsletter_phonebooks', []);
            foreach ($newsletter_phonebooks as $phonebookID) {
                //$fsms_base::save_to_phonebook($mobile, $phonebookID);
                $datap[] = [
                    "number" => $mobile,
                    "name"   => $name,
                    "phonebook_id" => (int) $phonebookID
                ];
                $fsms_base::save_to_phonebookv3($datap);
            }
            $fsms_base::send_newsletter_welcome_message($mobile, $name);
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    /**
     * 
     * Publish post notification.
     *
     */

    public function fsms_publish_post_notification($post_id)
    {
        $newsletter_new_post_notification = get_option('fsms_newsletter_new_post_notification', 'false');
        $newsletter_post_notification_message = get_option('fsms_newsletter_post_notification_message', '');
        if ($newsletter_new_post_notification == "false" || empty($newsletter_post_notification_message)) {
            return;
        }
        $notification_message = str_replace(array(
            '%title%',
            '%url%'
        ), array(
            get_the_title($post_id),
            wp_get_shortlink($post_id)
        ), $newsletter_post_notification_message);
        $fsms_base = class_farazsms_base::getInstance();
        $subscribers = $fsms_base::get_subscribers();
        $phones = [];
        foreach ($subscribers as $subscriber) {
            $phones[] = $subscriber->phone;
        }
        $fsms_base::send_message($phones, $notification_message);
        remove_action('publish_post', 'fsms_publish_post_notification', 10);
    }

    /**
     * 
     * Product published.
     *
     */

    public function fsms_product_published($new_status, $old_status, $post)
    {
        $post_type = get_post_type($post);
        if ($post_type !== "product") {
            return;
        }
        if ($new_status === 'publish' && $new_status !== $old_status) {
            $newsletter_new_prodcut_notification = get_option('fsms_newsletter_new_product_notification', 'false');
            $newsletter_prodcut_notification_message = get_option('fsms_newsletter_product_notification_message', '');
            if ($newsletter_new_prodcut_notification === "false" || empty($newsletter_prodcut_notification_message)) {
                return;
            }
            $product = wc_get_product($post->ID);
            $notification_message = str_replace(array(
                '%site_title%',
                '%product_name%',
                '%price%',
                '%url%'
            ), array(
                wp_title(),
                $product->get_name(),
                $product->get_price(),
                wp_get_shortlink($post->ID)
            ), $newsletter_prodcut_notification_message);
            $fsms_base = class_farazsms_base::getInstance();
            $subscribers = $fsms_base::get_subscribers();
            $phones = [];
            foreach ($subscribers as $subscriber) {
                $phones[] = $subscriber->phone;
            }
            $fsms_base::send_message($phones, $notification_message);
        }
    }

    /**
     * 
     * Admin login action.
     * 
     */

    public function fsms_admin_login_action($user_login, $user)
    {
        if (!in_array('administrator', (array) $user->roles)) {
            return;
        }
        $last_notification = get_user_meta($user->ID, 'faraz_low_credit_noti_sent_timestamp', TRUE);
        if (!empty($last_notification)) {
            $dif = time() - $last_notification;
            if ($dif < 86400) {
                return;
            }
        }
        $fsms_base = class_farazsms_base::getInstance();
        $credit = $fsms_base::get_credit();
        if (!$credit) {
            return;
        }
        if ((int)$credit < 10000) {
            $fsms_base::send_admin_low_credit_to_admin();
            update_user_meta($user->ID, 'faraz_low_credit_noti_sent_timestamp', time());
        }
    }

    /**
     * 
     * Admin rules login action
     * 
     */


    public function fsms_admin_roles_login_action($user_login, $user)
    {
        $credentials_option = get_option('fsms_credentials');
        $admin_login_noti_roles = $credentials_option['fsms_admin_login_noti_roles'];
        if (empty($admin_login_noti_roles)) {
            return;
        }
        if (empty(array_intersect($admin_login_noti_roles, $user->roles))) {
            return;
        }
        $credentials_option = get_option('fsms_credentials');
        $admin_login_noti = $credentials_option['fsms_admin_login_noti'];
        $admin_login_noti_p = $credentials_option['fsms_admin_login_noti_p'];
        if ($admin_login_noti == "false" || empty($admin_login_noti_p)) {
            return;
        }
        $data['date'] = date_i18n('H:i:s d-m-Y');
        $data['user_login'] = $user->user_login;
        $data['display_name'] = $user->display_name;
        $fsms_base = class_farazsms_base::getInstance();
        $fsms_base::send_admins_login_notification_to_superadmin($admin_login_noti_p, $data);
    }

    /**
     * 
     * Send timed message.
     * 
     */


    public function woo_send_timed_message($order_id)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $order = wc_get_order($order_id);
        $phone = $order->get_billing_phone();
        $products = [];
        /*
         * @todo need to fix this or have another approach
         */
        foreach ($order->get_items() as $item_id => $item) {
            $products[$item->get_product_id()] = $item->get_total();
        }
        $most_expensive_product_id = max(array_keys($products));
        $product = wc_get_product($most_expensive_product_id);
        $data["item"] = $product->get_title();
        $data["item_link"] = get_permalink($most_expensive_product_id);
        $fsms_base::send_timed_message($phone, $data, $order->get_date_created());
    }

    /**
     * 
     * Woocommerce retention action.
     * 
     */


    public function fsms_woo_retention_action()
    {
        $retention_order_no = get_option('fsms_woo_retention_order_no', '');
        $retention_order_month = get_option('fsms_woo_retention_order_month', '');
        $retention_message = get_option('fsms_woo_retention_message', '');
        if (empty($retention_order_no) || empty($retention_order_month) || empty($retention_message)) {
            return;
        }
        $fsms_base = class_farazsms_base::getInstance();
        global $wpdb;
        $customer_ids = $wpdb->get_col("SELECT DISTINCT meta_value  FROM $wpdb->postmeta WHERE meta_key = '_customer_user' AND meta_value > 0");
        if (sizeof($customer_ids) > 0) {
            foreach ($customer_ids as $customer_id) {
                $customer = new WC_Customer($customer_id);
                $last_order = $customer->get_last_order();
                if (!$last_order) {
                    continue;
                }
                $sent_retention_message = get_post_meta($last_order->get_id(), 'sent_retention_message', TRUE);
                if ($sent_retention_message == '1') {
                    continue;
                }
                $date_completed = $last_order->get_date_completed();
                if (!empty($date_completed) && $date_completed->getTimestamp() <= strtotime("-" . $retention_order_month . " Months")) {
                    $args = array(
                        'type' => 'shop_order',
                        'customer_id' => $customer_id,
                        'date_completed' => '<=' . strtotime("-" . $retention_order_month . " Months"),
                    );
                    $orders = wc_get_orders($args);
                    if (count($orders) >= $retention_order_no) {
                        $message = str_replace(array(
                            '%billing_full_name%',
                            '%shipping_full_name%',
                        ), array(
                            $last_order->get_formatted_billing_full_name(),
                            $last_order->get_formatted_shipping_full_name(),
                        ), $retention_message);
                        $fsms_base::send_message([$last_order->get_billing_phone()], $message);
                        update_post_meta($last_order->get_id(), 'sent_retention_message', '1');
                    }
                }
            }
        }
    }

    /**
     * 
     * First notification before expire
     * 
     */

    public function fsms_first_notification_before_expire($sent = false, $uid = 0, $lid = 0, $type = '')
    {
        $types = [];
        $types[] = (get_option('fsms_ihc_send_first_noti_sms', 'false') == "true") ? 'before_expire' : '';
        $types[] = (get_option('fsms_ihc_send_second_noti_sms', 'false') == "true") ? 'second_before_expire' : '';
        $types[] = (get_option('fsms_ihc_send_third_noti_sms', 'false') == "true") ? 'third_before_expire' : '';
        $first_noti_sms_message = get_option('fsms_first_noti_sms_message', '');
        if (empty($first_noti_sms_message) || !in_array($type, $types)) {
            return $sent;
        }
        $phone = get_user_meta($uid, "digits_phone", TRUE);
        if (empty($phone)) {
            return $sent;
        }
        $user = get_userdata($uid);
        $fsms_base = class_farazsms_base::getInstance();
        $message = str_replace(array(
            '%name%',
            '%time%',
        ), array(
            $user->display_name,
            get_option("ihc_notification_before_time", "5"),
        ), $first_noti_sms_message);
        $fsms_base::send_message([$phone], $message, "+98club");
        return $sent;
    }

    /**
     * 
     * AFF-WP register user.
     * 
     */

    public function fsms_affwp_register_user($affiliate_id, $status, $args)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $user_id = affwp_get_affiliate_user_id($affiliate_id);
        $user = get_user_by('id', $user_id);
        $user_mobile = $_POST['affs-user-mobile'] ?? '';
        update_user_meta($user->ID, 'affs_mobile', $user_mobile);
        $args['user_mobile'] = $user_mobile;
        if (!isset($args['user_nicename'])) $args['user_nicename'] = $user->nickname;
        //affs_add_mobile_book_number($user_mobile);
        if (get_option('fsms_aff_user_register', 'false') == 'true') {
            $user_register_pattern = get_option('fsms_aff_user_register_message', '');
            if (!empty($user_register_pattern)) {
                $fsms_base::affs_send_sms($user_mobile, $user_register_pattern, $args);
            }
        }
        if (get_option('fsms_aff_admin_user_register', 'false') == 'true') {
            $user_register_pattern = get_option('fsms_aff_admin_user_register_message', '');
            $admin_mobile = $fsms_base::getAdminNumber();
            if (!empty($user_register_pattern) && !empty($admin_mobile)) {
                $fsms_base::affs_send_sms($admin_mobile, $user_register_pattern, $args);
            }
        }
    }

    /**
     * 
     * Yith WooAF register user
     * 
     */

    public function fsms_yith_wcaf_register_user($id)
    {
        $fsms_base = class_farazsms_base::getInstance();

        $affiliate_info = get_affiliate_by_id($id);
        $user = get_user_by('id', $affiliate_info->user_id);

        $mobile = get_user_meta($user->ID, 'digits_phone_no')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($user->ID, 'wupp_mobile')[0] ?? '';
        $field_custom = get_option('fsms_aff_user_mobile_field', []);
        if (empty($mobile) && !empty($field_custom)) {
            foreach ($field_custom as $mobile_field) {
                $mobile = get_user_meta($user->ID, $mobile_field, TRUE);
                if (!empty($mobile) && class_farazsms_base::validate_mobile_number($mobile)) {
                    break;
                }
            }
        }

        $args['user_mobile'] = $mobile;

        if (!isset($args['user_nicename'])) $args['user_nicename'] = $user->nickname;
        if (get_option('fsms_aff_user_register', 'false') == 'true') {
            $user_register_pattern = get_option('fsms_aff_user_register_message', '');
            if (!empty($user_register_pattern)) {
                $fsms_base::affs_send_sms($mobile, $user_register_pattern, $args);
            }
        }
        if (get_option('fsms_aff_admin_user_register', 'false') == 'true') {
            $user_register_pattern = get_option('fsms_aff_admin_user_register_message', '');
            $admin_mobile = $fsms_base::getAdminNumber();
            if (!empty($user_register_pattern) && !empty($admin_mobile)) {
                $fsms_base::affs_send_sms($admin_mobile, $user_register_pattern, $args);
            }
        }
    }

    /**
     * 
     * UAP register user.
     * 
     */

    public function fsms_uap_register_user($user_id)
    {
        $fsms_base = class_farazsms_base::getInstance();

        $user = get_user_by('id', $user_id);
        $mobile = get_user_meta($user_id, 'digits_phone_no')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($user_id, 'wupp_mobile')[0] ?? '';
        $field_custom = get_option('fsms_aff_user_mobile_field', []);
        if (empty($mobile) && !empty($field_custom)) {
            foreach ($field_custom as $mobile_field) {
                $mobile = get_user_meta($user_id, $mobile_field, TRUE);
                if (!empty($mobile) && class_farazsms_base::validate_mobile_number($mobile)) {
                    break;
                }
            }
        }

        $args['user_mobile'] = $mobile;

        if (!isset($args['user_nicename'])) $args['user_nicename'] = $user->nickname;
        if (get_option('fsms_aff_user_register', 'false') == 'true') {
            $user_register_pattern = get_option('fsms_aff_user_register_message', '');
            if (!empty($user_register_pattern)) {
                $fsms_base::affs_send_sms($mobile, $user_register_pattern, $args);
            }
        }
        if (get_option('fsms_aff_admin_user_register', 'false') == 'true') {
            $user_register_pattern = get_option('fsms_aff_admin_user_register_message', '');
            $admin_mobile = $fsms_base::getAdminNumber();
            if (!empty($user_register_pattern) && !empty($admin_mobile)) {
                $fsms_base::affs_send_sms($admin_mobile, $user_register_pattern, $args);
            }
        }
    }

    /**
     * 
     * AFF-WP set affiliate status
     * 
     */

    public function fsms_affwp_set_affiliate_status($affiliate_id = 0, $status = '', $old_status = '')
    {
        if (doing_action('affwp_add_affiliate')) return;
        if (doing_action('affwp_affiliate_register')) return;
        $fsms_base = class_farazsms_base::getInstance();
        $user_id = affwp_get_affiliate_user_id($affiliate_id);
        $user = get_user_by('id', $user_id);
        $data['user_login'] = $user->user_login;
        $data['user_nicename'] = $user->nickname;
        $data['user_email'] = $user->user_email;
        $data['display_name'] = $user->display_name;
        $mobile = get_user_meta($user_id, 'affs_mobile')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($user_id, 'digits_phone_no')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($user_id, 'wupp_mobile')[0] ?? '';
        $field_custom = get_option('fsms_aff_user_mobile_field', []);
        if (empty($mobile) && !empty($field_custom)) {
            foreach ($field_custom as $mobile_field) {
                $mobile = get_user_meta($user_id, $mobile_field, TRUE);
                if (!empty($mobile) && class_farazsms_base::validate_mobile_number($mobile)) {
                    break;
                }
            }
        }
        if (empty($mobile)) {
            return;
        }
        if (substr($mobile, 0, 1) === '9') $mobile = '0' . $mobile;
        $data['user_mobile'] = $mobile;
        if (get_option('fsms_aff_user_on_approval', 'false') == 'true') {
            $aff_user_on_approval_pattern = get_option('fsms_aff_user_on_approval_message', '');
            if (!empty($user)) {
                $fsms_base::affs_send_sms($mobile, $aff_user_on_approval_pattern, $data);
            }
        }
        if (get_option('fsms_aff_admin_user_on_approval', 'false') == 'true') {
            $aff_admin_user_on_approval_pattern = get_option('fsms_aff_admin_user_on_approval_message', '');
            $admin_mobile = $fsms_base::getAdminNumber();
            if (!empty($aff_admin_user_on_approval_pattern) && !empty($admin_mobile)) {
                $fsms_base::affs_send_sms($admin_mobile, $aff_admin_user_on_approval_pattern, $data);
            }
        }
    }

    /**
     * 
     * Yith WooAF set affiliate status.
     * 
     */

    public function fsms_yith_wcaf_set_affiliate_status($affiliate_id)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $affiliate_info = get_affiliate_by_id($affiliate_id);
        $user = get_user_by('id', $affiliate_info->user_id);
        $data['user_login'] = $user->user_login;
        $data['user_nicename'] = $user->nickname;
        $data['user_email'] = $user->user_email;
        $data['display_name'] = $user->display_name;
        $mobile = get_user_meta($affiliate_info->user_id, 'affs_mobile')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($affiliate_info->user_id, 'digits_phone_no')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($affiliate_info->user_id, 'wupp_mobile')[0] ?? '';
        $field_custom = get_option('fsms_aff_user_mobile_field', []);
        if (empty($mobile) && !empty($field_custom)) {
            foreach ($field_custom as $mobile_field) {
                $mobile = get_user_meta($affiliate_info->user_id, $mobile_field, TRUE);
                if (!empty($mobile) && class_farazsms_base::validate_mobile_number($mobile)) {
                    break;
                }
            }
        }
        if (empty($mobile)) {
            return;
        }
        if (substr($mobile, 0, 1) === '9') $mobile = '0' . $mobile;
        $data['user_mobile'] = $mobile;
        if (get_option('fsms_aff_user_on_approval', 'false') == 'true') {
            $aff_user_on_approval_pattern = get_option('fsms_aff_user_on_approval_message', '');
            if (!empty($user)) {
                $fsms_base::affs_send_sms($mobile, $aff_user_on_approval_pattern, $data);
            }
        }
        if (get_option('fsms_aff_admin_user_on_approval', 'false') == 'true') {
            $aff_admin_user_on_approval_pattern = get_option('fsms_aff_admin_user_on_approval_message', '');
            $admin_mobile = $fsms_base::getAdminNumber();
            if (!empty($aff_admin_user_on_approval_pattern) && !empty($admin_mobile)) {
                $fsms_base::affs_send_sms($admin_mobile, $aff_admin_user_on_approval_pattern, $data);
            }
        }
    }

    /**
     * 
     * AFF-WP referral accepted
     * 
     */

    public function fsms_affwp_referral_accepted($affiliate_id, $referral)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $referral = (array)$referral;
        $user_id = affwp_get_affiliate_user_id($affiliate_id);
        $user = get_user_by('id', $user_id);
        $data['user_login'] = $user->user_login;
        $data['user_nicename'] = $user->nickname;
        $data['user_email'] = $user->user_email;
        $data['display_name'] = $user->display_name;
        $mobile = get_user_meta($user_id, 'digits_phone_no')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($user_id, 'wupp_mobile')[0] ?? '';
        if ($mobile == '') $mobile = get_user_meta($user_id, 'affs_mobile')[0] ?? '';
        $field_custom = get_option('fsms_aff_user_mobile_field', []);
        if (empty($mobile) && !empty($field_custom)) {
            foreach ($field_custom as $mobile_field) {
                $mobile = get_user_meta($user_id, $mobile_field, TRUE);
                if (!empty($mobile) && class_farazsms_base::validate_mobile_number($mobile)) {
                    break;
                }
            }
        }
        if (empty($mobile)) {
            return;
        }
        $data['user_mobile'] = $mobile;
        $data['amount'] = $referral['amount'];
        if (get_option('fsms_aff_user_new_ref', 'false') == 'true') {
            $aff_user_new_ref_message = get_option('fsms_aff_user_new_ref_message', '');
            if (!empty($aff_user_new_ref_message)) {
                $fsms_base::affs_send_sms($mobile, $aff_user_new_ref_message, $data);
            }
        }
        if (get_option('fsms_aff_admin_user_new_ref', 'false') == 'true') {
            $aff_admin_user_new_ref_message = get_option('fsms_aff_admin_user_new_ref_message', '');
            $admin_mobile = $fsms_base::getAdminNumber();
            if (!empty($aff_admin_user_new_ref_message) && !empty($admin_mobile)) {
                $fsms_base::affs_send_sms($admin_mobile, $aff_admin_user_new_ref_message, $data);
            }
        }
    }

    /**
     * 
     * AFF-WP register fields before tos
     * 
     */

    public function fsms_affwp_register_fields_before_tos()
    {
        if (is_user_logged_in()) {
            $mobile = get_user_meta(get_current_user_id(), 'digits_phone_no', TRUE) ?? '';
            if ($mobile == '')
                $mobile = get_user_meta(get_current_user_id(), 'wupp_mobile', TRUE) ?? '';
            if (substr($mobile, 0, 1) === '9') $mobile = '0' . $mobile;
        }
    ?>
        <p>
            <label for="affs-user-mobile"><?php esc_attr_e('Phone number', 'farazsms') ?></label>
            <input id="affs-user-mobile" type="text" name="affs-user-mobile" title="<?php esc_attr_e('Phone number', 'farazsms') ?>" required value="<?php echo $mobile ?? ''; ?>" />
        </p>
    <?php
    }

    /**
     * 
     * AFF-WP new affiliate end
     * 
     */

    public function fsms_affwp_new_affiliate_end()
    {
    ?>
        <tr class="form-row form-required">
            <th scope="row">
                <label for="affs-user-mobile"><?php esc_attr_e('Phone number', 'farazsms') ?></label>
            </th>
            <td>
                <input id="affs-user-mobile" type="text" name="affs-user-mobile" title="<?php esc_attr_e('Phone number', 'farazsms') ?>" required />
            </td>

        </tr>
    <?php
    }

    /**
     * 
     * AFF-WP edit affiliate end
     * 
     */

    public function fsms_affwp_edit_affiliate_end($affiliate)
    {
    ?>
        <tr class="form-row form-required">
            <th scope="row">
                <label for="affs-user-mobile"><?php esc_attr_e('Phone number', 'farazsms') ?></label>
            </th>
            <td>
                <input id="affs-user-mobile" type="text" name="affs-user-mobile" title="<?php esc_attr_e('Phone number', 'farazsms') ?>" value="<?php echo get_user_meta($affiliate->user_id, 'affs_mobile')[0] ?? ''; ?>" />
            </td>
        </tr>
<?php
    }

    /**
     * 
     * AFF-WP update affiliate.
     * 
     */

    public function fsms_affwp_update_affiliate($data)
    {
        if (isset($data['affs-user-mobile'])) {
            $user_id = affwp_get_affiliate_user_id($data['affiliate_id']);
            $user = get_user_by('id', $user_id);
            $mobile = $data['affs-user-mobile'] ?? '';
            update_user_meta($user->ID, 'affs_mobile', $mobile);
            affs_add_mobile_book_number($mobile);
        }
    }

    /**
     * 
     * Pre populate checkout fields.
     * 
     */

    public function fsms_pre_populate_checkout_fields($input, $key)
    {
        global $current_user;
        $digits_mobile = get_user_meta($current_user->ID, 'digits_phone_no', TRUE);
        switch ($key):
            case 'billing_first_name':
            case 'shipping_first_name':
                return $current_user->first_name;
                break;

            case 'billing_last_name':
            case 'shipping_last_name':
                return $current_user->last_name;
                break;
            case 'billing_phone':
                return !empty($digits_mobile) ? '0' . $digits_mobile : '';
                break;

        endswitch;
    }

    /**
     * 
     * Gravity form pre submission.
     * 
     */

    public function fsms_gf_pre_submission($form)
    {
        foreach ($_POST as $name => $value) {
            $_POST[$name] = class_farazsms_base::fsms_tr_num($value);
        }
    }

    /**
     * 
     * Digits filter mobile.
     * 
     */

    public function fsms_digits_filter_mobile($mobile)
    {
        return class_farazsms_base::fsms_tr_num($mobile);
    }

    /**
     * 
     * Check remaining days.
     * 
     */

    public function fsms_check_remaining_days()
    {
        $panel_expire_date = get_option('fsms_panel_expire_date', '');
        if (empty($panel_expire_date)) {
            return;
        }
        $future = strtotime($panel_expire_date);
        $timefromdb = time();
        $timeleft = $future - $timefromdb;
        $daysleft = round((($timeleft / 24) / 60) / 60);
        if (!is_numeric($daysleft)) {
            return;
        }
        if ($daysleft > 30) {
            delete_option('sent_low_remaining_days_30');
            delete_option('sent_low_remaining_days_7');
            return;
        }
        if ($daysleft > 20 && $daysleft < 30) {
            $already_sent = get_option('sent_low_remaining_days_30', '');
            if ($already_sent === '1') {
                return;
            }
            $fsms_base = class_farazsms_base::getInstance();
            $admin_mobile_number = $fsms_base::getAdminNumber();
            $message = __('Dear user, your panel will expire less than a month from now. To renew your SMS panel, contact Faraz SMS', 'farazsms');
            $fsms_base::send_message([$admin_mobile_number], $message, '+98club');
            update_option('sent_low_remaining_days_30', '1');
        } elseif ($daysleft > 1 && $daysleft < 7) {
            $already_sent = get_option('sent_low_remaining_days_7', '');
            if ($already_sent == '1') {
                return;
            }
            $fsms_base = class_farazsms_base::getInstance();
            $admin_mobile_number = $fsms_base::getAdminNumber();
            $message = __('Dear user, your panel will expire less than a week from now. To renew your SMS panel, contact Faraz SMS.', 'farazsms');
            $result = $fsms_base::send_message([$admin_mobile_number], $message, '+98club');
            update_option('sent_low_remaining_days_7', '1');
        }
    }

    /**
     * 
     * PMP membership membership expiry
     * 
     */

    public function fsms_pmp_membership_membership_expiry($user_id, $membership_id)
    {
        $pmp_send_expire_noti_sms = get_option('fsms_pmp_send_expire_noti_sms', 'false');
        $expire_noti_sms_message = get_option('fsms_pmp_expire_noti_sms_message', '');
        if ($pmp_send_expire_noti_sms === 'false' || empty($expire_noti_sms_message)) {
            return;
        }
        $fsms_base = class_farazsms_base::getInstance();
        $phone = get_user_meta($user_id, "digits_phone", TRUE);
        $selected_meta_keys = get_option('fsms_custom_phone_meta_keys', []);
        if (empty($phone) && !empty($selected_meta_keys)) {
            foreach ($selected_meta_keys as $meta) {
                $phone = get_user_meta($user_id, $meta, TRUE);
                if (!empty($phone) && $fsms_base::validate_mobile_number($phone)) {
                    break;
                }
            }
        }
        if (empty($phone)) {
            return;
        }
        $user = get_userdata($user_id);
        $message = str_replace(array(
            '%display_name%',
        ), array(
            $user->display_name,
        ), $expire_noti_sms_message);
        $fsms_base::send_message([$phone], $message, "+98club");
    }

    /**
     * 
     * Woocommerce checkout fields.
     * 
     */

    public function fsms_woocommerce_checkout_fields($fields)
    {
        $woo_checkout_otp = get_option('fsms_woo_checkout_otp', 'false');

        if ($woo_checkout_otp !== 'true') {
            return $fields;
        }

        $fields['billing_phone_send_otp'] = array(
            'label' => __('Verification code', 'farazsms'),
            'required' => '0',
            'type' => 'text',
            'class' => array(
                'form-row-wide',
                'fsms_otp_field'
            ),
            'priority' => 101
        );
        $fields['billing_phone_otp'] = array(
            'label' => __('Verification code', 'farazsms'),
            'required' => '1',
            'type' => 'number',
            'class' => array(
                'form-row-first',
                'fsms_otp_field'
            ),
            'priority' => 102
        );
        $fields['billing_phone_otp_button'] = array(
            'label' => __('Send', 'farazsms'),
            'required' => '0',
            'class' => array(
                'form-row-last',
                'fsms_otp_field_should_remove'
            ),
            'priority' => 103
        );
        return $fields;
    }

    /**
     * 
     * Woocommerce checkout process.
     * 
     */

    public function fsms_woocommerce_checkout_process()
    {
        $woo_checkout_otp = get_option('fsms_woo_checkout_otp', 'false');
        if ($woo_checkout_otp !== 'true') {
            return;
        }

        if (!$_POST['billing_phone_otp']) wc_add_notice(__('Please confirm your phone number first', 'farazsms'), 'error');
        $otp = $_POST['billing_phone_otp'];
        $fsms_base = class_farazsms_base::getInstance();
        $is_valid = $fsms_base::check_if_code_is_valid_for_woo($_POST['billing_phone'], $otp);
        if (!$is_valid) {
            wc_add_notice(__('The verification code entered is not valid', 'farazsms'), 'error');
        }
    }

    /**
     * 
     * Send OTP Code.
     * 
     */

    public function fsms_send_otp_code()
    {
        $fsms_base = class_farazsms_base::getInstance();
        $mobile = $_POST['mobile'];
        if (!isset($mobile)) {
            wp_send_json_error(__('Please enter phone number.', 'farazsms'));
        }
        $generated_code = rand(1000, 9999);
        $fsms_base::save_generated_code_to_db($mobile, $generated_code);
        $data = array(
            "code" => $generated_code,
        );
        $result = $fsms_base::send_woocommerce_verification_code($mobile, $data);
        if (!$result) {
            wp_send_json_error(__('An error occurred', 'farazsms'));
        } else {
            wp_send_json_success(__('Verification code sent successfully', 'farazsms'));
        }
    }

    /**
     * 
     * Delete OTP Code.
     * 
     */

    public function fsms_delete_otp_code($order_id)
    {
        $fsms_base = class_farazsms_base::getInstance();
        $order = wc_get_order($order_id);
        $fsms_base::delete_code_for_woo($order->get_billing_phone());
    }
}
