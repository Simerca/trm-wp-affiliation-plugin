<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://weebedia.com
 * @since      1.0.0
 *
 * @package    Trm_Wp_Affiliation
 * @subpackage Trm_Wp_Affiliation/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Trm_Wp_Affiliation
 * @subpackage Trm_Wp_Affiliation/includes
 * @author     Ayrton LECOUTRE <contact@weebedia.com>
 */
class Trm_Wp_Affiliation_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'trm-wp-affiliation',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
