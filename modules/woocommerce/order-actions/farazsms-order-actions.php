<?php

/**
 * Farazsms order actions.
 *
 * @package Farazsms
 * @since 2.7.0
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
	 * @since 2.7.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @return object Initialized object of class.
	 * @since 2.7.0
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
		$order_statuses = [
			'pending',
			'failed',
			'processing',
			'completed',
			'on-hold',
			'cancelled',
			'refunded',
		];

		foreach ( $order_statuses as $status ) {
			add_action( 'woocommerce_order_status_' . $status, [ $this, 'farazsms_fetch_order_actions' ] );
		}
	}

	public function farazsms_fetch_order_actions( $order_id ) {
		$status = str_replace( 'woocommerce_order_status_', '', current_filter() );

		// Fetch rows with the current order status from the database table
		$order_actions = self::fetch_order_actions_by_status( $status );
		//Get the WooCommerce order object
		$order = wc_get_order( $order_id );

		// Process the fetched order actions
		foreach ( $order_actions as $order_action ) {
			$total_price = $order->get_total();

			//Get the order_type
			$order_type       = $order_action['order_type']['type']['value'];
			$action_type      = $order_action['action']['type']['value'];
			$action_time      = $order_action['action']['action_time']['value'];
			$action_time_days = $order_action['action']['time'];
			$sms_message      = $order_action['action']['sms_message'];
			$pattern          = $order_action['action']['sms_pattern'];
			if ( $pattern ) {
				$patternMessage = Farazsms_Ippanel::get_registered_pattern_variables( $pattern );
			}


			// Get customer data
			$customer_number = get_post_meta( $order->get_customer_id(), $order_action['action']['mobile_meta_key']['value'], true );
			$customer_name   = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

			// Init $input_data
			$input_data = [];
			$variables  = [
				[
					'key'         => 'customer_name',
					'placeholder' => '%customer_name%',
					'value'       => $customer_name
				],
				[
					'key'         => 'order_id',
					'placeholder' => '%order_id%',
					'value'       => $order_id
				],
				[
					'key'         => 'total_price',
					'placeholder' => '%total_price%',
					'value'       => $total_price
				]
			];
			// Fill $input_data using $patternMessage
			if ( $pattern && $patternMessage ) {
				$input_data = Farazsms_Base::extract_dynamic_data( $patternMessage, $variables );
			}

			$message = str_replace(
				[
					'%customer_name%',
					'%order_id%',
					'%total_price%',
				],
				[
					$customer_name,
					$order_id,
					$total_price,
				],
				$sms_message
			);


			// Get the order date
			$order_date   = $order->get_date_created();
			$time_to_send = Farazsms_Base::calculate_scheduled_date( $order_date, $action_time_days );


			if ( self::is_order_match_order_type( $order, $order_type ) ) {
				// Check if the action type is 'save_customer_mobile_to_phonebook'
				if ( $action_type === 'save_customer_mobile_to_phonebook' ) {
					// Perform necessary actions for saving phone to phonebook
					self::save_customer_phone_to_phonebook( $order, $order_action );
				}
				if ( $action_type === 'send_sms_to_admin' ) {
					if ( $action_time === 'immediately' ) {
						// Perform necessary actions for sending pattern immediately
						Farazsms_Ippanel::send_pattern( $pattern, Farazsms_Base::$admin_number, $input_data );
					} else {
						// Perform necessary actions for sending timed SMS to admin
						Farazsms_Ippanel::send_timed_sms( Farazsms_Base::$admin_number, $time_to_send, $message );
					}
				}
				if ( $action_type === 'send_sms_to_customer' ) {
					if ( $action_time === 'immediately' ) {
						// Perform necessary actions for sending pattern immediately
						Farazsms_Ippanel::send_pattern( $pattern, $customer_number, $input_data );
					} else {
						// Perform necessary actions for sending timed SMS to customer
						Farazsms_Ippanel::send_timed_sms( $customer_number, $time_to_send, $message );
					}
				}
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
		$order_actions = $wpdb->get_results( $query );


		// Convert nested objects from JSON to arrays
		$order_actions = array_map( function ( $order_action ) {
			$order_action->order_type   = json_decode( $order_action->order_type, true );
			$order_action->order_status = json_decode( $order_action->order_status, true );
			$order_action->action       = json_decode( $order_action->action, true );

			return (array) $order_action;
		}, $order_actions );

		return $order_actions;
	}

	public static function is_order_match_order_type( $order, $order_type ) {
		// Check if the order type is "all_orders"
		if ( $order_type === 'all_orders' ) {
			return true; // Match all orders
		}

		// Check the order total against the minimum and maximum order total criteria
		$order_total         = (float) $order->get_total();
		$minimum_order_total = isset( $order_type['minimum_order_total'] ) ? (float) $order_type['minimum_order_total'] : null;
		$maximum_order_total = isset( $order_type['maximum_order_total'] ) ? (float) $order_type['maximum_order_total'] : null;

		if ( ( $minimum_order_total !== null && $order_total < $minimum_order_total ) || ( $maximum_order_total !== null && $order_total > $maximum_order_total ) ) {
			return false; // Order total does not match the criteria
		}

		// Check the order turn against the order count
		$order_turn = isset( $order_type['order_turn'] ) ? (int) $order_type['order_turn'] : null;

		if ( $order_turn !== null ) {
			$customer_id = $order->get_customer_id();

			// Retrieve the order count for the customer
			$customer_order_count = wc_get_customer_order_count( $customer_id );

			if ( $customer_order_count !== $order_turn ) {
				return false; // Order count does not match the criteria
			}
		}

		// Check the included products criteria
		$included_products = $order_type['included_products'] ?? null;

		if ( $included_products !== null ) {
			$product_ids = array_map( function ( $product ) {
				return $product['value'];
			}, $included_products );

			$order_items = $order->get_items();

			$found_products = array_filter( $order_items, function ( $item ) use ( $product_ids ) {
				return in_array( $item->get_product_id(), $product_ids );
			} );

			if ( $order_type === 'only_include' && count( $found_products ) !== count( $order_items ) ) {
				return false; // Order contains other products besides the included products
			}

			if ( $order_type === 'include' && empty( $found_products ) ) {
				return false; // No included products found in the order
			}
		}

		// Check the excluded products criteria
		$excluded_products = $order_type['excluded_products'] ?? [];

		foreach ( $excluded_products as $product ) {
			$product_id = $product['value'] ?? null;

			if ( $product_id && in_array( $product_id, $order->get_items_product_ids(), true ) ) {
				return false; // Excluded product found in the order
			}
		}

		return true; // All criteria match the order
	}

	public static function save_customer_phone_to_phonebook( $order, $order_action ) {
		$phone              = $order->get_billing_phone();
		$billing_first_name = $order->get_billing_first_name();
		$billing_last_name  = $order->get_billing_last_name();
		$user_full_name     = $billing_first_name . ' ' . $billing_last_name;

		$list    = [];
		$list[0] = (object) [
			'number'       => $phone,
			'name'         => $user_full_name ?? '',
			'phonebook_id' => (int) $order_action['action']['phonebook']['value']
		];

		Farazsms_Ippanel::save_list_of_phones_to_phonebook( $list );
	}

}

Farazsms_Order_Actions::get_instance();

