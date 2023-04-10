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
 * Class Farazsms_Order_Review.
 */
class Farazsms_Order_Review {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	private static $woo_poll;
	private static $woo_poll_time;
	private static $woo_poll_msg;


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
			self::$woo_poll                  = $woocommerce_options['woo_poll'];
			self::$woo_poll_time             = intval($woocommerce_options['woo_poll_time']);
			self::$woo_poll_msg              = $woocommerce_options['woo_poll_msg'];
		}

		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
		add_shortcode( 'farazsms_order_review_landing_page', [$this, 'order_review_page'] );
		add_action('init', [$this, 'create_order_review_landing_page']);
		add_action('wp_ajax_farazsms_submit_order_review', [$this, 'farazsms_submit_order_review']);
		add_action('wp_ajax_nopriv_farazsms_submit_order_review', [$this, 'farazsms_submit_order_review']);
		add_action( 'woocommerce_thankyou', [$this, 'schedule_review_reminder_sms'] );
		add_action( 'woocommerce_order_status_completed', [$this, 'schedule_review_reminder_sms'] );
	}

	public function enqueue_styles() {
		// Check if current page is the order review page
		if ( is_page( 'order-review' ) ) {
			// Enqueue your custom CSS file
			wp_enqueue_style( 'farazsms-order-review-css', FARAZSMS_URL . 'assets/css/farazsms-order-review-page.css', [], 12, 'all' );
		}
	}


	public function enqueue_scripts() {
		wp_enqueue_script( 'farazsms-order-review-js', FARAZSMS_URL . 'assets/js/farazsms-order-review-page.js', [], 12, true );
		wp_localize_script(
			'farazsms-order-review-js',
			'fsms_ajax_object',
			[ 'ajax_url' => admin_url( 'admin-ajax.php' ) ]
		);
	}

	public function order_review_page( $data ) {
		$data = shortcode_atts( [
			'order_id' => '',
		], $data );

		$order_id = intval( $data['order_id'] );
		$order = wc_get_order( $order_id );

		// Generate the HTML for the landing page
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'farazsms-order-review-page.php';
		$html = ob_get_clean();

		return $html;
	}

	public function create_order_review_landing_page() {
		// Check if the custom landing page already exists
		$custom_page = get_page_by_path('order-review');
		if (! $custom_page) {
			// Create a new page with the slug 'order-review'
			$page = [
				'post_title'    => __('Order Review', 'farazsms'),
				'post_name'     => 'order-review',
				'post_status'   => 'publish',
				'post_type'     => 'page',
				'post_content'  => '[farazsms_order_review_landing_page]'
			];
			wp_insert_post($page);
		}
	}

	public function farazsms_submit_order_review() {
		// Retrieve form data
		$product_id = $_POST['product_id'];
		$user_name = $_POST['user_name'];
		$user_email = $_POST['user_email'];
		$rating = $_POST['rating'];
		$review = sanitize_textarea_field($_POST['review']);

		// Validate form data
		if (!is_numeric($product_id) || !is_numeric($rating) || $rating < 1 || $rating > 5 || empty($review)) {
			wp_send_json_error('Invalid form data');
		}

		// Create the WooCommerce review object
		$data = [
			'comment_post_ID' => $product_id,
			'comment_author' => $user_name ?? 'Anonymous',
			'comment_author_email' => $user_email ?? 'info@farazsms.com',
			'comment_content' => $review,
			'comment_type' => 'review',
		];

		// Insert the review into the database
		$comment_id = wp_insert_comment($data);

		if (is_wp_error($comment_id)) {
			wp_send_json_error('Failed to save review');
		}

		// Add the rating to the comment
		update_comment_meta( $comment_id, 'rating', $rating );

		// Send a success response
		wp_send_json_success('Review posted successfully');

	}

	public function schedule_review_reminder_sms( $order_id ) {

		$order    = wc_get_order( $order_id );
		$phone_number    = $order->get_billing_phone();
		$data['review_link'] = 'https://example.com/review-page/?order_id=' . $order_id;
		$this->send_timed_message( $phone_number, $data, $order->get_date_created() );
	}

	public function send_timed_message( $phone_number, $data, $order_date ) {
		$fsms_woo_poll         = self::$woo_poll;
		$fsms_woo_poll_time    = self::$woo_poll_time;
		$fsms_woo_poll_message = self::$woo_poll_msg;
		if ( $fsms_woo_poll !== true || empty( $fsms_woo_poll_time ) || empty( $fsms_woo_poll_message ) ) {
			return;
		}
		$message = str_replace(
			[
				'%time%',
				'%sitename%',
				'%review_link%',
			],
			[
				$fsms_woo_poll_time,
				get_bloginfo( 'name' ),
				$data['review_link'],
			],
			$fsms_woo_poll_message
		);

		$date_to_send = date( 'Y-m-d H:i:s', strtotime( $order_date->date( 'Y-m-d H:i:s' ) . ' + ' . $fsms_woo_poll_time . ' days' ) );

		$body     = [
			'uname'   => Farazsms_Base::$username,
			'pass'    => Farazsms_Base::$password,
			'from'    => '+98club',
			'op'      => 'send',
			'to'      => $phone_number,
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


}

Farazsms_Order_Review::get_instance();

