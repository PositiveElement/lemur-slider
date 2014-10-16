<?php
/**
 * Lemur Slider Shortcode
 */


/**
 * TODO: extend shortcode to include template option
 * TODO: call the slider with a function, not directly with a shortcode... do_shortcode isn't any fun
 * TODO: Handle the templating options more elegantly
 */

//Slider shortcode, this is where the magic happens!
add_shortcode( 'lemur_slider', 'lemur_slider_shortcode' );
function lemur_slider_shortcode( $atts, $content ){
	extract(shortcode_atts(array(
		'id' => '0'
	), $atts));

	ob_start();

	global $post;

	//If nothing is specified, get the latest slider
	if ($id == 0) {
		$recent_slider = get_posts( array('posts_per_page' => 1, 'post_type' => 'lemur_slider') );
		$id = $recent_slider[0]->ID;
	}

	//Get the slider data
	$custom = get_post_custom($id);
	$slide_data = '';
	if (isset($custom['slide_data'][0])) $slide_data = $custom['slide_data'][0];
	$slide_data = apply_filters('lemur-slider-slide-data',unserialize($slide_data),$id);

	//Stop if we don't have at least one slide
	if (!$slide_data || get_post_status ( $id ) == 'trash' ) return;

	//Get global slider options
	$slider_options = '';
	if (isset($custom['slider_options'][0])) $slider_options = $custom['slider_options'][0];
	$slider_options = apply_filters('lemur-slider-options',unserialize($slider_options),$id);

	$slider_template = 'default'; if (isset($slider_options['template'])) $slider_template = $slider_options['template'];
	$slider_autoplay = false; if (isset($slider_options['autoplay'])) $slider_autoplay = $slider_options['autoplay'];
	$slider_autoplay_delay = '3000'; if (isset($slider_options['autoplay_delay']) && $slider_options['autoplay_delay'] !='') $slider_autoplay_delay = $slider_options['autoplay_delay'];
	$slider_autoheight = false; if (isset($slider_options['autoheight'])) $slider_autoheight = $slider_options['autoheight'];
	$slider_height = '500px'; if (isset($slider_options['height']) && $slider_options['height'] != '') $slider_height = $slider_options['height'];
	$slider_animation = 'fade'; if (isset($slider_options['animation'])) $slider_animation = $slider_options['animation'];
	if ($slider_autoheight) $slider_height = 'auto';
	$slider_inside_control = 'controls-outside'; if (isset($slider_options['control_position'])) $slider_inside_control = 'controls-inside';
	$slide_id = 1;
	$slider_orientation = 'horizontal'; if (isset($slider_options['orientation'])) $slider_orientation = $slider_options['orientation'];
	$slider_image_scale = 'fill'; if (isset($slider_options['image_scale'])) $slider_image_scale = $slider_options['image_scale'];

	//Change the color if neccessary
	$slider_color = false; if (isset($slider_options['color'])) $slider_color = $slider_options['color'];
	$slider_color_css = apply_filters('lemur-slider-color-css','#lemur-slider-'.$id.' .lemur-direction-nav a {color:'.$slider_color.';} #lemur-slider-'.$id.' .lemur-control-paging li a {background:'.$slider_color.';background:rgba('.implode(',',lemur_slider_hex2rgb($slider_color)).',0.1);border-color:'.$slider_color.';border-color:rgba('.implode(',',lemur_slider_hex2rgb($slider_color)).',0.5);}#lemur-slider-'.$id.' .lemur-control-paging li a:hover,#lemur-slider-'.$id.' .lemur-control-paging li a.lemur-active{background:'.$slider_color.';background:rgba('.implode(',',lemur_slider_hex2rgb($slider_color)).',0.7);}',$slider_color,$id);

	//Carousel CSS if neccessary
	$slider_carousel_class = '';
	$slider_carousel = false; if (isset($slider_options['carousel'])) $slider_carousel = $slider_options['carousel'];
	$slider_carousel_width = '250'; if (isset($slider_options['carousel_width'])) $slider_carousel_width = $slider_options['carousel_width'];
	$slider_carousel_margin = '5'; if (isset($slider_options['carousel_margin'])) $slider_carousel_margin = $slider_options['carousel_margin'];
	$slider_carousel_css = apply_filters('lemur-slider-carousel-css','#lemur-slider-'.$id.' .slides > li.slide {margin-right:'.$slider_carousel_margin.'px}',$id);
	$slider_carousel_attr = '';
	if ($slider_carousel) {
		$slider_carousel_attr = ' data-carousel="true" data-carousel-width="'.$slider_carousel_width.'" data-carousel-margin="'.$slider_carousel_margin.'"';
		$slider_carousel_class = 'carousel';
		$slider_orientation = 'horizontal';
	}

	//Advanced settings
	$advanced_settings = array();
	if (isset($slider_options['reverse']) && $slider_options['reverse'] != '') $advanced_settings[] = 'reverse:'.$slider_options['reverse'];
	if (isset($slider_options['animationLoop']) && $slider_options['animationLoop'] != '') $advanced_settings[] = 'animationLoop:'.$slider_options['animationLoop'];
	if (isset($slider_options['startAt']) && $slider_options['startAt'] != '') $advanced_settings[] = 'startAt:'.$slider_options['startAt'];
	if (isset($slider_options['animationSpeed']) && $slider_options['animationSpeed'] != '') $advanced_settings[] = 'animationSpeed:'.$slider_options['animationSpeed'];
	if (isset($slider_options['randomize']) && $slider_options['randomize'] != '') $advanced_settings[] = 'randomize:'.$slider_options['randomize'];
	if (isset($slider_options['controlNav']) && $slider_options['controlNav'] != '') $advanced_settings[] = 'controlNav:'.$slider_options['controlNav'];
	if (isset($slider_options['directionNav']) && $slider_options['directionNav'] != '') $advanced_settings[] = 'directionNav:'.$slider_options['directionNav'];

	$advanced_settings = 'data-advanced="'.implode (',',$advanced_settings).'"';



	do_action( 'lemur-slider-before', $id );

	/**
	 * TODO: build function to get the view/template -> cycloneslider has this functionality, check public function get_view_file( $template_name ) in class-cyclone-slider.php for an example of how it's done
	 */

	/**
	 * Lemur Slider Default Template
	 *
	 * So yeah... this is a little bit of a ghetto option, but basically we're going to check if the file lemurslider/{template-slug}/slider.php exists in the current theme. If it's there, we'll use that. If it's not, we'll fall back on the plugin's default/slider.php
	 */

	if( file_exists(realpath(get_stylesheet_directory()).DIRECTORY_SEPARATOR.'lemurslider'.DIRECTORY_SEPARATOR.$slider_template.DIRECTORY_SEPARATOR.'slider.php')) {
		require_once (get_stylesheet_directory() . '/lemurslider/' . $slider_template . '/slider.php');
	} else {
		require_once (LEMUR_SLIDER_DIR . '/templates/default/slider.php');      // don't bother using the $slider_template meta, because the only template packaged with the plugin is default :)
	}




	do_action( 'lemur-slider-after', $id );

	$slider = ob_get_contents();
	ob_end_clean();

	wp_enqueue_style('lemur-slider-css', LEMUR_SLIDER_URL.'assets/frontend/lemur-slider.css');
	wp_enqueue_script('lemur-slider-js', LEMUR_SLIDER_URL.'assets/frontend/lemur-slider.js',array( 'jquery' ));
	if ($slider_color != '') wp_add_inline_style( 'lemur-slider-css', $slider_color_css );
	if ($slider_carousel != '') wp_add_inline_style( 'lemur-slider-css', $slider_carousel_css);

	if (file_exists(get_stylesheet_directory() . '/lemur-slider.css')) {
		wp_enqueue_style( 'lemur-slider-custom-css', get_template_directory_uri() . '/lemur-slider.css' );
	}

	return $slider;
} // end Lemur Slider shortcode