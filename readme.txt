=== BNS Support ===
Contributors: cais
Donate link: http://buynowshop.com/
Tags: support, widget, multisite compatible, widget-only
Requires at least: 3.4
Tested up to: 3.8.1
Stable tag: 1.6.3
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Widget to display and share common helpful support details.

== Description ==

Simple display of useful support information in the sidebar. Easy to copy and paste details, such as: the blog name; WordPress version; name of installed theme; and, active plugins list. Help for those that help. The information is only viewable by logged-in readers; and, by optional default, the blog administrator(s) only.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `bns-support.php` to the `../wp-content/plugins/` directory.
2. Activate through the 'Plugins' menu.
3. Place the BNS Support widget appropriately in the Appearance | Widgets section of the dashboard.

-- or -

1. Go to 'Plugins' menu under your Dashboard
2. Click on the 'Add New' link
3. Search for bns-support
4. Install.
5. Activate through the 'Plugins' menu.
6. Place the BNS Support widget appropriately in the Appearance | Widgets section of the dashboard.

Reading this article for further assistance: http://wpfirstaid.com/2009/12/plugin-installation/

== Frequently Asked Questions ==
= What can I use for a shortcode? =
The shortcode `[ tech_support ]` (remove the spaces to use correctly) was recently added for those users that would like to use a post or page for the plugin output.

= How can I get support for this plugin? =
Please note, support may be available on the WordPress Support forums; but, it may be faster to visit http://buynowshop.com/plugins/bns-support/ and leave a comment with the issue you are experiencing.

= Why would I want to install this plugin? =
Often times when you need help with something on your blog it can be quite useful to provide some basic, common information that most every person wanting to help would appreciate. This plugin gives a simple, easy to copy and paste, set of details for support purposes only.

= Can I use this in more than one widget area? =
Yes, this plugin has been made for multi-widget compatibility but it really serves no purpose, yet.

== Screenshots ==

1. The Options Panel.
2. A sample of the sidebar display using the [Desk Mess Mirrored](http://wordpress.org/extend/themes/desk-mess-mirrored/) theme.
3. The sample information from the sidebar display copied and pasted into a common text editor.

== Other Notes ==
* Copyright 2009-2014  Edward Caissie  (email : edward.caissie@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 2,
  as published by the Free Software Foundation.

  You may NOT assume that you can use any other version of the GPL.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  The license for this software can also likely be found here:
  http://www.gnu.org/licenses/gpl-2.0.html

== Upgrade Notice ==
Please stay current with your WordPress installation, your active theme, and your plugins.

== Changelog ==
= 1.6.3 =
* Released January 2014
* Extract `PHP Details` into its own method
* Add PHP Safe Mode status
* Add PHP Allow URL fopen status
* Updated `readme.txt` with `tech_support` shortcode reference

= 1.6.2 =
* Released December 2013
* Corrected database connection

= 1.6.1 =
* Released December 2013
* Added shortcode name parameter for core filter auto-creation
* Added the option to put custom stylesheet in `/wp-content/` folder
* Added `WP_DEBUG` status display
* Added new method `MySQL Version Details` and corrected the reported data
* Minor rearrangement of layout for better readability

= 1.6 =
* Released September 2013
* Added shortcode functionality
* Changed `show_plugins` default to true (more common usage than false)

= 1.5.1 =
* Released May 2013
* Added conditional check for 'apache_get_modules'

= 1.5 =
* Released May 2013
* Added 'mod_rewrite' display check
* Change the widget output to a better grouping of details
* Refactored 'MultiSite Enabled', 'PHP Version', and 'MySQL Version' to be better filtered

= 1.4 =
* Release February 2013
* Added code block termination comments and other minor code formatting
* Added theme version checks against custom header data (see core trac ticket #16868)
* Moved all code into class structure
* Renamed some functions for more consistency
* Reorganized methods order in class
* Sorted out AuthorURI conditional test

= 1.3 =
* Release November 2012
* Add filter hooks and CSS classes to output strings
* Remove load_plugin_textdomain as redundant

= 1.2 =
* Required version set to 3.4
* Remove deprecated calls to `get_theme_data`
* Changed user role testing to capability testing
* Documentation and code format updates
* Programmatically add version number to enqueue calls

= 1.1.1 =
* Added conditional checks for WordPress 3.4 deprecation of `get_theme_data`
* confirmed compatible with WordPress 3.4

= 1.1 =
* released November 2011
* confirmed compatible with WordPress version 3.3
* added PHPDoc style documentation
* added `BNS Support TextDomain` i18n support
* added `Enqueue Plugin Scripts and Styles`
* added i18n support
* replaced Lester Chan's code with the (nearly identical) 'Plugin Lister' code by Paul G Getty
* added corrections to 'Plugin Lister' code
* removed 'Plugin Lister' options and excess/unneeded code
* removed 'Plugin Lister' description
* completely merged, stripped out excess, and re-wrote (as needed) 'Plugin Lister' code
* re-wrote Parent/Child-Theme Version code

= 1.0 =
* released June 2011
* confirmed compatible with WordPress version 3.2-beta2-18085
* re-sized options panel
* updated options panel screenshot
* enqueued stylesheet
* added display of MySQL version
* updated screenshots
* TO-DO: Re-write to show all roles of current user (carried forward from v0.9)
* TO-DO: Correct notices being generated via Lester Chan's code ... or replace?!

= 0.9 =
* released December 11, 2010
* Changed display from user "level" to user "role"

= 0.8 =
* correcting version number from 0.7.1

= 0.7.1 =
* released: 29 Aug 2010
* compatibility checked with WordPress 3.0.1
* clean up code to meet WP Standards
* added display of current PHP version
* edited display text

= 0.6 =
* compatible with WordPress version 3.0
* added Multisite check; displays 'True' or 'False'
* updated license declaration

= 0.5.3 =
* corrected location of ending tag for the 'CSS Wrapper'
* added 'margin-bottom:0;' property to credit style element

= 0.5.2 =
* compatible with WordPress version 2.9.2
* updated license declaration

= 0.5.1 =
* clarified the plugin's release under a GPL license
* change credits display option to 'false' by default
* minor option text changes
* changed screenshot of option panel

= 0.5 =
* added option to hide credits

= 0.4 =
* added option for displaying active plugins

= 0.3 =
* adapted Lester Chan's WP-Pluginsused code to list active plugins
* left justified displayed items via plugin CSS file

= 0.2.1 =
* minor adjustments to display

= 0.2 =
* compatibility check for 2.9.1 completed

= 0.1.1 =
* added direct URL to plugin page
* decreased width of screenshot-3

= 0.1 =
* Initial Release