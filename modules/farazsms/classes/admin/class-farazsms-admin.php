<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Farazsms
 * @subpackage Farazsms/admin
 * @author     FarazSMS <info@farazsms.com>
 */
class Farazsms_Admin extends Farazsms_Base {

	/**
	 * The ID of this plugin.
	 *
	 * @var    string $plugin_name The ID of this plugin.
	 * @since  1.0.0
	 * @access private
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var    string $version The current version of this plugin.
	 * @since  1.0.0
	 * @access private
	 */
	private $version;




	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		parent::__construct();
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}//end __construct()


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_styles() {
		wp_enqueue_style( 'farazsms-style', FARAZSMS_URL . 'build/index.css' );

	}//end admin_enqueue_styles()


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_enqueue_script(
			'farazsms-script',
			FARAZSMS_URL . 'build/index.js',
			[
				'wp-element',
				'wp-i18n',
			],
			'1.0.0',
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
				'rootapiurl'    => esc_url_raw( rest_url() ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'wproules'      => wp_roles(),
				'getphonebooks' => self::get_phonebooks(),
				// 'digitsSync'          => $this->sync_digits(),
				// 'wooSync'             => $this->sync_woo(),
				// 'booklySync'          => $this->sync_bookly(),
				// 'checkifapikeyisvalid' => self::check_if_apikey_is_valid(self::$apiKey),
			]
		);

		// Load Farazsms languages for JavaScript files.
		wp_set_script_translations( 'farazsms-script', 'farazsms', FARAZSMS_PATH . '/languages' );

		global $post;

		wp_enqueue_script( 'jquery-validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', [ 'jquery' ], $this->version, true );
		wp_enqueue_script( 'select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', [ 'jquery-validate' ], '1.0', true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/farazsms-admin.js', [ 'select2' ], $this->version, true );
		// wp_enqueue_script( 'select2' );
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( 'shop_order' === $post->post_type ) {
				wp_enqueue_style( 'farazsms-tracking-code', plugin_dir_url( __FILE__ ) . 'css/farazsms-tracking-code.css', [], $this->version, 'all' );
				wp_enqueue_script( 'jquery-validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', [ 'jquery' ], $this->version, true );
				wp_enqueue_script( 'farazsms-tracking-code', plugin_dir_url( __FILE__ ) . 'js/farazsms-tracking-code.js', [ 'jquery-validate' ], $this->version, true );
			}
		}

	}//end admin_enqueue_scripts()


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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
			__( 'PhoneBook', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/phonebook',
			[
				$this,
				'admin_page',
			]
		);
		add_submenu_page(
			FARAZSMS_SLUG,
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
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
			__( 'FarazSMS', 'farazsms' ),
			__( 'Integrations', 'farazsms' ),
			'manage_options',
			FARAZSMS_SLUG . '#/integrations',
			[
				$this,
				'admin_page',
			]
		);

	}//end init_menu()


	/**
	 * Init Admin Page.
	 *
	 * @return void
	 */
	public function admin_page() {
		include_once FARAZSMS_MODULES_PATH . 'farazsms/includes/farazsms-admin.php';

	}//end admin_page()


	/**
	 * Add bar menu. Show some links for farazsms plugin on the admin bar.
	 *
	 * @since 1.0.0
	 */


	public function admin_bar_menu() {
		$fsms_base = Farazsms_Base::get_instance();
		global $wp_admin_bar;
		if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		$wp_admin_bar->add_menu(
			[
				'id'     => 'farazsms',
				'parent' => null,
				'group'  => null,
				'title'  => __( 'FarazSms ', 'farazsms' ),
				'meta'   => [
					'title' => __( 'FarazSMS', 'textdomain' ),
					// This title will show on hover
				],
			]
		);
		$credit = $fsms_base->get_credit();
		if ( $credit ) {
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
				'href'   => get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=farazsms_settings#/sendsms',
			]
		);
		$wp_admin_bar->add_menu(
			[
				'parent' => 'farazsms',
				'title'  => __( 'FarazSMS', 'farazsms' ),
				'id'     => 'farazsms-admin-bar-go-to-site',
				'href'   => 'https://farazsms.com/',
			]
		);

	}//end admin_bar_menu()


	/**
	 * Plugin settings link on all plugins page.
	 *
	 * @since 2.0.0
	 */
	public function settings_link( $links ) {
		// Add settings link
		$settings_link = '<a href="' . FARAZSMS_SETTINGS_LINK . '">Settings</a>';

		// Add document link
		$doc_link = '<a href="' . FARAZSMS_WEB_MAIN_DOC . '" target="_blank" rel="noopener noreferrer">Docs</a>';
		array_push( $links, $settings_link, $doc_link );

		return $links;

	}//end settings_link()


	/**
	 * Show the latest posts from https://farazsms.com/ on dashboard widget
	 *
	 * @since 1.0.0
	 */
	public function rss_meta_box() {
		if ( get_option( 'fsms_rss_meta_box', '1' ) == '1' ) {
			add_meta_box(
				'FarazSMS_RSS',
				__( 'FarazSMS latest news', 'farazsms' ),
				[
					$this,
					'rss_postbox_container',
				],
				'dashboard',
				'side',
				'low'
			);
		}

	}//end rss_meta_box()


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

	}//end rss_postbox_container()


	/**
	 * Comments table columns.
	 *
	 * @since 1.0.0
	 */
	public function comments_fsms_table_columns( $my_cols ) {
		$temp_columns = [
			'mobile' => __( 'Phone number', 'farazsms' ),
		];

		return ( array_slice( $my_cols, 0, 3, true ) + $temp_columns + array_slice( $my_cols, 3, null, true ) );

	}//end comments_fsms_table_columns()


	public function comments_fsms_table_columns_content( $column, $comment_ID ) {
		global $comment;
		switch ( $column ) :
			case 'mobile':
			{
				echo get_comment_meta( $comment_ID, 'mobile', true );
				break;
			}
		endswitch;

	}//end comments_fsms_table_columns_content()


	/**
	 * Send SMS to phonebooks.
	 *
	 * @since 1.0.0
	 */
	public function ajax_send_message_to_phonebooks() {
		$fsms_base           = Farazsms_Base::get_instance();
		$message             = ( $_POST['message'] ?? '' );
		$phonebooks          = ( $_POST['phonebooks'] ?? [] );
		$send_to_subscribers = ( $_POST['send_to_subscribers'] ?? '' );
		$send_formnum_choice = ( $_POST['send_formnum_choice'] ?? '' );
		if ( $send_formnum_choice == '2' && ! strpos( $_POST['phones'], ',' ) ) {
			wp_send_json_error( __( 'Please enter manual numbers in the correct format', 'farazsms' ) );
		}

		if ( $send_formnum_choice == '1' ) {
			$send_formnum_choice = $fsms_base->get_service_sender_number();
		} else {
			$send_formnum_choice = $fsms_base->get_adver_sender_number();
		}

		$phones = explode( ',', ( $_POST['phones'] ?? '' ) );
		foreach ( $phones as $phone ) {
			if ( $fsms_base::validate_mobile_number( $phone ) ) {
				$fixed_phones[] = $fsms_base::validate_mobile_number( $phone );
			}
		}

		if ( empty( $phonebooks ) && empty( $fixed_phones ) && $send_to_subscribers == 'false' ) {
			wp_send_json_error( __( 'Please select at least one phonebook or manual number or newsletter members', 'farazsms' ) );
			wp_die();
		}

		if ( ! empty( $fixed_phones ) ) {
			$fsms_base->send_message( $fixed_phones, $message, $send_formnum_choice );
		}

		foreach ( $phonebooks as $phonebook ) {
			$phonebook_numbers = $fsms_base->get_phonebook_numbers( $phonebook );
			$fsms_base->send_message( $phonebook_numbers, $message, $send_formnum_choice );
		}

		wp_send_json_success();

	}//end ajax_send_message_to_phonebooks()


	/**
	 * Delete user from subscribers.
	 *
	 * @since 1.0.0
	 */
	public function fsms_delete_user_from_subscribers() {
		$fsms_base     = Farazsms_Base::get_instance();
		$subscriber_id = ( $_POST['subscriber_id'] ?? '' );
		$fsms_base::delete_subscriber( $subscriber_id );
		wp_send_json_success();

	}//end fsms_delete_user_from_subscribers()


	/**
	 * Send message to subscribers of newsletter.
	 *
	 * @since 1.0.0
	 */
	public function send_message_to_subscribers() {
		$fsms_base   = Farazsms_Base::get_instance();
		$message     = ( $_POST['message'] ?? '' );
		$subscribers = $fsms_base::get_subscribers();
		if ( ! $fsms_base::isAPIKeyEntered() ) {
			wp_send_json_error( __( 'Please enter the api key first in the Settings tab.', 'farazsms' ) );
		}

		if ( empty( $subscribers ) ) {
			wp_send_json_error( __( 'No one is a subscriber of the newsletter yet', 'farazsms' ) );
		}

		if ( str_contains( $message, '%name%' ) ) {
			foreach ( $subscribers as $subscriber ) {
				$message_fixed = str_replace( '%name%', $subscriber->name, $message );
				$fsms_base->send_message( [ $subscriber->phone ], $message_fixed, '+98club' );
			}
		} else {
			$phones = [];
			foreach ( $subscribers as $subscriber ) {
				$phones[] = $subscriber->phone;
			}

			$fsms_base->send_message( $phones, $message, '+98club' );
		}

		wp_send_json_success();

	}//end send_message_to_subscribers()


}//end class

