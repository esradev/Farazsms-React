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
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	public static $news_welcome;
	public static $news_welcome_pattern;
	public static $news_send_verify_pattern;
	public static $news_phonebook_id;
	public static $news_send_verify_via_pattern;
	public static $news_post_notify;
	public static $news_post_notify_msg;
	public static $news_product_notify;
	public static $news_product_notify_msg;

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
		add_shortcode( 'farazsms-newsletter', [ $this, 'farazsms_newsletter' ] );
		$newsletter_options = json_decode( get_option( 'farazsms_newsletter_options' ), true );
		if ( $newsletter_options ) {
			self::$news_phonebook_id            = $newsletter_options['news_phonebook']['value'] ?? '';
			self::$news_welcome                 = $newsletter_options['news_welcome'];
			self::$news_welcome_pattern         = $newsletter_options['news_welcome_pattern'];
			self::$news_send_verify_via_pattern = $newsletter_options['news_send_verify_via_pattern'];
			self::$news_send_verify_pattern     = $newsletter_options['news_send_verify_pattern'];
			self::$news_post_notify             = $newsletter_options['news_post_notify'];
			self::$news_post_notify_msg         = $newsletter_options['news_post_notify_msg'];
			self::$news_product_notify          = $newsletter_options['news_product_notify'];
			self::$news_product_notify_msg      = $newsletter_options['news_product_notify_msg'];
		}

		add_action( 'wp_ajax_fsms_newsletter_send_verification_code', [
			$this,
			'fsms_newsletter_send_verification_code'
		] );
		add_action( 'wp_ajax_nopriv_fsms_newsletter_send_verification_code', [
			$this,
			'fsms_newsletter_send_verification_code'
		] );

		add_action( 'wp_insert_post', [ $this, 'fsms_publish_post_notification' ], 10, 2 );
		add_action( 'transition_post_status', [ $this, 'fsms_product_published' ], 10, 3 );
	}

	/**
	 * Farazsms newsletter.
	 */
	public function farazsms_newsletter() {
		wp_enqueue_style( 'farazsms-newsletter' );
		wp_enqueue_script( 'farazsms-newsletter' );

		return '<div id="fsms_newsletter">
                  <form id="fsms_newsletter_form">
                    <div class="fsms_newsletter_input a">
                      <input id="fsms_newsletter_name" type="text" class="fsms_newsletter_text" placeholder="' . esc_attr__('First & Last name', 'farazsms') . '">
                    </div>
                    <div class="fsms_newsletter_input a">
                      <input id="fsms_newsletter_mobile" type="text" class="fsms_newsletter_text" placeholder="' . esc_attr__('Phone number', 'farazsms') . '">
                    </div>
                    <div class="fsms_newsletter_input b" style="display: none;">
                      <input id="fsms_newsletter_verify_code" type="text" class="fsms_newsletter_text" placeholder="' . esc_attr__('Verification code', 'farazsms') . '">
                    </div>
                    <input id="newsletter_send_ver_code" type="hidden" value="' . esc_attr(self::$news_send_verify_via_pattern) . '">
                  </form>
                    <div id="fsms_newsletter_message" style="display: none;">
                    </div>
                    <div class="fsms_newsletter_submit">
                      <button id="fsms_newsletter_submit_button" class="fsms_newsletter_button"><span class="button__text">' . esc_html__('Join', 'farazsms') . '</span></button>
                    </div>
                    <div id="fsms_newsletter_completion" class="fsms_newsletter_submit" style="display: none;">
                      <button id="fsms_newsletter_submit_code" class="fsms_newsletter_button"><span class="button__text">' . esc_html__('Send code', 'farazsms') . '</span></button>
                      <button id="fsms_newsletter_resend_code" class="fsms_newsletter_button"><span class="button__text">' . esc_html__('Send code again', 'farazsms') . '</span></button>
                    </div>
                </div>';
	}

	/**
	 * Newsletter send verification code
	 */
	public function fsms_newsletter_send_verification_code() {
		$mobile              = sanitize_text_field( $_POST['mobile'] );
		$name                = sanitize_text_field( $_POST['name'] );
		$phonebook_id        = isset( $_POST['phonebook_id'] ) ? absint( $_POST['phonebook_id'] ) : self::$news_phonebook_id;
		$send_verify_code    = isset( $_POST['send_verify_code'] ) ? sanitize_text_field( $_POST['send_verify_code'] ) : self::$news_send_verify_via_pattern;
		$verify_code_pattern = isset( $_POST['verify_code_pattern'] ) ? sanitize_text_field( $_POST['verify_code_pattern'] ) : self::$news_send_verify_pattern;
		$send_welcome_msg    = isset( $_POST['send_welcome_msg'] ) ? sanitize_text_field( $_POST['send_welcome_msg'] ) : self::$news_welcome;
		$welcome_msg_pattern = isset( $_POST['welcome_msg_pattern'] ) ? sanitize_text_field( $_POST['welcome_msg_pattern'] ) : self::$news_welcome_pattern;



		if ( self::check_if_phone_already_exist( $mobile ) ) {
			wp_send_json_error();
		}

		if ( ! $send_verify_code ) {
			$data = [
				'phone'      => $mobile,
				'name'       => $name,
				'phone_book' => $phonebook_id,
			];
			self::save_subscriber_to_db( $data );

			$list[0] = (object) [
				'number'       => $mobile,
				'name'         => $name,
				'phonebook_id' => (int) $phonebook_id
			];
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
			if ( $send_welcome_msg !== 'no' ) {
				self::send_newsletter_welcome_message( $mobile, $name, $welcome_msg_pattern );
			}
		} else {
			$generated_code = rand( 1000, 9999 );
			Farazsms_Base::save_generated_code_to_db( $mobile, $generated_code );
			$data = [
				'code'                => $generated_code,
				'name'                => $name,
				'phonebook_id'        => $phonebook_id,
				'verify_code_pattern' => $verify_code_pattern,
				'send_welcome_msg'    => $send_welcome_msg,
				'welcome_msg_pattern' => $welcome_msg_pattern,
			];
			self::send_newsletter_verification_code( $mobile, $data );
		}
		wp_send_json_success();
	}

	/**
	 * Send newsletter verification code.
	 */
	public function send_newsletter_verification_code( $phone, $data ) {
		$phone              = Farazsms_Base::fsms_tr_num( $phone );
		$verify_code_patten = $data['verify_code_pattern'];

		if ( empty( $phone ) || empty( $verify_code_patten ) || empty( $data ) ) {
			return;
		}

		$input_data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $verify_code_patten );
		if ( $patternMessage === null ) {
			return;
		}
		if ( str_contains( $patternMessage, '%code%' ) ) {
			$input_data['code'] = strval( $data['code'] );
		}
		if ( str_contains( $patternMessage, '%name%' ) ) {
			$input_data['name'] = strval( $data['name'] );
		}

		return Farazsms_Ippanel::send_pattern( $verify_code_patten, $phone, $input_data );
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
	 * Sends notification to subscribers when a new post is published.
	 *
	 * @param int     $post_id The ID of the post being published.
	 * @param WP_Post $post    The post object.
	 */
	public function fsms_publish_post_notification( int $post_id, WP_Post $post ) {

		// Check if the post is newly published.
		if ( $post->post_status !== 'publish' || $post->post_date !== $post->post_modified ) {
			return;
		}

		// Return if notification is disabled or message is not set.
		if ( false === self::$news_post_notify || empty( self::$news_post_notify_msg ) ) {
			return;
		}

		// Generate notification message.
		$notification_message = str_replace(
			[ '%title%', '%url%' ],
			[ get_the_title( $post_id ), wp_get_shortlink( $post_id ) ],
			self::$news_post_notify_msg
		);

		// Retrieve subscribers' phone numbers.
		$subscribers = self::get_subscribers();
		$phones      = wp_list_pluck( $subscribers, 'phone' );

		// Send notification message to all subscribers.
		Farazsms_Ippanel::send_message( $phones, $notification_message );
	}


	/**
	 * Sends notification to subscribers when a new product is published.
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $post
	 *
	 * @return void
	 */
	public function fsms_product_published( $new_status, $old_status, $post ) {
		// Use strict comparison operator and early return
		if ( $new_status !== 'publish' || $old_status === 'publish' ) {
			return;
		}

		$post_type = get_post_type( $post );
		// Use early return
		if ( $post_type !== 'product' ) {
			return;
		}

		// Use descriptive variable names
		$product_notify_enabled = self::$news_product_notify === true;
		$product_notify_message = self::$news_product_notify_msg;

		// Use early return
		if ( ! $product_notify_enabled || empty( $product_notify_message ) ) {
			return;
		}

		$product          = wc_get_product( $post->ID );
		$message_template = str_replace(
			[ '%site_title%', '%product_name%', '%price%', '%url%' ],
			[ wp_title(), $product->get_name(), $product->get_price(), wp_get_shortlink( $post->ID ) ],
			$product_notify_message
		);

		$subscribers = self::get_subscribers();
		$phones      = wp_list_pluck( $subscribers, 'phone' );

		Farazsms_Ippanel::send_message( $phones, $message_template );
	}

	/**
	 * Get subscribers.
	 */
	public static function get_subscribers() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_newsletter';

		return $wpdb->get_results( "SELECT * FROM $table_name" );
	}

	/**
	 * Send newsletter welcome message.
	 */
	public static function send_newsletter_welcome_message( $phone, $name, $welcome_msg_pattern ) {

		if ( empty( $phone ) || empty( $name ) || empty( $welcome_msg_pattern ) ) {
			return;
		}
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $welcome_msg_pattern );
		$input_data     = [];
		$phone          = Farazsms_Base::fsms_tr_num( $phone );
		if ( str_contains( $patternMessage, '%name%' ) ) {
			$input_data['name'] = strval( $name );
		}

		return Farazsms_Ippanel::send_pattern( $welcome_msg_pattern, $phone, $input_data );
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

	/**
	 * Add phone to newsletter.
	 */
	public function fsms_add_phone_to_newsletter() {
		$code                = sanitize_text_field($_POST['code'] );
		$mobile              = sanitize_text_field( $_POST['mobile'] );
		$name                = sanitize_text_field( $_POST['name'] );
		$phonebook_id        = isset( $_POST['phonebook_id'] ) ? absint( $_POST['phonebook_id'] ) : self::$news_phonebook_id;
		$send_welcome_msg    = isset( $_POST['send_welcome_msg'] ) ? sanitize_text_field( $_POST['send_welcome_msg'] ) : self::$news_welcome;
		$welcome_msg_pattern = isset( $_POST['welcome_msg_pattern'] ) ? sanitize_text_field( $_POST['welcome_msg_pattern'] ) : self::$news_welcome_pattern;


		$is_valid = self::check_if_code_is_valid( $mobile, $code );
		if ( $is_valid ) {
			$data = [
				'phone'      => $mobile,
				'name'       => $name,
				'phone_book' => $phonebook_id,
			];
			self::save_subscriber_to_db( $data );

			$list[0] = (object) [
				'number'       => $mobile,
				'name'         => $name,
				'phonebook_id' => (int) $phonebook_id
			];
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
			if ( $send_welcome_msg !== 'no' ) {
				self::send_newsletter_welcome_message( $mobile, $name, $welcome_msg_pattern );
			}
			wp_send_json_success();

		} else {
			wp_send_json_error();
		}
	}
}

Farazsms_Newsletter::get_instance();
