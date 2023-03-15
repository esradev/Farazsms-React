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

		// Get phonebooks from your plugin
		$phonebooks = Farazsms_Ippanel::get_phonebooks()['data'];

		// Loop through phonebooks and add as select options
		foreach ( $phonebooks as $phonebook ) {
			$phonebook_options[ $phonebook['id'] ] = $phonebook['title'];
		}

		$this->add_control(
			'phonebook',
			[
				'label'   => __( 'Phonebook', 'farazsms' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $phonebook_options,
			]
		);

		$this->add_control(
			'send_verify_code', [
				'label'        => __( 'Send Verify Code?', 'farazsms' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'farazsms' ),
				'label_off'    => esc_html__( 'No', 'farazsms' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'verify_code_pattern', [
				'label' => __( 'Verify Code Pattern', 'farazsms' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'send_welcome_msg', [
				'label'        => __( 'Send Welcome Message?', 'farazsms' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'farazsms' ),
				'label_off'    => esc_html__( 'No', 'farazsms' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'welcome_msg_pattern', [
				'label' => __( 'Welcome Message Pattern', 'farazsms' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->end_controls_section();

		// Form Section
		$this->start_controls_section(
			'form_section',
			[
				__( 'Form', 'farazsms' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'form_title',
			[
				'label' => __( 'Form Title', 'farazsms' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->end_controls_section();

		// Name Field Section
		$this->start_controls_section(
			'name_field_section',
			[
				'label' => __( 'Name Field', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Name Field Controls
		$this->add_control(
			'name_placeholder',
			[
				'label'   => __( 'Name Field Placeholder', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Like: Ghafary', 'farazsms' ),
			]
		);

		$this->add_control(
			'name_filed_color',
			[
				'label'     => __( 'Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .name-field' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_filed_text_color',
			[
				'label'     => __( 'Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .name-field' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_filed_margin',
			[
				'label'      => __( 'Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .name-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_filed_padding',
			[
				'label'      => __( 'Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .name-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Phone Field Section
		$this->start_controls_section(
			'phone_field_section',
			[
				'label' => __( 'Phone Field', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Phone Field Controls
		$this->add_control(
			'phone_placeholder',
			[
				'label'   => __( 'Phone Field Placeholder', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Like: 09300410381', 'farazsms' ),
			]
		);

		$this->add_control(
			'phone_filed_color',
			[
				'label'     => __( 'Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .phone-field' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'phone_filed_text_color',
			[
				'label'     => __( 'Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .phone-field' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'phone_filed_margin',
			[
				'label'      => __( 'Margin', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .phone-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'phone_filed_padding',
			[
				'label'      => __( 'Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .phone-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Submit Button Section
		$this->start_controls_section(
			'submit_button_section',
			[
				'label' => __( 'Submit Button', 'farazsms' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Submit Button Controls
		$this->add_control(
			'submit_button_text',
			[
				'label'   => __( 'Button Text', 'farazsms' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Submit', 'farazsms' ),
			]
		);

		$this->add_control(
			'submit_button_color',
			[
				'label'     => __( 'Button Background Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submit_button_text_color',
			[
				'label'     => __( 'Button Text Color', 'farazsms' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submit_button_margin',
			[
				'label'      => __( 'Margin', 'farazsms' ),
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
				'label'      => __( 'Padding', 'farazsms' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .submit-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            <h3><?php echo esc_attr( $settings['form_title'] ); ?></h3>
            <form id="fsms_newsletter_form">
                <input id="fsms_newsletter_phonebook_id" type="hidden" name="phonebook_id"
                       value="<?php echo esc_attr( $settings['phonebook'] ); ?>">
                <input id="fsms_newsletter_send_verify_code" type="hidden"
                       value="<?php echo $settings['send_verify_code'] ?>">
                <input id="fsms_newsletter_verify_code_pattern" type="hidden" name="verify_code_pattern"
                       value="<?php echo esc_attr( $settings['verify_code_pattern'] ); ?>">
                <input id="fsms_newsletter_send_welcome_msg" type="hidden" name="send_welcome_msg"
                       value="<?php echo esc_attr( $settings['send_welcome_msg'] ); ?>">
                <input id="fsms_newsletter_welcome_msg_pattern" type="hidden" name="welcome_msg_pattern"
                       value="<?php echo esc_attr( $settings['welcome_msg_pattern'] ); ?>">

                <div class="fsms_newsletter_input a">
                    <input id="fsms_newsletter_name" type="text" class="fsms_newsletter_text"
                                                                     placeholder="<?php echo esc_attr( $settings['name_placeholder'] ); ?>"
                                                                     style="color:<?php echo $settings['name_filed_text_color']; ?>;background-color:<?php echo $settings['name_filed_color']; ?>;margin:<?php echo $settings['name_filed_margin']; ?>;padding:<?php echo $settings['name_filed_padding']; ?>;">
                </div>
                <div class="fsms_newsletter_input a">
                    <input id="fsms_newsletter_mobile" type="text" class="fsms_newsletter_text"
                           placeholder="<?php echo esc_attr( $settings['phone_placeholder'] ); ?>"
                           style="color:<?php echo $settings['phone_filed_text_color']; ?>;background-color:<?php echo $settings['phone_filed_color']; ?>;margin:<?php echo $settings['phone_filed_margin']; ?>;padding:<?php echo $settings['phone_filed_padding']; ?>;">
                </div>
                <div class="fsms_newsletter_input b" style="display: none;">
                    <input id="fsms_newsletter_verify_code" type="text" class="fsms_newsletter_text"
                           placeholder="کد تایید">
                </div>

            </form>
            <div id="fsms_newsletter_message" style="display: none;">
            </div>
            <div class="fsms_newsletter_submit">
                <button id="fsms_newsletter_submit_button" class="fsms_newsletter_button submit-button"
                        style="color:<?php echo $settings['submit_button_text_color']; ?>;background-color:<?php echo $settings['submit_button_color']; ?>;margin:<?php echo $settings['submit_button_margin']; ?>;padding:<?php echo $settings['submit_button_padding']; ?>;">
                    <span class="button__text"><?php echo esc_attr( $settings['submit_button_text'] ); ?></span>
                </button>
            </div>
            <div id="fsms_newsletter_completion" class="fsms_newsletter_submit" style="display: none;">
                <button id="fsms_newsletter_submit_code" class="fsms_newsletter_button"><span class="button__text">ارسال کد</span>
                </button>
                <button id="fsms_newsletter_resend_code" class="fsms_newsletter_button"><span class="button__text">ارسال مجدد کد</span>
                </button>
            </div>
        </div>

        <script>
          jQuery(document).ready(function ($) {
            'use strict'
            let newsletter_send_ver_code = $('#newsletter_send_ver_code')
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
                  if (newsletter_send_ver_code.val() === false) {
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