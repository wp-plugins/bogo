<?php

function bogo_get_post_locale( $post_id ) {
	$locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $locale ) )
		$locale = WPLANG;

	if ( empty( $locale ) )
		$locale = 'en_US';

	return $locale;
}

function bogo_get_post_translations( $post_id = 0 ) {
	$post = get_post( $post_id );

	if ( ! $post )
		return false;

	$args = array(
		'posts_per_page' => -1,
		'post_status' => 'any',
		'post_type' => $post->post_type,
		'meta_key' => '_original_post',
		'meta_value' => $post->ID );

	$q = new WP_Query();
	return $q->query( $args );
}

?>