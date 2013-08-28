<?php
global $cur_category;

$cur_category = faq_get_category($_GET['cat']);
if(empty($_GET['cat'])) {
	$cur_category = faq_get_first_category();
}

if(!$cur_category) { ?>
<div class="wrap">
<div id="faq-wrapper">
	<h2><?php echo __('Create category','cms')?></h2>
    <p><?php echo __('You did not create a category yet. Please <a href="?page=faq-categories">create one</a> first.','cms')?></p>
</div>
</div>
<?php
} else {
?>
<div class="wrap">
<div id="faq-wrapper">
	<h2 class="faq_questions_header">&ldquo;<?php echo ucfirst($cur_category->category)?>&rdquo; <?php echo __('Questions','cms')?></h2>
    <div class="tablenav faq_questions_dropdown">
    	<?php faq_category_dropdown(); ?>
    </div>
    <form method="post" action="?page=faq-questions&amp;cat=<?php echo $cur_category->id?>" id="faq_form_action">
    <p>
    	<select name="faq_action">
        	<option value="actions"><?php echo __('Actions')?></option>
            <?php
				$categories = faq_get_categories();
				foreach($categories as $category) {
					if($category->category != $cur_category->category) {
						echo '<option value="cat-'.$category->id.'">'.__('Move to','cms').' '.$category->category.'</option>';
					}
				}
			?>
        	<option value="delete"><?php echo __('Delete')?></option>
        </select>
        <input type="submit" name="faq_form_action_changes" class="button-secondary" value="<?php echo __('Apply')?>" />
    	<input type="button" class="button-primary" value="<?php echo __('Add question','cms')?>" onclick="window.location='?page=faq-questions&amp;edit=true&amp;cat=<?php echo $cur_category->id?>'" />
    </p>
    <table class="widefat page fixed" cellpadding="0">
    	<thead>
        	<tr>
				<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
					<input type="checkbox"/>
				</th>
            	<th class="manage-column"><?php echo __('Question','cms')?></th>
            	<th class="manage-column"><?php echo __('Answer','cms')?></th>
            	<th class="manage-column">
                	<img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>images/move-up.png" />
                	<img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>images/move-down.png" />
                </th>
            </tr>
        </thead>
    	<tfoot>
        	<tr>
				<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
					<input type="checkbox"/>
				</th>
            	<th class="manage-column"><?php echo __('Question','cms')?></th>
            	<th class="manage-column"><?php echo __('Answer','cms')?></th>
            	<th class="manage-column">
                	<img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>images/move-up.png" />
                	<img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>images/move-down.png" />
                </th>
            </tr>
        </tfoot>
        <tbody>
            	<?php
				$questions = faq_get_questions($cur_category->id);
				if($questions) {
					$i=0;
					foreach($questions as $question) { $i++;
						?>
						<tr class="<?php echo (ceil($i/2) == ($i/2)) ? "" : "alternate"; ?>">
							<th class="check-column" scope="row">
								<input type="checkbox" value="<?php echo $question->id?>" name="faq_question[]" />
							</th>
							<td>                    
								<a href="?page=faq-questions&amp;id=<?php echo $question->id?>&amp;cat=<?php echo $question->category?>&amp;edit=true" class="row-title"><?php echo $question->question?></a>
								<div class="row-actions">
									<span class="edit"><a href="?page=faq-questions&amp;id=<?php echo $question->id?>&amp;cat=<?php echo $question->category?>&amp;edit=true">Edit</a> | </span>
									<span class="delete"><a href="?page=faq-questions&amp;cat=<?php echo $question->category?>&amp;delete=<?php echo $question->id?>" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a></span>
								</div>
							</td>
							<td><?php echo $question->answer?></td>
							<td>
								<?php if($i!=1) { ?><a class="faq-move_up" href="?page=faq-questions&amp;order-id=<?php echo $question->id; ?>&amp;direction=up&amp;cat=<?php echo $question->category; ?>"></a><?php } ?>
								<?php if($i != count($questions)) { ?><a class="faq-move_down" href="?page=faq-questions&amp;order-id=<?php echo $question->id; ?>&amp;direction=down&amp;cat=<?php echo $question->category; ?>"></a><?php } ?>
							</td>
						</tr>
						<?php
					}
                } else {
				?>
                	<tr><td colspan="4"><?php echo __('You did not create any questions yet for this category.','cms')?> <a href="?page=faq-questions&amp;edit=true&amp;cat=<?php echo $cur_category->id?>"><?php echo __('Create one','cms') ?></a></td></tr>
                <?php
				}
				?>
        </tbody>
    </table>
    <p>
    	<select name="faq_action-2">
        	<option value="actions"><?php echo __('Actions')?></option>
            <?php
				$categories = faq_get_categories();
				foreach($categories as $category) {
					if($category->category != $cur_category->category) {
						echo '<option value="cat-'.$category->id.'">'.__('Move to','cms').' '.$category->category.'</option>';
					}
				}
			?>
        	<option value="delete"><?php echo __('Delete')?></option>
        </select>
        <input type="submit" name="faq_form_action_changes-2" class="button-secondary" value="<?php echo __('Apply')?>" />
    	<input type="button" class="button-primary" value="<?php echo __('Add question','cms')?>" onclick="window.location='?page=faq-questions&amp;edit=true&amp;cat=<?php echo $cur_category->id?>'" />
    </p>
    </form>
</div>
</div>

<?php } //end if there is no category statement ?>