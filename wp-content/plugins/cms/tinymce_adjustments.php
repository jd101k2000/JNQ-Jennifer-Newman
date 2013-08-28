<?php
/*
=========
Een aantal stylesheet aanpassingen na het importen van het thema css ding (dmv TinyMCE Advanced)
=========
*/

function tinyMce_custom_css() {
	$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	return $pluginPath . 'css/tinymce_adjustments.css';
}

add_filter('mce_css','tinyMce_custom_css');
?>