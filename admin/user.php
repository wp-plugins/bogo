<?php

add_action( 'personal_options_update', 'bogo_update_user_option' );
add_action( 'edit_user_profile_update', 'bogo_update_user_option' );

function bogo_update_user_option( $user_id ) {
	if ( ! empty( $_POST['setting_bogo_accessible_locales'] ) ) {
		delete_user_meta( $user_id, 'accessible_locale' );

		if ( isset( $_POST['bogo_accessible_locales'] ) ) {
			$locales = (array) $_POST['bogo_accessible_locales'];
			$locales = array_intersect( $locales,
				array_keys( bogo_available_languages() ) );

			foreach ( $locales as $locale ) {
				add_user_meta( $user_id, 'accessible_locale', $locale );
			}
		}

		if ( ! metadata_exists( 'user', $user_id, 'accessible_locale' ) ) {
			add_user_meta( $user_id, 'accessible_locale', 'zxx' );
			// zxx is a special code in ISO 639-2
		}
	}

	if ( isset( $_POST['own_locale'] ) ) {
		$locale = trim( $_POST['own_locale'] );

		if ( bogo_is_available_locale( $locale ) ) {
			update_user_option( $user_id, 'locale', $locale, true );
		}
	}
}

add_action( 'personal_options', 'bogo_set_locale_options' );

function bogo_set_locale_options( $profileuser ) {
?>

<!-- Bogo plugin -->
<tr>
<th scope="row"><?php echo esc_html( __( 'Locale', 'bogo' ) ); ?></th>
<td>
<?php
	if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
		bogo_select_own_locale( $profileuser );
	} else {
		bogo_set_accessible_locales( $profileuser );
	}
?>
</td>
</tr>

<?php
}

function bogo_set_accessible_locales( $profileuser ) {
	$available_languages = bogo_available_languages( 'orderby=value' );
	$accessible_locales = get_user_meta( $profileuser->ID, 'accessible_locale' );

	if ( empty( $accessible_locales ) ) {
		$accessible_locales = array_keys( $available_languages );
	} else {
		$accessible_locales = array_intersect( $accessible_locales,
			array_keys( $available_languages ) );
	}

?>
<input type="hidden" name="setting_bogo_accessible_locales" value="1" />
<span class="description"><?php echo esc_html( __( 'This user is allowed to access the following locales:', 'bogo' ) ); ?></span><br />
<fieldset class="bogo-locale-options">

<?php
	foreach ( $available_languages as $locale => $language ) :
		$checked = in_array( $locale, $accessible_locales );
		$id_attr = 'bogo_accessible_locale-' . $locale;
?>
<label class="bogo-locale-option<?php echo $checked ? ' checked' : ''; ?>" for="<?php echo $id_attr; ?>">
<input type="checkbox" id="<?php echo $id_attr; ?>" name="bogo_accessible_locales[]" value="<?php echo esc_attr( $locale ); ?>"<?php echo $checked ? ' checked="checked"' : ''; ?> /><?php echo esc_html( $language ); ?>
</label>
<?php
	endforeach;
?>
</fieldset>
<?php
}

function bogo_select_own_locale( $profileuser ) {
	$available_languages = bogo_available_languages( 'orderby=value' );
	$selected = bogo_get_user_locale( $profileuser->ID );

?>
<select name="own_locale">
<?php foreach ( $available_languages as $locale => $lang ) : ?>
<option value="<?php echo esc_attr( $locale ); ?>" <?php selected( $locale, $selected ); ?>><?php echo esc_html( $lang ); ?></option>
<?php endforeach; ?>
</select>
<?php
}

?>