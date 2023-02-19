<?php

/**
 * The loader plugin class
 *
 * @link       https://farazsms.com/
 * @since      2.0.0
 *
 * @package    Farazsms
 * @subpackage Farazsms/classes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_Loader.
 */
class Farazsms_Loader {


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
		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-base.php';
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-ippanel.php';

		// The class responsible for the settings page
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-settings.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-i18n.php';

		// The class responsible for defining main options of the plugin.
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-options.php';

		// The class responsible for defining REST Routs API of the plugin.
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-routes.php';

		// The class responsible for defining all actions for elementor.
		require_once FARAZSMS_MODULES_PATH . 'elementor/class-farazsms-elementor.php';

		// The class responsible for defining all actions for woocommerce.
		require_once FARAZSMS_MODULES_PATH . 'woocommerce/class-farazsms-woocommerce.php';

		// The class responsible for defining all actions for edd.
		require_once FARAZSMS_MODULES_PATH . 'edd/class-farazsms-edd.php';

		// The class responsible for defining all actions for aff.
		require_once FARAZSMS_MODULES_PATH . 'aff/class-farazsms-aff.php';

		// The class responsible for defining all actions for newsletter.
		require_once FARAZSMS_MODULES_PATH . 'newsletter/class-farazsms-newsletter.php';

		// The class responsible for defining all actions for newsletter block.
//		require_once FARAZSMS_MODULES_PATH . 'newsletter/class-farazsms-newsletter-block.php';

		// The class responsible for defining all actions for login-notify.
		require_once FARAZSMS_MODULES_PATH . 'farazsms/core/class-farazsms-login-notify.php';

		// The class responsible for defining all actions for comments.
		require_once FARAZSMS_MODULES_PATH . 'farazsms/core/class-farazsms-comments.php';

		// The class responsible for defining all actions for membership.
		require_once FARAZSMS_MODULES_PATH . 'membership/class-farazsms-membership.php';

		// The class responsible for defining all actions for gravity-forms.
		require_once FARAZSMS_MODULES_PATH . 'gravity-forms/class-farazsms-gravity-forms.php';

		// The class responsible for defining all actions for digits.
		require_once FARAZSMS_MODULES_PATH . 'digits/class-farazsms-digits.php';
	}
}

Farazsms_Loader::get_instance();


