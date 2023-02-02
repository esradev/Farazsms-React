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
		public static $comment_phonebook;

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

		}

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

		}

		/**
		 * Farazsms send pattern function.
		 */
		public static function farazsms_send_pattern( $pattern, $phone, $input_data ) {
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
			}

		}

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

		}

		public static function save_to_phonebookv4( $list ) {
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

		}


		/**
		 * Send welcome message function.
		 */
		public function send_welcome_message( $phone, $user_id ) {
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
				self::send_message( [ $phone ], $welcome_message, '+98club' );
			} else {
				$patternMessage = self::get_registered_pattern_variables( self::$welcomep );
				if ( str_contains( $patternMessage, '%display_name%' ) ) {
					$input_data['display_name'] = $display_name;
				}

				if ( str_contains( $patternMessage, '%username%' ) ) {
					$input_data['username'] = $user_name;
				}

				return self::farazsms_send_pattern( self::$welcomep, $phone, $input_data );
			}//end if

		}


		/**
		 * Check if credentials is valid.
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
			$response = json_decode( $response['body'] );
			if ( $response->message == 1 ) {
				return true;
			}
			return false;
		}


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
		}


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
		}

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
			}

			return substr( $credit_rial, 0, - 1 );

		}


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
			}

		}


		/**
		 * Get registered pattern variables.
		 */
		public static function get_registered_pattern_variables( $pCode ) {
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
			return $response->data->patternMessage;
		}


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

		public static function send_message( $phones, $message, $sender = null ) {
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