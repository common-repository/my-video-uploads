=== Plugin Name ===
Contributors: glwe0903
Tags: Video, youtube, vimeo, feed, player
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.2.2

This plugin creates a playlist with player for youtube and vimeo videos.

== Description ==

 Video plugin that lists your YouTube and Vimeo uploads in a playlist with an embedded player.
 Use this shortcode in the editor: `[videoUploads width="" height=""]` or just `[videoUploads]`
Please define the width and height of the player. You can also use this php code in your template: `<?php do_action('myvideouploads'); ?>`

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload folder myVideoUploads to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php do_action('myvideouploads'); ?>` in your templates or add `[videoUploads width="" height=""]` or `[videoUploads]` in posts and pages
4. Insert your YouTube and/or Vimeo username and adjust the settings. Javascript and CSS needs to be enabled. Width and height settings will default to a preset value if you don't set your own parameters.

OR

1. Find it in Wordpress and click install plugin
2. Activate plugin from the Plugin menu in Wordpress
3. and 4. Proceed as written above!

== Frequently Asked Questions ==
	
 = Nothing yet =


== Screenshots ==

1. The options panel
2. The feed and player

== Changelog ==

= 1.0 =
* New plugin

= 1.1 =
* Added javascript functionality (jQuery)
* Changes in CSS
* IE fixes

= 1.2 =
* Bugfixes

= 1.2.1 =
* Bugfixes
* Some added code documentation
* Small changes to the settings page

= 1.2.2 =
* Now possible to disable/enable JS and sample CSS
* Added hoverIntent jquery plugin

== Upgrade Notice ==

= 1.0 =
It' new!

= 1.1 =
NOTE: If you have edited the stylesheet you must copy this to a safe place. Upgrading the plugin will reset the stylesheet!

= 1.2 =
NOTE: If you have edited the stylesheet you must copy this to a safe place. Upgrading the plugin will reset the stylesheet!
The stylesheet is really just for demonstration purposes. The design is really up to the owner of the site to decide.

= 1.2.1 =
NOTE: If you have edited the stylesheet you must copy this to a safe place. Upgrading the plugin will reset the stylesheet!
The stylesheet is really just for demonstration purposes. The design is really up to the owner of the site to decide.

= 1.2.2 = 
After installing the plugin, go to the options page and enable Javascript, css.
NOTE: If you have edited the stylesheet you must copy this to a safe place. Upgrading the plugin will reset the stylesheet!
The stylesheet is really just for demonstration purposes. The design is really up to the owner of the site to decide.
