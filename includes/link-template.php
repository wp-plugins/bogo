<?php

add_filter( 'post_link', 'bogo_post_link', 10, 3 );

function bogo_post_link( $permalink, $post, $leavename ) {
	if ( ! bogo_is_localizable_post_type( $post->post_type ) )
		return $permalink;

	$locale = bogo_get_post_locale( $post->ID );
	$sample = ( isset( $post->filter ) && 'sample' == $post->filter );
	$permalink_structure = get_option( 'permalink_structure' );

	$using_permalinks = $permalink_structure &&
		( $sample || ! in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) );

	$permalink = bogo_get_url_with_lang( $permalink, $locale,
		array( 'using_permalinks' => $using_permalinks ) );

	return $permalink;
}

add_filter( 'page_link', 'bogo_page_link', 10, 3 );

function bogo_page_link( $permalink, $id, $sample ) {
	if ( ! bogo_is_localizable_post_type( 'page' ) )
		return $permalink;

	$locale = bogo_get_post_locale( $id );
	$post = get_post( $id );

	if ( 'page' == get_option( 'show_on_front' ) ) {
		$front_page_id = get_option( 'page_on_front' );

		if ( $id == $front_page_id )
			return $permalink;

		$translations = bogo_get_post_translations( $front_page_id );

		if ( ! empty( $translations[$locale] ) ) {
			if ( $translations[$locale]->ID == $id ) {
				$home = set_url_scheme( get_option( 'home' ) );
				$home = trailingslashit( $home );
				return bogo_url( $home, $locale );
			}
		}
	}

	$permalink_structure = get_option( 'permalink_structure' );

	$using_permalinks = $permalink_structure &&
		( $sample || ! in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) );

	$permalink = bogo_get_url_with_lang( $permalink, $locale,
		array( 'using_permalinks' => $using_permalinks ) );

	return $permalink;
}

add_filter( 'post_type_link', 'bogo_post_type_link', 10, 4 );

function bogo_post_type_link( $permalink, $post, $leavename, $sample ) {
	if ( ! bogo_is_localizable_post_type( $post->post_type ) )
		return $permalink;

	$locale = bogo_get_post_locale( $post->ID );
	$permalink_structure = get_option( 'permalink_structure' );

	$using_permalinks = $permalink_structure &&
		( $sample || ! in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) );

	$permalink = bogo_get_url_with_lang( $permalink, $locale,
		array( 'using_permalinks' => $using_permalinks ) );

	return $permalink;
}

add_filter( 'year_link', 'bogo_year_link', 10, 2 );

function bogo_year_link( $link, $year ) {
	return bogo_url( $link );
}

add_filter( 'month_link', 'bogo_month_link', 10, 3 );

function bogo_month_link( $link, $year, $month ) {
	return bogo_url( $link );
}

add_filter( 'day_link', 'bogo_day_link', 10, 4 );

function bogo_day_link( $link, $year, $month, $day ) {
	return bogo_url( $link );
}

add_filter( 'feed_link', 'bogo_feed_link', 10, 2 );

function bogo_feed_link( $link, $feed ) {
	return bogo_url( $link );
}

add_filter( 'author_feed_link', 'bogo_author_feed_link', 10, 2 );

function bogo_author_feed_link( $link, $feed ) {
	return bogo_url( $link );
}

add_filter( 'category_feed_link', 'bogo_category_feed_link', 10, 2 );

function bogo_category_feed_link( $link, $feed ) {
	return bogo_url( $link );
}

add_filter( 'taxonomy_feed_link', 'bogo_taxonomy_feed_link', 10, 3 );

function bogo_taxonomy_feed_link( $link, $feed, $taxonomy ) {
	return bogo_url( $link );
}

add_filter( 'post_type_archive_link', 'bogo_post_type_archive_link', 10, 2 );

function bogo_post_type_archive_link( $link, $post_type ) {
	return bogo_url( $link );
}

add_filter( 'post_type_archive_feed_link', 'bogo_post_type_archive_feed_link', 10, 2 );

function bogo_post_type_archive_feed_link( $link, $feed ) {
	return bogo_url( $link );
}

add_filter( 'term_link', 'bogo_term_link', 10, 3 );

function bogo_term_link( $link, $term, $taxonomy ) {
	return bogo_url( $link );
}

add_filter( 'home_url', 'bogo_home_url' );

function bogo_home_url( $url ) {
	if ( is_admin() || ! did_action( 'template_redirect' ) )
		return $url;

	return bogo_url( $url );
}

add_action( 'wp_head', 'bogo_m17n_headers' );

function bogo_m17n_headers() {
	$languages = array();

	if ( is_singular() ) {
		$post_id = get_queried_object_id();

		if ( $post_id && $translations = bogo_get_post_translations( $post_id ) ) {
			$locale = get_locale();
			$translations[$locale] = get_post( $post_id );

			foreach ( $translations as $lang => $translation ) {
				$languages[] = array(
					'hreflang' => bogo_language_tag( $lang ),
					'href' => get_permalink( $translation ) );
			}
		}
	} else {
		$available_locales = bogo_available_locales();

		if ( 1 < count( $available_locales ) ) {
			foreach ( $available_locales as $locale ) {
				$languages[] = array(
					'hreflang' => bogo_language_tag( $locale ),
					'href' => bogo_url( null, $locale ) );
			}
		}
	}

	$languages = apply_filters( 'bogo_rel_alternate_hreflang', $languages );

	foreach ( (array) $languages as $language ) {
		$hreflang = isset( $language['hreflang'] ) ? $language['hreflang'] : '';
		$href = isset( $language['href'] ) ? $language['href'] : '';

		if ( $hreflang && $href ) {
			$link = sprintf( '<link rel="alternate" hreflang="%1$s" href="%2$s" />',
				esc_attr( $hreflang ), esc_url( $href ) );

			echo $link . "\n";
		}
	}
}

function bogo_language_switcher( $args = '' ) {
	global $wp_query;

	$defaults = array(
		'echo' => false );

	$args = wp_parse_args( $args, $defaults );

	$locale = get_locale();
	$available_languages = bogo_available_languages();

	$translations = array();
	$is_singular = false;
	$post_id = get_queried_object_id();

	if ( is_singular() || ! empty( $wp_query->is_posts_page ) ) {
		$translations = bogo_get_post_translations( $post_id );
		$is_singular = true;
	}

	$output = '<ul class="bogo-language-switcher">';

	$total = count( $available_languages );
	$count = 0;

	foreach ( $available_languages as $code => $name ) {
		$count += 1;
		$class = array();
		$class[] = bogo_language_tag( $code );
		$class[] = bogo_lang_slug( $code );

		if ( $locale == $code ) {
			$class[] = 'current';
		}

		if ( 1 == $count ) {
			$class[] = 'first';
		}

		if ( $total == $count ) {
			$class[] = 'last';
		}

		$class = implode( ' ', array_unique( $class ) );

		$output .= '<li class="' . esc_attr( $class ) . '">';

		if ( $is_singular ) {
			if ( empty( $translations[$code] ) || $locale == $code ) {
				$output .= esc_html( $name );
			} else {
				$output .= '<a rel="alternate" hreflang="' . bogo_language_tag( $code ) . '" href="' . get_permalink( $translations[$code] ) . '">' . esc_html( $name ) . '</a>';
			}
		} else {
			if ( $locale == $code ) {
				$output .= esc_html( $name );
			} else {
				$output .= '<a rel="alternate" hreflang="' . bogo_language_tag( $code ) . '" href="' . esc_url( bogo_url( null, $code ) ) . '">' . esc_html( $name ) . '</a>';
			}
		}

		$output .= '</li>';
	}

	$output .= '</ul>' . "\n";

	if ( $args['echo'] ) {
		echo $output;
	} else {
		return $output;
	}
}

add_filter( 'get_previous_post_join', 'bogo_adjacent_post_join', 10, 3 );
add_filter( 'get_next_post_join', 'bogo_adjacent_post_join', 10, 3 );

function bogo_adjacent_post_join( $join, $in_same_term, $excluded_terms ) {
	global $wpdb;

	$post = get_post();

	if ( $post && bogo_is_localizable_post_type( get_post_type( $post ) ) ) {
		$join .= " LEFT JOIN $wpdb->postmeta AS postmeta_bogo ON (p.ID = postmeta_bogo.post_id AND postmeta_bogo.meta_key = '_locale')";
	}

	return $join;
}

add_filter( 'get_previous_post_where', 'bogo_adjacent_post_where', 10, 3 );
add_filter( 'get_next_post_where', 'bogo_adjacent_post_where', 10, 3 );

function bogo_adjacent_post_where( $where, $in_same_term, $excluded_terms ) {
	global $wpdb;

	$post = get_post();

	if ( $post && bogo_is_localizable_post_type( get_post_type( $post ) ) ) {
		$locale = bogo_get_post_locale( $post->ID );

		$where .= " AND (1=0";
		$where .= $wpdb->prepare( " OR postmeta_bogo.meta_value LIKE %s", $locale );

		if ( bogo_is_default_locale( $locale ) ) {
			$where .= " OR postmeta_bogo.meta_id IS NULL";
		}

		$where .= ")";
	}

	return $where;
}

?>