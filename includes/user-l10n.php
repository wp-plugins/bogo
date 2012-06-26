<?php

add_action( 'personal_options_update', 'bogo_update_user_option' );
add_action( 'personal_options', 'bogo_select_own_locale' );

function bogo_update_user_option() {
	global $current_user;

	if ( ! isset( $_POST['own_locale'] ) || empty( $_POST['own_locale'] ) )
		$locale = null;
	else
		$locale = trim( $_POST['own_locale'] );

	update_user_option( $current_user->ID, 'locale', $locale, true );
}

function bogo_select_own_locale() {
	$available_languages = bogo_available_languages();

	$selected = get_user_option( 'locale' );

	if ( empty( $selected ) && defined( 'WPLANG' ) )
		$selected = WPLANG;

	if ( empty( $selected ) )
		$selected = 'en_US';
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

?>