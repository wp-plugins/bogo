<?php

require_once BOGO_PLUGIN_DIR . '/admin/user.php';
require_once BOGO_PLUGIN_DIR . '/admin/post.php';
require_once BOGO_PLUGIN_DIR . '/admin/nav-menu.php';
require_once BOGO_PLUGIN_DIR . '/admin/widgets.php';

add_action( 'admin_enqueue_scripts', 'bogo_admin_enqueue_scripts' );

function bogo_admin_enqueue_scripts( $hook_suffix ) {
	if ( 'widgets.php' == $hook_suffix ) {
		wp_enqueue_style( 'bogo-admin',
			plugins_url( 'admin/admin.css', BOGO_PLUGIN_BASENAME ),
			array(), BOGO_VERSION, 'all' );

		return;
	}

	if ( 'nav-menus.php' == $hook_suffix ) {
		$nav_menu_id = absint( get_user_option( 'nav_menu_recently_edited' ) );
		$nav_menu_items = wp_get_nav_menu_items( $nav_menu_id );
		$locales = array();

		foreach ( (array) $nav_menu_items as $item ) {
			$locales[$item->db_id] = $item->bogo_locales;
		}

		$prefix = 'menu-item-bogo-locale';

		wp_enqueue_script( 'bogo-admin',
			plugins_url( 'admin/admin.js', BOGO_PLUGIN_BASENAME ),
			array( 'jquery' ),
			BOGO_VERSION, true );

		wp_localize_script( 'bogo-admin', '_bogo', array(
			'availableLanguages' => bogo_available_languages( 'orderby=value' ),
			'locales' => $locales,
			'selectorLegend' => __( 'Displayed on pages in', 'bogo' ),
			'cbPrefix' => $prefix ) );

		wp_enqueue_style( 'bogo-admin',
			plugins_url( 'admin/admin.css', BOGO_PLUGIN_BASENAME ),
			array(), BOGO_VERSION, 'all' );

		return;
	}
}

?>