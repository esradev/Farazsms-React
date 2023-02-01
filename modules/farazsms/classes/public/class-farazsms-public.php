<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://farazsms.com/
 * @since      1.0.7
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Farazsms
 * @subpackage Farazsms/public
 * @author     FarazSMS <info@farazsms.com>
 */
class Farazsms_Public extends Farazsms_Base {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	// private $username;
	// private $password;
	// private $fromNum;
	// private $_woo_installed = false;
	// private $_digits_installed = false;

	public static $add_mobile_field;
	public static $required_mobile_field;
	public static $notify_admin_for_comment;
	public static $approved_comment_pattern;
	public static $comment_pattern;
	public static $notify_admin_for_comment_pattern;
	public static $comment_phonebook;

	public static $admin_login_notify;
	public static $admin_login_notify_pattern;
	public static $select_roles;

	public static $custom_phonebook;
	public static $custom_phone_meta_keys;
	public static $digits_phonebook;
	public static $woo_phonebook;
	public static $gf_phonebook;
	public static $gf_selected_field;

	public static $news_phonebook;
	public static $news_send_verify_via_pattern;
	public static $news_post_notify;
	public static $news_post_notify_msg;
	public static $news_product_notify;
	public static $news_product_notify_msg;

	public static $ihc_send_first_notify;
	public static $ihc_send_second_notify;
	public static $ihc_send_third_notify;
	public static $ihc_first_notify_msg;
	public static $ihc_notify_before_time;
	public static $pmp_send_expire_notify;
	public static $pmp_expire_notify_msg;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_shortcode( 'farazsms', [ $this, 'farazsms_newsletter' ] );

		$login_notify_options = json_decode( get_option( 'farazsms_login_notify_options' ), true );
		if ( $login_notify_options ) {

			self::$admin_login_notify         = $login_notify_options['admin_login_notify'];
			self::$admin_login_notify_pattern = $login_notify_options['admin_login_notify_pattern'];
			self::$select_roles               = $login_notify_options['select_roles'];
		}

		$comments_options = json_decode( get_option( 'farazsms_comments_options' ), true );
		if ( $comments_options ) {
			self::$add_mobile_field                 = $comments_options['add_mobile_field'];
			self::$required_mobile_field            = $comments_options['required_mobile_field'];
			self::$notify_admin_for_comment         = $comments_options['notify_admin_for_comment'];
			self::$approved_comment_pattern         = $comments_options['approved_comment_pattern'];
			self::$comment_pattern                  = $comments_options['comment_pattern'];
			self::$notify_admin_for_comment_pattern = $comments_options['notify_admin_for_comment_pattern'];
			self::$comment_phonebook                = $comments_options['comment_phonebook'];
		}

		$phonebook_options = json_decode( get_option( 'farazsms_phonebook_options' ), true );
		if ( $phonebook_options ) {
			self::$custom_phonebook       = $phonebook_options['custom_phonebook'];
			self::$custom_phone_meta_keys = $phonebook_options['custom_phone_meta_keys'];
			self::$digits_phonebook       = $phonebook_options['digits_phonebook'];
			self::$woo_phonebook          = $phonebook_options['woo_phonebook'];
			self::$gf_phonebook           = $phonebook_options['gf_phonebook'];
			self::$gf_selected_field      = $phonebook_options['gf_selected_field'];
		}

		$newsletter_options = json_decode( get_option( 'farazsms_newsletter_options' ), true );
		if ( $newsletter_options ) {
			self::$news_phonebook               = $newsletter_options['news_phonebook'];
			self::$news_send_verify_via_pattern = $newsletter_options['news_send_verify_via_pattern'];
			self::$news_post_notify             = $newsletter_options['news_post_notify'];
			self::$news_post_notify_msg         = $newsletter_options['news_post_notify_msg'];
			self::$news_product_notify          = $newsletter_options['news_product_notify'];
			self::$news_product_notify_msg      = $newsletter_options['news_product_notify_msg'];
		}

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
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/farazsms-public.css', array(), $this->version, 'all' );
		wp_register_style( 'farazsms-newsletter', plugin_dir_url( __FILE__ ) . 'assets/css/farazsms-newsletter.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/farazsms-public.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'farazsms-newsletter', plugin_dir_url( __FILE__ ) . 'js/farazsms-newsletter.js', array( 'jquery' ), $this->version, false );
	}


	/**
	 *
	 * Monitor update user metadata.
	 *
	 */

	public function monitor_update_user_metadata( $check, $object_id, $meta_key, $meta_value ) {
		$fsms_base          = Farazsms_Base::get_instance();
		$selected_meta_keys = self::$custom_phone_meta_keys ?? [];
		if ( ! in_array( $meta_key, $selected_meta_keys ) ) {
			return $check;
		}
		$phone = $fsms_base::validate_mobile_number( $meta_value );
		if ( ! $phone ) {
			return $check;
		}
		$custom_phonebooks = self::$custom_phonebook;
		if ( empty( $custom_phonebooks ) ) {
			return $check;
		}
		$user_info = get_userdata( $object_id );
		foreach ( $custom_phonebooks as $phonebookId ) {
			//$fsms_base::save_to_phonebook($phone, $phonebookId);
			$data[] = [
				'number'       => $phone,
				'name'         => $user_info->display_name ?? '',
				'phonebook_id' => (int) $phonebookId['value']
			];
			$fsms_base->save_to_phonebookv3( $data );
		}
		$fsms_base->send_welcome_message( $phone, $object_id );

		return $check;
	}

	/**
	 *
	 * User profile updated.
	 *
	 */
	public function fsms_user_profile_updated( $user_id, $old_user_data ) {
		$digits_phone = get_user_meta( $user_id, 'digits_phone', true );
		if ( empty( $digits_phone ) ) {
			return;
		}
		$fsms_base         = Farazsms_Base::get_instance();
		$digits_phonebooks = self::$digits_phonebook;
		//if(empty($digits_phonebooks)){return;}
		$user_info = get_userdata( $user_id );
		foreach ( $digits_phonebooks as $phonebookId ) {
			//$fsms_base::save_to_phonebook($phone, $phonebookId);
			$data[] = [
				'number'       => $digits_phone,
				'name'         => $user_info->display_name ?? '',
				'phonebook_id' => (int) $phonebookId['value']
			];
			$fsms_base->save_to_phonebookv3( $data );
		}
		$already_sent_one = get_user_meta( $user_id, 'sent_welcome_message', true );
		if ( ! empty( $already_sent_one ) && $already_sent_one == '1' ) {
			return;
		}
		$fsms_base->send_welcome_message( $digits_phone, $user_id );
		update_user_meta( $user_id, 'sent_welcome_message', '1' );
	}

	/**
	 *
	 * User created action.
	 *
	 */
	public function fsms_user_created_action( $userID ) {
		global $wpdb;
		$posts = $wpdb->get_row( 'SELECT phone FROM ' . $wpdb->prefix . "bookly_customers WHERE wp_user_id = '" . $userID . "'" );
	}


	/**
	 *
	 * Add mobile field to comment form
	 *
	 */
	public function add_mobile_field_to_comment_form() {
		if ( ! self::$add_mobile_field === true ) {
			return;
		}
		$r   = '';
		$res = '<p class="comment-form-phone">'
		       . '<label for="mobile">شماره موبایل';
		if ( self::$required_mobile_field === true ) {
			$res .= ' <span class="required">*</span></label>';
			$r   = 'required="required"';
		}
		$res .= '<input class="uk-input uk-width-large uk-display-block" oninput="if (this.value.length > 11) this.value = this.value.slice(0, 11);" type="number"  name="mobile" id="mobile"' . '' . $r . '/></p>';
		echo $res;
	}

	// Save mobile field.
	public function save_mobile_field( $comment_id ) {
		if ( isset( $_POST['mobile'] ) ) {
			$mobile = Farazsms_Base::validate_mobile_number( esc_attr( $_POST['mobile'] ) );
			update_comment_meta( $comment_id, 'mobile', $mobile );
		}
		$this->response_to_comment( $comment_id );
	}

	// Verify comment input
	public function verify_comment_input( $commentdata ) {
		if ( empty( $commentdata['comment_parent'] ) && self::$required_mobile_field === true ) {
			if ( ! isset( $_POST['mobile'] ) or empty( $_POST['mobile'] ) ) {
				wp_die( __( 'Error: Mobile number is required.', 'farazsms' ) );
			}
		}

		return $commentdata;
	}

	// Response to comment
	public function response_to_comment( $comment_id ) {
		$fsms_base = Farazsms_Base::get_instance();
		$comment   = get_comment( $comment_id );
		$data      = $this->comments_farazsms_shortcode( $comment, $comment_id );
		$mobile    = get_comment_meta( $data['parent'] )['mobile'][0] ?? '';
		$user      = get_user_by( 'id', $comment->user_id );
		$is_admin  = in_array( 'administrator', (array) $user->roles );
		if ( $comment->comment_parent == 0 ) {
			$mobile = get_comment_meta( $comment_id )['mobile'][0] ?? '';
			if ( ! empty( self::$approved_comment_pattern ) || ! empty( $mobile ) ) {
				$fsms_base->send_comment_reply_sms( $mobile, self::$approved_comment_pattern, $data );
			}
		}
		if ( self::$notify_admin_for_comment == 1 && ! $is_admin ) {
			$fsms_base->send_comment_reply_sms( $fsms_base::getAdminNumber(), self::$notify_admin_for_comment_pattern, $data );
		}
		if ( $is_admin ) {
			if ( empty( $mobile ) ) {
				return false;
			}
			if ( $comment->comment_parent == 0 ) {
				return false;
			}
			$comment = get_comment( $comment->comment_parent );
			$data    = $this->comments_farazsms_shortcode( $comment, $comment_id );
			$fsms_base->send_comment_reply_sms( $mobile, self::$comment_pattern, $data );
			$fsms_base->save_comment_mobile_to_phonebook( $mobile );
		}
	}

	/**
	 *
	 * Comments farazsms shortcode.
	 *
	 */

	public function comments_farazsms_shortcode( $comment, $comment_id ) {
		$post = get_post( $comment->comment_post_ID );

		return [
			'title'   => $post->post_title,
			'name'    => $comment->comment_author,
			'email'   => $comment->comment_author_email,
			//'link' => get_comment_link($comment_id),
			'link'    => wp_get_shortlink( $post->ID ) . '#comment-' . $comment->comment_ID,
			'content' => $comment->comment_content,
			'parent'  => $comment->comment_parent
		];
	}

	/**
	 *
	 * Gravity Form post update entry.
	 *
	 */

	public function fsms_club_gform_post_update_entry( $entry ) {
		$fsms_base                = Farazsms_Base::get_instance();
		$fsc_gravity_forms_fields = self::$gf_selected_field;
		$form_ids                 = array();
		$field_ids                = array();
		foreach ( $fsc_gravity_forms_fields as $field ) {
			$exploded    = explode( '-', $field );
			$form_ids[]  = $exploded[0];
			$field_ids[] = $exploded[1];
		}
		$form_id = $entry['form_id'];
		if ( ! in_array( $form_id, $form_ids ) ) {
			return;
		}
		foreach ( $field_ids as $field_id ) {
			$value = $entry[ $field_id ];
			$phone = $fsms_base::validate_mobile_number( $value );
			if ( ! $phone ) {
				return;
			}
			if ( $value !== null ) {
				$woo_gf_books = self::$gf_phonebook;
				foreach ( $woo_gf_books as $phonebookId ) {
					//$fsms_base::save_to_phonebook($phone, $phonebookId);
					$data[] = [
						'number'       => $phone,
						'name'         => '',
						'phonebook_id' => (int) $phonebookId['value']
					];
					$fsms_base->save_to_phonebookv3( $data );
				}
			}
		}
	}

	/**
	 *
	 * Temp init kook.
	 *
	 */

	public function temp_init_kook() {
		//print_r(wp_get_shortlink(50));
		return;
		// $fsms_base = Farazsms_Base::get_instance();
		// $fsms_base::save_to_phonebook(['09132789372','09038430716','09145236589'], '300536');
		// $this->fsms_first_notification_before_expire(FALSE, 5, null, 'before_expire');
		// $this->monitor_update_user_metadata(null, 5,'digits_phone', "09132789372");
		// $this->fsms_first_notification_before_expire(false, 5,'', "before_expire");
		$fsms_base = Farazsms_Base::get_instance();
		$fsms_base->send_welcome_message( '09038430716', 31 );
	}

	/**
	 *
	 * Farazsms newsletter.
	 *
	 */

	public function farazsms_newsletter( $atts = array() ) {
		wp_enqueue_style( 'farazsms-newsletter' );
		wp_enqueue_script( 'farazsms-newsletter' );
		wp_localize_script(
			'farazsms-newsletter',
			'fsms_ajax_object',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
		);
		$newsletter_send_ver_code = self::$news_send_verify_via_pattern;

		return '<div id="fsms_newsletter">
                  <form id="fsms_newsletter_form">
                    <div class="fsms_newsletter_input a">
                      <input id="fsms_newsletter_name" type="text" class="fsms_newsletter_text" placeholder=""نام و نام خانوادگی"">
                    </div>
                    <div class="fsms_newsletter_input a">
                      <input id="fsms_newsletter_mobile" type="text" class="fsms_newsletter_text" placeholder="شماره موبایل">
                    </div>
                    <div class="fsms_newsletter_input b" style="display: none;">
                      <input id="fsms_newsletter_verify_code" type="text" class="fsms_newsletter_text" placeholder="کد تایید">
                    </div>
                    <input id="newsletter_send_ver_code" type="hidden" value="' . $newsletter_send_ver_code . '">
                  </form>
                    <div id="fsms_newsletter_message" style="display: none;">
                    </div>
                    <div class="fsms_newsletter_submit">
                      <button id="fsms_newsletter_submit_button" class="fsms_newsletter_button"><span class="button__text">اشتراک</span></button>
                    </div>
                    <div id="fsms_newsletter_completion" class="fsms_newsletter_submit" style="display: none;">
                      <button id="fsms_newsletter_submit_code" class="fsms_newsletter_button"><span class="button__text">ارسال کد</span></button>
                      <button id="fsms_newsletter_resend_code" class="fsms_newsletter_button"><span class="button__text">ارسال مجدد کد</span></button>
                    </div>
                </div>';
	}

	/**
	 *
	 * Newsletter send verification code
	 *
	 */

	public function fsms_newsletter_send_verification_code() {
		$fsms_base                = Farazsms_Base::get_instance();
		$mobile                   = $_POST['mobile'];
		$name                     = $_POST['name'];
		$newsletter_send_ver_code = self::$news_send_verify_via_pattern;
		if ( $fsms_base::check_if_phone_already_exist( $mobile ) ) {
			wp_send_json_error();
		}
		if ( $newsletter_send_ver_code == 'false' ) {
			$data = array(
				'phone' => $mobile,
				'name'  => $name,
			);
			$fsms_base::save_subscriber_to_db( $data );
			$newsletter_phonebooks = self::$news_phonebook;
			foreach ( $newsletter_phonebooks as $phonebookID ) {
				//$fsms_base::save_to_phonebook($mobile, $phonebookID);
				$datap[] = [
					'number'       => $mobile,
					'name'         => $name,
					'phonebook_id' => (int) $phonebookID
				];
				$fsms_base->save_to_phonebookv3( $datap );
			}
			$fsms_base::send_newsletter_welcome_message( $mobile, $name );
			wp_send_json_success();
		} else {
			$generated_code = rand( 1000, 9999 );
			$fsms_base::save_generated_code_to_db( $mobile, $generated_code );
			$data = array(
				'code' => $generated_code,
				'name' => $name
			);
			$fsms_base->send_newsletter_verification_code( $mobile, $data );
			wp_send_json_success();
		}
	}

	/**
	 *
	 * Add phone to newsletter.
	 *
	 */

	public function fsms_add_phone_to_newsletter() {
		$fsms_base = Farazsms_Base::get_instance();
		$code      = $_POST['code'];
		$name      = $_POST['name'];
		$mobile    = $_POST['mobile'];
		$is_valid  = $fsms_base::check_if_code_is_valid( $mobile, $code );
		if ( $is_valid ) {
			$data = array(
				'phone' => $mobile,
				'name'  => $name
			);
			$fsms_base::save_subscriber_to_db( $data );
			$newsletter_phonebooks = self::$news_phonebook;
			foreach ( $newsletter_phonebooks as $phonebookID ) {
				//$fsms_base::save_to_phonebook($mobile, $phonebookID);
				$datap[] = [
					'number'       => $mobile,
					'name'         => $name,
					'phonebook_id' => (int) $phonebookID
				];
				$fsms_base->save_to_phonebookv3( $datap );
			}
			$fsms_base::send_newsletter_welcome_message( $mobile, $name );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 *
	 * Publish post notification.
	 *
	 */

	public function fsms_publish_post_notification( $post_id ) {
		$newsletter_new_post_notification     = self::$news_post_notify;
		$newsletter_post_notification_message = self::$news_post_notify_msg;
		if ( $newsletter_new_post_notification == 'false' || empty( $newsletter_post_notification_message ) ) {
			return;
		}
		$notification_message = str_replace( array(
			'%title%',
			'%url%'
		), array(
			get_the_title( $post_id ),
			wp_get_shortlink( $post_id )
		), $newsletter_post_notification_message );
		$fsms_base            = Farazsms_Base::get_instance();
		$subscribers          = $fsms_base::get_subscribers();
		$phones               = [];
		foreach ( $subscribers as $subscriber ) {
			$phones[] = $subscriber->phone;
		}
		$fsms_base->send_message( $phones, $notification_message );
		remove_action( 'publish_post', 'fsms_publish_post_notification', 10 );
	}

	/**
	 *
	 * Product published.
	 *
	 */

	public function fsms_product_published( $new_status, $old_status, $post ) {
		$post_type = get_post_type( $post );
		if ( $post_type !== 'product' ) {
			return;
		}
		if ( $new_status === 'publish' && $new_status !== $old_status ) {
			$newsletter_new_prodcut_notification     = self::$news_product_notify;
			$newsletter_prodcut_notification_message = self::$news_product_notify_msg;
			if ( $newsletter_new_prodcut_notification === 'false' || empty( $newsletter_prodcut_notification_message ) ) {
				return;
			}
			$product              = wc_get_product( $post->ID );
			$notification_message = str_replace( array(
				'%site_title%',
				'%product_name%',
				'%price%',
				'%url%'
			), array(
				wp_title(),
				$product->get_name(),
				$product->get_price(),
				wp_get_shortlink( $post->ID )
			), $newsletter_prodcut_notification_message );
			$fsms_base            = Farazsms_Base::get_instance();
			$subscribers          = $fsms_base::get_subscribers();
			$phones               = [];
			foreach ( $subscribers as $subscriber ) {
				$phones[] = $subscriber->phone;
			}
			$fsms_base->send_message( $phones, $notification_message );
		}
	}

	/**
	 *
	 * Admin login action.
	 *
	 */

	public function fsms_admin_login_action( $user_login, $user ) {
		if ( ! in_array( 'administrator', (array) $user->roles ) ) {
			return;
		}
		$last_notification = get_user_meta( $user->ID, 'faraz_low_credit_noti_sent_timestamp', true );
		if ( ! empty( $last_notification ) ) {
			$dif = time() - $last_notification;
			if ( $dif < 86400 ) {
				return;
			}
		}
		$fsms_base = Farazsms_Base::get_instance();
		$credit    = $fsms_base->get_credit();
		if ( ! $credit ) {
			return;
		}
		if ( (int) $credit < 10000 ) {
			$fsms_base->send_admin_low_credit_to_admin();
			update_user_meta( $user->ID, 'faraz_low_credit_noti_sent_timestamp', time() );
		}
	}

	/**
	 *
	 * Admin rules login action
	 *
	 */


	public function fsms_admin_roles_login_action( $user_login, $user ) {
		$admin_login_noti_roles = self::$select_roles;
		if ( empty( $admin_login_noti_roles ) ) {
			return;
		}
		// if (empty(array_intersect($admin_login_noti_roles, $user->roles))) {
		//     return;
		// }
		$admin_login_noti   = self::$admin_login_notify;
		$admin_login_noti_p = self::$admin_login_notify_pattern;
		if ( $admin_login_noti == 'false' || empty( $admin_login_noti_p ) ) {
			return;
		}
		$data['date']         = date_i18n( 'H:i:s d-m-Y' );
		$data['user_login']   = $user->user_login;
		$data['display_name'] = $user->display_name;
		$fsms_base            = Farazsms_Base::get_instance();
		$fsms_base->send_admins_login_notification_to_superadmin( $admin_login_noti_p, $data );
	}


	/**
	 *
	 * First notification before expire
	 *
	 */

	public function fsms_first_notification_before_expire( $sent = false, $uid = 0, $lid = 0, $type = '' ) {
		$types                  = [];
		$types[]                = ( self::$ihc_send_first_notify == 'true' ) ? 'before_expire' : '';
		$types[]                = ( self::$ihc_send_second_notify == 'true' ) ? 'second_before_expire' : '';
		$types[]                = ( self::$ihc_send_third_notify == 'true' ) ? 'third_before_expire' : '';
		$first_noti_sms_message = self::$ihc_first_notify_msg;
		if ( empty( $first_noti_sms_message ) || ! in_array( $type, $types ) ) {
			return $sent;
		}
		$phone = get_user_meta( $uid, 'digits_phone', true );
		if ( empty( $phone ) ) {
			return $sent;
		}
		$user      = get_userdata( $uid );
		$fsms_base = Farazsms_Base::get_instance();
		$message   = str_replace( array(
			'%name%',
			'%time%',
		), array(
			$user->display_name,
			self::$ihc_notify_before_time,
		), $first_noti_sms_message );
		$fsms_base->send_message( [ $phone ], $message, '+98club' );

		return $sent;
	}



	/**
	 *
	 * Pre populate checkout fields.
	 *
	 */

	public function fsms_pre_populate_checkout_fields( $input, $key ) {
		global $current_user;
		$digits_mobile = get_user_meta( $current_user->ID, 'digits_phone_no', true );
		switch ( $key ):
			case 'billing_first_name':
			case 'shipping_first_name':
				return $current_user->first_name;
				break;

			case 'billing_last_name':
			case 'shipping_last_name':
				return $current_user->last_name;
				break;
			case 'billing_phone':
				return ! empty( $digits_mobile ) ? '0' . $digits_mobile : '';
				break;

		endswitch;
	}

	/**
	 *
	 * Gravity form pre submission.
	 *
	 */

	public function fsms_gf_pre_submission( $form ) {
		foreach ( $_POST as $name => $value ) {
			$_POST[ $name ] = Farazsms_Base::fsms_tr_num( $value );
		}
	}

	/**
	 *
	 * Digits filter mobile.
	 *
	 */

	public function fsms_digits_filter_mobile( $mobile ) {
		return Farazsms_Base::fsms_tr_num( $mobile );
	}

	/**
	 *
	 * Check remaining days.
	 *
	 */

	public function fsms_check_remaining_days() {
		$panel_expire_date = get_option( 'fsms_panel_expire_date', '' );
		if ( empty( $panel_expire_date ) ) {
			return;
		}
		$future     = strtotime( $panel_expire_date );
		$timefromdb = time();
		$timeleft   = $future - $timefromdb;
		$daysleft   = round( ( ( $timeleft / 24 ) / 60 ) / 60 );
		if ( ! is_numeric( $daysleft ) ) {
			return;
		}
		if ( $daysleft > 30 ) {
			delete_option( 'sent_low_remaining_days_30' );
			delete_option( 'sent_low_remaining_days_7' );

			return;
		}
		if ( $daysleft > 20 && $daysleft < 30 ) {
			$already_sent = get_option( 'sent_low_remaining_days_30', '' );
			if ( $already_sent === '1' ) {
				return;
			}
			$fsms_base           = Farazsms_Base::get_instance();
			$admin_mobile_number = $fsms_base::getAdminNumber();
			$message             = __( 'Dear user, your panel will expire less than a month from now. To renew your SMS panel, contact Faraz SMS', 'farazsms' );
			$fsms_base->send_message( [ $admin_mobile_number ], $message, '+98club' );
			update_option( 'sent_low_remaining_days_30', '1' );
		} elseif ( $daysleft > 1 && $daysleft < 7 ) {
			$already_sent = get_option( 'sent_low_remaining_days_7', '' );
			if ( $already_sent == '1' ) {
				return;
			}
			$fsms_base           = Farazsms_Base::get_instance();
			$admin_mobile_number = $fsms_base::getAdminNumber();
			$message             = __( 'Dear user, your panel will expire less than a week from now. To renew your SMS panel, contact Faraz SMS.', 'farazsms' );
			$result              = $fsms_base->send_message( [ $admin_mobile_number ], $message, '+98club' );
			update_option( 'sent_low_remaining_days_7', '1' );
		}
	}

	/**
	 *
	 * PMP membership expiry
	 *
	 */

	public function fsms_pmp_membership_membership_expiry( $user_id, $membership_id ) {
		$pmp_send_expire_noti_sms = self::$pmp_send_expire_notify;
		$expire_noti_sms_message  = self::$pmp_expire_notify_msg;
		if ( $pmp_send_expire_noti_sms === 'false' || empty( $expire_noti_sms_message ) ) {
			return;
		}
		$fsms_base          = Farazsms_Base::get_instance();
		$phone              = get_user_meta( $user_id, 'digits_phone', true );
		$selected_meta_keys = self::$custom_phone_meta_keys ?? [];
		if ( empty( $phone ) && ! empty( $selected_meta_keys ) ) {
			foreach ( $selected_meta_keys as $meta ) {
				$phone = get_user_meta( $user_id, $meta, true );
				if ( ! empty( $phone ) && $fsms_base::validate_mobile_number( $phone ) ) {
					break;
				}
			}
		}
		if ( empty( $phone ) ) {
			return;
		}
		$user    = get_userdata( $user_id );
		$message = str_replace( array(
			'%display_name%',
		), array(
			$user->display_name,
		), $expire_noti_sms_message );
		$fsms_base->send_message( [ $phone ], $message, '+98club' );
	}

}
