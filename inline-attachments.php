<?php
/*
	Plugin Name: Inline Attachments
	Plugin URI: http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/
	Description: Add a Meta Box containing the Media Panel inside the edit screen. Also adjust wich options should be displayed for attachments (e.g. "Insert Image", "Image Size", "Alignment")
	Version: 0.9.4
	Author: Nonverbla
	Author URI: http://www.nonverbla.de/
	License: GPL
	
	Plugin: Copyright 2011 Nonverbla  (email : rasso@nonverbla.de)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/


$inline_attachments = new Inline_attachments();

class Inline_attachments {
	
	var $path;
	var $dir;
	
	
	function Inline_attachments(){
		// Path of plugin on server
		$this->path = dirname(__FILE__).'';
		// URL of plugin directory
		$this->dir = plugins_url('',__FILE__);
		
		// actions and hooks
		if(is_admin()) {
			// Adding the Meta Box
			add_action('admin_init', array($this, 'add_inline_attachments_meta_box'));
			add_action("init", array($this, "inline_attachments_localize"));
			// Adding the Settings Link to the Admin Menu
			add_action( 'admin_menu', array($this,'adminMenu'));
			// Settings Link in Plugin Screen
			add_filter('plugin_action_links', array($this,'add_settings_link'), 10, 2 );
			// Message after Plugin Activation
			register_activation_hook(__FILE__, array($this, 'inline_attachments_activation'));
			add_action('admin_head', array($this, 'inline_attachments_activation_message'));
			
			if(in_array($GLOBALS['pagenow'], array('media-upload.php', 'media.php'))){
				// if this is the media screen
				add_action('init', array($this,'add_media_screen_js'));
				add_action('admin_head', array($this,'add_media_screen_css'));
				add_action('admin_head', array($this,'javascript_gallery_link'));
				// Bulk Delete
				if($this->check_if_bulk_delete_enabled()) {
					add_action('admin_head', array($this, "add_attachments_bulk_delete"));
					add_action('init', array($this,'add_attachments_bulk_delete_js'));
				}
			} elseif(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php'))){
				// if this is the post edit screen
				add_action('init', array($this,'add_post_screen_js'));
				add_action('admin_head', array($this,'add_post_screen_css'));
			}
		}
		return true;
	}
	
	// Check if Bulk Delete is enabled 
	function check_if_bulk_delete_enabled(){
		// This functionality is based on my plugin "attachments bulk delte", so you won't need it if this plugin is activated in your wp installation already.
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		$abd = "attachments-bulk-delete/attachments-bulk-delete.php";
		$features = get_option("inline_attachments_features");
		if (!in_array($abd, $active_plugins) && $features[0][1]){
			return true;
		}
	}
	
	// Add Settings link to plugins - code from GD Star Ratings

	function add_settings_link($links, $file) {
		static $this_plugin;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		if ($file == $this_plugin){
			$settings_link = '<a href="options-general.php?page=inline-attachments.php">'.__("Settings", "inlineattachments").'</a>';
	 		array_unshift($links, $settings_link);
		}
		return $links;
	}
	function inline_attachments_activation(){
		add_option('inline_attachments_activated', true);
	}
	function inline_attachments_activation_message(){
		if (get_option('inline_attachments_activated', false)) {
			delete_option('inline_attachments_activated'); ?>
			<div class="updated fade" id="message">
	        	<p>
					<strong><?php _e("Inline Attachments was just activated.", "inlineattachments"); ?></strong> <a href="<?php echo admin_url('options-general.php?page=inline-attachments.php'); ?>"><?php _e("Click here", "inlineattachments") ?></a> <?php _e("to adjust the settings.", "inlineattachments"); ?>
				</p>
				<?php
					$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
					$plugin = "attachments/attachments.php";
					if (in_array($plugin, $active_plugins)){
						$warning = __("Warning", "inlineattachments");
						$msg = __("You seem to have the plugin 'Attachments' installed. Due to the very different approach of Inline Attachments, the functionality of 'Attachments' is not supported at the moment.", "inlineattachments");
						echo "<p><strong>{$warning}</strong> : {$msg}</p>";
					}
				?>
			</div>
		<?php }
	}
	function inline_attachments_localize(){
		// set text domain
		$lang_dir = dirname(plugin_basename( __FILE__ )) . '/lang/';
		load_plugin_textdomain('inlineattachments', false, $lang_dir );
	}
	/////////////////////////////////////////////////////////////////////////
	// Inline Attachment Box
	function add_inline_attachments_meta_box() {
		$inline_attachments_post_types = get_option("inline_attachments_post_types");
		$inline_attachments_box_titles = get_option("inline_attachments_box_titles");
		// ADD INLINE ATTACHMENTS BOX FOR EACH ACTIVATED POST TYPE 
		$count = 0;
		if($inline_attachments_post_types){
			foreach($inline_attachments_post_types as $pt) {
				add_meta_box('inline_attachments', $inline_attachments_box_titles[$count], array($this, 'inline_attachments_box_inner'), $pt, 'normal', 'high');
				$count ++;
			}
		}
	}
	function add_post_screen_js(){
		$script_url = $this->dir . "/js/inline-attachments-post-screen.js";
		wp_register_script('inline-attachments-post-screen', $script_url);
		wp_enqueue_script('inline-attachments-post-screen');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
	}
	function add_media_screen_js(){
		$script_url = $this->dir . "/js/inline-attachments-media-screen.js";
		wp_register_script('inline-attachments-media-screen', $script_url);
		wp_enqueue_script('inline-attachments-media-screen');
	}
	function add_attachments_bulk_delete_js(){
		$script_url = $this->dir . "/js/attachments-bulk-delete.js";
		wp_register_script('attachments-bulk-delete', $script_url);
		wp_enqueue_script('attachments-bulk-delete');
	}
	function javascript_gallery_link(){
		$wp_url = get_bloginfo("wpurl");
		$post_id = $_GET["post_id"];
		$link_text = __("Gallery", "inlineattachments");
		$gallery_link_element = "<a id='gallery-link' href='{$wp_url}/wp-admin/media-upload.php?type=file&tab=gallery&is_inline=1&post_id={$post_id}'>{$link_text} (<span id='attachments-count'>0</span>)</a>";
		?>
		<script type="text/javascript">
			var phpGalleryLink = "<?php echo $gallery_link_element; ?>";
		</script>
	<?php }
	function add_media_screen_css(){?>
		<style type="text/css" media="screen">
			/* This CSS comes from inline attachments and customizes the media screen */
			#media-upload .widefat {
				width: 100% !important;
				border-radius: 3px 3px 0px 0px;
				border-bottom: 0px none;
				min-width: 500px !important;
			}
			#media-upload #media-items {
				width: 100% !important;
				border-style: none !important;
				border-top: 1px solid #DFDFDF !important;
				min-width: 500px !important;
			}
			#media-upload .media-item {
				border: 1px solid #DFDFDF  !important;
				border-top: 0px none  !important;
				padding-right: 2px  !important;
				width: auto;
				min-width: 488px !important;
			}
			#media-upload .menu_order {
				text-align: left !important;
				width: 25% !important;
			}
			#sort-buttons {
				width: 97% !important;
				margin: 3px 0px -8px 0 !important;
				max-width: 5000px !important;
			}
			.sorthelper {
				width: 100% !important;
			}
			.ui-sortable-helper {
				left: 13px !important;
			}
			/* Here are the elements that should be hidden (set in Inline Attachments options panel) */
			<?php 
				$inline_attachments_media_elements = get_option("inline_attachments_media_elements");
				if($inline_attachments_media_elements){
					$count = 0;
					// add all the elements that should be hidden in the media screen
					foreach($inline_attachments_media_elements as $me) {
						if($me[2] != true && $me[1]){
							if($count > 0) echo ",";
							echo "\n\t\t" . $me[1];
							$count ++;
						}
					}
				}
			?> {
				display: none !important;
			}
			/* CSS For the Bulk Delete Checkboxes and Buttons */
			#bulk-delete-ui-wrapper {
				text-align: left;
				float: right;
				margin: 4px 0px 0px 0px;
				padding: 0px;
				overflow: hidden;
			}
			#bulk-delete-ui-wrapper .button {
				margin-left: 5px !important;
				display: block;
				float: right;
			}
			#bulk-delete-ajax-loader {
				width: 16px;
				height: 16px;
				margin: 0px 8px 0px 5px;
				display: none;
				padding-left: 10px !important;
			}
		</style>
	<?php }


	function add_post_screen_css(){ ?>
		<style type="text/css" media="screen">
			/* This CSS comes from inline attachments and customizes the post screen */
			#inline_attachments_footer {
				width: 100%;
				height: 14px;
				position: relative;
				text-align: right;
				overflow: hidden;
				border-top: 1px solid #dadada;
			}
			#inline_attachments_footer a.resizeButton {
				background: url("images/resize.gif") no-repeat scroll right bottom transparent;
				cursor: se-resize;
				display: block !important;
				margin: 2px 3px;
				position: relative;
				width: 12px;
				height: 12px;
				float: right;
			}
			#inline_attachments iframe {
				width: 100%;
			}
			#inline_attachments .inside {
				padding: 0px;
				margin: 0px;
				height: 0px;
				overflow: hidden;
			}
			#open_attachments_lightbox {
				position: absolute;
				top: 0px;
				right: 30px;
				height: 29px;
				line-height: 29px;
				text-shadow: 0 1px 0 #FFFFFF;
				font-size: 11px;
				font-weight: normal;
			}
		</style>
	<?php }
	function inline_attachments_box_inner($post, $content_block) { ?>

		<?php 
			global $post; 
			$args = array(
				'numberposts' => 1,
				'post_parent' => $post->ID,
				'post_status' => null,
				'post_type' => 'attachment'
			);
			$attachments = get_children( $args );
		?>
		<div id="inline_attachments_iframe_wrapper">
			<?php if(count($attachments) == 0): ?>
				<iframe id="inline_attachments_iframe" src="<?php bloginfo('wpurl'); ?>/wp-admin/media-upload.php?post_id=<?php echo $post->ID ?>&TB_iframe=1&is_inline=1&tab=type&attachments_thickbox=1"></iframe>
			<?php else: ?>
				<iframe id="inline_attachments_iframe" src="<?php bloginfo('wpurl'); ?>/wp-admin/media-upload.php?post_id=<?php echo $post->ID ?>&TB_iframe=1&is_inline=1&tab=gallery&attachments_thickbox=1"></iframe>
			<?php endif; ?>
		</div>

		<span id="open_attachments_lightbox">
			&nbsp;<a class="thickbox" href="media-upload.php?post_id=<?php echo $post->ID; ?>&amp;TB_iframe=1&amp;tab=gallery&amp;width=640&amp;height=455" href="#"><?php _e("Lightbox"); ?></a>
		</span>

		<div id="inline_attachments_footer">
			<a class="resizeButton" href="#"></a>
		</div> 
		<?php 
	} 
	/////////////////////////////////////////////////////////////////////////
	// Bulk Delete UI
	function add_attachments_bulk_delete(){
		global $post;
		$post_id = $post->ID;

		$images = get_children(array(
			'post_parent' => $post_id,
			'post_mime_type' => 'image'
		));
		$image_count = count($images);

		$bulk_delete_nonce = wp_create_nonce ('attachments-bulk-delete-nonce');

		if($image_count > 0){ ?>
			<span style='display: none;' id='bulk-delete-ui-wrapper'>
				<span style='display: none;' id='current-post-id'><?php echo $post_id; ?></span>
				<span id='cancel-delete-all-media-wrapper' style='display: none;'>
					<a class='button bulk-delete-attachment-button' href='#' id='cancel-delete-all-media'>
						<?php _e("Cancel", "inlineattachments"); ?>
					</a>
				</span>
				<a class='button bulk-delete-attachment-button' href='<?php echo $this->dir; ?>/php/bulk-delete.php?_wpnonce=<?php echo $bulk_delete_nonce; ?>&post=<?php echo $post_id; ?>' id='delete-all-media'>
					<?php _e("Delete selected Attachments", "inlineattachments"); ?>
				</a>
				<span style='display:none;' id='error-messages'>
					<span id='abd-none-selected'><?php _e("You need to select some attachments first.", "inlineattachments"); ?></span>
				</span>
				<img id='bulk-delete-ajax-loader' src='images/wpspin_light.gif' alt='' />
			</span>
		<?php }
	}
	/////////////////////////////////////////////////////////////////////////
	// Inline Attachments Options Page

	function adminMenu(){
		add_options_page( __( 'Inline Attachments', "inlineattachments" ), __( 'Inline Attachments', "inlineattachments" ), 'level_10', basename(__FILE__), array($this, 'optionsPage') );
	}

	function optionsPage(){ ?>

		<?php
			// get all public post types except attachments
			$args = array(
				"public" => true
			);
			$post_types = get_post_types($args, "objects");
			unset($post_types["attachment"]);

			$inline_attachments_post_types = get_option("inline_attachments_post_types");
			$inline_attachments_box_titles = get_option("inline_attachments_box_titles");

			if(!$inline_attachments_post_types){
				$inline_attachments_post_types = array();
				$inline_attachments_box_titles = array();
			}

			// Default media elements. Don't forget to re-save the settings in the admin area after you change any
			// of the css selectors or Descritpions, so the option can be updated. 

			// This Array contains all ELements you can hide or show:
			// [0] The Name of the Element
			// [1] The CSS Selector of the Element
			// [2] If the element should be visible (true) or not (false)

			$default_inline_attachments_media_elements = array(
				array(__("Sort Order"), "#sort-buttons", false),
				array("Tab “".__("From URL")."”", "#media-upload #tab-type_url", false),
				array("Tab “".__("Media Library")."”", "#media-upload #tab-library", false),
				array(__("Edit Image"), ".media-item-info .button", false),
				array(__("Title"), ".slidetoggle .post_title", true),
				array(__("Alternate Text"), ".slidetoggle .image_alt", true),
				array(__("Caption"), ".slidetoggle .post_excerpt", false),
				array(__("Description"), ".slidetoggle .post_content", false),
				array(__("Link URL"), ".slidetoggle .url", false),
				array(__("Media tags"), ".slidetoggle .media_tag", false),
				array(__("Alignment"), ".slidetoggle .align", false),
				array(__("Image") . strtolower(__("Size")), ".slidetoggle .image-size", false),
				array("Button “" . __("Insert into Post")."”", ".savesend input[type='submit']", false),
				array(__("Gallery Settings"), "#gallery-settings", false)
			);
			
			$inline_attachments_media_elements = get_option("inline_attachments_media_elements");
			if(!$inline_attachments_media_elements){
				$inline_attachments_media_elements = $default_inline_attachments_media_elements;
			}
			
			// Default Features
			
			$default_inline_attachments_features = array(
				array(__("Add functionionality for bulk deletion of attachments (Saves you a lot of time)", "inlineattachments"), true)
			);
			
			$inline_attachments_features = get_option("inline_attachments_features");
			if(!$inline_attachments_features){
				$inline_attachments_features = $default_inline_attachments_features;
			}

			// If the user clicked on “Save Changes”:

			if ( isset( $_POST['action'] ) ) {
				if ( $_POST['action'] == "inline_attachments_options_save" && $_POST['doaction_save']) {

					// Delete all previously saved options
					delete_option("inline_attachments_post_types");
					delete_option("inline_attachments_box_titles");
					delete_option("inline_attachments_media_elements");
					delete_option("inline_attachments_features");
					
					
					// Enable / Disable Meta Boxes for Post Types
					$count = 0;
					foreach($post_types as $pt){
						$inline_attachments_post_types[$count] = $_POST['post_type'][$count] ? $_POST['post_type'][$count] : false;
						$inline_attachments_box_titles[$count] = $_POST['box_title'][$count] ? $_POST['box_title'][$count] : false;
						$count ++;
					}
					update_option("inline_attachments_post_types", $inline_attachments_post_types);
					update_option("inline_attachments_box_titles", $inline_attachments_box_titles);

					// Enable / Disable Media Elements
					$count = 0;
					foreach($inline_attachments_media_elements as $me){
						$inline_attachments_media_elements[$count][2] = $_POST["ia_media_element"][$count] ? $_POST["ia_media_element"][$count] : false;
						$count ++;
					}
					update_option("inline_attachments_media_elements", $inline_attachments_media_elements);
					
					// Enable / Disable Additional Features
					
					$count = 0;
					foreach($inline_attachments_features as $feature){
						$inline_attachments_features[$count][1] = $_POST["features"][$count] ? $_POST["features"][$count] : false;
						$count ++;
					}
					update_option("inline_attachments_features", $inline_attachments_features);
					
					$message = __("Options saved.", "inlineattachments");
				} elseif( $_POST['action'] == "inline_attachments_options_save" && $_POST['doaction_reset'] ){
					$inline_attachments_post_types = array();
					$inline_attachments_box_titles = array();
					$inline_attachments_media_elements = $default_inline_attachments_media_elements;
					$inline_attachments_features = $default_inline_attachments_features;
					update_option("inline_attachments_post_types", $inline_attachments_post_types);
					update_option("inline_attachments_box_titles", $inline_attachments_box_titles);
					update_option("inline_attachments_media_elements", $inline_attachments_media_elements);
					update_option("inline_attachments_features", $inline_attachments_features);
					$message = __("All options have been set to their default values.", "inlineattachments");
				}
			}
		?>

		<div class='wrap'>
			<div id="icon-options-general" class="icon32">
				<br />
			</div>
			<h2><?php _e( 'Inline Attachments - Settings', "inlineattachments"); ?></h2>
			<?php if ( !empty( $message ) ) : ?>
				<div style="margin-top: 10px;" id="message" class="updated fade">

					<p>
						<strong><?php echo $message; ?> </strong><br />
					</p>
				</div>
			<?php endif; ?>
			<script type="text/javascript">
				var $ = jQuery;
				setTimeout(function(){
					$("#message").css({
						"overflow": "hidden",
						"height": "0px"
					}).animate({
						"height": $("#message p").outerHeight(true) + "px"
					}, 400);
				}, 100)
			</script>

			<form method="post">
				<h3><?php _e("Post Type Settings", "inlineattachments") ?></h3>
				<p>
					<strong><?php _e("Post Types", "inlineattachments") ?>:</strong> <?php _e( 'Select the post types you want to display the inline attachments box in.', "inlineattachments" ); ?><br />
					<strong><?php _e("MetaBox Titles", "inlineattachments") ?>:</strong> <?php _e( 'For each post type, you can set the title of the inline attachments box separately.', "inlineattachments" ); ?>
				</p>
				<?php wp_nonce_field( 'inline-attachments-options-nonce', 'inline-attachments-options-nonce', true, true ); ?>
				<input type="hidden" name="action" value="inline_attachments_options_save" />
				<input type="hidden" name="inline-attachments-options-nonce" value="true" />
				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr style="height: 36px;">
							<th scope='col' id='cb' class='manage-column column-cb check-column'>
								<input type="checkbox" />
							</th>
							<th scope='col' class='manage-column column-author sortable desc'>
								<?php _e("Post Types", "inlineattachments"); ?>
							</th>
							<th scope='col' class='manage-column column-title sortable desc'>
								<?php _e("MetaBox Titles", "inlineattachments"); ?>
							</th>

						</tr>
					</thead>

					<tbody id="the-list">
						<?php
							$count = 0;
							$alternate = true;
							foreach($post_types as $pt) : ?>
								<?php 
									$checked = $inline_attachments_post_types[$count];
									$box_title = $inline_attachments_box_titles[$count];
									if(!$box_title) $box_title = __("Media Files", "inlineattachments");
									$alternate = !$alternate;
								 ?>
								<tr id='post-type-<?php echo $count; ?>' class='<?php echo ($alternate? 'alternate' : ''); ?> author-self status-publish format-default iedit' valign="top">
									<th style="padding: 20px 0px 0px 0px;" scope="row" class="check-column">
										<input type="checkbox" <?php echo ($checked ? ' checked="checked"' : ''); ?> id="post_type[<?php echo $count; ?>]" name="post_type[<?php echo $count; ?>]" value="<?php echo $pt->name; ?>" />
									</th>
									<td style="line-height: 45px;" class="post-title page-title column-title">
										<strong>
											<span class="row-title">
												<label for="post_type[<?php echo $count; ?>]"><?php echo $pt->labels->menu_name; ?></label>
											</span>
										</strong>
									</td>
									<td style="padding: 14px 0px 0px 0px;" >
										<input id="title" type="text" autocomplete="off" value="<?php echo $box_title; ?>" placeholder="Title of Metabox, Default: 'Attachments'" tabindex="1" size="40" name="box_title[<?php echo $count; ?>]" />
									</td>
								</tr>
								<?php $count ++; ?>
							<?php endforeach; ?>
					</tbody>
				</table>
				<p style="display: none; margin: 20px 0px 20px 0px;">
					<input id="submit_changes1" class="button-primary" type="submit" value="<?php _e("Save Changes", "inlineattachments"); ?>" name="doaction_save" />
					<input id="reset1" class="button-secondary" type="submit" value="<?php _e("Reset Defaults", "inlineattachments"); ?>" name="doaction_reset" />
				</p>
				<!-- MEDIA ELEMENTS -->
				<h3><?php _e("Cleaning up the Attachments Screen", "inlineattachments") ?></h3>
				<p>
					<?php _e( "Select the options you want to be able to edit inside the attachments screen.", "inlineattachments" ); ?>
				</p>
				<ul>
					<?php 
						$count = -1; 
						foreach($inline_attachments_media_elements as $me): 
						$count ++; 
					?>
					<li>
						<input <?php echo ($me[2] == true ? ' checked="checked"' : ''); ?> id="ia_media_element[<?php echo $count; ?>]" type="checkbox" value="true" name="ia_media_element[<?php echo $count; ?>]">
						<label for="ia_media_element[<?php echo $count; ?>]"><?php echo $me[0]; ?></label>
						<span style="display: none; font-style:italic; color: #999; font-size: 11px;"class="help"><?php echo $me[1]; ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<!-- FEATURES -->
				<h3><?php _e("Additional Features", "inlineattachments") ?></h3>
				<ul>
					<?php 
						$count = -1; 
						foreach($inline_attachments_features as $feature): 
						$count ++; 
					?>
					<li>
						<input <?php echo ($feature[1] == true ? ' checked="checked"' : ''); ?> id="features[<?php echo $count; ?>]" type="checkbox" value="true" name="features[<?php echo $count; ?>]">
						<label for="features[<?php echo $count; ?>]"><?php echo $feature[0]; ?></label>
					</li>
					<?php endforeach; ?>
				</ul>
				
				<p style="margin: 20px 0px 20px 0px;">
					<input id="submit_changes2" class="button-primary" type="submit" value="<?php _e("Save Changes", "inlineattachments"); ?>" name="doaction_save" />
					<input id="reset2" class="button-secondary" type="submit" value="<?php _e("Reset Defaults", "inlineattachments"); ?>" name="doaction_reset" />
				</p>
				
			</form>
		</div>
	<?php 
	}
}
?>