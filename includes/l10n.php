<?php

add_action( 'init', 'bogo_add_l10n_custom_post_types', 11 );

function bogo_add_l10n_custom_post_types() {
	$labels = array(
		'name' => __( 'Translations', 'bogo' ),
		'singular_name' => __( 'Translation', 'bogo' ),
		'add_new_item' => __( 'Add New Translation', 'bogo' ),
		'edit_item' => __( 'Edit Translation', 'bogo' ),
		'new_item' => __( 'New Translation', 'bogo' ),
		'view_item' => __( 'View Translation', 'bogo' ),
		'search_items' => __( 'Search Translations', 'bogo' ),
		'not_found' => __( 'No translations found', 'bogo' ),
		'not_found_in_trash' => __( 'No translations found in Trash', 'bogo' ),
		'menu_name' => __( 'Translations', 'bogo' ) );

	$supports = array( 'title', 'editor', 'author', 'excerpt', 'custom-fields', 'revisions' );

	register_post_type( 'post_l10n', array(
		'labels' => $labels,
		'supports' => $supports,
		'show_ui' => true,
		'show_in_menu' => 'edit.php' ) );

	register_post_type( 'page_l10n', array(
		'labels' => $labels,
		'supports' => $supports,
		'show_ui' => true,
		'show_in_menu' => 'edit.php?post_type=page' ) );

	do_action( 'bogo_add_l10n_custom_post_types' );
}

add_filter( 'default_content', 'bogo_l10n_default_content' );
add_filter( 'default_title', 'bogo_l10n_default_content' );
add_filter( 'default_excerpt', 'bogo_l10n_default_content' );

function bogo_l10n_default_content( $post_content ) {
	if ( ! $parent_post = get_post( $_REQUEST['parent_id'] ) )
		return $post_content;

	$current_filter = current_filter();

	if ( 'default_content' == $current_filter )
		return $parent_post->post_content;

	if ( 'default_title' == $current_filter )
		return $parent_post->post_title;

	if ( 'default_excerpt' == $current_filter )
		return $parent_post->post_excerpt;

	return $post_content;
}

add_action( 'add_meta_boxes', 'bogo_add_l10n_meta_boxes', 10, 2 );

function bogo_add_l10n_meta_boxes( $post_type, $post ) {
	global $action;

	if ( '_l10n' == substr( $post_type, -5 ) || 'edit' != $action )
		return;

	add_meta_box( 'bogol10ndiv', __( 'Localization', 'bogo' ),
		'bogo_l10n_meta_box', $post_type, 'side', 'high' );
}

function bogo_l10n_meta_box( $post ) {
	$l10n_url = add_query_arg(
		array( 'post_type' => $post->post_type . '_l10n', 'parent_id' => $post->ID ),
		admin_url( 'post-new.php' ) );

?>
<div class="l10ndiv">
<p><?php echo esc_html( __( 'Add translation', 'bogo' ) ); ?>:
<select name="bogo_add_translation" id="bogo_add_translation">
<option value="">&ndash; <?php echo esc_html( __( 'Select language', 'bogo' ) ); ?> &ndash;</option>
<?php foreach ( bogo_languages() as $locale => $language ) : ?>
<option value="<?php echo esc_attr( $locale ); ?>"><?php echo esc_html( $language ); ?></option>
<?php endforeach; ?>
</select>
</p>
</div>
<script type="text/javascript">
/* <![CDATA[ */
(function($) {
$(function() {
	$('#bogo_add_translation').change(function() {
		if ($(this).val())
			window.open('<?php echo esc_url_raw( $l10n_url . '&locale=' ); ?>' + $(this).val());
	});
});
})(jQuery);
/* ]]> */
</script>
<?php
}

?>