<?php
/*
=========
Custom logo toevoegen aan de header van het admin panel
=========
*/
function add_logo() {
	echo '<div id="logo">';
		$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
		echo '<img src="'.$pluginPath.'/images/logo.png" alt="Admin-panel logo" />';
		echo '<a target="_blank" href="'.get_bloginfo('url').'"><span>'.get_bloginfo('sitename').'</span>';
		echo '<em id="site-visit-button">'. __("Visit Site","cms") .'</em></a>';
	echo '</div>';
}
add_action('admin_footer', 'add_logo');


function add_logo_css() {
	echo '
	<style type="text/css">
		#header-logo {
			visibility: hidden;	
		}
		#logo {
			position: absolute;
			top:4px;
			left:13px;
		}
			#logo img {
				vertical-align: -22px;
				margin-right: 8px;
			}
			#logo a {
				color: #333;
				font-size: 24px;
				text-decoration: none;
				font-family: Georgia, "Times new roman";
			}
			#logo a:hover span {
				text-decoration: underline;
			}
			#logo a #site-visit-button {
				color: #666;
			}
			#logo a:hover #site-visit-button {
				color: #333;
			}
			#logo #site-visit-button {
				background-color:#fff;
				background-image:url('.get_option('url').'/test/wp-admin/images/visit-site-button-grad.gif);
				color:#aaa;
				text-shadow:0 -1px 0 #eee;
			}
			#logo #site-visit-button {
				-moz-border-radius-bottomleft:3px;
				-moz-border-radius-bottomright:3px;
				-moz-border-radius-topleft:3px;
				-moz-border-radius-topright:3px;
				background-position:0 0;
				background-repeat:repeat-x;
				cursor:pointer;
				display:inline-block;
				font-size:10px;
				font-style:normal;
				line-height:17px;
				margin-left:10px;
				padding:0 6px;
				vertical-align:middle;
			}
			
		#wphead #site-heading {
			display: none;
		}
	</style>
	';
}
add_action('admin_head', 'add_logo_css');
?>