<?php

add_action(
	'widgets_init',
	function() {
		return register_widget( "Simple_Related_Posts_Widget" );
	}
);

class Simple_Related_Posts_Widget extends WP_Widget {

function __construct() {
	$widget_ops  = array( 'description' => __( 'Displays related posts.', SIRP_DOMAIN ) );
	$control_ops = array();
	parent::__construct(
		false,
		__( 'Simple Related Posts', SIRP_DOMAIN ),
		$widget_ops,
		$control_ops
	);
}

public function form( $par ) {

	// Title
	$title = ( isset( $par['title'] ) && $par['title']) ? esc_attr( $par['title'] ) : '';
	$id    = esc_attr( $this->get_field_id( 'title' ) );
	$name  = esc_attr( $this->get_field_name( 'title' ) );
	?>
	<p>
		<label for="<?php echo $id; ?>">
		<?php _e( 'Title: <br />', SIRP_DOMAIN ); ?>
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $title; ?>" />
		</label>
	</p>
	<?php

	// input howmany posts to display. default:5
	$count = ( isset( $par['pcount'] ) && $par['pcount']) ? esc_attr( (int)$par['pcount'] ) : 5;
	$id    = esc_attr( $this->get_field_id( 'pcount' ) );
	$name  = esc_attr( $this->get_field_name( 'pcount' ) );
	?>
	<p>
		<label for="<?php echo $id; ?>">
		<?php _e( 'Count: <br />', SIRP_DOMAIN ); ?>
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $count; ?>" />
		<?php _e( '<br />Default: 5', SIRP_DOMAIN ); ?>
		</label>
	</p>
	<?php
}

public function update( $new_instance, $old_instance ) {
	return $new_instance;
}

public function widget( $args, $par ) {

	$if_show_widget = apply_filters( 'sirp_if_show_widget', is_single() );

	if ( $if_show_widget ) {

		$count = ( isset( $par['pcount'] ) && $par['pcount'] ) ? (int)$par['pcount'] : 5;
		echo $args['before_widget'];
		if ( $par['title'] ) {
			echo $args['before_title'];
			echo esc_html( $par['title'] );
			echo $args['after_title'];
		}
		$p_arr = array();
		global $simple_related_posts;
		$post_data = $simple_related_posts->get_data();
		if ( $post_data ) {
			echo '<ul>';
			foreach ( $simple_related_posts->get_data() as $p ) {
				$p_arr[] = $p['ID'];
			}
			$p_args = array(
				'post_type'      => 'post',
				'posts_per_page' => $count,
				'no_found_rows'  => true,
				'post__in'       => $p_arr,
			);
			$p_args = apply_filters( 'sirp_query_args', $p_args );
			$my_query = new WP_Query( $p_args );
			while ( $my_query->have_posts() ):
				$my_query->the_post();
				?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php
			endwhile;
			wp_reset_postdata();
			echo '</ul>';
		} else {
			?>
			<p class="no-related-posts"><?php _e( 'No related posts.', SIRP_DOMAIN ); ?></p>
			<?php
		}
		echo $args['after_widget'];

	}

}

}
