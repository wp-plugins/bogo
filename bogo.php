<?php
/*
Plugin Name: Bogo
Description: A multilingualization plugin doesn't do what it shouldn't.
Plugin URI: http://ideasilo.wordpress.com/2009/05/05/bogo/
Author: Takayuki Miyoshi
Author URI: http://ideasilo.wordpress.com/
Text Domain: bogo
Domain Path: /languages/
Version: 2.0-dev
*/

/*  Copyright 2007-2012 Takayuki Miyoshi (email: takayukister at gmail.com)

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
	define( 'BOGO_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

if ( ! defined( 'BOGO_PLUGIN_URL' ) )
	define( 'BOGO_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

require_once BOGO_PLUGIN_DIR . '/includes/functions.php';
require_once BOGO_PLUGIN_DIR . '/includes/rewrite.php';
require_once BOGO_PLUGIN_DIR . '/includes/link-template.php';
require_once BOGO_PLUGIN_DIR . '/includes/widgets.php';
require_once BOGO_PLUGIN_DIR . '/includes/post-l10n-functions.php';
require_once BOGO_PLUGIN_DIR . '/includes/user-l10n-functions.php';
require_once BOGO_PLUGIN_DIR . '/includes/post-l10n.php';
require_once BOGO_PLUGIN_DIR . '/includes/user-l10n.php';

add_action( 'init', 'bogo_init' );

function bogo_init() {
	load_plugin_textdomain( 'bogo', 'wp-content/plugins/bogo/languages', 'bogo/languages' );

	if ( ! ( is_admin() || is_robots() || is_feed() || is_trackback() ) ) {
		$locale = get_locale();

		if ( ! isset( $_COOKIE['lang'] ) || $_COOKIE['lang'] != $locale )
			setcookie( 'lang', $locale, 0, '/' );
	}
}

add_filter( 'locale', 'bogo_locale' );

function bogo_locale( $locale ) {
	if ( is_admin() )
		return bogo_get_user_locale();

	if ( ( $lang = get_query_var( 'lang' ) ) && $closest = bogo_get_closest_locale( $lang ) )
		return $closest;

	if ( $default_locale = bogo_get_default_locale() )
		return $default_locale;

	return $locale;
}

add_filter( 'query_vars', 'bogo_query_vars' );

function bogo_query_vars( $query_vars ) {
	$query_vars[] = 'lang';

	return $query_vars;
}

add_action( 'parse_query', 'bogo_parse_query' );

function bogo_parse_query( $query ) {
	$qv = &$query->query_vars;

	if ( ! empty( $qv['bogo_suppress_locale_query'] ) )
		return;

	$lang = isset( $qv['lang'] ) ? $qv['lang'] : '';

	if ( is_admin() ) {
		$locale = $lang;
	} else {
		$locale = bogo_get_closest_locale( $lang );

		if ( empty( $locale ) )
			$locale = bogo_get_default_locale();
	}

	if ( empty( $locale ) )
		return;

	$meta_query = array(
		array( 'key' => '_locale', 'value' => $locale ) );

	if ( ! isset( $qv['meta_query'] ) )
		$qv['meta_query'] = array();

	$qv['meta_query'] = array_merge( $qv['meta_query'], $meta_query );
}

?>