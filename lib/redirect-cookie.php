<?php

/**
 * Redirect to terms page.
 *
 * @since 1.0.0
 */
function bnp_disclaimer_redirect() {
	$options = get_option( 'bnp_disclaimer_settings' );

	// Check if we are in the backend.
	if ( is_admin() ) {
		return;
	}

	// Leave if the user is logged in.
	if ( wp_get_current_user()->ID ) {
		return;
	}

	// Leave if we already have a valid cookie.
	if ( isset( $_COOKIE['bnp_disclaimer_terms_accepted'] ) ) {
		if ( is_page( $options['post_id'] ) ) {
			wp_redirect( home_url( '/' ) );
			exit;
		}
		return;
	}

	// Verify if the post_id is defined and not empty.
	if ( ! isset( $options['post_id'] ) && ! empty( $options['post_id'] ) ) {
		return;
	}

	// Check if we are in the $post_id page.
	if ( is_page( $options['post_id'] ) ) {
		return;
	}

	// Return the original URL as a query_var.
	$request_url = sprintf(
		'http%s://%s%s',
		is_ssl() ? 's' : '',
		$_SERVER['HTTP_HOST'],
		$_SERVER['REQUEST_URI']
	);
	$redirect_url = get_permalink( $options['post_id'] );

	wp_redirect( add_query_arg( 'bnp_original_url', $request_url, $redirect_url ) );
	exit;
}

/**
 * Handle form acceptance.
 */
function bnp_disclaimer_accept() {

	// Verify nonce.
	if ( isset( $_POST['bnp_disclaimer_nonce_field'] )
		&& ! wp_verify_nonce( $_POST['bnp_disclaimer_nonce_field'], 'bnp_disclaimer_nonce' ) ) {
		return;
	}

	// Return if the first terms ar not accepted.
	if ( ! isset( $_POST['bnp_disclaimer_term_1'] ) || ! $_POST['bnp_disclaimer_term_1'] ) {
		return;
	}

	// Return if the first terms ar not accepted.
	if ( ! isset( $_POST['bnp_disclaimer_term_2'] ) || ! $_POST['bnp_disclaimer_term_2'] ) {
		return;
	}

	$timeout = time() + 3600 * 24 * 30; // 30 days
	setcookie(
		'bnp_disclaimer_terms_accepted',
		true,
		$timeout,
		'/'
	);

	wp_redirect( esc_url( $_POST['bnp_disclaimer_original_url'] ) );
	exit;
}
