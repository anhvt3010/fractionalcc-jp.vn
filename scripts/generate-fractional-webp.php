<?php
/** Build portable theme image derivatives; never modify originals or private uploads. */
if ( 'cli' !== PHP_SAPI ) { exit( 1 ); }
if ( ! function_exists( 'imagewebp' ) ) { throw new RuntimeException( 'GD WebP support required.' ); }
$root = dirname( __DIR__ ) . '/wp-content/themes/project-child/assets/fractional/';
$manifest = array();
$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . 'assets/images', FilesystemIterator::SKIP_DOTS ) );
foreach ( $files as $file ) {
	if ( ! $file->isFile() || ! preg_match( '/\.(png|jpe?g)$/i', $file->getFilename() ) ) { continue; }
	$source = $file->getPathname();
	$relative = str_replace( '\\', '/', substr( $source, strlen( $root ) ) );
	$dimensions = getimagesize( $source );
	if ( ! $dimensions ) { throw new RuntimeException( 'Invalid source image: ' . $relative ); }
	$max = false !== strpos( $relative, '/feedback/' ) ? 720 : 1200;
	if ( false !== strpos( $relative, 'bui-dang-jp-logo' ) ) { $max = 144; }
	elseif ( false !== strpos( $relative, 'fractional-cc-logo' ) ) { $max = 500; }
	$widths = array( min( $max, $dimensions[0] ) );
	foreach ( array( 480, 768 ) as $width ) {
		if ( $width < $widths[0] ) { $widths[] = $width; }
	}
	$srcset = array();
	$extension_suffix = preg_match( '/\.jpe?g$/i', $source ) && is_file( preg_replace( '/\.jpe?g$/i', '.png', $source ) ) ? '-jpg' : '';
	foreach ( $widths as $index => $width ) {
		$destination = preg_replace( '/\.(png|jpe?g)$/i', $extension_suffix . ( 0 === $index ? '' : '-' . $width ) . '.webp', $source );
		$existing = is_file( $destination ) ? getimagesize( $destination ) : false;
		if ( ! $existing || $existing[0] !== $width || filemtime( $destination ) < filemtime( $source ) ) {
			$image = IMAGETYPE_PNG === $dimensions[2] ? imagecreatefrompng( $source ) : imagecreatefromjpeg( $source );
			if ( ! $image ) { throw new RuntimeException( 'Cannot decode: ' . $relative ); }
			$height = (int) round( $dimensions[1] * $width / $dimensions[0] );
			$target = imagecreatetruecolor( $width, $height );
			imagealphablending( $target, false );
			imagesavealpha( $target, true );
			imagecopyresampled( $target, $image, 0, 0, 0, 0, $width, $height, $dimensions[0], $dimensions[1] );
			if ( ! imagewebp( $target, $destination, 84 ) ) { throw new RuntimeException( 'Cannot write WebP.' ); }
			imagedestroy( $target );
			imagedestroy( $image );
		}
		$srcset[ $width ] = str_replace( '\\', '/', substr( $destination, strlen( $root ) ) );
	}
	ksort( $srcset, SORT_NUMERIC );
	$revision = hash( 'sha256', implode( '', array_map( static function ( $path ) use ( $root ) { return hash_file( 'sha256', $root . $path ); }, $srcset ) ) );
	$manifest[ hash_file( 'sha256', $source ) ] = array( 'source' => $relative, 'width' => $dimensions[0], 'height' => $dimensions[1], 'webp' => $srcset[ $widths[0] ], 'srcset' => $srcset, 'revision' => substr( $revision, 0, 12 ) );
}
ksort( $manifest );
file_put_contents( $root . 'image-variants.json', json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
echo 'Built portable WebP manifest for ' . count( $manifest ) . " original images; originals untouched.\n";
