<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor form Farazsms action.
 *
 * Custom Elementor form action which adds new subscriber to Farazsms phonebook after form submission.
 *
 * @since 1.0.0
 */
class Farazsms_Newsletter_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base
{

    /**
     * Get action name.
     *
     * Retrieve Farazsms action name.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'farazsms-newsletter-action';
    }

    /**
     * Get action label.
     *
     * Retrieve Farazsms action label.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Farazsms Newsletter', 'farazsms');
    }

    /**
     * Register action controls.
     *
     * Add input fields to allow the user to customize the action settings.
     *
     * @since 1.0.0
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {

        $widget->start_controls_section(
            'section_farazsms',
            [
                'label' => esc_html__('Farazsms Newsletter', 'farazsms'),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control("sms_sender_number", [
            "label" => esc_html__("Sender number", "farazsms"),
            "type" => \Elementor\Controls_Manager::TEXT,
            "label_block" => true,
            "separator" => "after",
        ]);

        $widget->add_control("sms_to_visitor", [
            "label" => esc_html__("Send sms to the visitor", "farazsms"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => esc_html__("Enable", "farazsms"),
            "label_off" => esc_html__("Disable", "farazsms"),
            "return_value" => "yes",
            "default" => "yes",
            "description" => esc_html__(
                "After completing the form and pressing the send button, an SMS will be sent to the visitor.",
                "farazsms"
            ),
        ]);

        $widget->add_control("sms_recipient", [
            "label" => esc_html__(
                "SMS recipient number (field shortcode)",
                "farazsms"
            ),
            "type" => \Elementor\Controls_Manager::TEXT,
            "placeholder" => 'Example: [field id="mobile"]',
            "label_block" => true,
            "condition" => [
                "sms_to_visitor" => "yes",
            ],
            "description" => esc_html__(
                "Find the firld id in each of form fields in the advanced tab.",
                "farazsms"
            ),
        ]);

        // send settings
        $widget->add_control("sms_send_type", [
            "label" => esc_html__("How to send", "farazsms"),
            "type" => \Elementor\Controls_Manager::SELECT,
            "default" => "none",
            "condition" => [
                "sms_to_visitor" => "yes",
            ],
            "options" => [
                "webservice" => esc_html__("Web Service", "farazsms"),
                "pattern" => esc_html__("Pattern", "farazsms"),
                "none" => esc_html__("None", "farazsms"),
            ],
        ]);

        $widget->add_control("sms_pattern_code", [
            "label" => esc_html__("Pattern code", "farazsms"),
            "type" => \Elementor\Controls_Manager::TEXT,
            "placeholder" => esc_html__("Enter your pattern code", "farazsms"),
            "label_block" => true,
            "condition" => [
                "sms_to_visitor" => "yes",
                "sms_send_type" => "pattern"
            ],
        ]);

        $widget->add_control("sms_content", [
            "label" => esc_html__("Sms content", "farazsms"),
            "type" => \Elementor\Controls_Manager::TEXTAREA,
            "rows" => 2,
            "placeholder" => esc_html__("Enter SMS text or pattern variables", "farazsms"),
            "condition" => [
                "sms_to_visitor" => "yes",
            ],
            "description" => esc_html__("If you choose the web service sending method, enter the text of your SMS. If you use a pattern, enter a variable along with its value with: in each line. The value can be a constant or a shortcode of any of the fields. For example<br> PersonName:[field id='name'] companyName:Farazsms'", "farazsms"),
            "separator" => "after",
        ]);

        $widget->add_control("sms_add_to_phonebook", [
            "label" => esc_html__("Save in the system phonebook", "farazsms"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => esc_html__("Enable", "your-plugin"),
            "label_off" => esc_html__("Disable", "your-plugin"),
            "return_value" => "yes",
            "default" => "yes",
            "condition" => [
                "sms_to_visitor" => "yes",
            ],
            'description' => esc_html__("The number entered by the visitor in the form will be saved in the phonebook of Farazsms system.", 'farazsms'),
        ]);
        //end send settings


        // send to admin
        $widget->add_control("sms_to_admin", [
            "label" => esc_html__("Send SMS to the manager", "farazsms"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => esc_html__("Enable", "farazsms"),
            "label_off" => esc_html__("Disable", "farazsms"),
            "return_value" => "yes",
            "default" => "yes",
            "description" => esc_html__(
                "If you want an SMS alert to be sent to the manager's number as soon as the visitor completes the form, check this option.",
                "farazsms"
            ),
            "separator" => "before",
        ]);

        $widget->add_control("sms_admin_method", [
            "label" => esc_html__("How to send", "farazsms"),
            "type" => \Elementor\Controls_Manager::SELECT,
            "default" => "none",
            "condition" => [
                "sms_to_admin" => "yes",
            ],
            "options" => [
                "webservice" => esc_html__("Web Service", "farazsms"),
                "pattern" => esc_html__("Pattern", "farazsms"),
                "none" => esc_html__("None", "farazsms"),
            ],
        ]);

        $widget->add_control("sms_admin_pattern", [
            "label" => esc_html__("Pattern code", "farazsms"),
            "type" => \Elementor\Controls_Manager::TEXT,
            "placeholder" => "Enter your pattern code",
            "label_block" => true,
            "condition" => [
                "sms_to_admin" => "yes",
                "sms_admin_method" => "pattern",
            ],
        ]);

        $widget->add_control("sms_admin_content", [
            "label" => esc_html__("Sms content", "farazsms"),
            "type" => \Elementor\Controls_Manager::TEXTAREA,
            "rows" => 2,
            "placeholder" => esc_html__("Enter SMS text or pattern variables", "farazsms"),
            "condition" => [
                "sms_to_admin" => "yes",
            ],
            "description" => esc_html__("By completing the form, an SMS will be sent to the admin. The administrator number must be defined in the plugin settings.", "farazsms"),
            "separator" => "after",

        ]);

        $widget->add_control("sms_to_other", [
            "label" => esc_html__("Send sms copy to others", "farazsms"),
            "type" => \Elementor\Controls_Manager::TEXT,
            "placeholder" => esc_html__("Example: 09121234567,09351234567", "farazsms"),
            "label_block" => true,
            "condition" => [
                "sms_to_admin" => "yes",
            ],
            "description" => esc_html__(
                "A copy of this SMS will be sent to these numbers, while sending to the administrator. (Separate with english commas.)",
                "farazsms"
            ),
        ]);
        //end send to admin

        $widget->end_controls_section();
    }

    /**
     * Run action.
     *
     * Runs the Sendy action after form submission.
     *
     * @since 1.0.0
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {

        $settings = $record->get('form_settings');

        //  Make sure that there is a Sendy installation URL.
        if (empty($settings['sendy_url'])) {
            return;
        }

        //  Make sure that there is a Sendy list ID.
        if (empty($settings['sendy_list'])) {
            return;
        }

        // Make sure that there is a Sendy email field ID (required by Sendy to subscribe users).
        if (empty($settings['sendy_email_field'])) {
            return;
        }

        // Get submitted form data.
        $raw_fields = $record->get('fields');

        // Normalize form data.
        $fields = [];
        foreach ($raw_fields as $id => $field) {
            $fields[$id] = $field['value'];
        }

        // Make sure the user entered an email (required by Sendy to subscribe users).
        if (empty($fields[$settings['sendy_email_field']])) {
            return;
        }

        // Request data based on the param list at https://sendy.co/api
        $sendy_data = [
            'email' => $fields[$settings['sendy_email_field']],
            'list' => $settings['sendy_list'],
            'ipaddress' => \ElementorPro\Core\Utils::get_client_ip(),
            'referrer' => isset($_POST['referrer']) ? $_POST['referrer'] : '',
        ];

        // Add name if field is mapped.
        if (empty($fields[$settings['sendy_name_field']])) {
            $sendy_data['name'] = $fields[$settings['sendy_name_field']];
        }

        // Send the request.
        wp_remote_post(
            $settings['sendy_url'] . 'subscribe',
            [
                'body' => $sendy_data,
            ]
        );
    }

    /**
     * On export.
     *
     * Clears Sendy form settings/fields when exporting.
     *
     * @since 1.0.0
     * @access public
     * @param array $element
     */
    public function on_export($element)
    {

        unset(
            $element['sendy_url'],
            $element['sendy_list'],
            $element['sendy_email_field'],
            $element['sendy_name_field']
        );

        return $element;
    }
}
