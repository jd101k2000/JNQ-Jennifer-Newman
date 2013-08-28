<?php
/*
=========
De settings etc. toevoegen aan het admin panel
=========
*/

function add_cms_adminpanel() {
	global $menu, $submenu;
	
    if(get_option('cms_minimum_level_general')) {
		$minLevelGeneral = get_option('cms_minimum_level_general');
	} else {
		$minLevelGeneral = 7;
	}
	
	add_submenu_page("options-general.php", __("CMS Settings",'cms'), __("CMS Settings",'cms'), $minLevelGeneral, "cms-settings.php","show_settings");
}

add_action('admin_menu', 'add_cms_adminpanel');


function add_cms_css() {
	$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	echo '
	<style type="text/css">
		#cms_ot .regular-text {
			width: 25em;
		}
		#cms_admin_options {
			background-color: #eee;
			border: 1px solid #ccc;
			padding: 0px 10px;
			overflow: hidden;
		}
		#cms_ot .small-text {
			width: 3em;
		}
		#cms_left_admin_options {
			float: left;
			width: 680px;
		}
		#plugin_checker {
			width: 250px;
			background-color: #fff;
			border: 1px solid #ccc;
			padding: 10px;
			float: right;
			margin: 10px 0px;
		}
	</style>
	';

}

add_action('admin_head', 'add_cms_css');


function add_settings_links_cms($links) {
	$settings_link = '<a href="options-general.php?page=cms-settings.php">' . __('Settings') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}

add_action('plugin_action_links_cms/cms.php', 'add_settings_links_cms');


function show_settings() { 
?>
<div class="wrap">
<div id="wrap_cms_settings">
	<h2><?php _e("CMS Settings",'cms'); ?></h2>
    <form id="cms_ot" method="post" enctype="multipart/form-data">
    <p><strong><?php _e("General information",'cms'); ?></strong></p>
    <p><label for="blog_naam"><?php _e("Sitename",'cms'); ?><br /><input class="regular-text" type="text" name="blog_naam" id="blog_naam" value="<?php echo get_option('blogname')?>" /></label></p>
    <p>&nbsp;</p>
    
    <?php
	//from here on not everyone can see the options
    if(get_option('cms_minimum_level')) {
		$minLevel = get_option('cms_minimum_level');
	} else {
		$minLevel = 9;
	}
	
    if(get_option('cms_minimum_level_general')) {
		$minLevelGeneral = get_option('cms_minimum_level_general');
	} else {
		$minLevelGeneral = 7;
	}
	
	global $current_user;
    get_currentuserinfo();
	if($current_user->user_level >= $minLevel) {
	?>
    <div id="cms_admin_options">
    <div id="cms_left_admin_options">
        <h3><?php _e('Admin options','cms'); ?></h3>
        <p><strong><?php _e("Switched on components","cms"); ?></strong></p>
        <p><label for="blog_inschakelen"><input id="blog_inschakelen" name="blog_inschakelen" type="checkbox" <?php if(get_option('blog_erin') == 1) { echo 'checked'; } ?> onchange="if(!this.checked) { document.getElementById('reacties_inschakelen').checked = false; }" /> <?php _e("Posts","cms"); ?></label></p>
        <p><label for="reacties_inschakelen"><input id="reacties_inschakelen" name="reacties_inschakelen" type="checkbox" <?php if(get_option('reacties_erin') == 1) { echo 'checked'; } ?> onchange="if(!document.getElementById('blog_inschakelen').checked) { this.checked = false; }" /> <?php _e("Comments","cms"); ?></label></p>
        <p><label for="cms_use_links"><input id="cms_use_links" name="cms_use_links" type="checkbox" <?php if(get_option('cms_use_links') == 1) { echo 'checked'; } ?> /> <?php _e("Links","cms"); ?></label></p>
        <p><label for="faq_inschakelen"><input id="faq_inschakelen" name="faq_inschakelen" type="checkbox" <?php if(!get_option('faq_inschakelen')) { echo 'checked'; } ?> /> <?php _e("FAQ","cms"); ?></label></p>
        <p>&nbsp;</p>
        <p><strong><?php _e("Custom admin-panel logo","cms"); ?></strong></p>
        <p><label for="logo_uploaden"><input id="logo_uploaden" name="logo_uploaden" type="file" /></label><input type="submit" name="cms_remove_logo" value="<?php _e('Remove custom logo','cms'); ?>" class="button-secondary" onclick="return confirm('<?php _e('Are you sure you want to remove the custom logo?','cms'); ?>')" /><br />
        <span class="howto"><?php _e("(the image should be 60px(w) by 50px(h) in PNG format with a transparent background)","cms"); ?></span></p>
        <p>&nbsp;</p>
        <p><strong><?php _e('Wordpress fixes/add-ons','cms'); ?></strong></p>
        <p><label for="cms_multiple_content"><input type="checkbox" name="cms_multiple_content" id="cms_multiple_content" <?php if(!get_option('cms_multiple_content')) { echo 'checked'; } ?> /> <?php _e('Be able to add multiple content blocks as a developer (<a href="http://plugins.trendwerk.nl/documentation/multiple-content-blocks/" target="_blank">documentation</a>)','cms'); ?></label></p>
        <p>&nbsp;</p>
        <p><strong><?php _e('tinyMCE adjustments','cms'); ?></strong></p>
        <p><label for="cms_trendwerk_stijl"><input type="checkbox" name="cms_trendwerk_stijl" id="cms_trendwerk_stijl" <?php if(!get_option('cms_trendwerk_stijl')) { echo 'checked'; } ?> /> <?php _e('Make small adjustments to remove unnessecary tinyMCE stuff','cms'); ?></label></p>
        <p><label for="cms_tinymce_stijl"><input type="checkbox" name="cms_tinymce_stijl" id="cms_tinymce_stijl" <?php if(!get_option('cms_tinymce_stijl')) { echo 'checked'; } ?> /> <?php _e('Use your templates stylesheet in tinyMCE (but always align left standard)','cms'); ?></label></p>
        <p>&nbsp;</p>
        <p><strong><?php _e('Dashboard options','cms'); ?></strong></p>
        <p><label for="cms_dashboard_links"><?php _e("HTML page with a list of links that will be displayed on the dashboard (you can always change this, and it will change on all your cms installs)",'cms'); ?><br /><input class="regular-text" type="text" name="cms_dashboard_links" id="cms_dashboard_links" value="<?php echo get_option('cms_dashboard_links')?>" /></label></p>
        <p><label for="cms_dashboard_feed_link"><?php _e("Link to the feed you want to display for your clients on the front page",'cms'); ?><br /><input class="regular-text" type="text" name="cms_dashboard_feed_link" id="cms_dashboard_feed_link" value="<?=get_option('cms_dashboard_feed_link')?>" /></label></p>
        <p><label for="cms_delete_messy_dashboard"><input type="checkbox" name="cms_delete_messy_dashboard" id="cms_delete_messy_dashboard" <?php if(!get_option('cms_delete_messy_dashboard')) { echo 'checked'; } ?> /> <?php _e('Clear the messy dashboard','cms'); ?></label></p>
        <p>&nbsp;</p>
        <p><strong><?php _e('Extra options','cms'); ?></strong></p>
        <p><label for="cms_adjust_menu"><input type="checkbox" name="cms_adjust_menu" id="cms_adjust_menu" <?php if(!get_option('cms_adjust_menu')) { echo 'checked'; } ?> /> <?php _e('Rename and adjust the position of the plugin menu items','cms'); ?></label></p>
        <p><label for="cms_keep_nag"><input type="checkbox" name="cms_keep_nag" id="cms_keep_nag" <?php if(!get_option('cms_keep_nag')) { echo 'checked'; } ?> /> <?php _e('Remove the update reminder for users lower than admin','cms'); ?></label></p>
        <p><label for="cms_minimum_level_general"><?php _e("Minimum user level to watch general options",'cms'); ?> (<a target="_blank" href="http://codex.wordpress.org/Roles_and_Capabilities#Role_to_User_Level_Conversion"><?php _e('roles','cms'); ?></a>)<br /><input class="small-text" type="text" name="cms_minimum_level_general" id="cms_minimum_level_general" value="<?php echo $minLevelGeneral?>" /></label></p>
        <p><label for="cms_minimum_level"><?php _e("Minimum user level to watch admin options",'cms'); ?> (<a target="_blank" href="http://codex.wordpress.org/Roles_and_Capabilities#Role_to_User_Level_Conversion"><?php _e('roles','cms'); ?></a>)<br /><input class="small-text" type="text" name="cms_minimum_level" id="cms_minimum_level" value="<?php echo $minLevel?>" /></label></p>
        <p>&nbsp;</p>
    </div>
    <div id="plugin_checker">
        <strong><?php _e('Plugin checker','cms'); ?></strong>
        <?php
			$pluginList = '';
			//check which plugins are installed
			
			if( ! function_exists('init_reus_menu') ) { 
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/reusables/" target="_blank">WP Reusables</a></p>';			
			}
			if ( ! function_exists('tadv_admin_head') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/tinymce-advanced/" target="_blank">tinyMCE Advanced</a></p>';
			}
			if ( ! function_exists('wpcf7_plugin_path') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/contact-form-7/" target="_blank">Contact form 7</a></p>';
			}
			if ( ! class_exists('nggLoader') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/nextgen-gallery/" target="_blank">NextGEN Gallery</a></p>';
			}
			if ( ! function_exists('bcn_display') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/breadcrumb-navxt/" target="_blank">Breadcrumb NavXT</a></p>';
			}
			if ( ! class_exists('ps_auto_sitemap') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/ps-auto-sitemap/" target="_blank">PS Auto Sitemap</a></p>';
			}
			if ( ! function_exists('_cman_php_warning') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/capsman/" target="_blank">Capability Manager</a></p>';
			}
			if ( ! class_exists('GoogleSitemapGeneratorLoader') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank">Google XML Sitemap</a></p>';
			}
			if ( ! function_exists('aioseop_get_version') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/all-in-one-seo-pack/" target="_blank">All in one SEO</a></p>';
			}
			if ( ! class_exists('Rb_Internal_Links') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/rb-internal-links/" target="_blank">RB Internal links</a></p>';
			}
			if ( ! function_exists('ga_admin_init') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/google-analyticator/" target="_blank">Google Analyticator</a></p>';
			}
			if ( ! class_exists('SearchEverything') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/search-everything/" target="_blank">Search everything</a></p>';
			}
			if ( ! class_exists('MaintenanceMode') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/maintenance-mode/" target="_blank">Maintenance mode</a></p>';
			}
			if ( ! function_exists('WPMailGuard_wp_head') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/wp-email-guard/" target="_blank">WP E-mail guard</a></p>';
			}
			if ( ! function_exists('ep_exclude_pages') ) {
				$pluginList .= '<p><a href="http://wordpress.org/extend/plugins/exclude-pages/" target="_blank">Exclude pages</a></p>';
			}
			
			if(empty($pluginList)) {
				echo '<p><em>'.__('You have all recommended plug-ins installed.','cms').'</em></p>';
			} else {
				echo '<p><em>'.__('I recommend these plug-ins if you want to use Wordpress as CMS:','cms').'</em></p>';
				echo $pluginList;
			}
		?>
    </div>
    </div>
    <?php } ?>
    <p><input type="submit" class="button-primary" name="cms_settings_save" value="<?php _e("Save changes","cms"); ?>" /></p>
    </form>
</div>
</div>
<?php } 

function process_cms_menu_adjustments() {
	if($_POST['cms_settings_save']) {
		update_option('blog_erin','0');
		update_option('reacties_erin','0');
		update_option('cms_use_links','0');
		if($_POST['blog_inschakelen'] == 'on') {
			update_option('blog_erin','1');
		}
		if($_POST['reacties_inschakelen'] == 'on') {
			update_option('reacties_erin','1');
		}
		if($_POST['cms_use_links'] == 'on') {
			update_option('cms_use_links','1');
		}
		
		if($_POST['faq_inschakelen'] == 'on') {
			update_option('faq_inschakelen','0');
		} else {
			update_option('faq_inschakelen','1');
		}
		
		if($_POST['cms_adjust_menu'] == 'on') {
			update_option('cms_adjust_menu','0');
		} else {
			update_option('cms_adjust_menu','1');
		}
		
		if($_POST['cms_keep_nag'] == 'on') {
			update_option('cms_keep_nag','0');
		} else {
			update_option('cms_keep_nag','1');
		}
		
	}	
}

add_action('admin_menu', 'process_cms_menu_adjustments');


function process_cms_new_logo() {
	if($_POST['cms_remove_logo']) {
		delete_option('cms_use_custom_logo');
	}
	
	if($_POST['cms_settings_save']) {
		if($_FILES['logo_uploaden']['tmp_name']) {
			$pluginPath = PLUGINDIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
			move_uploaded_file($_FILES['logo_uploaden']['tmp_name'],'../'.$pluginPath.'/images/logo.png');
			update_option('cms_use_custom_logo','1');
		}
	}	
	if($_POST['blog_naam']) {
		update_option('blogname',$_POST['blog_naam']);
	}
	if($_POST['cms_minimum_level']) {
		if(is_numeric($_POST['cms_minimum_level'])) {
			update_option('cms_minimum_level',$_POST['cms_minimum_level']);
		}
	}
	if($_POST['cms_minimum_level_general']) {
		if(is_numeric($_POST['cms_minimum_level_general'])) {
			update_option('cms_minimum_level_general',$_POST['cms_minimum_level_general']);
		}
	}
	if($_POST['cms_settings_save']) {
		if($_POST['cms_trendwerk_stijl'] == 'on') {
			update_option('cms_trendwerk_stijl','0');
		} else {
			update_option('cms_trendwerk_stijl','1');
		}
		
		if($_POST['cms_tinymce_stijl'] == 'on') {
			update_option('cms_tinymce_stijl','0');
		} else {
			update_option('cms_tinymce_stijl','1');
		}
		
		if($_POST['cms_page_order'] == 'on') {
			update_option('cms_page_order','0');
		} else {
			update_option('cms_page_order','1');
		}
			
		if($_POST['cms_multiple_content'] == 'on') {
			update_option('cms_multiple_content','0');
		} else {
			update_option('cms_multiple_content','1');
		}
		
		if($_POST['other-menu-title-off'] == 'on') {
			update_option('other-menu-title-off','0');
		} else {
			update_option('other-menu-title-off','1');
		}
		
		if($_POST['cms_delete_messy_dashboard'] == 'on') {
			update_option('cms_delete_messy_dashboard','0');
		} else {
			update_option('cms_delete_messy_dashboard','1');
		}
		
		if($_POST['cms_dashboard_links']) {
			update_option('cms_dashboard_links',$_POST['cms_dashboard_links']);
		} else {
			delete_option('cms_dashboard_links');
		}
		
		if($_POST['cms_dashboard_feed_link']) {
			update_option('cms_dashboard_feed_link',$_POST['cms_dashboard_feed_link']);
		} else {
			delete_option('cms_dashboard_feed_link');
		}
		
	}
}

add_action('admin_head','process_cms_new_logo');
?>