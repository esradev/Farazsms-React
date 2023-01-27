<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    Farazsms
 * @since      1.0.7
 */
class Farazsms_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;
		$collate = $wpdb->get_charset_collate();
		$newsletter_table_name = $wpdb->prefix . 'farazsms_newsletter';
		$verify_code_table_name = $wpdb->prefix . 'farazsms_vcode';
		$query   = "CREATE TABLE IF NOT EXISTS `$newsletter_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `name` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `phone_book` int(10) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";
		$query2   = "CREATE TABLE IF NOT EXISTS `$verify_code_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `code` int(4) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($query);
		dbDelta($query2);


		//copy bookly specific files to bookly plugin directory
		if (in_array('bookly-responsive-appointment-booking-tool/main.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			copy(WP_CONTENT_DIR . '/plugins/farazsms/classes/bookly_files/SMS.php', WP_CONTENT_DIR . '/plugins/bookly-responsive-appointment-booking-tool/lib/cloud/SMS.php');
			copy(WP_CONTENT_DIR . '/plugins/farazsms/classes/bookly_files/ippanel.js', WP_CONTENT_DIR . '/plugins/bookly-responsive-appointment-booking-tool/lib/cloud/ippanel.js');
		}

		// This option added for redirect after activation
		add_option('farazsms_do_activation_redirect', true);
	}
}
