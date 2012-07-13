<?php

function bogo_get_user_locale() {
	$locale = get_user_option( 'locale' );

	if ( empty( $locale ) )
		$locale = bogo_get_default_locale();

	return $locale;
}

?>