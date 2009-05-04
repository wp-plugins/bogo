<?php
/*
Plugin Name: Bogo
Plugin URI: http://ideasilo.wordpress.com/2009/05/05/bogo/
Description: Bogo allows each user to choose their locale for the admin panel.
Author: Takayuki Miyoshi
Version: 1.0
Author URI: http://ideasilo.wordpress.com/
*/

/*  Copyright 2007-2009 Takayuki Miyoshi (email: takayukister at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_filter( 'locale', 'bogo_locale' );
add_action( 'init', 'bogo_load_plugin_textdomain' );
add_action( 'personal_options_update', 'bogo_update_user_option' );
add_action( 'personal_options', 'bogo_select_own_locale' );

function bogo_locale( $locale ) {
	$locale_option = get_user_option( 'locale' );

	if ( ! empty( $locale_option ) )
		$locale = $locale_option;

	return $locale;
}

function bogo_load_plugin_textdomain() {
	load_plugin_textdomain( 'bogo', 'wp-content/plugins/bogo/languages', 'bogo/languages' );
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
			if ( preg_match( '/^([^.]+)\.mo$/', $filename, $regs ) ) {
				$locale = $regs[1];
				$locales[] = $locale;
			}
		}
		closedir( $handle );
	}

	return $locales;
}

function bogo_languages() {
	$languages = array(
		'af' => __( 'Afrikaans', 'bogo' ),
		'ar' => __( 'Arabic', 'bogo' ),
		'be_BY' => __( 'Belarusian', 'bogo' ),
		'bg_BG' => __( 'Bulgarian', 'bogo' ),
		'bn_BD' => __( 'Bengali', 'bogo' ),
		'ca' => __( 'Catalan', 'bogo' ),
		'cs_CZ' => __( 'Czech', 'bogo' ),
		'cy' => __( 'Welsh', 'bogo' ),
		'da_DK' => __( 'Danish', 'bogo' ),
		'de_DE' => __( 'German', 'bogo' ),
		'el' => __( 'Greek', 'bogo' ),
		'en_US' => __( 'English', 'bogo' ),
		'eo' => __( 'Esperanto', 'bogo' ),
		'es_ES' => __( 'Spanish', 'bogo' ),
		'et' => __( 'Estonian', 'bogo' ),
		'eu' => __( 'Basque', 'bogo' ),
		'fa_IR' => __( 'Persian', 'bogo' ),
		'fi_FI' => __( 'Finnish', 'bogo' ),
		'fo' => __( 'Faroese', 'bogo' ),
		'fr_FR' => __( 'French', 'bogo' ),
		'ge_GE' => __( 'Georgian', 'bogo' ),
		'gl_ES' => __( 'Galician', 'bogo' ),
		'he_IL' => __( 'Hebrew', 'bogo' ),
		'hr' => __( 'Croatian', 'bogo' ),
		'hu_HU' => __( 'Hungarian', 'bogo' ),
		'id_ID' => __( 'Indonesian', 'bogo' ),
		'is_IS' => __( 'Icelandic', 'bogo' ),
		'it_IT' => __( 'Italian', 'bogo' ),
		'ja' => __( 'Japanese', 'bogo' ),
		'km_KH' => __( 'Khmer', 'bogo' ),
		'ko_KR' => __( 'Korean', 'bogo' ),
		'lt_LT' => __( 'Lithuanian', 'bogo' ),
		'lv' => __( 'Latvian', 'bogo' ),
		'mg_MG' => __( 'Malagasy', 'bogo' ),
		'mk_MK' => __( 'Macedonian', 'bogo' ),
		'mn_MN' => __( 'Mongolian', 'bogo' ),
		'ms_MY' => __( 'Malay', 'bogo' ),
		'nb_NO' => __( 'Norwegian', 'bogo' ),
		'ni_ID' => __( 'Nias', 'bogo' ),
		'nl_NL' => __( 'Dutch', 'bogo' ),
		'pl_PL' => __( 'Polish', 'bogo' ),
		'pt_BR' => __( 'Brazilian Portuguese', 'bogo' ),
		'pt_PT' => __( 'Portuguese', 'bogo' ),
		'ro' => __( 'Romanian', 'bogo' ),
		'ru_RU' => __( 'Russian', 'bogo' ),
		'si_LK' => __( 'Sinhala', 'bogo' ),
		'sk' => __( 'Slovak', 'bogo' ),
		'sl_SI' => __( 'Slovenian', 'bogo' ),
		'sq' => __( 'Albanian', 'bogo' ),
		'sr_CS' => __( 'Serbian', 'bogo' ),
		'sv_SE' => __( 'Swedish', 'bogo' ),
		'su_ID' => __( 'Sundanese', 'bogo' ),
		'tg' => __( 'Tajik', 'bogo' ),
		'th' => __( 'Thai', 'bogo' ),
		'tr' => __( 'Turkish', 'bogo' ),
		'uk_UA' => __( 'Ukrainian', 'bogo' ),
		'uz_UZ' => __( 'Uzbek', 'bogo' ),
		'vi' => __( 'Vietnamse', 'bogo' ),
		'zh_CN' => __( 'Chinese', 'bogo' ),
		'zh_TW' => __( 'Traditional Chinese', 'bogo' )
	);

	return $languages;
}

?>