<?php
class Simple_Related_Posts_Tag extends Simple_Related_Posts_Base {
	
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
		
		if ( $num == '' ) {
			$option = get_option('sirp_options');
			$num = $option['display_num'];
		}

		$q = "SELECT ID FROM $wpdb->posts AS p
			INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id)
			INNER JOIN $wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			WHERE (tt.taxonomy = 'post_tag' AND tt.term_id IN ({$tag_list}))
			AND p.post_status = 'publish'
			AND p.post_type = 'post'
			AND p.ID != {$post_id}
			AND p.post_password = ''
			GROUP BY tr.object_id
			ORDER BY post_date DESC" . $wpdb->prepare( " LIMIT %d", $num );

		return $wpdb->get_results( $q, ARRAY_A );
	}
}