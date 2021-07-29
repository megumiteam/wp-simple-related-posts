<?php
class Simple_Related_Posts_Admin_Meta_Box {
	CONST NONCE_ACTION = 'SIMPLE_RELATED_POSTS';
	CONST NONCE_NAME = '_nonce_simple_related_posts';

	public function __construct() {
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'admin_footer-post.php', array( $this, 'admin_footer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_sirp_search_posts', array( $this, 'sirp_search_posts' ) );
		add_action( 'wp_ajax_sirp_reset_related_posts', array( $this, 'sirp_reset_related_posts' ) );
		
	}
	
	public function admin_footer() {

?>
<script>
	var sirp_post_id = <?php echo get_the_ID(); ?>;
</script>
<?php
	}
	
	public function save_post($post_id) {
		// auto save の時は何もしない
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// WP Cron API で呼び出された時は何もしない
		if ( wp_doing_cron() ) {
			return;
		}
		// nonce field がなければ何もしない
		if ( !isset( $_POST[self::NONCE_NAME] ) || !wp_verify_nonce( $_POST[self::NONCE_NAME], self::NONCE_ACTION ) ) {
			return;
		}

		// リビジョンなら本物の投稿の ID を取得
		if ( $parent_id = wp_is_post_revision( $post_id ) ) {
			$post_id = $parent_id;
		}

		if ( isset($_POST['simple_related_posts']) && is_array($_POST['simple_related_posts'])) {
			update_post_meta( $post_id, 'simple_related_posts', $_POST['simple_related_posts'] );
		} else {
			if ( get_post_meta( $post_id, 'simple_related_posts', true ) ) {
				delete_post_meta( $post_id, 'simple_related_posts' );
			}
		}
	}

    public function admin_enqueue_scripts() {
        wp_register_style( 'sipr-admin-css', SIRP_PLUGIN_URL . '/css/style.css', array(), date('YmdHis', filemtime(dirname( __FILE__ ) . '/../css/style.css')) );
        wp_enqueue_style( 'sipr-admin-css' );
        wp_register_script( 'sipr-admin-post-js', SIRP_PLUGIN_URL . '/js/admin-post.js', array(), date('YmdHis', filemtime(dirname( __FILE__ ) . '/../js/admin-post.js')) );
        wp_enqueue_script( 'sipr-admin-post-js' );
        wp_register_script( 'sipr-color-js', SIRP_PLUGIN_URL . '/js/jquery.color.js', array(), date('YmdHis', filemtime(dirname( __FILE__ ) . '/../js/jquery.color.js')) );
        wp_enqueue_script( 'sipr-color-js' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_localize_script( 'sipr-admin-post-js', 'objectL10n', array(
			'alert' => __( 'Maximum number of Related Posts %d', SIRP_DOMAIN ),
		) );
    }
    
    public function sirp_reset_related_posts() {
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
    
    public function sirp_search_posts() {
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
		add_meta_box( 'simple-related-posts', __( 'Related Posts', SIRP_DOMAIN ), array( $this, 'meta_box' ), 'post', 'advanced', 'low' );
	}
	
	public function meta_box() {
		global $simple_related_posts;

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		?>
<div class="sirp_relationship" >
	<!-- Left List -->
	<div class="relationship_left">
		<table class="widefat">
			<thead>
				<tr>
					<th>
						<input class="relationship_search" placeholder="<?php _e("Search...",SIRP_DOMAIN); ?>" type="text" />
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
		<h3><?php _e("Related Posts to display", SIRP_DOMAIN); ?><input type="button" id="sirp-reset" class="button-secondary" value="<?php _e('Reset', SIRP_DOMAIN); ?>" /></h3>
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
						<a href="' . get_permalink($p['ID']) . '" class="" data-post_id="' . $p['ID'] . '"><span class="title">' . $title . '</span><span class="sirp-button"></span></a>
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

		$sql = $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS ID FROM " . $wpdb->posts . " WHERE (((post_title LIKE '%%%s%%') OR (post_content LIKE '%%%s%%'))) AND " . $wpdb->posts .".post_type = 'post' AND post_status = 'publish' ORDER BY post_title LIKE '%%%s%%' DESC, post_date DESC LIMIT 0, 10", $s, $s, $s );
		
		return $wpdb->get_results($sql);
	}
}
new Simple_Related_Posts_Admin_Meta_Box();