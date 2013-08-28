<div class="art-sidebar1">      
<?php if (!art_sidebar(1)): ?>
<div class="art-Block">
    <div class="art-Block-cc"></div>
    <div class="art-Block-body">
<div class="art-BlockHeader">
    <div class="art-header-tag-icon">
        <div class="t"><?php _e('Categories', 'kubrick'); ?></div>
    </div>
</div><div class="art-BlockContent">
    <div class="art-BlockContent-body">
<ul>
  <?php wp_list_categories('show_count=1&title_li='); ?>
</ul>
		<div class="cleared"></div>
    </div>
</div>

		<div class="cleared"></div>
    </div>
</div>

<?php endif ?>
</div>
