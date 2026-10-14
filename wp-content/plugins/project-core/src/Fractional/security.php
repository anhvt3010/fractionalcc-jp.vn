<?php
/**
 * Security hardening for Fractional CC.
 * Handles XML-RPC restriction, author enumeration prevention, REST API user exposure mitigation, and security headers.
 *
 * @package Project_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Disable XML-RPC completely and remove pingback headers to prevent brute-force and DDoS amplification.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/** The authentication filter alone does not disable unauthenticated pingbacks. */
function project_core_fcc_block_xmlrpc() {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		status_header( 403 );
		wp_die( 'XML-RPC is disabled.', '', array( 'response' => 403 ) );
	}
}
add_action( 'init', 'project_core_fcc_block_xmlrpc', 0 );

/**
 * 2. Remove version disclosure and unnecessary discovery tags from HTML head.
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * 3. Prevent username enumeration via author scans (/?author=N and /author/* archives).
 */
function project_core_fcc_block_author_enumeration() {
	if ( ! is_admin() && ( is_author() || isset( $_GET['author'] ) ) ) {
		wp_safe_redirect( home_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'project_core_fcc_block_author_enumeration', 0 );

/**
 * 4. Protect user enumeration via WordPress REST API (/wp-json/wp/v2/users).
 * Only authenticated users with capability to list users can view the users endpoint.
 */
function project_core_fcc_restrict_rest_users( $result, $server, $request ) {
	if ( null !== $result ) {
		return $result;
	}

	// Match the resolved route: pretty URLs, rest_route, encoded URLs and batch requests.
	if ( preg_match( '#^/wp/v2/users(?:/|$)#', $request->get_route() ) ) {
		if ( ! is_user_logged_in() || ! current_user_can( 'list_users' ) ) {
			return rest_convert_error_to_response( new WP_Error(
				'rest_forbidden_users',
				__( 'Không có quyền xem danh sách người dùng.', 'project-core' ),
				array( 'status' => rest_authorization_required_code() )
			) );
		}
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'project_core_fcc_restrict_rest_users', 10, 3 );

add_filter(
	'wp_headers',
	function( $headers ) {
		unset( $headers['X-Pingback'] );
		$headers['X-Frame-Options']        = 'SAMEORIGIN';
		$headers['X-Content-Type-Options'] = 'nosniff';
		$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
		$headers['Permissions-Policy']     = 'camera=(), microphone=(), geolocation=()';
		return $headers;
	}
);
