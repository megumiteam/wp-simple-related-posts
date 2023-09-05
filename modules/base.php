<?php
class Simple_Related_Posts_Base {
	protected $option = '';

	public function __construct() {
		$this->option = get_option('sirp_options');
		if ( $this->option['post_content'] == 1 )
			add_filter( 'the_content', array($this, 'the_content') );
	}
	
	public function the_content( $content ) {
		if ( is_single() )
			return $content.$this->get_reloated_posts();
			
		return $content;
	}

	public function get_reloated_posts( $num = '' ) {

		$posts = $this->get_data( $num );
		if ( !$posts )
			return false;

		$html = '<h2 class="simple-related-posts-title">' . apply_filters( 'sirp_title' , __( 'Related Posts', SIRP_DOMAIN ) ) . '</h2><ul class="simple-related-posts">';
		foreach( $posts as $post ) {
			$html .= '<li><a href="' . get_permalink( $post['ID'] ) . '" >';
			$html .= apply_filters( 'sirp_before_post_title', '', $post['ID'] );
			$html .= apply_filters( 'sirp_post_title', '<p class="title">' . get_the_title($post['ID']) . '</p>', $post['ID'] );
			$html .= apply_filters( 'sirp_after_post_title', '', $post['ID'] );
			$html .= '</a></li>';
		}
		$html .= '</ul>';

		return $html;

	}
	public function get_data( $num = '' ) {
		$related_posts = $this->get_data_post_meta($num, get_the_ID());
		if ( !$related_posts )
			$related_posts = $this->get_data_original($num, get_the_ID());

		if ( is_array($related_posts) )
			$related_posts = array_unique( $related_posts, SORT_REGULAR );
			
		return $related_posts;
	}
	
	public function get_data_api( $num = '', $post_id = null ) {
		$related_posts = $this->get_data_post_meta($num, $post_id);
		if ( !$related_posts )
			$related_posts = $this->get_data_original($num, $post_id);

		if ( is_array($related_posts) )
			$related_posts = array_unique( $related_posts, SORT_REGULAR );
			
		return $related_posts;
	}

	public function get_data_post_meta( $num = '', $post_id = null ) {
		global $post;
		
		if ( !isset( $post_id ) )
    		$post_id = $post->ID;
    		
    	if ( empty( $post_id ) )
			return false;

		$posts = get_post_meta( $post_id, 'simple_related_posts', true );

		// If for some reason meta_value returns as serialized data, try maybe_unsirialize again.
		if ( ! is_array( $posts ) ) {
			$posts = maybe_serialize( $posts );
		}
		
		// `$posts` is maybe_unserialized so it's definitely an array. Therefore, `! $posts` is equivalent to `empty( $posts )`.
		if ( empty( $posts ) || ! is_array( $posts ) )
			return false;
		
		$posts_ids = array();
		foreach ( (array) $posts as $id ) {
			$my_post = get_post($id);
			if ( $my_post->post_status === 'publish' && empty($my_post->post_password) ) {
				$posts_ids[]['ID'] = $id;
			}
		}
		
		$current_num = count($posts);
		if ( $num == '' ) {
			$option = get_option('sirp_options');
			$display_num = intval($this->option['display_num']);
		} else {
			$display_num = intval($num);
		}
		
		if ( $current_num > $display_num ) {
			$num = $current_num - $display_num;
			for ($i = 0; $i < $num; $i++) {
				array_pop($posts_ids);
			}
			return $posts_ids;
		} else {
			return $posts_ids;
		}	
	}
	
	public function get_data_original( $num = '' ) {
	}
}