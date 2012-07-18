<?php

/* Posts List Table */

add_filter( 'manage_posts_columns', 'bogo_posts_columns' );
add_filter( 'manage_pages_columns', 'bogo_posts_columns' );

function bogo_posts_columns( $posts_columns ) {
	if ( ! isset( $posts_columns['locale'] ) ) {
		$posts_columns = array_merge(
			array_slice( $posts_columns, 0, 3 ),
			array( 'locale' => __( 'Locale', 'bogo' ) ),
			array_slice( $posts_columns, 3 ) );
	}

	return $posts_columns;
}

add_action( 'manage_pages_custom_column', 'bogo_manage_posts_custom_column', 10, 2 );
add_action( 'manage_posts_custom_column', 'bogo_manage_posts_custom_column', 10, 2 );

function bogo_manage_posts_custom_column( $column_name, $post_id ) {
	if ( 'locale' != $column_name )
		return;

	$locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $locale ) )
		return;

	$language = bogo_languages( $locale );

	if ( empty( $language ) )
		$language = $locale;

	echo sprintf( '<a href="%1$s">%2$s</a>',
		esc_url( add_query_arg(
			array( 'post_type' => get_post_type( $post_id ), 'lang' => $locale ),
			'edit.php' ) ),
		esc_html( $language ) );
}

add_action( 'restrict_manage_posts', 'bogo_restrict_manage_posts' );

function bogo_restrict_manage_posts() {
	$available_languages = bogo_available_languages();
	$current_locale = empty( $_GET['lang'] ) ? '' : $_GET['lang'];

	echo '<select name="lang">';

	$selected = ( '' == $current_locale ) ? ' selected="selected"' : '';

	echo '<option value=""' . $selected . '>'
		. esc_html( __( 'Show all locales', 'bogo' ) ) . '</option>';

	foreach ( $available_languages as $locale => $lang ) {
		$selected = ( $locale == $current_locale ) ? ' selected="selected"' : '';

		echo '<option value="' . esc_attr( $locale ) . '"' . $selected . '>'
			. esc_html( $lang ) . '</option>';
	}

	echo '</select>' . "\n";
}

add_filter( 'post_row_actions', 'bogo_post_row_actions', 10, 2 );
add_filter( 'page_row_actions', 'bogo_post_row_actions', 10, 2 );

function bogo_post_row_actions( $actions, $post ) {
	if ( 'trash' == $post->post_status )
		return $actions;

	$user_locale = bogo_get_user_locale();
	$post_locale = bogo_get_post_locale( $post->ID );

	if ( $user_locale == $post_locale )
		return $actions;

	$translations = bogo_get_post_translations( $post );

	if ( isset( $translations[$user_locale] ) )
		return $actions;

	$language = bogo_languages( $user_locale );

	if ( empty( $language ) )
		$language = $user_locale;

	$edit_link = admin_url( 'post-new.php?post_type=' . $post->post_type
		. '&locale=' . $user_locale
		. '&original_post=' . $post->ID );

	$actions['translate'] = '<a title="' . esc_attr( sprintf( __( 'Translate this item into %s', 'bogo' ), $language ) ) . '" href="' . $edit_link . '">' . esc_html( sprintf( __( 'Translate into %s', 'bogo' ), $language ) ) . '</a>';

	return $actions;
}

/* Single Post */

add_action( 'add_meta_boxes', 'bogo_add_l10n_meta_boxes', 10, 2 );

function bogo_add_l10n_meta_boxes( $post_type, $post ) {
	if ( in_array( $post_type, array( 'comment', 'link' ) ) )
		return;

	add_meta_box( 'bogol10ndiv', __( 'Language', 'bogo' ),
		'bogo_l10n_meta_box', null, 'side', 'high' );
}

function bogo_l10n_meta_box( $post ) {
	$initial = ( 'auto-draft' == $post->post_status );
	$available_languages = bogo_available_languages();

	if ( $initial ) {
		$locale = isset( $_REQUEST['locale'] ) ? $_REQUEST['locale'] : '';

		if ( empty( $locale ) || ! isset( $available_languages[$locale] ) )
			$locale = bogo_get_user_locale();

		$original_post = empty( $_REQUEST['original_post'] ) ? '' : $_REQUEST['original_post'];
	} else {
		$locale = bogo_get_post_locale( $post->ID );
		$original_post = get_post_meta( $post->ID, '_original_post', true );
	}

	$lang = bogo_languages( $locale );

	if ( empty( $lang ) )
		$lang = $locale;

?>
<div class="hidden">
<input type="hidden" name="locale" value="<?php echo esc_attr( $locale ); ?>" />
<input type="hidden" name="original_post" value="<?php echo esc_attr( $original_post ); ?>" />
</div>

<div class="descriptions">
<p><strong><?php echo esc_html( __( 'Language', 'bogo' ) ); ?>:</strong>
	<?php echo esc_html( $lang ); ?></p>
</div>

<?php
	bogo_metabox_translations( $post );
	bogo_metabox_add_translation( $post );
}

function bogo_metabox_translations( $post ) {
	$translations = bogo_get_post_translations( $post->ID );

	if ( empty( $translations ) )
		return;

?>
<p><strong><?php echo esc_html( __( 'Translations', 'bogo' ) ); ?>:</strong></p>

<ul style="list-style: disc inside; margin-left: 1em;">
<?php
	foreach ( $translations as $locale => $translation ) {
		$edit_link = get_edit_post_link( $translation->ID );

		echo '<li>';

		if ( $edit_link )
			echo '<a href="' . esc_url( $edit_link ) . '" target="_blank">' . get_the_title( $translation->ID ) . '</a>';
		else
			echo get_the_title( $translation->ID );

		$lang = bogo_languages( $locale );

		if ( empty( $lang ) )
			$lang = $locale;

		echo ' [' . $lang . ']';
		echo '</li>';
	}
?>
</ul>
<?php
}

function bogo_metabox_add_translation( $post ) {
	if ( 'auto-draft' == $post->post_status )
		return;

	$post_locale = bogo_get_post_locale( $post->ID );
	$user_locale = bogo_get_user_locale();

	if ( $post_locale == $user_locale )
		return;

	$locale = $user_locale;

	$translations = bogo_get_post_translations( $post->ID );

	if ( isset( $translations[$locale] ) )
		return;

	$lang = bogo_languages( $locale );

	if ( empty( $lang ) )
		$lang = $locale;

	$edit_link = admin_url( 'post-new.php?post_type=' . $post->post_type
		. '&locale=' . $locale
		. '&original_post=' . $post->ID );

?>
<p class="textright"><a href="<?php echo $edit_link; ?>" target="_blank" class="button"><?php echo esc_html( sprintf( __( 'Add %s translation', 'bogo' ), $lang ) ) ?></a></p>
<?php
}

add_filter( 'default_content', 'bogo_translation_default_content', 10, 2 );

function bogo_translation_default_content( $content, $post ) {
	if ( ! empty( $content ) )
		return $content;

	if ( ! empty( $_REQUEST['original_post'] ) ) {
		$original = get_post( $_REQUEST['original_post'] );

		if ( $original && ! empty( $original->post_content ) )
			$content = $original->post_content;
	}

	return $content;
}

add_filter( 'default_title', 'bogo_translation_default_title', 10, 2 );

function bogo_translation_default_title( $title, $post ) {
	if ( ! empty( $title ) )
		return $title;

	if ( ! empty( $_REQUEST['original_post'] ) ) {
		$original = get_post( $_REQUEST['original_post'] );

		if ( $original && ! empty( $original->post_title ) )
			$title = $original->post_title;
	}

	return $title;
}

add_filter( 'default_excerpt', 'bogo_translation_default_excerpt', 10, 2 );

function bogo_translation_default_excerpt( $excerpt, $post ) {
	if ( ! empty( $excerpt ) )
		return $excerpt;

	if ( ! empty( $_REQUEST['original_post'] ) ) {
		$original = get_post( $_REQUEST['original_post'] );

		if ( $original && ! empty( $original->post_excerpt ) )
			$excerpt = $original->post_excerpt;
	}

	return $excerpt;
}

add_action( 'save_post', 'bogo_save_post', 10, 2 );

function bogo_save_post( $post_id, $post ) {
	if ( ! empty( $_REQUEST['locale'] ) ) {
		$available_languages = bogo_available_languages();

		if ( isset( $available_languages[$_REQUEST['locale']] ) )
			$locale = $_REQUEST['locale'];
	}

	if ( empty( $locale ) )
		$locale = bogo_get_post_locale( $post_id );

	if ( ! empty( $locale ) )
		update_post_meta( $post_id, '_locale', $locale );

	$original = get_post_meta( $post_id, '_original_post', true );

	if ( empty( $original ) && ! empty( $_REQUEST['original_post'] ) ) {
		$original = get_post_meta( $_REQUEST['original_post'], '_original_post', true );

		if ( empty( $original ) )
			$original = (int) $_REQUEST['original_post'];
	}

	if ( ! empty( $original )
	&& $original != $post_id
	&& bogo_get_post_locale( $original ) != $locale )
		update_post_meta( $post_id, '_original_post', $original );
}

/*
 * Original slug is not passed as an argument in WP 3.4
 * http://core.trac.wordpress.org/changeset/21177
 */

if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {

add_filter( 'wp_unique_post_slug', 'bogo_unique_post_slug', 10, 6 );

function bogo_unique_post_slug( $slug, $post_id, $status, $type, $parent, $original ) {
	global $wp_rewrite;

	if ( in_array( $status, array( 'draft', 'pending', 'auto-draft', 'attachment' ) ) )
		return $slug;

	$feeds = is_array( $wp_rewrite->feeds ) ? $wp_rewrite->feeds : array();

	if ( in_array( $original, $feeds ) )
		return $slug;

	$locale = get_post_meta( $post_id, '_locale', true );

	$args = array(
		'posts_per_page' => 1,
		'post__not_in' => array( $post_id ),
		'post_type' => $type,
		'name' => $original,
		'meta_key' => '_locale',
		'meta_value' => $locale );

	$hierarchical = in_array( $type, get_post_types( array( 'hierarchical' => true ) ) );

	if ( $hierarchical ) {
		if ( preg_match( "@^($wp_rewrite->pagination_base)?\d+$@", $original ) )
			return $slug;

		$args['post_parent'] = $parent;
	}

	$q = new WP_Query();
	$posts = $q->query( $args );

	if ( empty( $posts ) )
		$slug = $original;

	return $slug;
}

}

?>