<?php

add_filter( 'wp_get_nav_menu_items', 'bogo_get_nav_menu_items', 10, 3 );

function bogo_get_nav_menu_items( $items, $menu, $args ) {
	if ( is_admin() ) {
		return $items;
	}

	$locale = get_locale();

	foreach ( $items as $key => $item ) {
		if ( ! in_array( $locale, $item->bogo_locales ) ) {
			unset( $items[$key] );
		}
	}

	return $items;
}

add_filter( 'wp_setup_nav_menu_item', 'bogo_setup_nav_menu_item' );

function bogo_setup_nav_menu_item( $menu_item ) {
	if ( isset( $menu_item->bogo_locales ) ) {
		return $menu_item;
	}

	$menu_item->bogo_locales = array();

	if ( isset( $menu_item->post_type ) && 'nav_menu_item' == $menu_item->post_type ) {
		$menu_item->bogo_locales = get_post_meta( $menu_item->ID, '_locale' );
	}

	if ( $menu_item->bogo_locales ) {
		$menu_item->bogo_locales = array_intersect(
			$menu_item->bogo_locales, array_keys( bogo_available_languages() ) );
	} else {
		$menu_item->bogo_locales = array_keys( bogo_available_languages() );
	}

	return $menu_item;
}

add_action( 'wp_update_nav_menu_item', 'bogo_update_nav_menu_item', 10, 2 );

function bogo_update_nav_menu_item( $menu_id, $menu_item_id ) {
	delete_post_meta( $menu_item_id, '_locale' );

	if ( isset( $_POST['menu-item-bogo-locale'][$menu_item_id] ) ) {
		$locales = (array) $_POST['menu-item-bogo-locale'][$menu_item_id];
		$locales = array_intersect( $locales,
			array_keys( bogo_available_languages() ) );

		foreach ( $locales as $locale ) {
			add_post_meta( $menu_item_id, '_locale', $locale );
		}
	}

	if ( ! metadata_exists( 'post', $menu_item_id, '_locale' ) ) {
		add_post_meta( $menu_item_id, '_locale', 'zxx' ); // special code in ISO 639-2
	}
}

?>