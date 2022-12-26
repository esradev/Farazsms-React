<?php

/**
 * @link       https://farazsms.com/
 * @since      1.0.8
 *
 * @package    Farazsms
 * @subpackage Farazsms/includes
 * 
 */

/**
 * 
 * Load IPPanel autoload file.
 * 
 */

require 'vendor/autoload.php';

use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;


/**
 * 
 * Initialize the class and set its properties.
 * 
 */
class class_farazsms_base
{
    private static $_instance = null;
    private static $username;
    private static $password;
    private static $admin_number;
    private static $fromNum;
    private static $fromNumAdver;
    private static $apiKey;
    private static $sendwm;
    private static $sendwm_with_pattern;
    private static $welcome_message;
    private static $welcomep;
    private static $_woo_installed = false;
    private static $_digits_installed = false;
    private static $_edd_installed = false;
    private static $_bookly_installed = false;
    private static $_GF_installed = false;
    private static $_ihc_installed = false;
    private static $_pmp_installed = false;
    private static $_aff_installed = false;
    private static $_uap_installed = false;
    private static $_wcaf_installed = false;

    private static $comment_phone_book;

    public function __construct()
    {
        /**
         * 
         * Init credential options.
         * 
         */

        $credentials_option = json_decode(get_option('farazsms_settings_options'), true);
        if ($credentials_option) {
            $fsms_uname = $credentials_option['username'];
            $fsms_password = $credentials_option['password'];
            $admin_number = $credentials_option['admin_number'];
            $fsms_apikey = $credentials_option['apikey'];
            $fsms_fromnum = $credentials_option['from_number'];
            $fsms_fromnum_adver = $credentials_option['from_number_adver'];

            if ($fsms_uname && $fsms_password && $fsms_fromnum) {
                self::$username = self::fsms_tr_num($fsms_uname);
                self::$password = self::fsms_tr_num($fsms_password);
                self::$admin_number = self::fsms_tr_num($admin_number);
                self::$fromNum = self::fsms_tr_num($fsms_fromnum);
                self::$fromNumAdver = self::fsms_tr_num($fsms_fromnum_adver);
            }
            self::$apiKey = $fsms_apikey;
        }

        $login_notify_options = json_decode(get_option('farazsms_login_notify_options'), true);
        if ($login_notify_options) {
            $fsms_sendwm = $login_notify_options['welcome_sms'];
            $fsms_welcomep = $login_notify_options['welcome_sms_p'];
            $fsms_sendwm_with_pattern = $login_notify_options['welcome_sms_use_p'];
            $fsms_welcome_message = $login_notify_options['welcome_sms_msg'];
            if ($fsms_uname && $fsms_password && $fsms_fromnum) {
                self::$sendwm = $fsms_sendwm === 'true';
                self::$welcomep = self::fsms_tr_num($fsms_welcomep);
                self::$sendwm_with_pattern = $fsms_sendwm_with_pattern === 'true';
                self::$welcome_message = self::fsms_tr_num($fsms_welcome_message);
            }
        }

        $comments_options = json_decode(get_option('farazsms_comments_options'), true);
        if ($comments_options) {
            self::$comment_phone_book = $comments_options['comment_phone_book'];
        }

        /**
         * 
         * Check is Woocommerce, Digits, Bookly and some other plugins are installed and activated.
         * 
         * And call Main functions.
         */

        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

        if (in_array('woocommerce/woocommerce.php', $active_plugins)) {
            self::$_woo_installed = true;
        }

        if (function_exists('digit_ready')) {
            self::$_digits_installed = true;
        }

        if (in_array('easy-digital-downloads/easy-digital-downloads.php', $active_plugins)) {
            self::$_edd_installed = true;
        }

        if (in_array('bookly-responsive-appointment-booking-tool/main.php', $active_plugins)) {
            self::$_bookly_installed = true;
        }

        if (in_array('gravityforms/gravityforms.php', $active_plugins)) {
            self::$_GF_installed = true;
        }

        if (in_array('indeed-membership-pro/indeed-membership-pro.php', $active_plugins)) {
            self::$_ihc_installed = true;
        }

        if (in_array('paid-memberships-pro/paid-memberships-pro.php', $active_plugins)) {
            self::$_pmp_installed = true;
        }

        if (in_array('affiliate-wp/affiliate-wp.php', $active_plugins)) {
            self::$_aff_installed = true;
        }

        if (in_array('indeed-affiliate-pro/indeed-affiliate-pro.php', $active_plugins)) {
            self::$_uap_installed = true;
        }

        if (in_array('yith-woocommerce-affiliates/init.php', $active_plugins)) {
            self::$_wcaf_installed = true;
        }
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get_service_sender_number()
    {
        return self::$fromNum;
    }

    public function get_adver_sender_number()
    {
        return self::$fromNumAdver;
    }

    public static function isDigitsInstalled()
    {
        return self::$_digits_installed;
    }

    public static function isWooInstalled()
    {
        return self::$_woo_installed;
    }

    public static function isEddInstalled()
    {
        return self::$_edd_installed;
    }

    public static function isBooklyInstalled()
    {
        return self::$_bookly_installed;
    }

    public static function isGFInstalled()
    {
        return self::$_GF_installed;
    }

    public static function isIHCInstalled()
    {
        return self::$_ihc_installed;
    }

    public static function isPMPInstalled()
    {
        return self::$_pmp_installed;
    }

    public static function isAFFInstalled()
    {
        return self::$_aff_installed;
    }

    public static function isUAPInstalled()
    {
        return self::$_uap_installed;
    }

    public static function isWCAFInstalled()
    {
        return self::$_wcaf_installed;
    }


    public static function getAdminNumber()
    {
        return self::$admin_number;
    }

    public static function isAPIKeyEntered()
    {
        return !empty(self::$apiKey);
    }

    public static function fsms_tr_num($str)
    {
        $num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
        $key_a = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        return str_replace($key_a, $num_a, $str);
    }

    /**
     * 
     * Validate mobile number.
     * 
     */

    public static function validate_mobile_number($phone)
    {
        $phone = self::fsms_tr_num($phone);
        $mobile_pattern = "/^(\s)*(\+98|0098|98|0)?(9\d{9})(\s*|$)/";
        preg_match($mobile_pattern, $phone, $matches);
        if (sizeof($matches) !== 5) {
            return FALSE;
        }
        return $matches[3];
    }

    /**
     * 
     * Farazsms send pattern function.
     * 
     */

    public function farazsms_send_pattern($pattern, $phone, $input_data)
    {
        if (!empty(self::$apiKey)) {
            $client = new \IPPanel\Client(self::$apiKey);
            try {
                $client->sendPattern(
                    $pattern,
                    self::$fromNum,
                    $phone,
                    $input_data
                );
                return TRUE;
            } catch (Error | HttpException | Exception $e) {
                return FALSE;
            }
        } else {
            $body = array(
                'user'        => self::$username,
                'pass'        => self::$password,
                'fromNum'     => self::$fromNum,
                'op'          => 'pattern',
                'patternCode' => $pattern,
                'toNum'       => $phone,
                'inputData'   => [$input_data]
            );
            $response = wp_remote_post(
                'http://ippanel.com/api/select',
                array(
                    'method'      => 'POST',
                    'headers'     => [
                        'Content-Type' => 'application/json',
                    ],
                    'data_format' => 'body',
                    'body'        => json_encode($body)
                )
            );
            if (is_wp_error($response)) {
                return false;
            }
            $response = json_decode($response['body']);
            if ($response->status->code !== 0) {
                return false;
            }

            return true;
        }
    }

    /**
     * 
     * Save to phonebook functions.
     * 
     */

    public function save_to_phonebook($phone, $phone_book)
    {
        $phone = self::fsms_tr_num($phone);
        $body = array(
            'uname'       => self::$username,
            'pass'        => self::$password,
            'op'          => 'phoneBookAdd',
            'phoneBookId' => $phone_book,
            'number'      => $phone
        );

        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        $response = json_decode($response['body']);
        if ($response->status->code !== 0) {
            return false;
        }

        return true;
    }

    public function save_to_phonebookv2($phone, $phone_book)
    {
        $phone = self::fsms_tr_num($phone);
        $body = array(
            'uname'       => self::$username,
            'pass'        => self::$password,
            'op'          => 'phoneBookAdd',
            'phoneBookId' => $phone_book,
            'number'      => $phone
        );
        $handler = curl_init("http://ippanel.com/api/select");
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $response2 = curl_exec($handler);
        $response2 = json_decode($response2);
        if ($response2->status->code !== 0) {
            return false;
        }

        return true;
    }

    public function save_to_phonebookv3($data)
    {
        if (empty(self::$apiKey) || empty($data)) {
            return;
        }
        $client = new \IPPanel\Client(self::$apiKey, "phonebook_api");
        try {
            return $client->numbersAddList($data);
        } catch (Error | HttpException | Exception $e) {
            return FALSE;
        }
    }

    /**
     * 
     * Send welcome message function.
     * 
     */

    public function send_welcome_message($phone, $uid)
    {
        $phone = self::fsms_tr_num($phone);
        if (!self::$sendwm || $uid === null) {
            return;
        }
        $user = get_userdata($uid);
        $display_name = $user->display_name;
        $user_name = $user->user_login;
        $input_data = array();
        if (!self::$sendwm_with_pattern) {
            if (empty(self::$welcome_message)) {
                return;
            }
            $welcome_message = str_replace(array(
                '%display_name%',
                '%username%'
            ), array(
                $display_name,
                $user_name
            ), self::$welcome_message);
            self::send_message([$phone], $welcome_message, '+98club');
        } else {
            $patternMessage = self::get_registered_pattern_variables(self::$welcomep);
            if (strpos($patternMessage, '%display_name%') !== false) {
                $input_data['display_name'] = $display_name;
            }
            if (strpos($patternMessage, '%username%') !== false) {
                $input_data['username'] = $user_name;
            }
            return self::farazsms_send_pattern(self::$welcomep, $phone, $input_data);
        }
    }

    /**
     * 
     * Check if credentials is valid.
     * 
     * Only place that we need farazsms username and password.
     * 
     */

    public function check_if_credentials_is_valid($uname, $pass)
    {
        $body = array(
            'username'       => self::fsms_tr_num($uname),
            'password'       => self::fsms_tr_num($pass),
        );

        $response = wp_remote_post(
            'http://reg.ippanel.com/parent/farazsms',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        $response = json_decode($response['body']);
        if ($response->message == 1) {
            return true;
        }

        return false;
    }

    /**
     * 
     * Check if API key is valid.
     * 
     */


    public function check_if_apikey_is_valid($apiKey)
    {
        try {
            $client = new \IPPanel\Client($apiKey);
            return $client->validateApiKey();
        } catch (Error | HttpException $e) {
            return FALSE;
        }
    }

    /**
     * 
     * Get phonebooks.
     * 
     */

    public static function get_phonebooks()
    {
        $body = array(
            'uname' => self::$username,
            'pass'  => self::$password,
            'op'    => 'booklist'
        );
        $resp = wp_remote_post(
            'http://ippanel.com/api/select',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        if (is_wp_error($resp)) {
            return [];
        }
        $resp = json_decode($resp['body']);
        if (intval($resp[0]) != 0) {
            return [];
        }
        $resp = json_decode($resp[1]);

        return $resp;
    }

    /**
     * 
     * Get Lines.
     * 
     */

    public function get_lines()
    {
        $body = array(
            'uname' => self::$username,
            'pass'  => self::$password,
            'op'    => 'lines'
        );
        $resp = wp_remote_post(
            'http://ippanel.com/api/select',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        $resp = json_decode($resp['body']);
        if (intval($resp[0]) != 0) {
            return false;
        }
        $resp = json_decode($resp[1]);

        return $resp;
    }

    /**
     * 
     * Get credit.
     * 
     */

    public function get_credit()
    {
        if (!empty(self::$apiKey)) {
            try {
                $client = new \IPPanel\Client(self::$apiKey);
                $credit = $client->getCredit();
                $separator = '/';
                if (strpos($credit, '/')) {
                    $separator = "/";
                }
                if (strpos($credit, '.')) {
                    $separator = ".";
                }
                $credit_rial = explode($separator, $credit)[0];
                return substr($credit_rial, 0, -1);
            } catch (Error | HttpException | Exception $e) {
                return FALSE;
            }
        } else {
            $body     = array(
                "uname" => self::$username,
                "pass"  => self::$password,
                'op'    => 'credit'
            );
            $response = wp_remote_post(
                'http://ippanel.com/api/select',
                array(
                    'method'      => 'POST',
                    'headers'     => [
                        'Content-Type' => 'application/json',
                    ],
                    'data_format' => 'body',
                    'body'        => json_encode($body)
                )
            );
            if (is_wp_error($response)) {
                return false;
            }
            $response = json_decode($response['body']);
            if ($response[0] !== 0) {
                return false;
            }
            $separator = '.';
            if (strpos($response[1], '/')) {
                $separator = "/";
            }
            if (strpos($response[1], '.')) {
                $separator = ".";
            }
            $credit_rial = explode($separator, $response[1])[0];
        }

        return substr($credit_rial, 0, -1);
    }

    /**
     * 
     * Send low credit notify to admin.
     * 
     */

    public function send_admin_low_credit_to_admin()
    {
        $fromnum = "3000505";
        if (empty(self::$admin_number)) {
            return;
        }
        $message = __('Dear user, The charge for your SMS panel is less than 10 thousand tomans, and your sites SMS may not be sent soon and your site may be blocked. I will charge the SMS system as soon as possible. www.farazsms.com, +982171333036', 'farazsms');
        if (!empty(self::$apiKey)) {
            try {
                $client = new \IPPanel\Client(self::$apiKey);
                return $client->send(
                    $fromnum,
                    [self::$admin_number],
                    $message
                );
            } catch (Error | HttpException | Exception $e) {
                return FALSE;
            }
        } else {
            $body = array(
                'uname'  => self::$username,
                'pass'   => self::$password,
                'from'   => $fromnum,
                'op'     => 'send',
                'to'     => [self::$admin_number],
                'time'   => '',
                'message' => $message
            );
            $response = wp_remote_post(
                'http://ippanel.com/api/select',
                array(
                    'method'      => 'POST',
                    'headers'     => [
                        'Content-Type' => 'application/json',
                    ],
                    'data_format' => 'body',
                    'body'        => json_encode($body)
                )
            );
            $response = json_decode($response['body']);
        }
    }

    /**
     * 
     * Get registered pattern variables.
     * 
     */


    public function get_registered_pattern_variables($pCode)
    {
        $body = array(
            'uname'       => self::$username,
            'pass'        => self::$password,
            'op'          => 'patternInfo',
            'patternCode' => $pCode,
        );

        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        if (is_wp_error($response)) {
            return NULL;
        }
        $response = json_decode($response['body']);
        $patternMessage = $response->data->patternMessage;
        return $patternMessage;
    }

    /**
     * 
     * Send comment replgy sms.
     * 
     */


    public function send_comment_reply_sms($phone, $pattern, $data)
    {
        $phone = self::fsms_tr_num($phone);
        if (empty($pattern)) {
            return;
        }
        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($pattern);
        if ($patternMessage === null) {
            return;
        }

        if (strpos($patternMessage, '%title%') !== false) {
            $input_data['title'] = $data['title'];
        }
        if (strpos($patternMessage, '%name%') !== false) {
            $input_data['name'] = $data['name'];
        }
        if (strpos($patternMessage, '%email%') !== false) {
            $input_data['email'] = $data['email'];
        }
        if (strpos($patternMessage, '%link%') !== false) {
            $input_data['link'] = $data['link'];
        }
        if (strpos($patternMessage, '%content%') !== false) {
            $input_data['content'] = $data['content'];
        }
        return self::farazsms_send_pattern($pattern, $phone, $input_data);
    }

    /**
     * 
     * Send comment replgy sms to admin
     * 
     */

    public function send_comment_reply_sms_to_admin($data)
    {
        $fsms_admin_notify_pcode = self::fsms_tr_num(get_option('fsms_admin_notify_pcode'));
        if (empty($fsms_admin_notify_pcode) || empty(self::$admin_number)) {
            return;
        }
        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($fsms_admin_notify_pcode);
        if ($patternMessage === null) {
            return;
        }

        if (strpos($patternMessage, '%title%') !== false) {
            $input_data['title'] = $data['title'];
        }
        if (strpos($patternMessage, '%name%') !== false) {
            $input_data['name'] = $data['name'];
        }
        if (strpos($patternMessage, '%email%') !== false) {
            $input_data['email'] = $data['email'];
        }
        if (strpos($patternMessage, '%link%') !== false) {
            $input_data['link'] = $data['link'];
        }
        if (strpos($patternMessage, '%content%') !== false) {
            $input_data['content'] = $data['content'];
        }
        return self::farazsms_send_pattern($fsms_admin_notify_pcode, self::$admin_number, $input_data);
    }

    /**
     * 
     * Save comment mobile to phonebook.
     * 
     */

    public function save_comment_mobile_to_phonebook($phone)
    {
        $phone = self::fsms_tr_num($phone);
        foreach (self::$comment_phone_book as $phone_bookId) {
            $this->save_to_phonebook($phone, $phone_bookId);
        }
    }

    /**
     * 
     * Send message.
     * 
     */

    public function send_message($phones, $message, $sender = null)
    {
        if (!empty($sender)) {
            $fromnum = $sender;
        } else {
            $fromnum = self::$fromNum;
        }
        if (!empty(self::$apiKey)) {
            $client = new \IPPanel\Client(self::$apiKey);
            try {
                $client->send(
                    $fromnum,
                    $phones,
                    $message
                );
                return TRUE;
            } catch (Error | HttpException | Exception $e) {
                return FALSE;
            }
        } else {
            $body = array(
                'uname'  => self::$username,
                'pass'   => self::$password,
                'from'   => $fromnum,
                'op'     => 'send',
                'to'     => $phones,
                'time'   => '',
                'message' => $message
            );
            $response = wp_remote_post(
                'http://ippanel.com/api/select',
                array(
                    'method'      => 'POST',
                    'headers'     => [
                        'Content-Type' => 'application/json',
                    ],
                    'data_format' => 'body',
                    'body'        => json_encode($body)
                )
            );
            if (is_wp_error($response)) {
                return false;
            }
            $response = json_decode($response['body']);
            if ($response->status->code !== 0) {
                return false;
            }

            return true;
        }
    }

    /**
     * 
     * Get phonebook numbers.
     * 
     */

    public function get_phonebook_numbers($phoneBookID)
    {
        $body = array(
            'uname'       => self::$username,
            'pass'        => self::$password,
            'op'          => 'booknumberlist',
            'phonebook_id' => $phoneBookID,
        );
        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        $response = json_decode($response['body']);

        if (in_array('105', $response)) {
            return false;
        }

        return $response;
    }

    /**
     * 
     * Send EDD sms.
     * 
     */

    public function send_edd_sms($phone, $pattern, $data)
    {
        $phone = self::fsms_tr_num($phone);
        if (empty($phone) or empty($pattern) or empty($data)) {
            return;
        }
        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($pattern);
        if ($patternMessage === null) {
            return;
        }
        if (strpos($patternMessage, '%phone%') !== false) {
            $input_data['phone'] = $data['phone'];
        }
        if (strpos($patternMessage, '%email%') !== false) {
            $input_data['email'] = $data['email'];
        }
        if (strpos($patternMessage, '%first_name%') !== false) {
            $input_data['first_name'] = $data['first_name'];
        }
        if (strpos($patternMessage, '%last_name%') !== false) {
            $input_data['last_name'] = $data['last_name'];
        }
        if (strpos($patternMessage, '%product%') !== false) {
            $input_data['product'] = $data['product'];
        }
        if (strpos($patternMessage, '%price%') !== false) {
            $input_data['price'] = $data['price'];
        }
        if (strpos($patternMessage, '%discount%') !== false) {
            $input_data['discount'] = $data['discount'];
        }
        if (strpos($patternMessage, '%total_price%') !== false) {
            $input_data['total_price'] = $data['total_price'];
        }
        if (strpos($patternMessage, '%link%') !== false) {
            $input_data['link'] = $data['link'];
        }
        if (strpos($patternMessage, '%payment_id%') !== false) {
            $input_data['payment_id'] = $data['payment_id'];
        }
        return self::farazsms_send_pattern($pattern, $phone, $input_data);
    }

    /**
     * 
     * Send newsletter verifiacation code.
     * 
     */

    public function send_newsletter_verification_code($phone, $data)
    {
        $phone = self::fsms_tr_num($phone);
        $pattern = get_option('fsms_newsletter_newsletter_pcode');
        if (empty($phone) ||  empty($pattern) || empty($data)) {
            return;
        }
        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($pattern);
        if ($patternMessage === null) {
            return;
        }
        if (strpos($patternMessage, '%code%') !== false) {
            $input_data['code'] = strval($data['code']);
        }
        if (strpos($patternMessage, '%name%') !== false) {
            $input_data['name'] = strval($data['name']);
        }
        return self::farazsms_send_pattern($pattern, $phone, $input_data);
    }

    /**
     * 
     * Send woocommerce verification code.
     * 
     */

    public function send_woocommerce_verification_code($phone, $data)
    {
        $phone = self::fsms_tr_num($phone);
        $pattern = get_option('fsms_woo_checkout_otp_pattern');
        if (empty($phone) ||  empty($pattern) || empty($data)) {
            return FALSE;
        }
        $input_data = array();
        $input_data['code'] = strval($data['code']);
        return self::farazsms_send_pattern($pattern, $phone, $input_data);
    }

    /**
     * 
     * Save subscriber to DB.
     * 
     */

    public static function save_subscriber_to_db($data)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'farazsms_newsletter';
        return $wpdb->insert($table_name, $data);
    }

    /**
     * 
     * Save generated code to DB
     * 
     */

    public static function save_generated_code_to_db($phone, $code)
    {
        global $wpdb;
        $data = array(
            'phone' => $phone,
            'code'  => $code
        );
        $table = $wpdb->prefix . 'farazsms_vcode';
        $wpdb->delete($table, array('phone' => $phone));
        return $wpdb->insert($table, $data);
    }

    /**
     * 
     * Check if code is valid.
     * 
     */

    public static function check_if_code_is_valid($phone, $code)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'farazsms_vcode';
        $generated_code = $wpdb->get_col("SELECT code FROM {$table} WHERE phone = '" . $phone . "'");
        if ($generated_code[0] == $code) {
            $wpdb->delete($table, array('phone' => $phone));
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * Check if code is valid for woocommerce
     * 
     */


    public static function check_if_code_is_valid_for_woo($phone, $code)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'farazsms_vcode';
        $generated_code = $wpdb->get_col("SELECT code FROM {$table} WHERE phone = '" . $phone . "'");
        if ($generated_code[0] == $code) {
            //$wpdb->delete( $table, array( 'phone' => $phone ) );
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * Delete code for woocommerce
     * 
     */

    public static function delete_code_for_woo($phone)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'farazsms_vcode';
        $wpdb->delete($table, array('phone' => $phone));
    }

    /**
     * 
     * Check if phone already exist.
     * 
     */

    public static function check_if_phone_already_exist($phone)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'farazsms_newsletter';
        $generated_code = $wpdb->get_col("SELECT phone FROM {$table} WHERE phone = '" . $phone . "'");
        if (!empty($generated_code[0])) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * Get subscribers.
     * 
     */

    public static function get_subscribers()
    {
        global $wpdb;
        global $wpdb;
        $table_name = $wpdb->prefix . 'farazsms_newsletter';
        return $wpdb->get_results("SELECT * FROM $table_name");
    }

    /**
     * 
     * Delete subscriber.
     * 
     */

    public static function delete_subscriber($subscriber_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'farazsms_newsletter';
        return $wpdb->delete($table, array('id' => $subscriber_id));
    }

    /**
     * 
     * Send newsletter welcome message.
     * 
     */

    public static function send_newsletter_welcome_message($phone, $name)
    {
        $newsletter_welcome = get_option('fsms_newsletter_welcome', 'false');
        $newsletter_welcomep = get_option('fsms_newsletter_welcomep', '');
        if (empty($phone) || empty($name) || $newsletter_welcome == "false" || empty($newsletter_welcomep)) {
            return;
        }
        $phone = self::fsms_tr_num($phone);
        return self::farazsms_send_pattern($newsletter_welcomep, $phone, array("name" => $name));
    }

    /**
     * 
     * Send admins login notification to superadmin.
     * 
     */

    public function send_admins_login_notification_to_superadmin($pattern, $data)
    {
        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($pattern);
        if (strpos($patternMessage, '%date%') !== false) {
            $input_data['date'] = $data['date'];
        }
        if (strpos($patternMessage, '%user_login%') !== false) {
            $input_data['user_login'] = $data['user_login'];
        }
        if (strpos($patternMessage, '%display_name%') !== false) {
            $input_data['display_name'] = $data['display_name'];
        }
        return self::farazsms_send_pattern($pattern, self::$admin_number, $input_data);
    }

    /**
     * 
     * Send timed message.
     * 
     */

    public function send_timed_message($phone, $data, $order_date)
    {
        $fsms_woo_poll = get_option('fsms_woo_poll', 'false');
        $fsms_woo_poll_time = get_option('fsms_woo_poll_time', '');
        $fsms_woo_poll_message = get_option('fsms_woo_poll_message', '');
        if ($fsms_woo_poll == "false" || empty($fsms_woo_poll_time) || empty($fsms_woo_poll_message)) {
            return;
        }

        $message = str_replace(array(
            '%time%',
            '%item%',
            '%sitename%',
            '%item_link%',
        ), array(
            $fsms_woo_poll_time,
            $data["item"],
            get_bloginfo("name"),
            $data["item_link"]
        ), $fsms_woo_poll_message);

        $date_to_send = date('Y-m-d H:i:s', strtotime($order_date->date("Y-m-d H:i:s") . ' + ' . $fsms_woo_poll_time . ' days'));

        $body = array(
            'uname'  => self::$username,
            'pass'   => self::$password,
            'from'   => "+98club",
            'op'     => 'send',
            'to'     => $phone,
            'time'   => $date_to_send,
            'message' => $message
        );
        $response = wp_remote_post(
            'http://ippanel.com/api/select',
            array(
                'method'      => 'POST',
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'data_format' => 'body',
                'body'        => json_encode($body)
            )
        );
        if (is_wp_error($response)) {
            return false;
        }
    }

    /**
     * 
     * Send tracking code.
     * 
     */

    public function send_tracking_code($phone, $tacking_code, $order_date)
    {
        $tracking_code_pattern = get_option('fsms_woo_tracking_code_pattern', '');
        if (empty($tracking_code_pattern)) {
            throw new Exception("پترن برای ارسال کد رهگیری وارد نشده است");
        }
        $phone = self::fsms_tr_num($phone);
        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($tracking_code_pattern);
        if ($patternMessage === null) {
            throw new Exception("احتمالا پترن شما تایید نشده است");
        }

        if (strpos($patternMessage, '%tracking_code%') !== false) {
            $input_data['tracking_code'] = strval($tacking_code);
        }

        if (strpos($patternMessage, '%order_id%') !== false) {
            $input_data['order_id'] = strval($order_date['order_id']);
        }

        if (strpos($patternMessage, '%order_status%') !== false) {
            $input_data['order_status'] = strval($order_date['order_status']);
        }

        if (strpos($patternMessage, '%billing_full_name%') !== false) {
            $input_data['billing_full_name'] = strval($order_date['billing_full_name']);
        }
        if (strpos($patternMessage, '%shipping_full_name%') !== false) {
            $input_data['shipping_full_name'] = strval($order_date['shipping_full_name']);
        }
        return self::farazsms_send_pattern($tracking_code_pattern, $phone, $input_data);
    }

    /**
     * 
     * AFFS send sms.
     * 
     */

    public static function affs_send_sms($phone, $user_register_pattern, $args)
    {

        $input_data = array();
        $patternMessage = self::get_registered_pattern_variables($user_register_pattern);
        if (strpos($patternMessage, '%user_login%') !== false) {
            $input_data['user_login'] = $args['user_login'];
        }
        if (strpos($patternMessage, '%user_nicename%') !== false) {
            $input_data['user_nicename'] = $args['user_nicename'];
        }
        if (strpos($patternMessage, '%user_email%') !== false) {
            $input_data['user_email'] = $args['user_email'];
        }
        if (strpos($patternMessage, '%display_name%') !== false) {
            $input_data['display_name'] = $args['display_name'];
        }
        if (strpos($patternMessage, '%user_mobile%') !== false) {
            $input_data['user_mobile'] = $args['user_mobile'];
        }
        if (strpos($patternMessage, '%amount%') !== false) {
            $input_data['amount'] = $args['amount'];
        }
        if (strpos($patternMessage, '%amount%') !== false) {
            $input_data['amount'] = $args['amount'];
        }

        return self::farazsms_send_pattern($user_register_pattern, $phone, $input_data);
    }

    /**
     * 
     * Send feedback message to server.
     * 
     */

    public function send_feedback_message_to_server($feedback_message)
    {
        $body = array(
            'uname'        => self::$username,
            'pass'        => self::$password,
            'subject'     => "بازخورد مستقیم از افزونه فراز اس ام اس",
            'description' => $feedback_message . PHP_EOL . get_site_url(),
            'type' => 'fiscal',   //'fiscal','webservice','problem','lineservices'
            'importance' => 'low',  //'low','middle','quick','acute'
            'sms_notification' => 'no', //'yes','no'
            'file' => '',
            'op' => 'ticketadd'
        );

        $handler = curl_init("http://ippanel.com/services.jspd");
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $body);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);
        $response = json_decode($response);
        if (is_wp_error($response)) {
            return false;
        }
        if ($response[0] !== 0 ||  !$response[1]) {
            return false;
        }

        return true;
    }
}
