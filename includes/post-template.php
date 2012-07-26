<?php

add_filter( 'body_class', 'bogo_body_class', 10, 2 );

function bogo_body_class( $classes, $class ) {
	$locale = bogo_language_tag( get_locale() );
	$locale = esc_attr( $locale );

	if ( $locale && ! in_array( $locale, $classes ) )
		$classes[] = $locale;

	return $classes;
}

?>