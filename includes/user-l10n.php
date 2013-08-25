<?php

add_action( 'personal_options_update', 'bogo_update_user_option' );

function bogo_update_user_option() {
	if ( ! isset( $_POST['own_locale'] ) || empty( $_POST['own_locale'] ) )
		$locale = null;
	else
		$locale = trim( $_POST['own_locale'] );

	update_user_option( get_current_user_id(), 'locale', $locale, true );
}

add_action( 'personal_options', 'bogo_select_own_locale' );

function bogo_select_own_locale() {
	$available_languages = bogo_available_languages( 'orderby=value' );

	$selected = bogo_get_user_locale();

?>

<!-- Bogo plugin -->
<tr>
<th scope="row"><?php echo esc_html( __( 'Locale', 'bogo' ) ); ?></th>
<td>
<select name="own_locale">
<?php foreach ( $available_languages as $locale => $lang ) : ?>
<option value="<?php echo esc_attr( $locale ); ?>" <?php selected( $locale, $selected ); ?>><?php echo esc_html( $lang ); ?></option>
<?php endforeach; ?>
</select>
</td>
</tr>

<?php
}

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

?>