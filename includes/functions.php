<?php

function bogo_languages() {
	static $languages = array();

	if ( ! empty( $languages ) ) {
		return $languages;
	}

	$languages = array(
		'af' => __( 'Afrikaans', 'bogo' ),
		'am' => __( 'Amharic', 'bogo' ),
		'an' => __( 'Aragonese', 'bogo' ),
		'ar' => __( 'Arabic', 'bogo' ),
		'as' => __( 'Assamese', 'bogo' ),
		'az' => __( 'Azerbaijani', 'bogo' ),
		'be_BY' => __( 'Belarusian', 'bogo' ),
		'bg_BG' => __( 'Bulgarian', 'bogo' ),
		'bn_BD' => __( 'Bangla', 'bogo' ),
		'bn_IN' => __( 'Indian Bengali', 'bogo' ),
		'bo' => __( 'Tibetan', 'bogo' ),
		'bs' => __( 'Bosnian', 'bogo' ),
		'ca' => __( 'Catalan', 'bogo' ),
		'ckb' => __( 'Central Kurdish', 'bogo' ),
		'co' => __( 'Corsican', 'bogo' ),
		'cs_CZ' => __( 'Czech', 'bogo' ),
		'cy' => __( 'Welsh', 'bogo' ),
		'da_DK' => __( 'Danish', 'bogo' ),
		'de_DE' => __( 'German', 'bogo' ),
		'dv' => __( 'Dhivehi', 'bogo' ),
		'el' => __( 'Greek', 'bogo' ),
		'en_US' => __( 'English', 'bogo' ),
		'en_AU' => __( 'Australian English', 'bogo' ),
		'en_CA' => __( 'Canadian English', 'bogo' ),
		'en_GB' => __( 'British English', 'bogo' ),
		'eo' => __( 'Esperanto', 'bogo' ),
		'es_ES' => __( 'Spanish', 'bogo' ),
		'es_CL' => __( 'Chilean Spanish', 'bogo' ),
		'es_MX' => __( 'Mexican Spanish', 'bogo' ),
		'es_PE' => __( 'Peruvian Spanish', 'bogo' ),
		'es_VE' => __( 'Venezuelan Spanish', 'bogo' ),
		'et' => __( 'Estonian', 'bogo' ),
		'eu' => __( 'Basque', 'bogo' ),
		'fa_IR' => __( 'Persian', 'bogo' ),
		'fi' => __( 'Finnish', 'bogo' ),
		'fo' => __( 'Faroese', 'bogo' ),
		'fr_FR' => __( 'French', 'bogo' ),
		'fy' => __( 'Frisian', 'bogo' ),
		'ga' => __( 'Irish', 'bogo' ),
		'gd' => __( 'Scottish Gaelic', 'bogo' ),
		'gl_ES' => __( 'Galician', 'bogo' ),
		'gu' => __( 'Gujarati', 'bogo' ),
		'he_IL' => __( 'Hebrew', 'bogo' ),
		'hi_IN' => __( 'Hindi', 'bogo' ),
		'hr' => __( 'Croatian', 'bogo' ),
		'ht' => __( 'Haitian', 'bogo' ),
		'hu_HU' => __( 'Hungarian', 'bogo' ),
		'hy' => __( 'Armenian', 'bogo' ),
		'id_ID' => __( 'Indonesian', 'bogo' ),
		'is_IS' => __( 'Icelandic', 'bogo' ),
		'it_IT' => __( 'Italian', 'bogo' ),
		'ja' => __( 'Japanese', 'bogo' ),
		'jv_ID' => __( 'Javanese', 'bogo' ),
		'ka_GE' => __( 'Georgian', 'bogo' ),
		'kk' => __( 'Kazakh', 'bogo' ),
		'km_KH' => __( 'Khmer', 'bogo' ),
		'kn' => __( 'Kannada', 'bogo' ),
		'ko_KR' => __( 'Korean', 'bogo' ),
		'li' => __( 'Limburgish', 'bogo' ),
		'lo' => __( 'Lao', 'bogo' ),
		'lt_LT' => __( 'Lithuanian', 'bogo' ),
		'lv' => __( 'Latvian', 'bogo' ),
		'mg_MG' => __( 'Malagasy', 'bogo' ),
		'mk_MK' => __( 'Macedonian', 'bogo' ),
		'ml_IN' => __( 'Malayalam', 'bogo' ),
		'mn' => __( 'Mongolian', 'bogo' ),
		'mr' => __( 'Marathi', 'bogo' ),
		'ms_MY' => __( 'Malay', 'bogo' ),
		'mt_MT' => __( 'Maltese', 'bogo' ),
		'my_MM' => __( 'Burmese', 'bogo' ),
		'nb_NO' => __( 'Norwegian', 'bogo' ),
		'ne_NP' => __( 'Nepali', 'bogo' ),
		'ni_ID' => __( 'Nias', 'bogo' ),
		'nl_NL' => __( 'Dutch', 'bogo' ),
		'os' => __( 'Ossetic', 'bogo' ),
		'pa_IN' => __( 'Punjabi', 'bogo' ),
		'pl_PL' => __( 'Polish', 'bogo' ),
		'pt_BR' => __( 'Brazilian Portuguese', 'bogo' ),
		'pt_PT' => __( 'Portuguese', 'bogo' ),
		'ro_RO' => __( 'Romanian', 'bogo' ),
		'ru_RU' => __( 'Russian', 'bogo' ),
		'sa_IN' => __( 'Sanskrit', 'bogo' ),
		'si_LK' => __( 'Sinhala', 'bogo' ),
		'sk_SK' => __( 'Slovak', 'bogo' ),
		'sl_SI' => __( 'Slovenian', 'bogo' ),
		'so_SO' => __( 'Somali', 'bogo' ),
		'sq' => __( 'Albanian', 'bogo' ),
		'sr_RS' => __( 'Serbian', 'bogo' ),
		'sv_SE' => __( 'Swedish', 'bogo' ),
		'su_ID' => __( 'Sundanese', 'bogo' ),
		'sw' => __( 'Swahili', 'bogo' ),
		'ta_IN' => __( 'Tamil', 'bogo' ),
		'te' => __( 'Telugu', 'bogo' ),
		'tg' => __( 'Tajik', 'bogo' ),
		'th' => __( 'Thai', 'bogo' ),
		'tl' => __( 'Tagalog', 'bogo' ),
		'tr_TR' => __( 'Turkish', 'bogo' ),
		'tt_RU' => __( 'Tatar', 'bogo' ),
		'ug_CN' => __( 'Uighur', 'bogo' ),
		'uk' => __( 'Ukrainian', 'bogo' ),
		'ur' => __( 'Urdu', 'bogo' ),
		'uz_UZ' => __( 'Uzbek', 'bogo' ),
		'vi' => __( 'Vietnamese', 'bogo' ),
		'zh_CN' => __( 'Chinese', 'bogo' ),
		'zh_TW' => __( 'Traditional Chinese', 'bogo' )
	);

	$languages = apply_filters( 'bogo_languages', $languages );

	return $languages;
}

function bogo_get_language( $locale ) {
	$languages = bogo_languages();

	if ( isset( $languages[$locale] ) )
		return $languages[$locale];

	return null;
}

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

function bogo_is_default_locale( $locale ) {
	$default_locale = bogo_get_default_locale();

	return ! empty( $locale ) && $locale == bogo_get_default_locale();
}

function bogo_available_locales( $args = '' ) {
	$defaults = array(
		'exclude' => array(),
		'current_user_can_access' => false );

	$args = wp_parse_args( $args, $defaults );

	$installed_locales = get_available_languages();
	$installed_locales[] = bogo_get_default_locale();
	$installed_locales[] = 'en_US';
	$installed_locales = array_unique( $installed_locales );
	$installed_locales = array_filter( $installed_locales );

	$available_locales = array();

	foreach ( $installed_locales as $locale ) {
		if ( in_array( $locale, (array) $args['exclude'] ) ) {
			continue;
		}

		if ( $args['current_user_can_access']
		&& ! current_user_can( 'bogo_access_locale', $locale ) ) {
			continue;
		}

		$available_locales[] = $locale;
	}

	return $available_locales;
}

function bogo_available_languages( $args = '' ) {
	$defaults = array(
		'exclude' => array(),
		'orderby' => 'key',
		'order' => 'ASC',
		'current_user_can_access' => false );

	$args = wp_parse_args( $args, $defaults );

	$langs = array();

	$available_locales = bogo_available_locales( $args );

	foreach ( $available_locales as $locale ) {
		$lang = bogo_get_language( $locale );
		$langs[$locale] = empty( $lang ) ? "[$locale]" : $lang;
	}

	if ( 'value' == $args['orderby'] ) {
		natcasesort( $langs );

		if ( 'DESC' == $args['order'] ) {
			$langs = array_reverse( $langs );
		}
	} else {
		if ( 'DESC' == $args['order'] ) {
			krsort( $langs );
		} else {
			ksort( $langs );
		}
	}

	$langs = apply_filters( 'bogo_available_languages', $langs, $args );

	return $langs;
}

function bogo_is_available_locale( $locale ) {
	return ! empty( $locale ) && in_array( $locale, bogo_available_locales() );
}

function bogo_filter_locales( $locales, $filter = 'available' ) {
	return array_intersect( $locales, bogo_available_locales() );
}

function bogo_language_tag( $locale ) {
	// http://www.ietf.org/rfc/bcp/bcp47.txt
	$tag = preg_replace( '/[^0-9a-zA-Z]+/', '-', $locale );
	$tag = trim( $tag, '-' );

	return apply_filters( 'bogo_language_tag', $tag, $locale );
}

function bogo_lang_slug( $locale ) {
	$tag = bogo_language_tag( $locale );
	$slug = $tag;

	if ( false !== $pos = strpos( $tag, '-' ) )
		$slug = substr( $tag, 0, $pos );

	$variations = preg_grep( '/^' . $slug . '/', bogo_available_locales() );

	if ( 1 < count( $variations ) )
		$slug = $tag;

	return apply_filters( 'bogo_lang_slug', $slug, $locale );
}

function bogo_get_lang_regex() {
	$langs = array_map( 'bogo_lang_slug', bogo_available_locales() );
	$langs = array_filter( $langs );

	if ( empty( $langs ) )
		return '';

	return '(' . implode( '|', $langs ) . ')';
}

function bogo_get_closest_locale( $var ) {
	$var = strtolower( $var );

	if ( ! preg_match( '/^([a-z]{2})(?:[_-]([a-z]{2}))?/', $var, $matches ) )
		return false;

	$language_code = $matches[1];
	$region_code = isset( $matches[2] ) ? $matches[2] : '';

	$locales = bogo_available_locales();

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

function bogo_url( $url = null, $lang = null ) {
	if ( ! $lang )
		$lang = get_locale();

	$args = array(
		'using_permalinks' => (bool) get_option( 'permalink_structure' ) );

	return bogo_get_url_with_lang( $url, $lang, $args );
}

function bogo_get_url_with_lang( $url = null, $lang = null, $args = '' ) {
	$defaults = array(
		'using_permalinks' => true );

	$args = wp_parse_args( $args, $defaults );

	if ( ! $url ) {
		if ( ! $url = redirect_canonical( $url, false ) ) {
			$url = is_ssl() ? 'https://' : 'http://';
			$url .= $_SERVER['HTTP_HOST'];
			$url .= $_SERVER['REQUEST_URI'];
		}

		if ( $frag = strstr( $url, '#' ) )
			$url = substr( $url, 0, - strlen( $frag ) );

		if ( $query = @parse_url( $url, PHP_URL_QUERY ) ) {
			parse_str( $query, $query_vars );

			foreach ( array_keys( $query_vars ) as $qv ) {
				if ( ! get_query_var( $qv ) )
					$url = remove_query_arg( $qv, $url );
			}
		}
	}

	$default_locale = bogo_get_default_locale();

	if ( ! $lang )
		$lang = $default_locale;

	$home = set_url_scheme( get_option( 'home' ) );
	$home = trailingslashit( $home );

	$url = remove_query_arg( 'lang', $url );

	if ( ! $args['using_permalinks'] ) {
		if ( $lang != $default_locale )
			$url = add_query_arg( array( 'lang' => bogo_lang_slug( $lang ) ), $url );

		return $url;
	}

	$available_languages = array_map( 'bogo_lang_slug', bogo_available_locales() );

	$url = preg_replace(
		'#^' . preg_quote( $home ) . '((' . implode( '|', $available_languages ) . ')/)?#',
		$home . ( $lang == $default_locale ? '' : bogo_lang_slug( $lang ) . '/' ),
		trailingslashit( $url ) );

	return $url;
}

function bogo_get_lang_from_url( $url = '' ) {
	if ( ! $url ) {
		$url = is_ssl() ? 'https://' : 'http://';
		$url .= $_SERVER['HTTP_HOST'];
		$url .= $_SERVER['REQUEST_URI'];
	}

	if ( $frag = strstr( $url, '#' ) )
		$url = substr( $url, 0, - strlen( $frag ) );

	$home = set_url_scheme( get_option( 'home' ) );
	$home = trailingslashit( $home );

	$available_languages = array_map( 'bogo_lang_slug', bogo_available_locales() );

	$regex = '#^' . preg_quote( $home ) . '(' . implode( '|', $available_languages ) . ')/#';

	if ( preg_match( $regex, trailingslashit( $url ), $matches ) )
		return $matches[1];

	if ( $query = @parse_url( $url, PHP_URL_QUERY ) ) {
		parse_str( $query, $query_vars );

		if ( isset( $query_vars['lang'] )
		&& in_array( $query_vars['lang'], $available_languages ) )
			return $query_vars['lang'];
	}

	return false;
}

function bogo_get_prop( $prop ) {
	$option = get_option( 'bogo' );

	if ( ! is_array( $option ) )
		$option = array();

	return isset( $option[$prop] ) ? $option[$prop] : '';
}

function bogo_set_prop( $prop, $value ) {
	$option = get_option( 'bogo' );

	if ( ! is_array( $option ) )
		$option = array();

	$option[$prop] = $value;

	update_option( 'bogo', $option );
}

?>