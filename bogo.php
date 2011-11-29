<?php
/*
Plugin Name: Bogo
Plugin URI: http://ideasilo.wordpress.com/2009/05/05/bogo/
Description: Bogo allows each user to choose their locale for the admin panel.
Author: Takayuki Miyoshi
Version: 2.0-dev
Author URI: http://ideasilo.wordpress.com/
*/

/*  Copyright 2007-2011 Takayuki Miyoshi (email: takayukister at gmail.com)

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

define( 'BOGO_VERSION', '2.0-dev' );

if ( ! defined( 'BOGO_PLUGIN_BASENAME' ) )
	define( 'BOGO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'BOGO_PLUGIN_NAME' ) )
	define( 'BOGO_PLUGIN_NAME', trim( dirname( BOGO_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'BOGO_PLUGIN_DIR' ) )
	define( 'BOGO_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BOGO_PLUGIN_NAME );

require_once BOGO_PLUGIN_DIR . '/includes/functions.php';
require_once BOGO_PLUGIN_DIR . '/includes/l10n.php';
require_once BOGO_PLUGIN_DIR . '/includes/user-l10n.php';

add_action( 'init', 'bogo_init' );

function bogo_init() {
	load_plugin_textdomain( 'bogo', 'wp-content/plugins/bogo/languages', 'bogo/languages' );
}

add_filter( 'locale', 'bogo_locale' );

function bogo_locale( $locale ) {
	if ( is_admin() ) {
		$locale_option = get_user_option( 'locale' );

		if ( ! empty( $locale_option ) )
			$locale = $locale_option;

		return $locale;
	}

	if ( isset( $_REQUEST['lang'] ) ) {
		if ( $closest = bogo_get_closest_locale( $_REQUEST['lang'] ) )
			$locale = $closest;

	} elseif ( is_user_logged_in() ) {
		$locale_option = get_user_option( 'locale' );

		if ( ! empty( $locale_option ) )
			$locale = $locale_option;

	} elseif ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
		$langs = bogo_http_accept_languages();

		foreach ( (array) $langs as $lang ) {
			if ( bogo_languages( $lang ) ) {
				$locale = $lang;
				break;
			} elseif ( 2 == strlen( $lang ) && $closest = bogo_get_closest_locale( $lang ) ) {
				$locale = $closest;
				break;
			}
		}
	}

	return $locale;
}

?>