<?php
/**
 * Define the routes for this plugin for enable REST Routs for API.
 *
 * @since    2.0.0
 * @access   private
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Routes.
 */
class Farazsms_Routes {
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
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'farazsms/v' . $version;

		//Register settings_options rest route
		register_rest_route( $namespace, '/' . 'settings_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_settings_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_settings_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register login_notify_options rest route
		register_rest_route( $namespace, '/' . 'login_notify_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_login_notify_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],

			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_login_notify_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register phonebook_options rest route
		register_rest_route( $namespace, '/' . 'phonebook_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_phonebook_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],

			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_phonebook_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register comments_options rest route
		register_rest_route( $namespace, '/' . 'comments_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_comments_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_comments_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register newsletter_options rest route
		register_rest_route( $namespace, '/' . 'newsletter_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_newsletter_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_newsletter_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register elementor_options rest route
		register_rest_route( $namespace, '/' . 'elementor_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_elementor_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_elementor_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register woocommerce_options rest route
		register_rest_route( $namespace, '/' . 'woocommerce_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_woocommerce_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_woocommerce_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register edd_options rest route
		register_rest_route( $namespace, '/' . 'edd_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_edd_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_edd_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register aff_options rest route
		register_rest_route( $namespace, '/' . 'aff_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_aff_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_aff_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register membership_options rest route
		register_rest_route( $namespace, '/' . 'membership_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_membership_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_membership_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register integrations_options rest route
		register_rest_route( $namespace, '/' . 'integrations_options', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_integrations_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_integrations_options' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		//Register user meta rest route
		register_rest_route( $namespace, '/' . 'usermeta', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_usermeta' ],
				'permission_callback' => [ $this, 'permissions_check' ],

			],
		] );

		//Register sync_woo rest route
		register_rest_route( $namespace, '/' . 'sync_woo', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'sync_woo' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register sync_bookly rest route
		register_rest_route( $namespace, '/' . 'sync_bookly', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'sync_bookly' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register sync_digits rest route
		register_rest_route( $namespace, '/' . 'sync_digits', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'sync_digits' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register validate_apikey rest route
		register_rest_route( $namespace, '/' . 'validate_apikey', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'validate_apikey' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register get_phonebook_numbers rest route
		register_rest_route( $namespace, '/' . 'get_phonebook_numbers', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_phonebook_numbers' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register get_subscribers_from_db rest route
		register_rest_route( $namespace, '/' . 'get_subscribers_from_db', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_subscribers_from_db' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register delete_subscriber_from_db rest route
		register_rest_route( $namespace, '/' . 'delete_subscriber_from_db', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'delete_subscriber_from_db' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register delete_subscribers_from_db rest route
		register_rest_route( $namespace, '/' . 'delete_subscribers_from_db', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'delete_subscribers_from_db' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register add_gravity_forms_action_to_db rest route
		register_rest_route( $namespace, '/' . 'add_gravity_forms_action_to_db', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_gravity_forms_action_to_db' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register get_gravity_forms_actions_from_db rest route
		register_rest_route( $namespace, '/' . 'get_gravity_forms_actions_from_db', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_gravity_forms_actions_from_db' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register delete_gravity_forms_actions_from_db rest route
		register_rest_route( $namespace, '/' . 'delete_gravity_forms_actions_from_db', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'delete_gravity_forms_actions_from_db' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register send_feedback_message rest route
		register_rest_route( $namespace, '/' . 'send_feedback_message', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'send_feedback_message' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register send_sms rest route
		register_rest_route( $namespace, '/' . 'send_sms', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'send_sms' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );

		//Register add_phonebook rest route
		register_rest_route( $namespace, '/' . 'add_phonebook', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_phonebook' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		] );
	}

	/**
	 * Get settings options.
	 */
	public function get_settings_options() {
		$farazsms_settings_options = get_option( 'farazsms_settings_options' );
		if ( empty( $farazsms_settings_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_settings_options;
	}

	/**
	 * Add settings options.
	 */
	public function add_settings_options( $data ) {
		$option      = [
			'apikey'            => $data['apikey'] ?: '',
			'username'          => $data['username'] ?: '',
			'password'          => $data['password'] ?: '',
			'admin_number'      => $data['admin_number'] ?: '',
			'from_number'       => $data['from_number'] ?: '3000505',
			'from_number_adver' => $data['from_number_adver'] ?: '+98club',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_settings_options', $option_json );
	}

	/**
	 * Get login notify options.
	 */
	public function get_login_notify_options() {
		$farazsms_login_notify_options = get_option( 'farazsms_login_notify_options' );
		if ( empty( $farazsms_login_notify_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_login_notify_options;
	}

	/**
	 * Add login notify options.
	 */
	public function add_login_notify_options( $data ) {
		$option      = [
			'welcome_sms'                => $data['welcome_sms'] ?: '',
			'welcome_sms_use_pattern'    => $data['welcome_sms_use_pattern'] ?: '',
			'welcome_sms_pattern'        => $data['welcome_sms_pattern'] ?: '',
			'welcome_sms_msg'            => $data['welcome_sms_msg'] ?: '',
			'admin_login_notify'         => $data['admin_login_notify'] ?: '',
			'admin_login_notify_pattern' => $data['admin_login_notify_pattern'] ?: '',
			'select_roles'               => $data['select_roles'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_login_notify_options', $option_json );
	}

	/**
	 * Get phonebook options.
	 */
	public function get_phonebook_options() {
		$farazsms_phonebook_options = get_option( 'farazsms_phonebook_options' );
		if ( empty( $farazsms_phonebook_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_phonebook_options;
	}

	/**
	 * Add login notify options.
	 */
	public function add_phonebook_options( $data ) {
		$option      = [
			'custom_phonebook'       => $data['custom_phonebook'] ?: [],
			'custom_phone_meta_keys' => $data['custom_phone_meta_keys'] ?: [],
			'digits_phonebook'       => $data['digits_phonebook'] ?: [],
			'woo_phonebook'          => $data['woo_phonebook'] ?: [],
			'bookly_phonebook'       => $data['bookly_phonebook'] ?: [],
			'gf_phonebook'           => $data['gf_phonebook'] ?: [],
			'gf_forms'               => $data['gf_forms'] ?: [],
			'gf_selected_field'      => $data['gf_selected_field'] ?: [],
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_phonebook_options', $option_json );
	}

	/**
	 * Get usermeta.
	 */
	public function get_usermeta() {
		global $wpdb;

		return $wpdb->get_results( 'SELECT DISTINCT meta_key FROM `' . $wpdb->prefix . 'usermeta`' );
	}

	/**
	 * Get comments.
	 */
	public function get_comments_options() {
		$farazsms_comments_options = get_option( 'farazsms_comments_options' );
		if ( empty( $farazsms_comments_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_comments_options;
	}

	/**
	 * Add comments options.
	 */
	public function add_comments_options( $data ) {
		$option      = [
			'add_mobile_field'                 => $data['add_mobile_field'] ?: '',
			'required_mobile_field'            => $data['required_mobile_field'] ?: '',
			'notify_admin_for_comment'         => $data['notify_admin_for_comment'] ?: '',
			'notify_admin_for_comment_pattern' => $data['notify_admin_for_comment_pattern'] ?: '',
			'comment_pattern'                  => $data['comment_pattern'] ?: '',
			'comment_reply_pattern'            => $data['comment_reply_pattern'] ?: '',
			'comment_phonebook'                => $data['comment_phonebook'] ?: [],
			'disable_email_filed'              => $data['disable_email_filed'] ?: '',
			'disable_website_filed'            => $data['disable_website_filed'] ?: '',
			'disable_cookies'                  => $data['disable_cookies'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_comments_options', $option_json );
	}

	/**
	 * Get newsletter options.
	 */
	public function get_newsletter_options() {
		$farazsms_newsletter_options = get_option( 'farazsms_newsletter_options' );
		if ( empty( $farazsms_newsletter_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_newsletter_options;
	}

	/**
	 * Add newsletter options.
	 */
	public function add_newsletter_options( $data ) {
		$option      = [
			'news_phonebook'               => $data['news_phonebook'] ?: [],
			'news_send_verify_via_pattern' => $data['news_send_verify_via_pattern'] ?: '',
			'news_send_verify_pattern'     => $data['news_send_verify_pattern'] ?: '',
			'news_welcome'                 => $data['news_welcome'] ?: '',
			'news_welcome_pattern'         => $data['news_welcome_pattern'] ?: '',
			'news_post_notify'             => $data['news_post_notify'] ?: '',
			'news_post_notify_msg'         => $data['news_post_notify_msg'] ?: '',
			'news_product_notify'          => $data['news_product_notify'] ?: '',
			'news_product_notify_msg'      => $data['news_product_notify_msg'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_newsletter_options', $option_json );
	}

	/**
	 * Get woocommerce options.
	 */
	public function get_woocommerce_options() {
		$farazsms_woocommerce_options = get_option( 'farazsms_woocommerce_options' );
		if ( empty( $farazsms_woocommerce_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_woocommerce_options;
	}

	/**
	 * Add woocommerce options.
	 */
	public function add_woocommerce_options( $data ) {
		$option      = [
			'woo_checkout_otp'          => $data['woo_checkout_otp'] ?: '',
			'woo_checkout_otp_pattern'  => $data['woo_checkout_otp_pattern'] ?: '',
			'woo_poll'                  => $data['woo_poll'] ?: '',
			'woo_poll_time'             => $data['woo_poll_time'] ?: '',
			'woo_poll_msg'              => $data['woo_poll_msg'] ?: '',
			'woo_tracking_pattern'      => $data['woo_tracking_pattern'] ?: '',
			'woo_retention_order_no'    => $data['woo_retention_order_no'] ?: '',
			'woo_retention_order_month' => $data['woo_retention_order_month'] ?: '',
			'woo_retention_msg'         => $data['woo_retention_msg'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_woocommerce_options', $option_json );
	}

	/**
	 * Get Edd options.
	 */
	public function get_edd_options() {
		$farazsms_edd_options = get_option( 'farazsms_edd_options' );
		if ( empty( $farazsms_edd_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_edd_options;
	}

	/**
	 * Add Edd options.
	 */
	public function add_edd_options( $data ) {
		$option      = [
			'edd_phonebook'     => $data['edd_phonebook'] ?: [],
			'edd_send_to_user'  => $data['edd_send_to_user'] ?: '',
			'edd_user_pattern'  => $data['edd_user_pattern'] ?: '',
			'edd_send_to_admin' => $data['edd_send_to_admin'] ?: '',
			'edd_admin_pattern' => $data['edd_admin_pattern'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_edd_options', $option_json );
	}

	/**
	 * Get aff options.
	 */
	public function get_aff_options() {
		$farazsms_aff_options = get_option( 'farazsms_aff_options' );
		if ( empty( $farazsms_aff_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_aff_options;
	}

	/**
	 * Add aff options.
	 */
	public function add_aff_options( $data ) {
		$option      = [
			'aff_user_mobile_field'              => $data['aff_user_mobile_field'] ?: '',
			'aff_user_register'                  => $data['aff_user_register'] ?: '',
			'aff_user_register_pattern'          => $data['aff_user_register_pattern'] ?: '',
			'aff_user_new_ref'                   => $data['aff_user_new_ref'] ?: '',
			'aff_user_new_ref_pattern'           => $data['aff_user_new_ref_pattern'] ?: '',
			'aff_user_on_approval'               => $data['aff_user_on_approval'] ?: '',
			'aff_user_on_approval_pattern'       => $data['aff_user_on_approval_pattern'] ?: '',
			'aff_admin_user_register'            => $data['aff_admin_user_register'] ?: '',
			'aff_admin_user_register_pattern'    => $data['aff_admin_user_register_pattern'] ?: '',
			'aff_admin_user_new_ref'             => $data['aff_admin_user_new_ref'] ?: '',
			'aff_admin_user_new_ref_pattern'     => $data['aff_admin_user_new_ref_pattern'] ?: '',
			'aff_admin_user_on_approval'         => $data['aff_admin_user_on_approval'] ?: '',
			'aff_admin_user_on_approval_pattern' => $data['aff_admin_user_on_approval_pattern'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_aff_options', $option_json );
	}

	/**
	 * Get membership options.
	 */
	public function get_membership_options() {
		$farazsms_membership_options = get_option( 'farazsms_membership_options' );
		if ( empty( $farazsms_membership_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_membership_options;
	}

	/**
	 * Add membership options.
	 */
	public function add_membership_options( $data ) {
		$option      = [
			'ihc_send_first_notify'  => $data['ihc_send_first_notify'] ?: '',
			'ihc_send_second_notify' => $data['ihc_send_second_notify'] ?: '',
			'ihc_send_third_notify'  => $data['ihc_send_third_notify'] ?: '',
			'ihc_first_notify_msg'   => $data['ihc_first_notify_msg'] ?: '',
			'ihc_notify_before_time' => $data['ihc_notify_before_time'] ?: '5',
			'pmp_send_expire_notify' => $data['pmp_send_expire_notify'] ?: '',
			'pmp_expire_notify_msg'  => $data['pmp_expire_notify_msg'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_membership_options', $option_json );
	}

	/**
	 * Get integrations options.
	 */
	public function get_integrations_options() {
		$farazsms_integrations_options = get_option( 'farazsms_integrations_options' );
		if ( empty( $farazsms_integrations_options ) ) {
			return new WP_Error( 'no_option', 'Invalid options', [ 'status' => 404 ] );
		}

		return $farazsms_integrations_options;
	}

	/**
	 * Add integrations options.
	 */
	public function add_integrations_options( $data ) {
		$option      = [
			'woocommerce'               => $data['woocommerce'] ?: '',
			'elementorPro'              => $data['elementorPro'] ?: '',
			'digits'                    => $data['digits'] ?: '',
			'edd'                       => $data['edd'] ?: '',
			'bookly'                    => $data['bookly'] ?: '',
			'gravityForms'              => $data['gravityForms'] ?: '',
			'indeedMembershipPro'       => $data['indeedMembershipPro'] ?: '',
			'paidMembershipsPro'        => $data['paidMembershipsPro'] ?: '',
			'affiliateWp'               => $data['affiliateWp'] ?: '',
			'indeedAffiliatePro'        => $data['indeedAffiliatePro'] ?: '',
			'yithWoocommerceAffiliates' => $data['yithWoocommerceAffiliates'] ?: '',
		];
		$option_json = wp_json_encode( $option );

		return update_option( 'farazsms_integrations_options', $option_json );
	}

	/**
	 * Woocommerce Synchronization with a phonebook.
	 *
	 * @throws Exception
	 */
	public function sync_woo() {
		if ( ! Farazsms_Base::$woo_phonebook_id ) {
			return 'noPhonebook';
		} else {
			$query     = new WC_Order_Query( [ 'limit' => 9999, 'type' => 'shop_order', 'return' => 'ids' ] );
			$order_ids = $query->get_orders();

			$list = [];
			foreach ( $order_ids as $order_id ) {
				$order  = wc_get_order( $order_id );
				$number = $order->get_billing_phone();
				$name   = $order->get_formatted_billing_full_name();

				$list[] = (object) [
					'number'       => $number,
					'name'         => $name,
					'phonebook_id' => (int) Farazsms_Base::$woo_phonebook_id
				];
			}
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

			return true;
		}
	}

	/**
	 * Digits Synchronization with a phonebook.
	 */
	public function sync_digits() {
		if ( ! Farazsms_Base::$digits_phonebook_id ) {
			return 'noPhonebook';
		} else {
			$users = get_users( [ 'fields' => 'ID' ] );

			$list = [];
			foreach ( $users as $userid ) {
				$user_digits_phone = get_user_meta( $userid, 'digits_phone', true );
				if ( empty( $user_digits_phone ) ) {
					continue;
				}
				$user_info = get_userdata( $userid );
				$number    = $user_digits_phone;
				$name      = $user_info->display_name ?? $user_info->first_name . ' ' . $user_info->last_name;

				$list[] = (object) [
					'number'       => $number,
					'name'         => $name,
					'phonebook_id' => (int) Farazsms_Base::$digits_phonebook_id
				];
			}
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

			return true;
		}

	}

	/**
	 * Bookly Synchronization with a phonebook.
	 */
	public function sync_bookly() {
		if ( ! Farazsms_Base::$bookly_phonebook_id ) {
			return 'noPhonebook';
		} else {
			global $wpdb;
			$bookly_customers = $wpdb->get_results( 'SELECT phone,full_name FROM ' . $wpdb->prefix . 'bookly_customers' );

			$list = [];
			foreach ( $bookly_customers as $customer ) {
				$number = substr( $customer->phone, - 10 );
				$name   = $customer->full_name;

				$list[] = (object) [
					'number'       => $number,
					'name'         => $name,
					'phonebook_id' => (int) Farazsms_Base::$bookly_phonebook_id
				];
			}
			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );

			return true;
		}

	}


	/**
	 * Validate apikey.
	 *
	 * @param $data
	 *
	 * @return mixed|null
	 * @since 2.0.0
	 */
	public function validate_apikey( $data ) {

		$url = 'http://rest.ippanel.com/v1/user';
		$args = [
			'method'      => 'GET',
			'headers'     => [
				'Authorization' => 'AccessKey ' . $data['apikey'],
				'Content-Type'  => 'application/json',
			],
		];

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body, true );
	}


	/**
	 * Get phonebook numbers.
	 *
	 * @param $phonebook_id
	 *
	 * @return mixed|null
	 * @since 2.0.0
	 */
	public static function get_phonebook_numbers( $phonebook_id ) {

		$url = 'http://api.ippanel.com/api/v1/phonebook/numbers';
		$args = [
			'method'      => 'GET',
			'headers'     => [
				'Authorization' => Farazsms_Base::$apiKey,
				'Content-Type'  => 'application/json',
			],
			'body'        => [
				'phonebook' => $phonebook_id['phonebook_id'],
				'page'      => 1,
				'per_page'  => 10,
			],
		];

		$response = wp_remote_post( add_query_arg( $args, $url ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body, true );
	}


	/**
	 * Get subscribers.
	 */
	public static function get_subscribers_from_db() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_newsletter';

		return json_encode( $wpdb->get_results( "SELECT * FROM $table_name" ), true );
	}

	/**
	 * Delete subscriber.
	 */
	public static function delete_subscriber_from_db( $subscriber_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'farazsms_newsletter';

		return json_decode( $wpdb->delete( $table, [ 'id' => $subscriber_id['subscriber_id'] ] ), true );
	}

	/**
	 * Delete multiple subscribers.
	 */
	public static function delete_subscribers_from_db( $subscriber_ids ) {
		global $wpdb;
		$table = $wpdb->prefix . 'farazsms_newsletter';

		$ids = implode( ',', array_map( 'intval', $subscriber_ids ) );

		$sql = "DELETE FROM $table WHERE id IN ($ids)";

		return $wpdb->query( $sql );
	}

	/**
	 * Add new gravity forms action to DB.
	 */
	public static function add_gravity_forms_action_to_db( $incomingData ) {
		global $wpdb;

		$data       = [
			'title'               => $incomingData['title'] ?: '',
			'phonebook_id'        => $incomingData['phonebook_id'] ?: '',
			'form_id'             => $incomingData['form_id'] ?: '',
			'field_id'            => $incomingData['field_id'] ?: '',
			'name_field_id'       => $incomingData['name_field_id'] ?: '',
			'content_field_id'    => $incomingData['content_field_id'] ?: '',
			'phonebook_label'     => $incomingData['phonebook_label'] ?: '',
			'form_label'          => $incomingData['form_label'] ?: '',
			'field_label'         => $incomingData['field_label'] ?: '',
			'name_field_label'    => $incomingData['name_field_label'] ?: '',
			'content_field_label' => $incomingData['content_field_label'] ?: '',
			'action_type'         => $incomingData['action_type'] ?: '',
			'action_label'        => $incomingData['action_label'] ?: '',
			'user_pattern_code'   => $incomingData['user_pattern_code'] ?: '',
			'admin_pattern_code'  => $incomingData['admin_pattern_code'] ?: '',
		];
		$table_name = $wpdb->prefix . 'farazsms_gravity_forms';

		return $wpdb->insert( $table_name, $data );
	}

	/**
	 * Get gravity forms actions.
	 */
	public static function get_gravity_forms_actions_from_db() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_gravity_forms';

		return json_encode( $wpdb->get_results( "SELECT * FROM $table_name" ), true );
	}

	/**
	 * Delete subscriber.
	 */
	public static function delete_gravity_forms_actions_from_db( $action_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'farazsms_gravity_forms';

		return json_decode( $wpdb->delete( $table, [ 'id' => $action_id['action_id'] ] ), true );
	}

	/**
	 * Send feedback message to server.
	 */
	public static function send_feedback_message( $feedback ) {
		$body = [
			'uname'            => Farazsms_Base::$username,
			'pass'             => Farazsms_Base::$password,
			'subject'          => $feedback['subject'],
			'description'      => $feedback['message'] . PHP_EOL . get_site_url(),
			'type'             => 'fiscal',   //'fiscal','webservice','problem','lineservices'
			'importance'       => 'low',  //'low','middle','quick','acute'
			'sms_notification' => 'no', //'yes','no'
			'file'             => '',
			'op'               => 'ticketadd'
		];

		$args = [
			'body'        => $body,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [],
			'cookies'     => []
		];

		$response = wp_remote_post( 'http://ippanel.com/services.jspd', $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code != 200 ) {
			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_data = json_decode( $response_body );
		if ( $response_data[0] !== 0 || ! $response_data[1] ) {
			return false;
		}

		return true;
	}

	/**
	 * Sends SMS to specified phone numbers, phonebooks, and subscribers.
	 *
	 * @param array $data Array of input data for SMS sending
	 *
	 * @return array || string
	 */

	public static function send_sms( $data ) {
		$message             = $data['message'];
		$phonebooks          = $data['phonebooks'];
		$send_to_subscribers = $data['send_to_subscribers'];
		$sender              = $data['send_fromnum_choice'];
		$phones              = $data['phones'];
		$fixed_phones        = [];
		$phonebooks_ids      = [];

		// initialize an array to collect responses
		$responses = [];

		if ( $sender ) {
			$sender = Farazsms_Base::$fromNum;
		}

		// Send SMS to fixed phones
		if ( ! str_contains( $phones, ',' ) ) {
			if ( Farazsms_Base::validate_mobile_number( $phones ) ) {
				$fixed_phones[] = Farazsms_Base::validate_mobile_number( $phones );
			}
		} else {
			$phones = explode( ',', $phones );
			foreach ( $phones as $phone ) {
				if ( Farazsms_Base::validate_mobile_number( $phone ) ) {
					$fixed_phones[] = Farazsms_Base::validate_mobile_number( $phone );
				}
			}
		}
		$sendToPhones_response = Farazsms_Ippanel::send_message($fixed_phones, $message, $sender);

		// Send SMS to phonebooks
		foreach ( $phonebooks as $phonebook ) {
			$phonebooks_ids[] =  $phonebook['value'];
		}
		$sendToPhonebooks_response = Farazsms_Ippanel::send_sms_to_phonebooks($phonebooks_ids, $message,$sender);


		// Send SMS to subscribers
		if ( $send_to_subscribers ) {
			$subscribers = Farazsms_Base::get_subscribers();
			if ( empty( $subscribers ) ) {
				return 'noSubscribers';
			}
			if ( str_contains( $message, '%name%' ) ) {
				foreach ( $subscribers as $subscriber ) {
					$message_fixed = str_replace( '%name%', $subscriber->name, $message );
					$sendToSubscribers_response[] = Farazsms_Ippanel::send_message( [ $subscriber->phone ], $message_fixed, $sender );
				}
			} else {
				$phones = [];
				foreach ( $subscribers as $subscriber ) {
					$phones[] = $subscriber->phone;
				}

				$sendToSubscribers_response[] = Farazsms_Ippanel::send_message( $phones, $message, $sender );
			}
		}

		return [
			'sendToPhones_response' => $sendToPhones_response,
			'sendToPhonebooks_response' => $sendToPhonebooks_response,
			'sendToSubscribers_response' => $sendToSubscribers_response ?? null,
		];
	}

	/**
	 * Add new phonebook
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function add_phonebook( $data ) {
		$label = $data['label'];

		$url      = 'http://api.ippanel.com/api/v1/phonebook/phonebooks';
		$headers  = [
			'accept'        => 'application/json',
			'Authorization' => Farazsms_Base::$apiKey,
			'Content-Type'  => 'application/json'
		];
		$body     = [
			'title' => $label,
		];
		$args     = [
			'headers' => $headers,
			'body'    => json_encode( $body ),
			'method'  => 'POST'
		];
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		} else {
			return $response;
		}
	}


	/**
	 * Check if a given request has permissions
	 *
	 * @return bool
	 */
	public function permissions_check( $request ) {
		//return true; <--use to make readable by all
		return current_user_can( 'manage_options' );
	}

}

Farazsms_Routes::get_instance();
