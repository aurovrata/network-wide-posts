<?php
/*
 * API File for setting up functions accessible from themes.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/public
 */

 /*
  * Funciton to load the network posts with the Wordpress loop
  *
  * This funciton will load the post title, the post id as a composite id of the blog id
  * and the post id, the post permalink, the post thumbnail url
  *
  * @since 1.0.0
  * @param     array    $args a list of arguments to pass to parametrise the results, lang=>'en' for example
  * @return    array    an array of posts as arrarys with keys: post_id, post_title, post_content, thumb_url, blog_id, post_url
  */
function get_network_wide_posts($args=''){
  require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-network-wide-posts-public.php';
  
  if(!isset($args['lang']) && function_exists('pll_current_language') ) $args['lang'] = pll_current_language();
  
  $results = Network_Wide_Posts_Public::get_network_wide_posts($args);
  $posts = array();
  $current_blog_id = get_current_blog_id();	
		
  foreach($results as $result){
    $blog_id = $result->blog_id;
    switch_to_blog($blog_id);
    $post_id = $result->nwp_id;
    $post_id = substr( $post_id, strlen($blog_id));
    $permalink = get_permalink($post_id);
    $posts[] = array(
      "post_id"=>$result->nwp_id,
      "post_title"=>$result->nwp_title,
      "post_content"=>$result->nwp_content,
      "post_url"=>$permalink,
      "thumb_url"=>$result->nwp_thumb_url,
      "blog_id"=>$result->blog_id
    );
  }
  return $posts;
 }
 
?>