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

	public static $woocommerce;
	public static $elementorPro;
	public static $digits;
	public static $edd;
	public static $bookly;
	public static $gravityForms;
	public static $indeedMembershipPro;
	public static $paidMembershipsPro;
	public static $affiliateWp;
	public static $indeedAffiliatePro;
	public static $yithWoocommerceAffiliates;

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
		$integrations_options = json_decode( get_option( 'farazsms_integrations_options' ), true );
		if ( $integrations_options ) {
			self::$woocommerce               = $integrations_options['woocommerce'] ?? '';
			self::$elementorPro              = $integrations_options['elementorPro'] ?? '';
			self::$digits                    = $integrations_options['digits'] ?? '';
			self::$edd                       = $integrations_options['edd'] ?? '';
			self::$bookly                    = $integrations_options['bookly'] ?? '';
			self::$gravityForms              = $integrations_options['gravityForms'] ?? '';
			self::$indeedMembershipPro       = $integrations_options['indeedMembershipPro '] ?? '';
			self::$paidMembershipsPro        = $integrations_options['paidMembershipsPro  '] ?? '';
			self::$affiliateWp               = $integrations_options['affiliateWp'] ?? '';
			self::$indeedAffiliatePro        = $integrations_options['indeedAffiliatePro'] ?? '';
			self::$yithWoocommerceAffiliates = $integrations_options['yithWoocommerceAffiliates'] ?? '';
		}
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
		if ( self::$woocommerce ) {
			require_once FARAZSMS_MODULES_PATH . 'woocommerce/class-farazsms-woocommerce.php';
			require_once FARAZSMS_MODULES_PATH . 'woocommerce/order-review/farazsms-order-review.php';
			require_once FARAZSMS_MODULES_PATH . 'woocommerce/order-actions/farazsms-order-actions.php';
		}

		// The class responsible for defining all actions for edd.
		if ( self::$edd ) {
			require_once FARAZSMS_MODULES_PATH . 'edd/class-farazsms-edd.php';
		}

		// The class responsible for defining all actions for aff.
		if ( self::$indeedAffiliatePro || self::$affiliateWp || self::$yithWoocommerceAffiliates ) {
			require_once FARAZSMS_MODULES_PATH . 'aff/class-farazsms-aff.php';
		}

		// The class responsible for defining all actions for newsletter.
		require_once FARAZSMS_MODULES_PATH . 'newsletter/class-farazsms-newsletter.php';

		// The class responsible for defining all actions for newsletter block.
//		require_once FARAZSMS_MODULES_PATH . 'newsletter/class-farazsms-newsletter-block.php';

		// The class responsible for defining all actions for login-notify.
		require_once FARAZSMS_MODULES_PATH . 'farazsms/core/class-farazsms-login-notify.php';

		// The class responsible for defining all actions for comments.
		require_once FARAZSMS_MODULES_PATH . 'farazsms/core/class-farazsms-comments.php';

		// The class responsible for defining all actions for membership.
		if ( self::$paidMembershipsPro || self::$indeedMembershipPro ) {
			require_once FARAZSMS_MODULES_PATH . 'membership/class-farazsms-membership.php';
		}

		// The class responsible for defining all actions for gravity-forms.
		if ( self::$gravityForms ) {
			require_once FARAZSMS_MODULES_PATH . 'gravity-forms/class-farazsms-gravity-forms.php';
		}
		// The class responsible for defining all actions for digits.
		if ( self::$digits ) {
			require_once FARAZSMS_MODULES_PATH . 'digits/class-farazsms-digits.php';
		}
	}

}

Farazsms_Loader::get_instance();


