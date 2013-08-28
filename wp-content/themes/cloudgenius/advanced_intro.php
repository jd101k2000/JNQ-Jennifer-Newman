<?php
/*
Template Name: Advanced Intro
*/
?>

<?php get_header(); ?>


<div class="art-contentLayout">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?> 

<div class="art-content home" style="width:960px;">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>
    <td valign="top">
     
		<div class="home_testimonials">
			<div class="side_ads"><?php the_block('ads-img'); ?></div>
			<div class="hoz"></div>
			<div class="side_text_home"></div>
			<div class="side_text_home_static"><?php the_block('ads-text'); ?></div>
		</div>  
		    
	</td>
    <td colspan="4" style="padding:0px; margin:0px;">
    
		<div class="thumb_container">
		    
		    <div class="new" style="right: 0px; top: 25px;"></div>	
			<div class="boxgrid captionfull">
			<a href="http://jnq.cloud-genius.com/products/m-bamboo-table">
				<img src="http://jnq.cloud-genius.com/wp-content/uploads/2013/03/Bench-orange-HP.png"/></a>
				<div class="cover boxcaption">
					<h3>NEW: M-Bamboo Table</h3>
					<p><a href="http://jnq.cloud-genius.com/products/m-bamboo-table">More Information</a></p>
				</div>		
			</div>

			<div class="boxgrid captionfull">
			<a href="http://jnq.cloud-genius.com/products/angle-table">
				<img src="http://jnq.cloud-genius.com/wp-content/uploads/2011/03/web_home-angle-table.jpg"/></a>
				<div class="cover boxcaption">
					<h3>Angle Table / Bench</h3>
					<p><a href="http://jnq.cloud-genius.com/products/angle-table">More Information</a></p>
				</div>		
			</div>

			<div class="boxgrid captionfull">
				<a href="http://jnq.cloud-genius.com/products/portal-bench">
				<img src="http://jnq.cloud-genius.com/wp-content/uploads/2012/11/groovTABLE_saffron_2.jpg"/></a>
				<div class="cover boxcaption">
					<h3>Portal Bench</h3>
					<p><a href="http://jnq.cloud-genius.com/products/portal-bench">More Information</a></p>
				</div>		
			</div>
			
			<div class="boxgrid captionfull">
			<a href="http://jnq.cloud-genius.com/products/m-bench">
				<img src="http://jnq.cloud-genius.com/wp-content/uploads/2013/03/Pink-Bench.png"/></a>
				<div class="cover boxcaption">
					<h3>NEW: M-Bench</h3>
					<p><a href="http://jnq.cloud-genius.com/products/trestle">More Information</a></p>
				</div>		
			</div>
			
			<div class="boxgrid captionfull">
			<a href="http://jnq.cloud-genius.com/products/a-frame-bench">
				<img src="http://jnq.cloud-genius.com/wp-content/themes/jennifernewman/images/a-frame-home.jpg"/></a>
				<div class="cover boxcaption">
					<h3>A Frame Bench</h3>
					<p><a href="http://jnq.cloud-genius.com/products/a-frame-bench">More Information</a></p>
				</div>		
			</div>

			<div class="boxgrid captionfull">
			<a href="http://jnq.cloud-genius.com/products/groove-table">
				<img src="http://jnq.cloud-genius.com/wp-content/uploads/2012/11/Turq-Groove.png"/></a>
				<div class="cover boxcaption">
					<h3>Groove Table</h3>
					<p><a href="http://jnq.cloud-genius.com/products/groove-table">More Information</a></p>
				</div>		
			</div>
			
			
		</div>		
      
      </td>
    </tr>

</table>



<?php comments_template(); ?>
<?php endwhile; endif; ?>

</div>

</div>
<div class="cleared"></div>

<?php get_footer(); ?>