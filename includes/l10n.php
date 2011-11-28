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

	$supports = array( 'title', 'editor', 'author',
		'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions' );

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

?>