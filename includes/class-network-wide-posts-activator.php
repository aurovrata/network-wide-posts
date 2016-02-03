<?php

/**
 * Fired during plugin activation
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/includes
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Network_Wide_Posts_Activator {

	/**
	 * Activate for the first blog only.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate($networkside) {
    //global $wpdb;
                 
    if (function_exists('is_multisite') && is_multisite()) {
      //$old_blog = $wpdb->blogid;
      //switch_to_blog(1);
      //switch_to_blog($old_blog);
      return;
    }else{
      exit("Network Wide Posts works only on multisites");
    }
  }
}
