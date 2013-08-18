<?php

add_action( 'parse_query', 'bogo_parse_query' );

function bogo_parse_query( $query ) {
	$qv = &$query->query_vars;

	if ( ! empty( $qv['bogo_suppress_locale_query'] ) )
		return;

	if ( isset( $qv['post_type'] ) && 'any' != $qv['post_type'] ) {
		$localizable = array_filter( (array) $qv['post_type'], 'bogo_is_localizable_post_type' );

		if ( empty( $localizable ) ) {
			$qv['bogo_suppress_locale_query'] = true;
			return;
		}
	}

	$lang = isset( $qv['lang'] ) ? $qv['lang'] : '';

	if ( is_admin() ) {
		$locale = $lang;
	} else {
		if ( $lang )
			$locale = bogo_get_closest_locale( $lang );
		else
			$locale = get_locale();

		if ( empty( $locale ) )
			$locale = bogo_get_default_locale();
	}

	if ( empty( $locale ) || ! bogo_is_available_locale( $locale ) ) {
		$qv['bogo_suppress_locale_query'] = true;
		return;
	}

	$qv['lang'] = $locale;

	if ( is_admin() )
		return;

	if ( '' != $qv['pagename'] ) {
		$query->queried_object = bogo_get_page_by_path( $qv['pagename'], $locale );

		if ( ! empty( $query->queried_object ) )
			$query->queried_object_id = (int) $query->queried_object->ID;
		else
			unset( $query->queried_object );

		if  ( 'page' == get_option( 'show_on_front' )
		&& isset( $query->queried_object_id )
		&& $query->queried_object_id == get_option( 'page_for_posts' ) ) {
			$query->is_page = false;
			$query->is_home = true;
			$query->is_posts_page = true;
		}
	}
}

add_filter( 'posts_join', 'bogo_posts_join', 10, 2 );

function bogo_posts_join( $join, $query ) {
	global $wpdb;

	$qv = &$query->query_vars;

	if ( ! empty( $qv['bogo_suppress_locale_query'] ) )
		return $join;

	$locale = empty( $qv['lang'] ) ? '' : $qv['lang'];

	if ( ! bogo_is_available_locale( $locale ) )
		return $join;

	if ( ! $meta_table = _get_meta_table( 'post' ) )
		return $join;

	$join .= " LEFT JOIN $meta_table AS postmeta_bogo ON ($wpdb->posts.ID = postmeta_bogo.post_id AND postmeta_bogo.meta_key = '_locale')";

	return $join;
}

add_filter( 'posts_where', 'bogo_posts_where', 10, 2 );

function bogo_posts_where( $where, $query ) {
	global $wpdb;

	$qv = &$query->query_vars;

	if ( ! empty( $qv['bogo_suppress_locale_query'] ) )
		return $where;

	$locale = empty( $qv['lang'] ) ? '' : $qv['lang'];

	if ( ! bogo_is_available_locale( $locale ) )
		return $where;

	if ( ! $meta_table = _get_meta_table( 'post' ) )
		return $where;

	$where .= " AND (1=0";

	$where .= $wpdb->prepare( " OR postmeta_bogo.meta_value LIKE %s", $locale );

	if ( bogo_is_default_locale( $locale ) )
		$where .= " OR postmeta_bogo.meta_id IS NULL";

	$where .= ")";

	return $where;
}

add_filter( 'option_sticky_posts', 'bogo_option_sticky_posts' );

function bogo_option_sticky_posts( $posts ) {
	if ( is_admin() )
		return $posts;

	$locale = get_locale();

	foreach ( $posts as $key => $post_id ) {
		if ( $locale != bogo_get_post_locale( $post_id ) )
			unset( $posts[$key] );
	}

	return $posts;
}

?>