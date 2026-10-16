<?php
/** Check repeated initialization only on a marked, isolated installation. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' );
if ( ! $root || realpath( $root ) === realpath( dirname( __DIR__, 3 ) ) ) { throw new RuntimeException( 'Isolated PROJECT_WORDPRESS_ROOT required.' ); }
require $root . '/wp-load.php';
if ( ! get_option( 'project_core_fcc_test_install' ) ) { throw new RuntimeException( 'Marked fixture required.' ); }
set_exception_handler( static function ( $error ) { fwrite( STDERR, $error->getMessage() . PHP_EOL ); exit( 1 ); } );
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( $admins[0] );
add_filter( 'pre_wp_mail', '__return_true' );
$checks = 0;
function fcc_idempotency_check( $ok, $label ) {
	global $checks;
	if ( ! $ok ) { throw new RuntimeException( $label ); }
	++$checks;
}
function fcc_idempotency_snapshot() {
	global $wpdb;
	$posts = $wpdb->get_results( "SELECT ID,post_type,post_status,post_title,post_name,post_content,post_excerpt,menu_order FROM {$wpdb->posts} ORDER BY ID", ARRAY_A );
	$meta = $wpdb->get_results( "SELECT meta_id,post_id,meta_key,meta_value FROM {$wpdb->postmeta} ORDER BY meta_id", ARRAY_A );
	return array( 'posts' => $posts, 'meta' => $meta );
}
function fcc_idempotency_duplicates( $key ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( "SELECT p.post_type,m.meta_value,COUNT(DISTINCT p.ID) AS records FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON m.post_id=p.ID WHERE m.meta_key=%s AND m.meta_value<>'' GROUP BY p.post_type,m.meta_value HAVING COUNT(DISTINCT p.ID)>1", $key ), ARRAY_A );
}
$pending = get_option( 'project_core_fcc_autosetup_pending' );
$original_lock = get_option( 'project_core_fcc_setup_lock' );
fcc_idempotency_check( ! $original_lock && ! $pending, 'Fixture must begin without a pending initializer or active worker.' );
$baseline = fcc_idempotency_snapshot();
$runs = array();
try {
	for ( $run = 1; $run <= 3; ++$run ) {
		$result = project_core_fcc_initialize();
		fcc_idempotency_check( ! is_wp_error( $result ), 'Repeated initialization succeeds.' );
		fcc_idempotency_check( $baseline === fcc_idempotency_snapshot(), 'Every existing post, metadata row and ID remains identical.' );
		fcc_idempotency_check( ! fcc_idempotency_duplicates( '_fcc_source_key' ), 'No duplicate content source keys.' );
		fcc_idempotency_check( ! fcc_idempotency_duplicates( '_fcc_asset_source' ), 'No duplicate media source paths.' );
		$runs[] = array( 'run' => $run, 'posts' => count( $baseline['posts'] ), 'metadataRows' => count( $baseline['meta'] ), 'sameIdsAndData' => true );
	}
	project_core_fcc_queue_initial_defaults();
	project_core_fcc_queue_initial_defaults();
	fcc_idempotency_check( ! get_option( 'project_core_fcc_autosetup_pending' ), 'Plugin/theme activation callbacks do not queue an initialized site.' );
	project_core_fcc_maybe_initialize_defaults();
	project_core_fcc_maybe_initialize_defaults();
	fcc_idempotency_check( $baseline === fcc_idempotency_snapshot(), 'Repeated automatic callbacks preserve existing data.' );
	$lock = array( 'started' => time(), 'token' => wp_generate_uuid4() );
	add_option( 'project_core_fcc_setup_lock', $lock, '', false );
	$result = project_core_fcc_initialize();
	fcc_idempotency_check( is_wp_error( $result ) && 'fcc_setup_busy' === $result->get_error_code(), 'Another active initializer blocks a second worker.' );
	project_core_fcc_release_setup_lock( array( 'started' => time() - HOUR_IN_SECONDS, 'token' => 'different-worker' ) );
	fcc_idempotency_check( $lock === get_option( 'project_core_fcc_setup_lock' ), 'A different worker cannot clear an active lock.' );
	project_core_fcc_release_setup_lock( $lock );
	fcc_idempotency_check( $baseline === fcc_idempotency_snapshot(), 'Blocked initialization creates no rows.' );
	update_option( 'project_core_fcc_autosetup_pending', 1, false );
	project_core_fcc_maybe_initialize_defaults();
	fcc_idempotency_check( ! get_option( 'project_core_fcc_autosetup_pending' ), 'A leftover automatic queue clears after successful reconciliation.' );
	fcc_idempotency_check( $baseline === fcc_idempotency_snapshot(), 'Processing a leftover queue creates no duplicate data.' );
	wp_set_current_user( 0 );
	$result = project_core_fcc_initialize();
	fcc_idempotency_check( is_wp_error( $result ) && 'fcc_permissions' === $result->get_error_code(), 'Anonymous initialization is rejected.' );
	fcc_idempotency_check( $baseline === fcc_idempotency_snapshot(), 'Rejected caller does not alter existing rows.' );
} finally {
	wp_set_current_user( $admins[0] );
	delete_option( 'project_core_fcc_autosetup_pending' );
	delete_option( 'project_core_fcc_setup_lock' );
}
echo wp_json_encode( array( 'result' => 'PASS', 'checks' => $checks, 'fixture' => home_url(), 'seedVersion' => get_option( 'project_core_fcc_seed_version' ), 'repeatedRuns' => $runs, 'duplicateContentSources' => fcc_idempotency_duplicates( '_fcc_source_key' ), 'duplicateMediaSources' => fcc_idempotency_duplicates( '_fcc_asset_source' ) ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
