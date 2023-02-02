<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://farazsms.com/
 * @since      1.0.7
 *
 * @package    Farazsms
 * @subpackage Farazsms/includes
 */
class Farazsms {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	public static $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	public static $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FARAZSMS_VERSION' ) ) {
			$this->version = FARAZSMS_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'farazsms';

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-base.php';

		// The class responsible for the settings page
		require_once FARAZSMS_MODULES_PATH . 'core/class-farazsms-settings.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-i18n.php';

		// The class responsible for defining main options of the plugin.
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-options.php';

		// The class responsible for defining REST Routs API of the plugin.
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-routes.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once FARAZSMS_MODULES_PATH . 'farazsms/class-farazsms-settings.php';

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

		// The class responsible for defining all actions for login-notify.
		require_once FARAZSMS_MODULES_PATH . 'core/class-farazsms-login-notify.php';

		// The class responsible for defining all actions for comments.
		require_once FARAZSMS_MODULES_PATH . 'core/class-farazsms-comments.php';

		// The class responsible for defining all actions for membership.
		require_once FARAZSMS_MODULES_PATH . 'membership/class-farazsms-membership.php';

		// The class responsible for defining all actions for gravity-forms.
		require_once FARAZSMS_MODULES_PATH . 'gravity-forms/class-farazsms-gravity-forms.php';
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version(): string {
		return $this->version;
	}
}
