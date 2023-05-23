<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Farazsms Newsletter Widget.
 *
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 2.0.0
 */
class Farazsms_Newsletter_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve list widget name.
	 *
	 * @return string Widget name.
	 * @since 2.0.0
	 * @access public
	 */
	public function get_name() {
		return 'farazsms-news';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve list widget title.
	 *
	 * @return string Widget title.
	 * @since 2.0.0
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'Farazsms Newsletter', 'farazsms' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve list widget icon.
	 *
	 * @return string Widget icon.
	 * @since 2.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @return string Widget help URL.
	 * @since 2.0.0
	 * @access public
	 */
	public function get_custom_help_url() {
		return 'https://developers.farazsms.com/docs/widgets/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the list widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since 2.0.0
	 * @access public
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the list widget belongs to.
	 *
	 * @return array Widget keywords.
	 * @since 2.0.0
	 * @access public
	 */
	public function get_keywords() {
		return [ 'farazsms', 'form', 'newsletter' ];
	}

	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Farazsms Newsletter', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$phonebook_options = [];

		$phonebooks = Farazsms_Ippanel::get_phonebooks();

		if ( is_array( $phonebooks ) ) {
			foreach ( $phonebooks as $phonebook ) {
				$phonebook_options[ $phonebook['id'] ] = $phonebook['title'];
			}
		}

		$this->add_control(
			'phonebook',
			[
				'label'   => esc_html__( 'Phonebook', 'farazsms' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $phonebook_options,
			]
		);

		$this->add_control(
			'send_verify_code', [
				'label'        => esc_html__( 'Send Verify Code?', 'farazsms' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'farazsms' ),
				'label_off'    => esc_html__( 'No', 'farazsms' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'verify_code_pattern', [
				'label' => esc_html__( 'Verify Code Pattern', 'farazsms' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'send_welcome_msg', [
				'label'        => esc_html__( 'Send Welcome Message?', 'farazsms' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'farazsms' ),
				'label_off'    => esc_html__( 'No', 'farazsms' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'welcome_msg_pattern', [
				'label' => esc_html__( 'Welcome Message Pattern', 'farazsms' ),
				'type'  => Controls_Manager::TEXT,
			]
		);
		$this->end_controls_section();

		// Add Style Controls For #fsms_newsletter
		$this->start_controls_section(
			'fsms_newsletter_section',
			[
				'label' => esc_html__( 'Farazsms Newsletter Form', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'form_title',
			[
				'label' => esc_html__( 'Form Title', 'farazsms' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'name_placeholder',
			[
				'label'   => esc_html__( 'Name Field Placeholder', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Like: Ghafary', 'farazsms' ),
			]
		);

		$this->add_control(
			'phone_placeholder',
			[
				'label'   => esc_html__( 'Phone Field Placeholder', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Like: 09300410381', 'farazsms' ),
			]
		);

		$this->add_control(
			'fsms_newsletter_background_color',
			[
				'label'     => esc_html__( 'Form Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #fsms_newsletter' => 'background-color: {{VALUE}};',
				],
				'default'   => '#f7f7f7',
			]
		);

		$this->add_control(
			'fsms_newsletter_border_radius',
			[
				'label'      => esc_html__( 'Form Border Radius', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #fsms_newsletter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'    => '5',
					'right'  => '5',
					'bottom' => '5',
					'left'   => '5',
					'unit'   => 'px',
				],
			]
		);

		$this->add_control(
			'fsms_newsletter_form_alignment',
			[
				'label'     => esc_html__( 'Form Alignment', 'farazsms' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'farazsms' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'farazsms' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'farazsms' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} #fsms_newsletter' => 'text-align: {{VALUE}};',
				],
				'default'   => 'center',
			]
		);

		$this->add_control(
			'fsms_newsletter_form_margin',
			[
				'label'      => esc_html__( 'Form Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #fsms_newsletter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'    => '5',
					'right'  => '30',
					'bottom' => '5',
					'left'   => '30',
					'unit'   => '%',
				],
			]
		);
		$this->add_control(
			'fsms_newsletter_form_padding',
			[
				'label'      => esc_html__( 'Form Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #fsms_newsletter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'    => 10,
					'right'  => 10,
					'bottom' => 10,
					'left'   => 10,
					'unit'   => 'px',
				],
			]
		);

		$this->add_responsive_control(
			'fsms_newsletter_input_width',
			[
				'label'      => esc_html__( 'Input Width', 'farazsms' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors'  => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'fsms_newsletter_input_margin',
			[
				'label'      => esc_html__( 'Input Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'    => '5',
					'right'  => '5',
					'bottom' => '0',
					'left'   => '5',
					'unit'   => 'px',
				],
			]
		);
		$this->add_control(
			'fsms_newsletter_input_padding',
			[
				'label'      => esc_html__( 'Input Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'    => 10,
					'right'  => 10,
					'bottom' => 10,
					'left'   => 10,
					'unit'   => 'px',
				],
			]
		);
		$this->add_control(
			'fsms_newsletter_input_direction',
			[
				'label'     => esc_html__( 'Input Text Direction', 'farazsms' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'rtl' => esc_html__( 'Right to Left', 'farazsms' ),
					'ltr' => esc_html__( 'Left to Right', 'farazsms' ),
				],
				'default'   => 'rtl',
				'selectors' => [
					'{{WRAPPER}} .fsms_newsletter_input' => 'direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'fsms_newsletter_text_border_radius',
			[
				'label'      => esc_html__( 'Text Border Radius', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'    => [
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
					'unit'   => 'px',
				],
			]
		);

		$this->add_control(
			'fsms_newsletter_text_background_color',
			[
				'label'     => esc_html__( 'Text Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'background-color: {{VALUE}};',
				],
				'default'   => '#ffffff',
			]
		);

		$this->add_control(
			'fsms_newsletter_text_font_size',
			[
				'label'      => esc_html__( 'Text Font Size', 'farazsms' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 30,
					],
					'em' => [
						'min' => 1,
						'max' => 3,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'fsms_newsletter_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .fsms_newsletter_text' => 'color: {{VALUE}};',
				],
				'default'   => '#000000',
			],
		);


		$this->end_controls_section();

		// Submit Button Section
		$this->start_controls_section(
			'submit_button_section',
			[
				'label' => esc_html__( 'Submit Button', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Submit Button Controls
		$this->add_control(
			'submit_button_text',
			[
				'label'   => esc_html__( 'Button Text', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Submit', 'farazsms' ),
			]
		);

		$this->add_control(
			'submit_button_color',
			[
				'label'     => esc_html__( 'Button Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit-button' => 'background-color: {{VALUE}};',
				],
				'default'   => '#0002cb',
			]
		);

		$this->add_control(
			'submit_button_text_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit-button' => 'color: {{VALUE}};',
				],
				'default'   => '#000000',
			]
		);

		$this->add_control(
			'submit_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .submit-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'submit_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .submit-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		// Submit Button Section
		$this->start_controls_section(
			'submit_code_button_section',
			[
				'label' => esc_html__( 'Submit Code Button', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		// Submit Code Button Controls
		$this->add_control(
			'submit_code_button_text',
			[
				'label'   => esc_html__( 'Button Text', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Submit Code', 'farazsms' ),
			]
		);

		$this->add_control(
			'submit_code_button_color',
			[
				'label'     => esc_html__( 'Button Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit-code-button' => 'background-color: {{VALUE}};',
				],
				'default'   => '#0002cb',
			]
		);

		$this->add_control(
			'submit_code_button_text_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit-code-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submit_code_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .submit-code-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'submit_code_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .submit-code-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Resent Button Section
		$this->start_controls_section(
			'resent_code_button_section',
			[
				'label' => esc_html__( 'Resent Code Button', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		// Resent Code Button Controls
		$this->add_control(
			'resent_code_button_text',
			[
				'label'   => esc_html__( 'Button Text', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Resent Code', 'farazsms' ),
			]
		);

		$this->add_control(
			'resent_code_button_color',
			[
				'label'     => esc_html__( 'Button Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .resent-code-button' => 'background-color: {{VALUE}};',
				],
				'default'   => '#0002cb',
			]
		);

		$this->add_control(
			'resent_code_button_text_color',
			[
				'label'     => esc_html__( 'Button Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .resent-code-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'resent_code_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .resent-code-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'resent_code_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .resent-code-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		wp_enqueue_script( 'elementor-frontend' );
		$settings = $this->get_settings_for_display();
		?>
        <div id="fsms_newsletter">
            <h3><?php echo esc_html( $settings['form_title'] ); ?></h3>
            <form id="fsms_newsletter_form">
                <input id="fsms_newsletter_phonebook_id" type="hidden" name="phonebook_id"
                       value="<?php echo esc_attr( $settings['phonebook'] ); ?>">
                <input id="fsms_newsletter_send_verify_code" type="hidden" name="send_verify_code"
                       value="<?php echo esc_attr($settings['send_verify_code']); ?>">
                <input id="fsms_newsletter_verify_code_pattern" type="hidden" name="verify_code_pattern"
                       value="<?php echo esc_attr( $settings['verify_code_pattern'] ); ?>">
                <input id="fsms_newsletter_send_welcome_msg" type="hidden" name="send_welcome_msg"
                       value="<?php echo esc_attr( $settings['send_welcome_msg'] ); ?>">
                <input id="fsms_newsletter_welcome_msg_pattern" type="hidden" name="welcome_msg_pattern"
                       value="<?php echo esc_attr( $settings['welcome_msg_pattern'] ); ?>">

                <div class="fsms_newsletter_input a">
                    <input id="fsms_newsletter_name" type="text" class="fsms_newsletter_text"
                           placeholder="<?php echo esc_attr( $settings['name_placeholder'] ); ?>">
                </div>
                <div class="fsms_newsletter_input a">
                    <input id="fsms_newsletter_mobile" type="text" class="fsms_newsletter_text"
                           placeholder="<?php echo esc_attr( $settings['phone_placeholder'] ); ?>">
                </div>
                <div class="fsms_newsletter_input b" style="display: none;">
                    <input id="fsms_newsletter_verify_code" type="text" class="fsms_newsletter_text"
                           placeholder="<?php echo esc_attr__( 'Verification code', 'farazsms' ); ?>">
                </div>

            </form>
            <div id="fsms_newsletter_message" style="display: none;">
            </div>
            <div class="fsms_newsletter_submit">
                <button id="fsms_newsletter_submit_button" class="submit-button"
                        style="color:<?php echo esc_attr($settings['submit_button_text_color'] ); ?>;background-color:<?php echo esc_attr($settings['submit_button_color']); ?>;margin:<?php echo esc_attr($settings['submit_button_margin']); ?>;padding:<?php echo esc_attr($settings['submit_button_padding']); ?>;">
                    <span><?php echo esc_html( $settings['submit_button_text'] ); ?></span>
                </button>
            </div>
            <div id="fsms_newsletter_completion" class="fsms_newsletter_submit" style="display: none;">
                <button id="fsms_newsletter_submit_code" class="submit-code-button"
                        style="color:<?php echo esc_attr($settings['submit_code_button_text_color']); ?>;background-color:<?php echo esc_attr($settings['submit_code_button_color']); ?>;margin:<?php echo esc_attr($settings['submit_code_button_margin']); ?>;padding:<?php echo esc_attr($settings['submit_code_button_padding']); ?>;">
                    <span><?php echo esc_html( $settings['submit_code_button_text'] ); ?></span>
                </button>
                <button id="fsms_newsletter_resend_code" class="resent-code-button"
                        style="color:<?php echo esc_attr($settings['resent_code_button_text_color']); ?>;background-color:<?php echo esc_attr($settings['resent_code_button_color']); ?>;margin:<?php echo esc_attr($settings['resent_code_button_margin']); ?>;padding:<?php echo esc_attr($settings['resent_code_button_padding']); ?>;">
                    <span><?php echo esc_html( $settings['resent_code_button_text'] ); ?></span>
                </button>
            </div>
        </div>

        <script>
          jQuery(document).ready(function ($) {
            'use strict'
            let submit_div = $('.fsms_newsletter_submit')
            let submit_button = $('#fsms_newsletter_submit_button')
            let submit_code = $('#fsms_newsletter_submit_code')
            let resend_code = $('#fsms_newsletter_resend_code')
            let newsletter_completion_div = $('#fsms_newsletter_completion')
            let name = $('#fsms_newsletter_name')
            let mobile = $('#fsms_newsletter_mobile')
            let phonebook_id = $('#fsms_newsletter_phonebook_id')
            let send_verify_code = $('#fsms_newsletter_send_verify_code')
            let verify_code_pattern = $('#fsms_newsletter_verify_code_pattern')
            let send_welcome_msg = $('#fsms_newsletter_send_welcome_msg')
            let welcome_msg_pattern = $('#fsms_newsletter_welcome_msg_pattern')
            let verify_code = $('#fsms_newsletter_verify_code')
            let newsletter_message = $('#fsms_newsletter_message')

            let has_error = false
            submit_button.click(function () {
              has_error = false
              name.removeClass('error')
              mobile.removeClass('error')
              if (name.val() === '') {
                has_error = true
                name.addClass('error')
              }
              if (mobile.val().length < 10) {
                has_error = true
                mobile.addClass('error')
              }
              if (has_error) {
                return
              }
              let data = {
                action: 'fsms_newsletter_send_verification_code',
                mobile: mobile.val(),
                name: name.val(),
                phonebook_id: phonebook_id.val(),
                send_verify_code: send_verify_code.val(),
                verify_code_pattern: verify_code_pattern.val(),
                send_welcome_msg: send_welcome_msg.val(),
                welcome_msg_pattern: welcome_msg_pattern.val(),
              }
              submit_button.addClass('fsms_button--loading')
              submit_button.prop('disabled', true)
              $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function (response) {
                submit_button.removeClass('fsms_button--loading')
                if (response.success) {
                  if (send_verify_code.val() !== 'yes') {
                    newsletter_message.removeClass('success error')
                    newsletter_message.hide()
                    newsletter_message.empty()
                    newsletter_message.addClass('success')
                    newsletter_message.append('ثبت نام با موفقیت انجام شد')
                    newsletter_message.show()
                  } else {
                    submit_div.hide()
                    $('.fsms_newsletter_input.a').hide()
                    newsletter_completion_div.show()
                    $('.fsms_newsletter_input.b').show()
                    let seconds = 90
                    let interval
                    resend_code.prop('disabled', true)
                    interval = setInterval(function () {
                      resend_code
                        .find('span')
                        .html('ارسال مجدد کد' + ' (' + seconds + ')')
                      if (seconds === 0) {
                        resend_code.find('span').html('ارسال مجدد کد')
                        resend_code.prop('disabled', false)
                        clearInterval(interval)
                      }
                      seconds--
                    }, 1000)
                  }
                } else {
                  newsletter_message.addClass('error')
                  newsletter_message.append('شما عضو خبرنامه هستید')
                  newsletter_message.show()
                }
              })
            })

            resend_code.click(function () {
              submit_button.click()
            })

            submit_code.click(function () {
              has_error = false
              verify_code.removeClass('error')
              if (verify_code.val() === '' || verify_code.val().length !== 4) {
                has_error = true
                verify_code.addClass('error')
              }
              if (has_error) {
                return
              }
              let data = {
                action: 'fsms_add_phone_to_newsletter',
                code: verify_code.val(),
                name: name.val(),
                mobile: mobile.val(),
                phonebook_id: phonebook_id.val(),
                welcome_msg_pattern: welcome_msg_pattern.val(),
              }
              submit_code.addClass('fsms_button--loading')
              submit_code.prop('disabled', true)
              $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function (response) {
                submit_code.removeClass('fsms_button--loading')
                submit_code.prop('disabled', false)
                newsletter_message.removeClass('success error')
                newsletter_message.hide()
                newsletter_message.empty()
                if (response.success) {
                  newsletter_message.addClass('success')
                  newsletter_message.append('ثبت نام با موفقیت انجام شد')
                  newsletter_message.show()
                  newsletter_completion_div.hide()
                } else {
                  newsletter_message.addClass('error')
                  newsletter_message.append('کد وارد شده صحیح نیست')
                  newsletter_message.show()
                }
              })
            })
          })
        </script>
		<?php

	}
}