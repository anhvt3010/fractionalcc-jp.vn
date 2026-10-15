<?php
/** Read-only integration checks for native Customizer image shortcuts and previews. */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }
$root = getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ );
require rtrim( $root, '/\\' ) . '/wp-load.php';
set_error_handler( function ( $severity, $message ) {
	if ( error_reporting() & $severity ) { throw new RuntimeException( $message ); }
	return false;
} );
$checks = 0;
function fcc_image_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	++$checks;
}
function fcc_image_section() {
	ob_start();
	get_template_part( 'template-parts/fractional/home/section-04' );
	return ob_get_clean();
}
$home = project_core_fcc_page_id( 'home' );
$key = 'fcc_ui_mechanism_routine_image';
$original = get_post_meta( $home, $key, true );
fcc_image_check( '' === project_child_fcc_image_edit_attributes( $key, 'home' ), 'Public HTML must not expose editor attributes.' );
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
fcc_image_check( ! empty( $admins ), 'An existing administrator is required.' );
wp_set_current_user( $admins[0] );
$native_hero = acf_prepare_field( acf_get_field( 'field_fcc_home_hero_product_image' ) );
$native_combo = project_core_fcc_featured_combo();
fcc_image_check( 'message' === $native_hero['type'] && '' === $native_hero['name'], 'Native homepage editor must not save an obsolete independent hero image.' );
fcc_image_check( false !== strpos( $native_hero['message'], esc_url( get_edit_post_link( $native_combo['id'], 'raw' ) ) ), 'Native hero instructions must link to the selected combo editor.' );
fcc_image_check( 'image' === acf_get_field( 'field_fcc_home_hero_product_image' )['type'], 'Preparing the native editor must not change the Customizer image schema.' );
require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
$GLOBALS['wp_customize'] = new WP_Customize_Manager( array( 'settings_previewed' => false ) );
$manager = $GLOBALS['wp_customize'];
$manager->register_controls();
project_child_fcc_customize_register( $manager );
$manager->start_previewing_theme();

foreach ( array( 'home', 'shared' ) as $scope ) {
	foreach ( project_child_fcc_schema()[ $scope ] as $fields ) {
		foreach ( $fields as $field ) {
			if ( 'image' !== $field['type'] ) { continue; }
			$id = project_child_fcc_customizer_id( $scope, $field['name'] );
			$setting = $manager->get_setting( $id );
			fcc_image_check( $manager->get_control( $id ) instanceof WP_Customize_Media_Control, 'Image controls must use the native Media library.' );
			fcc_image_check( 'refresh' === $setting->transport, 'Image previews must refresh responsive markup and alt text together.' );
			fcc_image_check( null !== $manager->selective_refresh->get_partial( $id ), 'Every registered image needs a native pencil partial.' );
		}
	}
}
$id = project_child_fcc_customizer_id( 'home', $key );
$setting = $manager->get_setting( $id );
fcc_image_check( false !== strpos( fcc_image_section(), 'data-fcc-image-setting="' . $id . '"' ), 'The usage image shown in the screenshot must have an edit shortcut.' );
$replacement = absint( get_post_meta( $home, 'fcc_ui_mechanism_breakthrough_image', true ) );
fcc_image_check( $replacement && $replacement !== (int) $original && wp_attachment_is_image( $replacement ), 'A distinct existing image is required for preview checks.' );
fcc_image_check( ! is_wp_error( $setting->validate( $replacement ) ), 'A valid Media image must be accepted.' );
foreach ( array( $home, -1, array( $replacement ), 'https://example.invalid/image.jpg' ) as $invalid ) {
	fcc_image_check( is_wp_error( $setting->validate( $invalid ) ), 'Invalid attachment input must be rejected.' );
}
fcc_image_check( ! is_wp_error( $setting->validate( 0 ) ), 'Native image removal must be permitted.' );
$manager->set_post_value( $id, $replacement );
$setting->preview();
fcc_image_check( $replacement === (int) get_post_meta( $home, $key, true ), 'Customizer preview must read the pending selection.' );
fcc_image_check( wp_get_attachment_image_url( $replacement, 'full' ) === project_child_fcc_image( $key, 'home' ), 'The renderer must use the pending selection.' );
$html = fcc_image_section();
$dom = new DOMDocument();
@$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
$xpath = new DOMXPath( $dom );
$images = $xpath->query( '//*[@data-fcc-image-setting="' . $id . '"]//img' );
fcc_image_check( 1 === $images->length && wp_get_attachment_image_url( $replacement, 'full' ) === $images->item( 0 )->getAttribute( 'src' ), 'The usage image must render the pending replacement.' );
$manager->set_post_value( $id, 0 );
$html = fcc_image_section();
$dom = new DOMDocument();
@$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
$xpath = new DOMXPath( $dom );
fcc_image_check( 0 === $xpath->query( '//*[@data-fcc-image-setting="' . $id . '"]//img' )->length, 'Removing the usage image must not leave a broken image.' );
fcc_image_check( false !== strpos( $html, 'data-fcc-image-setting="' . $id . '"' ), 'The image container must remain available for choosing a replacement.' );
$combo = project_core_fcc_featured_combo();
$hero_id = project_child_fcc_customizer_id( 'home', 'fcc_home_hero_product_image' );
$hero = $manager->get_setting( $hero_id );
fcc_image_check( $hero && $hero->post_id === $combo['id'] && 'fcc_image' === $hero->meta_key && 'field_fcc_combo_image' === $hero->acf_key, 'Hero image editor must write to the selected combo rather than an independent home override.' );
$original_combo_image = get_post_meta( $combo['id'], 'fcc_image', true );
$manager->set_post_value( $hero_id, $replacement );
$hero->preview();
fcc_image_check( wp_get_attachment_image_url( $replacement, 'full' ) === project_child_fcc_hero_product_image_url(), 'Hero must preview the selected combo image edit.' );
remove_filter( 'get_post_metadata', 'project_child_fcc_preview_metadata', 10 );
fcc_image_check( $original === get_post_meta( $home, $key, true ), 'Preview must leave the saved CMS image unchanged.' );
fcc_image_check( $original_combo_image === get_post_meta( $combo['id'], 'fcc_image', true ), 'Hero preview must leave the saved combo image unchanged.' );
wp_set_current_user( 0 );
fcc_image_check( '' === project_child_fcc_image_edit_attributes( $key, 'home' ), 'Anonymous visitors must not receive edit shortcuts even in a preview context.' );
fcc_image_check( is_wp_error( $setting->validate( $replacement ) ) && ! $setting->check_capabilities(), 'Unauthorized image changes must be denied.' );
echo "PASS: {$checks} read-only Customizer image controls, shortcuts, validation and preview checks.\n";
