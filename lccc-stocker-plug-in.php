<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.lorainccc.edu/
 * @since             1.0.0
 * @package           Lccc_Stocker_Plug_In
 *
 * @wordpress-plugin
 * Plugin Name:       LCCC Stocker Plug-in
 * Plugin URI:        http://www.lorainccc.edu/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            LCCC Web Developement Team
 * Author URI:        http://www.lorainccc.edu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lccc-stocker-plug-in
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lccc-stocker-plug-in-activator.php
 */
function activate_lccc_stocker_plug_in() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lccc-stocker-plug-in-activator.php';
	Lccc_Stocker_Plug_In_Activator::activate();
}
function lorainccc_stocker_plugin_scripts() {

	wp_enqueue_style('lccc_stocker_plug_in_style', plugin_dir_url( __FILE__ ) . 'css/lccc-stocker-plugin.css');
	
	
}
add_action ('init','lorainccc_stocker_plugin_scripts');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lccc-stocker-plug-in-deactivator.php
 */
function deactivate_lccc_stocker_plug_in() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lccc-stocker-plug-in-deactivator.php';
	Lccc_Stocker_Plug_In_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lccc_stocker_plug_in' );
register_deactivation_hook( __FILE__, 'deactivate_lccc_stocker_plug_in' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lccc-stocker-plug-in.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lccc_stocker_plug_in() {

	$plugin = new Lccc_Stocker_Plug_In();
	$plugin->run();

}
run_lccc_stocker_plug_in();
require_once( plugin_dir_path( __FILE__ ).'php/lccc_stocker_sponsor_cpt.php' );
require_once( plugin_dir_path( __FILE__ ).'php/widget-lccc-stocker-sponsor.php' );
require_once( plugin_dir_path( __FILE__ ).'php/lccc-sponsor-metabox.php' );