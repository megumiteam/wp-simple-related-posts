<?php
add_filter( 'sirp_before_post_title', 'sirp_default_before_post_title', 10, 2 );
function sirp_default_before_post_title($html, $post_id) {
	$options = get_option( 'sirp_options' );
	if ( !isset($options['post_thumbnail']) || $options['post_thumbnail'] != 1 ) 
		return $html;
		
	if ( has_post_thumbnail($post_id) ) {
		$attachment_id = get_post_thumbnail_id( $post_id );
		$image_attributes = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
		$html .= '<p class="thumb" style="width:' . $image_attributes[1] . 'px;" >' . get_the_post_thumbnail( $post_id, 'thumbnail' ). '</p>';
	}

	return $html; 
}

add_action( 'wp_head', 'sirp_add_stylesheet' );
function sirp_add_stylesheet() {
	$options = get_option( 'sirp_options' );
	if ( !isset($options['original_css']) || $options['original_css'] != 1 ) 
		return;

?>
<style type="text/css">
<?php echo $options['original_css_content']; ?>
</style>
<?php
}

add_filter( 'sirp_title', 'sirp_title' );
function sirp_title($title) {
	$options = get_option( 'sirp_options' );
	if ( isset($options['title']) && $options['title'] != '' )
		return $options['title'];
		
	return $title;
}