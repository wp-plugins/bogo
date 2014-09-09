<?php

add_action( 'wp_update_nav_menu_item', 'bogo_update_nav_menu_item', 10, 2 );

function bogo_update_nav_menu_item( $menu_id, $menu_item_id ) {
	delete_post_meta( $menu_item_id, '_locale' );

	if ( isset( $_POST['menu-item-bogo-locale'][$menu_item_id] ) ) {
		$locales = (array) $_POST['menu-item-bogo-locale'][$menu_item_id];
		$locales = bogo_filter_locales( $locales );

		foreach ( $locales as $locale ) {
			add_post_meta( $menu_item_id, '_locale', $locale );
		}
	}

	if ( ! metadata_exists( 'post', $menu_item_id, '_locale' ) ) {
		add_post_meta( $menu_item_id, '_locale', 'zxx' ); // special code in ISO 639-2
	}
}

?>