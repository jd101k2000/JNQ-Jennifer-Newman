<?php
//remove the update reminder
function ot_remove_nag() {
echo '
<style type="text/css">
	#update-nag { display: none; }
</style>
';
}

//check users levek
function ot_check_userlevel() {
	global $userdata;
	if ($userdata->user_level < 10) :
		add_action('admin_head', 'ot_remove_nag');
	endif;
}
add_action('admin_init', 'ot_check_userlevel');
?>