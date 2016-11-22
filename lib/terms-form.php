<?php

/**
 * Add terms form with filter.
 *
 * @since  1.0.0
 * @param  string $content The page's HTML
 * @return string          The original HTML with the form appended, if we are
 *                         accessing the terms page.
 */
function bnp_disclaimer_form( $content ) {
	$options = get_option( 'bnp_disclaimer_settings' );
	$default_args = array(
		'bnp_original_url' => home_url( '/' ),
	);
	$query_string = isset( $_SERVER['QUERY_STRING'] )
		&& ! empty( $_SERVER['QUERY_STRING'] )
			? $_SERVER['QUERY_STRING']
			: '';

	$args = wp_parse_args( $query_string, $default_args );

	// Verify nonce.
	if ( isset( $_POST['bnp_disclaimer_nonce_field'] )
		&& ! wp_verify_nonce( $_POST['bnp_disclaimer_nonce_field'], 'bnp_disclaimer_nonce' ) ) {
		return;
	}

	// In case this is an invalid form submission.
	if ( isset( $_POST['bnp_disclaimer_original_url'] ) ) {
		$args['bnp_original_url'] = $_POST['bnp_disclaimer_original_url'];
	}

	// Leave if we are not showing the terms page.
	if ( ! is_page( $options['post_id'] ) ) {
		return $content;
	}

	$content .= sprintf(
		'<form method="post" action="%s">',
		esc_url( get_permalink( $options['post_id'] ) )
	);

	$content .= wp_nonce_field( 'bnp_disclaimer_nonce', 'bnp_disclaimer_nonce_field' );

	$content .= sprintf(
		'<input type="hidden" name="bnp_disclaimer_original_url" value="%s">',
		esc_url( $args['bnp_original_url'] )
	);

	$content .= sprintf(
		'<label for="bnp_disclaimer_term_1">
			<input type="checkbox" name="bnp_disclaimer_term_1" id="bnp_disclaimer_term_1" value="true">
			%s
		</label>',
		__( 'First terms checkbox', 'bnp-disclaimer' )
	);

	$content .= sprintf(
		'<label for="bnp_disclaimer_term_2">
			<input type="checkbox" name="bnp_disclaimer_term_2" id="bnp_disclaimer_term_2" value="true">
			%s
		</label>',
		__( 'Second terms checkbox', 'bnp-disclaimer' )
	);

	$content .= sprintf(
		'<button type="submit">%s</button>',
		__( 'Accept', 'bnp-disclaimer' )
	);

	$content .= sprintf(
		'<a href="%s">%s</a>',
		isset( $options['decline_url'] )
			&& ! empty( $options['decline_url'] )
			? esc_url( $options['decline_url'] )
			: esc_url( home_url( '/' ) ),
		__( 'Decline', 'bnp-disclaimer' )
	);

	$content .= '</form>';

	return $content;
}
