<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Farazsms Newsletter Widget.
 *
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Farazsms_Newsletter_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve list widget name.
	 *
	 * @return string Widget name.
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
				'label'   => 'Phonebook',
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $phonebook_options,
			]
		);

		$this->add_control(
			'send_verify_code', [
				'label'        => 'Send Verify Code?',
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'textdomain' ),
				'label_off'    => esc_html__( 'Hide', 'textdomain' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'name',
			[
				'label' => __( 'Name', 'plugin-name' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'phone',
			[
				'label' => __( 'Phone', 'plugin-name' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
        <div id="fsms_newsletter">
            <form id="fsms_newsletter_form">
                <input type="hidden" name="phonebook_id" value="<?php echo esc_attr( $settings['phonebook'] ); ?>">
                <div class="fsms_newsletter_input a">
                    <input id="fsms_newsletter_name" type="text" class="fsms_newsletter_text" placeholder=" نام و نام
                           خانوادگی">
                </div>
                <div class="fsms_newsletter_input a">
                    <input id="fsms_newsletter_mobile" type="text" class="fsms_newsletter_text"
                           placeholder="شماره موبایل">
                </div>
                <div class="fsms_newsletter_input b" style="display: none;">
                    <input id="fsms_newsletter_verify_code" type="text" class="fsms_newsletter_text"
                           placeholder="کد تایید">
                </div>
                <input id="newsletter_send_ver_code" type="hidden"
                       value="<?php echo $settings['send_verify_code'] ?>">
            </form>
            <div id="fsms_newsletter_message" style="display: none;">
            </div>
            <div class="fsms_newsletter_submit">
                <button id="fsms_newsletter_submit_button" class="fsms_newsletter_button"><span class="button__text">اشتراک</span>
                </button>
            </div>
            <div id="fsms_newsletter_completion" class="fsms_newsletter_submit" style="display: none;">
                <button id="fsms_newsletter_submit_code" class="fsms_newsletter_button"><span class="button__text">ارسال کد</span>
                </button>
                <button id="fsms_newsletter_resend_code" class="fsms_newsletter_button"><span class="button__text">ارسال مجدد کد</span>
                </button>
            </div>
        </div>
        <!--<form action="#" method="post" id="farazsms-form">
            <label for="name">Name: <input type="text" name="name" id="name" required></label><br>
            <label for="phone">Phone: <input type="tel" name="phone" id="phone" required></label><br>
            <input type="hidden" name="phonebook_id" value="<?php /*echo esc_attr( $settings['phonebook'] ); */ ?>">
			<?php /*wp_nonce_field( 'farazsms_form_nonce', 'farazsms_form_nonce_field' ); */ ?>
            <button type="submit" id="submit-button">Submit</button>
        </form>

        <div id="form-response"></div>-->

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
            let verify_code = $('#fsms_newsletter_verify_code')
            let newsletter_message = $('#fsms_newsletter_message')

            let has_error = false
            submit_button.click(function () {
              has_error = false
              name.removeClass('error')
              mobile.removeClass('error')
              if (name.val() == '') {
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
              }
              submit_button.addClass('fsms_button--loading')
              submit_button.prop('disabled', true)
              $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function (response) {
                submit_button.removeClass('fsms_button--loading')
                if (response.success) {
                  if (newsletter_send_ver_code.val().length === 0) {
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
              if (verify_code.val() == '' || verify_code.val().length !== 4) {
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
          //jQuery(document).ready(function ($) {
          //  $('#farazsms-form').submit(function (event) {
          //    event.preventDefault()
          //
          //    // Disable submit button to prevent multiple submissions
          //    $('#submit-button').prop('disabled', true)
          //
          //    // Get form data
          //    let form_data = {
          //      'name': $('#name').val(),
          //      'phone': $('#phone').val(),
          //      'phonebook': $('#phonebook').val(),
          //      'action': 'farazsms_process_form',
          //      'security': '<?php //echo wp_create_nonce( 'farazsms_form_nonce' ); ?>//'
          //    }
          //
          //    // Submit form data via AJAX
          //    $.ajax({
          //      type: 'POST',
          //      url: '<?php //echo admin_url( 'admin-ajax.php' ); ?>//',
          //      data: form_data,
          //      dataType: 'json',
          //      success: function (response) {
          //        if (response.success) {
          //          $('#form-response').html(response)
          //        } else {
          //          $('#form-response').html('<div class="error">' + response + '</div>')
          //        }
          //      },
          //      error: function (jqXHR, textStatus, errorThrown) {
          //        $('#form-response').html('<div class="error">There was an error submitting the form.</div>')
          //      },
          //      complete: function () {
          //        // Re-enable submit button
          //        $('#submit-button').prop('disabled', false)
          //      }
          //    })
          //  })
          //})
        </script>
		<?php

	}
}