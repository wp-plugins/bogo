<?php

function bogo_get_post_locale( $post_id ) {
	$locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $locale ) )
		$locale = bogo_get_default_locale();

	return $locale;
}

function bogo_get_post_translations( $post_id = 0 ) {
	$post = get_post( $post_id );

	if ( ! $post )
		return false;

	if ( 'auto-draft' == $post->post_status ) {
		if ( ! empty( $_REQUEST['original_post'] ) ) {
			$original = get_post_meta( $_REQUEST['original_post'], '_original_post', true );

			if ( empty( $original ) )
				$original = (int) $_REQUEST['original_post'];
		} else {
			return false;
		}
	} else {
		$original = get_post_meta( $post->ID, '_original_post', true );
	}

	if ( empty( $original ) )
		$original = $post->ID;

	$args = array(
		'bogo_suppress_locale_query' => true,
		'posts_per_page' => -1,
		'post_status' => 'any',
		'post_type' => $post->post_type,
		'meta_key' => '_original_post',
		'meta_value' => $original );

	$q = new WP_Query();
	$posts = $q->query( $args );

	$translations = array();

	$original_post_status = get_post_status( $original );

	if ( $original != $post->ID && $original_post_status && 'trash' != $original_post_status ) {
		$locale = bogo_get_post_locale( $original );
		$translations[$locale] = get_post( $original );
	}

	$available_languages = bogo_available_languages();

	foreach ( $posts as $p ) {
		if ( $p->ID == $post->ID )
			continue;

		$locale = bogo_get_post_locale( $p->ID );

		if ( ! isset( $available_languages[$locale] ) )
			continue;

		if ( ! isset( $translations[$locale] ) )
			$translations[$locale] = $p;
	}

	return array_filter( $translations );
}

?>