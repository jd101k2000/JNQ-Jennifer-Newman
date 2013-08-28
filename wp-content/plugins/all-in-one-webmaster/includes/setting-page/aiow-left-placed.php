<?php
/**
 * @author Crunchify.com
 * Plugin: All in One Webmaster
 */
?>

<div class="postbox">
    <h3>Placed Analytics<?=$new_icon?></h3>

    <div>
        <table class="form-table">

            <tr valign="top">
                <th scope="row" style="width:32%;"><label><b>Placed</b> Application Key</label></th>
                <td>
                    <input id="styled" name="all_in_one_placed_analytics" type="text" size="55"
                           value="<?php echo get_option('all_in_one_placed_analytics'); ?>"/>
                    &nbsp;<?=$aiow_placed_ana?>
                    <br/>Application Key <font color="red"><code>a6e1321039ab</code></font><br/>
                </td>
            </tr>

            <tr valign="top" class="alternate">
                <th scope="row"><label>Custom Options<?=$new_icon?></label>
                    <br>
                    <br>
                    <a href="http://Crunchify.com/placed-analytics-added-to-all-in-one-webmaster-wordpress-plugin/"
                       target="_blank"><code>Setup Help</code></a>
                </th>

                <td>
                    <input name="all_in_one_placed_mobile_only"
                           type="checkbox"<?php if (get_option('all_in_one_placed_mobile_only') != '-1') echo 'checked="checked"'; ?>
                           value="1"/> &nbsp;&nbsp;<code>Check</code> if you have selected ONLY Non-Mobile version.
                    (Default: unchecked)<br>
                    unchecked = Web + Mobile <br>
                    checked = Non-Mobile Version
                    <br>
                    <br>
                    <input name="all_in_one_placed_tag_type"
                           type="checkbox"<?php if (get_option('all_in_one_placed_tag_type') != '-1') echo 'checked="checked"'; ?>
                           value="1"/> &nbsp;&nbsp;<code>Check</code> if you have selected Site Domain. (Default:
                    unchecked)<br>
                    unchecked = tag type (placed.com) <br>
                    checked = tag type (site domain)
                </td>
            </tr>

        </table>
    </div>
</div>
<div class="submit">
	<input name="my_aiowz_update_setting" type="hidden" value="<?php echo wp_create_nonce('aiowz-update-setting'); ?>" />
    <input type="submit" name="info_update1" class="button-primary" value="<?php _e('Update options'); ?> &raquo;"/>

</div>
