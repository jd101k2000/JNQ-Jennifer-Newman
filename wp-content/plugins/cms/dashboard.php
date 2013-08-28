<?php
/*
=========
Dashboard aanpassingen
=========
*/

function dashboard_links() { //de widget op de dashboard aanmaken
	wp_add_dashboard_widget('algemene-links', 'Links', 'add_dashboard_links');
}
if(get_option('cms_dashboard_links')) {
	add_action('wp_dashboard_setup', 'dashboard_links');
}


function add_dashboard_links() { //de werkelijke code die aangeroepen wordt bij het maken van de widget
	$links = file_get_contents(get_option('cms_dashboard_links'));
	echo $links;
}

function deleteMess_dashboard() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
if(!get_option('cms_delete_messy_dashboard')) {
	add_action('wp_dashboard_setup','deleteMess_dashboard');
}
?>