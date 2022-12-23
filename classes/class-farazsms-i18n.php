<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Farazsms
 * @subpackage Farazsms/includes
 * @author     FarazSMS <info@farazsms.com>
 */
class Farazsms_i18n
{
	/**
	 * Load Farazsms Text Domain for translation..
	 * 
	 * @since  2.0.0
	 * @return void
	 */

	public function load_plugin_textdomain()
	{
		// Default languages directory for Farazsms.
		$lang_dir = dirname(FARAZSMS_BASE) . '/languages';

		//Load Farazsms languages for PHP files.
		load_plugin_textdomain('farazsms', false, $lang_dir);
	}
}
