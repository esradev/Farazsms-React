<?php

/**
 * Farazsms aff.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Aff.
 */
class Farazsms_Aff {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	public static $aff_user_mobile_field;
	public static $aff_user_register;
	public static $aff_user_register_pattern;
	public static $aff_user_new_ref;
	public static $aff_user_new_ref_pattern;
	public static $aff_user_on_approval;
	public static $aff_user_on_approval_pattern;
	public static $aff_admin_user_register;
	public static $aff_admin_user_register_pattern;
	public static $aff_admin_user_new_ref;
	public static $aff_admin_user_new_ref_pattern;
	public static $aff_admin_user_on_approval;
	public static $aff_admin_user_on_approval_pattern;


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
		$aff_options = json_decode( get_option( 'farazsms_aff_options' ), true );
		if ( $aff_options ) {
			self::$aff_user_mobile_field              = $aff_options['aff_user_mobile_field']['value'] ?? '';
			self::$aff_user_register                  = $aff_options['aff_user_register'];
			self::$aff_user_register_pattern          = $aff_options['aff_user_register_pattern'];
			self::$aff_user_new_ref                   = $aff_options['aff_user_new_ref'];
			self::$aff_user_new_ref_pattern           = $aff_options['aff_user_new_ref_pattern'];
			self::$aff_user_on_approval               = $aff_options['aff_user_on_approval'];
			self::$aff_user_on_approval_pattern       = $aff_options['aff_user_on_approval_pattern'];
			self::$aff_admin_user_register            = $aff_options['aff_admin_user_register'];
			self::$aff_admin_user_register_pattern    = $aff_options['aff_admin_user_register_pattern'];
			self::$aff_admin_user_new_ref             = $aff_options['aff_admin_user_new_ref'];
			self::$aff_admin_user_new_ref_pattern     = $aff_options['aff_admin_user_new_ref_pattern'];
			self::$aff_admin_user_on_approval         = $aff_options['aff_admin_user_on_approval'];
			self::$aff_admin_user_on_approval_pattern = $aff_options['aff_admin_user_on_approval_pattern'];
		}

		add_action( 'affwp_register_user', [ $this, 'fsms_affwp_register_user', ], 10, 3 );
		add_action( 'yith_wcaf_new_affiliate', [ $this, 'fsms_yith_wcaf_register_user' ] );
		add_action( 'uap_on_register_action', [ $this, 'fsms_uap_register_user' ] );

		add_action( 'affwp_set_affiliate_status', [ $this, 'fsms_affwp_set_affiliate_status', ], 10, 3 );
		add_action( 'affwp_referral_accepted', [ $this, 'fsms_affwp_referral_accepted', ], 10, 2 );
		add_action( 'yith_wcaf_affiliate_enabled', [ $this, 'fsms_yith_wcaf_set_affiliate_status' ] );

		add_action( 'affwp_register_fields_before_tos', [ $this, 'fsms_affwp_register_fields_before_tos' ] );
		add_action( 'affwp_new_affiliate_end', [ $this, 'fsms_affwp_new_affiliate_end' ] );
		add_action( 'affwp_edit_affiliate_end', [ $this, 'fsms_affwp_edit_affiliate_end' ] );
		add_action( 'affwp_update_affiliate', [ $this, 'fsms_affwp_update_affiliate' ] );
	}

	/**
	 * AFF-WP register user.
	 */
	public function fsms_affwp_register_user( $affiliate_id, $status, $args ) {
		$user_id     = affwp_get_affiliate_user_id( $affiliate_id );
		$user        = get_user_by( 'id', $user_id );
		$user_mobile = $_POST['affs-user-mobile'] ?? '';
		update_user_meta( $user->ID, 'affs_mobile', $user_mobile );
		$args['user_mobile'] = $user_mobile;
		if ( ! isset( $args['user_nicename'] ) ) {
			$args['user_nicename'] = $user->nickname;
		}

		if ( self::$aff_user_register == 'true' ) {
			if ( ! empty( self::$aff_user_register_pattern ) ) {
				$this->affs_send_sms( $user_mobile, self::$aff_user_register_pattern, $args );
			}
		}
		if ( self::$aff_admin_user_register == 'true' ) {
			$admin_mobile = Farazsms_Base::$admin_number;
			if ( ! empty( self::$aff_admin_user_register_pattern ) && ! empty( $admin_mobile ) ) {
				$this->affs_send_sms( $admin_mobile, self::$aff_admin_user_register_pattern, $args );
			}
		}
	}

	/**
	 * AFFS send sms.
	 */
	public function affs_send_sms( $phone, $user_register_pattern, $args ) {
		$input_data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $user_register_pattern );
		if ( str_contains( $patternMessage, '%user_login%' ) ) {
			$input_data['user_login'] = $args['user_login'];
		}
		if ( str_contains( $patternMessage, '%user_nicename%' ) ) {
			$input_data['user_nicename'] = $args['user_nicename'];
		}
		if ( str_contains( $patternMessage, '%user_email%' ) ) {
			$input_data['user_email'] = $args['user_email'];
		}
		if ( str_contains( $patternMessage, '%display_name%' ) ) {
			$input_data['display_name'] = $args['display_name'];
		}
		if ( str_contains( $patternMessage, '%user_mobile%' ) ) {
			$input_data['user_mobile'] = $args['user_mobile'];
		}
		if ( str_contains( $patternMessage, '%amount%' ) ) {
			$input_data['amount'] = $args['amount'];
		}
		if ( str_contains( $patternMessage, '%amount%' ) ) {
			$input_data['amount'] = $args['amount'];
		}

		return Farazsms_Ippanel::send_pattern( $user_register_pattern, $phone, $input_data );
	}

	/**
	 * Yith WooAF register user
	 */
	public function fsms_yith_wcaf_register_user( $id ) {

		$affiliate_info = get_affiliate_by_id( $id );
		$user           = get_user_by( 'id', $affiliate_info->user_id );

		$mobile = get_user_meta( $user->ID, 'digits_phone_no' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user->ID, 'wupp_mobile' )[0] ?? '';
		}
		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user->ID, self::$aff_user_mobile_field, true );
			if ( ! empty( $mobile ) && Farazsms_Base::validate_mobile_number( $mobile ) ) {
				return;
			}
		}

		$args['user_mobile'] = $mobile;

		if ( ! isset( $args['user_nicename'] ) ) {
			$args['user_nicename'] = $user->nickname;
		}
		if ( self::$aff_user_register === true ) {
			if ( ! empty( self::$aff_user_register_pattern ) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_register_pattern, $args );
			}
		}
		if ( self::$aff_admin_user_register === true ) {
			$admin_mobile = Farazsms_Base::$admin_number;
			if ( ! empty( self::$aff_admin_user_register_pattern ) && ! empty( $admin_mobile ) ) {
				$this->affs_send_sms( $admin_mobile, self::$aff_admin_user_register_pattern, $args );
			}
		}
	}

	/**
	 * UAP register user.
	 */
	public function fsms_uap_register_user( $user_id ) {

		$user   = get_user_by( 'id', $user_id );
		$mobile = get_user_meta( $user_id, 'digits_phone_no' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'wupp_mobile' )[0] ?? '';
		}
		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user_id, self::$aff_user_mobile_field, true );
			if ( ! empty( $mobile ) && Farazsms_Base::validate_mobile_number( $mobile ) ) {
				return;
			}
		}

		$args['user_mobile'] = $mobile;

		if ( ! isset( $args['user_nicename'] ) ) {
			$args['user_nicename'] = $user->nickname;
		}
		if ( self::$aff_user_register ) {
			if ( ! empty( self::$aff_user_register_pattern ) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_register_pattern, $args );
			}
		}
		if ( self::$aff_admin_user_register ) {
			$admin_mobile = Farazsms_Base::$admin_number;
			if ( ! empty( self::$aff_admin_user_register_pattern ) && ! empty( $admin_mobile ) ) {
				$this->affs_send_sms( $admin_mobile, self::$aff_admin_user_register_pattern, $args );
			}
		}
	}

	/**
	 * AFF-WP set affiliate status
	 */
	public function fsms_affwp_set_affiliate_status( $affiliate_id = 0, $status = '', $old_status = '' ) {
		if ( doing_action( 'affwp_add_affiliate' ) ) {
			return;
		}
		if ( doing_action( 'affwp_affiliate_register' ) ) {
			return;
		}
		$user_id               = affwp_get_affiliate_user_id( $affiliate_id );
		$user                  = get_user_by( 'id', $user_id );
		$data['user_login']    = $user->user_login;
		$data['user_nicename'] = $user->nickname;
		$data['user_email']    = $user->user_email;
		$data['display_name']  = $user->display_name;
		$mobile                = get_user_meta( $user_id, 'affs_mobile' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'digits_phone_no' )[0] ?? '';
		}
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'wupp_mobile' )[0] ?? '';
		}

		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user_id, self::$aff_user_mobile_field, true );
			if ( ! empty( $mobile ) && Farazsms_Base::validate_mobile_number( $mobile ) ) {
				return;
			}
		}
		if ( empty( $mobile ) ) {
			return;
		}
		if ( str_starts_with( $mobile, '9' ) ) {
			$mobile = '0' . $mobile;
		}
		$data['user_mobile'] = $mobile;
		if ( self::$aff_user_on_approval ) {
			if ( ! empty( $user ) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_on_approval_pattern, $data );
			}
		}
		if ( self::$aff_admin_user_on_approval ) {
			$admin_mobile = Farazsms_Base::$admin_number;
			if ( ! empty( self::$aff_admin_user_on_approval_pattern ) && ! empty( $admin_mobile ) ) {
				$this->affs_send_sms( $admin_mobile, self::$aff_admin_user_on_approval_pattern, $data );
			}
		}
	}

	/**
	 * Yith WooAF set affiliate status.
	 */
	public function fsms_yith_wcaf_set_affiliate_status( $affiliate_id ) {
		$affiliate_info        = get_affiliate_by_id( $affiliate_id );
		$user                  = get_user_by( 'id', $affiliate_info->user_id );
		$data['user_login']    = $user->user_login;
		$data['user_nicename'] = $user->nickname;
		$data['user_email']    = $user->user_email;
		$data['display_name']  = $user->display_name;
		$mobile                = get_user_meta( $affiliate_info->user_id, 'affs_mobile' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $affiliate_info->user_id, 'digits_phone_no' )[0] ?? '';
		}
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $affiliate_info->user_id, 'wupp_mobile' )[0] ?? '';
		}

		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $affiliate_info->user_id, self::$aff_user_mobile_field, true );
			if ( ! empty( $mobile ) && Farazsms_Base::validate_mobile_number( $mobile ) ) {
				return;
			}
		}
		if ( empty( $mobile ) ) {
			return;
		}
		if ( str_starts_with( $mobile, '9' ) ) {
			$mobile = '0' . $mobile;
		}
		$data['user_mobile'] = $mobile;
		if ( self::$aff_user_on_approval ) {
			$aff_user_on_approval_pattern = self::$aff_user_on_approval_pattern;
			if ( ! empty( $user ) ) {
				$this->affs_send_sms( $mobile, $aff_user_on_approval_pattern, $data );
			}
		}
		if ( self::$aff_admin_user_on_approval ) {
			$aff_admin_user_on_approval_pattern = self::$aff_admin_user_on_approval_pattern;
			$admin_mobile                       = Farazsms_Base::$admin_number;
			if ( ! empty( $aff_admin_user_on_approval_pattern ) && ! empty( $admin_mobile ) ) {
				$this->affs_send_sms( $admin_mobile, $aff_admin_user_on_approval_pattern, $data );
			}
		}
	}

	/**
	 * AFF-WP referral accepted
	 */
	public function fsms_affwp_referral_accepted( $affiliate_id, $referral ) {
		$referral              = (array) $referral;
		$user_id               = affwp_get_affiliate_user_id( $affiliate_id );
		$user                  = get_user_by( 'id', $user_id );
		$data['user_login']    = $user->user_login;
		$data['user_nicename'] = $user->nickname;
		$data['user_email']    = $user->user_email;
		$data['display_name']  = $user->display_name;
		$mobile                = get_user_meta( $user_id, 'digits_phone_no' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'wupp_mobile' )[0] ?? '';
		}
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'affs_mobile' )[0] ?? '';
		}

		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user_id, self::$aff_user_mobile_field, true );
			if ( ! empty( $mobile ) && Farazsms_Base::validate_mobile_number( $mobile ) ) {
				return;
			}
		}
		if ( empty( $mobile ) ) {
			return;
		}
		$data['user_mobile'] = $mobile;
		$data['amount']      = $referral['amount'];
		if ( self::$aff_user_new_ref ) {
			if ( ! empty( self::$aff_user_new_ref_pattern ) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_new_ref_pattern, $data );
			}
		}
		if ( self::$aff_admin_user_new_ref == 'true' ) {
			$admin_mobile = Farazsms_Base::$admin_number;
			if ( ! empty( self::$aff_admin_user_new_ref_pattern ) && ! empty( $admin_mobile ) ) {
				$this->affs_send_sms( $admin_mobile, self::$aff_admin_user_new_ref_pattern, $data );
			}
		}
	}

	/**
	 * AFF-WP register fields before tos
	 */
	public function fsms_affwp_register_fields_before_tos() {
		if ( is_user_logged_in() ) {
			$mobile = get_user_meta( get_current_user_id(), 'digits_phone_no', true ) ?? '';
			if ( $mobile == '' ) {
				$mobile = get_user_meta( get_current_user_id(), 'wupp_mobile', true ) ?? '';
			}
			if ( str_starts_with( $mobile, '9' ) ) {
				$mobile = '0' . $mobile;
			}
		}
		?>
        <p>
            <label for="affs-user-mobile"><?php esc_attr_e( 'Phone number', 'farazsms' ) ?></label>
            <input id="affs-user-mobile" type="text" name="affs-user-mobile"
                   title="<?php esc_attr_e( 'Phone number', 'farazsms' ) ?>" required
                   value="<?php echo $mobile ?? ''; ?>"/>
        </p>
		<?php
	}

	/**
	 * AFF-WP new affiliate end
	 */
	public function fsms_affwp_new_affiliate_end() {
		?>
        <tr class="form-row form-required">
            <th scope="row">
                <label for="affs-user-mobile"><?php esc_attr_e( 'Phone number', 'farazsms' ) ?></label>
            </th>
            <td>
                <input id="affs-user-mobile" type="text" name="affs-user-mobile"
                       title="<?php esc_attr_e( 'Phone number', 'farazsms' ) ?>" required/>
            </td>

        </tr>
		<?php
	}

	/**
	 * AFF-WP edit affiliate end
	 */
	public function fsms_affwp_edit_affiliate_end( $affiliate ) {
		?>
        <tr class="form-row form-required">
            <th scope="row">
                <label for="affs-user-mobile"><?php esc_attr_e( 'Phone number', 'farazsms' ) ?></label>
            </th>
            <td>
                <input id="affs-user-mobile" type="text" name="affs-user-mobile"
                       title="<?php esc_attr_e( 'Phone number', 'farazsms' ) ?>"
                       value="<?php echo get_user_meta( $affiliate->user_id, 'affs_mobile' )[0] ?? ''; ?>"/>
            </td>
        </tr>
		<?php
	}

	/**
	 * AFF-WP update affiliate.
	 */
	public function fsms_affwp_update_affiliate( $data ) {
		if ( isset( $data['affs-user-mobile'] ) ) {
			$user_id = affwp_get_affiliate_user_id( $data['affiliate_id'] );
			$user    = get_user_by( 'id', $user_id );
			$mobile  = $data['affs-user-mobile'] ?? '';
			update_user_meta( $user->ID, 'affs_mobile', $mobile );
			affs_add_mobile_book_number( $mobile );
		}
	}
}

Farazsms_Aff::get_instance();
