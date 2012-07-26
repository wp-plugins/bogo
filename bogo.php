<?php
/*
Plugin Name: Bogo
Description: A straight-forward multilingualization plugin. No more double-digit custom DB tables or hidden HTML comments that could cause you headaches later on.
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

add_action( 'plugins_loaded', 'bogo_plugins_loaded' );

function bogo_plugins_loaded() {
	load_plugin_textdomain( 'bogo', 'wp-content/plugins/bogo/languages', 'bogo/languages' );
}

add_action( 'init', 'bogo_init' );

function bogo_init() {
	if ( ! ( is_admin() || is_robots() || is_feed() || is_trackback() ) ) {
		$locale = get_locale();

		if ( ! isset( $_COOKIE['lang'] ) || $_COOKIE['lang'] != $locale )
			setcookie( 'lang', $locale, 0, '/' );
	}
}

add_filter( 'locale', 'bogo_locale' );

function bogo_locale( $locale ) {
	global $wp_rewrite, $wp_query;

	if ( is_admin() )
		return bogo_get_user_locale();

	$default_locale = bogo_get_default_locale();

	if ( ! empty( $wp_query->query_vars ) ) {
		if ( ( $lang = get_query_var( 'lang' ) ) && $closest = bogo_get_closest_locale( $lang ) )
			return $closest;
		else
			return $default_locale;
	}

	if ( isset( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) {
		$url = is_ssl() ? 'https://' : 'http://';
		$url .= $_SERVER['HTTP_HOST'];
		$url .= $_SERVER['REQUEST_URI'];

		$home = trailingslashit( home_url() );
		$available_languages = bogo_available_languages();
		$available_languages = array_map( 'bogo_lang_slug', array_keys( $available_languages ) );
		$available_languages = implode( '|', $available_languages );
		$pattern = '#^' . preg_quote( $home ) . '(' . $available_languages . ')(/|$)' . '#';

		if ( preg_match( $pattern, $url, $matches ) && $closest = bogo_get_closest_locale( $matches[1] ) )
			return $closest;
	}

	if ( ! empty( $_REQUEST['lang'] ) && $closest = bogo_get_closest_locale( $_REQUEST['lang'] ) )
		return $closest;

	$locale = $default_locale;

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

	if ( isset( $qv['post_type'] ) && 'any' != $qv['post_type'] ) {
		$localizable = array_filter( (array) $qv['post_type'], 'bogo_is_localizable_post_type' );

		if ( empty( $localizable ) ) {
			$qv['bogo_suppress_locale_query'] = true;
			return;
		}
	}

	$lang = isset( $qv['lang'] ) ? $qv['lang'] : '';

	if ( is_admin() ) {
		$locale = $lang;
	} else {
		$locale = bogo_get_closest_locale( $lang );

		if ( empty( $locale ) )
			$locale = bogo_get_default_locale();
	}

	if ( empty( $locale ) || ! bogo_is_available_locale( $locale ) )
		$qv['bogo_suppress_locale_query'] = true;
	else
		$qv['lang'] = $locale;
}

add_filter( 'posts_join', 'bogo_posts_join', 10, 2 );

function bogo_posts_join( $join, $query ) {
	global $wpdb;

	$qv = &$query->query_vars;

	if ( ! empty( $qv['bogo_suppress_locale_query'] ) )
		return $join;

	$locale = empty( $qv['lang'] ) ? '' : $qv['lang'];

	if ( ! bogo_is_available_locale( $locale ) )
		return $join;

	if ( ! $meta_table = _get_meta_table( 'post' ) )
		return $join;

	$join .= " LEFT JOIN $meta_table AS postmeta_bogo ON ($wpdb->posts.ID = postmeta_bogo.post_id AND postmeta_bogo.meta_key = '_locale')";

	return $join;
}

add_filter( 'posts_where', 'bogo_posts_where', 10, 2 );

function bogo_posts_where( $where, $query ) {
	global $wpdb;

	$qv = &$query->query_vars;

	if ( ! empty( $qv['bogo_suppress_locale_query'] ) )
		return $where;

	$locale = empty( $qv['lang'] ) ? '' : $qv['lang'];

	if ( ! bogo_is_available_locale( $locale ) )
		return $where;

	if ( ! $meta_table = _get_meta_table( 'post' ) )
		return $where;

	$where .= " AND (1=0";

	$where .= $wpdb->prepare( " OR postmeta_bogo.meta_value LIKE %s", $locale );

	if ( bogo_is_default_locale( $locale ) )
		$where .= " OR postmeta_bogo.meta_id IS NULL";

	$where .= ")";

	return $where;
}

?>