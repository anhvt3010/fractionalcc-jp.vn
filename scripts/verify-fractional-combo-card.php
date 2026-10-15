<?php
/** Read-only regression checks for the homepage combo's live product data. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ );
require rtrim( $root, '/\\' ) . '/wp-load.php';
set_error_handler( function ( $severity, $message ) {
	if ( error_reporting() & $severity ) { throw new RuntimeException( $message ); }
	return false;
} );
$checks = 0;
function fcc_combo_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	++$checks;
}
function fcc_combo_render( $combo ) {
	ob_start();
	get_template_part( 'template-parts/fractional/components/combo-card', null, array( 'combo' => $combo, 'home' => true ) );
	return ob_get_clean();
}
$combos = project_core_fcc_combos();
fcc_combo_check( ! empty( $combos ), 'A published combo is required.' );
$combo = $combos[0];
$contents = project_core_fcc_combo_contents( $combo );
fcc_combo_check( count( $contents['products'] ) >= 2, 'Two published linked products are required.' );
$ids = array_column( $contents['products'], 'id' );
$html = fcc_combo_render( $combo );
foreach ( $contents['products'] as $product ) {
	fcc_combo_check( false !== strpos( $html, 'data-combo-product-id="' . $product['id'] . '"' ), 'Linked product must render.' );
	fcc_combo_check( false !== strpos( $html, esc_html( $product['name'] ) ), 'Product title must come from CMS.' );
	fcc_combo_check( false !== strpos( $html, esc_html( project_child_fcc_price( $product['salePrice'] ) ) ), 'Product price must come from CMS.' );
}
fcc_combo_check( false !== strpos( $html, esc_html( project_child_fcc_price( $combo['saveAmount'] ) ) ), 'Savings must use combo pricing.' );
fcc_combo_check( false !== strpos( $html, esc_html( $combo['gift'] ) ), 'Gift must retain the saved qualification.' );

// Filters and search must follow membership, not the old C-CX4 assumptions or gift copy.
$lip_products = project_core_fcc_posts( 'fcc_product' );
$lip_product = null;
foreach ( $lip_products as $post ) {
	$candidate = project_core_fcc_product( $post->ID, false );
	if ( 'lip' === $candidate['categoryType'] ) { $lip_product = $candidate; break; }
}
fcc_combo_check( null !== $lip_product, 'An existing lip product is required for filter checks.' );
$filter_combo = $combo;
$filter_combo['productIds'] = array( $lip_product['id'] );
foreach ( array( true, false ) as $home_card ) {
	ob_start();
	get_template_part( 'template-parts/fractional/components/combo-card', null, array( 'combo' => $filter_combo, 'home' => $home_card ) );
	$filter_html = ob_get_clean();
	$dom = new DOMDocument(); @$dom->loadHTML( '<?xml encoding="UTF-8">' . $filter_html ); $xp = new DOMXPath( $dom );
	$card = $xp->query( '//*[@data-type="combo"]' )->item( 0 );
	fcc_combo_check( $card && $card->getAttribute( 'data-concerns' ) === implode( ' ', array_unique( $lip_product['skinConcerns'] ) ), 'Combo filters must use the included product concerns on both card layouts.' );
	fcc_combo_check( false !== strpos( $card->getAttribute( 'data-search' ), mb_strtolower( $lip_product['name'] ) ), 'Combo search must include the actual linked product name.' );
}
$filter_combo['productIds'] = array();
$filter_html = fcc_combo_render( $filter_combo );
fcc_combo_check( false !== strpos( $filter_html, 'data-concerns=""' ), 'Empty membership must not inherit skin concerns from the previous combo or gift.' );

// Swap membership/order without touching database content.
$fixture = $combo;
$fixture['productIds'] = array_reverse( $ids );
$reversed = project_core_fcc_combo_contents( $fixture );
fcc_combo_check( array_reverse( $ids ) === array_column( $reversed['products'], 'id' ), 'Relationship order must be preserved.' );
$fixture['productIds'] = array( $ids[1] );
fcc_combo_check( array( $ids[1] ) === array_column( project_core_fcc_combo_contents( $fixture )['products'], 'id' ), 'Removing a selected product must remove its row.' );
$fixture['productIds'][] = project_core_fcc_page_id( 'home' );
fcc_combo_check( array( $ids[1] ) === array_column( project_core_fcc_combo_contents( $fixture )['products'], 'id' ), 'Invalid product references must not render.' );
$fixture['productIds'] = array();
fcc_combo_check( array( 'products' => array() ) === project_core_fcc_combo_contents( $fixture ), 'Empty membership must not resurrect template products.' );

// Simulate CMS edits at the metadata boundary, leaving the live database unchanged.
$test_price = 123456;
$price_filter = function ( $value, $post_id, $key, $single ) use ( $ids, &$test_price ) {
	if ( $ids[0] === (int) $post_id && 'fcc_salePrice' === $key ) { return $single ? $test_price : array( $test_price ); }
	return $value;
};
$title_filter = function ( $title, $post_id ) use ( $ids ) { return $ids[0] === (int) $post_id ? '<script>CMS title</script>' : $title; };
add_filter( 'get_post_metadata', $price_filter, 10, 4 );
add_filter( 'the_title', $title_filter, 10, 2 );
try {
	$updated = project_core_fcc_combo_contents( $combo );
	fcc_combo_check( (float) $test_price === $updated['products'][0]['salePrice'], 'A CMS price edit must update the linked product.' );
	$html = fcc_combo_render( $combo );
	fcc_combo_check( false !== strpos( $html, esc_html( project_child_fcc_price( $test_price ) ) ), 'Edited price must render immediately.' );
	fcc_combo_check( false !== strpos( $html, '&lt;script&gt;CMS title&lt;/script&gt;' ) && false === strpos( $html, '<script>CMS title</script>' ), 'Edited titles must render escaped.' );
	$test_price = '';
	fcc_combo_check( null === project_core_fcc_combo_contents( $combo )['products'][0]['salePrice'], 'Unknown product prices must not become zero.' );
	$html = fcc_combo_render( $combo );
	fcc_combo_check( false === strpos( $html, 'synergy-total-summary' ), 'The removed total must remain absent.' );
	fcc_combo_check( false !== strpos( $html, esc_html( project_child_fcc_price( null ) ) ), 'An unknown price must show the existing contact label.' );
	$test_price = 0;
	fcc_combo_check( 0.0 === project_core_fcc_combo_contents( $combo )['products'][0]['salePrice'], 'An explicit zero price must remain distinct from unknown.' );
} finally {
	remove_filter( 'get_post_metadata', $price_filter, 10 );
	remove_filter( 'the_title', $title_filter, 10 );
}
$fixture = $combo;
$fixture['gift'] = '';
$fixture['originalPrice'] = null;
$html = fcc_combo_render( $fixture );
fcc_combo_check( false === strpos( $html, 'synergy-gift-item' ), 'An empty gift must hide the gift row.' );
fcc_combo_check( false === strpos( $html, 'spotlight-save-badge' ) && false === strpos( $html, 'spotlight-price-del' ), 'Missing original price must hide savings and original price.' );
fcc_combo_check( false !== strpos( $html, 'data-product-select="combo_' . $combo['id'] . '"' ), 'Buy button must still select the actual combo.' );
echo "PASS: {$checks} read-only combo membership, pricing, escaping and rendering checks.\n";
