<?php
/**
 * Lemur Slider Widget
 */


//Slider widget
class Lemur_Slider_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'lemur_slider_widget', // Base ID
			'Lemur Slider', // Name
			array( 'description' => __( 'Display a Lemur Slider', 'lemur_slider' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$slider = $instance['slider'];

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo do_shortcode('[lemur_slider id="'.$slider.'"]');
		echo $after_widget;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Lemur Slider', 'lemur_slider' );
		}

		$slider = '';
		if ( isset( $instance[ 'slider' ] ) ) $slider = $instance[ 'slider' ];
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:','lemur_slider' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name( 'slider' ); ?>"><?php _e( 'Slider:','lemur_slider' ); ?></label>

			<?php
			$args = array(
				'post_type' => 'lemur_slider',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC'
			);
			$lemur_sliders = new WP_Query($args);
			?>
			<?php if ( $lemur_sliders->have_posts() ): ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'slider' ); ?>" name="<?php echo $this->get_field_name( 'slider' ); ?>">
					<?php while($lemur_sliders->have_posts()): $lemur_sliders->the_post(); ?>
						<option value="<?php the_ID(); ?>" <?php if(get_the_ID() == $slider): ?>selected="selected"<?php endif; ?>><?php the_title(); ?></option>
					<?php endwhile; wp_reset_query(); ?>
				</select>
			<?php else: ?>
				<em><?php _e('Create a slider first','lemur_slider'); ?></em>
			<?php endif; ?>
		</p>
	<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['slider'] = ( !empty( $new_instance['slider'] ) ) ? strip_tags( $new_instance['slider'] ) : '';

		return $instance;
	}

} //class Lemur_Slider_Widget extends WP_Widget

function lemur_slider_register_widget() {
	register_widget( 'Lemur_Slider_Widget' );
}
add_action( 'widgets_init', 'lemur_slider_register_widget' );
//Slider widget
