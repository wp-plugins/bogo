<?php

add_action( 'activate_' . BOGO_PLUGIN_BASENAME, 'bogo_flush_rewrite_rules' );

function bogo_flush_rewrite_rules() {
	bogo_add_rewrite_tags();
	flush_rewrite_rules();
}

add_action( 'init', 'bogo_add_rewrite_tags' );

function bogo_add_rewrite_tags() {
	add_rewrite_tag( '%lang%', '([a-z]{2}(?:-[A-Z]{2})?)', 'lang=' );
}

add_filter( 'root_rewrite_rules', 'bogo_root_rewrite_rules' );

function bogo_root_rewrite_rules( $root_rewrite ) {
	global $wp_rewrite;

	$extra = $wp_rewrite->generate_rewrite_rules(
		trailingslashit( $wp_rewrite->root ) . '%lang%/', EP_ROOT );

	$root_rewrite = array_merge( $extra, $root_rewrite );

	return $root_rewrite;
}

add_filter( 'post_rewrite_rules', 'bogo_post_rewrite_rules' );

function bogo_post_rewrite_rules( $post_rewrite ) {
	global $wp_rewrite;

	$permastruct = $wp_rewrite->permalink_structure;

	if ( ! apache_mod_loaded( 'mod_rewrite', true ) && ! iis7_supports_permalinks() )
		$permastruct = preg_replace( '#^/index\.php#', '/index.php/%lang%', $permastruct );
	elseif ( is_multisite() && ! is_subdomain_install() && is_main_site() )
		$permastruct = preg_replace( '#^/blog#', '/blog/%lang%', $permastruct );
	else
		$permastruct = preg_replace( '#^/#', '/%lang%/', $permastruct );

	$extra = $wp_rewrite->generate_rewrite_rules( $permastruct, EP_PERMALINK, false );

	$post_rewrite = array_merge( $extra, $post_rewrite );

	return $post_rewrite;
}

add_filter( 'date_rewrite_rules', 'bogo_date_rewrite_rules' );

function bogo_date_rewrite_rules( $date_rewrite ) {
	global $wp_rewrite;

	$permastruct = $wp_rewrite->get_date_permastruct();

	$permastruct = preg_replace(
		'#^' . $wp_rewrite->front . '#',
		trailingslashit( $wp_rewrite->front ) . '%lang%/',
		$permastruct );

	$extra = $wp_rewrite->generate_rewrite_rules( $permastruct, EP_DATE );

	$date_rewrite = array_merge( $extra, $date_rewrite );

	return $date_rewrite;
}

add_filter( 'comments_rewrite_rules', 'bogo_comments_rewrite_rules' );

function bogo_comments_rewrite_rules( $comments_rewrite ) {
	global $wp_rewrite;

	$extra = $wp_rewrite->generate_rewrite_rules(
		trailingslashit( $wp_rewrite->root ) . '%lang%/' . $wp_rewrite->comments_base,
		EP_COMMENTS, true, true, true, false );

	$comments_rewrite = array_merge( $extra, $comments_rewrite );

	return $comments_rewrite;
}

add_filter( 'search_rewrite_rules', 'bogo_search_rewrite_rules' );

function bogo_search_rewrite_rules( $search_rewrite ) {
	global $wp_rewrite;

	$extra = $wp_rewrite->generate_rewrite_rules(
		trailingslashit( $wp_rewrite->root ) . '%lang%/' . $wp_rewrite->search_base . '/%search%',
		EP_SEARCH );

	$search_rewrite = array_merge( $extra, $search_rewrite );

	return $search_rewrite;
}

add_filter( 'author_rewrite_rules', 'bogo_author_rewrite_rules' );

function bogo_author_rewrite_rules( $author_rewrite ) {
	global $wp_rewrite;

	$permastruct = $wp_rewrite->get_author_permastruct();

	$permastruct = preg_replace(
		'#^' . $wp_rewrite->front . '#',
		trailingslashit( $wp_rewrite->front ) . '%lang%/',
		$permastruct );

	$extra = $wp_rewrite->generate_rewrite_rules( $permastruct, EP_AUTHORS );

	$author_rewrite = array_merge( $extra, $author_rewrite );

	return $author_rewrite;
}

add_filter( 'page_rewrite_rules', 'bogo_page_rewrite_rules' );

function bogo_page_rewrite_rules( $page_rewrite ) {
	global $wp_rewrite;

	$wp_rewrite->add_rewrite_tag( '%pagename%', '(.?.+?)', 'pagename=' );
	$permastruct = trailingslashit( $wp_rewrite->root ) . '%lang%/%pagename%';

	$extra = $wp_rewrite->generate_rewrite_rules(
		$permastruct, EP_PAGES, true, true, false, false );

	$page_rewrite = array_merge( $extra, $page_rewrite );

	return $page_rewrite;
}

add_filter( 'category_rewrite_rules', 'bogo_category_rewrite_rules' );

function bogo_category_rewrite_rules( $category_rewrite ) {
	return bogo_taxonomy_rewrite_rules( $category_rewrite, 'category', EP_CATEGORIES );
}

add_filter( 'post_tag_rewrite_rules', 'bogo_post_tag_rewrite_rules' );

function bogo_post_tag_rewrite_rules( $post_tag_rewrite ) {
	return bogo_taxonomy_rewrite_rules( $post_tag_rewrite, 'post_tag', EP_TAGS );
}

add_filter( 'post_format_rewrite_rules', 'bogo_post_format_rewrite_rules' );

function bogo_post_format_rewrite_rules( $post_format_rewrite ) {
	return bogo_taxonomy_rewrite_rules( $post_format_rewrite, 'post_format' );
}

function bogo_taxonomy_rewrite_rules( $taxonomy_rewrite, $taxonomy, $ep_mask = EP_NONE ) {
	global $wp_rewrite;

	$permastruct = $wp_rewrite->get_extra_permastruct( $taxonomy );

	$permastruct = preg_replace(
		'#^' . $wp_rewrite->front . '#',
		trailingslashit( $wp_rewrite->front ) . '%lang%/',
		$permastruct );

	$extra = $wp_rewrite->generate_rewrite_rules( $permastruct, $ep_mask );

	return array_merge( $extra, $taxonomy_rewrite );
}

?>