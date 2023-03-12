<?php

/**
 * Farazsms digits.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Digits.
 */
class Farazsms_Digits {
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
		add_filter( 'update_user_metadata', [ $this, 'monitor_update_user_metadata' ], 499, 4 );
		add_action( 'profile_update', [ $this, 'fsms_user_profile_updated' ], 99, 2 );
	}

	/**
	 * Monitor update user metadata.
	 */
	public function monitor_update_user_metadata( $check, $object_id, $meta_key, $meta_value ) {
		$selected_meta_keys = Farazsms_Base::$custom_phone_meta_keys_id ?? '';
		if ( $meta_key === $selected_meta_keys ) {
			return $check;
		}
		$phone = Farazsms_Base::validate_mobile_number( $meta_value );
		if ( ! $phone ) {
			return $check;
		}

		if ( empty( Farazsms_Base::$custom_phonebook_id ) ) {
			return $check;
		}
		$user_info = get_userdata( $object_id );
		$list      = [];

		$list[0] = (object) [
			'number'       => $phone,
			'name'         => $user_info->display_name ?? '',
			'phonebook_id' => (int) Farazsms_Base::$custom_phonebook_id
		];
		Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

		Farazsms_Base::send_welcome_message( $phone, $object_id );

		return $check;
	}

	/**
	 * User profile updated.
	 */
	public function fsms_user_profile_updated( $user_id, $old_user_data ) {
		$digits_phone = get_user_meta( $user_id, 'digits_phone', true );
		if ( empty( $digits_phone ) ) {
			return;
		}

		$user_info = get_userdata( $user_id );

		$list    = [];
		$list[0] = (object) [
			'number'       => $digits_phone,
			'name'         => $user_info->display_name ?? '',
			'phonebook_id' => (int) Farazsms_Base::$digits_phonebook_id
		];

		Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

		$already_sent_one = get_user_meta( $user_id, 'sent_welcome_message', true );
		if ( ! empty( $already_sent_one ) && $already_sent_one == '1' ) {
			return;
		}
		Farazsms_Base::send_welcome_message( $digits_phone, $user_id );
		update_user_meta( $user_id, 'sent_welcome_message', '1' );
	}


}

Farazsms_Digits::get_instance();
