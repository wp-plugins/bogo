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

function bogo_get_url_with_lang( $url, $lang ) {
	$home = trailingslashit( home_url() );

	if ( 0 !== strpos( $url, $home ) )
		return $url;

	$url = substr_replace( $url, $home . $lang . '/', 0, strlen( $home ) );

	return $url;
}

?>