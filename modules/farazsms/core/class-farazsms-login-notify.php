<?php

/**
 * Farazsms login notify.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Login_Notify.
 */
class Farazsms_Login_Notify {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	private static $admin_login_notify;
	private static $admin_login_notify_pattern;
	private static $select_roles;

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
		$login_notify_options = json_decode( get_option( 'farazsms_login_notify_options' ), true );
		if ( $login_notify_options ) {
			self::$admin_login_notify         = $login_notify_options['admin_login_notify'];
			self::$admin_login_notify_pattern = $login_notify_options['admin_login_notify_pattern'];
			self::$select_roles               = $login_notify_options['select_roles'];
		}

		add_action( 'wp_login', [ $this, 'fsms_admin_login_action' ], 10, 2 );
		add_action( 'wp_login', [ $this, 'fsms_admin_roles_login_action' ], 11, 2 );

	}

	/**
	 * Admin login action.
	 */
	public function fsms_admin_login_action( $user_login, $user ) {
		if ( ! in_array( 'administrator', (array) $user->roles ) ) {
			return;
		}
		$last_notification = get_user_meta( $user->ID, 'farazsms_low_credit_notify_sent_timestamp', true );
		if ( ! empty( $last_notification ) ) {
			$dif = time() - $last_notification;
			if ( $dif < 86400 ) {
				return;
			}
		}

		$credit = Farazsms_Ippanel::get_credit();
		if ( ! $credit ) {
			return;
		}
		if ( (int) $credit < 10000 ) {
			Farazsms_Ippanel::send_admin_low_credit_to_admin();
			update_user_meta( $user->ID, 'farazsms_low_credit_notify_sent_timestamp', time() );
		}
	}

	/**
	 * Admin rules login action
	 */
	public function fsms_admin_roles_login_action( $user_login, $user ) {
		if ( empty( self::$select_roles ) ) {
			return;
		}

		if ( self::$admin_login_notify !== true || empty( self::$admin_login_notify_pattern ) ) {
			return;
		}
		$data['date']         = date_i18n( 'H:i:s d-m-Y' );
		$data['user_login']   = $user->user_login;
		$data['display_name'] = $user->display_name;
		// Retrieve all roles of the logged-in user
		$user_roles = $user->roles;

		// Check if any of the user's roles match the selected roles
		$select_roles = json_decode(self::$select_roles, true); // Convert the JSON string to an associative array
		$selected_role_values = array_column($select_roles, 'value'); // Get an array of role values

		foreach ($user_roles as $user_role) {
			if (in_array($user_role, $selected_role_values)) {
				// Current user's role matches one of the selected roles, proceed with sending the notification
				$data['date'] = date_i18n('H:i:s d-m-Y');
				$data['user_login'] = $user->user_login;
				$data['display_name'] = $user->display_name;
				self::send_admins_login_notification_to_superadmin(self::$admin_login_notify_pattern, $data);
				break; // Exit the loop if a match is found
			}
		}
	}

	/**
	 * Send admins login notification to super admin.
	 */
	public function send_admins_login_notification_to_superadmin( $pattern, $data ) {
		$input_data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
		if ( str_contains( $patternMessage, '%date%' ) ) {
			$input_data['date'] = $data['date'];
		}
		if ( str_contains( $patternMessage, '%user_login%' ) ) {
			$input_data['user_login'] = $data['user_login'];
		}
		if ( str_contains( $patternMessage, '%display_name%' ) ) {
			$input_data['display_name'] = $data['display_name'];
		}

		return Farazsms_Ippanel::send_pattern( $pattern, Farazsms_Base::$admin_number, $input_data );
	}
}

Farazsms_Login_Notify::get_instance();
