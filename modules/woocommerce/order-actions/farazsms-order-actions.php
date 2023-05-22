<?php

/**
 * Farazsms order actions.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Order_Actions.
 */
class Farazsms_Order_Actions {
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
		// Hook functions for order status changes
		add_action( 'woocommerce_order_status_pending', [ $this, 'farazsms_fetch_pending_order_actions' ] );
		add_action('woocommerce_order_status_failed', [$this, 'farazsms_fetch_failed_order_actions'] );
		add_action( 'woocommerce_order_status_processing', [ $this, 'farazsms_fetch_processing_order_actions' ] );
		add_action( 'woocommerce_order_status_completed', [ $this, 'farazsms_fetch_completed_order_actions' ] );
		add_action( 'woocommerce_order_status_on-hold', [ $this, 'farazsms_fetch_on_hold_order_actions' ] );
		add_action('woocommerce_order_status_cancelled', [$this, 'farazsms_fetch_cancelled_order_actions'] );
		add_action('woocommerce_order_status_refunded', [$this, 'farazsms_fetch_refunded_order_actions'] );
	}

	// Pending payment order status
	public function farazsms_fetch_pending_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'pending' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status( 'pending' );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// Failed order status
	public function farazsms_fetch_failed_order_actions($order_id) {
		// Fetch rows with order_status set to 'failed' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status('failed');

		// Process the fetched order actions
		foreach ($order_actions as $order_action) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// Processing order status
	public function farazsms_fetch_processing_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'processing' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status( 'processing' );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// Completed order status
	public function farazsms_fetch_completed_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'completed' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status( 'completed' );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// On-hold order status
	public function farazsms_fetch_on_hold_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'on-hold' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status( 'on-hold' );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// Cancelled order status
	public function farazsms_fetch_cancelled_order_actions($order_id) {
		// Fetch rows with order_status set to 'cancelled' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status('cancelled');

		// Process the fetched order actions
		foreach ($order_actions as $order_action) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// Refunded order status
	public function farazsms_fetch_refunded_order_actions($order_id) {
		// Fetch rows with order_status set to 'refunded' from the database table
		$order_actions = self::farazsms_fetch_order_actions_by_status('refunded');

		// Process the fetched order actions
		foreach ($order_actions as $order_action) {
			// Perform necessary actions for each order action
			// Example: Send SMS, process data, etc.
		}
	}

	// Function to fetch order actions by status from the database table
	public static function farazsms_fetch_order_actions_by_status( $status ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_woocommerce_order_actions';

		// Query to fetch rows with the specified order status
		$query = $wpdb->prepare( "SELECT * FROM $table_name WHERE order_status = %s", $status );

		// Fetch the rows
		$order_actions = $wpdb->get_results( $query, ARRAY_A );

		return $order_actions;
	}

}

Farazsms_Order_Actions::get_instance();

