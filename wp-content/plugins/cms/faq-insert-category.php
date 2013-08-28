<?php

?>
<script type="text/javascript">
function insertFaq(id){
	window.opener.tinyMCE.execCommand('mceInsertContent', 0, '[faq id="'+id+'"]');	
	window.close();
}
</script>

<style>
#wphead,
#adminmenu,
#footer,
#update-nag,
#screen-meta,
#logo
{
	display: none;
}
#wpbody {
	margin: 0px;
}
#wpcontent {
	padding-bottom: 0px;
}
#wpbody-content {
	float: none;
	padding: 10px;
	width: auto;
}
div.wrap { 
	margin: 0px;
	display: block;
	position: relative;
	top: 0;
	left: 0;
}
body.wp-admin {
	min-width: inherit;
}
</style>


<select name="faq_insert" id="faq_insert">
	<?php
		$categories = faq_get_categories();
		foreach($categories as $category) {
			echo '<option value="'.$category->id.'">'.ucfirst($category->category).'</option>';
		}
	?>
</select>

<input type="submit" class="button-primary" value="<?php echo __('Insert FAQ')?>" onclick="insertFaq(document.getElementById('faq_insert').value);" />