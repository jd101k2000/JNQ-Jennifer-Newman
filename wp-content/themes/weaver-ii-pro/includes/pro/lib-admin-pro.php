<?php
/*
Rwwvr Pro Fonts

This code is Copyright 2011 by Bruce Wampler, all rights reserved.
This code is licensed under the terms of the accompanying license file: license.html.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/
/* =========================== fonts admin code =========================== */

function weaverii_fonts_pro_admin() {
    /* options - these are coded into Weaver II
      'wii_post_pretitle', 'wii_post_prebody', 'wii_post_postbody'
    */
    global $weaverii_fonts_defs;

    $weaverii_std_fonts = array( '','Google Web Font',
	'"Helvetica Neue", Helvetica, sans-serif',
	'Arial,Helvetica,sans-serif',
	'Verdana,Arial,sans-serif',
	'Tahoma, Arial,sans-serif',
	'"Arial Black",Arial,sans-serif',
	'"Avant Garde",Arial,sans-serif',
	'"Comic Sans MS",Arial,sans-serif',
	'Impact,Arial,sans-serif',
	'"Trebuchet MS", Helvetica, sans-serif',
	'"Century Gothic",Arial,sans-serif',
	'"Lucida Grande",Arial,sans-serif',
	'Univers,Arial,sans-serif',
	'"Times New Roman",Times,serif',

	'"Bitstream Charter",Times,serif',
	'Georgia,Times,serif',
	'Palatino,Times,serif',
	'Bookman,Times,serif',
	'Garamond,Times,serif',

	'"Courier New",Courier',
	'"Andale Mono",Courier'
    );
?>
<script language="javascript" type="text/javascript">

  function weaverii_copy_google_3_4()
  {
    var cur = jQuery('#fonts_google_font_list').val();
    var g3 = jQuery('#font_google_link').val();
    var g4 = jQuery('#font_google_font_code').val();
    var add = g3 + '<!-- ' + g4 + " -->";
    if (cur && cur.indexOf(add) >= 0) {
	alert("That Google Font Definition already added.");
	return;
    }
    var fix = cur + add + "\n";
    jQuery('#fonts_google_font_list').val(fix);
  }

  function weaverii_generate_font_css() {
    var font_font_family = jQuery("#font_font_family").val();
    var font_font_weight = jQuery("#font_font_weight").val();
    var font_font_style = jQuery("#font_font_style").val();
    var font_font_variant = jQuery("#font_font_variant").val();
    var font_font_size = jQuery("#font_font_size").val();
    var font_font_size_value = jQuery("#font_font_size_value").val();
    var font_font_size_units = jQuery("#font_font_size_units").val();
    var g3 = jQuery('#font_google_link').val();
    var g4 = jQuery('#font_google_font_code').val();

    var css = '{';
    if (g4 && g3 && font_font_family == 'Google Web Font' ) {
	css += g4;
    } else if (font_font_family) {
	css += 'font-family:' + font_font_family + ';';
    }

    if (font_font_weight) css += 'font-weight:' + font_font_weight + ';';
    if (font_font_style) css += 'font-style:' + font_font_style + ';';
    if (font_font_variant) css += 'font-variant:' + font_font_variant + ';';

    if (font_font_size_value) css += 'font-size:' + font_font_size_value + font_font_size_units + ';';
    else if (font_font_size) css += 'font-size:' + font_font_size + ';';

    css += '}';
    jQuery('#font_generate_font_code').val(css);
  }
  function weaverii_copy_font_css(destinationid)
  {
    var css = jQuery('#font_generate_font_code').val();
    var cur = jQuery("#"+destinationid).val();
    var paste = cur + css;
    jQuery("#"+destinationid).val(paste);
  }
</script>


    <div><a name="fonts_top"></a>
    <p class='wvr-option-section'>Weaver II Pro - Font Control <?php weaveriip_help_link('pro-help.html#font_control','Font control help'); ?></p>
<?php

    if (weaverii_hide_advanced())
	echo '<p>Advanced Font Control options hidden.</p><div style="display:none;">';

    echo ('&nbsp;|&nbsp;');
    $count = 0;
    foreach ($weaverii_fonts_defs as $option => $row) {
	if ($row['id'][0] == '_') {
	    echo('<a href="#' . $row['id'] . '">' . $row['label'] . '</a>&nbsp;|&nbsp;');
	} else {
	    $count++;
	}
    }

    $tdir = weaverii_relative_url('') . 'includes/pro/';
    $readme = $tdir . 'pro-help.html';
?>
<a href="<?php echo $readme; ?>#font_control" target="_blank"><strong>Font Control Help</strong></a>&nbsp;|
<br />
    <p>The Weaver II Pro Font Control panel gives you fine tuned control over the fonts various elements of your site will use.
    You can use a set of standard Web fonts, or for total flexibility, you can use <em>any</em> of the free
    <?php weaverii_site('/webfonts', 'http://www.google.com','Google Web Fonts'); ?><strong>Google Web Fonts</strong></a>. Once you
    get the hang of using this interface, it is quite easy to specify fonts. However, there is a small learning curve,
    and you really should read the complete instructions in the
    <a href="<?php echo $readme; ?>#font_control" target="_blank">Weaver II Help document</a>!
    </p>
    <p>For best results, <strong>please</strong> follow <span style="color:red;">Steps 1, 2, 3, and 4</span> for each font you want to use. Read
    the instructions for each step carefully.</p>
    <hr />

 	<fieldset class="options">
	    <span style="font-weight:bold; color:blue;">Weaver II Font Style Generator</span>
	    &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $readme; ?>#font_control" target="_blank"><strong>Font Control Help</strong></a><br /><br />
	    <h3><span style="color:red; text-decoration:underline;font-weight:bold;font-size:larger;">Step 1.</span> Specify Font Family and Attributes</h3>
	   <p>You may specify a standard Web font by using the "Specify Standard Font Family" pull-down on the left below,
	   or you may use any Google font by first selecting "Google Web Font" on the left, then filling in 3 and 4 in the
	   Google box on the right. You can use the Font-Weight, Font Style, etc., for either a standard or a Google Font.
	   Then follow Steps 2, 3, and 4 for either standard or Google fonts. You can repeat this process (Steps 1 to 4) to specify
	   different fonts for different elements.</p>

	<div style="float:left;"><span style="font-weight:bold; color:green;">Specify Standard Font Family</span><br />
	    &nbsp;&nbsp;
<?php
	    weaverii_select('font_font_family',$weaverii_std_fonts);
?>
	    <span style="font-weight:bold;color:red;">&nbsp;-OR-&nbsp;</span><br /><hr />
	    <span style="font-weight:bold; color:green;">Font-Weight: </span>
<?php
	    weaverii_select('font_font_weight',array('', 'normal', 'bold', 'bolder', 'lighter',
	      '100', '200', '300', '400', '500', '600', '700', '800', '900'));
?>
	    <br />
	    <span style="font-weight:bold; color:green;">Font Style: &nbsp;&nbsp;</span>
<?php
	    weaverii_select('font_font_style',array('', 'normal', 'italic', 'oblique'));
?>
	    <br />
	    <span style="font-weight:bold; color:green;">Font Variant: </span>
<?php
	    weaverii_select('font_font_variant',array('', 'normal', 'small-caps'));
?>
	    <br />

	    <span style="font-weight:bold; color:green;">Font Size: &nbsp;&nbsp;&nbsp;</span>
<?php
	    weaverii_select('font_font_size',array('', 'Specify value', 'xx-small', 'x-small', 'small', 'medium',
		'large', 'x-large', 'xx-large', 'smaller', 'larger'));
?>
	    <br />
	    Font Size value:
	    <input type="text" style="width:34px;height:24px;" class="regular-text" name="<?php weaverii_sapi_main_name('font_font_size_value'); ?>"
                id="font_font_size_value" value="<?php weaverii_esc_textarea(weaverii_getopt('font_font_size_value')); ?>" />
<?php
	    weaverii_select('font_font_size_units',array('em','pc','pt','px','%'));
?>
	</div>
	<div style="float:left;border:1px solid #aaa;padding:4px;">
    &nbsp;<span style="font-weight:bold; color:green;">Specify Google Web Font Family</span>
    <br /><ol>
	<li><strong>&larr;</strong> Select "Google Web Font" from "<strong>Specify Standard Font Family</strong>" pull-down list on the left.</li>
	<li>Go to <?php weaverii_site('/webfonts', 'http://www.google.com','Google Web Fonts'); ?><strong>Google Web Fonts</strong></a> site to select a font.
	Open the font's <strong><em>Quick-use</em></strong> page.</li>
	<li>Paste Quick-use <strong>#3 &lt;link&gt;</strong> code here:
	<textarea name="<?php weaverii_sapi_main_name('font_google_link'); ?>" id="font_google_link" rows=2 style="width: 300px"><?php
	      weaverii_esc_textarea(weaverii_getopt('font_google_link')); ?></textarea></li>
	<li>Paste Quick-use <strong>#4 CSS</strong> code here: &nbsp;&nbsp;
	<textarea name="<?php weaverii_sapi_main_name('font_google_font_code'); ?>" id="font_google_font_code" rows=1 style="width: 300px"><?php
	      weaverii_esc_textarea(weaverii_getopt('font_google_font_code')); ?></textarea></li>
	<li>Click the<em>Generate Font CSS Definition</em> button,<br /> then click the<em>Paste current Google #3 and #4 to list of Available Google fonts</em> and <em>Save Settings</em> <br />if you plan to use this Google Web Font on your site.</li>
    </ol>
    </div><div style="clear:both;"></div>
    <br /><div></div>
    <div>
    <h3><span style="color:red; text-decoration:underline;font-weight:bold;font-size:larger;">Step 2.</span> &nbsp;<input id="generate_css" class= "js_button" type="button" value="Click Here to Generate Font CSS Definition" onclick="weaverii_generate_font_css()" /> &nbsp;
    <small>&larr; Click this button to generate a CSS definition you can paste into the different font areas below.</small></h3>
    <textarea name="<?php weaverii_sapi_main_name('font_generate_font_code');?>" id="font_generate_font_code" readonly rows=1 style="width: 800px;background:#eee;"><?php
	      weaverii_esc_textarea(weaverii_getopt('font_generate_font_code')); ?></textarea><br/>
    <strong style="color:#a04;">Paste above CSS code into style boxes in the Weaver II Font Options section below.</strong> <small>No need to Copy, just click the Paste CSS button. Getting just "{}"? <strong>Re-read</strong> the Step 1 directions!</small></div>
    <br />
    </fieldset>
	<?php weaverii_sapi_submit('','',false); ?>
	The above Font Style Generator settings will be saved when you Save Settings, but they generally are used
	on a one-shot basis.
	<hr />

    <fieldset class="options">
	<span style="font-weight:bold; color:blue;">Weaver II Font Options</span><br />
	<h3><span style="color:red; text-decoration:underline;font-weight:bold;font-size:larger;">Step 3.</span> Define font definition load path for Google Fonts you use</h3>
	<p><strong>If</strong> you are using any Google Fonts, you <strong><em>MUST</em></strong> add the definitions you pasted into #3 and #4 above
	to the "Available Google Fonts" box below so that your site will be able to load the Google Fonts. If you are just using
	standard web font families, then you can skip this step.</p>


	<p><input id="copy_google" class= "js_button" type="button" value="Click Here to Paste current Google #3 and #4 to Available Google fonts list" onclick="weaverii_copy_google_3_4()" />&nbsp;&nbsp;<strong style="color:red;">Important!</strong> You still must click the "Save Settings" button to save the Google Font definitions in the Available Google Fonts setting!</p>

	<table class="optiontable">
	    <tr>
	    <th scope="row" align="right"><span style="color:green;">Available Google Fonts:</span><br />
	    <small style="font-weight:normal;">List of Google fonts that will be available for use on your site.
	    You can copy/paste the "font-family: ..." CSS code into any of the sections below if you need to later.</small></th>
	    <td ><textarea name="<?php weaverii_sapi_main_name('fonts_google_font_list'); ?>" id='fonts_google_font_list' rows=4 style="width: 620px"><?php
	      weaverii_esc_textarea(weaverii_getopt('fonts_google_font_list')); ?></textarea></td>
	    </tr>
	</table>

	<h3><span style="color:red; text-decoration:underline;font-weight:bold;font-size:larger;">Step 4.</span> Paste Font CSS Defintions into Boxes of items you want to specify</h3>
	<p>You can now use the "Paste CSS" buttons next to specific text items to use the currently defined font in the "Step 2"
	Font CSS Definition. You need to change that definition for each different font you use. The same applies to "Step 3" if
	your are using Google Fonts.</p>
	<table class="optiontable">

<?php
	foreach ($weaverii_fonts_defs as $option => $val) {
	    weaverii_fonts_row($val);
	}
?>
    <tr><td>&nbsp;</td></tr>
    <tr>
	<th scope="row" align="right" style="color:green;">Fonts Box Lines:&nbsp;</th>
	<td>
	    <input type="text" style="width:30px;height:22px;" class="regular-text" name="<?php weaverii_sapi_main_name('fonts_edit_lines'); ?>"
                id="fonts_edit_lines" value="<?php weaverii_esc_textarea(weaverii_getopt('fonts_edit_lines')); ?>" />
	    <small>Number of lines to display in each edit box on this page.</small>
	</td>
    </tr>
	</table>
	</fieldset>
	<br />
    <hr />
    </div>
<?php
    if (weaverii_hide_advanced())
	echo '</div>';
}

function weaverii_fonts_row($row) {
    // echo a Fonts row
    if ($row['id'][0] == '_') {		// row title  - needs name linke, not sapi id
?>
    <tr><th scope="row" align="left" style="color:blue"><br /><a name="<?php echo $row['id'];?>"></a><?php weaverii_esc_textarea($row['label']); ?>&nbsp;&nbsp;</th>
	<td><br /><?php weaverii_esc_textarea($row['info']); ?>&nbsp;&nbsp;&nbsp;<a href="#total_fonts_top"><strong>Top</strong></a>
	<td><span style="float:none;"><?php weaverii_sapi_submit('','',false); ?></span>
	</td>
    </tr>
<?php
    } else {
	$rows = weaverii_getopt('fonts_edit_lines');
	if (!$rows) $rows = 2;
?>
    <tr><th scope="row" align="right" style="width:220px"><?php weaverii_esc_textarea($row['label']); ?>:&nbsp;</th>
	<td ><textarea name="<?php weaverii_sapi_main_name($row['id']); ?>" id=<?php echo $row['id']; ?> rows=<?php echo $rows; ?> style="width: 300px"><?php
	      weaverii_esc_textarea(weaverii_getopt($row['id'])); ?></textarea></td><td>
	      <input id="paste_css" class= "js_button" type="button" value="&larr;Paste CSS&nbsp;" onclick="weaverii_copy_font_css('<?php echo $row['id']; ?>')" />
	      <small>Click button to paste current Font CSS Definition defined above to this element.</small>
	      <br><small><?php weaverii_esc_textarea($row['info']); ?></small></td>
    </tr>
<?php
    }
}

function weaverii_select($id, $list) {
?>
    <select name="<?php weaverii_sapi_main_name($id); ?>" id="<?php echo $id; ?>">
<?php foreach ($list as $option) { ?>
        <option<?php if ( weaverii_getopt( $id ) == $option) { echo ' selected="selected"'; }?>><?php weaverii_esc_textarea($option); ?></option>
<?php } ?>
    </select>
<?php
}


//==================== RUN TIME ==========


function weaverii_fonts_wp_head() {
    echo ("<!-- Weaver II Pro Google Fonts -->\n");
    $google = weaverii_getopt('fonts_google_font_list');
    if ($google)
	echo $google;
}


function weaverii_fonts_output_style($sout) {

    weaverii_f_write($sout,"/* Weaver II Pro Total Fonts */\n");

    global $weaverii_fonts_defs;
    foreach ($weaverii_fonts_defs as $option => $val) {
	$fonts = weaverii_getopt($val['id']);
	if ($fonts) {
	    $rule = $val['tag'] != '+++' ? $val['tag'] : '';
	    weaverii_f_write($sout,$rule . $fonts . "\n");
	}
    }
}

// ==============================================   BACKGROUND IMAGES ===========================================
function weaverii_adv_bgimages() {
    weaverii_hide_advanced('begin');
?>
	<div class="wvr-option-header">
	Background Images
	<?php weaverii_help_link('help.html#BackgroundImages','Help on Background Images');?></div><br />
	<br />

        <table class="optiontable">
<?php

	weaverii_bgimg_widerow('Full Screen Site BG Image','_wii_bg_fullsite_url','Full screen centered auto-sized BG image.  (&#9679;Pro)','250px');

	weaverii_bgimg_widerow('Wrapper BG Image','_wii_bg_wrapper_url','Background image for outer wrapper (#wrapper) (&#9679;Pro)');

	weaverii_repeat_row('_wii_bg_wrapper_rpt');

	weaverii_bgimg_widerow('Header BG Image','_wii_bg_header_url','Background image for header (#branding) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_header_rpt');

	weaverii_bgimg_widerow('Main BG Image','_wii_bg_main_url','Background image for main area - wraps everything after header (#main) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_main_rpt');

	weaverii_bgimg_widerow('Container BG Image','_wii_bg_container_url','Background image for Container - (#container_wrap) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_container_rpt');

	weaverii_bgimg_widerow('Content BG Image','_wii_bg_content_url','Background image for Content - wraps page/post area (#content) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_content_rpt');

	weaverii_bgimg_widerow('Page content BG Image','_wii_bg_page_url','Background image for Page content area (#container .page) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_page_rpt');

	weaverii_bgimg_widerow('Post BG Image','_wii_bg_post_url','Background image for Post content area (#container .post) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_post_rpt');

	weaverii_bgimg_widerow('Left Sidebar Areas BG Image','_wii_bg_widgets_left_url','Background image for widget areas on left (#sidber_wrap_left) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_widgets_left_rpt');

	weaverii_bgimg_widerow('Right Sidebar Areas BG Image','_wii_bg_widgets_right_url','Background image for widget areas on right (#sidber_wrap_right) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_widgets_right_rpt');

	weaverii_bgimg_widerow('Footer BG Image','_wii_bg_footer_url','Background image for Footer area (#colophon) (&#9679;Pro)');
	weaverii_repeat_row('_wii_bg_footer_rpt');

    echo "</table>\n";

    weaverii_hide_advanced('end');
}

// ========================================== manual rows ==========================================
function weaverii_bgimg_widerow($th,$rid,$desc,$width='') {
    $style = '';
    $style_desc = 'style="padding-left: 10px"';
    if (!weaverii_init_base()) {
	$style = ' style="color:#999;"';
	$style_desc = $style;
    } else if ($width != '') {
        $style = ' style="width:' . $width . ';"';
    }

?>
    <tr>
	<th scope="row" align="right"<?php echo $style . '>' . $th; ?>:&nbsp;</th>
	<td>
<?php	if (weaverii_init_base()) { ?>
	    <input name="<?php weaverii_sapi_main_name($rid); ?>" type="text" style="width:240px;height:22px;" class="regular-text"
		id="<?php echo $rid; ?>" value="<?php echo (esc_textarea(weaverii_getopt($rid))); ?>" />
<?php 		weaverii_media_lib_button($rid); ?>
<?php	} else { ?>
	    <span style="color:#999;">Pro Version&nbsp;&nbsp;&nbsp;</span>
	    <input name="<?php weaverii_sapi_main_name($rid); ?>" type="hidden" style="width:240px;height:22px;" class="regular-text"
	      id="<?php echo $rid; ?>" value="<?php echo (esc_textarea(weaverii_getopt($rid))); ?>" />

	</td>
<?php	} ?>
	<td <?php echo $style_desc;?>><small><?php echo $desc; ?></small></td>
    </tr>
<?php

}

function weaverii_repeat_row($rid) {
    if (!weaverii_init_base())
        echo '<div style="display:none;>';
?>
    <tr>
	<th scope="row" align="right">&nbsp;</th>
	<td colspan="2" style="font-size:80%;">
	    <input type="radio" name="<?php weaverii_sapi_main_name($rid); ?>"
                value="repeat" <?php echo(weaverii_getopt($rid) == 'repeat' ? 'checked' : ''); ?> /> repeat &nbsp;
	    <input type="radio" name="<?php weaverii_sapi_main_name($rid); ?>"
                value="repeat-x" <?php echo(weaverii_getopt($rid) == 'repeat-x' ? 'checked' : ''); ?> /> repeat-x &nbsp;
	    <input type="radio" name="<?php weaverii_sapi_main_name($rid); ?>"
                value="repeat-y" <?php echo(weaverii_getopt($rid) == 'repeat-y' ? 'checked' : ''); ?> /> repeat-y &nbsp;
	    <input type="radio" name="<?php weaverii_sapi_main_name($rid); ?>"
                value="no-repeat" <?php echo(weaverii_getopt($rid) == 'no-repeat' ? 'checked' : ''); ?> /> no-repeat
	</td>
    </tr>
<?php
    if (!weaverii_init_base())
        echo '</div>';
}

// ==============================================   SAVE/RESTORE PRO ===========================================
function weaverii_pro_saverestore(){
    /* admin tab for saving and restoring theme */
    $weaverii_theme_dir = weaverii_f_uploads_base_dir() .'weaverii-theme/';
?>
    <div class="wvr-option-subheader">Save/Restore Settings using Files on your site host (&#9679;Pro)</div>
    <h4>You can save all your current settings in a backup file:</h4>
    <ol style="font-size: 90%">
     <li>Save <em>all</em> your current settings in a backup file on your site's file system (in <?php echo($weaverii_theme_dir);?>). Automatically names the backup file to include current date and time.
     Survives Weaver II Theme updates. -or-</li>
    <li>Save only <em>theme related</em> settings to a file you name on your Site's file system (in <?php echo($weaverii_theme_dir);?>.</li>
    </ol>
<?php if (weaverii_allow_multisite()) : ?>
    <h4>You can restore a saved theme or backup file by:</h4>
    <ol style="font-size: 90%">
    <li>Restoring a theme/backup that you saved in a file on your site (to current settings). -or-</li>
    <li>Uploading a theme/backup from a file saved on your own computer (to current settings). </li>
    </ol>
<?php endif; ?>
<?php if (!weaverii_allow_multisite()) : ?>
    <h4>You will be unable to restore your saved file directly</h4>
    <p>Since this is a WordPress Multi-site installation, you are restricted from uploading
    a Weaver II theme/backup from a saved file. However, the save file capability gives you the ability
    to save your work so you can transfer it to a WordPress site where you have full admin
    capabilities (non-Multi-site installation, for example), or to share with others. Please
    note that you <em>can</em> save your settings in the WordPress Database which will allow you
    to explore other predefined themes without losing your work.
    </p>
<?php endif; ?>
  <br />
    <div class="wvr-option-subheader">Save All Current Settings in Backup File (&#9679;Pro)</div><br />
     <strong>Backup</strong> <u>all</u> current options in a <strong>file</strong> on your
     WordPress Site's <em><?php echo($weaverii_theme_dir);?></em> directory named 'weaverii_backup_yyyy-mm-dd-hhmm.w2b'
     where the last part is a GMT based date and time stamp.
<?php if (weaverii_allow_multisite()) : ?>
    You will be able to restore this theme later using the <strong>Restore Saved Theme/Backup</strong> section.
<?php endif; ?>
    Please be sure you've saved any changes you might have made.<br />
     <form enctype="multipart/form-data" name='backup-settings' method='post'>
	<span class='submit'><input name='backup_settings' type='submit' value='Backup All Current Settings'/></span>
    <?php weaverii_nonce_field('backup_settings'); ?>
    </form><br />

    <div class="wvr-option-subheader">Save Current Theme Related Settings to File (&#9679;Pro)</div><br />
     <strong>Save</strong> current <em>theme related</em> settings, either by downloading
    to <strong>your computer</strong> or saving a <strong>file</strong> on your WordPress Site's <em><?php echo($weaverii_theme_dir);?></em> directory.
<?php if (weaverii_allow_multisite()) : ?>
    You will be able to restore this theme later using the <strong>Restore Saved Theme/Backup</strong> section.
<?php endif; ?>
    <em>Theme related</em> settings include most standard Weaver settings <em>except</em>: Site Copyright, SEO settings,
    Weaver Pro HTML Insert areas, Background Images, FavIcons, and Weaver II Pro shortcode settings.<br /><br />

  <strong>Save as file on this website's server</strong>
 <p>Please provide a name for your file, then click the "Save File" button. <b>Warning:</b> Duplicate names will
    automatically overwrite existing file without notification.</p>
 <form enctype="multipart/form-data" name='savetheme' method='post'><table cellspacing='10' cellpadding='5'>
    <tr>
    <td>Name for saved theme: <input type="text" name="savethemename" size="30" />&nbsp;<small>(Please use a meaningful
    name - do not provide file extension. Name might be altered to standard form.)</small></td></tr>
	<tr>
	<td><span class='submit'><input name='filesavetheme' type='submit' value='Save Theme in File'/></span>&nbsp;&nbsp;
	<strong>Save Theme in File</strong> - <small>Theme will be saved in <em><?php echo($weaverii_theme_dir);?></em>
	directory on your site server.</small></td>
        </tr>
    </table>
    <?php weaverii_nonce_field('filesavetheme'); ?>
 </form><br />

<?php if (weaverii_allow_multisite()) : ?>


    <div class="wvr-option-subheader">Restore Saved Theme/Backup from file (&#9679;Pro)</div><br />
    You can restore a previously saved theme (.w2t) or backup (.w2b) file directly from your WordPress
    Site's <em><?php echo($weaverii_theme_dir);?></em> directory, or from a file saved on your computer.
    Note: after you restore a saved theme, it will be loaded into the current settings. A <em>theme</em> restore will
    replace only settings that are not site-specific. A <em>backup</em> file will replace all current settings.
    If you've uploaded the theme from your computer, you might then want to also save a local copy on your
    website server.<br /><br />

    <form enctype="multipart/form-data" name='localrestoretheme' method='post'><table cellspacing='10' cellpadding='5'>
    <table>
    <tr><td><strong>Restore from file saved on this website's server</strong></td></tr>
    <tr>
        <td>Select theme/backup file: <?php  weaverii_subtheme_list('wii_restorename'); ?>&nbsp;Note: <strong>.w2t</strong> are Theme definitions. <strong>.w2b</strong> are full backups. (Restores to current settings.)</td></tr>
	<tr>
	<td><span class='submit'><input name='restoretheme' type='submit' value='Restore Theme/Backup'/></span>&nbsp;&nbsp;
	<strong>Restore</strong> a theme/backup  you've previously saved on your site's <em><?php echo($weaverii_theme_dir);?></em> directory. Will replace current settings.</td>
    </tr>
        <tr><td>&nbsp;</td></tr>
    </table>
    <?php weaverii_nonce_field('restoretheme') ; ?>
    </form>

<div class="wvr-option-subheader">Save Settings for Alternate Mobile Theme (&#9679;Pro)<?php weaverii_help_link('help.html#AltMobileTheme','Help on Alternate Mobile Theme');?></div>
<p>This will save your current settings to a special Mobile Settings database entry. You can use this to create a totally
separate style used when the site is viewed from a Mobile device. You <strong>must</strong> enable the
<em>Use Alternate Mobile Theme</em> option on the Advanced:Mobile tab for these settings to be used. <strong>IMPORTANT!</strong> Be sure to save backup copies of both your normal and mobile theme settings using one of the above
save to file options. You will need them to be able to tweak the alternate mobile theme settings.</p>
  <form name="wii_save_mobile_form" method="post"
	<span class="submit"><input type="submit" name="save_mobiletheme" value="Save Settings for Mobile View"/></span>
	<strong>Save all current settings in Alternate Mobile Theme Settings.</strong>
<?php	 weaverii_nonce_field('save_mobiletheme'); ?>
    </form>


    <form enctype="multipart/form-data" name='themenames' method='post'>
    <div class="wvr-option-subheader">Theme Name and Description (&#9679;Pro)</div>
        <p>You can change the name an description of your current settings if you would like to create a new theme
	theme file for sharing with others, or for you own identification.
	</p>
	<input name="wii_themename" id="wii_themename" value="<?php echo weaverii_getopt('wii_themename'); ?>" />
	<br />
	<textarea name="wii_theme_description" id="_wii_favicon_url" rows=2 style="width: 350px"><?php echo(esc_textarea(weaverii_getopt('wii_theme_description'))); ?></textarea>
	<br />
        <span class='submit'><input name='renametheme' type='submit' value='Save Theme Name and Description'/></span>
        <?php weaverii_nonce_field('renametheme'); ?>
    </form>
    <br />

    <form enctype="multipart/form-data" name='maintaintheme' method='post'>
    <div class="wvr-option-subheader" >Subtheme and Backup File Maintenance (&#9679;Pro)</div><br />
        <?php weaverii_subtheme_list('selectName'); ?>

        <span class='submit'><input name='deletetheme' type='submit' value='Delete Subtheme/Backup File'/></span>
          <strong>Warning!</strong> This action can't be undone, so be sure you mean to delete a file!
	  <?php weaverii_nonce_field('deletetheme'); ?>
    </form>
<?php endif;
    ?>
    <br />
<?php
}  /* end weaverii_saverestore_admin */

/*
    ==================== SAVE / RESTORE THEMES AND BACKUPS ==========================
*/
function weaverii_savemytheme() {
    /* saves all current settings into My Saved Theme file, changes current theme name. */
    /* Weaver II will save themes and backups in files.
    = .wvr files are theme files, and are pretty much compatible back to 2010 Weaver II 1.1.
	Older versions of .wvr files will include "per-site" settings that are now being
	ignored, but are harmless. .wvr files saved by new versions of Weaver II will not
	include per-site settings.
    = .wvb files are backup versions which save everything that is possible to set. They
	are used to save "My Saved Theme" as well as backup files
 */
    weaverii_setopt('wii_subtheme', 'My Saved Theme');		/* make it my saved theme */
    return weaverii_write_backup('weaverii_my_saved_theme', false /* not theme */);
}

function weaverii_savebackup() {
    /* generate file name with current date and time to save backup file */
    $name = 'weaverii_backup_' . date('Y-m-d-Hi');

    if (weaverii_write_backup($name, false))
	return $name;
    else
	return false;
}

function weaverii_write_current_theme($savefile) {
     return weaverii_write_backup($savefile, true);		// write a theme save file
}

function weaverii_write_backup($savefile, $is_theme = true) {
    /*	write the current settings to a file, return true or false
	$savefile is a base-name - no directory, no extension
    */

    global $weaverii_pro_opts;
    global $weaverii_opts_cache;


    $save_dir = weaverii_f_uploads_base_dir() . 'weaverii-subthemes';
    $save_url = weaverii_f_uploads_base_url() . 'weaverii-subthemes';

    if ($is_theme)
	$ext = '.w2t';
    else
	$ext = '.w2b';

    $usename = strtolower(sanitize_file_name($savefile));
    $usename = str_replace($ext,'',$usename);
    if (strlen($usename) < 1) return '';
    $usename = $usename . $ext;

    $wii_theme_dir_exists = weaverii_f_mkdir($save_dir);
    $wii_theme_dir_writable = $wii_theme_dir_exists;

   if (!weaverii_f_is_writable($save_dir)) {
        $wii_theme_dir_writable = false;
    }

    $filename = trailingslashit($save_dir) . $usename;

    if (!$wii_theme_dir_writable || !$wii_theme_dir_exists || !($handle = weaverii_f_open($filename, 'w')) ) {
	weaverii_f_file_access_fail('Unable to create file. Probably a file system permission problem. File: ' . $filename);
	return '';
    }

    $tosave = weaverii_get_save_settings($is_theme);

    /* file open, ready to write - so let's write something - either a backup or a theme */

    weaverii_f_write($handle, $tosave);	// write all Weaver II settings to user save file
    weaverii_f_close($handle);

    return trailingslashit($save_url) . $usename;
}

function weaverii_upload_backup($file_basename) {

    return weaverii_upload_theme( weaverii_f_uploads_base_dir() . 'weaverii-subthemes/' . $file_basename . '.w2b' );
}


function weaverii_upload_theme($filename) {

    if (!weaverii_f_exists($filename)) return weaverii_f_fail("Can't open $filename");     	/* can't open */

    $contents = weaverii_f_get_contents($filename);

    if (!$contents) return weaverii_f_fail("Can't open $filename");

    return weaverii_set_current_to_serialized_values($contents);
 }

function weaverii_subtheme_list($lbl) {
    // output the form to select a file list from weaverii-subthemes directory
?>
    <select name="<?php echo($lbl);?>" id="<?php echo($lbl);?>">
	<option value="None">-- Select File --</option>
<?php
	    // echo the theme file list
	    $theme_dir = weaverii_f_uploads_base_dir() . 'weaverii-subthemes/';
	    if($media_dir = opendir($theme_dir)){
		while ($m_file = readdir($media_dir)) {
		    $len = strlen($m_file);
		    $ext = $len > 4 ? substr($m_file,$len-4,4) : '';
		    if($ext == '.w2t' || $ext == '.w2b' ) {
		        echo '<option value="'.$m_file.'">'.$m_file.'</option>';
		    }
		}
	    }
?>
    </select>
<?php
}
?>
