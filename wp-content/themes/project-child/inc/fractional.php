<?php
/** Fractional CC presentation bootstrap. @package Project_Child */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/fractional-render.php';
require_once __DIR__ . '/fractional-components.php';
require_once __DIR__ . '/fractional-spotlight.php';

function project_child_fcc_kind() {
	if ( is_singular( 'fcc_product' ) ) { return 'product'; }
	$template = get_page_template_slug();
	if ( preg_match( '#^templates/fractional-(home|catalog|policies|thankyou)\.php$#', (string) $template, $match ) ) { return $match[1]; }
	return '';
}

function project_child_fcc_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	$groups = project_child_fcc_schema();
	$labels = array( 'shared' => 'Thông tin chung', 'home' => 'Trang chủ', 'catalog' => 'Danh mục', 'policies' => 'Chính sách', 'thankyou' => 'Cảm ơn' );
	foreach ( (array) $groups as $kind => $sections ) {
		$fields = array();
		foreach ( $sections as $section => $section_fields ) {
			$fields[] = array( 'key' => 'field_fcc_tab_' . md5( $kind . $section ), 'label' => $section, 'type' => 'tab', 'placement' => 'left' );
			$fields = array_merge( $fields, array_values( $section_fields ) );
		}
		$location = 'shared' === $kind ? array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_site' ) : array( 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/fractional-' . $kind . '.php' );
		acf_add_local_field_group( array( 'key' => 'group_fcc_ui_' . $kind, 'title' => 'Nội dung giao diện – ' . $labels[ $kind ], 'fields' => $fields, 'location' => array( array( $location ) ), 'position' => 'normal', 'hide_on_screen' => 'shared' === $kind ? array() : array( 'the_content' ), 'show_in_rest' => 0 ) );
	}
}
add_action( 'acf/init', 'project_child_fcc_register_fields' );

function project_child_fcc_schema() {
	static $schema;
	if ( null === $schema ) {
		$schema = json_decode( file_get_contents( __DIR__ . '/fractional-fields.json' ), true );
		// Individual card content is now edited on the selected product/combo.
		foreach ( isset( $schema['home'] ) ? $schema['home'] : array() as $section => $fields ) {
			$schema['home'][ $section ] = array_diff_key( $fields, array_flip( array( 'fcc_ui_ffed63b9bad263', 'fcc_ui_3c6bdffa7d9479', 'fcc_ui_5fdb1175c7533c', 'fcc_ui_6b5c492b8ee8c7', 'fcc_ui_46606573b70ffc', 'fcc_ui_4177f27e6977cf' ) ) );
			if ( isset( $fields['fcc_ui_cc3c2901a4cef8'] ) ) {
				$schema['home'][ $section ] = array_intersect_key( $fields, array_flip( array( 'fcc_ui_1bde33c83730c6', 'fcc_ui_cc3c2901a4cef8', 'fcc_ui_a12e257cd86c9f' ) ) );
			}
		}
	}
	return is_array( $schema ) ? $schema : array();
}

function project_child_fcc_prime_images() {
	$schema = project_child_fcc_schema();
	$ids = array();
	foreach ( isset( $schema['shared'] ) ? $schema['shared'] : array() as $section ) {
		foreach ( $section as $field ) {
			if ( 'image' === $field['type'] ) { $ids[] = absint( get_post_meta( project_core_fcc_page_id( 'shared' ), $field['name'], true ) ); }
		}
	}
	$ids = array_values( array_unique( array_filter( $ids ) ) );
	if ( $ids ) { get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post__in' => $ids, 'posts_per_page' => count( $ids ), 'no_found_rows' => true ) ); }
}

function project_child_fcc_enqueue() {
	$kind = project_child_fcc_kind();
	if ( ! $kind ) { return; }
	if ( function_exists( 'project_core_fcc_page_id' ) ) { project_child_fcc_prime_images(); }
	// These pages supply the complete supplied design, including header/footer.
	foreach ( wp_styles()->queue as $handle ) {
		if ( 0 === strpos( $handle, 'kadence' ) || in_array( $handle, array( 'global-styles', 'wp-block-library', 'classic-theme-styles', 'project-child-main' ), true ) ) { wp_dequeue_style( $handle ); }
	}
	$base = get_stylesheet_directory_uri() . '/assets/fractional/';
	$styles = array( 'design' => 'assets/css/style.css', 'responsive' => 'assets/css/responsive.css', 'integration' => 'wordpress.css' );
	if ( file_exists( get_stylesheet_directory() . '/assets/fractional/' . $kind . '.css' ) ) { $styles[ $kind ] = $kind . '.css'; }
	foreach ( $styles as $name => $path ) {
		wp_enqueue_style( 'fcc-' . $name, $base . $path, 'design' === $name ? array() : array( 'fcc-design' ), (string) filemtime( get_stylesheet_directory() . '/assets/fractional/' . $path ) );
	}
	wp_enqueue_script( 'fcc-interactions', $base . 'main.js', array(), (string) filemtime( get_stylesheet_directory() . '/assets/fractional/main.js' ), true );
	if ( function_exists( 'project_core_fcc_frontend_data' ) ) {
		$configuration = array(
			'kind'        => $kind,
			'productId'   => 'product' === $kind ? get_queried_object_id() : 0,
			'orderMode'   => get_option( 'project_core_fcc_order_mode', 'smtp' ),
			'endpoint'    => admin_url( 'admin-post.php' ),
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'fcc_order_action' ),
			'thankyouUrl' => project_core_fcc_url( 'thankyou' ),
			'homeUrl'     => project_core_fcc_url( 'home' ),
		);
		wp_add_inline_script( 'fcc-interactions', 'window.FCC_DATA=' . wp_json_encode( project_core_fcc_frontend_data( in_array( $kind, array( 'home', 'catalog', 'product' ), true ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';window.FCC_CONFIG=' . wp_json_encode( $configuration, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}
}
add_action( 'wp_enqueue_scripts', 'project_child_fcc_enqueue', 100 );

function project_child_fcc_render( $kind ) {
	if ( ! function_exists( 'project_core_fcc_page_id' ) ) {
		status_header( 503 );
		$message = current_user_can( 'manage_options' ) ? 'Cần kích hoạt Project Core để hiển thị website.' : 'Trang web tạm thời chưa sẵn sàng.';
		wp_die( esc_html( $message ), '', array( 'response' => 503 ) );
	}
	remove_action( 'wp_head', 'rel_canonical' );
	?><!doctype html>
	<html lang="vi"><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
	<body <?php body_class( 'fcc-site' ); ?>><?php wp_body_open(); ?>
	<?php get_template_part( 'template-parts/fractional/' . $kind ); ?>
	<?php wp_footer(); ?></body></html><?php
}

function project_child_fcc_title( $title ) {
	$kind = project_child_fcc_kind();
	if ( ! $kind || ! function_exists( 'project_core_fcc_page_id' ) ) { return $title; }
	if ( 'product' === $kind ) { return get_the_title( get_queried_object_id() ) . ' | ' . project_core_fcc_site( 'name' ); }
	$value = get_post_meta( project_core_fcc_page_id( $kind ), 'fcc_' . $kind . '_seo_title', true );
	return $value ?: $title;
}
add_filter( 'pre_get_document_title', 'project_child_fcc_title' );

function project_child_fcc_metadata() {
	$kind = project_child_fcc_kind();
	if ( ! $kind || ! function_exists( 'project_core_fcc_page_id' ) ) { return; }
	$description = 'product' === $kind ? project_child_fcc_product_value( 'tagline' ) : get_post_meta( project_core_fcc_page_id( $kind ), 'fcc_' . $kind . '_seo_description', true );
	$url = get_permalink( get_queried_object_id() );
	$image = 'product' === $kind ? project_child_fcc_product_value( 'image' ) : project_child_fcc_asset( 'assets/images/banner/combo-c-cx4.png' );
	echo '<meta name="description" content="' . esc_attr( $description ) . '"><link rel="canonical" href="' . esc_url( $url ) . '">';
	echo '<meta property="og:type" content="website"><meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '"><meta property="og:description" content="' . esc_attr( $description ) . '"><meta property="og:url" content="' . esc_url( $url ) . '"><meta property="og:image" content="' . esc_url( $image ) . '"><meta name="twitter:card" content="summary_large_image">';
	echo '<link rel="icon" href="' . esc_url( project_child_fcc_asset( 'assets/images/brand/bui-dang-jp-logo.png' ) ) . '">';
}
add_action( 'wp_head', 'project_child_fcc_metadata', 2 );

function project_child_fcc_robots( $robots ) {
	if ( project_child_fcc_kind() && function_exists( 'project_core_fcc_site' ) && ( 'approved' !== project_core_fcc_site( 'content_status' ) || 'thankyou' === project_child_fcc_kind() ) ) {
		$robots['noindex'] = true; unset( $robots['index'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'project_child_fcc_robots' );

function project_child_fcc_block_editor( $use, $post ) {
	return 'page' === $post->post_type && 0 === strpos( (string) get_page_template_slug( $post->ID ), 'templates/fractional-' ) ? false : $use;
}
add_filter( 'use_block_editor_for_post', 'project_child_fcc_block_editor', 10, 2 );
