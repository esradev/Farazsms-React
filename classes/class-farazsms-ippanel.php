<?php

/**
 * Define the IPPanel API class.
 *
 * @since      2.0.0
 * @package    Farazsms
 * @subpackage Farazsms/classes
 * @author     FarazSMS <info@farazsms.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Ippanel.
 */
class Farazsms_Ippanel {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

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
	}

	/**
	 * Get phonebooks.
	 *
	 * @since 2.0.0
	 */
	public static function get_phonebooks() {
		// Try the first method: wp_remote_post
		$url = 'https://ippanel.com/services.jspd';
		$data = [
			'uname' => Farazsms_Base::$username,
			'pass' => Farazsms_Base::$password,
			'op' => 'booklist'
		];
		$args = [
			'body' => $data,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'sslverify' => false, // Set to true if your server has a valid SSL certificate
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded'
			]
		];
		$response = wp_remote_post( $url, $args );
		if ( ! is_wp_error( $response ) ) {
			$response_body = wp_remote_retrieve_body( $response );
			$response_data = json_decode( $response_body );
			$res_code = $response_data[0];
			$res_data = json_decode($response_data[1], true);
			return $res_data;
		}

		if (is_wp_error($response)) {
			// Try the second method: wp_remote_request
			$args = [
				'method'      => 'GET',
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.0',
				'headers'     => [
					'Authorization' => Farazsms_Base::$apiKey,
					'Content-Type'  => 'application/json'
				],
			];
			$response = wp_remote_request( 'http://api.ippanel.com/api/v1/phonebook/phonebooks', $args );
			if ( ! is_wp_error( $response ) ) {
				$response_code = wp_remote_retrieve_response_code( $response );
				if ( $response_code == 200 ) {
					$response_body = wp_remote_retrieve_body( $response );
					$response_data = json_decode( $response_body, true );
					if ( $response_data ) {
						return $response_data['data'];
					}
				}
			}
		}
		// Both methods failed, return false
		return false;
	}


	/**
	 * Save to a phone to phonebook functions.
	 *
	 * @since 2.0.0
	 */
	public static function save_a_phone_to_phonebook( $phone, $phonebook_id ) {
		$phone   = Farazsms_Base::fsms_tr_num( $phone );
		$body    = [
			'uname'       => Farazsms_Base::$username,
			'pass'        => Farazsms_Base::$password,
			'op'          => 'phoneBookAdd',
			'phoneBookId' => $phonebook_id,
			'number'      => $phone,
		];

		$args = [
			'body'        => json_encode( $body ),
			'headers'     => [ 'Content-Type' => 'application/json' ],
			'method'      => 'POST',
			'data_format' => 'body',
		];
		$response = wp_remote_post( 'http://ippanel.com/api/select', $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$res = json_decode( wp_remote_retrieve_body( $response ), true );
//		if ( $res->status->code !== 0 ) {
//			return false;
//		}

		return true;
	}

	/**
	 * Save list of phones to phonebook
	 *
	 * @param array $list
	 *
	 * @return false|mixed|null
	 * @since 2.0.0
	 */
	public static function save_list_of_phones_to_phonebook( array $list ) {
		$body = [
			'list' => $list,
		];
		$headers = [
			'Authorization' => Farazsms_Base::$apiKey,
			'Content-Type' => 'application/json',
		];
		$args = [
			'method' => 'POST',
			'headers' => $headers,
			'body' => json_encode( $body ),
		];
		$response = wp_remote_post( 'http://api.ippanel.com/api/v1/phonebook/numbers-add-list', $args );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$res = json_decode( wp_remote_retrieve_body( $response ), true );
		return $res;
	}

	/**
	 * Get registered pattern variables.
	 *
	 * @since 2.0.0
	 */
	public static function get_registered_pattern_variables( $patternCode ) {
		$uname = Farazsms_Base::$username;
		$pass  = Farazsms_Base::$password;
		$body  = [
			'uname'       => $uname,
			'pass'        => $pass,
			'op'          => 'patternInfo',
			'patternCode' => $patternCode,
		];

		$res   = wp_remote_post(
			'http://ippanel.com/api/select',
			[
				'method'      => 'POST',
				'headers'     => [ 'Content-Type' => 'application/json' ],
				'data_format' => 'body',
				'body'        => json_encode( $body ),
			]
		);
		if ( is_wp_error( $res ) ) {
			return $res;
		}
		$res = json_decode( $res['body'] , true);

		return $res['data']['patternMessage'] ?? null;
	}


	/**
	 * Send Message function.
	 *
	 * @param $phones
	 * @param $message
	 * @param $sender
	 *
	 * @return array|mixed|WP_Error|null
	 */
	public static function send_message( $phones ,$message, $sender = '') {

		if ( ! $sender) {
			$sender = Farazsms_Base::$fromNum;
		}

		$body     = [
			'op'      => 'send',
			'uname'   => Farazsms_Base::$username,
			'pass'    => Farazsms_Base::$password,
			'message' => $message,
			'from'    => $sender,
			'to'      => $phones,
			'time'    => '',
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
			return $response;
		}

		$response = json_decode( $response['body'] );
		if ( $response->status->code !== 0 ) {
			return $response;
		}

		return $response;
	}

	public static function send_sms_to_phonebooks ($phonebooks_ids, $message ,$sender = '' ) {
		if ( ! $sender) {
			$sender = Farazsms_Base::$fromNum;
		}

		$url = 'https://ippanel.com/services.jspd';

		$param = [
			'uname' => Farazsms_Base::$username,
			'pass' => Farazsms_Base::$password,
			'from' => $sender,
			'message' => $message,
			'bookid' => json_encode($phonebooks_ids),
			'op' => 'booksend'
		];

		$response = wp_remote_post( $url, [
			'method' => 'POST',
			'body' => $param
		] );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return "Something went wrong: $error_message";
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			$response_data = json_decode( $response_body );
			$res_code = $response_data[0];
			$res_data = $response_data[1];
			return $res_data;
		}
	}

	/**
	 * Send Webservice Single.
	 *
	 * @param array $recipient
	 * @param $sender
	 * @param $message
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public static function send_webservice_single( array $recipient, $sender, $message ) {
		if ( empty( $sender ) ) {
			$sender = Farazsms_Base::$fromNum;
		}

		$body = [
			'recipient' => $recipient,
			'sender'    => $sender,
			'message'   => $message
		];

		$args = [
			'method'      => 'POST',
			'headers'     => [
				'accept'        => 'application/json',
				'Authorization' => Farazsms_Base::$apiKey,
				'Content-Type'  => 'application/json'
			],
			'body'        => json_encode( $body ),
			'data_format' => 'body',
		];

		$response = wp_remote_post( 'http://api.ippanel.com/api/v1/sms/send/webservice/single', $args );

		if ( is_wp_error( $response ) ) {
			return false;
		} else {
			$res = json_decode( wp_remote_retrieve_body( $response ) );
			return true;
		}
	}


	/**
	 * Get credit.
	 *
	 * @return false|string
	 * @since 2.0.0
	 *
	 */
	public static function get_credit() {
		$body     = [
			'uname' => Farazsms_Base::$username,
			'pass'  => Farazsms_Base::$password,
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

		$response = json_decode( $response['body'], true );
		if (!is_array($response) || !isset($response[1])) {
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

		return substr( $credit_rial, 0, - 1 );
	}


	/**
	 * Send low credit notify to admin.
	 *
	 * @return void
	 * @since 2.0.0
	 *
	 */
	public static function send_admin_low_credit_to_admin() {
		$fromnum = '3000505';
		if ( empty( Farazsms_Base::$admin_number ) ) {
			return;
		}
		$message  = __( 'Dear user, The charge for your SMS panel is less than 10 thousand tomans, and your sites SMS may not be sent soon and your site may be blocked. I will charge the SMS system as soon as possible. www.farazsms.com, +982171333036', 'farazsms' );
		$body     = [
			'uname'   => Farazsms_Base::$username,
			'pass'    => Farazsms_Base::$password,
			'from'    => $fromnum,
			'op'      => 'send',
			'to'      => [ Farazsms_Base::$admin_number ],
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
		json_decode( $response['body'] );
	}


	/**
	 * Send pattern.
	 *
	 * @param $pattern
	 * @param $phone
	 * @param $input_data
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public static function send_pattern($pattern, $phone, $input_data ) {
		$body     = [
			'user'        => Farazsms_Base::$username,
			'pass'        => Farazsms_Base::$password,
			'fromNum'     => Farazsms_Base::$fromNum,
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

		$response = json_decode( $response['body'] , true );
//		if ( $response['status']['code'] !== 0 ) {
//			return false;
//		}

		return true;
	}

	/**
	 * Check if credentials is valid.
	 */
	public static function check_if_credentials_is_valid() {
		$body = [
			'username' => Farazsms_Base::$username,
			'password' => Farazsms_Base::$password,
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

	public static function send_timed_sms ($phone_number, $date, $message) {
		// Define the endpoint URL and request parameters
		$url = 'https://api2.ippanel.com/api/v1/sms/send/webservice/single';
		$params = [
			'recipient' => [ $phone_number ],
			'sender' => '+983000505',
			'time' => $date,
			'message' => $message
		];
		$headers = [
			'Accept' => 'application/json',
			'Apikey' => Farazsms_Base::$apiKey,
			'Content-Type' => 'application/json'
		];

		// Make the wp_remote_post() request
		$response = wp_remote_post($url, [
			'headers' => $headers,
			'body' => json_encode($params),
		] );

		// Check for errors and output the response data
		if (is_wp_error($response)) {
			echo 'Error: ' . $response->get_error_message();
		} else {
			$response_data = json_decode(wp_remote_retrieve_body($response));
		}
	}
}

Farazsms_Ippanel::get_instance();

