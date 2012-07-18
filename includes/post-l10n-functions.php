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

	$original = get_post_meta( $post->ID, '_original_post', true );

	if ( ! empty( $original ) )
		$original = get_post( $original );

	if ( empty( $original ) || 'trash' == get_post_status( $original ) )
		$original = $post;

	$args = array(
		'posts_per_page' => -1,
		'post_status' => 'any',
		'post_type' => $post->post_type,
		'meta_key' => '_original_post',
		'meta_value' => (int) $original->ID );

	$q = new WP_Query();
	$posts = $q->query( $args );

	$translations = array();

	foreach ( $posts as $p )
		$translations[bogo_get_post_locale( $p->ID )] = $p;

	foreach ( (array) get_post_meta( $original->ID, '_translations', true ) as $key => $value ) {
		if ( ! isset( $translations[$key] ) ) {
			$translation = get_post( $value );

			if ( ! empty( $translation ) && 'trash' != get_post_status( $translation ) )
				$translations[$key] = $translation;
		}
	}

	$translations[bogo_get_post_locale( $original->ID )] = $original;
	$translations[bogo_get_post_locale( $post->ID )] = $post;

	$available_languages = bogo_available_languages();

	foreach ( $translations as $key => $value ) {
		if ( ! isset( $available_languages[$key] ) )
			unset( $translations[$key] );
	}

	return array_filter( $translations );
}

?>