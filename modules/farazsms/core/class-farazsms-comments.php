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

	private static $disable_email_filed;
	private static $disable_website_filed;
	private static $disable_cookies;
	private static $add_mobile_field;
	private static $required_mobile_field;
	private static $notify_admin_for_comment;
	private static $comment_reply_pattern;
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
			self::$disable_email_filed              = $comments_options['disable_email_filed'];
			self::$disable_website_filed            = $comments_options['disable_website_filed'];
			self::$disable_cookies                  = $comments_options['disable_cookies'];
			self::$add_mobile_field                 = $comments_options['add_mobile_field'];
			self::$required_mobile_field            = $comments_options['required_mobile_field'];
			self::$notify_admin_for_comment         = $comments_options['notify_admin_for_comment'];
			self::$comment_reply_pattern            = $comments_options['comment_reply_pattern'];
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
		add_filter( 'comment_form_default_fields', [ $this, 'disable_comment_fields' ], 99, 1 );

		// Hook into WordPress comment hooks to trigger sending SMS notifications
		add_action( 'comment_unapproved_to_approved', [ $this, 'send_author_sms_on_comment_approved' ] );
//        add_action( 'edit_comment', [ $this, 'send_author_sms_on_comment_edit' ], 10, 2 );
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

		return array_slice( $my_cols, 0, 3, true ) + $temp_columns + array_slice( $my_cols, 3, null, true );
	}

	public function comments_fsms_table_columns_content( $column, $comment_ID ) {
		switch ( $column ) :

			case 'mobile' :
			{
				echo esc_html( get_comment_meta( $comment_ID, 'mobile', true ) );
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
		$allowed_tags = [
			'input' => [
				'class'       => [],
				'type'        => [],
				'name'        => [],
				'id'          => [],
				'placeholder' => [],
				'required'    => [],
			],
			'p'     => [
				'class' => [],
			],
			'span'  => [
				'class' => [],
			],
		];

		$mobile_filed = '<p class="comment-form-phone uk-margin-top"><label for="mobile">' . esc_html__( 'Mobile', 'farazsms' );
		if ( self::$required_mobile_field === true ) {
			$mobile_filed .= ' <span class="required">*</span></label>';
			$required     = 'required="required"';
		}
		$mobile_filed .= '<input class="uk-input uk-width-large uk-display-block" type="text" name="mobile" id="mobile" placeholder=" ' . esc_attr__( 'Like: 09300410381', 'farazsms' ) . '" /></p>';


		echo wp_kses( $mobile_filed, $allowed_tags );
	}

	// Save mobile field.
	public function save_mobile_field( $comment_id ) {
		if ( isset( $_POST['mobile'] ) ) {
			$mobile = Farazsms_Base::validate_mobile_number( sanitize_text_field( $_POST['mobile'] ) );
			add_comment_meta( $comment_id, 'mobile', $mobile );
		}
		self::send_admin_sms_on_new_comment( $comment_id );
		$comment = get_comment( $comment_id );
		if ( $comment->comment_approved == 1 ) {
			$comment = get_comment( $comment_id );
			self::send_author_sms_on_comment_approved( $comment );
		}

	}

	// Verify comment input
	public function verify_comment_input( $comment_data ) {
		if ( empty( $comment_data['comment_parent'] ) && self::$required_mobile_field === true && empty( sanitize_text_field( $_POST['mobile'] ) ) ) {
			wp_die( esc_html__( 'Error: Mobile number is required.', 'farazsms' ) );
		}

		return $comment_data;
	}


	/**
	 * Sends an SMS notification to the site admin when a new comment is submitted.
	 *
	 * @param int $comment_ID ID of the comment being submitted.
	 */
	public function send_admin_sms_on_new_comment( int $comment_ID ) {
		$comment           = get_comment( $comment_ID );
		$comment_post_type = get_post_type( $comment->comment_post_ID );

		// Check if the comment is on a post
		if ( $comment_post_type === 'post' ) {
			$data                  = self::comments_farazsms_shortcode( $comment );
			$comment_author_mobile = get_comment_meta( $comment_ID, 'mobile', true ) ?? '';
			$user_name             = $comment->comment_author;
			$user                  = get_user_by( 'id', $comment->user_id );

			if ( isset( $user ) && is_object( $user ) ) {
				$is_admin = in_array( 'administrator', $user->roles );
			} else {
				$is_admin = false;
			}

			$admin_number = Farazsms_Base::$admin_number;

			if ( self::$comment_phonebook_id ) {
				self::save_comment_mobile_to_phonebook( $comment_author_mobile, $user_name );
			}

			// Send SMS notification only to the admin
			if ( ! $is_admin && ! empty( $admin_number && self::$notify_admin_for_comment ) ) {
				self::send_comment_sms( $admin_number, self::$notify_admin_for_comment_pattern, $data );
			}
		}
	}


	/**
	 * Send SMS to author of comment when comment is approved.
	 * Send SMS to author of parent comment if there is an approved reply comment.
	 *
	 * @param $comment
	 *
	 * @return void
	 */
	public function send_author_sms_on_comment_approved( $comment ) {
		$comment_author_mobile        = get_comment_meta( $comment->comment_ID, 'mobile', true ) ?? '';
		$comment_parent               = $comment->comment_parent;
		$comment_parent_author_mobile = '';

		if ( $comment_parent != 0 ) {
			$comment_parent_author_mobile = get_comment_meta( $comment_parent, 'mobile', true ) ?? '';
		}

		if ( empty( $comment_author_mobile ) && empty( $comment_parent_author_mobile ) ) {
			return;
		}

		$data = self::comments_farazsms_shortcode( $comment );

		if ( ! empty( $comment_author_mobile ) && ! empty( self::$comment_pattern ) ) {
			self::send_comment_sms( $comment_author_mobile, self::$comment_pattern, $data );
		}

		if ( ! empty( $comment_parent_author_mobile ) && ! empty( self::$comment_reply_pattern ) ) {
			$reply_data = self::comments_farazsms_shortcode( $comment );
			self::send_comment_sms( $comment_parent_author_mobile, self::$comment_reply_pattern, $reply_data );
		}
	}

	/**
	 * Send comment sms.
	 *
	 * @param $phone
	 * @param $pattern
	 * @param $data
	 *
	 * @return bool|void
	 */
	public function send_comment_sms( $phone, $pattern, $data ) {
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
	public function comments_farazsms_shortcode( $comment ) {
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

	/**
	 * Disable comment fields.
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function disable_comment_fields( $fields ) {
		if ( self::$disable_email_filed ) {
			unset( $fields['email'] );
		}
		if ( self::$disable_website_filed ) {
			unset( $fields['url'] );
		}
		if ( self::$disable_cookies ) {
			unset( $fields['cookies'] );
		}

		return $fields;
	}
}

Farazsms_Comments::get_instance();
