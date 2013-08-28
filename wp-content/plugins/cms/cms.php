<?php
/*
Plugin Name: CMS
Plugin URI: http://www.trendwerk.nl/documentation/cms/
Description: A collection of plugins that optimize WordPress to use as a CMS. Includes all our other plugins and some extra's. When installing these plugins, please deactive all others plugins by Ontwerpstudio Trendwerk
Version: 3.0
Author: Ontwerpstudio Trendwerk
Author URI: http://www.trendwerk.nl/
*/

/*
	All includes are small adjustments for WordPress
	Comments in sub-files are dutch, comments in this "root" file are english
*/

/*
========
Multiple content blocks plugin
========
*/
if(!get_option('cms_multiple_content')) {
	include('multiple_content.php');
}

/*
========
CMS Hide Update reminder
========
*/
if(!get_option('cms_keep_nag')) {
	include('hide_updates.php');
}

/*
=========
Admin panel
=========
*/

include('add_adminpanel.php');

/*
=========
Add your own custom logo
=========
*/

if(get_option('cms_use_custom_logo')) {
	include('logo.php');
}

/*
=========
Add some adjustments to TinyMCE/edit page
=========
*/

if(!get_option('cms_trendwerk_stijl')) {
	include('editing_adjustments.php');
}

/*
=========
Add the custom menu title
=========
*/

/*

Stop including other menu item since Custom Menu 

if(!get_option('other-menu-title-off')) {
	include('other-menu-title.php');
}

*/

/*
=========
Some adjustments in the tinyMCE editor: ie. you can import stylesheet to it with TinyMCE Advanced; this always aligns it to the left
=========
*/
if(!get_option('cms_tinymce_stijl')) {
	include('tinymce_adjustments.php');
}


/*
=========
Page manager plugin
=========
*/


/*

Stop including Page Manager since WordPress' Custom Menu

if(!get_option('cms_page_order')) {
	include('page_manager.php');
}
*/


/*
=========
include the FAQ plugin
=========
*/

if(!get_option('faq_inschakelen')) {
	include('faq.php');
}

/*
=========
Do some adjustments in the menu -> Page goes up, rename some plugins
=========
*/

include('menu_adjustments.php');

/*
=========
Adds your custom RSS feed
=========
*/

if(get_option('cms_dashboard_feed_link')) {
	include('custom_feed.php');
}

/*
=========
Remove silly stuff from the dashboard
=========
*/

include('dashboard.php');

/*
=========
Add translations
=========
*/

function ot_loadtranslation() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain('cms', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/languages', dirname(plugin_basename(__FILE__)).'/languages');
}

add_action('init', 'ot_loadtranslation');
?>