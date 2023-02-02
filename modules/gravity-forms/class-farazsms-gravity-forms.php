<?php

/**
 * Farazsms gravity forms.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}
/**
 * Class Farazsms_Gravity_Forms.
 */
class Farazsms_Gravity_Forms
{
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
		add_action( 'gform_entry_created', [ $this, 'fsms_club_gform_post_update_entry' ] );
		add_action( 'gform_pre_submission', [ $this, 'fsms_gf_pre_submission' ] );
	}

	/**
	 * Gravity Form post update entry.
	 */
	public function fsms_club_gform_post_update_entry( $entry ) {
		$fsc_gravity_forms_fields = self::$gf_selected_field;
		$form_ids                 = [];
		$field_ids                = [];
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
			$phone = Farazsms_Base::validate_mobile_number( $value );
			if ( ! $phone ) {
				return;
			}
			if ( $value !== null ) {
				$woo_gf_books = Farazsms_Base::$gf_phonebook;
				foreach ( $woo_gf_books as $phonebookId ) {
					$data[] = [
						'number'       => $phone,
						'name'         => '',
						'phonebook_id' => (int) $phonebookId['value']
					];
					Farazsms_Base::save_to_phonebookv4( $data );
				}
			}
		}
	}

	/**
	 * Gravity form pre submission.
	 */
	public function fsms_gf_pre_submission( $form ) {
		foreach ( $_POST as $name => $value ) {
			$_POST[ $name ] = Farazsms_Base::fsms_tr_num( $value );
		}
	}


}
Farazsms_Gravity_Forms::get_instance();
