<?php
/** Quick-reference columns for the project's native content lists. @package Project_Core */
defined( 'ABSPATH' ) || exit;

/** Keep native row actions, primary title and Screen Options available. */
function project_core_fcc_content_list_columns( $columns, $type ) {
	$definitions = array(
		'fcc_product' => array( 'fcc_type' => 'Loại / Quy cách', 'fcc_price' => 'Giá bán', 'fcc_dossier' => 'Hồ sơ CBMP' ),
		'fcc_combo' => array( 'fcc_code' => 'Mã combo', 'fcc_price' => 'Giá combo', 'fcc_combo_products' => 'Sản phẩm bên trong' ),
		'fcc_ingredient' => array( 'fcc_tag' => 'Nhóm / Nhãn', 'fcc_description' => 'Mô tả' ),
		'fcc_site' => array( 'fcc_brand' => 'Thương hiệu', 'fcc_contact' => 'Liên hệ', 'fcc_content_status' => 'Trạng thái nội dung' ),
	);
	$result = array();
	if ( isset( $columns['cb'] ) ) { $result['cb'] = $columns['cb']; }
	if ( in_array( $type, array( 'fcc_product', 'fcc_combo' ), true ) ) { $result['fcc_image'] = 'Ảnh'; }
	if ( isset( $columns['title'] ) ) { $result['title'] = $columns['title']; }
	$result = array_merge( $result, $definitions[ $type ] );
	foreach ( $columns as $key => $label ) {
		if ( ! isset( $result[ $key ] ) && ! in_array( $key, array( 'fcc_featured', 'date' ), true ) ) { $result[ $key ] = $label; }
	}
	if ( isset( $columns['fcc_featured'] ) ) { $result['fcc_featured'] = $columns['fcc_featured']; }
	if ( isset( $columns['date'] ) ) { $result['date'] = $columns['date']; }
	return $result;
}

foreach ( array( 'fcc_product', 'fcc_combo', 'fcc_ingredient', 'fcc_site' ) as $type ) {
	add_filter( 'manage_' . $type . '_posts_columns', function ( $columns ) use ( $type ) { return project_core_fcc_content_list_columns( $columns, $type ); }, 20 );
	add_action( 'manage_' . $type . '_posts_custom_column', 'project_core_fcc_content_list_cell', 10, 2 );
}

/** Read metadata directly so draft/private items are also represented correctly. */
function project_core_fcc_admin_meta( $id, $key ) {
	$value = get_post_meta( $id, $key, true );
	return is_scalar( $value ) ? (string) $value : '';
}

function project_core_fcc_content_list_cell( $column, $id ) {
	switch ( $column ) {
		case 'fcc_image':
			$image = wp_get_attachment_image( absint( project_core_fcc_admin_meta( $id, 'fcc_image' ) ), array( 48, 48 ), false, array( 'alt' => '', 'class' => 'fcc-admin-thumb', 'loading' => 'lazy' ) );
			$link = get_edit_post_link( $id, 'raw' );
			echo $image ? ( $link ? '<a href="' . esc_url( $link ) . '" aria-label="' . esc_attr( 'Sửa ' . get_the_title( $id ) ) . '">' . $image . '</a>' : $image ) : '—';
			break;
		case 'fcc_type':
			$type = project_core_fcc_admin_meta( $id, 'fcc_categoryType' );
			$choices = project_core_fcc_product_category_choices();
			$label = isset( $choices[ $type ] ) ? $choices[ $type ] : project_core_fcc_admin_meta( $id, 'fcc_category' );
			echo esc_html( $label ?: '—' );
			$volume = project_core_fcc_admin_meta( $id, 'fcc_volume' );
			if ( '' !== $volume ) { echo '<span class="fcc-admin-muted">' . esc_html( $volume ) . '</span>'; }
			break;
		case 'fcc_price':
			project_core_fcc_quick_edit_row_data( $id );
			$price = project_core_fcc_admin_meta( $id, 'fcc_salePrice' );
			if ( '' === $price || ! is_numeric( $price ) ) { echo 'Chưa có giá'; break; }
			echo '<strong>' . esc_html( number_format( (float) $price, 0, ',', '.' ) . ' đ' ) . '</strong>';
			$original = project_core_fcc_admin_meta( $id, 'fcc_originalPrice' );
			if ( '' !== $original && is_numeric( $original ) && (float) $original > (float) $price ) {
				echo '<span class="fcc-admin-muted">Giá gốc: ' . esc_html( number_format( (float) $original, 0, ',', '.' ) . ' đ' ) . '</span>';
			}
			break;
		case 'fcc_dossier':
			$cbmp = project_core_fcc_admin_meta( $id, 'fcc_cbmp' );
			echo $cbmp ? '<span class="fcc-admin-cbmp">' . esc_html( $cbmp ) . '</span>' : 'Chưa có số CBMP';
			$status = project_core_fcc_admin_meta( $id, 'fcc_legalStatus' );
			if ( '' !== $status ) { echo '<span class="fcc-admin-muted">' . esc_html( $status ) . '</span>'; }
			break;
		case 'fcc_code':
			echo esc_html( project_core_fcc_admin_meta( $id, 'fcc_code' ) ?: '—' );
			break;
		case 'fcc_combo_products':
			$ids = array_values( array_unique( array_filter( array_map( 'absint', (array) get_post_meta( $id, 'fcc_productIds', true ) ) ) ) );
			if ( ! $ids ) { echo 'Chưa chọn sản phẩm'; break; }
			foreach ( $ids as $product_id ) {
				$product = get_post( $product_id );
				echo '<span class="fcc-admin-related">';
				if ( ! $product || 'fcc_product' !== $product->post_type || 'trash' === $product->post_status ) {
					echo esc_html( 'Sản phẩm không còn khả dụng (#' . $product_id . ')' );
				} elseif ( ! current_user_can( 'read_post', $product_id ) ) {
					echo 'Không có quyền xem sản phẩm';
				} else {
					$name = project_core_fcc_admin_meta( $product_id, 'fcc_shortName' ) ?: get_the_title( $product );
					$link = get_edit_post_link( $product_id, 'raw' );
					echo $link ? '<a href="' . esc_url( $link ) . '">' . esc_html( $name ) . '</a>' : esc_html( $name );
				}
				echo '</span>';
			}
			break;
		case 'fcc_tag':
			echo esc_html( project_core_fcc_admin_meta( $id, 'fcc_tag' ) ?: '—' );
			break;
		case 'fcc_description':
			$text = wp_strip_all_tags( project_core_fcc_admin_meta( $id, 'fcc_description' ) );
			echo $text ? '<span title="' . esc_attr( $text ) . '">' . esc_html( wp_trim_words( $text, 24 ) ) . '</span>' : 'Chưa có mô tả';
			break;
		case 'fcc_brand':
			echo esc_html( project_core_fcc_admin_meta( $id, 'fcc_name' ) ?: '—' );
			break;
		case 'fcc_contact':
			$phone = project_core_fcc_admin_meta( $id, 'fcc_hotlineDisplay' ) ?: project_core_fcc_admin_meta( $id, 'fcc_hotline' );
			$email = project_core_fcc_admin_meta( $id, 'fcc_email' );
			if ( ! $phone && ! $email ) { echo 'Chưa có liên hệ'; break; }
			if ( $phone ) { echo '<span class="fcc-admin-related"><a href="' . esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></span>'; }
			if ( $email ) { echo '<span class="fcc-admin-related">' . ( is_email( $email ) ? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>' : esc_html( $email ) ) . '</span>'; }
			break;
		case 'fcc_content_status':
			$status = project_core_fcc_admin_meta( $id, 'fcc_content_status' );
			$labels = array( 'local_sample' => 'Dữ liệu mẫu · Chờ duyệt', 'approved' => 'Đã duyệt' );
			echo esc_html( isset( $labels[ $status ] ) ? $labels[ $status ] : ( $status ?: 'Chưa chọn trạng thái' ) );
			break;
	}
}

/** Batch related posts/attachments once per list page, avoiding per-row queries. */
function project_core_fcc_prime_list_references( $posts, $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || ! in_array( $query->get( 'post_type' ), array( 'fcc_product', 'fcc_combo' ), true ) ) { return $posts; }
	update_meta_cache( 'post', wp_list_pluck( $posts, 'ID' ) );
	$ids = array();
	foreach ( $posts as $post ) {
		$ids[] = absint( project_core_fcc_admin_meta( $post->ID, 'fcc_image' ) );
		if ( 'fcc_combo' === $post->post_type ) { $ids = array_merge( $ids, array_map( 'absint', (array) get_post_meta( $post->ID, 'fcc_productIds', true ) ) ); }
	}
	$ids = array_values( array_unique( array_filter( $ids ) ) );
	if ( $ids ) { _prime_post_caches( $ids, false, true ); }
	return $posts;
}
add_filter( 'the_posts', 'project_core_fcc_prime_list_references', 10, 2 );

function project_core_fcc_content_list_styles( $hook ) {
	$screen = get_current_screen();
	if ( 'edit.php' !== $hook || ! $screen || ! in_array( $screen->post_type, array( 'fcc_product', 'fcc_combo', 'fcc_ingredient', 'fcc_site' ), true ) ) { return; }
	$root = dirname( __DIR__, 2 );
	wp_enqueue_style( 'fcc-admin-content-lists', plugins_url( 'assets/admin-content-lists.css', $root . '/project-core.php' ), array(), (string) filemtime( $root . '/assets/admin-content-lists.css' ) );
}
add_action( 'admin_enqueue_scripts', 'project_core_fcc_content_list_styles' );
