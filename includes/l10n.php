<?php

add_action( 'add_meta_boxes', 'bogo_add_l10n_meta_boxes', 10, 2 );

function bogo_add_l10n_meta_boxes( $post_type, $post ) {
	if ( in_array( $post_type, array( 'comment', 'link' ) ) )
		return;

	if ( 'auto-draft' == $post->post_status )
		return;

	add_meta_box( 'bogol10ndiv', __( 'Localization', 'bogo' ),
		'bogo_l10n_meta_box', null, 'side', 'high' );
}

function bogo_l10n_meta_box( $post ) {
	$available_languages = bogo_available_languages();

	$post_locale = bogo_get_post_locale( $post->ID );

	if ( isset( $available_languages[$post_locale] ) )
		unset( $available_languages[$post_locale] );

	$select = '<select name="bogo-make-translation-in" id="bogo-make-translation-in">';
	$select .= '<option value="">' . esc_html( __( 'Select Language', 'bogo' ) ) . '</option>';

	foreach ( $available_languages as $locale => $lang )
		$select .= '<option value="' . esc_attr( $locale ) . '">' . esc_html( $lang ) . '</option>';

	$select .= '</select>';

	$link = 'post-new.php?post_type=' . $post->post_type;
	$link = admin_url( $link );

?>
<p><?php echo $select; ?> <a href="<?php echo esc_url_raw( $link ); ?>" id="bogo-make-translation" class="button-secondary" target="_blank"><?php echo esc_html( __( 'Make Translation', 'bogo' ) ); ?></a></p>

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

?>