=== Inline Attachments ===

Contributors: Nonverbla
Plugin Name: Inline Attachments
Plugin URI: http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/
Tags: attachments, inline, thickbox, meta box, adjust, options, admin, bulk, delete, image, checkbox, ajax
Author URI: http://www.nonverbla.de/
Author: Nonverbla
Donate link: http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 0.9.4
Version: 0.9.4

Shows attachments directly on the post edit screen. Also gives you full control over image edit options.

== Description ==

I very often use WordPress as a CMS for clients like artists or photographers, who want to present a big amount of media files on their websites. Most of the time, the media files actually are the very center of these websites.

To reflect this focus on media files in the admin area, I developed the plugin "Inline Attachments". It adds a Meta Box to the post edit screen, that holds the media-uploads panel inside an iframe. If no Media Files are attached to a post yet, it shows the upload tab, otherwise it shows the list of attached media files.

In the plugin settings, you can select all post types, where the Inline Attachments Meta Box should be displayed, and you can specify a name for each (e.g. ”Photographs“ or ”Images“). Default is ”Media Files“.

Another thing with mainly media-driven websites is, that you normally don't write a text post and put some images into the text flow. Because of this, you wouldn't need the options for Image Size, Alignment, Link URL and so on in the attachment screen. With Inline Attachments, you can easily select which options should be visible to the admin for editing media files.

If you have comments or questions, please go to the official plugin page: [www.nonverbla.de/blog/wordpress-plugin-inline-attachments](http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/)

**Please Note**
Due to it's very different approach, *Inline Attachments* does **not** support the functionality of the plugin *Attachments*.

**Please rate the plugin and post comments, if anything doesn't work as expected. ––>**

== Installation ==

1. Install easily with the WordPress plugin control panel or manually download the plugin and upload the extracted folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the plugin settings page called “Inline Attachments” and select the post types you want to have it activated for. Also select all the fields you want to enable for media editing.
3. Go to the post edit sceen, and here it is, your inline attachments meta box.

== Screenshots ==

1. If activated, Inline Attachments displays all attachments of a post inside the post edit screen
2. The Inline Attachments Options Screen

== Upgrade Notice ==
= 0.9.4 =
* Added localization and German translation
* Added the functionality of my plugin [Attachments Bulk Delete](http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/). I like having everything in the same place ;)
= 0.9.3 =
* Added ajax-saving of attachments order and input fields on change
= 0.9.2 =
* Added automatic saving of changed attachments properties on post save / update
* Optimized the styling while dragging an attachment to change sort order.
= 0.9.1 =
* This release also enables the plugins functionality on newly created posts.

== Changelog ==

= 0.9.4 =
* Added localization and German translation
* Added the functionality of my plugin [Attachments Bulk Delete](http://www.nonverbla.de/blog/wordpress-plugin-inline-attachments/). I like having everything in the same place ;)
= 0.9.3 =
* Added ajax-saving of attachments order and input fields on change
= 0.9.2 =
* Added automatic saving of changed attachments properties on post save / update
* Optimized the styling while dragging an attachment to change sort order.
= 0.9.1 =
* Added plugin initialization also for post-new.php, not just post.php, so that it also works on newly created posts



