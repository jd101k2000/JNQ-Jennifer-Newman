<?php
/*
Template Name: Products
*/
?>

<?php get_header(); ?>
<div class="art-contentLayout">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?> 

<div class="productads">
<div class="side_text"><?php the_block('ads-text'); ?></div>
<?php the_block('ads'); ?>
</div>

<div class="art-content">

<!-- Banner Section -->
<div class="banner">
<?php if(function_exists('show_media_header')){ show_media_header(); } ?>
</div>
<!-- Banner Section -->


<!-- Product Section -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top" style="width:183px; border-right-color:#CCC; border-right-style:dotted; border-right-width:1px"><?php the_block('thumbnails'); ?></td>
    <td align="left" valign="top">
    
    
    <div class="art-Post">
    <div class="art-Post-body">
<div class="art-Post-inner art-article">
<h1 class="art-PostHeader"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h1>
<?php $icons = array(); ?>
<?php if (!is_page()): ?><?php ob_start(); ?><?php the_time(__('F jS, Y', 'kubrick')) ?>
<?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (0 != count($icons)): ?>
<div class="art-PostHeaderIcons art-metadata-icons">
<?php echo implode(' | ', $icons); ?>

</div>
<?php endif; ?>
<div class="art-PostContent">

          <?php if (is_search()) the_excerpt(); else the_content(__('Read the rest of this entry &raquo;', 'kubrick')); ?>
          <?php if (is_page() or is_single()) wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
        
</div>
<div class="cleared"></div>

</div>

		<div class="cleared"></div>
    </div>
</div>


</td>
  </tr>
</table>
<!-- Product Section -->



<?php comments_template(); ?>
<?php endwhile; endif; ?>

</div>

</div>
<div class="cleared"></div>

<?php get_footer(); ?>