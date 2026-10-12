<?php
/** Product metadata in native WordPress Quick Edit. @package Project_Core */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_quick_edit_fields() {
	return array(
		'salePrice' => array( 'label' => 'Giá bán (VNĐ)', 'type' => 'number' ),
		'originalPrice' => array( 'label' => 'Giá gốc (VNĐ)', 'type' => 'number' ),
		'shortName' => array( 'label' => 'Tên ngắn', 'type' => 'text' ),
		'volume' => array( 'label' => 'Quy cách / Dung tích', 'type' => 'text' ),
		'categoryType' => array( 'label' => 'Loại sản phẩm', 'type' => 'select' ),
		'origin' => array( 'label' => 'Xuất xứ', 'type' => 'text' ),
		'cbmp' => array( 'label' => 'Số CBMP', 'type' => 'text' ),
		'legalStatus' => array( 'label' => 'Trạng thái hồ sơ', 'type' => 'text' ),
		'tagline' => array( 'label' => 'Công dụng / Mô tả ngắn', 'type' => 'text' ),
	);
}

/** Each AJAX-replaced row includes fresh values for the next edit. */
function project_core_fcc_quick_edit_row_data( $id ) {
	if ( 'fcc_product' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) { return; }
	$values = array();
	foreach ( project_core_fcc_quick_edit_fields() as $name => $field ) { $values[ $name ] = project_core_fcc_admin_meta( $id, 'fcc_' . $name ); }
	echo '<span class="fcc-product-quick-data" hidden data-fields="' . esc_attr( wp_json_encode( $values ) ) . '"></span>';
}

function project_core_fcc_quick_edit_form( $column, $type ) {
	if ( 'fcc_product' !== $type || 'fcc_price' !== $column ) { return; }
	?>
	<fieldset class="inline-edit-col-left fcc-quick-edit-fields" disabled>
		<div class="inline-edit-col">
			<h4>Thông tin sản phẩm</h4>
			<p class="description">Giá nhập bằng số VNĐ, không dùng dấu phân cách. Để trống nếu chưa có giá; số 0 được lưu là giá 0.</p>
			<input type="hidden" name="fcc_quick_edit_present" value="1">
			<?php wp_nonce_field( 'fcc_product_quick_edit', 'fcc_quick_edit_nonce', false ); ?>
			<div class="fcc-quick-edit-grid">
				<?php foreach ( project_core_fcc_quick_edit_fields() as $name => $field ) : ?>
				<label class="fcc-quick-edit-field<?php echo 'tagline' === $name ? ' fcc-quick-edit-field-wide' : ''; ?>">
					<span><?php echo esc_html( $field['label'] ); ?></span>
					<?php if ( 'select' === $field['type'] ) : ?>
					<select name="fcc_quick_edit[<?php echo esc_attr( $name ); ?>]" data-fcc-field="<?php echo esc_attr( $name ); ?>">
						<option value="">Chưa chọn</option>
						<?php foreach ( project_core_fcc_product_category_choices() as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option><?php endforeach; ?>
					</select>
					<?php else : ?>
					<input type="<?php echo esc_attr( $field['type'] ); ?>" name="fcc_quick_edit[<?php echo esc_attr( $name ); ?>]" data-fcc-field="<?php echo esc_attr( $name ); ?>"<?php if ( 'number' === $field['type'] ) : ?> min="0" step="1" inputmode="numeric"<?php endif; ?>>
					<?php endif; ?>
				</label>
				<?php endforeach; ?>
			</div>
			<p class="fcc-quick-edit-error" role="alert" hidden></p>
		</div>
	</fieldset>
	<?php
}
add_action( 'quick_edit_custom_box', 'project_core_fcc_quick_edit_form', 10, 2 );

/** Validate all submitted custom values before native WordPress saves any fields. */
function project_core_fcc_quick_edit_values( $input ) {
	if ( ! is_array( $input ) ) { return new WP_Error( 'fcc_quick_fields', 'Thông tin sửa nhanh không hợp lệ.' ); }
	$values = array();
	foreach ( project_core_fcc_quick_edit_fields() as $name => $field ) {
		if ( ! array_key_exists( $name, $input ) ) { continue; }
		if ( ! is_scalar( $input[ $name ] ) ) { return new WP_Error( 'fcc_quick_value', 'Giá trị không hợp lệ: ' . $field['label'] ); }
		$value = trim( (string) $input[ $name ] );
		if ( 'number' === $field['type'] && '' !== $value ) {
			$normalized = ltrim( $value, '0' );
			$normalized = '' === $normalized ? '0' : $normalized;
			$maximum = (string) PHP_INT_MAX;
			if ( ! preg_match( '/^[0-9]+$/D', $value ) || strlen( $normalized ) > strlen( $maximum ) || ( strlen( $normalized ) === strlen( $maximum ) && strcmp( $normalized, $maximum ) > 0 ) ) {
				return new WP_Error( 'fcc_quick_price', $field['label'] . ' phải là số nguyên không âm, không có dấu phân cách.' );
			}
			$value = $normalized;
		} else {
			$value = sanitize_text_field( $value );
		}
		if ( 'select' === $field['type'] && '' !== $value && ! array_key_exists( $value, project_core_fcc_product_category_choices() ) ) {
			return new WP_Error( 'fcc_quick_category', 'Hãy chọn loại sản phẩm trong danh sách.' );
		}
		$values[ $name ] = $value;
	}
	return $values;
}

function project_core_fcc_is_product_quick_edit() {
	return wp_doing_ajax() && isset( $_POST['action'], $_POST['fcc_quick_edit_present'] ) && 'inline-save' === $_POST['action'] && '1' === $_POST['fcc_quick_edit_present'];
}

function project_core_fcc_quick_edit_authorized( $id ) {
	return 'fcc_product' === get_post_type( $id ) && current_user_can( 'edit_post', $id )
		&& isset( $_POST['_inline_edit'], $_POST['fcc_quick_edit_nonce'] )
		&& is_string( $_POST['_inline_edit'] ) && is_string( $_POST['fcc_quick_edit_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_inline_edit'] ) ), 'inlineeditnonce' )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fcc_quick_edit_nonce'] ) ), 'fcc_product_quick_edit' );
}

function project_core_fcc_validate_quick_edit_request() {
	if ( ! project_core_fcc_is_product_quick_edit() ) { return; }
	$id = isset( $_POST['post_ID'] ) && is_scalar( $_POST['post_ID'] ) ? absint( $_POST['post_ID'] ) : 0;
	if ( ! project_core_fcc_quick_edit_authorized( $id ) ) { wp_die( esc_html( 'Không có quyền sửa hoặc phiên sửa nhanh đã hết hạn. Hãy tải lại danh sách.' ), '', array( 'response' => 403 ) ); }
	$values = project_core_fcc_quick_edit_values( isset( $_POST['fcc_quick_edit'] ) ? wp_unslash( $_POST['fcc_quick_edit'] ) : null );
	if ( is_wp_error( $values ) ) { wp_die( esc_html( $values->get_error_message() ) ); }
}
add_action( 'wp_ajax_inline-save', 'project_core_fcc_validate_quick_edit_request', 0 );

function project_core_fcc_save_quick_edit( $id ) {
	if ( ! project_core_fcc_is_product_quick_edit() || ! project_core_fcc_quick_edit_authorized( $id ) || wp_is_post_revision( $id ) || wp_is_post_autosave( $id ) ) { return; }
	$values = project_core_fcc_quick_edit_values( isset( $_POST['fcc_quick_edit'] ) ? wp_unslash( $_POST['fcc_quick_edit'] ) : null );
	if ( is_wp_error( $values ) ) { return; }
	foreach ( $values as $name => $value ) {
		$key = 'field_fcc_' . $name;
		if ( function_exists( 'update_field' ) && acf_get_field( $key ) ) { update_field( $key, $value, $id ); }
		else { update_post_meta( $id, 'fcc_' . $name, $value ); }
	}
}
add_action( 'save_post_fcc_product', 'project_core_fcc_save_quick_edit' );

function project_core_fcc_quick_edit_script( $hook ) {
	$screen = get_current_screen();
	if ( 'edit.php' !== $hook || ! $screen || 'fcc_product' !== $screen->post_type ) { return; }
	$root = dirname( __DIR__, 2 );
	wp_enqueue_script( 'fcc-product-quick-edit', plugins_url( 'assets/admin-product-quick-edit.js', $root . '/project-core.php' ), array( 'inline-edit-post' ), (string) filemtime( $root . '/assets/admin-product-quick-edit.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'project_core_fcc_quick_edit_script' );
