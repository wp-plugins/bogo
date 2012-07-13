<?php

add_filter( 'post_link', 'bogo_post_link', 10, 3 );

function bogo_post_link( $permalink, $post, $leavename ) {
	$default_locale = bogo_get_default_locale();
	$locale = bogo_get_post_locale( $post->ID );

	if ( $default_locale == $locale )
		return $permalink;

	$permalink_structure = get_option( 'permalink_structure' );

	if ( empty( $permalink_structure )
	|| in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
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