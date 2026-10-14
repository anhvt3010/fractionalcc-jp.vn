<?php
/** Fractional CC presentation bootstrap. @package Project_Child */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/fractional-render.php';
require_once __DIR__ . '/fractional-components.php';
require_once __DIR__ . '/fractional-spotlight.php';
require_once __DIR__ . '/fractional-admin.php';

function project_child_fcc_kind() {
	if ( is_singular( 'fcc_product' ) ) { return 'product'; }
	$template = get_page_template_slug();
	if ( preg_match( '#^templates/fractional-(home|catalog|policies|thankyou|concerns)\.php$#', (string) $template, $match ) ) { return $match[1]; }
	return '';
}

/** Public templates read metadata directly; register editor schemas only where needed. */
function project_child_fcc_needs_editor_fields() {
	return is_admin() || 'cli' === PHP_SAPI || isset( $GLOBALS['wp_customize'] );
}

function project_child_fcc_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) || ! project_child_fcc_needs_editor_fields() ) { return; }
	$groups = project_child_fcc_schema();
	$labels = array( 'shared' => 'Thông tin chung', 'home' => 'Trang chủ', 'catalog' => 'Danh mục', 'policies' => 'Chính sách', 'thankyou' => 'Cảm ơn', 'concerns' => 'Vấn đề làn da' );
	foreach ( (array) $groups as $kind => $sections ) {
		if ( 'shared' === $kind && is_admin() ) { $sections = project_child_fcc_admin_shared_sections( $sections ); }
		$fields = array();
		foreach ( $sections as $section => $section_fields ) {
			$fields[] = array( 'key' => 'field_fcc_tab_' . md5( $kind . $section ), 'label' => $section, 'type' => 'tab', 'placement' => 'left' );
			if ( 'home' === $kind && isset( $section_fields['fcc_ui_44137315906c33'] ) ) {
				$fields[] = array( 'key' => 'field_fcc_home_catalog_help', 'name' => '', 'label' => 'Chọn sản phẩm và combo hiển thị', 'type' => 'message', 'message' => 'Mở <a href="' . esc_url( admin_url( 'edit.php?post_type=fcc_product' ) ) . '">Sản phẩm</a> hoặc <a href="' . esc_url( admin_url( 'edit.php?post_type=fcc_combo' ) ) . '">Combo</a>, bật <strong>Hiển thị trong danh mục trang chủ</strong> và nhập thứ tự. Tối đa 6 mục tổng cộng; độc lập với 2 sản phẩm chủ lực. Tên, ảnh, mô tả và giá lấy từ sản phẩm/combo đã chọn.', 'new_lines' => '', 'esc_html' => 0 );
			}
			if ( 'concerns' === $kind && in_array( $section, array( '02. Đốm nâu & không đều màu', '03. Lỗ chân lông & da sần', '04. Lão hóa & nếp nhăn', '05. Thâm mụn sau viêm' ), true ) ) {
				$fields[] = array(
					'key'       => 'field_fcc_regimen_hint_' . md5( $section ),
					'name'      => '',
					'label'     => 'Sản phẩm gợi ý cho tình trạng da này',
					'type'      => 'message',
					'message'   => '💡 <em>Các sản phẩm / combo hiển thị trên website cho tình trạng da này được chọn và sắp xếp tại khung <strong>"Gợi ý phác đồ – sản phẩm theo tình trạng da"</strong> (nằm ở phía trên đầu trang, chọn tab tương ứng để thêm/bớt hoặc kéo thả đổi thứ tự).</em>',
					'new_lines' => '',
					'esc_html'  => 0,
				);
			}
			$fields = array_merge( $fields, array_values( $section_fields ) );
		}
		$location = 'shared' === $kind ? array( 'param' => 'post_type', 'operator' => '==', 'value' => 'fcc_site' ) : array( 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/fractional-' . $kind . '.php' );
		acf_add_local_field_group( array( 'key' => 'group_fcc_ui_' . $kind, 'title' => 'shared' === $kind && is_admin() ? 'Nội dung theo vị trí trên website' : 'Nội dung giao diện – ' . $labels[ $kind ], 'fields' => $fields, 'menu_order' => 'shared' === $kind ? 10 : 0, 'location' => array( array( $location ) ), 'position' => 'normal', 'hide_on_screen' => 'shared' === $kind ? array() : array( 'the_content' ), 'show_in_rest' => 0 ) );
	}
}
add_action( 'acf/init', 'project_child_fcc_register_fields' );

/** Native editor submissions must select real, supported media attachments. */
function project_child_fcc_validate_image_field( $valid, $value, $field ) {
	if ( true !== $valid || empty( $value ) || empty( $field['name'] ) || 0 !== strpos( $field['name'], 'fcc_' ) ) { return $valid; }
	if ( ! current_user_can( 'upload_files' ) ) { return 'Bạn không có quyền chọn ảnh.'; }
	if ( ! is_scalar( $value ) || ! ctype_digit( (string) $value ) || ! wp_attachment_is_image( (int) $value ) || ! in_array( get_post_mime_type( (int) $value ), array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) { return 'Chọn ảnh JPG, PNG hoặc WebP có trong Thư viện.'; }
	return $valid;
}
add_filter( 'acf/validate_value/type=image', 'project_child_fcc_validate_image_field', 10, 3 );

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
			if ( isset( $fields['fcc_ui_44137315906c33'] ) ) {
				// Cards read product/combo data; retain only this section's heading and footer CTA.
				$schema['home'][ $section ] = array_intersect_key( $fields, array_flip( array( 'fcc_ui_63b89170181142', 'fcc_ui_44137315906c33', 'fcc_ui_ab00a54528cf05', 'fcc_ui_573d0aefa67d9b', 'fcc_ui_839b3de79eb53b' ) ) );
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
			if ( isset( $field['type'], $field['name'] ) && 'image' === $field['type'] ) { $ids[] = absint( get_post_meta( project_core_fcc_page_id( 'shared' ), $field['name'], true ) ); }
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
	wp_enqueue_style( 'fcc-google-fonts', 'https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,500;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap', array(), null );
	$css_file = file_exists( get_stylesheet_directory() . '/assets/fractional/assets/css/style.min.css' ) ? 'assets/css/style.min.css' : 'assets/css/style.css';
	$resp_file = file_exists( get_stylesheet_directory() . '/assets/fractional/assets/css/responsive.min.css' ) ? 'assets/css/responsive.min.css' : 'assets/css/responsive.css';
	$styles = array( 'design' => $css_file, 'responsive' => $resp_file, 'integration' => 'wordpress.css' );
	if ( file_exists( get_stylesheet_directory() . '/assets/fractional/' . $kind . '.css' ) ) { $styles[ $kind ] = $kind . '.css'; }
	foreach ( $styles as $name => $path ) {
		wp_enqueue_style( 'fcc-' . $name, $base . $path, 'design' === $name ? array( 'fcc-google-fonts' ) : array( 'fcc-design' ), (string) filemtime( get_stylesheet_directory() . '/assets/fractional/' . $path ) );
	}
	$js_file = file_exists( get_stylesheet_directory() . '/assets/fractional/main.min.js' ) ? 'main.min.js' : 'main.js';
	wp_enqueue_script( 'fcc-interactions', $base . $js_file, array(), (string) filemtime( get_stylesheet_directory() . '/assets/fractional/' . $js_file ), true );
	if ( function_exists( 'project_core_fcc_frontend_data' ) ) {
		$client_labels = array();
		$defaults = project_core_fcc_default_document();
		if ( ! is_wp_error( $defaults ) && isset( $defaults['contentManagement']['clientLabels'] ) ) {
			foreach ( $defaults['contentManagement']['clientLabels'] as $key => $name ) { $client_labels[ $key ] = project_child_fcc_raw_text( $name, 'shared' ); }
		}
		$configuration = array(
			'labels'      => $client_labels,
			'kind'        => $kind,
			'productId'   => 'product' === $kind ? get_queried_object_id() : 0,
			'orderMode'   => get_option( 'project_core_fcc_order_mode', 'smtp' ),
			'endpoint'    => admin_url( 'admin-post.php' ),
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'fcc_order_action' ),
			'thankyouUrl' => project_core_fcc_url( 'thankyou' ),
			'homeUrl'     => project_core_fcc_url( 'home' ),
		);
		wp_add_inline_script( 'fcc-interactions', 'window.FCC_DATA=' . wp_json_encode( project_core_fcc_frontend_data( in_array( $kind, array( 'home', 'catalog', 'product', 'concerns' ), true ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';window.FCC_CONFIG=' . wp_json_encode( $configuration, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}
}
add_action( 'wp_enqueue_scripts', 'project_child_fcc_enqueue', 100 );

function project_child_fcc_performance_hints() {
	$kind = project_child_fcc_kind();
	if ( ! $kind ) { return; }
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	if ( 'home' === $kind && function_exists( 'project_child_fcc_hero_product_image_url' ) ) {
		$hero_url = project_child_fcc_hero_product_image_url();
		$webp_url = function_exists( 'project_child_fcc_webp_url' ) ? project_child_fcc_webp_url( $hero_url ) : '';
		if ( $webp_url ) {
			echo '<link rel="preload" as="image" href="' . esc_url( $webp_url ) . '" type="image/webp" fetchpriority="high">' . "\n";
		} elseif ( $hero_url ) {
			echo '<link rel="preload" as="image" href="' . esc_url( $hero_url ) . '" fetchpriority="high">' . "\n";
		}
	}
}
add_action( 'wp_head', 'project_child_fcc_performance_hints', 1 );


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
	if ( 'product' === $kind ) { return get_post_meta( get_queried_object_id(), 'fcc_product_seo_title', true ) ?: get_the_title( get_queried_object_id() ) . ' | ' . project_core_fcc_site( 'name' ); }
	$value = get_post_meta( project_core_fcc_page_id( $kind ), 'fcc_' . $kind . '_seo_title', true );
	return $value ?: $title;
}
add_filter( 'pre_get_document_title', 'project_child_fcc_title' );

function project_child_fcc_metadata() {
	$kind = project_child_fcc_kind();
	if ( ! $kind || ! function_exists( 'project_core_fcc_page_id' ) ) { return; }
	$description = 'product' === $kind ? ( get_post_meta( get_queried_object_id(), 'fcc_product_seo_description', true ) ?: project_child_fcc_product_value( 'tagline' ) ) : get_post_meta( project_core_fcc_page_id( $kind ), 'fcc_' . $kind . '_seo_description', true );
	$url = get_permalink( get_queried_object_id() );
	$product_share = 'product' === $kind ? wp_get_attachment_image_url( absint( get_post_meta( get_queried_object_id(), 'fcc_product_seo_image', true ) ), 'full' ) : '';
	$image = 'product' === $kind ? ( $product_share ?: project_child_fcc_product_value( 'image' ) ) : project_child_fcc_image( 'fcc_' . $kind . '_seo_image', $kind );
	echo '<meta name="description" content="' . esc_attr( $description ) . '"><link rel="canonical" href="' . esc_url( $url ) . '">';
	echo '<meta property="og:type" content="website"><meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '"><meta property="og:description" content="' . esc_attr( $description ) . '"><meta property="og:url" content="' . esc_url( $url ) . '">';
	if ( $image ) { echo '<meta property="og:image" content="' . esc_url( $image ) . '"><meta name="twitter:card" content="summary_large_image">'; }
	$favicon = project_child_fcc_image( 'fcc_site_favicon' );
	if ( $favicon ) { remove_action( 'wp_head', 'wp_site_icon', 99 ); echo '<link rel="icon" href="' . esc_url( $favicon ) . '">'; }
}
add_action( 'wp_head', 'project_child_fcc_metadata', 2 );

function project_child_fcc_robots( $robots ) {
	$local = 'production' !== wp_get_environment_type() || in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1', '::1' ), true );
	if ( project_child_fcc_kind() && function_exists( 'project_core_fcc_site' ) && ( $local || 'approved' !== project_core_fcc_site( 'content_status' ) || 'thankyou' === project_child_fcc_kind() ) ) {
		$robots['noindex'] = true; unset( $robots['index'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'project_child_fcc_robots' );

function project_child_fcc_block_editor( $use, $post ) {
	return 'page' === $post->post_type && 0 === strpos( (string) get_page_template_slug( $post->ID ), 'templates/fractional-' ) ? false : $use;
}
add_filter( 'use_block_editor_for_post', 'project_child_fcc_block_editor', 10, 2 );
