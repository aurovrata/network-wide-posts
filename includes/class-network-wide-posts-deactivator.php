<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/includes
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Network_Wide_Posts_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate($networkwide) {
    if(!$networkwide) return;
    //let's remvove the saved options, as well as the view
    global $wpdb;
    $wpdb->query("DROP VIEW ". $wpdb->prefix . NWP_VIEW_POSTS_NAME . " , " .$wpdb->prefix . NWP_VIEW_POSTS_NAME . "_thumbs" );
    
    $current_blog_id = get_current_blog_id();
    switch_to_blog( 1 );
    
    //delete_option( $option );
    delete_option( NWP_PLUGIN_NAME . '-options' );
    delete_option( NWP_PLUGIN_NAME . '-options-aliases' );
    delete_option( NWP_PLUGIN_NAME . '-languages');
    delete_option( NWP_PLUGIN_NAME . '-blog-term-id');
		delete_option( NWP_PLUGIN_NAME . '-manual-order');
		delete_option( NWP_PLUGIN_NAME . '-order-type');
    //error_log("NWP: options deleted ");
    //switch_to_blog($current_blog_id); //back to where we started
		restore_current_blog();
	}

}
