<?php

function bogo_get_post_locale( $post_id ) {
	$locale = get_post_meta( $post_id, '_locale', true );

	if ( empty( $locale ) )
		$locale = WPLANG;

	if ( empty( $locale ) )
		$locale = 'en_US';

	return $locale;
}

function bogo_get_post_translations( $post_id = 0 ) {
	$post = get_post( $post_id );

	if ( ! $post )
		return false;

	$args = array(
		'posts_per_page' => -1,
		'post_status' => 'any',
		'post_type' => $post->post_type,
		'meta_key' => '_original_post',
		'meta_value' => $post->ID );

	$q = new WP_Query();
	return $q->query( $args );
}

add_action( 'add_meta_boxes', 'bogo_add_l10n_meta_boxes', 10, 2 );

function bogo_add_l10n_meta_boxes( $post_type, $post ) {
	if ( in_array( $post_type, array( 'comment', 'link' ) ) )
		return;

	if ( 'auto-draft' == $post->post_status && empty( $_REQUEST['locale'] ) )
		return;

	add_meta_box( 'bogol10ndiv', __( 'Localization', 'bogo' ),
		'bogo_l10n_meta_box', null, 'side', 'high' );
}

function bogo_l10n_meta_box( $post ) {
	$post_type_object = get_post_type_object( $post->post_type );
	$post_type_label = strtolower( $post_type_object->labels->singular_name );

	$translations = bogo_get_post_translations( $post->ID );

	if ( $translations ) {
?>
<div class="translations">
<p><strong><?php echo esc_html( sprintf( __( 'Translations of this %s:', 'bogo' ), $post_type_label ) ); ?></strong></p>

<ul>
<?php
	foreach ( $translations as $translation ) {
		$locale = get_post_meta( $translation->ID, '_locale', true );

		if ( empty( $locale ) )
			continue;

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
</div><!-- .translations -->
<?php
	}

	$available_languages = bogo_available_languages();
	$post_locale = bogo_get_post_locale( $post->ID );

	if ( 'auto-draft' == $post->post_status ) {
		$locale = empty( $_REQUEST['locale'] ) ? $post_locale : $_REQUEST['locale'];
		$original_post = empty( $_REQUEST['original_post'] ) ? '' : $_REQUEST['original_post'];
	} else {
		$locale = $post_locale;
		$original_post = get_post_meta( $post->ID, '_original_post', true );
	}

?>
<div class="hidden">
<input type="hidden" name="locale" value="<?php echo esc_attr( $locale ); ?>" />
<input type="hidden" name="original_post" value="<?php echo esc_attr( $original_post ); ?>" />
</div>
<?php

	if ( 'auto-draft' == $post->post_status || ! empty( $original_post ) ) {
		if ( ! empty( $original_post ) ) {
			$lang = bogo_languages( $locale );

			if ( empty( $lang ) )
				$lang = $locale;

			$original_post_title = get_the_title( $original_post );
			$original_post_edit_link = get_edit_post_link( $original_post );

			if ( $original_post_edit_link )
				$original_post_edit_link = '<a href="' . esc_url( $original_post_edit_link ) . '" target="_blank">' . $original_post_title . '</a>';
			else
				$original_post_edit_link = $original_post_title;

			echo '<p>' . sprintf( __( 'This %1$s is a %2$s translation of %3$s.', 'bogo' ), $post_type_label, $lang, $original_post_edit_link ) . '</p>';
		} else {
			echo '<p>' . sprintf( __( 'This %1$s is a %2$s translation.', 'bogo' ), $post_type_label, $lang ) . '</p>';
		}

		return;
	}

	if ( isset( $available_languages[$post_locale] ) )
		unset( $available_languages[$post_locale] );

	$select = '<select name="bogo-make-translation-in" id="bogo-make-translation-in">';
	$select .= '<option value="">' . esc_html( __( 'Select Language', 'bogo' ) ) . '</option>';

	foreach ( $available_languages as $locale => $lang )
		$select .= '<option value="' . esc_attr( $locale ) . '">' . esc_html( $lang ) . '</option>';

	$select .= '</select>';

	$link = 'post-new.php?post_type=' . $post->post_type . '&original_post=' . $post->ID;
	$link = admin_url( $link );

?>
<div class="make-translation">
<p><strong><?php echo esc_html( sprintf( __( 'Make a translation of this %s:', 'bogo' ), $post_type_label ) ); ?></strong></p>
<p><?php echo $select; ?> <a href="<?php echo esc_url_raw( $link ); ?>" id="bogo-make-translation" class="button-secondary" target="_blank"><?php echo esc_html( __( 'Make Translation', 'bogo' ) ); ?></a></p>
</div><!-- .make-translation -->

<script type="text/javascript">
/* <![CDATA[ */
(function($) {
$(function() {
	$('#bogo-make-translation').hide();

	$('#bogo-make-translation-in').change(function() {
		var locale = $(this).val();

		if (locale) {
			var link = '<?php echo esc_url_raw( $link . '&locale=' ); ?>' + locale;
			$('#bogo-make-translation').attr('href', link).show();
		} else {
			$('#bogo-make-translation').hide();
		}
	});
});
})(jQuery);
/* ]]> */
</script>
<?php
}

add_action( 'save_post', 'bogo_save_post', 10, 2 );

function bogo_save_post( $post_id, $post ) {
	$available_languages = bogo_available_languages();

	if ( ! empty( $_REQUEST['locale'] ) && isset( $available_languages[$_REQUEST['locale']] ) )
		update_post_meta( $post_id, '_locale', $_REQUEST['locale'] );

	if ( ! empty( $_REQUEST['original_post'] ) && get_post( $_REQUEST['original_post'] ) )
		update_post_meta( $post_id, '_original_post', $_REQUEST['original_post'] );
}

?>