<?php

/* Posts List Table */

add_filter( 'manage_pages_columns', 'bogo_pages_columns' );

function bogo_pages_columns( $posts_columns ) {
	return bogo_posts_columns( $posts_columns, 'page' );
}

add_filter( 'manage_posts_columns', 'bogo_posts_columns', 10, 2 );

function bogo_posts_columns( $posts_columns, $post_type ) {
	if ( ! bogo_is_localizable_post_type( $post_type ) )
		return $posts_columns;

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
	$post_type = get_post_type( $post_id );

	if ( ! bogo_is_localizable_post_type( $post_type ) )
		return;

	if ( 'locale' != $column_name )
		return;

	$locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $locale ) )
		return;

	$language = bogo_get_language( $locale );

	if ( empty( $language ) )
		$language = $locale;

	echo sprintf( '<a href="%1$s">%2$s</a>',
		esc_url( add_query_arg(
			array( 'post_type' => $post_type, 'lang' => $locale ),
			'edit.php' ) ),
		esc_html( $language ) );
}

add_action( 'restrict_manage_posts', 'bogo_restrict_manage_posts' );

function bogo_restrict_manage_posts() {
	global $post_type;

	if ( ! bogo_is_localizable_post_type( $post_type ) ) {
		return;
	}

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
	if ( ! bogo_is_localizable_post_type( $post->post_type ) )
		return $actions;

	$post_type_object = get_post_type_object( $post->post_type );

	if ( ! current_user_can( $post_type_object->cap->edit_post, $post->ID )
	|| 'trash' == $post->post_status )
		return $actions;

	$user_locale = bogo_get_user_locale();
	$post_locale = bogo_get_post_locale( $post->ID );

	if ( $user_locale == $post_locale )
		return $actions;

	$translations = bogo_get_post_translations( $post );

	if ( ! empty( $translations[$user_locale] ) ) {
		$translation = $translations[$user_locale];

		if ( empty( $translation->ID ) || $translation->ID == $post->ID )
			return $actions;

		$title = __( 'Edit %s translation of this item', 'bogo' );
		$text = __( 'Edit %s translation', 'bogo' );

		$edit_link = get_edit_post_link( $translation->ID );

	} else {
		$title = __( 'Translate this item into %s', 'bogo' );
		$text = __( 'Translate into %s', 'bogo' );

		$edit_link = admin_url( 'post-new.php?post_type=' . $post->post_type
			. '&locale=' . $user_locale
			. '&original_post=' . $post->ID );
	}

	$language = bogo_get_language( $user_locale );

	if ( empty( $language ) )
		$language = $user_locale;

	$actions['translate'] = '<a title="' . esc_attr( sprintf( $title, $language ) ) . '" href="' . $edit_link . '">' . esc_html( sprintf( $text, $language ) ) . '</a>';

	return $actions;
}

/* Single Post */

add_action( 'add_meta_boxes', 'bogo_add_l10n_meta_boxes', 10, 2 );

function bogo_add_l10n_meta_boxes( $post_type, $post ) {
	if ( ! bogo_is_localizable_post_type( $post_type ) )
		return;

	if ( in_array( $post_type, array( 'comment', 'link' ) ) )
		return;

	add_meta_box( 'bogol10ndiv', __( 'Language', 'bogo' ),
		'bogo_l10n_meta_box', null, 'side', 'high' );
}

function bogo_l10n_meta_box( $post ) {
	$initial = ( 'auto-draft' == $post->post_status );

	if ( $initial ) {
		$locale = isset( $_REQUEST['locale'] ) ? $_REQUEST['locale'] : '';

		if ( ! bogo_is_available_locale( $locale ) )
			$locale = bogo_get_user_locale();

		$original_post = empty( $_REQUEST['original_post'] ) ? '' : $_REQUEST['original_post'];
	} else {
		$locale = bogo_get_post_locale( $post->ID );
		$original_post = get_post_meta( $post->ID, '_original_post', true );

		if ( empty( $original_post ) )
			$original_post = $post->ID;
	}

	$lang = bogo_get_language( $locale );

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

		$lang = bogo_get_language( $locale );

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

	$lang = bogo_get_language( $locale );

	if ( empty( $lang ) )
		$lang = $locale;

	$edit_link = admin_url( 'post-new.php?post_type=' . $post->post_type
		. '&locale=' . $locale
		. '&original_post=' . $post->ID );

?>
<p class="textright"><a href="<?php echo $edit_link; ?>" target="_blank" class="button"><?php echo esc_html( sprintf( __( 'Add %s translation', 'bogo' ), $lang ) ) ?></a></p>
<?php
}

add_filter( 'default_title', 'bogo_translation_default', 10, 2 );
add_filter( 'default_content', 'bogo_translation_default', 10, 2 );
add_filter( 'default_excerpt', 'bogo_translation_default', 10, 2 );

function bogo_translation_default( $value, $post ) {
	if ( ! empty( $value )
	|| empty( $_REQUEST['original_post'] )
	|| ! $original = get_post( $_REQUEST['original_post'] ) ) {
		return $value;
	}

	if ( 'default_title' == current_filter() ) {
		$value = $original->post_title;
	} elseif ( 'default_content' == current_filter() ) {
		$value = $original->post_content;
	} elseif ( 'default_excerpt' == current_filter() ) {
		$value = $original->post_excerpt;
	}

	return $value;
}

add_action( 'save_post', 'bogo_save_post', 10, 2 );

function bogo_save_post( $post_id, $post ) {
	if ( did_action( 'import_start' ) && ! did_action( 'import_end' ) ) // Importing
		return;

	if ( ! bogo_is_localizable_post_type( $post->post_type ) )
		return;

	$old_locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $old_locale ) ) {
		if ( ! empty( $_REQUEST['locale'] ) && bogo_is_available_locale( $_REQUEST['locale'] ) )
			$locale = $_REQUEST['locale'];
		elseif ( 'auto-draft' == get_post_status( $post_id ) )
			$locale = bogo_get_user_locale();
		else
			$locale = bogo_get_default_locale();
	}

	if ( ! empty( $locale ) && $locale != $old_locale )
		update_post_meta( $post_id, '_locale', $locale );
	else
		$locale = $old_locale;

	if ( $original = get_post_meta( $post_id, '_original_post', true ) )
		return;

	if ( ! empty( $_REQUEST['original_post'] ) ) {
		$original = get_post_meta( $_REQUEST['original_post'], '_original_post', true );

		if ( empty( $original ) )
			$original = (int) $_REQUEST['original_post'];

		update_post_meta( $post_id, '_original_post', $original );
		return;
	}

	$original = $post_id;

	while ( 1 ) {
		$q = new WP_Query();

		$posts = $q->query( array(
			'bogo_suppress_locale_query' => true,
			'posts_per_page' => 1,
			'post_status' => 'any',
			'post_type' => $post->post_type,
			'meta_key' => '_original_post',
			'meta_value' => $original ) );

		if ( empty( $posts ) ) {
			update_post_meta( $post_id, '_original_post', $original );
			return;
		}

		$original += 1;
	}
}

add_filter( 'wp_unique_post_slug', 'bogo_unique_post_slug', 10, 6 );

function bogo_unique_post_slug( $slug, $post_id, $status, $type, $parent, $original ) {
	global $wp_rewrite;

	if ( ! bogo_is_localizable_post_type( $type ) ) {
		return $slug;
	}

	$feeds = is_array( $wp_rewrite->feeds ) ? $wp_rewrite->feeds : array();

	if ( in_array( $original, $feeds ) ) {
		return $slug;
	}

	$locale = bogo_get_post_locale( $post_id );

	if ( empty( $locale ) ) {
		return $slug;
	}

	$args = array(
		'posts_per_page' => 1,
		'post__not_in' => array( $post_id ),
		'post_type' => $type,
		'name' => $original,
		'lang' => $locale );

	$hierarchical = in_array( $type, get_post_types( array( 'hierarchical' => true ) ) );

	if ( $hierarchical ) {
		if ( preg_match( "@^($wp_rewrite->pagination_base)?\d+$@", $original ) ) {
			return $slug;
		}

		$args['post_parent'] = $parent;
	}

	$q = new WP_Query();
	$posts = $q->query( $args );

	if ( empty( $posts ) ) {
		$slug = $original;
	}

	return $slug;
}

?>