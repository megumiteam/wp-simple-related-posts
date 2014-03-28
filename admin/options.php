<?php
class Simple_Related_Posts_Admin_Options {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
	}
	
	public function admin_menu() {
		add_options_page( __( 'Simple Related Posts', SRP_DOMAIN ), __( 'Simple Related Posts', SRP_DOMAIN ), 'manage_options', 'simple_related_posts', array( $this, 'option_page'));
	}

	public function option_page() {
	?>
<div class="wrap">
	
<h2><?php _e( 'Simple Related Posts', SRP_DOMAIN ); ?></h2>
	
<form action="options.php" method="post">
<?php settings_fields( 'srp_options' ); ?>
<?php do_settings_sections( 'srp' ); ?>
	
<p class="submit"><input name="Submit" type="submit" value="<?php _e( 'save', SRP_DOMAIN ) ?>" class="button-primary" /></p>
</form>
	
</div>
<?php
	}
	
	public function admin_init() {
		register_setting( 'srp_options', 'srp_options', array( $this, 'srp_options_validate' ) );	
		add_settings_section( 'srp_main', __( 'Configuration', SRP_DOMAIN ), array( $this, 'srp_section_text' ), 'srp' );	
		add_settings_field( 'srp_target', __( 'Display method', SRP_DOMAIN ), array( $this, 'setting_srp_target' ), 'srp', 'srp_main' );
		add_settings_field( 'srp_display_num',  __( 'Views', SRP_DOMAIN ), array( $this, 'setting_srp_display_num' ), 'srp', 'srp_main' );
		add_settings_field( 'srp_title',  __( 'Related Posts Title', SRP_DOMAIN ), array( $this, 'setting_srp_title' ), 'srp', 'srp_main' );
		add_settings_field( 'srp_post_content',  __( 'Post Content', SRP_DOMAIN ), array( $this, 'setting_srp_post_content' ), 'srp', 'srp_main' );
		add_settings_field( 'srp_post_thumbnail',  __( 'Post Thumbnail', SRP_DOMAIN ), array( $this, 'setting_srp_post_thumbnail' ), 'srp', 'srp_main' );
		add_settings_field( 'srp_original_css',  __( 'CSS', SRP_DOMAIN ), array( $this, 'setting_srp_original_css' ), 'srp', 'srp_main' );
		//add_settings_field( 'srp_rss_post_content',  __( 'RSS', SRP_DOMAIN ), array( $this, 'setting_srp_rss_post_content' ), 'srp', 'srp_main' );
	}
	
	public function srp_section_text() {
	}
	
	public function setting_srp_target() {
		$options = get_option( 'srp_options' );
	
		echo '<label for="srp_target_tag"><input id="srp_target_tag" name="srp_options[target]" type="radio" '.checked( $options['target'], 'Simple_Related_Posts_Tag', false ).' value="Simple_Related_Posts_Tag" />' . __( 'View Related Posts based on tags', SRP_DOMAIN ) . '</label><br />';
		echo '<label for="srp_target_category"><input id="srp_target_category" name="srp_options[target]" type="radio" '.checked( $options['target'], 'Simple_Related_Posts_Category', false ).' value="Simple_Related_Posts_Category" />' . __( 'View Related Posts based on categoris', SRP_DOMAIN ) . '</label><br />';
		echo '<label for="srp_target_category_tag"><input id="srp_target_category_tag" name="srp_options[target]" type="radio" '.checked( $options['target'], 'Simple_Related_Posts_Category_Tag', false ).' value="Simple_Related_Posts_Category_Tag" />' . __( 'View Related Posts based on categoris and tag', SRP_DOMAIN ) . '</label>';
	}
	
	public function setting_srp_display_num() {
		$options = get_option( 'srp_options' );

		echo '<input id="srp_display_num" name="srp_options[display_num]" size="2" type="text" value="' . esc_attr( $options['display_num'] ) . '" />';
	}
	
	public function setting_srp_title() {
		$options = get_option( 'srp_options', __('Related Posts', SRP_DOMAIN ) );
		

		echo '<input id="srp_title" name="srp_options[title]" size="15" type="text" value="' . esc_attr( $options['title'] ) . '" />';
	}

	public function setting_srp_post_content() {
		$options = get_option( 'srp_options' );

		echo '<label for="srp_post_content"><input id="srp_post_content" name="srp_options[post_content]" type="checkbox" '.checked( $options['post_content'], 1, false ).' value="1" />' . __( 'Allow to display related posts at the end of the post content automatically', SRP_DOMAIN ) . '</label>';
	}
	
	public function setting_srp_post_thumbnail() {
		$options = get_option( 'srp_options' );

		echo '<label for="srp_post_thumbnail"><input id="srp_post_thumbnail" name="srp_options[post_thumbnail]" type="checkbox" '.checked( $options['post_thumbnail'], 1, false ).' value="1" />' . __( 'Allow to display a thumbnail to related posts', SRP_DOMAIN ) . '</label>';
	}
	
	public function setting_srp_original_css() {
		$options = get_option( 'srp_options' );

		echo '<label for="srp_original_css"><input id="srp_original_css" name="srp_options[original_css]" type="checkbox" '.checked( $options['original_css'], 1, false ).' value="1" />' . __( 'Use css', SRP_DOMAIN ) . '</label>';
	}

	public function setting_srp_rss_post_content() {
		$options = get_option( 'srp_options' );

		echo '<label for="srp_rss_post_content"><input id="srp_rss_post_content" name="srp_options[rss_post_content]" type="checkbox" '.checked( $options['rss_post_content'], 1, false ).' value="1" />' . __( 'Allow to add related posts at the end of the RSS post content automatically', SRP_DOMAIN ) . '</label>';
	}
	
	public function srp_options_validate( $input ) {
		$newinput['target'] = trim( $input['target'] );
		$newinput['display_num'] = absint( $input['display_num'] );
		$newinput['post_content'] = absint( $input['post_content'] );
		$newinput['post_thumbnail'] = absint( $input['post_thumbnail'] );
		$newinput['title'] = trim( $input['title'] );
		$newinput['original_css'] = absint( $input['original_css'] );
		$newinput['rss_post_content'] = absint( $input['rss_post_content'] );
	
		return $newinput;
	}
	
function admin_footer(){
	$options = get_option( 'srp_options' );
?>
<script type="text/javascript">
	var srp_display_num = <?php echo esc_js( $options['display_num'] ); ?>;
</script>
<?php
}
}
new Simple_Related_Posts_Admin_Options();