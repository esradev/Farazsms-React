<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Farazsms
 * @subpackage Farazsms/classes
 * @author     FarazSMS <info@farazsms.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_i18n.
 */
class Farazsms_i18n {
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
		add_action( 'plugins_loaded', [$this, 'load_plugin_textdomain'] );
	}

	/**
	 * Load Farazsms Text Domain for translation..
	 *
	 * @since  2.0.0
	 * @return void
	 */

	public function load_plugin_textdomain()
	{
		// Default languages directory for Farazsms.
		$lang_dir = dirname(FARAZSMS_BASE) . '/languages';

		//Load Farazsms languages for PHP files.
		load_plugin_textdomain('farazsms', false, $lang_dir);
	}
}

Farazsms_i18n::get_instance();

