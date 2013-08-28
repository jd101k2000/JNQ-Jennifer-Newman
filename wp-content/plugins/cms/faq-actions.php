<?php
global $wpdb;

//show options
if($_POST['faq-options-changes']) {
	if($_POST['faq-javascript'] != 'on') {
		update_option('faq-javascript-off','true');
	} else {
		delete_option('faq-javascript-off');
	}
	if($_POST['faq-askquestions'] != 'on') {
		update_option('faq-askquestions-off','true');
	} else {
		delete_option('faq-askquestions-off');
	}
	$faq_email = mysql_real_escape_string($_POST['faq-email']);
	update_option('faq-email',$faq_email);
}

//change faq order
if($_GET['direction'] && $_GET['order-id']) {
	$this_question = faq_get_question($_GET['order-id']);
	
	
	//determine change id
	if($_GET['direction'] == 'down') { 
		$change_id_sql = "SELECT id FROM ".$wpdb->prefix."ot_faq_questions WHERE id<".$this_question->id." AND category='".$this_question->category."' ORDER BY id DESC LIMIT 0,1";
	} else {
		$change_id_sql = "SELECT id FROM ".$wpdb->prefix."ot_faq_questions WHERE id>".$this_question->id." AND category='".$this_question->category."' ORDER BY id ASC LIMIT 0,1";
	}
	$change_id = $wpdb->get_results($change_id_sql);
	$change_id = $change_id[0]->id;
	
	//change the questions
	$temp = $wpdb->query("UPDATE ".$wpdb->prefix."ot_faq_questions SET id='".(0-$change_id)."' WHERE id='".$change_id."'");
	$new = $wpdb->query("UPDATE ".$wpdb->prefix."ot_faq_questions SET id='".$change_id."' WHERE id='".$this_question->id."'");
	$final = $wpdb->query("UPDATE ".$wpdb->prefix."ot_faq_questions SET id='".$this_question->id."' WHERE id='".(0-$change_id)."'");
}

//process the changes in questions
if($_POST['faq_add_question'] && $_POST['faq_question'] && $_POST['faq_answer']) { //add new question
	$category = $_POST['faq_category'];
	$question = $_POST['faq_question'];
	$answer = $_POST['faq_answer'];
	if(empty($_POST['faq_question_id'])) {
		$wpdb->query("INSERT INTO ".$wpdb->prefix."ot_faq_questions(category,question,answer) VALUES('".$category."','".$question."','".$answer."');");
	} else {
		//update huidige
		$question_id = $_POST['faq_question_id'];
		$wpdb->query("UPDATE ".$wpdb->prefix."ot_faq_questions SET category='".$category."', question='".$question."', answer='".$answer."' WHERE id='".$question_id."'");
	}
}

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
		} else {
			//move to category
			$move_to = str_replace('cat-','',$action);
			foreach($selected as $question) {
				$wpdb->query("UPDATE ".$wpdb->prefix."ot_faq_questions SET category='".$move_to."' WHERE id='".$question."'");
			}
		}
	}
}

//process the changes in categories
if($_POST['faq_add_category'] && $_POST['faq_category_name']) { //add new category
	$category = $_POST['faq_category_name'];
	
	if(empty($_POST['faq_category_id'])) {
		$wpdb->query("INSERT INTO ".$wpdb->prefix."ot_faq_categories(category) VALUES('".$category."');");
	} else {
		//update category
		$category_id = $_POST['faq_category_id'];
		$wpdb->query("UPDATE ".$wpdb->prefix."ot_faq_categories SET category='".$category."' WHERE id='".$category_id."'");
	}
}

if($_GET['delete_cat']) {
	$wpdb->query("DELETE FROM ".$wpdb->prefix."ot_faq_categories WHERE id='".$_GET['delete_cat']."'");
}

if($_POST['faq_form_action_changes_cat'] || $_POST['faq_form_action_changes_cat-2']) {
	$action = $_POST['faq_action'];
	if($_POST['faq_form_action_changes_cat-2']) {
		$action = $_POST['faq-action-2'];
	}
	$selected = $_POST['faq_category'];
	
	if($action != 'actions') {
		if($action == 'delete') {
			//delete selected
			foreach($selected as $category) {
				$wpdb->query("DELETE FROM ".$wpdb->prefix."ot_faq_categories WHERE id='".$category."'");
			}
		}
	}
}
?>