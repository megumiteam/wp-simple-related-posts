<?php
/*
Plugin Name: WP Simple Related Posts
Plugin URI: https://github.com/megumiteam/wp-simple-related-posts
Description: Display Related Posts. Very Simple.
Author: digitalcube
Version: 1.6.1
Author URI: https://github.com/megumiteam/wp-simple-related-posts
Text Domain: simple-related-posts
Domain Path: /languages/


Copyright 2018 - 2021 degitalcube (email : info@digitalcube)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'SIRP_DOMAIN' ) )
	define( 'SIRP_DOMAIN', 'wp-simple-related-posts' );

if ( ! defined( 'SIRP_PLUGIN_URL' ) )
	define( 'SIRP_PLUGIN_URL', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ));

if ( ! defined( 'SIRP_PLUGIN_DIR' ) )
	define( 'SIRP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ));

load_plugin_textdomain( SIRP_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages' );

add_action( 'admin_init', 'simple_related_posts_install' );
function simple_related_posts_install() {
	$css = <<<EOM
.simple-related-posts {
	list-style: none;
}

.simple-related-posts * {
	margin:0;
	padding:0;
}

.simple-related-posts li {
	margin-bottom: 10px;
}

.simple-related-posts li a {
	display: block;
}
.simple-related-posts li a p {
	display: table-cell;
	vertical-align: top;
}

.simple-related-posts li .thumb {
	padding-right: 10px;
}
EOM;
	if ( !get_option('simple_related_posts_installed') ) {
		$args = array(
			'target'       => 'Simple_Related_Posts_Tag',
			'display_num'  => 5,
			'post_content' => 1,
			'post_thumbnail'   => 1,
			'title' => __('Related Posts', SIRP_DOMAIN ),
			'original_css' => 1,
			'original_css_content' => $css,
			'rss_post_content' => ''
		);
		update_option('sirp_options', $args);
		update_option('simple_related_posts_installed', 1);
	} else {
		$option = get_option('sirp_options');
		if ( !array_key_exists( 'original_css_content', $option ) ) {
			$option['original_css_content'] = $css;
			update_option('sirp_options', $option);
		}
	}
}

class Simple_Related_Posts {
	private $related = '';

	public function __construct() {
		$this->requirements();
		$option = get_option('sirp_options');
		if ( class_exists($option['target']) ) {
			$this->related = new $option['target'];
		}
		add_filter( 'wp_insert_post_data', [ $this, 'save_simple_related_posts' ], 10, 2 );
	}

	private function requirements() {
		require_once(dirname(  __FILE__ ) . '/modules/base.php');

		$sirp_dirs = array(
			dirname(  __FILE__ ) . '/admin/',
			dirname(  __FILE__ ) . '/modules/'
		);
		foreach ( $sirp_dirs as $dir ) {
			opendir($dir);
			while(($ent = readdir()) !== false) {
				if(!is_dir($ent) && strtolower(substr($ent,-4)) == ".php") {
					require_once($dir.$ent);
				}
			}
			closedir();
		}
		do_action( 'sirp_addon_requirement' );
	}

	public function get_data_original($num = '') {
		return $this->related->get_data_original($num);
	}

	public function get_data($num = '') {
		return $this->related->get_data($num);
	}

	public function get_data_api( $num = '', $post_id = null ) {
		return $this->related->get_data_api($num, $post_id);
	}

	public function save_simple_related_posts( $data, $postarr ) {
		if ( 'draft' === $data['post_status'] && isset( $postarr['simple_related_posts'] ) ) {
			$related_posts = $postarr['simple_related_posts'];
			update_post_meta( $postarr['ID'], 'simple_related_posts', $related_posts );
		}
		return $data;
	}
}

$simple_related_posts = new Simple_Related_Posts();

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'json-rest-api/plugin.php' ) && ( '3.9.2' <= get_bloginfo( 'version' ) ) ) {
	require_once( dirname(  __FILE__ ) . '/lib/wp-rest-api.php' );

	function sirp_json_api_related_filters( $server ) {
		// Related
		$wp_json_related = new WP_JSON_SIRP( $server );
		add_filter( 'json_endpoints', array( $wp_json_related, 'register_routes' ), 1 );
	}
	add_action( 'wp_json_server_before_serve', 'sirp_json_api_related_filters', 10, 1 );
}
