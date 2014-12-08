<?php 
/*
Plugin Name: WP Simple Related Posts
Plugin URI: http://www.kakunin-pl.us/
Description: Display Related Posts. Very Simple.
Author: horike takahiro
Version: 1.4.4
Author URI: http://www.kakunin-pl.us/
Text Domain: simple-related-posts
Domain Path: /languages/


Copyright 2014 horike takahiro (email : horike37@gmail.com)

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
	}
		
	private function requirements() {
		require_once(SIRP_PLUGIN_DIR . '/modules/base.php');

		$sirp_dirs = array(
			SIRP_PLUGIN_DIR . '/admin/',
			SIRP_PLUGIN_DIR . '/modules/'
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
}

$simple_related_posts = new Simple_Related_Posts();