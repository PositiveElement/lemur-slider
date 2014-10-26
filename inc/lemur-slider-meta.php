<?php
/**
 * All of the custom post meta for Lemur Slider
 */


/**
 * TODO: after slider templating has been built, build select meta to select alternate templates
 */


//Lemur Slider custom metabox
add_action('admin_init', 'register_lemur_slider_slide_box');
add_action('save_post', 'save_lemur_slider_slide_box',10,2);

function register_lemur_slider_slide_box() {
	add_meta_box('lemur_slider_slides', __('Slider Options','lemur_slider'), 'lemur_slider_slide_box_callback', 'lemur_slider', 'normal', 'low');
}

function lemur_slider_slide_box_callback() {
	global $post;
	$custom = get_post_custom($post->ID);
	$slide_data = '';
	if (isset($custom['slide_data'][0])) $slide_data = $custom['slide_data'][0];

	wp_nonce_field( basename( __FILE__ ), 'lemur_slider_slide_nonce' );

	if ( ! did_action( 'wp_enqueue_media' ) ) wp_enqueue_media();

	?>

	<div id="slides" style="display:none;">
	<?php if($slide_data): $slide_data = unserialize($slide_data); foreach($slide_data as $key => $slide): ?>
		<?php if(!isset($slide['autoslide'])) {

			/**
			 * Manage Single Slide
			 */

			?>
			<div class="slide">
				<header>
					<span><em>#<?php echo $key+1; ?></em> <?php _e('slide','lemur_slider'); ?></span>
					<nav>
						<a href="#" class="nemusicon-docs duplicate"><?php _e('duplicate','lemur_slider'); ?></a>
						<a href="#" class="nemusicon-minus-circled delete"><?php _e('delete','lemur_slider'); ?></a>
					</nav>
				</header>
				<div class="body">
					<div class="left">
						<?php
						$bg = '';
						if ($slide['image']!='') {
							$image = $slide['image'];
							if (is_numeric($image)) $image = wp_get_attachment_url($slide['image']);
							$bg = 'style="background-image:url('.$image.')"';
						}
						?>
						<div class="image<?php if($slide['image']!=''):?> hasimg<?php endif; ?>" <?php echo $bg; ?>>
							<a href="#" class="nemusicon-upload-cloud upload"><?php _e('Upload Image','lemur_slider'); ?></a>
							<a href="#" class="upload_video <?php if($slide['video']): ?>selected nemusicon-cancel-circled<?php endif; ?>" title="<?php _e('Youtube or Vimeo link','lemur_slider'); ?>">
								<?php if($slide['video']): ?>
									<?php _e('Video selected','lemur_slider'); ?>
								<?php else: ?>
									<?php _e('or video','lemur_slider'); ?>
								<?php endif; ?>
							</a>
							<input type="text" class="image-link" value="<?php echo $slide['image']; ?>" name="slide_data[<?php echo $key; ?>][image]" />
							<input type="text" class="video-link" value="<?php echo $slide['video']; ?>" name="slide_data[<?php echo $key; ?>][video]" />
						</div>
					</div>
					<div class="right">
						<div class="row caption">
							<div class="field">
								<label><?php _e('Image Caption','lemur_slider'); ?> <em><?php _e('you can use html code','lemur_slider'); ?></em></label>
								<textarea class="caption" name="slide_data[<?php echo $key; ?>][caption]"><?php echo $slide['caption']; ?></textarea>
							</div>
							<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
								<a href="tl" <?php if($slide['caption_position']=='caption tl' || $slide['caption_position']==''):?>class="active"<?php endif; ?> title="<?php _e('Top Left','lemur_slider'); ?>"></a>
								<a href="tc" <?php if($slide['caption_position']=='caption tc'):?>class="active"<?php endif; ?> title="<?php _e('Top Center','lemur_slider'); ?>"></a>
								<a href="tr" <?php if($slide['caption_position']=='caption tr'):?>class="active"<?php endif; ?> title="<?php _e('Top Right','lemur_slider'); ?>"></a>
								<a href="cl" <?php if($slide['caption_position']=='caption cl'):?>class="active"<?php endif; ?> title="<?php _e('Center Left','lemur_slider'); ?>"></a>
								<a href="cc" <?php if($slide['caption_position']=='caption cc'):?>class="active"<?php endif; ?> title="<?php _e('Center','lemur_slider'); ?>"></a>
								<a href="cr" <?php if($slide['caption_position']=='caption cr'):?>class="active"<?php endif; ?> title="<?php _e('Center Right','lemur_slider'); ?>"></a>
								<a href="bl" <?php if($slide['caption_position']=='caption bl'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
								<a href="bc" <?php if($slide['caption_position']=='caption bc'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
								<a href="br" <?php if($slide['caption_position']=='caption br'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
								<input type="hidden" class="caption_position" name="slide_data[<?php echo $key; ?>][caption_position]" value="<?php echo $slide['caption_position']; ?>" />
							</span>
							<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
								<a href="<?php echo $slide['animation_position']; ?>" class="nemusicon-down"></a>
								<input type="hidden" class="animation_position" name="slide_data[<?php echo $key; ?>][animation_position]" value="<?php echo $slide['animation_position']; ?>" />
							</span>
						</div>
						<div class="row">
							<div class="field">
								<label><?php _e('Image Link','lemur_slider'); ?> <em>e.g. http://google.com</em></label>
								<input class="slide-link" name="slide_data[<?php echo $key; ?>][link]" type="text" value="<?php echo $slide['link']; ?>" />
							</div>
							<a href="#" class="nemusicon-popup link_target" title="<?php _e('Open in new window','lemur_slider'); ?>"></a>
							<input class="link_target" type="hidden" name="slide_data[<?php echo $key; ?>][link_target]" value="<?php echo $slide['link_target']; ?>" />
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		<?php } else {
			/**
			 * Manage Automated Slide
			 * (it's existing/previously saved)
			 */
			?>
			<div class="slide slide-auto">
			<header>
				<span class="nemusicon-rocket"><?php _e('Automated slides','lemur_slider'); ?></span>
				<nav>
					<a href="#" class="nemusicon-docs duplicate"><?php _e('duplicate','lemur_slider'); ?></a>
					<a href="#" class="nemusicon-minus-circled delete"><?php _e('delete','lemur_slider'); ?></a>
				</nav>
			</header>
			<div class="body">
			<div class="auto-slide-type">
						<span>
							<a href="#autoslide-tab-<?php echo $key; ?>-posts" data-type="posts" class="nemusicon-pin<?php if($slide['auto_slide']['type'] == '' || $slide['auto_slide']['type'] == 'posts'): ?> active<?php endif; ?>"><?php _e('Posts','lemur_slider'); ?></a>
							<a href="#autoslide-tab-<?php echo $key; ?>-flickr" data-type="flickr" class="nemusicon-flickr-circled<?php if($slide['auto_slide']['type'] == 'flickr'): ?> active<?php endif; ?>"><?php _e('Flickr','lemur_slider'); ?></a>
							<a href="#autoslide-tab-<?php echo $key; ?>-instagram" data-type="instagram" class="nemusicon-instagram<?php if($slide['auto_slide']['type'] == 'instagram'): ?> active<?php endif; ?>"><?php _e('Instagram','lemur_slider'); ?></a>
							<a href="#autoslide-tab-<?php echo $key; ?>-attached" data-type="attached" class="nemusicon-attach<?php if($slide['auto_slide']['type'] == 'attached'): ?> active<?php endif; ?>"><?php _e('Attached Photos','lemur_slider'); ?></a>
						</span>
				<input type="hidden" class="field-autoslide_type" name="slide_data[<?php echo $key; ?>][auto_slide][type]" value="<?php echo $slide['auto_slide']['type']; ?>" />
			</div>

			<div class="auto-slide-type-tab"<?php if($slide['auto_slide']['type'] == '' || $slide['auto_slide']['type'] == 'posts'): ?> style="display:block;"<?php endif; ?>>
				<div class="auto-slide-form">
					<p>
						<label><?php _e('Post type','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][post_type]" class="post_type lemur-chosen">
							<?php
							$post_types=get_post_types(array('public' => true),'objects');
							foreach ($post_types as $post_type ):
								?>
								<option value="<?php echo $post_type->name; ?>"<?php if($slide['auto_slide']['post_type']==$post_type->name):?> selected="selected"<?php endif; ?>>
									<?php echo $post_type->label; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
					<p>
						<label><?php _e('Category','lemur_slider'); ?></label>
						<?php wp_dropdown_categories(array(
							'show_count' => true,
							'name' => 'slide_data['.$key.'][auto_slide][category]',
							'hierarchical' => true,
							'show_option_all' => __('From all category','lemur_slider'),
							'selected' => $slide['auto_slide']['category'],
							'class' => 'lemur-chosen'
						)); ?>
					</p>

					<?php
					/**
					 * Add checkbox to allow the user to enter post ID's instead of selecting a category
					 * TODO: Hide Limit box if Select Posts is checked, and use -1 in the query for posts_per_page
					 * TODO: Set the value as 0 if the checkbox is not checked and 1 if it is checked. Getting a PHP notice when the box isn't checked: "Notice: Undefined index: select_posts" on line 75 of templates/default/slider.php, I believe because there's just no value there (or because it's empty). It still functions fine, but notices and warnings are annoying!
					 */
					?>
					<p>
						<label><?php _e('Select Posts','lemur_slider'); ?></label>
						<input type="checkbox" value="1" name="slide_data[<?php echo $key; ?>][auto_slide][select_posts]" <?php if($slide['auto_slide']['select_posts']=='1'):?>checked="checked"<?php endif; ?>  />

						<label><?php _e('Include Posts','lemur_slider'); ?></label>
						<span class="description">Enter comma-separated list of post ID's. IE 18,2,34,5</span>
						<input type="text" value="<?php echo $slide['auto_slide']['include_posts']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][include_posts]" class="include_posts" />
					</p>


					<p>
						<label><?php _e('Order by','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][orderby]" class="orderby lemur-chosen">
							<option value="title"<?php if($slide['auto_slide']['orderby']=='title'):?> selected="selected"<?php endif; ?>><?php _e('Name','lemur_slider'); ?></option>
							<option value="date"<?php if($slide['auto_slide']['orderby']=='date'):?> selected="selected"<?php endif; ?>><?php _e('Date','lemur_slider'); ?></option>
							<option value="comment_count"<?php if($slide['auto_slide']['orderby']=='comment_count'):?> selected="selected"<?php endif; ?>><?php _e('Comments','lemur_slider'); ?></option>
							<option value="rand"<?php if($slide['auto_slide']['orderby']=='rand'):?> selected="selected"<?php endif; ?>><?php _e('Random','lemur_slider'); ?></option>
							<option value="post__in"<?php if($slide['auto_slide']['orderby']=='post__in'):?> selected="selected"<?php endif; ?>><?php _e('Order Entered','lemur_slider'); ?></option>
						</select>
					</p>
					<p>
						<label><?php _e('Order','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][order]" class="order lemur-chosen">
							<option value="ASC"<?php if($slide['auto_slide']['order']=='ASC'):?> selected="selected"<?php endif; ?>><?php _e('Ascending','lemur_slider'); ?></option>
							<option value="DESC"<?php if($slide['auto_slide']['order']=='DESC'):?> selected="selected"<?php endif; ?>><?php _e('Descending','lemur_slider'); ?></option>
						</select>
					</p>
					<p class="half">
						<label><?php _e('Limit','lemur_slider'); ?></label>
						<input type="number" value="<?php echo $slide['auto_slide']['limit']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][limit]" class="limit" />
					</p>
					<p class="half">
						<label><?php _e('Caption','lemur_slider'); ?></label>
								<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
									<a href="tl" <?php if($slide['caption_position']=='caption tl' || $slide['caption_position']==''):?>class="active"<?php endif; ?> title="<?php _e('Top Left','lemur_slider'); ?>"></a>
									<a href="tc" <?php if($slide['caption_position']=='caption tc'):?>class="active"<?php endif; ?> title="<?php _e('Top Center','lemur_slider'); ?>"></a>
									<a href="tr" <?php if($slide['caption_position']=='caption tr'):?>class="active"<?php endif; ?> title="<?php _e('Top Right','lemur_slider'); ?>"></a>
									<a href="cl" <?php if($slide['caption_position']=='caption cl'):?>class="active"<?php endif; ?> title="<?php _e('Center Left','lemur_slider'); ?>"></a>
									<a href="cc" <?php if($slide['caption_position']=='caption cc'):?>class="active"<?php endif; ?> title="<?php _e('Center','lemur_slider'); ?>"></a>
									<a href="cr" <?php if($slide['caption_position']=='caption cr'):?>class="active"<?php endif; ?> title="<?php _e('Center Right','lemur_slider'); ?>"></a>
									<a href="bl" <?php if($slide['caption_position']=='caption bl'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
									<a href="bc" <?php if($slide['caption_position']=='caption bc'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
									<a href="br" <?php if($slide['caption_position']=='caption br'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
									<input type="hidden" class="caption_position" name="slide_data[<?php echo $key; ?>][caption_position]" value="<?php echo $slide['caption_position']; ?>" />
								</span>
								<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
									<a href="<?php echo $slide['animation_position']; ?>" class="nemusicon-down"></a>
									<input type="hidden" class="animation_position" name="slide_data[<?php echo $key; ?>][animation_position]" value="<?php echo $slide['animation_position']; ?>" />
								</span>
					</p>
				</div>
				<input type="hidden" name="slide_data[<?php echo $key; ?>][autoslide]" class="autoslide" value="1" />
				<div class="clear"></div>
			</div>

			<div class="auto-slide-type-tab"<?php if($slide['auto_slide']['type'] == 'flickr'): ?> style="display:block;"<?php endif; ?>>
				<div class="auto-slide-form flickr">
					<p>
						<label><?php _e('Set ID','lemur_slider'); ?></label>
						<input type="text" value="<?php echo $slide['auto_slide']['flickr']['set_id']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][flickr][set_id]" class="field-flickr-setid" />
						<small>Go to your Flickr account and click on one of your sets. In the end of URL you'll see a number, this is the set id. Example: 72157625956932639</small>
					</p>
					<p>
						<label><?php _e('User ID','lemur_slider'); ?></label>
						<input type="text" value="<?php echo $slide['auto_slide']['flickr']['user_id']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][flickr][user_id]" class="field-flickr-user_id" />
						<small>You can use the public Flickr api to get photos from users without an api key</small>
					</p>
					<p>
						<label><?php _e('Link the slides to','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][flickr][linkto]" class="order lemur-chosen field-flickr-linkto">
							<option value=""<?php if($slide['auto_slide']['flickr']['linkto']==''):?> selected="selected"<?php endif; ?>><?php _e('No link','lemur_slider'); ?></option>
							<option value="image"<?php if($slide['auto_slide']['flickr']['linkto']=='image'):?> selected="selected"<?php endif; ?>><?php _e('Link to image','lemur_slider'); ?></option>
							<option value="flickr"<?php if($slide['auto_slide']['flickr']['linkto']=='flickr'):?> selected="selected"<?php endif; ?>><?php _e('Link to flickr','lemur_slider'); ?></option>
						</select>
					</p>
					<p class="half">
						<label><?php _e('Limit','lemur_slider'); ?></label>
						<input type="number" value="<?php echo $slide['auto_slide']['flickr']['limit']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][flickr][limit]" class="field-flickr-limit" />
					</p>
					<p class="half">
						<label><?php _e('Caption','lemur_slider'); ?></label>
								<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
									<a href="tl" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption tl'):?>class="active"<?php endif; ?> title="<?php _e('Top Left','lemur_slider'); ?>"></a>
									<a href="tc" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption tc'):?>class="active"<?php endif; ?> title="<?php _e('Top Center','lemur_slider'); ?>"></a>
									<a href="tr" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption tr'):?>class="active"<?php endif; ?> title="<?php _e('Top Right','lemur_slider'); ?>"></a>
									<a href="cl" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption cl'):?>class="active"<?php endif; ?> title="<?php _e('Center Left','lemur_slider'); ?>"></a>
									<a href="cc" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption cc'):?>class="active"<?php endif; ?> title="<?php _e('Center','lemur_slider'); ?>"></a>
									<a href="cr" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption cr'):?>class="active"<?php endif; ?> title="<?php _e('Center Right','lemur_slider'); ?>"></a>
									<a href="bl" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption bl'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
									<a href="bc" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption bc'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
									<a href="br" <?php if($slide['auto_slide']['flickr']['caption_position']=='caption br'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
									<input type="hidden" class="field-flickr-caption_position" name="slide_data[<?php echo $key; ?>][auto_slide][flickr][caption_position]" value="<?php echo $slide['auto_slide']['flickr']['caption_position']; ?>" />
								</span>
								<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
									<a href="<?php echo $slide['auto_slide']['flickr']['animation_position']; ?>" class="nemusicon-down"></a>
									<input type="hidden" class="field-flickr-animation_position" name="slide_data[<?php echo $key; ?>][auto_slide][flickr][animation_position]" value="<?php echo $slide['auto_slide']['flickr']['animation_position']; ?>" />
								</span>
					</p>
					<div class="clear"></div>
				</div>
			</div>
			<div class="auto-slide-type-tab"<?php if($slide['auto_slide']['type'] == 'instagram'): ?> style="display:block;"<?php endif; ?>>
				<div class="auto-slide-form instagram">
					<p>
						<label><?php _e('Hash','lemur_slider'); ?></label>
						<input type="text" class="field-instagram-hash" value="<?php echo $slide['auto_slide']['instagram']['hash']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][instagram][hash]" />
						<small>Get a list of recently tagged media.</small>
					</p>
					<p class="half">
						<label><?php _e('Limit','lemur_slider'); ?></label>
						<input type="number" value="<?php echo $slide['auto_slide']['instagram']['limit']; ?>" name="slide_data[<?php echo $key; ?>][auto_slide][instagram][limit]" class="field-instagram-limit" />
					</p>
					<p>
						<label><?php _e('Link the slides to','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][instagram][linkto]" class="order lemur-chosen field-instagram-linkto">
							<option value=""<?php if($slide['auto_slide']['instagram']['linkto']==''):?> selected="selected"<?php endif; ?>><?php _e('No link','lemur_slider'); ?></option>
							<option value="image"<?php if($slide['auto_slide']['instagram']['linkto']=='image'):?> selected="selected"<?php endif; ?>><?php _e('Link to image','lemur_slider'); ?></option>
							<option value="instagram"<?php if($slide['auto_slide']['instagram']['linkto']=='instagram'):?> selected="selected"<?php endif; ?>><?php _e('Link to instagram','lemur_slider'); ?></option>
						</select>
					</p>
					<p class="half">
						<label><?php _e('Caption','lemur_slider'); ?></label>
								<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
									<a href="tl" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption tl'):?>class="active"<?php endif; ?> title="<?php _e('Top Left','lemur_slider'); ?>"></a>
									<a href="tc" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption tc'):?>class="active"<?php endif; ?> title="<?php _e('Top Center','lemur_slider'); ?>"></a>
									<a href="tr" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption tr'):?>class="active"<?php endif; ?> title="<?php _e('Top Right','lemur_slider'); ?>"></a>
									<a href="cl" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption cl'):?>class="active"<?php endif; ?> title="<?php _e('Center Left','lemur_slider'); ?>"></a>
									<a href="cc" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption cc'):?>class="active"<?php endif; ?> title="<?php _e('Center','lemur_slider'); ?>"></a>
									<a href="cr" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption cr'):?>class="active"<?php endif; ?> title="<?php _e('Center Right','lemur_slider'); ?>"></a>
									<a href="bl" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption bl'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
									<a href="bc" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption bc'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
									<a href="br" <?php if($slide['auto_slide']['instagram']['caption_position']=='caption br'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
									<input type="hidden" class="field-instagram-caption_position" name="slide_data[<?php echo $key; ?>][auto_slide][instagram][caption_position]" value="<?php echo $slide['auto_slide']['instagram']['caption_position']; ?>" />
								</span>
								<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
									<a href="<?php echo $slide['auto_slide']['instagram']['animation_position']; ?>" class="nemusicon-down"></a>
									<input type="hidden" class="field-instagram-animation_position" name="slide_data[<?php echo $key; ?>][auto_slide][instagram][animation_position]" value="<?php echo $slide['auto_slide']['instagram']['animation_position']; ?>" />
								</span>
					</p>
					<div class="clear"></div>
				</div>
			</div>
			<div class="auto-slide-type-tab"<?php if($slide['auto_slide']['type'] == 'attached'): ?> style="display:block;"<?php endif; ?>>
				<div class="auto-slide-form attached">
					<p>
						<label><?php _e('Link the slides to','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][attached][linkto]" class="order lemur-chosen field-attached-linkto">
							<option value=""<?php if($slide['auto_slide']['attached']['linkto']==''):?> selected="selected"<?php endif; ?>><?php _e('No link','lemur_slider'); ?></option>
							<option value="image"<?php if($slide['auto_slide']['attached']['linkto']=='image'):?> selected="selected"<?php endif; ?>><?php _e('Link to image','lemur_slider'); ?></option>
							<option value="post"<?php if($slide['auto_slide']['attached']['linkto']=='post'):?> selected="selected"<?php endif; ?>><?php _e('Link to post','lemur_slider'); ?></option>
						</select>
					</p>
					<p>
						<label><?php _e('Caption','lemur_slider'); ?></label>
						<select name="slide_data[<?php echo $key; ?>][auto_slide][attached][caption]" class="order lemur-chosen field-attached-caption">
							<option value=""<?php if($slide['auto_slide']['attached']['caption']==''):?> selected="selected"<?php endif; ?>><?php _e('No caption','lemur_slider'); ?></option>
							<option value="caption"<?php if($slide['auto_slide']['attached']['caption']=='caption'):?> selected="selected"<?php endif; ?>><?php _e('Caption','lemur_slider'); ?></option>
							<option value="title"<?php if($slide['auto_slide']['attached']['caption']=='title'):?> selected="selected"<?php endif; ?>><?php _e('Title','lemur_slider'); ?></option>
							<option value="title_caption"<?php if($slide['auto_slide']['attached']['caption']=='title_caption'):?> selected="selected"<?php endif; ?>><?php _e('Title & Caption','lemur_slider'); ?></option>
						</select>
					</p>
					<p>
						<label><?php _e('Caption position','lemur_slider'); ?></label>
								<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
									<a href="tl" <?php if($slide['auto_slide']['attached']['caption_position']=='caption tl' || $slide['auto_slide']['attached']['caption_position']==''):?>class="active"<?php endif; ?> title="<?php _e('Top Left','lemur_slider'); ?>"></a>
									<a href="tc" <?php if($slide['auto_slide']['attached']['caption_position']=='caption tc'):?>class="active"<?php endif; ?> title="<?php _e('Top Center','lemur_slider'); ?>"></a>
									<a href="tr" <?php if($slide['auto_slide']['attached']['caption_position']=='caption tr'):?>class="active"<?php endif; ?> title="<?php _e('Top Right','lemur_slider'); ?>"></a>
									<a href="cl" <?php if($slide['auto_slide']['attached']['caption_position']=='caption cl'):?>class="active"<?php endif; ?> title="<?php _e('Center Left','lemur_slider'); ?>"></a>
									<a href="cc" <?php if($slide['auto_slide']['attached']['caption_position']=='caption cc'):?>class="active"<?php endif; ?> title="<?php _e('Center','lemur_slider'); ?>"></a>
									<a href="cr" <?php if($slide['auto_slide']['attached']['caption_position']=='caption cr'):?>class="active"<?php endif; ?> title="<?php _e('Center Right','lemur_slider'); ?>"></a>
									<a href="bl" <?php if($slide['auto_slide']['attached']['caption_position']=='caption bl'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
									<a href="bc" <?php if($slide['auto_slide']['attached']['caption_position']=='caption bc'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
									<a href="br" <?php if($slide['auto_slide']['attached']['caption_position']=='caption br'):?>class="active"<?php endif; ?> title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
									<input type="hidden" class="field-attached-caption_position" name="slide_data[<?php echo $key; ?>][auto_slide][attached][caption_position]" value="<?php echo $slide['auto_slide']['attached']['caption_position']; ?>" />
								</span>
								<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
									<a href="<?php echo $slide['auto_slide']['attached']['animation_position']; ?>" class="nemusicon-down"></a>
									<input type="hidden" class="field-attached-animation_position" name="slide_data[<?php echo $key; ?>][auto_slide][attached][animation_position]" value="<?php echo $slide['auto_slide']['attached']['animation_position']; ?>" />
								</span>
					</p>
					<div class="clear"></div>
				</div>
			</div>
			</div>
			</div>
		<?php } // end existing slide management (if isset automated or not automated) ?>
	<?php endforeach; endif; ?>
	</div>

	<?php
	/**
	 * Regular Slide: Create New
	 *
	 * Activated by the "Create a new slide" button to add new slide
	 */
	?>

	<div class="slide" id="slide_empty" style="display:none">
		<header>
			<span><em>1.</em> <?php _e('slide','lemur_slider'); ?></span>
			<nav>
				<a href="#" class="nemusicon-docs duplicate"><?php _e('duplicate','lemur_slider'); ?></a>
				<a href="#" class="nemusicon-minus-circled delete"><?php _e('delete','lemur_slider'); ?></a>
			</nav>
		</header>
		<div class="body">
			<div class="left">
				<div class="image">
					<a href="#" class="nemusicon-upload-cloud upload"><?php _e('Upload Image','lemur_slider'); ?></a>
					<a href="#" class="upload_video" data-selected_text="Video selected" title="<?php _e('Youtube or Vimeo link','lemur_slider'); ?>"><?php _e('or video','lemur_slider'); ?></span></a>
					<input type="text" class="image-link" name="image" />
					<input type="text" class="video-link" name="video" />
				</div>
			</div>
			<div class="right">
				<div class="row caption">
					<div class="field">
						<label><?php _e('Image Caption','lemur_slider'); ?> <em><?php _e('you can use html code','lemur_slider'); ?></em></label>
						<textarea class="caption" name="caption"></textarea>
					</div>
					<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
						<a href="tl" class="active" title="<?php _e('Top Left','lemur_slider'); ?>"></a>
						<a href="tc" title="<?php _e('Top Center','lemur_slider'); ?>"></a>
						<a href="tr" title="<?php _e('Top Right','lemur_slider'); ?>"></a>
						<a href="cl" title="<?php _e('Center Left','lemur_slider'); ?>"></a>
						<a href="cc" title="<?php _e('Center','lemur_slider'); ?>"></a>
						<a href="cr" title="<?php _e('Center Right','lemur_slider'); ?>"></a>
						<a href="bl" title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
						<a href="bc" title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
						<a href="br" title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
						<input type="hidden" class="caption_position" name="caption_position" value="" />
					</span>
					<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
						<a href="right" class="nemusicon-down"></a>
						<input type="hidden" class="animation_position" name="" value="right" />
					</span>
				</div>
				<div class="row">
					<div class="field">
						<label><?php _e('Image Link','lemur_slider'); ?> <em>e.g. http://google.com</em></label>
						<input class="slide-link" name="link" type="text" />
					</div>
					<a href="#" class="nemusicon-popup link_target" title="<?php _e('Open in new window','lemur_slider'); ?>"></a>
					<input class="link_target" type="hidden" name="link_target" value="0" />
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>


	<?php
	/**
	 * Automated Slide: Create New
	 *
	 * Activated by the "Create automated slides" button to add new automated slide set
	 */
	?>



	<div class="slide slide-auto" id="slide_auto_empty" style="display:none">
	<header>
		<span class="nemusicon-rocket"><?php _e('Automated slides','lemur_slider'); ?></span>
		<nav>
			<a href="#" class="nemusicon-docs duplicate"><?php _e('duplicate','lemur_slider'); ?></a>
			<a href="#" class="nemusicon-minus-circled delete"><?php _e('delete','lemur_slider'); ?></a>
		</nav>
	</header>
	<div class="body">
	<div class="auto-slide-type">
				<span>
					<a href="#autoslide-tab-posts" data-type="posts" class="nemusicon-pin active"><?php _e('Posts','lemur_slider'); ?></a>
					<a href="#autoslide-tab-flickr" data-type="flickr" class="nemusicon-flickr-circled"><?php _e('Flickr','lemur_slider'); ?></a>
					<a href="#autoslide-tab-instagram" data-type="instagram" class="nemusicon-instagram"><?php _e('Instagram','lemur_slider'); ?></a>
					<a href="#autoslide-tab-attached" data-type="attached" class="nemusicon-attach"><?php _e('Attached Photos','lemur_slider'); ?></a>
				</span>
		<input type="hidden" name="autoslide_type" class="field-autoslide_type" value="posts" />
	</div>

	<div class="auto-slide-type-tab" style="display:block;">

		<div class="auto-slide-form">
			<p>
				<label><?php _e('Post type','lemur_slider'); ?></label>
				<select name="post_type" class="post_type lemur-chosen">
					<?php
					$post_types=get_post_types(array('public' => true),'objects');
					foreach ($post_types as $post_type ):
						?>
						<option value="<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label><?php _e('Category','lemur_slider'); ?></label>
				<?php wp_dropdown_categories(array(
					'show_count' => true,
					'hierarchical' => true,
					'show_option_all' => __('From all category','lemur_slider'),
					'class' => 'lemur-chosen'
				)); ?>
			</p>

			<?php
			/**
			 * Add checkbox to allow the user to enter post ID's instead of selecting a category
			 * TODO: While Select Posts is checked, disable the category select/use in query
			 * TODO: Hide Limit box if Select Posts is checked, and use -1 in the query for posts_per_page
			 */
			?>
			<p>
				<label><?php _e('Select Posts','lemur_slider'); ?></label>
				<input type="checkbox" value="1" name="select_posts" />

				<label><?php _e('Include Posts','lemur_slider'); ?></label>
				<span class="instructions">Enter comma-separated list of post ID's. IE 18,2,34,5</span>
				<input type="text" value="" name="include_posts" class="include_posts" />
			</p>

			<?php
			/**
			 * Define options for post sort order
			 * TODO: "Order Entered" will only be available if Select Posts is true
			 */
			?>
			<p>
				<label><?php _e('Order by','lemur_slider'); ?></label>
				<select name="order" class="orderby lemur-chosen">
					<option value="title"><?php _e('Name','lemur_slider'); ?></option>
					<option value="date"><?php _e('Date','lemur_slider'); ?></option>
					<option value="comment_count"><?php _e('Comments','lemur_slider'); ?></option>
					<option value="rand"><?php _e('Random','lemur_slider'); ?></option>
					<option value="post__in"><?php _e('Order Entered','lemur_slider'); ?></option>
				</select>
				<br />
				<label><?php _e('Order','lemur_slider'); ?></label>
				<select name="order" class="order lemur-chosen">
					<option value="ASC"><?php _e('Ascending','lemur_slider'); ?></option>
					<option value="DESC"><?php _e('Descending','lemur_slider'); ?></option>
				</select>
			</p>
			<p>

			</p>
			<p class="half">
				<label><?php _e('Limit','lemur_slider'); ?></label>
				<input type="number" value="5" name="limit" class="limit" />
			</p>
			<p class="half">
				<label><?php _e('Caption','lemur_slider'); ?></label>
						<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
							<a href="tl" class="active" title="<?php _e('Top Left','lemur_slider'); ?>"></a>
							<a href="tc" title="<?php _e('Top Center','lemur_slider'); ?>"></a>
							<a href="tr" title="<?php _e('Top Right','lemur_slider'); ?>"></a>
							<a href="cl" title="<?php _e('Center Left','lemur_slider'); ?>"></a>
							<a href="cc" title="<?php _e('Center','lemur_slider'); ?>"></a>
							<a href="cr" title="<?php _e('Center Right','lemur_slider'); ?>"></a>
							<a href="bl" title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
							<a href="bc" title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
							<a href="br" title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
							<input type="hidden" class="caption_position" name="caption_position" value="" />
						</span>
						<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
							<a href="right" class="nemusicon-down"></a>
							<input type="hidden" class="animation_position" name="" value="right" />
						</span>
			</p>
			<div class="clear"></div>
		</div>
		<input type="hidden" name="autoslide" class="autoslide" value="1" />
	</div>

	<div class="auto-slide-type-tab">
		<div class="auto-slide-form flickr">
			<p>
				<label><?php _e('Set ID','lemur_slider'); ?></label>
				<input type="text" value="" name="" class="field-flickr-setid" />
				<small>Go to your Flickr account and click on one of your sets. In the end of URL you'll see a number, this is the set id. Example: 72157625956932639</small>
			</p>
			<p>
				<label><?php _e('User ID','lemur_slider'); ?></label>
				<input type="text" value="" name="" class="field-flickr-user_id" />
				<small>You can use the public Flickr api to get photos from users without an api key</small>
			</p>
			<p>
				<label><?php _e('Link the slides to','lemur_slider'); ?></label>
				<select name="" class="order lemur-chosen field-flickr-linkto">
					<option value="" selected="selected"><?php _e('No link','lemur_slider'); ?></option>
					<option value="image"><?php _e('Link to image','lemur_slider'); ?></option>
					<option value="flickr"><?php _e('Link to flickr','lemur_slider'); ?></option>
				</select>
			</p>
			<p class="half">
				<label><?php _e('Limit','lemur_slider'); ?></label>
				<input type="number" value="5" name="" class="field-flickr-limit" />
			</p>
			<p class="half">
				<label><?php _e('Caption','lemur_slider'); ?></label>
						<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
							<a href="tl" title="<?php _e('Top Left','lemur_slider'); ?>"></a>
							<a href="tc" title="<?php _e('Top Center','lemur_slider'); ?>"></a>
							<a href="tr" title="<?php _e('Top Right','lemur_slider'); ?>"></a>
							<a href="cl" title="<?php _e('Center Left','lemur_slider'); ?>"></a>
							<a href="cc" title="<?php _e('Center','lemur_slider'); ?>"></a>
							<a href="cr" title="<?php _e('Center Right','lemur_slider'); ?>"></a>
							<a href="bl" title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
							<a href="bc" title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
							<a href="br" title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
							<input type="hidden" class="field-flickr-caption_position" name="" value="" />
						</span>
						<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
							<a href="right" class="nemusicon-down"></a>
							<input type="hidden" class="field-flickr-animation_position" name="" value="right" />
						</span>
			</p>
			<div class="clear"></div>
		</div>
	</div>

	<div class="auto-slide-type-tab">
		<div class="auto-slide-form instagram">
			<p>
				<label><?php _e('Hash','lemur_slider'); ?></label>
				<input type="text" value="" name="" class="field-instagram-hash" />
				<small>Get a list of recently tagged media.</small>
			</p>
			<p class="half">
				<label><?php _e('Limit','lemur_slider'); ?></label>
				<input type="number" value="5" name="" class="field-instagram-limit" />
			</p>
			<p>
				<label><?php _e('Link the slides to','lemur_slider'); ?></label>
				<select name="" class="order lemur-chosen field-instagram-linkto">
					<option value="" selected="selected"><?php _e('No link','lemur_slider'); ?></option>
					<option value="image"><?php _e('Link to image','lemur_slider'); ?></option>
					<option value="instagram"><?php _e('Link to instagram','lemur_slider'); ?></option>
				</select>
			</p>
			<p class="half">
				<label><?php _e('Caption','lemur_slider'); ?></label>
						<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
							<a href="tl" title="<?php _e('Top Left','lemur_slider'); ?>"></a>
							<a href="tc" title="<?php _e('Top Center','lemur_slider'); ?>"></a>
							<a href="tr" title="<?php _e('Top Right','lemur_slider'); ?>"></a>
							<a href="cl" title="<?php _e('Center Left','lemur_slider'); ?>"></a>
							<a href="cc" title="<?php _e('Center','lemur_slider'); ?>"></a>
							<a href="cr" title="<?php _e('Center Right','lemur_slider'); ?>"></a>
							<a href="bl" title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
							<a href="bc" title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
							<a href="br" title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
							<input type="hidden" class="field-instagram-caption_position" name="" value="" />
						</span>
						<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
							<a href="right" class="nemusicon-down"></a>
							<input type="hidden" class="field-instagram-animation_position" name="" value="right" />
						</span>
			</p>
			<div class="clear"></div>
		</div>
	</div>

	<div class="auto-slide-type-tab"<?php if($slide['auto_slide']['type'] == 'attached'): ?> style="display:block;"<?php endif; ?>>
		<div class="auto-slide-form attached">
			<p>
				<label><?php _e('Link the slides to','lemur_slider'); ?></label>
				<select name="" class="order lemur-chosen field-attached-linkto">
					<option value="" selected="selected"><?php _e('No link','lemur_slider'); ?></option>
					<option value="image"><?php _e('Link to image','lemur_slider'); ?></option>
					<option value="post"><?php _e('Link to post','lemur_slider'); ?></option>
				</select>
			</p>
			<p>
				<label><?php _e('Caption','lemur_slider'); ?></label>
				<select name="" class="order lemur-chosen field-attached-caption">
					<option value="" selected="selected"><?php _e('No caption','lemur_slider'); ?></option>
					<option value="caption"><?php _e('Caption','lemur_slider'); ?></option>
					<option value="title"><?php _e('Title','lemur_slider'); ?></option>
					<option value="title_caption"><?php _e('Title & Caption','lemur_slider'); ?></option>
				</select>
			</p>
			<p>
				<label><?php _e('Caption position','lemur_slider'); ?></label>
						<span class="caption_position" data-tooltip="<?php _e('Caption Position','lemur_slider'); ?>">
							<a href="tl" title="<?php _e('Top Left','lemur_slider'); ?>"></a>
							<a href="tc" title="<?php _e('Top Center','lemur_slider'); ?>"></a>
							<a href="tr" title="<?php _e('Top Right','lemur_slider'); ?>"></a>
							<a href="cl" title="<?php _e('Center Left','lemur_slider'); ?>"></a>
							<a href="cc" title="<?php _e('Center','lemur_slider'); ?>"></a>
							<a href="cr" title="<?php _e('Center Right','lemur_slider'); ?>"></a>
							<a href="bl" title="<?php _e('Bottom Left','lemur_slider'); ?>"></a>
							<a href="bc" title="<?php _e('Bottom Center','lemur_slider'); ?>"></a>
							<a href="br" title="<?php _e('Bottom Right','lemur_slider'); ?>"></a>
							<input type="hidden" class="field-attached-caption_position" name="" value="" />
						</span>
						<span class="animation_direction" data-tooltip="<?php _e('Animation Position','lemur_slider'); ?>">
							<a href="right" class="nemusicon-down"></a>
							<input type="hidden" class="field-attached-animation_position" name="" value="right" />
						</span>
			</p>
			<div class="clear"></div>
		</div>
	</div>


	<div class="clear"></div>
	</div>
	</div>

	<div class="align-center">
		<a href="#" class="nemusicon-plus-circled" id="add_slide"><?php _e('Create a new slide','lemur_slider'); ?></a>
		<a href="#" class="nemusicon-rocket" id="add_automated_slide"><?php _e('Create automated slides','lemur_slider'); ?></a>
	</div>
<?php
}

function save_lemur_slider_slide_box($post_id, $post) {
	global $post;

	if ( !isset( $_POST['lemur_slider_slide_nonce'] ) || !wp_verify_nonce( $_POST['lemur_slider_slide_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$post_meta = array();
	$post_meta['slide_data'] = '';
	if (isset($_POST['slide_data'])) $post_meta['slide_data'] = $_POST['slide_data'];

	foreach ($post_meta as $key => $value) {

		// save all of the individual slide info in an array stored in the slide_data post meta
		$new_meta_value = ( isset( $value ) ? $value : '' );
		$meta_key = $key;
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}
} // end function save_lemur_slider_slide_box($post_id, $post)
// End save function for the slider contents

/**
 * Set and save slider options meta
 */

add_action( 'post_submitbox_misc_actions', 'register_lemur_slider_additionals' );
add_action( 'save_post', 'save_lemur_slider_additionals',10,2 );
function register_lemur_slider_additionals() {
	global $post;
	if (get_post_type($post) == 'lemur_slider') {
		$custom = get_post_custom($post->ID);

		$slider_options = '';
		if (isset($custom['slider_options'][0])) $slider_options = $custom['slider_options'][0];

		$slider_options = unserialize($slider_options);

		$slider_template = '';
		if (isset($slider_options['template'])) $slider_template = $slider_options['template'];
		$slider_autoplay = '';
		if (isset($slider_options['autoplay'])) $slider_autoplay = $slider_options['autoplay'];
		$slider_inside_control = '';
		if (isset($slider_options['control_position'])) $slider_inside_control = $slider_options['control_position'];
		$slider_autoplay_delay = $slider_options['autoplay_delay'];
		$slider_autoheight = '';
		if (isset($slider_options['autoheight'])) $slider_autoheight = $slider_options['autoheight'];
		$slider_height = $slider_options['height'];
		$slider_animation = 'slide';
		if (isset($slider_options['animation'])) $slider_animation = $slider_options['animation'];
		$slider_color = $slider_options['color'];
		$slider_orientation = $slider_options['orientation'];
		$slider_image_scale_mode = '';
		if (isset($slider_options['image_scale'])) $slider_image_scale_mode = $slider_options['image_scale'];
		$slider_carousel = '';
		if (isset($slider_options['carousel'])) $slider_carousel = $slider_options['carousel'];
		$slider_carousel_width = '';
		if (isset($slider_options['carousel_width'])) $slider_carousel_width = $slider_options['carousel_width'];
		$slider_carousel_margin = '';
		if (isset($slider_options['carousel_margin'])) $slider_carousel_margin = $slider_options['carousel_margin'];


		wp_nonce_field( basename( __FILE__ ), 'slider_additionals' );

		//Enqueue color picker
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');

		/**
		 * Build out options meta box
		 */

		?>
		<div class="misc-pub-section lemur-slider-option slider-extra" id="slider-template" style="display: none;">
			<strong class="nemusicon-gift"><?php _e('Template','lemur_slider'); ?></strong>
			<?php
			/**
			 * TODO: build out template select that allows users to choose from the available templates
			 */
			// hang onto this for when we're ready to actually allow the user to select a template.
			// that said, the values will probably be selected from a dropdown, not entered in a textbox, but whatever
			// echo '<input type="text" value="' . $slider_template . '" name="slider_options[template]" placeholder="default" />';
			?>
			<input type="text" value="default" name="slider_options[template]" />
		</div>
		<div class="misc-pub-section lemur-slider-option">
			<strong class="nemusicon-play"><?php _e('Autoplay','lemur_slider'); ?></strong>
			<input type="checkbox" value="1" name="slider_options[autoplay]" <?php if($slider_autoplay): ?> checked="checked"<?php endif; ?> />
		</div>
		<div class="misc-pub-section lemur-slider-option slider-extra" id="autoplay-delay" <?php if(!$slider_autoplay): ?>style="display:none;"<?php endif; ?>>
			<strong class="nemusicon-back-in-time"><?php _e('Delay','lemur_slider'); ?></strong>
			<input type="text" value="<?php echo $slider_autoplay_delay; ?>" name="slider_options[autoplay_delay]" placeholder="3000" /> <em>ms</em>
		</div>
		<div class="misc-pub-section lemur-slider-option">
			<strong class="nemusicon-arrow-combo"><?php _e('Auto Height','lemur_slider'); ?></strong>
			<input type="checkbox" value="1" name="slider_options[autoheight]" <?php if($slider_autoheight): ?> checked="checked"<?php endif; ?> />
		</div>
		<div class="misc-pub-section lemur-slider-option slider-extra" id="slider-height" <?php if($slider_autoheight): ?>style="display:none;"<?php endif; ?>>
			<strong class="nemusicon-resize-vertical"><?php _e('Height','lemur_slider'); ?></strong>
			<input type="text" value="<?php echo $slider_height; ?>" name="slider_options[height]" placeholder="500px" /> <em>px</em>
		</div>
		<div class="misc-pub-section lemur-slider-option">
			<strong class="nemusicon-doc-landscape"><?php _e('Inside controls','lemur_slider'); ?></strong>
			<input type="checkbox" value="1" name="slider_options[control_position]" <?php if($slider_inside_control): ?> checked="checked"<?php endif; ?> />
		</div>
		<div class="misc-pub-section lemur-slider-option slider-extra slider-color">
			<strong class="nemusicon-palette"><?php _e('Color','lemur_slider'); ?></strong>
			<input type="text" value="<?php echo $slider_color; ?>" name="slider_options[color]" placeholder="#000000" class="color" />
		</div>
		<div class="misc-pub-section lemur-slider-option image-scale-option">
			<strong class="nemusicon-picture"><?php _e('Image Scale','lemur_slider'); ?></strong>
			<select name="slider_options[image_scale]" style="display:none;">
				<option value="fill"<?php if ($slider_image_scale_mode == 'fill' || $slider_image_scale_mode == ''): ?> selected="selected"<?php endif; ?>><?php _e('Fill','lemur_slider'); ?></option>
				<option value="fit"<?php if ($slider_image_scale_mode == 'fit'): ?> selected="selected"<?php endif; ?>><?php _e('Fit','lemur_slider'); ?></option>
				<option value="none"<?php if ($slider_image_scale_mode == 'none'): ?> selected="selected"<?php endif; ?>><?php _e('None','lemur_slider'); ?></option>
			</select>
			<span class="image-scale-options">
				<a data-type="fill" href="#"<?php if ($slider_image_scale_mode == 'fill' || $slider_image_scale_mode == ''): ?> class="active"<?php endif; ?>><?php _e('Fill','lemur_slider'); ?></a>
				<a data-type="fit" href="#"<?php if ($slider_image_scale_mode == 'fit'): ?> class="active"<?php endif; ?>><?php _e('Fit','lemur_slider'); ?></a>
				<a data-type="none" href="#"<?php if ($slider_image_scale_mode == 'none'): ?> class="active"<?php endif; ?>><?php _e('None','lemur_slider'); ?></a>
			</span>
		</div>
		<div class="misc-pub-section lemur-slider-option carousel-option">
			<strong class="nemusicon-ellipsis"><?php _e('Carousel','lemur_slider'); ?></strong>
			<input type="checkbox" value="1" name="slider_options[carousel]" <?php if($slider_carousel): ?> checked="checked"<?php endif; ?> />
			<div class="additional-fields<?php if($slider_carousel): ?> visible<?php endif; ?>" id="carousel-fields">
				<span><?php _e('Item width','lemur_slider'); ?></span> <input type="number" value="<?php echo $slider_carousel_width; ?>" name="slider_options[carousel_width]" placeholder="250" /> <em>px</em><br/>
				<span><?php _e('Margin','lemur_slider'); ?></span> <input type="number" value="<?php echo $slider_carousel_margin; ?>" name="slider_options[carousel_margin]" placeholder="5" /> <em>px</em>
			</div>
		</div>
		<div class="misc-pub-section lemur-slider-option slider-extra-checkbox fade-type">
			<strong class="fade <?php if($slider_animation == 'fade'): ?>active<?php endif; ?>"><?php _e('Fade','lemur_slider'); ?></strong>
			<a href="#" <?php if($slider_animation == 'slide'): ?>class="on"<?php endif; ?>><span></span></a>
			<strong class="slide <?php if($slider_animation == 'slide'): ?>active<?php endif; ?>"><?php _e('Slide','lemur_slider'); ?></strong>
			<input type="hidden" value="<?php echo $slider_animation; ?>" name="slider_options[animation]" />
			<div class="clear"></div>
		</div>
		<div class="misc-pub-section lemur-slider-option slider-extra-checkbox orientation-type">
			<strong class="horizontal <?php if($slider_orientation == 'horizontal' || $slider_orientation == ''): ?>active<?php endif; ?>"><?php _e('Horizontal','lemur_slider'); ?></strong>
			<a href="#" <?php if($slider_orientation == 'vertical'): ?>class="on"<?php endif; ?>><span></span></a>
			<strong class="vertical <?php if($slider_orientation == 'vertical'): ?>active<?php endif; ?>"><?php _e('Vertical','lemur_slider'); ?></strong>
			<input type="hidden" value="<?php echo $slider_orientation; ?>" name="slider_options[orientation]" />
			<div class="clear"></div>
		</div>
		<?php
		global $pagenow;
		if ($pagenow == 'post.php'):
			?>
			<div class="misc-pub-section lemur-slider-option">
				<strong class="nemusicon-code"><?php _e('Shortcode:','lemur_slider'); ?></strong>
				<input type="text" value="[lemur_slider id=&quot;<?php echo $post->ID; ?>&quot;]" readonly="true" />
			</div>
		<?php endif; ?>
		<div class="misc-pub-section">
			<?php
			if ( 'publish' == $post->post_status ) {
				$preview_link = LEMUR_SLIDER_URL.'lemur-slider-preview.php?id='.$post->ID;
				$preview_button = __( 'Preview Changes', 'lemur_slider' );
			} else {
				$preview_link = LEMUR_SLIDER_URL.'lemur-slider-preview.php?id='.$post->ID;
				$preview_button = __( 'Preview', 'lemur_slider' );
			}
			?>
			<a class="button" href="<?php echo $preview_link; ?>" target="_blank"><?php echo $preview_button; ?></a>
			<a href="#" class="lemur-slider-advanced-settings">Advanced settings</a>
		</div>
		<div class="misc-pub-section lemur-slider-advanced-settings-section">
			<ul>
				<?php
				$slider_options_reverse = ''; if (isset($slider_options['reverse'])) $slider_options_reverse = $slider_options['reverse'];
				$slider_options_animationloop = ''; if (isset($slider_options['animationLoop'])) $slider_options_animationloop = $slider_options['animationLoop'];
				$slider_options_startat = ''; if (isset($slider_options['startAt'])) $slider_options_startat = $slider_options['startAt'];
				$slider_options_animationspeed = ''; if (isset($slider_options['animationSpeed'])) $slider_options_animationspeed = $slider_options['animationSpeed'];
				$slider_options_randomize = ''; if (isset($slider_options['randomize'])) $slider_options_randomize = $slider_options['randomize'];
				$slider_options_controlnav = ''; if (isset($slider_options['controlNav'])) $slider_options_controlnav = $slider_options['controlNav'];
				$slider_options_directionnav = ''; if (isset($slider_options['directionNav'])) $slider_options_directionnav = $slider_options['directionNav'];
				?>
				<li><label>reverse:</label><input type="text" name="slider_options[reverse]" value="<?php echo $slider_options_reverse; ?>" placeholder="false" /></li>
				<li><label>animationLoop:</label><input type="text" name="slider_options[animationLoop]" value="<?php echo $slider_options_animationloop; ?>" placeholder="true" /></li>
				<li><label>startAt:</label><input type="text" name="slider_options[startAt]" value="<?php echo $slider_options_startat; ?>" placeholder="0" /></li>
				<li><label>animationSpeed:</label><input type="text" name="slider_options[animationSpeed]" value="<?php echo $slider_options_animationspeed; ?>" placeholder="600" /></li>
				<li><label>randomize:</label><input type="text" name="slider_options[randomize]" value="<?php echo $slider_options_randomize; ?>" placeholder="false" /></li>
				<li><label>controlNav:</label><input type="text" name="slider_options[controlNav]" value="<?php echo $slider_options_controlnav; ?>" placeholder="true" /></li>
				<li><label>directionNav:</label><input type="text" name="slider_options[directionNav]" value="<?php echo $slider_options_directionnav; ?>" placeholder="true" /></li>
			</ul>
		</div>
	<?php
	}
}
function save_lemur_slider_additionals($post_id, $post) {

	global $post;

	if ( !isset( $_POST['slider_additionals'] ) || !wp_verify_nonce( $_POST['slider_additionals'], basename( __FILE__ ) ) )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$post_meta = array();
	$post_meta['slider_options'] = '';
	if (isset($_POST['slider_options'])) $post_meta['slider_options'] = $_POST['slider_options'];

	foreach ($post_meta as $key => $value) {
		$new_meta_value = ( isset( $value ) ? $value : '' );
		$meta_key = $key;
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}

}
//Additional metabox for slider options