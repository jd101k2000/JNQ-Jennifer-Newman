<?php
/*
Template Name: Full Intro
*/
?>

<?php get_header(); ?>

<div class="art-contentLayout">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?> 

<div class="art-content home" style="width:960px;">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
  <td colspan="5" align="left" valign="top" style="padding-right:5px; width:350px">
  	<div class="panelcontent" style="margin-bottom:10px; padding-top:30px;"><?php the_block('topnotice'); ?></div>
  </td>
  </tr>
  <tr>
    <td valign="top">
     
		<div class="home_testimonials">
		<div class="side_text_home"><?php the_block('ads-text'); ?></div>
		</div>  
		    
	</td>
    <td colspan="4">	 <?php $icons = array(); ?>
      <?php if (!is_page()): ?><?php ob_start(); ?><?php the_time(__('F jS, Y', 'kubrick')) ?>
      <?php $icons[] = ob_get_clean(); ?><?php endif; ?><?php if (0 != count($icons)): ?>
      <div class="art-PostHeaderIcons art-metadata-icons">
        <?php echo implode(' | ', $icons); ?>
        
        </div>
      <?php endif; ?>
      
      <?php if (is_search()) the_excerpt(); else the_content(__('Read the rest of this entry &raquo;', 'kubrick')); ?>
      <?php if (is_page() or is_single()) wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
      
      <div class="cleared"></div></td>
    </tr>
  <tr>
    <td colspan="5" align="left" valign="top" style="padding-right:5px; width:350px"><div class="panelcontent"><?php the_block('homenotice'); ?></div></td>
  </tr>
</table>



<?php comments_template(); ?>
<?php endwhile; endif; ?>

</div>

</div>
<div class="cleared"></div>

<?php get_footer(); ?>