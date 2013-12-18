<?php

function bogo_get_user_locale( $user_id = 0 ) {
	$locale = get_user_option( 'locale', $user_id );

	if ( empty( $locale ) )
		$locale = bogo_get_default_locale();

	return $locale;
}

?>