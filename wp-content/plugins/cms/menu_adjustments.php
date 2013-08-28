<?php
/*
=========
Adjust the menu
=========
*/
 
if (!function_exists('cms_change_menu')) {
	function cms_change_menu() {
		global $menu, $submenu;
		// Unset Post & Page menus so we can change them
		
		$menu_pages = $menu[20];
		$menu_posts = $menu[5];
		
		unset($menu[5]);
		unset($menu[20]);
		
		//"Links" of "Koppelingen" uit het menu verwijderen
		if(!get_option('cms_use_links')) {
			unset($menu[15]);
		}
		if(get_option('reacties_erin') == 0) {
			unset($menu[25]);
		}
			
		// Change menu order to reflect new positions
		if(current_user_can('edit_pages')) {
			$menu[5] = $menu_pages;
		}
		
		if(get_option('blog_erin') == 1) {
			$menu[20] = $menu_posts;
		}

		if(!get_option('cms_adjust_menu')) {
			$post_types = get_post_types();
			
			$vanaf = count($post_types) + 25;
			
			//separator tussen de menu's toevoegen
			$menu[$vanaf][0] = "";
			$menu[$vanaf][1] = "read";
			$menu[$vanaf][2] = "separator2";
			$menu[$vanaf][3] = "";
			$menu[$vanaf][4] = "wp-menu-separator";
			
			$i=$vanaf+1;
			foreach($menu as $c=>$menu_item) { //move some of my recommended plugins up, so its easy for the client to control the extra functions
				if($menu_item[2] == 'wpcf7' || $menu_item[2] == 'nextgen-gallery' || $menu_item[2] == 'reusables/edit.php' || $menu_item[2] == 'faq-questions') {
					$menu[$i] = $menu_item;
					if($menu_item[2] == 'wpcf7') {
						$menu[$i][0] = __('Forms','cms');
					} else if($menu_item[2] == 'nextgen-gallery') {
						$menu[$i][0] = __('Gallery','cms');
					} else if($menu_item[2] == 'reusables/edit.php') {
						$menu[$i][0] = __('Global content','cms');
					} else if($menu_item[2] == 'faq-questions') {
						$menu[$i][0] = __('FAQ','cms');
						if(get_option('faq_inschakelen')) {
							unset($menu[$i]);	
						}
					}
					unset($menu[$c]);
					$i++;
					if($i>=59) { //op 59 begint weergave enz
						break;
					}
				}
			}
		}
	}
}

add_action('admin_menu', 'cms_change_menu',100); //priority is low
?>