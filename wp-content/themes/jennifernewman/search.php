<?php get_header(); ?>
<div class="art-contentLayout">
<?php include (TEMPLATEPATH . '/sidebar1.php'); ?><div class="art-content">


	<?php if (have_posts()) : ?>

<div class="art-Post">
		    <div class="art-Post-body">
		<div class="art-Post-inner art-article">
		
<div class="art-PostContent">
        
        
		<h2><?php _e('Search Results', 'kubrick'); ?></h2>

		<?php
		$prev_link = get_previous_posts_link(__('Newer Entries &raquo;', 'kubrick'));
		$next_link = get_next_posts_link(__('&laquo; Older Entries', 'kubrick'));
		?>

		<?php if ($prev_link || $next_link): ?>
<div class="navigation">
	<div class="alignleft"><?php echo $next_link; ?></div>
	<div class="alignright"><?php echo $prev_link; ?></div>
</div>
		<?php endif; ?>


		</div>
		<div class="cleared"></div>
		

		</div>
		
				<div class="cleared"></div>
		    </div>
		</div>
		


		<?php while (have_posts()) : the_post(); ?>
<div class="art-Post">
			    <div class="art-Post-body">
			<div class="art-Post-inner art-article">
			<h2 class="art-PostHeader">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>">
			<?php the_title(); ?>
			</a>
			</h2>
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
			
		<?php endwhile; ?>

		<?php if ($prev_link || $next_link): ?>
		
<div class="art-Post">
		    <div class="art-Post-body">
		<div class="art-Post-inner art-article">
		
<div class="art-PostContent">
        
        
<div class="navigation">
	<div class="alignleft"><?php echo $next_link; ?></div>
	<div class="alignright"><?php echo $prev_link; ?></div>
</div>
		

		</div>
		<div class="cleared"></div>
		

		</div>
		
				<div class="cleared"></div>
		    </div>
		</div>
		
		
		<?php endif; ?>

	<?php else : ?>
<div class="art-Post">
		    <div class="art-Post-body">
		<div class="art-Post-inner art-article">
		
<div class="art-PostContent">
        
        
        <h2><?php _e('Search Results', 'kubrick'); ?></h2>
		<h2 class="center"><?php _e('No posts found. Try a different search?', 'kubrick'); ?></h2>
		<?php if(function_exists('get_search_form')) get_search_form(); ?>
		

		</div>
		<div class="cleared"></div>
		

		</div>
		
				<div class="cleared"></div>
		    </div>
		</div>
		

	<?php endif; ?>

</div>

</div>
<div class="cleared"></div>

<?php get_footer(); ?>