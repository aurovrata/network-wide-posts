<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/public
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Network_Wide_Posts_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	static protected $nwp_order_type=null;

	static protected $nwp_manual_order=null;

	static protected $nwp_new_post_top=true;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		//let's load the options from the DB
		self::set_order_settings();

	}

	static private function set_order_settings(){
		if(is_null(self::$nwp_order_type))
			self::$nwp_order_type = get_option(NWP_PLUGIN_NAME."-order-type",'time');
		if(is_null(self::$nwp_manual_order))
			self::$nwp_manual_order = get_option(NWP_PLUGIN_NAME."-manual-order",array());
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Network_Wide_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Network_Wide_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/network-wide-posts-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Network_Wide_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Network_Wide_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/network-wide-posts-public.js', array( 'jquery' ), $this->version, false );

	}

	/*
	 * Function called by front-end hook init
	 *
	 */
	public function load_api(){
			/**
			* The Plugin public api
			*
			*/
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/network-wide-posts-api.php';
	}
	/**
	 * Function to retrieve the post from the view of network-wide posts.
	 *
	 * @since    1.0.0
	 */
	static public function get_network_wide_posts($args=''){
		global $wpdb;
		self::set_order_settings();
		$sql_query = "SELECT posts.nwp_id,
			posts.nwp_title,
			posts.nwp_name,
			posts.blog_id,
			posts.nwp_excerpt,
			if(isnull(thumbs.nwp_thumb_url),'', thumbs.nwp_thumb_url) as nwp_thumb_url,
			posts.nwp_thumb_id";

		if(function_exists('pll_default_language')) $sql_query.=", posts.nwp_lang "; //usage of polylang

    $sql_query.= "   FROM ". $wpdb->prefix . NWP_VIEW_POSTS_NAME . " as posts ";

		$sql_query.= "    LEFT JOIN  " . $wpdb->prefix . NWP_VIEW_POSTS_NAME . "_thumbs as thumbs ON posts.nwp_thumb_id = thumbs.nwp_thumb_id
												AND posts.blog_id = thumbs.blog_id ";

		if(isset($args['lang']) && function_exists('pll_default_language') ){
			$sql_query .= "WHERE posts.nwp_lang = '".$args['lang']."' ORDER BY ";
		}else{
			$sql_query .= "ORDER BY  ";
		}

		switch(self::$nwp_order_type){
			case 'manual':
				if(empty(self::$nwp_manual_order)){
					$sql_query .= "nwp_date DESC";
				}else{
					if(self::$nwp_new_post_top)
						$sql_query .= "FIELD(nwp_id, ". implode(",",self::$nwp_manual_order) . "), nwp_date DESC";
					else
						$sql_query .= "if(FIELD(nwp_id, ". implode(",",self::$nwp_manual_order) . ")=0,1,0), FIELD(nwp_id, ". implode(",",self::$nwp_manual_order) . "), nwp_date DESC";
				}
				break;
			case 'slug':
				$sql_query .= "nwp_name";
				break;
			case 'time':
			default:
				$sql_query .= "nwp_date DESC";
				break;
		}

		$posts = $wpdb->get_results($sql_query);
		//error_log("NWP: Found ".$wpdb->num_rows ." posts \n" . $wpdb->last_query);
		if(!isset($posts)) return array();
		return $posts;
	}
}
