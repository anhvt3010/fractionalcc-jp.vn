<?php
/** Admin-only organization of shared presentation fields. @package Project_Child */
defined( 'ABSPATH' ) || exit;

/** Keep the original field keys/types; only the admin labels and placement change. */
function project_child_fcc_admin_shared_sections( $original ) {
	$fields = array();
	foreach ( $original as $section ) { $fields = array_merge( $fields, $section ); }
	$path = __DIR__ . '/fractional-admin-map.json';
	if ( ! is_readable( $path ) ) { return $original; }
	$map = json_decode( file_get_contents( $path ), true );
	if ( ! is_array( $map ) ) { return $original; }
	$sections = array();
	$product_url = '';
	foreach ( $map as $id => $definition ) {
		$title = $definition['label'];
		$url = '';
		if ( 'product' === $definition['page'] && function_exists( 'project_core_fcc_legacy_product_id' ) ) {
			if ( ! $product_url ) {
				$product_id = project_core_fcc_legacy_product_id( 1 );
				// ACF registers fields before project post types at init; this URL is valid at both stages.
				$product_url = $product_id ? add_query_arg( array( 'post_type' => 'fcc_product', 'p' => $product_id ), home_url( '/' ) ) : project_core_fcc_url( 'catalog' );
			}
			$url = $product_url;
		} elseif ( $definition['page'] && function_exists( 'project_core_fcc_url' ) ) {
			$url = project_core_fcc_url( $definition['page'] );
		}
		if ( $url && $definition['fragment'] ) { $url .= '#' . $definition['fragment']; }
		$link = $url ? ' <a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . ( 'modal' === $id ? 'Xem trang và bấm Mua ngay' : 'Xem vị trí trên website' ) . '</a>.' : '';
		$sections[ $title ] = array(
			array( 'key' => 'field_fcc_admin_help_' . $id, 'name' => '', 'type' => 'message', 'label' => 'Vị trí và cách sử dụng', 'message' => esc_html( $definition['description'] ) . $link, 'new_lines' => '', 'esc_html' => 0 ),
		);
		foreach ( $definition['fields'] as $name => $description ) {
			if ( ! isset( $fields[ $name ] ) ) { continue; }
			$field = $fields[ $name ];
			$field['label'] = $description['label'];
			$field['instructions'] = ( 'archive' === $id ? 'Nội dung lưu trữ. ' : 'Hiển thị tại: ' . esc_html( $title ) . '. ' ) . ( isset( $description['note'] ) ? esc_html( $description['note'] ) . ' ' : '' ) . $link;
			$field['wrapper']['width'] = 'image' === $field['type'] ? 50 : 100;
			$sections[ $title ][ $name ] = $field;
			unset( $fields[ $name ] );
		}
	}
	if ( $fields ) { $sections['Nội dung bổ sung'] = $fields; }
	return $sections;
}

/** A local finder opens native ACF tabs and focuses the existing field, without saving. */
function project_child_fcc_content_editor_finder( $post ) {
	if ( 'fcc_site' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) || ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	?>
	<div class="fcc-content-finder" id="fcc-content-finder">
		<h2>Tìm nội dung và vị trí hiển thị</h2>
		<p>Chọn tab theo khu vực bên dưới, hoặc tìm bằng tên mục/chữ đang thấy trên website. Nội dung riêng của trang chủ và từng sản phẩm được chỉnh ở mục tương ứng. Bạn vẫn có thể sửa text trực tiếp ngoài giao diện như trước.</p>
		<label for="fcc-field-search">Tìm trường nội dung</label>
		<input type="search" id="fcc-field-search" placeholder="Ví dụ: menu, hotline, chân trang, ảnh đánh giá…" autocomplete="off" aria-controls="fcc-field-results" aria-describedby="fcc-field-search-status">
		<p id="fcc-field-search-status" role="status" aria-live="polite">Kết quả sẽ ghi rõ nhóm và tên trường. Chọn kết quả để mở đúng tab.</p>
		<ul id="fcc-field-results" hidden></ul>
	</div>
	<?php
}
add_action( 'edit_form_after_title', 'project_child_fcc_content_editor_finder' );

function project_child_fcc_content_editor_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'fcc_site' !== $screen->post_type ) { return; }
	$assets = array( 'css/fractional-content-editor.css', 'js/fractional-content-editor.js' );
	foreach ( $assets as $asset ) {
		$path = get_stylesheet_directory() . '/assets/' . $asset;
		$url = get_stylesheet_directory_uri() . '/assets/' . $asset;
		if ( 0 === strpos( $asset, 'css/' ) ) { wp_enqueue_style( 'fcc-content-editor', $url, array( 'acf-input' ), (string) filemtime( $path ) ); }
		else { wp_enqueue_script( 'fcc-content-editor', $url, array( 'acf-input' ), (string) filemtime( $path ), true ); }
	}
}
add_action( 'admin_enqueue_scripts', 'project_child_fcc_content_editor_assets' );
