<?php
/** Exercise portable uploaded-video defaults on a marked fixture only; restore all temporary data. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__ ) ) ) { throw new RuntimeException( 'Isolated fixture required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( $admins[0] );
$fixture = $argv[1] ?? '';
if ( ! is_readable( $fixture ) ) { throw new RuntimeException( 'Local MP4 test fixture required.' ); }
$checks = 0;
function fcc_video_import_check( $condition, $label ) { global $checks; if ( ! $condition ) { throw new RuntimeException( $label ); } ++$checks; }
$defaults_path = WP_PLUGIN_DIR . '/project-core/defaults/fractional.json'; $original = file_get_contents( $defaults_path );
$path = 'assets/videos/approved/qa-video-import.mp4';
$target = get_stylesheet_directory() . '/assets/fractional/' . $path;
if ( file_exists( $target ) ) { throw new RuntimeException( 'New fixture asset required.' ); }
wp_mkdir_p( dirname( $target ) ); copy( $fixture, $target );
$post_id = 0; $attachment_id = 0;
try {
	$data = json_decode( $original, true );
	$data['media'][ $path ] = array( 'sha256' => hash_file( 'sha256', $target ), 'title' => 'QA video import', 'alt' => '', 'caption' => '', 'description' => '' );
	$field = array( 'type' => 'file', 'key' => 'field_fcc_ui_brand_video_file', 'value' => $path );
	$data['pages']['home']['fields']['fcc_ui_brand_video_file'] = $field;
	fcc_video_import_check( ! is_wp_error( project_core_fcc_validate_defaults( $data ) ), 'Packaged MP4 metadata/hash accepted.' );
	$invalid = $data; $invalid['pages']['home']['fields']['fcc_ui_brand_video_file']['value'] = '123';
	fcc_video_import_check( is_wp_error( project_core_fcc_validate_defaults( $invalid ) ), 'Installation-specific media ID rejected.' );
	$invalid = $data; $invalid['pages']['home']['fields']['fcc_ui_brand_video_file']['value'] = array( $path );
	fcc_video_import_check( is_wp_error( project_core_fcc_validate_defaults( $invalid ) ), 'Malformed media path rejected.' );
	fcc_video_import_check( '' === project_core_fcc_media_path_type( 'assets/videos/../wp-config.php' ), 'Media traversal rejected.' );
	$invalid = $data; $invalid['media'][ $path ]['sha256'] = str_repeat( '0', 64 );
	fcc_video_import_check( is_wp_error( project_core_fcc_validate_defaults( $invalid ) ), 'Modified video bytes rejected.' );
	file_put_contents( $defaults_path, wp_json_encode( $data ) );
	$post_id = wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'QA temporary video import', 'post_status' => 'draft' ), true );
	fcc_video_import_check( ! is_wp_error( $post_id ), 'Temporary fixture page created.' );
	fcc_video_import_check( true === project_core_fcc_seed_snapshot_fields( $post_id, array( 'fcc_ui_brand_video_file' => $field ) ), 'Portable file field seeded.' );
	$attachment_id = (int) get_post_meta( $post_id, 'fcc_ui_brand_video_file', true );
	fcc_video_import_check( $attachment_id > 0 && 'video/mp4' === get_post_mime_type( $attachment_id ), 'Field maps to a real video attachment on the target site.' );
	fcc_video_import_check( hash_file( 'sha256', get_attached_file( $attachment_id ) ) === hash_file( 'sha256', $fixture ), 'Video bytes preserved.' );
	fcc_video_import_check( $attachment_id === project_core_fcc_seed_media( $path ), 'Repeated import reuses the same attachment.' );
	update_post_meta( $post_id, 'fcc_ui_brand_video_file', 0 );
	project_core_fcc_seed_snapshot_fields( $post_id, array( 'fcc_ui_brand_video_file' => $field ) );
	fcc_video_import_check( '0' === get_post_meta( $post_id, 'fcc_ui_brand_video_file', true ), 'Cleared video selection remains cleared on rerun.' );
} finally {
	file_put_contents( $defaults_path, $original );
	if ( is_int( $post_id ) && $post_id > 0 ) { wp_delete_post( $post_id, true ); }
	if ( $attachment_id ) { wp_delete_attachment( $attachment_id, true ); }
	wp_delete_file( $target );
}
echo 'PASS: ', $checks, ' portable video import, integrity, ID mapping and rerun checks; fixture restored.', PHP_EOL;
