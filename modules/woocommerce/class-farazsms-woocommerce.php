<?php

/**
 * Farazsms woocommerce.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Woocommerce.
 */
class Farazsms_Woocommerce {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	private static $woo_checkout_otp_pattern;
	private static $woo_poll;
	private static $woo_poll_time;
	private static $woo_poll_msg;
	private static $woo_tracking_pattern;
	private static $woo_checkout_otp;
	private static $woo_retention_order_no;
	private static $woo_retention_order_month;
	private static $woo_retention_msg;


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
		$woocommerce_options = json_decode( get_option( 'farazsms_woocommerce_options' ), true );
		if ( $woocommerce_options ) {
			self::$woo_checkout_otp          = $woocommerce_options['woo_checkout_otp'];
			self::$woo_checkout_otp_pattern  = $woocommerce_options['woo_checkout_otp_pattern'];
			self::$woo_poll                  = $woocommerce_options['woo_poll'];
			self::$woo_poll_time             = $woocommerce_options['woo_poll_time'];
			self::$woo_poll_msg              = $woocommerce_options['woo_poll_msg'];
			self::$woo_tracking_pattern      = $woocommerce_options['woo_tracking_pattern'];
			self::$woo_retention_order_no    = $woocommerce_options['woo_retention_order_no'];
			self::$woo_retention_order_month = $woocommerce_options['woo_retention_order_month'];
			self::$woo_retention_msg         = $woocommerce_options['woo_retention_msg'];
		}

		add_action( 'add_meta_boxes', [ $this, 'tracking_code_order_postbox' ] );
		add_action( 'wp_ajax_fsms_send_tracking_code_sms', [ $this, 'send_tracking_code_sms' ] );
		add_action( 'wp_ajax_nopriv_fsms_send_tracking_code_sms', [ $this, 'send_tracking_code_sms' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'woocommerce_thankyou', [ $this, 'woo_payment_finished' ] );
//		add_action( 'woocommerce_thankyou', [ $this, 'woo_send_timed_message' ] );
//		add_action( 'init', [$this, 'fsms_woo_retention_action' ]);
		add_action( 'woocommerce_checkout_get_value', [ $this, 'fsms_pre_populate_checkout_fields' ], 10, 2 );
		add_filter( 'woocommerce_billing_fields', [ $this, 'fsms_woocommerce_checkout_fields' ] );
		add_action( 'woocommerce_checkout_process', [ $this, 'fsms_woocommerce_checkout_process' ] );
		add_action( 'wp_ajax_fsms_send_otp_code', [ $this, 'fsms_send_otp_code' ] );
		add_action( 'wp_ajax_nopriv_fsms_send_otp_code', [ $this, 'fsms_send_otp_code' ] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$woo_checkout_otp = self::$woo_checkout_otp;
		if ( $woo_checkout_otp && is_checkout() ) {
			wp_enqueue_style( 'farazsms-woo-otp', FARAZSMS_URL . 'assets/css/farazsms-woo-otp.css', [], '2.0', 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$woo_checkout_otp = self::$woo_checkout_otp;
		if ( $woo_checkout_otp && is_checkout() ) {
			wp_enqueue_script( 'farazsms-woo-otp', FARAZSMS_URL . 'assets/js/farazsms-woo-otp.js', [ 'jquery' ], '2.0', true );
			wp_localize_script(
				'farazsms-woo-otp',
				'fsms_ajax_url',
				[ 'ajax_url' => admin_url( 'admin-ajax.php' ) ]
			);
		}
	}

	/**
	 * Send Tracking code for orders.
	 *
	 * @since 1.0.0
	 */
	public function tracking_code_order_postbox() {
		add_meta_box(
			'fsms-tracking_send_sms',
			__( 'Send tracking code', 'farazsms' ),
			[
				$this,
				'add_order_tracking_box',
			],
			'shop_order',
			'side',
			'core'
		);

	}


	public function add_order_tracking_box( $post ) {
		echo '<div id="fsms-tracking-code-container">';
		echo '<div id="fsms-tracking-code-input"><input type="text" name="tracking_code" id="fsms_tracking_code" placeholder="' . __( 'Enter tracking code', 'farazsms' ) . '"/></div>';

		// Select input for selecting the service provider
		echo '<div id="fsms-tracking-code-provider">';
		echo '<label for="fsms_tracking_provider">' . __( 'Service Provider', 'farazsms' ) . '</label>';
		echo '<select name="tracking_provider" id="fsms_tracking_provider">';

		// Default options
		echo '<option value="post_office">' . __( 'Post Office', 'farazsms' ) . '</option>';
		echo '<option value="tipaxco">' . __( 'Tipaxco', 'farazsms' ) . '</option>';

		// Option for custom provider name
		echo '<option value="custom_provider">' . __( 'Custom Provider', 'farazsms' ) . '</option>';

		echo '</select>';
		echo '</div>';

		// Date picker for selecting the date of posting
		echo '<div id="fsms-tracking-code-date">';
		echo '<label for="fsms_tracking_date">' . __( 'Date of Posting', 'farazsms' ) . '</label>';
		echo '<input type="date" name="tracking_date" id="fsms_tracking_date" />';
		echo '</div>';

		echo '<div id="fsms-tracking-code-button"><div class="fsms_button" id="send_tracking_code_button"><span class="button__text">' . __( 'Send Sms', 'farazsms' ) . '</span></div></div>';
		echo '<input type="hidden" id="fsms-tracking-code-order_id" value="' . $post->ID . '">';
		echo '<div id="send_tracking_code_response" style="display: none;"></div>';
		echo '</div>';

		?>
        <script>
          // Handle custom provider input
          const select = document.querySelector('#fsms_tracking_provider')
          const input = document.createElement('input')
          input.type = 'text'
          input.style.display = 'none'
          input.name = 'tracking_provider'
          input.id = 'fsms_custom_provider'
          input.placeholder = 'نام شرکت یا نحوه ارسال سفارش'
          select.after(input)
          select.addEventListener('change', function () {
            if (this.value === 'custom_provider') {
              input.style.display = ''
              input.focus()
            } else {
              input.style.display = 'none'
              input.value = ''
            }
          })
        </script>
		<?php
	}


	public function send_tracking_code_sms() {
		$tracking_code = ( $_POST['tracking_code'] ?? '' );
		$order_id      = ( $_POST['order_id'] ?? '' );
		try {
			if ( empty( $tracking_code ) || strlen( $tracking_code ) < 20 ) {
				throw new Exception( __( 'Please enter the tracking code correctly.', 'farazsms' ) );
			}

			$order = wc_get_order( $order_id );
			$phone = $order->get_billing_phone();
			if ( empty( $phone ) ) {
				throw new Exception( __( 'Customer phone number not entered.', 'farazsms' ) );
			}

			$order_data['order_id']           = $order->get_id();
			$order_data['order_status']       = wc_get_order_status_name( $order->get_status() );
			$order_data['billing_full_name']  = $order->get_formatted_billing_full_name();
			$order_data['shipping_full_name'] = $order->get_formatted_shipping_full_name();
			$this->send_tracking_code( $phone, $tracking_code, $order_data );
			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

	}

	/*public function send_tracking_code_sms() {
		$tracking_code = ( $_POST['tracking_code'] ?? '' );
		$tracking_provider = ( $_POST['tracking_provider'] ?? '' );
		$tracking_date = ( $_POST['tracking_date'] ?? '' );
		$order_id = ( $_POST['order_id'] ?? '' );
		try {
			if ( empty( $tracking_code ) || strlen( $tracking_code ) < 20 ) {
				throw new Exception( __( 'Please enter the tracking code correctly.', 'farazsms' ) );
			}

			if ( empty( $tracking_provider ) ) {
				throw new Exception( __( 'Please select a service provider.', 'farazsms' ) );
			}

			if ( empty( $tracking_date ) ) {
				throw new Exception( __( 'Please select the date of posting.', 'farazsms' ) );
			}

			$order = wc_get_order( $order_id );
			$phone = $order->get_billing_phone();
			if ( empty( $phone ) ) {
				throw new Exception( __( 'Customer phone number not entered.', 'farazsms' ) );
			}

			$order_data['order_id']           = $order->get_id();
			$order_data['order_status']       = wc_get_order_status_name( $order->get_status() );
			$order_data['billing_full_name']  = $order->get_formatted_billing_full_name();
			$order_data['shipping_full_name'] = $order->get_formatted_shipping_full_name();

			$this->set_tracking_pattern( $tracking_provider, $tracking_date );
			$this->send_tracking_code( $phone, $tracking_code, $order_data );

			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
*/


	/**
	 * Send tracking code.
	 *
	 * @throws Exception
	 */
	public function send_tracking_code( $phone, $tracking_code, $order_data ) {

		if ( empty( self::$woo_tracking_pattern ) ) {
			throw new Exception( __( 'Pattern not entered to send tracking code', 'farazsms' ) );
		}

		$phone          = Farazsms_Base::fsms_tr_num( $phone );
		$input_data     = [];
		$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( self::$woo_tracking_pattern );
		if ( $patternMessage === null ) {
			throw new Exception( __( 'Probably your pattern has not been approved', 'farazsms' ) );
		}
		if ( str_contains( $patternMessage, '%tracking_code%' ) ) {
			$input_data['tracking_code'] = strval( $tracking_code );
		}
		if ( str_contains( $patternMessage, '%order_id%' ) ) {
			$input_data['order_id'] = strval( $order_data['order_id'] );
		}
		if ( str_contains( $patternMessage, '%order_status%' ) ) {
			$input_data['order_status'] = strval( $order_data['order_status'] );
		}
		if ( str_contains( $patternMessage, '%billing_full_name%' ) ) {
			$input_data['billing_full_name'] = strval( $order_data['billing_full_name'] );
		}
		if ( str_contains( $patternMessage, '%shipping_full_name%' ) ) {
			$input_data['shipping_full_name'] = strval( $order_data['shipping_full_name'] );
		}

		return Farazsms_Ippanel::send_pattern( self::$woo_tracking_pattern, $phone, $input_data );
	}

	/*	public function send_tracking_code( $phone, $tracking_code, $order_data, $provider, $date ) {

			if ( empty( self::$woo_tracking_pattern ) ) {
				throw new Exception( __( 'Pattern not entered to send tracking code', 'farazsms' ) );
			}

			$phone          = Farazsms_Base::fsms_tr_num( $phone );
			$input_data     = [];
			$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( self::$woo_tracking_pattern );
			if ( $patternMessage === null ) {
				throw new Exception( __( 'Probably your pattern has not been approved', 'farazsms' ) );
			}
			if ( str_contains( $patternMessage, '%tracking_code%' ) ) {
				$input_data['tracking_code'] = strval( $tracking_code );
			}
			if ( str_contains( $patternMessage, '%order_id%' ) ) {
				$input_data['order_id'] = strval( $order_data['order_id'] );
			}
			if ( str_contains( $patternMessage, '%order_status%' ) ) {
				$input_data['order_status'] = strval( $order_data['order_status'] );
			}
			if ( str_contains( $patternMessage, '%billing_full_name%' ) ) {
				$input_data['billing_full_name'] = strval( $order_data['billing_full_name'] );
			}
			if ( str_contains( $patternMessage, '%shipping_full_name%' ) ) {
				$input_data['shipping_full_name'] = strval( $order_data['shipping_full_name'] );
			}
			if ( str_contains( $patternMessage, '%provider%' ) ) {
				$input_data['provider'] = strval( $provider );
			}
			if ( str_contains( $patternMessage, '%date%' ) ) {
				$input_data['date'] = strval( $date );
			}

			return Farazsms_Ippanel::send_pattern( self::$woo_tracking_pattern, $phone, $input_data );
		}*/


	/**
	 * Send woocommerce verification code.
	 */
	public function send_woocommerce_verification_code( $phone, $data ) {
		$phone = Farazsms_Base::fsms_tr_num( $phone );
		if ( empty( $phone ) || empty( self::$woo_checkout_otp_pattern ) || empty( $data ) ) {
			return false;
		}

		$input_data         = [];
		$input_data['code'] = strval( $data['code'] );

		return Farazsms_Ippanel::send_pattern( self::$woo_checkout_otp_pattern, $phone, $input_data );
	}

	/**
	 * Check if code is valid for woocommerce
	 */
	public function check_if_code_is_valid_for_woo( $phone, $code ) {
		global $wpdb;
		$table          = $wpdb->prefix . 'farazsms_vcode';
		$generated_code = $wpdb->get_col( "SELECT code FROM {$table} WHERE phone = '" . $phone . "'" );
		if ( $generated_code[0] == $code ) {
			// $wpdb->delete( $table, array( 'phone' => $phone ) );
			return true;
		}

		return false;
	}

	/**
	 * Delete code for woocommerce
	 */
	public function delete_code_for_woo( $phone ) {
		global $wpdb;
		$table = $wpdb->prefix . 'farazsms_vcode';
		$wpdb->delete( $table, [ 'phone' => $phone ] );
	}

	/**
	 * Send timed message.
	 */
	public function send_timed_message( $phone, $data, $order_date ) {
		$fsms_woo_poll         = self::$woo_poll;
		$fsms_woo_poll_time    = self::$woo_poll_time;
		$fsms_woo_poll_message = self::$woo_poll_msg;
		if ( $fsms_woo_poll == 'false' || empty( $fsms_woo_poll_time ) || empty( $fsms_woo_poll_message ) ) {
			return;
		}
		$message = str_replace(
			[
				'%time%',
				'%item%',
				'%sitename%',
				'%item_link%',
			],
			[
				$fsms_woo_poll_time,
				$data['item'],
				get_bloginfo( 'name' ),
				$data['item_link'],
			],
			$fsms_woo_poll_message
		);

		$date_to_send = date( 'Y-m-d H:i:s', strtotime( $order_date->date( 'Y-m-d H:i:s' ) . ' + ' . $fsms_woo_poll_time . ' days' ) );

		$body     = [
			'uname'   => Farazsms_Base::$username,
			'pass'    => Farazsms_Base::$password,
			'from'    => '+98club',
			'op'      => 'send',
			'to'      => $phone,
			'time'    => $date_to_send,
			'message' => $message,
		];
		$response = wp_remote_post(
			'http://ippanel.com/api/select',
			[
				'method'      => 'POST',
				'headers'     => [ 'Content-Type' => 'application/json' ],
				'data_format' => 'body',
				'body'        => json_encode( $body ),
			]
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}
	}

	/**
	 * Woocommerce payment finished.
	 */
	public function woo_payment_finished( $id ) {
		$order = wc_get_order( $id );
		$phone = $order->get_billing_phone();

		if ( empty( $phone ) ) {
			return;
		}

		Farazsms_Ippanel::save_a_phone_to_phonebook( $phone, Farazsms_Base::$woo_phonebook_id );
		$this->fsms_delete_otp_code( $order );

		return true;
	}

	/**
	 *
	 * Send timed message.
	 *
	 */
	public function woo_send_timed_message( $order_id ) {

		$order    = wc_get_order( $order_id );
		$phone    = $order->get_billing_phone();
		$products = [];
		/*
		 * TODO: need to fix this or have another approach
		 */
		foreach ( $order->get_items() as $item_id => $item ) {
			$products[ $item->get_product_id() ] = $item->get_total();
		}
		$most_expensive_product_id = max( array_keys( $products ) );
		$product                   = wc_get_product( $most_expensive_product_id );
		$data['item']              = $product->get_title();
		$data['item_link']         = get_permalink( $most_expensive_product_id );
		$this->send_timed_message( $phone, $data, $order->get_date_created() );
	}

	/**
	 * Woocommerce retention action.
	 */
	public function fsms_woo_retention_action() {
		$retention_order_no    = self::$woo_retention_order_no;
		$retention_order_month = self::$woo_retention_order_month;
		$retention_message     = self::$woo_retention_msg;
		if ( empty( $retention_order_no ) || empty( $retention_order_month ) || empty( $retention_message ) ) {
			return;
		}

		global $wpdb;
		$customer_ids = $wpdb->get_col( "SELECT DISTINCT meta_value  FROM $wpdb->postmeta WHERE meta_key = '_customer_user' AND meta_value > 0" );
		if ( sizeof( $customer_ids ) > 0 ) {
			foreach ( $customer_ids as $customer_id ) {
				$customer   = new WC_Customer( $customer_id );
				$last_order = $customer->get_last_order();
				if ( ! $last_order ) {
					continue;
				}
				$sent_retention_message = get_post_meta( $last_order->get_id(), 'sent_retention_message', true );
				if ( $sent_retention_message == '1' ) {
					continue;
				}
				$date_completed = $last_order->get_date_completed();
				if ( ! empty( $date_completed ) && $date_completed->getTimestamp() <= strtotime( '-' . $retention_order_month . ' Months' ) ) {
					$args   = [
						'type'           => 'shop_order',
						'customer_id'    => $customer_id,
						'date_completed' => '<=' . strtotime( '-' . $retention_order_month . ' Months' ),
					];
					$orders = wc_get_orders( $args );
					if ( count( $orders ) >= $retention_order_no ) {
						$message = str_replace( [
							'%billing_full_name%',
							'%shipping_full_name%',
						], [
							$last_order->get_formatted_billing_full_name(),
							$last_order->get_formatted_shipping_full_name(),
						], $retention_message );
						Farazsms_Ippanel::send_message( [ $last_order->get_billing_phone() ], $message );
						update_post_meta( $last_order->get_id(), 'sent_retention_message', '1' );
					}
				}
			}
		}
	}

	/**
	 * Woocommerce checkout fields.
	 */
	public function fsms_woocommerce_checkout_fields( $fields ) {
		if ( self::$woo_checkout_otp !== 'true' ) {
			return $fields;
		}

		$fields['billing_phone_send_otp']   = [
			'label'    => __( 'Verification code', 'farazsms' ),
			'required' => '0',
			'type'     => 'text',
			'class'    => [
				'form-row-wide',
				'fsms_otp_field'
			],
			'priority' => 101
		];
		$fields['billing_phone_otp']        = [
			'label'    => __( 'Verification code', 'farazsms' ),
			'required' => '1',
			'type'     => 'number',
			'class'    => [
				'form-row-first',
				'fsms_otp_field'
			],
			'priority' => 102
		];
		$fields['billing_phone_otp_button'] = [
			'label'    => __( 'Send', 'farazsms' ),
			'required' => '0',
			'class'    => [
				'form-row-last',
				'fsms_otp_field_should_remove'
			],
			'priority' => 103
		];

		return $fields;
	}

	/**
	 * Woocommerce checkout process.
	 */
	public function fsms_woocommerce_checkout_process() {
		if ( self::$woo_checkout_otp !== 'true' ) {
			return;
		}

		if ( ! $_POST['billing_phone_otp'] ) {
			wc_add_notice( __( 'Please confirm your phone number first', 'farazsms' ), 'error' );
		}
		$otp      = $_POST['billing_phone_otp'];
		$is_valid = $this->check_if_code_is_valid_for_woo( $_POST['billing_phone'], $otp );
		if ( ! $is_valid ) {
			wc_add_notice( __( 'The verification code entered is not valid', 'farazsms' ), 'error' );
		}
	}

	/**
	 * Send OTP Code.
	 */
	public function fsms_send_otp_code() {
		$mobile = $_POST['mobile'];
		if ( ! isset( $mobile ) ) {
			wp_send_json_error( __( 'Please enter phone number.', 'farazsms' ) );
		}
		$generated_code = rand( 1000, 9999 );
		Farazsms_Base::save_generated_code_to_db( $mobile, $generated_code );
		$data   = [
			'code' => $generated_code,
		];
		$result = $this->send_woocommerce_verification_code( $mobile, $data );
		if ( ! $result ) {
			wp_send_json_error( __( 'An error occurred', 'farazsms' ) );
		} else {
			wp_send_json_success( __( 'Verification code sent successfully', 'farazsms' ) );
		}
	}

	/**
	 * Delete OTP Code.
	 */
	public function fsms_delete_otp_code( $order_id ) {
		$order = wc_get_order( $order_id );
		$this::delete_code_for_woo( $order->get_billing_phone() );
	}

	/**
	 *
	 * Pre-populate checkout fields.
	 *
	 */

	public function fsms_pre_populate_checkout_fields( $input, $key ) {
		global $current_user;
		$digits_mobile = get_user_meta( $current_user->ID, 'digits_phone_no', true );
		switch ( $key ):
			case 'billing_first_name':
			case 'shipping_first_name':
				return $current_user->first_name;
				break;

			case 'billing_last_name':
			case 'shipping_last_name':
				return $current_user->last_name;
				break;
			case 'billing_phone':
				return ! empty( $digits_mobile ) ? '0' . $digits_mobile : '';
				break;

		endswitch;
	}
}

Farazsms_Woocommerce::get_instance();
