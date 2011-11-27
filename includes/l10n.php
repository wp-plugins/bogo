<?php

add_action( 'init', 'bogo_add_l10n_custom_post_type' );

function bogo_add_l10n_custom_post_type() {
	$labels = array(
		'name' => __( 'Translations', 'bogo' ),
		'singular_name' => __( 'Translation', 'bogo' ),
		'add_new_item' => __( 'Add New Translation', 'bogo' ),
		'edit_item' => __( 'Edit Translation', 'bogo' ),
		'new_item' => __( 'New Translation', 'bogo' ),
		'view_item' => __( 'View Translation', 'bogo' ),
		'search_items' => __( 'Search Translations', 'bogo' ),
		'not_found' => __( 'No translations found', 'bogo' ),
		'not_found_in_trash' => __( 'No translations found in Trash', 'bogo' ) );

	$supports = array( 'title', 'editor', 'author',
		'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions' );

	$args = array(
		'labels' => $labels,
		'show_ui' => true,
		'supports' => $supports );

	register_post_type( 'l10n', $args );
}

?>