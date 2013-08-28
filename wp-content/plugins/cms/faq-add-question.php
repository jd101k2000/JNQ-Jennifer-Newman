<?php
global $cur_category;

$cur_category = faq_get_category($_GET['cat']);
if(empty($_GET['cat'])) {
	$cur_category = faq_get_first_category();
}

//create meta boxes
add_meta_box('faq_category', __('Category'), 'faq_category_meta_box', 'faq', 'normal', 'core');
add_meta_box('faq_question', __('Question'), 'faq_question_meta_box', 'faq', 'normal', 'core');
add_meta_box('faq_answer', __('Answer'), 'faq_answer_meta_box', 'faq', 'normal', 'core');
?>
<div class="wrap">
<div id="faq-wrapper">
<form method="post" action="?page=faq-questions&amp;cat=<?php echo $cur_category->id?>">    
	<h2><?php if($_GET['id']) { echo __('Edit question'); } else { echo __('Add'); ?> &ldquo;<?php echo ucfirst($cur_category->category)?>&rdquo; <?php echo __('question','cms')?> <?php } ?></h2>
    
	<div id="poststuff" class="metabox-holder">
    <?php
		do_meta_boxes('faq', 'normal','low');
	?>
	</div>

<input type="submit" value="<?php if($_GET['id']) { echo __('Edit question','cms'); } else { echo __('Add question','cms'); } ?>" name="faq_add_question" class="button-primary">
</form>
</div>
</div>