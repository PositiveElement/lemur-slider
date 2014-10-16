<?php
/**
 * Lemur Slider Admin scripts and settings
 * including CPT columns
 */



//Add post type class to body in admin
function lemur_slider_add_to_admin_body_class( $classes ) {
	global $post;
	$mode = '';
	$uri = $_SERVER["REQUEST_URI"];
	$post_type = '';
	if ($post) $post_type = get_post_type($post->ID);
	if (strstr($uri,'edit.php')) {
		$mode = ' edit-list-';
	}
	if (strstr($uri,'post.php')) {
		$mode = ' edit-page-';
	}
	$classes .= $mode . $post_type. ' ';
	return $classes;
}
add_filter('admin_body_class', 'lemur_slider_add_to_admin_body_class');
//Add post type class to body in admin

//Slider Custom Table and another modifications
function lemur_slider_custom_columns($columns) {
	$columns = array(
		'cb'	 	=> '<input type="checkbox" />',
		'preview'	=> __('Preview','lemur_slider'),
		'title' 	=> __('Slider','lemur_slider'),
		'number' 	=> __('Number of slides','lemur_slider'),
		'shortcode'	=> __('Shortcode','lemur_slider'),
		'slider_id'	=> __('Slider ID','lemur_slider'),
		'date'		=> __('Date','lemur_slider'),
	);
	return $columns;
}

function lemur_slider_custom_columns_data($column) {
	global $post;
	$custom = get_post_custom($post->ID);
	$slide_data = array();
	if (isset($custom['slide_data'][0])) $slide_data = unserialize($custom['slide_data'][0]);
	if($column == 'shortcode') {
		echo '<input type="text" value="[lemur_slider id=&quot;'.$post->ID.'&quot;]" readonly="true" />';
	}
	if($column == 'preview') {
		echo '<div class="preview_image">';
		if ($slide_data[0]['image']) {
			if(is_numeric($slide_data[0]['image'])) {
				$image_url = wp_get_attachment_image_src($slide_data[0]['image'],'thumbnail', true);
				$image_url = $image_url[0];
			} else {
				$image_url = $slide_data[0]['image'];
			}
			echo '<div style="background-image:url('.$image_url.')"></div>';
		} else {
			echo '<div class="nemusicon-docs"></div>';
		}
		echo '</div>';
	}
	if($column == 'number') {
		echo count($slide_data);
	}
	if($column == 'slider_id') {
		echo '#'.$post->ID;
	}
}

add_action("manage_lemur_slider_posts_custom_column", "lemur_slider_custom_columns_data");
add_filter("manage_edit-lemur_slider_columns", "lemur_slider_custom_columns");

add_filter('post_row_actions','lemur_slider_action_row',10,2);
function lemur_slider_action_row($actions,$post) {
	if ($post->post_type =="lemur_slider"){
		$preview_link = LEMUR_SLIDER_URL.'lemur-slider-preview.php?id='.$post->ID;
		$preview_button = __( 'Preview', 'lemur_slider' );
		array_splice($actions, 2, 0, '<a href="'.$preview_link.'" target="_blank">'.$preview_button.'</a>');
	}
	return $actions;
}
//Slider Custom Table and another modifications

//Convert hex codes to rgba
function lemur_slider_hex2rgb($hex) {
	$hex = str_replace("#", "", $hex);

	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
	return $rgb;
}
