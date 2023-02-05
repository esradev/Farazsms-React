<?php

/**
 * Farazsms edd.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Edd.
 */
class Farazsms_Edd {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	public static $edd_phonebook;
	public static $edd_send_to_user;
	public static $edd_user_pattern;
	public static $edd_send_to_admin;
	public static $edd_admin_pattern;


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
		$edd_options = json_decode( get_option( 'farazsms_edd_options' ), true );
		if ( $edd_options ) {
			self::$edd_phonebook     = $edd_options['edd_phonebook'];
			self::$edd_send_to_user  = $edd_options['edd_send_to_user'];
			self::$edd_user_pattern  = $edd_options['edd_user_pattern'];
			self::$edd_send_to_admin = $edd_options['edd_send_to_admin'];
			self::$edd_admin_pattern = $edd_options['edd_admin_pattern'];
		}

		add_action( 'edd_payment_personal_details_list', [ $this, 'fsms_edd_show_phone' ], 10, 2 );
		add_action( 'edd_complete_purchase', [ $this, 'fsms_edd_complete_purchase_action' ], 10, 3 );
		add_action( 'edd_purchase_form_user_info_fields', [ $this, 'fsms_show_mobile_field_checkout_field' ] );
		add_action( 'edd_payment_meta', [ $this, 'fsms_show_mobile_meta' ] );
		add_action( 'edd_checkout_error_checks', [ $this, 'fsms_validate_mobile_field' ], 10, 2 );

	}

	/**
	 * EDD show phone.
	 */
	public function fsms_edd_show_phone( $payment_meta, $user_info ) {
		$phone = $payment_meta['phone'] ?? __( 'User not logged in', 'farazsms' );
		?>
        <li style="list-style: none; margin-top: 5px;">'<?php esc_attr_e( 'Phone number: ', 'farazsms' ) ?>
            '<?php echo $phone ?></li>
		<?php
	}

	/**
	 * EDD complete purchase action.
	 */
	public function fsms_edd_complete_purchase_action( $payment_id, $payment, $customer ) {

		$payment_meta = edd_get_payment_meta( $payment_id );
		$mobile       = $payment_meta['phone'];
		$list         = $this->get_edd_order_data( $payment_meta );
		if ( self::$edd_send_to_user ) {
			$this->send_edd_sms( $mobile, self::$edd_user_pattern, $list );
		}
		if ( self::$edd_send_to_admin ) {
			$this->send_edd_sms( Farazsms_Base::$admin_number, self::$edd_admin_pattern, $list );
		}

		$edd_phonebook    = array_column( self::$edd_phonebook, 'value' );
		$edd_phonebook_id = current( $edd_phonebook );

		$list[] = (object) [
			'number'       => $mobile,
			'name'         => '',
			'options'      => (object) [ '100' => 'value' ],
			'phonebook_id' => (int) $edd_phonebook_id
		];
		Farazsms_Base::save_list_of_phones_to_phonebook( $list );
	}

	/**
	 * Get edd order data.
	 */
	public function get_edd_order_data( $payment_meta ) {
		$result               = null;
		$result['phone']      = $payment_meta['phone'];
		$result['email']      = $payment_meta['email'];
		$result['first_name'] = $payment_meta['user_info']['first_name'];
		$result['last_name']  = $payment_meta['user_info']['last_name'];

		for ( $i = 0; $i < count( $payment_meta['cart_details'] ); $i ++ ) {
			if ( isset( $result['product'] ) ) {
				$result['product'] .= ' - ' . $payment_meta['cart_details'][ $i ]['name'] . '(' . $payment_meta['cart_details'][ $i ]['quantity'] . ')';
			} else {
				$result['product'] = $payment_meta['cart_details'][ $i ]['name'] . '(' . $payment_meta['cart_details'][ $i ]['quantity'] . ')';
			}
			if ( isset( $result['price'] ) ) {
				$result['price'] = intval( $result['price'] ) + intval( $payment_meta['cart_details'][ $i ]['subtotal'] );
			} else {
				$result['price'] = intval( $payment_meta['cart_details'][ $i ]['subtotal'] );
			}
			if ( isset( $result['discount'] ) ) {
				$result['discount'] = intval( $result['discount'] ) + intval( $payment_meta['cart_details'][ $i ]['discount'] );
			} else {
				$result['discount'] = intval( $payment_meta['cart_details'][ $i ]['discount'] );
			}
			if ( isset( $result['total_price'] ) ) {
				$result['total_price'] = intval( $result['total_price'] ) + intval( $payment_meta['cart_details'][ $i ]['price'] );
			} else {
				$result['total_price'] = intval( $payment_meta['cart_details'][ $i ]['price'] );
			}
		}

		for ( $i = 0; $i < count( $payment_meta['downloads'] ); $i ++ ) {
			$files = edd_get_download_files( $payment_meta['downloads'][ $i ]['id'], $variable_price_id = null );
			if ( $files ) {
				foreach ( $files as $key => $file ) {
					if ( ! isset( $result['link'] ) ) {
						$result['link'] .= $file['file'] . ' ';
					} else {
						$result['link'] = $file['file'] . ' | ';
					}
				}
			}
		}

		$result['price']       = number_format( intval( $result['price'] ) );
		$result['discount']    = number_format( intval( $result['discount'] ) );
		$result['total_price'] = number_format( intval( $result['total_price'] ) );

		return $result;
	}

	/**
	 * Show mobile field checkout field.
	 */
	public function fsms_show_mobile_field_checkout_field() {
		$user   = wp_get_current_user();
		$mobile = get_user_meta( $user->ID, 'digits_phone' )[0];
		if ( str_contains( $mobile, '+98' ) ) {
			$mobile = str_replace( '+98', '0', $mobile );
		} else {
			$mobile = '';
		}
		?>
        <p id="edd-phone-wrap">
            <label class="edd-label" for="edd-phone"><?php esc_attr_e( 'Phone number: ', 'farazsms' ) ?></label>
            <span class="edd-description">
                <?php esc_attr_e( 'We use this to send order information.', 'farazsms' ) ?>
            </span>
            <input class="edd-input" type="text" name="edd_phone" id="edd-phone"
                   placeholder="<?php esc_attr_e( 'Phone number: ', 'farazsms' ) ?>" value="<?php echo $mobile ?>"/>
        </p>
		<?php
	}

	/**
	 * Show mobile meta.
	 */
	public function fsms_show_mobile_meta( $payment_meta ) {
		if ( 0 !== did_action( 'edd_pre_process_purchase' ) ) {
			$payment_meta['phone'] = isset( $_POST['edd_phone'] ) ? sanitize_text_field( $_POST['edd_phone'] ) : '';
		}

		return $payment_meta;
	}

	/**
	 * Show validate mobile field.
	 */
	public function fsms_validate_mobile_field( $valid_data, $data ) {
		if ( empty( $data['edd_phone'] ) or ! preg_match( '/^09[0-9]{9}$/', $data['edd_phone'] ) ) {
			edd_set_error( 'empty_phone', __( 'Enter a valid phone number.' ) );
		}
	}

	/**
	 * Send EDD sms.
	 */


	public function send_edd_sms( $phone, $pattern, $data ) {
		$phone = Farazsms_Base::fsms_tr_num( $phone );
		if ( empty( $phone ) or empty( $pattern ) or empty( $data ) ) {
			return;
		}

		$input_data     = [];
		$patternMessage = Farazsms_Base::get_registered_pattern_variables( $pattern );
		if ( $patternMessage === null ) {
			return;
		}

		if ( str_contains( $patternMessage, '%phone%' ) ) {
			$input_data['phone'] = $data['phone'];
		}

		if ( str_contains( $patternMessage, '%email%' ) ) {
			$input_data['email'] = $data['email'];
		}

		if ( str_contains( $patternMessage, '%first_name%' ) ) {
			$input_data['first_name'] = $data['first_name'];
		}

		if ( str_contains( $patternMessage, '%last_name%' ) ) {
			$input_data['last_name'] = $data['last_name'];
		}

		if ( str_contains( $patternMessage, '%product%' ) ) {
			$input_data['product'] = $data['product'];
		}

		if ( str_contains( $patternMessage, '%price%' ) ) {
			$input_data['price'] = $data['price'];
		}

		if ( str_contains( $patternMessage, '%discount%' ) ) {
			$input_data['discount'] = $data['discount'];
		}

		if ( str_contains( $patternMessage, '%total_price%' ) ) {
			$input_data['total_price'] = $data['total_price'];
		}

		if ( str_contains( $patternMessage, '%link%' ) ) {
			$input_data['link'] = $data['link'];
		}

		if ( str_contains( $patternMessage, '%payment_id%' ) ) {
			$input_data['payment_id'] = $data['payment_id'];
		}

		return Farazsms_Base::farazsms_send_pattern( $pattern, $phone, $input_data );

	}//end send_edd_sms()

}

Farazsms_Edd::get_instance();
