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
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/admin
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Network_Wide_Posts_Terms {
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
	 * View name created in the DB for collecting ntwork-wide posts.
	 *
	 * @since 1.0.0
	 * @access static
	 * @var string VIEW_POSTS_NAME view name
	 */
	const  VIEW_POSTS_NAME = "network_wide_posts";

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
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $term_type    Type of taxonomy used for network-wide terms, one of the constant value, AUTOMATIC_TAG/AUTOMATIC_CAT/SELECTED_TAX.
	 */
	protected $term_type;
	
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $term_name    Network-wide term name.
	 */
	protected $term_name;
	
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $term_slug    Network-wide term slug.
	 */
	protected $term_slug;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->term_type = self::AUTOMATIC_TAG;
		$this->term_name = __("Network-wide","network-wide-posts");
		$this->term_slug = "network-wide";
		//load the child blog terms if they exists
		$this->blog_terms = get_option($this->plugin_name."-blog-term-id", array());
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
				foreach ($blogids as $blog_id) $this->create_blog_term($blog_id);
				
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
	}
	
	/**
	 * Function to create network-wide term in a given site.
	 *
	 * This function will switch the given blog_id, create the term and then switch back to the current blog.
	 *
	 * @since    1.0.0
	 * @param    string    $blog_id       the id of the blog in which to set the therm.
	 */
	public function create_blog_term($blog_id){
		$current_blog_id = get_current_blog_id();	
		switch_to_blog($blog_id);
		switch( true ){
			case ( self::AUTOMATIC_TAG === $this->term_type ):
				$args = array(
					'description'=>'Network-wide -- DO NOT DELETE',
					'slug' => $this->term_slug
				);
				$taxonomy = 'post_tag';
				$term = get_term_by( 'slug', $this->term_slug, $taxonomy);
				if( !$term ) {
					//create tag
					$term = wp_insert_term( $this->term_name, $taxonomy, $args);
					if(is_array($term)) $term_id = $term['term_id']; 
					else $term_id = -1;
				}else $term_id=$term->term_id;
				$this->set_blog_term_id( $blog_id, $term_id);
				break;
			case (self::AUTOMATIC_CAT === $this->term_type ):
				$args = array(
					'description'=>'Network-wide -- DO NOT DELETE',
					'slug' => $this->term_slug
				);
				$taxonomy = 'category';
				$term = get_term_by( 'slug', $this->term_slug, $taxonomy);
				if( !$term ) {
					//create tag
					$term = wp_insert_term( $this->term_name, $taxonomy, $args);
					if(is_array($term)) $term_id = $term['term_id']; 
					else $term_id = -1;
				}else $term_id=$term->term_id;
				$this->set_blog_term_id( $blog_id, $term_id);
				break;
			case (self::SELECTED_TAX === $this->term_type ):
			default:
				//TODO, nothing?
				break;
		}
		//switch back to current blog
		switch_to_blog($current_blog_id); //back to where we started
		
		//let's build the necessary views for the network-side posts
		$this->create_network_wide_posts_view();
	}
	
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
	 * @var    $blog_id     The blog_id to set.
	 * @var    $term_id     The site's term id
	 */
	private function set_blog_term_id($blog_id, $term_id){
		$this->blog_terms["blog-".$blog_id]=$term_id;
	}
	/**
	 * Function to get the network-wide term id for a given site.
	 *
	 * @since    1.0.0
	 * @var       $blog_id   The site id for which to retrieve the term id.
	 * @return    int        The term id for the given site id.
	 */
	private function get_blog_term_id($blog_id){
		return $this->blog_terms["blog-".$blog_id];
	}
	
	/**
	 * Function to create the DB view of network-wide posts.
	 *
	 * @since    1.0.0
	 */
	protected function create_network_wide_posts_view(){
		global $wpdb;
		switch( true ){
			case ( self::AUTOMATIC_TAG === $this->term_type ):
			case ( self::AUTOMATIC_CAT === $this->term_type ):
				
				//view for posts wp_network_wide_posts
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				$sql_view = "CREATE OR REPLACE VIEW " . $wpdb->prefix . self::VIEW_POSTS_NAME . " AS ";
				$sql_union=' ';
				$sql_values = array();
				foreach ($blogids as $blog_id){
					$sql_view .=  $sql_union . $this->sql_blog_posts_select( $blog_id);
					$sql_union=" UNION ";
					$sql_values[]=$this->get_blog_term_id($blog_id);
				}
				$wpdb->query( $wpdb->prepare( $sql_view ,$sql_values) );
				error_log("Network-wide Posts: built view --- \n".$wpdb->last_query);
				
				//view for thumbs wp_network_wide_posts_thumbs
				$sql_view = "CREATE OR REPLACE VIEW " . $wpdb->prefix . self::VIEW_POSTS_NAME . "_thumbs AS ";
				$sql_union=' ';
				$sql_values = array();
				foreach ($blogids as $blog_id){
					$sql_view .=  $sql_union . $this->sql_blog_posts_thumbs_select( $blog_id);
					$sql_union=" UNION ";
					$sql_values[]=$blog_id;
				}
				$wpdb->query( $wpdb->prepare( $sql_view , $sql_values) );
				error_log("Network-wide Posts: built view --- \n".$wpdb->last_query);
				break;
			case (self::SELECTED_TAX === $this->term_type):
				//TODO
				//set up user selected terms
				break;
			default:
				break;
		}
	}
	
	/**
	 * Function to get the the sql select for given blog_id.
	 *
	 * @since    1.0.0
	 * @var       $blog_id  blog id for which the sql string needs to be built 
	 * @return    string     the sql select for given blog_id.
	 */
	private function sql_blog_posts_select($blog_id){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		if($blog_id>1) $table_prefix = $wpdb->prefix . $blog_id . "_";
		return "SELECT concat('".$blog_id."',ID) AS net_wide_id, post_title, post_name, post_date, post_content, ".$table_prefix."postmeta.meta_value as thumb_id, '".$blog_id."' AS blog_id 
						FROM ".$table_prefix."posts, ".$table_prefix."term_relationships, ".$table_prefix."postmeta
							WHERE ".$table_prefix."posts.post_status LIKE 'publish'
								AND ".$table_prefix."posts.ID = ".$table_prefix."term_relationships.object_id
								AND ".$table_prefix."term_relationships.term_taxonomy_id = %d
								AND ".$table_prefix."postmeta.meta_key LIKE '_thumbnail_id'
								AND ".$table_prefix."postmeta.post_id = ".$table_prefix."posts.ID";
	}
	/**
	 * Function to get the the sql select for given blog_id.
	 *
	 * @since    1.0.0
	 * @var       $blog_id  blog id for which the sql string needs to be built 
	 * @return    string     the sql select for given blog_id.
	 */
	private function sql_blog_posts_thumbs_select($blog_id){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		if($blog_id>1) $table_prefix = $wpdb->prefix . $blog_id . "_";
		return "SELECT ".$table_prefix."posts.ID AS post_id,'".$blog_id."' AS blog_id, ".$table_prefix."posts.guid AS thumb_url 
			FROM ".$table_prefix."posts, ". $wpdb->prefix . self::VIEW_POSTS_NAME . "
				WHERE ".$table_prefix."posts.ID = ". $wpdb->prefix . self::VIEW_POSTS_NAME . ".thumb_id
					AND ". $wpdb->prefix . self::VIEW_POSTS_NAME . ".blog_id= %d ";
	}
	
	public function get_network_wide_posts(){
		global $wpdb;
		$sql_query = "SELECT posts.net_wide_id, posts.post_title, posts.post_name, posts.blog_id, posts.post_content, thumbs.thumb_url 
       FROM ". $wpdb->prefix . self::VIEW_POSTS_NAME . " as posts, " . $wpdb->prefix . self::VIEW_POSTS_NAME . "_thumbs as thumbs 
        WHERE posts.thumb_id = thumbs.post_id
         AND posts.blog_id = thumbs.blog_id";
		$posts = $wpdb->get_results($sql_query);
		return $posts;
	}
	
	public function save_posts_order(){
		//wp_verify_nonce( $nonce, $action );
		
		if( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'nwp_ordering_nonce') )
		return;
	    
	    global $wpdb;
	    $order = explode(",",$_POST['order']);
	    $category = $_POST['category'];
	    
	    $table_name = $wpdb->prefix . $this->deefuse_ReOrder_tableName;
	    $total = $wpdb->get_var( $wpdb->prepare("select count(*) as total from `$table_name` where category_id = %d", $category) );
	    
	    // if category has not been sorted as yet
	    if($total == 0)
	    {
		foreach($order as $post_id) {
		    $value[] = "($category, $post_id)";
		}
		$sql = sprintf("insert into $table_name (category_id,post_id) values %s", implode(",",$value));
	        $wpdb->query($sql);
	    }
	    else
	    {
		$results = $wpdb->get_results($wpdb->prepare("select * from `$table_name` where category_id = %d order by id", $category));
		foreach($results as $index => $result_row) {
		    $result_arr[$result_row->post_id] = $result_row;
		}
		$start = 0;
		foreach($order as $post_id) {
		    $inc_row = $result_arr[$post_id];
		    $incl = 1; //$inc_row->incl; @toto
		    $row = $results[$start];
		    ++$start;
		    $id = $row->id;
		    $sql = $wpdb->prepare("update $table_name set post_id = %d,incl = %d where id = %d",$post_id, $incl, $id);
		    $wpdb->query($sql);
		}
	    }
	    
	    
	    
	    die();
	}
}
?>