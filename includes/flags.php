<?php

add_action( 'wp_head', 'bogo_flag_css' );

function bogo_flag_css() {
	$flags = array();

	if ( apply_filters( 'bogo_use_flags', true ) ) {
		$locales = bogo_available_locales();

		foreach ( $locales as $locale ) {
			if ( $flag = bogo_get_flag( $locale ) ) {
				$flags[$locale] = $flag;
			}
		}
	}

	if ( ! $flags ) {
		return;
	}

	$side = is_rtl() ? 'right' : 'left';

	echo '<style type="text/css">' . "\n";

	foreach ( $flags as $locale => $flag ) {
		echo '.bogo-language-switcher .' . bogo_language_tag( $locale ) . ' {';
		echo ' background: url("' . $flag . '") no-repeat ' . $side . ' center;';
		echo ' }' . "\n";
	}

	echo '</style>' . "\n";
}

function bogo_get_flag( $locale ) {
	$locale = explode( '_', $locale );
	$locale = array_slice( $locale, 0, 2 ); // de_DE_formal => de_DE
	$locale = implode( '_', $locale );

	$special_cases = array(
		'ca' => 'catalonia',
		'gd' => 'scotland',
		'cy' => 'wales',
		'am' => 'et',
		'az' => 'az',
		'bs' => 'ba',
		'el' => 'gr',
		'et' => 'ee',
		'fi' => 'fi',
		'ga' => 'ie',
		'hr' => 'hr',
		'ht' => 'ht',
		'hy' => 'am',
		'ja' => 'jp',
		'kk' => 'kz',
		'lo' => 'la',
		'lv' => 'lv',
		'mn' => 'mn',
		'sq' => 'al',
		'tg' => 'tj',
		'th' => 'th',
		'tl' => 'ph',
		'uk' => 'ua',
		'vi' => 'vn' );

	if ( isset( $special_cases[$locale] ) ) {
		$file = $special_cases[$locale] . '.png';
	} elseif ( preg_match( '/_([A-Z]{2})$/', $locale, $matches ) ) {
		$file = strtolower( $matches[1] ) . '.png';
	} else {
		$file = 'zz.png'; // 'zz.png' doesn't exist, just a dummy.
	}

	$file = path_join( 'images/flag-icons', $file );
	$url = '';

	if ( file_exists( path_join( BOGO_PLUGIN_DIR, $file ) ) ) {
		$url = bogo_plugin_url( $file );
	}

	return apply_filters( 'bogo_get_flag', $url, $locale );
}
