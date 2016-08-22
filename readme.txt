=== Genesis Simple Share ===
Contributors: nathanrice, studiopress, wpmuguru, nick_thegeek, bgardner
Tags: genesis, share, share buttons, facebook, twitter, pinterest, stumbleupon, linkedin, social
Requires at least: 3.7
Tested up to: 4.6
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Genesis Simple Share allows you to easily add beautiful share buttons to your site using a Genesis child theme

== Description ==

Genesis Simple Share is created to be simple and easy to use within your Genesis child theme. We have tried to focus on making default options and initial setup a breeze. 

The default settings should automatically enable the share icons on posts. You may wish to make some changes so in your WordPress dashboard hover over the Genesis menu item and click the Simple Share menu item in that submenu. 

Once there you can choose which post types Simple Share will be active on, if it should show on archives, and which icons to enable. You can also drag and drop the icons to change the order they will display in on your site. You may wish to add your Twitter user name to the twitter icon settings. This will add a via: @username after the share text on Twitter. You may also add a URL to an image to use as the default image.

== Installation ==

Download the latest version of the Genesis Simple Share plugin and upload as a new plugin within your WordPress dashboard or via FTP to the wp-content/plugins folder and then activate the plugin.

== Frequently Asked Questions ==

= Where can I learn more about this plugin? =

We’ve got a small wiki in the git repo for the plugin with some cool tips and tricks you can use.
https://github.com/copyblogger/genesis-simple-share/wiki/Usage-Tips


== Changelog ==

= 1.0.9 =
* Fixed: Facebook count continues to work after recent Facebook API changes.

= 1.0.8 =
* Fixed: LinkedIn button works with recent change to LinkedIn API. Note, the button now uses the sharrre.php file to fetch results, which can cause problems accessing share counts with some hosts depending on settings.

= 1.0.7 =
* Fixed: potential xss vector

= 1.0.6 =
* Fixed: styling tweeks for twitter button without count.

= 1.0.5 =
* Fixed: styling issues following removal of tweet count.

= 1.0.4 =
* Include a force to show argument in icon output functions.
* Disabled Twitter counter for now because twitter is removing the ability to get the counts after 10-2015.

= 1.0.3 =
* Fix error resulting in double icon output.

= 1.0.2 =
* Fix error where icons do not show in archives when showing the excerpt.

= 1.0.1 =
* Fixes potential error where only first post in archive will show share buttons.

= 1.0 =
* Initial WordPress.org release.

== Upgrade Notice ==

= 1.0.8 =
* Fixes LinkedIn button so that it works with recent change to LinkedIn API. Note, the button now uses the sharrre.php file to fetch results, which can cause problems accessing share counts with some hosts depending on settings.

= 1.0.1 =
* Fixes potential error where only first post in archive will show share buttons

= 1.0 =
Plugin was added to the WordPress.org repo. Users should update to ensure they have the latest code.
