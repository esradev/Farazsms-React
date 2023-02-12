<?php

/**
 * Farazsms newsletter.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Newsletter.
 */
class Farazsms_Newsletter {
	private static $elementorPro;

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	private static $news_welcome;
	private static $news_welcome_pattern;
	private static $news_send_verify_pattern;
	private static $news_phonebook_id;
	private static $news_send_verify_via_pattern;
	private static $news_post_notify;
	private static $news_post_notify_msg;
	private static $news_product_notify;
	private static $news_product_notify_msg;

	/**
	 * Initiator
	 *
	 * @return object Initialized object of class.
	 * @since 2.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'farazsms', [ $this, 'farazsms_newsletter' ] );
		$newsletter_options = json_decode( get_option( 'farazsms_newsletter_options' ), true );
		if ( $newsletter_options ) {
			self::$news_welcome                 = $newsletter_options['news_welcome'];
			self::$news_welcome_pattern         = $newsletter_options['news_welcome_pattern'];
			self::$news_send_verify_pattern     = $newsletter_options['news_send_verify_pattern'];
			self::$news_phonebook_id            = current( array_column( $newsletter_options['news_phonebook'], 'value' ) );
			self::$news_send_verify_via_pattern = $newsletter_options['news_send_verify_via_pattern'];
			self::$news_post_notify             = $newsletter_options['news_post_notify'];
			self::$news_post_notify_msg         = $newsletter_options['news_post_notify_msg'];
			self::$news_product_notify          = $newsletter_options['news_product_notify'];
			self::$news_product_notify_msg      = $newsletter_options['news_product_notify_msg'];
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'wp_ajax_fsms_newsletter_send_verification_code', [
			$this,
			'fsms_newsletter_send_verification_code'
		] );
		add_action( 'wp_ajax_nopriv_fsms_newsletter_send_verification_code', [
			$this,
			'fsms_newsletter_send_verification_code'
		] );

		add_action( 'wp_ajax_fsms_add_phone_to_newsletter', [ $this, 'fsms_add_phone_to_newsletter' ] );
		add_action( 'wp_ajax_nopriv_fsms_add_phone_to_newsletter', [ $this, 'fsms_add_phone_to_newsletter' ] );

		add_action( 'publish_post', [ $this, 'fsms_publish_post_notification' ] );
		add_action( 'transition_post_status', [ $this, 'fsms_product_published', 10, 3 ] );

		add_action( 'wp_ajax_fsms_send_message_to_subscribers', [ $this, 'send_message_to_subscribers' ] );
		add_action( 'wp_ajax_nopriv_fsms_send_message_to_subscribers', [ $this, 'send_message_to_subscribers' ] );

		add_action( 'wp_ajax_fsms_delete_user_from_subscribers', [ $this, 'fsms_delete_user_from_subscribers' ] );
		add_action( 'wp_ajax_nopriv_fsms_delete_user_from_subscribers', [
			$this,
			'fsms_delete_user_from_subscribers'
		] );

		add_action( 'wp_ajax_fsms_send_message_to_phonebooks', [ $this, 'ajax_send_message_to_phonebooks' ] );
		add_action( 'wp_ajax_nopriv_fsms_send_message_to_phonebooks', [ $this, 'ajax_send_message_to_phonebooks' ] );

	}

	/**
	 * Enqueue styles
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( 'farazsms-newsletter', plugin_dir_url( __FILE__ ) . 'css/farazsms-newsletter.css', [], 2.0, 'all' );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( 'farazsms-newsletter', plugin_dir_url( __FILE__ ) . 'js/farazsms-newsletter.js', [ 'jquery' ], 2.0, false );
	}

	/**
	 * Delete user from subscribers.
	 *
	 * @since 1.0.0
	 */
	public function fsms_delete_user_from_subscribers() {
		$subscriber_id = ( $_POST['subscriber_id'] ?? '' );
		self::delete_subscriber( $subscriber_id );
		wp_send_json_success();
	}

	/**
	 * Send message to subscribers of newsletter.
	 *
	 * @since 1.0.0
	 */
	public function send_message_to_subscribers() {

		$message     = ( $_POST['message'] ?? '' );
		$subscribers = self::get_subscribers();
		if ( ! Farazsms_Base::$apiKey ) {
			wp_send_json_error( __( 'Please enter the api key first in the Settings tab.', 'farazsms' ) );
		}

		if ( empty( $subscribers ) ) {
			wp_send_json_error( __( 'No one is a subscriber of the newsletter yet', 'farazsms' ) );
		}

		if ( str_contains( $message, '%name%' ) ) {
			foreach ( $subscribers as $subscriber ) {
				$message_fixed = str_replace( '%name%', $subscriber->name, $message );
				Farazsms_Ippanel::send_message( [ $subscriber->phone ], $message_fixed, '+98club' );
			}
		} else {
			$phones = [];
			foreach ( $subscribers as $subscriber ) {
				$phones[] = $subscriber->phone;
			}

			Farazsms_Ippanel::send_message( $phones, $message, '+98club' );
		}

		wp_send_json_success();
	}

	/**
	 * Farazsms newsletter.
	 */
	public function farazsms_newsletter( $atts = [] ) {
		wp_enqueue_style( 'farazsms-newsletter' );
		wp_enqueue_script( 'farazsms-newsletter' );
		wp_localize_script(
			'farazsms-newsletter',
			'fsms_ajax_object',
			[ 'ajax_url' => admin_url( 'admin-ajax.php' ) ]
		);
		$newsletter_send_ver_code = self::$news_send_verify_via_pattern;

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
	 * Newsletter send verification code
	 */
	public function fsms_newsletter_send_verification_code() {

		$mobile                   = $_POST['mobile'];
		$name                     = $_POST['name'];
		$newsletter_send_ver_code = self::$news_send_verify_via_pattern;
		if ( self::check_if_code_is_valid( $mobile, $newsletter_send_ver_code ) ) {
			wp_send_json_error();
		}
		if ( $newsletter_send_ver_code == 'false' ) {
			$data = [
				'phone' => $mobile,
				'name'  => $name,
			];
			self::save_subscriber_to_db( $data );

			$list[] = (object) [
				'number'       => $mobile,
				'name'         => $name,
				'phonebook_id' => (int) self::$news_phonebook_id
			];
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

			self::send_newsletter_welcome_message( $mobile, $name );
			wp_send_json_success();
		} else {
			$generated_code = rand( 1000, 9999 );
			self::save_generated_code_to_db( $mobile, $generated_code );
			$data = [
				'code' => $generated_code,
				'name' => $name
			];
			self::send_newsletter_verification_code( $mobile, $data );
			wp_send_json_success();
		}
	}

	/**
	 * Send newsletter verification code.
	 */
	public function send_newsletter_verification_code( $phone, $data ) {
		$phone   = Farazsms_Base::fsms_tr_num( $phone );
		$pattern = self::$news_send_verify_pattern;
		if ( empty( $phone ) || empty( $pattern ) || empty( $data ) ) {
			return;
		}

		$input_data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
		if ( $patternMessage === null ) {
			return;
		}
		if ( str_contains( $patternMessage, '%code%' ) ) {
			$input_data['code'] = strval( $data['code'] );
		}
		if ( str_contains( $patternMessage, '%name%' ) ) {
			$input_data['name'] = strval( $data['name'] );
		}
		return Farazsms_Ippanel::send_pattern( $pattern, $phone, $input_data );
	}

	/**
	 * Save subscriber to DB.
	 */
	public static function save_subscriber_to_db( $data ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_newsletter';

		return $wpdb->insert( $table_name, $data );
	}

	/**
	 * Add phone to newsletter.
	 */
	public function fsms_add_phone_to_newsletter() {
		$code     = $_POST['code'];
		$name     = $_POST['name'];
		$mobile   = $_POST['mobile'];
		$is_valid = self::check_if_code_is_valid( $mobile, $code );
		if ( $is_valid ) {
			$data = [
				'phone' => $mobile,
				'name'  => $name
			];
			self::save_subscriber_to_db( $data );

			$list[] = (object) [
				'number'       => $mobile,
				'name'         => $name,
				'phonebook_id' => (int) self::$news_phonebook_id
			];
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

			self::send_newsletter_welcome_message( $mobile, $name );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Publish post notification.
	 */
	public function fsms_publish_post_notification( $post_id ) {
		$newsletter_new_post_notification     = self::$news_post_notify;
		$newsletter_post_notification_message = self::$news_post_notify_msg;
		if ( $newsletter_new_post_notification == 'false' || empty( $newsletter_post_notification_message ) ) {
			return;
		}
		$notification_message = str_replace( [
			'%title%',
			'%url%'
		], array(
			get_the_title( $post_id ),
			wp_get_shortlink( $post_id )
		), $newsletter_post_notification_message );
		$subscribers          = self::get_subscribers();
		$phones               = [];
		foreach ( $subscribers as $subscriber ) {
			$phones[] = $subscriber->phone;
		}
		Farazsms_Ippanel::send_message( $phones, $notification_message );
		remove_action( 'publish_post', 'fsms_publish_post_notification', 10 );
	}

	/**
	 * Product published.
	 */
	public function fsms_product_published( $new_status, $old_status, $post ) {
		$post_type = get_post_type( $post );
		if ( $post_type !== 'product' ) {
			return;
		}
		if ( $new_status === 'publish' && $new_status !== $old_status ) {
			$newsletter_new_prodcut_notification     = self::$news_product_notify;
			$newsletter_prodcut_notification_message = self::$news_product_notify_msg;
			if ( $newsletter_new_prodcut_notification === 'false' || empty( $newsletter_prodcut_notification_message ) ) {
				return;
			}
			$product              = wc_get_product( $post->ID );
			$notification_message = str_replace( [
				'%site_title%',
				'%product_name%',
				'%price%',
				'%url%'
			], [
				wp_title(),
				$product->get_name(),
				$product->get_price(),
				wp_get_shortlink( $post->ID )
			], $newsletter_prodcut_notification_message );
			$subscribers          = self::get_subscribers();
			$phones               = [];
			foreach ( $subscribers as $subscriber ) {
				$phones[] = $subscriber->phone;
			}
			Farazsms_Ippanel::send_message( $phones, $notification_message );
		}
	}

	/**
	 * Get subscribers.
	 */
	public static function get_subscribers() {
		global $wpdb;
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_newsletter';

		return $wpdb->get_results( "SELECT * FROM $table_name" );
	}

	/**
	 * Delete subscriber.
	 */
	public static function delete_subscriber( $subscriber_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'farazsms_newsletter';

		return $wpdb->delete( $table, [ 'id' => $subscriber_id ] );
	}


	/**
	 * Send newsletter welcome message.
	 */
	public static function send_newsletter_welcome_message( $phone, $name ) {
		$newsletter_welcome  = self::$news_welcome;
		$newsletter_welcomep = self::$news_welcome_pattern;
		if ( empty( $phone ) || empty( $name ) || $newsletter_welcome == 'false' || empty( $newsletter_welcomep ) ) {
			return;
		}

		$phone = self::fsms_tr_num( $phone );

		return Farazsms_Ippanel::send_pattern( $newsletter_welcomep, $phone, [ 'name' => $name ] );
	}

	/**
	 * Check if phone already exist.
	 */
	public static function check_if_phone_already_exist( $phone ) {
		global $wpdb;
		$table          = $wpdb->prefix . 'farazsms_newsletter';
		$generated_code = $wpdb->get_col( "SELECT phone FROM {$table} WHERE phone = '" . $phone . "'" );
		if ( ! empty( $generated_code[0] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Send SMS to phonebooks.
	 *
	 * @since 1.0.0
	 */
	public function ajax_send_message_to_phonebooks() {
		$fsms_base           = Farazsms_Base::get_instance();
		$message             = ( $_POST['message'] ?? '' );
		$phonebooks          = ( $_POST['phonebooks'] ?? [] );
		$send_to_subscribers = ( $_POST['send_to_subscribers'] ?? '' );
		$send_formnum_choice = ( $_POST['send_formnum_choice'] ?? '' );
		if ( $send_formnum_choice == '2' && ! strpos( $_POST['phones'], ',' ) ) {
			wp_send_json_error( __( 'Please enter manual numbers in the correct format', 'farazsms' ) );
		}

		if ( $send_formnum_choice == '1' ) {
			$send_formnum_choice = Farazsms_Base::$fromNum;
		} else {
			$send_formnum_choice = Farazsms_Base::$fromNumAdver;
		}

		$phones = explode( ',', ( $_POST['phones'] ?? '' ) );
		foreach ( $phones as $phone ) {
			if ( $fsms_base::validate_mobile_number( $phone ) ) {
				$fixed_phones[] = $fsms_base::validate_mobile_number( $phone );
			}
		}

		if ( empty( $phonebooks ) && empty( $fixed_phones ) && $send_to_subscribers == 'false' ) {
			wp_send_json_error( __( 'Please select at least one phonebook or manual number or newsletter members', 'farazsms' ) );
			wp_die();
		}

		if ( ! empty( $fixed_phones ) ) {
			Farazsms_Ippanel::send_message( $fixed_phones, $message, $send_formnum_choice );
		}

		foreach ( $phonebooks as $phonebook ) {
			$phonebook_numbers = self::get_phonebook_numbers( $phonebook );
			Farazsms_Ippanel::send_message( $phonebook_numbers, $message, $send_formnum_choice );
		}

		wp_send_json_success();

	}

	/**
	 * Get phonebook numbers.
	 */
	public function get_phonebook_numbers( $phoneBookID ) {
		$body     = [
			'uname'        => Farazsms_Base::$username,
			'pass'         => Farazsms_Base::$password,
			'op'           => 'booknumberlist',
			'phonebook_id' => $phoneBookID,
		];
		$response = wp_remote_post(
			'http://ippanel.com/api/select',
			[
				'method'      => 'POST',
				'headers'     => [ 'Content-Type' => 'application/json' ],
				'data_format' => 'body',
				'body'        => json_encode( $body ),
			]
		);
		$response = json_decode( $response['body'] );

		if ( in_array( '105', $response ) ) {
			return false;
		}
		return $response;
	}

	/**
	 * Check if code is valid.
	 */
	public static function check_if_code_is_valid( $phone, $code ) {
		global $wpdb;
		$table          = $wpdb->prefix . 'farazsms_vcode';
		$generated_code = $wpdb->get_col( "SELECT code FROM {$table} WHERE phone = '" . $phone . "'" );
		if ( $generated_code[0] == $code ) {
			$wpdb->delete( $table, [ 'phone' => $phone ] );

			return true;
		}

		return false;
	}
}

Farazsms_Newsletter::get_instance();
