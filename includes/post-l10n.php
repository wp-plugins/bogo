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

?>