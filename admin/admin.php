<?php

require_once BOGO_PLUGIN_DIR . '/admin/includes/user.php';
require_once BOGO_PLUGIN_DIR . '/admin/includes/post.php';
require_once BOGO_PLUGIN_DIR . '/admin/includes/nav-menu.php';
require_once BOGO_PLUGIN_DIR . '/admin/includes/widgets.php';

add_action( 'admin_enqueue_scripts', 'bogo_admin_enqueue_scripts' );

function bogo_admin_enqueue_scripts( $hook_suffix ) {
	if ( false !== strpos( $hook_suffix, 'bogo-tools' )
	|| 'widgets.php' == $hook_suffix
	|| 'user-edit.php' == $hook_suffix ) {
		wp_enqueue_style( 'bogo-admin',
			plugins_url( 'admin/includes/css/admin.css', BOGO_PLUGIN_BASENAME ),
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
			plugins_url( 'admin/includes/js/admin.js', BOGO_PLUGIN_BASENAME ),
			array( 'jquery' ),
			BOGO_VERSION, true );

		wp_localize_script( 'bogo-admin', '_bogo', array(
			'availableLanguages' => bogo_available_languages( 'orderby=value' ),
			'locales' => $locales,
			'selectorLegend' => __( 'Displayed on pages in', 'bogo' ),
			'cbPrefix' => $prefix ) );

		wp_enqueue_style( 'bogo-admin',
			plugins_url( 'admin/includes/css/admin.css', BOGO_PLUGIN_BASENAME ),
			array(), BOGO_VERSION, 'all' );

		return;
	}

	if ( 'options-general.php' == $hook_suffix ) {
		wp_enqueue_script( 'bogo-admin',
			plugins_url( 'admin/includes/js/admin.js', BOGO_PLUGIN_BASENAME ),
			array( 'jquery' ),
			BOGO_VERSION, true );

		wp_localize_script( 'bogo-admin', '_bogo', array(
			'defaultLocale' => bogo_get_default_locale() ) );

		return;
	}
}

add_action( 'admin_menu', 'bogo_admin_menu' );

function bogo_admin_menu() {
	$tools = add_management_page(
		__( 'Bogo Tools', 'bogo' ), __( 'Bogo', 'bogo' ),
		'update_core', 'bogo-tools', 'bogo_tools_page' );

	add_action( 'load-' . $tools, 'bogo_load_tools_page' );
}

function bogo_load_tools_page() {
	require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

	$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

	if ( 'install_translation' == $action ) {
		check_admin_referer( 'bogo-tools' );

		if ( ! current_user_can( 'update_core' ) ) {
			wp_die( __( 'You are not allowed to install translations.', 'bogo' ) );
		}

		$locale = isset( $_GET['locale'] ) ? $_GET['locale'] : null;

		if ( wp_download_language_pack( $locale ) ) {
			$redirect_to = add_query_arg(
				array( 'locale' => $locale, 'message' => 'success' ),
				menu_page_url( 'bogo-tools', false ) );
		} else {
			$redirect_to = add_query_arg(
				array( 'locale' => $locale, 'message' => 'failed' ),
				menu_page_url( 'bogo-tools', false ) );
		}

		wp_safe_redirect( $redirect_to );
		exit();
	}
}

function bogo_tools_page() {
	$message = "";

	if ( isset( $_GET['message'] ) ) {
		if ( 'success' == $_GET['message'] ) {
			$message = __( "Translation installed successfully.", 'bogo' );
		} elseif ( 'failed' == $_GET['message'] ) {
			$message = __( "Translation install failed.", 'bogo' );
		}
	}

	$default_locale = bogo_get_default_locale();
	$available_locales = bogo_available_locales();

?>
<div class="wrap">

<h2><?php echo esc_html( __( 'Bogo Tools', 'bogo' ) ); ?></h2>

<?php if ( ! empty( $message ) ) : ?>
<div id="message" class="updated"><p><?php echo esc_html( $message ); ?></p></div>
<?php endif; ?>

<h3 class="title"><?php echo esc_html( __( 'Available Languages', 'bogo' ) ); ?></h3>

<table id="bogo-languages-table" class="widefat">
<thead>
	<tr><th></th><th><?php echo esc_html( __( 'Language', 'bogo' ) ); ?></th></tr>
</thead>
<tfoot>
	<tr><th></th><th><?php echo esc_html( __( 'Language', 'bogo' ) ); ?></th></tr>
</tfoot>
<tbody id="translations">
	<tr><th>1</th><td><p>
		<strong><?php echo esc_html( bogo_get_language( $default_locale ) ); ?></strong>
		[<?php echo esc_html( $default_locale ); ?>]
		<br /><?php echo esc_html( __( 'Site Default Language', 'bogo' ) ); ?>
	</p></td></tr>

<?php
	$count = 1;

	foreach ( $available_locales as $locale ) {
		if ( $locale == $default_locale ) {
			continue;
		}

		$count += 1;
?>
	<tr><th><?php echo $count; ?></th><td><p>
		<strong><?php echo esc_html( bogo_get_language( $locale ) ); ?></strong>
		[<?php echo esc_html( $locale ); ?>]
		<br /><?php echo esc_html( __( 'Installed', 'bogo' ) ); ?>
	</p></td></tr>
<?php
	}

	$can_install = wp_can_install_language_pack();

	foreach ( wp_get_available_translations() as $locale => $translation ) {
		if ( in_array( $locale, $available_locales ) ) {
			continue;
		}

		$count += 1;

		$install_link = '';

		if ( $can_install ) {
			$install_link = menu_page_url( 'bogo-tools', false );
			$install_link = add_query_arg(
				array( 'action' => 'install_translation', 'locale' => $locale ),
				$install_link );
			$install_link = wp_nonce_url( $install_link, 'bogo-tools' );
			$install_link = sprintf( '<a href="%1$s" class="install">%2$s</a>',
				$install_link, esc_html( __( 'Install', 'bogo' ) ) );
		}
?>
	<tr><th><?php echo $count; ?></th><td><p>
		<strong><?php echo esc_html( bogo_get_language( $locale ) ); ?></strong>
		[<?php echo esc_html( $locale ); ?>]
		<?php echo $install_link; ?>
	</p></td></tr>
<?php
	}
?>

</tbody>
</table>

</div>
<?php
}

?>