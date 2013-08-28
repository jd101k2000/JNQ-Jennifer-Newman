<?php
/*
=========
De RSS feed van Ontwerpstudio Trendwerk
=========
*/

function dashboard_ot_feed() { //de widget op de dashboard aanmaken
	wp_add_dashboard_widget('custom-feed', __('Nieuws'), 'add_dashboard_ot_feed');
}
add_action('wp_dashboard_setup', 'dashboard_ot_feed');


function dashboard_ot_feed_css() { //css toevoegen
	echo '
	<style type="text/css">
		#dashboard_ot_feed li {
			padding-bottom: 5px;
		}
		#dashboard_ot_feed li p {
			padding:0;
			margin:0;
		}
		#dashboard ot_feed .rss-date {
			color: #666;
		}
	</style>
	';
}
add_action('admin_head', 'dashboard_ot_feed_css');


function add_dashboard_ot_feed() { //de werkelijke code die aangeroepen wordt bij het maken van de widget
	include_once(ABSPATH . WPINC . '/rss.php');
	$feed_url = get_option('cms_dashboard_feed_link');
	
	$rss = fetch_rss($feed_url);
	$rss->items = array_slice($rss->items, 0, 3);	
	echo '<ul id="dashboard_ot_feed">';
	foreach ($rss->items as $item) {
		echo '<li>';
		echo '<a class="rsswidget" target="_blank" href=' . wp_filter_kses($item['link']) . '>' . wptexturize(wp_specialchars($item['title'])) . '</a> <span class="rss-date">'. date('d/m/y',strtotime($item['pubdate'])).'</span>';
		echo '<p>' . wptexturize(wp_specialchars($item['description'])) . '</p>';
		echo '<a target="_blank" href=' . wp_filter_kses($item['link']) . '>Lees verder</a>';
		echo '</li>';
	}
	echo '</ul>';
}
?>