<?php
/*
Weaver II Pro Runtime Library

This code is Copyright 2011 by Bruce Wampler, all rights reserved.
This code is licensed under the terms of the accompanying license file: license.html.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/
//==================== Pro RUN TIME ==========

function weaverii_init_base() {
    // if (strtotime(date('Y-m-d'))>strtotime('2012-11-30'))return false;
    return true;
}

function weaverii_pro_wp_head() {

// === include Google Fonts links
    $google = weaverii_getopt('fonts_google_font_list');
    if ($google) {
	echo ("<!-- Weaver II Pro Google Fonts -->\n");
	echo $google;
    }

}

function weaverii_pro_enqueue_scripts($vers='1') {
    if (function_exists('weaveriip_slider_scripts')) weaveriip_slider_scripts($vers);
    if (function_exists('weaveriip_showhide_scripts')) weaveriip_showhide_scripts($vers);
    if (function_exists('weaveriip_moreopts_scripts')) weaveriip_moreopts_scripts($vers);
    // kludgy fix for enqueue-script
    global $weaverii_cur_page_ID;
    global $post;

    if (!$weaverii_cur_page_ID &&is_object($post))
	$weaverii_cur_page_ID = get_the_ID();

    weaverii_masonry('enqueue-script');
}

function weaverii_masonry($act = false) {
    global $weaverii_cur_template;

    $is_pt = false;

    if (strpos($weaverii_cur_template,'paget-posts.php') !== false) {
	    $is_pt = true;
    }
    if (is_singular() && ! $is_pt) {	// don't emit anything for non-blog pages
	return false;
    }

    $usem = weaverii_get_per_page_value('wvr_pwp_masonry');	// per page to override...
    if ($usem < 2)
	$usem = weaverii_getopt('masonry_cols');
    if ($usem < 2) {
	return false;
    }
    switch ($act) {
	case 'begin-posts':	// wrap all posts
	    echo '<div id="blog-posts" class="cf">';
	    break;
	case 'begin-post' :	// wrap one post
	    global $weaverii_cur_post_id;
	    $weaverii_cur_post_id = get_the_ID();// we need to know now
	    if (weaverii_is_checked_post_opt('wvr_masonry_span2')) {	// span 2 columns
		$usem .= '-span-2';
	    }
	    echo '<div class="cf blog-post blog-post-cols-' . $usem . '">';	// for masonry
	    break;
	case 'end-post':	// end of one post
	    echo "</div> <!-- .blog-post -->\n";
	    break;
	case 'end-posts':	// end of all posts
	    echo '</div> <!-- #blog-posts -->' . "\n";
	    break;
	case 'invoke-code':
?>
<script type='text/javascript'>
jQuery(function(){var $container=jQuery('#blog-posts');$container.imagesLoaded(function(){
$container.masonry({itemSelector:'.blog-post'});});});
jQuery(window).resize(function(){jQuery('#blog-posts').masonry({itemSelector:'.blog-post'});});
</script>
<?php
	    break;

	case 'enqueue-script':
	    $url =  trailingslashit(get_template_directory_uri());
	    wp_enqueue_script('weaverMasonry', $url.'includes/pro/masonry/jquery.masonry.min.js',array(),WEAVERII_VERSION);
	    break;
    }	// end switch
    return true;
}

function weaverii_pro_output_style($sout) {
    global $weaverii_header_who;
// === Fonts from Pro Fonts
    weaverii_f_write($sout,"/* Weaver II Pro Fonts */\n");

    global $weaverii_fonts_defs;
    foreach ($weaverii_fonts_defs as $option => $val) {
	$fonts = weaverii_getopt($val['id']);
	if ($fonts) {
	    $rule = $val['tag'] != '+++' ? $val['tag'] : '';
	    weaverii_f_write($sout,$rule . $fonts . "\n");
	}
    }

// ======================= background areas ============================
   $val = weaverii_getopt('_wii_bg_fullsite_url');
   if ($val != '') {
        weaverii_f_write($sout,
"html {background: url($val) no-repeat center center fixed; -webkit-background-size: cover;
-moz-background-size: cover;-o-background-size: cover;background-size: cover;}
body {background-color:transparent;}\n");
	weaverii_f_write($sout,
"#ie8 html, #ie7 html {background:none;}
#ie8 body ,#ie7 body { background-image: url('$val'); background-attachment: fixed; }\n");
// #ie8 body, #ie7 body {background-image: url($val)}\n");
    /* IE8, IE7 scaling - doesn't look good, but here it is...
	  -ms-filter:\"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$val', sizingMethod='scale')\";
filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$val', sizingMethod='scale');
    */

   }
   weaveriip_bgimg_style($sout,'_wii_bg_wrapper_url','#wrapper');
   weaveriip_bgimg_style($sout,'_wii_bg_header_url','#branding');
   weaveriip_bgimg_style($sout,'_wii_bg_main_url','#main');
   weaveriip_bgimg_style($sout,'_wii_bg_container_url','#container_wrap');
   weaveriip_bgimg_style($sout,'_wii_bg_content_url','#content');
   weaveriip_bgimg_style($sout,'_wii_bg_page_url','#container .page');
   weaveriip_bgimg_style($sout,'_wii_bg_post_url','#container .post');
   weaveriip_bgimg_style($sout,'_wii_bg_widgets_left_url','#sidebar_wrap_left');
   weaveriip_bgimg_style($sout,'_wii_bg_widgets_right_url','#sidebar_wrap_right');
   weaveriip_bgimg_style($sout,'_wii_bg_footer_url','#colophon');


    weaveriip_display_none_style($sout,'wii_hide_p_category','#content .category-title, #content .category-archive-meta');
    weaveriip_display_none_style($sout,'wii_hide_p_tag','#content .tag-title, #content .category-archive-meta');
    weaveriip_display_none_style($sout,'wii_hide_p_author','#content .author-title');
    weaveriip_display_none_style($sout,'wii_hide_p_date','#content .archive-title');
    weaveriip_display_none_style($sout,'wii_hide_p_search', '#content .search-results');

    $add_css = '';
    switch ($weaverii_header_who) {
	case 'category':
	    $add_css = weaverii_getopt('wii_p_category_css');
	    break;
	case 'tag':
	    $add_css = weaverii_getopt('wii_p_tag_css');
	    break;
	case 'author':
	    $add_css = weaverii_getopt('wii_p_author_css');
	    break;
	case 'archive':
	    $add_css = weaverii_getopt('wii_p_date_css');
	    break;
	case 'search':
	    $add_css = weaverii_getopt('wii_p_search_css');
	    break;
	default:
	    break;
    }
    if ($add_css != '') {
	weaverii_f_write($sout,"/* Weaver II custom css for $weaverii_header_who */\n$add_css \n");
    }

    if (function_exists('weaveriip_slider_output_style')) weaveriip_slider_output_style($sout);
    if (function_exists('weaveriip_extra_menu_output_style')) weaveriip_extra_menu_output_style($sout);
    if (function_exists('weaveriip_totalcss_output_style')) weaveriip_totalcss_output_style($sout);	// always last
}

function weaveriip_header_insert() {
    // This is called from header.php

    if (function_exists('weaveriip_header_gadget_insert')) weaveriip_header_gadget_insert();
    if (function_exists('weaveriip_moreopts_header_insert')) weaveriip_moreopts_header_insert();
}

function weaveriip_bgimg_style($sout,$id,$name) {
    $val = weaverii_getopt($id);
    if ($val != '') {
	$fixid = str_replace('_url','',$id);
	$rpt = weaverii_getopt($fixid . '_rpt');
	if (strlen($rpt) < 6) $rpt = 'repeat';	// set to default
	weaverii_f_write($sout, $name . '{background-image:url('. apply_filters('weaverii_css',parse_url($val,PHP_URL_PATH)) . ');background-repeat:' . $rpt . ';}' . "\n");
    }
}

function weaveriip_display_none_style($sout,$id,$name) {
    $val = weaverii_getopt($id);
    if ($val)
	weaverii_f_write($sout, $name . '{display:none;}' . "\n");

}

function weaveriip_help_link($link, $info) {
    $t_dir = weaverii_relative_url('');

    $pp_help =  '<a href="' . $t_dir . 'includes/pro/' . $link . '" target="_blank" title="' . $info . '">'
	. '<img class="entry-cat-img" src="' . $t_dir . 'images/icons/help-1.png" style="position:relative; top:4px; padding-left:4px;" alt="help" /></a>';
    echo($pp_help);
}

// ============================ OPTIONS ===========================

function weaverii_opt_cache($switch = null) {
    // load the options cache - from regular or mobile depending...
    global $weaverii_opts_cache;

    if (isset($switch)) {
	$weaverii_opts_cache = $switch;
    } else if (!$weaverii_opts_cache) {
	$weaverii_opts_cache = apply_filters('weaverii_switch_theme',
	    get_option( apply_filters('weaver_options','weaverii_settings') ,array()));	// start with the default
    }

    if (isset($weaverii_opts_cache['_wii_mobile_alt_theme'])
	&& $weaverii_opts_cache['_wii_mobile_alt_theme']
	&& $weaverii_opts_cache['_wii_mobile_alt_theme'] != 'saved_mobile'
	&& !is_admin()
	&& weaverii_use_mobile('mobile')) { // want mobile alternative - but not on sim...
	$sim = $weaverii_opts_cache['_wii_sim_mobile'];
	if (!$sim || $sim == 'none') {
	    $mobile_opts = get_option( apply_filters('weaver_options','weaverii_settings_mobile') );	// only used in Pro theme...
	    if ($mobile_opts !== false) {
		$weaverii_opts_cache = $mobile_opts;
	    }
	}
    }
}

function weaverii_pro_opt_cache($switch = null) {
    // load the options cache - from regular or mobile depending...
    global $weaverii_pro_opts;

    if (isset($switch)) {
	$weaverii_pro_opts = $switch;
    } else if (!$weaverii_pro_opts) {
	$weaverii_pro_opts = apply_filters('weaverii_switch_theme_pro',
	    get_option( apply_filters('weaver_options','weaverii_pro') ,array()));
    }
    if (weaverii_getopt_checked('_wii_mobile_alt_theme') && !is_admin() && weaverii_use_mobile('mobile')) {
	    $weaverii_pro_opts = get_option( apply_filters('weaver_options','weaverii_pro_mobile') );
	if ($weaverii_pro_opts === false)
	    $weaverii_pro_opts = get_option( apply_filters('weaver_options','weaverii_pro') );
    }
}

function weaverii_pro_setpost_checkbox($name) {
    if (isset($_POST[$name])) weaverii_pro_setopt($name, 'checked');
	else weaverii_pro_setopt($name, false);
}

function weaverii_pro_getopt($name) {
    global $weaverii_pro_opts;
    weaverii_pro_opt_cache();

    if (isset($weaverii_pro_opts[$name]))
        return $weaverii_pro_opts[$name];
    else
        return false;
}

function weaverii_pro_setopt($name, $value) {
    global $weaverii_pro_opts;
    if (!$weaverii_pro_opts)
        $weaverii_pro_opts = get_option( apply_filters('weaver_options','weaverii_pro') ,array());
    $weaverii_pro_opts[$name] = $value;
}

function weaverii_pro_isset($name){
    global $weaverii_pro_opts;

    weaverii_pro_opt_cache();

    $val = isset($weaverii_pro_opts[$name]);
    return $val;
}

function weaverii_pro_update_options($id) {
    global $weaverii_pro_opts;
    if (!$weaverii_pro_opts)
        $weaverii_pro_opts = get_option( apply_filters('weaver_options','weaverii_pro') ,array());

    weaverii_wpupdate_option('weaverii_pro',$weaverii_pro_opts);
    weaverii_save_opts('weaverii_pro');		// need to re-write the stylesheet
}

function weaveriip_default_int($value, $min, $max, $default='') {
    if (!is_numeric($value) || !is_int((int)$value)) {
	return $default;
    } else {
 	if ($value == '' || (int)$value < $min || (int)$value > $max)
            return $default;
	else
	    return $value;
    }
}

function weaveriip_default_dec($value, $min, $max, $default='') {
    if (!is_numeric($value)) {
	return $default;
    } else {
 	if ($value == '' || $value < $min || $value > $max)
            return $default;
	else
	    return $value;
    }
}

function weaveriip_clear_opts() {
    global $weaverii_pro_opts;
    $weaver_pro_opts = false;
    delete_option( apply_filters('weaver_options','weaverii_pro') );
    delete_option( apply_filters('weaver_options','weaverii_pro_mobile') );
    delete_option( apply_filters('weaver_options','weaverii_settings_mobile') );
}

function weaveriip_save_opts_backup() {
    global $weaverii_pro_opts;

    if (!$weaverii_pro_opts)
        $weaverii_pro_opts = get_option( apply_filters('weaver_options','weaverii_pro'),array() );
    weaverii_wpupdate_option('weaverii_pro_backup',$weaverii_pro_opts);
}

function weaveriip_restore_opts_backup() {
   global $weaverii_pro_opts;
   $saved = get_option( apply_filters('weaver_options','weaverii_pro_backup') ,array());
   if (!empty($saved)) {
	$weaver_pro_opts = $saved;
	weaverii_wpupdate_option('weaverii_pro',$weaver_pro_opts);
   }
}

function weaveriip_moreopts_scripts($vers='1') {
}

function weaveriip_bracket($txt,$head,$tail){
    $lead = strpos($txt, $head);
    if ($lead === false || $lead != 0)
	$txt = $head . $txt;
    $end = strrchr($txt, $tail);
    if ($end === false || strlen($end) > 1)
	$txt = $txt . $tail;
    return $txt;
}

require_once( dirname( __FILE__ ) . '/globals-runtime-pro.php' );
/* ------------------------------------ Weaver II Pro FEATURE IMPLEMENTATIONS ------------------------ */

if (weaverii_getopt('_wii_show_totalcss'))		// Total CSS
    require_once('weaverii-pro-total-css.php');

if (!weaverii_getopt('_wii_hide_slider')) {		// Slider Menu
    require_once('weaverii-pro-code-slider.php');}
if (!weaverii_getopt('_wii_hide_extramenus'))	// Extra Menus
    require_once('weaverii-pro-code-extramenu.php');


if (!weaverii_getopt('_wii_hide_linkbuttons'))	// Link Buttons
    require_once('weaverii-pro-code-linkbuttons.php');
if (!weaverii_getopt('_wii_hide_socialbuttons')) // Social Buttons
    require_once('weaverii-pro-code-social.php');
if (!weaverii_getopt('_wii_hide_headergadgets')) // Header Gadgets
    require_once('weaverii-pro-code-headerg.php');

if (!weaverii_getopt('_wii_hide_widgetarea'))	// Widget Area
    require_once('weaverii-pro-sc-widget-area.php');
if (!weaverii_getopt('_wii_hide_searchbox'))	// Search Form
    require_once('weaverii-pro-sc-search.php');
if (!weaverii_getopt('_wii_hide_showfeed'))	// Show Feed
    require_once('weaverii-pro-sc-feed.php');
if (!weaverii_getopt('_wii_hide_popuplink'))	// Popup Link
    require_once('weaverii-pro-sc-popup.php');
if (!weaverii_getopt('_wii_hide_showhide'))	// Show/Hide Text
    require_once('weaverii-pro-sc-showhide.php');
if (!weaverii_getopt('_wii_hide_commentpolicy')) // Comment Policy
    require_once('weaverii-pro-sc-disclaimer.php');
if (!weaverii_getopt('_wii_hide_shortcoder'))	// Shortcoder
    require_once('weaverii-pro-code-shortcoder.php');

if (weaverii_getopt('_wii_show_php'))			// PHP
    require_once('weaverii-pro-sc-php.php');

?>
