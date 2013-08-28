<?php
/**
 * @author Crunchify.com
 * Plugin: All in One Webmaster
 */
?>

<form method="post" mame="info_update2" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update2" id="info_update2" value="true"/>

    <div class="postbox">
        <h3>Automatic sitemap submission to Google and Bing</h3>

        <div>
            <table class="form-table">

                <tr valign="top" class="alternate">
                    <th scope="row" style="width:29%;"><label>Please provide existing Sitemap URL</label></th>
                    <td>
                        <input name="sitemap_URL" type="text" size="75"
                               value="<?php echo get_option('sitemap_URL'); ?>"/>
                        <br/>
                        (example: http://example.com/sitemap.xml)
                    </td>
                </tr>

            </table>

        </div>
    </div>

    <div class="submit">
    <input name="my_aiowz_update_setting" type="hidden" value="<?php echo wp_create_nonce('aiowz-update-setting'); ?>" />
    
        <input type="submit" name="info_update2" class="button-primary"
               value="<?php _e('Submit to Google and Bing'); ?> &raquo;"/>

    </div>


    <?=$show_sitemap?>
</form>

</div>