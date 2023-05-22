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
		add_action( 'woocommerce_order_status_pending', [ $this, 'farazsms_fetch_pending_order_actions' ], 99,1 );
		add_action( 'woocommerce_order_status_failed', [ $this, 'farazsms_fetch_failed_order_actions' ], 99,1 );
		add_action( 'woocommerce_order_status_processing', [ $this, 'farazsms_fetch_processing_order_actions' ], 99,1 );
		add_action( 'woocommerce_order_status_completed', [ $this, 'farazsms_fetch_completed_order_actions' ], 99,1 );
		add_action( 'woocommerce_order_status_on-hold', [ $this, 'farazsms_fetch_on_hold_order_actions' ], 99,1 );
		add_action( 'woocommerce_order_status_cancelled', [ $this, 'farazsms_fetch_cancelled_order_actions' ], 99,1 );
		add_action( 'woocommerce_order_status_refunded', [ $this, 'farazsms_fetch_refunded_order_actions' ], 99,1 );
	}

	// Pending payment order status
	public function farazsms_fetch_pending_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'pending' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'pending' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// Failed order status
	public function farazsms_fetch_failed_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'failed' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'failed' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );


		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// Processing order status
	public function farazsms_fetch_processing_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'processing' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'processing' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// Completed order status
	public function farazsms_fetch_completed_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'completed' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'completed' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// On-hold order status
	public function farazsms_fetch_on_hold_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'on-hold' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'on-hold' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// Cancelled order status
	public function farazsms_fetch_cancelled_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'cancelled' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'cancelled' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// Refunded order status
	public function farazsms_fetch_refunded_order_actions( $order_id ) {
		// Fetch rows with order_status set to 'refunded' from the database table
		$order_actions = self::fetch_order_actions_by_status( 'refunded' );

		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {

			//Get the order_type
			$order_type = $order_action['order_type'];

			if(self::is_order_match_order_type($order, $order_type)) {
				// Perform necessary actions for each order action
				// Example: Send SMS, process data, etc.
			}
		}
	}

	// Function to fetch order actions by status from the database table
	public static function fetch_order_actions_by_status( $status ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'farazsms_woocommerce_order_actions';

		// Query to fetch rows with the specified order status
		$query = $wpdb->prepare(
			"SELECT * FROM $table_name WHERE JSON_EXTRACT(order_status, '$.value') = %s",
			$status
		);

		// Fetch the rows
		$order_actions = $wpdb->get_results($query);


		// Convert nested objects from JSON to arrays
		$order_actions = array_map(function($order_action) {
			$order_action->order_type = json_decode($order_action->order_type, true);
			$order_action->order_status = json_decode($order_action->order_status, true);
			$order_action->action = json_decode($order_action->action, true);
			return (array) $order_action;
		}, $order_actions);

		return $order_actions;
	}

	public static function is_order_match_order_type($order, $order_type) {

		// Extract the order type value
		$order_type_value = $order_type['type']['value'] ?? null;


		// Check if the order type is "all_orders"
		if ($order_type_value === 'all_orders') {
			return true; // Match all orders
		}

		// Check the order total against the minimum and maximum order total criteria
		$order_total = (float) $order->get_total();
		$minimum_order_total = isset($order_type['minimum_order_total']) ? (float) $order_type['minimum_order_total'] : null;
		$maximum_order_total = isset($order_type['maximum_order_total']) ? (float) $order_type['maximum_order_total'] : null;

		if (($minimum_order_total !== null && $order_total < $minimum_order_total) || ($maximum_order_total !== null && $order_total > $maximum_order_total)) {
			return false; // Order total does not match the criteria
		}

		// Check the order turn against the order count
		$order_turn = isset($order_type['order_turn']) ? (int) $order_type['order_turn'] : null;

		if ($order_turn !== null) {
			$customer_id = $order->get_customer_id();

			// Retrieve the order count for the customer
			$customer_order_count = wc_get_customer_order_count($customer_id);

			if ($customer_order_count !== $order_turn) {
				return false; // Order count does not match the criteria
			}
		}

		// Check the included products criteria
		$included_products = $order_type['included_products'] ?? null;

		if ($included_products !== null) {
			$product_ids = array_map(function($product) {
				return $product['value'];
			}, $included_products);

			$order_items = $order->get_items();

			$found_products = array_filter($order_items, function($item) use ($product_ids) {
				return in_array($item->get_product_id(), $product_ids);
			});

			if ($order_type_value === 'only_include' && count($found_products) !== count($order_items)) {
				return false; // Order contains other products besides the included products
			}

			if ($order_type_value === 'include' && empty($found_products)) {
				return false; // No included products found in the order
			}
		}

		// Check the excluded products criteria
		$excluded_products = $order_type['excluded_products'] ?? [];

		foreach ($excluded_products as $product) {
			$product_id = $product['value'] ?? null;

			if ($product_id && in_array($product_id, $order->get_items_product_ids(), true)) {
				return false; // Excluded product found in the order
			}
		}

		return true; // All criteria match the order
	}


}

Farazsms_Order_Actions::get_instance();

