<?php

add_filter( 'widget_display_callback', 'bogo_widget_display_callback', 10, 3 );

function bogo_widget_display_callback( $instance, $widget, $args ) {
	if ( isset( $instance['bogo_locales'] ) ) {
		$locale = get_locale();

		if ( ! in_array( $locale, (array) $instance['bogo_locales'] ) )
			$instance = false;
	}

	return $instance;
}

add_action( 'in_widget_form', 'bogo_in_widget_form', 10, 3 );

function bogo_in_widget_form( $widget, $return, $instance ) {
	$available_languages = bogo_available_languages( 'orderby=value' );

	$selected_languages = ! isset( $instance['bogo_locales'] )
		? array_keys( $available_languages ) : (array) $instance['bogo_locales'];

?>
<fieldset style="padding: .4em 1em .6em; border: 1px solid #ccc;">
<legend style="padding: 0 1em;"><?php echo esc_html( __( 'Displayed on pages in:', 'bogo' ) ); ?></legend>
<?php foreach ( $available_languages as $locale => $language ) : ?>
<input type="checkbox" id="<?php echo $widget->get_field_id( 'bogo_locales' ); ?>" name="<?php echo $widget->get_field_name( 'bogo_locales' ); ?>[]" value="<?php echo esc_attr( $locale ); ?>"<?php echo in_array( $locale, $selected_languages ) ? ' checked="checked"' : ''; ?> />
	<?php echo esc_html( $language ); ?><br />
<?php endforeach; ?>
</fieldset>
<?php

	$return = null;
}

add_filter( 'widget_update_callback', 'bogo_widget_update_callback', 10, 4 );

function bogo_widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {
	if ( isset( $new_instance['bogo_locales'] ) && is_array( $new_instance['bogo_locales'] ) )
		$instance['bogo_locales'] = $new_instance['bogo_locales'];
	else
		$instance['bogo_locales'] = array();

	return $instance;
}

?>