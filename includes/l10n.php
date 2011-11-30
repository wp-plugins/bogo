<?php

add_action( 'init', 'bogo_add_l10n_custom_post_types', 11 );

function bogo_add_l10n_custom_post_types() {
	$labels = array(
		'name' => __( 'Translations', 'bogo' ),
		'singular_name' => __( 'Translation', 'bogo' ),
		'add_new_item' => __( 'Add New Translation', 'bogo' ),
		'edit_item' => __( 'Edit Translation', 'bogo' ),
		'new_item' => __( 'New Translation', 'bogo' ),
		'view_item' => __( 'View Translation', 'bogo' ),
		'search_items' => __( 'Search Translations', 'bogo' ),
		'not_found' => __( 'No translations found', 'bogo' ),
		'not_found_in_trash' => __( 'No translations found in Trash', 'bogo' ),
		'menu_name' => __( 'Translations', 'bogo' ) );

	$supports = array( 'title', 'editor', 'author', 'excerpt', 'custom-fields', 'revisions' );

	register_post_type( 'l10n', array(
		'labels' => $labels,
		'supports' => $supports,
		'show_ui' => true,
		'show_in_menu' => false ) );

	do_action( 'bogo_add_l10n_custom_post_types' );
}

add_filter( 'the_posts', 'bogo_l10n_posts_filter', 11, 2 );

function bogo_l10n_posts_filter( $posts, $query ) {
	$locale = get_locale();

	foreach ( (array) $posts as $post ) {
		$translation = bogo_get_post_translation( $post->ID, $locale );

		if ( ! $translation )
			continue;

		$post->post_title = $translation->post_title;
		$post->post_content = $translation->post_content;
		$post->post_excerpt = $translation->post_excerpt;
		$post->post_author = $translation->post_author;
	}

	return $posts;
}

add_filter( 'default_content', 'bogo_l10n_default_content' );
add_filter( 'default_title', 'bogo_l10n_default_content' );
add_filter( 'default_excerpt', 'bogo_l10n_default_content' );

function bogo_l10n_default_content( $post_content ) {
	global $post_type;

	if ( 'l10n' != $post_type )
		return $post_content;

	if ( ! $parent_post = get_post( $_REQUEST['parent_id'] ) )
		return $post_content;

	$current_filter = current_filter();

	if ( 'default_content' == $current_filter )
		return $parent_post->post_content;

	if ( 'default_title' == $current_filter )
		return $parent_post->post_title;

	if ( 'default_excerpt' == $current_filter )
		return $parent_post->post_excerpt;

	return $post_content;
}

add_action( 'add_meta_boxes', 'bogo_add_l10n_meta_boxes', 10, 2 );

function bogo_add_l10n_meta_boxes( $post_type, $post ) {
	global $action;

	add_meta_box( 'bogol10ndiv', __( 'Localization', 'bogo' ),
		'bogo_l10n_meta_box', $post_type, 'side', 'high' );
}

function bogo_l10n_meta_box( $post ) {
	if ( 'l10n' == $post->post_type ) {
		echo '<div class="l10ndiv">' . "\n";

		$parent_id = $post->post_parent ? $post->post_parent : $_REQUEST['parent_id'];

		if ( $parent_post = get_post( $parent_id ) ) {
?>
<p><strong><?php echo esc_html( __( 'Original Post:', 'bogo' ) ); ?></strong>
<a href="<?php echo get_edit_post_link( $parent_post->ID ); ?>" target="_blank"><?php echo esc_html( $parent_post->post_title ); ?></a></p>

<input type="hidden" name="parent_id" value="<?php echo absint( $parent_post->ID ); ?>" />
<?php
		}

		if ( ! $locale = bogo_get_post_locale( $post->ID ) )
			$locale = $_REQUEST['locale'];

		if ( $locale ) {
?>
<p>
<strong><?php echo esc_html( __( 'Locale:', 'bogo' ) ); ?></strong>
<?php echo esc_html( $locale ); ?>
<?php if ( $language = bogo_languages( $locale ) ) echo ' (' . esc_html( $language ) . ')'; ?>
</p>

<input type="hidden" name="bogo_locale" value="<?php echo esc_attr( $locale ); ?>" />
<?php
		}

		echo '</div>' . "\n";

		return;
	}

	$l10n_url = add_query_arg(
		array( 'post_type' => 'l10n', 'parent_id' => $post->ID ),
		admin_url( 'post-new.php' ) );

?>
<div class="l10ndiv">

<?php
	if ( $translations = bogo_get_post_translations( $post->ID ) ) :
?>
<p><strong><?php echo esc_html( __( 'Edit Translation:', 'bogo' ) ); ?></strong></p>
<ul>
<?php
		foreach ( $translations as $tr ) {
			echo '<li>';
			echo '<a href="' . get_edit_post_link( $tr->ID ) . '" target="_blank">' . esc_html( $tr->post_title ) . '</a>';

			if ( $language = bogo_get_post_locale( $tr->ID, true ) )
				echo ' [' . esc_html( $language ) . ']';

			echo '</li>';
		}
?>
</ul>
<?php
	endif;
?>

<p><strong><?php echo esc_html( __( 'Add New Translation:', 'bogo' ) ); ?></strong></p>
<p>
<?php bogo_translation_select( $post->ID ); ?>
<a href="#" id="bogo_add_translation_link" class="button hidden" target="blank"><?php echo esc_html( __( 'Add', 'bogo' ) ); ?></a>
</p>
</div>
<script type="text/javascript">
/* <![CDATA[ */
(function($) {
$(function() {
	$('#bogo_add_translation').change(function() {
		var locale = $(this).val();

		if (locale) {
			var link = '<?php echo esc_url_raw( $l10n_url . '&locale=' ); ?>' + locale;
			$('#bogo_add_translation_link').attr('href', link).show();
		}
	});
});
})(jQuery);
/* ]]> */
</script>
<?php
}

function bogo_translation_select( $post_id ) {
	$languages = bogo_languages();

	if ( ( $locale = get_locale() ) && isset( $languages[$locale] ) )
		unset( $languages[$locale] );

	$translations = bogo_get_post_translations( $post_id );

	foreach ( (array) $translations as $tr ) {
		if ( $locale = bogo_get_post_locale( $tr->ID ) )
			unset( $languages[$locale] );
	}

?>
<select name="bogo_add_translation" id="bogo_add_translation">
<option value=""><?php echo esc_html( __( '&ndash; Select Language &ndash;', 'bogo' ) ); ?></option>

<?php
	$user_translated = bogo_locales_current_user_has_translated();

	foreach ( $user_translated as $locale ) :

		if ( isset( $languages[$locale] ) )
			unset( $languages[$locale] );
		else
			continue;

		$language = bogo_languages( $locale );
?>
<option value="<?php echo esc_attr( $locale ); ?>"><?php echo esc_html( $language ); ?></option>
<?php
	endforeach;
?>

<?php foreach ( $languages as $locale => $language ) : ?>
<option value="<?php echo esc_attr( $locale ); ?>"><?php echo esc_html( $language ); ?></option>
<?php endforeach; ?>
</select>
<?php
}

add_action( 'save_post', 'bogo_add_l10n_save_post', 10, 2 );

function bogo_add_l10n_save_post( $post_id, $post ) {
	if ( 'l10n' == $post->post_type && $locale = $_POST['bogo_locale'] ) {
		update_post_meta( $post_id, '_locale',  $locale );
	}
}

?>