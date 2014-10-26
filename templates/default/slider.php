<?php
/**
 * Template Name: Default
 */

/**
 * TODO: replace some of template with actions that we can hook into rather than having so much markup in there that really shouldn't be messed with no matter the template styling/formatting.
 */

?>


<div class="lemur-slider animation-<?php echo $slider_animation; ?> <?php echo $slider_inside_control; ?> <?php echo $slider_orientation; ?> <?php echo $slider_image_scale; ?> <?php echo $slider_carousel_class; ?>" id="lemur-slider-<?php echo $id; ?>" data-autoplay="<?php echo $slider_autoplay; ?>" data-autoplay-delay="<?php echo $slider_autoplay_delay; ?>" style="height:<?php echo $slider_height; ?>" data-animation="<?php echo $slider_animation; ?>" data-autoheight="<?php echo $slider_autoheight; ?>" data-orientation="<?php echo $slider_orientation; ?>"<?php echo $slider_carousel_attr; ?> <?php echo $advanced_settings; ?> data-content_width="<?php global $content_width; echo $content_width; ?>">
	<ul class="slides">
		<?php foreach ($slide_data as $slide): ?>
			<?php if(!isset($slide['video'])) $slide['video'] = ''; ?>

			<?php if(!isset($slide['autoslide'])): ?>
				<li class="slide slide-<?php echo $slide_id; ?>">
					<?php
					if (is_numeric($slide['image'])) {
						$slide['image'] = wp_get_attachment_image_src($slide['image'],apply_filters('lemur-slider-image-size','full',$id));
						$slide['image'] = $slide['image'][0];
					}
					?>
					<?php if($slide['video']): ?>
						<div class="slide-image slide-image-video" style="background-image:url(<?php echo $slide['image']; ?>);height: <?php echo $slider_height; ?>;">
							<a href="" class="nemusicon-play start-video"></a>
						</div>
						<?php
						$image_url = parse_url($slide['video']);
						if (isset($image_url['host'])) {
							if($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com'){
								$array = explode("&", $image_url['query']);
								echo apply_filters('lemur-slider-youtube-embed-code','<iframe width="100%" style="height:'.$slider_height.';" src="http://www.youtube.com/embed/'.substr($array[0], 2).'?enablejsapi=1" frameborder="0" allowfullscreen class="youtubeplayer" id="youtubeplayer-'.$id.'-'.$slide_id.'"></iframe>',substr($array[0], 2),$id);
							} else if($image_url['host'] == 'www.vimeo.com' || $image_url['host'] == 'vimeo.com'){
								echo apply_filters('lemur-slider-vimeo-embed-code','<iframe src="http://player.vimeo.com/video/'.substr($image_url['path'], 1).'?badge=0&api=1&player_id=vimeoplayer-'.$id.'-'.$slide_id.'" class="vimeoplayer" id="vimeoplayer-'.$id.'-'.$slide_id.'" width="100%" style="height:'.$slider_height.';" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',substr($image_url['path'], 1),$id);
							}
						}
						?>
					<?php else: ?>
						<?php if($slider_autoheight): ?>
							<img src="<?php echo $slide['image']; ?>" />
						<?php else: ?>
							<div class="slide-image" style="background-image:url(<?php echo $slide['image']; ?>);height: <?php echo $slider_height; ?>;"></div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if(isset($slide['caption'])): ?>
						<?php if($slide['caption'] != ''): ?>
							<?php
							$position = 'tl';
							if (isset($slide['caption_position'])) {
								if ($slide['caption_position'] != '') $position = $slide['caption_position'];
							}
							?>
							<div class="caption <?php echo $position; ?> anim-<?php if (isset($slide['animation_position'])) echo $slide['animation_position']; ?>">
								<?php do_action( 'lemur-slider-before-caption', $id, $slide_id,0 ); ?>
								<?php echo apply_filters('lemur-slider-caption-text',do_shortcode($slide['caption']),$id,$slide_id); ?>
								<?php do_action( 'lemur-slider-after-caption', $id, $slide_id,0 ); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if(isset($slide['link'])): ?>
						<?php if($slide['link'] != ''): ?>
							<?php if ($slide['link_target']) $slide['link_target'] = 'target="_blank"'; ?>
							<a href="<?php echo $slide['link']; ?>" <?php echo $slide['link_target']; ?> class="slide-link"></a>
						<?php endif; ?>
					<?php endif; ?>
				</li>
			<?php else: ?>


				<?php
				//If this is an automated posts slider
				if ($slide['auto_slide']['type'] == '' || $slide['auto_slide']['type'] == 'posts'):

					if( $slide['auto_slide']['select_posts'] != '1' ) {
						// when Select Posts is unchecked, use the args below
						$args = array(
							'post_type' => $slide['auto_slide']['post_type'],
							'posts_per_page' => $slide['auto_slide']['limit'],
							'cat' => $slide['auto_slide']['category'],
							'orderby' => $slide['auto_slide']['orderby'],
							'order' => $slide['auto_slide']['order']
						);
					} else {
						// if Select Posts is checked, we'll add the post__in argument to include the values from the Include Posts text box
						// we'll also set $include_posts and convert it to an array
						$include_posts = $slide['auto_slide']['include_posts'];
						$include_posts = explode(',', $include_posts);

						$args = array(
							'post_type'         => $slide['auto_slide']['post_type'],
							'posts_per_page'    => -1,
							'post__in'          =>  $include_posts,
							'orderby'           => $slide['auto_slide']['orderby'],
							'order'             => $slide['auto_slide']['order']
						);
					}
					$auto_slides = new WP_Query(apply_filters( 'lemur-slider-auto-slide-query', $args, $id ));
					?>
					<?php while($auto_slides->have_posts()): $auto_slides->the_post(); ?>
						<?php

						/**
						 * TODO: add better handling within the template for posts without an attached image. This if statement is annoying me.
						 */
						if( has_post_thumbnail() ) {
							$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), apply_filters('lemur-slider-image-size','full',$id));
							$thumb = $thumb[0];
							$thumb_stylebackground = 'url(' . $thumb . ')';
						} else {
							$thumb = $slider_color;     // the color is set in the slider options meta. We're just going to set it as the background for all slides that don't have an image for the time being
							$thumb_stylebackground = $thumb;
						}

						?>
						<li class="slide slide-<?php echo $slide_id; ?> autoslide-<?php the_ID(); ?>">
							<?php



							if($slider_autoheight && has_post_thumbnail()) { ?>
								<img src="<?php echo $thumb; ?>" />
							<?php } else {

						        echo '<div class="slide-image" style="background:' . $thumb_stylebackground . ';height: ' . $slider_height . '"></div>';

							} // end if($slider_autoheight && has_post_thumbnail())


							$position = 'tl';
							if (isset($slide['caption_position'])) {
								if ($slide['caption_position'] != '') $position = $slide['caption_position'];
							}
							?>
							<div class="caption <?php echo $position; ?> anim-<?php if (isset($slide['animation_position'])) echo $slide['animation_position']; ?>">
								<h1><?php the_title(); ?></h1>
								<?php do_action( 'lemur-slider-before-autoslide-caption', $id, $slide_id, get_the_ID() ); ?>
								<?php echo apply_filters('lemur-slider-autoslide-caption','<p>'.get_the_excerpt().'</p>', $id, $slide_id, get_the_ID()); ?>
								<?php do_action( 'lemur-slider-after-autoslide-caption', $id, $slide_id, get_the_ID() ); ?>
							</div>
							<?php echo apply_filters('lemur-slider-autoslide-link','<a href="'.get_permalink().'" class="slide-link"></a>', $id, $slide_id, get_the_ID()); ?>
						</li>
						<?php $slide_id++; endwhile; wp_reset_query(); ?>
				<?php endif; ?>

				<?php
				//If flickr posts
				if ($slide['auto_slide']['type'] == 'flickr'): ?>
					<?php if (get_option('lemur_slider_flickr_api_key') != '' || $slide['auto_slide']['flickr']['user_id'] != ''): ?>
						<li class="slide slide-<?php echo $slide_id; ?> flickr-slides" data-api="<?php echo get_option('lemur_slider_flickr_api_key'); ?>" data-setid="<?php echo $slide['auto_slide']['flickr']['set_id']; ?>" data-caption="<?php echo $slide['auto_slide']['flickr']['caption_position']; ?>" data-caption-animation="<?php echo $slide['auto_slide']['flickr']['animation_position']; ?>" data-linkto="<?php echo $slide['auto_slide']['flickr']['linkto']; ?>" data-limit="<?php echo $slide['auto_slide']['flickr']['limit']; ?>" data-userid="<?php echo $slide['auto_slide']['flickr']['user_id']; ?>"></li>
					<?php endif; ?>
				<?php endif; ?>

				<?php
				//If instagram posts
				if ($slide['auto_slide']['type'] == 'instagram'): ?>
					<li class="slide slide-<?php echo $slide_id; ?> instagram-slides" data-client_id="<?php echo get_option('lemur_slider_instagram_client_id'); ?>" data-access_token="<?php echo get_option('lemur_slider_instagram_token'); ?>" data-caption="<?php echo $slide['auto_slide']['instagram']['caption_position']; ?>" data-caption-animation="<?php echo $slide['auto_slide']['instagram']['animation_position']; ?>" data-user_id="<?php echo get_option('lemur_slider_instagram_user_id'); ?>" data-limit="<?php echo $slide['auto_slide']['instagram']['limit']; ?>" data-hash="<?php echo $slide['auto_slide']['instagram']['hash']; ?>" data-linkto="<?php echo $slide['auto_slide']['instagram']['linkto']; ?>"></li>
				<?php endif; ?>

				<?php
				//If attached posts
				if ($slide['auto_slide']['type'] == 'attached'): ?>
					<?php

					$att_args = array(
						'post_type'	     => 'attachment',
						'numberposts'    => -1,
						'post_status'    => null,
						'post_parent'    => $post->ID,
						'post_mime_type' => 'image',
						'orderby'        => 'menu_order'
					);
					$attachments = get_posts(apply_filters( 'lemur-slider-auto-slide-attached-query', $att_args, $id ));

					if( $attachments ): ?>

						<?php foreach( $attachments as $attachment ): ?>
							<?php $attachment_img = wp_get_attachment_image_src( $attachment->ID ,'full'); ?>
							<li class="slide slide-<?php echo $slide_id; ?> attachment-<?php echo $attachment->ID; ?>">
								<?php if($slider_autoheight): ?>
									<img src="<?php echo $attachment_img[0]; ?>" />
								<?php else: ?>
									<div class="slide-image" style="background-image:url(<?php echo $attachment_img[0]; ?>);height: <?php echo $slider_height; ?>;"></div>
								<?php endif; ?>

								<?php if($slide['auto_slide']['attached']['caption'] != ''): ?>
									<?php
									$position = 'tl';
									if (isset($slide['auto_slide']['attached']['caption_position'])) {
										if ($slide['auto_slide']['attached']['caption_position'] != '') $position = $slide['auto_slide']['attached']['caption_position'];
									}
									?>
									<div class="caption <?php echo $position; ?> anim-<?php if (isset($slide['auto_slide']['attached']['animation_position'])) echo $slide['auto_slide']['attached']['animation_position']; ?>">
										<?php if($slide['auto_slide']['attached']['caption'] == 'caption'): ?>
											<p><?php echo $attachment->post_excerpt; ?></p>
										<?php endif; ?>

										<?php if($slide['auto_slide']['attached']['caption'] == 'title'): ?>
											<h1><?php echo $attachment->post_title; ?></h1>
										<?php endif; ?>

										<?php if($slide['auto_slide']['attached']['caption'] == 'title_caption'): ?>
											<h1><?php echo $attachment->post_title; ?></h1>
											<p><?php echo $attachment->post_excerpt; ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<?php if($slide['auto_slide']['attached']['linkto'] == 'post'): ?>
									<?php echo apply_filters('lemur-slider-autoslide-attached-link','<a href="'.get_permalink($attachment->ID).'" class="slide-link"></a>', $id, $slide_id, $attachment->ID); ?>
								<?php endif; ?>
								<?php if($slide['auto_slide']['attached']['linkto'] == 'image'): ?>
									<?php echo apply_filters('lemur-slider-autoslide-attached-link','<a href="'.$attachment_img[0].'" class="slide-link"></a>', $id, $slide_id, $attachment->ID); ?>
								<?php endif; ?>
							</li>
							<?php $slide_id++; endforeach; ?>

					<?php endif; ?>

				<?php endif; ?>

			<?php endif; ?>
			<?php $slide_id++; endforeach; ?>
	</ul>
</div>