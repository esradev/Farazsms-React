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
		$handler = curl_init( 'http://api.ippanel.com/api/v1/phonebook/phonebooks' );
		curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'GET' );
		curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handler, CURLOPT_HTTPHEADER, [
			'Authorization: ' . Farazsms_Base::$apiKey,
			'Content-Type:application/json'
		] );

		$res = curl_exec( $handler );
		$res = json_decode( $res, true );

		if ( ! $res ) {
			return false;
		} else {
			return $res;
		}

	}

	public static function get_phonebooks_old_version() {
		$uname = Farazsms_Base::$username;
		$pass  = Farazsms_Base::$password;
		$body  = [
			'uname' => $uname,
			'pass'  => $pass,
			'op'    => 'booklist',
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
		$res = json_decode( $res['body'] );
		if ( intval( $res[0] ) != 0 ) {
			return $res;
		}

		return json_decode( $res[1] );
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
		$handler = curl_init( 'http://ippanel.com/api/select' );
		curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $handler, CURLOPT_POSTFIELDS, json_encode( $body ) );
		curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handler, CURLOPT_HTTPHEADER, [ 'Content-Type:application/json' ] );
		$res = curl_exec( $handler );
		$res = json_decode( $res, true );
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
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public static function save_list_of_phones_to_phonebook( array $list ) {
		$body    = [
			'list' => $list,
		];
		$handler = curl_init( 'http://api.ippanel.com/api/v1/phonebook/numbers-add-list' );
		curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $handler, CURLOPT_POSTFIELDS, json_encode( $body ) );
		curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handler, CURLOPT_HTTPHEADER, [
			'Authorization: ' . Farazsms_Base::$apiKey,
			'Content-Type:application/json'
		] );

		$res = curl_exec( $handler );

		return json_decode( $res );
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
	 * Send message.
	 *
	 * @since 2.0.0
	 */
	public static function send_message( $phones, $message, $sender = null ) {
		if ( ! empty( $sender ) ) {
			$fromnum = $sender;
		} else {
			$fromnum = Farazsms_Base::$fromNum;
		}

		$body     = [
			'uname'   => Farazsms_Base::$username,
			'pass'    => Farazsms_Base::$password,
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

		$body    = [
			'recipient' => $recipient,
			'sender'    => $sender,
			'message'   => $message
		];
		$handler = curl_init( 'http://api.ippanel.com/api/v1/sms/send/webservice/single' );
		curl_setopt( $handler, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $handler, CURLOPT_POSTFIELDS, json_encode( $body ) );
		curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handler, CURLOPT_HTTPHEADER, [
			'accept: application/json',
			'Authorization: ' . Farazsms_Base::$apiKey,
			'Content-Type:application/json'
		] );

		$res = curl_exec( $handler );
		$res = json_decode( $res );

		return true;
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
}

Farazsms_Ippanel::get_instance();

