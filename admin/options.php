<?php
class Simple_Related_Posts_Admin_Options {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
	}
	
	public function admin_menu() {
		add_options_page( __( 'Simple Related Posts', SIRP_DOMAIN ), __( 'Simple Related Posts', SIRP_DOMAIN ), 'manage_options', 'simple_related_posts', array( $this, 'option_page'));
	}

	public function option_page() {
	?>
<div class="wrap">
	
<h2><?php _e( 'Simple Related Posts', SIRP_DOMAIN ); ?></h2>
	
<form action="options.php" method="post">
<?php settings_fields( 'sirp_options' ); ?>
<?php do_settings_sections( 'sirp' ); ?>
	
<p class="submit"><input name="Submit" type="submit" value="<?php _e( 'save', SIRP_DOMAIN ) ?>" class="button-primary" /></p>
</form>
	
</div>
<?php
	}
	
	public function admin_init() {
		register_setting( 'sirp_options', 'sirp_options', array( $this, 'sirp_options_validate' ) );	
		add_settings_section( 'sirp_main', __( 'Configuration', SIRP_DOMAIN ), array( $this, 'sirp_section_text' ), 'sirp' );	
		add_settings_field( 'sirp_target', __( 'Display method', SIRP_DOMAIN ), array( $this, 'setting_sirp_target' ), 'sirp', 'sirp_main' );
		add_settings_field( 'sirp_display_num',  __( 'Views', SIRP_DOMAIN ), array( $this, 'setting_sirp_display_num' ), 'sirp', 'sirp_main' );
		add_settings_field( 'sirp_title',  __( 'Related Posts Title', SIRP_DOMAIN ), array( $this, 'setting_sirp_title' ), 'sirp', 'sirp_main' );
		add_settings_field( 'sirp_post_content',  __( 'Post Content', SIRP_DOMAIN ), array( $this, 'setting_sirp_post_content' ), 'sirp', 'sirp_main' );
		add_settings_field( 'sirp_post_thumbnail',  __( 'Post Thumbnail', SIRP_DOMAIN ), array( $this, 'setting_sirp_post_thumbnail' ), 'sirp', 'sirp_main' );
		add_settings_field( 'sirp_original_css',  __( 'CSS', SIRP_DOMAIN ), array( $this, 'setting_sirp_original_css' ), 'sirp', 'sirp_main' );
		//add_settings_field( 'sirp_rss_post_content',  __( 'RSS', SIRP_DOMAIN ), array( $this, 'setting_sirp_rss_post_content' ), 'sirp', 'sirp_main' );
	}
	
	public function sirp_section_text() {
	}
	
	public function setting_sirp_target() {
		$options = get_option( 'sirp_options' );
	
		echo '<label for="sirp_target_tag"><input id="sirp_target_tag" name="sirp_options[target]" type="radio" '.checked( $options['target'], 'Simple_Related_Posts_Tag', false ).' value="Simple_Related_Posts_Tag" />' . __( 'View Related Posts based on tags', SIRP_DOMAIN ) . '</label><br />';
		echo '<label for="sirp_target_category"><input id="sirp_target_category" name="sirp_options[target]" type="radio" '.checked( $options['target'], 'Simple_Related_Posts_Category', false ).' value="Simple_Related_Posts_Category" />' . __( 'View Related Posts based on categoris', SIRP_DOMAIN ) . '</label><br />';
		echo '<label for="sirp_target_category_tag"><input id="sirp_target_category_tag" name="sirp_options[target]" type="radio" '.checked( $options['target'], 'Simple_Related_Posts_Category_Tag', false ).' value="Simple_Related_Posts_Category_Tag" />' . __( 'View Related Posts based on categoris and tag', SIRP_DOMAIN ) . '</label><br />';
		do_action( 'sirp_target_option', $options['target'] );
	}
	
	public function setting_sirp_display_num() {
		$options = get_option( 'sirp_options' );

		echo '<input id="sirp_display_num" name="sirp_options[display_num]" size="2" type="text" value="' . esc_attr( $options['display_num'] ) . '" />';
	}
	
	public function setting_sirp_title() {
		$options = get_option( 'sirp_options', __('Related Posts', SIRP_DOMAIN ) );
		

		echo '<input id="sirp_title" name="sirp_options[title]" size="15" type="text" value="' . esc_attr( $options['title'] ) . '" />';
	}

	public function setting_sirp_post_content() {
		$options = get_option( 'sirp_options' );

		echo '<label for="sirp_post_content"><input id="sirp_post_content" name="sirp_options[post_content]" type="checkbox" '.checked( $options['post_content'], 1, false ).' value="1" />' . __( 'Allow to display related posts at the end of the post content automatically', SIRP_DOMAIN ) . '</label>';
	}
	
	public function setting_sirp_post_thumbnail() {
		$options = get_option( 'sirp_options' );

		echo '<label for="sirp_post_thumbnail"><input id="sirp_post_thumbnail" name="sirp_options[post_thumbnail]" type="checkbox" '.checked( $options['post_thumbnail'], 1, false ).' value="1" />' . __( 'Allow to display a thumbnail to related posts', SIRP_DOMAIN ) . '</label>';
	}
	
	public function setting_sirp_original_css() {
		$options = get_option( 'sirp_options' );

		echo '<label for="sirp_original_css"><input id="sirp_original_css" name="sirp_options[original_css]" type="checkbox" '.checked( $options['original_css'], 1, false ).' value="1" />' . __( 'Use css', SIRP_DOMAIN ) . '</label>';
	}

	public function setting_sirp_rss_post_content() {
		$options = get_option( 'sirp_options' );

		echo '<label for="sirp_rss_post_content"><input id="sirp_rss_post_content" name="sirp_options[rss_post_content]" type="checkbox" '.checked( $options['rss_post_content'], 1, false ).' value="1" />' . __( 'Allow to add related posts at the end of the RSS post content automatically', SIRP_DOMAIN ) . '</label>';
	}
	
	public function sirp_options_validate( $input ) {
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
	$options = get_option( 'sirp_options' );
?>
<script type="text/javascript">
	var sirp_display_num = <?php echo esc_js( $options['display_num'] ); ?>;
</script>
<?php
}
}
new Simple_Related_Posts_Admin_Options();