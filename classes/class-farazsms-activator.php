<?php

/**
 * Fired during plugin activation.
 *
 * @since      2.0.0
 */

class Farazsms_Activator {

	public static function activate() {
		global $wpdb;
		$collate                = $wpdb->get_charset_collate();
		$newsletter_table_name  = $wpdb->prefix . 'farazsms_newsletter';
		$verify_code_table_name = $wpdb->prefix . 'farazsms_vcode';
		$query                  = "CREATE TABLE IF NOT EXISTS `$newsletter_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `name` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `phone_book` int(10) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";
		$query2                 = "CREATE TABLE IF NOT EXISTS `$verify_code_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `code` int(4) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );
		dbDelta( $query2 );

		// This option added for redirect after activation
		add_option( 'farazsms_do_activation_redirect', true );
	}
}
