<?php

/**
 * @link  https://farazsms.com/
 * @since 1.0.8
 *
 * @package    Farazsms
 * @subpackage Farazsms/includes
 */

/**
 * Load IPPanel autoload file.
 */

require_once(__DIR__.'/../vendor/autoload.php');

use IPPanel\Client;
use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;

if ( ! class_exists( 'Farazsms_Base' ) ) {
	/**
	 * Initialize the class and set its properties.
	 */
	class Farazsms_Base {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

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

		private static $admin_login_notify_pattern;

		private static $comment_phonebook;

		private static $news_welcome;

		private static $news_welcome_pattern;

		private static $news_send_verify_pattern;


		public function __construct() {
			/*
			 *
			 * Init credential options.
			 *
			 */

			$credentials_option = json_decode( get_option( 'farazsms_settings_options' ), true );
			if ( $credentials_option ) {
				$fsms_uname         = $credentials_option['username'];
				$fsms_password      = $credentials_option['password'];
				$admin_number       = $credentials_option['admin_number'];
				$fsms_apikey        = $credentials_option['apikey'];
				$fsms_fromnum       = $credentials_option['from_number'];
				$fsms_fromnum_adver = $credentials_option['from_number_adver'];

				if ( $fsms_uname && $fsms_password && $fsms_fromnum ) {
					self::$username     = self::fsms_tr_num( $fsms_uname );
					self::$password     = self::fsms_tr_num( $fsms_password );
					self::$admin_number = self::fsms_tr_num( $admin_number );
					self::$fromNum      = self::fsms_tr_num( $fsms_fromnum );
					self::$fromNumAdver = self::fsms_tr_num( $fsms_fromnum_adver );
				}

				self::$apiKey = $fsms_apikey;
			}

			$login_notify_options = json_decode( get_option( 'farazsms_login_notify_options' ), true );
			if ( $login_notify_options ) {
				$fsms_sendwm                      = $login_notify_options['welcome_sms'];
				$fsms_welcomep                    = $login_notify_options['welcome_sms_pattern'];
				$fsms_sendwm_with_pattern         = $login_notify_options['welcome_sms_use_pattern'];
				$fsms_welcome_message             = $login_notify_options['welcome_sms_msg'];
				$admin_login_notify_pattern       = $login_notify_options['admin_login_notify_pattern'];
				self::$sendwm                     = $fsms_sendwm === 'true';
				self::$welcomep                   = self::fsms_tr_num( $fsms_welcomep );
				self::$sendwm_with_pattern        = $fsms_sendwm_with_pattern === 'true';
				self::$welcome_message            = self::fsms_tr_num( $fsms_welcome_message );
				self::$admin_login_notify_pattern = self::fsms_tr_num( $admin_login_notify_pattern );
			}

			$comments_options = json_decode( get_option( 'farazsms_comments_options' ), true );
			if ( $comments_options ) {
				self::$comment_phonebook = $comments_options['comment_phonebook'];
			}

			$newsletter_options = json_decode( get_option( 'farazsms_newsletter_options' ), true );
			if ( $newsletter_options ) {
				self::$news_welcome             = $newsletter_options['news_welcome'];
				self::$news_welcome_pattern     = $newsletter_options['news_welcome_pattern'];
				self::$news_send_verify_pattern = $newsletter_options['news_send_verify_pattern'];
			}

		}//end __construct()


		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/*
				 * Farazsms loaded.
				 *
				 * Fires when Farazsms was fully loaded and instantiated.
				 *
				 * @since 1.0.0
				 */
				do_action( 'farazsms_loaded' );
			}

			return self::$instance;

		}//end get_instance()


		public function get_service_sender_number() {
			return self::$fromNum;

		}//end get_service_sender_number()


		/**
		 * @return array|string|string[]
		 */
		public function get_adver_sender_number() {
			return self::$fromNumAdver;

		}//end get_adver_sender_number()


		public static function getAdminNumber() {
			return self::$admin_number;

		}//end getAdminNumber()


		public static function isAPIKeyEntered() {
			return ! empty( self::$apiKey );

		}//end isAPIKeyEntered()


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

		}//end fsms_tr_num()

		/**
		 * Validate mobile number.
		 */
		public static function validate_mobile_number( $phone ) {
			$phone          = self::fsms_tr_num( $phone );
			$mobile_pattern = '/^(\s)*(\+98|0098|98|0)?(9\d{9})(\s*|$)/';
			preg_match( $mobile_pattern, $phone, $matches );
			if ( sizeof( $matches ) !== 5 ) {
				return false;
			}

			return $matches[3];

		}//end validate_mobile_number()

		/**
		 * Farazsms send pattern function.
		 */
		public function farazsms_send_pattern( $pattern, $phone, $input_data ) {
			if ( ! empty( self::$apiKey ) ) {
				$client = new Client( self::$apiKey );
				try {
					$client->sendPattern(
						$pattern,
						self::$fromNum,
						$phone,
						$input_data
					);

					return true;
				} catch ( Error|HttpException|Exception $e ) {
					return false;
				}
			} else {
				$body     = [
					'user'        => self::$username,
					'pass'        => self::$password,
					'fromNum'     => self::$fromNum,
					'op'          => 'pattern',
					'patternCode' => $pattern,
					'toNum'       => $phone,
					'inputData'   => [ $input_data ],
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
				if ( is_wp_error( $response ) ) {
					return false;
				}

				$response = json_decode( $response['body'] );
				if ( $response->status->code !== 0 ) {
					return false;
				}

				return true;
			}//end if

		}//end farazsms_send_pattern()


		/**
		 * Check if API key is valid.
		 */


		public function check_if_apikey_is_valid( $apiKey ) {
			try {
				$client = new Client( $apiKey );

				return $client->validateApiKey();
			} catch ( Error|HttpException $e ) {
				return false;
			}

		}//end check_if_apikey_is_valid()


		/**
		 * Save to phonebook functions.
		 */


		public function save_to_phonebook( $phone, $phonebook ) {
			$phone = self::fsms_tr_num( $phone );
			$body  = [
				'uname'       => self::$username,
				'pass'        => self::$password,
				'op'          => 'phoneBookAdd',
				'phoneBookId' => $phonebook,
				'number'      => $phone,
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
			if ( $response->status->code !== 0 ) {
				return false;
			}

			return true;

		}//end save_to_phonebook()


		public function save_to_phonebookv2( $phone, $phonebook ) {
			$phone   = self::fsms_tr_num( $phone );
			$body    = [
				'uname'       => self::$username,
				'pass'        => self::$password,
				'op'          => 'phoneBookAdd',
				'phoneBookId' => $phonebook,
				'number'      => $phone,
			];
			$handler = curl_init( 'http://ippanel.com/api/select' );
			curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $handler, CURLOPT_POSTFIELDS, json_encode( $body ) );
			curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $handler, CURLOPT_HTTPHEADER, [ 'Content-Type:application/json' ] );
			$response2 = curl_exec( $handler );
			$response2 = json_decode( $response2 );
			if ( $response2->status->code !== 0 ) {
				return false;
			}

			return true;

		}//end save_to_phonebookv2()


		public function save_to_phonebookv3( $data ) {
			if ( empty( self::$apiKey ) || empty( $data ) ) {
				return;
			}

			$client = new Client( self::$apiKey, 'phonebook_api' );
			try {
				return $client->numbersAddList( $data );
			} catch ( Error|HttpException|Exception $e ) {
				return false;
			}

		}//end save_to_phonebookv3()

		public function save_to_phonebookv4( $list ) {
			$body    = [
				'list'       => $list,
			];
			$handler = curl_init( 'http://api.ippanel.com/api/v1/phonebook/numbers-add-list' );
			curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $handler, CURLOPT_POSTFIELDS, json_encode( $body ) );
			curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $handler, CURLOPT_HTTPHEADER, [ 'Authorization: ' . self::$apiKey , 'Content-Type:application/json' ] );

			curl_exec( $handler );

			return true;

		}//end save_to_phonebookv4()


		/**
		 * Send welcome message function.
		 */


		public function send_welcome_message( $phone, $uid ) {
			$phone = self::fsms_tr_num( $phone );
			if ( ! self::$sendwm || $uid === null ) {
				return;
			}

			$user         = get_userdata( $uid );
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
				self::send_message( [ $phone ], $welcome_message, '+98club' );
			} else {
				$patternMessage = self::get_registered_pattern_variables( self::$welcomep );
				if ( strpos( $patternMessage, '%display_name%' ) !== false ) {
					$input_data['display_name'] = $display_name;
				}

				if ( strpos( $patternMessage, '%username%' ) !== false ) {
					$input_data['username'] = $user_name;
				}

				return self::farazsms_send_pattern( self::$welcomep, $phone, $input_data );
			}//end if

		}//end send_welcome_message()


		/**
		 * Check if credentials is valid.
		 *
		 * Only place that we need farazsms username and password.
		 */


		public function check_if_credentials_is_valid() {
			$body = [
				'username' => self::$username,
				'password' => self::$password,
			];

			$response = wp_remote_post(
				'http://reg.ippanel.com/parent/farazsms',
				[
					'method'      => 'POST',
					'headers'     => [ 'Content-Type' => 'application/json' ],
					'data_format' => 'body',
					'body'        => json_encode( $body ),
				]
			);
			// var_dump($response);
			$response = json_decode( $response['body'] );
			if ( $response->message == 1 ) {
				return true;
			}

			return false;

		}//end check_if_credentials_is_valid()


		/**
		 * Get phonebooks.
		 */


		public static function get_phonebooks() {
			$uname = self::$username;
			$pass  = self::$password;
			$body  = [
				'uname' => $uname,
				'pass'  => $pass,
				'op'    => 'booklist',
			];

			$resp = wp_remote_post(
				'http://ippanel.com/api/select',
				[
					'method'      => 'POST',
					'headers'     => [ 'Content-Type' => 'application/json' ],
					'data_format' => 'body',
					'body'        => json_encode( $body ),
				]
			);
			if ( is_wp_error( $resp ) ) {
				return $resp;
			}

			$resp = json_decode( $resp['body'] );
			if ( intval( $resp[0] ) != 0 ) {
				return $resp;
			}

			return json_decode( $resp[1] );

		}//end get_phonebooks()


		/**
		 * Get Lines.
		 */


		public function get_lines() {
			$body = [
				'uname' => self::$username,
				'pass'  => self::$password,
				'op'    => 'lines',
			];
			$resp = wp_remote_post(
				'http://ippanel.com/api/select',
				[
					'method'      => 'POST',
					'headers'     => [ 'Content-Type' => 'application/json' ],
					'data_format' => 'body',
					'body'        => json_encode( $body ),
				]
			);
			$resp = json_decode( $resp['body'] );
			if ( intval( $resp[0] ) != 0 ) {
				return false;
			}

			return json_decode( $resp[1] );

		}//end get_lines()


		/**
		 * Get credit.
		 */


		public function get_credit() {
			if ( ! empty( self::$apiKey ) ) {
				try {
					$client    = new Client( self::$apiKey );
					$credit    = $client->getCredit();
					$separator = '/';
					if ( strpos( $credit, '/' ) ) {
						$separator = '/';
					}

					if ( strpos( $credit, '.' ) ) {
						$separator = '.';
					}

					$credit_rial = explode( $separator, $credit )[0];

					return substr( $credit_rial, 0, - 1 );
				} catch ( Error|HttpException|Exception $e ) {
					return false;
				}
			} else {
				$body     = [
					'uname' => self::$username,
					'pass'  => self::$password,
					'op'    => 'credit',
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
				if ( is_wp_error( $response ) ) {
					return false;
				}

				$response = json_decode( $response['body'] );
				if ( $response[0] !== 0 ) {
					return false;
				}

				$separator = '.';
				if ( strpos( $response[1], '/' ) ) {
					$separator = '/';
				}

				if ( strpos( $response[1], '.' ) ) {
					$separator = '.';
				}

				$credit_rial = explode( $separator, $response[1] )[0];
			}//end if

			return substr( $credit_rial, 0, - 1 );

		}//end get_credit()


		/**
		 * Send low credit notify to admin.
		 */


		public function send_admin_low_credit_to_admin() {
			$fromnum = '3000505';
			if ( empty( self::$admin_number ) ) {
				return;
			}

			$message = __( 'Dear user, The charge for your SMS panel is less than 10 thousand tomans, and your sites SMS may not be sent soon and your site may be blocked. I will charge the SMS system as soon as possible. www.farazsms.com, +982171333036', 'farazsms' );
			if ( ! empty( self::$apiKey ) ) {
				try {
					$client = new Client( self::$apiKey );

					return $client->send(
						$fromnum,
						[ self::$admin_number ],
						$message
					);
				} catch ( Error|HttpException|Exception $e ) {
					return false;
				}
			} else {
				$body     = [
					'uname'   => self::$username,
					'pass'    => self::$password,
					'from'    => $fromnum,
					'op'      => 'send',
					'to'      => [ self::$admin_number ],
					'time'    => '',
					'message' => $message,
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
			}//end if

		}//end send_admin_low_credit_to_admin()


		/**
		 * Get registered pattern variables.
		 */


		public function get_registered_pattern_variables( $pCode ) {
			$body = [
				'uname'       => self::$username,
				'pass'        => self::$password,
				'op'          => 'patternInfo',
				'patternCode' => $pCode,
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
			if ( is_wp_error( $response ) ) {
				return null;
			}

			$response       = json_decode( $response['body'] );
			$patternMessage = $response->data->patternMessage;

			return $patternMessage;

		}//end get_registered_pattern_variables()


		/**
		 * Send comment replay sms.
		 */


		public function send_comment_reply_sms( $phone, $pattern, $data ) {
			$phone = self::fsms_tr_num( $phone );
			if ( empty( $pattern ) ) {
				return;
			}

			$input_data     = [];
			$patternMessage = self::get_registered_pattern_variables( $pattern );
			if ( $patternMessage === null ) {
				return;
			}

			if ( strpos( $patternMessage, '%title%' ) !== false ) {
				$input_data['title'] = $data['title'];
			}

			if ( strpos( $patternMessage, '%name%' ) !== false ) {
				$input_data['name'] = $data['name'];
			}

			if ( strpos( $patternMessage, '%email%' ) !== false ) {
				$input_data['email'] = $data['email'];
			}

			if ( strpos( $patternMessage, '%link%' ) !== false ) {
				$input_data['link'] = $data['link'];
			}

			if ( strpos( $patternMessage, '%content%' ) !== false ) {
				$input_data['content'] = $data['content'];
			}

			return self::farazsms_send_pattern( $pattern, $phone, $input_data );

		}//end send_comment_reply_sms()


		/**
		 * Send comment replay sms to admin
		 */


		public function send_comment_reply_sms_to_admin( $data ) {
			$fsms_admin_notify_pcode = self::fsms_tr_num( self::$admin_login_notify_pattern );
			if ( empty( $fsms_admin_notify_pcode ) || empty( self::$admin_number ) ) {
				return;
			}

			$input_data     = [];
			$patternMessage = self::get_registered_pattern_variables( $fsms_admin_notify_pcode );
			if ( $patternMessage === null ) {
				return;
			}

			if ( strpos( $patternMessage, '%title%' ) !== false ) {
				$input_data['title'] = $data['title'];
			}

			if ( strpos( $patternMessage, '%name%' ) !== false ) {
				$input_data['name'] = $data['name'];
			}

			if ( strpos( $patternMessage, '%email%' ) !== false ) {
				$input_data['email'] = $data['email'];
			}

			if ( strpos( $patternMessage, '%link%' ) !== false ) {
				$input_data['link'] = $data['link'];
			}

			if ( strpos( $patternMessage, '%content%' ) !== false ) {
				$input_data['content'] = $data['content'];
			}

			return self::farazsms_send_pattern( $fsms_admin_notify_pcode, self::$admin_number, $input_data );

		}//end send_comment_reply_sms_to_admin()


		/**
		 * Save comment mobile to phonebook.
		 */


		public function save_comment_mobile_to_phonebook( $phone ) {
			$phone = self::fsms_tr_num( $phone );
			foreach ( self::$comment_phonebook as $phonebookId ) {
				$this->save_to_phonebook( $phone, $phonebookId['value'] );
			}

		}//end save_comment_mobile_to_phonebook()


		/**
		 * Send message.
		 */


		public function send_message( $phones, $message, $sender = null ) {
			if ( ! empty( $sender ) ) {
				$fromnum = $sender;
			} else {
				$fromnum = self::$fromNum;
			}

			if ( ! empty( self::$apiKey ) ) {
				$client = new Client( self::$apiKey );
				try {
					$client->send(
						$fromnum,
						$phones,
						$message
					);

					return true;
				} catch ( Error|HttpException|Exception $e ) {
					return false;
				}
			} else {
				$body     = [
					'uname'   => self::$username,
					'pass'    => self::$password,
					'from'    => $fromnum,
					'op'      => 'send',
					'to'      => $phones,
					'time'    => '',
					'message' => $message,
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
				if ( is_wp_error( $response ) ) {
					return false;
				}

				$response = json_decode( $response['body'] );
				if ( $response->status->code !== 0 ) {
					return false;
				}

				return true;
			}//end if

		}//end send_message()


		/**
		 * Get phonebook numbers.
		 */


		public function get_phonebook_numbers( $phoneBookID ) {
			$body     = [
				'uname'        => self::$username,
				'pass'         => self::$password,
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

		}//end get_phonebook_numbers()


		/**
		 * Send EDD sms.
		 */


		public function send_edd_sms( $phone, $pattern, $data ) {
			$phone = self::fsms_tr_num( $phone );
			if ( empty( $phone ) or empty( $pattern ) or empty( $data ) ) {
				return;
			}

			$input_data     = [];
			$patternMessage = self::get_registered_pattern_variables( $pattern );
			if ( $patternMessage === null ) {
				return;
			}

			if ( strpos( $patternMessage, '%phone%' ) !== false ) {
				$input_data['phone'] = $data['phone'];
			}

			if ( strpos( $patternMessage, '%email%' ) !== false ) {
				$input_data['email'] = $data['email'];
			}

			if ( strpos( $patternMessage, '%first_name%' ) !== false ) {
				$input_data['first_name'] = $data['first_name'];
			}

			if ( strpos( $patternMessage, '%last_name%' ) !== false ) {
				$input_data['last_name'] = $data['last_name'];
			}

			if ( strpos( $patternMessage, '%product%' ) !== false ) {
				$input_data['product'] = $data['product'];
			}

			if ( strpos( $patternMessage, '%price%' ) !== false ) {
				$input_data['price'] = $data['price'];
			}

			if ( strpos( $patternMessage, '%discount%' ) !== false ) {
				$input_data['discount'] = $data['discount'];
			}

			if ( strpos( $patternMessage, '%total_price%' ) !== false ) {
				$input_data['total_price'] = $data['total_price'];
			}

			if ( strpos( $patternMessage, '%link%' ) !== false ) {
				$input_data['link'] = $data['link'];
			}

			if ( strpos( $patternMessage, '%payment_id%' ) !== false ) {
				$input_data['payment_id'] = $data['payment_id'];
			}

			return self::farazsms_send_pattern( $pattern, $phone, $input_data );

		}//end send_edd_sms()


		/**
		 * Send newsletter verification code.
		 */


		public function send_newsletter_verification_code( $phone, $data ) {
			$phone   = self::fsms_tr_num( $phone );
			$pattern = self::$news_send_verify_pattern;
			if ( empty( $phone ) || empty( $pattern ) || empty( $data ) ) {
				return;
			}

			$input_data     = [];
			$patternMessage = self::get_registered_pattern_variables( $pattern );
			if ( $patternMessage === null ) {
				return;
			}

			if ( strpos( $patternMessage, '%code%' ) !== false ) {
				$input_data['code'] = strval( $data['code'] );
			}

			if ( strpos( $patternMessage, '%name%' ) !== false ) {
				$input_data['name'] = strval( $data['name'] );
			}

			return self::farazsms_send_pattern( $pattern, $phone, $input_data );

		}//end send_newsletter_verification_code()

		/**
		 * Save subscriber to DB.
		 */


		public static function save_subscriber_to_db( $data ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'farazsms_newsletter';

			return $wpdb->insert( $table_name, $data );

		}//end save_subscriber_to_db()


		/**
		 * Save generated code to DB
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

		}//end save_generated_code_to_db()


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

		}//end check_if_code_is_valid()

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

		}//end check_if_phone_already_exist()


		/**
		 * Get subscribers.
		 */
		public static function get_subscribers() {
			global $wpdb;
			global $wpdb;
			$table_name = $wpdb->prefix . 'farazsms_newsletter';

			return $wpdb->get_results( "SELECT * FROM $table_name" );

		}//end get_subscribers()


		/**
		 * Delete subscriber.
		 */
		public static function delete_subscriber( $subscriber_id ) {
			global $wpdb;
			$table = $wpdb->prefix . 'farazsms_newsletter';

			return $wpdb->delete( $table, [ 'id' => $subscriber_id ] );

		}//end delete_subscriber()


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

			return ( new Farazsms_Base() )->farazsms_send_pattern( $newsletter_welcomep, $phone, [ 'name' => $name ] );

		}//end send_newsletter_welcome_message()


		/**
		 * Send admins login notification to super admin.
		 */
		public function send_admins_login_notification_to_superadmin( $pattern, $data ) {
			$input_data     = [];
			$patternMessage = self::get_registered_pattern_variables( $pattern );
			if ( strpos( $patternMessage, '%date%' ) !== false ) {
				$input_data['date'] = $data['date'];
			}

			if ( strpos( $patternMessage, '%user_login%' ) !== false ) {
				$input_data['user_login'] = $data['user_login'];
			}

			if ( strpos( $patternMessage, '%display_name%' ) !== false ) {
				$input_data['display_name'] = $data['display_name'];
			}

			return self::farazsms_send_pattern( $pattern, self::$admin_number, $input_data );

		}//end send_admins_login_notification_to_superadmin()



		/**
		 * AFFS send sms.
		 */
		public static function affs_send_sms( $phone, $user_register_pattern, $args ) {
			$input_data     = [];
			$patternMessage = ( new Farazsms_Base() )->get_registered_pattern_variables( $user_register_pattern );
			if ( strpos( $patternMessage, '%user_login%' ) !== false ) {
				$input_data['user_login'] = $args['user_login'];
			}

			if ( strpos( $patternMessage, '%user_nicename%' ) !== false ) {
				$input_data['user_nicename'] = $args['user_nicename'];
			}

			if ( strpos( $patternMessage, '%user_email%' ) !== false ) {
				$input_data['user_email'] = $args['user_email'];
			}

			if ( strpos( $patternMessage, '%display_name%' ) !== false ) {
				$input_data['display_name'] = $args['display_name'];
			}

			if ( strpos( $patternMessage, '%user_mobile%' ) !== false ) {
				$input_data['user_mobile'] = $args['user_mobile'];
			}

			if ( strpos( $patternMessage, '%amount%' ) !== false ) {
				$input_data['amount'] = $args['amount'];
			}

			if ( strpos( $patternMessage, '%amount%' ) !== false ) {
				$input_data['amount'] = $args['amount'];
			}

			return ( new Farazsms_Base() )->farazsms_send_pattern( $user_register_pattern, $phone, $input_data );

		}//end affs_send_sms()


		/**
		 * Send feedback message to server.
		 */


		public function send_feedback_message_to_server( $feedback_message ) {
			$body = [
				'uname'            => self::$username,
				'pass'             => self::$password,
				'subject'          => __( 'Direct feedback from Faraz SMS plugin', 'farazsms' ),
				'description'      => $feedback_message . PHP_EOL . get_site_url(),
				'type'             => 'fiscal',
				// 'fiscal','webservice','problem','lineservices'
				'importance'       => 'low',
				// 'low','middle','quick','acute'
				'sms_notification' => 'no',
				// 'yes','no'
				'file'             => '',
				'op'               => 'ticketadd',
			];

			$handler = curl_init( 'http://ippanel.com/services.jspd' );
			curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $handler, CURLOPT_POSTFIELDS, $body );
			curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
			$response = curl_exec( $handler );
			$response = json_decode( $response );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			if ( $response[0] !== 0 || ! $response[1] ) {
				return false;
			}

			return true;

		}//end send_feedback_message_to_server()

	}//end class

	/*
	 *  Prepare if class 'Farazsms_Base' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Farazsms_Base::get_instance();
}//end if


if ( ! function_exists( 'farazsms_base' ) ) {


	/**
	 * Get global class.
	 *
	 * @return object
	 */
	function farazsms_base() {
		return Farazsms_Base::get_instance();

	}//end farazsms_base()


}