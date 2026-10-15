<?php
/** One native WordPress media editor for representative image and gallery order. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_product_media_box() {
	add_meta_box( 'fcc-product-media', 'Ảnh sản phẩm – đại diện & thứ tự', 'project_core_fcc_render_product_media', 'fcc_product', 'normal', 'high' );
}
add_action( 'add_meta_boxes_fcc_product', 'project_core_fcc_product_media_box' );

function project_core_fcc_render_product_media( $post ) {
	$ids = project_core_fcc_gallery_ids( $post->ID );
	$primary = absint( get_post_meta( $post->ID, 'fcc_image', true ) );
	if ( $primary && ! in_array( $primary, $ids, true ) && wp_attachment_is_image( $primary ) ) { array_unshift( $ids, $primary ); }
	$hover = absint( get_post_meta( $post->ID, 'fcc_hoverImage', true ) );
	if ( $hover && ! in_array( $hover, $ids, true ) && wp_attachment_is_image( $hover ) ) { $ids[] = $hover; }
	$images = array();
	foreach ( $ids as $id ) {
		$images[] = array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'thumbnail' ) ?: wp_get_attachment_url( $id ), 'title' => get_the_title( $id ) );
	}
	wp_nonce_field( 'fcc_product_media_' . $post->ID, 'fcc_product_media_nonce' );
	?>
	<div id="fcc-product-media-editor">
		<p>Ảnh đại diện dùng trên thẻ sản phẩm, trang chi tiết và đơn hàng mới. Đánh dấu <strong>Ảnh khi rê chuột (Hover card)</strong> để tạo hiệu ứng đổi ảnh và phóng to nhẹ khi khách xem danh sách. Kéo ảnh hoặc dùng nút lên/xuống để sắp xếp dải thumbnail; thứ tự độc lập với lựa chọn đại diện. Nhấn <strong>Cập nhật</strong> để lưu.</p>
		<input type="hidden" name="fcc_product_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<input type="hidden" name="fcc_product_primary" value="<?php echo esc_attr( $primary ); ?>">
		<input type="hidden" name="fcc_product_hover" value="<?php echo esc_attr( $hover ); ?>">
		<button type="button" class="button fcc-media-add">Chọn / thêm ảnh sản phẩm</button>
		<p class="fcc-media-status" role="status" aria-live="polite"></p>
		<ul class="fcc-media-list" aria-label="Ảnh sản phẩm theo thứ tự"></ul>
		<script type="application/json" class="fcc-media-data"><?php echo wp_json_encode( $images, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?></script>
		<noscript><p>Cần bật JavaScript để chọn và sắp xếp ảnh. Ảnh đang lưu được giữ nguyên.</p></noscript>
	</div>
	<?php
}

function project_core_fcc_product_media_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'fcc_product' !== $screen->post_type ) { return; }
	wp_enqueue_media();
	$root = dirname( __DIR__, 2 );
	wp_enqueue_script( 'fcc-product-media', plugins_url( 'assets/product-media.js', $root . '/project-core.php' ), array( 'jquery', 'jquery-ui-sortable', 'media-views' ), (string) filemtime( $root . '/assets/product-media.js' ), true );
	wp_enqueue_style( 'fcc-product-media', plugins_url( 'assets/product-media.css', $root . '/project-core.php' ), array(), (string) filemtime( $root . '/assets/product-media.css' ) );
}
add_action( 'admin_enqueue_scripts', 'project_core_fcc_product_media_assets' );

function project_core_fcc_save_product_media( $post_id ) {
	static $saving = false;
	if ( $saving || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) { return; }
	if ( ! isset( $_POST['fcc_product_media_nonce'] ) || ! is_string( $_POST['fcc_product_media_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fcc_product_media_nonce'] ) ), 'fcc_product_media_' . $post_id ) ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'upload_files' ) ) { return; }
	$raw = isset( $_POST['fcc_product_gallery'] ) && is_string( $_POST['fcc_product_gallery'] ) ? wp_unslash( $_POST['fcc_product_gallery'] ) : null;
	$primary_raw = isset( $_POST['fcc_product_primary'] ) && is_string( $_POST['fcc_product_primary'] ) ? wp_unslash( $_POST['fcc_product_primary'] ) : null;
	$hover_raw = isset( $_POST['fcc_product_hover'] ) && is_string( $_POST['fcc_product_hover'] ) ? wp_unslash( $_POST['fcc_product_hover'] ) : '0';
	$valid = null !== $raw && strlen( $raw ) <= 2000 && ( '' === $raw || preg_match( '/^[1-9][0-9]*(?:,[1-9][0-9]*)*$/', $raw ) ) && null !== $primary_raw && preg_match( '/^[0-9]+$/', $primary_raw ) && preg_match( '/^[0-9]+$/', $hover_raw );
	$ids = $valid && '' !== $raw ? array_map( 'absint', explode( ',', $raw ) ) : array();
	$primary = absint( $primary_raw );
	$hover = absint( $hover_raw );
	$valid = $valid && count( $ids ) <= 50 && count( $ids ) === count( array_unique( $ids ) ) && ( ! $primary || in_array( $primary, $ids, true ) ) && ( ! $hover || in_array( $hover, $ids, true ) );
	foreach ( $ids as $id ) {
		if ( ! wp_attachment_is_image( $id ) || ! in_array( get_post_mime_type( $id ), array( 'image/jpeg', 'image/png', 'image/webp' ), true ) || ! current_user_can( 'read_post', $id ) ) { $valid = false; break; }
	}
	if ( ! $valid ) { set_transient( 'fcc_media_error_' . get_current_user_id(), 1, MINUTE_IN_SECONDS ); return; }
	$saving = true;
	try {
		$content = (string) get_post_field( 'post_content', $post_id );
		$updated = wp_update_post( wp_slash( array( 'ID' => $post_id, 'post_content' => project_core_fcc_gallery_content( $content, $ids ) ) ), true );
		if ( is_wp_error( $updated ) ) { set_transient( 'fcc_media_error_' . get_current_user_id(), 1, MINUTE_IN_SECONDS ); return; }
		update_post_meta( $post_id, 'fcc_image', $primary );
		update_post_meta( $post_id, '_fcc_image', 'field_fcc_image' );
		update_post_meta( $post_id, 'fcc_hoverImage', $hover );
		update_post_meta( $post_id, '_fcc_hoverImage', 'field_fcc_hoverImage' );
		update_post_meta( $post_id, '_fcc_gallery_initialized', 1 );
		update_post_meta( $post_id, '_fcc_product_media_customized', 1 );
	} finally { $saving = false; }
}
add_action( 'save_post_fcc_product', 'project_core_fcc_save_product_media', 20 );

function project_core_fcc_product_media_notice() {
	$key = 'fcc_media_error_' . get_current_user_id();
	if ( get_transient( $key ) ) { delete_transient( $key ); echo '<div class="notice notice-error"><p>Không lưu được ảnh sản phẩm. Chọn tối đa 50 ảnh JPG/PNG/WebP hợp lệ trong Thư viện; ảnh đại diện phải thuộc danh sách. Bộ ảnh trước đó được giữ nguyên.</p></div>'; }
}
add_action( 'admin_notices', 'project_core_fcc_product_media_notice' );
