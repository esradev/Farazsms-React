<?php

/**
 * Farazsms gravity forms.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Gravity_Forms.
 */
class Farazsms_Gravity_Forms {
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
		add_action( 'gform_after_submission', [ $this, 'process_submissions' ], 10, 2 );
	}

	/**
	 * Process Submissions
	 *
	 * This function is called when a Gravity Forms submission is made.
	 * It retrieves the relevant rows from the database table and performs
	 * an action on each row, such as sending an email or updating a custom table.
	 *
	 * @param array $entry The current form submission's entry.
	 * @param array $form The current form's metadata.
	 */
	public function process_submissions( $entry, $form ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_gravity_forms';

		// Retrieve rows from the database table
		$results = $wpdb->get_results(
			"SELECT * FROM $table_name"
		);

		// Loop through the results and perform an action on each row
		foreach ( $results as $row ) {
			$form_id  = $row->form_id;
			$phone_field_id = $row->field_id;
			$name_field_id = $row->name_field_id;
			$content_field_id = $row->content_field_id;
			$phonebook_id   = $row->phonebook_id;
			$action = $row->action_type;



			if ( $form['id'] == $form_id ) {
				$phone_number = rgar( $entry, $phone_field_id );
				$name = rgar($entry, $name_field_id);
				$content = rgar($entry, $content_field_id);
				// Switch statement to handle different action types
				switch ( $action ) {
					case 'saveToPhonebook':
						$list[0] = (object) [
							'number'       => $phone_number,
							'name'         => $name ?? '',
							'phonebook_id' => (int) $phonebook_id
						];
						Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
						break;
					case 'sendSmsToUser':
						$pattern = $row->user_pattern_code;
						$input_data = [];
						$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
						if ( $patternMessage === null ) {
							return;
						}
						if ( str_contains( $patternMessage, '%name%' ) ) {
							$input_data['name'] = $name ?? '';
						}
						if ( str_contains( $patternMessage, '%content%' ) ) {
							$input_data['content'] = $content ?? '';
						}

						Farazsms_Ippanel::send_pattern( $pattern, $phone_number, $input_data );
						break;
					case 'sendSmsToAdmin':
						$pattern = $row->admin_pattern_code;
						$input_data = [];
						$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
						if ( $patternMessage === null ) {
							return;
						}
						if ( str_contains( $patternMessage, '%name%' ) ) {
							$input_data['name'] = $name ?? '';
						}
						if ( str_contains( $patternMessage, '%content%' ) ) {
							$input_data['content'] = $content ?? '';
						}
						Farazsms_Ippanel::send_pattern( $pattern, Farazsms_Base::$admin_number, $input_data );
						break;
				}
			}
		}
	}
}

Farazsms_Gravity_Forms::get_instance();
