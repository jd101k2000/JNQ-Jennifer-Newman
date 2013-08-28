<?php
/**
 * @author Crunchify.com
 * Plugin: All in One Webmaster
 */
?>

<div class="postbox">
    <h3>Analytics Options</h3>

    <div>
        <table class="form-table">

            <tr valign="top" class="alternate">
                <th scope="row" style="width:32%;"><label>1) <b>Google</b> Universal Analytics</label></th>
                <td>
                    <input id="styled" name="all_in_one_google_analytics" type="text" placeholder="Tracking ID" size="20"
                           value="<?php echo get_option('all_in_one_google_analytics'); ?>"/>
                    &nbsp;&nbsp;
                   <input id="styled" name="all_in_one_google_analytics_domain" type="text" placeholder="Domain Name" size="30"
                           value="<?php echo get_option('all_in_one_google_analytics_domain'); ?>"/>
                    &nbsp;<?=$aiow_google_ana?>
                    <br/>(Web Property ID: <font color="red"><code>UA-8123456-1</code></font> & Domain Name: <font color="red"><code>crunchify.com</code></font>)<br/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="width:32%;"><label>2) <b>Quantcast</b> Analytics</label></th>
                <td>
                    <input id="styled" name="all_in_one_quantcast_analytics" type="text" size="55"
                           value="<?php echo get_option('all_in_one_quantcast_analytics'); ?>"/>
                    &nbsp;<?=$aiow_quantcast_ana?>
                    <br/>(qacct value. i.e. <font color="red"><code>p-a8SWEoiOWPo5Q</code></font>)<br/>
                </td>
            </tr>

            <tr valign="top" class="alternate">
                <th scope="row" style="width:32%;"><label>3) <b>Clicky</b> Analytics</label></th>
                <td>
                    <input id="styled" name="all_in_one_clicky_analytics" type="text" size="55"
                           value="<?php echo get_option('all_in_one_clicky_analytics'); ?>"/>
                    &nbsp;<?=$aiow_clicky_ana?>
                    <br/>(Site ID: <font color="red"><code>324543</code></font>)<br/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="width:32%;"><label>4) <b>Compete</b> Analytics</label></th>
                <td>
                    <input id="styled" name="all_in_one_compete_analytics" type="text" size="55"
                           value="<?php echo get_option('all_in_one_compete_analytics'); ?>"/>
                    &nbsp;<?=$aiow_compete_ana?>
                    <br/>(src="//c.compete.com/bootstrap/s/<font
                        color="red"><code>4b6705ef8ded7e9cb0067318dde11c3e/compete-com</code></font>/bootstrap.js")<br/>
                </td>
            </tr>
            
            <tr valign="top" class="alternate">
                <th scope="row" style="width:32%;"><label>5) <b>SiteMeter</b> Analytics/Tracking</label></th>
                <td>
                    <input id="styled" name="all_in_one_sitemeter_analytics" type="text" size="55"
                           value="<?php echo get_option('all_in_one_sitemeter_analytics'); ?>"/>
                    &nbsp;<?=$aiow_sitemeter_ana?>
                    <br/>(src="<font
                        color="red"><code>http://s44.sitemeter.com/js/counter.js?site=s44AShah</code></font>")<br/>
                </td>
            </tr>

        </table>
    </div>
</div>

<div class="submit">
	<input name="my_aiowz_update_setting" type="hidden" value="<?php echo wp_create_nonce('aiowz-update-setting'); ?>" />
    <input type="submit" name="info_update1" class="button-primary" value="<?php _e('Update options'); ?> &raquo;"/>

</div>
