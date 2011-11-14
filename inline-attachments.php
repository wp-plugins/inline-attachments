<?php
/*
	Plugin Name: Inline Attachments
	Plugin URI: http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/
	Description: Add a Meta Box containing the Media Panel inside the edit screen. Also adjust wich options should be displayed for attachments (e.g. "Insert Image", "Image Size", "Alignment")
	Version: 0.9.2
	Author: Nonverbla
	Author URI: http://www.nonverbla.de/
	
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


// Add Settings link to plugins - code from GD Star Ratings

function add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=inline-attachments.php">'.__("Settings").'</a>';
 		array_unshift($links, $settings_link);
	}
	return $links;
}


if(is_admin()) {
	
	add_action('admin_init','inline_attachments_init');
	add_action( 'admin_menu', 'adminMenu');
	add_filter('plugin_action_links', 'add_settings_link', 10, 2 );
	register_activation_hook(__FILE__, 'inline_attachments_activation');
	
	if($pagenow == "media-upload.php" || $pagenow == "media.php"){
		add_action('admin_head', 'add_attachment_css');
		add_action('admin_head', 'add_attachment_js');
	} elseif($pagenow == "post.php" || $pagenow == "post-new.php"){
		add_action('init', 'add_post_screen_js');
		add_action('admin_head', 'add_post_screen_css');
	}
}
function inline_attachments_activation_message(){
	if (get_option('inline_attachments_activated', false)) {
		delete_option('inline_attachments_activated'); ?>
		<div class="updated fade" id="message">
        	<p>
				<strong>Inline Attachments was just activated.</strong> <a href="<?php echo admin_url('options-general.php?page=inline-attachments.php'); ?>">Go here</a> to adjust the settings.
			</p>
		</div>
	<?php }
}
/////////////////////////////////////////////////////////////////////////
// Inline Attachment Box

function inline_attachments_init() {
	inline_attachments_activation_message();
	
	$inline_attachments_post_types = get_option('inline_attachments_post_types');
	$inline_attachments_box_titles = get_option('inline_attachments_box_titles');
	// ADD INLINE ATTACHMENTS BOX FOR EACH ACTIVATED POST TYPE 
	$count = 0;
	if($inline_attachments_post_types){
		foreach($inline_attachments_post_types as $pt) {
			add_meta_box('inline_attachments', $inline_attachments_box_titles[$count], 'inline_attachments_box_inner', $pt, 'normal', 'high');
			$count ++;
		}
	}
}

function add_post_screen_js(){
	$plugin_directory = get_bloginfo("wpurl") . "/wp-content/plugins/inline-attachments";
	$script_url = $plugin_directory . "/js/inline-attachments-post-screen.js";
	wp_register_script('inline-attachments-post-screen', $script_url);
	wp_enqueue_script('inline-attachments-post-screen');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
}
function add_attachment_js(){?>
	<script type="text/javascript">
		$ = jQuery;
		var galleryTabTimeout;
		function checkIfMoreThanZero(){
			if(parseInt($("#attachments-count").text()) > 0){
				$("#tab-gallery").css("display", "block");
			} else {
				galleryTabTimeout = setTimeout(checkIfMoreThanZero, 5000);
			}
			
		}
		$(document).ready(function(){
			if($("#tab-gallery").length == 0){
				$("#sidemenu").append('<li id="tab-gallery"><a href="/cms/wp-admin/media-upload.php?type=file&tab=gallery&post_id=408">Gallery (<span id="attachments-count">0</span>)</a></li>');
				$("#tab-gallery").css("display", "none");
				checkIfMoreThanZero();
			}
			
		})
	</script>
<?php }
function add_attachment_css(){?>
	<style type="text/css" media="screen">
		/* This CSS comes from inline attachments */
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
	</style>
<?php }


function add_post_screen_css(){ ?>
	<style type="text/css" media="screen">
		/* This CSS comes from inline attachments */
		#inline_attachments_footer {
			width: 100%;
			height: 14px;
			position: relative;
			text-align: right;
			overflow: hidden;
			border-top: 1px solid #dadada;
		}
		#inline_attachments_footer a.resizeButton {
			background: url("<?php bloginfo('wpurl'); ?>/wp-content/plugins/inline-attachments/img/resize.gif") no-repeat scroll right bottom transparent;
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
			<iframe id="inline_attachments_iframe" src="<?php bloginfo('wpurl'); ?>/wp-admin/media-upload.php?post_id=<?php echo $post->ID ?>&is_inline=1&tab=type"></iframe>
		<?php else: ?>
			<iframe id="inline_attachments_iframe" src="<?php bloginfo('wpurl'); ?>/wp-admin/media-upload.php?post_id=<?php echo $post->ID ?>&is_inline=1&tab=gallery"></iframe>
		<?php endif; ?>
	</div>
	
	<span id="open_attachments_lightbox">
		&nbsp;<a class="thickbox" href="media-upload.php?post_id=<?php echo $post->ID; ?>&amp;TB_iframe=1&amp;tab=gallery&amp;width=640&amp;height=455" href="#">Lightbox ↗</a>
	</span>
	
	<div id="inline_attachments_footer">
		<a class="resizeButton" href="#"></a>
	</div>
<?php } 

	/////////////////////////////////////////////////////////////////////////
	// Inline Attachments Options Page
	
	function adminMenu(){
		add_options_page( __( 'Inline Attachments' ), __( 'Inline Attachments' ), 'level_10', basename(__FILE__), 'optionsPage' );
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
			
			// The defaults. Don't forget to re-save the settings in the admin area after you change any
			// of the css selectors or Descritpions, so the option can be updated. 
			
			// This Array contains all ELements you can hide or show:
			// [0] The Name of the Element
			// [1] The CSS Selector of the Element
			// [2] If the element should be visible (true) or not (false)
			
			$default_inline_attachments_media_elements = array(
				array(__("Sort Order"), "#sort-buttons", true),
				array("Tab “".__("From URL")."”", "#media-upload #tab-type_url", false),
				array("Tab “".__("Media Library")."”", "#media-upload #tab-library", false),
				array(__("Edit Image"), ".media-item-info .button", false),
				array(__("Title"), ".slidetoggle .post_title", true),
				array(__("Alternate Text"), ".slidetoggle .image_alt", false),
				array(__("Caption"), ".slidetoggle .post_excerpt", true),
				array(__("Description"), ".slidetoggle .post_content", true),
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
			
			// If the user clicked on “Save Changes”:
			
			if ( isset( $_POST['action'] ) ) {
				if ( $_POST['action'] == "inline_attachments_options_save" && $_POST['doaction_save']) {
					
					// Delete all previously saved options
					delete_option("inline_attachments_post_types");
					delete_option("inline_attachments_box_titles");
					delete_option("inline_attachments_media_elements");
					
					$count = 0;
					// POST TYPES AND BOX TITLES
					foreach($post_types as $pt){
						$inline_attachments_post_types[$count] = $_POST['post_type'][$count] ? $_POST['post_type'][$count] : false;
						$inline_attachments_box_titles[$count] = $_POST['box_title'][$count] ? $_POST['box_title'][$count] : false;
						$count ++;
					}
					update_option("inline_attachments_post_types", $inline_attachments_post_types);
					update_option("inline_attachments_box_titles", $inline_attachments_box_titles);
					
					// MEDIA ELEMENTS SHOW / HIDE
					
					delete_option("inline_attachments_media_elements");
					$inline_attachments_media_elements = $default_inline_attachments_media_elements;
					
					$count = 0;
					foreach($inline_attachments_media_elements as $me){
						$inline_attachments_media_elements[$count][2] = $_POST["ia_media_element"][$count] ? $_POST["ia_media_element"][$count] : false;
						$count ++;
					}
					update_option("inline_attachments_media_elements", $inline_attachments_media_elements);
					
					$message = "Options saved.";
				} elseif( $_POST['action'] == "inline_attachments_options_save" && $_POST['doaction_reset'] ){
					$inline_attachments_post_types = array();
					$inline_attachments_box_titles = array();
					$inline_attachments_media_elements = $default_inline_attachments_media_elements;
					update_option("inline_attachments_post_types", $inline_attachments_post_types);
					update_option("inline_attachments_box_titles", $inline_attachments_box_titles);
					update_option("inline_attachments_media_elements", $inline_attachments_media_elements);
					$message = "All options have been set to their default values.";
				}
			}
		?>

		
		<div class='wrap'>
			<div id="icon-options-general" class="icon32">
				<br />
			</div>
			<h2><?php _e( 'Inline Attachments - Settings' ); ?></h2>
			<?php if ( !empty( $message ) ) : ?>
				<div style="margin-top: 10px;" id="message" class="updated fade">
					
					<p>
						<strong><?php _e( $message ); ?> </strong><br />
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
				<h3>Post Type Settings</h3>
				<p>
					<strong><?php _e("Post Types") ?>:</strong> <?php _e( 'Select the post types you want to display the inline attachments box in.' ); ?><br />
					<strong><?php _e("MetaBox Titles") ?>:</strong> <?php _e( 'For each post type, you can set the title of the inline attachments box separately.' ); ?>
				</p>
				<?php wp_nonce_field( 'inline-attachments-nonce', 'inline-attachments-nonce', true, true ); ?>
				<input type="hidden" name="action" value="inline_attachments_options_save" />
				<input type="hidden" name="inline-attachments-nonce" value="true" />
				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr style="height: 36px;">
							<th scope='col' id='cb' class='manage-column column-cb check-column'>
								<input type="checkbox" />
							</th>
							<th scope='col' class='manage-column column-author sortable desc'>
								<?php _e("Post Types"); ?>
							</th>
							<th scope='col' class='manage-column column-title sortable desc'>
								<?php _e("MetaBox Titles"); ?>
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
									if(!$box_title) $box_title = __("Media Files");
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
				<p style="margin: 20px 0px 20px 0px;">
					<input id="submit_changes1" class="button-primary" type="submit" value="<?php _e("Save Changes") ?>" name="doaction_save" />
					<input id="reset1" class="button-secondary" type="submit" value="<?php _e("Reset Defaults") ?>" name="doaction_reset" />
				</p>
				<h3><?php _e("Cleaning up the Attachments Screen") ?></h3>
				<p>
					<?php _e( 'Select the options you want to be able to edit inside the attachments screen.' ); ?>
				</p>
				<ul>
					<?php $count = -1; foreach($inline_attachments_media_elements as $me): $count ++; ?>
					<li>
						<input <?php echo ($me[2] == true ? ' checked="checked"' : ''); ?> id="ia_media_element[<?php echo $count; ?>]" type="checkbox" value="true" name="ia_media_element[<?php echo $count; ?>]">
						<label for="ia_media_element[<?php echo $count; ?>]"><?php echo $me[0]; ?></label>
						<span style="display: none; font-style:italic; color: #999; font-size: 11px;"class="help"><?php echo $me[1]; ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<p style="margin: 20px 0px 20px 0px;">
					<input id="submit_changes2" class="button-primary" type="submit" value="<?php _e("Save Changes") ?>" name="doaction_save" />
					<input id="reset2" class="button-secondary" type="submit" value="<?php _e("Reset Defaults") ?>" name="doaction_reset" />
				</p>
				
			</form>
		</div>
	<?php }

?>