<?php

/**
 * Farazsms comments.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}
/**
 * Class Farazsms_Comments.
 */
class Farazsms_Comments
{
	private static $elementorPro;

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	private static $add_mobile_field;
	private static $required_mobile_field;
	private static $notify_admin_for_comment;
	private static $approved_comment_pattern;
	private static $comment_pattern;
	private static $notify_admin_for_comment_pattern;
	private static $comment_phonebook_id;

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
		$comments_options = json_decode( get_option( 'farazsms_comments_options' ), true );
		if ( $comments_options ) {
			self::$add_mobile_field                 = $comments_options['add_mobile_field'];
			self::$required_mobile_field            = $comments_options['required_mobile_field'];
			self::$notify_admin_for_comment         = $comments_options['notify_admin_for_comment'];
			self::$approved_comment_pattern         = $comments_options['approved_comment_pattern'];
			self::$comment_pattern                  = $comments_options['comment_pattern'];
			self::$notify_admin_for_comment_pattern = $comments_options['notify_admin_for_comment_pattern'];
			self::$comment_phonebook_id                = current(array_column($comments_options['comment_phonebook'], 'value'));
		}

		add_action( 'manage_edit-comments_columns', [ $this, 'comments_fsms_table_columns' ] );
		add_action( 'manage_comments_custom_column', [ $this, 'comments_fsms_table_columns_content' ], 10, 2 );
		add_filter( 'update_user_metadata', [ $this, 'monitor_update_user_metadata' ], 499, 4 );
		add_action( 'profile_update', [ $this, 'fsms_user_profile_updated' ], 99, 2 );
		add_action( 'comment_form_logged_in_after', [ $this, 'add_mobile_field_to_comment_form' ] );
		add_action( 'comment_form_after_fields', [ $this, 'add_mobile_field_to_comment_form' ] );
		add_action( 'preprocess_comment', [ $this, 'verify_comment_input' ] );
		add_action( 'comment_post', [ $this, 'save_mobile_field' ] );

	}

	/**
	 * Comments table columns.
	 *
	 * @since 1.0.0
	 */
	public function comments_fsms_table_columns( $my_cols ) {
		$temp_columns = [
			'mobile' => __( 'Phone number', 'farazsms' ),
		];

		return ( array_slice( $my_cols, 0, 3, true ) + $temp_columns + array_slice( $my_cols, 3, null, true ) );

	}

	public function comments_fsms_table_columns_content( $column, $comment_ID ) {
		global $comment;
		switch ( $column ) :
			case 'mobile':
			{
				echo get_comment_meta( $comment_ID, 'mobile', true );
				break;
			}
		endswitch;

	}

	/**
	 * Monitor update user metadata.
	 */
	public function monitor_update_user_metadata( $check, $object_id, $meta_key, $meta_value ) {
		$selected_meta_keys = Farazsms_Base::$custom_phone_meta_keys ?? [];
		if ( ! in_array( $meta_key, $selected_meta_keys ) ) {
			return $check;
		}
		$phone = Farazsms_Base::validate_mobile_number( $meta_value );
		if ( ! $phone ) {
			return $check;
		}
		$custom_phonebooks = Farazsms_Base::$custom_phonebook;
		if ( empty( $custom_phonebooks ) ) {
			return $check;
		}
		$user_info = get_userdata( $object_id );
		foreach ( $custom_phonebooks as $phonebookId ) {
			$data[] = [
				'number'       => $phone,
				'name'         => $user_info->display_name ?? '',
				'phonebook_id' => (int) $phonebookId['value']
			];
			Farazsms_Base::save_list_of_phones_to_phonebook( $data );
		}
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

		$digits_phonebooks = Farazsms_Base::$digits_phonebook;

		$user_info = get_userdata( $user_id );
		foreach ( $digits_phonebooks as $phonebookId ) {
			$data[] = [
				'number'       => $digits_phone,
				'name'         => $user_info->display_name ?? '',
				'phonebook_id' => (int) $phonebookId['value']
			];
			Farazsms_Base::save_list_of_phones_to_phonebook( $data );
		}
		$already_sent_one = get_user_meta( $user_id, 'sent_welcome_message', true );
		if ( ! empty( $already_sent_one ) && $already_sent_one == '1' ) {
			return;
		}
		Farazsms_Base::send_welcome_message( $digits_phone, $user_id );
		update_user_meta( $user_id, 'sent_welcome_message', '1' );
	}

	/**
	 * Add mobile field to comment form
	 */
	public function add_mobile_field_to_comment_form() {
		if ( ! self::$add_mobile_field === true ) {
			return;
		}
		$r   = '';
		$res = '<p class="comment-form-phone"><label for="mobile">شماره موبایل';
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
		$comment   = get_comment( $comment_id );
		$data      = $this->comments_farazsms_shortcode( $comment, $comment_id );
		$mobile    = get_comment_meta( $data['parent'] )['mobile'][0] ?? '';
		$user      = get_user_by( 'id', $comment->user_id );
		$is_admin  = in_array( 'administrator', (array) $user->roles );
		if ( $comment->comment_parent == 0 ) {
			$mobile = get_comment_meta( $comment_id )['mobile'][0] ?? '';
			if ( ! empty( self::$approved_comment_pattern ) || ! empty( $mobile ) ) {
				$this->send_comment_reply_sms( $mobile, self::$approved_comment_pattern, $data );
			}
		}
		if ( self::$notify_admin_for_comment == 1 && ! $is_admin ) {
			$this->send_comment_reply_sms( Farazsms_Base::$admin_number, self::$notify_admin_for_comment_pattern, $data );
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
			$this->send_comment_reply_sms( $mobile, self::$comment_pattern, $data );
			$this->save_comment_mobile_to_phonebook( $mobile );
		}
	}

	/**
	 * Send comment replay sms.
	 */
	public function send_comment_reply_sms( $phone, $pattern, $data ) {
		$phone = Farazsms_Base::fsms_tr_num( $phone );
		if ( empty( $pattern ) ) {
			return;
		}

		$input_data     = [];
		$patternMessage = Farazsms_Base::get_registered_pattern_variables( $pattern );
		if ( $patternMessage === null ) {
			return;
		}
		if ( str_contains( $patternMessage, '%title%' ) ) {
			$input_data['title'] = $data['title'];
		}
		if ( str_contains( $patternMessage, '%name%' ) ) {
			$input_data['name'] = $data['name'];
		}
		if ( str_contains( $patternMessage, '%email%' ) ) {
			$input_data['email'] = $data['email'];
		}
		if ( str_contains( $patternMessage, '%link%' ) ) {
			$input_data['link'] = $data['link'];
		}
		if ( str_contains( $patternMessage, '%content%' ) ) {
			$input_data['content'] = $data['content'];
		}

		return Farazsms_Base::farazsms_send_pattern( $pattern, $phone, $input_data );

	}

	/**
	 * Send comment replay sms to admin
	 */
	public function send_comment_reply_sms_to_admin( $data ) {
		$fsms_admin_notify_pcode = Farazsms_Base::fsms_tr_num( Farazsms_Base::$admin_login_notify_pattern );
		if ( empty( $fsms_admin_notify_pcode ) || empty( Farazsms_Base::$admin_number ) ) {
			return;
		}

		$input_data     = [];
		$patternMessage = Farazsms_Base::get_registered_pattern_variables( $fsms_admin_notify_pcode );
		if ( $patternMessage === null ) {
			return;
		}

		if ( str_contains( $patternMessage, '%title%' ) ) {
			$input_data['title'] = $data['title'];
		}
		if ( str_contains( $patternMessage, '%name%' ) ) {
			$input_data['name'] = $data['name'];
		}
		if ( str_contains( $patternMessage, '%email%' ) ) {
			$input_data['email'] = $data['email'];
		}
		if ( str_contains( $patternMessage, '%link%' ) ) {
			$input_data['link'] = $data['link'];
		}
		if ( str_contains( $patternMessage, '%content%' ) ) {
			$input_data['content'] = $data['content'];
		}

		return Farazsms_Base::farazsms_send_pattern( $fsms_admin_notify_pcode, Farazsms_Base::$admin_number, $input_data );

	}

	/**
	 * Save comment mobile to phonebook.
	 */
	public function save_comment_mobile_to_phonebook( $phone ) {
		$phone = Farazsms_Base::fsms_tr_num( $phone );
		Farazsms_Base::save_a_phone_to_phonebook( $phone, self::$comment_phonebook_id );
	}

	/**
	 * Comments farazsms shortcode.
	 */
	public function comments_farazsms_shortcode( $comment, $comment_id ) {
		$post = get_post( $comment->comment_post_ID );

		return [
			'title'   => $post->post_title,
			'name'    => $comment->comment_author,
			'email'   => $comment->comment_author_email,
			'link'    => wp_get_shortlink( $post->ID ) . '#comment-' . $comment->comment_ID,
			'content' => $comment->comment_content,
			'parent'  => $comment->comment_parent
		];
	}
}
Farazsms_Comments::get_instance();
