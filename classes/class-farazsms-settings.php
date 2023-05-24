<?php

/**
 * Farazsms settings.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Farazsms_Settings {
	public static $actual_link;
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
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_styles' ] );
		add_action( 'admin_menu', [ $this, 'init_menu' ] );
		if ( Farazsms_Base::$apiKey ) {
			add_action( 'admin_bar_menu', [ $this, 'admin_bar_menu' ], 60 );
		}
		add_filter( 'plugin_action_links_' . FARAZSMS_BASE, [ $this, 'settings_link' ] );
		add_action( 'wp_dashboard_setup', [ $this, 'rss_meta_box' ] );
		add_action( 'init', [ $this, 'check_remaining_days' ] );
		self::$actual_link = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}


	/**
	 * Enqueue styles
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( 'farazsms-newsletter', FARAZSMS_URL . 'assets/css/farazsms-newsletter.css', [], FARAZSMS_VERSION, 'all' );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( 'farazsms-newsletter', FARAZSMS_URL . 'assets/js/farazsms-newsletter.js', [ 'jquery' ], FARAZSMS_VERSION, true );
		wp_localize_script(
			'farazsms-newsletter',
			'fsms_ajax_object',
			[ 'ajax_url' => admin_url( 'admin-ajax.php' ) ]
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_styles() {
		if ( self::$actual_link === FARAZSMS_SETTINGS_LINK ) {
			wp_enqueue_style( 'farazsms-style', FARAZSMS_URL . 'build/index.css', [], FARAZSMS_VERSION );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_enqueue_script( 'react-multi-date-picker', FARAZSMS_URL . 'assets/js/react-multi-date-picker.js', [
			'wp-element',
			'wp-i18n',
		], FARAZSMS_VERSION, true );
		wp_enqueue_script(
			'farazsms-script',
			FARAZSMS_URL . 'build/index.js',
			[
				'wp-element',
				'wp-i18n',
			],
			FARAZSMS_VERSION,
			true
		);

		/*
		 * Add a localization object ,The base rest api url and a security nonce
		 * @see https://since1979.dev/snippet-014-setup-axios-for-the-wordpress-rest-api/
		 * */
		wp_localize_script(
			'farazsms-script',
			'farazsmsJsObject',
			[
				'rootapiurl'        => esc_url_raw( rest_url() ),
				'nonce'             => wp_create_nonce( 'wp_rest' ),
				'wproules'          => wp_roles(),
				'username'          => Farazsms_Base::$username,
				'password'          => Farazsms_Base::$password,
				'getPhonebooks'     => Farazsms_Ippanel::get_phonebooks(),
				'getCredit'         => Farazsms_Ippanel::get_credit(),
				'getActivePlugins'  => get_option( 'active_plugins' ),
				'isDigitsInstalled' => function_exists( 'digit_ready' ),
				'settingsUrl'       => FARAZSMS_SETTINGS_LINK,
			]
		);

		// Load Farazsms languages for JavaScript files.
		wp_set_script_translations( 'farazsms-script', 'farazsms', FARAZSMS_PATH . '/languages' );

		wp_enqueue_script( 'jquery-validate', FARAZSMS_URL . 'assets/js/jquery.validate.min.js', [ 'jquery' ], FARAZSMS_VERSION, true );
		wp_enqueue_script( 'select2', FARAZSMS_URL . 'assets/js/select2.min.js', [ 'jquery-validate' ], FARAZSMS_VERSION, true );

		wp_enqueue_style( 'farazsms-tracking-code', FARAZSMS_URL . 'assets/css/farazsms-tracking-code.css', [], FARAZSMS_VERSION, 'all' );
		wp_enqueue_script( 'farazsms-tracking-code', FARAZSMS_URL . 'assets/js/farazsms-tracking-code.js', [ 'jquery-validate' ], FARAZSMS_VERSION, true );
	}

	/**
	 * Add Admin Menu.
	 *
	 * @return void
	 */
	public function init_menu() {
		add_menu_page(
			__( 'Farazsms', 'farazsms' ),
			__( 'Farazsms', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG,
			[
				$this,
				'admin_page',
			],
			'dashicons-testimonial',
			100
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Settings', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG,
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Login Notify', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/login_notify',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Phonebook', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/phonebook',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Gravity Forms', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/gravity_forms',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Synchronization', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/synchronization',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Comments', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/comments',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Woocommerce', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/woocommerce',
			[
				$this,
				'admin_page',
			]
		);
        add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
	        __( 'WooCommerce Order SMS', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/woocommerce_order_actions',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Edd', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/edd',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Newsletter', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/newsletter',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Affiliate', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/aff',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Membership', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/membership',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Send Sms', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/send_sms',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Support', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/support',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'Farazsms', 'farazsms' ),
			__( 'Integrations', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/integrations',
			[
				$this,
				'admin_page',
			]
		);
	}

	/**
	 * Init Admin Page.
	 *
	 * @return void
	 */
	public function admin_page() {
		include_once FARAZSMS_MODULES_PATH . 'farazsms/farazsms-admin-page.php';
	}

	/**
	 * Add bar menu. Show some links for farazsms plugin on the admin bar.
	 *
	 * @since 1.0.0
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar;
		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		$wp_admin_bar->add_menu(
			[
				'id'     => 'farazsms',
				'parent' => null,
				'group'  => null,
				'title'  => __( 'Farazsms', 'farazsms' ),
				'meta'   => [
					'title' => __( 'Farazsms', 'textdomain' ),
					// This title will show on hover
				],
			]
		);
		$credit = Farazsms_Ippanel::get_credit();
		if ( is_numeric($credit) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'farazsms',
					'id'     => 'farazsms-admin-bar-credit',
					'title'  => __( 'Account credit: ', 'farazsms' ) . number_format( $credit ) . __( ' $IR_T', 'farazsms' ),
					'href'   => get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=farazsms_settings',
				]
			);
		}

		$wp_admin_bar->add_menu(
			[
				'parent' => 'farazsms',
				'title'  => __( 'Send Sms', 'farazsms' ),
				'id'     => 'farazsms-admin-bar-send-sms',
				'href'   => get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=farazsms_settings#/send_sms',
			]
		);
		$wp_admin_bar->add_menu(
			[
				'parent' => 'farazsms',
				'title'  => __( 'Farazsms', 'farazsms' ),
				'id'     => 'farazsms-admin-bar-go-to-site',
				'href'   => 'https://farazsms.com/',
			]
		);
	}

	/**
	 * Plugin settings link on all plugins page.
	 *
	 * @since 2.0.0
	 */
	public function settings_link( $links ) {
		// Add settings link
		$settings_link = '<a href="' . FARAZSMS_SETTINGS_LINK . '">' . esc_html__( 'Settings', 'farazsms' ) . '</a>';

		// Add document link
		$doc_link = '<a href="' . FARAZSMS_WEB_MAIN_DOC . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Docs', 'farazsms' ) . '</a>';
		array_push( $links, $settings_link, $doc_link );

		return $links;
	}

	/**
	 * Show the latest posts from https://farazsms.com/ on dashboard widget
	 *
	 * @since 1.0.0
	 */
	public function rss_meta_box() {
		if ( get_option( 'fsms_rss_meta_box', '1' ) == '1' ) {
			add_meta_box(
				'Farazsms_Rss',
				__( 'Farazsms latest news', 'farazsms' ),
				[
					$this,
					'rss_postbox_container',
				],
				'dashboard',
				'side',
				'low'
			);
		}
	}

	public function rss_postbox_container() {
		?>
		<div class="fsms-rss-widget">
			<?php
			wp_widget_rss_output(
				'https://farazsms.com/feed/',
				[
					'items'        => 3,
					'show_summary' => 1,
					'show_author'  => 1,
					'show_date'    => 1,
				]
			);
			?>
		</div>
		<?php

	}

	/**
	 * Check remaining days.
	 */
	public function check_remaining_days() {
		$panel_expire_date = get_option( 'fsms_panel_expire_date', '' );
		if ( empty( $panel_expire_date ) ) {
			return;
		}
		$future     = strtotime( $panel_expire_date );
		$timefromdb = time();
		$timeleft   = $future - $timefromdb;
		$daysleft   = round( ( ( $timeleft / 24 ) / 60 ) / 60 );
		if ( ! is_numeric( $daysleft ) ) {
			return;
		}
		if ( $daysleft > 30 ) {
			delete_option( 'sent_low_remaining_days_30' );
			delete_option( 'sent_low_remaining_days_7' );

			return;
		}
		if ( $daysleft > 20 && $daysleft < 30 ) {
			$already_sent = get_option( 'sent_low_remaining_days_30', '' );
			if ( $already_sent === '1' ) {
				return;
			}
			$message = __( 'Dear user, your panel will expire less than a month from now. To renew your SMS panel, contact Farazsms', 'farazsms' );
			Farazsms_Ippanel::send_message( [ Farazsms_Base::$admin_number ], $message, '+98club' );
			update_option( 'sent_low_remaining_days_30', '1' );
		} elseif ( $daysleft > 1 && $daysleft < 7 ) {
			$already_sent = get_option( 'sent_low_remaining_days_7', '' );
			if ( $already_sent == '1' ) {
				return;
			}

			$message = __( 'Dear user, your panel will expire less than a week from now. To renew your SMS panel, contact Farazsms.', 'farazsms' );
			Farazsms_Ippanel::send_message( [ Farazsms_Base::$admin_number ], $message, '+98club' );
			update_option( 'sent_low_remaining_days_7', '1' );
		}
	}
}

Farazsms_Settings::get_instance();
