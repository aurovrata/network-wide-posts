<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Network_Wide_Posts
 * @subpackage Network_Wide_Posts/admin/partials
 */
?>

<form id="form_result" method="post">
	<div id="result">
		<div id="sorter_box">
			<h3><?php _e('How do you wish to order your newtwork-wide posts ?','network-wide-posts');?></h3>
      <div id="catOrderedRadioBox">
    <?php
      // get_option( $option, $default )
      $order_options = get_option($this->plugin_name."-order-type",'time');
      $check_time = $check_slug = $check_manual = $list_class ="";
      switch(true){
        case ("manual" == $order_options):
          $check_manual ='checked = "checked" ';
          break;
        case ("slug" == $order_options):
          $check_slug ='checked = "checked" ';
          $list_class = "sorting-disabled";
          break;
        case ("time" == $order_options):
        default:
          $check_time ='checked = "checked" ';
          $list_class = "sorting-disabled";
          break;
      }
    ?>
    <?php  //wp_nonce_field( $action, $field_name, $show_referer_field, $echo_field )
        wp_nonce_field( 'save_nwp_ordering', 'nwp_ordering_nonce', true, true );
    ?>
        <label for="order-time">
          <input type="radio" <?php echo $check_time;?>class="option_order" id="yes" value="time" name="<?php echo $this->plugin_name."-order-type";?>"/>
          <span><?php _e('Order by published date', 'network-wide-posts');?></span>
        </label><br/>
        <label for="order-slug">
          <input type="radio" <?php echo $check_slug;?>class="option_order" id="no" value="slug" name="<?php echo $this->plugin_name."-order-type";?>"/>
          <span><?php _e('Order by post slug', 'network-wide-posts')?></span>
        </label><br/>
        <label for="order-manual">
          <input type="radio" <?php echo $check_manual;?>class="option_order" id="no" value="manual" name="<?php echo $this->plugin_name."-order-type";?>"/>
          <span><?php _e('Manual order', 'network-wide-posts')?></span>
        </label>
        <span class="spinner" id="spinner-ajax-radio"></span>
      </div>
      <h3 class="floatLeft"><?php _e('Network-wide posts:', 'network-wide-posts');?></h3>
      <span id="spinner-ajax-order" class="spinner"></span>
      <div class="clearBoth"></div>
      <ul class="headers"><li><span class="nwp-column">Post Title</span><span class="nwp-column">Site</span><span class="nwp-column">Post slug</span></li></ul>
      <ul id="sortable-list" class="order-list <?php echo $list_class;?>" >
    <?php
      // On liste les posts du tableau $posts_array pour le trie
      $alternate = 'alternate';
			$aliases = get_option($this->plugin_name . '-options-aliases');
      foreach ($posts as $post) {
      ?>
        <li id="post-<?php echo $post->nwp_id;?>" class="<?php echo $alternate?>">
          <span class="title nwp-column"><?php echo $post->nwp_title;?></span>
          <span class="blog-id nwp-column"><?php echo $aliases['site-'.$post->blog_id];?></span>
          <span class="slug nwp-column"><?php echo $post->nwp_name;?></span>
          <span class="lang nwp-column"><?php echo $post->nwp_lang;?></span>
        </li>
    <?php
        if(empty($alternate)) $alternate = 'alternate';
        else $alternate = '';
      } ?>
    
      </ul>
    </div>
  </div>
</form>
