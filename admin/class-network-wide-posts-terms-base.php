<?php

/**
 * The file that defines all the core server side action
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/admin
 */

/**
 * The Network-wide taxonomy terms used to identify the posts we want on the main site.
 *
 * This is an abstract class.  Implimentation of this class determines the functionality
 * depending on the configuration of the site.
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/admin
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
abstract class Network_Wide_Posts_Terms {
	/**
	 * Set of constants to track admin user's perference for retriving posts from child sites.
	 *
	 * @since 1.0.0
	 * @access static
	 * @var string AUTOMATIC_TAG sets value for automatic creation and usage of tags in child sites
	 */
	const AUTOMATIC_TAG = "automatic-tag";
	/**
	 * Set of constants to track admin user's perference for retriving posts from child sites.
	 *
	 * @since 1.0.0
	 * @access static
	 * @var string AUTOMATIC_CAT sets value for automatic creation and usage of categories in child sites
	 */
	const AUTOMATIC_CAT = "automatic_cat";
	/**
	 * Set of constants to track admin user's perference for retriving posts from child sites.
	 *
	 * @since 1.0.0
	 * @access static
	 * @var string SELECTED_TAX users selected set of tags/categories from child sites
	 */
	const SELECTED_TAX = "user-selected";

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	
	/**
	 * Cache the network-wide term IDs in each blog
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $blog_terms    blog_is=>term_id key, value pair
	 */
	protected $blog_terms;
	
	/**
	 * The network-wide taxonomy type selected.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $term_type    Type of taxonomy used for network-wide terms, one of the constant value, AUTOMATIC_TAG/AUTOMATIC_CAT/SELECTED_TAX.
	 */
	protected $term_type;
	
	/**
	 * The network-wide taxonomy term name.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $term_name    Network-wide term name.
	 */
	protected $term_name;
	
	/**
	 * The network-wide taxonomy term slug.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $term_slug    Network-wide term slug.
	 */
	protected $term_slug;
	
	/**
	 * The network-wide posts order requested.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $nwp_order_type    Network-wide posts order type.
	 */
	protected $nwp_order_type;
	
	/**
	 * The network-wide posts manual order.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $nwp_manual_order    Network-wide posts manual order.
	 */
	protected $nwp_manual_order;
	
	/**
	 *  Should network-wide posts not in manual order appear on the top or bottom.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      boolean    $nwp_new_post_top    Set to true by default.
	 */
	protected $nwp_new_post_top=true;

  /**
   * Initiliase the object.
   *
   * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	protected function initialise($plugin_name,$version){
    $this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->term_type = self::AUTOMATIC_TAG;
    
    //retrieve some options
		$plugin_options = get_option( $this->plugin_name . '-options', array() );
    if( isset($plugin_options['term-slug'] ) && isset($plugin_options['term-name'] )){
      $this->term_name = $plugin_options['term-name'];
      $this->term_slug = $plugin_options['term-slug'];
    }else{
      $this->term_name = __("Network-wide","network-wide-posts");
      $this->term_slug = "network-wide";
    }
		 
		//load the child blog terms if they exists
		$this->blog_terms = get_option($this->plugin_name."-blog-term-id", array());
		$this->nwp_manual_order = get_option($this->plugin_name."-manual-order",array());
		$this->nwp_order_type = get_option($this->plugin_name."-order-type",'time');
    
    //initialise the blog aliases
    $aliases = get_option( $this->plugin_name . '-options-aliases', array() );
    if(empty($aliases)){
      global $wpdb;
  		$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
      $aliases = array();
			foreach ($blogids as $blog_id) $aliases['site-'.$blog_id] = 'blog('.$blog_id.')';
      
      update_option($this->plugin_name . '-options-aliases',$aliases);
    }
  }
	
	/**
	 * Function to set network-wide terms used to retrieve posts.
	 *
	 * @since    1.0.0
	 * @param      string    $terms       Array of term_slug=>term_name values.
	 */
	public function set_network_wide_terms($network_tax, $terms){
		global $wpdb;
		$blog_terms = array();
		$term_id='';
		$this->term_type = $network_tax;
    
		switch( true ){
			case ( self::AUTOMATIC_TAG === $network_tax ):
			case ( self::AUTOMATIC_CAT === $network_tax ):
				$this->term_name = current($terms);
				$this->term_slug = key($terms);
        
				// Get all blog ids
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) $this->create_blog_term($blog_id, $this->term_name, $this->term_slug , 'Network-wide -- DO NOT DELETE');
				
				break;
			case (self::SELECTED_TAX === $network_tax):
				//TODO
				//set up user selected terms
				break;
			default:
				break;
		}
		
		//keep record of blog term id in options
		update_option($this->plugin_name."-blog-term-id",$this->blog_terms);
    
    //let's build the necessary views for the network-side posts
    $this->create_network_wide_posts_view();
	}
	
	/**
	 * Function to create network-wide term in a given site.
	 *
	 * This function will switch the given blog_id, create the term and then switch back to the current blog.
	 *
	 * @since    1.0.0
	 * @param    string    $blog_id       the id of the blog in which to set the term.
	 */
	
	public function initialise_new_blog($blog_id){
    create_blog_term($blog_id, $this->term_name, $this->term_slug , 'Network-wide -- DO NOT DELETE');
    //let's build the necessary views for the network-side posts
		$this->create_network_wide_posts_view();
  }
  /**
	 * Function to create network-wide term in a given site.
	 *
	 * This function will switch the given blog_id, create the term and then switch back to the current blog.
	 *
	 * @since    1.0.0
	 * @param    string    $blog_id          the id of the blog in which to set the term.
	 * @param    string    $term_name        the term name
	 * @param    string    $term_slug        the term slug
	 * @param    string    $term_description the description of the term
	 */
  abstract protected function create_blog_term($blog_id, $term_name, $term_slug  , $term_description='Network-wide -- DO NOT DELETE');
	
	/**
	 * Function to get the network-wide term name.
	 *
	 * @since    1.0.0
	 * @return    string     The name of the network-wide term.
	 */
	public function get_term_name(){
		return $this->term_name;
	}
	
	/**
	 * Function to get the network-wide term slug.
	 *
	 * @since    1.0.0
	 * @return    string     The slug of the network-wide term.
	 */
	public function get_term_slug(){
		return $this->term_slug;
	}
	/**
	 * Function to set a site's network-wide term id.
	 *
	 * @since    1.0.0
	 * @param    int    $blog_id     The blog_id to set.
	 * @param    mixed  $term_id     The site's term id, or an array if the site has multiple languages using taxonomy for languages
	 */
	protected function set_blog_term_id($blog_id, $term_id){
		$this->blog_terms["blog-".$blog_id]=$term_id;
	}
	/**
	 * Function to get the network-wide term id for a given site.
	 *
	 * @since    1.0.0
	 * @param    int  $blog_id   The site id for which to retrieve the term id.
	 * @return   int        The term id for the given site id.
	 */
	protected function get_blog_term_id($blog_id){
		return $this->blog_terms["blog-".$blog_id];
	}
	
	/**
	 * Function to create the DB view of network-wide posts.
	 *
	 * @since    1.0.0
	 */
	abstract protected function create_network_wide_posts_view();
  
  /**
	 * Function to retrieve the post from the view of network-wide posts.
	 *
	 * @since    1.0.0
	 * @param    array    $args     optional array of parameters to parse for selecting posts
	 * @return   array    an array of results form the $wpdb->get_results() function
	 */
  abstract public function get_network_wide_posts($args='');
	
	/**
	 * Function to get the the sql select for given blog_id.
	 *
	 * @since    1.0.0
	 * @param     int   $blog_id  blog id for which the sql string needs to be built
	 * * @param   mixed   $args  additional parameter for languages or other settings 
	 * @return    string     the sql select for given blog_id.
	 */
	abstract protected function sql_blog_posts_select($blog_id, $args=null);
	/**
	 * Function to get the the sql select for given blog_id.
	 *
	 * @since    1.0.0
	 * @var       $blog_id  blog id for which the sql string needs to be built 
	 * @return    string     the sql select for given blog_id.
	 */
	protected function sql_blog_posts_thumbs_select($blog_id){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		if($blog_id>1) $table_prefix = $wpdb->prefix . $blog_id . "_";
		return "SELECT ".$table_prefix."posts.ID AS nwp_thumb_id,'".$blog_id."' AS blog_id, ".$table_prefix."posts.guid AS nwp_thumb_url 
			FROM ".$table_prefix."posts, ". $wpdb->prefix . NWP_VIEW_POSTS_NAME . "
				WHERE ".$table_prefix."posts.ID = ". $wpdb->prefix . NWP_VIEW_POSTS_NAME . ".nwp_thumb_id
					AND ". $wpdb->prefix . NWP_VIEW_POSTS_NAME . ".blog_id= %d ";
	}
	
	public function save_posts_order(){
		
		//wp_verify_nonce( $nonce, $action );
		if( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'save_nwp_ordering') ){
			return;
		}
	  switch(true){
			case isset($_POST['nwp_order_type']): //save as option
				update_option($this->plugin_name."-order-type",$_POST['nwp_order_type']);
				if('manual'!=$_POST['nwp_order_type']){
					$this->nwp_manual_order = array(); //clear any saved manual order
					update_option($this->plugin_name."-manual-order",$this->nwp_manual_order);  
				}
				$this->nwp_order_type = $_POST['nwp_order_type'];
				error_log("NWP Order type: ".$this->nwp_order_type);
				break;
			case isset($_POST['nwp_list_order']): //save as option and load in view table
				global $wpdb;
				$ordered_list = explode(",",$_POST['nwp_list_order']);
				error_log("NWP: Saving Order ,\n".print_r($ordered_list,true));
				$arr = array();
				foreach($ordered_list as $post){
					$split = explode("-",$post);
					$arr[] = $split[1];
				}
				$this->nwp_manual_order = $arr;
				update_option($this->plugin_name."-manual-order",$this->nwp_manual_order);
				break;
			default:
				break;
		}
		die();
	}
}
?>