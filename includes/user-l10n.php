<?php

add_filter( 'locale', 'bogo_locale' );
add_action( 'personal_options_update', 'bogo_update_user_option' );
add_action( 'personal_options', 'bogo_select_own_locale' );

function bogo_locale( $locale ) {
	if ( ! function_exists( 'wp_get_current_user' ) )
		return $locale;

	$locale_option = get_user_option( 'locale' );

	if ( ! empty( $locale_option ) )
		$locale = $locale_option;

	return $locale;
}

function bogo_update_user_option() {
	global $current_user;

	if ( ! isset( $_POST['own_locale'] ) || empty( $_POST['own_locale'] ) )
		$locale = null;
	else
		$locale = trim( $_POST['own_locale'] );

	update_user_option( $current_user->id, 'locale', $locale, true );
}

function bogo_select_own_locale() {
	$languages = bogo_languages();

	$installed_locales = bogo_installed_locales();
	$installed_locales[] = 'en_US';
	$installed_locales = array_unique( $installed_locales );

	$locales = array();
	foreach ( $installed_locales as $il ) {
		$label = array_key_exists( $il, $languages ) ? $languages[$il] : "[$il]";
		$locales[] = array( $il, $label );
	}

	usort( $locales, create_function( '$a, $b', 'return strnatcmp($a[1], $b[1]);' ) );

	$selected = get_user_option( 'locale' );
	if ( empty( $selected ) && defined( 'WPLANG' ) )
		$selected = WPLANG;
	if ( empty( $selected ) )
		$selected = 'en_US';

?>

<!-- Bogo plugin -->
<tr>
<th scope="row"><?php _e( 'Locale', 'bogo' ); ?></th>
<td>
<select name="own_locale">
<?php foreach ( $locales as $locale ) : ?>
<option value="<?php echo $locale[0]; ?>" <?php selected( $locale[0], $selected ); ?>><?php echo $locale[1]; ?></option>
<?php endforeach; ?>
</select>
</td>
</tr>

<?php
}

function bogo_installed_locales() {
	$locales = array();

	if ( $handle = opendir( WP_LANG_DIR ) ) {
		rewinddir( $handle );
		while ( false !== ( $file = readdir( $handle ) ) ) {
			$filename = basename( $file );

			// exceptional case
			if ( false !== strpos( $filename, 'continents-cities' ) )
				continue;

			if ( preg_match( '/^([^.]+)\.mo$/', $filename, $regs ) ) {
				$locale = $regs[1];
				$locales[] = $locale;
			}
		}
		closedir( $handle );
	}

	return $locales;
}

?>