=== Network-wide posts ===
Contributors: aurovrata
Donate link: https://www.paypal.com/us/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PAENZZXUMUKSG
Tags: multisite, network, network-wide taxonomy, posts, manual order, polylang, multilingual, multi language
Requires at least: 3.5
Tested up to: 4.7.3
Stable tag: 1.1.1
Network: true
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables child-site posts on a network to be displayed in the main site.

== Description ==

This is a multisite plugin.

The plugin creates a network-wide tag in all post_tag taxonomy of each site.

Tagged posts are made available for a theme developer to display on the home site.

Furthermore, the home site admin dashboard has a new submenu Posts->Network Wide (the name of your network-wide tag) which allows the admin to manually order the network-wide posts with a drag and drop interface.

This plugin was originally designed for a client site.  The site is a multi-resort group of hotels.  Each hotel has special offers through the season.  Each special offer is promoted on the home site.  Special offers need to be pushed up to the top of the page at various moments during the season.  This plugin allows them to do so.

The plugin is compatible with the excellent [PolyLang](https://wordpress.org/plugins/polylang/) multi-language plugin.

**Main Features of this plugin**

- Allows multi-site child-blog posts authors to tag their articles as network-wide
- These child-blog posts can be manually ordered in the main blog
- It is fully compatible with the PolyLang multi-language plugin

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Navigate to your main blog admin Dashboard, and set your network-wide taxonomy in the options page Settings->Network Wide Post.
4. You can also set aliases for your child blogs to facilitate network-wide posts manual ordering process.
5. To order your posts, navigate to the Dashboard Posts->Network Wide submenu page.
6. If you have multiple languages, each language posts will appear under a separate tab.


== Frequently Asked Questions ==

= How do I display the network-wide post in a page? =

The functionality is exposed through a single function `get_network_wide_posts()`,
which returns an array of posts with a limited number of meta-fields, which are,

- `post_id` the id of the post
- `post_title` the title of the post
- `post_excerpt` the excerpt of the post
- `post_url` the permalink of the post
- `thumb_url` the thumbnail image URL of the post
- `blog_id` the id of the blog to which this post belongs to

Here is the example of how to use it in a page template
`
$nwposts = array();
if(function_exists('get_network_wide_posts')){
  $nwposts = get_network_wide_posts();
  foreach($nwposts as $post){
    ?>
<article id="post-<?php echo $post['post_id']; ?>" >
  <a class="post-thumbnail" href="<?php echo $post['post_url'];?>" >
		<img src="<?php echo $post['thumb_url'];?>" class="attachment-post-thumbnail" alt="<?php echo $post['post_title'];?>">
  </a>
	<header class="entry-header">
		<h1><?php echo $post['post_title'];?></h1>
	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php echo $post['post_excerpt'];?>
	</div><!-- .entry-content -->
  </article><!-- #post-## -->
<?php
  }
}
?>
`
== Screenshots ==

1. Shows the main blog Dashboard settings page for Network Wide posts.
If you change the name of the network-wide,the new name will be used as the sub-menu in the Posts dashboard menu.
2. The sub-menu in the Posts dashboard section.
You can order the post as per the default published date, the slug or opt for a manual order.
3. If you use the PolyLang plugin to enable multi-language content,
the plugin allows you to sort each set of language specific network-wide posts in tabs.

== Upgrade Notice ==
= 1.1.1 =
* fixed a bug which can lead to wrong results in front end
= 1.1 =
* Fixed a bug which prevents posts form loading

== Changelog ==
= 1.1.1 =
* bug fix in public class SQL query
= 1.1 =
* Fixed a bug which prevented the posts to be loaded when no feature image was set

= 1.0 =
* Initial launch of the plugin with PolyLang compatibility
