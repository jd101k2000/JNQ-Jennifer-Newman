<?php
/**
 * @author Crunchify.com
 * Plugin: All in One Webmaster
 */
?>
<div class="postbox">
    <h3>Webmaster Options</h3>

    <div>
        <table class="form-table">

            <tr valign="top" class="alternate">
                <th scope="row" style="width:32%;"><label>1) <b>Google</b> WebMaster Central</label></th>
                <td>
                    <input id="styled" name="all_in_one_google_webmaster" type="text" size="55"
                           value="<?php echo get_option('all_in_one_google_webmaster'); ?>"/>
                    &nbsp;<?=$aiow_google_web?>

                    <br/>(meta name="google-site-verification" content="<font color="red"><code>Volxdfasfasd3i3e_wATasfdsSDb0uFqvNVhLk7ZVY</code></font>")<br/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:32%;"><label>2) <b>Bing</b> WebMaster Center</label></th>
                <td>
                    <input id="styled" name="all_in_one_bing_webmaster" type="text" size="55"
                           value="<?php echo get_option('all_in_one_bing_webmaster'); ?>"/>
                    &nbsp;<?=$aiow_bing_web?>
                    <br/>(meta name="msvalidate.01" content="<font
                        color="red"><code>ASBKDW71D43Z67AB2D39636C89B88A</code></font>")<br/>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:32%;"><label>3) <b>Alexa</b> Rank</label></th>
                <td>
                    <input id="styled" name="all_in_one_alexa_webmaster" type="text" size="55"
                           value="<?php echo get_option('all_in_one_alexa_webmaster'); ?>"/>
                    &nbsp;<?=$aiow_alexa_web?>
                    <br/>(meta name="alexaVerifyID" content="<font color="red"><code>OKJ3RsasdfKHGST1uqa8zcBfrjtY</code></font>")<br/>
                </td>
            </tr>
            <tr valign="top" class="alternate">
                <th scope="row" style="width:32%;"><label>4) <b>BlogCatalog</b></label><?=$new_icon?></th>
                <td>
                    <input id="styled" name="all_in_one_bcatalog_webmaster" type="text" size="55"
                           value="<?php echo get_option('all_in_one_bcatalog_webmaster'); ?>"/>
                    &nbsp;<?=$aiow_blogcatalog_web?>
                    <br/>(meta name="blogcatalog" content="<font color="red"><code>7DS9234212</code></font>")<br/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="width:32%;"><label>5) <b>Facebook Insights</b></label><?=$new_icon?></th>
                <td>
                    <input id="styled" name="all_in_one_fbinsights_webmaster" type="text" size="55"
                           value="<?php echo get_option('all_in_one_fbinsights_webmaster'); ?>"/>
                    &nbsp;<?=$aiow_fbinsight_web?>
                    <br/>(meta property="fb:admins" content="<font color="red"><code>573435354</code></font>")<br/>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="submit">
	<input name="my_aiowz_update_setting" type="hidden" value="<?php echo wp_create_nonce('aiowz-update-setting'); ?>" />
    <input type="submit" name="info_update1" class="button-primary" value="<?php _e('Update options'); ?> &raquo;"/>

</div>
