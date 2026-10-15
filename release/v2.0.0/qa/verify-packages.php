<?php
/** Verify the shipped archives against their source and ZIP CRCs. CLI only. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
$root = dirname( __DIR__, 3 );
$release = dirname( __DIR__ );
$report = array( 'version' => '2.0.0', 'passed' => true, 'packages' => array() );
if ( 2 !== count( glob( $release . '/*.zip' ) ) ) { throw new RuntimeException( 'Exactly two ZIPs required.' ); }
foreach ( array( 'project-core' => 'plugins', 'project-child' => 'themes' ) as $slug => $kind ) {
    $file = $release . '/' . $slug . '-v2.0.0.zip';
    $source = $root . '/wp-content/' . $kind . '/' . $slug;
    $zip = new ZipArchive();
    if ( true !== $zip->open( $file, ZipArchive::CHECKCONS ) ) { throw new RuntimeException( 'Invalid archive.' ); }
    $entries = array();
    for ( $index = 0; $index < $zip->numFiles; ++$index ) {
        $stat = $zip->statIndex( $index );
        $name = $stat['name'];
        if ( 0 !== strpos( $name, $slug . '/' ) || false !== strpos( $name, '..' ) || false !== strpos( $name, '\\' ) || isset( $entries[ $name ] ) || preg_match( '#(^|/)(\.env[^/]*|wp-config\.php|node_modules|\.git|cache|backups|uploads)(/|$)|\.(log|sql|sql\.gz)$#', $name ) ) { throw new RuntimeException( 'Unsafe archive path: ' . $name ); }
        $bytes = $zip->getFromIndex( $index );
        $path = $source . substr( $name, strlen( $slug ) );
        if ( false === $bytes || (int) sprintf( '%u', crc32( $bytes ) ) !== $stat['crc'] || ! is_file( $path ) || hash( 'sha256', $bytes ) !== hash_file( 'sha256', $path ) ) { throw new RuntimeException( 'CRC/source mismatch: ' . $name ); }
        $entries[ $name ] = true;
    }
    $source_count = 0;
    foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source, FilesystemIterator::SKIP_DOTS ) ) as $entry ) { if ( $entry->isFile() ) { ++$source_count; } }
    if ( $source_count !== count( $entries ) ) { throw new RuntimeException( 'Incomplete archive.' ); }
    $report['packages'][] = array( 'file' => basename( $file ), 'root' => $slug . '/', 'files' => count( $entries ), 'bytes' => filesize( $file ), 'sha256' => hash_file( 'sha256', $file ), 'crc_and_source_bytes' => 'passed' );
    $zip->close();
}
$data = json_decode( file_get_contents( $root . '/wp-content/plugins/project-core/defaults/fractional.json' ), true );
if ( '2.0.0' !== $data['version'] || 'approved' !== $data['approval']['status'] ) { throw new RuntimeException( 'Approved version mismatch.' ); }
$report['defaults'] = array( 'pages' => count( $data['pages'] ), 'page_fields' => array_sum( array_map( static function ( $page ) { return count( $page['fields'] ); }, $data['pages'] ) ), 'products' => count( $data['products'] ), 'combos' => count( $data['combos'] ), 'media' => count( $data['media'] ), 'sha256' => hash_file( 'sha256', $root . '/wp-content/plugins/project-core/defaults/fractional.json' ) );
file_put_contents( __DIR__ . '/package-report.json', json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL );
echo json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
