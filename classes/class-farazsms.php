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
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Farazsms_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

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
		$this->set_locale();
		$this->set_options();
		$this->set_routes();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Farazsms_Loader. Orchestrates the hooks of the plugin.
	 * - Farazsms_i18n. Defines internationalization functionality.
	 * - Farazsms_Admin. Defines all hooks for the admin area.
	 * - Farazsms_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-base.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-i18n.php';

		/**
		 * The class responsible for defining main options
		 * of the plugin.
		 */
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-options.php';

		/**
		 * The class responsible for defining REST Routs API
		 * of the plugin.
		 */
		require_once FARAZSMS_CLASSES_PATH . 'class-farazsms-routes.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once FARAZSMS_MODULES_PATH . 'farazsms/classes/admin/class-farazsms-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once FARAZSMS_MODULES_PATH . 'farazsms/classes/public/class-farazsms-public.php';

		/**
		 * The class responsible for defining all actions for elementor.
		 *
		 * @since 2.0.0
		 */
		require_once FARAZSMS_MODULES_PATH . 'elementor/class-farazsms-elementor.php';

		/**
		 * The class responsible for defining all actions for woocommerce.
		 *
		 * @since 2.0.0
		 */
		require_once FARAZSMS_MODULES_PATH . 'woocommerce/class-farazsms-woocommerce.php';


		$this->loader = new Farazsms_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Farazsms_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Farazsms_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Define the options for this plugin for save in DB.
	 *
	 * Uses the Farazsms_Options class in order to set the options and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_options() {

		$plugin_options = new Farazsms_Options();

		$this->loader->add_action( 'init', $plugin_options, 'register_settings_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_login_notify_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_phonebook_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_comments_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_newsletter_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_woocommerce_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_edd_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_aff_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_membership_options' );
		$this->loader->add_action( 'init', $plugin_options, 'register_integrations_options' );
	}

	/**
	 * Define the routes for this plugin for enable REST Routs for API.
	 *
	 * Uses the Farazsms_Routes class in order to set the routes and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_routes() {

		$plugin_routes = new Farazsms_Routes();

		$this->loader->add_action( 'rest_api_init', $plugin_routes, 'register_routes' );
	}

	/**
	 * Register all the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Farazsms_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_styles' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'init_menu' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'admin_bar_menu', 60 );
		$this->loader->add_filter( 'plugin_action_links_' . FARAZSMS_BASE, $plugin_admin, 'settings_link' );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'rss_meta_box' );

		$this->loader->add_action( 'wp_ajax_fsms_save_settings', $plugin_admin, 'ajax_save_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_save_settings', $plugin_admin, 'ajax_save_settings' );

		$this->loader->add_action( 'wp_ajax_fsms_save_phone_book_settings', $plugin_admin, 'ajax_save_phone_book_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_save_phone_book_settings', $plugin_admin, 'ajax_phone_book_save_settings' );

		$this->loader->add_action( 'wp_ajax_fsms_send_message_to_phonebooks', $plugin_admin, 'ajax_send_message_to_phonebooks' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_send_message_to_phonebooks', $plugin_admin, 'ajax_send_message_to_phonebooks' );

		$this->loader->add_action( 'wp_ajax_fsms_sync_operation', $plugin_admin, 'ajax_sync_operate' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_sync_operation', $plugin_admin, 'ajax_sync_operate' );

		$this->loader->add_action( 'wp_ajax_fsms_save_comment_settings', $plugin_admin, 'ajax_save_comment_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_save_comment_settings', $plugin_admin, 'ajax_save_comment_settings' );

		$this->loader->add_action( 'manage_edit-comments_columns', $plugin_admin, 'comments_fsms_table_columns' );
		$this->loader->add_action( 'manage_comments_custom_column', $plugin_admin, 'comments_fsms_table_columns_content', 10, 2 );

		$this->loader->add_action( 'wp_ajax_fsms_save_edd_settings', $plugin_admin, 'ajax_save_edd_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_save_edd_settings', $plugin_admin, 'ajax_save_edd_settings' );

		$this->loader->add_action( 'wp_ajax_fsms_save_woo_settings', $plugin_admin, 'ajax_save_woo_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_save_woo_settings', $plugin_admin, 'ajax_save_woo_settings' );

		$this->loader->add_action( 'wp_ajax_fsms_send_message_to_subscribers', $plugin_admin, 'send_message_to_subscribers' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_send_message_to_subscribers', $plugin_admin, 'send_message_to_subscribers' );

		$this->loader->add_action( 'wp_ajax_fsms_async_add_phone', $plugin_admin, 'fsms_async_add_phone' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_async_add_phone', $plugin_admin, 'fsms_async_add_phone' );


		$this->loader->add_action( 'wp_ajax_fsms_save_newsletter_settings', $plugin_admin, 'fsms_save_newsletter_settings' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_save_newsletter_settings', $plugin_admin, 'fsms_save_newsletter_settings' );

		$this->loader->add_action( 'wp_ajax_fsms_delete_user_from_subscribers', $plugin_admin, 'fsms_delete_user_from_subscribers' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_delete_user_from_subscribers', $plugin_admin, 'fsms_delete_user_from_subscribers' );

			}

	/**
	 * Register all the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Farazsms_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'update_user_metadata', $plugin_public, 'monitor_update_user_metadata', 499, 4 );
		$this->loader->add_action( 'profile_update', $plugin_public, 'fsms_user_profile_updated', 99, 2 );
		$this->loader->add_action( 'comment_form_logged_in_after', $plugin_public, 'add_mobile_field_to_comment_form' );
		$this->loader->add_action( 'comment_form_after_fields', $plugin_public, 'add_mobile_field_to_comment_form' );
		$this->loader->add_action( 'preprocess_comment', $plugin_public, 'verify_comment_input' );
		$this->loader->add_action( 'comment_post', $plugin_public, 'save_mobile_field' );
		$this->loader->add_action( 'edd_payment_personal_details_list', $plugin_public, 'fsms_edd_show_phone', 10, 2 );
		$this->loader->add_action( 'edd_complete_purchase', $plugin_public, 'fsms_edd_complete_purchase_action', 10, 3 );
		$this->loader->add_action( 'edd_purchase_form_user_info_fields', $plugin_public, 'fsms_show_mobile_field_checkout_field' );
		$this->loader->add_action( 'edd_payment_meta', $plugin_public, 'fsms_show_mobile_meta' );
		$this->loader->add_action( 'edd_checkout_error_checks', $plugin_public, 'fsms_validate_mobile_field', 10, 2 );

		$this->loader->add_action( 'gform_entry_created', $plugin_public, 'fsms_club_gform_post_update_entry' );
		$this->loader->add_action( 'gform_pre_submission', $plugin_public, 'fsms_gf_pre_submission' );

		$this->loader->add_action( 'wp_ajax_fsms_newsletter_send_verification_code', $plugin_public, 'fsms_newsletter_send_verification_code' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_newsletter_send_verification_code', $plugin_public, 'fsms_newsletter_send_verification_code' );

		$this->loader->add_action( 'wp_ajax_fsms_add_phone_to_newsletter', $plugin_public, 'fsms_add_phone_to_newsletter' );
		$this->loader->add_action( 'wp_ajax_nopriv_fsms_add_phone_to_newsletter', $plugin_public, 'fsms_add_phone_to_newsletter' );

		$this->loader->add_action( 'publish_post', $plugin_public, 'fsms_publish_post_notification' );

		$this->loader->add_action( 'wp_login', $plugin_public, 'fsms_admin_login_action', 10, 2 );
		$this->loader->add_action( 'wp_login', $plugin_public, 'fsms_admin_roles_login_action', 11, 2 );

		$this->loader->add_filter( 'ihc_filter_notification_before_expire', $plugin_public, 'fsms_first_notification_before_expire', 10, 4 );

		$this->loader->add_action( 'affwp_register_user', $plugin_public, 'fsms_affwp_register_user', 10, 3 );
		$this->loader->add_action( 'yith_wcaf_new_affiliate', $plugin_public, 'fsms_yith_wcaf_register_user' );
		$this->loader->add_action( 'uap_on_register_action', $plugin_public, 'fsms_uap_register_user' );

		$this->loader->add_action( 'affwp_set_affiliate_status', $plugin_public, 'fsms_affwp_set_affiliate_status', 10, 3 );
		$this->loader->add_action( 'affwp_referral_accepted', $plugin_public, 'fsms_affwp_referral_accepted', 10, 2 );
		$this->loader->add_action( 'yith_wcaf_affiliate_enabled', $plugin_public, 'fsms_yith_wcaf_set_affiliate_status' );

		$this->loader->add_action( 'affwp_register_fields_before_tos', $plugin_public, 'fsms_affwp_register_fields_before_tos' );
		$this->loader->add_action( 'affwp_new_affiliate_end', $plugin_public, 'fsms_affwp_new_affiliate_end' );
		$this->loader->add_action( 'affwp_edit_affiliate_end', $plugin_public, 'fsms_affwp_edit_affiliate_end' );
		$this->loader->add_action( 'affwp_update_affiliate', $plugin_public, 'fsms_affwp_update_affiliate' );

		$this->loader->add_action( 'transition_post_status', $plugin_public, 'fsms_product_published', 10, 3 );

		//$this->loader->add_action( 'user_register', $plugin_public, 'fsms_user_created_action', 99);
		//$this->loader->add_filter( 'digits_filter_mobile', $plugin_public, 'fsms_digits_filter_mobile', 99);
		//temp
		$this->loader->add_action( 'init', $plugin_public, 'fsms_check_remaining_days' );
		$this->loader->add_action( 'pmpro_membership_post_membership_expiry', $plugin_public, 'fsms_pmp_membership_membership_expiry', 10, 2 );
		$this->loader->add_action( 'init', $plugin_public, 'temp_init_kook' );
	}

	/**
	 * Run the loader to execute all the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Farazsms_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader(): Farazsms_Loader {
		return $this->loader;
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
