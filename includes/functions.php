<?php

function bogo_get_default_locale() {
	if ( defined( 'WPLANG' ) )
		$locale = WPLANG;

	if ( is_multisite() ) {
		if ( defined( 'WP_INSTALLING' ) || ( false === $ms_locale = get_option( 'WPLANG' ) ) )
			$ms_locale = get_site_option( 'WPLANG' );

		if ( $ms_locale !== false )
			$locale = $ms_locale;
	}

	if ( empty( $locale ) )
		$locale = 'en_US';

	return $locale;
}

function bogo_languages( $locale = '' ) {
	$languages = array(
		'af' => __( 'Afrikaans', 'bogo' ),
		'ar' => __( 'Arabic', 'bogo' ),
		'be_BY' => __( 'Belarusian', 'bogo' ),
		'bg_BG' => __( 'Bulgarian', 'bogo' ),
		'bn_BD' => __( 'Bangla', 'bogo' ),
		'ca' => __( 'Catalan', 'bogo' ),
		'cs_CZ' => __( 'Czech', 'bogo' ),
		'cy' => __( 'Welsh', 'bogo' ),
		'da_DK' => __( 'Danish', 'bogo' ),
		'de_DE' => __( 'German', 'bogo' ),
		'el' => __( 'Greek', 'bogo' ),
		'en_GB' => __( 'British English', 'bogo' ),
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
		'vi' => __( 'Vietnamese', 'bogo' ),
		'zh_CN' => __( 'Chinese', 'bogo' ),
		'zh_TW' => __( 'Traditional Chinese', 'bogo' )
	);

	$languages = apply_filters( 'bogo_languages', $languages );

	if ( empty( $locale ) )
		return $languages;

	if ( ! empty( $languages[$locale] ) )
		return $languages[$locale];

	return null;		
}

function bogo_available_languages( $args = '' ) {
	$defaults = array(
		'exclude' => array(),
		'orderby' => 'key',
		'order' => 'ASC' );

	$args = wp_parse_args( $args, $defaults );

	$langs = array();

	$installed_locales = get_available_languages();
	$installed_locales[] = 'en_US';
	$installed_locales = array_unique( $installed_locales );

	foreach ( $installed_locales as $locale ) {
		if ( in_array( $locale, (array) $args['exclude'] ) )
			continue;

		$lang = bogo_languages( $locale );

		if ( empty( $lang ) )
			$lang = "[$locale]";

		$langs[$locale] = $lang;
	}

	if ( 'value' == $args['orderby'] ) {
		natcasesort( $langs );

		if ( 'DESC' == $args['order'] )
			$langs = array_reverse( $langs );
	} else {
		if ( 'DESC' == $args['order'] )
			krsort( $langs );
		else
			ksort( $langs );
	}

	$langs = apply_filters( 'bogo_available_languages', $langs, $args );

	return $langs;
}

function bogo_get_closest_locale( $var ) {
	$var = strtolower( $var );

	if ( ! preg_match( '/^([a-z]{2})(?:_([a-z]{2}))?/', $var, $matches ) )
		return false;

	$language_code = $matches[1];
	$region_code = isset( $matches[2] ) ? $matches[2] : '';

	$locales = array_keys( bogo_available_languages() );

	if ( $region_code ) {
		$locale = $language_code . '_' . strtoupper( $region_code );

		if ( false !== array_search( $locale, $locales ) )
			return $locale;
	}

	$locale = $language_code;

	if ( false !== array_search( $locale, $locales ) )
		return $locale;

	if ( $matches = preg_grep( "/^{$locale}_/", $locales ) )
		return array_shift( $matches );

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
			$qvalue = isset( $matches[2] ) ? 0 + $matches[2] : 1;

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

?>