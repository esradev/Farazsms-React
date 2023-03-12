<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Form_Record;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor form Farazsms action.
 *
 * Custom Elementor form action which adds new subscriber to Farazsms phonebook after form submission.
 *
 * @since 2.0.0
 */
class Farazsms_Newsletter_Action_After_Submit extends Action_Base {

	/**
	 * Get action name.
	 *
	 * Retrieve Farazsms action name.
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 */
	public function get_name() {
		return 'farazsms-newsletter-action';
	}

	/**
	 * Get action label.
	 *
	 * Retrieve Farazsms action label.
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 */
	public function get_label() {
		return esc_html__( 'Farazsms Newsletter', 'farazsms' );
	}

	/**
	 * Register action controls.
	 *
	 * Add input fields to allow the user to customize the action settings.
	 *
	 * @param Widget_Base $widget
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function register_settings_section( $widget ) {

		$widget->start_controls_section(
			'section_farazsms',
			[
				'label'     => esc_html__( 'Farazsms Newsletter', 'farazsms' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control( 'sms_sender_number', [
			'label'       => esc_html__( 'Sender number', 'farazsms' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'separator'   => 'after',
		] );

		$widget->add_control( 'sms_to_visitor', [
			'label'        => esc_html__( 'Send sms to the visitor', 'farazsms' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'Enable', 'farazsms' ),
			'label_off'    => esc_html__( 'Disable', 'farazsms' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => esc_html__(
				'After completing the form and pressing the send button, an SMS will be sent to the visitor.',
				'farazsms'
			),
		] );

		$widget->add_control( 'sms_recipient', [
			'label'       => esc_html__(
				'SMS recipient number (field shortcode)',
				'farazsms'
			),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => esc_html__( 'Example: [field id="mobile"]', 'farazsms' ),
			'label_block' => true,
			'condition'   => [
				'sms_to_visitor' => 'yes',
			],
			'description' => esc_html__(
				'Find the field id in each of form fields in the advanced tab.',
				'farazsms'
			),
		] );

		// send settings
		$widget->add_control( 'sms_send_type', [
			'label'     => esc_html__( 'How to send?', 'farazsms' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'none',
			'condition' => [
				'sms_to_visitor' => 'yes',
			],
			'options'   => [
				'webservice' => esc_html__( 'Web Service', 'farazsms' ),
				'pattern'    => esc_html__( 'Pattern', 'farazsms' ),
				'none'       => esc_html__( 'None', 'farazsms' ),
			],
		] );

		$widget->add_control( 'sms_pattern_code', [
			'label'       => esc_html__( 'Pattern code', 'farazsms' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => esc_html__( 'Enter your pattern code', 'farazsms' ),
			'label_block' => true,
			'condition'   => [
				'sms_to_visitor' => 'yes',
				'sms_send_type'  => 'pattern'
			],
		] );

		$widget->add_control( 'sms_content', [
			'label'       => esc_html__( 'Sms content', 'farazsms' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 2,
			'placeholder' => esc_html__( 'Enter SMS text or pattern variables', 'farazsms' ),
			'condition'   => [
				'sms_to_visitor' => 'yes',
			],
			'description' => esc_html__( "If you choose the web service sending method, enter the text of your SMS. If you use a pattern, enter a variable along with its value with: in each line. The value can be a constant or a shortcode of the fields. For example<br> PersonName:[field id='name'] companyName:Farazsms'", 'farazsms' ),
			'separator'   => 'after',
		] );

		$widget->add_control( 'sms_add_to_phonebook', [
			'label'        => esc_html__( 'Save in the system phonebook', 'farazsms' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'Enable', 'farazsms' ),
			'label_off'    => esc_html__( 'Disable', 'farazsms' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => [
				'sms_to_visitor' => 'yes',
			],
			'description'  => esc_html__( 'The number entered by the visitor in the form will be saved in the phonebook of Farazsms system.', 'farazsms' ),
		] );

		$phonebook_options = [];

		// Get phonebooks from your plugin
		$phonebooks = Farazsms_Ippanel::get_phonebooks()['data'];

		// Loop through phonebooks and add as select options
		foreach ( $phonebooks as $phonebook ) {
			$phonebook_options[ $phonebook['id'] ] = $phonebook['title'];
		}

		$widget->add_control(
			'phonebook',
			[
				'label'   => 'Phonebook',
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $phonebook_options,
			]
		);
		//end send settings


		// send to admin
		$widget->add_control( 'sms_to_admin', [
			'label'        => esc_html__( 'Send sms to the admin', 'farazsms' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'Enable', 'farazsms' ),
			'label_off'    => esc_html__( 'Disable', 'farazsms' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => esc_html__(
				'If you want an SMS alert to be sent to the admin number as soon as the visitor completes the form, check this option.',
				'farazsms'
			),
			'separator'    => 'before',
		] );

		$widget->add_control( 'sms_admin_method', [
			'label'     => esc_html__( 'How to send?', 'farazsms' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'none',
			'condition' => [
				'sms_to_admin' => 'yes',
			],
			'options'   => [
				'webservice' => esc_html__( 'Web Service', 'farazsms' ),
				'pattern'    => esc_html__( 'Pattern', 'farazsms' ),
				'none'       => esc_html__( 'None', 'farazsms' ),
			],
		] );

		$widget->add_control( 'sms_admin_pattern', [
			'label'       => esc_html__( 'Pattern code', 'farazsms' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => esc_html__( 'Enter your pattern code', 'farazsms' ),
			'label_block' => true,
			'condition'   => [
				'sms_to_admin'     => 'yes',
				'sms_admin_method' => 'pattern',
			],
		] );

		$widget->add_control( 'sms_admin_content', [
			'label'       => esc_html__( 'Sms content', 'farazsms' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 2,
			'placeholder' => esc_html__( 'Enter SMS text or pattern variables', 'farazsms' ),
			'condition'   => [
				'sms_to_admin' => 'yes',
			],
			'description' => esc_html__( 'By completing the form, an SMS will be sent to the admin. The administrator number must be defined in the plugin settings.', 'farazsms' ),
			'separator'   => 'after',

		] );

		$widget->add_control( 'sms_to_other', [
			'label'       => esc_html__( 'Send sms copy to others', 'farazsms' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => esc_html__( 'Example: 09121234567,09351234567', 'farazsms' ),
			'label_block' => true,
			'condition'   => [
				'sms_to_admin' => 'yes',
			],
			'description' => esc_html__(
				'A copy of this SMS will be sent to these numbers, while sending to the administrator. (Separate with english commas.)',
				'farazsms'
			),
		] );
		//end send to admin

		$widget->end_controls_section();
	}

	/**
	 * Run action.
	 *
	 * Runs the Farazsms action after form submission.
	 *
	 * @param Form_Record $record
	 * @param Ajax_Handler $ajax_handler
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function run( $record, $ajax_handler ) {
		$settings   = $record->get( 'form_settings' );
		$raw_fields = $record->get( 'fields' );
		$fields     = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		$from         = $settings['sms_sender_number'];
		$to           = $record->replace_setting_shortcodes( $settings['sms_recipient'] );
		$farazpattern = $settings['sms_pattern_code'];
		$phonebook    = $settings['phonebook'];
		$content      = $settings['sms_content'];

		if ( $settings['sms_add_to_phonebook'] == 'yes' ) {
			$list[0] = (object) [
				'number'       => $to,
				'name'         => '',
				'phonebook_id' => (int) $phonebook
			];

			Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
		}

		// Send to visitor with pattern
		if ( $settings['sms_to_visitor'] == 'yes' && $settings['sms_send_type'] == 'pattern' ) {
			$massage = strip_tags( trim( $content ) );
			$massage = str_replace( "\r\n", ';', $massage );
			$massage = str_replace( "\n", ';', $massage );
			$splited = explode( ';', $massage );

			$input_data = [];
			foreach ( $splited as $parm ) {
				$splited_parm = explode( ':', $parm );
				if ( str_contains( trim( $splited_parm[1] ), '[field' ) ) {
					$input_data[ $splited_parm[0] ] = $record->replace_setting_shortcodes(
						trim( $splited_parm[1] )
					);
				} else {
					$input_data[ $splited_parm[0] ] = trim( $splited_parm[1] );
				}
			}

			Farazsms_Ippanel::send_pattern( $farazpattern, $to, $input_data );
		}

		// Send to visitor with webService
		if ( $settings['sms_to_visitor'] == 'yes' && $settings['sms_send_type'] == 'webservice' ) {
			if ( str_contains( trim( $content ), '[field' ) ) {
				$content_value = $record->replace_setting_shortcodes( $settings['sms_content'] );
				Farazsms_Ippanel::send_message( [ $to ], $content_value );
			} else {
				Farazsms_Ippanel::send_message( [ $to ], $content );
			}
		}

		// Send to Admin with pattern
		$admin_content = $settings['sms_admin_content'];
		$admin_pattern = $settings['sms_admin_pattern'];
		$other_numbers = $settings['sms_to_other'];

		$admins_numbers   = explode( ',', $other_numbers );
		$admins_numbers[] = Farazsms_Base::$admin_number;


		if ( $settings['sms_to_admin'] == 'yes' && $settings['sms_admin_method'] == 'pattern' ) {
			$adminContent = strip_tags( trim( $admin_content ) );
			$adminContent = str_replace( "\r\n", ';', $adminContent );
			$adminContent = str_replace( "\n", ';', $adminContent );
			$adminsplited = explode( ';', $adminContent );

			$admin_input_data = [];
			foreach ( $adminsplited as $adminparm ) {
				$adminsplited_parm = explode( ':', $adminparm );
				if ( str_contains( trim( $adminsplited_parm[1] ), '[field' ) ) {
					$admin_input_data[ $adminsplited_parm[0] ] = $record->replace_setting_shortcodes( trim( $adminsplited_parm[1] ) );
				} else {
					$admin_input_data[ $adminsplited_parm[0] ] = trim( $adminsplited_parm[1] );
				}
			}
			foreach ( $admins_numbers as $adminnum ) {
				Farazsms_Ippanel::send_pattern( $admin_pattern, $adminnum, $admin_input_data );
			}
		}

		// Send to Admin with webService
		if ( $settings['sms_to_admin'] == 'yes' && $settings['sms_admin_method'] == 'webservice' ) {
			if ( str_contains( trim( $admin_content ), '[field' ) ) {
				$admin_content_value = $record->replace_setting_shortcodes( $settings['sms_admin_content'] );
				Farazsms_Ippanel::send_message( $admins_numbers, $admin_content_value );
			} else {
				Farazsms_Ippanel::send_message( $admins_numbers, $admin_content );
			}
		}
	}

	public function on_export( $element ) {
	}
}
