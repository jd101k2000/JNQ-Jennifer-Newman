<?php

//create meta boxes
add_meta_box('faq_category', __('Name'), 'faq_categoryname_meta_box', 'faq', 'normal', 'core');
?>
<div class="wrap">
<div id="faq-wrapper">
<form method="post" action="?page=faq-categories">    
	<h2><?php if($_GET['id']) { echo __('Edit category','cms'); } else { echo __('Add category','cms'); } ?></h2>
    
	<div id="poststuff" class="metabox-holder">
    <?php
		do_meta_boxes('faq', 'normal','low');
	?>
	</div>

<input type="submit" value="<?php if($_GET['id']) { echo __('Edit category','cms'); } else { echo __('Add category','cms'); } ?>" name="faq_add_category" class="button-primary">
</form>
</div>
</div>