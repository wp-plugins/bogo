<?php

function bogo_get_user_locale() {
	$locale = get_user_option( 'locale' );

	if ( empty( $locale ) && defined( 'WPLANG' ) )
		$locale = WPLANG;

	if ( empty( $locale ) )
		$locale = 'en_US';

	return $locale;
}

?>