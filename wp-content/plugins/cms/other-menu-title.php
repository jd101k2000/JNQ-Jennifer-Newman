<?php
function pas_titel_aan($pages) {
	//verander naam van page aan de front-end
	$bail_out = ( ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) || ( strpos( $_SERVER[ 'PHP_SELF' ], 'wp-admin' ) !== false ) );
	if($bail_out) return $pages;
	
	for($i=0; $i<count($pages); $i++) {
		$page = get_page($pages[$i]->ID);
		$other_menu_title = get_post_meta($page->ID,'other-menu-title');
		if($other_menu_title[0]) {
			$pages[$i]->post_title = $other_menu_title[0];
		}
	}
	
	return $pages;
}
add_filter('get_pages','pas_titel_aan');

//backend
function menu_title_editor() {
	global $post;
	$custom = get_post_custom($post->ID);
	?>
		<input type="text" name="menu_title" id="menu_title" style="width:100%" value="<?php echo $custom['other-menu-title'][0]; ?>" />
	<?php
}

function voeg_menu_title_editor_toe() {
	add_meta_box('menu_title_editor','Menu title','menu_title_editor','page','side','low');
}
add_action('add_meta_boxes','voeg_menu_title_editor_toe');

function menu_title_opslaan($post_id) {
	if(!wp_is_post_autosave($post_id)) {
		update_post_meta($post_id,'other-menu-title',$_POST['menu_title']);
	}
}
add_action('save_post','menu_title_opslaan');
?>