<?php

/* Post Template */

add_filter( 'body_class', 'bogo_body_class', 10, 2 );

function bogo_body_class( $classes, $class ) {
	$locale = bogo_language_tag( get_locale() );
	$locale = esc_attr( $locale );

	if ( $locale && ! in_array( $locale, $classes ) )
		$classes[] = $locale;

	return $classes;
}

add_filter( 'post_class', 'bogo_post_class', 10, 3 );

function bogo_post_class( $classes, $class, $post_id ) {
	$locale = bogo_get_post_locale( $post_id );
	$locale = bogo_language_tag( $locale );
	$locale = esc_attr( $locale );

	if ( $locale && ! in_array( $locale, $classes ) )
		$classes[] = $locale;

	return $classes;
}

function bogo_get_post_locale( $post_id ) {
	$locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $locale ) )
		$locale = bogo_get_default_locale();

	return $locale;
}

function bogo_localizable_post_types() {
	$localizable = array( 'post', 'page' );

	return apply_filters( 'bogo_localizable_post_types', $localizable );
}

function bogo_is_localizable_post_type( $post_type ) {
	return ! empty( $post_type ) && in_array( $post_type, bogo_localizable_post_types() );
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

	foreach ( $posts as $p ) {
		if ( $p->ID == $post->ID )
			continue;

		$locale = bogo_get_post_locale( $p->ID );

		if ( ! bogo_is_available_locale( $locale ) )
			continue;

		if ( ! isset( $translations[$locale] ) )
			$translations[$locale] = $p;
	}

	return array_filter( $translations );
}

function bogo_get_page_by_path( $page_path, $locale = null, $post_type = 'page' ) {
	global $wpdb;

	if ( ! bogo_is_available_locale( $locale ) )
		$locale = bogo_get_default_locale();

	$page_path = rawurlencode( urldecode( $page_path ) );
	$page_path = str_replace( '%2F', '/', $page_path );
	$page_path = str_replace( '%20', ' ', $page_path );

	$parts = explode( '/', trim( $page_path, '/' ) );
	$parts = array_map( 'esc_sql', $parts );
	$parts = array_map( 'sanitize_title_for_query', $parts );

	$in_string = "'" . implode( "','", $parts ) . "'";
	$post_type_sql = $post_type;
	$wpdb->escape_by_ref( $post_type_sql );

	$q = "SELECT ID, post_name, post_parent FROM $wpdb->posts";
	$q .= " LEFT JOIN $wpdb->postmeta ON ID = $wpdb->postmeta.post_id AND meta_key = '_locale'";
	$q .= " WHERE 1=1";
	$q .= " AND post_name IN ($in_string)";
	$q .= " AND (post_type = '$post_type_sql' OR post_type = 'attachment')";
	$q .= " AND (1=0";
	$q .= $wpdb->prepare( " OR meta_value LIKE %s", $locale );
	$q .= bogo_is_default_locale( $locale ) ? " OR meta_id IS NULL" : "";
	$q .= ")";

	$pages = $wpdb->get_results( $q, OBJECT_K );

	$revparts = array_reverse( $parts );

	$foundid = 0;

	foreach ( (array) $pages as $page ) {
		if ( $page->post_name != $revparts[0] )
			continue;

		$count = 0;
		$p = $page;

		while ( $p->post_parent != 0 && isset( $pages[$p->post_parent] ) ) {
			$count++;
			$parent = $pages[$p->post_parent];

			if ( ! isset( $revparts[$count] ) || $parent->post_name != $revparts[$count] )
				break;

			$p = $parent;
		}

		if ( $p->post_parent == 0
		&& $count + 1 == count( $revparts )
		&& $p->post_name == $revparts[$count] ) {
			$foundid = $page->ID;
			break;
		}
	}

	if ( $foundid )
		return get_page( $foundid );

	return null;
}

?>