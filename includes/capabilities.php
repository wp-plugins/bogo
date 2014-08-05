<?php

add_filter( 'user_has_cap', 'bogo_user_has_cap', 10, 4 );

function bogo_user_has_cap( $capabilities, $caps_in_question, $args, $user ) {
	if ( in_array( 'bogo_access_locale', $caps_in_question ) ) {

		if ( ! empty( $capabilities['manage_options'] ) ) {
			$capabilities['bogo_access_locale'] = true;
			return $capabilities;
		}

		$locale = $args[2];
		$accessible_locales = get_user_meta( $user->ID, 'accessible_locale' );

		if ( empty( $accessible_locales ) ) {
			$capabilities['bogo_access_locale'] = true;
		} else {
			$accessible_locales = bogo_filter_locales( $accessible_locales );

			if ( in_array( $locale, $accessible_locales ) ) {
				$capabilities['bogo_access_locale'] = true;
			}
		}
	}

	return $capabilities;
}

?>