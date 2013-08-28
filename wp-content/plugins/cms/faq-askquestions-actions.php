<?php
global $wpdb;

if($_GET['delete']) {
	$wpdb->query("DELETE FROM ".$wpdb->prefix."ot_faq_questions WHERE id='".$_GET['delete']."'");
}

if($_POST['faq_form_action_changes'] || $_POST['faq_form_action_changes-2']) {
	$action = $_POST['faq_action'];
	if($_POST['faq_form_action_changes-2']) {
		$action = $_POST['faq-action-2'];
	}
	$selected = $_POST['faq_question'];
	
	if($action != 'actions') {
		if($action == 'delete') {
			//delete selected
			foreach($selected as $question) {
				$wpdb->query("DELETE FROM ".$wpdb->prefix."ot_faq_questions WHERE id='".$question."'");
			}
		}
	}
}
?>