<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.lorainccc.edu/
 * @since      1.0.0
 *
 * @package    Lccc_Stocker_Plug_In
 * @subpackage Lccc_Stocker_Plug_In/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Lccc_Stocker_Plug_In
 * @subpackage Lccc_Stocker_Plug_In/includes
 * @author     LCCC Web Developement Team <info@lorainccc.edu>
 */
class Lccc_Stocker_Plug_In_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'lccc-stocker-plug-in',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
