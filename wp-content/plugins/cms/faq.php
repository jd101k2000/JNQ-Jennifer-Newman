<?php
//FRONT END
function add_faq_front($content) {
	while(strpos($content,'[faq id')) {
		$new_content = strip_tags($content);
		
		$faq_string_start = strpos($new_content,'[faq id="');
		$faq_string_start = substr($new_content,$faq_string_start);
		$faq_string_end = strpos($faq_string_start,'"]')+2;
		$faq_string = substr($faq_string_start,0,$faq_string_end);
		
		$faq_id = str_replace('[faq id="','',$faq_string);
		$faq_id = str_replace('"]','',$faq_id);
		
		
		//show all the questions
		$category = faq_get_category($faq_id);
		$new_content = '<h4 class="faq-header">'.$category->category.'</h4>';
		
		$questions = faq_get_questions($faq_id);
		
		$new_content .= faq_show($questions);
		
		$content = str_replace('[faq id="'.$faq_id.'"]',$new_content,$content);
	}
	
	if(strpos($content,'[faq-ask-questions]')) {
		global $wpdb;
		if(!get_option('faq-askquestions-off')) {
			if($_POST['new-question'] && $_POST['question']) {
				$question = mysql_real_escape_string($_POST['question']);
				$category = mysql_real_escape_string($_POST['category']);
				$new_question = $wpdb->query("INSERT INTO ".$wpdb->prefix."ot_faq_questions(question,category) VALUES('".$question."','".$category."');");
				$message = __('Your question was submitted.','cms');
				
				$faq_email = get_option('faq-email');
				if($faq_email) { //send e-mail that a question is asked
				
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					
					$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
					$headers .= 'From: FAQ <faq@'.$domain.'>' . "\r\n";
					
					$cat = faq_get_category($category);
					$subject = $cat->category.' '.__('Question','cms');
					$email = '
					<html>
					<body>
						<h2>&quot;'.$cat->category.'&quot; '.__('Question','cms').'</h2>
						<p>'.__('You are receiving this e-mail because someone asked a question via your website','cms').' <a href="'.get_option('siteurl').'">'.get_option('siteurl').'</a></p>
						<p><strong>'.__('Category').'</strong>: '.$cat->category.'</p>
						<p><strong>'.__('Question').'</strong>: '.$question.'</p>
					</body>
					</html>
					';
					
					wp_mail($faq_email,$subject,$email,$headers);
				}
				
			}
			$new_content = '<div class="ask-question">';
			$new_content .= '<form method="post">';
				$new_content .= '<h3>'.__('Ask a question','cms').'</h3>';
				
				if($message) {
					$new_content .= '<p class="faq-message">'.$message.'</p>';
				} else {			
					$new_content .= '<p class="faq-ask-label"><label for="category">'.__('What is your question about?','cms').'</label></p>';
					$new_content .= '<p><select name="category">';
					$categories = faq_get_categories();
					foreach($categories as $category) {
						$new_content .= '<option value="'.$category->id.'">'.$category->category.'</option>';
					}
					$new_content .= '</select></p>';
					
					$new_content .= '<p class="faq-ask-label"><label for="question">'.__('What is your question?','cms').'</label></p>';
					$new_content .= '<p><textarea name="question" id="question"></textarea></p>';
					
					$new_content .= '<input type="submit" name="new-question" value="'.__('Ask','cms').'" />';
				}
			$new_content .= '<form>';
			$new_content .= '</div>';
			
			$content = str_replace('[faq-ask-questions]',$new_content,$content);
		}
	}
	
	if(strpos($content,'[faq-search]')) {
		global $wpdb;
		
		$string = mysql_real_escape_string($_POST['faq-search']);		
		
		$new_content = '<div class="faq-search">';
			$new_content .= '<form method="post">';
				$new_content .= '<h3>'.__('Search FAQ','cms').'</h3>';
				$new_content .= '<p><input type="text" name="faq-search" id="faq-search" value="'.$string.'" /> ';
				
				$new_content .= '<select name="faq-cat">';
					$categories = faq_get_categories();
					$new_content .= '<option value="0">'.__('All','cms').'</value>';
					foreach($categories as $category) {
						$selected = '';
						if($_POST['faq-cat'] == $category->id) $selected = ' selected="selected"';
						$new_content .= '<option'.$selected.' value="'.$category->id.'">'.$category->category.'</option>';
					}
				$new_content .= '</select>';
				
				$new_content .= '<input type="submit" name="faq-search-btn" id="faq-search-btn" value="'.__('Search','cms').'" /></p>';
			$new_content .= '</form>';
		$new_content .= '</div>';
		
		if(!$_POST['faq-search']) {
			$content = str_replace('[faq-search]',$new_content,$content);
		} else {
			$questions = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_questions WHERE question LIKE '%".$string."%' OR answer LIKE '%".$string."%'");
			
			if($_POST['faq-cat']) {
				$questions = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_questions WHERE category='".$_POST['faq-cat']."' AND question LIKE '%".$string."%' OR answer LIKE '%".$string."%'");
			}
			
			if($questions && strlen($string) > 2) {
				$new_content .= faq_show($questions);
			} else {
				$new_content .= __('No results for your search: ','cms').'<strong>'.$string.'</strong>.';
			}
			$new_content .= '';
			
			$content = $new_content;
		}
	}
	
	return $content;
}
add_filter('the_content','add_faq_front');

function faq_show($questions) {
	$new_content = '';
	if(get_option('faq-javascript-off')) {
		$new_content .= '<ul id="faq-questions-list">';
		foreach($questions as $question) {
			$new_content .= '<li class="faq-question"><a href="#question-'.$question->id.'">'.$question->question.'</a></p>';
		}
		$new_content .= '</ul>';
		
		foreach($questions as $question) {
			$new_content .= '<p class="faq-question"><a name="question-'.$question->id.'">'.$question->question.'</a></p>';
			$new_content .= '<p class="faq-answer">'.$question->answer.'</p>';
		}
	} else {
		foreach($questions as $question) {
			$new_content .= '<p class="faq-question"><a onclick="faq_showQuestion('.$question->id.');" style="cursor: pointer;">'.$question->question.'</a></p>';
			$new_content .= '<p class="faq-answer" id="faq-question-'.$question->id.'" style="display: none;">'.$question->answer.'</p>';
		}
	}
	return $new_content;
}


function add_faq_front_js() {
	$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	echo '<script src="'.$pluginPath.'js/faq-frontend.js" type="text/javascript"></script>';
}

add_action('wp_head','add_faq_front_js');
//END FRONT END

//add the admin panel
function add_faq_adminpanel() {
	global $menu, $submenu;
	
    if(get_option('cms_minimum_level_general')) { //for CMS plugin support
		$minLevelGeneral = get_option('cms_minimum_level_general');
	} else {
		$minLevelGeneral = 7;
	}
	
	$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	
	add_menu_page(__('FAQ','cms'), __('FAQ','cms'), $minLevelGeneral , 'faq-questions', faq_questions,$pluginPath.'images/faq-icon.png');
	add_submenu_page('faq-questions', __('Questions','cms'), __('Questions','cms'), $minLevelGeneral , 'faq-questions', faq_questions); 
	
	if(!get_option('faq-askquestions-off')) {
		$questions = faq_get_empty_questions();
		if(count($questions) > 0) {
			$showMenu = '<strong>'.__('Open questions','cms').' ('.count($questions).')</strong>';
		} else {
			$showMenu = __('Open questions','cms');
		}
		add_submenu_page('faq-questions', __('Open questions','cms'), $showMenu, $minLevelGeneral , 'faq-askquestions', faq_askquestions);
		
	} 
	
	add_submenu_page('faq-questions', __('Categories','cms'), __('Categories','cms'), $minLevelGeneral , 'faq-categories', faq_categories);  
	add_submenu_page('faq-questions', __('Options'), __('Options'), 10, 'faq-options', faq_options);  
}
add_action('admin_menu', 'add_faq_adminpanel');


//add to the editor
function init_faq_editor() {

	global $user_level;

	if($user_level > 1 and !current_user_can("edit_posts") and ! current_user_can("edit_pages")) {
		return;
	}
	if(get_user_option("rich_editing") == "true") {
		add_filter("mce_external_plugins", "add_faq_editor");
		add_filter("mce_buttons", "register_faq_editor");
	}
}
add_action("init", "init_faq_editor");

function register_faq_editor($buttons) {
	array_push($buttons, "separator", "faq");
	return $buttons;
}

function add_faq_editor($plug_array) {
	$plug_array['faq'] = plugins_url('/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'js/faq-add-tinymce.js');
	return $plug_array;
}

//the admin panel for question creation
function faq_options() {
	include('faq-actions.php');
	include('faq-options.php');
}

function faq_askquestions() {
	include('faq-askquestions-actions.php');
	include('faq-askquestions.php');
}

function faq_questions() {
	include('faq-actions.php');
	
	if(empty($_GET['insert'])) {
		if(empty($_GET['edit'])) {
			include('faq-manage-questions.php');
		} else {
			include('faq-add-question.php');	
		}
	} else {
		include('faq-insert-category.php');
	}
}

function faq_categories() {
	include('faq-actions.php');
			
	if(empty($_GET['edit'])) {
		include('faq-manage-categories.php');
	} else {
		include('faq-add-category.php');	
	}
}

//add some css
function faq_add_css() {
	$pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	echo '<link href="'.$pluginPath.'/css/faq-style.css" rel="stylesheet" type="text/css" />';
}

add_action('admin_head','faq_add_css');

//FAQ functions below
function faq_get_first_category() {
	global $wpdb;
	$first_cat = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_categories ORDER BY id ASC LIMIT 0,1");
	return $first_cat[0];
}

function faq_get_questions($category) {
	global $wpdb;
	$questions = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_questions WHERE category='".$category."' AND answer!='' ORDER BY id DESC");
	return $questions;
}

function faq_get_empty_questions() {
	global $wpdb;
	$questions = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_questions WHERE answer='' ORDER BY id DESC");
	return $questions;
}

function faq_get_categories() {
	global $wpdb;
	$categories = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_categories ORDER BY id ASC");
	return $categories;
}

function faq_get_category($id) {
	global $wpdb;
	$the_cat = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_categories WHERE id='".$id."'");
	return $the_cat[0];
}

function faq_get_question($id) {
	global $wpdb;
	$the_question = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ot_faq_questions WHERE id='".$id."'");
	return $the_question[0];
}

//END FAQ functions

//on install, create the right database tables
function faq_install() {
   global $wpdb;
   
   $table_name = $wpdb->prefix . "ot_faq_questions";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  category int(9) DEFAULT '0' NOT NULL,
	  question text NOT NULL,
	  answer text NOT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
    }
	
   $table_name = $wpdb->prefix . "ot_faq_categories";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  category VARCHAR(50) NOT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);


      $insert = "INSERT INTO " . $table_name .
            " (category) " .
            "VALUES ('".__('General','cms')."')";

      $results = $wpdb->query( $insert );
    }
	
}
faq_install();


//function to show dropdown in overview
function faq_category_dropdown() {
?>
    <select name="faq_category" id="faq_category" onchange="window.location='?page=faq-questions&amp;cat='+this.value">
    	<?php
			global $cur_category;
			$categories = faq_get_categories();
			foreach($categories as $category) {
				$selected = '';
				if($category->category == $cur_category->category) $selected = ' selected="selected" ';
				echo '<option'.$selected.' value="'.$category->id.'">'.$category->category.'</option>';
			}
		?>
    </select>
<?php
}

//functions to show editors
function faq_categoryname_meta_box() {
?>
		<?php			
			$category_id = 0;
			if($_GET['id']) $category_id = $_GET['id'];
			
			global $edit_category;
			if($category_id) $edit_category = faq_get_category($category_id);
		?>
	<input type="hidden" name="faq_category_id" value="<?php echo $category_id?>" />
    <input type="text" name="faq_category_name" value="<?php echo $edit_category->category?>" class="regular-text" />
<?php
}

function faq_category_meta_box() {
?>
    <select name="faq_category" id="faq_category">
    	<?php
			global $cur_category;
			$categories = faq_get_categories();
			foreach($categories as $category) {
				$selected = '';
				if($category->category == $cur_category->category) $selected = ' selected="selected" ';
				echo '<option'.$selected.' value="'.$category->id.'">'.$category->category.'</option>';
			}
			
			$question_id = 0;
			if($_GET['id']) $question_id = $_GET['id'];
			
			global $edit_question;
			if($question_id) $edit_question = faq_get_question($question_id);
		?>
    </select>
	<input type="hidden" name="faq_question_id" value="<?php echo $question_id?>" />
<?php
}

function faq_question_meta_box() {
	global $edit_question;
	?>
    <textarea id="excerpt" name="faq_question"><?php echo $edit_question->question?></textarea>
    <?php
}

function faq_answer_meta_box() {
	global $edit_question;
	?>
    <textarea id="content" name="faq_answer"><?php echo $edit_question->answer?></textarea>
    <?php
}
?>