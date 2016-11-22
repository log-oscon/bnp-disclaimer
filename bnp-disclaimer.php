<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       BNP Disclaimer
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress dashboard.
 * Version:           1.0.0
 * Author:            BNP
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bnp-disclaimer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in lib/Activator.php
 */
register_activation_hook( __FILE__, 'run_activation' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in lib/Deactivator.php
 */
register_deactivation_hook( __FILE__, 'run_deactivation' );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
add_action( 'plugins_loaded', 'run_plugin' );

function run_plugin() {

	// Create the plugin's settings page.
	add_action( 'admin_menu', 'bnp_disclaimer_settings_menu' );
	add_action( 'admin_init', 'bnp_disclaimer_register_settings' );

	// Add for to terms page.
	add_filter( 'the_content', 'bnp_disclaimer_form' );

	// Redirect to terms page.
	add_action( 'pre_get_posts', 'bnp_disclaimer_redirect' );

	// Handle terms form.
	add_action( 'init', 'bnp_disclaimer_accept' );
}

require_once( dirname( __FILE__ ) . '/lib/options-page.php' );

require_once( dirname( __FILE__ ) . '/lib/terms-form.php' );

require_once( dirname( __FILE__ ) . '/lib/redirect-cookie.php' );

/**
 * TODO
 * [X] - Document functions
 * [X] - Cleanup the plugin with multiple files
 * [X] - Get all post-types
 * [X] - Orderby title (ASC/DESC)
 * [X] - Group post-types
 * [X] - Bypass page load
 * [X] - Add redirect url to original request as query arg
 * [X] - Render for with filter
 * [] - Render form with shortcode
 * [X] - Return home for every user that tries to access the terms after accepting
 * [] - Add more validations to the options
 * [] - Add reset button
 * [] - Translations
 */
