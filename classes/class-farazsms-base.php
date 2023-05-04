<?php

/**
 * Farazsms base class.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Base.
 */
class Farazsms_Base {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	public static $username;
	public static $password;
	public static $admin_number;
	public static $fromNum;
	public static $fromNumAdver;
	public static $apiKey;

	public static $sendwm;
	public static $sendwm_with_pattern;
	public static $welcome_message;
	public static $welcomep;
	public static $admin_login_notify_pattern;

	public static $custom_phonebook_id;
	public static $custom_phone_meta_keys_id;
	public static $digits_phonebook_id;
	public static $woo_phonebook_id;
	public static $bookly_phonebook_id;
	public static $gf_phonebook_id;
	public static $gf_forms_id;
	public static $gf_selected_field_id;

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

		$credentials_option = json_decode( get_option( 'farazsms_settings_options' ), true );
		if ( $credentials_option ) {
			self::$username     = $credentials_option['username'];
			self::$password     = $credentials_option['password'];
			self::$admin_number = $credentials_option['admin_number'];
			self::$apiKey       = $credentials_option['apikey'];
			self::$fromNum      = $credentials_option['from_number']['value'] ?? '3000505';
			self::$fromNumAdver = $credentials_option['from_number_adver']['value'] ?? '+98club';
		}

		$login_notify_options = json_decode( get_option( 'farazsms_login_notify_options' ), true );
		if ( $login_notify_options ) {
			self::$sendwm                     = $login_notify_options['welcome_sms'];
			self::$welcomep                   = $login_notify_options['welcome_sms_pattern'];
			self::$sendwm_with_pattern        = $login_notify_options['welcome_sms_use_pattern'];
			self::$welcome_message            = $login_notify_options['welcome_sms_msg'];
			self::$admin_login_notify_pattern = $login_notify_options['admin_login_notify_pattern'];
		}

		$phonebook_options = json_decode( get_option( 'farazsms_phonebook_options' ), true );
		if ( $phonebook_options ) {
			self::$custom_phonebook_id       = $phonebook_options['custom_phonebook']['value'] ?? '';
			self::$custom_phone_meta_keys_id = $phonebook_options['custom_phone_meta_keys']['value'] ?? '';
			self::$digits_phonebook_id       = $phonebook_options['digits_phonebook']['value'] ?? '';
			self::$woo_phonebook_id          = $phonebook_options['woo_phonebook']['value'] ?? '';
			self::$bookly_phonebook_id       = $phonebook_options['bookly_phonebook']['value'] ?? '';
			self::$gf_forms_id               = $phonebook_options['gf_forms']['value'] ?? '';
			self::$gf_phonebook_id           = $phonebook_options['gf_phonebook']['value'] ?? '';
			self::$gf_selected_field_id      = $phonebook_options['gf_selected_field']['value'] ?? '';
		}
	}

	/**
	 * Replace persian numbers with english.
	 *
	 * @since 2.0.0
	 */
	public static function fsms_tr_num( $str ) {
		$num_a = [
			'0',
			'1',
			'2',
			'3',
			'4',
			'5',
			'6',
			'7',
			'8',
			'9',
			'.',
		];
		$key_a = [
			'۰',
			'۱',
			'۲',
			'۳',
			'۴',
			'۵',
			'۶',
			'۷',
			'۸',
			'۹',
		];

		return str_replace( $key_a, $num_a, $str );
	}

	/**
	 * Validate mobile number.
	 *
	 * @param $phone
	 *
	 * @return false|string
	 * @since 2.0.0
	 */
	public static function validate_mobile_number( $phone ) {
		$phone          = self::fsms_tr_num( $phone );
		$mobile_pattern = '/^(\s)*(\+98|0098|98|0)?(9\d{9})(\s*|$)/';
		$matches        = [];
		if ( is_string( $phone ) ) {
			preg_match( $mobile_pattern, $phone, $matches );
		}
		if ( count( $matches ) !== 5 ) {
			return false;
		}

		return $matches[3];
	}

	/**
	 * Send welcome message function.
	 *
	 * @since 2.0.0
	 */
	public static function send_welcome_message( $phone, $user_id ) {
		$phone = self::fsms_tr_num( $phone );
		if ( ! self::$sendwm || $user_id === null ) {
			return;
		}

		$user         = get_userdata( $user_id );
		$display_name = $user->display_name;
		$user_name    = $user->user_login;
		$input_data   = [];
		if ( ! self::$sendwm_with_pattern ) {
			if ( empty( self::$welcome_message ) ) {
				return;
			}

			$welcome_message = str_replace(
				[
					'%display_name%',
					'%username%',
				],
				[
					$display_name,
					$user_name,
				],
				self::$welcome_message
			);
			Farazsms_Ippanel::send_message( [ $phone ], $welcome_message, '+98club' );
		} else {
			$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( self::$welcomep );
			if ( str_contains( $patternMessage, '%display_name%' ) ) {
				$input_data['display_name'] = $display_name;
			}

			if ( str_contains( $patternMessage, '%username%' ) ) {
				$input_data['username'] = $user_name;
			}

			return Farazsms_Ippanel::send_pattern( self::$welcomep, $phone, $input_data );
		}
	}

	/**
	 * Save generated code to DB
	 *
	 * @since 2.0.0
	 */
	public static function save_generated_code_to_db( $phone, $code ) {
		global $wpdb;
		$data  = [
			'phone' => $phone,
			'code'  => $code,
		];
		$table = $wpdb->prefix . 'farazsms_vcode';
		$wpdb->delete( $table, [ 'phone' => $phone ] );

		return $wpdb->insert( $table, $data );
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
	 * Normalize phone number to generate international iran phone
	 *
	 * @param $phone_number
	 *
	 * @return mixed|string
	 */
	public static function normalize_phone_number( $phone_number ) {
		// Check if the phone number is in the international format for Iran
		if ( preg_match( '/^\+98\d{10}$/', $phone_number ) ) {
			return $phone_number;
		}

		// Add the country code for Iran if it's not already present
		if ( ! str_starts_with( $phone_number, '+98' ) ) {
			$phone_number = '+98' . ltrim( $phone_number, '0' );
		}

		// Remove any remaining leading zeros
		return ltrim( $phone_number, '0' );
	}
}

Farazsms_Base::get_instance();
