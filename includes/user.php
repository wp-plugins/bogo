<?php

/* Admin Bar */

add_action( 'admin_bar_menu', 'bogo_admin_bar_menu' );

function bogo_admin_bar_menu( $wp_admin_bar ) {
	$current_locale = bogo_get_user_locale();
	$current_language = bogo_get_language( $current_locale );

	if ( ! $current_language )
		$current_language = $current_locale;

	$wp_admin_bar->add_menu( array(
		'parent' => 'top-secondary',
		'id' => 'bogo-user-locale',
		'title' => '&#10004; ' . $current_language ) );

	$available_languages = bogo_available_languages(
		array( 'exclude' => array( $current_locale ) ) );

	foreach ( $available_languages as $locale => $lang ) {
		$url = admin_url( 'profile.php?action=bogo-switch-locale&locale=' . $locale );

		$url = add_query_arg(
			array( 'redirect_to' => urlencode( $_SERVER['REQUEST_URI'] ) ),
			$url );

		$url = wp_nonce_url( $url, 'bogo-switch-locale' );

		$wp_admin_bar->add_menu( array(
			'parent' => 'bogo-user-locale',
			'id' => 'bogo-user-locale-' . $locale,
			'title' => $lang,
			'href' => $url ) );
	}
}

add_action( 'admin_init', 'bogo_switch_user_locale' );

function bogo_switch_user_locale() {
	if ( empty( $_REQUEST['action'] ) || 'bogo-switch-locale' != $_REQUEST['action'] )
		return;

	check_admin_referer( 'bogo-switch-locale' );

	$locale = isset( $_REQUEST['locale'] ) ? $_REQUEST['locale'] : '';

	if ( ! bogo_is_available_locale( $locale ) || $locale == bogo_get_user_locale() )
		return;

	update_user_option( get_current_user_id(), 'locale', $locale, true );

	if ( ! empty( $_REQUEST['redirect_to'] ) ) {
		wp_safe_redirect( $_REQUEST['redirect_to'] );
		exit();
	}
}

function bogo_get_user_locale( $user_id = 0 ) {
	$locale = get_user_option( 'locale', $user_id );

	if ( empty( $locale ) )
		$locale = bogo_get_default_locale();

	return $locale;
}

?>