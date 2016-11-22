<?php

/**
 * Add settings menu page.
 */
function bnp_disclaimer_settings_menu() {
	add_options_page(
		__( 'Settings', 'bnp-disclaimer' ),
		__( 'BNP Disclaimer', 'bnp-disclaimer' ),
		'manage_options',
		'bnp-disclaimer.php',
		'bnp_disclaimer_settings'
	);
}

/**
 * Setting menu page content.
 */
function bnp_disclaimer_settings() {
	echo '<div class="wrap">';

	printf( '<h1>%s</h1>', __( 'Settings', 'bnp-disclaimer' ) );

	echo '<form method="post" action="options.php">';

		settings_fields( 'bnp_disclaimer_settings_field' );
		do_settings_sections( 'bnp-disclaimer.php' );
		submit_button();

	echo '</form>';

	echo '</div>';
}

/**
 * Register the plugin's settings.
 *
 * @since 1.0.0
 */
function bnp_disclaimer_register_settings() {

	register_setting(
		'bnp_disclaimer_settings_field',
		'bnp_disclaimer_settings',
		'bnp_disclaimer_sanitize_options'
	);

	add_settings_section(
		'bnp_disclaimer_post',
		__( 'Select reference post', 'bnp-disclaimer' ),
		'bnp_disclaimer_post_callback',
		'bnp-disclaimer.php'
	);

	add_settings_field(
		'bnp_disclaimer_post_list',
		__( 'Site item', 'bnp-disclaimer' ),
		'bnp_disclaimer_post_list_callback',
		'bnp-disclaimer.php',
		'bnp_disclaimer_post'
	);

	add_settings_field(
		'bnp_disclaimer_post_decline_redirect',
		__( 'Decline action link', 'bnp-disclaimer' ),
		'bnp_disclaimer_post_decline_redirect_callback',
		'bnp-disclaimer.php',
		'bnp_disclaimer_post'
	);

}

/**
 * Sanitize plugin options.
 *
 * @param array $options Options to sanitize.
 **/
function bnp_disclaimer_sanitize_options( $options ) {
	// TODO: Fix this url validation.
	if ( ! empty( $options['decline_url'] )
		&& esc_url( $options['decline_url'] ) !== $options['decline_url'] ) {
		$message = __( 'You must provide a valid URL.', 'bnp-disclaimer' );
	}

	if ( ! empty( $message ) ) {
		add_settings_error(
			'bnp_disclaimer_settings',
			esc_attr( 'settings_updated' ),
			$message,
			'error'
		);
	}

	return $options;
}

/**
 * Section text: manual_settings.
 *
 * @since	1.0.0
 */
function bnp_disclaimer_post_callback() {
	printf(
		'<p>%s<br>%s</p>',
		__( "Choose the item of the site to which you'd like to attach the disclaimer.", 'bnp-disclaimer' ),
		__( 'All traffic will be redirected to this page if the user has not accepted the terms.', 'bnp-disclaimer' )
	);
}

/**
 * Post selector.
 *
 * @since	1.0.0
 */
function bnp_disclaimer_post_list_callback() {
	// TODO: Include all post-types.
	$options = get_option( 'bnp_disclaimer_settings' );
	$post_types = get_post_types( array(), 'objects' );
	$post_types_excluded = array(
		'attachment',
		'revision',
		'nav_menu_item',
	);
	$excluded_pages = array(
		get_option( 'page_on_front' ),
		get_option( 'page_for_posts' ),
	);

	if ( ! isset( $options['post_id'] ) ) {
		$options['post_id'] = 0;
	}

	echo '<select name="bnp_disclaimer_settings[post_id]">';

	printf(
		'<option value="">%s</option>',
		__( 'Select a post', 'bnp-disclaimer' )
	);

	foreach ( $post_types as $post_type ) {

		// Don't include excluded post-type posts.
		if ( in_array( $post_type->name, $post_types_excluded ) ) {
			continue;
		}

		$posts = get_posts( array(
				'post_type'      => $post_type->name,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => -1,
		) );

		printf( '<optgroup label="%s">', esc_attr( $post_type->label ) );

		foreach ( $posts as $post ) {
			if ( in_array( $post->ID, $excluded_pages ) ) {
				continue;
			}

			printf(
				'<option value="%d" %s>%s (%s)</option>',
				esc_attr( $post->ID ),
				selected( $options['post_id'], $post->ID, false ),
				$post->post_title,
				get_the_author_meta( 'nicename', $post->post_author )
			);
		}

		echo '</optgroup>';
	}

	echo '</select>';
}

/**
 * Decline URL.
 *
 * @since	1.0.0
 */
function bnp_disclaimer_post_decline_redirect_callback() {
	$options = get_option( 'bnp_disclaimer_settings' );

	if ( ! isset( $options['decline_url'] ) ) {
		$options['decline_url'] = '';
	}

	printf(
		'<input type="url" name="bnp_disclaimer_settings[decline_url]" value="%s" class="regular-text code">',
		esc_url( $options['decline_url'] )
	);

	printf(
		'<p class="description">%s</p>',
		__( 'The link to redirect to, eg. http://example.com', 'bnp-disclaimer' )
	);
}
