<?php
class Simple_Related_Posts_Category_Tag extends Simple_Related_Posts_Base {

	public function get_data_original( $num = '', $post_id = null ) {
		global $wpdb, $post;
		
		if ( !isset($post_id) )
    		$post_id = $post->ID;

		if ( empty( $post_id ) )
			return false;

		$current_tags = get_the_tags( $post_id );
		if ( !$current_tags )
			return false;
		
		$tags = array();

		foreach ( (array) $current_tags as $tag ) {
			$tags[] = absint( $tag->term_id );
		}

		$tag_list = implode( ',', $tags );
		
		
		$current_categories = get_the_category( $post_id );
		if ( !$current_categories )
			return false;
		
		$categories = array();

		foreach ( (array) $current_categories as $category ) {
			$categories[] = absint( $category->term_id );
		}

		$category_list = implode( ',', $categories );
		
		if ( $num == '' ) {
			$option = get_option('sirp_options');
			$num = $option['display_num'];
		}

		$args = array( 
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => $num,
				'post__not_in' => array($post_id),
				'orderby' => 'rand',
				'has_password' => false,
				'tax_query' => array(
								'relation' => 'AND',
								array(
									'taxonomy' => 'category',
									'terms' => $categories,
									'field' => 'term_id',
									'operator' => 'IN',
									),
								array(
									'taxonomy' => 'post_tag',
									'terms' => $tags,
									'field' => 'term_id',
									'operator' => 'IN',
									),
							), 
			);
		$results = get_posts( $args );
		
		$results_array = array();
		
		if ( empty($results) )
			return false;
			
		foreach ( $results as $result ) {
			$results_array[]['ID'] = $result->ID;
		}
		return $results_array;
	}
	
	
}