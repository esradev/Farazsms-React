<?php

/**
 * Farazsms membership.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}
/**
 * Class Farazsms_Membership.
 */
class Farazsms_Membership
{
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	private static $ihc_send_first_notify;
	private static $ihc_send_second_notify;
	private static $ihc_send_third_notify;
	private static $ihc_first_notify_msg;
	private static $ihc_notify_before_time;
	private static $pmp_send_expire_notify;
	private static $pmp_expire_notify_msg;

	/**
	 * Initiator
	 *
	 * @since 2.0.0
	 * @return object Initialized object of class.
	 */
	public static function get_instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$membership_options = json_decode( get_option( 'farazsms_membership_options' ), true );
		if ( $membership_options ) {
			self::$ihc_send_first_notify  = $membership_options['ihc_send_first_notify'];
			self::$ihc_send_second_notify = $membership_options['ihc_send_second_notify'];
			self::$ihc_send_third_notify  = $membership_options['ihc_send_third_notify'];
			self::$ihc_first_notify_msg   = $membership_options['ihc_first_notify_msg'];
			self::$ihc_notify_before_time = $membership_options['ihc_notify_before_time'];
			self::$pmp_send_expire_notify = $membership_options['pmp_send_expire_notify'];
			self::$pmp_expire_notify_msg  = $membership_options['pmp_expire_notify_msg'];
		}

		add_filter( 'ihc_filter_notification_before_expire', [ $this, 'fsms_first_notification_before_expire' ], 10, 4 );
		add_action( 'pmpro_membership_post_membership_expiry', [ $this, 'fsms_pmp_membership_membership_expiry' ], 10, 2 );
	}

	/**
	 * First notification before expire
	 */
	public function fsms_first_notification_before_expire( $sent = false, $uid = 0, $lid = 0, $type = '' ) {
		$types                  = [];
		$types[]                = ( self::$ihc_send_first_notify === true ) ? 'before_expire' : '';
		$types[]                = ( self::$ihc_send_second_notify === true ) ? 'second_before_expire' : '';
		$types[]                = ( self::$ihc_send_third_notify === true ) ? 'third_before_expire' : '';
		if ( empty( self::$ihc_first_notify_msg ) || ! in_array( $type, $types ) ) {
			return $sent;
		}
		$phone = get_user_meta( $uid, 'digits_phone', true );
		if ( empty( $phone ) ) {
			return $sent;
		}
		$user      = get_userdata( $uid );
		$message   = str_replace( [
			'%name%',
			'%time%',
		], [
			$user->display_name,
			self::$ihc_notify_before_time,
		], self::$ihc_first_notify_msg );
		Farazsms_Ippanel::send_message( [ $phone ], $message, '+98club' );

		return $sent;
	}

	/**
	 * PMP membership expiry
	 */

	public function fsms_pmp_membership_membership_expiry( $user_id, $membership_id ) {
		if ( self::$pmp_send_expire_notify !== true || empty( self::$pmp_expire_notify_msg ) ) {
			return;
		}

		$phone              = get_user_meta( $user_id, 'digits_phone', true );

		if ( empty( $phone ) && ! empty( Farazsms_Base::$custom_phone_meta_keys_id ) ) {
				$phone = get_user_meta( $user_id, Farazsms_Base::$custom_phone_meta_keys_id, true );
				if ( ! empty( $phone ) && Farazsms_Base::validate_mobile_number( $phone ) ) {
					return;
				}
		}
		if ( empty( $phone ) ) {
			return;
		}
		$user    = get_userdata( $user_id );
		$message = str_replace( [
			'%display_name%',
		], [
			$user->display_name,
		], self::$pmp_expire_notify_msg );
		Farazsms_Ippanel::send_message( [ $phone ], $message, '+98club' );
	}
}
Farazsms_Membership::get_instance();
