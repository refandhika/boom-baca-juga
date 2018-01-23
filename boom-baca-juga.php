<?php
/**
* Plugin Name: 	Boombastis - Baca Juga
* Plugin URI: 	https://www.boombastis.com
* Description: 	Boombastis "Baca Juga" section, adding box containing several link to other article inside post content.
* Version: 		1.0.3
* Author: 		Refa Andhika
* Author URI: 	https://www.boombastis.com
* License: 		Private
* License URI: 	https://www.boombastis.com
*
*/

/**
* Init settings menu
*/
function bbjbox_setting_init() {
	register_setting('general_bbjbox', 'bbjbox_options');

	add_settings_section('bbjbox_first_section', 'Desktop Native Ads Setting', 'bbjbox_desktop_callback', 'general_bbjbox');

	add_settings_field('bbjbox_desktop_native_ads', 'Native Ads Tags', 'bbjbox_desktop1_callback', 'general_bbjbox', 'bbjbox_first_section');
	add_settings_field('bbjbox_desktop_native_script', 'Native Ads Scripts', 'bbjbox_desktop2_callback', 'general_bbjbox', 'bbjbox_first_section');

	add_settings_section('bbjbox_second_section', 'Mobile Native Ads Setting', 'bbjbox_mobile_callback', 'general_bbjbox');

	add_settings_field('bbjbox_mobile_native_ads', 'Native Ads Tags', 'bbjbox_mobile1_callback', 'general_bbjbox', 'bbjbox_second_section');
	add_settings_field('bbjbox_mobile_native_script', 'Native Ads Scripts', 'bbjbox_mobile2_callback', 'general_bbjbox', 'bbjbox_second_section');
}
add_action( 'admin_init', 'bbjbox_setting_init' );

function create_bbjbox_settings_page() {
	$page_title = 'Boombastis - Baca Juga Setting';
	$menu_title = 'Boom - Baca Juga';
	$capability = 'manage_options';
	$slug = 'bbjbox';
	$callback = 'bbjbox_setting_page_content';

	add_submenu_page('options-general.php', $page_title, $menu_title, $capability, $slug, $callback);
}
add_action( 'admin_menu', 'create_bbjbox_settings_page' );

function bbjbox_setting_page_content() { 

	if ( !current_user_can('manage_options') ) :
		return;
	endif;

	if ( isset( $_GET['setting-updated'] ) ) :
		add_settings_error( 'bbjbox_messages', 'bbjbox_messages', __( 'Setting Saved', 'general_bbjbox'), 'updated' );
	endif;

	settings_errors( 'bbjbox_messages' );
	?>
	<div class="wrap">
		<h2>Boombastis - Baca Juga</h2>
		<p>A box containing related article will be added automatically to your content.</p>
		<form action="options.php" method="post">
			<?php 
				settings_fields('general_bbjbox');
				do_settings_sections('general_bbjbox');
				submit_button('Save Settings');
			?>
		</form>
	</div>
<?php
}

/**
* Callback for desktop section
*/

function bbjbox_desktop_callback($args) {
	switch ($args['id']) {
		case 'bbjbox_first_section':
			echo 'Add native ads for desktop view only.';
			break;
	}
}

function bbjbox_desktop1_callback($args) {
	$options = get_option('bbjbox_options');
	?>
	<input name="bbjbox_options[bbjbox_desktop_ads]" id="bbjbox_desktop_ads" text="text" size="100" value="<?php echo isset( $options['bbjbox_desktop_ads'] ) ? esc_attr($options['bbjbox_desktop_ads']) : '';?>"/>
	<?php 
}

function bbjbox_desktop2_callback($args) {
	$options = get_option('bbjbox_options');
	?>
	<textarea name="bbjbox_options[bbjbox_desktop_scripts]" id="bbjbox_desktop_scripts" cols="100" rows="4"><?php echo isset( $options['bbjbox_desktop_scripts'] ) ? esc_attr($options['bbjbox_desktop_scripts']) : '';?></textarea>
	<?php 
}

/**
* Callback for mobile section
*/

function bbjbox_mobile_callback($args) {
	switch ($args['id']) {
		case 'bbjbox_second_section':
			echo 'Add native ads for mobile view only.';
			break;
	}
}

function bbjbox_mobile1_callback($args) {
	$options = get_option('bbjbox_options');
	?>
	<input name="bbjbox_options[bbjbox_mobile_ads]" id="bbjbox_mobile_ads" text="text" size="100" value="<?php echo isset( $options['bbjbox_mobile_ads'] ) ? esc_attr($options['bbjbox_mobile_ads']) : '';?>"/>
	<?php 
}

function bbjbox_mobile2_callback($args) {
	$options = get_option('bbjbox_options');
	?>
	<textarea name="bbjbox_options[bbjbox_mobile_scripts]" id="bbjbox_mobile_scripts" cols="100" rows="4"><?php echo isset( $options['bbjbox_mobile_scripts'] ) ? esc_attr($options['bbjbox_mobile_scripts']) : '';?></textarea>
	<?php 
}

/**
* Main Box Creator
*
* @param 	string 	$content 				Get from wordpress the_content.
* @return 	string 							Modified content with related article box.
*/
function insert_boom_baca_juga_box($content){
	global $post;

	if ( $post->post_type == 'post' ) :

		$options = get_option('bbjbox_options');	
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

		if ( wp_is_mobile() ) :
			$ads=$options['bbjbox_mobile_ads'];
		else :
			$ads=$options['bbjbox_desktop_ads'];
		endif;

		/* Wrap link inside Box */
		$bacajugain='<div class="rec-article">
				<div class="rec-article-title">Baca Juga</div>'.
				$ads.
				$baca_juga_inlink.'
			</div>';

		/* Split content by paragraph for insertion */
		$paragraph = explode('</p>', wpautop($content));

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
	endif;

	return $content;
}
add_action('loop_start', function( WP_Query $query ) {
	if ( $query->is_main_query() ) {
		add_filter('the_content', 'insert_boom_baca_juga_box', -10);
	}
});
add_action('loop_end', function( WP_Query $query ) {
	if ( has_filter('the_content', 'insert_boom_baca_juga_box') ) {
		remove_filter('the_content', 'insert_boom_baca_juga_box', -10);
	}
});

/**
* Add individual styling for Baca Juga Box
*/
function boom_baca_juga_css(){
	$plugin_dir = plugin_dir_url( __FILE__ );

	wp_enqueue_style('box_styling', $plugin_dir.'css/style.css');
}
add_action('wp_enqueue_scripts', 'boom_baca_juga_css');

/**
* Add custom native ads script to footer 
*/
function add_na_scripts_footer(){
	$options = get_option('bbjbox_options');
	
	if ( wp_is_mobile() ):
		echo $options['bbjbox_mobile_scripts'];
	else :
		echo $options['bbjbox_desktop_scripts'];
	endif;
}
add_action('wp_footer', 'add_na_scripts_footer');

/*EOF*/