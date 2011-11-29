<?php

function bogo_languages( $locale = '' ) {
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

	$languages = apply_filters( 'bogo_languages', $languages );

	if ( ! empty( $locale ) )
		return $languages[$locale];

	return $languages;
}

function bogo_get_closest_locale( $var ) {
	$var = strtolower( $var );

	if ( ! preg_match( '/^([a-z]{2})(?:_([a-z]{2}))?/', $var, $matches ) )
		return false;

	$language_code = $matches[1];
	$region_code = $matches[2];

	$locales = array_keys( bogo_languages() );

	if ( $region_code ) {
		$locale = $language_code . '_' . strtoupper( $region_code );

		if ( false !== array_search( $locale, $locales ) )
			return $locale;
	}

	$locale = $language_code;

	if ( false !== array_search( $locale, $locales ) )
		return $locale;

	if ( $matches = preg_grep( "/^{$locale}_/", $locales ) )
		return $matches[0];

	return false;
}

function bogo_http_accept_languages() {
	if ( ! isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) )
		return false;

	$languages = array();

	foreach ( explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) as $lang ) {
		$lang = trim( strtolower( $lang ) );

		if ( preg_match( '/^([a-z-]+)(?:;q=([0-9.]+))?$/', $lang, $matches ) ) {
			$language_tag = $matches[1];
			$qvalue = ( isset( $matches[2] ) ? 0 : 1 ) + $matches[2];

			if ( preg_match( '/^([a-z]{2})(?:-([a-z]{2}))?$/', $language_tag, $matches ) ) {
				$language_tag = $matches[1];

				if ( isset( $matches[2] ) )
					$language_tag .= '_' . strtoupper( $matches[2] );

				$languages[$language_tag] = $qvalue;
			}
		}
	}

	natsort( $languages );

	return array_reverse( array_keys( $languages ) );
}

function bogo_get_post_locale( $post_id, $return_language = false ) {
	$locale = get_post_meta( $post_id, '_locale', true );

	if ( $return_language )
		return bogo_languages( $locale );

	return $locale;
}

function bogo_get_post_translations( $post_id ) {
	$translations = get_posts( array(
		'numberposts' => -1,
		'post_parent' => $post_id,
		'post_type' => 'l10n',
		'post_status' => 'any' ) );

	return $translations;
}

function bogo_locales_current_user_has_translated() {
	global $wpdb;

	$current_user = wp_get_current_user();

	$q = "SELECT meta_value FROM $wpdb->postmeta"
		. " INNER JOIN $wpdb->posts ON post_id = ID"
		. " WHERE meta_key LIKE '_locale' AND post_type LIKE 'l10n'"
		. $wpdb->prepare( " AND post_author = %d", $current_user->ID )
		. " GROUP BY meta_value ORDER BY count(meta_value) DESC";

	return $wpdb->get_col( $q );
}

?>