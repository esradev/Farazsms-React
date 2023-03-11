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

		// Retrieve rows from the database table where action is "saveToPhonebook"
		$results = $wpdb->get_results(
			"SELECT * FROM $table_name WHERE action_type = 'saveToPhonebook'"
		);

		// Loop through the results and perform an action on each row
		foreach ( $results as $row ) {
			$phone_form_id  = $row->form_id;
			$phone_field_id = $row->field_id;
			$phonebook_id   = $row->phonebook_id;

			if ( $form['id'] == $phone_form_id ) {
				$phone_number = rgar( $entry, $phone_field_id );

				if ( $row->action_type === 'saveToPhonebook' ) {// Check if the form ID matches the current form
					// Get the phone number entered by the user

					$list[0] = (object) [
						'number'       => $phone_number,
						'name'         => '',
						'phonebook_id' => (int) $phonebook_id
					];
					Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
				}
			}
		}
	}
}

Farazsms_Gravity_Forms::get_instance();
