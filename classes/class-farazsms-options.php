<?php

/**
 * Farazsms options.
 * Define the options for this plugin for save in DB.
 *
 * @package Farazsms
 * @since    2.0.0
 * @access   private
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class Farazsms_Options.
 */
class Farazsms_Options
{
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
	 * @since 2.0.0
	 * @return object Initialized object of class.
	 */
	public static function get_instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action( 'init', [ $this , 'register_settings_options' ] );
		add_action( 'init', [ $this , 'register_login_notify_options' ] );
		add_action( 'init', [ $this , 'register_phonebook_options' ] );
		add_action( 'init', [ $this , 'register_comments_options' ] );
		add_action( 'init', [ $this , 'register_newsletter_options' ] );
		add_action( 'init', [ $this , 'register_woocommerce_options' ] );
		add_action( 'init', [ $this , 'register_elementor_options' ] );
		add_action( 'init', [ $this , 'register_edd_options' ] );
		add_action( 'init', [ $this , 'register_aff_options' ] );
		add_action( 'init', [ $this , 'register_membership_options' ] );
		add_action( 'init', [ $this , 'register_integrations_options' ] );
	}

	/**
	 * Register settings options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_settings_options() {
		$farazsms_settings_options = '';
		add_option( 'farazsms_settings_options', $farazsms_settings_options );
	}

	/**
	 * Register login notify options
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_login_notify_options() {
		$farazsms_login_notify_options = '';
		add_option( 'farazsms_login_notify_options', $farazsms_login_notify_options );
	}

	/**
	 * Register Phonebook options
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_phonebook_options() {
		$farazsms_phonebook_options = '';
		add_option( 'farazsms_phonebook_options', $farazsms_phonebook_options );
	}

	/**
	 * Register comments options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_comments_options() {
		$farazsms_comments_options = '';
		add_option( 'farazsms_comments_options', $farazsms_comments_options );
	}

	/**
	 * Register newsletter options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_newsletter_options() {
		$farazsms_newsletter_options = '';
		add_option( 'farazsms_newsletter_options', $farazsms_newsletter_options );
	}

	/**
	 * Register WooCommerce options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_woocommerce_options() {
		$farazsms_woocommerce_options = '';
		add_option( 'farazsms_woocommerce_options', $farazsms_woocommerce_options );
	}

	/**
	 * Register Elementor options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_elementor_options() {
		$farazsms_elementor_options = '';
		add_option( 'farazsms_elementor_options', $farazsms_elementor_options );
	}

	/**
	 * Register EDD options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_edd_options() {
		$farazsms_edd_options = '';
		add_option( 'farazsms_edd_options', $farazsms_edd_options );
	}

	/**
	 * Register Aff options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_aff_options() {
		$farazsms_aff_options = '';
		add_option( 'farazsms_aff_options', $farazsms_aff_options );
	}

	/**
	 * Register Membership options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_membership_options() {
		$farazsms_membership_options = '';
		add_option( 'farazsms_membership_options', $farazsms_membership_options );
	}

	/**
	 * Register Integrations options.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register_integrations_options() {
		$farazsms_integrations_options = '';
		add_option( 'farazsms_integrations_options', $farazsms_integrations_options );
	}

}
Farazsms_Options::get_instance();
