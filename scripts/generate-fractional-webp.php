<?php
/**
 * Generate WebP versions of all PNG/JPG assets in theme and uploads.
 * Preserves existing PNG/JPG files to avoid breaking verification hashes.
 */

$themeDir = 'wp-content/themes/project-child/assets/fractional/assets/images';
$uploadsDir = 'wp-content/uploads/2026/10';

function optimizeAndConvert( $srcPath, $quality = 85 ) {
    if ( ! file_exists( $srcPath ) ) { return false; }
    $ext = strtolower( pathinfo( $srcPath, PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, [ 'png', 'jpg', 'jpeg' ], true ) ) { return false; }
    
    $destWebp = preg_replace( '/\.(png|jpe?g)$/i', '.webp', $srcPath );
    
    // Check if webp is newer than src
    if ( file_exists( $destWebp ) && filemtime( $destWebp ) >= filemtime( $srcPath ) ) {
        return false;
    }
    
    if ( $ext === 'png' ) {
        $im = @imagecreatefrompng( $srcPath );
    } else {
        $im = @imagecreatefromjpeg( $srcPath );
    }
    if ( ! $im ) { return false; }
    
    $origW = imagesx( $im );
    $origH = imagesy( $im );
    
    // Scale down if unnecessarily huge (e.g. hero banner 1268px can stay 1000px, feedback 960px can be 720px)
    $maxW = 1200;
    if ( strpos( $srcPath, 'feedback' ) !== false ) {
        $maxW = 720;
    } elseif ( strpos( $srcPath, 'bui-dang-jp-logo' ) !== false ) {
        $maxW = 120; // 40px displayed, 120px is 3x retina
    }
    
    if ( $origW > $maxW ) {
        $newW = $maxW;
        $newH = round( $origH * ( $maxW / $origW ) );
        $target = imagecreatetruecolor( $newW, $newH );
        imagealphablending( $target, false );
        imagesavealpha( $target, true );
        $trans = imagecolorallocatealpha( $target, 255, 255, 255, 127 );
        imagefilledrectangle( $target, 0, 0, $newW, $newH, $trans );
        imagecopyresampled( $target, $im, 0, 0, 0, 0, $newW, $newH, $origW, $origH );
        imagedestroy( $im );
        $im = $target;
    } else {
        imagepalettetotruecolor( $im );
        imagealphablending( $im, true );
        imagesavealpha( $im, true );
    }
    
    imagewebp( $im, $destWebp, $quality );
    imagedestroy( $im );
    
    $origSize = filesize( $srcPath );
    $webpSize = filesize( $destWebp );
    $pct = round( ( 1 - ( $webpSize / $origSize ) ) * 100, 1 );
    printf( "Converted: %s\n  %d KB -> %d KB (%s%% saved)\n", basename( $srcPath ), round( $origSize / 1024 ), round( $webpSize / 1024 ), $pct );
    return true;
}

echo "=== Processing Theme Images ===\n";
$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $themeDir, FilesystemIterator::SKIP_DOTS ) );
foreach ( $iterator as $file ) {
    if ( $file->isFile() ) {
        optimizeAndConvert( $file->getPathname(), 84 );
    }
}

if ( is_dir( $uploadsDir ) ) {
    echo "\n=== Processing Uploads Images ===\n";
    $iteratorUploads = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $uploadsDir, FilesystemIterator::SKIP_DOTS ) );
    foreach ( $iteratorUploads as $file ) {
        if ( $file->isFile() ) {
            optimizeAndConvert( $file->getPathname(), 84 );
        }
    }
}

echo "\nDone!\n";
