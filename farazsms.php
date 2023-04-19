<?php

/**
 * @wordpress-plugin
 * Plugin Name: Farazsms
 * Plugin URI: https://farazsms.com/
 * Description: By using the Farazsms plugin, you can professionally equip your site with a powerful SMS tool for information and marketing. Saving customers numbers in the phone book, sending welcome SMS, sending reply SMS to comments, etc. are part of the features of this powerful SMS plugin.
 * Version: 2.5.0
 * Requires at least: 5.3
 * Requires PHP: 7.1
 * Author: farazsmsdeveloper, wpstormdev
 * Text Domain: farazsms
 * Domain Path: /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 7.5.1
 * @package Farazsms
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms.
 */
class Farazsms {


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
		$this->define_constants();
		$this->farazsms_loader();

		register_activation_hook( __FILE__, [ $this, 'activate_farazsms' ] );
		add_action( 'activated_plugin', [ $this, 'farazsms_activation_redirect' ] );
	}

	/**
	 * Defines all constants
	 *
	 * @since 2.0.0
	 */
	public function define_constants() {

		/**
		 * Defines all constants
		 *
		 * @since 2.0.0
		 */
		define( 'FARAZSMS_VERSION', '2.5.0' );
		define( 'FARAZSMS_FILE', __FILE__ );
		define( 'FARAZSMS_PATH', plugin_dir_path( FARAZSMS_FILE ) );
		define( 'FARAZSMS_BASE', plugin_basename( FARAZSMS_FILE ) );
		define( 'FARAZSMS_SLUG', 'farazsms_settings' );
		define( 'FARAZSMS_SETTINGS_LINK', admin_url( 'admin.php?page=' . FARAZSMS_SLUG ) );
		define( 'FARAZSMS_CLASSES_PATH', FARAZSMS_PATH . 'classes/' );
		define( 'FARAZSMS_MODULES_PATH', FARAZSMS_PATH . 'modules/' );
		define( 'FARAZSMS_URL', plugins_url( '/', FARAZSMS_FILE ) );
		define( 'FARAZSMS_WEB_MAIN', 'https://farazsms.com/' );
		define( 'FARAZSMS_WEB_MAIN_DOC', FARAZSMS_WEB_MAIN . 'farazsms-wordpress-plugin/' );
	}

	/**
	 * Require loader farazsms class.
	 *
	 * @return void
	 */
	public function farazsms_loader() {
		require FARAZSMS_CLASSES_PATH . 'class-farazsms-loader.php';
	}

	/**
	 * Require farazsms activator class.
	 *
	 * @return void
	 */
	public function activate_farazsms() {
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-activator.php';
		Farazsms_Activator::activate();
	}


	/**
	 * Redirect user to plugin settings page after plugin activated.
	 *
	 * @return void
	 */
	public function farazsms_activation_redirect() {
		if ( get_option( 'farazsms_do_activation_redirect', false ) ) {
			delete_option( 'farazsms_do_activation_redirect' );
			exit( wp_redirect( FARAZSMS_SETTINGS_LINK ) );
		}
	}

}

Farazsms::get_instance();