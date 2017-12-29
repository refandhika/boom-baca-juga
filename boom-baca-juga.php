<?php
/*
Plugin Name: 	Boombastis - Baca Juga
Plugin URI: 	https://www.boombastis.com
Description: 	Boombastis "Baca Juga" section, adding box containing several link to other article inside post content.
Version: 		1.0.0
Author: 		Refa Andhika
Author URI: 	https://www.boombastis.com
License: 		Private
License URI: 	https://www.boombastis.com

*/

/* Main Box Creator */
function insert_boom_baca_juga_box($content){
	$id_post = get_the_ID();

	$args = array(
		'posts_per_page'   => 7,
		'offset'           => 0,
		'category'         => 0,
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	       => '',
		'author_name'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$posts_array = get_posts($args);

	$i=1;
	$baca_juga_inlink='';
	/* Get Two Latest Article */
	foreach ($posts_array as $key => $value) {
		if($id_post!=$value->ID){
			$baca_juga_inlink=$baca_juga_inlink.'<div class="rec-article-cont">
				<a class="ajudul" href="'.get_permalink($value->ID).'">'.get_the_title($value->ID).'</a>
			</div>';
			$i++;
			if($i==3) {break;}
		}
	}

	/* Wrap link inside Box */
	$bacajugain='<div class="rec-article">
			<div class="rec-article-title">Baca Juga</div>
			<div data-advs-adspot-id="OTk5OjEzMTQ0" style="display:none"></div>'.
			$baca_juga_inlink.'
		</div>';

	/* Split content by paragraph for insertion */
	$paragraph = explode('</p>', $content);

	/* Handle Blockquotes Interaction */
	/* 'bqflag' is marking whether content has blockquotes or no */
	$bqflag = false;
	/* 'pc' is paragraph count */
	$pc=0;
	for ($i=0;$i<count($paragraph);$i++){
		if (strpos($paragraph[$i], '<blockquote') !== false) {
			$bqflag = true;
		}
		if ($bqflag === false){
			switch ($pc) {
				case 1:
					$paragraph[$i] = $paragraph[$i].$bacajugain;
					break;
			 	default:
			 		break;
			}
			$pc++;
		}
		if (strpos($paragraph[$i], '</blockquote') !== false) {
			$bqflag = false;
		}
	}

	/* Merge content back */
	$content=implode('</p>', $paragraph);

	return $content;
}
add_filter('the_content', 'insert_boom_baca_juga_box', 10);

/* Add individual styling for Baca Juga Box */
function boom_baca_juga_css(){
	$plugin_dir = plugin_dir_url( __FILE__ );

	wp_enqueue_style('box_styling', $plugin_dir.'css/style.css');
}
add_action('wp_enqueue_scripts', 'boom_baca_juga_css');

/*EOF*/