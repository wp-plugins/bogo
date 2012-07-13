<?php

add_filter( 'post_link', 'bogo_post_link', 10, 3 );

function bogo_post_link( $permalink, $post, $leavename ) {
	$default_locale = bogo_get_default_locale();
	$locale = bogo_get_post_locale( $post->ID );

	if ( $default_locale == $locale )
		return $permalink;

	$sample = ( isset( $post->filter ) && 'sample' == $post->filter );

	$permalink_structure = get_option( 'permalink_structure' );

	if ( empty( $permalink_structure )
	|| ! $sample && in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
		return add_query_arg( array( 'lang' => $locale ), $permalink );

	$permalink = bogo_get_url_with_lang( $permalink, $locale );

	return $permalink;
}

add_filter( 'page_link', 'bogo_page_link', 10, 3 );

function bogo_page_link( $permalink, $id, $sample ) {
	if ( 'page' == get_option( 'show_on_front' ) && $id == get_option( 'page_on_front' ) )
		return $permalink;

	$default_locale = bogo_get_default_locale();
	$locale = bogo_get_post_locale( $id );

	if ( $default_locale == $locale )
		return $permalink;

	$post = get_post( $id );

	$permalink_structure = get_option( 'permalink_structure' );

	if ( empty( $permalink_structure )
	|| ! $sample && in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
		return add_query_arg( array( 'lang' => $locale ), $permalink );

	$permalink = bogo_get_url_with_lang( $permalink, $locale );

	return $permalink;
}

add_filter( 'post_type_link', 'bogo_post_type_link', 10, 4 );

function bogo_post_type_link( $permalink, $post, $leavename, $sample ) {
	$default_locale = bogo_get_default_locale();
	$locale = bogo_get_post_locale( $post->ID );

	if ( $default_locale == $locale )
		return $permalink;

	$permalink_structure = get_option( 'permalink_structure' );

	if ( empty( $permalink_structure )
	|| ! $sample && in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
		return add_query_arg( array( 'lang' => $locale ), $permalink );

	$permalink = bogo_get_url_with_lang( $permalink, $locale );

	return $permalink;
}

add_filter( 'year_link', 'bogo_year_link', 10, 2 );

function bogo_year_link( $link, $year ) {
	return bogo_get_general_link( $link );
}

add_filter( 'month_link', 'bogo_month_link', 10, 3 );

function bogo_month_link( $link, $year, $month ) {
	return bogo_get_general_link( $link );
}

add_filter( 'day_link', 'bogo_day_link', 10, 4 );

function bogo_day_link( $link, $year, $month, $day ) {
	return bogo_get_general_link( $link );
}

add_filter( 'feed_link', 'bogo_feed_link', 10, 2 );

function bogo_feed_link( $link, $feed ) {
	return bogo_get_general_link( $link );
}

add_filter( 'author_feed_link', 'bogo_author_feed_link', 10, 2 );

function bogo_author_feed_link( $link, $feed ) {
	return bogo_get_general_link( $link );
}

add_filter( 'category_feed_link', 'bogo_category_feed_link', 10, 2 );

function bogo_category_feed_link( $link, $feed ) {
	return bogo_get_general_link( $link );
}

add_filter( 'taxonomy_feed_link', 'bogo_taxonomy_feed_link', 10, 3 );

function bogo_taxonomy_feed_link( $link, $feed, $taxonomy ) {
	return bogo_get_general_link( $link );
}

add_filter( 'post_type_archive_link', 'bogo_post_type_archive_link', 10, 2 );

function bogo_post_type_archive_link( $link, $post_type ) {
	return bogo_get_general_link( $link );
}

add_filter( 'post_type_archive_feed_link', 'bogo_post_type_archive_feed_link', 10, 2 );

function bogo_post_type_archive_feed_link( $link, $feed ) {
	return bogo_get_general_link( $link );
}

function bogo_get_general_link( $link ) {
	$default_locale = bogo_get_default_locale();
	$locale = get_locale();

	if ( $default_locale == $locale )
		return $link;

	$permalink_structure = get_option( 'permalink_structure' );

	if ( empty( $permalink_structure ) )
		return add_query_arg( array( 'lang' => $locale ), $link );

	return bogo_get_url_with_lang( $link, $locale );
}

function bogo_get_url_with_lang( $url, $lang ) {
	$home = trailingslashit( home_url() );

	if ( 0 !== strpos( $url, $home ) )
		return $url;

	$url = substr_replace( $url, $home . $lang . '/', 0, strlen( $home ) );

	return $url;
}

?>