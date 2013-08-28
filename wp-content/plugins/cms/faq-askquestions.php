<div class="wrap">
<div id="faq-wrapper">
	<h2 class="faq_questions_header"><?php echo __('Open questions','cms')?></h2>
    
    <form method="post" action="?page=faq-askquestions" id="faq_form_action">
    <p>
    	<select name="faq_action">
        	<option value="actions"><?php echo __('Actions')?></option>
        	<option value="delete"><?php echo __('Delete')?></option>
        </select>
        <input type="submit" name="faq_form_action_changes" class="button-secondary" value="<?php echo __('Apply')?>" />
    </p>
    <table class="widefat page fixed" cellpadding="0">
    	<thead>
        	<tr>
				<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
					<input type="checkbox"/>
				</th>
            	<th class="manage-column"><?php echo __('Question','cms')?></th>
            	<th class="manage-column"><?php echo __('Category','cms')?></th>
            </tr>
        </thead>
    	<tfoot>
        	<tr>
				<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
					<input type="checkbox"/>
				</th>
            	<th class="manage-column"><?php echo __('Question','cms')?></th>
            	<th class="manage-column"><?php echo __('Category','cms')?></th>
            </tr>
        </tfoot>
        <tbody>
            	<?php
				$questions = faq_get_empty_questions();
				if($questions) {
					$i=0;
					foreach($questions as $question) { $i++;
						$category = faq_get_category($question->category);
						?>
						<tr class="<?php echo (ceil($i/2) == ($i/2)) ? "" : "alternate"; ?>">
							<th class="check-column" scope="row">
								<input type="checkbox" value="<?php echo $question->id?>" name="faq_question[]" />
							</th>
							<td>                    
								<a href="?page=faq-questions&amp;id=<?php echo $question->id?>&amp;cat=<?php echo $question->category?>&amp;edit=true" class="row-title"><?php echo $question->question?></a>
								<div class="row-actions">
									<span class="edit"><a href="?page=faq-questions&amp;id=<?php echo $question->id?>&amp;cat=<?php echo $question->category?>&amp;edit=true">Answer</a> | </span>
									<span class="delete"><a href="?page=faq-askquestions&amp;delete=<?php echo $question->id?>" onclick="return confirm('<?php echo __('Are you sure you want to delete this question?'); ?>');">Delete</a></span>
								</div>
							</td>
							<td><?php echo $category->category?></td>
						</tr>
						<?php
					}
                } else {
				?>
                	<tr><td colspan="2"><?php echo __('There are no open questions.','cms')?></td></tr>
                <?php
				}
				?>
        </tbody>
    </table>
    <p>
    	<select name="faq_action-2">
        	<option value="actions"><?php echo __('Actions')?></option>
        	<option value="delete"><?php echo __('Delete')?></option>
        </select>
        <input type="submit" name="faq_form_action_changes-2" class="button-secondary" value="<?php echo __('Apply')?>" />
    </p>
    </form>
</div>
</div>