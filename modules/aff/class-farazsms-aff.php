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

        // WP Affiliate
		add_action( 'affwp_register_user', [ $this, 'fsms_affwp_register_user', ], 10, 3 );
		add_action( 'affwp_set_affiliate_status', [ $this, 'fsms_affwp_set_affiliate_status', ], 10, 3 );
		add_action( 'affwp_referral_accepted', [ $this, 'fsms_affwp_referral_accepted', ], 10, 2 );
		add_action( 'affwp_register_fields_before_tos', [ $this, 'fsms_affwp_register_fields_before_tos' ] );
		add_action( 'affwp_new_affiliate_end', [ $this, 'fsms_affwp_new_affiliate_end' ] );
		add_action( 'affwp_edit_affiliate_end', [ $this, 'fsms_affwp_edit_affiliate_end' ] );

        // Yith WooCommerce Affiliate
		add_action( 'yith_wcaf_new_affiliate', [ $this, 'fsms_yith_wcaf_register_user' ] );
		add_action( 'yith_wcaf_affiliate_enabled', [ $this, 'fsms_yith_wcaf_set_affiliate_status' ] );
		add_action('yith_wcaf_register_form', [$this, 'fsms_yith_wcaf_add_mobile_field']);
		add_action('yith_wcaf_settings_form_after_payment_email', [$this, 'fsms_yith_wcaf_add_mobile_field_on_settings']);
		add_action('yith_wcaf_new_affiliate', [$this, 'fsms_yith_wcaf_register_mobile_field']);
		add_action('yith_wcaf_save_affiliate_settings', [$this, 'fsms_yith_wcaf_register_mobile_field_settings'], 10, 2);

	}

	/**
     * Affiliate Send SMS
     *
	 * @param $phone
	 * @param $pattern
	 * @param $incoming_data
	 *
	 * @return bool
	 */
	public function affs_send_sms( $phone, $pattern, $incoming_data ) {
		$data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
		if ( str_contains( $patternMessage, '%user_login%' ) ) {
			$data['user_login'] = $incoming_data['user_login'];
		}
		if ( str_contains( $patternMessage, '%user_nicename%' ) ) {
			$data['user_nicename'] = $incoming_data['user_nicename'];
		}
		if ( str_contains( $patternMessage, '%user_email%' ) ) {
			$data['user_email'] = $incoming_data['user_email'];
		}
		if ( str_contains( $patternMessage, '%display_name%' ) ) {
			$data['display_name'] = $incoming_data['display_name'];
		}
		if ( str_contains( $patternMessage, '%user_mobile%' ) ) {
			$data['user_mobile'] = $incoming_data['user_mobile'];
		}
		if ( str_contains( $patternMessage, '%amount%' ) ) {
			$data['amount'] = $incoming_data['amount'];
		}
		return Farazsms_Ippanel::send_pattern( $pattern, $phone, $data );
	}

	/**
     * WP Affiliate Send SMS on affiliate register.
     *
	 * @param $affiliate_id
	 * @param $status
	 * @param $data
	 *
	 * @return void
	 */
	public function fsms_affwp_register_user( $affiliate_id, $status, $data ) {
		$user_id     = affwp_get_affiliate_user_id( $affiliate_id );
		$user        = get_user_by( 'id', $user_id );
		$user_mobile = isset( $_POST['farazsms_affiliate_phone'] ) ? sanitize_text_field( $_POST['farazsms_affiliate_phone'] ) : '';
		update_user_meta( $user->ID, 'farazsms_affiliate_phone', $user_mobile );
		$data['user_mobile'] = $user_mobile;
		if ( ! isset( $data['user_nicename'] ) ) {
			$data['user_nicename'] = $user->nickname;
		}

		if ( self::$aff_user_register ) {
			if ( ! empty( self::$aff_user_register_pattern ) ) {
				$this->affs_send_sms( $user_mobile, self::$aff_user_register_pattern, $data );
			}
		}
		if ( self::$aff_admin_user_register ) {
			if ( ! empty( self::$aff_admin_user_register_pattern ) && ! empty( Farazsms_Base::$admin_number ) ) {
				$this->affs_send_sms( Farazsms_Base::$admin_number, self::$aff_admin_user_register_pattern, $data );
			}
		}
	}

	/**
     * WP Affiliate Send SMS on affiliate approval.
	 *
	 * @param int $affiliate_id
	 * @param string $status
	 * @param string $old_status
	 *
	 * @return void
	 */
	public function fsms_affwp_set_affiliate_status(  $affiliate_id = 0, $status = '', $old_status = '' ) {
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
		$mobile                = get_user_meta( $user_id, 'farazsms_affiliate_phone' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'digits_phone_no' )[0] ?? '';
		}
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $user_id, 'wupp_mobile' )[0] ?? '';
		}

		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user_id, self::$aff_user_mobile_field, true );
		}
		if ( str_starts_with( $mobile, '9' ) ) {
			$mobile = '0' . $mobile;
		}
		$data['user_mobile'] = $mobile;
		if ( self::$aff_user_on_approval && ! empty( $user ) && ! empty($mobile) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_on_approval_pattern, $data );
		}
		if ( self::$aff_admin_user_on_approval && ! empty( self::$aff_admin_user_on_approval_pattern ) && ! empty( Farazsms_Base::$admin_number ) ) {
				$this->affs_send_sms( Farazsms_Base::$admin_number, self::$aff_admin_user_on_approval_pattern, $data );
		}
	}

	/**
     * WP Affiliate Send SMS on affiliate referral.
     *
	 * @param $affiliate_id
	 * @param $referral
	 *
	 * @return void
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
			$mobile = get_user_meta( $user_id, 'farazsms_affiliate_phone' )[0] ?? '';
		}
		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user_id, self::$aff_user_mobile_field, true );
		}
		$data['user_mobile'] = $mobile;
		$data['amount']      = $referral['amount'];
		if ( self::$aff_user_new_ref && ! empty( self::$aff_user_new_ref_pattern) && ! empty($mobile) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_new_ref_pattern, $data );
		}
		if ( self::$aff_admin_user_new_ref && ! empty( self::$aff_admin_user_new_ref_pattern ) && ! empty( Farazsms_Base::$admin_number ) ) {
				$this->affs_send_sms( Farazsms_Base::$admin_number, self::$aff_admin_user_new_ref_pattern, $data );
		}
	}

	/**
     * WP Affiliate register fields.
     *
	 * @return void
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
	        <label for="farazsms_affiliate_phone"><?php esc_html_e( 'Phone number', 'farazsms' ) ?></label>
	        <input id="farazsms_affiliate_phone" type="text" name="farazsms_affiliate_phone"
                   title="<?php esc_attr_e( 'Phone number', 'farazsms' ) ?>" required
                   value="<?php echo esc_attr( $mobile ?? '' ); ?>"/>
        </p>
		<?php
	}

	/**
     * WP Affiliate on new affiliate end.
     *
	 * @return void
	 */
	public function fsms_affwp_new_affiliate_end() {
		?>
        <tr class="form-row form-required">
            <th scope="row">
                <label for="farazsms_affiliate_one"><?php esc_html_e( 'Phone number', 'farazsms' ) ?></label>
            </th>
            <td>
                <input id="farazsms_affiliate_phone" type="text" name="farazsms_affiliate_phone"
                       title="<?php esc_attr_e( 'Phone number', 'farazsms' ) ?>" required/>
            </td>

        </tr>
		<?php
	}

	/**
     * WP Affiliate on edit affiliate end.
	 * @param $affiliate
	 *
	 * @return void
	 */
	public function fsms_affwp_edit_affiliate_end( $affiliate ) {
		?>
        <tr class="form-row form-required">
            <th scope="row">
                <label for="farazsms_affiliate_one"><?php esc_html_e( 'Phone number', 'farazsms' ) ?></label>
            </th>
            <td>
                <input id="farazsms_affiliate_phone" type="text" name="farazsms_affiliate_phone"
                       title="<?php esc_attr_e( 'Phone number', 'farazsms' ) ?>"
                       value="<?php echo esc_attr(get_user_meta( $affiliate->user_id, 'farazsms_affiliate_phone' )[0] ?? ''); ?>"/>
            </td>
        </tr>
		<?php
	}

	/**
     * Yith WooCommerce Affiliate Send SMS on affiliate register.
     *
	 * @param $id
	 *
	 * @return void
	 */
	public function fsms_yith_wcaf_register_user( $id ) {
		$affiliate = YITH_WCAF_Affiliates()->get_affiliate_by_id($id);
		$user           = get_user_by( 'id', $affiliate['user_id'] );

		$mobile = isset( $_POST['affs-user-mobile'] ) ? sanitize_text_field( $_POST['affs-user-mobile'] ) : '';
		update_user_meta( $user->ID, 'farazsms_affiliate_phone', $mobile );

		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $user->ID, self::$aff_user_mobile_field, true );
		}
		$data['user_mobile'] = $mobile;
		$data['user_login']    = $user->user_login;
		$data['user_nicename'] = $user->nickname;
		$data['user_email']    = $user->user_email;
		$data['display_name']  = $user->display_name;
//		$data['amount']      = $referral['amount'];

		if ( self::$aff_user_register && ! empty( self::$aff_user_register_pattern ) ) {
				$this->affs_send_sms( $mobile, self::$aff_user_register_pattern, $data );
		}
		if ( self::$aff_admin_user_register && ! empty( self::$aff_admin_user_register_pattern ) && ! empty( Farazsms_Base::$admin_number ) ) {
				$this->affs_send_sms( Farazsms_Base::$admin_number, self::$aff_admin_user_register_pattern, $data );
		}
	}

	/**
     * Yith WooCommerce Affiliate Send SMS on affiliate approval.
     *
	 * @param $id
	 *
	 * @return void
	 */
	public function fsms_yith_wcaf_set_affiliate_status( $id ) {
		$affiliate = YITH_WCAF_Affiliates()->get_affiliate_by_id($id);

		$user                  = get_user_by( 'id', $affiliate['user_id'] );
		$data['user_login']    = $user->user_login;
		$data['user_nicename'] = $user->nickname;
		$data['user_email']    = $user->user_email;
		$data['display_name']  = $user->display_name;
		$mobile                = get_user_meta( $id, 'farazsms_affiliate_phone' )[0] ?? '';
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $id, 'digits_phone_no' )[0] ?? '';
		}
		if ( $mobile == '' ) {
			$mobile = get_user_meta( $id, 'wupp_mobile' )[0] ?? '';
		}
		if ( empty( $mobile ) && ! empty( self::$aff_user_mobile_field ) ) {
			$mobile = get_user_meta( $id, self::$aff_user_mobile_field, true );
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
			if ( ! empty( self::$aff_admin_user_on_approval_pattern ) && ! empty( Farazsms_Base::$admin_number ) ) {
				$this->affs_send_sms( Farazsms_Base::$admin_number, self::$aff_admin_user_on_approval_pattern, $data );
			}
		}
	}

	/**
     * Yith WooCommerce Affiliate add mobile field to registration form.
     *
	 * @return void
	 */
	public function fsms_yith_wcaf_add_mobile_field()
	{
		$farazsms_affiliate_phone = !empty($_POST['farazsms_affiliate_phone']) ? $_POST['farazsms_affiliate_phone'] : '';

		$fields = "
        <p class='form-row form-row-wide'>
            <label for='farazsms_affiliate_phone'>" . __('Phone:', 'farazsms') . " <span class='required'>*</span></label>
            <input type='text' class='input-text' name='farazsms_affiliate_phone' id='farazsms_affiliate_phone' value='$farazsms_affiliate_phone' required placeholder='" . __('Enter your phone number', 'farazsms') . "' />
        </p>
    ";
		echo $fields;
	}

	/**
	 * Yith WooCommerce Affiliate add mobile field on settings.
	 *
	 * @return void
	 */
	public function fsms_yith_wcaf_add_mobile_field_on_settings()
	{
		$user = get_current_user_id();

		if ($user) {
			$farazsms_affiliate_phone = get_user_meta($user, 'farazsms_affiliate_phone', true);

			$fields = "
            <p class='form-row form-row-wide'>
                <label for='farazsms_affiliate_phone'>" . __('Phone:', 'farazsms') . "</label>
                <input type='text' class='input-text' name='farazsms_affiliate_phone' id='farazsms_affiliate_phone' value='$farazsms_affiliate_phone' required placeholder='" . __('Enter your phone number', 'farazsms') . "' />
            </p>
        ";
			echo $fields;
		}
	}

	/**
     *  Yith WooCommerce Affiliate register mobile field.
     *
	 * @param $id
	 *
	 * @return void
	 */
	public function fsms_yith_wcaf_register_mobile_field($id)
	{
		$affiliate = YITH_WCAF_Affiliates()->get_affiliate_by_id($id);

		if (isset($_POST['farazsms_affiliate_phone'])) {
			update_user_meta($affiliate['user_id'], 'farazsms_affiliate_phone', sanitize_text_field($_POST['farazsms_affiliate_phone']));
		}
	}

	/**
     *  Yith WooCommerce Affiliate register mobile field on settings.
	 * @param $change
	 * @param $id
	 *
	 * @return void
	 */
	public function fsms_yith_wcaf_register_mobile_field_settings($change, $id)
	{
		if (isset($_POST['farazsms_affiliate_phone'])) {
			update_user_meta($id, 'farazsms_affiliate_phone', sanitize_text_field($_POST['farazsms_affiliate_phone']));
		}
	}

}

Farazsms_Aff::get_instance();
