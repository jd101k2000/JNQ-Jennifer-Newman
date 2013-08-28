<?php
$faq_javascript = get_option('faq-javascript-off');
$faq_askquestions = get_option('faq-askquestions-off');
?>

<div class="wrap">
<div id="faq-wrapper">
	<h2><?php echo __('Options')?></h2>
	<form action="?page=faq-options" method="post">
    	<p><input type="checkbox" name="faq-javascript" id="faq-javascript" <?php if(!$faq_javascript) echo 'checked' ?> /> <label for="faq-javascript"><?php echo __('Use Javascript to show the FAQ','cms')?></label></p>
    	<p><input type="checkbox" name="faq-askquestions" id="faq-askquestions" <?php if(!$faq_askquestions) echo 'checked' ?> /> <label for="faq-askquestions"><?php echo __('The ability for users to ask questions. Insert this in a page with <code>[faq-ask-questions]</code>.','cms')?></label></p>
        <p><label for="faq-email"><strong><?php echo __('When a user submits a question, send an e-mail to this address:','cms'); ?></strong></label><br /><input class="regular-text" type="text" name="faq-email" id="faq-email" value="<?php echo get_option('faq-email'); ?>" /></p>
        <p><input type="submit" name="faq-options-changes" value="<?php echo __('Save changes','cms')?>" class="button-primary" /></p>
    </form>
    <h2><?php echo __('Information')?></h2>
    <p>To allow users to ask questions, insert <code>[faq-ask-questions]</code> where you want in a page or post.</p>
    <p>To allow users to search in the faq, insert <code>[faq-search]</code> where you want the search bar.</p>
</div>
</div>