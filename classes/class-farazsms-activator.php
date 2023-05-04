<?php

/**
 * Fired during plugin activation.
 *
 * @since      2.0.0
 */

class Farazsms_Activator {

	public static function activate() {
		global $wpdb;
		$collate                   = $wpdb->get_charset_collate();
		$newsletter_table_name     = $wpdb->prefix . 'farazsms_newsletter';
		$verify_code_table_name    = $wpdb->prefix . 'farazsms_vcode';
		$gravity_forms_table_name  = $wpdb->prefix . 'farazsms_gravity_forms';
		$tracking_codes_table_name = $wpdb->prefix . 'farazsms_tracking_codes';

		$newsletter                = "CREATE TABLE IF NOT EXISTS `$newsletter_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `name` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `phone_book` int(10) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";

		$verify_code               = "CREATE TABLE IF NOT EXISTS `$verify_code_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `phone` BIGINT(10) UNSIGNED NOT NULL ,
					 `code` int(4) DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";

		$gravity_forms             = "CREATE TABLE IF NOT EXISTS `$gravity_forms_table_name` (
					 `id` int(10) NOT NULL AUTO_INCREMENT,
					 `title` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `phonebook_id` int(10),
					 `form_id` int(10) NOT NULL,
					 `field_id` int(10) NOT NULL,
					 `name_field_id` int(10) NOT NULL,
					 `content_field_id` int(10) NOT NULL,
					 `phonebook_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `form_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `field_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `name_field_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `content_field_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `action_type` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `action_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `user_pattern_code` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 `admin_pattern_code` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
					 PRIMARY KEY (`id`)
					) $collate";

		$tracking_codes            = "CREATE TABLE IF NOT EXISTS `$tracking_codes_table_name` (
			 `id` int(10) NOT NULL AUTO_INCREMENT,
			 `order_id` mediumint(9) NOT NULL,
             `tracking_code` varchar(255) NOT NULL,
             `post_service_provider` varchar(255) NOT NULL,
             `post_date` date NOT NULL,
             PRIMARY KEY  (id)
		) $collate";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $newsletter );
		dbDelta( $verify_code );
		dbDelta( $gravity_forms );
		dbDelta( $tracking_codes );

		// Use ALTER TABLE to add new columns if they don't already exist on gravity forms table
		// TODO: Can be removed latter.
		$wpdb->query( "ALTER TABLE `$gravity_forms_table_name`
    ADD COLUMN `name_field_id` int(10) NOT NULL AFTER `field_id`,
    ADD COLUMN `name_field_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `field_label`,
    ADD COLUMN `content_field_id` int(10) NOT NULL AFTER `name_field_id`,
    ADD COLUMN `content_field_label` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `name_field_label`,
    ADD COLUMN `user_pattern_code` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `action_label`,
    ADD COLUMN `admin_pattern_code` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `user_pattern_code`
;" );

		// This option added for redirect after activation
		add_option( 'farazsms_do_activation_redirect', true );

	}

	public static function modify_option(){
		//TODO: This part of code convert $options['from_number'] to be an array.

		// Retrieve the options
		$options = json_decode( get_option('farazsms_settings_options'), true );

		// Check if the options need to be updated
		if (isset($options['from_number']) && is_string($options['from_number'])) {
			$options['from_number'] = [
				'value' => $options['from_number'],
				'label' => $options['from_number']
			];
		}

		if (isset($options['from_number_adver']) && is_string($options['from_number_adver'])) {
			$options['from_number_adver'] = [
				'value' => $options['from_number_adver'],
				'label' => $options['from_number_adver']
			];
		}
		$options_json = wp_json_encode( $options );
		// Save the updated options
		update_option('farazsms_settings_options', $options_json);
	}
}
