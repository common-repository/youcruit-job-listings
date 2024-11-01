<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://youcruit.com
 * @since             1.0.0
 * @package           YouCruitAds
 *
 * @wordpress-plugin
 * Plugin Name:       YouCruit Job Listings
 * Plugin URI:        https://youcruit.com
 * Description:       Makes it easy for candidates to find and apply to your jobs on your website.
 * Version:           1.2.20
 * Author:            YouCruit
 * Author URI:        https://youcruit.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       youcruit-job-listings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_youCruitAds() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/YouCruitAdsActivator.php';
	YouCruitAdsActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_youCruitAds() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/YouCruitAdsDeactivator.php';
	YouCruitAdsDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_youCruitAds' );
register_deactivation_hook( __FILE__, 'deactivate_youCruitAds' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/YouCruitAds.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_youCruitAds() {
	$plugin = new YouCruitAds();
	$plugin->run();
}
run_youCruitAds();
