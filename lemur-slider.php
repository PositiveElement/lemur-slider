<?php
/*
Plugin Name: Lemur Slider
Plugin URI:
Description: Forked from the Nemus Slider by Viszt Péter (http://visztpeter.me)
Version: 1.0
Author: Positive Element
Author URI: http://www.positiveelement.com
License: GPLv2 or later
*/

if( !defined('LEMUR_SLIDER_DIR') ) {
	define( 'LEMUR_SLIDER_DIR', plugin_dir_path( __FILE__ ) );
}
// Define a path constant as well as the dir constant... because we might want the realpath instead?
if(!defined('LEMUR_SLIDER_PATH')){
	define('LEMUR_SLIDER_PATH', realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR );
}
if( !defined('LEMUR_SLIDER_URL') ) {
	define('LEMUR_SLIDER_URL', plugin_dir_url(__FILE__));
}

define('LEMUR_SLIDER_VERSION', '1.2.3');
if (!defined('LEMUR_SLIDER_VERSION_KEY')) define('LEMUR_SLIDER_VERSION_KEY', 'lemur_slider_version');
if (!defined('LEMUR_SLIDER_VERSION_NUM')) define('LEMUR_SLIDER_VERSION_NUM', '1.2.3');
add_option(LEMUR_SLIDER_VERSION_KEY, LEMUR_SLIDER_VERSION_NUM);


//Extra button in the plugins list
add_filter('plugin_action_links', 'lemur_slider_plugin_action_links', 10, 2);
function lemur_slider_plugin_action_links($links, $file) {
	static $this_plugin;

	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

	if ($file == $this_plugin) {
		
		$settings_link = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=lemur_slider">'.__('Manage sliders','lemur_slider').'</a>';

		array_unshift($links, $settings_link);
	}
	return $links;
}

//Register the Lemur Slider Post Type to manage the sliders
add_action('init', 'register_lemur_slider_post_type');
function register_lemur_slider_post_type() {

	//Post type registration
	register_post_type('lemur_slider', array(
		'labels' => array(
			'name' => __('Sliders','lemur_slider'),
		    'singular_name' => __('Slider','lemur_slider'),
		    'add_new_item' => __('Add New Slider','lemur_slider'),
		    'edit_item' => __('Edit Slider','lemur_slider'),
		    'new_item' => __('New Slider','lemur_slider'),
		    'all_items' => __('All Sliders','lemur_slider'),
		    'view_item' => __('View Slider','lemur_slider'),
		    'search_items' => __('Search Sliders','lemur_slider'),
		    'not_found' =>  __('No sliders found','lemur_slider'),
		    'not_found_in_trash' => __('No sliders found in the trash','lemur_slider')
		),
		'public' => false,
		'show_ui' => true,
		'publicly_queryable' => false,
		'supports' => array('title'),
		'capability_type' => array("lemur-slider", "lemur-sliders"),
		'map_meta_cap' => true,
		'menu_icon' => 'dashicons-images-alt2'
	));
	
	//Translation support
	load_plugin_textdomain('lemur_slider', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

}

//Capabilities
function lemur_slider_add_caps_to_admin() {
	$caps = array(
		'read',
		'read_lemur-slider',
		'read_private_lemur-sliders',
		'edit_lemur-sliders',
		'edit_private_lemur-sliders',
		'edit_published_lemur-sliders',
		'edit_others_lemur-sliders',
		'publish_lemur-sliders',
		'delete_lemur-sliders',
		'delete_private_lemur-sliders',
		'delete_published_lemur-sliders',
		'delete_others_lemur-sliders',
	);
	$roles = array(
		get_role( 'administrator' ),
		get_role( 'editor' ),
	);
	foreach ($roles as $role) {
		foreach ($caps as $cap) {
			$role->add_cap( $cap );
		}
	}
}
add_action( 'after_setup_theme', 'lemur_slider_add_caps_to_admin' );

//Lemur Slider admin CSS & JS
function lemur_slider_admin_js_css() {
    wp_enqueue_style('lemur_slider_admin_css', LEMUR_SLIDER_URL.'assets/admin/admin.css');
    wp_enqueue_script('lemur_slider_admin_js', LEMUR_SLIDER_URL.'assets/admin/admin.js');
    wp_enqueue_style('lemur_slider_chosen_css', LEMUR_SLIDER_URL.'assets/admin/chosen/chosen.css');
    wp_enqueue_script('lemur_slider_chosen_js', LEMUR_SLIDER_URL.'assets/admin/chosen/chosen.jquery.min.js');
}
add_action('admin_init', 'lemur_slider_admin_js_css');


/**
 * Require Lemur Slider includes and classes
 */

require_once (LEMUR_SLIDER_DIR . '/inc/lemur-slider-meta.php');
require_once (LEMUR_SLIDER_DIR . '/inc/lemur-slider-admin.php');
require_once (LEMUR_SLIDER_DIR . '/inc/lemur-slider-shortcode.php');
require_once (LEMUR_SLIDER_DIR . '/inc/lemur-slider-widget.php');



/**
 * TODO: Replace this php function with one that, ya know, actually gets the slider and doesn't just do_shortcode
 */

//Get slider using php function
function lemur_slider($id = 0) {
	if ($id == 0) {
		$recent_slider = get_posts( array('posts_per_page' => 1, 'post_type' => 'lemur_slider') );
		$id = $recent_slider[0]->ID;
	}
	echo do_shortcode('[lemur_slider id="'.$id.'"]');
}
//Get slider using php function

//Slider shortcode button
add_action('init', 'lemur_slider_add_tinymce_button');
function lemur_slider_add_tinymce_button() {
	if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
		add_filter('mce_external_plugins', 'lemur_slider_add_tinymce_plugin');
		add_filter('mce_buttons', 'lemur_slider_register_tinymce_button');
	}
}

function lemur_slider_register_tinymce_button($buttons) {
   array_push($buttons, "lemur_slider");
   return $buttons;
}

function lemur_slider_add_tinymce_plugin($plugin_array) {
   $plugin_array['lemur_slider'] = LEMUR_SLIDER_URL.'assets/admin/tinymce.js';
   return $plugin_array;
}

//Ajax function for populating the select field, we don't want to make a query unnecessary, right?
add_action('wp_ajax_lemur_slider_load_sliders', 'lemur_slider_load_sliders_callback');
function lemur_slider_load_sliders_callback() {
	global $wpdb;

	$args = array(
		'post_type' => 'lemur_slider',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC'
	);
	$lemur_sliders = new WP_Query($args);
	
	$postsArray = array(); 
		
	while($lemur_sliders->have_posts()): $lemur_sliders->the_post();

		$posts = array(
			'id' => get_the_ID(),
			'title' => get_the_title()
		);
		array_push($postsArray,$posts);

	endwhile; wp_reset_query();
	
	$data = json_encode($postsArray);
	echo $data;

	die();
}
//Slider shortcode button

//Lemur slider settings
require_once (LEMUR_SLIDER_DIR . '/inc/lemur-slider-settings.php');