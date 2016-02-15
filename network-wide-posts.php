<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://syllogic.in
 * @since             1.0.0
 * @package           Network_Wide_Posts
 *
 * @wordpress-plugin
 * Plugin Name:       Network Wide Posts
 * Plugin URI:        http://wordpress.syllogic.in
 * Description:       This is a WP Netowrk plugin which allows you to grab posts from the network blogs and display then in the main site. 
 * Version:           1.0.0
 * Author:            Aurovrata V.
 * Author URI:        http://syllogic.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       network-wide-posts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-network-wide-posts-activator.php
 */
function activate_network_wide_posts($networkwide) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-network-wide-posts-activator.php';
	Network_Wide_Posts_Activator::activate($networkwide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-network-wide-posts-deactivator.php
 */
function deactivate_network_wide_posts($networkwide) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-network-wide-posts-deactivator.php';
	Network_Wide_Posts_Deactivator::deactivate($networkwide);
}

register_activation_hook( __FILE__, 'activate_network_wide_posts' );
register_deactivation_hook( __FILE__, 'deactivate_network_wide_posts' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-network-wide-posts.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_network_wide_posts() {

	$plugin = new Network_Wide_Posts();
	$plugin->run();

}
run_network_wide_posts();
