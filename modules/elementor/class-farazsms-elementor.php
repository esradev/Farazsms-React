<?php

/**
 * Farazsms elementor.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
use Elementor\Widgets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Farazsms_Elementor {
	private static $elementorPro;
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
	 * @return object initialized object of class.
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
			self::$elementorPro = $integrations_options['elementorPro'] ?? '';
		}
		if ( self::$elementorPro ) {
			add_action( 'elementor_pro/forms/actions/register', [ $this, 'add_new_farazsms_newsletter_form_action' ] );
		}

		add_action( 'elementor/widgets/register', [ $this, 'register_newsletter_widget' ] );
	}

	/**
	 *  Add new action
	 */
	public function add_new_farazsms_newsletter_form_action( $form_actions_registrar ) {

		include_once( __DIR__ . '/form-actions/class-farazsms-newsletter-action-after-submit.php' );

		$form_actions_registrar->register( new Farazsms_Newsletter_Action_After_Submit() );
	}

	/**
	 * Register List Widget.
	 *
	 * Include widget file and register widget class.
	 *
	 * @param Widgets_Manager $widgets_manager Elementor widgets manager.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_newsletter_widget( $widgets_manager ) {

		require_once( __DIR__ . '/widgets/class-farazsms-newsletter-widget.php' );

		$widgets_manager->register( new Farazsms_Newsletter_Widget() );

	}

}

Farazsms_Elementor::get_instance();
