<?php
class Simple_Related_Posts_Admin_Meta_Box {
	public function __construct() {
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'admin_footer-post.php', array( $this, 'admin_footer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_srp_search_posts', array( $this, 'srp_search_posts' ) );
		add_action( 'wp_ajax_srp_reset_related_posts', array( $this, 'srp_reset_related_posts' ) );
		
	}
	
	public function admin_footer() {

?>
<script>
	var srp_post_id = <?php echo get_the_ID(); ?>;
</script>
<?php
	}
	
	public function save_post($post_id) {
		if ( isset($_POST['simple_related_posts']) && is_array($_POST['simple_related_posts']))
			update_post_meta( $post_id, 'simple_related_posts', $_POST['simple_related_posts'] );
	}

    public function admin_enqueue_scripts() {
        wp_register_style( 'spr-admin-css', SRP_PLUGIN_URL . '/css/style.css' );
        wp_enqueue_style( 'spr-admin-css' );
        wp_register_script( 'spr-admin-js', SRP_PLUGIN_URL . '/js/common.js' );
        wp_enqueue_script( 'spr-admin-js' );
        wp_register_script( 'spr-color-js', SRP_PLUGIN_URL . '/js/jquery.color.js' );
        wp_enqueue_script( 'spr-color-js' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_localize_script( 'spr-admin-js', 'objectL10n', array(
			'alert' => __( 'Maximum number of Related Posts %d', SRP_DOMAIN ),
		) );
    }
    
    public function srp_reset_related_posts() {
	    global $simple_related_posts, $post;
	    if ( !isset($_POST['post_id']) || !is_numeric($_POST['post_id']) )
    		return;
		
		$post = get_post($_POST['post_id']);
		$results = $simple_related_posts->get_data_original();	
		$json = array();
		$cnt  = 0;
		
		if ( empty($results) )
			return;

		foreach ( $results as $id ) {
			$json[$cnt]['ID'] = $id['ID'];
			$json[$cnt]['post_title'] = get_the_title($id['ID']);
			$image = get_the_post_thumbnail( $id['ID'], array(21, 21) );
			$json[$cnt]['post_thumbnail'] = !empty($image) ? $image : '';
			$json[$cnt]['permalink'] = get_permalink($id['ID']);
			$cnt++;
		}
		echo json_encode($json);
		exit;
    }
    
    public function srp_search_posts() {
    	if ( !isset($_POST['s']) )
    		return;
		
		$resutls = $this->search($_POST['s']);
		if ( empty($resutls) )
			return;

		$json = array();
		$cnt  = 0;
		foreach ( $resutls as $ret ) {
			$json[$cnt]['ID'] = $ret->ID;
			$json[$cnt]['post_title'] = get_the_title($ret->ID);
			$image = get_the_post_thumbnail( $ret->ID, array(21, 21) );
			$json[$cnt]['post_thumbnail'] = !empty($image) ? $image : '';
			$json[$cnt]['permalink'] = get_permalink($ret->ID);
			$cnt++;
		}
		echo json_encode($json);
		exit;
    }
	
	public function add_meta_box() {
		add_meta_box( 'simple-related-posts', __( 'Related Posts', SRP_DOMAIN ), array( $this, 'meta_box' ), 'post', 'advanced', 'low' );
	}
	
	public function meta_box() {
		global $simple_related_posts;
				
		?>
<div class="srp_relationship" >
	<!-- Left List -->
	<div class="relationship_left">
		<table class="widefat">
			<thead>
				<tr>
					<th>
						<input class="relationship_search" placeholder="<?php _e("Search...",SRP_DOMAIN); ?>" type="text" />
					</th>
				</tr>
			</thead>
		</table>
		<ul class="relationship_list">
			<li class="load-more">
			</li>
		</ul>
	</div>
	<!-- /Left List -->
	
	<!-- Right List -->
	<div class="relationship_right">
		<h3><?php _e("Related Posts to display", SRP_DOMAIN); ?><input type="button" id="srp-reset" class="button-secondary" value="<?php _e('Reset', SRP_DOMAIN); ?>" /></h3>
		<ul class="bl relationship_list">
		<?php
			$related_posts = $simple_related_posts->get_data();
			
			if ( !empty($related_posts) ) {
				foreach( $related_posts as $p ) {
					$image = get_the_post_thumbnail( $p['ID'], array(21, 21) );
					
					$title = ''; 
					if ( !empty($image) )
						$title .= '<div class="result-thumbnail">' . $image . '</div>';
	
					$title .= get_the_title($p['ID']);
					
					echo '<li>
						<a href="' . get_permalink($p['ID']) . '" class="" data-post_id="' . $p['ID'] . '"><span class="title">' . $title . '</span><span class="srp-button"></span></a>
					</li>';					
				}	
			}		
		?>
		</ul>
	</div>
	<!-- / Right List -->
	
</div>
<?php
	}
	
	public function search($s) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS ID FROM " . $wpdb->posts . " WHERE (((post_title LIKE '%%%s%%') OR (post_content LIKE '%%%s%%'))) AND wp_posts.post_type = 'post' AND post_status = 'publish' ORDER BY post_title LIKE '%%%s%%' DESC, post_date DESC LIMIT 0, 10", $s, $s, $s );
		
		return $wpdb->get_results($sql);
	}
}
new Simple_Related_Posts_Admin_Meta_Box();