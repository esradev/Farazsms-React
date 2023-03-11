<?php

/**
 * Farazsms comments.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Comments.
 */
class Farazsms_Comments {
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
		$comments_options = json_decode( get_option( 'farazsms_comments_options' ), true );
		if ( $comments_options ) {
			self::$add_mobile_field                 = $comments_options['add_mobile_field'];
			self::$required_mobile_field            = $comments_options['required_mobile_field'];
			self::$notify_admin_for_comment         = $comments_options['notify_admin_for_comment'];
			self::$approved_comment_pattern         = $comments_options['approved_comment_pattern'];
			self::$comment_pattern                  = $comments_options['comment_pattern'];
			self::$notify_admin_for_comment_pattern = $comments_options['notify_admin_for_comment_pattern'];
			self::$comment_phonebook_id             = $comments_options['comment_phonebook']['value'] ?? '';
		}

		add_filter( 'manage_edit-comments_columns', [ $this, 'comments_fsms_table_columns' ] );
		add_action( 'manage_comments_custom_column', [ $this, 'comments_fsms_table_columns_content' ], 10, 2 );
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
			'mobile' => __( 'Mobile', 'farazsms' ),
		];

		$my_cols = array_slice( $my_cols, 0, 3, true ) + $temp_columns + array_slice( $my_cols, 3, null, true );

		return $my_cols;
	}

	public function comments_fsms_table_columns_content( $column, $comment_ID ) {
		global $comment;
		switch ( $column ) :

			case 'mobile' :
			{
				echo get_comment_meta( $comment_ID, 'mobile', true );
				break;
			}
		endswitch;
	}

	/**
	 * Add mobile field to comment form
	 */
	public function add_mobile_field_to_comment_form() {
		if ( ! self::$add_mobile_field === true ) {
			return;
		}

		$mobile_filed = '<p class="comment-form-phone uk-margin-top"><label for="mobile">' . __( 'Phone number', 'farazsms' ) . '</label>';
		if ( self::$required_mobile_field === true ) {
			$mobile_filed .= ' <span class="required">*</span>';
			$required     = 'required="required"';
		}
		$mobile_filed .= '<input class="uk-input uk-width-large uk-display-block" type="text" name="mobile" id="mobile /></p>';


		echo $mobile_filed;


	}

	// Save mobile field.
	public function save_mobile_field( $comment_id ) {
		if ( isset( $_POST['mobile'] ) ) {
			$mobile = Farazsms_Base::validate_mobile_number( esc_attr( $_POST['mobile'] ) );
			add_comment_meta( $comment_id, 'mobile', $mobile );
		}
		$this->response_to_comment( $comment_id );
	}

	// Verify comment input
	public function verify_comment_input( $comment_data ) {
		if ( empty( $comment_data['comment_parent'] ) && self::$required_mobile_field === true ) {
			if ( ! isset( $_POST['mobile'] ) or empty( $_POST['mobile'] ) ) {
				wp_die( __( 'Error: Mobile number is required.', 'farazsms' ) );
			}
		}

		return $comment_data;
	}

	// Response to comment
	public function response_to_comment( $comment_id ) {
		$comment   = get_comment( $comment_id );
		$data      = $this->comments_farazsms_shortcode( $comment, $comment_id );
		$mobile    = get_comment_meta( $comment_id )['mobile'][0] ?? '';
		$user_name = $comment->comment_author;
		$user      = get_user_by( 'id', $comment->user_id );
		$is_admin  = in_array( 'administrator', $user->roles );

		if ( self::$comment_phonebook_id ) {
			$this->save_comment_mobile_to_phonebook( $mobile, $user_name );
		}

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
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
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

		return Farazsms_Ippanel::send_pattern( $pattern, $phone, $input_data );

	}

	/**
	 * Send comment replay sms to admin
	 */
	public function send_comment_reply_sms_to_admin( $data ) {
		$fsms_admin_notify_pattern_code = Farazsms_Base::fsms_tr_num( Farazsms_Base::$admin_login_notify_pattern );
		if ( empty( $fsms_admin_notify_pattern_code ) || empty( Farazsms_Base::$admin_number ) ) {
			return;
		}

		$input_data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $fsms_admin_notify_pattern_code );
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

		return Farazsms_Ippanel::send_pattern( $fsms_admin_notify_pattern_code, Farazsms_Base::$admin_number, $input_data );

	}

	/**
	 * Save comment mobile to phonebook.
	 */
	public function save_comment_mobile_to_phonebook( $number, $name ) {
		$list[0] = (object) [
			'number'       => $number,
			'name'         => $name,
			'phonebook_id' => (int) self::$comment_phonebook_id
		];
		Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
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
