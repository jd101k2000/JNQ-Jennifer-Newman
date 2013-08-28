<div class="wrap">
<div id="faq-wrapper">
	<h2><?php echo __('Categories','cms')?></h2>
    
    <form method="post" action="?page=faq-categories" id="faq_form_action">
    <p>
    	<select name="faq_action">
        	<option value="actions"><?php echo __('Actions')?></option>
        	<option value="delete"><?php echo __('Delete')?></option>
        </select>
        <input type="submit" name="faq_form_action_changes_cat" class="button-secondary" value="<?php echo __('Apply')?>" />
    	<input type="button" class="button-primary" value="<?php echo __('Add category','cms')?>" onclick="window.location='?page=faq-categories&amp;edit=true'" />
    </p>
    <table class="widefat page fixed" cellpadding="0">
    	<thead>
        	<tr>
				<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
					<input type="checkbox"/>
				</th>
            	<th class="manage-column"><?php echo __('Category','cms')?></th>
            </tr>
        </thead>
    	<tfoot>
        	<tr>
				<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
					<input type="checkbox"/>
				</th>
            	<th class="manage-column"><?php echo __('Category','cms')?></th>
            </tr>
        </tfoot>
        <tbody>
            	<?php
				$categories = faq_get_categories();
				if($categories) {
					$i=0;
					foreach($categories as $category) { $i++;
					?>
                    <tr class="<?php echo (ceil($i/2) == ($i/2)) ? "" : "alternate"; ?>">
						<th class="check-column" scope="row">
							<input type="checkbox" value="<?php echo $category->id?>" name="faq_category[]" />
						</th>
						<td>                    
							<a href="?page=faq-categories&amp;id=<?php echo $category->id?>&amp;edit=true" class="row-title"><?php echo $category->category?></a>
                            <div class="row-actions">
                                <span class="edit"><a href="?page=faq-categories&amp;id=<?php echo $category->id?>&amp;edit=true">Edit</a> | </span>
                                <span class="delete"><a href="?page=faq-categories&amp;delete_cat=<?php echo $category->id?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a></span>
                            </div>
                        </td>
                    </tr>
				<?php
					}
                } else {
				?>
                	<tr><td colspan="2"><?php echo __('You did not create any categories yet.','cms')?> <a href="?page=faq-categories&amp;edit=true"><?php echo __('Create one','cms') ?></a></td></tr>
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
        <input type="submit" name="faq_form_action_changes_cat-2" class="button-secondary" value="<?php echo __('Apply')?>" />
    	<input type="button" class="button-primary" value="<?php echo __('Add category','cms')?>" onclick="window.location='?page=faq-categories&amp;edit=true'" />
    </p>
    </form>
</div>
</div>