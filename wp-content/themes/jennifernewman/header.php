<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<meta name="google-site-verification" content="5w8xssc7QuwWO0M_ETc-mkWAT0INmsgvH5XQKGYvFrE" />

<title><?php if (is_home () ) { bloginfo('name'); } elseif ( is_category() ) { single_cat_title(); if(get_bloginfo('name') != "") echo ' - ' ; bloginfo('name'); }
 elseif (is_single() ) { single_post_title(); }
 elseif (is_page() ) { bloginfo('name'); if(get_bloginfo('name') != "") echo ': '; single_post_title(); }
 else { wp_title('',true); } ?></title>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/script.js"></script>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.ie6.css" type="text/css" media="screen" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.ie7.css" type="text/css" media="screen" /><![endif]-->
<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', 'kubrick'), get_bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php printf(__('%s Atom Feed', 'kubrick'), get_bloginfo('name')); ?>" href="<?php bloginfo('atom_url'); ?>" /> 
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<!-- New Fonts -->
		<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/fonts/stylesheet.css" type="text/css" charset="utf-8">


		<script type="text/javascript" src="http://jqueryjs.googlecode.com/files/jquery-1.3.1.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function(){
				//To switch directions up/down and left/right just place a "-" in front of the top/left attribute
				//Full Caption Sliding (Hidden to Visible)
				$('.boxgrid.captionfull').hover(function(){
					$(".cover", this).stop().animate({top:'115px'},{queue:false,duration:160});
				}, function() {
					$(".cover", this).stop().animate({top:'180px'},{queue:false,duration:160});
				});
			});
		</script>


<?php wp_head(); ?>
</head>
<div id="art-main">
<div class="art-Sheet">
    <div class="art-Sheet-body">

<div class="art-Header">
    <div class="art-Header-jpeg">
    	<div class="topnotice">
			<h2>inside-outside furniture...<a href="https://twitter.com/search/realtime?q=%23jnstudio&src=typd" target="_blank"><font color=#FFBF00>#jnstudio</a></font></h2>      	
    	</div>
    </div>
    <a class="fancybox btnhead" href="#form" title="Qutotations">Request a Quotation</a>
</div>

<div class="art-nav" id="top-menu">


	<?php include (TEMPLATEPATH . '/sidebar3.php'); ?>
	
				    			<div class="flag"></div>

	
</div>


<div id="form" style="display:none">
	<iframe height="390" allowTransparency="true" frameborder="0" scrolling="yes" style="width:100%; border:none" src="http://www.jennifernewman.com/machform/embed.php?id=1" title="Jennifer Newman - Quotations"><a href="http://www.jennifernewman.com/machform/view.php?id=1" title="Jennifer Newman - Quotations">Jennifer Newman - Quotations</a></iframe>
</div>