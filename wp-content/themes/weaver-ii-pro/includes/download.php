<?php
// will down load current settings based on db setting

$wp_root = dirname(__FILE__) .'/../../../../';
if(file_exists($wp_root . 'wp-load.php')) {
	require_once($wp_root . "wp-load.php");
} else if(file_exists($wp_root . 'wp-config.php')) {
	require_once($wp_root . "wp-config.php");
} else {
	exit;
}

@error_reporting(0);
    $wii_is_theme = false;
    $convert = false;

    if (isset($_REQUEST['_wpnonce']))
	$nonce = $_REQUEST['_wpnonce'];
    else if (isset($_REQUEST['_wpnoncet'])) {
	$nonce = $_REQUEST['_wpnoncet'];
	$wii_is_theme = true;
    } else if (isset($_REQUEST['_wpnoncea'])) {
	$nonce = $_REQUEST['_wpnoncea'];
	$convert = true;
    }
    else if (isset($_REQUEST['_wpnonceat'])) {
	$nonce = $_REQUEST['_wpnonceat'];
	$wii_is_theme = true;
	$convert = true;
    }
    else
	$nonce = '';

    if (! wp_verify_nonce($nonce, 'wii_download')) {
	@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
	wp_die('Sorry - download must be initiated from admin panel.');
    }

    if (headers_sent()) {
	@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
	wp_die('Headers Sent: The headers have been sent by another plugin - there may be a plugin conflict.');
    }


    $wii_opts = get_option( apply_filters('weaver_options','weaverii_settings') ,array());
    $wii_pro_opts = get_option( apply_filters('weaver_options','weaverii_pro') ,array());
    $wii_save = array();
    $wii_save['weaverii_base'] = $wii_opts;

    if ($wii_is_theme) {
	$wii_header = 'W2T-V01.00';
	$wii_fn = 'weaver-ii-theme-settings.w2t';
	foreach ($wii_opts as $opt => $val) {
	    if ($opt[0] == '_')
		$wii_save['weaverii_base'][$opt] = false;
	}
	$wii_save['weaverii_pro'] = array();
    }
    else {
	$wii_header = 'W2B-V01.00';			/* Save all settings: 10 byte header */
	$wii_fn = 'weaver-ii-backup-settings.w2b';
	$wii_save['weaverii_pro'] = $wii_pro_opts;
    }

    if ($convert) {	// convert to aspen format
	$not_in_aspen = array (
    'wii_premain_insert', 'wii_precontent_insert', 'wii_precomments_insert', 'wii_postcomments_insert', 'wii_prefooter_insert',
    'wii_presidebar_left_insert', 'wii_presidebar_right_insert', 'wii_perpagewidgets', 'wii_style_version', 'wii_subtheme',
    'wii_theme_image', 'wii_hide_old_weaver' =>'', 'wii_version_id', '_wii_no_final_div', '_wii_inline_style', '_wii_save_mods_basic',
    '_wii_save_mods_pro', 'hdr_headertop_padding__T', 'hdr_headertop_padding__B', 'wii_title_on_header_xy_X',
    'wii_title_on_header_xy_Y', 'wii_title_on_header_xy_desc_X', 'wii_title_on_header_xy_desc_Y', 'wii_header_width_int',
    'wii_menu_spacing_int', 'wii_menu_leftpad_int', 'wii_menu_leftpad2_int', 'wii_separator_width_int', 'wii_menu_liwidth',
    'wii_menu_addhtml-left', 'wii_info_html1', 'wii_info_html2', 'wii_info_html3', 'wii_contentlist_bullet_custom',
    'wii_widgetlist_bullet_custom', 'sb_container_bgcolor', 'sb_container_bgcolor_css', 'wii_border_width_int', 'wii_border_style',
    'wii_search_msg', '_wii_metainfo', '_wii_imgsrc_url', '_wii_apple_touch_icon_url', '_wii_sim_mobile', '_wii_custom_style',
    '_wii_comment_reply_msg', '_wii_hdr_widg_bgcolor', '_wii_hdr_widg_bgcolor_css', '_wii_hdr_widg_fontsize', '_wii_hdr_widg_h_int',
    '_wii_hdr_widg_1_bgcolor', '_wii_hdr_widg_1_bgcolor_css', '_wii_hdr_widg_1_w_int', '_wii_hdr_widg_1_w_mobile_int',
    '_wii_hdr_widg_2_bgcolor', '_wii_hdr_widg_2_bgcolor_css', '_wii_hdr_widg_2_w_int', '_wii_hdr_widg_2_w_mobile_int',
    '_wii_hdr_widg_3_bgcolor', '_wii_hdr_widg_3_bgcolor_css', '_wii_hdr_widg_3_w_int', '_wii_hdr_widg_3_w_mobile_int',
    '_wii_hdr_widg_4_bgcolor', '_wii_hdr_widg_4_bgcolor_css', '_wii_hdr_widg_4_w_int', '_wii_hdr_widg_4_w_mobile_int',
    '_wii_bg_fullsite_url', '_wii_bg_wrapper_url', '_wii_bg_header_url', '_wii_bg_main_url', '_wii_bg_container_url',
    '_wii_bg_content_url', '_wii_bg_page_url', '_wii_bg_post_url', '_wii_bg_widgets_left_url', '_wii_bg_widgets_right_url',
    '_wii_bg_footer_url', 'wiip_fonts_content', 'wiip_fonts_title', 'wiip_fonts_menubar', 'wiip_fonts_menu_vertical',
    'wiip_fonts_menu_horizontal', 'wiip_fonts_titles-headings', 'wiip_fonts_site_title', 'wiip_fonts_site_desc',
    'wiip_fonts_page_title', 'wiip_fonts_post_entry_title', 'wiip_fonts_entry_format', 'wiip_fonts_wdg_title',
    'wiip_fonts_page_content', 'wiip_fonts_post_content', 'wiip_fonts_wdg_content', 'wiip_fonts_post_info', 'wiip_fonts_navigation',
    'wiip_fonts_captions', 'wiip_fonts_links', 'wiip_fonts_meta_links', 'wiip_fonts_wdg_links', 'wiip_fonts_custom1',
    'wiip_fonts_custom2', 'wiip_fonts_custom3', 'wiip_fonts_custom4', 'font_font_family', 'font_font_weight', 'font_font_style',
    'font_font_variant', 'font_font_size', 'font_font_size_value', 'font_font_size_units', 'font_google_link',
    'font_google_font_code', 'font_generate_font_code', 'fonts_google_font_list', 'fonts_edit_lines', '_wvr_custom_posted_on',
    '_wvr_custom_posted_in', '_wvr_custom_posted_on_single', '_wvr_custom_posted_in_single', '_wvr_mobile_fullmsg',
    '_wvr_mobile_mobilemsg', '_wii_mobile_site_title', '_wii_mobile_header_url', '_wii_mobile_tablet_header_url', '_wii_mobile_css'
);
	$defaults = array(	// if Weaver II didn't have a default, Aspen need one for these
    'site_fontsize_int' => '12',
    'site_line_height_dec' => '1.5',
    'site_fontsize_mobile_int' => '12',
    'wrapper_padding' => '0',
    'widget_size_int' => '120',
    'widget_top' => '5',
    'widget_bottom_indent_int' => '5',
    'border_color' => '#222',
    'title_font_size' => '300',
    'title_max_w' => '90',
    'desc_font_size' => '133',
    'desc_position_xy_X' => '10',
    'desc_max_w' => '90',
    'menu_height_int' => '38',
    'footer_border_int' => '4',
    'widget_top_indent_int' => '5',
    'widget_top_margin_T' => '10',
    'widget_bottom_indent_int' => '5',
    'widget_widget_margin_T' => '10',
    'widget_widget_margin_B' => '0',
    'widget_padding_int' => '10',
    'widget_top_t' => '4',
    'menubar_text_size_int' => '133',
    'infob_text_size_int' => '110',
    'infob_padding_T' => '4',
    'infob_padding_B' => '4',
    'content_size_int' => '133',
    'entrytitle_size_int' => '150',
    'content_top_dec' => '1.5',
    'content_p_list_dec' => '1.5',
    'info_font_size_int' => '80',
    'footer_size_int' => '100'
    );
	// conversions:   	wii_ -->  --> ''
	//			wvr_ -> ''
	$copy = array();
	foreach ($wii_save['weaverii_base'] as $key => $val) {
	    if (in_array($key, $not_in_aspen))
		continue;
	    if ($key == 'wii_weaverii_tables') {
		$new_key = 'aspen_tables';
	    } else if ($key == 'wii_hide_post_bubble') {
		$new_key = 'pp_hide_post_bubble';
	    } else {
		$new_key = str_replace('wii_','',$key);
		$new_key = str_replace('wvr_','',$new_key);
	    }
	    $copy[$new_key] = $wii_opts[$key];	// set to new value
	}
	foreach ($defaults as $def_key => $def_val) {	// force some to have Aspen defaults
	    if ( !isset($copy[$def_key]) || $copy[$def_key] == '') {
		$copy[$def_key] = $def_val;
	    }
	}
	$wii_save = array();
	$wii_save['aspen_base'] = $copy;
	unset($wii_save['weaverii_pro']);	//don't want this

	if ($wii_is_theme) {
	    $wii_fn = 'aspen-converted-settings.ath';
	    $wii_header = 'ATH-V01.00';
	}
	else {
	    $wii_fn = 'aspen-converted-settings.abu';
	    $wii_header = 'ABU-V01.00';
	}
    }

    $wii_settings = $wii_header . serialize($wii_save);	/* serialize full set of options right now */

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$wii_fn);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($wii_settings));

    echo $wii_settings;
    exit;
?>
