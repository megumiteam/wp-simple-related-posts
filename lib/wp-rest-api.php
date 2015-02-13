<?php
class WP_JSON_SIRP extends WP_JSON_Posts {

	public function register_routes( $routes ) {
		$ranking_routes = array(
			'/posts/(?P<id>\d+)/sirp_related' => array(
				array( array( $this, 'get_related' ), WP_JSON_Server::READABLE ),
			),
			'/sirp_related/(?P<id>\d+)' => array(
				array( array( $this, 'get_related' ), WP_JSON_Server::READABLE ),
			),
		);
		return array_merge( $routes, $ranking_routes );
	}

	public function get_related( $id = '', $filter = array(), $context = 'view'  ) {
		$option     = get_option('sirp_options');
		$num        = ! empty( $filter['num'] ) ? (int) $filter['num'] : (int) $option['display_num'];
		$ids        = sirp_get_related_posts_id_api( $num, $id );
		$posts_list = array();
		foreach ( $ids as $id ) {
			$posts_list[] = get_post( $id['ID'] );
		}
		$response = new WP_JSON_Response();

		if ( ! $posts_list ) {
			$response->set_data( array() );
			return $response;
		}

		$struct = array();

		$response->header( 'Last-Modified', mysql2date( 'D, d M Y H:i:s', get_lastpostmodified( 'GMT' ), 0 ).' GMT' );

		foreach ( $posts_list as $post ) {
			$post = get_object_vars( $post );

			if ( ! $this->check_read_permission( $post ) ) {
				continue;
			}

			$response->link_header( 'item', json_url( '/posts/' . $post['ID'] ), array( 'title' => $post['post_title'] ) );
			$post_data = $this->prepare_post( $post, $context );
			if ( is_wp_error( $post_data ) ) {
				continue;
			}

			$struct[] = $post_data;
		}
		$response->set_data( $struct );
		return $response;
	}

}
