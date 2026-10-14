<?php
/**
 * Minify CSS and JS assets in project-child.
 */

$themeDir = dirname( __DIR__ ) . '/wp-content/themes/project-child/assets/fractional/';

// 1. Minify style.css
$cssPath = $themeDir . 'assets/css/style.css';
if ( file_exists( $cssPath ) ) {
    $css = file_get_contents( $cssPath );
    // Strip comments
    $min = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
    // Normalize space
    $min = str_replace( ["\r\n", "\r", "\n", "\t"], ' ', $min );
    $min = preg_replace( '/\s+/', ' ', $min );
    $min = preg_replace( '/\s*([:;{}])\s*/', '$1', $min );
    $min = preg_replace( '/;}/', '}', $min );
    file_put_contents( $themeDir . 'assets/css/style.min.css', trim( $min ) );
    printf( "style.css: %d KB -> style.min.css: %d KB\n", round( filesize( $cssPath ) / 1024 ), round( strlen( $min ) / 1024 ) );
}

// 2. Minify responsive.css
$respPath = $themeDir . 'assets/css/responsive.css';
if ( file_exists( $respPath ) ) {
    $resp = file_get_contents( $respPath );
    $min = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $resp );
    $min = str_replace( ["\r\n", "\r", "\n", "\t"], ' ', $min );
    $min = preg_replace( '/\s+/', ' ', $min );
    $min = preg_replace( '/\s*([:;{}])\s*/', '$1', $min );
    $min = preg_replace( '/;}/', '}', $min );
    file_put_contents( $themeDir . 'assets/css/responsive.min.css', trim( $min ) );
    printf( "responsive.css: %d KB -> responsive.min.css: %d KB\n", round( filesize( $respPath ) / 1024 ), round( strlen( $min ) / 1024 ) );
}

// 3. Minify main.js
$jsPath = $themeDir . 'main.js';
if ( file_exists( $jsPath ) ) {
    $js = file_get_contents( $jsPath );
    // Strip multi-line comments
    $js = preg_replace( '/\/\*[\s\S]*?\*\//', '', $js );
    // Strip single-line comments
    $lines = explode( "\n", $js );
    $cleanLines = [];
    foreach ( $lines as $line ) {
        $trimmed = trim( $line );
        if ( 0 === strpos( $trimmed, '//' ) ) {
            continue;
        }
        $cleanLines[] = $line;
    }
    $min = implode( "\n", $cleanLines );
    // Compact double empty lines
    $min = preg_replace( '/\n\s*\n/', "\n", $min );
    file_put_contents( $themeDir . 'main.min.js', trim( $min ) );
    printf( "main.js: %d KB -> main.min.js: %d KB\n", round( filesize( $jsPath ) / 1024 ), round( strlen( $min ) / 1024 ) );
}

// One blocking stylesheet; font URLs already resolve relative to the bundle directory.
$bundle = '';
foreach ( array( 'fonts.css', 'assets/css/style.min.css', 'assets/css/responsive.min.css', 'wordpress.css' ) as $part ) {
    $css = file_get_contents( $themeDir . $part );
    $css = preg_replace( '!/\*.*?\*/!s', '', $css );
    $css = preg_replace( '/\s+/', ' ', $css );
    $css = preg_replace( '/\s*([{};])\s*/', '$1', $css );
    $bundle .= trim( $css ) . "\n";
}
file_put_contents( $themeDir . 'site.min.css', $bundle );
echo "Minification and site CSS bundle completed!\n";
