<?php
/*
=========
Custom style toevoegen aan het admin panel
=========
*/
function add_custom_css() {
	//hide 'save all changes'
	//ook het verbergen van overbodige paragraphs
	$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	echo '
	<style type="text/css">
	#media-upload .savebutton {
		display: none!important;
	}
	#add_image {
		background: url('.$pluginPath.'images/icon_image.png) no-repeat;
		padding-left: 26px!important;
		padding-top: 6px!important;
		margin-left: 5px;
	}
		#add_image img {
			display: none;
		}
		
	#add_video {
		display: none;
	}
		
	#add_audio {
		display: none;
	}
	#add_media {
		background: url('.$pluginPath.'images/icon_media.png) no-repeat;
		padding-left: 26px!important;
		padding-top: 6px!important;
		margin-left: 5px;
	}
		#add_media img {
			display: none;
		}

	</style>
	';
}
add_action('admin_head', 'add_custom_css');
?>