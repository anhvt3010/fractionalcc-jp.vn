<?php
/** Read-only comparison of packaged Excel source, defaults and WordPress catalog. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ );
require rtrim( $root, '/\\' ) . '/wp-load.php';
$defaults = project_core_fcc_default_document();
$source = json_decode( file_get_contents( WP_PLUGIN_DIR . '/project-core/defaults/catalog-excel.json' ), true );
$checks = 0;
function fcc_catalog_check( $condition, $label ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $label ); }
	++$checks;
}
$expected_prices = array( 628000, 386000, 810000, 910000, 386000, 790000, 910000, 630000, 445000, 445000 );
$mapping = array( 'brand' => 'Thương hiệu', 'category' => 'Phân loại', 'origin' => 'Xuất xứ', 'tagline' => 'Công dụng', 'ingredientsText' => 'Thành phần chính', 'legalStatus' => 'Tình trạng pháp lý', 'cbmp' => 'Số Công bố mỹ phẩm', 'notes' => 'Ghi chú' );
fcc_catalog_check( 10 === count( $source['rows'] ) && 10 === count( $defaults['products'] ), 'Exactly ten Excel/default product records required.' );
foreach ( $source['rows'] as $row ) {
	$id = project_core_fcc_legacy_product_id( $row['id'] );
	$product = project_core_fcc_product( $id );
	$default = $defaults['products'][ $row['id'] - 1 ];
	fcc_catalog_check( (int) $default['id'] === (int) $row['id'], 'Product identity/order mismatch.' );
	fcc_catalog_check( $product['name'] === $row['values']['Tên sản phẩm'] && $default['name'] === $product['name'], 'Product title mismatch.' );
	foreach ( $mapping as $key => $column ) {
		$expected = $row['values'][ $column ];
		// Approved copy review removed this slogan; the original Excel remains unchanged.
		if ( 'tagline' === $key && in_array( (int) $row['id'], array( 1, 8 ), true ) ) { $expected = str_replace( ' chỉ trong 1 bước', '', $expected ); }
		// Native textarea saves use CRLF; Excel imports use LF. Compare the same text.
		$expected = str_replace( array( "\r\n", "\r" ), "\n", $expected );
		fcc_catalog_check( str_replace( array( "\r\n", "\r" ), "\n", $product[ $key ] ) === $expected, 'Runtime/source mismatch: ' . $row['id'] . '/' . $key );
		fcc_catalog_check( str_replace( array( "\r\n", "\r" ), "\n", $default[ $key ] ) === $expected, 'Default/source mismatch: ' . $row['id'] . '/' . $key );
	}
	foreach ( array( 'targetUsers' => 'Đối tượng phù hợp', 'uspBullets' => 'USP / Điểm nổi bật' ) as $key => $column ) {
		$expected = project_core_fcc_lines( $row['values'][ $column ] );
		fcc_catalog_check( $product[ $key ] === $expected && project_core_fcc_lines( $default[ $key ] ) === $expected, 'Multiline content mismatch: ' . $row['id'] . '/' . $key );
	}
	fcc_catalog_check( (float) $product['salePrice'] === (float) $expected_prices[ $row['id'] - 1 ] && (float) $default['salePrice'] === (float) $expected_prices[ $row['id'] - 1 ], 'Sale price mismatch.' );
	$expected_original = 1 === (int) $row['id'] ? 850000 : null;
	$default_original = '' === $default['originalPrice'] || null === $default['originalPrice'] ? null : (float) $default['originalPrice'];
	fcc_catalog_check( $product['originalPrice'] === ( null === $expected_original ? null : (float) $expected_original ) && $default_original === ( null === $expected_original ? null : (float) $expected_original ), 'Original price/blank mismatch.' );
	$image_id = absint( get_post_meta( $id, 'fcc_image', true ) );
	// Product photos were replaced after the Excel import; verify the approved media manifest.
	$expected_hash = $defaults['media'][ $default['image'] ]['sha256'];
	fcc_catalog_check( get_post_meta( $image_id, '_fcc_asset_source', true ) === $default['image'], 'Runtime image is not mapped to the approved product photo.' );
	fcc_catalog_check( hash_file( 'sha256', get_attached_file( $image_id ) ) === $expected_hash, 'Uploaded image bytes differ from approved media.' );
	fcc_catalog_check( hash_file( 'sha256', get_stylesheet_directory() . '/assets/fractional/' . $default['image'] ) === $expected_hash, 'Packaged image bytes differ from approved media.' );
	$actives = $product['actives'];
	foreach ( $actives as &$active ) { $active['name'] = html_entity_decode( $active['name'], ENT_QUOTES, 'UTF-8' ); }
	unset( $active );
	$expected_actives = array_map( static function ( $active ) { return array_intersect_key( $active, array_flip( array( 'name', 'tag', 'desc' ) ) ); }, $default['actives'] );
	fcc_catalog_check( $actives === $expected_actives, 'Ingredient cards were not updated fully.' );
	foreach ( array( 'spotlightTitle', 'spotlightDescription', 'spotlightHighlights' ) as $field ) {
		fcc_catalog_check( get_post_meta( $id, 'fcc_' . $field, true ) === $default[ $field ], 'Featured card content mismatch.' );
	}
	fcc_catalog_check( count( $product['gallery'] ) >= 1 && $product['gallery'][0] === $product['image'], 'Main image must be the first gallery image.' );
	echo 'PASS: Product ' . $row['id'] . ' — ' . $product['name'] . PHP_EOL;
}
$combos = project_core_fcc_combos();
$combo = array_values( array_filter( $combos, function ( $item ) { return 'C-CX4' === $item['code']; } ) );
fcc_catalog_check( 1 === count( $combo ), 'Exactly one C–CX4 combo required.' );
$combo = $combo[0];
fcc_catalog_check( 1237500.0 === $combo['salePrice'] && 1700000.0 === $combo['originalPrice'] && 462500.0 === $combo['saveAmount'], 'Combo sale/original/saving mismatch.' );
fcc_catalog_check( $combo['productIds'] === array( project_core_fcc_legacy_product_id( 6 ), project_core_fcc_legacy_product_id( 7 ) ), 'Combo references incorrect products.' );
fcc_catalog_check( (float) $defaults['combos'][0]['salePrice'] === 1237500.0 && (float) $defaults['combos'][0]['originalPrice'] === 1700000.0 && (float) $defaults['combos'][0]['saveAmount'] === 462500.0, 'Default combo pricing mismatch.' );
echo 'PASS: C–CX4 combo; ' . $checks . ' checks passed.' . PHP_EOL;
