<?php
/** Managed-content integration tests; never use the working database. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require rtrim( $root, '/\\' ) . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
$checks = 0;
function fcc_managed_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; }
fcc_managed_check( true === project_core_fcc_initialize_content_management(), 'Management migration succeeds.' );
$data = project_core_fcc_default_document();
$pages = get_option( 'project_core_fcc_pages' );
foreach ( $data['contentManagement']['pages'] as $scope => $names ) {
	foreach ( $names as $name ) {
		$field = acf_get_field( 'field_' . $name );
		$default = $data['pages'][ $scope ]['fields'][ $name ];
		$value = get_post_meta( $pages[ $scope ], $name, true );
		fcc_managed_check( $field && $field['type'] === $default['type'], 'Every managed value has the matching ACF field.' );
		fcc_managed_check( metadata_exists( 'post', $pages[ $scope ], $name ), 'Every managed value initialized.' );
		if ( 'image' === $default['type'] ) {
			fcc_managed_check( wp_attachment_is_image( $value ) && hash_file( 'sha256', get_attached_file( $value ) ) === hash_file( 'sha256', get_stylesheet_directory() . '/assets/fractional/' . $default['value'] ), 'Managed image matches packaged source.' );
		} else { fcc_managed_check( (string) $value === (string) $default['value'], 'Managed text/URL matches the existing content.' ); }
	}
}
fcc_managed_check( ! isset( $data['contentManagement']['pages']['concerns']['fcc_regimen_dom_nau'] ), 'Unapproved regimen choices are not part of this migration.' );
foreach ( project_core_fcc_regimens() as $regimen ) { fcc_managed_check( ! array_key_exists( 'fcc_' . $regimen['name'], $data['pages']['concerns']['fields'] ), 'Regimen selections remain absent from defaults.' ); }

$sessions = WP_Session_Tokens::get_instance( $admins[0] );
$expires = time() + 600;
$token = $sessions->create( $expires );
$cookies = array( new WP_Http_Cookie( array( 'name' => AUTH_COOKIE, 'value' => wp_generate_auth_cookie( $admins[0], $expires, 'auth', $token ) ) ), new WP_Http_Cookie( array( 'name' => LOGGED_IN_COOKIE, 'value' => wp_generate_auth_cookie( $admins[0], $expires, 'logged_in', $token ) ) ) );
function fcc_managed_request( $url, $body = null, $authenticated = true ) {
	global $cookies;
	$args = array( 'timeout' => 25, 'cookies' => $authenticated ? $cookies : array() );
	if ( null !== $body ) { $args['method'] = 'POST'; $args['body'] = $body; }
	$result = wp_remote_request( $url, $args );
	if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_code() ); }
	return $result;
}
function fcc_managed_input( $html, $name ) {
	$dom = new DOMDocument(); @$dom->loadHTML( $html ); $xpath = new DOMXPath( $dom );
	$input = $xpath->query( '//input[@name="' . $name . '"]' )->item( 0 );
	if ( ! $input ) { throw new RuntimeException( 'Missing native editor input: ' . $name ); }
	return $input->getAttribute( 'value' );
}
function fcc_managed_save( $id, $fields, $valid_nonce = true, $authenticated = true ) {
	$editor = fcc_managed_request( admin_url( 'post.php?post=' . $id . '&action=edit' ) );
	fcc_managed_check( 200 === wp_remote_retrieve_response_code( $editor ), 'Native editor responds.' );
	$html = wp_remote_retrieve_body( $editor );
	$post = get_post( $id );
	$body = array( 'action' => 'editpost', 'post_ID' => $id, 'post_type' => $post->post_type, 'post_status' => $post->post_status, 'post_title' => $post->post_title, 'content' => $post->post_content, 'page_template' => get_page_template_slug( $id ), '_wpnonce' => fcc_managed_input( $html, '_wpnonce' ), '_acf_screen' => 'post', '_acf_post_id' => $id, '_acf_nonce' => $valid_nonce ? fcc_managed_input( $html, '_acf_nonce' ) : 'invalid-management-test-nonce', 'acf' => $fields );
	$result = fcc_managed_request( admin_url( 'post.php' ), $body, $authenticated );
	clean_post_cache( $id ); wp_cache_delete( $id, 'post_meta' );
	return $result;
}
try {
	foreach ( array( 'home', 'shared', 'concerns', 'catalog' ) as $scope ) {
		$editor = fcc_managed_request( admin_url( 'post.php?post=' . $pages[ $scope ] . '&action=edit' ) );
		$html = wp_remote_retrieve_body( $editor );
		fcc_managed_check( ! preg_match( '/Fatal error|Warning:|Deprecated:/', $html ), 'No editor PHP errors.' );
		foreach ( $data['contentManagement']['pages'][ $scope ] as $name ) { fcc_managed_check( false !== strpos( $html, 'data-key="field_' . $name . '"' ), 'Every scoped field is present in the native editor.' ); }
	}
	$concern_name = $data['contentManagement']['pages']['concerns'][1];
	$concern = $pages['concerns'];
	$text = 'Managed editor text <b>escaped</b>';
	fcc_managed_save( $concern, array( 'field_' . $concern_name => $text ) );
	fcc_managed_check( $text === get_post_meta( $concern, $concern_name, true ), 'Native form persists text.' );
	$r = fcc_managed_request( get_permalink( $concern ), null, false );
	fcc_managed_check( false !== strpos( wp_remote_retrieve_body( $r ), esc_html( $text ) ) && false === strpos( wp_remote_retrieve_body( $r ), $text ), 'Edited public content is escaped.' );
	fcc_managed_save( $concern, array( 'field_' . $concern_name => 'invalid nonce overwrite' ), false );
	fcc_managed_check( $text === get_post_meta( $concern, $concern_name, true ), 'Invalid ACF nonce prevents field changes.' );
	fcc_managed_save( $concern, array( 'field_' . $concern_name => 'anonymous overwrite' ), true, false );
	fcc_managed_check( $text === get_post_meta( $concern, $concern_name, true ), 'Anonymous native save cannot change content.' );

	$image_name = 'fcc_concerns_dom_nau_image';
	$image = (int) get_post_meta( $pages['home'], 'fcc_ui_mechanism_breakthrough_image', true );
	fcc_managed_save( $concern, array( 'field_' . $image_name => $image ) );
	fcc_managed_check( $image === (int) get_post_meta( $concern, $image_name, true ), 'Image replacement persists.' );
	$r = fcc_managed_request( get_permalink( $concern ), null, false );
	fcc_managed_check( false !== strpos( wp_remote_retrieve_body( $r ), wp_get_attachment_image_url( $image, 'full' ) ), 'Selected image renders.' );
	fcc_managed_save( $concern, array( 'field_' . $image_name => 0 ) );
	fcc_managed_check( 0 === (int) get_post_meta( $concern, $image_name, true ), 'Explicit image removal persists.' );
	$r = fcc_managed_request( get_permalink( $concern ), null, false );
	fcc_managed_check( false === strpos( wp_remote_retrieve_body( $r ), 'src=""' ), 'No broken empty image after removal.' );
	$field = acf_get_field( 'field_' . $image_name );
	fcc_managed_check( is_string( project_child_fcc_validate_image_field( true, $concern, $field ) ), 'Non-attachment IDs rejected.' );
	fcc_managed_check( is_string( project_child_fcc_validate_image_field( true, array( $image ), $field ) ), 'Malformed image values rejected.' );

	fcc_managed_save( $concern, array( 'field_fcc_concerns_seo_title' => 'Managed concern SEO title', 'field_fcc_concerns_seo_description' => 'Managed concern SEO description', 'field_fcc_concerns_seo_image' => $image ) );
	$r = fcc_managed_request( get_permalink( $concern ), null, false ); $html = wp_remote_retrieve_body( $r );
	fcc_managed_check( false !== strpos( $html, '<title>Managed concern SEO title</title>' ) && false !== strpos( $html, 'content="Managed concern SEO description"' ) && false !== strpos( $html, '<meta property="og:image" content="' . wp_get_attachment_image_url( $image, 'full' ) ), 'Page SEO controls render.' );
	fcc_managed_save( $pages['shared'], array( 'field_fcc_site_favicon' => $image, 'field_fcc_hotline' => '0123456789', 'field_fcc_hotlineDisplay' => '0123 456 789' ) );
	$r = fcc_managed_request( get_permalink( $concern ), null, false ); $html = wp_remote_retrieve_body( $r );
	fcc_managed_check( 1 === substr_count( $html, '<link rel="icon"' ) && false !== strpos( $html, '<link rel="icon" href="' . wp_get_attachment_image_url( $image, 'full' ) ), 'Managed favicon has a single owner.' );
	foreach ( array( 'home', 'catalog', 'policies' ) as $scope ) {
		$r = fcc_managed_request( get_permalink( $pages[ $scope ] ), null, false ); $html = wp_remote_retrieve_body( $r );
		fcc_managed_check( false !== strpos( $html, 'tel:0123456789' ) && false === strpos( $html, '0985 561 862' ), 'Hotline labels and links sync on ' . $scope );
	}
	$product = project_core_fcc_legacy_product_id( 1 );
	fcc_managed_save( $product, array( 'field_fcc_rating' => 4.2, 'field_fcc_reviewCount' => 12, 'field_fcc_product_seo_title' => 'Managed product SEO', 'field_fcc_product_seo_description' => 'Managed product description' ) );
	$r = fcc_managed_request( get_permalink( $product ), null, false ); $html = wp_remote_retrieve_body( $r );
	fcc_managed_check( false !== strpos( $html, '4.2 / 5.0 (12+' ) && false !== strpos( $html, '<title>Managed product SEO</title>' ), 'Product-specific rating and SEO render.' );

	$combo = project_core_fcc_posts( 'fcc_combo' )[0]->ID;
	fcc_managed_save( $combo, array( 'field_fcc_combo_rating' => 4.1, 'field_fcc_combo_reviewCount' => 15 ) );
	$r = fcc_managed_request( get_permalink( $pages['catalog'] ), null, false );
	fcc_managed_check( false !== strpos( wp_remote_retrieve_body( $r ), '4.1 (15+' ), 'Combo score uses its native fields.' );
	project_core_fcc_initialize_content_management(); project_core_fcc_initialize();
	fcc_managed_check( $text === get_post_meta( $concern, $concern_name, true ) && 0 === (int) get_post_meta( $concern, $image_name, true ) && 'Managed concern SEO title' === get_post_meta( $concern, 'fcc_concerns_seo_title', true ), 'Reruns preserve edited text, cleared image and SEO.' );
	wp_set_current_user( 0 );
	fcc_managed_check( is_wp_error( project_core_fcc_initialize_content_management() ), 'Anonymous migration denied.' );
} finally { $sessions->destroy( $token ); }
echo "PASS: {$checks} management fields/native editor/security/render/migration checks.\n";
